-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Des 2025 pada 07.34
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
-- Struktur dari tabel `biaya_promote_ta`
--

CREATE TABLE `biaya_promote_ta` (
  `id_biaya_promote_ta` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `keperluan` varchar(150) DEFAULT NULL,
  `biaya` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bpjs`
--

CREATE TABLE `bpjs` (
  `id_bpjs` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Status_BPJS_KT` enum('Aktif','Tidak Aktif') DEFAULT NULL,
  `Status_BPJS_KS` enum('Aktif','Tidak Aktif') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bpjs`
--

INSERT INTO `bpjs` (`id_bpjs`, `id_karyawan`, `Status_BPJS_KT`, `Status_BPJS_KS`, `created_at`, `updated_at`) VALUES
(1, 3, 'Aktif', 'Aktif', '2025-12-10 06:55:33', NULL),
(2, 5, 'Aktif', 'Aktif', '2025-12-12 03:17:08', NULL),
(4, 8, 'Aktif', 'Tidak Aktif', '2025-12-14 08:42:52', NULL),
(5, 9, 'Aktif', 'Aktif', '2025-12-14 10:21:22', NULL),
(6, 2, 'Aktif', 'Aktif', '2025-12-14 14:03:42', NULL);

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
  `NIK_KTP_Suami_Istri` varchar(16) DEFAULT NULL,
  `Tempat_Lahir_Suami_Istri` varchar(255) DEFAULT NULL,
  `Tanggal_Lahir_Suami_Istri` date DEFAULT NULL,
  `Nomor_Telepon_Suami_Istri` varchar(255) DEFAULT NULL,
  `Pendidikan_Terakhir_Suami_Istri` varchar(255) DEFAULT NULL,
  `anak` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_keluarga`
--

INSERT INTO `data_keluarga` (`id_keluarga`, `id_karyawan`, `Nama_Ayah_Kandung`, `Nama_Ibu_Kandung`, `Nama_Lengkap_Suami_Istri`, `NIK_KTP_Suami_Istri`, `Tempat_Lahir_Suami_Istri`, `Tanggal_Lahir_Suami_Istri`, `Nomor_Telepon_Suami_Istri`, `Pendidikan_Terakhir_Suami_Istri`, `anak`, `created_at`, `updated_at`) VALUES
(1, 3, 'Asep', 'Dini', 'Putri', '66666666666', 'Jakarta', '1985-07-15', '081333333333', 'S1', '[{\"nama\":\"Martin Manewar\",\"tempat_lahir\":\"Jakarta\",\"tanggal_lahir\":\"2000-05-15\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"S1\"}]', '2025-12-10 06:49:53', NULL),
(2, 5, 'Ahmad Budi', 'Siti', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-12 03:17:08', NULL),
(4, 8, 'Ayah Karyawan 1', 'Ibu Karyawan 1', 'Suami Karyawan 1', '22222222222', 'Pati', '1996-10-14', '081222222222', 'S1', '[{\"nama\":\"Anak 1 Karyawan 1\",\"tempat_lahir\":\"Pati\",\"tanggal_lahir\":\"2020-01-14\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"SD\"},{\"nama\":\"Anak 2 Karyawan 1\",\"tempat_lahir\":\"Pati\",\"tanggal_lahir\":\"2024-01-14\",\"jenis_kelamin\":\"P\",\"pendidikan\":\"Belum Sekolah\"}]', '2025-12-14 08:42:52', NULL),
(5, 9, 'Ahmad Dahlan', 'Lusi', 'Mirai', '123456', 'Batam', '2002-10-14', '081222222222', 'S1', '[{\"nama\":\"Bas\",\"tempat_lahir\":\"Batam\",\"tanggal_lahir\":\"2024-01-14\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"Belum Sekolah\"}]', '2025-12-14 10:21:21', NULL),
(6, 2, 'a', 'b', 'moji', '1234', 'Jakarta', '2001-10-28', '081432432432', 'SMA', '[{\"nama\":\"Dewa\",\"tempat_lahir\":\"Kudus\",\"tanggal_lahir\":\"2023-01-16\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"Belum Sekolah\"},{\"nama\":\"Dewi\",\"tempat_lahir\":\"Kudus\",\"tanggal_lahir\":\"2025-02-10\",\"jenis_kelamin\":\"P\",\"pendidikan\":\"Belum Sekolah\"}]', '2025-12-14 14:03:42', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `interview_hr`
--

CREATE TABLE `interview_hr` (
  `id_interview_hr` int(11) NOT NULL,
  `kandidat_id` int(11) NOT NULL,
  `hari_tanggal` date DEFAULT NULL,
  `nama_kandidat` varchar(150) DEFAULT NULL,
  `nama_interviewer` varchar(150) DEFAULT NULL,
  `posisi_dilamar` varchar(100) DEFAULT NULL,
  `model_wawancara` enum('Online','Offline') DEFAULT NULL,
  `skor_profesional` int(11) DEFAULT NULL,
  `catatan_profesional` text DEFAULT NULL,
  `skor_spiritual` int(11) DEFAULT NULL,
  `catatan_spiritual` text DEFAULT NULL,
  `skor_learning` int(11) DEFAULT NULL,
  `catatan_learning` text DEFAULT NULL,
  `skor_initiative` int(11) DEFAULT NULL,
  `catatan_initiative` text DEFAULT NULL,
  `skor_komunikasi` int(11) DEFAULT NULL,
  `catatan_komunikasi` text DEFAULT NULL,
  `skor_problem_solving` int(11) DEFAULT NULL,
  `catatan_problem_solving` text DEFAULT NULL,
  `skor_teamwork` int(11) DEFAULT NULL,
  `catatan_teamwork` text DEFAULT NULL,
  `catatan_tambahan` text DEFAULT NULL,
  `keputusan` enum('DITERIMA','DITOLAK','MENGUNDURKAN DIRI') DEFAULT NULL,
  `total` varchar(100) DEFAULT NULL,
  `hasil_akhir` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kandidat`
--

CREATE TABLE `kandidat` (
  `id_kandidat` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `posisi_id` int(11) NOT NULL,
  `tanggal_melamar` date DEFAULT NULL,
  `sumber` varchar(100) DEFAULT NULL,
  `status_akhir` enum('Masuk','CV Lolos','Psikotes Lolos','Tes Kompetensi Lolos','Interview HR Lolos','Interview User Lolos','Diterima','Tidak Lolos') DEFAULT 'Masuk',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kandidat`
--

INSERT INTO `kandidat` (`id_kandidat`, `nama`, `posisi_id`, `tanggal_melamar`, `sumber`, `status_akhir`, `created_at`, `updated_at`) VALUES
(3, 'Joni', 1, NULL, NULL, 'Masuk', '2025-12-16 23:45:32', '2025-12-16 23:45:32'),
(4, 'Isyana', 5, NULL, NULL, 'Masuk', '2025-12-17 00:45:54', '2025-12-17 00:45:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kandidat_lanjut_user`
--

CREATE TABLE `kandidat_lanjut_user` (
  `id_kandidat_lanjut_user` int(11) NOT NULL,
  `kandidat_id` int(11) DEFAULT NULL,
  `user_terkait` varchar(100) DEFAULT NULL,
  `tanggal_interview_hr` date DEFAULT NULL,
  `tanggal_penyerahan` date DEFAULT NULL,
  `tanggal_interview_user_ass` date DEFAULT NULL,
  `hasil_ass` enum('Lolos','Tidak Lolos') DEFAULT NULL,
  `tanggal_interview_user_asm` date DEFAULT NULL,
  `hasil_asm` enum('Lolos','Tidak Lolos') DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `NIK_KTP` varchar(16) DEFAULT NULL,
  `Nama_Lengkap_Sesuai_Ijazah` varchar(255) DEFAULT NULL,
  `Tempat_Lahir_Karyawan` varchar(255) DEFAULT NULL,
  `Tanggal_Lahir_Karyawan` date DEFAULT NULL,
  `Umur_Karyawan` varchar(255) DEFAULT NULL,
  `Jenis_Kelamin_Karyawan` enum('L','P') DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `NIK`, `Status`, `Kode`, `Nama_Sesuai_KTP`, `NIK_KTP`, `Nama_Lengkap_Sesuai_Ijazah`, `Tempat_Lahir_Karyawan`, `Tanggal_Lahir_Karyawan`, `Umur_Karyawan`, `Jenis_Kelamin_Karyawan`, `Status_Pernikahan`, `Golongan_Darah`, `Nomor_Telepon_Aktif_Karyawan`, `Email`, `Alamat_KTP`, `RT`, `RW`, `Kelurahan_Desa`, `Kecamatan`, `Kabupaten_Kota`, `Provinsi`, `Alamat_Domisili`, `RT_Sesuai_Domisili`, `RW_Sesuai_Domisili`, `Kelurahan_Desa_Domisili`, `Kecamatan_Sesuai_Domisili`, `Kabupaten_Kota_Sesuai_Domisili`, `Provinsi_Sesuai_Domisili`, `Alamat_Lengkap`, `created_at`, `updated_at`) VALUES
(2, 111, '1', 'Aktif', 'Mirai', '1111', 'Mirai', 'Kudus', '2001-01-09', '24 Tahun', 'P', 'Menikah', 'AB', '081234234234', 'mirai@gmail.com', 'Jl. Manggis Kudus', '003', '001', 'WERGU WETAN', 'KOTA KUDUS', 'KABUPATEN KUDUS', 'JAWA TENGAH', 'Jl. Manggis Kudus', '003', '001', 'WERGU WETAN', 'KOTA KUDUS', 'KABUPATEN KUDUS', 'JAWA TENGAH', 'Jl. Manggis Kudus', '2025-12-10 06:45:49', NULL),
(3, 21670004, '1', 'Aktif', 'Marcell Bas', '234', 'Marcell Bas', 'pati', '1980-02-15', '45 Tahun', 'L', 'Menikah', 'O', '081999999999', 'marcellbas@gmail.com', 'Jakarta', '01', '01', 'KEBON SIRIH', 'MENTENG', 'KOTA JAKARTA PUSAT', 'DKI JAKARTA', 'Pati', '01', '02', 'PURI', 'PATI', 'KABUPATEN PATI', 'JAWA TENGAH', 'Puri Pati', '2025-12-10 06:45:49', NULL),
(5, 123456, '1', 'Aktif', 'Budi Santoso', '3.31123452345234', 'Budi Santoso', 'Pati', '2002-02-12', '23 Tahun', 'L', 'Belum Menikah', 'Tidak Tahu', '081234567890', 'budi@gmail.com', 'Ds Bumiayu RT 01 RW 02 Kecamatan Wedarijaksa Kabupaten Pati', '001', '002', 'BUMIAYU', 'WEDARIJAKSA', 'KABUPATEN PATI', 'JAWA TENGAH', 'Ds Bumiayu RT 01 RW 02 Kecamatan Wedarijaksa Kabupaten Pati', '001', '002', 'BUMIAYU', 'WEDARIJAKSA', 'KABUPATEN PATI', 'JAWA TENGAH', 'Ds Bumiayu RT 01 RW 02 Kecamatan Wedarijaksa Kabupaten Pati', '2025-12-12 03:17:08', NULL),
(8, 333333333, '1', 'Aktif', 'Karyawan1', '1', 'Karyawan1', 'Pati', '1996-01-01', '29 Tahun', 'P', 'Menikah', 'B', '081234567890', 'karyawan1@gmail.com', 'Pati', '01', '02', 'WINONG', 'PATI', 'KABUPATEN PATI', 'JAWA TENGAH', 'Pati', '01', '02', 'WINONG', 'PATI', 'KABUPATEN PATI', 'JAWA TENGAH', 'Pati', '2025-12-14 08:42:52', NULL),
(9, 1234, '1', 'Aktif', 'Tokio', '3311234523452345', 'Tokio', 'Pati', '1999-03-14', '26 Tahun', 'L', 'Menikah', 'Tidak Tahu', '081234567890', 'tokio@gmail.com', 'batam', '001', '002', 'BELIAN', 'BATAM KOTA', 'KOTA B A T A M', 'KEPULAUAN RIAU', 'batam', '001', '002', 'BELIAN', 'BATAM KOTA', 'KOTA B A T A M', 'KEPULAUAN RIAU', 'batam', '2025-12-14 10:21:21', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kontrak`
--

CREATE TABLE `kontrak` (
  `id_kontrak` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Tanggal_Mulai_Tugas` date DEFAULT NULL,
  `PKWT_Berakhir` date DEFAULT NULL,
  `Tanggal_Diangkat_Menjadi_Karyawan_Tetap` date DEFAULT NULL,
  `Riwayat_Penempatan` varchar(255) DEFAULT NULL,
  `Tanggal_Riwayat_Penempatan` date DEFAULT NULL,
  `Mutasi_Promosi_Demosi` varchar(255) DEFAULT NULL,
  `Tanggal_Mutasi_Promosi_Demosi` date DEFAULT NULL,
  `Masa_Kerja` varchar(255) DEFAULT NULL,
  `NO_PKWT_PERTAMA` varchar(255) DEFAULT NULL,
  `NO_SK_PERTAMA` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kontrak`
--

INSERT INTO `kontrak` (`id_kontrak`, `id_karyawan`, `Tanggal_Mulai_Tugas`, `PKWT_Berakhir`, `Tanggal_Diangkat_Menjadi_Karyawan_Tetap`, `Riwayat_Penempatan`, `Tanggal_Riwayat_Penempatan`, `Mutasi_Promosi_Demosi`, `Tanggal_Mutasi_Promosi_Demosi`, `Masa_Kerja`, `NO_PKWT_PERTAMA`, `NO_SK_PERTAMA`, `created_at`, `updated_at`) VALUES
(1, 3, '2021-03-05', '2020-02-15', '2020-03-15', 'Sales', '2017-07-15', 'Promosi', '2021-01-15', '4 Tahun 9 Bulan 10 Hari', '001/PKWT', '001/SK', '2025-12-10 06:58:20', NULL),
(2, 5, '2025-11-24', '2026-05-02', NULL, NULL, NULL, NULL, NULL, '0 Tahun 0 Bulan 18 Hari', NULL, NULL, '2025-12-12 03:17:08', NULL),
(6, 2, '2018-03-08', '2026-01-14', '2025-12-15', 'Akuntan', '2025-12-23', 'Promosi', '2025-12-09', '7 Tahun 9 Bulan 7 Hari', 'Npkwt1', 'Nsk1', '2025-12-14 15:19:47', NULL),
(7, 8, '2021-03-15', NULL, NULL, NULL, NULL, NULL, NULL, '4 Tahun 9 Bulan 1 Hari', NULL, NULL, '2025-12-16 03:25:51', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_assessments`
--

CREATE TABLE `kpi_assessments` (
  `id_kpi_assessment` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) NOT NULL,
  `penilai_id` bigint(20) DEFAULT NULL,
  `tahun` varchar(4) NOT NULL,
  `periode` varchar(50) NOT NULL,
  `tanggal_penilaian` date DEFAULT NULL,
  `total_skor_akhir` decimal(8,2) NOT NULL DEFAULT 0.00,
  `grade_akhir` varchar(20) DEFAULT NULL,
  `status` enum('DRAFT','SUBMITTED','APPROVED') NOT NULL DEFAULT 'DRAFT',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kpi_assessments`
--

INSERT INTO `kpi_assessments` (`id_kpi_assessment`, `karyawan_id`, `penilai_id`, `tahun`, `periode`, `tanggal_penilaian`, `total_skor_akhir`, `grade_akhir`, `status`, `created_at`, `updated_at`) VALUES
(4, 2, 3, '2025', 'Tahunan', '2025-12-18', '0.00', NULL, 'DRAFT', '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(5, 3, 3, '2025', 'Tahunan', '2025-12-18', '0.00', NULL, 'DRAFT', '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(6, 5, 3, '2025', 'Tahunan', '2025-12-18', '0.00', NULL, 'DRAFT', '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(7, 8, 3, '2025', 'Tahunan', '2025-12-19', '0.00', NULL, 'DRAFT', '2025-12-18 18:44:50', '2025-12-18 18:44:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_items`
--

CREATE TABLE `kpi_items` (
  `id_kpi_item` bigint(20) UNSIGNED NOT NULL,
  `kpi_assessment_id` bigint(20) UNSIGNED NOT NULL,
  `perspektif` varchar(255) NOT NULL,
  `key_result_area` varchar(255) DEFAULT NULL,
  `key_performance_indicator` text NOT NULL,
  `polaritas` varchar(100) DEFAULT NULL,
  `satuan` varchar(255) DEFAULT NULL,
  `realisasi` varchar(255) DEFAULT '0',
  `skor` double(8,2) DEFAULT 0.00,
  `skor_akhir` double(8,2) DEFAULT 0.00,
  `bobot` decimal(5,2) NOT NULL,
  `target` varchar(255) DEFAULT NULL,
  `target_tahunan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kpi_items`
--

INSERT INTO `kpi_items` (`id_kpi_item`, `kpi_assessment_id`, `perspektif`, `key_result_area`, `key_performance_indicator`, `polaritas`, `satuan`, `realisasi`, `skor`, `skor_akhir`, `bobot`, `target`, `target_tahunan`, `created_at`, `updated_at`) VALUES
(1, 4, 'Financial', 'Efisiensi Anggaran', 'Persentase penggunaan anggaran operasional sesuai budget', 'Minimize', '%', '0', 0.00, 0.00, '10.00', '100', NULL, '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(2, 4, 'Customer', 'Kepuasan Pelanggan (Internal/Eksternal)', 'Nilai rata-rata kepuasan user/klien (Survey)', 'Maximize', 'Skala', '0', 0.00, 0.00, '20.00', '4.5', NULL, '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(3, 4, 'Customer', 'Penanganan Komplain', 'Jumlah komplain yang tidak terselesaikan (Unresolved)', 'Minimize', 'Kasus', '0', 0.00, 0.00, '10.00', '0', NULL, '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(4, 4, 'Internal Process', 'Penyelesaian Tugas Utama', 'Persentase penyelesaian project/tugas tepat waktu (On-time)', 'Maximize', '%', '0', 0.00, 0.00, '30.00', '100', NULL, '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(5, 4, 'Internal Process', 'Kualitas Kerja', 'Jumlah kesalahan (error/rework) major dalam pekerjaan', 'Minimize', 'Kasus', '0', 0.00, 0.00, '15.00', '0', NULL, '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(6, 4, 'Learning & Growth', 'Pengembangan Diri', 'Jumlah jam pelatihan / training yang diikuti', 'Maximize', 'Jam', '0', 0.00, 0.00, '10.00', '20', NULL, '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(7, 4, 'Learning & Growth', 'Kedisiplinan', 'Persentase kehadiran kerja (Absensi)', 'Maximize', '%', '0', 0.00, 0.00, '5.00', '98', NULL, '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(8, 5, 'Financial', 'Efisiensi Anggaran', 'Persentase penggunaan anggaran operasional sesuai budget', 'Minimize', '%', '0', 0.00, 0.00, '10.00', '100', NULL, '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(9, 5, 'Customer', 'Kepuasan Pelanggan (Internal/Eksternal)', 'Nilai rata-rata kepuasan user/klien (Survey)', 'Maximize', 'Skala', '0', 0.00, 0.00, '20.00', '4.5', NULL, '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(10, 5, 'Customer', 'Penanganan Komplain', 'Jumlah komplain yang tidak terselesaikan (Unresolved)', 'Minimize', 'Kasus', '0', 0.00, 0.00, '10.00', '0', NULL, '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(11, 5, 'Internal Process', 'Penyelesaian Tugas Utama', 'Persentase penyelesaian project/tugas tepat waktu (On-time)', 'Maximize', '%', '0', 0.00, 0.00, '30.00', '100', NULL, '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(12, 5, 'Internal Process', 'Kualitas Kerja', 'Jumlah kesalahan (error/rework) major dalam pekerjaan', 'Minimize', 'Kasus', '0', 0.00, 0.00, '15.00', '0', NULL, '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(13, 5, 'Learning & Growth', 'Pengembangan Diri', 'Jumlah jam pelatihan / training yang diikuti', 'Maximize', 'Jam', '0', 0.00, 0.00, '10.00', '20', NULL, '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(14, 5, 'Learning & Growth', 'Kedisiplinan', 'Persentase kehadiran kerja (Absensi)', 'Maximize', '%', '0', 0.00, 0.00, '5.00', '98', NULL, '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(15, 6, 'Financial', 'Efisiensi Anggaran', 'Persentase penggunaan anggaran operasional sesuai budget', 'Minimize', '%', '0', 0.00, 0.00, '10.00', '100', NULL, '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(16, 6, 'Customer', 'Kepuasan Pelanggan (Internal/Eksternal)', 'Nilai rata-rata kepuasan user/klien (Survey)', 'Maximize', 'Skala', '0', 0.00, 0.00, '20.00', '4.5', NULL, '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(17, 6, 'Customer', 'Penanganan Komplain', 'Jumlah komplain yang tidak terselesaikan (Unresolved)', 'Minimize', 'Kasus', '0', 0.00, 0.00, '10.00', '0', NULL, '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(18, 6, 'Internal Process', 'Penyelesaian Tugas Utama', 'Persentase penyelesaian project/tugas tepat waktu (On-time)', 'Maximize', '%', '0', 0.00, 0.00, '30.00', '100', NULL, '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(19, 6, 'Internal Process', 'Kualitas Kerja', 'Jumlah kesalahan (error/rework) major dalam pekerjaan', 'Minimize', 'Kasus', '0', 0.00, 0.00, '15.00', '0', NULL, '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(20, 6, 'Learning & Growth', 'Pengembangan Diri', 'Jumlah jam pelatihan / training yang diikuti', 'Maximize', 'Jam', '0', 0.00, 0.00, '10.00', '20', NULL, '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(21, 6, 'Learning & Growth', 'Kedisiplinan', 'Persentase kehadiran kerja (Absensi)', 'Maximize', '%', '0', 0.00, 0.00, '5.00', '98', NULL, '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(22, 7, 'Financial', 'Efisiensi Anggaran', 'Persentase penggunaan anggaran operasional sesuai budget', 'Minimize', '%', '0', 0.00, 0.00, '10.00', '100', NULL, '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(23, 7, 'Customer', 'Kepuasan Pelanggan (Internal/Eksternal)', 'Nilai rata-rata kepuasan user/klien (Survey)', 'Maximize', 'Skala', '0', 0.00, 0.00, '20.00', '4.5', NULL, '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(24, 7, 'Customer', 'Penanganan Komplain', 'Jumlah komplain yang tidak terselesaikan (Unresolved)', 'Minimize', 'Kasus', '0', 0.00, 0.00, '10.00', '0', NULL, '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(25, 7, 'Internal Process', 'Penyelesaian Tugas Utama', 'Persentase penyelesaian project/tugas tepat waktu (On-time)', 'Maximize', '%', '0', 0.00, 0.00, '30.00', '100', NULL, '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(26, 7, 'Internal Process', 'Kualitas Kerja', 'Jumlah kesalahan (error/rework) major dalam pekerjaan', 'Minimize', 'Kasus', '0', 0.00, 0.00, '15.00', '0', NULL, '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(27, 7, 'Learning & Growth', 'Pengembangan Diri', 'Jumlah jam pelatihan / training yang diikuti', 'Maximize', 'Jam', '0', 0.00, 0.00, '10.00', '20', NULL, '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(28, 7, 'Learning & Growth', 'Kedisiplinan', 'Persentase kehadiran kerja (Absensi)', 'Maximize', '%', '0', 0.00, 0.00, '5.00', '98', NULL, '2025-12-18 18:44:51', '2025-12-18 18:44:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_scores`
--

CREATE TABLE `kpi_scores` (
  `id_kpi_score` bigint(20) UNSIGNED NOT NULL,
  `kpi_item_id` bigint(20) UNSIGNED NOT NULL,
  `tipe_periode` enum('SEMESTER','BULAN') NOT NULL,
  `nama_periode` varchar(255) NOT NULL,
  `bulan_urutan` int(11) DEFAULT NULL,
  `target` varchar(255) NOT NULL,
  `realisasi` varchar(255) DEFAULT NULL,
  `skor` decimal(8,2) NOT NULL DEFAULT 0.00,
  `skor_akhir` decimal(8,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kpi_scores`
--

INSERT INTO `kpi_scores` (`id_kpi_score`, `kpi_item_id`, `tipe_periode`, `nama_periode`, `bulan_urutan`, `target`, `realisasi`, `skor`, `skor_akhir`, `created_at`, `updated_at`) VALUES
(1, 1, 'SEMESTER', 'Semester 1', NULL, '100', '0', '0.00', '0.00', '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(2, 2, 'SEMESTER', 'Semester 1', NULL, '4.5', '0', '0.00', '0.00', '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(3, 3, 'SEMESTER', 'Semester 1', NULL, '0', '0', '0.00', '0.00', '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(4, 4, 'SEMESTER', 'Semester 1', NULL, '100', '0', '0.00', '0.00', '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(5, 5, 'SEMESTER', 'Semester 1', NULL, '0', '0', '0.00', '0.00', '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(6, 6, 'SEMESTER', 'Semester 1', NULL, '20', '0', '0.00', '0.00', '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(7, 7, 'SEMESTER', 'Semester 1', NULL, '98', '0', '0.00', '0.00', '2025-12-18 00:31:49', '2025-12-18 00:31:49'),
(8, 8, 'SEMESTER', 'Semester 1', NULL, '100', '0', '0.00', '0.00', '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(9, 9, 'SEMESTER', 'Semester 1', NULL, '4.5', '0', '0.00', '0.00', '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(10, 10, 'SEMESTER', 'Semester 1', NULL, '0', '0', '0.00', '0.00', '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(11, 11, 'SEMESTER', 'Semester 1', NULL, '100', '0', '0.00', '0.00', '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(12, 12, 'SEMESTER', 'Semester 1', NULL, '0', '0', '0.00', '0.00', '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(13, 13, 'SEMESTER', 'Semester 1', NULL, '20', '0', '0.00', '0.00', '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(14, 14, 'SEMESTER', 'Semester 1', NULL, '98', '0', '0.00', '0.00', '2025-12-18 00:33:24', '2025-12-18 00:33:24'),
(15, 15, 'SEMESTER', 'Semester 1', NULL, '100', '0', '0.00', '0.00', '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(16, 16, 'SEMESTER', 'Semester 1', NULL, '4.5', '0', '0.00', '0.00', '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(17, 17, 'SEMESTER', 'Semester 1', NULL, '0', '0', '0.00', '0.00', '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(18, 18, 'SEMESTER', 'Semester 1', NULL, '100', '0', '0.00', '0.00', '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(19, 19, 'SEMESTER', 'Semester 1', NULL, '0', '0', '0.00', '0.00', '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(20, 20, 'SEMESTER', 'Semester 1', NULL, '20', '0', '0.00', '0.00', '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(21, 21, 'SEMESTER', 'Semester 1', NULL, '98', '0', '0.00', '0.00', '2025-12-18 00:33:36', '2025-12-18 00:33:36'),
(22, 22, 'SEMESTER', 'Semester 1', NULL, '100', '0', '0.00', '0.00', '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(23, 23, 'SEMESTER', 'Semester 1', NULL, '4.5', '0', '0.00', '0.00', '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(24, 24, 'SEMESTER', 'Semester 1', NULL, '0', '0', '0.00', '0.00', '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(25, 25, 'SEMESTER', 'Semester 1', NULL, '100', '0', '0.00', '0.00', '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(26, 26, 'SEMESTER', 'Semester 1', NULL, '0', '0', '0.00', '0.00', '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(27, 27, 'SEMESTER', 'Semester 1', NULL, '20', '0', '0.00', '0.00', '2025-12-18 18:44:51', '2025-12-18 18:44:51'),
(28, 28, 'SEMESTER', 'Semester 1', NULL, '98', '0', '0.00', '0.00', '2025-12-18 18:44:51', '2025-12-18 18:44:51');

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
(1, '2025_12_17_000000_create_rekrutmen_daily_table', 1),
(2, '2025_12_17_074546_create_kpi_tables', 2),
(3, '2025_12_18_042424_add_columns_to_kpi_items_table', 3),
(4, '2025_12_20_022325_add_status_to_posisi_table', 4);

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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pekerjaan`
--

INSERT INTO `pekerjaan` (`id_pekerjaan`, `id_karyawan`, `Jabatan`, `Bagian`, `Departement`, `Divisi`, `Unit`, `Jenis_Kontrak`, `Perjanjian`, `Lokasi_Kerja`, `created_at`, `updated_at`) VALUES
(1, 3, 'General Manager (GM)', 'Sales & Marketing', 'MARKETING & SALES', 'Marketing', 'Factory 1', 'PKWTT', 'Tetap', 'Central Java - Pati', '2025-12-10 06:54:30', NULL),
(2, 5, 'Staff', 'Web Dev', 'BUSINESS DEVELOPMENT', 'HRD', 'Factory 1', NULL, 'Kontrak', 'Central Java - Pati', '2025-12-12 03:17:08', NULL),
(4, 8, 'Staff', 'Marketing', 'FINANCE & ACCOUNTING', 'Finance & Accounting', 'Factory 1', 'PKWT', 'Kontrak', 'Central Java - Pati', '2025-12-14 08:42:52', NULL),
(5, 9, 'Staff', 'Sales', 'MARKETING & SALES', 'Div. Marketing & Sales', 'Factory 1', 'PKWTT', 'Tetap', 'South Sumatra - Lampung', '2025-12-14 10:21:21', NULL),
(6, 2, 'Staff', 'finance', 'FINANCE & ACCOUNTING', 'Finance & Accounting', 'Factory 1', 'PKWT', 'Kontrak', 'Central Java - Pati', '2025-12-14 14:03:43', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemberkasan`
--

CREATE TABLE `pemberkasan` (
  `id_pemberkasan` int(11) NOT NULL,
  `kandidat_id` int(11) DEFAULT NULL,
  `follow_up` text DEFAULT NULL,
  `kandidat_kirim_berkas` date DEFAULT NULL,
  `selesai_recruitment` date DEFAULT NULL,
  `selesai_skgk_finance` date DEFAULT NULL,
  `selesai_ttd_manager_hrd` date DEFAULT NULL,
  `selesai_ttd_user` date DEFAULT NULL,
  `selesai_ttd_direktur` date DEFAULT NULL,
  `jadwal_ttd_kontrak` date DEFAULT NULL,
  `background_checking` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemberkasan`
--

INSERT INTO `pemberkasan` (`id_pemberkasan`, `kandidat_id`, `follow_up`, `kandidat_kirim_berkas`, `selesai_recruitment`, `selesai_skgk_finance`, `selesai_ttd_manager_hrd`, `selesai_ttd_user`, `selesai_ttd_direktur`, `jadwal_ttd_kontrak`, `background_checking`, `created_at`, `updated_at`) VALUES
(1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-17 00:46:58', '2025-12-17 00:46:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendidikan`
--

CREATE TABLE `pendidikan` (
  `id_pendidikan` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Pendidikan_Terakhir` enum('SD','SLTP','SLTA','DIPLOMA I','DIPLOMA II','DIPLOMA III','DIPLOMA IV','S1','S2') DEFAULT NULL,
  `Nama_Lengkap_Tempat_Pendidikan_Terakhir` varchar(255) DEFAULT NULL,
  `Jurusan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendidikan`
--

INSERT INTO `pendidikan` (`id_pendidikan`, `id_karyawan`, `Pendidikan_Terakhir`, `Nama_Lengkap_Tempat_Pendidikan_Terakhir`, `Jurusan`, `created_at`, `updated_at`) VALUES
(1, 3, 'S2', 'Universitas Diponegoro', 'Manajemen', '2025-12-10 06:59:33', NULL),
(2, 5, 'S1', 'Universitas PGRI Semarang', 'Informatika', '2025-12-12 03:17:08', NULL),
(4, 8, 'S1', 'Universitas Negeri Semarang', 'Manajemen', '2025-12-14 08:42:52', NULL),
(5, 9, 'S1', 'Universitas Diponegoro', 'Manajemen', '2025-12-14 10:21:22', NULL),
(6, 2, 'S1', 'Universitas Sebelas Maret', 'Akuntansi', '2025-12-14 15:11:34', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `perusahaan`
--

CREATE TABLE `perusahaan` (
  `id_perusahaan` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Perusahaan` enum('CV BERKAH NEGERI MULIA','PT INTI DUNIA MANDIRI','PT SOCHA INTI INFORMATIKA','PT TIMUR SEMESTA ABADI','PT WADJA INTI MULIA','PT WADJA INTI MULIA PERSADA','PT WADJA KARYA DUNIA','TAMANSARI EQUESTRIAN PARK') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `perusahaan`
--

INSERT INTO `perusahaan` (`id_perusahaan`, `id_karyawan`, `Perusahaan`, `created_at`, `updated_at`) VALUES
(1, 3, 'PT WADJA INTI MULIA', '2025-12-10 07:04:07', NULL),
(2, 5, 'PT WADJA KARYA DUNIA', '2025-12-12 03:17:08', NULL),
(4, 8, 'PT WADJA INTI MULIA', '2025-12-14 08:42:52', NULL),
(5, 9, 'PT WADJA KARYA DUNIA', '2025-12-14 10:21:22', NULL),
(6, 2, 'PT WADJA KARYA DUNIA', '2025-12-14 14:34:16', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `posisi`
--

CREATE TABLE `posisi` (
  `id_posisi` int(11) NOT NULL,
  `nama_posisi` varchar(150) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `posisi`
--

INSERT INTO `posisi` (`id_posisi`, `nama_posisi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Software Engineer', 'aktif', '2025-12-16 21:53:11', '2025-12-16 21:53:11'),
(2, 'Quality Assurance', 'aktif', '2025-12-16 21:53:11', '2025-12-16 21:53:11'),
(3, 'Product Manager', 'aktif', '2025-12-16 21:53:11', '2025-12-16 21:53:11'),
(4, 'UI/UX Designer', 'aktif', '2025-12-16 21:53:11', '2025-12-16 21:53:11'),
(5, 'Sales Tangerang', 'aktif', '2025-12-16 21:53:11', '2025-12-17 00:44:46'),
(6, 'Smoke Test Position', 'aktif', '2025-12-16 23:26:27', '2025-12-16 23:26:27'),
(7, 'Sales Pati', 'aktif', '2025-12-16 23:26:27', '2025-12-16 23:33:47'),
(11, 'Sales Bali', 'aktif', '2025-12-16 23:33:58', '2025-12-16 23:33:58'),
(12, 'Sales Bandung', 'aktif', '2025-12-17 19:03:24', '2025-12-17 19:03:24'),
(13, 'Sales Jakarta', 'aktif', '2025-12-18 20:09:45', '2025-12-18 20:09:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `proses_rekrutmen`
--

CREATE TABLE `proses_rekrutmen` (
  `id_proses_rekrutmen` int(11) NOT NULL,
  `kandidat_id` int(11) NOT NULL,
  `cv_lolos` tinyint(1) DEFAULT NULL,
  `tanggal_cv` date DEFAULT NULL,
  `psikotes_lolos` tinyint(1) DEFAULT NULL,
  `tanggal_psikotes` date DEFAULT NULL,
  `tes_kompetensi_lolos` tinyint(1) DEFAULT NULL,
  `tanggal_tes_kompetensi` date DEFAULT NULL,
  `interview_hr_lolos` tinyint(1) DEFAULT NULL,
  `tanggal_interview_hr` date DEFAULT NULL,
  `interview_user_lolos` tinyint(1) DEFAULT NULL,
  `tanggal_interview_user` date DEFAULT NULL,
  `tahap_terakhir` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekrutmen_daily`
--

CREATE TABLE `rekrutmen_daily` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `posisi_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `total_pelamar` int(11) NOT NULL DEFAULT 0,
  `lolos_cv` int(11) NOT NULL DEFAULT 0,
  `lolos_psikotes` int(11) NOT NULL DEFAULT 0,
  `lolos_kompetensi` int(11) NOT NULL DEFAULT 0,
  `lolos_hr` int(11) NOT NULL DEFAULT 0,
  `lolos_user` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `rekrutmen_daily`
--

INSERT INTO `rekrutmen_daily` (`id`, `posisi_id`, `date`, `total_pelamar`, `lolos_cv`, `lolos_psikotes`, `lolos_kompetensi`, `lolos_hr`, `lolos_user`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-11-27', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:22', '2025-12-16 21:53:22'),
(2, 2, '2025-11-27', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:22', '2025-12-16 21:53:22'),
(3, 3, '2025-11-27', 1, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:23', '2025-12-16 21:53:23'),
(4, 4, '2025-11-27', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:23', '2025-12-16 21:53:23'),
(5, 5, '2025-11-27', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:23', '2025-12-16 21:53:23'),
(6, 1, '2025-11-28', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:23', '2025-12-16 21:53:23'),
(7, 2, '2025-11-28', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:23', '2025-12-16 21:53:23'),
(8, 3, '2025-11-28', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:23', '2025-12-16 21:53:23'),
(9, 4, '2025-11-28', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:23', '2025-12-16 21:53:23'),
(10, 5, '2025-11-28', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:23', '2025-12-16 21:53:23'),
(11, 1, '2025-11-29', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:23', '2025-12-16 21:53:23'),
(12, 2, '2025-11-29', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:24', '2025-12-16 21:53:24'),
(13, 3, '2025-11-29', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:24', '2025-12-16 21:53:24'),
(14, 4, '2025-11-29', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:24', '2025-12-16 21:53:24'),
(15, 5, '2025-11-29', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:24', '2025-12-16 21:53:24'),
(16, 1, '2025-11-30', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:24', '2025-12-16 21:53:24'),
(17, 2, '2025-11-30', 1, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:24', '2025-12-16 21:53:24'),
(18, 3, '2025-11-30', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:24', '2025-12-16 21:53:24'),
(19, 4, '2025-11-30', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:24', '2025-12-16 21:53:24'),
(20, 5, '2025-11-30', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:24', '2025-12-16 21:53:24'),
(21, 1, '2025-12-01', 11, 0, 0, 0, 0, 0, '', 3, '2025-12-16 21:53:25', '2025-12-17 00:41:41'),
(22, 2, '2025-12-01', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:25', '2025-12-16 21:53:25'),
(23, 3, '2025-12-01', 1, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:25', '2025-12-16 21:53:25'),
(24, 4, '2025-12-01', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:25', '2025-12-16 21:53:25'),
(25, 5, '2025-12-01', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:25', '2025-12-16 21:53:25'),
(26, 1, '2025-12-02', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:25', '2025-12-16 21:53:25'),
(27, 2, '2025-12-02', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:25', '2025-12-16 21:53:25'),
(28, 3, '2025-12-02', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:25', '2025-12-16 21:53:25'),
(29, 4, '2025-12-02', 1, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:25', '2025-12-16 21:53:25'),
(30, 5, '2025-12-02', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:25', '2025-12-16 21:53:25'),
(31, 1, '2025-12-03', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:25', '2025-12-16 21:53:25'),
(32, 2, '2025-12-03', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(33, 3, '2025-12-03', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(34, 4, '2025-12-03', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(35, 5, '2025-12-03', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(36, 1, '2025-12-04', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(37, 2, '2025-12-04', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(38, 3, '2025-12-04', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(39, 4, '2025-12-04', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(40, 5, '2025-12-04', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(41, 1, '2025-12-05', 1, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(42, 2, '2025-12-05', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(43, 3, '2025-12-05', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(44, 4, '2025-12-05', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(45, 5, '2025-12-05', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(46, 1, '2025-12-06', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(47, 2, '2025-12-06', 1, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(48, 3, '2025-12-06', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(49, 4, '2025-12-06', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:26', '2025-12-16 21:53:26'),
(50, 5, '2025-12-06', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:27', '2025-12-16 21:53:27'),
(51, 1, '2025-12-07', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:27', '2025-12-16 21:53:27'),
(52, 2, '2025-12-07', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:27', '2025-12-16 21:53:27'),
(53, 3, '2025-12-07', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:27', '2025-12-16 21:53:27'),
(54, 4, '2025-12-07', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:27', '2025-12-16 21:53:27'),
(55, 5, '2025-12-07', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:27', '2025-12-16 21:53:27'),
(56, 1, '2025-12-08', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:27', '2025-12-16 21:53:27'),
(57, 2, '2025-12-08', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:27', '2025-12-16 21:53:27'),
(58, 3, '2025-12-08', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:27', '2025-12-16 21:53:27'),
(59, 4, '2025-12-08', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:27', '2025-12-16 21:53:27'),
(60, 5, '2025-12-08', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:28', '2025-12-16 21:53:28'),
(61, 1, '2025-12-09', 1, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:28', '2025-12-16 21:53:28'),
(62, 2, '2025-12-09', 1, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:28', '2025-12-16 21:53:28'),
(63, 3, '2025-12-09', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:28', '2025-12-16 21:53:28'),
(64, 4, '2025-12-09', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:28', '2025-12-16 21:53:28'),
(65, 5, '2025-12-09', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:28', '2025-12-16 21:53:28'),
(66, 1, '2025-12-10', 1, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:28', '2025-12-16 21:53:28'),
(67, 2, '2025-12-10', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:28', '2025-12-16 21:53:28'),
(68, 3, '2025-12-10', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(69, 4, '2025-12-10', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(70, 5, '2025-12-10', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(71, 1, '2025-12-11', 1, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(72, 2, '2025-12-11', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(73, 3, '2025-12-11', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(74, 4, '2025-12-11', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(75, 5, '2025-12-11', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(76, 1, '2025-12-12', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(77, 2, '2025-12-12', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(78, 3, '2025-12-12', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(79, 4, '2025-12-12', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(80, 5, '2025-12-12', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(81, 1, '2025-12-13', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(82, 2, '2025-12-13', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:29', '2025-12-16 21:53:29'),
(83, 3, '2025-12-13', 1, 0, 0, 0, 0, 0, NULL, 3, '2025-12-16 21:53:29', '2025-12-18 20:08:04'),
(84, 4, '2025-12-13', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(85, 5, '2025-12-13', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(86, 1, '2025-12-14', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(87, 2, '2025-12-14', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(88, 3, '2025-12-14', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(89, 4, '2025-12-14', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(90, 5, '2025-12-14', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(91, 1, '2025-12-15', 5, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(92, 2, '2025-12-15', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(93, 3, '2025-12-15', 3, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(94, 4, '2025-12-15', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(95, 5, '2025-12-15', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:30', '2025-12-16 21:53:30'),
(96, 1, '2025-12-16', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:31', '2025-12-16 21:53:31'),
(97, 2, '2025-12-16', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:31', '2025-12-16 21:53:31'),
(98, 3, '2025-12-16', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:31', '2025-12-16 21:53:31'),
(99, 4, '2025-12-16', 1, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:31', '2025-12-16 21:53:31'),
(100, 5, '2025-12-16', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:31', '2025-12-16 21:53:31'),
(102, 2, '2025-12-17', 4, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:31', '2025-12-16 21:53:31'),
(103, 3, '2025-12-17', 0, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:31', '2025-12-16 21:53:31'),
(104, 4, '2025-12-17', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:31', '2025-12-16 21:53:31'),
(105, 5, '2025-12-17', 2, 0, 0, 0, 0, 0, '', 1, '2025-12-16 21:53:31', '2025-12-16 21:53:31'),
(107, 7, '2025-12-01', 4, 0, 0, 0, 0, 0, '', 3, '2025-12-17 00:42:49', '2025-12-17 00:43:03'),
(108, 5, '2025-12-18', 2, 0, 0, 0, 0, 0, '', 3, '2025-12-17 19:35:04', '2025-12-17 19:35:04'),
(109, 13, '2025-12-01', 0, 11, 0, 0, 0, 0, NULL, 3, '2025-12-18 20:10:20', '2025-12-18 21:20:02'),
(110, 13, '2025-12-07', 20, 0, 0, 0, 0, 0, NULL, 3, '2025-12-18 20:44:32', '2025-12-18 23:59:25'),
(111, 4, '2025-12-19', 1, 0, 0, 0, 0, 0, NULL, 3, '2025-12-18 21:13:45', '2025-12-18 21:13:45'),
(112, 3, '2025-12-19', 4, 0, 0, 0, 0, 0, NULL, 3, '2025-12-18 23:01:22', '2025-12-18 23:01:30'),
(113, 11, '2025-12-09', 0, 0, 0, 0, 0, 0, NULL, 3, '2025-12-18 23:36:12', '2025-12-18 23:36:12'),
(114, 12, '2025-12-08', 5, 0, 0, 0, 0, 0, NULL, 3, '2025-12-18 23:36:19', '2025-12-18 23:36:19'),
(115, 13, '2025-12-05', 5, 0, 0, 0, 0, 0, NULL, 3, '2025-12-18 23:44:56', '2025-12-18 23:44:56'),
(116, 13, '2025-12-02', 0, 9, 0, 0, 0, 0, NULL, 3, '2025-12-19 21:30:45', '2025-12-19 21:30:45');

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
('5v63kTWKGUSK43iLHMZfKn2R1WCz0xmmSbPMv3ZW', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNXJXcVBPSlZ2TkV3TEJXOHZ1ODJDYjFsUG1vRjdnSU1HREpnRkJqcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zaWduaW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1766195274),
('Zh0pvn3UjcRNmYVEJ9mUZJs9zFrTWL4gqUKQ3sgB', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaWFReG03Q2lqZTFMQnVqVUVBQnJaUmxCVE1PZWxKa1FpSEVWU2ZXVSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC93aWctcmVrcnV0bWVuL2NyZWF0ZSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1766212152);

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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `status_karyawan`
--

INSERT INTO `status_karyawan` (`id_status`, `id_karyawan`, `Tanggal_Non_Aktif`, `Alasan_Non_Aktif`, `Ijazah_Dikembalikan`, `Bulan`, `created_at`, `updated_at`) VALUES
(1, 3, NULL, '-', NULL, NULL, '2025-12-10 07:05:00', NULL),
(3, 8, '2025-10-16', 'Cuti Melahirkan', 'Tidak', NULL, '2025-12-14 08:42:52', NULL),
(4, 2, '2025-12-29', 'Cuti', 'Tidak', NULL, '2025-12-14 14:03:42', NULL);

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

-- --------------------------------------------------------

--
-- Struktur dari tabel `wig_rekrutmen`
--

CREATE TABLE `wig_rekrutmen` (
  `id_wig_rekrutmen` int(11) NOT NULL,
  `posisi_id` int(11) NOT NULL,
  `fpk_user` date DEFAULT NULL,
  `fpk_hrd` date DEFAULT NULL,
  `fpk_finance` date DEFAULT NULL,
  `fpk_direktur` date DEFAULT NULL,
  `tanggal_publish_loker` date DEFAULT NULL,
  `total_pelamar` int(11) DEFAULT NULL,
  `total_lead` int(11) DEFAULT NULL,
  `total_lolos_psikotes` int(11) DEFAULT NULL,
  `tanggal_tes_kompetensi` date DEFAULT NULL,
  `dipanggil_tes_kompetensi` int(11) DEFAULT NULL,
  `hadir_tes_kompetensi` int(11) DEFAULT NULL,
  `lolos_tes_kompetensi` int(11) DEFAULT NULL,
  `tanggal_interview_hr` date DEFAULT NULL,
  `dipanggil_interview_hr` int(11) DEFAULT NULL,
  `hadir_interview_hr` int(11) DEFAULT NULL,
  `lolos_interview_hr` int(11) DEFAULT NULL,
  `tanggal_interview_user` date DEFAULT NULL,
  `dipanggil_interview_user` int(11) DEFAULT NULL,
  `hadir_interview_user` int(11) DEFAULT NULL,
  `lolos_interview_user` int(11) DEFAULT NULL,
  `tanggal_bg_checking` date DEFAULT NULL,
  `tanggal_mulai_training` date DEFAULT NULL,
  `tanggal_selesai_training` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `biaya_promote_ta`
--
ALTER TABLE `biaya_promote_ta`
  ADD PRIMARY KEY (`id_biaya_promote_ta`);

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
-- Indeks untuk tabel `interview_hr`
--
ALTER TABLE `interview_hr`
  ADD PRIMARY KEY (`id_interview_hr`),
  ADD KEY `kandidat_id` (`kandidat_id`);

--
-- Indeks untuk tabel `kandidat`
--
ALTER TABLE `kandidat`
  ADD PRIMARY KEY (`id_kandidat`),
  ADD KEY `posisi_id` (`posisi_id`);

--
-- Indeks untuk tabel `kandidat_lanjut_user`
--
ALTER TABLE `kandidat_lanjut_user`
  ADD PRIMARY KEY (`id_kandidat_lanjut_user`),
  ADD KEY `kandidat_id` (`kandidat_id`);

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
-- Indeks untuk tabel `kpi_assessments`
--
ALTER TABLE `kpi_assessments`
  ADD PRIMARY KEY (`id_kpi_assessment`),
  ADD KEY `kpi_assessments_karyawan_id_foreign` (`karyawan_id`);

--
-- Indeks untuk tabel `kpi_items`
--
ALTER TABLE `kpi_items`
  ADD PRIMARY KEY (`id_kpi_item`),
  ADD KEY `kpi_items_kpi_assessment_id_foreign` (`kpi_assessment_id`);

--
-- Indeks untuk tabel `kpi_scores`
--
ALTER TABLE `kpi_scores`
  ADD PRIMARY KEY (`id_kpi_score`),
  ADD KEY `kpi_scores_kpi_item_id_foreign` (`kpi_item_id`);

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
-- Indeks untuk tabel `pemberkasan`
--
ALTER TABLE `pemberkasan`
  ADD PRIMARY KEY (`id_pemberkasan`),
  ADD KEY `kandidat_id` (`kandidat_id`);

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
-- Indeks untuk tabel `posisi`
--
ALTER TABLE `posisi`
  ADD PRIMARY KEY (`id_posisi`);

--
-- Indeks untuk tabel `proses_rekrutmen`
--
ALTER TABLE `proses_rekrutmen`
  ADD PRIMARY KEY (`id_proses_rekrutmen`),
  ADD KEY `kandidat_id` (`kandidat_id`);

--
-- Indeks untuk tabel `rekrutmen_daily`
--
ALTER TABLE `rekrutmen_daily`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rekrutmen_daily_posisi_id_date_unique` (`posisi_id`,`date`);

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
-- Indeks untuk tabel `wig_rekrutmen`
--
ALTER TABLE `wig_rekrutmen`
  ADD PRIMARY KEY (`id_wig_rekrutmen`),
  ADD KEY `posisi_id` (`posisi_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `biaya_promote_ta`
--
ALTER TABLE `biaya_promote_ta`
  MODIFY `id_biaya_promote_ta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `bpjs`
--
ALTER TABLE `bpjs`
  MODIFY `id_bpjs` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `data_keluarga`
--
ALTER TABLE `data_keluarga`
  MODIFY `id_keluarga` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `interview_hr`
--
ALTER TABLE `interview_hr`
  MODIFY `id_interview_hr` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kandidat`
--
ALTER TABLE `kandidat`
  MODIFY `id_kandidat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kandidat_lanjut_user`
--
ALTER TABLE `kandidat_lanjut_user`
  MODIFY `id_kandidat_lanjut_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `kontrak`
--
ALTER TABLE `kontrak`
  MODIFY `id_kontrak` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `kpi_assessments`
--
ALTER TABLE `kpi_assessments`
  MODIFY `id_kpi_assessment` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kpi_items`
--
ALTER TABLE `kpi_items`
  MODIFY `id_kpi_item` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `kpi_scores`
--
ALTER TABLE `kpi_scores`
  MODIFY `id_kpi_score` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pekerjaan`
--
ALTER TABLE `pekerjaan`
  MODIFY `id_pekerjaan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pemberkasan`
--
ALTER TABLE `pemberkasan`
  MODIFY `id_pemberkasan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  MODIFY `id_pendidikan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id_perusahaan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `posisi`
--
ALTER TABLE `posisi`
  MODIFY `id_posisi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `proses_rekrutmen`
--
ALTER TABLE `proses_rekrutmen`
  MODIFY `id_proses_rekrutmen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rekrutmen_daily`
--
ALTER TABLE `rekrutmen_daily`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT untuk tabel `status_karyawan`
--
ALTER TABLE `status_karyawan`
  MODIFY `id_status` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `wig_rekrutmen`
--
ALTER TABLE `wig_rekrutmen`
  MODIFY `id_wig_rekrutmen` int(11) NOT NULL AUTO_INCREMENT;

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
-- Ketidakleluasaan untuk tabel `interview_hr`
--
ALTER TABLE `interview_hr`
  ADD CONSTRAINT `interview_hr_ibfk_1` FOREIGN KEY (`kandidat_id`) REFERENCES `kandidat` (`id_kandidat`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kandidat`
--
ALTER TABLE `kandidat`
  ADD CONSTRAINT `kandidat_ibfk_1` FOREIGN KEY (`posisi_id`) REFERENCES `posisi` (`id_posisi`);

--
-- Ketidakleluasaan untuk tabel `kandidat_lanjut_user`
--
ALTER TABLE `kandidat_lanjut_user`
  ADD CONSTRAINT `kandidat_lanjut_user_ibfk_1` FOREIGN KEY (`kandidat_id`) REFERENCES `kandidat` (`id_kandidat`);

--
-- Ketidakleluasaan untuk tabel `kontrak`
--
ALTER TABLE `kontrak`
  ADD CONSTRAINT `kontrak_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`);

--
-- Ketidakleluasaan untuk tabel `kpi_assessments`
--
ALTER TABLE `kpi_assessments`
  ADD CONSTRAINT `kpi_assessments_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kpi_items`
--
ALTER TABLE `kpi_items`
  ADD CONSTRAINT `kpi_items_kpi_assessment_id_foreign` FOREIGN KEY (`kpi_assessment_id`) REFERENCES `kpi_assessments` (`id_kpi_assessment`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kpi_scores`
--
ALTER TABLE `kpi_scores`
  ADD CONSTRAINT `kpi_scores_kpi_item_id_foreign` FOREIGN KEY (`kpi_item_id`) REFERENCES `kpi_items` (`id_kpi_item`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pekerjaan`
--
ALTER TABLE `pekerjaan`
  ADD CONSTRAINT `pekerjaan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`);

--
-- Ketidakleluasaan untuk tabel `pemberkasan`
--
ALTER TABLE `pemberkasan`
  ADD CONSTRAINT `pemberkasan_ibfk_1` FOREIGN KEY (`kandidat_id`) REFERENCES `kandidat` (`id_kandidat`);

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
-- Ketidakleluasaan untuk tabel `proses_rekrutmen`
--
ALTER TABLE `proses_rekrutmen`
  ADD CONSTRAINT `proses_rekrutmen_ibfk_1` FOREIGN KEY (`kandidat_id`) REFERENCES `kandidat` (`id_kandidat`);

--
-- Ketidakleluasaan untuk tabel `rekrutmen_daily`
--
ALTER TABLE `rekrutmen_daily`
  ADD CONSTRAINT `rekrutmen_daily_posisi_id_foreign` FOREIGN KEY (`posisi_id`) REFERENCES `posisi` (`id_posisi`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `status_karyawan`
--
ALTER TABLE `status_karyawan`
  ADD CONSTRAINT `status_karyawan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`);

--
-- Ketidakleluasaan untuk tabel `wig_rekrutmen`
--
ALTER TABLE `wig_rekrutmen`
  ADD CONSTRAINT `wig_rekrutmen_ibfk_1` FOREIGN KEY (`posisi_id`) REFERENCES `posisi` (`id_posisi`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
