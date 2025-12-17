-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi server:                 8.0.30 - MySQL Community Server - GPL
-- OS Server:                    Win64
-- HeidiSQL Versi:               12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Membuang struktur basisdata untuk presensi-geolocation
CREATE DATABASE IF NOT EXISTS `presensi-geolocation` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `presensi-geolocation`;

-- membuang struktur untuk table presensi-geolocation.departemen
CREATE TABLE IF NOT EXISTS `departemen` (
  `kode_dept` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dept` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`kode_dept`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.departemen: ~13 rows (lebih kurang)
DELETE FROM `departemen`;
INSERT INTO `departemen` (`kode_dept`, `nama_dept`) VALUES
	('AKN', 'Akutansi'),
	('ANG', 'Anggaran'),
	('HKM', 'Hukum'),
	('HUM', 'Humas'),
	('KEU', 'Keuangan'),
	('LTS', 'Layanan Transaksi'),
	('PEM', 'Pemeliharaan'),
	('PGD', 'Pengadaan'),
	('PLL', 'Pelayanan Lalu Lintas'),
	('PTI', 'Pengawasan Transaksi & TI'),
	('SDM', 'Sumber Daya Manusia'),
	('THS', 'Teknik & HSE'),
	('UMM', 'UMUM');

-- membuang struktur untuk table presensi-geolocation.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.failed_jobs: ~0 rows (lebih kurang)
DELETE FROM `failed_jobs`;

-- membuang struktur untuk table presensi-geolocation.jam_kerja
CREATE TABLE IF NOT EXISTS `jam_kerja` (
  `kode_jam_kerja` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_jam_kerja` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `awal_jam_masuk` time NOT NULL,
  `jam_masuk` time NOT NULL,
  `akhir_jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  PRIMARY KEY (`kode_jam_kerja` DESC) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.jam_kerja: ~2 rows (lebih kurang)
DELETE FROM `jam_kerja`;
INSERT INTO `jam_kerja` (`kode_jam_kerja`, `nama_jam_kerja`, `awal_jam_masuk`, `jam_masuk`, `akhir_jam_masuk`, `jam_pulang`) VALUES
	('JK02', 'Shift Malam', '16:30:00', '17:00:00', '18:00:00', '22:00:00'),
	('JK01', 'Reguler', '07:30:00', '08:00:00', '09:00:00', '17:00:00');

-- membuang struktur untuk table presensi-geolocation.karyawan
CREATE TABLE IF NOT EXISTS `karyawan` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_dept` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_jam_kerja` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.karyawan: ~9 rows (lebih kurang)
DELETE FROM `karyawan`;
INSERT INTO `karyawan` (`email`, `nama_lengkap`, `jabatan`, `no_hp`, `foto`, `kode_dept`, `password`, `remember_token`, `kode_jam_kerja`) VALUES
	('andrirusmandani@gmail.com', 'Andri Rusmandani', 'Pengadaan', '081214447503', NULL, 'PGD', '$2y$12$vukkYUakl5zGrbwYF4/.EuZR5dR.Zq4Tsahrk3uAKasB3tyagf2yu', NULL, NULL),
	('daffa@gmail.com', 'Daffa Rahmandani', 'Pengawasan Transaksi & TI', '085257172793', 'daffa_gmail_com.jpg', 'PTI', '$2y$12$FIDsSZnQdtiOANBtJ/fiGuqT3wwku6G4MOcr1wfrAkVI6viVxEIB2', NULL, NULL),
	('ervina@gmail.com', 'Ervina', 'Keuangan', '081282456560', NULL, 'KEU', '$2y$12$TeQQtcjydPCyuFKoNEvE3u1lvOWA2WtoTPmkRAlZtnf0ewoTXMiM.', NULL, NULL),
	('fairuz@gmail.com', 'Fairuz Pramandani', 'IT', '081259813952', 'fairuz_gmail_com.png', 'PTI', '$2y$12$6Npt2yF9NA8RiL5tSxlrpewuRuQHoCfNVvE8iEY.tifWYHEUVfL/O', NULL, 'JK02'),
	('fara@gmail.com', 'faradiva', 'Akutansi', '081381361', NULL, 'AKN', '$2y$12$DVUNhZ9vXt1KR0cnawcNqOpwmb5huwrdfQPRVhNXnSR3LEp0L2hKW', NULL, NULL),
	('firman@gmail.com', 'Firman', 'Pengawasan Transaksi & TI', '082372953510', 'firman_gmail_com.jpg', 'PTI', '$2y$12$cBFzT08afpQ9TP0Z4A7JLeCRK4MEa4ZjYdS.DpJioDSuei4qixcqa', NULL, NULL),
	('pujo@gmail.com', 'pujianto', 'Humas', '088989819606', NULL, 'HMS', '$2y$12$qO7MKBNS4KJW9Ju6dP8JWOTxfiHbSvwRcDw8RVavEQvYwneRMBx0W', NULL, NULL),
	('ricky@gmail.com', 'Ricky Ardiansya', 'Sumber Daya Manusia', '088989819606', 'ricky_gmail_com.jpg', 'SDM', '$2y$12$OMf5bGh36i5ZPBG7274VYuIoZGGGmc./vHjYKI26ipKfSB0zYltZC', NULL, NULL),
	('viraaulidiyasukma@gmail.com', 'Vira Aulidiya Sukma', 'Keuangan', '081252240490', 'viraaulidiyasukma_gmail_com.png', 'KEU', '$2y$12$UrWbH5x2WzNndHrZLPJNEO4HkQXyDkj.eQoRMnEfC2qOjJwc/Aj2q', NULL, NULL);

-- membuang struktur untuk table presensi-geolocation.konfigurasi_lokasi
CREATE TABLE IF NOT EXISTS `konfigurasi_lokasi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lokasi_kantor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `radius` smallint NOT NULL,
  `nama_lokasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.konfigurasi_lokasi: ~9 rows (lebih kurang)
DELETE FROM `konfigurasi_lokasi`;
INSERT INTO `konfigurasi_lokasi` (`id`, `lokasi_kantor`, `radius`, `nama_lokasi`) VALUES
	(2, '-7.344679449869948, 112.73472526289694', 100, 'Gerbang Tol Menanggal'),
	(3, '-7.343896355960009, 112.73525385237829', 100, 'PT CMS'),
	(5, '-7.3470567753921845, 112.78926810447702', 100, 'Gerbang Tol TambakSumur 1'),
	(7, '-7.346229427986888, 112.78391129687654', 100, 'Gerbang Tol TambakSumur 2'),
	(8, '-7.342710270634166, 112.75812544774416', 100, 'Gerbang Tol Berbek 1'),
	(9, '-7.343178996660292, 112.75237635443398', 100, 'Gerbang Tol Berbek 2'),
	(10, '-7.357726813064598, 112.80496911781243', 100, 'Gerbang Tol Juanda'),
	(11, '-7.497382601382557, 112.72027988527945', 100, 'Home'),
	(12, '-7.271023552592832, 112.74449834834134', 100, 'Test Lokasi');

-- membuang struktur untuk table presensi-geolocation.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.migrations: ~5 rows (lebih kurang)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2025_11_18_152908_update_sanctum_tokenable_to_string', 2);

-- membuang struktur untuk table presensi-geolocation.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.password_reset_tokens: ~1 rows (lebih kurang)
DELETE FROM `password_reset_tokens`;
INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
	('fairuz@gmail.com', '$2y$12$K116KGCgdKagfuFBNK0t0OU.rAM0fcjseiQqjMPhNzxSlQRR1h0T6', '2025-11-15 05:20:18');

-- membuang struktur untuk table presensi-geolocation.pengajuan_izin
CREATE TABLE IF NOT EXISTS `pengajuan_izin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tgl_izin` date DEFAULT NULL,
  `status` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'i : izin s : sakit',
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approved` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '0 : Pending 1: Disetujui 2: Ditolak',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.pengajuan_izin: ~9 rows (lebih kurang)
DELETE FROM `pengajuan_izin`;
INSERT INTO `pengajuan_izin` (`id`, `email`, `tgl_izin`, `status`, `keterangan`, `status_approved`) VALUES
	(1, 'fairuz@gmail.com', '2025-11-12', 'izin', 'rumah', '2'),
	(2, 'fairuz@gmail.com', '2025-11-14', 'izin', 'Pulang Kampung', '0'),
	(3, 'ricky@gmail.com', '2025-11-14', 'sakit', 'Ambeyen', '2'),
	(4, 'daffa@gmail.com', '2025-11-17', 'Izin', 'Keluarga kecelakaan', '1'),
	(5, 'firman@gmail.com', '2025-11-11', 'Izin', 'Pulang', '2'),
	(6, 'viraaulidiyasukma@gmail.com', '2025-11-11', 'Izin', 'Rumah Sakit', '1'),
	(7, 'andrirusmandani@gmail.com', '2025-11-17', 'Sakit', 'gigi', '0'),
	(8, 'fairuz@gmail.com', '2025-11-11', 'Sakit', 'Demam', '1'),
	(9, 'fairuz@gmail.com', '2025-11-17', 'i', 'Visit', '0');

-- membuang struktur untuk table presensi-geolocation.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`) USING BTREE,
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.personal_access_tokens: ~31 rows (lebih kurang)
DELETE FROM `personal_access_tokens`;
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', 'd4011429c475b83185d451271661b4c3a6dcd05768b7c2e6ae026322a3dc10c9', '["*"]', '2025-11-18 08:46:17', NULL, '2025-11-18 08:36:27', '2025-11-18 08:46:17'),
	(2, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '2e51b3da7707a152de91bb28515e0bbb69381a5737d619b499100c0c3f919388', '["*"]', '2025-11-18 09:30:48', NULL, '2025-11-18 09:07:24', '2025-11-18 09:30:48'),
	(3, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '6ee7da0019677e78dfa859d2d5dd79c5f3a47498a5ddf4eee8f79e79ca6d7d51', '["*"]', '2025-11-18 09:31:23', NULL, '2025-11-18 09:31:22', '2025-11-18 09:31:23'),
	(4, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', 'a008870ed6950b12a90dd8873dca548fae84f100f5ec9330b1ecab3d44e4dcdf', '["*"]', '2025-11-18 09:38:12', NULL, '2025-11-18 09:33:31', '2025-11-18 09:38:12'),
	(5, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '4a24757cbeeae5441a4018ce6bf937a346db43762a2165a976b86f28d210baf5', '["*"]', '2025-11-18 09:40:49', NULL, '2025-11-18 09:38:28', '2025-11-18 09:40:49'),
	(6, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', 'dbfbcf7b29e8abe94ed6c81c994e57c1590e0529deffbed99900936808e33d8d', '["*"]', '2025-11-18 09:43:15', NULL, '2025-11-18 09:42:46', '2025-11-18 09:43:15'),
	(7, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', 'bb99cf8fa601d48ab8638c7ea0c00c0989b8cbeda24fe3628ffd429187455b65', '["*"]', '2025-11-18 09:45:14', NULL, '2025-11-18 09:44:43', '2025-11-18 09:45:14'),
	(8, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', 'd0a82dfd5cf005d0cee7be6dcd84c6e17fe7dbdfd6a8c01856af6e4efc78032d', '["*"]', '2025-11-18 09:46:00', NULL, '2025-11-18 09:45:23', '2025-11-18 09:46:00'),
	(9, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', 'dbdb1b24829240c6b2309c0596201b59172aee1abbd2dd1989a12dd41ee59bc7', '["*"]', '2025-11-18 09:46:22', NULL, '2025-11-18 09:46:21', '2025-11-18 09:46:22'),
	(10, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '48012d11fd1327ea30190e60ac4b471186ea295da46a9a6636473c6e2a4ccf90', '["*"]', '2025-11-18 09:52:02', NULL, '2025-11-18 09:47:56', '2025-11-18 09:52:02'),
	(11, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '5ae5ee17b8c5161a0664b9a5b1d4606984459de553cf98edf83ba879f357dc93', '["*"]', '2025-11-18 09:56:04', NULL, '2025-11-18 09:53:29', '2025-11-18 09:56:04'),
	(12, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '4fee6aaf3d6ef344e14dcd3949c04c5eb1fb5afde40f31fa868ec5041133f45b', '["*"]', '2025-11-18 10:00:46', NULL, '2025-11-18 10:00:04', '2025-11-18 10:00:46'),
	(13, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '0b34e03da4951502e1a20762d8d02769b35223b6a86adbbc0d9ee488fd7eeec4', '["*"]', '2025-11-18 10:08:50', NULL, '2025-11-18 10:06:28', '2025-11-18 10:08:50'),
	(14, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '4d9bd48e84c8d49b001d46afe22db0ed7b662d08b35d561048551cb1bec331de', '["*"]', '2025-11-18 10:45:45', NULL, '2025-11-18 10:21:25', '2025-11-18 10:45:45'),
	(15, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '51257bc38d01de30ec302b53035070f3a941043a8a3ea12155f7e7ee8d7077fa', '["*"]', '2025-11-18 10:45:45', NULL, '2025-11-18 10:45:04', '2025-11-18 10:45:45'),
	(16, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', 'b6b59e681f2f01f8493b3e4479b3a49420cb3d443d67f020a1a18d4506bfccbc', '["*"]', '2025-11-18 10:50:45', NULL, '2025-11-18 10:50:44', '2025-11-18 10:50:45'),
	(17, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '5af6aca939a2abd2dc5689a55888843d32c4f29f1c18165df6a2940ded77fbf5', '["*"]', '2025-11-18 14:56:59', NULL, '2025-11-18 10:54:41', '2025-11-18 14:56:59'),
	(18, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '143ffbafe63db5cb2a85c53b121a21700a91d9dcb8bc44ab62c03ff88ae3f921', '["*"]', '2025-11-18 14:58:06', NULL, '2025-11-18 14:58:02', '2025-11-18 14:58:06'),
	(19, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '280f01274a0eebaac8e0a541f04cf6d4384dcb0981ac47c65cc5cf17c78ddab7', '["*"]', '2025-11-18 17:04:30', NULL, '2025-11-18 14:58:25', '2025-11-18 17:04:30'),
	(20, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '7ab0d8cbca611747542b37f1157df65d3652f3adc7f29f701e218ef004408354', '["*"]', '2025-11-18 17:51:45', NULL, '2025-11-18 17:09:38', '2025-11-18 17:51:45'),
	(21, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '722ad46f80014b8c08980326b8f003192287ca4721adbddbe83dbd5b6ee9ea4e', '["*"]', '2025-11-18 17:58:22', NULL, '2025-11-18 17:52:48', '2025-11-18 17:58:22'),
	(22, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '59d6ee6c8360704949eecc1b3c4753e28088218c91af7f6d717321882dcd769d', '["*"]', '2025-11-18 18:03:54', NULL, '2025-11-18 17:59:47', '2025-11-18 18:03:54'),
	(23, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', '3ff226a9abcde58edc03fcdf3d523d92fb55d5a402e7f605633ceb71858b6d0b', '["*"]', '2025-11-18 18:10:21', NULL, '2025-11-18 18:10:13', '2025-11-18 18:10:21'),
	(24, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'authToken', 'e2e200728ca561c3bfc56d80b443012f82ac6ee9187e16681e54b22aa6aab9e7', '["*"]', '2025-11-18 18:12:55', NULL, '2025-11-18 18:12:54', '2025-11-18 18:12:55'),
	(25, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'f0db236fe6cfd100d862f560536187620f9bd9aed8c9d5f7ffba84c8a11450a3', '["*"]', NULL, NULL, '2025-12-15 11:26:16', '2025-12-15 11:26:16'),
	(26, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '345d459b751a9fd9e4e767b3f3fcd11c43420161fe22c35746567affa8148aae', '["*"]', NULL, NULL, '2025-12-15 20:07:13', '2025-12-15 20:07:13'),
	(27, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'fca2180af488a21528316e6064f13ce5cc099208c79ee2df728985b45ee2f2b1', '["*"]', NULL, NULL, '2025-12-15 20:10:49', '2025-12-15 20:10:49'),
	(28, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '2cea4b43f4a3c38c7619f8a00edcd9ad09d3fa787705e3350abd7a36ac8d22af', '["*"]', NULL, NULL, '2025-12-15 20:10:58', '2025-12-15 20:10:58'),
	(29, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'c3f07ab8429abb4c22d02a6963f046f1a0116b4546ac70d3316e868e552a0ba3', '["*"]', NULL, NULL, '2025-12-15 20:11:02', '2025-12-15 20:11:02'),
	(30, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '8a933932d5921a0f08ec708a3f6d1e701367d8fa29f4971372783f5ed0ec0210', '["*"]', NULL, NULL, '2025-12-15 20:14:14', '2025-12-15 20:14:14'),
	(31, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '053d607c2e8f4efbacd81db5a7e628c5617c15158dcb0c05187abfd87303a93b', '["*"]', NULL, NULL, '2025-12-15 20:20:37', '2025-12-15 20:20:37');

-- membuang struktur untuk table presensi-geolocation.presensi
CREATE TABLE IF NOT EXISTS `presensi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `tgl_presensi` date NOT NULL,
  `jam_in` time NOT NULL,
  `jam_out` time DEFAULT NULL,
  `foto_in` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_out` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_in` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `location_out` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.presensi: ~8 rows (lebih kurang)
DELETE FROM `presensi`;
INSERT INTO `presensi` (`id`, `email`, `tgl_presensi`, `jam_in`, `jam_out`, `foto_in`, `foto_out`, `location_in`, `location_out`) VALUES
	(22, 'fairuz@gmail.com', '2025-11-14', '17:53:20', '17:53:47', 'fairuz@gmail.com-2025-11-05-in.png', 'fairuz@gmail.com-2025-11-05-out.png', '-7.3039872,112.7645184', '-7.3039872,112.7645184'),
	(23, 'fairuz@gmail.com', '2025-11-06', '00:07:30', '00:07:43', 'fairuz@gmail.com-2025-11-06-in.png', 'fairuz@gmail.com-2025-11-06-out.png', '-7.4975402,112.7203576', '-7.4975402,112.7203576'),
	(25, 'fairuz@gmail.com', '2025-11-09', '03:38:53', '03:41:44', 'fairuz@gmail.com-2025-11-07-in.png', 'fairuz@gmail.com-2025-11-07-out.png', '-7.3105408,112.738304', '-7.3105408,112.738304'),
	(26, 'fairuz@gmail.com', '2025-11-10', '14:08:42', '14:25:39', 'fairuz@gmail.com-2025-11-10-in.png', 'fairuz@gmail.com-2025-11-10-out.png', '-7.2843264,112.7514112', '-7.2843264,112.7514112'),
	(27, 'fairuz@gmail.com', '2025-11-11', '00:32:51', '00:33:01', 'fairuz@gmail.com-2025-11-11-in.png', 'fairuz@gmail.com-2025-11-11-out.png', '-7.2638,112.7374', '-7.2638,112.7374'),
	(28, 'fairuz@gmail.com', '2025-11-12', '08:20:47', '20:49:28', 'fairuz@gmail.com-2025-11-12-in.png', 'fairuz@gmail.com-2025-11-12-out.png', '-7.2709,112.7446', '-7.2709,112.7446'),
	(30, 'fairuz@gmail.com', '2025-11-13', '18:19:18', '18:20:04', 'fairuz@gmail.com-2025-11-13-in.png', 'fairuz@gmail.com-2025-11-13-out.png', '-7.3203712,112.738304', '-7.3203712,112.738304'),
	(34, 'fairuz@gmail.com', '2025-11-24', '00:36:24', NULL, 'fairuz@gmail.com-2025-11-24-in.png', NULL, '-7.3039872,112.738304', NULL);

-- membuang struktur untuk table presensi-geolocation.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_email_unique` (`email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.users: ~1 rows (lebih kurang)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Fairuz Pramandani', 'fairuzpramandani5@gmail.com', NULL, '$2y$12$wqxdjwvDClTl/ZXf2gFUbuTxS5fXPcg.AVPI2rAfSYgTLMUyvCsMm', NULL, NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
