<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CekRole::class,
        ]);
        
        // Saat aplikasi dijalankan di balik ngrok (atau reverse proxy lain),
        // Laravel perlu mempercayai header X-Forwarded-Proto agar url()
        // menghasilkan alamat https. Tanpa ini, tautan dan QR Code akan
        // tetap memakai http sehingga Snap, Service Worker, dan kamera
        // HP menolak memuatnya.
        $middleware->trustProxies(at: '*');

        // Pengecualian CSRF untuk Webhook Midtrans
        $middleware->validateCsrfTokens(except: [
            'api/midtrans-webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();