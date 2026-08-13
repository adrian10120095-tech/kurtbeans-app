<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Barista - Kurtbeans Coffee</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fallback CDN Tailwind & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

    <!-- Alpine.js Wrapper: Mengatur Tab dan Filter -->
    <div x-data="{ 
        activeTab:    (function () { try { return JSON.parse(sessionStorage.getItem('barista_tab') || '{}').activeTab || 'dashboard'; } catch (e) { return 'dashboard'; } })(),
        filterStatus: (function () { try { return JSON.parse(sessionStorage.getItem('barista_tab') || '{}').filterStatus || 'semua'; } catch (e) { return 'semua'; } })()
    }"
    x-init="
        $watch('activeTab',    () => sessionStorage.setItem('barista_tab', JSON.stringify({ activeTab, filterStatus })));
        $watch('filterStatus', () => sessionStorage.setItem('barista_tab', JSON.stringify({ activeTab, filterStatus })));
    "
    class="flex h-screen w-full">

        <!-- ================= SIDEBAR ================= -->
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
                <button @click="activeTab = 'antrean'" 
                        :class="activeTab === 'antrean' ? 'bg-white text-kurtbeans-dark shadow-md' : 'text-gray-400 hover:bg-white/10 hover:text-white'" 
                        class="w-full text-left px-5 py-4 rounded-2xl font-bold transition-all duration-200 flex items-center gap-4 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Antrean
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

        <!-- ================= MAIN CONTENT ================= -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50 relative">
            
            <!-- Topbar -->
            <header class="px-10 py-6 flex justify-between items-center bg-gray-50 z-10">
                <h2 class="text-3xl font-bold text-kurtbeans-dark" x-text="activeTab === 'dashboard' ? 'Dashboard Barista' : 'Antrean Pesanan Lunas'">Dashboard Barista</h2>
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
                        <div class="w-8 h-8 bg-kurtbeans-brown rounded-full flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        {{ Auth::user()->nama_lengkap ?? 'Barista Aktif' }}
                    </div>
                </div>
            </header>

            <!-- Scrollable Content Area -->
            <div class="flex-1 overflow-y-auto px-10 pb-10">
                
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-2xl border border-green-200 shadow-sm flex items-center gap-3 font-semibold">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Stat Cards (Berlaku untuk kedua tab sesuai Wireframe C01 & C02) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex flex-col items-center justify-center relative overflow-hidden h-32">
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-blue-50 rounded-full opacity-60"></div>
                        <span class="text-4xl font-bold text-kurtbeans-dark z-10">{{ $jumlahBaru ?? 0 }}</span>
                        <span class="text-sm text-gray-500 mt-1 z-10 font-bold uppercase tracking-wider">Antrean Masuk (Lunas)</span>
                    </div>
                    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex flex-col items-center justify-center relative overflow-hidden h-32">
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-orange-50 rounded-full opacity-60"></div>
                        <span class="text-4xl font-bold text-orange-600 z-10">{{ $jumlahDiproses ?? 0 }}</span>
                        <span class="text-sm text-gray-500 mt-1 z-10 font-bold uppercase tracking-wider">Sedang Diproses</span>
                    </div>
                    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex flex-col items-center justify-center relative overflow-hidden h-32">
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-green-50 rounded-full opacity-60"></div>
                        <span class="text-4xl font-bold text-green-600 z-10">{{ $jumlahSelesai ?? 0 }}</span>
                        <span class="text-sm text-gray-500 mt-1 z-10 font-bold uppercase tracking-wider">Diambil Hari Ini</span>
                    </div>
                </div>

                <!-- Filters (Hanya muncul di Tab Antrean - Sesuai C02) -->
                <div x-show="activeTab === 'antrean'" class="mb-6 flex space-x-3 bg-white p-2 rounded-2xl w-max shadow-sm border border-gray-100" style="display: none;">
                    <button @click="filterStatus = 'semua'" :class="filterStatus === 'semua' ? 'bg-kurtbeans-dark text-white' : 'text-gray-500 hover:bg-gray-100'" class="px-5 py-2 rounded-xl text-sm font-bold transition">Semua</button>
                    <button @click="filterStatus = 'baru'" :class="filterStatus === 'baru' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100'" class="px-5 py-2 rounded-xl text-sm font-bold transition">Baru</button>
                    <button @click="filterStatus = 'diproses'" :class="filterStatus === 'diproses' ? 'bg-orange-500 text-white' : 'text-gray-500 hover:bg-gray-100'" class="px-5 py-2 rounded-xl text-sm font-bold transition">Diproses</button>
                    <button @click="filterStatus = 'selesai'" :class="filterStatus === 'selesai' ? 'bg-green-600 text-white' : 'text-gray-500 hover:bg-gray-100'" class="px-5 py-2 rounded-xl text-sm font-bold transition">Selesai</button>
                </div>

                <!-- ================= PANEL FOKUS (hanya Tab Dashboard) ================= -->
                <div x-show="activeTab === 'dashboard'" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                    <!-- Pesanan yang harus dikerjakan berikutnya -->
                    <div class="lg:col-span-2 bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-800">Fokus Sekarang</h3>
                            <span class="text-xs text-gray-400 font-semibold">Pesanan terlama yang belum selesai</span>
                        </div>
                        <div class="p-8">
                            @if($fokus)
                                <div class="flex items-start gap-6">
                                    <div class="text-center shrink-0">
                                        <span class="block text-5xl font-black text-kurtbeans-dark leading-none">{{ $fokus['id'] }}</span>
                                        <span class="block text-xs font-bold text-gray-400 mt-2 uppercase tracking-wider">Meja {{ $fokus['meja'] }}</span>
                                    </div>
                                    <div class="flex-1 border-l border-gray-100 pl-6">
                                        <p class="font-bold text-gray-800 mb-1">{{ $fokus['pemesan'] }}</p>
                                        <p class="text-xs text-gray-400 font-semibold mb-3">Masuk {{ $fokus['waktu'] }} WIB</p>
                                        <ul class="text-sm text-gray-600 font-medium space-y-1">
                                            @foreach($fokus['menu'] as $m)
                                                <li>&bull; {{ $m }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <button @click="activeTab = 'antrean'" class="mt-6 w-full bg-kurtbeans-dark hover:bg-gray-800 text-white font-bold py-3 rounded-xl text-sm transition">
                                    Buka Papan Antrean
                                </button>
                            @else
                                <div class="text-center py-10">
                                    <p class="text-gray-400 font-semibold">Tidak ada pesanan yang perlu dikerjakan.</p>
                                    <p class="text-xs text-gray-300 mt-1">Semua antrean sudah bersih.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Menunggu diambil pelanggan -->
                    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                        <div class="px-6 py-5 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800">Menunggu Diambil</h3>
                        </div>
                        <div class="flex-1 p-6 space-y-3 overflow-y-auto max-h-[320px]">
                            @forelse($antreanSiap ?? [] as $s)
                                <div class="flex items-center justify-between border border-gray-100 rounded-xl px-4 py-3 bg-gray-50/50"
                                     data-kartu-siap data-siap-pada="{{ $s['siap_pada'] }}">
                                    <div>
                                        <span class="block font-black text-green-600">{{ $s['id'] }}</span>
                                        <span class="block text-[11px] text-gray-500 font-semibold">Meja {{ $s['meja'] }} &middot; {{ $s['pemesan'] }}</span>
                                    </div>
                                    <span class="text-[11px] font-bold text-gray-400" data-penanda-tunggu>&mdash;</span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 italic text-center py-6">Tidak ada yang menunggu.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- ================= STATUS BOARD KANBAN (hanya Tab Antrean) ================= -->
                <div x-show="activeTab === 'antrean'" style="display: none;" class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden flex flex-col mb-8">
                    <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">Antrean Pesanan Lunas — Status Board</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-100 p-6 gap-6 lg:gap-0 lg:p-0 min-h-[400px]">
                        
                        <!-- KOLOM 1: BARU / MASUK -->
                        <div class="bg-gray-50/30 p-6" x-show="filterStatus === 'semua' || filterStatus === 'baru'">
                            <div class="flex items-center gap-2 mb-6">
                                <div class="w-3 h-3 bg-blue-600 rounded-sm"></div>
                                <h4 class="font-bold text-sm text-gray-800 tracking-wider uppercase">BARU / MASUK</h4>
                            </div>
                            
                            <div class="space-y-4" id="kolom-baru" data-kosong="Belum ada pesanan baru.">
                                <!-- Dummy Data Mapping / Gunakan Foreach di sini -->
                                @forelse($antreanBaru ?? [] as $baru)
                                <div class="kartu-antrean bg-white border border-gray-200 p-5 rounded-2xl shadow-sm hover:shadow-md transition" data-id-pesanan="{{ $baru['id_pesanan'] }}">
                                    <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-3">
                                        <div>
                                            <h5 class="font-black text-2xl text-kurtbeans-dark">{{ $baru['id'] ?? '-' }}</h5>
                                            <p class="text-xs text-gray-500 font-semibold mt-0.5">{{ $baru['pemesan'] ?? '-' }}</p>
                                            <span class="text-xs font-bold bg-blue-50 text-blue-700 px-2 py-1 rounded">Meja {{ $baru['meja'] ?? '-' }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-400">{{ $baru['waktu'] ?? '00:00' }} WIB</span>
                                    </div>
                                    <ul class="text-sm text-gray-600 font-medium space-y-1.5 mb-5 min-h-[60px]">
                                        @foreach($baru['menu'] ?? [] as $m)
                                            <li>• {{ $m }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" onclick="aksiBarista(this, {{ $baru['id_pesanan'] }}, 'proses')" class="w-full bg-kurtbeans-dark hover:bg-gray-800 text-white font-bold py-2.5 rounded-xl shadow-sm transition flex items-center justify-center gap-2 text-sm">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                                        Mulai Proses
                                    </button>
                                    <p class="text-[10px] text-center text-gray-400 mt-2 font-medium italic">&rarr; notif FCM ke pelanggan</p>
                                </div>
                                @empty
                                    <p class="text-sm text-gray-400 italic text-center py-4">Belum ada pesanan baru.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- KOLOM 2: SEDANG DIPROSES -->
                        <div class="bg-orange-50/20 p-6" x-show="filterStatus === 'semua' || filterStatus === 'diproses'">
                            <div class="flex items-center gap-2 mb-6">
                                <div class="w-3 h-3 bg-orange-500 rounded-sm"></div>
                                <h4 class="font-bold text-sm text-gray-800 tracking-wider uppercase">SEDANG DIPROSES</h4>
                            </div>
                            
                            <div class="space-y-4" id="kolom-diproses" data-kosong="Tidak ada pesanan diproses.">
                                @forelse($antreanDiproses ?? [] as $proses)
                                <div class="kartu-antrean bg-white border-2 border-orange-200 p-5 rounded-2xl shadow-sm hover:shadow-md transition" data-id-pesanan="{{ $proses['id_pesanan'] }}">
                                    <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-3">
                                        <div>
                                            <h5 class="font-black text-2xl text-orange-600">{{ $proses['id'] ?? '-' }}</h5>
                                            <p class="text-xs text-gray-500 font-semibold mt-0.5">{{ $proses['pemesan'] ?? '-' }}</p>
                                            <span class="text-xs font-bold bg-orange-100 text-orange-800 px-2 py-1 rounded">Meja {{ $proses['meja'] ?? '-' }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-400">{{ $proses['waktu'] ?? '00:00' }} WIB</span>
                                    </div>
                                    <ul class="text-sm text-gray-600 font-medium space-y-1.5 mb-5 min-h-[60px]">
                                        @foreach($proses['menu'] ?? [] as $m)
                                            <li>• {{ $m }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" onclick="aksiBarista(this, {{ $proses['id_pesanan'] }}, 'selesai')" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 rounded-xl shadow-sm transition flex items-center justify-center gap-2 text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Tandai Selesai
                                    </button>
                                    <p class="text-[10px] text-center text-gray-400 mt-2 font-medium italic">&rarr; notif FCM ke pelanggan</p>
                                </div>
                                @empty
                                    <p class="text-sm text-gray-400 italic text-center py-4">Tidak ada pesanan diproses.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- KOLOM 3: SIAP DIAMBIL (menunggu diserahkan ke pelanggan) -->
                        <div class="bg-gray-50/50 p-6" x-show="filterStatus === 'semua' || filterStatus === 'selesai'">
                            <div class="flex items-center gap-2 mb-6">
                                <div class="w-3 h-3 bg-green-500 rounded-sm"></div>
                                <h4 class="font-bold text-sm text-gray-800 tracking-wider uppercase">SIAP DIAMBIL</h4>
                            </div>

                            <div class="space-y-4" id="kolom-siap" data-kosong="Tidak ada pesanan menunggu diambil.">
                                @forelse($antreanSiap ?? [] as $siap)
                                <div class="kartu-antrean bg-white border border-green-200 p-5 rounded-2xl shadow-sm transition-colors"
                                     data-kartu-siap data-siap-pada="{{ $siap['siap_pada'] }}" data-id-pesanan="{{ $siap['id_pesanan'] }}">
                                    <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-3">
                                        <div>
                                            <h5 class="font-black text-2xl text-green-600" data-nomor>{{ $siap['id'] ?? '-' }}</h5>
                                            <p class="text-xs text-gray-500 font-semibold mt-0.5">{{ $siap['pemesan'] ?? '-' }}</p>
                                            <span class="text-xs font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded">Meja {{ $siap['meja'] ?? '-' }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-400">{{ $siap['selesai'] ?? '-' }} WIB</span>
                                    </div>

                                    <ul class="text-sm text-gray-600 font-medium space-y-1.5 mb-4 min-h-[60px]">
                                        @foreach($siap['menu'] ?? [] as $m)
                                            <li>&bull; {{ $m }}</li>
                                        @endforeach
                                    </ul>

                                    {{-- Penanda waktu tunggu. Angkanya dihitung ulang tiap
                                         detik oleh peramban, jadi tetap berjalan walaupun
                                         halaman belum dimuat ulang. --}}
                                    <div class="mb-3 text-center text-xs font-bold rounded-lg py-2 bg-gray-50 text-gray-500 border border-gray-200"
                                         data-penanda-tunggu>
                                        Menghitung...
                                    </div>

                                    <button type="button" onclick="aksiBarista(this, {{ $siap['id_pesanan'] }}, 'diambil')" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl text-sm transition shadow-sm">
                                        Sudah Diambil
                                    </button>
                                </div>
                                @empty
                                    <p class="text-sm text-gray-400 italic text-center py-4">Tidak ada pesanan menunggu diambil.</p>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ================= TABEL RIWAYAT (hanya Tab Dashboard) ================= -->
                <div x-show="activeTab === 'dashboard'" class="bg-white rounded-[24px] shadow-sm border border-gray-100 flex flex-col overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800">Riwayat Pesanan Diserahkan Hari Ini</h3>
                    </div>
                    <div class="overflow-x-auto p-4">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/80 text-gray-500 text-sm border-b border-gray-100">
                                    <th class="px-6 py-4 font-semibold rounded-tl-xl">No. Antrean</th>
                                    <th class="px-6 py-4 font-semibold">Waktu Masuk</th>
                                    <th class="px-6 py-4 font-semibold">Menu Utama</th>
                                    <th class="px-6 py-4 font-semibold">Meja</th>
                                    <th class="px-6 py-4 font-semibold rounded-tr-xl">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($riwayatSelesai ?? [] as $riwayat)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-kurtbeans-dark">{{ $riwayat['id'] }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-500">{{ $riwayat['waktu'] }} WIB</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-700">{{ $riwayat['menu'] }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Meja {{ $riwayat['meja'] }}</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-green-50 text-green-700 border border-green-200 px-3 py-1 rounded-md text-xs font-bold">Selesai</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400 font-medium">Belum ada pesanan yang diserahkan hari ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

<script>
/*
   PEMBARUAN OTOMATIS TANPA REFRESH MANUAL
   =======================================
   Halaman menanyakan "sidik jari" keadaan terkini ke server setiap
   6 detik. Sidik jari itu ringkas (jumlah baris + waktu
   perubahan terakhir), jadi lalu lintasnya sangat kecil.

   Halaman hanya dimuat ulang ketika sidik jarinya berbeda, sehingga
   tidak ada kedipan layar selama tidak terjadi apa-apa.

   Polling dipilih daripada WebSocket agar tidak menambah kebutuhan
   server terpisah (Reverb/Pusher) yang berada di luar lingkup sistem.
*/
(function () {
    const ALAMAT   = '{{ route('barista.sinyal') }}';
    const JEDA     = 6 * 1000;
    const KUNCI    = 'barista_tab';
    let   sidikAwal = null;
    let   jumlahBaruSebelumnya = null;

    // Tab & filter aktif dipersist oleh Alpine: x-data membacanya dari
    // sessionStorage saat init, dan $watch menyimpannya setiap kali berubah.
    // Jadi pemuatan ulang otomatis maupun manual (F5) selalu kembali ke tab
    // yang sama. Tidak ada lagi mekanisme tab terpisah di sini agar tak
    // terjadi balapan.

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
                if ((data.baru ?? 0) > (jumlahBaruSebelumnya ?? 0)) bunyikan();
                jumlahBaruSebelumnya = data.baru ?? 0;
                window.location.reload();
            }
        } catch (e) {
            console.error('Gagal memeriksa pembaruan:', e);
        }
    }

    // Dipanggil setelah aksi lokal (AJAX barista) agar polling TIDAK
    // memuat ulang halaman untuk perubahan yang sudah kita tampilkan sendiri.
    window.baristaResyncSidik = async function () {
        try {
            const r = await fetch(ALAMAT, { headers: { 'Accept': 'application/json' } });
            if (!r.ok) return;
            const d = await r.json();
            sidikAwal = d.sidik;
            jumlahBaruSebelumnya = d.baru ?? jumlahBaruSebelumnya;
        } catch (e) { /* diabaikan */ }
    };

    periksa();
    setInterval(periksa, JEDA);
    document.addEventListener('visibilitychange', function () { if (!document.hidden) periksa(); });
})();
</script>


<script>
/*
   PENGHITUNG WAKTU TUNGGU
   =======================
   Angka "menunggu N menit" dihitung ulang oleh peramban setiap detik
   berdasarkan waktu pesanan dinyatakan siap. Kalau angka ini dirender
   server, nilainya akan membeku sampai halaman dimuat ulang — padahal
   pemuatan ulang otomatis hanya terjadi bila ADA perubahan data,
   sedangkan waktu tunggu terus berjalan tanpa perubahan data apa pun.
*/
(function () {
    const AMBANG = 5; // menit; lewat ini kartu berubah merah

    function perbarui() {
        document.querySelectorAll('[data-kartu-siap]').forEach(function (kartu) {
            const mulai = kartu.dataset.siapPada;
            const penanda = kartu.querySelector('[data-penanda-tunggu]');
            if (!mulai || !penanda) return;

            const menit = Math.max(0, Math.floor((Date.now() - new Date(mulai).getTime()) / 60000));
            const mendesak = menit >= AMBANG;

            // Kartu ringkas di tab Dashboard hanya menampilkan angka
            if (penanda.tagName === 'SPAN') {
                penanda.textContent = menit + ' mnt';
                penanda.className = 'text-[11px] font-bold ' + (mendesak ? 'text-red-600' : 'text-gray-400');
                return;
            }

            penanda.textContent = mendesak
                ? 'Menunggu ' + menit + ' menit — panggil pelanggan'
                : 'Menunggu ' + menit + ' menit';

            penanda.className = 'mb-3 text-center text-xs font-bold rounded-lg py-2 '
                + (mendesak ? 'bg-red-50 text-red-700 border border-red-200'
                            : 'bg-gray-50 text-gray-500 border border-gray-200');

            kartu.className = 'bg-white border p-5 rounded-2xl shadow-sm transition-colors '
                + (mendesak ? 'border-red-300 ring-2 ring-red-100' : 'border-green-200');

            const nomor = kartu.querySelector('[data-nomor]');
            if (nomor) {
                nomor.className = 'font-black text-2xl ' + (mendesak ? 'text-red-600' : 'text-green-600');
            }
        });
    }

    perbarui();
    setInterval(perbarui, 1000);
})();
</script>
<script>
/*
   AKSI BARISTA TANPA PINDAH HALAMAN
   =================================
   Tombol "Mulai Proses / Tandai Selesai / Sudah Diambil" dikirim lewat
   AJAX (fetch), lalu kartunya dipindahkan langsung ke kolom tujuan —
   halaman TIDAK dimuat ulang dan tab tetap di "Antrean". Sidik jari
   polling diselaraskan setelahnya supaya pembaruan otomatis tidak ikut
   memuat ulang untuk perubahan yang sudah kita tampilkan sendiri.
*/
async function aksiBarista(btn, id, tipe) {
    const kartu = btn.closest('[data-id-pesanan]');   // data-* bertahan walau className kartu ditimpa timer
    const labelAsli = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Memproses…';

    try {
        const res = await fetch('/barista/' + tipe + '/' + id, {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || data.status !== 'success') {
            throw new Error(data.message || 'Aksi gagal. Silakan coba lagi.');
        }
        pindahkanKartu(btn, kartu, id, tipe, data);
        if (window.baristaResyncSidik) window.baristaResyncSidik();
    } catch (e) {
        btn.disabled = false;
        btn.innerHTML = labelAsli;
        alert(e.message || 'Terjadi kesalahan. Silakan coba lagi.');
    }
}

// Menampilkan / menyembunyikan teks "kosong" sebuah kolom sesuai isinya.
function periksaKolomKosong(kolom) {
    if (!kolom) return;
    const adaKartu = kolom.querySelector(':scope > [data-id-pesanan]');
    let ph = kolom.querySelector(':scope > p.italic');
    if (adaKartu) {
        if (ph) ph.remove();
    } else if (!ph) {
        ph = document.createElement('p');
        ph.className = 'text-sm text-gray-400 italic text-center py-4';
        ph.textContent = kolom.dataset.kosong || 'Kosong.';
        kolom.appendChild(ph);
    }
}

function tombolBerikut(id, tipe) {
    if (tipe === 'selesai') {
        return '<button type="button" onclick="aksiBarista(this, ' + id + ', \'selesai\')" '
            + 'class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 rounded-xl shadow-sm transition flex items-center justify-center gap-2 text-sm">'
            + '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>'
            + 'Tandai Selesai</button>';
    }
    // diambil
    return '<button type="button" onclick="aksiBarista(this, ' + id + ', \'diambil\')" '
        + 'class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl text-sm transition shadow-sm">'
        + 'Sudah Diambil</button>';
}

function pindahkanKartu(btn, kartu, id, tipe, data) {
    if (!kartu) return;
    const sumber = kartu.parentElement;

    // ---- Diserahkan ke pelanggan: kartu hilang ----
    if (tipe === 'diambil') {
        kartu.style.transition = 'opacity .25s ease, transform .25s ease';
        kartu.style.opacity = '0';
        kartu.style.transform = 'scale(.96)';
        setTimeout(function () { kartu.remove(); periksaKolomKosong(sumber); }, 250);
        return;
    }

    // ---- Mulai diproses: pindah ke kolom "Sedang Diproses" (oranye) ----
    if (tipe === 'proses') {
        const dest = document.getElementById('kolom-diproses');
        btn.outerHTML = tombolBerikut(id, 'selesai');
        const h5 = kartu.querySelector('h5'); if (h5) h5.className = 'font-black text-2xl text-orange-600';
        kartu.className = 'kartu-antrean bg-white border-2 border-orange-200 p-5 rounded-2xl shadow-sm hover:shadow-md transition';
        if (dest) dest.appendChild(kartu);
        periksaKolomKosong(sumber); periksaKolomKosong(dest);
        return;
    }

    // ---- Selesai dibuat: pindah ke kolom "Siap Diambil" (hijau) ----
    if (tipe === 'selesai') {
        const dest = document.getElementById('kolom-siap');
        // Siapkan penanda waktu tunggu sebelum tombol baru.
        if (!kartu.querySelector('[data-penanda-tunggu]')) {
            const wt = document.createElement('div');
            wt.setAttribute('data-penanda-tunggu', '');
            wt.className = 'mb-3 text-center text-xs font-bold rounded-lg py-2 bg-gray-50 text-gray-500 border border-gray-200';
            wt.textContent = 'Menghitung…';
            btn.parentNode.insertBefore(wt, btn);
        }
        btn.outerHTML = tombolBerikut(id, 'diambil');
        const h5 = kartu.querySelector('h5'); if (h5) { h5.className = 'font-black text-2xl text-green-600'; h5.setAttribute('data-nomor', ''); }
        // Atribut yang membuat penghitung waktu tunggu mulai berjalan.
        kartu.setAttribute('data-kartu-siap', '');
        kartu.setAttribute('data-siap-pada', data.siap_pada || new Date().toISOString());
        kartu.className = 'kartu-antrean bg-white border border-green-200 p-5 rounded-2xl shadow-sm transition-colors';
        if (dest) dest.appendChild(kartu);
        periksaKolomKosong(sumber); periksaKolomKosong(dest);
        return;
    }
}
</script>

</body>
</html>