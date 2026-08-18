<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <!-- viewport-fit=cover + tanpa user-scalable=no:
         pelanggan tetap boleh memperbesar teks (aksesibilitas),
         dan layout menghormati notch / home indicator iPhone. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Pesan Menu &middot; Kurtbeans Coffee</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA: iOS hanya mengizinkan Web Push bila situs dipasang ke Layar Utama -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#16110D">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Kurtbeans">
    <link rel="apple-touch-icon" href="/images/logo2.png">

    <!-- Tipografi: Plus Jakarta Sans (huruf buatan Tokotype, Jakarta) untuk teks,
         JetBrains Mono khusus angka supaya harga & nomor meja terbaca seperti struk. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Midtrans Snap JS (Ganti URL ke production jika sudah live) -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.clientKey') }}"></script>

    <!-- Firebase Cloud Messaging (Web Push Notification) -->
    <script src="https://www.gstatic.com/firebasejs/9.22.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.1/firebase-messaging-compat.js"></script>

    <script>
        /* Palet diturunkan dari logo Kurtbeans yang berupa stempel hitam-putih.
           Hitam pekat memegang aksi utama, dan satu warna hangat (ember)
           dipakai HANYA untuk angka yang penting bagi pelanggan:
           nomor meja, harga, dan total. */
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

        /* Sembunyikan scrollbar tapi tetap bisa di-scroll */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Panel keranjang: lembar geser di HP, panel menetap di layar lebar */
        .modal-slide-up { transform: translateY(100%); transition: transform .38s cubic-bezier(.32,.72,0,1); }
        .modal-slide-up.active { transform: translateY(0); }
        @media (min-width: 1024px) {
            .modal-slide-up, .modal-slide-up.active { transform: none; }
        }

        /* Tanda "stempel" — mengambil bentuk dari logo Kurtbeans */
        .stempel {
            border: 2px solid var(--ink);
            outline: 2px solid var(--ink);
            outline-offset: 2px;
        }

        /* Garis sobek struk di atas total pembayaran */
        .sobek { border-top: 2px dashed var(--line); }

        /* Judul kategori dengan garis penuh di sisa barisnya */
        .judul-kategori { display: flex; align-items: center; gap: 1rem; }
        .judul-kategori::after {
            content: ''; flex: 1; height: 1px; background: var(--line);
        }

        /* Kartu menu: sentuhan angkat halus saat disentuh/diarahkan */
        .menu-item { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
        @media (hover: hover) {
            .menu-item:hover { transform: translateY(-2px); }
        }

        /* Pil kategori & kartu metode bayar dikendalikan atribut data-aktif,
           bukan penggantian string kelas — jauh lebih tahan salah ketik. */
        .cat-btn {
            border: 1px solid var(--line); background: #fff; color: #6E6157;
            transition: all .18s ease;
        }
        .cat-btn[data-aktif="1"] {
            background: var(--ink); border-color: var(--ink); color: #fff;
            box-shadow: 0 6px 16px -8px rgba(32,22,18,.5);
        }
        .opsi-bayar { border: 1.5px solid var(--line); transition: all .18s ease; }
        .opsi-bayar[data-aktif="1"] {
            border-color: var(--ember); background: #FCF6F1;
            box-shadow: 0 0 0 3px rgba(180,83,28,.10);
        }

        /* Tombol tambah bulat di kartu menu */
        .btn-tambah { transition: transform .15s ease, background-color .18s ease; }
        .btn-tambah:active { transform: scale(.88); }

        /* Aksesibilitas: fokus keyboard selalu terlihat */
        :focus-visible { outline: 2px solid var(--ember); outline-offset: 2px; }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; animation: none !important; }
        }
    </style>
</head>
<body class="bg-paper font-sans text-ink antialiased">

@php
    // Dipakai untuk keadaan kosong bila belum ada menu sama sekali.
    $totalMenu = $kategori->sum(fn ($k) => $k->menu->count());
@endphp

