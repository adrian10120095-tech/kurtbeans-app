<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = true;

    protected $fillable = [
        'id_pesanan',
        'order_id',
        'gross_amount',
        'payment_type',
        'transaction_status',
        'fraud_status',
        'payment_token',
        'payment_url',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}