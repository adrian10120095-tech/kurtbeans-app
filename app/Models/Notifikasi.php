<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'id_pelanggan_sementara',
        'judul',
        'pesan',
        'status',
        'dikirim_pada',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function pelanggan()
    {
        return $this->belongsTo(PelangganSementara::class, 'id_pelanggan_sementara', 'id_pelanggan_sementara');
    }
}