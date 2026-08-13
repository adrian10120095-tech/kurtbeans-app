<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Pesanan Saya &middot; Kurtbeans Coffee</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA: iOS hanya mengizinkan Web Push bila situs dipasang ke Layar Utama -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#16110D">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Kurtbeans">
    <link rel="apple-touch-icon" href="/images/logo2.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Midtrans Snap JS — untuk melanjutkan pembayaran pesanan yang belum dibayar -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.1/firebase-messaging-compat.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        /* Palet sama persis dengan halaman menu: hitam stempel untuk aksi,
           satu warna hangat (ember) untuk hal yang butuh perhatian pelanggan —
           nomor antrean, harga, dan tahap yang sedang berjalan. */
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink:    '#201612',
                        ink2:   '#6E6157',
                        paper:  '#FBF7F1',
                        line:   '#ECE4DA',
                        ember:  '#B4531C',
                        emberSoft: '#F6EBE0',
                        blush:  '#F7EAE1',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    },
                    boxShadow: {
                        card: '0 1px 2px rgba(32,22,18,.04), 0 8px 24px -12px rgba(32,22,18,.12)',
                        lift: '0 2px 6px rgba(32,22,18,.06), 0 18px 40px -18px rgba(32,22,18,.22)',
                    },
                }
            }
        }
    </script>

    <style>
        :root { --ink:#201612; --line:#ECE4DA; --paper:#FBF7F1; --ember:#B4531C; }

        body { -webkit-tap-highlight-color: transparent; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        @keyframes denyut { 0%, 100% { opacity: 1; } 50% { opacity: .35; } }
        .denyut { animation: denyut 1.8s ease-in-out infinite; }

        @keyframes lingkarNaik { from { transform: scale(.8); opacity: 0 } to { transform: scale(1); opacity: 1 } }
        .naik { animation: lingkarNaik .35s cubic-bezier(.32,.72,0,1) both; }

        /* Cincin berdenyut untuk tahap yang SEDANG berjalan — menarik mata pelanggan */
        @keyframes cincin {
            0%   { box-shadow: 0 0 0 0 rgba(180,83,28,.45); }
            70%  { box-shadow: 0 0 0 10px rgba(180,83,28,0); }
            100% { box-shadow: 0 0 0 0 rgba(180,83,28,0); }
        }
        .cincin { animation: cincin 2s cubic-bezier(.4,0,.2,1) infinite; }

        /* Banner status "siap" yang bernapas pelan */
        @keyframes napas { 0%,100% { transform: scale(1); } 50% { transform: scale(1.015); } }
        .napas { animation: napas 2.4s ease-in-out infinite; }

        /* Tanda "stempel" — mengambil bentuk dari logo Kurtbeans */
        .stempel {
            border: 2px solid var(--ink);
            outline: 2px solid var(--ink);
            outline-offset: 2px;
        }

        /* Garis sobek struk */
        .sobek { border-top: 2px dashed var(--line); }

        :focus-visible { outline: 2px solid var(--ember); outline-offset: 2px; }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; animation: none !important; }
        }
    </style>
</head>
<body class="bg-paper font-sans text-ink antialiased">

<!-- ================= BILAH ATAS ================= -->
<header class="sticky top-0 z-30 border-b border-line bg-blush/95 backdrop-blur">
    <div class="mx-auto flex w-full max-w-4xl items-center gap-3 px-4 py-3.5 sm:px-6 sm:py-4">
        <a href="{{ route('customer.menu') }}" aria-label="Kembali ke menu"
           class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-line bg-white shadow-card transition active:scale-95 hover:border-ember">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="min-w-0 flex-1">
            <h1 class="text-lg font-extrabold leading-tight sm:text-xl">Pesanan saya</h1>
            <p class="mt-1 flex items-center gap-2 text-xs text-ink2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-2 py-0.5 font-semibold text-ember shadow-card">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-ember denyut"></span> Diperbarui otomatis
                </span>
                <span class="hidden sm:inline">&middot; Meja {{ $nomor_meja ?? '-' }}</span>
            </p>
        </div>

        @if($nomor_meja)
        <div class="stempel shrink-0 rounded-lg bg-white px-3 py-1.5 text-center">
            <span class="block text-[9px] font-bold uppercase tracking-[0.2em] leading-none text-ink2">Meja</span>
            <span class="block font-mono text-xl font-bold leading-tight text-ember">{{ $nomor_meja }}</span>
        </div>
        @endif
    </div>