<!-- ================= BILAH ATAS ================= -->
<header class="sticky top-0 z-30 bg-blush/95 backdrop-blur border-b border-line">
    <div class="mx-auto w-full max-w-6xl px-4 sm:px-6">

        <div class="flex items-center justify-between gap-4 py-3.5 sm:py-4">
            <div class="flex items-center gap-3 min-w-0">
                <img src="/images/logo2.png" alt="" class="h-11 w-11 rounded-2xl object-contain bg-white border border-line p-1.5 shrink-0 shadow-card">
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-ember">Kurtbeans Coffee</p>
                    <h1 class="text-lg sm:text-xl font-extrabold leading-tight truncate">Pesan dari meja Anda</h1>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <!-- Pintasan ke pemantauan pesanan (SKPL-F-6) — di HP dipindah ke drawer -->
                <a href="{{ route('customer.status') }}"
                   class="hidden lg:inline-flex h-11 items-center gap-2 rounded-full bg-ink px-4 sm:px-5 font-semibold text-sm text-white transition active:scale-95 shadow-card hover:bg-ink/90">
                    <i class="fas fa-receipt text-white/90"></i>
                    <span>Pesanan saya</span>
                </a>

                <!-- SIGNATURE: nomor meja sebagai stempel -->
                <div class="stempel rounded-lg bg-white px-3 py-1.5 text-center">
                    <span class="block text-[9px] font-bold uppercase tracking-[0.2em] text-ink2 leading-none">Meja</span>
                    <span class="block font-mono text-xl font-bold leading-tight text-ember">{{ $nomor_meja }}</span>
                </div>

                <!-- Tombol menu (hamburger) — hanya di HP / tablet -->
                <button type="button" onclick="bukaMenuDrawer()" aria-label="Buka menu" aria-controls="menu-drawer" aria-expanded="false"
                    class="lg:hidden flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-line bg-white shadow-card transition active:scale-90 hover:border-ember">
                    <i class="fas fa-bars text-ink"></i>
                </button>
            </div>
        </div>

        <!-- PENCARIAN -->
        <div class="relative pb-3">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 -mt-1.5 text-ink2/60 text-sm"></i>
            <label for="searchInput" class="sr-only">Cari menu</label>
            <input type="search" id="searchInput" onkeyup="searchMenu()" autocomplete="off"
                placeholder="Cari kopi, teh, atau cemilan…"
                class="w-full rounded-full border border-line bg-white py-3 pl-11 pr-4 text-sm placeholder:text-ink2/60 shadow-card focus:border-ember focus:outline-none focus:ring-0 transition">
        </div>

        <!-- PENYARING KATEGORI -->
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-3">
            <button type="button" onclick="filterCategory('all', this)" data-aktif="1"
                class="cat-btn shrink-0 rounded-full px-4 py-2 text-sm font-bold whitespace-nowrap">Semua</button>
            @foreach($kategori as $k)
                @if($k->menu->count() > 0)
                <button type="button" onclick="filterCategory('cat-{{ $k->id_kategori }}', this)"
                    class="cat-btn shrink-0 rounded-full px-4 py-2 text-sm font-semibold whitespace-nowrap">{{ $k->nama_kategori }}</button>
                @endif
            @endforeach
        </div>
    </div>
</header>

<!-- ================= MENU DRAWER (HAMBURGER) — HP / TABLET ================= -->
<div id="menu-drawer-overlay" onclick="tutupMenuDrawer()"
     class="fixed inset-0 z-[60] hidden bg-ink/40 backdrop-blur-[2px] lg:hidden"></div>

<aside id="menu-drawer" role="dialog" aria-modal="true" aria-label="Menu navigasi"
       class="fixed right-0 top-0 z-[61] flex h-full w-[86%] max-w-xs translate-x-full flex-col bg-paper shadow-lift transition-transform duration-300 lg:hidden"
       style="transition-timing-function: cubic-bezier(.32,.72,0,1);">

    <!-- Kepala drawer -->
    <div class="flex items-center justify-between gap-3 border-b border-line bg-blush px-5 py-4">
        <div class="flex items-center gap-3 min-w-0">
            <img src="/images/logo2.png" alt="" class="h-10 w-10 rounded-2xl object-contain bg-white border border-line p-1.5 shrink-0">
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-ember leading-none">Kurtbeans</p>
                <p class="mt-1 text-sm font-extrabold leading-none">Meja {{ $nomor_meja }}</p>
            </div>
        </div>
        <button type="button" onclick="tutupMenuDrawer()" aria-label="Tutup menu"
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-line bg-white transition active:scale-90">
            <i class="fas fa-xmark text-ink"></i>
        </button>
    </div>

    <!-- Isi drawer (dapat digulir) -->
    <div class="no-scrollbar flex-1 overflow-y-auto px-5 py-5">

        <!-- Pintasan pemantauan pesanan -->
        <a href="{{ route('customer.status') }}"
           class="flex items-center gap-3 rounded-2xl bg-ink px-4 py-3.5 font-bold text-white shadow-card transition active:scale-[.98]">
            <i class="fas fa-receipt"></i>
            <span class="flex-1">Pesanan saya</span>
            <i class="fas fa-chevron-right text-xs text-white/70"></i>
        </a>

        <!-- Lompatan kategori -->
        <p class="mb-2 mt-6 text-[10px] font-extrabold uppercase tracking-[0.18em] text-ink2">Kategori</p>
        <nav class="flex flex-col gap-1">
            <button type="button" onclick="filterDariDrawer('all')"
                class="flex items-center gap-3 rounded-xl px-3 py-3 text-left text-sm font-bold transition active:scale-[.98] hover:bg-white">
                <i class="fas fa-mug-saucer w-5 text-center text-ember"></i>
                <span class="flex-1">Semua menu</span>
            </button>
            @foreach($kategori as $k)
                @if($k->menu->count() > 0)
                <button type="button" onclick="filterDariDrawer('cat-{{ $k->id_kategori }}')"
                    class="flex items-center gap-3 rounded-xl px-3 py-3 text-left text-sm font-semibold transition active:scale-[.98] hover:bg-white">
                    <i class="fas fa-tag w-5 text-center text-ink2/50"></i>
                    <span class="flex-1">{{ $k->nama_kategori }}</span>
                    <span class="font-mono text-xs font-bold text-ink2">{{ $k->menu->count() }}</span>
                </button>
                @endif
            @endforeach
        </nav>
    </div>

    <!-- Kaki drawer -->
    <div class="sobek mx-5"></div>
    <p class="px-5 py-4 text-center text-[11px] leading-relaxed text-ink2"
       style="padding-bottom: calc(1rem + env(safe-area-inset-bottom));">
        Pindai QR di meja untuk memesan &middot; Kurtbeans Coffee
    </p>
