<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - Kurtbeans Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fallback CDN Tailwind: Memastikan CSS langsung jalan jika npm run dev lupa dinyalakan -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        kurtbeans: {
                            dark: '#1a2b29',
                            cream: '#f4f1e1',
                            brown: '#2b1a10',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased overflow-hidden font-sans">

    <!-- Alpine.js Wrapper -->
    <div x-data="{ 
        activeTab: (function () { try { return JSON.parse(sessionStorage.getItem('kasir_tab') || '{}').activeTab || 'dashboard'; } catch (e) { return 'dashboard'; } })(), 
        modalOpen: false, 
        selectedId: '', 
        selectedTotal: 0,
        selectedMeja: '',
        cariTrx: '',
        filterMetode: 'semua',
        cocokTrx(el) {
            const metodeCocok = this.filterMetode === 'semua' || this.filterMetode === el.dataset.metode;
            const kunci = this.cariTrx.toLowerCase().trim();
            const cariCocok = kunci === '' || (el.dataset.cari || '').includes(kunci);
            return metodeCocok && cariCocok;
        },
        tidakAdaHasil() {
            const baris = document.querySelectorAll('tr[data-metode]');
            for (const b of baris) { if (this.cocokTrx(b)) return false; }
            return baris.length > 0;
        }
    }"
    x-init="$watch('activeTab', v => { try { sessionStorage.setItem('kasir_tab', JSON.stringify({ activeTab: v })); } catch (e) {} })"
    class="flex h-screen w-full">

        <!-- SIDEBAR (Sesuai Estetika Admin) -->
        <aside class="w-72 bg-kurtbeans-dark text-white flex flex-col h-screen shrink-0 transition-all duration-300 shadow-xl z-20">
            <div class="bg-white/2 p-3 w-full text-center rounded-xl shadow-inner">
                    <img src="{{ asset('images/logo3.png') }}" alt="Kurtbeans Coffee" class="w-32 mx-auto">
                </div>
            
            <nav class="flex-1 px-4 py-2 space-y-2 overflow-y-auto">
                <button @click="activeTab = 'dashboard'" 
                        :class="activeTab === 'dashboard' ? 'bg-white text-kurtbeans-dark shadow-md' : 'text-gray-400 hover:bg-white/10 hover:text-white'" 
                        class="w-full text-left px-5 py-4 rounded-2xl font-bold transition-all duration-200 flex items-center gap-4 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </button>
                <button @click="activeTab = 'transaksi'" 
                        :class="activeTab === 'transaksi' ? 'bg-white text-kurtbeans-dark shadow-md' : 'text-gray-400 hover:bg-white/10 hover:text-white'" 
                        class="w-full text-left px-5 py-4 rounded-2xl font-bold transition-all duration-200 flex items-center gap-4 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Transaksi
                </button>
                <button @click="activeTab = 'antrean'" 
                        :class="activeTab === 'antrean' ? 'bg-white text-kurtbeans-dark shadow-md' : 'text-gray-400 hover:bg-white/10 hover:text-white'" 
                        class="w-full text-left px-5 py-4 rounded-2xl font-bold transition-all duration-200 flex items-center gap-4 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Antrean Lunas
                </button>
            </nav>

            <div class="p-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-[#2a3b39] hover:bg-red-500/90 text-white px-4 py-3.5 rounded-2xl font-bold transition-colors duration-200 flex items-center justify-center gap-3 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50 relative">
            
            <!-- Topbar -->
            <header class="px-10 py-6 flex justify-between items-center bg-gray-50 z-10">
                <h2 class="text-3xl font-bold text-kurtbeans-dark" x-text="activeTab === 'dashboard' ? 'Dashboard Kasir' : (activeTab === 'transaksi' ? 'Daftar Transaksi' : 'Antrean Lunas')">Dashboard Kasir</h2>
                <p class="text-xs text-gray-400 font-semibold mt-1 flex items-center gap-1.5">
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Pembaruan otomatis aktif
                </p>
                
                <div class="flex items-center space-x-4">
                    <div class="bg-white border border-gray-200 text-gray-600 px-5 py-2.5 rounded-full text-sm font-semibold flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}
                    </div>
                    <div class="bg-white border border-gray-200 text-gray-800 px-2 py-1.5 pr-5 rounded-full text-sm font-bold flex items-center gap-3 shadow-sm">
                        <div class="w-8 h-8 bg-kurtbeans-dark rounded-full flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        {{ Auth::user()->nama_lengkap ?? 'Kasir Aktif' }}
                    </div>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-10 pb-10">
                
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-2xl border border-green-200 shadow-sm flex items-center gap-3 font-semibold">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-2xl border border-red-200 shadow-sm flex items-center gap-3 font-semibold">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- ================= TAB: DASHBOARD (B01) ================= -->
                <div x-show="activeTab === 'dashboard'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    
                    <!-- Stat Cards (Gaya Admin: Kotak putih, angka besar, label di bawah) -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex flex-col items-center justify-center relative overflow-hidden h-36">
                            <div class="absolute -top-6 -right-6 w-24 h-24 bg-blue-50 rounded-full opacity-60"></div>
                            <span class="text-5xl font-bold text-kurtbeans-dark z-10">{{ $transaksiHariIni ?? 0 }}</span>
                            <span class="text-sm text-gray-500 mt-2 z-10 font-medium">Transaksi Hari Ini</span>
                        </div>
                        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex flex-col items-center justify-center relative overflow-hidden h-36">
                            <div class="absolute -top-6 -right-6 w-24 h-24 bg-red-50 rounded-full opacity-60"></div>
                            <span class="text-5xl font-bold text-kurtbeans-dark z-10">{{ $menungguValidasi ?? 0 }}</span>
                            <span class="text-sm text-gray-500 mt-2 z-10 font-medium">Menunggu Validasi</span>
                        </div>
                        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex flex-col items-center justify-center relative overflow-hidden h-36">
                            <div class="absolute -top-6 -right-6 w-24 h-24 bg-green-50 rounded-full opacity-60"></div>
                            <span class="text-5xl font-bold text-kurtbeans-dark z-10">{{ $antreanLunas ?? 0 }}</span>
                            <span class="text-sm text-gray-500 mt-2 z-10 font-medium">Antrean Lunas</span>
                        </div>
                        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex flex-col items-center justify-center relative overflow-hidden h-36">
                            <div class="absolute -top-6 -right-6 w-24 h-24 bg-orange-50 rounded-full opacity-60"></div>
                            <span class="text-3xl font-bold text-kurtbeans-dark z-10">Rp {{ number_format($totalTunaiDiterima ?? 0, 0, ',', '.') }}</span>
                            <span class="text-sm text-gray-500 mt-2 z-10 font-medium">Total Tunai Diterima</span>
                        </div>
                    </div>

                    <!-- Layout Bawah Dashboard (2 Kolom Kiri, 1 Kolom Kanan) -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                        
                        <!-- KIRI: Pantau Transaksi & Validasi -->
                        <div class="xl:col-span-2 flex flex-col gap-6">
                            <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 flex flex-col overflow-hidden">
                                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                                    <h3 class="text-xl font-bold text-gray-800">Pantau Transaksi & Validasi Tunai</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-gray-50/50 text-gray-500 text-sm border-b border-gray-100">
                                                <th class="px-6 py-4 font-semibold">No. Pesanan</th>
                                                <th class="px-6 py-4 font-semibold">Nama Pemesan</th>
                                                <th class="px-6 py-4 font-semibold">Meja</th>
                                                <th class="px-6 py-4 font-semibold">Total Harga</th>
                                                <th class="px-6 py-4 font-semibold">Metode</th>
                                                <th class="px-6 py-4 font-semibold">Status</th>
                                                <th class="px-6 py-4 font-semibold">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @forelse(collect($semuaTransaksi ?? [])->take(5) as $trx)
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="px-6 py-4 text-sm font-bold text-gray-700">#{{ str_pad($trx->id_pesanan ?? 0, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td class="px-6 py-4 text-sm font-semibold text-gray-700">{{ $trx->pelanggan->nama_pemesan ?? '-' }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-600">Meja {{ $trx->meja->nomor_meja ?? '-' }}</td>
                                                <td class="px-6 py-4 text-sm font-bold text-gray-800">Rp {{ number_format($trx->total_harga ?? 0, 0, ',', '.') }}</td>

                                                {{-- Kolom metode_pembayaran baru terisi SETELAH pembayaran berhasil,
                                                     jadi selama masih kosong jangan ditampilkan sebagai "Midtrans". --}}
                                                <td class="px-6 py-4">
                                                    @if(($trx->metode_pembayaran ?? '') == 'Tunai')
                                                        <span class="bg-orange-50 text-orange-700 border border-orange-200 px-3 py-1 rounded-full text-xs font-bold">Tunai</span>
                                                    @elseif(!empty($trx->metode_pembayaran))
                                                        <span class="bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-full text-xs font-bold">{{ ucfirst(str_replace('_', ' ', $trx->metode_pembayaran)) }}</span>
                                                    @else
                                                        <span class="text-gray-400 text-xs font-semibold italic">Belum dipilih</span>
                                                    @endif
                                                </td>

                                                {{-- Status pembayaran yang sebenarnya, dibaca langsung dari database. --}}
                                                <td class="px-6 py-4">
                                                    @if(($trx->status_pembayaran ?? '') == 'Lunas')
                                                        <span class="bg-green-50 text-green-700 border border-green-200 px-3 py-1 rounded-full text-xs font-bold">Lunas</span>
                                                    @elseif(($trx->status_pembayaran ?? '') == 'Gagal')
                                                        <span class="bg-red-50 text-red-700 border border-red-200 px-3 py-1 rounded-full text-xs font-bold">Gagal</span>
                                                    @else
                                                        <span class="bg-yellow-50 text-yellow-700 border border-yellow-200 px-3 py-1 rounded-full text-xs font-bold">Belum Lunas</span>
                                                    @endif
                                                </td>

                                                {{-- Validasi hanya untuk pesanan yang pelanggannya memilih bayar tunai.
                                                     Pesanan online yang ditinggalkan cukup dibatalkan. --}}
                                                <td class="px-6 py-4">
                                                    @if(($trx->status_pembayaran ?? '') == 'Belum Lunas')
                                                        <div class="flex items-center gap-2">
                                                            @if(($trx->metode_pembayaran ?? '') == 'Tunai')
                                                                <button @click="modalOpen = true; selectedId = '{{ $trx->id_pesanan }}'; selectedTotal = '{{ number_format($trx->total_harga ?? 0, 0, ',', '.') }}'; selectedMeja = '{{ $trx->meja->nomor_meja ?? '-' }}'" class="bg-kurtbeans-dark hover:bg-gray-800 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm whitespace-nowrap">
                                                                    Validasi Tunai
                                                                </button>
                                                            @endif
                                                            {{-- Batalkan tersedia untuk SEMUA pesanan yang belum lunas,
                                                                 termasuk yang tunai: pelanggan bisa berubah pikiran
                                                                 soal metode bayar atau membatalkan pesanannya. --}}
                                                            <form method="POST" action="/kasir/batalkan/{{ $trx->id_pesanan }}" onsubmit="return confirm('Batalkan pesanan #{{ str_pad($trx->id_pesanan, 4, '0', STR_PAD_LEFT) }}?\n\nPesanan yang dibatalkan tidak dapat dikembalikan.')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap">
                                                                    Batalkan
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 text-xs font-semibold">&mdash;</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400 font-medium">Belum ada transaksi hari ini.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="px-8 py-5 border-t border-gray-100 bg-gray-50/50 text-right">
                                    <button @click="activeTab = 'transaksi'" class="text-sm text-kurtbeans-dark font-bold hover:underline">Lihat Semua Transaksi &rarr;</button>
                                </div>
                            </div>
                        </div>

                        <!-- KANAN: Antrean & Notifikasi -->
                        <div class="flex flex-col gap-6">
                            
                            <!-- Panel Antrean -->
                            <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 flex flex-col h-[350px]">
                                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                                    <h3 class="text-lg font-bold text-gray-800">Antrean Pesanan Lunas</h3>
                                </div>
                                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                                    @forelse(collect($antreanPesanan ?? [])->take(3) as $antrean)
                                        <div class="border border-gray-100 p-4 rounded-2xl shadow-sm bg-gray-50/50">
                                            <div class="flex gap-4">
                                                <div class="w-14 h-14 bg-white border border-gray-200 rounded-xl flex items-center justify-center shadow-sm shrink-0">
                                                    <span class="font-bold text-xl text-kurtbeans-dark">M{{ str_pad($antrean->meja->nomor_meja ?? 0, 2, '0', STR_PAD_LEFT) }}</span>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex justify-between items-start mb-1">
                                                        <span class="font-bold text-sm text-gray-800">{{ $antrean->pelanggan->nama_pemesan ?? '-' }} &middot; Meja {{ $antrean->meja->nomor_meja ?? '-' }}</span>
                                                        <span class="text-xs text-gray-500 font-medium">{{ \Carbon\Carbon::parse($antrean->tgl_bayar ?? now())->format('H:i') }} WIB</span>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mb-2 truncate font-medium">
                                                        Rp {{ number_format($antrean->total_harga ?? 0, 0, ',', '.') }}
                                                    </div>
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-[10px] font-bold bg-white border border-gray-200 px-2.5 py-1 rounded-lg text-gray-600">{{ $antrean->metode_pembayaran ?? '' }}</span>
                                                        <span class="text-[10px] font-bold text-kurtbeans-dark uppercase tracking-wide">{{ $antrean->status_pesanan ?? '' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="h-full flex items-center justify-center">
                                            <p class="text-gray-400 text-sm italic font-medium">Semua antrean lunas kosong.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Panel Notifikasi -->
                            <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 flex flex-col flex-1">
                                <div class="px-6 py-5 border-b border-gray-100">
                                    <h3 class="text-lg font-bold text-gray-800">Notifikasi Validasi</h3>
                                </div>
                                <div class="p-6 space-y-4 overflow-y-auto max-h-[200px]">
                                    @php
                                        $notifTunai = collect($semuaTransaksi ?? [])->filter(function($trx) {
                                            return ($trx->metode_pembayaran ?? '') == 'Tunai' && ($trx->status_pembayaran ?? '') == 'Belum Lunas';
                                        })->take(3);
                                    @endphp
                                    @forelse($notifTunai as $nt)
                                        <div class="flex items-start gap-3 text-sm">
                                            <div class="w-2.5 h-2.5 rounded-full bg-red-500 shrink-0 mt-1.5 shadow-sm"></div>
                                            <p class="text-gray-600 leading-relaxed font-medium">
                                                Pesanan <span class="font-bold text-kurtbeans-dark">#{{ str_pad($nt->id_pesanan, 4, '0', STR_PAD_LEFT) }}</span> menunggu validasi pembayaran Tunai <br><span class="text-xs text-gray-400">(Meja {{ $nt->meja->nomor_meja ?? '-' }})</span>
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-gray-400 text-sm italic font-medium">Tidak ada notifikasi validasi baru.</p>
                                    @endforelse
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ================= TAB: TRANSAKSI (B02) ================= -->
                <div x-show="activeTab === 'transaksi'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 flex flex-col overflow-hidden min-h-[500px]">
                        <div class="px-8 py-6 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4 bg-gray-50/30">
                            <h3 class="text-xl font-bold text-gray-800">Daftar Transaksi Keseluruhan</h3>
                            
                            <!-- Pencarian & penyaring metode -->
                            <div class="flex flex-wrap gap-3">
                                <div class="relative">
                                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"></path></svg>
                                    <input type="text" x-model="cariTrx" placeholder="Cari no. pesanan, nama, atau meja…"
                                           class="border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm font-medium focus:outline-none focus:border-kurtbeans-dark focus:ring-1 focus:ring-kurtbeans-dark w-72 shadow-sm">
                                </div>
                                <select x-model="filterMetode" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none bg-white shadow-sm">
                                    <option value="semua">Semua Metode</option>
                                    <option value="tunai">Tunai</option>
                                    <option value="midtrans">Midtrans</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto p-4">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/80 text-gray-500 text-sm border-b border-gray-100">
                                        <th class="px-6 py-4 font-semibold rounded-tl-xl">Waktu</th>
                                        <th class="px-6 py-4 font-semibold">No. Pesanan</th>
                                        <th class="px-6 py-4 font-semibold">Nama Pemesan</th>
                                        <th class="px-6 py-4 font-semibold">Meja</th>
                                        <th class="px-6 py-4 font-semibold">Total Harga</th>
                                        <th class="px-6 py-4 font-semibold">Metode</th>
                                        <th class="px-6 py-4 font-semibold">Status</th>
                                        <th class="px-6 py-4 font-semibold rounded-tr-xl">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($semuaTransaksi ?? [] as $trx)
                                    @php
                                        $kategoriMetode = ($trx->metode_pembayaran === 'Tunai') ? 'tunai' : 'midtrans';
                                        $cariTrx = \Illuminate\Support\Str::lower(
                                            str_pad($trx->id_pesanan ?? 0, 4, '0', STR_PAD_LEFT) . ' ' .
                                            ($trx->id_pesanan ?? '') . ' ' .
                                            ($trx->pelanggan->nama_pemesan ?? '') . ' meja ' .
                                            ($trx->meja->nomor_meja ?? '')
                                        );
                                    @endphp
                                    <tr data-metode="{{ $kategoriMetode }}" data-cari="{{ $cariTrx }}" x-show="cocokTrx($el)" class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-500">{{ \Carbon\Carbon::parse($trx->tgl_pesan ?? now())->format('H:i') }} WIB</td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-700">#{{ str_pad($trx->id_pesanan ?? 0, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">{{ $trx->pelanggan->nama_pemesan ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-600">Meja {{ $trx->meja->nomor_meja ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-800">Rp {{ number_format($trx->total_harga ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            @if(($trx->metode_pembayaran ?? '') == 'Tunai')
                                                <span class="bg-orange-50 text-orange-700 border border-orange-200 px-3 py-1 rounded-full text-xs font-bold">Tunai</span>
                                            @elseif(!empty($trx->metode_pembayaran))
                                                <span class="bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-full text-xs font-bold">{{ ucfirst(str_replace('_', ' ', $trx->metode_pembayaran)) }}</span>
                                            @else
                                                <span class="text-gray-400 text-xs font-semibold italic">Belum dipilih</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(($trx->status_pembayaran ?? '') == 'Lunas')
                                                <span class="bg-green-50 text-green-700 border border-green-200 px-3 py-1 rounded-full text-xs font-bold">Lunas</span>
                                            @elseif(($trx->status_pembayaran ?? '') == 'Gagal')
                                                <span class="bg-red-50 text-red-700 border border-red-200 px-3 py-1 rounded-full text-xs font-bold">Gagal</span>
                                            @else
                                                <span class="bg-yellow-50 text-yellow-700 border border-yellow-200 px-3 py-1 rounded-full text-xs font-bold">Belum Lunas</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(($trx->status_pembayaran ?? '') == 'Belum Lunas')
                                                <div class="flex items-center gap-2">
                                                    @if(($trx->metode_pembayaran ?? '') == 'Tunai')
                                                        <button @click="modalOpen = true; selectedId = '{{ $trx->id_pesanan }}'; selectedTotal = '{{ number_format($trx->total_harga ?? 0, 0, ',', '.') }}'; selectedMeja = '{{ $trx->meja->nomor_meja ?? '-' }}'" class="bg-kurtbeans-dark hover:bg-gray-800 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm whitespace-nowrap">
                                                            Validasi Tunai
                                                        </button>
                                                    @endif
                                                    <form method="POST" action="/kasir/batalkan/{{ $trx->id_pesanan }}" onsubmit="return confirm('Batalkan pesanan #{{ str_pad($trx->id_pesanan, 4, '0', STR_PAD_LEFT) }}?\n\nPesanan yang dibatalkan tidak dapat dikembalikan.')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap">
                                                            Batalkan
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs font-semibold">&mdash;</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="8" class="px-6 py-10 text-center text-gray-400 font-medium">Tidak ada data transaksi.</td></tr>
                                    @endforelse
                                    <tr x-show="tidakAdaHasil()" style="display:none;">
                                        <td colspan="8" class="px-6 py-10 text-center text-gray-400 font-medium">Tidak ada transaksi yang cocok dengan pencarian atau filter.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ================= TAB: ANTREAN LUNAS (B03) ================= -->
                <div x-show="activeTab === 'antrean'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @forelse($antreanPesanan ?? [] as $antrean)
                            <div class="bg-white border border-gray-100 p-6 rounded-[24px] shadow-sm hover:shadow-md transition flex flex-col">
                                <div class="flex justify-between items-start border-b border-gray-100 pb-4 mb-4">
                                    <div>
                                        <h4 class="font-bold text-3xl text-kurtbeans-dark">M{{ str_pad($antrean->meja->nomor_meja ?? 0, 2, '0', STR_PAD_LEFT) }}</h4>
                                        <p class="text-sm font-medium text-gray-500">Meja {{ $antrean->meja->nomor_meja ?? '-' }}</p>
                                    </div>
                                    <span class="text-xs font-bold bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-gray-500">{{ \Carbon\Carbon::parse($antrean->tgl_bayar ?? now())->format('H:i') }} WIB</span>
                                </div>
                                <ul class="text-sm space-y-3 mb-6 text-gray-700 flex-1 overflow-y-auto max-h-[150px] font-medium">
                                    @foreach($antrean->detailPesanan ?? [] as $detail)
                                        <li class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded-lg">
                                            <span>{{ $detail->menu->nama_menu ?? 'Menu Dihapus' }}</span>
                                            <span class="font-bold text-kurtbeans-dark bg-white px-2 py-0.5 rounded shadow-sm">x{{ $detail->quantity ?? 1 }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                                    <span class="text-xs font-bold px-3 py-1.5 rounded-lg border {{ ($antrean->metode_pembayaran ?? '') == 'Tunai' ? 'bg-orange-50 text-orange-700 border-orange-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                        {{ $antrean->metode_pembayaran ?? '' }}
                                    </span>
                                    <span class="text-xs font-black text-gray-500 uppercase tracking-wider bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200">
                                        {{ $antrean->status_pesanan ?? 'MENUNGGU' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full flex flex-col justify-center items-center h-64 bg-white rounded-[24px] border border-gray-100 shadow-sm">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-gray-500 font-bold text-lg">Antrean Kosong</p>
                                <p class="text-gray-400 font-medium text-sm mt-1">Belum ada pesanan lunas yang menunggu diproses.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </main>

        <!-- ================= MODAL VALIDASI TUNAI ================= -->
        <div x-show="modalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" @click="modalOpen = false" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-[24px] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
                    
                    <div class="bg-white px-6 pt-6 pb-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl leading-6 font-bold text-gray-800 mb-5" id="modal-title">Validasi Pembayaran Tunai</h3>
                                <div class="bg-gray-50 rounded-2xl p-5 space-y-4 border border-gray-100">
                                    <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                                        <span class="text-sm font-semibold text-gray-500">No. Transaksi</span>
                                        <span class="font-bold text-gray-800 bg-white px-2 py-1 rounded shadow-sm border border-gray-100">#<span x-text="selectedId"></span></span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                                        <span class="text-sm font-semibold text-gray-500">Meja Pelanggan</span>
                                        <span class="font-bold text-gray-800">Meja <span x-text="selectedMeja"></span></span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2">
                                        <span class="text-sm font-semibold text-gray-500">Total Harga</span>
                                        <span class="text-2xl font-bold text-kurtbeans-dark">Rp <span x-text="selectedTotal"></span></span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 mt-4 text-center font-medium">Pastikan Anda telah menerima uang tunai dari pelanggan sebelum menekan tombol Lunas.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50/80 px-6 py-4 border-t border-gray-100 flex gap-3 flex-row-reverse">
                        <form method="POST" :action="`/kasir/validasi/${selectedId}`" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-3 bg-kurtbeans-dark text-sm font-bold text-white hover:bg-gray-800 transition focus:outline-none">
                                Konfirmasi Lunas
                            </button>
                        </form>
                        <button type="button" @click="modalOpen = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-200 shadow-sm px-4 py-3 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 transition focus:outline-none">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

<script>
/*
   PEMBARUAN OTOMATIS TANPA REFRESH MANUAL
   =======================================
   Halaman menanyakan "sidik jari" keadaan terkini ke server setiap
   8 detik. Sidik jari itu ringkas (jumlah baris + waktu
   perubahan terakhir), jadi lalu lintasnya sangat kecil.

   Halaman hanya dimuat ulang ketika sidik jarinya berbeda, sehingga
   tidak ada kedipan layar selama tidak terjadi apa-apa.

   Polling dipilih daripada WebSocket agar tidak menambah kebutuhan
   server terpisah (Reverb/Pusher) yang berada di luar lingkup sistem.
*/
(function () {
    const ALAMAT   = '{{ route('kasir.sinyal') }}';
    const JEDA     = 8 * 1000;
    const KUNCI    = 'kasir_tab';
    let   sidikAwal = null;
    let   jumlahBaruSebelumnya = null;

    // Tab aktif dipersist oleh Alpine: x-data membacanya dari sessionStorage
    // saat init, dan $watch menyimpannya setiap kali berubah. Jadi pemuatan
    // ulang otomatis maupun manual (F5) selalu kembali ke tab yang sama.
    // Tidak ada lagi mekanisme tab terpisah di sini agar tak terjadi balapan.

    // --- Nada singkat saat ada pesanan baru (tanpa file audio) ---
    function bunyikan() {
        try {
            const konteks = new (window.AudioContext || window.webkitAudioContext)();
            const osilator = konteks.createOscillator();
            const gain = konteks.createGain();
            osilator.connect(gain); gain.connect(konteks.destination);
            osilator.type = 'sine';
            osilator.frequency.value = 880;
            gain.gain.setValueAtTime(0.15, konteks.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, konteks.currentTime + 0.4);
            osilator.start(); osilator.stop(konteks.currentTime + 0.4);
        } catch (e) { /* peramban memblokir audio otomatis, diabaikan */ }
    }

    async function periksa() {
        if (document.hidden) return;
        try {
            const respons = await fetch(ALAMAT, { headers: { 'Accept': 'application/json' } });
            if (!respons.ok) return;
            const data = await respons.json();

            if (sidikAwal === null) {
                sidikAwal = data.sidik;
                jumlahBaruSebelumnya = data.baru ?? null;
                return;
            }

            if (data.sidik !== sidikAwal) {
                if ((data.menunggu_validasi ?? 0) > (jumlahBaruSebelumnya ?? 0)) bunyikan();
                jumlahBaruSebelumnya = data.menunggu_validasi ?? 0;
                window.location.reload();
            }
        } catch (e) {
            console.error('Gagal memeriksa pembaruan:', e);
        }
    }

    periksa();
    setInterval(periksa, JEDA);
    document.addEventListener('visibilitychange', function () { if (!document.hidden) periksa(); });
})();
</script>

</body>
</html>