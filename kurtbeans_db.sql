-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Agu 2026 pada 20.48
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kurtbeans_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('kurtbeans-cache-admin123|203.17.85.42', 'i:1;', 1786566409),
('kurtbeans-cache-admin123|203.17.85.42:timer', 'i:1786566409;', 1786566409),
('kurtbeans-cache-barits|127.0.0.1', 'i:1;', 1779112345),
('kurtbeans-cache-barits|127.0.0.1:timer', 'i:1779112345;', 1779112345),
('kurtbeans-cache-cek-midtrans-10', 'b:1;', 1784708100),
('kurtbeans-cache-cek-midtrans-11', 'b:1;', 1784707801),
('kurtbeans-cache-cek-midtrans-12', 'b:1;', 1784708275),
('kurtbeans-cache-cek-midtrans-14', 'b:1;', 1784708519),
('kurtbeans-cache-cek-midtrans-15', 'b:1;', 1784709041),
('kurtbeans-cache-cek-midtrans-17', 'b:1;', 1784710310),
('kurtbeans-cache-cek-midtrans-18', 'b:1;', 1784710551),
('kurtbeans-cache-cek-midtrans-23', 'b:1;', 1784731025),
('kurtbeans-cache-cek-midtrans-24', 'b:1;', 1784736158),
('kurtbeans-cache-cek-midtrans-27', 'b:1;', 1785919330),
('kurtbeans-cache-cek-midtrans-28', 'b:1;', 1785919396),
('kurtbeans-cache-cek-midtrans-29', 'b:1;', 1785919510),
('kurtbeans-cache-cek-midtrans-30', 'b:1;', 1785919688),
('kurtbeans-cache-cek-midtrans-31', 'b:1;', 1785944633),
('kurtbeans-cache-cek-midtrans-33', 'b:1;', 1785945557),
('kurtbeans-cache-cek-midtrans-34', 'b:1;', 1786633955),
('kurtbeans-cache-kasir|103.82.14.89', 'i:1;', 1786635250),
('kurtbeans-cache-kasir|103.82.14.89:timer', 'i:1786635250;', 1786635250),
('kurtbeans-cache-kurtbeans_antrean_awal_20260722', 'i:1;', 1784782800),
('kurtbeans-cache-kurtbeans_antrean_awal_20260723', 'i:25;', 1784869200),
('kurtbeans-cache-kurtbeans_antrean_awal_20260805', 'i:26;', 1785992400),
('kurtbeans-cache-kurtbeans_antrean_awal_20260813', 'i:34;', 1786683600);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_menu`, `quantity`, `subtotal`, `catatan`) VALUES
(1, 1, 13, 1, 25000.00, NULL),
(2, 2, 13, 1, 25000.00, NULL),
(3, 3, 13, 1, 25000.00, NULL),
(4, 3, 14, 1, 15000.00, NULL),
(5, 4, 13, 1, 25000.00, NULL),
(6, 4, 15, 1, 25000.00, NULL),
(7, 5, 14, 1, 15000.00, NULL),
(8, 6, 13, 1, 25000.00, NULL),
(9, 6, 14, 1, 15000.00, NULL),
(10, 7, 14, 1, 15000.00, NULL),
(11, 7, 15, 1, 25000.00, NULL),
(12, 8, 13, 1, 25000.00, NULL),
(13, 8, 14, 1, 15000.00, NULL),
(14, 9, 13, 2, 50000.00, NULL),
(15, 9, 14, 1, 15000.00, NULL),
(16, 10, 14, 1, 15000.00, NULL),
(17, 11, 14, 2, 30000.00, NULL),
(18, 12, 13, 1, 25000.00, NULL),
(19, 12, 14, 1, 15000.00, NULL),
(20, 13, 14, 2, 30000.00, NULL),
(21, 14, 13, 1, 25000.00, NULL),
(22, 14, 14, 1, 15000.00, NULL),
(23, 14, 15, 1, 25000.00, NULL),
(24, 14, 16, 1, 23000.00, NULL),
(25, 15, 14, 1, 15000.00, NULL),
(26, 16, 13, 1, 25000.00, NULL),
(27, 17, 15, 1, 25000.00, NULL),
(28, 18, 14, 1, 15000.00, NULL),
(29, 19, 13, 1, 25000.00, NULL),
(30, 20, 18, 1, 23000.00, NULL),
(31, 21, 13, 1, 25000.00, NULL),
(32, 21, 14, 1, 15000.00, NULL),
(33, 22, 15, 1, 25000.00, NULL),
(34, 23, 21, 1, 18000.00, NULL),
(35, 24, 21, 1, 18000.00, NULL),
(36, 24, 23, 1, 15000.00, NULL),
(37, 25, 13, 1, 25000.00, NULL),
(38, 25, 24, 1, 10000.00, NULL),
(39, 26, 14, 1, 15000.00, NULL),
(40, 27, 18, 1, 23000.00, NULL),
(41, 28, 13, 1, 25000.00, NULL),
(42, 29, 13, 1, 25000.00, NULL),
(43, 30, 13, 1, 25000.00, NULL),
(44, 30, 14, 1, 15000.00, NULL),
(45, 31, 13, 1, 25000.00, NULL),
(46, 31, 14, 1, 15000.00, NULL),
(47, 31, 18, 1, 23000.00, NULL),
(48, 32, 13, 1, 25000.00, NULL),
(49, 33, 14, 1, 15000.00, NULL),
(50, 34, 13, 1, 25000.00, NULL),
(51, 35, 14, 1, 15000.00, NULL),
(52, 35, 24, 1, 10000.00, NULL),
(53, 35, 26, 1, 20000.00, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'Coffee', 'Coffee', '2026-06-23 10:24:26'),
(2, 'Ice Tea', 'Tea', '2026-06-23 10:24:40'),
(5, 'Mocktail', NULL, '2026-07-08 17:30:59'),
(6, 'MilkShake Ice', 'MilkShake', '2026-07-22 14:22:44'),
(7, 'Manual Brew', NULL, '2026-07-22 14:22:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `meja`
--

CREATE TABLE `meja` (
  `id_meja` int(11) NOT NULL,
  `nomor_meja` int(11) NOT NULL,
  `qr_code` varchar(255) NOT NULL,
  `status_meja` enum('Tersedia','Terisi') DEFAULT 'Tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `meja`
--

INSERT INTO `meja` (`id_meja`, `nomor_meja`, `qr_code`, `status_meja`, `created_at`) VALUES
(10, 1, 'meja_1_1782849847.svg', 'Terisi', '2026-06-30 20:04:07'),
(11, 2, 'meja_2_1784090624.svg', 'Tersedia', '2026-07-15 04:43:45'),
(12, 12, 'meja_12_1786635808.svg', 'Tersedia', '2026-07-15 04:43:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL,
  `status_menu` enum('Tersedia','Habis') DEFAULT 'Tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `menu`
--

INSERT INTO `menu` (`id_menu`, `id_kategori`, `nama_menu`, `harga`, `stok`, `gambar`, `status_menu`, `created_at`, `updated_at`) VALUES
(13, 1, 'Capucino', 25000.00, 30, '1782847365_Cappuccino coffee isolated_ Illustration.jpeg', 'Tersedia', '2026-06-30 12:22:45', '2026-06-30 12:22:45'),
(14, 1, 'americano', 15000.00, 30, '1782847382_download.jpeg', 'Tersedia', '2026-06-30 12:23:02', '2026-08-13 15:55:18'),
(15, 6, 'Mactha', 25000.00, 20, '1782847410_Matcha Cup.jpeg', 'Tersedia', '2026-06-30 12:23:30', '2026-07-22 14:23:55'),
(16, 6, 'Taro', 23000.00, 5, '1782847439_A cup of taro latte.jpeg', 'Tersedia', '2026-06-30 12:23:59', '2026-07-22 14:23:40'),
(18, 1, 'Butterscoth', 23000.00, 0, '1784711989_WhatsApp Image 2026-07-22 at 16.18.32.jpeg', 'Tersedia', '2026-07-22 09:19:50', '2026-07-22 14:23:24'),
(19, 7, 'Coffe Tubruk', 10000.00, 0, '1784730279_Kopi Tubruk.png', 'Tersedia', '2026-07-22 14:24:40', '2026-07-22 14:26:41'),
(20, 7, 'Vietnam Drip', 15000.00, 0, '1784730332_Vietnam Drip.jpeg', 'Tersedia', '2026-07-22 14:25:32', '2026-07-22 14:25:32'),
(21, 7, 'Japanese', 18000.00, 0, '1784730358_Japanese.jpeg', 'Tersedia', '2026-07-22 14:25:58', '2026-07-22 14:25:58'),
(22, 7, 'V60', 18000.00, 0, '1784730382_Manual Brew.jpeg', 'Tersedia', '2026-07-22 14:26:22', '2026-07-22 14:26:22'),
(23, 6, 'MilkShake Coklat', 15000.00, 0, '1784730498_Milkshake Coklat.jpeg', 'Tersedia', '2026-07-22 14:28:18', '2026-07-22 14:28:18'),
(24, 2, 'Ice Sweet Tea', 10000.00, 0, '1784730525_Ice Sweet Tea.jpeg', 'Tersedia', '2026-07-22 14:28:45', '2026-07-22 14:28:45'),
(25, 2, 'Ice Lemon Tea', 13000.00, 0, '1784730551_Ice Lemon Tea.jpeg', 'Tersedia', '2026-07-22 14:29:11', '2026-07-22 14:29:11'),
(26, 2, 'Ice Lyche Tea', 20000.00, 0, '1784730608_Lychee Leci Tea.jpeg', 'Tersedia', '2026-07-22 14:30:08', '2026-07-22 14:30:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_pelanggan_sementara` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `status` enum('Terkirim','Gagal','Pending') DEFAULT 'Pending',
  `dikirim_pada` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifikasi`
--

INSERT INTO `notifikasi` (`id_notifikasi`, `id_pesanan`, `id_pelanggan_sementara`, `judul`, `pesan`, `status`, `dikirim_pada`) VALUES
(1, 4, 4, 'Pesanan Anda Siap!', 'Hai Tes Pemesanan dikasir, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(2, 5, 5, 'Pesanan Anda Siap!', 'Hai rido, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(3, 6, 6, 'Pesanan Anda Siap!', 'Hai Farhan, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(4, 7, 7, 'Pesanan Anda Siap!', 'Hai Didoy, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(5, 8, 8, 'Pesanan Anda Siap!', 'Hai Farhan, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(6, 11, 11, 'Pesanan Anda Siap!', 'Hai farhan, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(7, 13, 13, 'Pesanan Anda Siap!', 'Hai ferdi, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(8, 14, 14, 'Pesanan Anda Siap!', 'Hai adri, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(9, 16, 16, 'Pesanan Anda Siap!', 'Hai Neng, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(10, 15, 15, 'Pesanan Anda Siap!', 'Hai ad, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(11, 17, 17, 'Pesanan Anda Siap!', 'Hai bagus, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(12, 18, 18, 'Pesanan Anda Siap!', 'Hai kurt, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(13, 19, 19, 'Pesanan Anda Siap!', 'Hai Dia, pesanan Anda sudah siap diambil di Bar.', 'Terkirim', '2026-07-22 16:16:29'),
(14, 20, 20, 'Pesanan Anda Siap!', 'Hai AUF, pesanan Anda sudah siap diambil di Bar.', 'Terkirim', '2026-07-22 16:21:16'),
(15, 21, 21, 'Pesanan Anda Siap!', 'Hai enda, pesanan Anda sudah siap diambil di Bar.', 'Terkirim', '2026-07-22 20:23:33'),
(16, 22, 22, 'Pesanan Anda Siap!', 'Hai Ahmad, pesanan Anda sudah siap diambil di Bar.', 'Terkirim', '2026-07-22 21:34:52'),
(17, 23, 23, 'Pesanan Anda Siap!', 'Hai Ian, pesanan Anda sudah siap diambil di Bar.', 'Terkirim', '2026-07-22 21:37:39'),
(18, 24, 24, 'Pesanan Anda Siap!', 'Hai sidik, pesanan Anda sudah siap diambil di Bar.', 'Terkirim', '2026-07-22 23:02:47'),
(19, 26, 26, 'Pesanan Anda Siap!', 'Hai AUF, pesanan Anda sudah siap diambil di Bar.', 'Terkirim', '2026-08-05 15:10:10'),
(20, 27, 27, 'Pesanan Anda Siap!', 'Hai p, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(21, 29, 29, 'Pesanan Anda Siap!', 'Hai tes2, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(22, 30, 30, 'Pesanan Anda Siap!', 'Hai kali, pesanan Anda sudah siap diambil di Bar.', 'Terkirim', '2026-08-05 15:48:25'),
(23, 31, 31, 'Pesanan Anda Siap!', 'Hai enda, pesanan Anda sudah siap diambil di Bar.', 'Terkirim', '2026-08-05 22:49:19'),
(24, 33, 33, 'Pesanan Anda Siap!', 'Hai Budi, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(25, 34, 34, 'Pesanan Anda Siap!', 'Hai pe, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL),
(26, 34, 34, 'Pesanan Anda Siap!', 'Hai pe, pesanan Anda sudah siap diambil di Bar.', 'Gagal', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan_sementara`
--

