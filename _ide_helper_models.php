<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id_detail
 * @property int $id_pesanan
 * @property int $id_menu
 * @property int $quantity
 * @property numeric $subtotal
 * @property string|null $catatan
 * @property-read \App\Models\Menu $menu
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailPesanan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailPesanan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailPesanan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailPesanan whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailPesanan whereIdDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailPesanan whereIdMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailPesanan whereIdPesanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailPesanan whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DetailPesanan whereSubtotal($value)
 */
	class DetailPesanan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_kategori
 * @property string $nama_kategori
 * @property string|null $deskripsi
 * @property string $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Menu> $menu
 * @property-read int|null $menu_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Menu> $menus
 * @property-read int|null $menus_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereIdKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kategori whereNamaKategori($value)
 */
	class Kategori extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_meja
 * @property int $nomor_meja
 * @property string $qr_code
 * @property string|null $status_meja
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meja newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meja newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meja query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meja whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meja whereIdMeja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meja whereNomorMeja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meja whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meja whereStatusMeja($value)
 */
	class Meja extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_menu
 * @property int $id_kategori
 * @property string $nama_menu
 * @property numeric $harga
 * @property int $stok
 * @property string|null $gambar
 * @property string|null $status_menu
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Kategori $kategori
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereGambar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereIdKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereIdMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereNamaMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereStatusMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereStok($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereUpdatedAt($value)
 */
	class Menu extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_notifikasi
 * @property int $id_pesanan
 * @property int $id_pelanggan_sementara
 * @property string $judul
 * @property string $pesan
 * @property string|null $status
 * @property string|null $dikirim_pada
 * @property-read \App\Models\PelangganSementara $pelanggan
 * @property-read \App\Models\Pesanan $pesanan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereDikirimPada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereIdNotifikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereIdPelangganSementara($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereIdPesanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi wherePesan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereStatus($value)
 */
	class Notifikasi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_pelanggan_sementara
 * @property string $nama_pemesan
 * @property string|null $token_subscription
 * @property string $session_id
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PelangganSementara newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PelangganSementara newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PelangganSementara query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PelangganSementara whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PelangganSementara whereIdPelangganSementara($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PelangganSementara whereNamaPemesan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PelangganSementara whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PelangganSementara whereTokenSubscription($value)
 */
	class PelangganSementara extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_pembayaran
 * @property int $id_pesanan
 * @property string $order_id
 * @property numeric $gross_amount
 * @property string|null $payment_type
 * @property string|null $transaction_status
 * @property string|null $fraud_status
 * @property string|null $payment_token
 * @property string|null $payment_url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Pesanan $pesanan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereFraudStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereGrossAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereIdPembayaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereIdPesanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran wherePaymentToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran wherePaymentUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereTransactionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pembayaran whereUpdatedAt($value)
 */
	class Pembayaran extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_user
 * @property string $username
 * @property string $password
 * @property string $nama_lengkap
 * @property string $role
 * @property string|null $last_login
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengguna newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengguna newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengguna query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengguna whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengguna whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengguna whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengguna whereNamaLengkap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengguna wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengguna whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengguna whereUsername($value)
 */
	class Pengguna extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_pesanan
 * @property int $id_meja
 * @property int $id_pelanggan_sementara
 * @property numeric $total_harga
 * @property string $status_pesanan
 * @property string|null $status_pembayaran
 * @property string|null $metode_pembayaran
 * @property string|null $midtrans_order_id
 * @property \Illuminate\Support\Carbon|null $tgl_pesan
 * @property \Illuminate\Support\Carbon|null $tgl_bayar
 * @property \Illuminate\Support\Carbon|null $tgl_selesai
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DetailPesanan> $detail
 * @property-read int|null $detail_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DetailPesanan> $detailPesanan
 * @property-read int|null $detail_pesanan_count
 * @property-read mixed $no_antrean
 * @property-read \App\Models\Meja $meja
 * @property-read \App\Models\PelangganSementara $pelanggan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pembayaran> $pembayaran
 * @property-read int|null $pembayaran_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereIdMeja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereIdPelangganSementara($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereIdPesanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereMetodePembayaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereMidtransOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereStatusPembayaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereStatusPesanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereTglBayar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereTglPesan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereTglSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereTotalHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pesanan whereUpdatedAt($value)
 */
	class Pesanan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

