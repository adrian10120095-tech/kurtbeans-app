<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    public $timestamps = true;

    protected $fillable = ['id_kategori', 'nama_menu', 'harga', 'gambar', 'status_menu'];

    public function kategori() {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}