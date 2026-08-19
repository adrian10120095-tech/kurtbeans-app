<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = ['id_pesanan', 'id_menu', 'quantity', 'subtotal', 'catatan'];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
    }

    /**
     * Pesanan induk dari baris rincian ini.
     *
     * Dipakai AdminController@index untuk menyaring perhitungan
     * "Menu Terlaris" agar hanya menghitung pesanan yang berstatus
     * "Lunas" pada bulan berjalan — pesanan yang dibatalkan atau
     * belum dibayar tidak ikut dihitung sebagai penjualan.
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}