</header>

<div class="mx-auto w-full max-w-4xl px-4 sm:px-6">

    <!-- Panduan khusus iOS: Web Push hanya jalan dari aplikasi Layar Utama -->
    <div id="panduan-ios" style="display:none;" class="mt-5 rounded-2xl border border-line bg-white p-4">
        <div class="flex gap-3">
            <i class="fas fa-bell mt-0.5 text-ember"></i>
            <div class="flex-1">
                <h3 class="text-sm font-bold">Aktifkan notifikasi pesanan</h3>
                <p class="mt-1 text-xs leading-relaxed text-ink2">
                    Di iPhone, notifikasi hanya bisa aktif kalau halaman ini dipasang ke Layar Utama.
                    Ketuk tombol <strong class="text-ink">Bagikan</strong> di bawah Safari, lalu pilih
                    <strong class="text-ink">Tambah ke Layar Utama</strong>. Buka Kurtbeans dari ikon tersebut.
                </p>
                <button type="button" onclick="tutupPanduanIos()" class="mt-2 text-xs font-bold underline underline-offset-2">Mengerti</button>
            </div>
        </div>
    </div>

    <!-- Tombol aktifkan notifikasi (muncul bila izin belum diberikan) -->
    <div id="tombol-notifikasi" style="display:none;" class="mt-5">
        <button type="button" onclick="aktifkanNotifikasi()"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-ink py-3.5 text-sm font-bold text-white transition active:scale-[.98]">
            <i class="fas fa-bell"></i> Aktifkan notifikasi pesanan
        </button>
    </div>

    <!-- ================= DAFTAR PESANAN ================= -->
    <main id="daftar-pesanan" class="flex flex-col gap-5 py-6"
          style="padding-bottom: calc(3rem + env(safe-area-inset-bottom));">

        @forelse($daftarPesanan as $p)
        @php
            $urutan = ['Menunggu Pembayaran', 'Menunggu Diproses', 'Diproses', 'Siap Diambil', 'Selesai'];
            $label  = [
                'Menunggu Pembayaran' => 'Menunggu Bayar',
                'Menunggu Diproses'   => 'Diterima',
                'Diproses'            => 'Diproses',
                'Siap Diambil'        => 'Siap',
                'Selesai'             => 'Diambil',
            ];
            $indeks = array_search($p->status_pesanan, $urutan);
            $batal  = $p->status_pesanan === 'Dibatalkan' || $p->status_pembayaran === 'Gagal';
            $tunai  = $p->metode_pembayaran === 'Tunai';

            // Banner status besar: nada (ready/soft/muted), ikon, judul, keterangan.
            if ($batal) {
                $hero = ['muted', 'fa-circle-xmark', 'Pesanan dibatalkan', 'Pesanan ini tidak diproses.'];
            } elseif ($p->status_pesanan === 'Selesai') {
                $hero = ['muted', 'fa-circle-check', 'Pesanan selesai', 'Sudah diambil. Terima kasih, selamat menikmati!'];
            } elseif ($p->status_pesanan === 'Siap Diambil') {
                $hero = ['ready', 'fa-mug-hot', 'Siap diambil!', 'Silakan ambil pesanan Anda di bar.'];
            } elseif ($p->status_pesanan === 'Diproses') {
                $hero = ['soft', 'fa-blender', 'Sedang dibuat', 'Barista sedang menyiapkan pesanan Anda.'];
            } elseif ($p->status_pesanan === 'Menunggu Diproses') {
                $hero = ['soft', 'fa-circle-check', 'Pesanan diterima', 'Menunggu giliran untuk dibuat barista.'];
            } elseif ($tunai) {
                $hero = ['soft', 'fa-money-bill-wave', 'Menunggu pembayaran', 'Tunjukkan nomor antrean Anda ke kasir.'];
            } else {
                $hero = ['soft', 'fa-clock', 'Menunggu pembayaran', 'Selesaikan pembayaran untuk mulai diproses.'];
            }
        @endphp

        <article class="overflow-hidden rounded-3xl border border-line bg-white shadow-card" data-id="{{ $p->id_pesanan }}">

            {{-- ===== BANNER STATUS BESAR ===== --}}
            @if($hero[0] === 'ready')
                <div class="napas flex items-center gap-4 bg-ink px-5 py-5 text-white">
                    <span class="cincin flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-2xl"><i class="fas {{ $hero[1] }}"></i></span>
                    <div class="min-w-0 flex-1">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-white/60">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-white denyut"></span> Status pesanan
                        </span>
                        <p class="mt-0.5 text-xl font-extrabold leading-tight">{{ $hero[2] }}</p>
                        <p class="mt-0.5 text-sm text-white/75">{{ $hero[3] }}</p>
                    </div>
                </div>
            @elseif($hero[0] === 'soft')
                <div class="flex items-center gap-4 bg-emberSoft px-5 py-5">
                    <span class="cincin flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-2xl text-ember"><i class="fas {{ $hero[1] }}"></i></span>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-ember">Status pesanan</span>
                        <p class="mt-0.5 text-xl font-extrabold leading-tight text-ink">{{ $hero[2] }}</p>
                        <p class="mt-0.5 text-sm text-ink2">{{ $hero[3] }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-4 bg-paper px-5 py-5">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-2xl text-ink2"><i class="fas {{ $hero[1] }}"></i></span>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-ink2">Status pesanan</span>
                        <p class="mt-0.5 text-xl font-extrabold leading-tight text-ink">{{ $hero[2] }}</p>
                        <p class="mt-0.5 text-sm text-ink2">{{ $hero[3] }}</p>
                    </div>
                </div>
            @endif

            <div class="md:grid md:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)]">

                {{-- ----- Kolom kiri: identitas pesanan + lini masa ----- --}}
                <div class="md:border-r md:border-line">

                    {{-- Kepala kartu --}}
                    <div class="flex items-start justify-between gap-4 border-b border-line px-5 py-4">
                        <div>
                            <span class="block text-[10px] font-extrabold uppercase tracking-[0.18em] text-ink2">No. antrean</span>
                            <span class="block font-mono text-4xl font-bold leading-tight text-ember">{{ $p->no_antrean }}</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-sm font-bold">{{ $p->pelanggan->nama_pemesan ?? '-' }}</span>
                            <span class="mt-0.5 block font-mono text-xs text-ink2">{{ optional($p->tgl_pesan)->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    {{-- Lini masa status --}}
                    <div class="px-5 py-5" data-timeline>
                        @if($batal)
                            <div class="flex items-center gap-3 rounded-xl border border-line bg-paper px-4 py-3">
                                <i class="fas fa-circle-xmark text-ink2"></i>
                                <span class="text-sm font-bold">Pesanan dibatalkan</span>
                            </div>
                        @else
                            <div class="flex items-start">
                                @foreach($urutan as $i => $tahap)
                                    @php
                                        $lewat    = $indeks !== false && $i <  $indeks;
                                        $sekarang = $indeks !== false && $i === $indeks;
                                    @endphp
                                    <div class="relative flex flex-1 flex-col items-center">
                                        @if($i > 0)
                                            <div class="absolute right-1/2 top-3.5 h-0.5 w-full {{ ($lewat || $sekarang) ? 'bg-ink' : 'bg-line' }}"></div>
                                        @endif

                                        <div class="relative z-10 flex h-7 w-7 items-center justify-center rounded-full text-[10px]
                                            @if($sekarang) cincin bg-ember text-white
                                            @elseif($lewat) bg-ink text-white
                                            @else border border-line bg-white text-line @endif">
                                            <i class="fas fa-check"></i>
                                        </div>

                                        <span class="mt-2 px-0.5 text-center text-[10px] font-bold leading-tight
                                            @if($sekarang) text-ember @elseif($lewat) text-ink @else text-ink2/50 @endif">
                                            {{ $label[$tahap] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Pesanan ONLINE yang belum dibayar: tombol lanjutkan pembayaran --}}
                    @if(!$batal && $p->status_pembayaran === 'Belum Lunas' && !$tunai)
                    <div class="px-5 pb-5">
                        <button type="button" onclick="lanjutkanPembayaran({{ $p->id_pesanan }}, this)"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-ink py-3.5 font-bold text-white shadow-card transition active:scale-[.98] hover:bg-ember">
                            <i class="fas fa-credit-card text-sm"></i>
                            <span>Lanjutkan pembayaran</span>
                        </button>
                        <p class="mt-2 text-center text-[11px] text-ink2">Ketuk untuk memilih metode &amp; menyelesaikan pembayaran Anda.</p>
                    </div>
                    @endif

                    {{-- Sorotan tambahan hanya untuk kasus bayar tunai di kasir --}}
                    @if(!$batal && $p->status_pembayaran === 'Belum Lunas' && $tunai)
                    <div class="px-5 pb-5">
                        <div class="flex items-center gap-3 rounded-xl border-2 border-ember bg-emberSoft px-4 py-3">
                            <i class="fas fa-money-bill-wave text-ember"></i>
                            <p class="text-xs font-semibold leading-relaxed">
                                Tunjukkan antrean <strong class="font-mono">{{ $p->no_antrean }}</strong> ke kasir untuk membayar.
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- ----- Kolom kanan: rincian item + total ----- --}}
                <div class="flex flex-col border-t border-line md:border-t-0">
                    <div class="flex-1 px-5 py-5">
                        <h3 class="mb-3 text-[10px] font-extrabold uppercase tracking-[0.18em] text-ink2">Rincian pesanan</h3>
                        <div class="flex flex-col gap-2.5" data-item>
                            @foreach($p->detail as $d)
                            <div class="flex items-baseline justify-between gap-3 text-sm">
                                <span class="min-w-0 truncate">{{ $d->menu->nama_menu ?? 'Menu dihapus' }}</span>
                                <span class="shrink-0 font-mono text-xs font-bold text-ink2">&times;{{ $d->quantity }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="sobek mx-5"></div>

                    <div class="flex items-center justify-between gap-3 px-5 py-4">
                        <div class="flex flex-wrap items-center gap-2">
                            @if($p->status_pembayaran === 'Lunas')
                                <span class="rounded-md bg-ink px-2.5 py-1 text-[10px] font-bold tracking-wide text-white">LUNAS</span>
                            @elseif($p->status_pembayaran === 'Gagal')
                                <span class="rounded-md border border-line bg-paper px-2.5 py-1 text-[10px] font-bold tracking-wide text-ink2">GAGAL</span>
                            @else
                                <span class="rounded-md border-2 border-ember px-2.5 py-1 text-[10px] font-bold tracking-wide text-ember">BELUM LUNAS</span>
                            @endif

                            @if($p->metode_pembayaran)
                                <span class="text-[10px] font-semibold text-ink2">{{ ucfirst(str_replace('_', ' ', $p->metode_pembayaran)) }}</span>
                            @endif
                        </div>
                        <span class="font-mono text-lg font-bold text-ember">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </article>

        @empty
        <div class="rounded-3xl border border-line bg-white px-6 py-16 text-center shadow-card">
            <div class="stempel mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emberSoft">
                <i class="fas fa-receipt text-2xl text-ember"></i>
            </div>
            <h2 class="mt-6 text-lg font-extrabold">Belum ada pesanan</h2>
            <p class="mx-auto mt-1.5 max-w-xs text-sm text-ink2">
                Pesanan yang Anda buat dari Meja {{ $nomor_meja ?? '-' }} akan muncul di sini beserta status terkininya.
            </p>
            <a href="{{ route('customer.menu') }}"
               class="mt-6 inline-flex items-center gap-2 rounded-xl bg-ink px-6 py-3.5 text-sm font-bold text-white shadow-card transition active:scale-95 hover:bg-ember">
                <i class="fas fa-mug-hot text-xs"></i> Mulai pesan
            </a>
        </div>
        @endforelse
    </main>
</div>


<!-- ================= PENGUMUMAN "PESANAN SIAP" =================
     Cadangan bila izin push ditolak atau situs belum dipasang sebagai
     aplikasi Layar Utama: pelanggan tetap dapat kabar selama halaman
     ini terbuka. -->
<div id="panel-siap" style="display:none;"
     class="fixed inset-0 z-[9998] flex flex-col items-center justify-center bg-ink px-6 text-center text-white">
    <div class="naik">
        <div class="cincin mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-white/10 border-2 border-white/30">
            <i class="fas fa-mug-hot text-4xl"></i>
        </div>
        <p class="mt-8 flex items-center justify-center gap-2 text-xs font-extrabold uppercase tracking-[0.3em] text-white/60">
            <span class="inline-block h-1.5 w-1.5 rounded-full bg-white denyut"></span> Pesanan siap
        </p>
        <p class="mt-3 text-3xl font-extrabold">Silakan ambil di bar</p>

        <div id="panel-siap-antrean" class="mt-8" style="display:none;">
            <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-white/50">No. antrean</p>
            <p id="panel-siap-nomor" class="font-mono text-6xl font-bold">-</p>
        </div>

        <button type="button" onclick="tutupPanelSiap()"
                class="mt-10 rounded-xl bg-white px-8 py-3.5 font-bold text-ink transition active:scale-95">
            Oke, saya ambil
        </button>
    </div>
</div>


<script>
    /*
       Halaman ini menanyakan status terbaru ke server setiap 10 detik.
       Polling dipilih (bukan WebSocket) karena tidak menambah kebutuhan
       server tambahan, dan pembaruan status pesanan tidak menuntut
       kecepatan sepersekian detik.

       Notifikasi push tetap menjadi pemberitahuan utama; halaman ini
       adalah pelengkap agar pelanggan bisa memeriksa kapan saja.
    */
    let statusTerakhir = {};
    let statusSebelumnya = {};   // status_pesanan terakhir yang sudah dilihat

    document.querySelectorAll('[data-id]').forEach(function (kartu) {
        statusTerakhir[kartu.dataset.id] = null;
    });

    async function segarkanStatus() {
        // Hemat kuota: berhenti memeriksa saat tab tidak dilihat.
        if (document.hidden) return;

        // Jangan muat ulang selagi pengumuman "pesanan siap" masih terbuka.
        if (document.getElementById('panel-siap').style.display === 'flex') return;

        // Jangan muat ulang saat jendela pembayaran (Snap) sedang terbuka.
        if (window.pembayaranSedangDibuka) return;

        try {
            const respons = await fetch('{{ route("customer.status.data") }}', {
                headers: { 'Accept': 'application/json' }
            });
            if (!respons.ok) return;

            const data = await respons.json();
            let adaPerubahan = false;

            (data.pesanan || []).forEach(function (p) {
                const kunci = String(p.id_pesanan);
                const nilai = p.status_pesanan + '|' + p.status_pembayaran;

                if (statusTerakhir[kunci] !== undefined && statusTerakhir[kunci] !== null
                    && statusTerakhir[kunci] !== nilai) {
                    adaPerubahan = true;
                }
                statusTerakhir[kunci] = nilai;
            });

            // Jumlah pesanan bertambah juga dianggap perubahan.
            if ((data.pesanan || []).length !== document.querySelectorAll('[data-id]').length) {
                adaPerubahan = true;
            }

            // Deteksi pesanan yang BARU SAJA berubah jadi "Siap Diambil"
            // supaya bisa diumumkan sebelum halaman dimuat ulang.
            const siap = (data.pesanan || []).find(function (p) {
                const sebelum = statusSebelumnya[String(p.id_pesanan)];
                return p.status_pesanan === 'Siap Diambil'
                    && sebelum && sebelum !== 'Siap Diambil';
            });

            (data.pesanan || []).forEach(function (p) {
                statusSebelumnya[String(p.id_pesanan)] = p.status_pesanan;
            });

            if (siap && typeof window.umumkanPesananSiap === 'function') {
                window.umumkanPesananSiap(siap.no_antrean);
                return; // jangan muat ulang; biar panel tetap terlihat
            }

            if (adaPerubahan) {
                window.location.reload();
            }
        } catch (e) {
            console.error('Gagal memperbarui status:', e);
        }
    }

    // Rekam status awal supaya muat ulang pertama tidak langsung terpicu.
    (async function () {
        try {
            const respons = await fetch('{{ route("customer.status.data") }}', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await respons.json();
            (data.pesanan || []).forEach(function (p) {
                statusTerakhir[String(p.id_pesanan)] = p.status_pesanan + '|' + p.status_pembayaran;
                statusSebelumnya[String(p.id_pesanan)] = p.status_pesanan;
            });
        } catch (e) { /* diabaikan */ }
    })();

    setInterval(segarkanStatus, 10000);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) segarkanStatus();
    });
