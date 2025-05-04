-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Bulan Mei 2025 pada 05.49
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
-- Database: `hartono_motor`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name`, `slug`, `description`, `order`, `created_at`, `updated_at`) VALUES
(1, 'Tips Perawatan Mobil', 'tips-perawatan-mobil', 'Berbagai tips dan trik untuk merawat mobil Anda agar tetap prima', 1, '2025-05-02 06:02:51', '2025-05-02 06:02:51'),
(2, 'Teknologi Otomotif', 'teknologi-otomotif', 'Informasi terkini tentang teknologi dan inovasi di dunia otomotif', 2, '2025-05-02 06:02:51', '2025-05-02 06:02:51'),
(3, 'Servis dan Perbaikan', 'servis-dan-perbaikan', 'Panduan servis dan perbaikan mobil untuk berbagai masalah umum', 3, '2025-05-02 06:02:51', '2025-05-02 06:02:51'),
(4, 'Sparepart dan Aksesoris', 'sparepart-dan-aksesoris', 'Informasi tentang sparepart dan aksesoris mobil terbaru', 4, '2025-05-02 06:02:51', '2025-05-02 06:02:51'),
(5, 'Berita Otomotif', 'berita-otomotif', 'Berita terkini seputar dunia otomotif dalam dan luar negeri', 5, '2025-05-02 06:02:51', '2025-05-02 06:02:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `category_id`, `author_id`, `is_published`, `is_featured`, `published_at`, `view_count`, `created_at`, `updated_at`) VALUES
(1, 'Yonatan Dikabarkan halo', 'yonatan-dikabarkan-halo', 'Karep', '<p>Top G</p>', 'blog-featured/01JT8JJTD8SD4HVMZ8627KGZJA.png', 2, 1, 1, 0, '2025-05-02 06:21:35', 0, '2025-05-02 06:22:12', '2025-05-02 06:28:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `blog_post_tag`
--

CREATE TABLE `blog_post_tag` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `blog_post_id` bigint(20) UNSIGNED NOT NULL,
  `blog_tag_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `blog_post_tag`
--

