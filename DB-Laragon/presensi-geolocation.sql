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

-- membuang struktur untuk table presensi-geolocation.audit_logs
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email_login` varchar(255) DEFAULT NULL,
  `tipe_kecurangan` varchar(50) DEFAULT 'Face_Mismatch',
  `pesan_log` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Membuang data untuk tabel presensi-geolocation.audit_logs: ~7 rows (lebih kurang)
DELETE FROM `audit_logs`;
INSERT INTO `audit_logs` (`id`, `email_login`, `tipe_kecurangan`, `pesan_log`, `created_at`, `updated_at`) VALUES
	(1, 'fairuz@gmail.com', 'Fake_GPS', 'User mencoba absen di koordinat (-7.490844905457351, 112.72614093746937) yang berada di luar radius kantor.', '2026-04-06 09:53:49', '2026-04-06 09:53:49'),
	(2, 'fairuz@gmail.com', 'Fake_GPS', 'Tuyul GPS terdeteksi saat buka peta.', '2026-04-08 12:38:37', NULL),
	(3, 'fairuz@gmail.com', 'Fake_GPS', 'Tuyul GPS terdeteksi saat buka peta.', '2026-04-11 05:43:34', NULL),
	(4, 'fairuz@gmail.com', 'Fake_GPS', 'Mencoba Fake GPS saat Mode Pesawat (Offline).', '2026-04-11 07:22:18', NULL),
	(5, 'fairuz@gmail.com', 'Fake_GPS', 'Mencoba Fake GPS saat Mode Pesawat (Offline).', '2026-04-11 07:22:19', NULL),
	(6, 'fairuz@gmail.com', 'Fake_GPS', 'Tuyul GPS terdeteksi (Online).', '2026-05-08 04:42:28', NULL),
	(7, 'fairuz@gmail.com', 'Fake_GPS', 'Tuyul GPS terdeteksi (Online).', '2026-05-08 04:44:00', NULL),
	(8, 'fairuzpram5@gmail.com', 'Fake_GPS', 'Tuyul GPS terdeteksi (Online).', '2026-05-12 04:38:58', NULL),
	(9, 'fairuzpramandani5@gmail.com', 'Fake_GPS', 'Tuyul GPS terdeteksi (Online).', '2026-05-22 09:05:59', NULL);

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
  `foto_wajah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `face_embedding` text COLLATE utf8mb4_unicode_ci,
  `kode_dept` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_jam_kerja` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.karyawan: ~6 rows (lebih kurang)
DELETE FROM `karyawan`;
INSERT INTO `karyawan` (`email`, `nama_lengkap`, `jabatan`, `no_hp`, `foto`, `foto_wajah`, `face_embedding`, `kode_dept`, `password`, `remember_token`, `kode_jam_kerja`, `created_at`, `updated_at`) VALUES
	('andrirusmandani@gmail.com', 'Andri Rusmandani', 'Pengadaan', '081214447503', NULL, NULL, NULL, 'PGD', '$2y$12$vukkYUakl5zGrbwYF4/.EuZR5dR.Zq4Tsahrk3uAKasB3tyagf2yu', NULL, NULL, NULL, NULL),
	('ervina@gmail.com', 'Ervina', 'Keuangan', '081282456560', NULL, NULL, NULL, 'KEU', '$2y$12$TeQQtcjydPCyuFKoNEvE3u1lvOWA2WtoTPmkRAlZtnf0ewoTXMiM.', NULL, NULL, NULL, NULL),
	('fairuz@gmail.com', 'Fairuz Pramandani', 'IT', '081259813952', 'fairuz_gmail_com_1775451942.jpg', 'fairuz_gmail_com_validasi.jpg', '[-0.1099357157945633,0.027564283460378647,0.07318060100078583,-0.059778157621622086,-0.07602322846651077,-0.05912836268544197,-0.019616633653640747,-0.13248910009860992,0.12583968043327332,-0.13124889135360718,0.2774398624897003,-0.021595466881990433,-0.23531068861484528,-0.1295742243528366,0.1086491197347641,0.10973015427589417,-0.10285960137844086,-0.12619726359844208,-0.06569300591945648,-0.044099412858486176,0.047504350543022156,-0.040230292826890945,0.025469474494457245,0.028710052371025085,-0.13707156479358673,-0.38368698954582214,-0.07896946370601654,-0.08759760111570358,0.008257344365119934,-0.034299686551094055,-0.0036109071224927902,0.01917184144258499,-0.24674072861671448,-0.08748658746480942,0.014768733642995358,0.11367490887641907,-0.009235600009560585,-0.017280567437410355,0.12724140286445618,-0.02002570778131485,-0.22152748703956604,-0.05206037312746048,0.010189106687903404,0.15001705288887024,0.10106172412633896,0.08676266670227051,0.008388700895011425,-0.025601178407669067,0.060323361307382584,-0.2516815662384033,0.09954730421304703,0.05949809402227402,0.08811797946691513,0.01645287312567234,0.04906376451253891,-0.17531566321849823,0.011042519472539425,0.08920507878065109,-0.21818070113658905,0.038031820207834244,0.03268224000930786,-0.0802379846572876,-0.09723706543445587,-0.04058416560292244,0.29614540934562683,0.17080479860305786,-0.1509842425584793,-0.09094860404729843,0.1283409297466278,-0.12334102392196655,-0.06154271587729454,0.03181681036949158,-0.11328008025884628,-0.16540826857089996,-0.3283209502696991,0.06402206420898438,0.39306437969207764,0.10594234615564346,-0.25081667304039,-0.01866655796766281,-0.07930873334407806,0.043767839670181274,0.083961121737957,0.09421622008085251,-0.019690344110131264,0.08901578187942505,-0.059637781232595444,-0.00035900436341762543,0.13759547472000122,-0.004693005234003067,-0.0646778866648674,0.2816462218761444,-0.044950082898139954,0.13947810232639313,0.04385601356625557,0.014730296097695827,-0.051178909838199615,-0.08200327306985855,-0.14287793636322021,-0.0704100951552391,0.10971684753894806,-0.0036143604665994644,0.001423591747879982,0.1306053251028061,-0.18088223040103912,0.1407119482755661,-0.012909656390547752,0.017037954181432724,0.06365393102169037,0.05227748677134514,-0.07650399208068848,-0.11667606234550476,0.07408494502305984,-0.2572984993457794,0.2102411389350891,0.2072906345129013,0.021929733455181122,0.14072498679161072,0.10041524469852448,0.0006285971030592918,0.04031423479318619,-0.012796719558537006,-0.19886834919452667,-0.05653173848986626,0.08701097965240479,-0.05606203153729439,0.06231622025370598,0.01920820027589798]', 'PTI', '$2y$12$L7FkjRntZ2efG2G/zD1SDusC8IbCmldjSkVsApeunxW6F693SuLnm', '1c8dbd8a734d7e272a40d58d838ad2495bb5987f1159055336ca384efe424faa0041abe00f95c6f8', 'JK01', NULL, '2026-01-13 06:00:38'),
	('fairuzpramandani5@gmail.com', 'fairuz pramandani', 'IT', '081259813952', NULL, 'fairuzpramandani5_gmail_com_validasi.jpg', '[-0.1300942450761795,0.027104977518320084,0.022925011813640594,-0.03438791632652283,-0.07389190793037415,-0.07215225696563721,-0.02415047399699688,-0.14072492718696594,0.1488979160785675,-0.08483109623193741,0.258978009223938,-0.039053261280059814,-0.20083127915859222,-0.13571859896183014,0.10428066551685333,0.12074743211269379,-0.10869733989238739,-0.09174559265375137,-0.0838257446885109,-0.05746087804436684,0.029072199016809464,-0.054889556020498276,0.04997942969202995,0.03416884317994118,-0.12451543658971786,-0.3695424497127533,-0.0655503049492836,-0.0804317444562912,0.0471898578107357,-0.04206657037138939,-0.05473220348358154,0.042830146849155426,-0.23212915658950806,-0.12675978243350983,0.04296443238854408,0.15775218605995178,0.04162609577178955,0.015264498069882393,0.1376604586839676,0.01551498007029295,-0.21310663223266602,-0.017916057258844376,0.014348692260682583,0.19125083088874817,0.16090187430381775,0.0909213125705719,-0.00044916942715644836,-0.07086482644081116,0.12257274985313416,-0.20665313303470612,0.06981272995471954,0.05776943638920784,0.08893833309412003,-0.012940384447574615,0.04362059012055397,-0.1769602745771408,-0.0016146907582879066,0.0888211652636528,-0.18602219223976135,0.017502563074231148,0.06743521988391876,-0.07872762531042099,-0.11766674369573593,-0.07828119397163391,0.2781711518764496,0.14139631390571594,-0.1394079476594925,-0.11676168441772461,0.12011663615703583,-0.08944213390350342,-0.06030414626002312,0.020887019112706184,-0.12066297978162766,-0.14525383710861206,-0.35638508200645447,0.055560946464538574,0.4153745472431183,0.0868389680981636,-0.2863324284553528,-0.002321278676390648,-0.10013937205076218,0.03209920972585678,0.09505313634872437,0.10283675044775009,-0.007215876132249832,0.08070875704288483,-0.08821755647659302,-0.048987455666065216,0.14203085005283356,-0.01424634549766779,-0.030318094417452812,0.2202833890914917,-0.039635904133319855,0.15614303946495056,0.029302002862095833,0.03232787922024727,-0.13022834062576294,-0.05879547446966171,-0.18266482651233673,-0.1292186975479126,0.09871311485767365,-0.0185144804418087,0.030538689345121384,0.12113650143146515,-0.21975694596767426,0.17038556933403015,0.00783519446849823,0.022588377818465233,0.06524816155433655,0.03649206832051277,-0.03334180265665054,-0.07787996530532837,0.08537044376134872,-0.2532346248626709,0.20371927320957184,0.21199257671833038,0.03912771865725517,0.15085509419441223,0.11090350896120071,0.03633430600166321,0.011421476490795612,0.06891272962093353,-0.1809249073266983,-0.058293603360652924,0.035272300243377686,-0.04610363766551018,0.05336526781320572,0.03061816096305847]', 'PTI', '$2y$12$Y.BsUpxW1MuWqps/czGKFuKQuaKnQaqxi7z5mwQzi8nykmmmsr1JC', 'db28c449da6f3614d2964fef5f204044d15ed251b1441e331310fa93ed6ae3ebd6a1261b64cef301', 'JK01', NULL, NULL),
	('firman@gmail.com', 'Firman', 'Pengawasan Transaksi & TI', '082372953510', 'firman_gmail_com.jpg', 'firman_gmail_com_validasi.jpg', '[-0.09266200661659241,0.05847596377134323,0.03586289659142494,-0.07206610590219498,-0.042724646627902985,-0.0804382860660553,-0.03914455324411392,-0.14156727492809296,0.14617054164409637,-0.11150233447551727,0.281932532787323,-0.06598430126905441,-0.1957145631313324,-0.16949954628944397,0.07869595289230347,0.10698577761650085,-0.11499588936567307,-0.09965161234140396,-0.052427180111408234,-0.001233893446624279,0.052522242069244385,-0.04638374596834183,0.051061347126960754,0.07015451788902283,-0.10953918844461441,-0.3792833685874939,-0.11625320464372635,-0.08120499551296234,0.03314587473869324,-0.0416957288980484,-0.07839743793010712,0.015287220478057861,-0.1995055377483368,-0.08110138773918152,0.005084073171019554,0.09245739877223969,0.03771679103374481,-0.01766021177172661,0.13237278163433075,-0.03191668540239334,-0.22655873000621796,-0.010697371326386929,0.017086923122406006,0.1615440547466278,0.18344970047473907,0.09377454966306686,0.013326317071914673,-0.04975138232111931,0.07952581346035004,-0.22395998239517212,0.07874539494514465,0.08616863936185837,0.10377014428377151,0.0018355189822614193,0.041427746415138245,-0.1450110822916031,-0.010793143883347511,0.1462385654449463,-0.13371950387954712,0.02001817338168621,0.02345873787999153,-0.059567246586084366,-0.054914213716983795,-0.0073736729100346565,0.33271652460098267,0.15817300975322723,-0.15994125604629517,-0.08678986877202988,0.12530530989170074,-0.1349576711654663,-0.06794796884059906,0.024110643193125725,-0.11736534535884857,-0.15893442928791046,-0.2907245457172394,0.07544229924678802,0.36820122599601746,0.10544706135988235,-0.2887229323387146,0.03819938749074936,-0.13239821791648865,0.06923946738243103,0.10959849506616592,0.09588827937841415,-0.04691432788968086,0.04661596193909645,-0.11512792110443115,-0.07755870372056961,0.20026634633541107,-0.021898001432418823,0.003504307009279728,0.29706570506095886,-0.0273467767983675,0.14865034818649292,0.040028683841228485,-0.00993531197309494,-0.042810868471860886,-0.05085102468729019,-0.10956797003746033,-0.05557327717542648,0.12907250225543976,-0.030849676579236984,-0.017602184787392616,0.14116141200065613,-0.2499230057001114,0.1292625069618225,-0.005830477923154831,0.03649374470114708,0.11558062583208084,0.04485563933849335,-0.0030736210756003857,-0.116785928606987,0.07929988205432892,-0.27431365847587585,0.19728532433509827,0.1889798939228058,0.04191787540912628,0.1534654051065445,0.09594407677650452,0.061256758868694305,0.0431235246360302,-0.010266745463013649,-0.1588360071182251,-0.031860172748565674,0.10328686237335205,-0.06652607768774033,0.042436517775058746,0.018348587676882744]', 'PTI', '$2y$12$XIIqTvWkVZQ0EY52WM.MM.uaLOSVp17yb3VdtUqSmq8aolW0C/6ZG', 'b58e77e1a9a98d7cfc5e987330c9464fdbb074e1117c57a7b4f8e206df9b9ee9474d23e52d39cdd6', NULL, NULL, NULL),
	('viraaulidiyasukma@gmail.com', 'Vira Aulidiya Sukma', 'Keuangan', '081252240490', 'viraaulidiyasukma_gmail_com.png', NULL, NULL, 'KEU', '$2y$12$UrWbH5x2WzNndHrZLPJNEO4HkQXyDkj.eQoRMnEfC2qOjJwc/Aj2q', NULL, NULL, NULL, NULL);

-- membuang struktur untuk table presensi-geolocation.konfigurasi_lokasi
CREATE TABLE IF NOT EXISTS `konfigurasi_lokasi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lokasi_kantor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `radius` smallint NOT NULL,
  `nama_lokasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.konfigurasi_lokasi: ~7 rows (lebih kurang)
DELETE FROM `konfigurasi_lokasi`;
INSERT INTO `konfigurasi_lokasi` (`id`, `lokasi_kantor`, `radius`, `nama_lokasi`) VALUES
	(2, '-7.344679449869948, 112.73472526289694', 100, 'Gerbang Tol Menanggal'),
	(3, '-7.343896355960009, 112.73525385237829', 100, 'PT CMS'),
	(5, '-7.3470567753921845, 112.78926810447702', 100, 'Gerbang Tol TambakSumur 1'),
	(7, '-7.346229427986888, 112.78391129687654', 100, 'Gerbang Tol TambakSumur 2'),
	(8, '-7.342710270634166, 112.75812544774416', 100, 'Gerbang Tol Berbek 1'),
	(9, '-7.343178996660292, 112.75237635443398', 100, 'Gerbang Tol Berbek 2'),
	(10, '-7.357726813064598, 112.80496911781243', 100, 'Gerbang Tol Juanda'),
	(13, '-7.497379384583942, 112.72029058866063', 100, 'Kantor Home');

-- membuang struktur untuk table presensi-geolocation.log_kecurangan
CREATE TABLE IF NOT EXISTS `log_kecurangan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email_login` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pesan_kecurangan` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `waktu` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Membuang data untuk tabel presensi-geolocation.log_kecurangan: ~4 rows (lebih kurang)
DELETE FROM `log_kecurangan`;
INSERT INTO `log_kecurangan` (`id`, `email_login`, `pesan_kecurangan`, `waktu`) VALUES
	(1, 'fairuz@gmail.com', 'Peringatan! Akun Fairuz Pramandani mencoba absen menggunakan wajah yang Tidak Dikenal dalam sistem.', '2026-02-20 00:16:27'),
	(2, 'fairuz@gmail.com', 'Peringatan! Akun Fairuz Pramandani mencoba absen menggunakan wajah yang Tidak Dikenal dalam sistem.', '2026-02-20 00:20:05'),
	(3, 'fairuz@gmail.com', 'Tuyul GPS terdeteksi saat buka peta.', '2026-04-08 14:01:57'),
	(4, 'fairuz@gmail.com', 'Tuyul GPS terdeteksi saat buka peta.', '2026-04-08 14:02:27'),
	(5, 'fairuz@gmail.com', 'Tuyul GPS terdeteksi saat buka peta.', '2026-04-08 19:19:21');