</script>


<script>
/*
   PENGUMUMAN "PESANAN SIAP"
   =========================
   Dipanggil dari dua tempat: polling status di atas, dan onMessage
   Firebase di bawah. Menampilkan panel layar penuh, membunyikan nada
   dua nada lewat Web Audio API (tanpa file audio), dan menggetarkan
   perangkat bila didukung.
*/
function nadaSiap() {
    try {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return;
        const ctx = new Ctx();

        // Dua nada naik: 880 Hz lalu 1320 Hz.
        [[880, 0], [1320, 0.18]].forEach(function (pasangan) {
            const frekuensi = pasangan[0];
            const jeda      = pasangan[1];
            const mulai     = ctx.currentTime + jeda;

            const osilator = ctx.createOscillator();
            const gain     = ctx.createGain();

            osilator.type = 'sine';
            osilator.frequency.value = frekuensi;

            gain.gain.setValueAtTime(0.0001, mulai);
            gain.gain.exponentialRampToValueAtTime(0.28, mulai + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, mulai + 0.35);

            osilator.connect(gain);
            gain.connect(ctx.destination);
            osilator.start(mulai);
            osilator.stop(mulai + 0.4);
        });
    } catch (e) { /* perangkat tidak mendukung, diabaikan */ }
}

window.umumkanPesananSiap = function (noAntrean) {
    const panel = document.getElementById('panel-siap');
    if (!panel || panel.style.display === 'flex') return;

    const kotakAntrean = document.getElementById('panel-siap-antrean');
    if (noAntrean) {
        document.getElementById('panel-siap-nomor').textContent = noAntrean;
        kotakAntrean.style.display = '';
    } else {
        kotakAntrean.style.display = 'none';
    }

    panel.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    nadaSiap();
    if (navigator.vibrate) navigator.vibrate([180, 90, 180]);
};

