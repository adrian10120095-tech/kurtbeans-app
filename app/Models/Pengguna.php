<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    protected $table = 'pengguna';
    protected $primaryKey = 'id_user';
    public $timestamps = false; // Menggunakan created_at bawaan

    protected $fillable = [
        'username', 'password', 'nama_lengkap', 'role', 'last_login'
    ];

    protected $hidden = ['password'];
}
