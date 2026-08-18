<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\BaristaController;
use App\Http\Controllers\MidtransWebhookController;

// Rute Default (Halaman Utama)
Route::get('/', function () {
    return redirect('/login');
});

// --- FLOW PELANGGAN ---
Route::get('/order', [CustomerController::class, 'scanQr'])->name('scan');
Route::get('/menu', [CustomerController::class, 'menu'])->name('customer.menu');
Route::post('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
Route::post('/simpan-fcm', [CustomerController::class, 'simpanTokenFCM']);
// Dipanggil browser setelah Snap melaporkan pembayaran berhasil.
// Server memverifikasi ulang ke Midtrans sebelum menandai Lunas.
Route::post('/konfirmasi-pembayaran', [CustomerController::class, 'konfirmasiPembayaran'])->name('customer.konfirmasi');

// Pemantauan pesanan oleh pelanggan (SKPL-F-6)
Route::get('/pesanan-saya', [CustomerController::class, 'statusPesanan'])->name('customer.status');
Route::get('/pesanan-saya/data', [CustomerController::class, 'statusPesananJson'])->name('customer.status.data');
// Melanjutkan pembayaran pesanan yang sudah dibuat tetapi belum dibayar
// (mis. pelanggan menutup Snap tanpa membayar). Membuat Snap token baru.
Route::post('/pesanan-saya/bayar', [CustomerController::class, 'lanjutkanPembayaran'])->name('customer.lanjutkan');

// --- WEBHOOK MIDTRANS ---
// Dipanggil server Midtrans, bukan peramban. Alamatnya harus sama
// dengan pengecualian CSRF di bootstrap/app.php dan dengan
// "Payment Notification URL" di dashboard Midtrans.
Route::post('/api/midtrans-webhook', [MidtransWebhookController::class, 'handler'])
        ->name('midtrans.webhook');

// --- DASHBOARD INTERNAL (Dilindungi Breeze + Role) ---
Route::middleware('auth')->group(function () {
    
    // Penengah (Dispatcher) untuk rute 'dashboard' bawaan Breeze
    Route::get('/dashboard', function () {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        $role = $user->role;
        
        if ($role === 'Admin') return redirect()->route('admin.dashboard');
        if ($role === 'Kasir') return redirect()->route('kasir.dashboard');
        if ($role === 'Barista') return redirect()->route('barista.dashboard');
        return abort(403);
    })->name('dashboard');

    // Rute Admin (LENGKAP DENGAN SEMUA CRUD)
    Route::middleware('role:Admin')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        
        // CRUD Pengguna (Pegawai)
        Route::post('/pengguna', [AdminController::class, 'storePengguna'])->name('admin.pengguna.store');
        Route::put('/pengguna/{id}', [AdminController::class, 'updatePengguna'])->name('admin.pengguna.update');
        Route::delete('/pengguna/{id}', [AdminController::class, 'destroyPengguna'])->name('admin.pengguna.destroy');
        
        // CRUD Kategori
        Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('admin.kategori.store');
        Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('admin.kategori.update');
        Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('admin.kategori.destroy');
        
        // CRUD Menu
        Route::post('/menu', [AdminController::class, 'storeMenu'])->name('admin.menu.store');
        Route::put('/menu/{id}', [AdminController::class, 'updateMenu'])->name('admin.menu.update');
        Route::delete('/menu/{id}', [AdminController::class, 'destroyMenu'])->name('admin.menu.destroy');
        
        // CRUD Meja & QR Code
        Route::post('/meja', [AdminController::class, 'storeMeja'])->name('admin.meja.store');
        Route::put('/meja/{id}', [AdminController::class, 'updateMeja'])->name('admin.meja.update');
        Route::delete('/meja/{id}', [AdminController::class, 'destroyMeja'])->name('admin.meja.destroy');
        Route::get('/meja/{id}/qr', [AdminController::class, 'generateQR'])->name('admin.meja.qr');
    });

    // Rute Kasir
    Route::middleware('role:Kasir')->prefix('kasir')->group(function () {
        Route::get('/dashboard', [KasirController::class, 'index'])->name('kasir.dashboard');
        Route::get('/sinyal', [KasirController::class, 'sinyal'])->name('kasir.sinyal');
        // Form di blade memakai @method('PATCH'), jadi rutenya harus PATCH.
        Route::patch('/validasi/{id}', [KasirController::class, 'validasiTunai'])->name('kasir.validasi');
        Route::patch('/batalkan/{id}', [KasirController::class, 'batalkanPesanan'])->name('kasir.batalkan');
    });

    // Rute Barista
    Route::middleware('role:Barista')->prefix('barista')->group(function () {
        Route::get('/dashboard', [BaristaController::class, 'index'])->name('barista.dashboard');
        Route::get('/sinyal', [BaristaController::class, 'sinyal'])->name('barista.sinyal');
        Route::patch('/proses/{id}', [BaristaController::class, 'mulaiProses'])->name('barista.proses');
        Route::patch('/selesai/{id}', [BaristaController::class, 'tandaiSelesai'])->name('barista.selesai');
        Route::patch('/diambil/{id}', [BaristaController::class, 'tandaiDiambil'])->name('barista.diambil');
    });

        // Tombol toggle status menu (Tersedia/Habis)
    Route::patch('/admin/menu/{id}/toggle-status', [AdminController::class, 'toggleStatusMenu'])
        ->name('admin.menu.toggle');
});

require __DIR__.'/auth.php'; // Rute otentikasi bawaan Breeze