// Setelah ditutup, halaman dimuat ulang agar kartu pesanan
// menampilkan status terbaru.
function tutupPanelSiap() {
    document.getElementById('panel-siap').style.display = 'none';
    document.body.style.overflow = '';
    window.location.reload();
}

/*
   PANDUAN iOS
   ===========
   Safari di iPhone tidak mendukung Web Push untuk tab biasa. Panduan
   hanya ditampilkan bila perangkatnya memang iOS DAN situs belum
   dibuka sebagai aplikasi Layar Utama.
*/
function tutupPanduanIos() {
    document.getElementById('panduan-ios').style.display = 'none';
    try { localStorage.setItem('kurtbeans_panduan_ios', '1'); } catch (e) { /* diabaikan */ }
}

(function () {
    const iOS = /iPad|iPhone|iPod/.test(navigator.userAgent)
             || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

    const terpasang = window.navigator.standalone === true
                   || window.matchMedia('(display-mode: standalone)').matches;

    let pernahDitutup = false;
    try { pernahDitutup = localStorage.getItem('kurtbeans_panduan_ios') === '1'; } catch (e) { /* diabaikan */ }

    if (iOS && !terpasang && !pernahDitutup) {
        document.getElementById('panduan-ios').style.display = '';
    }
})();
</script>


