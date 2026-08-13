<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PelangganSementara extends Model
{
    protected $table = 'pelanggan_sementara';
    protected $primaryKey = 'id_pelanggan_sementara';
    public $timestamps = false;

    protected $fillable = ['nama_pemesan', 'token_subscription', 'session_id'];
}