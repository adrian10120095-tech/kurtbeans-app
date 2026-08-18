<?php

/*
|--------------------------------------------------------------------------
| Pesan Autentikasi Bahasa Indonesia
|--------------------------------------------------------------------------
|
| Kunci 'failed' dipanggil oleh App\Http\Requests\Auth\LoginRequest melalui
| trans('auth.failed') ketika Auth::attempt() gagal. Kalimatnya disamakan
| persis dengan Tabel 3.18 Skenario Use Case Login, Extension 2A:
| "Username atau Password salah".
|
*/

return [

    'failed'   => 'Username atau Password salah.',
    'password' => 'Kata sandi yang dimasukkan salah.',
    'throttle' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam :seconds detik.',

];