INSERT INTO `blog_post_tag` (`id`, `blog_post_id`, `blog_tag_id`, `created_at`, `updated_at`) VALUES
(1, 1, 8, '2025-05-02 06:22:12', '2025-05-02 06:22:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `blog_tags`
--

CREATE TABLE `blog_tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `blog_tags`
--

INSERT INTO `blog_tags` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Perawatan Mobil', 'perawatan-mobil', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(2, 'Mesin', 'mesin', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(3, 'AC Mobil', 'ac-mobil', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(4, 'Ban', 'ban', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(5, 'Oli', 'oli', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(6, 'Rem', 'rem', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(7, 'Transmisi', 'transmisi', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(8, 'Aki', 'aki', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(9, 'Radiator', 'radiator', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(10, 'Suspensi', 'suspensi', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(11, 'Teknologi', 'teknologi', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(12, 'Mobil Listrik', 'mobil-listrik', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(13, 'Hybrid', 'hybrid', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(14, 'Kelistrikan', 'kelistrikan', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(15, 'Servis Berkala', 'servis-berkala', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(16, 'DIY', 'diy', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(17, 'Sparepart', 'sparepart', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(18, 'Aksesoris', 'aksesoris', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(19, 'Interior', 'interior', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(20, 'Eksterior', 'eksterior', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(21, 'Berita', 'berita', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(22, 'Tips', 'tips', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(23, 'Review', 'review', '2025-05-02 06:03:04', '2025-05-02 06:03:04'),
(24, 'Tutorial', 'tutorial', '2025-05-02 06:03:04', '2025-05-02 06:03:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `car_model` varchar(255) NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('laravel_cache_356a192b7913b04c54574d18c28d46e6395428ab', 'i:1;', 1746287395),
('laravel_cache_356a192b7913b04c54574d18c28d46e6395428ab:timer', 'i:1746287395;', 1746287395),
('laravel_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:2;', 1746284797),
('laravel_cache_livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1746284797;', 1746284797);

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
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL COMMENT 'How the customer found us',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_service_date` date DEFAULT NULL,
  `service_count` int(11) NOT NULL DEFAULT 0,
  `total_spent` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `city`, `birth_date`, `gender`, `notes`, `source`, `is_active`, `last_service_date`, `service_count`, `total_spent`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'test 1', '0852913212323', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-05-03', 2, 0.00, '2025-05-03 09:00:19', '2025-05-03 09:15:21', NULL),
(2, 'Bukan', '085290816081', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-05-03', 1, 0.00, '2025-05-03 09:12:20', '2025-05-03 09:12:20', NULL);

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
-- Struktur dari tabel `galleries`
--

CREATE TABLE `galleries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gallery_categories`
--

CREATE TABLE `gallery_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `gallery_categories`
--

INSERT INTO `gallery_categories` (`id`, `name`, `slug`, `description`, `order`, `created_at`, `updated_at`) VALUES
(1, 'Bengkel', 'bengkel', 'Foto-foto fasilitas dan area bengkel Hartono Motor', 1, '2025-05-02 04:26:33', '2025-05-02 04:26:33'),
(2, 'Mekanik', 'mekanik', 'Foto-foto tim mekanik Hartono Motor', 2, '2025-05-02 04:26:33', '2025-05-02 04:26:33'),
(3, 'Hasil Servis', 'hasil-servis', 'Foto-foto hasil servis dan perbaikan kendaraan', 3, '2025-05-02 04:26:33', '2025-05-02 04:26:33'),
(4, 'Sparepart', 'sparepart', 'Foto-foto koleksi sparepart yang tersedia', 4, '2025-05-02 04:26:33', '2025-05-02 04:26:33'),
(5, 'Kegiatan', 'kegiatan', 'Foto-foto kegiatan dan event Hartono Motor', 5, '2025-05-02 04:26:33', '2025-05-02 04:26:33');

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
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_05_02_065944_create_bookings_table', 1),
(5, '2025_05_02_072606_create_services_table', 1),
(6, '2025_05_02_073618_create_service_updates_table', 1),
(7, '2025_05_02_101542_create_promos_table', 1),
(8, '2025_05_02_111643_create_gallery_categories_table', 1),
(9, '2025_05_02_111712_create_galleries_table', 1),
(10, '2025_05_02_125042_create_blog_categories_table', 2),
(11, '2025_05_02_125049_create_blog_posts_table', 2),
(12, '2025_05_02_125057_create_blog_tags_table', 2),
(13, '2025_05_02_125105_create_blog_post_tag_table', 2),
(14, '2025_05_03_145314_create_customers_table', 3),
(15, '2025_05_03_150000_add_customer_id_to_services_table', 3),
(16, '2025_05_10_000001_add_slug_to_galleries_table', 3),
(17, '2025_05_10_000002_add_slug_to_promos_table', 3);

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
-- Struktur dari tabel `promos`
--

CREATE TABLE `promos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `promo_price` decimal(10,2) DEFAULT NULL,
  `discount_percentage` int(11) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `promo_code` varchar(255) DEFAULT NULL,
  `remaining_slots` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `promos`
--

INSERT INTO `promos` (`id`, `title`, `slug`, `description`, `image_path`, `original_price`, `promo_price`, `discount_percentage`, `start_date`, `end_date`, `is_featured`, `is_active`, `promo_code`, `remaining_slots`, `created_at`, `updated_at`) VALUES
(1, 'gata', 'gata', 'gagag', NULL, 12321.00, 0.00, 100, '2025-04-30 20:15:17', '2025-05-16 20:15:20', 1, 1, 'asdsa', 13, '2025-05-02 06:15:48', '2025-05-03 08:18:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `car_model` varchar(255) NOT NULL,
  `license_plate` varchar(255) DEFAULT NULL,
  `service_type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `parts_used` text DEFAULT NULL,
  `labor_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `parts_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('in_progress','completed','cancelled') NOT NULL DEFAULT 'in_progress',
  `notes` text DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `services`
--

INSERT INTO `services` (`id`, `booking_id`, `customer_id`, `customer_name`, `phone`, `car_model`, `license_plate`, `service_type`, `description`, `parts_used`, `labor_cost`, `parts_cost`, `total_cost`, `status`, `notes`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'Gilberto Lumoindong', '085290816081', 'Lambo Revuelto', 'W 1 L', 'Tune Up Mesin', 'gatau', 'gratis dah', 0.00, 0.00, 0.00, 'completed', NULL, '2025-05-03 14:49:25', '2025-05-03 07:34:54', '2025-05-03 07:49:25'),
(2, NULL, NULL, 'Roosevelt', '082230026711', 'Xpander Ultimate', 'L oek 123', 'Lainnya', 'Rusak Body Mobil Bagian Kanan', 'Pintu Baru XPANDER', 0.00, 0.00, 0.00, 'in_progress', NULL, NULL, '2025-05-03 08:48:12', '2025-05-03 08:48:12'),
(3, NULL, NULL, 'Yonatabn', '085290816081', 'Lambo Revuelto', 'W 1 L', 'Servis AC', NULL, NULL, 0.00, 0.00, 0.00, 'in_progress', NULL, NULL, '2025-05-03 08:54:13', '2025-05-03 08:54:13'),
(4, NULL, NULL, 'Hartono Sutanto', '08529132123', 'asd', NULL, 'Tune Up Mesin', 'asdas', NULL, 0.00, 0.00, 0.00, 'in_progress', NULL, NULL, '2025-05-03 08:55:07', '2025-05-03 08:55:07'),
(5, NULL, 1, 'test 1', '0852913212323', 'test 1', NULL, 'Servis AC', NULL, NULL, 0.00, 0.00, 0.00, 'in_progress', NULL, NULL, '2025-05-03 09:00:19', '2025-05-03 09:00:19'),
(6, NULL, 2, 'Bukan', '085290816081', 'Lambo Revuelto', 'WQ 91021 XE', 'Servis Berkala', 'Ntah', NULL, 0.00, 0.00, 0.00, 'in_progress', 'Kok ga jelas orang e', NULL, '2025-05-03 09:12:20', '2025-05-03 09:12:20'),
(7, NULL, 1, 'test 1', '0852913212323', 'asd', 'WQ 91021 XE', 'Servis AC', 'adasdsa', NULL, 0.00, 0.00, 0.00, 'in_progress', NULL, NULL, '2025-05-03 09:15:21', '2025-05-03 09:15:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `service_updates`
--

CREATE TABLE `service_updates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `update_type` enum('inspection','in_progress','parts_replaced','testing','completed','other') NOT NULL DEFAULT 'other',
  `sent_to_customer` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `service_updates`
--

INSERT INTO `service_updates` (`id`, `service_id`, `title`, `description`, `image_path`, `update_type`, `sent_to_customer`, `sent_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Inspeksi visual', 'Kami telah melakukan inspeksi awal pada kendaraan Anda. Inspeksi visual', 'service-updates/01JTB95Z2EE95E2VXDCJKYTAFK.png', 'inspection', 1, '2025-05-03 07:35:37', '2025-05-03 07:35:37', '2025-05-03 07:35:37'),
(2, 2, 'Ga bisa dIperbaiki', 'maaf ya bwang', 'service-updates/01JTBDCAD3X34TV29SDE5FVXS7.png', 'testing', 1, '2025-05-03 08:48:59', '2025-05-03 08:48:59', '2025-05-03 08:48:59');

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
('1B0g8sKLyUP31CNii6cpSZUdSQoKVOhsCXOgX1MF', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoieWwzSWwxWnpsYWIxZ0I0SVZEUzZ0bE5MSGhDbFN6enZQQ3RWaE9INiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRqMHh3U2MzNDB6Z0VjVXVOTUNlSGV1d2ppOEFQbUZZWWI3NmNSRnBZUVMvR2RQZmhrdjE2eSI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==', 1746289892),
('cfeuAXI2TGPwlc1IPJeek9D4AQ06Pq5qXPZCXyXI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTlNFSXNwQmNUc2Y0UllyN0JWZ3VzVnlMcVY5WUtKeW8zUGgxdThKaiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2FkbWluL2N1c3RvbWVyLWRhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMzOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYWRtaW4vbG9naW4iO319', 1746328223),
('N9qznckplGA6h48z8oKjHb0aka4N21oGcOVzZmFv', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiNk9CRkE0TUNFWjNXaGRmcG9iRGlKOEpsMTVtODBGZXppUmU5QkVPMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9jdXN0b21lci1kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjI3OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYWRtaW4iO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkajB4d1NjMzQwemdFY1V1Tk1DZUhldXdqaThBUG1GWVliNzZjUkZwWVFTL0dkUGZoa3YxNnkiO3M6ODoiZmlsYW1lbnQiO2E6MDp7fX0=', 1746292921);

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
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@hartonomotor.com', NULL, '$2y$12$j0xwSc340zgEcUuNMCeHeuwji8APmFYYb76cRFpYQS/GdPfhkv16y', 'YsqFqyze7iz1x39m0DAKEo2PaxpKhFDXQMRL0FUivA3n2gFqsm1XxU5Xy6sU', '2025-05-02 04:31:24', '2025-05-02 04:31:24');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_categories_slug_unique` (`slug`);

--
-- Indeks untuk tabel `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_posts_slug_unique` (`slug`),
  ADD KEY `blog_posts_category_id_foreign` (`category_id`),
  ADD KEY `blog_posts_author_id_foreign` (`author_id`);

--
-- Indeks untuk tabel `blog_post_tag`
--
ALTER TABLE `blog_post_tag`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_post_tag_blog_post_id_blog_tag_id_unique` (`blog_post_id`,`blog_tag_id`),
  ADD KEY `blog_post_tag_blog_tag_id_foreign` (`blog_tag_id`);

--
-- Indeks untuk tabel `blog_tags`
--
ALTER TABLE `blog_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_tags_slug_unique` (`slug`);

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_phone_unique` (`phone`),
  ADD UNIQUE KEY `customers_email_unique` (`email`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `galleries_category_id_foreign` (`category_id`);

--
-- Indeks untuk tabel `gallery_categories`
--
ALTER TABLE `gallery_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gallery_categories_slug_unique` (`slug`);

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
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `promos`
--
ALTER TABLE `promos`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_booking_id_foreign` (`booking_id`),
  ADD KEY `services_customer_id_foreign` (`customer_id`);

--
-- Indeks untuk tabel `service_updates`
--
ALTER TABLE `service_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_updates_service_id_foreign` (`service_id`);

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
-- AUTO_INCREMENT untuk tabel `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `blog_post_tag`
--
ALTER TABLE `blog_post_tag`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `blog_tags`
--
ALTER TABLE `blog_tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gallery_categories`
--
ALTER TABLE `gallery_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `promos`
--
ALTER TABLE `promos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `service_updates`
--
ALTER TABLE `service_updates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_posts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `blog_post_tag`
--
ALTER TABLE `blog_post_tag`
  ADD CONSTRAINT `blog_post_tag_blog_post_id_foreign` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_post_tag_blog_tag_id_foreign` FOREIGN KEY (`blog_tag_id`) REFERENCES `blog_tags` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `galleries`
--
ALTER TABLE `galleries`
  ADD CONSTRAINT `galleries_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `gallery_categories` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `services_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `service_updates`
--
ALTER TABLE `service_updates`
  ADD CONSTRAINT `service_updates_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
