<?php

/*
|--------------------------------------------------------------------------
| Pesan Validasi Bahasa Indonesia
|--------------------------------------------------------------------------
|
| Berkas ini menerjemahkan pesan kesalahan validasi bawaan Laravel ke dalam
| Bahasa Indonesia. Blok 'custom' di bagian bawah berisi pesan khusus yang
| kalimatnya disamakan persis dengan Extension pada Skenario Use Case di
| BAB 3 skripsi (Tabel 3.12, 3.20, 3.21, dan 3.22).
|
| Agar berkas ini terpakai, pastikan berkas .env memuat:
|     APP_LOCALE=id
| lalu jalankan: php artisan config:clear
|
*/

return [

    'accepted'             => 'Kolom :attribute harus disetujui.',
    'after'                => 'Kolom :attribute harus berisi tanggal setelah :date.',
    'after_or_equal'       => 'Kolom :attribute harus berisi tanggal setelah atau sama dengan :date.',
    'alpha'                => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash'           => 'Kolom :attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num'            => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'array'                => 'Kolom :attribute harus berupa larik (array).',
    'before'               => 'Kolom :attribute harus berisi tanggal sebelum :date.',
    'before_or_equal'      => 'Kolom :attribute harus berisi tanggal sebelum atau sama dengan :date.',
    'boolean'              => 'Kolom :attribute harus bernilai benar atau salah.',
    'confirmed'            => 'Konfirmasi :attribute tidak cocok.',
    'current_password'     => 'Kata sandi tidak sesuai.',
    'date'                 => 'Kolom :attribute bukan tanggal yang sah.',
    'date_equals'          => 'Kolom :attribute harus berisi tanggal yang sama dengan :date.',
    'date_format'          => 'Kolom :attribute tidak cocok dengan format :format.',
    'different'            => 'Kolom :attribute dan :other harus berbeda.',
    'digits'               => 'Kolom :attribute harus terdiri dari :digits angka.',
    'digits_between'       => 'Kolom :attribute harus terdiri dari :min sampai :max angka.',
    'email'                => 'Kolom :attribute harus berupa alamat surel yang sah.',
    'exists'               => 'Data :attribute yang dipilih tidak sah.',
    'file'                 => 'Kolom :attribute harus berupa berkas.',
    'filled'               => 'Kolom :attribute wajib diisi.',
    'image'                => 'Kolom :attribute harus berupa gambar.',
    'in'                   => 'Data :attribute yang dipilih tidak sah.',
    'integer'              => 'Kolom :attribute harus berupa bilangan bulat.',
    'ip'                   => 'Kolom :attribute harus berupa alamat IP yang sah.',
    'json'                 => 'Kolom :attribute harus berupa berkas JSON yang sah.',
    'mimes'                => 'Kolom :attribute harus berupa berkas berjenis: :values.',
    'mimetypes'            => 'Kolom :attribute harus berupa berkas berjenis: :values.',
    'not_in'               => 'Data :attribute yang dipilih tidak sah.',
    'numeric'              => 'Kolom :attribute harus berupa angka.',
    'present'              => 'Kolom :attribute harus ada.',
    'regex'                => 'Format kolom :attribute tidak sah.',
    'required'             => 'Kolom :attribute wajib diisi.',
    'required_if'          => 'Kolom :attribute wajib diisi bila :other bernilai :value.',
    'required_with'        => 'Kolom :attribute wajib diisi bila terdapat :values.',
    'required_without'     => 'Kolom :attribute wajib diisi bila tidak terdapat :values.',
    'same'                 => 'Kolom :attribute dan :other harus sama.',
    'string'               => 'Kolom :attribute harus berupa teks.',
    'unique'               => 'Data :attribute sudah digunakan.',
    'uploaded'             => 'Berkas :attribute gagal diunggah.',
    'url'                  => 'Format kolom :attribute tidak sah.',

    'min' => [
        'numeric' => 'Kolom :attribute minimal bernilai :min.',
        'file'    => 'Berkas :attribute minimal berukuran :min kilobita.',
        'string'  => 'Kolom :attribute minimal terdiri dari :min karakter.',
        'array'   => 'Kolom :attribute minimal terdiri dari :min data.',
    ],

    'max' => [
        'numeric' => 'Kolom :attribute maksimal bernilai :max.',
        'file'    => 'Berkas :attribute maksimal berukuran :max kilobita.',
        'string'  => 'Kolom :attribute maksimal terdiri dari :max karakter.',
        'array'   => 'Kolom :attribute maksimal terdiri dari :max data.',
    ],

    'between' => [
        'numeric' => 'Kolom :attribute harus bernilai antara :min dan :max.',
        'file'    => 'Berkas :attribute harus berukuran antara :min sampai :max kilobita.',
        'string'  => 'Kolom :attribute harus terdiri antara :min sampai :max karakter.',
        'array'   => 'Kolom :attribute harus terdiri antara :min sampai :max data.',
    ],

    'size' => [
        'numeric' => 'Kolom :attribute harus bernilai :size.',
        'file'    => 'Berkas :attribute harus berukuran :size kilobita.',
        'string'  => 'Kolom :attribute harus terdiri dari :size karakter.',
        'array'   => 'Kolom :attribute harus terdiri dari :size data.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pesan Validasi Khusus
    |--------------------------------------------------------------------------
    |
    | Kalimat di bawah ini disamakan persis dengan Extension pada Skenario
    | Use Case di BAB 3, sehingga pesan yang muncul di aplikasi sama dengan
    | yang tertulis pada naskah skripsi.
    |
    */

    'custom' => [

        // Tabel 3.12 — Melakukan Checkout, Extension 3A
        'nama_pemesan' => [
            'required' => 'Nama wajib diisi.',
        ],

        // Tabel 3.20 — Mengelola Data Kategori, Extension 3A
        'nama_kategori' => [
            'required' => 'Nama kategori wajib diisi.',
            'unique'   => 'Kategori sudah ada.',
        ],

        // Tabel 3.21 — Mengelola Data Meja, Extension 3A
        'nomor_meja' => [
            'required' => 'Nomor meja wajib diisi.',
            'integer'  => 'Nomor meja harus berupa angka.',
            'unique'   => 'Meja sudah terdaftar.',
        ],

        // Tabel 3.22 — Mengelola Data Pengguna, Extension 3A
        'username' => [
            'required' => 'Username wajib diisi.',
            'unique'   => 'Username sudah digunakan.',
        ],

        // Tabel 3.19 — Mengelola Data Menu, Extension 3A
        'nama_menu' => [
            'required' => 'Nama menu wajib diisi.',
        ],
        'harga' => [
            'required' => 'Harga wajib diisi.',
            'numeric'  => 'Harga harus berupa angka.',
        ],
        'id_kategori' => [
            'required' => 'Kategori wajib dipilih.',
            'exists'   => 'Kategori yang dipilih tidak ditemukan.',
        ],
        'gambar' => [
            'image' => 'Berkas yang diunggah harus berupa gambar.',
            'mimes' => 'Format gambar tidak sesuai, gunakan JPG atau PNG.',
            'max'   => 'Ukuran gambar maksimal 2 MB.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nama Kolom
    |--------------------------------------------------------------------------
    |
    | Mengganti nama kolom teknis menjadi istilah yang dipahami pengguna
    | ketika muncul di dalam pesan kesalahan.
    |
    */

    'attributes' => [
        'nama_pemesan'  => 'Nama Pemesan',
        'nama_menu'     => 'Nama Menu',
        'nama_kategori' => 'Nama Kategori',
        'nama_lengkap'  => 'Nama Lengkap',
        'nomor_meja'    => 'Nomor Meja',
        'id_kategori'   => 'Kategori',
        'status_menu'   => 'Status Menu',
        'status_meja'   => 'Status Meja',
        'harga'         => 'Harga',
        'gambar'        => 'Gambar',
        'username'      => 'Username',
        'password'      => 'Kata Sandi',
        'role'          => 'Peran',
        'metode'        => 'Metode Pembayaran',
        'cart'          => 'Keranjang',
        'fcm_token'     => 'Token Notifikasi',
        'id_pesanan'    => 'ID Pesanan',
    ],

];