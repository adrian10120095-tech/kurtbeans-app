<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use Carbon\Carbon;
use App\Services\SinkronisasiMidtrans;

class KasirController extends Controller
{
    /**
     * Pesanan yang pembayarannya masih menggantung di Midtrans.
     */
    private function pesananTertunda()
    {
        return Pesanan::query()
                ->where('status_pembayaran', 'Belum Lunas')
                ->whereNotNull('midtrans_order_id')
                ->where('tgl_pesan', '>=', now()->subDay())
                ->get();
    }

    public function index()
    {
        // Selaraskan dulu pesanan yang pembayarannya masih tertunda.
        // Tanpa ini, kasir hanya melihat status terbarui kalau pelanggan
        // kebetulan sedang membuka halaman "Pesanan Saya" di ponselnya.
        (new SinkronisasiMidtrans)->selaraskan($this->pesananTertunda());

        $hariIni = Carbon::today();

        // Definisikan rentang waktu dari jam 00:00:00 sampai 23:59:59
        $awalHari = $hariIni->copy()->startOfDay();
        $akhirHari = $hariIni->copy()->endOfDay();

        // 1. Mengambil Data untuk Stat Cards
        $transaksiHariIni = Pesanan::query()
                                ->where('tgl_pesan', '>=', $awalHari)
                                ->where('tgl_pesan', '<=', $akhirHari)
                                ->count();

        // Hanya pesanan yang pelanggannya MEMILIH bayar tunai saat checkout.
        // Kolom metode_pembayaran kini diisi sejak checkout, bukan setelah
        // pembayaran, sehingga filter ini valid.
        $menungguValidasi = Pesanan::query()
                                ->where('tgl_pesan', '>=', $awalHari)
                                ->where('tgl_pesan', '<=', $akhirHari)
                                ->where('status_pembayaran', 'Belum Lunas')
                                ->where('metode_pembayaran', 'Tunai')
                                ->count();

        $antreanLunas = Pesanan::query()
                            ->where('status_pembayaran', 'Lunas')
                            ->whereIn('status_pesanan', ['Menunggu Diproses', 'Diproses'])
                            ->count();

        $totalTunaiDiterima = Pesanan::query()
                                    ->where('tgl_pesan', '>=', $awalHari)
                                    ->where('tgl_pesan', '<=', $akhirHari)
                                    ->where('status_pembayaran', 'Lunas')
                                    ->where('metode_pembayaran', 'Tunai')
                                    ->sum('total_harga');
        // 2. Mengambil Data untuk Tabel Pantau Transaksi (Menunggu Validasi Tunai & Riwayat Midtrans)
        $semuaTransaksi = Pesanan::query()->with(['meja', 'pelanggan'])
                                 ->where('tgl_pesan', '>=', $awalHari)
                                 ->where('tgl_pesan', '<=', $akhirHari)
                                 ->orderBy('tgl_pesan', 'desc')
                                 ->get();

        // 3. Mengambil Data untuk Panel Antrean Pesanan Lunas
        $antreanPesanan = Pesanan::query()->with(['meja', 'detailPesanan.menu', 'pelanggan'])
                                 ->where('status_pembayaran', 'Lunas')
                                 ->whereIn('status_pesanan', ['Menunggu Diproses', 'Diproses'])
                                 ->orderBy('tgl_bayar', 'asc')
                                 ->get();

        return view('kasir.dashboard', compact(
            'transaksiHariIni', 
            'menungguValidasi', 
            'antreanLunas', 
            'totalTunaiDiterima', 
            'semuaTransaksi', 
            'antreanPesanan'
        ));
    }

    /**
     * Sidik jari ringkas keadaan transaksi hari ini.
     *
     * Dipanggil berkala oleh dashboard untuk mengetahui apakah ada
     * perubahan, tanpa perlu mengirim ulang seluruh data. Halaman baru
     * dimuat ulang bila nilainya berbeda dari sebelumnya.
     */
    public function sinyal()
    {
        // PENTING: sinkronisasi harus dijalankan DI SINI, bukan hanya di
        // index(). Halaman dashboard cuma dimuat ulang kalau sidik jarinya
        // berubah, sedangkan sidik jari hanya berubah kalau status di
        // database sudah diperbarui. Kalau sinkronisasi hanya ada di
        // index(), keduanya saling menunggu dan status tidak pernah
        // berubah tanpa refresh manual.
        //
        // Pemanggilan berulang aman karena SinkronisasiMidtrans membatasi
        // pengecekan satu pesanan maksimal sekali per 15 detik.
        (new SinkronisasiMidtrans)->selaraskan($this->pesananTertunda());

        $awalHari  = Carbon::today()->startOfDay();
        $akhirHari = Carbon::today()->endOfDay();

        $ringkas = Pesanan::query()
                    ->where('tgl_pesan', '>=', $awalHari)
                    ->where('tgl_pesan', '<=', $akhirHari)
                    ->selectRaw('COUNT(*) as jumlah, MAX(updated_at) as terakhir')
                    ->first();

        $belumLunas = Pesanan::query()
                        ->where('tgl_pesan', '>=', $awalHari)
                        ->where('tgl_pesan', '<=', $akhirHari)
                        ->where('status_pembayaran', 'Belum Lunas')
                        ->where('metode_pembayaran', 'Tunai')
                        ->count();

        return response()->json([
            'sidik'            => md5(($ringkas->jumlah ?? 0) . '|' . ($ringkas->terakhir ?? '')),
            'jumlah'           => (int) ($ringkas->jumlah ?? 0),
            'menunggu_validasi'=> $belumLunas,
        ]);
    }

    public function validasiTunai(Request $request, $id)
    {
        // Proses validasi pembayaran tunai manual oleh kasir
        $pesanan = Pesanan::findOrFail($id);

        // Hanya pesanan tunai yang belum lunas yang boleh divalidasi.
        if ($pesanan->metode_pembayaran === 'Tunai' && $pesanan->status_pembayaran === 'Belum Lunas') {
            $pesanan->status_pembayaran = 'Lunas';
            $pesanan->status_pesanan    = 'Menunggu Diproses';
            $pesanan->metode_pembayaran = 'Tunai';
            $pesanan->tgl_bayar = now();
            $pesanan->save();

            return redirect()->route('kasir.dashboard')->with('success', 'Pembayaran tunai untuk Pesanan #' . $pesanan->id_pesanan . ' berhasil divalidasi.');
        }

        return redirect()->route('kasir.dashboard')->with('error', 'Validasi gagal: Pesanan tidak memenuhi syarat.');
    }

    /**
     * Membatalkan pesanan yang belum lunas.
     *
     * Dipakai untuk dua keadaan:
     *   1. Pesanan online yang ditinggalkan pelanggan (agar tidak menumpuk).
     *   2. Pelanggan berubah pikiran soal metode bayar atau membatalkan
     *      pesanannya di kasir.
     *
     * Pesanan yang sudah LUNAS sengaja tidak bisa dibatalkan dari sini,
     * karena pembatalannya menyangkut pengembalian dana (refund) yang
     * berada di luar lingkup sistem.
     */
    public function batalkanPesanan($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        if ($pesanan->status_pembayaran === 'Lunas') {
            return redirect()->route('kasir.dashboard')
                             ->with('error', 'Pesanan yang sudah lunas tidak dapat dibatalkan.');
        }

        $pesanan->update([
            'status_pesanan'    => 'Dibatalkan',
            'status_pembayaran' => 'Gagal',
        ]);

        return redirect()->route('kasir.dashboard')
                         ->with('success', 'Pesanan #' . $pesanan->id_pesanan . ' dibatalkan.');
    }
}