<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kurtbeans Coffee</title>
    <!-- Tailwind CSS (via CDN untuk development) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        kurtbeans: {
                            dark: '#1a2b29',    // Hijau Gelap
                            light: '#f9f6f0',   // Krem Terang
                            accent: '#2b1a10',  // Cokelat Espresso
                            accentHover: '#3d2517',
                            border: '#e2ddcc'
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }

        /* Penomoran baris tabel menu.
           Dihitung CSS, bukan Blade, karena baris yang disembunyikan
           penyaring memakai display:none dan tidak ikut dihitung —
           jadi nomornya selalu 1,2,3 walau sebagian baris tersaring.
           Kalau memakai $loop->iteration, nomornya akan melompat-lompat. */
        tbody.penomoran-baris { counter-reset: baris; }
        tbody.penomoran-baris > tr { counter-increment: baris; }
        tbody.penomoran-baris > tr .nomor-baris::before { content: counter(baris); }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>

<body class="bg-gray-50 font-sans text-gray-800" 
      x-data="{ 
          // Menyimpan posisi tab di LocalStorage agar tidak reset saat refresh
          activeTab: localStorage.getItem('kurtbeans_admin_tab') || 'dashboard', 
          sidebarOpen: false, 
          
          // Fitur Pencarian (Live Search)
          searchMenu: '',
          searchKategori: '',
          searchMeja: '',
          searchPengguna: '',

          // ---- Modal Edit Meja & Pengguna ----
          showEditMeja: false,
          editMeja: { id: '', nomor_meja: '', status_meja: 'Tersedia' },
          bukaEditMeja(m) { this.editMeja = { id: m.id, nomor_meja: m.nomor_meja, status_meja: m.status_meja }; this.showEditMeja = true; },
          showEditPengguna: false,
          editPengguna: { id: '', nama_lengkap: '', username: '', role: 'Kasir' },
          bukaEditPengguna(p) { this.editPengguna = { id: p.id, nama_lengkap: p.nama_lengkap, username: p.username, role: p.role }; this.showEditPengguna = true; },

          // ---- Penyaring menu berdasarkan kategori ----
          // 'all' = tampilkan semua. Selain itu berisi id_kategori (string).
          filterKategoriMenu: 'all',

          // Salinan ringkas daftar menu, dipakai HANYA untuk menghitung
          // berapa baris yang sedang tampil (tabelnya sendiri tetap
          // dirender Blade, bukan JavaScript).
          menusRingkas: @js($menus->map(fn ($m) => [
              'nama' => mb_strtolower($m->nama_menu),
              'kat'  => (string) $m->id_kategori,
          ])->values()),

          // Daftar id kategori yang masih ada. Dipakai untuk memulihkan
          // penyaring bila kategori yang tersimpan sudah dihapus admin.
          kategoriValid: @js($kategoris->pluck('id_kategori')->map(fn ($id) => (string) $id)->values()),

          // Satu baris menu tampil bila lolos pencarian DAN penyaring kategori.
          cocokMenu(nama, kat) {
              const kunci = this.searchMenu.toLowerCase().trim();
              const lolosCari = kunci === '' || nama.includes(kunci);
              const lolosKategori = this.filterKategoriMenu === 'all'
                                 || this.filterKategoriMenu === kat;
              return lolosCari && lolosKategori;
          },

          get jumlahMenuTampil() {
              return this.menusRingkas.filter(m => this.cocokMenu(m.nama, m.kat)).length;
          },

          // State Modal Tambah
          showModalPengguna: false, 
          showModalKategori: false, 
          showModalMenu: false, 
          showModalMeja: false,

          // State Modal Edit
          showEditMenu: false,
          showEditKategori: false,

          // Data untuk di-edit
          editMenuData: {},
          editKategoriData: {},

          // Method membuka form edit
          openEditMenu(data) {
              this.editMenuData = JSON.parse(JSON.stringify(data));
              this.showEditMenu = true;
          },
          openEditKategori(data) {
              this.editKategoriData = JSON.parse(JSON.stringify(data));
              this.showEditKategori = true;
          }
      }"
      x-init="
          // Pulihkan penyaring kategori yang terakhir dipakai. Kalau kategorinya
          // sudah dihapus, kembalikan ke 'Semua' supaya tabel tidak tampak
          // kosong tanpa sebab yang jelas.
          filterKategoriMenu = (function (tersimpan, sah) {
              return (tersimpan === 'all' || sah.includes(tersimpan)) ? tersimpan : 'all';
          })(localStorage.getItem('kurtbeans_admin_filter_kategori') || 'all', kategoriValid);

          $watch('activeTab', value => localStorage.setItem('kurtbeans_admin_tab', value));
          $watch('filterKategoriMenu', value => localStorage.setItem('kurtbeans_admin_filter_kategori', value));
      ">

    <div class="flex h-screen overflow-hidden">

        <!-- Overlay Sidebar Mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-gray-900/50 backdrop-blur-sm lg:hidden" x-cloak></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-kurtbeans-dark text-white shadow-2xl transition-transform duration-300 lg:static lg:translate-x-0 flex flex-col">
              <div class="border border-kurtbeans-light/5 bg-white/2 p-3 w-full text-center rounded-xl shadow-inner">
                    <img src="{{ asset('images/logo3.png') }}" alt="Kurtbeans Coffee" class="w-32 mx-auto">
                </div>

            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto text-sm font-medium">
                <button @click="activeTab = 'dashboard'; sidebarOpen = false" :class="activeTab === 'dashboard' ? 'bg-kurtbeans-light text-kurtbeans-dark font-bold shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white'" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-layer-group w-6"></i> Dashboard
                </button>
                <button @click="activeTab = 'menu'; sidebarOpen = false" :class="activeTab === 'menu' ? 'bg-kurtbeans-light text-kurtbeans-dark font-bold shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white'" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-mug-hot w-6"></i> Kelola Menu
                </button>
                <button @click="activeTab = 'kategori'; sidebarOpen = false" :class="activeTab === 'kategori' ? 'bg-kurtbeans-light text-kurtbeans-dark font-bold shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white'" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-tags w-6"></i> Kategori
                </button>
                <button @click="activeTab = 'meja'; sidebarOpen = false" :class="activeTab === 'meja' ? 'bg-kurtbeans-light text-kurtbeans-dark font-bold shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white'" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-qrcode w-6"></i> Meja & QR Code
                </button>
                <button @click="activeTab = 'pengguna'; sidebarOpen = false" :class="activeTab === 'pengguna' ? 'bg-kurtbeans-light text-kurtbeans-dark font-bold shadow-md' : 'text-gray-300 hover:bg-white/10 hover:text-white'" class="w-full flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                    <i class="fa-solid fa-users w-6"></i> Kelola Pengguna
                </button>
            </nav>

            <div class="p-4 border-t border-white/10">
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white rounded-xl transition-colors font-semibold text-sm">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden relative">
            
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-6 lg:px-10 z-10 shadow-sm">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-kurtbeans-dark transition-colors">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                    <h2 class="text-2xl font-bold text-gray-800 tracking-tight" x-text="activeTab === 'dashboard' ? 'Dashboard Admin' : (activeTab === 'meja' ? 'Daftar Meja & QR Code' : 'Daftar ' + activeTab.charAt(0).toUpperCase() + activeTab.slice(1))"></h2>
                </div>
                <div class="flex items-center gap-5">
                    <div class="hidden md:block text-sm font-medium text-gray-500 bg-gray-100 px-4 py-1.5 rounded-full">
                        <i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d M Y') }}
                    </div>
                    <div class="flex items-center gap-3 bg-kurtbeans-light px-4 py-1.5 rounded-full border border-kurtbeans-border shadow-sm">
                        <div class="w-7 h-7 rounded-full bg-kurtbeans-accent text-white flex items-center justify-center text-xs font-bold">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <span class="text-sm font-bold text-kurtbeans-dark">Admin</span>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-6 lg:p-10 bg-[#f8f9fa]">

                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center shadow-sm">
                    <i class="fa-solid fa-circle-check mr-2"></i> <span class="font-medium">{{ session('success') }}</span>
                </div>
                @endif
                @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center shadow-sm">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i> <span class="font-medium">{{ session('error') }}</span>
                </div>
                @endif
                @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm">
                    <div class="flex items-center font-bold mb-1"><i class="fa-solid fa-circle-exclamation mr-2"></i> Data gagal disimpan, periksa kembali:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- TAB DASHBOARD -->
                <div x-show="activeTab === 'dashboard'" x-transition.opacity>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center hover:shadow-md transition-shadow relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 w-16 h-16 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                            <p class="text-4xl font-bold text-gray-900 relative z-10">{{ collect($menus)->where('status_menu', 'Tersedia')->count() }}</p>
                            <p class="text-sm text-gray-500 font-medium mt-1 relative z-10">Total Menu Aktif</p>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center hover:shadow-md transition-shadow relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 w-16 h-16 bg-green-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                            <p class="text-4xl font-bold text-gray-900 relative z-10">{{ count($mejas) }}</p>
                            <p class="text-sm text-gray-500 font-medium mt-1 relative z-10">Total Meja Terdaftar</p>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center hover:shadow-md transition-shadow relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 w-16 h-16 bg-purple-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                            <p class="text-4xl font-bold text-gray-900 relative z-10">{{ count($penggunas) }}</p>
                            <p class="text-sm text-gray-500 font-medium mt-1 relative z-10">Total Pengguna</p>
                        </div>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center hover:shadow-md transition-shadow relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 w-16 h-16 bg-orange-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                            <p class="text-4xl font-bold text-gray-900 relative z-10">{{ count($kategoris) }}</p>
                            <p class="text-sm text-gray-500 font-medium mt-1 relative z-10">Total Kategori</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Quick Links menggunakan variabel activeTab Alpine -->
                            <button @click="activeTab = 'menu'" class="bg-white p-6 rounded-2xl border border-gray-100 text-center hover:border-kurtbeans-accent hover:shadow-md transition-all group">
                                <div class="w-12 h-12 mx-auto rounded-xl bg-gray-50 flex items-center justify-center mb-3 text-gray-600 group-hover:bg-kurtbeans-accent group-hover:text-white transition-colors"><i class="fa-solid fa-mug-hot text-xl"></i></div>
                                <h4 class="font-bold text-gray-800">Kelola Menu</h4>
                                <p class="text-xs text-gray-400 mt-1">Tambah / Edit / Hapus</p>
                            </button>
                            <button @click="activeTab = 'meja'" class="bg-white p-6 rounded-2xl border border-gray-100 text-center hover:border-kurtbeans-accent hover:shadow-md transition-all group">
                                <div class="w-12 h-12 mx-auto rounded-xl bg-gray-50 flex items-center justify-center mb-3 text-gray-600 group-hover:bg-kurtbeans-accent group-hover:text-white transition-colors"><i class="fa-solid fa-qrcode text-xl"></i></div>
                                <h4 class="font-bold text-gray-800">Meja & QR Code</h4>
                                <p class="text-xs text-gray-400 mt-1">Generate / Download</p>
                            </button>
                            <button @click="activeTab = 'kategori'" class="bg-white p-6 rounded-2xl border border-gray-100 text-center hover:border-kurtbeans-accent hover:shadow-md transition-all group">
                                <div class="w-12 h-12 mx-auto rounded-xl bg-gray-50 flex items-center justify-center mb-3 text-gray-600 group-hover:bg-kurtbeans-accent group-hover:text-white transition-colors"><i class="fa-solid fa-tags text-xl"></i></div>
                                <h4 class="font-bold text-gray-800">Kategori</h4>
                                <p class="text-xs text-gray-400 mt-1">Tambah / Edit / Hapus</p>
                            </button>
                            <button @click="activeTab = 'pengguna'" class="bg-white p-6 rounded-2xl border border-gray-100 text-center hover:border-kurtbeans-accent hover:shadow-md transition-all group">
                                <div class="w-12 h-12 mx-auto rounded-xl bg-gray-50 flex items-center justify-center mb-3 text-gray-600 group-hover:bg-kurtbeans-accent group-hover:text-white transition-colors"><i class="fa-solid fa-users text-xl"></i></div>
                                <h4 class="font-bold text-gray-800">Pengguna</h4>
                                <p class="text-xs text-gray-400 mt-1">Kelola Staff / Jabatan</p>
                            </button>
                        </div>
                        
                        <!-- Menu Terlaris (Sekarang menggunakan data dinamis) -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-800">Menu Terlaris Bulan Ini</h3>
                                <span class="text-xs bg-kurtbeans-light text-kurtbeans-dark px-2 py-1 rounded font-bold">Top 4</span>
                            </div>
                            <div class="p-6 flex-1 flex flex-col justify-center space-y-5">
                                @forelse($menuTerlaris ?? [] as $index => $terlaris)
                                <div class="flex items-center gap-4 text-sm">
                                    <div class="w-8 h-8 rounded-full {{ $index == 0 ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600' }} flex items-center justify-center font-bold">{{ $loop->iteration }}</div>
                                    <div class="w-32 font-semibold text-gray-700 truncate">{{ $terlaris->nama_menu }}</div>
                                    <div class="flex-1 bg-gray-100 h-2.5 rounded-full overflow-hidden">
                                        <div class="bg-kurtbeans-dark h-full rounded-full {{ $index > 0 ? 'opacity-80' : '' }}" style="width: {{ max(20, 100 - ($index * 25)) }}%"></div>
                                    </div>
                                    <div class="text-xs font-bold text-gray-500 w-10 text-right">{{ $terlaris->total_terjual }}x</div>
                                </div>
                                @empty
                                <div class="text-center text-gray-500 text-sm py-4">
                                    Belum ada data pesanan bulan ini.
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB MENU -->
                <div x-show="activeTab === 'menu'" x-cloak x-transition.opacity>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
                        <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                            <div class="flex w-full md:w-auto gap-3 flex-1">
                                <div class="relative w-full max-w-sm">
                                    <i class="fa-solid fa-search absolute left-4 top-3.5 text-gray-400"></i>
                                    <!-- Alpine Live Search -->
                                    <input type="text" x-model="searchMenu" placeholder="Cari nama menu..." class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-2.5 text-sm focus:border-kurtbeans-accent focus:ring-1 focus:ring-kurtbeans-accent outline-none transition-all">
                                </div>
                            </div>
                            <button @click="showModalMenu = true" class="w-full md:w-auto bg-kurtbeans-accent hover:bg-kurtbeans-accentHover text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm transition-all transform hover:scale-105 whitespace-nowrap">
                                <i class="fa-solid fa-plus mr-2"></i> Tambah Menu
                            </button>
                        </div>

                        <!-- ============ PENYARING KATEGORI ============
                             Angka di setiap pil adalah jumlah menu pada kategori
                             tersebut, supaya admin langsung tahu isi tiap kategori
                             tanpa harus membukanya satu per satu. -->
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-3">
                                <span class="hidden sm:block text-[11px] font-bold uppercase tracking-wider text-gray-400 whitespace-nowrap">Kategori</span>

                                <div class="flex gap-2 overflow-x-auto pb-1 -mb-1">
                                    <button type="button" @click="filterKategoriMenu = 'all'"
                                        :class="filterKategoriMenu === 'all'
                                            ? 'bg-kurtbeans-accent text-white border-kurtbeans-accent'
                                            : 'bg-white text-gray-600 border-gray-200 hover:border-kurtbeans-accent'"
                                        class="shrink-0 border rounded-xl px-4 py-2 text-sm font-bold transition-all whitespace-nowrap">
                                        Semua
                                        <span class="ml-1.5 text-xs font-semibold opacity-70">{{ $menus->count() }}</span>
                                    </button>

                                    @foreach($kategoris as $k)
                                    <button type="button" @click="filterKategoriMenu = '{{ $k->id_kategori }}'"
                                        :class="filterKategoriMenu === '{{ $k->id_kategori }}'
                                            ? 'bg-kurtbeans-accent text-white border-kurtbeans-accent'
                                            : 'bg-white text-gray-600 border-gray-200 hover:border-kurtbeans-accent'"
                                        class="shrink-0 border rounded-xl px-4 py-2 text-sm font-semibold transition-all whitespace-nowrap">
                                        {{ $k->nama_kategori }}
                                        <span class="ml-1.5 text-xs font-semibold opacity-70">{{ $menus->where('id_kategori', $k->id_kategori)->count() }}</span>
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[800px]">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                        <th class="px-6 py-4 font-bold w-12">No</th>
                                        <th class="px-6 py-4 font-bold w-20">Foto</th>
                                        <th class="px-6 py-4 font-bold">Nama Menu</th>
                                        <th class="px-6 py-4 font-bold">Harga</th>
                                        <th class="px-6 py-4 font-bold text-center">Status</th>
                                        <th class="px-6 py-4 font-bold text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-100 penomoran-baris">
                                    @foreach($menus as $m)
                                    <!-- Baris tampil bila lolos pencarian DAN penyaring kategori -->
                                    <tr class="hover:bg-gray-50/50 transition-colors group"
                                        x-show="cocokMenu(@js(mb_strtolower($m->nama_menu)), @js((string) $m->id_kategori))">
                                        <td class="px-6 py-4 text-gray-500"><span class="nomor-baris"></span></td>
                                        <td class="px-6 py-4">
                                            @if($m->gambar)
                                                <img src="{{ asset('storage/menu/'.$m->gambar) }}" alt="{{ $m->nama_menu }}" class="w-12 h-12 rounded-xl object-cover shadow-sm">
                                            @else
                                                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400"><i class="fa-solid fa-image"></i></div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900 text-base">{{ $m->nama_menu }}</div>
                                            <div class="text-xs font-medium text-gray-500 mt-0.5 bg-gray-100 inline-block px-2 py-0.5 rounded-md">{{ $m->kategori->nama_kategori ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-kurtbeans-accent">Rp {{ number_format($m->harga,0,',','.') }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if($m->status_menu == 'Tersedia')
                                                <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-bold whitespace-nowrap inline-flex items-center">
                                                    <i class="fas fa-check-circle mr-1"></i> Ready
                                                </span>
                                            @else
                                                <span class="bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full font-bold whitespace-nowrap inline-flex items-center">
                                                    <i class="fas fa-times-circle mr-1"></i> Tidak Ready
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button @click="openEditMenu({{ json_encode($m, JSON_HEX_APOS | JSON_HEX_QUOT) }})" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors"><i class="fa-solid fa-pen"></i></button>
                                                <form action="{{ url('/admin/menu/'.$m->id_menu) }}" method="POST" class="inline" onsubmit="return confirm('Hapus menu ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach

                                    <!-- Keadaan kosong: tidak ada menu yang lolos penyaring -->
                                    <tr x-show="jumlahMenuTampil === 0" x-cloak>
                                        <td colspan="6" class="px-6 py-16 text-center">
                                            <i class="fa-solid fa-mug-hot text-3xl text-gray-300"></i>
                                            <p class="mt-3 font-bold text-gray-700">Tidak ada menu di penyaring ini</p>
                                            <p class="mt-1 text-xs text-gray-500">Coba pilih kategori lain, atau kosongkan kotak pencarian.</p>
                                            <button type="button" @click="filterKategoriMenu = 'all'; searchMenu = ''"
                                                class="mt-4 text-xs font-bold text-kurtbeans-accent underline underline-offset-2">
                                                Tampilkan semua menu
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB KATEGORI -->
                <div x-show="activeTab === 'kategori'" x-cloak x-transition.opacity>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center mb-6">
                        <div class="relative w-full max-w-sm">
                            <i class="fa-solid fa-search absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="text" x-model="searchKategori" placeholder="Cari Kategori..." class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-2.5 text-sm focus:border-kurtbeans-accent outline-none transition-all">
                        </div>
                        <button @click="showModalKategori = true" class="w-full md:w-auto bg-kurtbeans-accent hover:bg-kurtbeans-accentHover text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm transition-all transform hover:scale-105">
                            <i class="fa-solid fa-plus mr-2"></i> Tambah Kategori
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                    <th class="px-6 py-4 font-bold w-16">No</th>
                                    <th class="px-6 py-4 font-bold">Nama Kategori</th>
                                    <th class="px-6 py-4 font-bold">Deskripsi</th>
                                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @foreach($kategoris as $k)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-6 py-4 text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $k->nama_kategori }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $k->deskripsi ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="openEditKategori({{ json_encode($k, JSON_HEX_APOS | JSON_HEX_QUOT) }})" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors mr-1"><i class="fa-solid fa-pen"></i></button>
                                        <form action="{{ url('/admin/kategori/'.$k->id_kategori) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB MEJA & QR CODE -->
                <div x-show="activeTab === 'meja'" x-cloak x-transition.opacity>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center mb-6">
                        <div class="relative w-full max-w-sm">
                            <i class="fa-solid fa-search absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="text" x-model="searchMeja" placeholder="Cari Nomor Meja..." class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-2.5 text-sm focus:border-kurtbeans-accent outline-none transition-all">
                        </div>
                        <button @click="showModalMeja = true" class="w-full md:w-auto bg-kurtbeans-accent hover:bg-kurtbeans-accentHover text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm transition-all transform hover:scale-105">
                            <i class="fa-solid fa-plus mr-2"></i> Tambah Meja
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                    <th class="px-6 py-4 font-bold w-24">Meja</th>
                                    <th class="px-6 py-4 font-bold">Link Validasi QR</th>
                                    <th class="px-6 py-4 font-bold">Status</th>
                                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @foreach($mejas as $mj)
                                <tr class="hover:bg-gray-50/50 transition-colors group" x-show="searchMeja === '' || '{{ $mj->nomor_meja }}'.includes(searchMeja)">
                                    <td class="px-6 py-4 font-bold text-xl text-gray-900">{{ $mj->nomor_meja }}</td>
                                    <td class="px-6 py-4 text-gray-500 font-mono text-xs bg-gray-50 rounded-lg max-w-xs truncate">{{ url('/order?meja='.$mj->nomor_meja) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $mj->status_meja == 'Tersedia' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ $mj->status_meja }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button type="button" @click="bukaEditMeja({ id: {{ $mj->id_meja }}, nomor_meja: {{ $mj->nomor_meja }}, status_meja: '{{ $mj->status_meja }}' })" class="bg-amber-50 text-amber-600 hover:bg-amber-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors"><i class="fa-solid fa-pen mr-1"></i> Edit</button>
                                            <a href="{{ url('/admin/meja/'.$mj->id_meja.'/qr') }}" target="_blank" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                                QR Code
                                            </a>
                                            <form action="{{ url('/admin/meja/'.$mj->id_meja) }}" method="POST" class="inline" onsubmit="return confirm('Hapus meja ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB PENGGUNA -->
                <div x-show="activeTab === 'pengguna'" x-cloak x-transition.opacity>
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center mb-6">
                        <div class="relative w-full max-w-sm">
                            <i class="fa-solid fa-search absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="text" x-model="searchPengguna" placeholder="Cari Username atau Nama..." class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-2.5 text-sm focus:border-kurtbeans-accent outline-none transition-all">
                        </div>
                        <button @click="showModalPengguna = true" class="w-full md:w-auto bg-kurtbeans-accent hover:bg-kurtbeans-accentHover text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm transition-all transform hover:scale-105">
                            <i class="fa-solid fa-user-plus mr-2"></i> Tambah Pegawai
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                    <th class="px-6 py-4 font-bold">Informasi Akun</th>
                                    <th class="px-6 py-4 font-bold">Role / Jabatan</th>
                                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @foreach($penggunas as $p)
                                <tr class="hover:bg-gray-50/50 transition-colors group" x-show="searchPengguna === '' || '{{ strtolower($p->nama_lengkap . $p->username) }}'.includes(searchPengguna.toLowerCase())">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $p->nama_lengkap }}</div>
                                        <div class="text-gray-500 text-xs mt-0.5">@ {{ $p->username }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $p->role === 'Admin' ? 'bg-purple-50 text-purple-700 border-purple-200' : ($p->role === 'Kasir' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-orange-50 text-orange-700 border-orange-200') }}">
                                            {{ $p->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($p->role !== 'Admin')
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button type="button" @click="bukaEditPengguna({ id: {{ $p->id_user }}, nama_lengkap: @js($p->nama_lengkap), username: @js($p->username), role: '{{ $p->role }}' })" class="bg-amber-50 text-amber-600 hover:bg-amber-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors"><i class="fa-solid fa-pen mr-1"></i> Edit</button>
                                                <form action="{{ url('/admin/pengguna/'.$p->id_user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pegawai ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors"><i class="fa-solid fa-trash mr-1"></i> Hapus</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 font-bold bg-gray-100 px-3 py-1.5 rounded-lg"><i class="fa-solid fa-shield-halved mr-1"></i> Proteksi</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ============================================== -->
    <!-- MODALS SECTION (CREATE & EDIT)                 -->
    <!-- ============================================== -->

    <!-- MODAL TAMBAH MENU -->
    <div x-show="showModalMenu" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-10 text-center sm:p-0">
            <div x-show="showModalMenu" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showModalMenu = false"></div>
            
            <div x-show="showModalMenu" x-transition.scale.origin.bottom class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl w-full max-w-2xl z-50 transform transition-all border border-gray-100">
                <form action="{{ url('/admin/menu') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="px-8 py-5 border-b border-gray-100">
                        <h3 class="font-bold text-lg text-gray-900">Tambah Menu Minuman Baru</h3>
                    </div>

                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Nama Menu</label>
                            <input type="text" name="nama_menu" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Pilih Kategori</label>
                                <select name="id_kategori" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm appearance-none">
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    @foreach($kategoris as $k)
                                        <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Status Ketersediaan</label>
                                <select name="status_menu" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm appearance-none">
                                    <option value="Tersedia">Tersedia</option>
                                    <option value="Habis">Habis</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Harga (Rupiah)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                </div>
                                <input type="number" name="harga" required class="w-full bg-white border border-gray-300 rounded-xl pl-12 pr-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Foto Menu</label>
                            <input type="file" name="gambar" accept="image/*" class="block w-full text-sm text-gray-500 bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none 
                                file:mr-4 file:py-3 file:px-6 file:rounded-l-xl file:border-0 
                                file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 
                                hover:file:bg-gray-200 file:cursor-pointer cursor-pointer transition-all">
                        </div>
                    </div>
                    <div class="px-8 py-5 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-kurtbeans-accent hover:bg-kurtbeans-accentHover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all">Simpan Menu</button>
                        <button type="button" @click="showModalMenu = false" class="bg-white border border-gray-300 text-gray-600 px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT MENU (Dinamis dari state Alpine) -->
    <div x-show="showEditMenu" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-10 text-center sm:p-0">
            <div x-show="showEditMenu" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showEditMenu = false"></div>
            
            <div x-show="showEditMenu" x-transition.scale.origin.bottom class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl w-full max-w-2xl z-50 transform transition-all border border-gray-100">
                <form :action="'{{ url('/admin/menu') }}/' + editMenuData.id_menu" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="px-8 py-5 border-b border-gray-100">
                        <h3 class="font-bold text-lg text-gray-900">Ubah Data Menu Minuman</h3>
                    </div>

                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Nama Menu</label>
                            <input type="text" name="nama_menu" x-model="editMenuData.nama_menu" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Pilih Kategori</label>
                                <select name="id_kategori" x-model="editMenuData.id_kategori" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm appearance-none">
                                    <option value="" disabled>-- Pilih Kategori --</option>
                                    @foreach($kategoris as $k)
                                        <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Status Ketersediaan</label>
                                <select name="status_menu" x-model="editMenuData.status_menu" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm appearance-none">
                                    <option value="Tersedia">Tersedia</option>
                                    <option value="Habis">Habis</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Harga (Rupiah)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                </div>
                                <input type="number" name="harga" x-model="editMenuData.harga" required class="w-full bg-white border border-gray-300 rounded-xl pl-12 pr-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Ubah Foto</label>
                            <input type="file" name="gambar" accept="image/*" class="block w-full text-sm text-gray-500 bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none 
                                file:mr-4 file:py-3 file:px-6 file:rounded-l-xl file:border-0 
                                file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 
                                hover:file:bg-gray-200 file:cursor-pointer cursor-pointer transition-all">
                        </div>
                    </div>
                    <div class="px-8 py-5 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all">Update Menu</button>
                        <button type="button" @click="showEditMenu = false" class="bg-white border border-gray-300 text-gray-600 px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH KATEGORI -->
    <div x-show="showModalKategori" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div x-show="showModalKategori" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showModalKategori = false"></div>
            <div x-show="showModalKategori" x-transition.scale.origin.bottom class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl w-full max-w-lg z-50 transform transition-all border border-gray-100">
                <form action="{{ url('/admin/kategori') }}" method="POST">
                    @csrf
                    <div class="px-8 py-5 border-b border-gray-100"><h3 class="font-bold text-lg text-gray-900">Tambah Kategori Baru</h3></div>
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Nama Kategori</label>
                            <input type="text" name="nama_kategori" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" rows="3" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm"></textarea>
                        </div>
                    </div>
                    <div class="px-8 py-5 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-kurtbeans-accent hover:bg-kurtbeans-accentHover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all">Simpan</button>
                        <button type="button" @click="showModalKategori = false" class="bg-white border border-gray-300 text-gray-600 px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT KATEGORI (Dinamis dari state Alpine) -->
    <div x-show="showEditKategori" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div x-show="showEditKategori" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showEditKategori = false"></div>
            <div x-show="showEditKategori" x-transition.scale.origin.bottom class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl w-full max-w-lg z-50 transform transition-all border border-gray-100">
                <form :action="'{{ url('/admin/kategori') }}/' + editKategoriData.id_kategori" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-8 py-5 border-b border-gray-100"><h3 class="font-bold text-lg text-gray-900">Ubah Data Kategori</h3></div>
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Nama Kategori</label>
                            <input type="text" name="nama_kategori" x-model="editKategoriData.nama_kategori" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" x-model="editKategoriData.deskripsi" rows="3" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm"></textarea>
                        </div>
                    </div>
                    <div class="px-8 py-5 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all">Update Data</button>
                        <button type="button" @click="showEditKategori = false" class="bg-white border border-gray-300 text-gray-600 px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH MEJA -->
    <div x-show="showModalMeja" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div x-show="showModalMeja" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showModalMeja = false"></div>
            <div x-show="showModalMeja" x-transition.scale.origin.bottom class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl w-full max-w-sm z-50 transform transition-all border border-gray-100">
                <form action="{{ url('/admin/meja') }}" method="POST">
                    @csrf
                    <div class="px-8 py-5 border-b border-gray-100"><h3 class="font-bold text-lg text-gray-900">Tambah Meja Baru</h3></div>
                    <div class="p-8">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Nomor Meja</label>
                        <input type="number" name="nomor_meja" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm text-center text-lg font-bold">
                        <div class="mt-4 bg-blue-50 text-blue-600 p-3 rounded-lg text-xs flex items-start gap-2">
                            <i class="fa-solid fa-circle-info mt-0.5"></i>
                            <p>Sistem otomatis mem-<i>generate</i> link pesanan dan QR Code unik untuk meja ini.</p>
                        </div>
                    </div>
                    <div class="px-8 py-5 flex flex-col gap-3">
                        <button type="submit" class="w-full bg-kurtbeans-accent hover:bg-kurtbeans-accentHover text-white px-8 py-3 rounded-xl text-sm font-bold shadow-md transition-all">Simpan Data</button>
                        <button type="button" @click="showModalMeja = false" class="w-full bg-white border border-gray-300 text-gray-600 px-8 py-3 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH PENGGUNA -->
    <div x-show="showModalPengguna" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-10 text-center">
            <div x-show="showModalPengguna" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showModalPengguna = false"></div>
            <div x-show="showModalPengguna" x-transition.scale.origin.bottom class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl w-full max-w-lg z-50 transform transition-all border border-gray-100">
                <form action="{{ url('/admin/pengguna') }}" method="POST">
                    @csrf
                    <div class="px-8 py-5 border-b border-gray-100"><h3 class="font-bold text-lg text-gray-900">Tambah Pegawai Baru</h3></div>
                    <div class="p-8 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Username Login</label>
                            <input type="text" name="username" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Password</label>
                            <input type="password" name="password" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Jabatan (Role)</label>
                            <select name="role" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm appearance-none">
                                <option value="" disabled selected>-- Pilih Jabatan --</option>
                                <option value="Kasir">Kasir</option>
                                <option value="Barista">Barista</option>
                            </select>
                        </div>
                    </div>
                    <div class="px-8 py-5 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-kurtbeans-accent hover:bg-kurtbeans-accentHover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all">Simpan</button>
                        <button type="button" @click="showModalPengguna = false" class="bg-white border border-gray-300 text-gray-600 px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT MEJA -->
    <div x-show="showEditMeja" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div x-show="showEditMeja" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showEditMeja = false"></div>
            <div x-show="showEditMeja" x-transition.scale.origin.bottom class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl w-full max-w-sm z-50 transform transition-all border border-gray-100">
                <form :action="'/admin/meja/' + editMeja.id" method="POST">
                    @csrf @method('PUT')
                    <div class="px-8 py-5 border-b border-gray-100"><h3 class="font-bold text-lg text-gray-900">Edit Meja</h3></div>
                    <div class="p-8 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Nomor Meja</label>
                            <input type="number" name="nomor_meja" x-model="editMeja.nomor_meja" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm text-center text-lg font-bold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Status Meja</label>
                            <select name="status_meja" x-model="editMeja.status_meja" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm appearance-none">
                                <option value="Tersedia">Tersedia</option>
                                <option value="Terisi">Terisi</option>
                            </select>
                        </div>
                        <div class="bg-amber-50 text-amber-700 p-3 rounded-lg text-xs flex items-start gap-2">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            <p>Jika <b>nomor meja</b> diubah, QR Code otomatis dibuat ulang mengikuti nomor baru — stiker QR di meja perlu dicetak &amp; ditempel ulang.</p>
                        </div>
                    </div>
                    <div class="px-8 py-5 flex flex-col gap-3">
                        <button type="submit" class="w-full bg-kurtbeans-accent hover:bg-kurtbeans-accentHover text-white px-8 py-3 rounded-xl text-sm font-bold shadow-md transition-all">Simpan Perubahan</button>
                        <button type="button" @click="showEditMeja = false" class="w-full bg-white border border-gray-300 text-gray-600 px-8 py-3 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT PENGGUNA -->
    <div x-show="showEditPengguna" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-10 text-center">
            <div x-show="showEditPengguna" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showEditPengguna = false"></div>
            <div x-show="showEditPengguna" x-transition.scale.origin.bottom class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl w-full max-w-lg z-50 transform transition-all border border-gray-100">
                <form :action="'/admin/pengguna/' + editPengguna.id" method="POST">
                    @csrf @method('PUT')
                    <div class="px-8 py-5 border-b border-gray-100"><h3 class="font-bold text-lg text-gray-900">Edit Pegawai</h3></div>
                    <div class="p-8 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" x-model="editPengguna.nama_lengkap" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Username Login</label>
                            <input type="text" name="username" x-model="editPengguna.username" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Password Baru</label>
                            <input type="password" name="password" placeholder="Kosongkan bila tidak ingin mengubah" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Jabatan (Role)</label>
                            <select name="role" x-model="editPengguna.role" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-kurtbeans-accent focus:ring-2 focus:ring-kurtbeans-accent/20 outline-none transition-all shadow-sm appearance-none">
                                <option value="Kasir">Kasir</option>
                                <option value="Barista">Barista</option>
                            </select>
                        </div>
                    </div>
                    <div class="px-8 py-5 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-kurtbeans-accent hover:bg-kurtbeans-accentHover text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all">Simpan Perubahan</button>
                        <button type="button" @click="showEditPengguna = false" class="bg-white border border-gray-300 text-gray-600 px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>