</aside>

<!-- ================= ISI HALAMAN ================= -->
<div class="mx-auto w-full max-w-6xl px-4 sm:px-6">
    <div class="lg:grid lg:grid-cols-[minmax(0,1fr)_380px] lg:gap-8 lg:items-start">

        <!-- ---------- DAFTAR MENU ---------- -->
        <main class="pt-6 pb-32 lg:pb-12">

                       @if(!empty($galatMenu))
                <div class="rounded-2xl border border-dashed border-ember/50 bg-emberSoft px-6 py-16 text-center">
                    <i class="fas fa-triangle-exclamation text-3xl text-ember"></i>
                    <p class="mt-4 font-bold">{{ $galatMenu }}</p>
                    <p class="mt-1 text-sm text-ink2">Muat ulang halaman setelah koneksi kembali stabil.</p>
                </div>
            @elseif($totalMenu === 0)
                <div class="rounded-2xl border border-dashed border-line bg-white px-6 py-16 text-center">
                    <i class="fas fa-mug-hot text-3xl text-ink2/40"></i>
                    <p class="mt-4 font-bold">Menu belum tersedia</p>
                    <p class="mt-1 text-sm text-ink2">Silakan tanyakan ke barista di konter.</p>
                </div>
            @endif

            @foreach($kategori as $k)
                @if($k->menu->count() > 0)
                <section class="category-section mb-10" id="cat-{{ $k->id_kategori }}">
                    <h2 class="judul-kategori mb-4 text-sm font-extrabold uppercase tracking-[0.18em] text-ink2">
                        {{ $k->nama_kategori }}
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($k->menu as $m)
                        <article class="menu-item group flex items-center gap-4 rounded-2xl border border-line bg-white p-3 shadow-card hover:border-ember/40 hover:shadow-lift"
                                 data-name="{{ strtolower($m->nama_menu) }}">

                            @if($m->gambar)
                                <img src="{{ asset('storage/menu/' . $m->gambar) }}" alt="{{ $m->nama_menu }}"
                                     loading="lazy" decoding="async"
                                     class="h-24 w-24 shrink-0 rounded-2xl object-cover bg-paper">
                            @else
                                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-2xl bg-emberSoft text-ember/50" aria-hidden="true">
                                    <i class="fas fa-mug-hot text-2xl"></i>
                                </div>
                            @endif

                            <div class="flex min-w-0 flex-1 flex-col gap-2 self-stretch py-0.5">
                                <h3 class="font-bold leading-snug text-[15px]">{{ $m->nama_menu }}</h3>
                                <span class="font-mono text-base font-bold text-ember">
                                    Rp {{ number_format($m->harga, 0, ',', '.') }}
                                </span>
                                <div class="mt-auto flex justify-end">
                                    <button type="button"
                                        onclick="addToCart({{ $m->id_menu }}, @js($m->nama_menu), {{ $m->harga }}, @js($m->gambar ? asset('storage/menu/' . $m->gambar) : ''))"
                                        aria-label="Tambah {{ $m->nama_menu }} ke keranjang"
                                        class="btn-tambah inline-flex shrink-0 items-center gap-1.5 rounded-full bg-ink px-4 py-2 text-sm font-bold text-white hover:bg-ember">
                                        <i class="fas fa-plus text-[11px]"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </section>
                @endif
            @endforeach

            <!-- Hasil pencarian kosong -->
            <div id="no-result" style="display:none" class="rounded-2xl border border-dashed border-line bg-white px-6 py-16 text-center">
                <i class="fas fa-magnifying-glass text-2xl text-ink2/40"></i>
                <p class="mt-4 font-bold">Tidak ada menu yang cocok</p>
                <p class="mt-1 text-sm text-ink2">Coba kata kunci lain, atau pilih kategori di atas.</p>
            </div>
        </main>

        <!-- ---------- PANEL PESANAN ----------
             Di HP: lembar yang digeser dari bawah.
             Di layar ≥1024px: panel menetap di sisi kanan, selalu terlihat. -->
        <div id="cart-modal-overlay" onclick="closeCart()"
             class="fixed inset-0 z-40 hidden bg-ink/40 backdrop-blur-[2px] lg:hidden"></div>

        <aside id="cart-modal"
               class="modal-slide-up fixed inset-x-0 bottom-0 z-50 mx-auto flex h-[86vh] w-full max-w-lg flex-col rounded-t-3xl bg-white shadow-2xl
                      lg:static lg:z-auto lg:mt-6 lg:h-[calc(100vh-9rem)] lg:max-w-none lg:rounded-2xl lg:border lg:border-line lg:shadow-none lg:sticky lg:top-[9.5rem]">

            <!-- Kepala panel -->
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <div>
                    <h2 class="font-extrabold">Pesanan Anda</h2>
                    <p class="text-xs text-ink2">Meja {{ $nomor_meja }}</p>
                </div>
                <button type="button" onclick="closeCart()" aria-label="Tutup keranjang"
                    class="flex h-9 w-9 items-center justify-center rounded-full border border-line text-ink2 transition active:scale-90 lg:hidden">
                    <i class="fas fa-chevron-down text-sm"></i>
                </button>
            </div>

            <!-- Badan panel (dapat digulir) -->
            <div class="no-scrollbar flex-1 overflow-y-auto px-5 py-5">

                <!-- Keadaan kosong (terlihat di layar lebar) -->
                <div id="cart-empty" class="py-10 text-center">
                    <i class="fas fa-basket-shopping text-2xl text-ink2/40"></i>
                    <p class="mt-3 font-bold text-sm">Keranjang masih kosong</p>
                    <p class="mt-1 text-sm text-ink2">Pilih menu di sebelah untuk mulai memesan.</p>
                </div>

                <!-- Daftar item (dirender oleh JS) -->
                <div id="cart-items" class="flex flex-col gap-3"></div>

                <div id="cart-form" class="mt-6 hidden">
                    <div class="sobek pt-5">
                        <label for="nama_pemesan" class="mb-2 block text-xs font-extrabold uppercase tracking-[0.14em] text-ink2">
                            Nama pemesan <span class="text-ember">*</span>
                        </label>
                        <input type="text" id="nama_pemesan" placeholder="Nama yang dipanggil barista" required
                            class="w-full rounded-xl border border-line bg-white px-4 py-3 text-sm focus:border-ink focus:outline-none focus:ring-0 transition">
                    </div>

                    <!-- Metode pembayaran (SKPL-F-5) -->
                    <fieldset class="mt-5">
                        <legend class="mb-2 text-xs font-extrabold uppercase tracking-[0.14em] text-ink2">
                            Cara membayar <span class="text-ember">*</span>
                        </legend>

                        <label id="opsi-online" data-aktif="1"
                               class="opsi-bayar mb-2 flex cursor-pointer items-start gap-3 rounded-xl p-4 transition">
                            <input type="radio" name="metode" value="midtrans" checked onchange="pilihMetode()"
                                   class="mt-0.5 h-4 w-4 accent-ink">
                            <span class="flex-1">
                                <span class="text-sm font-bold">Bayar sekarang</span>
                                <span class="mt-0.5 text-xs text-ink2">QRIS, transfer, atau kartu. Pesanan langsung masuk ke barista.</span>
                            </span>
                            <i class="fas fa-bolt text-sm text-ember"></i>
                        </label>

                        <label id="opsi-tunai"
                               class="opsi-bayar flex cursor-pointer items-start gap-3 rounded-xl p-4 transition">
                            <input type="radio" name="metode" value="tunai" onchange="pilihMetode()"
                                   class="mt-0.5 h-4 w-4 accent-ink">
                            <span class="flex-1">
                                <span class="block text-sm font-bold">Bayar di kasir</span>
                                <span class="mt-0.5 block text-xs text-ink2">Tunjukkan nomor antrean Anda ke kasir. Diproses setelah kasir memvalidasi.</span>
                            </span>
                            <i class="fas fa-money-bill-wave text-sm text-ink2"></i>
                        </label>
                    </fieldset>
                </div>
            </div>

            <!-- Kaki panel: total + tombol bayar. pb-safe agar tidak tertutup home indicator iPhone. -->
            <div class="border-t border-line bg-white px-5 pt-4 rounded-b-2xl"
                 style="padding-bottom: calc(1rem + env(safe-area-inset-bottom));">
                <div class="mb-4 flex items-end justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-[0.14em] text-ink2">Total</span>
                    <span class="font-mono text-2xl font-bold text-ember" id="checkout-total">Rp 0</span>
                </div>
                <button type="button" onclick="processCheckout()" id="btn-checkout"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-ink py-4 font-bold text-white transition active:scale-[.98] disabled:opacity-40 disabled:active:scale-100">
                    <span id="label-checkout">Bayar sekarang</span>
                    <i class="fas fa-lock text-xs" id="ikon-checkout"></i>
                </button>
            </div>
        </aside>
    </div>
