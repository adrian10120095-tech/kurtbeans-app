<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['username' => 'admin', 'password' => Hash::make('12345678'), 'nama_lengkap' => 'Admin Utama', 'role' => 'Admin'],
            ['username' => 'kasir', 'password' => Hash::make('12345678'), 'nama_lengkap' => 'Kasir KurtBeans', 'role' => 'Kasir'],
            ['username' => 'barista', 'password' => Hash::make('12345678'), 'nama_lengkap' => 'Barista KurtBeans', 'role' => 'Barista'],
        ];

        foreach ($users as $user) {
            Pengguna::query()->create($user);
        }
    }
}