<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Meja {{ $meja->nomor_meja }} — Kurtbeans Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                colors: { kurtbeans: { dark: '#1a2b29', cream: '#f4f1e1', brown: '#2b1a10' } }
            } }
        }
    </script>
    <style>
        /* Saat dicetak, hanya area QR yang muncul. */
        @media print {
            body * { visibility: hidden; }
            #area-cetak, #area-cetak * { visibility: visible; }
            #area-cetak { position: absolute; inset: 0; margin: auto; }
            .tanpa-cetak { display: none !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 font-sans text-kurtbeans-dark flex items-center justify-center p-6">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-[28px] shadow-xl border border-gray-100 overflow-hidden">

            <!-- Kepala -->
            <div class="bg-kurtbeans-dark px-8 py-6 text-center text-white">
                <p class="text-[11px] font-bold uppercase tracking-[0.25em] text-white/60">Kurtbeans Coffee</p>
                <h1 class="mt-1 text-xl font-extrabold">Scan untuk Memesan</h1>
            </div>

            <!-- Area yang ikut tercetak -->
            <div id="area-cetak" class="px-8 py-8 flex flex-col items-center">
                <div class="mb-5 text-center">
                    <span class="block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400">Nomor Meja</span>
                    <span class="block text-5xl font-extrabold text-kurtbeans-dark leading-tight">{{ $meja->nomor_meja }}</span>
                </div>

                <!-- QR di tengah, dengan area putih (quiet zone) agar mudah dipindai -->
                <div id="kotak-qr" class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm inline-flex items-center justify-center max-w-full [&>svg]:h-auto [&>svg]:max-w-full">
                    {!! $qrcode !!}
                </div>

                <p class="mt-5 text-center text-xs text-gray-400 break-all max-w-xs">{{ $url }}</p>
            </div>

            <!-- Tombol aksi (tidak ikut tercetak) -->
            <div class="tanpa-cetak px-8 pb-8">
                <div class="grid grid-cols-2 gap-3">
                    <button onclick="unduhPNG()" class="col-span-2 inline-flex items-center justify-center gap-2 rounded-xl bg-kurtbeans-dark hover:bg-gray-800 text-white font-bold py-3.5 transition active:scale-[.98]">
                        <i class="fa-solid fa-download"></i> Unduh QR (PNG)
                    </button>
                    <button onclick="unduhSVG()" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-bold py-3 transition active:scale-[.98]">
                        <i class="fa-solid fa-vector-square"></i> SVG
                    </button>
                    <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-bold py-3 transition active:scale-[.98]">
                        <i class="fa-solid fa-print"></i> Cetak
                    </button>
                </div>
                <button onclick="window.close()" class="mt-3 w-full text-center text-sm font-semibold text-gray-400 hover:text-gray-600 py-2">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function ambilSVG() {
            return document.querySelector('#kotak-qr svg');
        }

        function unduhBlob(blob, nama) {
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = nama;
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(a.href); }, 1000);
        }

        // Unduh sebagai SVG (vektor) — langsung dari SVG yang tampil.
        function unduhSVG() {
            const xml = new XMLSerializer().serializeToString(ambilSVG());
            unduhBlob(new Blob([xml], { type: 'image/svg+xml' }), 'QR-Meja-{{ $meja->nomor_meja }}.svg');
        }

        // Unduh sebagai PNG resolusi tinggi — SVG digambar ke canvas di peramban
        // (tanpa perlu ekstensi server seperti imagick).
        function unduhPNG() {
            const svg = ambilSVG();
            const xml = new XMLSerializer().serializeToString(svg);
            const sumber = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(xml)));
            const gambar = new Image();
            gambar.onload = function () {
                const sisi = 1024; // QR persegi, resolusi tinggi untuk cetak/berbagi
                const canvas = document.createElement('canvas');
                canvas.width = sisi;
                canvas.height = sisi;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, sisi, sisi);
                ctx.drawImage(gambar, 0, 0, sisi, sisi);
                canvas.toBlob(function (blob) {
                    unduhBlob(blob, 'QR-Meja-{{ $meja->nomor_meja }}.png');
                }, 'image/png');
            };
            gambar.onerror = function () {
                alert('Gagal menyiapkan PNG. Silakan gunakan tombol SVG.');
            };
            gambar.src = sumber;
        }
    </script>
</body>
</html>