</div>

<!-- BILAH KERANJANG MENGAMBANG (hanya di HP / tablet) -->
<div id="floating-cart"
     class="fixed inset-x-0 bottom-0 z-40 translate-y-full transition-transform duration-300 lg:hidden"
     style="padding-bottom: calc(.75rem + env(safe-area-inset-bottom));">
    <div class="mx-auto w-full max-w-lg px-3 pt-2">
        <div class="flex items-center justify-between gap-3 rounded-2xl bg-ink px-3 py-2.5 shadow-lift">
            <button type="button" onclick="openCart()" class="flex min-w-0 items-center gap-3 text-left">
                <span class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white">
                    <i class="fas fa-basket-shopping"></i>
                    <span id="cart-badge" class="absolute -right-1.5 -top-1.5 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full border-2 border-ink bg-ember px-1 font-mono text-[10px] font-bold leading-none text-white">0</span>
                </span>
                <span class="min-w-0">
                    <span class="block text-[10px] font-bold uppercase tracking-[0.16em] text-white/60">Total pesanan</span>
                    <span class="block font-mono text-lg font-bold text-white" id="cart-total-price">Rp 0</span>
                </span>
            </button>
            <button type="button" onclick="openCart()"
                class="shrink-0 rounded-xl bg-white px-5 py-3 font-bold text-ink transition active:scale-95">
                Lihat <i class="fas fa-chevron-right ml-1 text-xs"></i>
            </button>
        </div>
    </div>