<script>
/*
   PENDAFTARAN TOKEN NOTIFIKASI (jaring pengaman)
   ==============================================
   Token juga didaftarkan dari halaman ini, bukan hanya saat checkout.
   Alasannya: di iPhone, aplikasi Layar Utama punya penyimpanan terpisah
   dari tab Safari, sehingga token yang didaftarkan lewat Safari tidak
   berlaku di aplikasi Layar Utama. Halaman ini dijalankan di dalam
   aplikasi itu sendiri, jadi tokennya pasti cocok.

   Baris pelanggan_sementara sudah ada di titik ini (dibuat saat
   checkout), sehingga token langsung bisa dikaitkan.
*/
(function () {
    const firebaseConfig = {
        apiKey: "AIzaSyC3OOb_au6qCFnCTD5aBEnzyKd4h_Kd83k",
        authDomain: "kurtbeans-notifikasi-f0a93.firebaseapp.com",
        projectId: "kurtbeans-notifikasi-f0a93",
        messagingSenderId: "806105612585",
        appId: "1:806105612585:web:7fd8614f1b058b265fb738"
    };
    const VAPID_KEY = "BJUlR6HS5Bf3oOhpvNwU0C5718yapyS6phl3slnopef8nw5urENTVCRPUr59BbQHSc6WMwcfgnE0zUkaJ_Gmy8s";

    async function daftarkanToken(dariTombol) {
        if (!('serviceWorker' in navigator) || !('Notification' in window)) return;
        if (!window.PushManager) return;   // iOS: hanya ada di aplikasi Layar Utama

        try {
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);
            const messaging = firebase.messaging();

            // Notifikasi yang tiba saat halaman ini terbuka
            messaging.onMessage(function (payload) {
                const isi = (payload.notification && payload.notification.body) || '';
                if (typeof window.umumkanPesananSiap === 'function') {
                    window.umumkanPesananSiap('');
                } else {
                    alert(isi);
                }
            });

            if (Notification.permission === 'default' && !dariTombol) return;

            const izin = Notification.permission === 'granted'
                ? 'granted'
                : await Notification.requestPermission();

            if (izin !== 'granted') return;

            const token = await messaging.getToken({
                vapidKey: VAPID_KEY,
                serviceWorkerRegistration: registration
            });
            if (!token) return;

            await fetch('/simpan-fcm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ fcm_token: token })
            });

            const tombol = document.getElementById('tombol-notifikasi');
            if (tombol) tombol.style.display = 'none';
        } catch (e) {
            console.error('Pendaftaran token notifikasi gagal:', e);
        }
    }

    window.aktifkanNotifikasi = function () { daftarkanToken(true); };

    window.addEventListener('load', function () {
        daftarkanToken(false);

        // Tampilkan tombol bila notifikasi memang bisa diaktifkan tapi belum.
        if (window.PushManager && 'Notification' in window
            && Notification.permission !== 'granted') {
            const tombol = document.getElementById('tombol-notifikasi');
            if (tombol) tombol.style.display = 'block';
        }
    });
})();
</script>

