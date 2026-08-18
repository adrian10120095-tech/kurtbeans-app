<?php

namespace App\Support;

use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * HARI OPERASIONAL
 * ================
 * Kurtbeans beroperasi pukul 18.00 - 02.00, sehingga satu malam kerja
 * melewati pergantian tanggal kalender. Kelas ini menerjemahkan waktu
 * nyata menjadi "hari operasional" supaya seluruh laporan, antrean, dan
 * dashboard memakai satuan waktu yang sama dengan cara kerja kedai.
 *
 * Contoh dengan batas pukul 12.00:
 *
 *   Waktu pesanan            Tanggal kalender   Hari operasional
 *   22 Jul 19.00             22 Jul             22 Jul   <- malam yang sama
 *   23 Jul 01.30             23 Jul             22 Jul   <- malam yang sama
 *   23 Jul 19.00             23 Jul             23 Jul   <- malam berikutnya
 *
 * CATATAN PENTING:
 * Kelas ini bergantung pada zona waktu aplikasi. Pastikan config/app.php
 * memakai 'Asia/Jakarta' (APP_TIMEZONE=Asia/Jakarta di .env). Kalau masih
 * UTC, batas pukul 12.00 akan jatuh pukul 19.00 WIB - tepat di tengah jam
 * operasional - dan datanya justru terbelah lebih parah.
 */
class HariOperasional
{
    /** Jam pergantian hari operasional (0-23). */
    public static function jamBatas(): int
    {
        return (int) config('kurtbeans.jam_batas_operasional', 12);
    }

    /**
     * Tanggal operasional untuk sebuah waktu.
     * Caranya: mundurkan waktu sebanyak jam batas, lalu ambil tanggalnya.
     * Pesanan pukul 01.30 mundur 12 jam jadi pukul 13.30 hari sebelumnya,
     * sehingga tetap tercatat pada malam kerja yang benar.
     */
    public static function tanggal($waktu = null): Carbon
    {
        $waktu = $waktu ? Carbon::parse($waktu) : now();

        return $waktu->copy()->subHours(self::jamBatas())->startOfDay();
    }

    /**
     * Rentang waktu nyata [mulai, selesai) untuk satu hari operasional.
     * Contoh: 22 Jul pukul 12.00 sampai 23 Jul pukul 12.00.
     *
     * Batas akhir bersifat EKSKLUSIF supaya tidak ada pesanan yang
     * terhitung di dua hari sekaligus.
     */
    public static function rentang($waktu = null): array
    {
        $mulai = self::tanggal($waktu)->addHours(self::jamBatas());

        return [$mulai, $mulai->copy()->addDay()];
    }

    /**
     * Membatasi sebuah query ke satu hari operasional.
     *
     * Pemakaian:
     *   HariOperasional::batasi(Pesanan::query())->count();
     *   HariOperasional::batasi(Pesanan::query(), 'tgl_selesai')->get();
     */
    public static function batasi($query, string $kolom = 'tgl_pesan', $waktu = null)
    {
        [$mulai, $selesai] = self::rentang($waktu);

        return $query->where($kolom, '>=', $mulai)
                     ->where($kolom, '<', $selesai);
    }

    /**
     * Label untuk ditampilkan di layar, misalnya "Malam 22 Jul 2026".
     */
    public static function label($waktu = null): string
    {
        return self::tanggal($waktu)->translatedFormat('d M Y');
    }

    /**
     * id_pesanan terkecil pada satu hari operasional.
     *
     * Dipakai untuk mereset nomor antrean tiap malam tanpa menambah kolom
     * baru di database: nomor antrean = selisih id terhadap pesanan pertama
     * malam itu. Karena id_pesanan bersifat AUTO_INCREMENT dan tidak pernah
     * berubah, nomor antrean seorang pelanggan TIDAK akan bergeser walaupun
     * ada pesanan lain yang dibatalkan.
     *
     * Hasilnya disimpan di cache karena nilainya tetap sepanjang malam.
     */
    public static function idPesananPertama($waktu = null): int
    {
        [$mulai, $selesai] = self::rentang($waktu);
        $kunci = 'kurtbeans_antrean_awal_' . $mulai->format('Ymd');

        $tersimpan = Cache::get($kunci);
        if ($tersimpan) {
            return (int) $tersimpan;
        }

        $terkecil = Pesanan::query()
                        ->where('tgl_pesan', '>=', $mulai)
                        ->where('tgl_pesan', '<', $selesai)
                        ->min('id_pesanan');

        // Hanya disimpan bila malam itu memang sudah ada pesanan. Kalau
        // belum ada, jangan sampai nilai kosong ikut tersimpan di cache.
        if ($terkecil) {
            Cache::put($kunci, $terkecil, $selesai);
        }

        return (int) ($terkecil ?: 0);
    }
}