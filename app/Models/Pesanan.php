<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public $timestamps = true;

    protected $fillable = [
        'id_meja', 'id_pelanggan_sementara', 'total_harga', 'status_pesanan', 
        'status_pembayaran', 'metode_pembayaran', 'midtrans_order_id', 
        'tgl_pesan', 'tgl_bayar', 'tgl_selesai'
    ];

    protected $casts = [
        'tgl_pesan'   => 'datetime',
        'tgl_bayar'   => 'datetime',
        'tgl_selesai' => 'datetime',
    ];

    public function detail() {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    // Alias dari detail(). Dipakai oleh view Kasir (detailPesanan.menu)
    // supaya penamaan relasi konsisten di seluruh aplikasi.
    public function detailPesanan() {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function pembayaran() {
        return $this->hasMany(Pembayaran::class, 'id_pesanan', 'id_pesanan');
    }

    public function pelanggan() {
        return $this->belongsTo(PelangganSementara::class, 'id_pelanggan_sementara', 'id_pelanggan_sementara');
    }
    public function meja() {
        return $this->belongsTo(Meja::class, 'id_meja', 'id_meja');
    }

    // Nomor antrean yang ditampilkan ke Kasir/Barista, mis. A012
    public function getNoAntreanAttribute()
{
    $awal = \App\Support\HariOperasional::idPesananPertama($this->tgl_pesan);
    $urut = max(1, $this->id_pesanan - $awal + 1);

    return 'A' . str_pad($urut, 3, '0', STR_PAD_LEFT);
}
}