-- membuang struktur untuk table presensi-geolocation.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.migrations: ~6 rows (lebih kurang)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2025_11_18_152908_update_sanctum_tokenable_to_string', 2),
	(6, '2025_12_19_223003_add_timestamps_to_karyawan_table', 3);

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
  `bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approved` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '0 : Pending 1: Disetujui 2: Ditolak',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.pengajuan_izin: ~6 rows (lebih kurang)
DELETE FROM `pengajuan_izin`;
INSERT INTO `pengajuan_izin` (`id`, `email`, `tgl_izin`, `status`, `keterangan`, `bukti`, `status_approved`) VALUES
	(1, 'fairuz@gmail.com', '2025-11-12', 'izin', 'rumah', NULL, '2'),
	(2, 'fairuz@gmail.com', '2025-11-14', 'izin', 'Pulang Kampung', NULL, '0'),
	(3, 'ricky@gmail.com', '2025-11-14', 'sakit', 'Ambeyen', NULL, '2'),
	(4, 'daffa@gmail.com', '2025-11-17', 'Izin', 'Keluarga kecelakaan', NULL, '1'),
	(5, 'firman@gmail.com', '2025-11-11', 'Izin', 'Pulang', NULL, '2'),
	(6, 'viraaulidiyasukma@gmail.com', '2025-11-11', 'Izin', 'Rumah Sakit', NULL, '1'),
	(14, 'fairuz@gmail.com', '2026-04-14', 's', 'mual', '1775907815_fairuz_gmail_com.jpg', '1'),
	(15, 'pramandani@gmail.com', '2026-04-28', 's', 'mual', '1777215996_pramandani_gmail_com.png', '1'),
	(16, 'fairuzpram5@gmail.com', '2026-05-14', 'i', 'cuti', NULL, '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.personal_access_tokens: ~62 rows (lebih kurang)
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
	(31, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '053d607c2e8f4efbacd81db5a7e628c5617c15158dcb0c05187abfd87303a93b', '["*"]', NULL, NULL, '2025-12-15 20:20:37', '2025-12-15 20:20:37'),
	(32, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '2ba00aacacca56b03b1502c0b8a8a45ead6213b417acae80cb26404dc630b14f', '["*"]', NULL, NULL, '2025-12-19 10:20:24', '2025-12-19 10:20:24'),
	(33, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '5a14398d75a61b65c887e93e612cc732f4f097c1aeb80011eddf4ca66ddbcde6', '["*"]', NULL, NULL, '2025-12-19 10:21:17', '2025-12-19 10:21:17'),
	(34, 'App\\Models\\Karyawan', 'aril@gmail.com', 'flutter-karyawan', 'fd39cc1389cf63405b89512bc4d8114f9b7447e37f6c70b98755d5ea0193f437', '["*"]', NULL, NULL, '2025-12-19 10:34:34', '2025-12-19 10:34:34'),
	(35, 'App\\Models\\Karyawan', 'ricky@gmail.com', 'flutter-karyawan', 'af809db11f9b91dc7808fb12b8be4bf85e8e814fc60eee084c2989f152eb2dc5', '["*"]', NULL, NULL, '2025-12-19 10:42:21', '2025-12-19 10:42:21'),
	(36, 'App\\Models\\Karyawan', 'ricky@gmail.com', 'flutter-karyawan', '8e1355c9ff97a1c43fcec2643981edfa4fe9ef714b42078c55cdae4b734a4c38', '["*"]', NULL, NULL, '2025-12-19 10:42:48', '2025-12-19 10:42:48'),
	(37, 'App\\Models\\Karyawan', 'ricky@gmail.com', 'flutter-karyawan', 'aa88b4148a3ff3ec07ee679f0587b54f91f33fa294b90cf32a8e0e9202dc0e1f', '["*"]', NULL, NULL, '2025-12-19 10:50:08', '2025-12-19 10:50:08'),
	(38, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'cd92ee4c63604be62361428650ee7e653fbc300c5f21d950c101e2c5dc0f5f5b', '["*"]', NULL, NULL, '2025-12-19 11:22:15', '2025-12-19 11:22:15'),
	(39, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '610f5722635950103d5b1ea75ec28b4ae2e338fc0b828ffd706bd1b29387a527', '["*"]', NULL, NULL, '2025-12-19 11:35:32', '2025-12-19 11:35:32'),
	(40, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'e571fcb02210c4acc09e38069d359f7728e03748976e1e006885f3e19b0c5e91', '["*"]', NULL, NULL, '2025-12-19 11:52:36', '2025-12-19 11:52:36'),
	(41, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'a06e3f419505c4c942b5435aee28be1650e8e77bd70bc466012862c549d459ce', '["*"]', NULL, NULL, '2025-12-19 12:02:14', '2025-12-19 12:02:14'),
	(42, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'a05f92d192ba4c4b7c3fff2d422f797712336e43536f9b267a5f0bcc0025ef5f', '["*"]', NULL, NULL, '2025-12-19 14:08:15', '2025-12-19 14:08:15'),
	(43, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '74a283d5d6e047fd112fd8cdc4826d7c0c1613a445ca9c203af4e57a5c57638c', '["*"]', NULL, NULL, '2025-12-19 15:23:10', '2025-12-19 15:23:10'),
	(44, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'e1577b3144dfcff0f848cfb5ed1acff91e656f8482ee1a22fc9cc0fb9c2140c3', '["*"]', NULL, NULL, '2025-12-19 15:25:11', '2025-12-19 15:25:11'),
	(45, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '18feb8618f0ff49c74ce0ba247b0fc0d5da1a47835b226f996cf35acf688b5db', '["*"]', NULL, NULL, '2025-12-19 15:32:41', '2025-12-19 15:32:41'),
	(46, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '3347b84ff27fa2b0257e64e37ef1f7f2283b757524ba3a30c395874024a4fa26', '["*"]', NULL, NULL, '2025-12-19 19:44:28', '2025-12-19 19:44:28'),
	(47, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '5e54bb2394c7e67555adc2d3c4418c2c43c471c7dac0d9407c234c00562a638f', '["*"]', NULL, NULL, '2025-12-19 19:53:47', '2025-12-19 19:53:47'),
	(48, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '9c42708a695d3a3f949c6b68b4822e14b03fe89edcc3c86ded1159fc0779c209', '["*"]', NULL, NULL, '2025-12-19 20:27:28', '2025-12-19 20:27:28'),
	(49, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '2840eaebec58158e7f8322e5cad9b6c1037b14dd891d80dacc8d9cc14d4c9f50', '["*"]', NULL, NULL, '2025-12-19 20:28:15', '2025-12-19 20:28:15'),
	(50, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'a3bcf55b66134e573dbe48a45893f1800206a5a6e85bdfcf4dd160e8dab98b55', '["*"]', NULL, NULL, '2025-12-23 16:25:37', '2025-12-23 16:25:37'),
	(51, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '6d013ed3d6c442cd35a53b91e47603ab74092b59713c8ca70f1640ddb2f842e7', '["*"]', NULL, NULL, '2025-12-23 16:31:39', '2025-12-23 16:31:39'),
	(52, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'd81fc16be1148d985fab4b03f77a23e97b52fb571afd7a0f6248a1e054b59ae5', '["*"]', NULL, NULL, '2025-12-23 16:37:24', '2025-12-23 16:37:24'),
	(53, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '23e801da41e6df9e7fe9c6aca1ec4593f518993c1a4d8381163eeb3746e7352e', '["*"]', NULL, NULL, '2025-12-23 17:00:10', '2025-12-23 17:00:10'),
	(54, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '1600ab9ac198a44545ce1e7cea38bf1da948237142a36d31cc650ca0c94c540a', '["*"]', NULL, NULL, '2025-12-23 17:01:22', '2025-12-23 17:01:22'),
	(55, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'f7d8ccf201fa66cb5e7926846aa5d8b7db60c2334fa29e78436bbec5d665336c', '["*"]', NULL, NULL, '2025-12-23 18:16:51', '2025-12-23 18:16:51'),
	(56, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'fd5bf9ceabde9225bb096c8bb1be888e088f2ef66113e6c57af68b9a9de181d9', '["*"]', NULL, NULL, '2025-12-23 18:19:14', '2025-12-23 18:19:14'),
	(57, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'dc501a9668048ed2d1dd60e9d01502feb83576ed50e0067aa2ae0897cc344822', '["*"]', NULL, NULL, '2025-12-23 18:45:26', '2025-12-23 18:45:26'),
	(58, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '337d8f20d521e0be9e46566b0ef3314637409526bd656fa0e709488e7de01383', '["*"]', NULL, NULL, '2025-12-24 05:38:02', '2025-12-24 05:38:02'),
	(59, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '9d0bc8ff5dfa6717bde142468ac1743f1f299eebeaf161441d0caa127a65ca96', '["*"]', NULL, NULL, '2025-12-24 06:08:39', '2025-12-24 06:08:39'),
	(60, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'ebee38bf95ca0e211daced4f17b8377cc64deec397ac13d601291ce505fa037d', '["*"]', NULL, NULL, '2025-12-24 07:54:09', '2025-12-24 07:54:09'),
	(61, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', 'b8999272ae36b2bce013d8ab06030da156ae4915165de5072cb38b010ed6a546', '["*"]', NULL, NULL, '2025-12-24 09:14:55', '2025-12-24 09:14:55'),
	(62, 'App\\Models\\Karyawan', 'fairuz@gmail.com', 'flutter-karyawan', '0b935efca91161ca2879471a69adefcce0cfa588932da6b66731b3dfdbc4d0f4', '["*"]', NULL, NULL, '2025-12-24 09:24:52', '2025-12-24 09:24:52');

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
  `kode_jam_kerja` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_out` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.presensi: ~47 rows (lebih kurang)
DELETE FROM `presensi`;
INSERT INTO `presensi` (`id`, `email`, `tgl_presensi`, `jam_in`, `jam_out`, `foto_in`, `foto_out`, `location_in`, `kode_jam_kerja`, `location_out`) VALUES
	(22, 'fairuz@gmail.com', '2025-11-14', '17:53:20', '17:53:47', 'fairuz@gmail.com-2025-11-05-in.png', 'fairuz@gmail.com-2025-11-05-out.png', '-7.3039872,112.7645184', NULL, '-7.3039872,112.7645184'),
	(23, 'fairuz@gmail.com', '2025-11-06', '00:07:30', '00:07:43', 'fairuz@gmail.com-2025-11-06-in.png', 'fairuz@gmail.com-2025-11-06-out.png', '-7.4975402,112.7203576', NULL, '-7.4975402,112.7203576'),
	(25, 'fairuz@gmail.com', '2025-11-09', '03:38:53', '03:41:44', 'fairuz@gmail.com-2025-11-07-in.png', 'fairuz@gmail.com-2025-11-07-out.png', '-7.3105408,112.738304', NULL, '-7.3105408,112.738304'),
	(26, 'fairuz@gmail.com', '2025-11-10', '14:08:42', '14:25:39', 'fairuz@gmail.com-2025-11-10-in.png', 'fairuz@gmail.com-2025-11-10-out.png', '-7.2843264,112.7514112', NULL, '-7.2843264,112.7514112'),
	(27, 'fairuz@gmail.com', '2025-11-11', '00:32:51', '00:33:01', 'fairuz@gmail.com-2025-11-11-in.png', 'fairuz@gmail.com-2025-11-11-out.png', '-7.2638,112.7374', NULL, '-7.2638,112.7374'),
	(28, 'fairuz@gmail.com', '2025-11-12', '08:20:47', '20:49:28', 'fairuz@gmail.com-2025-11-12-in.png', 'fairuz@gmail.com-2025-11-12-out.png', '-7.2709,112.7446', NULL, '-7.2709,112.7446'),
	(30, 'fairuz@gmail.com', '2025-11-13', '18:19:18', '18:20:04', 'fairuz@gmail.com-2025-11-13-in.png', 'fairuz@gmail.com-2025-11-13-out.png', '-7.3203712,112.738304', NULL, '-7.3203712,112.738304'),
	(34, 'fairuz@gmail.com', '2025-11-24', '00:36:24', NULL, 'fairuz@gmail.com-2025-11-24-in.png', NULL, '-7.3039872,112.738304', NULL, NULL),
	(35, 'fairuz@gmail.com', '2025-12-23', '10:34:05', NULL, 'fairuz@gmail.com-2025-12-23-in.png', NULL, '-7.2876032,112.738304', 'JK02', NULL),
	(36, 'fairuz@gmail.com', '2026-01-06', '14:13:41', '14:14:54', 'fairuz@gmail.com-2026-01-06-in.png', 'fairuz@gmail.com-2026-01-06-out.png', '-7.323648,112.738304', 'JK02', '-7.323648,112.738304'),
	(37, 'fairuz@gmail.com', '2026-04-11', '14:01:11', NULL, 'fairuz@gmail.com-2026-04-11-in.png', NULL, '-7.4973688,112.72028', 'JK01', NULL),
	(38, 'fairuz@gmail.com', '2026-04-11', '14:01:27', NULL, 'fairuz@gmail.com-2026-04-11-in.png', NULL, '-7.4973688,112.72028', 'JK01', NULL),
	(39, 'fairuz@gmail.com', '2026-04-11', '14:02:11', NULL, 'fairuz@gmail.com-2026-04-11-in.png', NULL, '-7.4973688,112.72028', 'JK01', NULL),
	(40, 'fairuz@gmail.com', '2026-04-11', '14:02:32', NULL, 'fairuz@gmail.com-2026-04-11-in.png', NULL, '-7.4973688,112.72028', 'JK01', NULL),
	(41, 'fairuz@gmail.com', '2026-04-11', '14:03:25', NULL, 'fairuz@gmail.com-2026-04-11-in.png', NULL, '-7.4973688,112.72028', 'JK01', NULL),
	(42, 'fairuz@gmail.com', '2026-04-11', '14:03:43', NULL, 'fairuz@gmail.com-2026-04-11-in.png', NULL, '-7.4973688,112.72028', 'JK01', NULL),
	(43, 'fairuz@gmail.com', '2026-04-11', '14:24:27', NULL, 'fairuz@gmail.com-2026-04-11-in.png', NULL, '-7.4973603,112.7202821', 'JK01', NULL),
	(44, 'fairuz@gmail.com', '2026-04-11', '14:29:35', NULL, 'fairuz@gmail.com-2026-04-11-in.png', NULL, '-7.4973811,112.7202719', 'JK01', NULL),
	(45, 'fairuz@gmail.com', '2026-04-12', '19:56:00', '21:29:17', 'fairuz@gmail.com-2026-04-12-in.png', 'fairuz@gmail.com-2026-04-12-out.png', '-7.497361,112.7202793', 'JK01', '-7.4973747,112.7206127'),
	(46, 'fairuz@gmail.com', '2026-04-12', '21:29:33', NULL, 'fairuz@gmail.com-2026-04-12-in.png', NULL, '-7.4973747,112.7206127', 'JK01', NULL),
	(47, 'pramandani@gmail.com', '2026-04-26', '22:05:07', NULL, 'pramandani@gmail.com-2026-04-26-in.png', NULL, '-7.4973816,112.7202942', 'JK01', NULL),
	(48, 'fairuz@gmail.com', '2026-05-08', '12:18:12', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973835,112.7202998', 'JK01', NULL),
	(49, 'fairuz@gmail.com', '2026-05-08', '12:33:35', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973818,112.7202971', 'JK01', NULL),
	(50, 'fairuz@gmail.com', '2026-05-08', '12:42:52', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973822,112.7202971', 'JK01', NULL),
	(51, 'fairuz@gmail.com', '2026-05-08', '12:43:04', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973822,112.7202971', 'JK01', NULL),
	(52, 'fairuz@gmail.com', '2026-05-08', '12:43:04', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973822,112.7202971', 'JK01', NULL),
	(53, 'fairuz@gmail.com', '2026-05-08', '12:43:04', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973822,112.7202971', 'JK01', NULL),
	(54, 'fairuz@gmail.com', '2026-05-08', '12:52:06', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973845,112.7202975', 'JK01', NULL),
	(55, 'fairuz@gmail.com', '2026-05-08', '12:52:19', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973845,112.7202975', 'JK01', NULL),
	(56, 'fairuz@gmail.com', '2026-05-08', '12:52:55', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973512,112.7205762', 'JK01', NULL),
	(57, 'fairuz@gmail.com', '2026-05-08', '12:53:08', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973512,112.7205762', 'JK01', NULL),
	(58, 'fairuz@gmail.com', '2026-05-08', '12:54:28', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4976152,112.7204251', 'JK01', NULL),
	(59, 'fairuz@gmail.com', '2026-05-08', '12:54:41', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4976152,112.7204251', 'JK01', NULL),
	(60, 'fairuz@gmail.com', '2026-05-08', '12:55:15', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973756,112.7202938', 'JK01', NULL),
	(61, 'fairuz@gmail.com', '2026-05-08', '12:55:27', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973756,112.7202938', 'JK01', NULL),
	(62, 'fairuz@gmail.com', '2026-05-08', '12:55:41', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973754,112.7202914', 'JK01', NULL),
	(63, 'fairuz@gmail.com', '2026-05-08', '12:55:54', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973754,112.7202914', 'JK01', NULL),
	(64, 'fairuz@gmail.com', '2026-05-08', '12:57:14', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973722,112.7203105', 'JK01', NULL),
	(65, 'fairuz@gmail.com', '2026-05-08', '12:57:26', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.4973722,112.7203105', 'JK01', NULL),
	(66, 'fairuz@gmail.com', '2026-05-08', '12:59:09', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.49738,112.7202983', 'JK01', NULL),
	(67, 'fairuz@gmail.com', '2026-05-08', '12:59:21', NULL, 'fairuz@gmail.com-2026-05-08-in.png', NULL, '-7.49738,112.7202983', 'JK01', NULL),
	(68, 'fairuzpram5@gmail.com', '2026-05-12', '11:37:15', NULL, 'fairuzpram5@gmail.com-2026-05-12-in.png', NULL, '-7.4973952,112.7203185', 'JK01', NULL),
	(69, 'fairuzpram5@gmail.com', '2026-05-12', '11:37:26', NULL, 'fairuzpram5@gmail.com-2026-05-12-in.png', NULL, '-7.4973952,112.7203185', 'JK01', NULL),
	(70, 'fairuzpram5@gmail.com', '2026-05-12', '11:37:53', NULL, 'fairuzpram5@gmail.com-2026-05-12-in.png', NULL, '-7.4973946,112.7203185', 'JK01', NULL),
	(71, 'fairuzpram5@gmail.com', '2026-05-12', '11:38:05', NULL, 'fairuzpram5@gmail.com-2026-05-12-in.png', NULL, '-7.4973946,112.7203185', 'JK01', NULL),
	(72, 'fairuzpram5@gmail.com', '2026-05-12', '11:42:09', NULL, 'fairuzpram5@gmail.com-2026-05-12-in.png', NULL, '-7.4973982,112.7203179', 'JK01', NULL),
	(73, 'fairuzpram5@gmail.com', '2026-05-12', '11:42:21', NULL, 'fairuzpram5@gmail.com-2026-05-12-in.png', NULL, '-7.4973982,112.7203179', 'JK01', NULL);

-- membuang struktur untuk table presensi-geolocation.purchases
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `status` enum('success','canceled') COLLATE utf8mb4_unicode_520_ci DEFAULT 'success',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- Membuang data untuk tabel presensi-geolocation.purchases: ~0 rows (lebih kurang)
DELETE FROM `purchases`;
INSERT INTO `purchases` (`id`, `product_id`, `qty`, `status`, `created_at`) VALUES
	(1, 2, 2, 'success', '2026-01-05 09:13:08');

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
  `face_embedding` text COLLATE utf8mb4_unicode_ci COMMENT 'Menyimpan kode vektor wajah dari Python',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_email_unique` (`email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Membuang data untuk tabel presensi-geolocation.users: ~1 rows (lebih kurang)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `face_embedding`) VALUES
	(1, 'Fairuz Pramandani', 'fairuzpramandani5@gmail.com', NULL, '$2y$12$wqxdjwvDClTl/ZXf2gFUbuTxS5fXPcg.AVPI2rAfSYgTLMUyvCsMm', NULL, NULL, NULL, NULL),
	(13, 'daffa rahmandani', 'daffarahmandani5@gmail.com', NULL, '$2y$12$JhVPCTR6bD54raRCHIg8De.YGOg60PADvnMp0uEQG/K6lkbvitui2', NULL, '2026-01-12 14:16:54', '2026-01-12 14:16:54', NULL),
	(14, 'Fairuz Pramandani', 'fairuzpraman5@gmail.com', NULL, '$2y$12$e5ajw87AtZFcGWtpBPlwlecHm3ZMk52du5O1t/5J6n0NouC6hpMzG', NULL, '2026-05-12 04:48:44', '2026-05-12 04:48:44', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