</div>

<!-- ================= JAVASCRIPT ================= -->
<script>
    // Data Keranjang
    let cart = {};

    // Format Rupiah
    const formatRupiah = (angka) => new Intl.NumberFormat('id-ID').format(angka);

    // Layar lebar: panel keranjang selalu terlihat, jadi bilah mengambang
    // dan lembar geser tidak dipakai.
    const layarLebar = () => window.matchMedia('(min-width: 1024px)').matches;

    // Tambah ke Keranjang
    function addToCart(id, name, price, img) {
        if (cart[id]) {
            cart[id].qty += 1;
        } else {
            cart[id] = { id_menu: id, name: name, price: price, img: img, qty: 1 };
        }
        updateCartUI();
        document.getElementById('floating-cart').classList.remove('translate-y-full');
    }

    // Kurangi dari Keranjang
    function minCart(id) {
        if (cart[id]) {
            cart[id].qty -= 1;
            if (cart[id].qty <= 0) delete cart[id];
        }
        updateCartUI();

        if (Object.keys(cart).length === 0) {
            document.getElementById('floating-cart').classList.add('translate-y-full');
            closeCart();
        }
    }

    // Update UI Keranjang
    function updateCartUI() {
        let totalItems = 0;
        let totalPrice = 0;
        const cartItemsContainer = document.getElementById('cart-items');
        cartItemsContainer.innerHTML = '';

        for (const key in cart) {
            const item = cart[key];
            totalItems += item.qty;
            totalPrice += (item.price * item.qty);

            const baris = document.createElement('div');
            baris.className = 'flex items-center gap-3';
            baris.innerHTML = `
                <div class="min-w-0 flex-1">
                    <h4 class="truncate text-sm font-bold">${item.name}</h4>
                    <p class="mt-0.5 font-mono text-sm font-bold text-ember">Rp ${formatRupiah(item.price * item.qty)}</p>
                </div>
                <div class="flex items-center gap-1 rounded-full border border-line p-1">
                    <button type="button" data-aksi="kurang" aria-label="Kurangi ${item.name}"
                        class="flex h-7 w-7 items-center justify-center rounded-full text-ink2 transition active:scale-90 hover:bg-paper">
                        <i class="fas fa-minus text-[10px]"></i>
                    </button>
                    <span class="w-6 text-center font-mono text-sm font-bold">${item.qty}</span>
                    <button type="button" data-aksi="tambah" aria-label="Tambah ${item.name}"
                        class="flex h-7 w-7 items-center justify-center rounded-full bg-ink text-white transition active:scale-90">
                        <i class="fas fa-plus text-[10px]"></i>
                    </button>
                </div>
            `;
            // Dipasang lewat addEventListener, bukan onclick di dalam string,
            // supaya nama menu berisi tanda kutip tidak merusak HTML.
            baris.querySelector('[data-aksi="kurang"]').addEventListener('click', () => minCart(item.id_menu));
            baris.querySelector('[data-aksi="tambah"]').addEventListener('click', () => addToCart(item.id_menu, item.name, item.price, item.img));
            cartItemsContainer.appendChild(baris);
        }

        const adaIsi = totalItems > 0;
        document.getElementById('cart-empty').classList.toggle('hidden', adaIsi);
        document.getElementById('cart-form').classList.toggle('hidden', !adaIsi);
        document.getElementById('btn-checkout').disabled = !adaIsi;

        document.getElementById('cart-badge').innerText = totalItems;
        document.getElementById('cart-total-price').innerText = `Rp ${formatRupiah(totalPrice)}`;
        document.getElementById('checkout-total').innerText = `Rp ${formatRupiah(totalPrice)}`;
    }

    // Buka / Tutup Panel Keranjang
    function openCart() {
        if (layarLebar()) return;
        if (Object.keys(cart).length === 0) return;
        document.getElementById('cart-modal-overlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => { document.getElementById('cart-modal').classList.add('active'); }, 10);
    }

    function closeCart() {
        if (layarLebar()) return;
        document.getElementById('cart-modal').classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => { document.getElementById('cart-modal-overlay').classList.add('hidden'); }, 320);
    }

    // Filter Kategori
    function filterCategory(catId, tombol) {
        document.querySelectorAll('.cat-btn').forEach(btn => btn.removeAttribute('data-aktif'));
        if (tombol) tombol.setAttribute('data-aktif', '1');

        // Pencarian direset supaya kedua penyaring tidak saling bertabrakan.
        document.getElementById('searchInput').value = '';
        document.querySelectorAll('.menu-item').forEach(item => { item.style.display = ''; });
        document.getElementById('no-result').style.display = 'none';

        document.querySelectorAll('.category-section').forEach(section => {
            section.style.display = (catId === 'all' || section.id === catId) ? '' : 'none';
        });
    }

    // Fungsi Pencarian
    function searchMenu() {
        const input = document.getElementById('searchInput').value.toLowerCase().trim();
        let found = false;

        document.querySelectorAll('.category-section').forEach(section => {
            let adaDiBagianIni = false;

            section.querySelectorAll('.menu-item').forEach(item => {
                const cocok = item.getAttribute('data-name').includes(input);
                item.style.display = cocok ? '' : 'none';
                if (cocok) { adaDiBagianIni = true; found = true; }
            });

            section.style.display = adaDiBagianIni ? '' : 'none';
        });

        document.getElementById('no-result').style.display = found ? 'none' : '';
    }

    /* ==========================================================
       WEB PUSH NOTIFICATION (SKPL-F-7 & F-8)
       ==========================================================
       Konfigurasi di bawah bersifat publik (Firebase Web Config
       + VAPID public key). Yang wajib dirahasiakan adalah file
       Service Account JSON di sisi server, bukan nilai-nilai ini.
    */
    const firebaseConfig = {
        apiKey: "AIzaSyC3OOb_au6qCFnCTD5aBEnzyKd4h_Kd83k",
        authDomain: "kurtbeans-notifikasi-f0a93.firebaseapp.com",
        projectId: "kurtbeans-notifikasi-f0a93",
        messagingSenderId: "806105612585",
        appId: "1:806105612585:web:7fd8614f1b058b265fb738"
    };

    const VAPID_KEY = "BJUlR6HS5Bf3oOhpvNwU0C5718yapyS6phl3slnopef8nw5urENTVCRPUr59BbQHSc6WMwcfgnE0zUkaJ_Gmy8s";

    let fcmToken = null;         // Token perangkat, diisi setelah izin diberikan
    let fcmMessaging = null;
    let fcmRegistration = null;  // Cache agar service worker hanya disiapkan sekali

    // Registrasi service worker + inisialisasi Firebase saat halaman dibuka.
    async function siapkanFirebase() {
        if (!('serviceWorker' in navigator) || !('Notification' in window)) {
            console.warn('Peramban tidak mendukung Web Push Notification.');
            return null;
        }

        if (fcmRegistration) return fcmRegistration;

        try {
            fcmRegistration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }
            fcmMessaging = firebase.messaging();

            // Notifikasi yang tiba saat halaman ini sedang dibuka
            // tidak ditangani service worker, jadi ditampilkan manual.
            fcmMessaging.onMessage(function (payload) {
                const judul = (payload.notification && payload.notification.title) || 'Kurtbeans Coffee';
                const isi   = (payload.notification && payload.notification.body) || '';
                tampilkanNotifikasiDalamHalaman(judul, isi);
            });

            return fcmRegistration;
        } catch (e) {
            console.error('Gagal menyiapkan Firebase:', e);
            fcmRegistration = null;
            return null;
        }
    }

    /**
     * Meminta izin notifikasi lalu mengambil token perangkat.
     * Dipanggil dari klik tombol "Bayar sekarang" karena sebagian
     * peramban hanya mengizinkan permintaan izin dari aksi pengguna.
     *
     * Mengembalikan token, atau null bila pelanggan menolak
     * (Ext 2A SKPL-F-7: proses tetap lanjut tanpa notifikasi).
     */
    async function mintaIzinNotifikasi() {
        if (!('Notification' in window)) return null;
        if (Notification.permission === 'denied') return null;
        if (fcmToken) return fcmToken;

        try {
            const registration = await siapkanFirebase();
            if (!registration || !fcmMessaging) return null;

            const izin = await Notification.requestPermission();
            if (izin !== 'granted') return null;

            fcmToken = await fcmMessaging.getToken({
                vapidKey: VAPID_KEY,
                serviceWorkerRegistration: registration
            });

            return fcmToken;
        } catch (e) {
            console.error('Gagal mengambil token FCM:', e);
            return null;
        }
    }

    /**
     * Mengirim token ke server agar tersimpan di kolom
     * pelanggan_sementara.token_subscription.
     *
     * Dipanggil SETELAH checkout berhasil, karena baris
     * pelanggan_sementara baru dibuat pada saat checkout.
     */
    async function simpanTokenKeServer(token) {
        if (!token) return;

        try {
            await fetch('/simpan-fcm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ fcm_token: token })
            });
        } catch (e) {
            console.error('Gagal menyimpan token FCM:', e);
        }
    }

    // Notifikasi sederhana untuk kondisi halaman sedang aktif (foreground).
    function tampilkanNotifikasiDalamHalaman(judul, isi) {
        const kotak = document.createElement('div');
        kotak.className = 'fixed left-4 right-4 top-4 z-[9999] mx-auto flex max-w-md items-start gap-3 rounded-2xl border-l-4 border-ember bg-white p-4 shadow-2xl';
        kotak.innerHTML = `
            <i class="fas fa-mug-hot mt-0.5 text-lg text-ember"></i>
            <div class="flex-1">
                <h4 class="text-sm font-bold">${judul}</h4>
                <p class="mt-0.5 text-sm text-ink2">${isi}</p>
            </div>
            <button type="button" class="px-2 text-ink2" aria-label="Tutup" onclick="this.parentElement.remove()">&times;</button>
        `;
        document.body.appendChild(kotak);
        setTimeout(() => kotak.remove(), 10000);
    }

    // Siapkan service worker sejak halaman dimuat (belum meminta izin).
    window.addEventListener('load', siapkanFirebase);
    window.addEventListener('load', updateCartUI);

    // Metode yang sedang dipilih pelanggan: 'midtrans' atau 'tunai'
    function metodeTerpilih() {
        const dipilih = document.querySelector('input[name="metode"]:checked');
        return dipilih ? dipilih.value : 'midtrans';
    }

    // Menyorot kartu opsi yang aktif dan menyesuaikan label tombol.
    function pilihMetode() {
        const metode = metodeTerpilih();

        const sorot = (id, aktif) => {
            const el = document.getElementById(id);
            if (aktif) el.setAttribute('data-aktif', '1');
            else el.removeAttribute('data-aktif');
        };
        sorot('opsi-online', metode === 'midtrans');
        sorot('opsi-tunai',  metode === 'tunai');

        document.getElementById('label-checkout').textContent =
            metode === 'tunai' ? 'Pesan & bayar di kasir' : 'Bayar sekarang';
        document.getElementById('ikon-checkout').className =
            metode === 'tunai' ? 'fas fa-receipt text-xs' : 'fas fa-lock text-xs';
    }

    // Proses Checkout
    function processCheckout() {
        const namaPemesan = document.getElementById('nama_pemesan').value.trim();
        if (!namaPemesan) {
            alert('Mohon isi Nama Pemesan terlebih dahulu!');
            document.getElementById('nama_pemesan').focus();
            return;
        }

        const metode = metodeTerpilih();
        const btnCheckout = document.getElementById('btn-checkout');
        const labelTombol = metode === 'tunai' ? 'Pesan & bayar di kasir' : 'Bayar sekarang';
        const ikonTombol  = metode === 'tunai' ? 'fa-receipt' : 'fa-lock';
        const labelAsli = '<span id="label-checkout">' + labelTombol + '</span>'
                        + '<i class="fas ' + ikonTombol + ' text-xs" id="ikon-checkout"></i>';

        const kunciTombol = (teks) => {
            btnCheckout.disabled = true;
            btnCheckout.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + teks;
        };
        const bukaTombol = () => {
            btnCheckout.disabled = false;
            btnCheckout.innerHTML = labelAsli;
        };

        kunciTombol('Memproses…');

        // Minta izin notifikasi selagi masih dalam konteks klik pengguna.
        // Kalau ditolak, pemesanan tetap dilanjutkan (Ext 2A SKPL-F-7).
        //
        // Hasilnya disimpan sebagai Promise, BUKAN dipanggil begitu saja.
        // Pengambilan token bisa memakan beberapa detik (terutama di iOS),
        // sehingga kalau tidak ditunggu, variabel fcmToken masih null saat
        // hendak dikirim ke server dan notifikasi tidak akan pernah sampai.
        const izinBerjalan = mintaIzinNotifikasi();

        // Kirim hanya id_menu dan qty. Harga dihitung ulang di server
        // dari database supaya tidak bisa dimanipulasi dari browser.
        const cartArray = Object.values(cart).map(item => ({
            id_menu: item.id_menu,
            qty: item.qty
        }));

        fetch('{{ route("customer.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ nama_pemesan: namaPemesan, cart: cartArray, metode: metode })
        })
        .then(async (response) => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(data.error || data.message || 'Gagal memproses pesanan.');
            }
            return data;
        })
        .then(data => {
            bukaTombol();

            // Baris pelanggan_sementara sudah dibuat oleh checkout,
            // jadi token notifikasi baru bisa dikaitkan sekarang.
            // Ditunggu sampai token benar-benar siap.
            izinBerjalan.then(function (token) {
                simpanTokenKeServer(token);
            });

            // ---- Alur TUNAI: tidak membuka Midtrans sama sekali ----
            if (data.metode === 'Tunai') {
                cart = {};
                updateCartUI();
                closeCart();
                alert('Pesanan berhasil dibuat!' +
                      '\nNomor antrean Anda: ' + (data.no_antrean || '-') +
                      '\n\nSilakan tunjukkan nomor antrean ini ke kasir untuk membayar. ' +
                      'Pesanan akan diproses setelah pembayaran divalidasi.');
                window.location.href = '{{ route("customer.status") }}';
                return;
            }

            // ---- Alur ONLINE: buka Snap Midtrans ----
            if (!data.snap_token) {
                alert('Gagal mendapatkan token pembayaran.');
                return;
            }

            closeCart();

            window.snap.pay(data.snap_token, {
                onSuccess: function (result) {
                    konfirmasiPembayaran(data.id_pesanan);
                },
                onPending: function (result) {
                    // QRIS, Virtual Account, dan e-wallet tidak langsung
                    // lunas. Pelanggan diarahkan ke halaman pemantauan,
                    // yang akan menanyakan status ke Midtrans secara
                    // berkala sampai pembayaran benar-benar masuk.
                    cart = {};
                    updateCartUI();
                    closeCart();
                    alert('Pembayaran sedang diproses.' +
                          '\n\nSelesaikan pembayaran Anda, lalu pantau status pesanan ' +
                          'di halaman "Pesanan Saya". Status akan berubah otomatis.');
                    window.location.href = '{{ route("customer.status") }}';
                },
                onError: function (result) {
                    alert('Pembayaran gagal. Silakan coba lagi atau bayar di kasir.');
                },
                onClose: function () {
                    alert('Anda menutup halaman pembayaran sebelum menyelesaikan transaksi.');
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Terjadi kesalahan pada server.');
            bukaTombol();
        });
    }

    // Memberitahu server bahwa pembayaran selesai.
    // Server memverifikasi ulang ke Midtrans sebelum menandai Lunas,
    // jadi status TIDAK ditentukan oleh browser.
    function konfirmasiPembayaran(idPesanan, diamJikaPending = false) {
        fetch('{{ route("customer.konfirmasi") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id_pesanan: idPesanan })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                cart = {};
                updateCartUI();
                alert('Pembayaran berhasil!\nNomor antrean Anda: ' + (data.no_antrean || '-') +
                      '\nPesanan Anda sedang disiapkan.');
                window.location.href = '{{ route("customer.status") }}';
            } else if (data.status === 'pending') {
                if (!diamJikaPending) {
                    alert('Pembayaran Anda masih menunggu penyelesaian.');
                }
            } else {
                alert(data.message || 'Pembayaran gagal diverifikasi. Silakan konfirmasi ke kasir.');
            }
        })
        .catch(() => {
            alert('Pembayaran diterima, tetapi verifikasi gagal. Silakan tunjukkan bukti bayar ke kasir.');
        });
    }
