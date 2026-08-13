<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Pesanan;
use App\Models\Pembayaran;

class MidtransWebhookController extends Controller
{
    public function handler(Request $request)
    {
        Log::info('Webhook Midtrans masuk: order_id=' . $request->order_id
                  . ' status=' . $request->transaction_status);

        $serverKey = config('services.midtrans.serverKey');

        $hashed = hash('sha512',
            $request->order_id . $request->status_code . $request->gross_amount . $serverKey
        );

        if ($hashed !== $request->signature_key) {
            Log::warning('Webhook Midtrans ditolak: signature tidak valid.');
            return response()->json(['status' => 'invalid signature'], 403);
        }

        $pesanan = Pesanan::query()->where('midtrans_order_id', $request->order_id)->first();

        if (!$pesanan) {
            return response()->json(['status' => 'order not found'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $fraudStatus       = $request->fraud_status;

        if (in_array($transactionStatus, ['capture', 'settlement']) && $fraudStatus !== 'deny') {
            // Hanya update kalau belum lunas, supaya tidak menimpa
            // pesanan yang sudah lebih dulu dikonfirmasi dari browser.
            if ($pesanan->status_pembayaran !== 'Lunas') {
                $pesanan->update([
                    'status_pembayaran' => 'Lunas',
                    'status_pesanan'    => 'Menunggu Diproses',
                    'metode_pembayaran' => $request->payment_type,
                    'tgl_bayar'         => now(),
                ]);
            }
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $pesanan->update(['status_pembayaran' => 'Gagal']);
        }

        Pembayaran::query()->updateOrCreate(
            ['order_id' => $request->order_id],
            [
                'id_pesanan'         => $pesanan->id_pesanan,
                'gross_amount'       => $request->gross_amount,
                'payment_type'       => $request->payment_type,
                'transaction_status' => $transactionStatus,
                'fraud_status'       => $fraudStatus,
            ]
        );

        Log::info('Webhook Midtrans selesai diproses untuk ' . $request->order_id);

        return response()->json(['status' => 'ok']);
    }
}