<script>
/*
   LANJUTKAN PEMBAYARAN
   ====================
   Untuk pesanan yang sudah dibuat tetapi belum dibayar (mis. pelanggan
   menutup Snap tanpa membayar). Server membuatkan Snap token baru,
   lalu Snap dibuka kembali. Saat Snap terbuka, polling status ditahan
   agar halaman tidak dimuat ulang di tengah pembayaran.
*/
window.pembayaranSedangDibuka = false;

function lanjutkanPembayaran(idPesanan, tombol) {
    const labelAsli = tombol ? tombol.innerHTML : '';
    if (tombol) {
        tombol.disabled = true;
        tombol.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan…';
    }
    const kembalikanTombol = () => {
        if (tombol) { tombol.disabled = false; tombol.innerHTML = labelAsli; }
    };

    fetch('{{ route("customer.lanjutkan") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id_pesanan: idPesanan })
    })
    .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || 'Gagal menyiapkan pembayaran.');
        return data;
    })
    .then((data) => {
        // Sudah lunas / dibatalkan / tunai -> tidak membuka Snap.
        if (data.status === 'sudah_lunas') {
            alert('Pesanan ini sudah lunas.');
            window.location.reload();
            return;
        }
        if (data.status === 'batal') {
            alert('Pesanan ini sudah dibatalkan.');
            window.location.reload();
            return;
        }
        if (data.status === 'tunai') {
            alert('Pembayaran tunai.\nTunjukkan nomor antrean ' + (data.no_antrean || '-') + ' ke kasir untuk membayar.');
            kembalikanTombol();
            return;
        }
        if (data.status !== 'online' || !data.snap_token) {
            alert(data.message || 'Tidak dapat memulai pembayaran. Silakan coba lagi.');
            kembalikanTombol();
            return;
        }

        // Buka Snap. Selama terbuka, polling tidak me-reload halaman.
        window.pembayaranSedangDibuka = true;
        kembalikanTombol();

        window.snap.pay(data.snap_token, {
            onSuccess: function () {
                window.pembayaranSedangDibuka = false;
                konfirmasiPembayaranStatus(data.id_pesanan);
            },
            onPending: function () {
                // QRIS / VA / e-wallet: belum langsung lunas. Halaman ini
                // sudah memantau otomatis, jadi cukup dimuat ulang.
                window.pembayaranSedangDibuka = false;
                alert('Pembayaran sedang diproses. Status akan diperbarui otomatis di halaman ini.');
                window.location.reload();
            },
            onError: function () {
                window.pembayaranSedangDibuka = false;
                alert('Pembayaran gagal. Silakan coba lagi atau bayar di kasir.');
            },
            onClose: function () {
                // Pelanggan menutup Snap lagi tanpa membayar — biarkan,
                // tombol "Lanjutkan pembayaran" tetap tersedia.
                window.pembayaranSedangDibuka = false;
            }
        });
    })
    .catch((err) => {
        alert(err.message || 'Terjadi kesalahan. Silakan coba lagi.');
        kembalikanTombol();
    });
}

// Verifikasi ke server setelah Snap melaporkan sukses (server menanya
// ulang ke Midtrans — status tidak dipercaya dari peramban).
function konfirmasiPembayaranStatus(idPesanan) {
    fetch('{{ route("customer.konfirmasi") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id_pesanan: idPesanan })
    })
    .then((res) => res.json())
    .then((data) => {
        if (data.status === 'success') {
            alert('Pembayaran berhasil!\nNomor antrean Anda: ' + (data.no_antrean || '-'));
        }
        window.location.reload();
    })
    .catch(() => window.location.reload());
}
</script>
</body>
</html>