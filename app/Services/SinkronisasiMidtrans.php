<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Transaction;

/**
 * Menyelaraskan status pembayaran lokal dengan status sebenarnya di Midtrans.
 *
 * Kelas ini dipakai bersama oleh halaman pemantauan pelanggan DAN dashboard
 * kasir. Sebelumnya logikanya hanya ada di CustomerController, sehingga status
 * pembayaran hanya ikut terbarui kalau kebetulan pelanggan sedang membuka
 * halaman "Pesanan Saya". Kalau pelanggan sudah menutup ponselnya, kasir tidak
 * pernah melihat perubahannya.
 *
 * Webhook Midtrans tetap menjadi jalur utama di server produksi. Kelas ini
 * adalah jaring pengaman bila webhook tidak sampai — misalnya saat aplikasi
 * dijalankan di localhost, atau ketika alamat ngrok berubah dan pengaturan
 * di dashboard Midtrans belum diperbarui.
 */
class SinkronisasiMidtrans
{
    /** Jeda minimal pengecekan ulang untuk satu pesanan (detik). */
    private const JEDA_DETIK = 15;

    private function siapkan(): void
    {
        Config::$serverKey    = config('services.midtrans.serverKey');
        Config::$isProduction = (bool) config('services.midtrans.isProduction');
        Config::$isSanitized  = (bool) config('services.midtrans.isSanitized');
        Config::$is3ds        = (bool) config('services.midtrans.is3ds');
    }

    /**
     * @param  iterable<Pesanan>  $daftarPesanan
     * @return int  Jumlah pesanan yang statusnya berubah.
     */
    public function selaraskan($daftarPesanan): int
    {
        $berubah = 0;

        foreach ($daftarPesanan as $pesanan) {
            if (!$this->perluDicek($pesanan)) {
                continue;
            }

            $kunci = 'cek-midtrans-' . $pesanan->id_pesanan;
            if (Cache::has($kunci)) {
                continue;
            }
            Cache::put($kunci, true, self::JEDA_DETIK);

            $this->siapkan();

            try {
                $status = Transaction::status($pesanan->midtrans_order_id);
            } catch (\Throwable $e) {
                Log::warning('Sinkronisasi Midtrans gagal untuk pesanan #'
                             . $pesanan->id_pesanan . ': ' . $e->getMessage());
                continue;
            }

            if ($this->terapkan($pesanan, $status)) {
                $berubah++;
            }
        }

        return $berubah;
    }

    private function perluDicek(Pesanan $pesanan): bool
    {
        if ($pesanan->status_pembayaran !== 'Belum Lunas') return false;
        if (empty($pesanan->midtrans_order_id))            return false;

        // Pesanan lebih tua dari sehari dianggap kedaluwarsa.
        if ($pesanan->tgl_pesan && $pesanan->tgl_pesan->lt(now()->subDay())) return false;

        return true;
    }

    private function terapkan(Pesanan $pesanan, $status): bool
    {
        $transactionStatus = $status->transaction_status ?? null;
        $fraudStatus       = $status->fraud_status ?? null;
        $paymentType       = $status->payment_type ?? 'Midtrans';

        $lunas = in_array($transactionStatus, ['capture', 'settlement'])
                 && $fraudStatus !== 'deny';

        if ($lunas) {
            $pesanan->update([
                'status_pembayaran' => 'Lunas',
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

            Log::info('Pesanan #' . $pesanan->id_pesanan . ' ditandai Lunas lewat sinkronisasi ('
                      . $transactionStatus . ' / ' . $paymentType . ').');

            return true;
        }

        if (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $pesanan->update(['status_pembayaran' => 'Gagal']);
            return true;
        }

        return false;
    }
}