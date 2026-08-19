<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    public $timestamps = true;

    protected $fillable = ['id_kategori', 'nama_menu', 'harga', 'gambar', 'status_menu'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Baris rincian pesanan yang memuat menu ini.
     *
     * Relasi ini dibutuhkan AdminController@index untuk menghitung
     * "Menu Terlaris" lewat withSum('detailPesanan', 'quantity').
     * Sebelumnya relasi ini TIDAK ADA, sehingga withSum() melempar
     * BadMethodCallException, ditangkap try/catch di controller, dan
     * panel Menu Terlaris selalu tampil kosong tanpa pesan apa pun.
     */
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_menu', 'id_menu');
    }
}