</script>
<script>
    /* ==========================================================
       MENU DRAWER (HAMBURGER) — hanya aktif di HP / tablet
       ==========================================================
       Tidak menambah logika baru: lompatan kategori memakai ulang
       fungsi filterCategory() lewat klik pada pil kategori yang
       sudah ada, sehingga sorotan pil tetap sinkron. */
    const tombolHamburger = document.querySelector('[aria-controls="menu-drawer"]');

    function bukaMenuDrawer() {
        const panel = document.getElementById('menu-drawer');
        document.getElementById('menu-drawer-overlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // beri jeda 1 frame agar transisi geser terlihat
        requestAnimationFrame(() => panel.classList.remove('translate-x-full'));
        if (tombolHamburger) tombolHamburger.setAttribute('aria-expanded', 'true');
    }

    function tutupMenuDrawer() {
        const panel = document.getElementById('menu-drawer');
        panel.classList.add('translate-x-full');
        document.body.style.overflow = '';
        setTimeout(() => {
            document.getElementById('menu-drawer-overlay').classList.add('hidden');
        }, 300);
        if (tombolHamburger) tombolHamburger.setAttribute('aria-expanded', 'false');
    }

    // Pilih kategori dari drawer: klik pil kategori yang bersesuaian
    // (menjalankan filterCategory dengan elemen yang benar), lalu tutup.
    function filterDariDrawer(catId) {
        const pil = document.querySelectorAll('.cat-btn');
        for (const p of pil) {
            const oc = p.getAttribute('onclick') || '';
            if (oc.includes("'" + catId + "'")) { p.click(); break; }
        }
        tutupMenuDrawer();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Tutup dengan tombol Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const panel = document.getElementById('menu-drawer');
            if (panel && !panel.classList.contains('translate-x-full')) tutupMenuDrawer();
        }
    });
</script>
</body>
</html>