/*
 |--------------------------------------------------------------------------
 | Firebase Cloud Messaging - Service Worker
 |--------------------------------------------------------------------------
 |
 | File ini WAJIB berada di root folder public/ agar cakupannya
 | mencakup seluruh aplikasi. Service worker inilah yang menerima
 | notifikasi ketika tab peramban sedang tidak aktif (background).
 |
 | Konfigurasi di bawah bersifat publik (Firebase Web Config), berbeda
 | dengan Service Account JSON yang harus dirahasiakan.
 |
 */

importScripts('https://www.gstatic.com/firebasejs/9.22.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.1/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyC3OOb_au6qCFnCTD5aBEnzyKd4h_Kd83k",
    authDomain: "kurtbeans-notifikasi-f0a93.firebaseapp.com",
    projectId: "kurtbeans-notifikasi-f0a93",
    messagingSenderId: "806105612585",
    appId: "1:806105612585:web:7fd8614f1b058b265fb738"
});

const messaging = firebase.messaging();

// Dipanggil saat notifikasi tiba dan tab peramban TIDAK sedang aktif.
messaging.onBackgroundMessage(function (payload) {
    console.log('[firebase-messaging-sw.js] Pesan latar belakang diterima:', payload);

    const judul = (payload.notification && payload.notification.title) || 'Kurtbeans Coffee';
    const opsi = {
        body: (payload.notification && payload.notification.body) || '',
        icon: '/images/logo2.png',
        badge: '/images/logo2.png',
        vibrate: [200, 100, 200],
        tag: 'kurtbeans-pesanan',
        requireInteraction: true
    };

    self.registration.showNotification(judul, opsi);
});

// Ketika notifikasi diklik, fokuskan/buka kembali halaman pemesanan.
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (daftar) {
            for (const client of daftar) {
                if ('focus' in client) return client.focus();
            }
            if (clients.openWindow) return clients.openWindow('/menu');
        })
    );
});