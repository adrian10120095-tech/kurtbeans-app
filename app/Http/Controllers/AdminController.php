<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Menu;
use App\Models\Kategori;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminController extends Controller
{
    // ================= DASHBOARD UTAMA =================
    public function index()
    {
        $kategoris = Kategori::orderBy('id_kategori', 'desc')->get();
        $menus     = Menu::with('kategori')->orderBy('id_menu', 'desc')->get();
        $mejas     = Meja::orderBy('nomor_meja', 'asc')->get();
        $penggunas = Pengguna::orderBy('role', 'asc')->get();

        return view('admin.dashboard', [
            'kategoris'    => $kategoris,
            'menus'        => $menus,
            'mejas'        => $mejas,
            'penggunas'    => $penggunas,
            'menuTerlaris' => $this->menuTerlaris(),
        ]);
    }

    /**
     * Empat menu paling laris pada BULAN BERJALAN.
     *
     * Hanya menghitung baris rincian yang pesanannya berstatus "Lunas",
     * sehingga pesanan yang dibatalkan atau belum dibayar tidak ikut
     * terhitung sebagai penjualan. Judul panel di dashboard berbunyi
     * "Menu Terlaris Bulan Ini", jadi rentangnya dibatasi satu bulan
     * agar angka di layar sesuai dengan judulnya.
     *
     * Penyaringan "> 0" dilakukan di PHP, bukan lewat having(), karena
     * having() tanpa group by ditolak MySQL ketika mode ONLY_FULL_GROUP_BY
     * aktif (bawaan MySQL 5.7 ke atas).
     */
    private function menuTerlaris()
    {
        try {
            return Menu::query()
                ->withSum(['detailPesanan as total_terjual' => function ($query) {
                    $query->whereHas('pesanan', function ($q) {
                        $q->where('status_pembayaran', 'Lunas')
                          ->whereBetween('tgl_pesan', [
                              now()->startOfMonth(),
                              now()->endOfMonth(),
                          ]);
                    });
                }], 'quantity')
                ->orderByDesc('total_terjual')
                ->take(4)
                ->get()
                ->filter(fn ($menu) => (int) $menu->total_terjual > 0)
                ->values();
        } catch (\Throwable $e) {
            // Dashboard tetap terbuka walau perhitungan gagal, tetapi
            // kegagalannya DICATAT — versi sebelumnya menelan error
            // tanpa jejak sehingga panel diam-diam selalu kosong.
            Log::error('Gagal menghitung Menu Terlaris: ' . $e->getMessage());

            return collect();
        }
    }

    // ================= CRUD PENGGUNA (PEGAWAI) =================
    public function storePengguna(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:pengguna,username',
            'password' => 'required|min:6',
            'role' => 'required|in:Kasir,Barista'
        ]);

        Pengguna::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->back()->with('success', 'Pegawai baru berhasil ditambahkan!');
    }

    public function destroyPengguna($id)
    {
        $pengguna = Pengguna::findOrFail($id);

        // Proteksi: Cegah penghapusan Admin
        if ($pengguna->role === 'Admin') {
            return redirect()->back()->with('error', 'Admin tidak dapat dihapus!');
        }

        $pengguna->delete();
        return redirect()->back()->with('success', 'Pegawai berhasil dihapus!');
    }

    public function updatePengguna(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        // Proteksi: akun Admin tidak diubah dari menu kelola pegawai.
        if ($pengguna->role === 'Admin') {
            return redirect()->back()->with('error', 'Akun Admin tidak dapat diubah dari sini!');
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username'     => 'required|string|max:50|unique:pengguna,username,' . $id . ',id_user',
            'password'     => 'nullable|min:6',
            'role'         => 'required|in:Kasir,Barista',
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'role'         => $request->role,
        ];

        // Password hanya diganti bila diisi; dikosongkan berarti tetap.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        return redirect()->back()->with('success', 'Data pegawai berhasil diperbarui!');
    }

    // ================= CRUD KATEGORI =================
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori,nama_kategori',
            'deskripsi' => 'nullable|string'
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    // Ubah data kategori
    public function updateKategori(Request $request, $id)
    {
        $request->validate([
            // Pengecualian unique ID agar bisa disimpan dengan nama yang sama
            'nama_kategori' => 'required|string|max:50|unique:kategori,nama_kategori,' . $id . ',id_kategori',
            'deskripsi' => 'nullable|string'
        ]);

        $kategori = Kategori::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi
        ]);

        return redirect()->back()->with('success', 'Data Kategori berhasil diperbarui!');
    }

    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        // Cek apakah kategori masih memiliki menu
        if ($kategori->menus()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki menu!');
        }

        $kategori->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }

    // ================= CRUD MENU =================
    public function storeMenu(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'harga' => 'required|numeric|min:0',
            'status_menu' => 'required|in:Tersedia,Habis',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Ketersediaan menu ditentukan murni oleh status_menu (Tersedia/Habis).
        // Sistem tidak mengelola jumlah stok.
        $data = [
            'nama_menu' => $request->nama_menu,
            'id_kategori' => $request->id_kategori,
            'harga' => $request->harga,
            'status_menu' => $request->status_menu
        ];

        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            // PENTING: tentukan disk 'public' secara eksplisit.
            // Jika tidak, Laravel 11+ memakai disk default 'local' yang
            // root-nya storage/app/private, bukan storage/app/public.
            $file->storeAs('menu', $filename, 'public');
            $data['gambar'] = $filename;
        }

        Menu::create($data);
        return redirect()->back()->with('success', 'Menu berhasil ditambahkan!');
    }

    // Ubah data menu
    public function updateMenu(Request $request, $id)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'harga' => 'required|numeric|min:0',
            'status_menu' => 'required|in:Tersedia,Habis',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $menu = Menu::findOrFail($id);

        // Ketersediaan menu ditentukan murni oleh status_menu (Tersedia/Habis).
        $data = [
            'nama_menu' => $request->nama_menu,
            'id_kategori' => $request->id_kategori,
            'harga' => $request->harga,
            'status_menu' => $request->status_menu
        ];

        // Cek jika pengguna mengupload gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika file fisiknya ada di storage
            if ($menu->gambar && Storage::disk('public')->exists('menu/' . $menu->gambar)) {
                Storage::disk('public')->delete('menu/' . $menu->gambar);
            }

            // Upload gambar baru
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            // PENTING: tentukan disk 'public' secara eksplisit, sama seperti di storeMenu.
            $file->storeAs('menu', $filename, 'public');
            $data['gambar'] = $filename;
        }

        $menu->update($data);
        return redirect()->back()->with('success', 'Data Menu berhasil diperbarui!');
    }

    public function destroyMenu($id)
    {
        $menu   = Menu::findOrFail($id);
        $gambar = $menu->gambar;

        // Kolom detail_pesanan.id_menu memakai FOREIGN KEY tanpa ON DELETE,
        // sehingga MySQL menolak (RESTRICT) penghapusan menu yang pernah
        // dipesan dan aplikasi berhenti dengan galat 500.
        //
        // Supaya Admin tetap bebas menghapus menu kapan pun — termasuk menu
        // yang dulu laris — baris rincian pesanan yang memuat menu ini
        // dihapus lebih dulu, keduanya dalam SATU transaksi basis data
        // sehingga tidak mungkin tersisa data setengah jadi.
        //
        // Catatan: kolom pesanan.total_harga TIDAK diubah, jadi nominal pada
        // riwayat transaksi Kasir tetap sesuai dengan yang dulu dibayar.
        try {
            DB::transaction(function () use ($menu) {
                $menu->detailPesanan()->delete();
                $menu->delete();
            });
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus menu #' . $id . ': ' . $e->getMessage());

            return redirect()->back()->with('error', 'Menu gagal dihapus. Silakan coba lagi.');
        }

        // Berkas gambar dihapus SETELAH basis data berhasil diperbarui,
        // agar gambar tidak hilang percuma bila penghapusan batal.
        if ($gambar && Storage::disk('public')->exists('menu/' . $gambar)) {
            Storage::disk('public')->delete('menu/' . $gambar);
        }

        return redirect()->back()->with('success', 'Menu berhasil dihapus!');
    }

    public function toggleStatusMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->status_menu = $menu->status_menu === 'Tersedia' ? 'Habis' : 'Tersedia';
        $menu->save();
        return redirect()->back()->with('success', 'Status ketersediaan menu diperbarui!');
    }

    // ================= CRUD MEJA & QR CODE =================
    public function storeMeja(Request $request)
    {
        $request->validate([
            'nomor_meja' => 'required|integer|unique:meja,nomor_meja'
        ]);

        // Buat nama file QR Code yang unik
        $nama_file_qr = 'meja_' . $request->nomor_meja . '_' . time() . '.svg';

        // Tentukan URL yang akan disematkan ke dalam QR Code
        $url = url('/order?meja=' . $request->nomor_meja);

        // Cek folder
        if (!Storage::disk('public')->exists('qrcodes')) {
            Storage::disk('public')->makeDirectory('qrcodes');
        }

        // Generate QR Code ke dalam format SVG sebagai string
        $qrCodeData = QrCode::format('svg')->size(300)->generate($url);

        // Simpan file SVG
        Storage::disk('public')->put('qrcodes/' . $nama_file_qr, $qrCodeData);

        Meja::create([
            'nomor_meja' => $request->nomor_meja,
            'status_meja' => 'Tersedia',
            'qr_code' => $nama_file_qr
        ]);

        return redirect()->back()->with('success', 'Meja dan QR Code berhasil ditambahkan!');
    }

    public function updateMeja(Request $request, $id)
    {
        $meja = Meja::findOrFail($id);

        $request->validate([
            'nomor_meja'  => 'required|integer|unique:meja,nomor_meja,' . $id . ',id_meja',
            'status_meja' => 'required|in:Tersedia,Terisi',
        ]);

        $data = ['status_meja' => $request->status_meja];

        // QR Code berisi URL yang memuat nomor meja. Jadi QR hanya perlu
        // dibuat ulang bila NOMOR meja berubah — kalau hanya status yang
        // diubah, QR lama tetap dipakai. (Halaman tampil QR juga selalu
        // membuat ulang secara live dari nomor meja terkini.)
        if ((int) $meja->nomor_meja !== (int) $request->nomor_meja) {
            // Hapus file QR lama
            if ($meja->qr_code && Storage::disk('public')->exists('qrcodes/' . $meja->qr_code)) {
                Storage::disk('public')->delete('qrcodes/' . $meja->qr_code);
            }

            // Buat QR baru mengikuti nomor meja yang baru
            if (!Storage::disk('public')->exists('qrcodes')) {
                Storage::disk('public')->makeDirectory('qrcodes');
            }
            $nama_file_qr = 'meja_' . $request->nomor_meja . '_' . time() . '.svg';
            $url = url('/order?meja=' . $request->nomor_meja);
            $qrCodeData = QrCode::format('svg')->size(300)->generate($url);
            Storage::disk('public')->put('qrcodes/' . $nama_file_qr, $qrCodeData);

            $data['nomor_meja'] = $request->nomor_meja;
            $data['qr_code']    = $nama_file_qr;
        }

        $meja->update($data);

        return redirect()->back()->with('success', 'Data meja berhasil diperbarui!');
    }

    public function destroyMeja($id)
    {
        $meja = Meja::findOrFail($id);

        // Kolom pesanan.id_meja juga memakai FOREIGN KEY tanpa ON DELETE
        // (RESTRICT). BERBEDA dengan kasus menu: menghapus meja berarti
        // harus menghapus seluruh PESANAN pada meja itu, dan karena
        // pembayaran/detail_pesanan/notifikasi ber-ON DELETE CASCADE ke
        // pesanan, seluruh catatan transaksinya ikut lenyap.
        //
        // Karena itu penghapusan meja yang sudah punya riwayat sengaja
        // ditahan di sini — cukup dengan pesan yang jelas, bukan layar
        // galat 500 dari basis data.
        if (Pesanan::query()->where('id_meja', $meja->id_meja)->exists()) {
            return redirect()->back()->with(
                'error',
                'Meja tidak dapat dihapus karena sudah memiliki riwayat pesanan. Ubah statusnya bila meja tidak dipakai lagi.'
            );
        }

        // Hapus file QR Code secara otomatis saat meja dihapus
        if ($meja->qr_code && Storage::disk('public')->exists('qrcodes/' . $meja->qr_code)) {
            Storage::disk('public')->delete('qrcodes/' . $meja->qr_code);
        }

        $meja->delete();

        return redirect()->back()->with('success', 'Meja berhasil dihapus!');
    }

    public function generateQR($id)
    {
        $meja = Meja::findOrFail($id);
        $url = url('/order?meja=' . $meja->nomor_meja);

        // SVG QR dibuat langsung (vektor, tajam saat dicetak). Deklarasi XML
        // dibuang supaya bersih ketika disisipkan ke dalam halaman HTML.
        $qrcode = QrCode::format('svg')->size(320)->margin(1)->generate($url);
        $qrcode = preg_replace('/<\?xml.*?\?>\s*/', '', $qrcode);

        return view('admin.meja-qr', compact('meja', 'qrcode', 'url'));
    }
}