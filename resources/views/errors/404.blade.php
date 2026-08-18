<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan &mdash; Kurtbeans Coffee</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink:       '#201612',
                        ink2:      '#6E6157',
                        paper:     '#FBF7F1',
                        line:      '#ECE4DA',
                        ember:     '#B4531C',
                        emberSoft: '#F6EBE0',
                        blush:     '#F7EAE1',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                },
            },
        };
    </script>

    <style>
        body { background-color: #FBF7F1; color: #201612; }

        /* Bingkai garis ganda meniru stempel logo Kurtbeans. */
        .stempel {
            border: 2px solid #201612;
            box-shadow: 0 0 0 3px #FBF7F1, 0 0 0 5px #201612;
        }
    </style>
</head>
<body class="font-sans antialiased">

    <main class="flex min-h-screen items-center justify-center px-5 py-12">
        <div class="w-full max-w-md text-center">

            <div class="mx-auto mb-8 flex h-20 w-20 items-center justify-center rounded-2xl bg-emberSoft">
                <i class="fa-solid fa-qrcode text-3xl text-ember"></i>
            </div>

            <p class="stempel mx-auto mb-7 inline-block rounded-xl px-5 py-2 font-mono text-sm font-bold tracking-[0.2em]">
                404
            </p>

            <h1 class="text-2xl font-extrabold leading-tight sm:text-3xl">
                Halaman Tidak Ditemukan
            </h1>

            <p class="mt-3 text-sm leading-relaxed text-ink2">
                @if(isset($exception) && $exception->getMessage())
                    {{ $exception->getMessage() }}
                @else
                    Tautan yang Anda buka tidak tersedia. Pastikan Anda memindai QR Code
                    yang tertempel di meja, lalu coba sekali lagi.
                @endif
            </p>

            <div class="mt-8 rounded-2xl border border-dashed border-line bg-white px-5 py-6 text-left">
                <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-ink2">
                    Yang bisa Anda lakukan
                </p>
                <ul class="mt-3 space-y-2 text-sm text-ink2">
                    <li class="flex gap-3">
                        <i class="fa-solid fa-camera mt-1 text-ember"></i>
                        <span>Pindai ulang QR Code di meja Anda.</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-mug-hot mt-1 text-ember"></i>
                        <span>Bila QR Code rusak atau sobek, beri tahu barista di konter.</span>
                    </li>
                </ul>
            </div>

            <p class="mt-8 font-mono text-[11px] uppercase tracking-[0.25em] text-ink2/60">
                Kurtbeans Coffee
            </p>

        </div>
    </main>

</body>
</html>