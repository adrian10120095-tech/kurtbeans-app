<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Notifikasi;
use App\Services\SinkronisasiMidtrans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class BaristaController extends Controller
{
    public function index()
    {
        // Hanya pesanan yang SUDAH LUNAS yang boleh masuk antrean dapur.
        $pesanan = Pesanan::query()
                    ->with(['detail.menu', 'meja', 'pelanggan'])
                    ->where('status_pembayaran', 'Lunas')
                    ->orderBy('tgl_bayar', 'asc')
                    ->get();

        $antreanBaru     = $this->petakan($pesanan->where('status_pesanan', 'Menunggu Diproses'));
        $antreanDiproses = $this->petakan($pesanan->where('status_pesanan', 'Diproses'));

        // Minuman sudah jadi dan menunggu diambil pelanggan.
        // Inilah yang perlu dipantau Barista: kalau terlalu lama tidak
        // diambil, minumannya dingin dan meja bar penuh.
        $antreanSiap = $this->petakan($pesanan->where('status_pesanan', 'Siap Diambil'));

        // Sudah benar-benar diserahkan ke pelanggan.
        $sudahDiambil = $pesanan->where('status_pesanan', 'Selesai')
                                ->where('tgl_selesai', '>=', now()->startOfDay());

        // Pesanan paling tua yang belum dikerjakan -> yang harus
        // didahulukan Barista. Dipakai panel "Fokus Sekarang".
        $fokus = $antreanBaru->first() ?? $antreanDiproses->first();

        return view('barista.dashboard', [
            'fokus'           => $fokus,
            'antreanBaru'     => $antreanBaru,
            'antreanDiproses' => $antreanDiproses,
            'antreanSiap'     => $antreanSiap,
            'riwayatSelesai'  => $this->petakanRiwayat($sudahDiambil),
            'jumlahBaru'      => $antreanBaru->count(),
            'jumlahDiproses'  => $antreanDiproses->count(),
            'jumlahSiap'      => $antreanSiap->count(),
            'jumlahSelesai'   => $sudahDiambil->count(),
        ]);
    }

    /**
     * Sidik jari ringkas keadaan antrean dapur.
     *
     * Dipakai dashboard Barista untuk mendeteksi pesanan baru atau
     * perubahan status tanpa harus mengunduh ulang seluruh halaman.
     */
    public function sinyal()
    {
        // Barista ikut menyelaraskan supaya pesanan yang baru lunas tetap
        // masuk antrean walaupun tidak ada dashboard kasir yang terbuka.
        // Aman dipanggil berulang: ada pembatas 15 detik per pesanan.
        (new SinkronisasiMidtrans)->selaraskan(
            Pesanan::query()
                ->where('status_pembayaran', 'Belum Lunas')
                ->whereNotNull('midtrans_order_id')
                ->where('tgl_pesan', '>=', now()->subDay())
                ->get()
        );

        $ringkas = Pesanan::query()
                    ->where('status_pembayaran', 'Lunas')
                    ->whereIn('status_pesanan', ['Menunggu Diproses', 'Diproses', 'Siap Diambil'])
                    ->selectRaw('COUNT(*) as jumlah, MAX(updated_at) as terakhir')
                    ->first();

        $baru = Pesanan::query()
                    ->where('status_pembayaran', 'Lunas')
                    ->where('status_pesanan', 'Menunggu Diproses')
                    ->count();

        return response()->json([
            'sidik'  => md5(($ringkas->jumlah ?? 0) . '|' . ($ringkas->terakhir ?? '')),
            'baru'   => $baru,
        ]);
    }

    /**
     * Mengubah objek Pesanan menjadi array sederhana
     * sesuai struktur yang dipakai kartu antrean di view.
     */
    private function petakan($koleksi)
    {
        return $koleksi->values()->map(function ($p) {
            return [
                'id_pesanan' => $p->id_pesanan,
                'id'         => $p->no_antrean,
                'meja'       => $p->meja->nomor_meja ?? '-',
                'pemesan'    => $p->pelanggan->nama_pemesan ?? '-',
                'waktu'      => optional($p->tgl_bayar)->format('H:i') ?? '-',
                'selesai'    => optional($p->tgl_selesai)->format('H:i') ?? '-',
                // Waktu pesanan dinyatakan siap, dipakai penghitung waktu
                // tunggu di sisi peramban agar angkanya berjalan sendiri.
                'siap_pada'  => optional($p->tgl_selesai)->toIso8601String(),
                'menu'       => $p->detail->map(function ($d) {
                    return ($d->menu->nama_menu ?? 'Menu dihapus') . ' x' . $d->quantity;
                })->all(),
            ];
        });
    }

    private function petakanRiwayat($koleksi)
    {
        return $koleksi->values()->map(function ($p) {
            return [
                'id'     => $p->no_antrean,
                'waktu'  => optional($p->tgl_bayar)->format('H:i') ?? '-',
                'menu'   => $p->detail->map(fn ($d) => ($d->menu->nama_menu ?? '-') . ' x' . $d->quantity)->implode(', '),
                'meja'   => $p->meja->nomor_meja ?? '-',
            ];
        });
    }

    /**
     * Barista menekan "Mulai Proses": Menunggu Diproses -> Diproses
     */
    public function mulaiProses(Request $request, $id)
    {
        $pesanan = Pesanan::query()->findOrFail($id);

        if ($pesanan->status_pembayaran !== 'Lunas') {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Pesanan belum lunas, tidak bisa diproses.'], 422);
            }
            return redirect()->back()->with('error', 'Pesanan belum lunas, tidak bisa diproses.');
        }

        $pesanan->update(['status_pesanan' => 'Diproses']);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'id_pesanan' => $pesanan->id_pesanan, 'no_antrean' => $pesanan->no_antrean]);
        }

        return redirect()->back()->with('success', 'Pesanan ' . $pesanan->no_antrean . ' mulai diproses.');
    }

    /**
     * Barista menekan "Tandai Selesai": Diproses -> Siap Diambil (+ notifikasi FCM)
     */
    public function tandaiSelesai(Request $request, $id)
    {
        $pesanan = Pesanan::query()->with('pelanggan')->findOrFail($id);

        // Tabel 4.21 (Kasus Data Salah): pesanan yang belum lunas tidak
        // boleh berpindah status. Penjagaan yang sama sudah ada pada
        // mulaiProses(), jadi seluruh perpindahan status kini konsisten.
        if ($pesanan->status_pembayaran !== 'Lunas') {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Pesanan belum lunas, status tidak dapat diubah.'], 422);
            }
            return redirect()->back()->with('error', 'Pesanan belum lunas, status tidak dapat diubah.');
        }

        $pesanan->update([
            'status_pesanan' => 'Siap Diambil',
            'tgl_selesai'    => now()
        ]);

        $this->kirimNotifikasi($pesanan);

        if ($request->wantsJson()) {
            return response()->json([
                'status'     => 'success',
                'id_pesanan' => $pesanan->id_pesanan,
                'no_antrean' => $pesanan->no_antrean,
                'siap_pada'  => optional($pesanan->tgl_selesai)->toIso8601String(),
                'selesai'    => optional($pesanan->tgl_selesai)->format('H:i'),
            ]);
        }

        return redirect()->back()->with('success', 'Pesanan ' . $pesanan->no_antrean . ' siap diambil.');
    }

    /**
     * Barista menyerahkan pesanan ke pelanggan: Siap Diambil -> Selesai.
     *
     * Langkah ini yang menutup siklus pesanan. Tanpanya, pesanan akan
     * menggantung selamanya di kolom "Siap Diambil" dan riwayat
     * kunjungan pelanggan tidak pernah berakhir.
     */
    public function tandaiDiambil(Request $request, $id)
    {
        $pesanan = Pesanan::query()->findOrFail($id);

        if ($pesanan->status_pesanan !== 'Siap Diambil') {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Pesanan belum siap diambil.'], 422);
            }
            return redirect()->back()->with('error', 'Pesanan belum siap diambil.');
        }

        $pesanan->update(['status_pesanan' => 'Selesai']);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'id_pesanan' => $pesanan->id_pesanan, 'no_antrean' => $pesanan->no_antrean]);
        }

        return redirect()->back()->with('success', 'Pesanan ' . $pesanan->no_antrean . ' telah diserahkan.');
    }

    /**
     * Memicu Web Push Notification lewat FCM.
     *
     * Dibungkus pengecekan class_exists supaya dashboard tetap jalan
     * walaupun paket kreait/laravel-firebase belum terpasang.
     */
    private function kirimNotifikasi(Pesanan $pesanan): void
    {
        $judul = 'Pesanan Anda Siap!';
        $pesan = 'Hai ' . ($pesanan->pelanggan->nama_pemesan ?? 'Pelanggan')
                 . ', pesanan Anda sudah siap diambil di Bar.';

        // Catat dulu sebagai Pending (tabel notifikasi), lalu perbarui
        // statusnya sesuai hasil pengiriman. Ini yang membuat riwayat
        // notifikasi bisa ditelusuri sesuai Class Diagram.
        $catatan = Notifikasi::query()->create([
            'id_pesanan'             => $pesanan->id_pesanan,
            'id_pelanggan_sementara' => $pesanan->id_pelanggan_sementara,
            'judul'                  => $judul,
            'pesan'                  => $pesan,
            'status'                 => 'Pending',
        ]);

        $token = $pesanan->pelanggan->token_subscription ?? null;

        if (!$token) {
            // Pelanggan tidak mengizinkan notifikasi (Ext 1A SKPL-F-17):
            // proses berhenti tanpa error.
            $catatan->update(['status' => 'Gagal']);
            return;
        }

        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification([
                    'title' => $judul,
                    'body'  => $pesan,
                ]);

            Firebase::messaging()->send($message);

            $catatan->update([
                'status'       => 'Terkirim',
                'dikirim_pada' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim notifikasi FCM: ' . $e->getMessage());
            $catatan->update(['status' => 'Gagal']);
        }
    }
}