CREATE TABLE `pelanggan_sementara` (
  `id_pelanggan_sementara` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `token_subscription` text DEFAULT NULL,
  `session_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pelanggan_sementara`
--

INSERT INTO `pelanggan_sementara` (`id_pelanggan_sementara`, `nama_pemesan`, `token_subscription`, `session_id`, `created_at`) VALUES
(1, 'tes', NULL, 'EtyZNEqhnHXKVuxKuUPR95GHthi4goOv341qQUDR', '2026-07-22 06:05:27'),
(2, 'Tes2', NULL, 'FHNVn1bVWJeJkPS6RQunc7zl79e7Ctp5nvXDVHK0', '2026-07-22 06:11:08'),
(3, 'Tes Meja 2', 'ea8A7QgmtahvCrOtfu2DFe:APA91bHLKlTbkLFyZwgh1W6gvFmSoPRzZZU_tav0cO3uCKJPacDD-U7AtURartDAeG7G6UI4P-_FwQzYUmsznvd_-hMu2qMMHT_kVyi-67vRGLPK1QQeR3A', 'FHNVn1bVWJeJkPS6RQunc7zl79e7Ctp5nvXDVHK0', '2026-07-22 06:15:40'),
(4, 'Tes Pemesanan dikasir', 'ea8A7QgmtahvCrOtfu2DFe:APA91bHLKlTbkLFyZwgh1W6gvFmSoPRzZZU_tav0cO3uCKJPacDD-U7AtURartDAeG7G6UI4P-_FwQzYUmsznvd_-hMu2qMMHT_kVyi-67vRGLPK1QQeR3A', 'FHNVn1bVWJeJkPS6RQunc7zl79e7Ctp5nvXDVHK0', '2026-07-22 06:28:04'),
(5, 'rido', 'ea8A7QgmtahvCrOtfu2DFe:APA91bHLKlTbkLFyZwgh1W6gvFmSoPRzZZU_tav0cO3uCKJPacDD-U7AtURartDAeG7G6UI4P-_FwQzYUmsznvd_-hMu2qMMHT_kVyi-67vRGLPK1QQeR3A', '0802a025-aef8-4834-8b2e-2e42849c9bd5', '2026-07-22 06:54:27'),
(6, 'Farhan', 'ea8A7QgmtahvCrOtfu2DFe:APA91bHLKlTbkLFyZwgh1W6gvFmSoPRzZZU_tav0cO3uCKJPacDD-U7AtURartDAeG7G6UI4P-_FwQzYUmsznvd_-hMu2qMMHT_kVyi-67vRGLPK1QQeR3A', '7e5c1ba7-76d7-4038-822f-ae63aeec44e4', '2026-07-22 07:02:16'),
(7, 'Didoy', NULL, '7b6a6979-dfe2-4e44-8290-53ecf47d49ba', '2026-07-22 07:04:06'),
(8, 'Farhan', 'ea8A7QgmtahvCrOtfu2DFe:APA91bHLKlTbkLFyZwgh1W6gvFmSoPRzZZU_tav0cO3uCKJPacDD-U7AtURartDAeG7G6UI4P-_FwQzYUmsznvd_-hMu2qMMHT_kVyi-67vRGLPK1QQeR3A', '7e5c1ba7-76d7-4038-822f-ae63aeec44e4', '2026-07-22 07:17:27'),
(9, 'Farhan', 'ea8A7QgmtahvCrOtfu2DFe:APA91bHLKlTbkLFyZwgh1W6gvFmSoPRzZZU_tav0cO3uCKJPacDD-U7AtURartDAeG7G6UI4P-_FwQzYUmsznvd_-hMu2qMMHT_kVyi-67vRGLPK1QQeR3A', 'b786b07a-5bbc-444c-aecd-cd466103b475', '2026-07-22 07:21:05'),
(10, 'Farhan', 'ea8A7QgmtahvCrOtfu2DFe:APA91bHLKlTbkLFyZwgh1W6gvFmSoPRzZZU_tav0cO3uCKJPacDD-U7AtURartDAeG7G6UI4P-_FwQzYUmsznvd_-hMu2qMMHT_kVyi-67vRGLPK1QQeR3A', 'b786b07a-5bbc-444c-aecd-cd466103b475', '2026-07-22 07:33:16'),
(11, 'farhan', NULL, '863edea4-d31b-48b8-81ee-e4fa6ad51e5b', '2026-07-22 08:07:16'),
(12, 'kukuh', NULL, '863edea4-d31b-48b8-81ee-e4fa6ad51e5b', '2026-07-22 08:10:07'),
(13, 'ferdi', NULL, '863edea4-d31b-48b8-81ee-e4fa6ad51e5b', '2026-07-22 08:11:07'),
(14, 'adri', NULL, '0350e9a5-7cd7-46e4-bcae-e6980059db02', '2026-07-22 08:18:44'),
(15, 'ad', NULL, '0350e9a5-7cd7-46e4-bcae-e6980059db02', '2026-07-22 08:29:46'),
(16, 'Neng', NULL, 'ce5aa3b5-3cc7-4f0f-8655-ee80dc7c807c', '2026-07-22 08:42:36'),
(17, 'bagus', NULL, '3a4e4db5-19fb-4f69-b6c6-05833354945a', '2026-07-22 08:51:19'),
(18, 'kurt', NULL, '3a4e4db5-19fb-4f69-b6c6-05833354945a', '2026-07-22 08:55:31'),
(19, 'Dia', 'eXeojNN4gtMJ3m4USQ4xuO:APA91bGsrFQosAflR2OrtYY-emscasK8dxx_Ox3ORlYcLHkxsyG7Zx9yIU5QSS-mg1V01vOdCqTxqoEBALNzGI3jr9VUPRmtq4UQE96CGffo7WRexW1cjNg', 'f8276364-d2e1-4d41-af5b-282ce1e1f4f0', '2026-07-22 09:15:26'),
(20, 'AUF', 'e_lCPpn3BJGwnjS524-wBF:APA91bHg2hTQSOox2qrcSx7ABGggzCjNPSErw6ubTE3bNASkl7CmC0kj7LWV6G3VBFTJpolVig1rihHNfiruAyAfm12DposPsYuFUyb0TACT8hs0Qrnm_V4', '10b034cc-7869-449f-bca7-323e061d20d6', '2026-07-22 09:20:29'),
(21, 'enda', 'cq-5RYUZ-YDlKvPZBz5FKX:APA91bH7iU2Iz-VZOyYlP4ZshbFDjgYMK68dOAkUAbVOM-qK0lmaDDRDkWzLreZ65EHiKwW2gxEbyiTarrDjzObtCLQYhpGwevJbzRRc0AHUVVAfsyFDXUA', '24fc84f9-686e-4a20-be06-7b73004663c9', '2026-07-22 13:22:33'),
(22, 'Ahmad', 'f-ARCP8UlqHwlvLjRY8cLg:APA91bEGKr5PzIF7lAq1gkWXhrWJGTg0e-_7rz-QljRtwkhCYOospbrAf2R_wMsygz7UhwphYk_wxvZ-6CWfPLgBrOtif3-6yQLD2Fn_wdNqlwVC6iI7YyE', '3f8a8309-e20c-4b49-9428-563c5f508714', '2026-07-22 14:34:05'),
(23, 'Ian', 'f-ARCP8UlqHwlvLjRY8cLg:APA91bEGKr5PzIF7lAq1gkWXhrWJGTg0e-_7rz-QljRtwkhCYOospbrAf2R_wMsygz7UhwphYk_wxvZ-6CWfPLgBrOtif3-6yQLD2Fn_wdNqlwVC6iI7YyE', '05d9da7d-5683-4d24-a339-d386e34ecdb4', '2026-07-22 14:36:27'),
(24, 'sidik', 'ewtWG0HwDMMtVZIZqqj4Pe:APA91bEidWc9GyY2a-1JTBXnL959TEfrm79maKvo7QZCmqmUnkKoUjsdFs8Hv6xAtrr1TLW27AFeyWoSm6of_bA7MZ_n5Mb_Fs52oJ7-X4zp9xCqpHJC49M', 'f971bbc4-28e3-4d84-8ae7-a03c9aea2255', '2026-07-22 16:02:05'),
(25, 'Lilis', 'eXeojNN4gtMJ3m4USQ4xuO:APA91bGsrFQosAflR2OrtYY-emscasK8dxx_Ox3ORlYcLHkxsyG7Zx9yIU5QSS-mg1V01vOdCqTxqoEBALNzGI3jr9VUPRmtq4UQE96CGffo7WRexW1cjNg', 'e8a1c649-160c-4b7c-82c3-9ee64821b54d', '2026-07-23 07:58:29'),
(26, 'AUF', 'fR_Clx5yccwqxBC7Jlc8Oy:APA91bEKPAJK9rfWEVc3xjbs3Dy67sTfTfG7RudZkhWkXogK8LXt0HeIdkNj3ztgHtB8OVt-B-QzhjI_WYVDlvI7frCWaxXiSQflExthiFcCF8SkfVK5dxo', '4cbdbe9f-912f-4623-a559-00508577e049', '2026-08-05 08:08:03'),
(27, 'p', NULL, '41055011-efe2-4725-88aa-cf60c4291d2e', '2026-08-05 08:30:11'),
(28, 'tes', NULL, '41055011-efe2-4725-88aa-cf60c4291d2e', '2026-08-05 08:42:22'),
(29, 'tes2', 'cn3kwJJDuxlMXAq9aufa7E:APA91bEF-m6vpaPAFDueJvZ9quTEHdrs08bgIO1Hxqf50TZ7K3lIvqroLkZOubLn8uFJrQnGKt1dOt_VMphLsdC9d2U0m32TzfyXc3XlsK8Fu51ka8_EC8k', '41055011-efe2-4725-88aa-cf60c4291d2e', '2026-08-05 08:44:50'),
(30, 'kali', 'cn3kwJJDuxlMXAq9aufa7E:APA91bEF-m6vpaPAFDueJvZ9quTEHdrs08bgIO1Hxqf50TZ7K3lIvqroLkZOubLn8uFJrQnGKt1dOt_VMphLsdC9d2U0m32TzfyXc3XlsK8Fu51ka8_EC8k', '41055011-efe2-4725-88aa-cf60c4291d2e', '2026-08-05 08:47:26'),
(31, 'enda', 'eVca1kh7iNu_9iitUex_-k:APA91bHeA5xAsWjtSlqvqQJbwm9b_aW058tudP_ZmY658DUwmqlnzsvxZiG1y60awb0Yl5JFhQVXXyRlBEC1Leb0Tq4H_-5X8TY44GQmlu0u2_vhabMAKeA', '582fbab5-a109-4f82-8ca7-2920ade00136', '2026-08-05 15:43:16'),
(32, 'hen', 'eVca1kh7iNu_9iitUex_-k:APA91bHeA5xAsWjtSlqvqQJbwm9b_aW058tudP_ZmY658DUwmqlnzsvxZiG1y60awb0Yl5JFhQVXXyRlBEC1Leb0Tq4H_-5X8TY44GQmlu0u2_vhabMAKeA', '582fbab5-a109-4f82-8ca7-2920ade00136', '2026-08-05 15:52:54'),
(33, 'Budi', NULL, '86578a62-0900-49b3-a6c1-a887bacc2736', '2026-08-05 15:58:43'),
(34, 'pe', NULL, '043ca657-1ea4-4e5b-bb20-9a3a796b52fa', '2026-08-13 15:11:38'),
(35, 'adrian', 'dnpP_yiSGdVrJZjuqnvf-7:APA91bHp4s2Itq_BxVKiA6JNCwk_hBQFKfmI81JFNi8mCgQ817M56BQdwB2iIDCA1tkNVEj3ZqzfRdVw4nxlxw721sbUGPLg_chHDcEjud2dkv6YpF4hzlE', '3ce306f7-9107-4b4b-a3a7-0e87dc8ac1ff', '2026-08-13 15:45:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `order_id` varchar(100) NOT NULL,
  `gross_amount` decimal(10,2) NOT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `transaction_status` varchar(50) DEFAULT NULL,
  `fraud_status` varchar(50) DEFAULT NULL,
  `payment_token` varchar(255) DEFAULT NULL,
  `payment_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `order_id`, `gross_amount`, `payment_type`, `transaction_status`, `fraud_status`, `payment_token`, `payment_url`, `created_at`, `updated_at`) VALUES
(1, 1, 'KB-1784700327-637', 25000.00, NULL, 'pending', NULL, '9b7b5678-3aeb-457a-b4ec-ccd855c54c42', NULL, '2026-07-22 06:05:28', '2026-07-22 06:05:28'),
(2, 2, 'KB-1784700668-933', 25000.00, NULL, 'pending', NULL, 'fd5a905a-6008-4bad-bbb8-58e7a99b3f44', NULL, '2026-07-22 06:11:08', '2026-07-22 06:11:08'),
(3, 3, 'KB-1784700940-941', 40000.00, NULL, 'pending', NULL, '35bf5aa3-0b17-4041-b320-5643208006ae', NULL, '2026-07-22 06:15:41', '2026-07-22 06:15:41'),
(4, 10, 'KB-1784705596-494', 15000.00, NULL, 'pending', NULL, '15343387-061d-4a7c-842f-37bc78704083', NULL, '2026-07-22 07:33:16', '2026-07-22 07:33:16'),
(5, 11, 'KB-1784707635-951', 30000.00, 'gopay', 'settlement', 'accept', '8f0ce9c7-83db-45d8-965a-58b8bc3cc4d9', NULL, '2026-07-22 08:07:16', '2026-07-22 08:09:47'),
(6, 12, 'KB-1784707807-883', 40000.00, NULL, 'pending', NULL, '5f739378-8f1a-4ab5-b826-fb3e91086ebd', NULL, '2026-07-22 08:10:07', '2026-07-22 08:10:07'),
(7, 13, 'KB-1784707867-638', 30000.00, 'bank_transfer', 'settlement', 'accept', 'c1e7b182-e938-4ec0-8b4e-990b387b281a', NULL, '2026-07-22 08:11:07', '2026-07-22 08:11:47'),
(8, 14, 'KB-1784708324-140', 88000.00, 'qris', 'settlement', 'accept', '840707d5-f9ee-4347-9f20-68b2bafb2ab5', NULL, '2026-07-22 08:18:45', '2026-07-22 08:24:27'),
(9, 15, 'KB-1784708986-301', 15000.00, 'bank_transfer', 'settlement', 'accept', '1bf55270-32f9-40bb-a6f5-9219fdfaf1b5', NULL, '2026-07-22 08:29:46', '2026-07-22 08:30:26'),
(10, 17, 'KB-1784710279-276', 25000.00, 'gopay', 'settlement', 'accept', 'ae9fa4b3-c8f6-4ecd-bc6d-e3ace18207c5', NULL, '2026-07-22 08:51:20', '2026-07-22 08:51:36'),
(11, 18, 'KB-1784710531-713', 15000.00, 'gopay', 'settlement', 'accept', 'dd3bfeac-3511-48df-abf3-93fa84ababa7', NULL, '2026-07-22 08:55:31', '2026-07-22 08:55:37'),
(12, 23, 'KB-1784730987-474', 18000.00, 'gopay', 'settlement', 'accept', 'e090ef9a-0c4e-472c-b470-54eb486ef8b4', NULL, '2026-07-22 14:36:28', '2026-07-22 14:36:50'),
(13, 24, 'KB-1784736125-137', 33000.00, 'gopay', 'settlement', 'accept', '197664e8-2564-4421-bc44-925b73321b37', NULL, '2026-07-22 16:02:06', '2026-07-22 16:02:24'),
(14, 27, 'KB-1785919286-210', 23000.00, 'gopay', 'settlement', 'accept', 'e42c8fdb-8fd2-486c-8b2d-54ae0b12a665', NULL, '2026-08-05 08:30:11', '2026-08-05 08:41:55'),
(15, 28, 'KB-1785919387-650', 25000.00, NULL, 'pending', NULL, '6206d803-1db9-4731-89e0-16fdc124c32d', NULL, '2026-08-05 08:42:23', '2026-08-05 08:43:08'),
(16, 29, 'KB-1785919490-572', 25000.00, 'gopay', 'settlement', 'accept', '209ec3b5-fac8-4488-8e6f-a054a69446c9', NULL, '2026-08-05 08:44:50', '2026-08-05 08:44:56'),
(17, 30, 'KB-1785919663-168', 40000.00, 'gopay', 'settlement', 'accept', '0cc12ac6-4e21-42b6-936a-013e3d70633a', NULL, '2026-08-05 08:47:26', '2026-08-05 08:47:53'),
(18, 31, 'KB-1785944596-749', 63000.00, 'gopay', 'settlement', 'accept', 'ff030b8d-5203-4053-9098-e2cda562f25d', NULL, '2026-08-05 15:43:16', '2026-08-05 15:43:38'),
(19, 33, 'KB-1785945523-423', 15000.00, 'gopay', 'settlement', 'accept', '299a9561-f2bf-49d9-a991-90e1bd02720a', NULL, '2026-08-05 15:58:44', '2026-08-05 15:59:03'),
(20, 34, 'KB-1786633898-288', 25000.00, 'gopay', 'settlement', 'accept', '766192a6-1f7b-4023-8294-af7f6f2b8849', NULL, '2026-08-13 15:11:39', '2026-08-13 15:12:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('Admin','Kasir','Barista') NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id_user`, `username`, `password`, `nama_lengkap`, `role`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$12$.Uk.CJtwcL8EWWzSaEpFv.31Tox4qOh.h.d1sWGD.sMEAXC3XUsyS', 'Admin Utama', 'Admin', NULL, '2026-05-18 12:49:39'),
(2, 'kasir', '$2y$12$R7V2eq0wGdTe5fSbEqIIyuOG3EcRQAuCvfDACjkTtt.DmkraDrVDK', 'Kasir KurtBeans', 'Kasir', NULL, '2026-05-18 12:49:39'),
(3, 'barista', '$2y$12$YGURU/nipC6xzPtZL090c.wOxFSyvcVG0AFvKLL4rPxM8Z49kzq.6', 'Barista KurtBeans', 'Barista', NULL, '2026-05-18 12:49:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_meja` int(11) NOT NULL,
  `id_pelanggan_sementara` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status_pesanan` enum('Menunggu Pembayaran','Menunggu Diproses','Diproses','Siap Diambil','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu Pembayaran',
  `status_pembayaran` enum('Belum Lunas','Lunas','Gagal') DEFAULT 'Belum Lunas',
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `midtrans_order_id` varchar(100) DEFAULT NULL,
  `tgl_pesan` datetime DEFAULT current_timestamp(),
  `tgl_bayar` datetime DEFAULT NULL,
  `tgl_selesai` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_meja`, `id_pelanggan_sementara`, `total_harga`, `status_pesanan`, `status_pembayaran`, `metode_pembayaran`, `midtrans_order_id`, `tgl_pesan`, `tgl_bayar`, `tgl_selesai`, `created_at`, `updated_at`) VALUES
(1, 10, 1, 25000.00, 'Dibatalkan', 'Gagal', NULL, 'KB-1784700327-637', '2026-07-22 13:05:27', NULL, NULL, '2026-07-22 06:05:27', '2026-07-22 06:27:20'),
(2, 10, 2, 25000.00, 'Dibatalkan', 'Gagal', NULL, 'KB-1784700668-933', '2026-07-22 13:11:08', NULL, NULL, '2026-07-22 06:11:08', '2026-07-22 06:27:18'),
(3, 11, 3, 40000.00, 'Dibatalkan', 'Gagal', NULL, 'KB-1784700940-941', '2026-07-22 13:15:40', NULL, NULL, '2026-07-22 06:15:40', '2026-07-22 06:27:15'),
(4, 10, 4, 50000.00, 'Selesai', 'Lunas', 'Tunai', NULL, '2026-07-22 13:28:04', '2026-07-22 13:28:35', '2026-07-22 13:29:40', '2026-07-22 06:28:04', '2026-07-22 06:55:41'),
(5, 12, 5, 15000.00, 'Selesai', 'Lunas', 'Tunai', NULL, '2026-07-22 13:54:27', '2026-07-22 13:54:56', '2026-07-22 13:55:47', '2026-07-22 06:54:27', '2026-07-22 06:57:37'),
(6, 12, 6, 40000.00, 'Selesai', 'Lunas', 'Tunai', NULL, '2026-07-22 14:02:16', '2026-07-22 14:02:42', '2026-07-22 14:04:51', '2026-07-22 07:02:16', '2026-07-22 07:13:55'),
(7, 10, 7, 40000.00, 'Selesai', 'Lunas', 'Tunai', NULL, '2026-07-22 14:04:06', '2026-07-22 14:04:28', '2026-07-22 14:09:25', '2026-07-22 07:04:06', '2026-07-22 07:13:58'),
(8, 12, 8, 40000.00, 'Selesai', 'Lunas', 'Tunai', NULL, '2026-07-22 14:17:27', '2026-07-22 14:17:39', '2026-07-22 14:17:55', '2026-07-22 07:17:27', '2026-07-22 07:20:14'),
(9, 10, 9, 65000.00, 'Dibatalkan', 'Gagal', 'Tunai', NULL, '2026-07-22 14:21:05', NULL, NULL, '2026-07-22 07:21:05', '2026-07-22 07:22:16'),
(10, 10, 10, 15000.00, 'Menunggu Pembayaran', 'Gagal', NULL, 'KB-1784705596-494', '2026-07-22 14:33:16', NULL, NULL, '2026-07-22 07:33:16', '2026-07-22 08:14:46'),
(11, 10, 11, 30000.00, 'Selesai', 'Lunas', 'gopay', 'KB-1784707635-951', '2026-07-22 15:07:16', '2026-07-22 15:09:47', '2026-07-22 15:17:07', '2026-07-22 08:07:16', '2026-07-22 08:24:03'),
(12, 10, 12, 40000.00, 'Dibatalkan', 'Gagal', NULL, 'KB-1784707807-883', '2026-07-22 15:10:07', NULL, NULL, '2026-07-22 08:10:07', '2026-07-22 08:17:44'),
(13, 10, 13, 30000.00, 'Selesai', 'Lunas', 'bank_transfer', 'KB-1784707867-638', '2026-07-22 15:11:07', '2026-07-22 15:11:47', '2026-07-22 15:26:36', '2026-07-22 08:11:07', '2026-07-22 08:26:46'),
(14, 12, 14, 88000.00, 'Selesai', 'Lunas', 'qris', 'KB-1784708324-140', '2026-07-22 15:18:44', '2026-07-22 15:24:27', '2026-07-22 15:29:12', '2026-07-22 08:18:44', '2026-07-22 08:29:15'),
(15, 12, 15, 15000.00, 'Selesai', 'Lunas', 'bank_transfer', 'KB-1784708986-301', '2026-07-22 15:29:46', '2026-07-22 15:30:26', '2026-07-22 15:49:54', '2026-07-22 08:29:46', '2026-07-22 08:49:57'),
(16, 10, 16, 25000.00, 'Selesai', 'Lunas', 'Tunai', NULL, '2026-07-22 15:42:36', '2026-07-22 15:42:55', '2026-07-22 15:43:25', '2026-07-22 08:42:36', '2026-07-22 08:44:06'),
(17, 11, 17, 25000.00, 'Selesai', 'Lunas', 'gopay', 'KB-1784710279-276', '2026-07-22 15:51:19', '2026-07-22 15:51:36', '2026-07-22 15:53:44', '2026-07-22 08:51:19', '2026-07-22 08:54:23'),
(18, 11, 18, 15000.00, 'Selesai', 'Lunas', 'gopay', 'KB-1784710531-713', '2026-07-22 15:55:31', '2026-07-22 15:55:37', '2026-07-22 15:56:20', '2026-07-22 08:55:31', '2026-07-22 08:56:55'),
(19, 10, 19, 25000.00, 'Selesai', 'Lunas', 'Tunai', NULL, '2026-07-22 16:15:27', '2026-07-22 16:15:58', '2026-07-22 16:16:18', '2026-07-22 09:15:27', '2026-07-22 09:16:42'),
(20, 10, 20, 23000.00, 'Selesai', 'Lunas', 'Tunai', NULL, '2026-07-22 16:20:29', '2026-07-22 16:21:03', '2026-07-22 16:21:15', '2026-07-22 09:20:29', '2026-07-22 09:21:30'),
(21, 10, 21, 40000.00, 'Selesai', 'Lunas', 'Tunai', NULL, '2026-07-22 20:22:33', '2026-07-22 20:22:52', '2026-07-22 20:23:22', '2026-07-22 13:22:33', '2026-07-22 13:23:50'),
(22, 10, 22, 25000.00, 'Selesai', 'Lunas', 'Tunai', NULL, '2026-07-22 21:34:05', '2026-07-22 21:34:16', '2026-07-22 21:34:50', '2026-07-22 14:34:05', '2026-07-22 14:35:01'),
(23, 10, 23, 18000.00, 'Selesai', 'Lunas', 'gopay', 'KB-1784730987-474', '2026-07-22 21:36:27', '2026-07-22 21:36:50', '2026-07-22 21:37:38', '2026-07-22 14:36:27', '2026-07-22 14:37:55'),
(24, 11, 24, 33000.00, 'Selesai', 'Lunas', 'gopay', 'KB-1784736125-137', '2026-07-22 23:02:05', '2026-07-22 23:02:24', '2026-07-22 23:02:46', '2026-07-22 16:02:05', '2026-07-22 16:03:06'),
(25, 10, 25, 35000.00, 'Dibatalkan', 'Gagal', 'Tunai', NULL, '2026-07-23 14:58:29', NULL, NULL, '2026-07-23 07:58:29', '2026-07-23 08:01:39'),
(26, 10, 26, 15000.00, 'Selesai', 'Lunas', 'Tunai', NULL, '2026-08-05 15:08:03', '2026-08-05 15:09:24', '2026-08-05 15:09:59', '2026-08-05 08:08:03', '2026-08-05 08:10:31'),
(27, 10, 27, 23000.00, 'Selesai', 'Lunas', 'gopay', 'KB-1785919286-210', '2026-08-05 15:30:11', '2026-08-05 15:41:55', '2026-08-05 15:45:20', '2026-08-05 08:30:11', '2026-08-05 08:45:46'),
(28, 10, 28, 25000.00, 'Dibatalkan', 'Gagal', NULL, 'KB-1785919387-650', '2026-08-05 15:42:22', NULL, NULL, '2026-08-05 08:42:22', '2026-08-05 08:43:14'),
(29, 10, 29, 25000.00, 'Selesai', 'Lunas', 'gopay', 'KB-1785919490-572', '2026-08-05 15:44:50', '2026-08-05 15:44:56', '2026-08-05 15:45:28', '2026-08-05 08:44:50', '2026-08-05 08:48:09'),
(30, 10, 30, 40000.00, 'Selesai', 'Lunas', 'gopay', 'KB-1785919663-168', '2026-08-05 15:47:26', '2026-08-05 15:47:53', '2026-08-05 15:48:23', '2026-08-05 08:47:26', '2026-08-05 08:48:37'),
(31, 10, 31, 63000.00, 'Selesai', 'Lunas', 'gopay', 'KB-1785944596-749', '2026-08-05 22:43:16', '2026-08-05 22:43:38', '2026-08-05 22:49:08', '2026-08-05 15:43:16', '2026-08-05 15:49:42'),
(32, 10, 32, 25000.00, 'Dibatalkan', 'Gagal', 'Tunai', NULL, '2026-08-05 22:52:54', NULL, NULL, '2026-08-05 15:52:54', '2026-08-05 15:53:37'),
(33, 10, 33, 15000.00, 'Selesai', 'Lunas', 'gopay', 'KB-1785945523-423', '2026-08-05 22:58:43', '2026-08-05 22:59:03', '2026-08-05 22:59:27', '2026-08-05 15:58:43', '2026-08-05 15:59:51'),
(34, 10, 34, 25000.00, 'Siap Diambil', 'Lunas', 'gopay', 'KB-1786633898-288', '2026-08-13 22:11:38', '2026-08-13 22:12:21', '2026-08-13 22:46:39', '2026-08-13 15:11:38', '2026-08-13 15:46:39'),
(35, 12, 35, 45000.00, 'Diproses', 'Lunas', 'Tunai', NULL, '2026-08-13 22:45:54', '2026-08-13 22:46:18', NULL, '2026-08-13 15:45:54', '2026-08-13 15:46:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('4sfdGbiPSo4vMnMb41Zw0jUE1OmrxJrtkRhM4juT', 2, '103.82.14.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTjkzTGNOcFhmdmswekUxOUFqYVcwNG1GTUVxZHNlcjdVT2tCWDVyTCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjA6Imh0dHBzOi8vc3Bva2VuLXF1aW50dXBsZS1kZXNwaXNlLm5ncm9rLWZyZWUuZGV2L2thc2lyL3NpbnlhbCI7czo1OiJyb3V0ZSI7czoxMjoia2FzaXIuc2lueWFsIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1786636052),
('GAmdOTgGnUzCty8epCT6i7z5sR4eOf355FtLkQBy', 1, '103.82.14.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNUtlOFcxaFA0RlhQTTBPZjFjWmpwSnFjakNHdWNuYXZudU1uZEx1VCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjM6Imh0dHBzOi8vc3Bva2VuLXF1aW50dXBsZS1kZXNwaXNlLm5ncm9rLWZyZWUuZGV2L2FkbWluL2Rhc2hib2FyZCI7czo1OiJyb3V0ZSI7czoxNToiYWRtaW4uZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1786638416),
('mHzkCocv805PCAuyI9mIFWvdwW9UkPpRID0YkUtl', NULL, '114.122.109.124', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5.2 Mobile/15E148 Safari/604.1', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiQ05rYWtKTXU0SDNsTVNTQXlGMzFBTzM0OUpFOWhKRGtTWFF4QkU4UyI7czo3OiJpZF9tZWphIjtpOjEyO3M6MTA6Im5vbW9yX21lamEiO2k6MTI7czo5OiJrdW5qdW5nYW4iO3M6MzY6IjNjZTMwNmY3LTkxMDctNGI0Yi1hM2E3LTBlODdkYzhhYzFmZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHBzOi8vc3Bva2VuLXF1aW50dXBsZS1kZXNwaXNlLm5ncm9rLWZyZWUuZGV2L21lbnUiO3M6NToicm91dGUiO3M6MTM6ImN1c3RvbWVyLm1lbnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1786636534),
('NlL6LXOxLX74nulG6HmT9cW133qY6slf06v1gc9M', 3, '103.82.14.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibDAxZ0Voc2ZPck9waTV6RjdjREYwN3RzZkFBa1dOTkpkemZLQXhRMSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjYyOiJodHRwczovL3Nwb2tlbi1xdWludHVwbGUtZGVzcGlzZS5uZ3Jvay1mcmVlLmRldi9iYXJpc3RhL3NpbnlhbCI7czo1OiJyb3V0ZSI7czoxNDoiYmFyaXN0YS5zaW55YWwiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1786639923),
('VvtGsnLaKKxrWrRraTwgAJw8c9avwqvXIKnVExhy', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTFNMMGhEWEowb1lsbVo3dWtIWEl2R2VKVWZSd2Q4NGxiUEltNE9vciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9rYXNpci9zaW55YWwiO3M6NToicm91dGUiO3M6MTI6Imthc2lyLnNpbnlhbCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1786633794);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `meja`
--
ALTER TABLE `meja`
  ADD PRIMARY KEY (`id_meja`),
  ADD UNIQUE KEY `nomor_meja` (`nomor_meja`);

--
-- Indeks untuk tabel `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_pelanggan_sementara` (`id_pelanggan_sementara`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `pelanggan_sementara`
--
ALTER TABLE `pelanggan_sementara`
  ADD PRIMARY KEY (`id_pelanggan_sementara`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD UNIQUE KEY `midtrans_order_id` (`midtrans_order_id`),
  ADD KEY `id_meja` (`id_meja`),
  ADD KEY `id_pelanggan_sementara` (`id_pelanggan_sementara`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `meja`
--
ALTER TABLE `meja`
  MODIFY `id_meja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `pelanggan_sementara`
--
ALTER TABLE `pelanggan_sementara`
  MODIFY `id_pelanggan_sementara` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifikasi_ibfk_2` FOREIGN KEY (`id_pelanggan_sementara`) REFERENCES `pelanggan_sementara` (`id_pelanggan_sementara`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_meja`) REFERENCES `meja` (`id_meja`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_pelanggan_sementara`) REFERENCES `pelanggan_sementara` (`id_pelanggan_sementara`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
