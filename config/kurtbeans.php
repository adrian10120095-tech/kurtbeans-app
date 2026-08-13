<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Batas Pergantian Hari Operasional
    |--------------------------------------------------------------------------
    |
    | Kurtbeans Coffee beroperasi pukul 18.00 sampai 02.00 dini hari, jadi
    | satu malam kerja MELEWATI tengah malam. Kalau sistem memakai tanggal
    | kalender biasa, pesanan pukul 23.50 dan pukul 00.10 dianggap dua hari
    | berbeda padahal keduanya terjadi pada malam kerja yang sama.
    |
    | Karena itu sistem memakai "hari operasional": satu malam kerja penuh
    | yang bergantinya BUKAN pukul 00.00, melainkan pada jam di bawah ini.
    |
    | Nilai 12 berarti hari operasional berganti pukul 12.00 siang. Angka ini
    | dipilih karena berada di tengah-tengah jam tutup (02.00) dan jam buka
    | (18.00), sehingga masih aman kalau suatu hari kedai tutup lebih larut
    | (misalnya pukul 03.00) atau buka lebih awal (misalnya pukul 16.00).
    |
    | Ubah nilainya hanya bila jam operasional kedai berubah drastis.
    |
    */

    'jam_batas_operasional' => 12,

];