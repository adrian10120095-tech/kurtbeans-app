<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\Menu;
use App\Models\Kategori;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminController extends Controller
{
    // ================= DASHBOARD UTAMA =================
    public function index() 
    { 
        $kategoris = Kategori::orderBy('id_kategori', 'desc')->get();
        $menus = Menu::with('kategori')->orderBy('id_menu', 'desc')->get();
        $mejas = Meja::orderBy('nomor_meja', 'asc')->get();
        $penggunas = Pengguna::orderBy('role', 'asc')->get();
        
        // Mengambil 4 Menu Terlaris berdasarkan relasi ke Detail Pesanan
        // Dibungkus try-catch sebagai fallback jika relasi model belum diatur
        try {
            $menuTerlaris = Menu::withSum('detailPesanan as total_terjual', 'quantity')
                ->having('total_terjual', '>', 0)
                ->orderByDesc('total_terjual')
                ->take(4)
                ->get();
        } catch (\Exception $e) {
            $menuTerlaris = collect([]); // Fallback jika terjadi error
        }
        
        return view('admin.dashboard', compact('kategoris', 'menus', 'mejas', 'penggunas', 'menuTerlaris'));
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

    // FUNGSI BARU: Untuk mengubah/edit data kategori
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

    // FUNGSI BARU: Untuk mengubah/edit data menu
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
        $menu = Menu::findOrFail($id);
        
        // Hapus file gambar secara otomatis saat menu dihapus
        if ($menu->gambar && Storage::disk('public')->exists('menu/' . $menu->gambar)) {
            Storage::disk('public')->delete('menu/' . $menu->gambar);
        }
        
        $menu->delete();
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