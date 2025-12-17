-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Des 2025 pada 04.59
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbwr`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bpjs`
--

CREATE TABLE `bpjs` (
  `id_bpjs` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Status_BPJS_KT` varchar(255) DEFAULT NULL,
  `Status_BPJS_KS` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bpjs`
--

INSERT INTO `bpjs` (`id_bpjs`, `id_karyawan`, `Status_BPJS_KT`, `Status_BPJS_KS`, `created_at`, `update_at`) VALUES
(1, 3, 'aktif', 'aktif', '2025-12-10 06:55:33', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_keluarga`
--

CREATE TABLE `data_keluarga` (
  `id_keluarga` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Nama_Ayah_Kandung` varchar(255) DEFAULT NULL,
  `Nama_Ibu_Kandung` varchar(255) DEFAULT NULL,
  `Nama_Lengkap_Suami_Istri` varchar(255) DEFAULT NULL,
  `NIK_KTP_Suami_Istri` double DEFAULT NULL,
  `Tempat_Lahir_Suami_Istri` varchar(255) DEFAULT NULL,
  `Tanggal_Lahir_Suami_Istri` date DEFAULT NULL,
  `Nomor_Telepon_Suami_Istri` varchar(255) DEFAULT NULL,
  `Pendidikan_Terakhir_Suami_Istri` varchar(255) DEFAULT NULL,
  `Nama_Lengkap_Anak_Pertama` varchar(255) DEFAULT NULL,
  `Tempat_Lahir_Anak_Pertama` varchar(255) DEFAULT NULL,
  `Tanggal_Lahir_Anak_Pertama` date DEFAULT NULL,
  `Jenis_Kelamin_Anak_Pertama` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `Pendidikan_Terakhir_Anak_Pertama` varchar(255) DEFAULT NULL,
  `Nama_Lengkap_Anak_Kedua` varchar(255) DEFAULT NULL,
  `Tempat_Lahir_Anak_Kedua` varchar(255) DEFAULT NULL,
  `Tanggal_Lahir_Anak_Kedua` varchar(255) DEFAULT NULL,
  `Jenis_Kelamin_Anak_Kedua` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `Pendidikan_Terakhir_Anak_Kedua` varchar(255) DEFAULT NULL,
  `Nama_Lengkap_Anak_Ketiga` varchar(255) DEFAULT NULL,
  `Tempat_Lahir_Anak_Ketiga` varchar(255) DEFAULT NULL,
  `Tanggal_Lahir_Anak_Ketiga` date DEFAULT NULL,
  `Jenis_Kelamin_Anak_Ketiga` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `Pendidikan_Terakhir_Anak_Ketiga` varchar(255) DEFAULT NULL,
  `Nama_Lengkap_Anak_Keempat` varchar(255) DEFAULT NULL,
  `Tempat_Lahir_Anak_Keempat` varchar(255) DEFAULT NULL,
  `Tanggal_Lahir_Anak_Keempat` date DEFAULT NULL,
  `Jenis_Kelamin_Anak_Keempat` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `Pendidikan_Terakhir_Anak_Keempat` varchar(255) DEFAULT NULL,
  `Nama_Lengkap_Anak_Kelima` varchar(255) DEFAULT NULL,
  `Tempat_Lahir_Anak_Kelima` varchar(255) DEFAULT NULL,
  `Tanggal_Lahir_Anak_Kelima` date DEFAULT NULL,
  `Jenis_Kelamin_Anak_Kelima` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `Pendidikan_Terakhir_Anak_Kelima` varchar(255) DEFAULT NULL,
  `Nama_Lengkap_Anak_Keenam` varchar(255) DEFAULT NULL,
  `Tempat_Lahir_Anak_Keenam` varchar(255) DEFAULT NULL,
  `Tanggal_Lahir_Anak_Keenam` date DEFAULT NULL,
  `Jenis_Kelamin_Anak_Keenam` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `Pendidikan_Terakhir_Anak_Keenam` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_keluarga`
--

INSERT INTO `data_keluarga` (`id_keluarga`, `id_karyawan`, `Nama_Ayah_Kandung`, `Nama_Ibu_Kandung`, `Nama_Lengkap_Suami_Istri`, `NIK_KTP_Suami_Istri`, `Tempat_Lahir_Suami_Istri`, `Tanggal_Lahir_Suami_Istri`, `Nomor_Telepon_Suami_Istri`, `Pendidikan_Terakhir_Suami_Istri`, `Nama_Lengkap_Anak_Pertama`, `Tempat_Lahir_Anak_Pertama`, `Tanggal_Lahir_Anak_Pertama`, `Jenis_Kelamin_Anak_Pertama`, `Pendidikan_Terakhir_Anak_Pertama`, `Nama_Lengkap_Anak_Kedua`, `Tempat_Lahir_Anak_Kedua`, `Tanggal_Lahir_Anak_Kedua`, `Jenis_Kelamin_Anak_Kedua`, `Pendidikan_Terakhir_Anak_Kedua`, `Nama_Lengkap_Anak_Ketiga`, `Tempat_Lahir_Anak_Ketiga`, `Tanggal_Lahir_Anak_Ketiga`, `Jenis_Kelamin_Anak_Ketiga`, `Pendidikan_Terakhir_Anak_Ketiga`, `Nama_Lengkap_Anak_Keempat`, `Tempat_Lahir_Anak_Keempat`, `Tanggal_Lahir_Anak_Keempat`, `Jenis_Kelamin_Anak_Keempat`, `Pendidikan_Terakhir_Anak_Keempat`, `Nama_Lengkap_Anak_Kelima`, `Tempat_Lahir_Anak_Kelima`, `Tanggal_Lahir_Anak_Kelima`, `Jenis_Kelamin_Anak_Kelima`, `Pendidikan_Terakhir_Anak_Kelima`, `Nama_Lengkap_Anak_Keenam`, `Tempat_Lahir_Anak_Keenam`, `Tanggal_Lahir_Anak_Keenam`, `Jenis_Kelamin_Anak_Keenam`, `Pendidikan_Terakhir_Anak_Keenam`, `created_at`, `update_at`) VALUES
(1, 3, 'Moh. Karyadi', 'Sumarti', '-', NULL, NULL, NULL, '081325952225', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Laki-laki', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-10 06:49:53', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` bigint(20) NOT NULL,
  `NIK` double DEFAULT NULL,
  `Status` enum('1','0') DEFAULT NULL,
  `Kode` enum('Aktif','Tidak Aktif') DEFAULT NULL,
  `Nama_Sesuai_KTP` varchar(255) DEFAULT NULL,
  `NIK_KTP` double DEFAULT NULL,
  `Nama_Lengkap_Sesuai_Ijazah` varchar(255) DEFAULT NULL,
  `Tempat_Lahir_Karyawan` varchar(255) DEFAULT NULL,
  `Tanggal_Lahir_Karyawan` date DEFAULT NULL,
  `Umur_Karyawan` varchar(255) DEFAULT NULL,
  `Jenis_Kelamin_Karyawan` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `Status_Pernikahan` enum('Belum Menikah','Menikah','Cerai Hidup','Cerai Mati (Duda/Janda)') DEFAULT NULL,
  `Golongan_Darah` enum('Tidak Tahu','A','B','O','AB') DEFAULT NULL,
  `Nomor_Telepon_Aktif_Karyawan` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Alamat_KTP` varchar(255) DEFAULT NULL,
  `RT` varchar(255) DEFAULT NULL,
  `RW` varchar(255) DEFAULT NULL,
  `Kelurahan_Desa` varchar(255) DEFAULT NULL,
  `Kecamatan` varchar(255) DEFAULT NULL,
  `Kabupaten_Kota` varchar(255) DEFAULT NULL,
  `Provinsi` varchar(255) DEFAULT NULL,
  `Alamat_Domisili` varchar(255) DEFAULT NULL,
  `RT_Sesuai_Domisili` varchar(255) DEFAULT NULL,
  `RW_Sesuai_Domisili` varchar(255) DEFAULT NULL,
  `Kelurahan_Desa_Domisili` varchar(255) DEFAULT NULL,
  `Kecamatan_Sesuai_Domisili` varchar(255) DEFAULT NULL,
  `Kabupaten_Kota_Sesuai_Domisili` varchar(255) DEFAULT NULL,
  `Provinsi_Sesuai_Domisili` varchar(255) DEFAULT NULL,
  `Alamat_Lengkap` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `NIK`, `Status`, `Kode`, `Nama_Sesuai_KTP`, `NIK_KTP`, `Nama_Lengkap_Sesuai_Ijazah`, `Tempat_Lahir_Karyawan`, `Tanggal_Lahir_Karyawan`, `Umur_Karyawan`, `Jenis_Kelamin_Karyawan`, `Status_Pernikahan`, `Golongan_Darah`, `Nomor_Telepon_Aktif_Karyawan`, `Email`, `Alamat_KTP`, `RT`, `RW`, `Kelurahan_Desa`, `Kecamatan`, `Kabupaten_Kota`, `Provinsi`, `Alamat_Domisili`, `RT_Sesuai_Domisili`, `RW_Sesuai_Domisili`, `Kelurahan_Desa_Domisili`, `Kecamatan_Sesuai_Domisili`, `Kabupaten_Kota_Sesuai_Domisili`, `Provinsi_Sesuai_Domisili`, `Alamat_Lengkap`, `created_at`, `update_at`) VALUES
(2, 1, '1', 'Aktif', 'hamzah', 1, 'hamzah', 'pati', '2018-03-09', '17', 'Laki-laki', 'Belum Menikah', 'O', '081367735262', 'hamzah@gmail.com', 'pati', '1', '2', 'bumiayu', 'wedarijaksa', 'pati', 'jawa tengah', 'pati', '1', '3', 'bumiayu', 'wedarijaksa', 'pati', 'jawa tengah', 'bumiayu pati', '2025-12-10 06:45:49', NULL),
(3, 21670004, '', 'Aktif', 'Hamzah Noer Arifin', NULL, NULL, 'pati', NULL, NULL, NULL, NULL, NULL, '081367735262', 'hamzahnoerarifin11@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Ds Bumiayu RT 01 RW 02 Kecamatan Wedarijaksa Kabupaten Pati', '2025-12-10 06:45:49', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kontrak`
--

CREATE TABLE `kontrak` (
  `id_kontrak` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Tanggal_Mulai_Tugas` date DEFAULT NULL,
  `PKWT_berakhir` varchar(255) DEFAULT NULL,
  `Tanggal_Diangkat_Menjadi_Karyawan_Tetap` varchar(255) DEFAULT NULL,
  `Riwayat_Penempatan` varchar(255) DEFAULT NULL,
  `Tanggal_Riwayat_Penempatan` varchar(255) DEFAULT NULL,
  `Mutasi_Promosi_Demosi` varchar(255) DEFAULT NULL,
  `Tanggal_Mutasi_Promosi_Demosi` varchar(255) DEFAULT NULL,
  `Masa_Kerja` varchar(255) DEFAULT NULL,
  `NO_PKWT_PERTAMA` varchar(255) DEFAULT NULL,
  `NO_SK_PERTAMA` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kontrak`
--

INSERT INTO `kontrak` (`id_kontrak`, `id_karyawan`, `Tanggal_Mulai_Tugas`, `PKWT_berakhir`, `Tanggal_Diangkat_Menjadi_Karyawan_Tetap`, `Riwayat_Penempatan`, `Tanggal_Riwayat_Penempatan`, `Mutasi_Promosi_Demosi`, `Tanggal_Mutasi_Promosi_Demosi`, `Masa_Kerja`, `NO_PKWT_PERTAMA`, `NO_SK_PERTAMA`, `created_at`, `update_at`) VALUES
(1, 3, '2021-03-05', '5 maret 2026', NULL, NULL, NULL, NULL, NULL, NULL, '004/A00/PKWT-KB/VIII/25', NULL, '2025-12-10 06:58:20', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pekerjaan`
--

CREATE TABLE `pekerjaan` (
  `id_pekerjaan` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Jabatan` enum('Staff','Kepala Regu','Kepala Unit','Kepala Shift','Head Of Brances (HOB)','Supervisor','Manager','General Manager (GM)','Direktur') DEFAULT NULL,
  `Bagian` varchar(255) DEFAULT NULL,
  `Departement` enum('BUSINESS','BUSINESS DEVELOPMENT','DIREKSI','FINANCE & ACCOUNTING','MARKETING & SALES','OPERASIONAL','RESEARCH & ENGINEERING','STABLE') DEFAULT NULL,
  `Divisi` enum('Accounting','Branches Bali','Branches Bandung','Branches Bekasi','Branches Bogor','Branches Cianjur','Branches Cirebon','Branches Jember','Branches Kediri','Branches Lampung','Branches Latubo','Branches Madiun','Branches Madura','Branches Magelang','Branches Malang','Branches Pati','Branches Purwakarta','Branches Purwokerto','Branches Semarang','Branches Serang','Branches Sidoarjo','Branches Surakarta','Branches Tangerang','Branches Tasikmalaya','Branches Tegal','Business','Design Product','Development','Direktur Umum','Div. Business Development','Div. Marketing & Sales','Div. Research & Engineering','Divisi Operasional','East Area Sales','Engineering Service','Fabrikasi F1 (WKD)','Fabrikasi F2 (Residensial Door)','Fabrikasi F2 (Wadja)','Factory','Finance','Finance & Accounting','Finance & Invesment','Finance Center','General Affair & Logistik','General Affairs','HR Center','HRD','Logistik','Logistik Marketing & Sales','Marcom','Marketing','Online Direct Selling','Project Spesialist','Prototype','Research','Research & Engineering','West Area Sales') DEFAULT NULL,
  `Unit` enum('Branches Bali','Branches Bandung','Branches Bekasi','Branches Bogor','Branches Bojonegoro','Branches Cianjur','Branches Cirebon','Branches Jember','Branches Kediri','Branches Lampung','Branches Latubo','Branches Madiun','Branches Madura','Branches Magelang','Branches Makassar','Branches Malang','Branches Palembang','Branches Pati','Branches Purwakarta','Branches Purwokerto','Branches Semarang','Branches Serang','Branches Sidoarjo','Branches Surakarta','Branches Tangerang','Branches Tasikmalaya','Branches Tegal','Branches Yogyakarta','Divisi Operasional','Factory','Factory 1','Factory 2','Factory 2 Residensial & Project','Factory 3','Factory 4','Finance','General Affair','Logistic','Maintanance','Online & Project Selling','Organization Development','Specialist K3') DEFAULT NULL,
  `Jenis_Kontrak` enum('PKWT','PKWTT') DEFAULT NULL,
  `Perjanjian` enum('Harian Lepas','Kontrak','Tetap') DEFAULT NULL,
  `Lokasi_Kerja` enum('Central Java - Pati','Central Java - Pekalongan','Central Java - Purwokerto','Central Java - Surakarta','Central Java - Magelang','Central Java - Semarang','Central Java - Tegal','West Java - Purwakarta','West Java - Cianjur','West Java - Bandung','West Java - Bogor','West Java - Cirebon','West Java - Tangerang','West Java - Bekasi','West Java - Tasikmalaya','Banten - Serang','East Java - Bojonegoro','East Java - Jember','East Java - Madiun','East Java - Madura','East Java - Sidoarjo','Bali - Bali','East Java - Malang','East Java - Kediri','South Sumatra - Lampung','South Sumatra - Palembang','DIY - Yogyakarta','South Sulawesi - Makassar') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pekerjaan`
--

INSERT INTO `pekerjaan` (`id_pekerjaan`, `id_karyawan`, `Jabatan`, `Bagian`, `Departement`, `Divisi`, `Unit`, `Jenis_Kontrak`, `Perjanjian`, `Lokasi_Kerja`, `created_at`, `update_at`) VALUES
(1, 3, '', NULL, '', NULL, NULL, NULL, NULL, '', '2025-12-10 06:54:30', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendidikan`
--

CREATE TABLE `pendidikan` (
  `id_pendidikan` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Pendidikan_Terakhir` varchar(255) DEFAULT NULL,
  `Nama_Lengkap_Tempat_Pendidikan_Terakhir` varchar(255) DEFAULT NULL,
  `Jurusan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendidikan`
--

INSERT INTO `pendidikan` (`id_pendidikan`, `id_karyawan`, `Pendidikan_Terakhir`, `Nama_Lengkap_Tempat_Pendidikan_Terakhir`, `Jurusan`, `created_at`, `update_at`) VALUES
(1, 3, 'S1', 'Universitas PGRI Semarang', 'Informatika', '2025-12-10 06:59:33', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `perusahaan`
--

CREATE TABLE `perusahaan` (
  `id_perusahaan` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Perusahaan` enum('CV BERKAH NEGERI MULIA','PT INTI DUNIA MANDIRI','PT SOCHA INTI INFORMATIKA','PT TIMUR SEMESTA ABADI','PT WADJA INTI MULIA','PT WADJA INTI MULIA PERSADA','PT WADJA KARYA DUNIA','PT WADJA TEKNIK MULIA','TAMANSARI EQUESTRIAN PARK') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `perusahaan`
--

INSERT INTO `perusahaan` (`id_perusahaan`, `id_karyawan`, `Perusahaan`, `created_at`, `update_at`) VALUES
(1, 3, '', '2025-12-10 07:04:07', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(80) NOT NULL,
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
('4zW1zjVqaAevQUOg4onpXuye3wlqmavsLx5seHVp', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibXZjdE5KcGxBalVERUdmbDA0MDh2VkJlc2w0YWJ4aThZdjVmNmdiUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9rYXJ5YXdhbi9jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1765419511),
('CUWvd3uVM9uiiAUBXLdhQVApAGYK0MsVA8oHsgpU', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN3BmUFdEUTBlQjhTVVdRODZONzV2NnE4RmM1dWx2aGUyZkNjanprayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9rYXJ5YXdhbi9jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1765357043);

-- --------------------------------------------------------

--
-- Struktur dari tabel `status_karyawan`
--

CREATE TABLE `status_karyawan` (
  `id_status` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Tanggal_Non_Aktif` date DEFAULT NULL,
  `Alasan_Non_Aktif` varchar(255) DEFAULT NULL,
  `Ijazah_Dikembalikan` varchar(255) DEFAULT NULL,
  `Bulan` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `update_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `status_karyawan`
--

INSERT INTO `status_karyawan` (`id_status`, `id_karyawan`, `Tanggal_Non_Aktif`, `Alasan_Non_Aktif`, `Ijazah_Dikembalikan`, `Bulan`, `created_at`, `update_at`) VALUES
(1, 3, NULL, '-', NULL, NULL, '2025-12-10 07:05:00', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `jabatan` varchar(200) DEFAULT NULL,
  `role` varchar(80) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `nik`, `jabatan`, `role`, `password`, `created_at`, `updated_at`) VALUES
(3, 'Admin One', 'admin@example.com', NULL, '', 'admin', '$2y$12$W9P9cpNxjAjDjuw/NsfZSuEUroH6NJfnBpmPF65x4qsBK52lJM3VS', '2025-12-07 23:46:38', '2025-12-07 23:46:38'),
(4, 'Felicia Lind MD', 'fahey.cordia@example.net', NULL, '', NULL, '$2y$12$DwOeRYdbLK03bKLl0IZKDuL2a4.BX.QTHXepenOU3RabSO12/hLXm', '2025-12-07 23:46:38', '2025-12-07 23:46:38'),
(5, 'Liam Brown', 'bogisich.zander@example.com', NULL, '', NULL, '$2y$12$DwOeRYdbLK03bKLl0IZKDuL2a4.BX.QTHXepenOU3RabSO12/hLXm', '2025-12-07 23:46:38', '2025-12-07 23:46:38'),
(6, 'Prof. Catalina Goldner II', 'madyson47@example.org', NULL, '', NULL, '$2y$12$DwOeRYdbLK03bKLl0IZKDuL2a4.BX.QTHXepenOU3RabSO12/hLXm', '2025-12-07 23:46:38', '2025-12-07 23:46:38'),
(7, 'Dr. Julius Hessel MD', 'ceasar07@example.org', NULL, '', NULL, '$2y$12$DwOeRYdbLK03bKLl0IZKDuL2a4.BX.QTHXepenOU3RabSO12/hLXm', '2025-12-07 23:46:38', '2025-12-07 23:46:38'),
(8, 'Ms. Mona Mayert', 'mariana91@example.net', NULL, '', NULL, '$2y$12$DwOeRYdbLK03bKLl0IZKDuL2a4.BX.QTHXepenOU3RabSO12/hLXm', '2025-12-07 23:46:38', '2025-12-07 23:46:38'),
(9, 'Alvis Stokes DDS', 'glangosh@example.com', NULL, '', NULL, '$2y$12$DwOeRYdbLK03bKLl0IZKDuL2a4.BX.QTHXepenOU3RabSO12/hLXm', '2025-12-07 23:46:39', '2025-12-07 23:46:39'),
(10, 'Zita Rempel', 'zroob@example.org', NULL, '', NULL, '$2y$12$DwOeRYdbLK03bKLl0IZKDuL2a4.BX.QTHXepenOU3RabSO12/hLXm', '2025-12-07 23:46:39', '2025-12-07 23:46:39'),
(11, 'Dr. Jayda Kris DVM', 'connor.eichmann@example.net', NULL, '', NULL, '$2y$12$DwOeRYdbLK03bKLl0IZKDuL2a4.BX.QTHXepenOU3RabSO12/hLXm', '2025-12-07 23:46:39', '2025-12-07 23:46:39'),
(12, 'Donnie Reilly', 'aida99@example.net', NULL, '', NULL, '$2y$12$DwOeRYdbLK03bKLl0IZKDuL2a4.BX.QTHXepenOU3RabSO12/hLXm', '2025-12-07 23:46:39', '2025-12-07 23:46:39'),
(14, 'Test User', 'test@example.com', NULL, NULL, NULL, '$2y$12$nZFZk6/LA1aL8TgRC58MOuBUzRYSDgK9kYlpOrHWTtkF9GjQe8dtG', '2025-12-09 07:20:01', '2025-12-09 07:20:01');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bpjs`
--
ALTER TABLE `bpjs`
  ADD PRIMARY KEY (`id_bpjs`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `data_keluarga`
--
ALTER TABLE `data_keluarga`
  ADD PRIMARY KEY (`id_keluarga`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indeks untuk tabel `kontrak`
--
ALTER TABLE `kontrak`
  ADD PRIMARY KEY (`id_kontrak`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pekerjaan`
--
ALTER TABLE `pekerjaan`
  ADD PRIMARY KEY (`id_pekerjaan`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  ADD PRIMARY KEY (`id_pendidikan`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD PRIMARY KEY (`id_perusahaan`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `status_karyawan`
--
ALTER TABLE `status_karyawan`
  ADD PRIMARY KEY (`id_status`),
  ADD KEY `id_karyawan` (`id_karyawan`);

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
-- AUTO_INCREMENT untuk tabel `bpjs`
--
ALTER TABLE `bpjs`
  MODIFY `id_bpjs` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `data_keluarga`
--
ALTER TABLE `data_keluarga`
  MODIFY `id_keluarga` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kontrak`
--
ALTER TABLE `kontrak`
  MODIFY `id_kontrak` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pekerjaan`
--
ALTER TABLE `pekerjaan`
  MODIFY `id_pekerjaan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  MODIFY `id_pendidikan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id_perusahaan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `status_karyawan`
--
ALTER TABLE `status_karyawan`
  MODIFY `id_status` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bpjs`
--
ALTER TABLE `bpjs`
  ADD CONSTRAINT `bpjs_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`);

--
-- Ketidakleluasaan untuk tabel `data_keluarga`
--
ALTER TABLE `data_keluarga`
  ADD CONSTRAINT `data_keluarga_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`);

--
-- Ketidakleluasaan untuk tabel `kontrak`
--
ALTER TABLE `kontrak`
  ADD CONSTRAINT `kontrak_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`);

--
-- Ketidakleluasaan untuk tabel `pekerjaan`
--
ALTER TABLE `pekerjaan`
  ADD CONSTRAINT `pekerjaan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`);

--
-- Ketidakleluasaan untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  ADD CONSTRAINT `pendidikan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`);

--
-- Ketidakleluasaan untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD CONSTRAINT `perusahaan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`);

--
-- Ketidakleluasaan untuk tabel `status_karyawan`
--
ALTER TABLE `status_karyawan`
  ADD CONSTRAINT `status_karyawan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
