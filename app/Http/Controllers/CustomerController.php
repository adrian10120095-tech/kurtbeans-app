<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\SinkronisasiMidtrans;
use App\Models\Menu;
use App\Models\Kategori;
use App\Models\Meja;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use App\Models\DetailPesanan;
use App\Models\PelangganSementara;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class CustomerController extends Controller
{
    /**
     * Menyiapkan konfigurasi SDK Midtrans.
     * Dipanggil sebelum setiap pemanggilan API Midtrans.
     */
    private function initMidtrans(): void
    {
        Config::$serverKey    = config('services.midtrans.serverKey');
        Config::$isProduction = (bool) config('services.midtrans.isProduction');
        Config::$isSanitized  = (bool) config('services.midtrans.isSanitized');
        Config::$is3ds        = (bool) config('services.midtrans.is3ds');
    }

    // ================= SCAN QR CODE & INISIALISASI SESI =================
    public function scanQr(Request $request)
    {
        $nomorMeja = $request->query('meja');

        if (!$nomorMeja) {
            abort(400, 'QR Code tidak valid. Harap scan QR Code yang ada di meja Anda.');
        }

        $meja = Meja::query()->where('nomor_meja', $nomorMeja)->first();

        if (!$meja) {
            abort(404, 'Meja nomor ' . $nomorMeja . ' tidak ditemukan di sistem.');
        }

        // ---- Penentuan KUNJUNGAN ----
        // Satu kunjungan = satu "struk". Riwayat yang terlihat pelanggan
        // dibatasi pada kunjungan yang sedang berlangsung, sehingga
        // pelanggan berikutnya di meja yang sama memulai dari nol.
        //
        // Kunjungan lama HANYA dilanjutkan bila memindai meja yang sama
        // DAN masih ada pesanan yang belum diambil/dibatalkan. Ini supaya
        // pelanggan yang memindai ulang di tengah kunjungan tidak
        // kehilangan riwayat pesanannya.
        $kunjunganLama = session('kunjungan');
        $lanjutkan     = false;

        if ($kunjunganLama && session('id_meja') == $meja->id_meja) {
            $idPelangganLama = PelangganSementara::query()
                                    ->where('session_id', $kunjunganLama)
                                    ->pluck('id_pelanggan_sementara');

            $lanjutkan = $idPelangganLama->isNotEmpty()
                && Pesanan::query()
                        ->whereIn('id_pelanggan_sementara', $idPelangganLama)
                        ->whereNotIn('status_pesanan', ['Selesai', 'Dibatalkan'])
                        ->exists();
        }

        session([
            'id_meja'    => $meja->id_meja,
            'nomor_meja' => $meja->nomor_meja,
            'kunjungan'  => $lanjutkan ? $kunjunganLama : (string) Str::uuid(),
        ]);

        return redirect()->route('customer.menu');
    }

    // ================= MENAMPILKAN HALAMAN MENU =================
    public function menu()
    {
        if (!session()->has('id_meja')) {
            return redirect('/')->with('error', 'Silakan scan QR Code di meja Anda terlebih dahulu untuk memulai pemesanan.');
        }

        $kategori = Kategori::query()->with(['menu' => function ($query) {
            $query->where('status_menu', 'Tersedia');
        }])->get();

        $nomor_meja = session('nomor_meja');

        return view('customer.menu', compact('kategori', 'nomor_meja'));
    }

    // ================= PROSES CHECKOUT & PAYMENT GATEWAY =================
    public function checkout(Request $request)
    {
        if (!session()->has('id_meja')) {
            return response()->json(['error' => 'Sesi pesanan telah habis atau Anda belum melakukan scan QR Code.'], 403);
        }

        $request->validate([
            'nama_pemesan'      => 'required|string|max:100',
            'metode'            => 'required|in:midtrans,tunai',
            'cart'              => 'required|array|min:1',
            'cart.*.id_menu'    => 'required|integer',
            'cart.*.qty'        => 'required|integer|min:1',
        ]);

        // PENTING: harga TIDAK diambil dari data yang dikirim browser,
        // melainkan dibaca ulang dari database. Selain lebih aman
        // (pelanggan tidak bisa memanipulasi harga lewat DevTools),
        // ini juga menghindari ketidakcocokan nama field antara
        // JavaScript keranjang (price) dan controller (harga).
        $idMenu   = collect($request->cart)->pluck('id_menu')->unique();
        $daftarMenu = Menu::query()->whereIn('id_menu', $idMenu)->get()->keyBy('id_menu');

        $totalHarga = 0;
        $baris      = [];

        foreach ($request->cart as $item) {
            $menu = $daftarMenu->get($item['id_menu']);

            if (!$menu) {
                return response()->json(['error' => 'Ada menu yang sudah tidak tersedia. Silakan muat ulang halaman.'], 422);
            }
            if ($menu->status_menu !== 'Tersedia') {
                return response()->json(['error' => 'Maaf, menu "' . $menu->nama_menu . '" sedang tidak tersedia.'], 422);
            }

            $qty      = (int) $item['qty'];
            $subtotal = $menu->harga * $qty;
            $totalHarga += $subtotal;

            $baris[] = [
                'id_menu'  => $menu->id_menu,
                'quantity' => $qty,
                'subtotal' => $subtotal,
                'catatan'  => $item['catatan'] ?? null,
            ];
        }

        $bayarTunai = $request->metode === 'tunai';
        $orderId    = 'KB-' . time() . '-' . rand(100, 999);

        // Semua penulisan dibungkus transaksi database supaya kalau
        // salah satu gagal, tidak ada pesanan setengah jadi.
        try {
            $pesanan = DB::transaction(function () use ($request, $baris, $totalHarga, $orderId, $bayarTunai) {
                $pelanggan = PelangganSementara::query()->create([
                    'nama_pemesan' => $request->nama_pemesan,
                    // Diisi token kunjungan, bukan ID sesi peramban, supaya
                    // riwayat tidak ikut terbawa ke kunjungan berikutnya.
                    'session_id'   => session('kunjungan')
                ]);

                $pesanan = Pesanan::query()->create([
                    'id_meja'                => session('id_meja'),
                    'id_pelanggan_sementara' => $pelanggan->id_pelanggan_sementara,
                    'total_harga'            => $totalHarga,
                    'status_pesanan'         => 'Menunggu Pembayaran',
                    'status_pembayaran'      => 'Belum Lunas',
                    // Metode diisi sejak awal supaya Kasir bisa membedakan
                    // pesanan yang memang akan dibayar tunai dari pesanan
                    // online yang ditinggalkan pelanggan.
                    'metode_pembayaran'      => $bayarTunai ? 'Tunai' : null,
                    'midtrans_order_id'      => $bayarTunai ? null : $orderId,
                    'tgl_pesan'              => now(),
                ]);

                foreach ($baris as $b) {
                    DetailPesanan::query()->create(array_merge($b, ['id_pesanan' => $pesanan->id_pesanan]));
                }

                return $pesanan;
            });
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan pesanan: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan pesanan. Silakan coba lagi.'], 500);
        }

        // ---- Alur TUNAI: berhenti di sini, tidak menghubungi Midtrans ----
        // Pesanan menunggu kasir menerima uang lalu menekan "Validasi Tunai".
        if ($bayarTunai) {
            return response()->json([
                'metode'     => 'Tunai',
                'id_pesanan' => $pesanan->id_pesanan,
                'no_antrean' => $pesanan->no_antrean,
                'message'    => 'Pesanan dibuat. Silakan bayar di kasir.',
            ]);
        }

        // ---- Alur ONLINE: meminta Snap Token ke Midtrans ----
        $this->initMidtrans();

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                // Midtrans hanya menerima bilangan bulat (Rupiah tanpa desimal)
                'gross_amount' => (int) round($totalHarga),
            ],
            'customer_details' => [
                'first_name' => $request->nama_pemesan,
            ],
            'item_details' => collect($baris)->map(function ($b) use ($daftarMenu) {
                $menu = $daftarMenu->get($b['id_menu']);
                return [
                    'id'       => (string) $b['id_menu'],
                    'price'    => (int) round($menu->harga),
                    'quantity' => $b['quantity'],
                    'name'     => substr($menu->nama_menu, 0, 50),
                ];
            })->values()->all(),
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
        } catch (\Exception $e) {
            Log::error('Midtrans getSnapToken gagal: ' . $e->getMessage());
            $pesanan->update(['status_pembayaran' => 'Gagal']);
            return response()->json([
                'error' => 'Layanan pembayaran sedang gangguan, silakan bayar di kasir.'
            ], 502);
        }

        // Catat transaksi pembayaran (tabel pembayaran)
        Pembayaran::query()->create([
            'id_pesanan'         => $pesanan->id_pesanan,
            'order_id'           => $orderId,
            'gross_amount'       => $totalHarga,
            'transaction_status' => 'pending',
            'payment_token'      => $snapToken,
        ]);

        return response()->json([
            'metode'     => 'Midtrans',
            'snap_token' => $snapToken,
            'id_pesanan' => $pesanan->id_pesanan,
        ]);
    }

    // ================= KONFIRMASI PEMBAYARAN (dipanggil dari onSuccess Snap) =================
    /**
     * Dipanggil browser setelah Snap melaporkan pembayaran berhasil.
     *
     * Status TIDAK dipercaya dari browser. Server menanyakan ulang
     * status transaksi langsung ke Midtrans (Get Status API), jadi
     * pelanggan tidak bisa memalsukan pembayaran. Endpoint ini yang
     * membuat alur tetap jalan di localhost, karena webhook Midtrans
     * tidak bisa menjangkau alamat lokal.
     */
    public function konfirmasiPembayaran(Request $request)
    {
        $request->validate(['id_pesanan' => 'required|integer']);

        $pesanan = Pesanan::query()->find($request->id_pesanan);

        if (!$pesanan) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        // Sudah lunas (mis. webhook atau sinkronisasi dashboard lebih dulu
        // menandainya) -> tidak perlu diproses lagi. Nomor antrean TETAP
        // dikirim, karena jalur inilah yang paling sering diambil sekarang
        // setelah webhook aktif.
        if ($pesanan->status_pembayaran === 'Lunas') {
            return response()->json([
                'status'     => 'success',
                'no_antrean' => $pesanan->no_antrean,
                'message'    => 'Pembayaran sudah tercatat lunas.',
            ]);
        }

        $this->initMidtrans();

        try {
            $status = Transaction::status($pesanan->midtrans_order_id);
        } catch (\Exception $e) {
            Log::error('Midtrans Transaction::status gagal: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak dapat memverifikasi pembayaran. Silakan konfirmasi ke kasir.'
            ], 502);
        }

        $transactionStatus = $status->transaction_status ?? null;
        $fraudStatus       = $status->fraud_status ?? null;
        $paymentType       = $status->payment_type ?? 'Midtrans';

        $lunas = in_array($transactionStatus, ['capture', 'settlement'])
                 && $fraudStatus !== 'deny';

        if ($lunas) {
            $pesanan->update([
                'status_pembayaran' => 'Lunas',
                // Masuk antrean Barista sebagai pesanan baru
                'status_pesanan'    => 'Menunggu Diproses',
                'metode_pembayaran' => $paymentType,
                'tgl_bayar'         => now(),
            ]);

            Pembayaran::query()->updateOrCreate(
                ['order_id' => $pesanan->midtrans_order_id],
                [
                    'id_pesanan'         => $pesanan->id_pesanan,
                    'gross_amount'       => $pesanan->total_harga,
                    'payment_type'       => $paymentType,
                    'transaction_status' => $transactionStatus,
                    'fraud_status'       => $fraudStatus,
                ]
            );

            return response()->json([
                'status'     => 'success',
                'no_antrean' => $pesanan->no_antrean,
                'message'    => 'Pembayaran berhasil. Pesanan Anda masuk antrean.'
            ]);
        }

        if (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $pesanan->update(['status_pembayaran' => 'Gagal']);
            return response()->json(['status' => 'failed', 'message' => 'Pembayaran gagal atau dibatalkan.']);
        }

        return response()->json(['status' => 'pending', 'message' => 'Pembayaran Anda masih menunggu penyelesaian.']);
    }

    // ================= MELANJUTKAN PEMBAYARAN YANG BELUM SELESAI =================
    /**
     * Membuka kembali pembayaran untuk pesanan yang sudah dibuat tetapi
     * belum lunas — mis. pelanggan menutup Snap tanpa membayar atau salah
     * pencet. Karena Midtrans menolak order_id yang sudah dipakai, dibuat
     * order_id + Snap token BARU, lalu midtrans_order_id pesanan diperbarui
     * supaya konfirmasi & sinkronisasi status tetap konsisten.
     */
    public function lanjutkanPembayaran(Request $request)
    {
        $request->validate(['id_pesanan' => 'required|integer']);

        if (!session()->has('id_meja')) {
            return response()->json(['status' => 'error', 'message' => 'Sesi Anda telah berakhir. Silakan scan QR Code kembali.'], 403);
        }

        // Keamanan: pastikan pesanan memang milik kunjungan & meja ini.
        $pesanan = $this->pesananSesiIni()->firstWhere('id_pesanan', (int) $request->id_pesanan);

        if (!$pesanan) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan pada sesi ini.'], 404);
        }

        // Sudah lunas -> tidak perlu bayar lagi.
        if ($pesanan->status_pembayaran === 'Lunas') {
            return response()->json(['status' => 'sudah_lunas', 'no_antrean' => $pesanan->no_antrean]);
        }

        // Dibatalkan -> tidak bisa dibayar.
        if ($pesanan->status_pesanan === 'Dibatalkan') {
            return response()->json(['status' => 'batal', 'message' => 'Pesanan ini sudah dibatalkan.']);
        }

        // Pesanan tunai: pembayaran dilakukan di kasir, bukan lewat Snap.
        if ($pesanan->metode_pembayaran === 'Tunai') {
            return response()->json([
                'status'     => 'tunai',
                'no_antrean' => $pesanan->no_antrean,
                'message'    => 'Tunjukkan nomor antrean Anda ke kasir untuk membayar.',
            ]);
        }

        // ---- Pesanan online yang belum dibayar: buat Snap token baru ----
        $this->initMidtrans();

        $orderId = 'KB-' . time() . '-' . rand(100, 999);

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) round($pesanan->total_harga),
            ],
            'customer_details' => [
                'first_name' => $pesanan->pelanggan->nama_pemesan ?? 'Pelanggan',
            ],
            'item_details' => $pesanan->detail->map(function ($d) {
                return [
                    'id'       => (string) $d->id_menu,
                    'price'    => (int) round(($d->subtotal ?? 0) / max(1, $d->quantity)),
                    'quantity' => $d->quantity,
                    'name'     => substr($d->menu->nama_menu ?? 'Menu', 0, 50),
                ];
            })->values()->all(),
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
        } catch (\Exception $e) {
            Log::error('Midtrans getSnapToken (lanjutkan) gagal: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Layanan pembayaran sedang gangguan, silakan bayar di kasir.',
            ], 502);
        }

        // Arahkan pesanan ke transaksi yang baru.
        $pesanan->update(['midtrans_order_id' => $orderId]);

        // Perbarui (atau buat) baris pembayaran dengan token baru.
        $pembayaran = Pembayaran::query()->where('id_pesanan', $pesanan->id_pesanan)
                        ->latest('id_pembayaran')->first();
        if ($pembayaran) {
            $pembayaran->update([
                'order_id'           => $orderId,
                'gross_amount'       => $pesanan->total_harga,
                'transaction_status' => 'pending',
                'payment_token'      => $snapToken,
            ]);
        } else {
            Pembayaran::query()->create([
                'id_pesanan'         => $pesanan->id_pesanan,
                'order_id'           => $orderId,
                'gross_amount'       => $pesanan->total_harga,
                'transaction_status' => 'pending',
                'payment_token'      => $snapToken,
            ]);
        }

        return response()->json([
            'status'     => 'online',
            'snap_token' => $snapToken,
            'id_pesanan' => $pesanan->id_pesanan,
        ]);
    }

    // ================= PEMANTAUAN PESANAN OLEH PELANGGAN =================
    /**
     * Mengambil seluruh pesanan milik sesi peramban yang sedang aktif.
     *
     * Pelanggan tidak punya akun, jadi kepemilikan pesanan ditentukan
     * dari session_id yang tersimpan di pelanggan_sementara saat checkout.
     * Dengan begitu pelanggan hanya bisa melihat pesanannya sendiri.
     */
    private function pesananSesiIni()
    {
        $idMeja = session('id_meja');

        // Belum scan QR Code -> tidak ada meja aktif -> tidak ada yang ditampilkan.
        if (!$idMeja) {
            return collect();
        }

        $idPelanggan = PelangganSementara::query()
                            ->where('session_id', session('kunjungan'))
                            ->pluck('id_pelanggan_sementara');

        if ($idPelanggan->isEmpty()) {
            return collect();
        }

        // Disaring DUA lapis:
        //   1. token kunjungan -> hanya pesanan pada kunjungan yang berjalan
        //   2. id_meja         -> hanya pesanan pada meja yang sedang aktif
        //
        // Lapis pertama membuat riwayat berakhir bersama kunjungan
        // (seperti struk kertas), lapis kedua membuat riwayat berpindah
        // mengikuti meja yang sedang di-scan.
        return Pesanan::query()
                ->with(['detail.menu', 'meja', 'pelanggan'])
                ->whereIn('id_pelanggan_sementara', $idPelanggan)
                ->where('id_meja', $idMeja)
                ->orderBy('id_pesanan', 'desc')
                ->get();
    }

    /**
     * Halaman "Pesanan Saya" (SKPL-F-6).
     */
    public function statusPesanan()
    {
        if (!session()->has('id_meja')) {
            return redirect('/')->with('error', 'Silakan scan QR Code di meja Anda terlebih dahulu.');
        }

        return view('customer.status', [
            'daftarPesanan' => $this->pesananSesiIni(),
            'nomor_meja'    => session('nomor_meja'),
        ]);
    }

    /**
     * Versi JSON dari halaman di atas, dipanggil berkala oleh halaman
     * status agar perubahan dari Kasir/Barista langsung terlihat
     * tanpa pelanggan perlu memuat ulang halaman.
     */
    public function statusPesananJson()
    {
        $daftar = $this->pesananSesiIni();

        // Selaraskan dulu dengan Midtrans, baru kirim hasilnya ke peramban.
        (new SinkronisasiMidtrans)->selaraskan($daftar);

        $data = $this->pesananSesiIni()->map(function ($p) {
            return [
                'id_pesanan'        => $p->id_pesanan,
                'no_antrean'        => $p->no_antrean,
                'nama_pemesan'      => $p->pelanggan->nama_pemesan ?? '-',
                'nomor_meja'        => $p->meja->nomor_meja ?? '-',
                'total_harga'       => (int) $p->total_harga,
                'metode_pembayaran' => $p->metode_pembayaran,
                'status_pembayaran' => $p->status_pembayaran,
                'status_pesanan'    => $p->status_pesanan,
                'waktu'             => optional($p->tgl_pesan)->format('d M Y, H:i'),
                'item'              => $p->detail->map(function ($d) {
                    return [
                        'nama' => $d->menu->nama_menu ?? 'Menu dihapus',
                        'qty'  => $d->quantity,
                    ];
                })->values(),
            ];
        });

        return response()->json(['pesanan' => $data]);
    }

    // ================= SIMPAN TOKEN PUSH NOTIFICATION =================
    public function simpanTokenFCM(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $pelanggan = PelangganSementara::query()
                        ->where('session_id', session('kunjungan'))
                        ->latest('id_pelanggan_sementara')
                        ->first();

        if ($pelanggan) {
            $pelanggan->update(['token_subscription' => $request->fcm_token]);
            return response()->json(['status' => 'success', 'message' => 'Token FCM berhasil disimpan.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Data pelanggan tidak ditemukan pada sesi ini.'], 404);
    }
}