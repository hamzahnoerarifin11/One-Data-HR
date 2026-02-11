-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Feb 2026 pada 10.51
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bpjs`
--

INSERT INTO `bpjs` (`id_bpjs`, `id_karyawan`, `Status_BPJS_KT`, `Status_BPJS_KS`, `created_at`, `updated_at`) VALUES
(1, 3, 'Aktif', 'Aktif', '2025-12-10 06:55:33', '2025-12-29 04:16:51'),
(2, 5, 'Aktif', 'Aktif', '2025-12-12 03:17:08', '2025-12-29 04:16:51'),
(4, 8, 'Aktif', 'Tidak Aktif', '2025-12-14 08:42:52', '2025-12-29 04:16:51'),
(5, 9, 'Aktif', 'Aktif', '2025-12-14 10:21:22', '2025-12-29 04:16:51'),
(6, 2, 'Aktif', 'Aktif', '2025-12-14 14:03:42', '2025-12-29 06:07:59'),
(10, 242, 'Aktif', 'Aktif', '2026-01-09 07:22:27', '2026-01-09 07:22:27'),
(11, 18, 'Aktif', 'Aktif', '2026-01-12 08:43:23', '2026-01-12 08:43:23'),
(12, 82, 'Aktif', 'Aktif', '2026-01-12 08:44:30', '2026-01-12 08:44:30'),
(13, 14, NULL, NULL, '2026-01-19 03:52:11', '2026-01-19 03:52:11'),
(14, 92, NULL, NULL, '2026-01-19 06:21:30', '2026-01-19 06:21:30'),
(15, 654, NULL, NULL, '2026-01-19 07:08:26', '2026-01-19 07:08:26'),
(16, 16, NULL, NULL, '2026-01-19 07:10:33', '2026-01-19 07:10:33'),
(17, 256, NULL, NULL, '2026-01-19 07:11:27', '2026-01-19 07:11:27'),
(18, 395, 'Aktif', 'Aktif', '2026-01-24 02:16:12', '2026-01-24 02:16:12'),
(19, 593, NULL, NULL, '2026-01-31 03:06:54', '2026-01-31 03:06:54'),
(20, 903, NULL, NULL, '2026-01-31 03:48:41', '2026-01-31 03:48:41'),
(21, 6, 'Aktif', 'Aktif', '2026-02-02 03:45:40', '2026-02-02 03:45:40'),
(23, 7, NULL, NULL, '2026-02-04 02:39:00', '2026-02-04 02:39:00'),
(24, 8, NULL, NULL, '2026-02-06 02:18:41', '2026-02-06 02:18:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `companies`
--

INSERT INTO `companies` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'PT Wadja Karya Dunia', '2026-01-22 07:25:19', '2026-01-22 07:25:19'),
(3, 'PT. One Data Indonesia', '2026-01-22 07:29:46', '2026-01-22 07:29:46'),
(4, 'PT. Teknologi Maju', '2026-01-22 07:29:46', '2026-01-22 07:29:46'),
(5, 'PT. Digital Solutions', '2026-01-22 07:29:46', '2026-01-22 07:29:46'),
(6, 'PT. Inovasi Teknologi', '2026-01-22 07:29:46', '2026-01-22 07:29:46'),
(7, 'PT. Sistem Informasi', '2026-01-22 07:29:46', '2026-01-22 07:29:46'),
(8, 'PT Wadja Inti Mulia', '2026-01-23 07:48:40', '2026-01-23 07:48:40');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_keluarga`
--

INSERT INTO `data_keluarga` (`id_keluarga`, `id_karyawan`, `Nama_Ayah_Kandung`, `Nama_Ibu_Kandung`, `Nama_Lengkap_Suami_Istri`, `NIK_KTP_Suami_Istri`, `Tempat_Lahir_Suami_Istri`, `Tanggal_Lahir_Suami_Istri`, `Nomor_Telepon_Suami_Istri`, `Pendidikan_Terakhir_Suami_Istri`, `anak`, `created_at`, `updated_at`) VALUES
(1, 3, 'Asep', 'Dini', 'Putri', '66666666666', 'Jakarta', '1985-07-15', '081333333333', 'S1', '[{\"nama\":\"Martin Manewar\",\"tempat_lahir\":\"Jakarta\",\"tanggal_lahir\":\"2000-05-15\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"S1\"}]', '2025-12-10 06:49:53', '2025-12-29 04:17:24'),
(2, 5, 'Ahmad Budi', 'Siti', NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2025-12-12 03:17:08', '2026-01-19 07:27:28'),
(4, 8, 'Ayah Karyawan 1', 'Ibu Karyawan 1', 'Suami Karyawan 1', '22222222222', 'Pati', '1996-10-14', '081222222222', 'S1', '[{\"nama\":\"Anak 1 Karyawan 1\",\"tempat_lahir\":\"Pati\",\"tanggal_lahir\":\"2020-01-14\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"SD\"},{\"nama\":\"Anak 2 Karyawan 1\",\"tempat_lahir\":\"Pati\",\"tanggal_lahir\":\"2024-01-14\",\"jenis_kelamin\":\"P\",\"pendidikan\":\"Belum Sekolah\"}]', '2025-12-14 08:42:52', '2025-12-29 04:17:24'),
(5, 9, 'Ahmad Dahlan', 'Lusi', 'Mirai', '123456', 'Batam', '2002-10-14', '081222222222', 'S1', '[{\"nama\":\"Bas\",\"tempat_lahir\":\"Batam\",\"tanggal_lahir\":\"2024-01-14\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"Belum Sekolah\"}]', '2025-12-14 10:21:21', '2025-12-29 04:17:24'),
(6, 2, 'aa', 'bb', 'moji', '1234', 'Jakarta', '2001-10-28', '081432432432', 'SMA', '[{\"nama\":\"Dewa\",\"tempat_lahir\":\"Kudus\",\"tanggal_lahir\":\"2023-01-16\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"Belum Sekolah\"},{\"nama\":\"Dewi\",\"tempat_lahir\":\"Kudus\",\"tanggal_lahir\":\"2025-02-10\",\"jenis_kelamin\":\"P\",\"pendidikan\":\"Belum Sekolah\"}]', '2025-12-14 14:03:42', '2025-12-29 06:07:59'),
(10, 242, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-01-09 07:22:27', '2026-01-09 07:22:27'),
(12, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-01-12 08:43:23', '2026-01-12 08:43:23'),
(13, 82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-01-12 08:44:30', '2026-01-12 08:44:30'),
(14, 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-01-19 03:52:11', '2026-01-19 03:52:11'),
(15, 92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-01-19 06:21:30', '2026-01-19 06:21:30'),
(16, 654, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-01-19 07:08:26', '2026-01-19 07:08:26'),
(17, 16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-01-19 07:10:33', '2026-01-19 07:10:33'),
(18, 256, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-01-19 07:11:27', '2026-01-19 07:11:27'),
(19, 395, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-01-24 02:16:12', '2026-01-24 02:16:12'),
(20, 593, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-01-31 03:06:54', '2026-01-31 03:06:54'),
(21, 903, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-01-31 03:48:41', '2026-01-31 03:48:41'),
(22, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-02-02 03:45:40', '2026-02-02 03:45:40'),
(24, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-02-04 02:39:00', '2026-02-04 02:39:00'),
(25, 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-02-06 02:18:41', '2026-02-06 02:18:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `departments`
--

INSERT INTO `departments` (`id`, `company_id`, `division_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Departemen Pengembangan Software', '2026-01-22 07:33:06', '2026-01-22 07:33:06'),
(2, 2, 1, 'Departemen Sistem Informasi', '2026-01-22 07:33:06', '2026-01-22 07:33:06'),
(3, 2, 2, 'Departemen Keuangan', '2026-01-22 07:33:06', '2026-01-22 07:33:06'),
(4, 2, 2, 'Departemen Akuntansi', '2026-01-22 07:33:06', '2026-01-22 07:33:06'),
(5, 2, 3, 'Departemen SDM', '2026-01-22 07:33:06', '2026-01-22 07:33:06'),
(6, 2, 6, 'OD & HR 2', '2026-01-23 02:00:43', '2026-01-23 02:02:33'),
(8, 8, 8, 'Marketing', '2026-01-23 08:51:03', '2026-01-23 08:51:03'),
(9, 8, 9, 'OD & HR 2', '2026-02-05 07:49:02', '2026-02-05 07:49:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `divisions`
--

CREATE TABLE `divisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `divisions`
--

INSERT INTO `divisions` (`id`, `company_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 2, 'Divisi Teknologi Informasi', '2026-01-22 07:32:01', '2026-01-22 07:32:01'),
(2, 2, 'Divisi Keuangan', '2026-01-22 07:32:01', '2026-01-22 07:32:01'),
(3, 2, 'Divisi Sumber Daya Manusia', '2026-01-22 07:32:01', '2026-01-22 07:32:01'),
(4, 3, 'Divisi Pemasaran', '2026-01-22 07:32:01', '2026-01-22 07:32:01'),
(5, 3, 'Divisi Operasional', '2026-01-22 07:32:02', '2026-01-22 07:32:02'),
(6, 2, 'HR', '2026-01-22 07:51:06', '2026-01-22 07:51:06'),
(8, 8, 'Sales & Marketing', '2026-01-23 08:36:49', '2026-01-23 08:37:09'),
(9, 8, 'HR', '2026-02-05 07:48:29', '2026-02-05 07:48:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `interview_hr`
--

CREATE TABLE `interview_hr` (
  `id_interview_hr` int(11) NOT NULL,
  `kandidat_id` int(11) NOT NULL,
  `posisi_id` int(11) DEFAULT NULL,
  `hari_tanggal` date DEFAULT NULL,
  `nama_interviewer` varchar(150) DEFAULT NULL,
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
  `total` int(11) DEFAULT NULL,
  `hasil_akhir` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `interview_hr`
--

INSERT INTO `interview_hr` (`id_interview_hr`, `kandidat_id`, `posisi_id`, `hari_tanggal`, `nama_interviewer`, `model_wawancara`, `skor_profesional`, `catatan_profesional`, `skor_spiritual`, `catatan_spiritual`, `skor_learning`, `catatan_learning`, `skor_initiative`, `catatan_initiative`, `skor_komunikasi`, `catatan_komunikasi`, `skor_problem_solving`, `catatan_problem_solving`, `skor_teamwork`, `catatan_teamwork`, `catatan_tambahan`, `keputusan`, `total`, `hasil_akhir`, `created_at`, `updated_at`) VALUES
(17, 65, 1, '2026-01-20', 'BUDI', 'Offline', 3, NULL, 3, NULL, 4, NULL, 3, NULL, 2, NULL, 4, NULL, 3, NULL, NULL, 'DITERIMA', 22, NULL, '2026-01-20 03:05:42', '2026-01-20 03:05:42');

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
  `link_cv` text DEFAULT NULL,
  `file_pdf` varchar(255) DEFAULT NULL,
  `status_akhir` enum('CV Lolos','Psikotes Lolos','Tes Kompetensi Lolos','Interview HR Lolos','Interview User Lolos','Diterima','Tidak Lolos') DEFAULT 'CV Lolos',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tgl_lolos_cv` date DEFAULT NULL,
  `tgl_lolos_psikotes` date DEFAULT NULL,
  `tgl_lolos_kompetensi` date DEFAULT NULL,
  `tgl_lolos_hr` date DEFAULT NULL,
  `tgl_lolos_user` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kandidat`
--

INSERT INTO `kandidat` (`id_kandidat`, `nama`, `posisi_id`, `tanggal_melamar`, `sumber`, `link_cv`, `file_pdf`, `status_akhir`, `created_at`, `updated_at`, `tgl_lolos_cv`, `tgl_lolos_psikotes`, `tgl_lolos_kompetensi`, `tgl_lolos_hr`, `tgl_lolos_user`) VALUES
(65, 'Teguh Andromeda', 1, '2026-01-19', 'linkedin', NULL, '1768799037_KPI_220022105866_2026_(6).pdf', 'Tes Kompetensi Lolos', '2026-01-19 05:03:57', '2026-01-20 08:05:53', NULL, NULL, '2026-01-20', '2026-01-20', NULL),
(116, 'upip', 2, '2026-01-28', 'glints', NULL, NULL, 'Interview HR Lolos', '2026-01-28 06:25:54', '2026-01-28 06:26:26', NULL, NULL, NULL, '2026-01-28', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kandidat_lanjut_user`
--

CREATE TABLE `kandidat_lanjut_user` (
  `id_kandidat_lanjut_user` int(11) NOT NULL,
  `kandidat_id` int(11) DEFAULT NULL,
  `posisi_id` int(11) DEFAULT NULL,
  `tanggal_interview_hr` date DEFAULT NULL,
  `tanggal_penyerahan` date DEFAULT NULL,
  `detail_interview` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
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
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `atasan_id` bigint(20) DEFAULT NULL,
  `NIK` double DEFAULT NULL,
  `Status` enum('1','0') DEFAULT NULL,
  `Kode` enum('Aktif','Non Aktif') DEFAULT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `user_id`, `atasan_id`, `NIK`, `Status`, `Kode`, `Nama_Sesuai_KTP`, `NIK_KTP`, `Nama_Lengkap_Sesuai_Ijazah`, `Tempat_Lahir_Karyawan`, `Tanggal_Lahir_Karyawan`, `Umur_Karyawan`, `Jenis_Kelamin_Karyawan`, `Status_Pernikahan`, `Golongan_Darah`, `Nomor_Telepon_Aktif_Karyawan`, `Email`, `Alamat_KTP`, `RT`, `RW`, `Kelurahan_Desa`, `Kecamatan`, `Kabupaten_Kota`, `Provinsi`, `Alamat_Domisili`, `RT_Sesuai_Domisili`, `RW_Sesuai_Domisili`, `Kelurahan_Desa_Domisili`, `Kecamatan_Sesuai_Domisili`, `Kabupaten_Kota_Sesuai_Domisili`, `Provinsi_Sesuai_Domisili`, `Alamat_Lengkap`, `created_at`, `updated_at`) VALUES
(2, NULL, NULL, 220022307162, '1', 'Aktif', 'Yudha Adhitya', '3319021607890000', 'Yudha Adhitya', 'Kudus', '1989-07-16', '36 Tahun', 'L', 'Menikah', 'B', '81575075043', 'yudha.yycell@gmail.com', 'Dk.Randangan rt.002/003 Ds.Semirejo Kec.Gembong Kab.Pati', '2', '3', 'Semirejo', 'Gembong', 'Pati', 'Jawa Tengah', 'Dk.Randangan rt.002/003 Ds.Semirejo Kec.Gembong Kab.Pati', '002', '003', 'Semirejo', 'Gembong', 'Pati', 'Jawa Tengah', 'Dk. Randangan RT/RW 02/03 Desa Samirejo, Kec. Gembong, Kab. Pati', '2025-12-22 20:48:30', '2026-02-02 07:34:38'),
(3, NULL, NULL, 220021901558, '1', 'Aktif', 'Subroto, S.Farm., Apt', '3318190308850000', 'Subroto, S.Farm., Apt', 'Pati', '1985-08-03', '40 Tahun', 'L', 'Menikah', 'A', '82221204444', 'SUBROTOAPOTEKER@GMAIL.COM', 'Tayukulon', '4', '3', 'Tayukulon', 'Tayu', 'Pati', 'Jawa Tengah', 'Perum Kutoharjo', '001', '007', 'Kutoharjo', 'Pati', 'Pati', 'Jawa Tengah', 'Jl. Yudistira 6, No. 2, Perum Kutoharjo, Rt. 001/007, Kec. Pati, Kab. Pati', '2025-12-22 20:48:30', '2026-02-02 04:43:30'),
(6, NULL, NULL, 220022209070, '1', 'Aktif', 'Sona Ardhyan', '3275032001940020', 'Sona Ardhyan', 'Jakarta', '1998-11-18', '27 Tahun', 'L', 'Menikah', 'A', '83897776438', 'ardhyansona@gmail.com', 'Jl. Bulustalan Gang 3A No. 362C', '4', '3', 'BULUSTALAN', 'SEMARANG SELATAN', 'KOTA SEMARANG', 'JAWA TENGAH', 'Jl. Bulustalan Gang 3A No. 362C', '4', '3', 'BULUSTALAN', 'SEMARANG SELATAN', 'KOTA SEMARANG', 'JAWA TENGAH', 'Jl. Bulustalan Gang 3A No. 362C', '2026-02-02 03:45:40', '2026-02-02 06:14:09'),
(7, NULL, NULL, 120021803065, '1', 'Aktif', 'Bagus Aji', '3374110204790010', 'Bagus Aji', 'Selatpanjang-(Riau)', '1979-04-02', '46 Tahun', 'L', 'Menikah', 'A', '81226126797', 'bagusaji@gmail.com', NULL, '6', '5', NULL, NULL, NULL, NULL, NULL, '006', '007', NULL, NULL, NULL, NULL, NULL, '2026-02-04 02:39:00', '2026-02-08 09:29:32'),
(8, NULL, NULL, 120020304001, '1', 'Aktif', 'Abu Naim', '3318140210780000', 'Abu Naim', 'Pati', '1978-10-02', '47 Tahun', 'L', 'Menikah', NULL, '8122826537', 'abunaimku@yahoo.co.id', 'Ds. Tamansari', '01', '01', 'TAMANSARI', 'TLOGOWUNGU', 'KABUPATEN PATI', 'JAWA TENGAH', 'Ds. Tamansari', '01', '01', 'TAMANSARI', 'TLOGOWUNGU', 'KABUPATEN PATI', 'JAWA TENGAH', 'Ds. Tamansari', '2026-02-06 02:18:41', '2026-02-06 02:18:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kbi_assessments`
--

CREATE TABLE `kbi_assessments` (
  `id_kbi_assessment` bigint(20) UNSIGNED NOT NULL,
  `karyawan_id` bigint(20) UNSIGNED NOT NULL,
  `penilai_id` bigint(20) UNSIGNED NOT NULL,
  `tipe_penilai` enum('DIRI_SENDIRI','ATASAN','BAWAHAN') NOT NULL,
  `tahun` year(4) NOT NULL,
  `periode` varchar(255) NOT NULL DEFAULT 'Semester 1',
  `rata_rata_akhir` double NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'DRAFT',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kbi_items`
--

CREATE TABLE `kbi_items` (
  `id_kbi_item` bigint(20) UNSIGNED NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `perilaku` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kbi_scores`
--

CREATE TABLE `kbi_scores` (
  `id_kbi_score` bigint(20) UNSIGNED NOT NULL,
  `kbi_assessment_id` bigint(20) UNSIGNED NOT NULL,
  `kbi_item_id` bigint(20) UNSIGNED NOT NULL,
  `skor` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kontrak`
--

INSERT INTO `kontrak` (`id_kontrak`, `id_karyawan`, `Tanggal_Mulai_Tugas`, `PKWT_Berakhir`, `Tanggal_Diangkat_Menjadi_Karyawan_Tetap`, `Riwayat_Penempatan`, `Tanggal_Riwayat_Penempatan`, `Mutasi_Promosi_Demosi`, `Tanggal_Mutasi_Promosi_Demosi`, `Masa_Kerja`, `NO_PKWT_PERTAMA`, `NO_SK_PERTAMA`, `created_at`, `updated_at`) VALUES
(1, 3, '2021-03-05', '2020-02-15', '2020-03-15', 'Sales', '2017-07-15', 'Promosi', '2021-01-15', '4 Tahun 11 Bulan 2 Hari', '001/PKWT', '001/SK', '2025-12-10 06:58:20', '2026-02-07 06:14:45'),
(2, 5, '2025-11-24', '2026-05-02', NULL, NULL, NULL, NULL, NULL, '0 Tahun 2 Bulan 3 Hari', NULL, NULL, '2025-12-12 03:17:08', '2026-01-27 03:55:21'),
(6, 2, '2018-03-08', '2026-01-14', '2025-12-15', 'Akuntan', '2025-12-23', 'Promosi', '2025-12-09', '7 Tahun 10 Bulan 27 Hari', 'Npkwt11', 'Nsk11', '2025-12-14 15:19:47', '2026-02-04 01:20:09'),
(7, 8, '2021-03-15', NULL, NULL, NULL, NULL, NULL, NULL, '4 Tahun 10 Bulan 24 Hari', NULL, NULL, '2025-12-16 03:25:51', '2026-02-08 09:26:22'),
(12, 242, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-09 07:22:28', '2026-01-09 07:22:28'),
(13, 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-12 08:43:23', '2026-01-12 08:43:23'),
(14, 82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-12 08:44:30', '2026-01-12 08:44:30'),
(15, 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-19 03:52:11', '2026-01-19 03:52:11'),
(16, 92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-19 06:21:30', '2026-01-19 06:21:30'),
(17, 654, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-19 07:08:26', '2026-01-19 07:08:26'),
(18, 16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-19 07:10:33', '2026-01-19 07:10:33'),
(19, 256, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-19 07:11:27', '2026-01-19 07:11:27'),
(20, 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-23 04:28:38', '2026-01-23 04:28:38'),
(21, 395, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-24 02:16:12', '2026-01-24 02:16:12'),
(22, 593, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-31 03:06:54', '2026-01-31 03:06:54'),
(23, 903, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-01-31 03:48:41', '2026-01-31 03:48:41'),
(24, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-02-02 03:45:40', '2026-02-02 03:45:40'),
(26, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-02-04 02:39:00', '2026-02-04 02:39:00'),
(27, 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, '2026-02-06 02:18:41', '2026-02-06 02:18:41');

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
  `grade` varchar(50) DEFAULT NULL,
  `grade_akhir` varchar(20) DEFAULT NULL,
  `status` enum('DRAFT','SUBMITTED','APPROVED','FINAL') NOT NULL DEFAULT 'DRAFT',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kpi_assessments`
--

INSERT INTO `kpi_assessments` (`id_kpi_assessment`, `karyawan_id`, `penilai_id`, `tahun`, `periode`, `tanggal_penilaian`, `total_skor_akhir`, `grade`, `grade_akhir`, `status`, `created_at`, `updated_at`) VALUES
(46, 4, 27, '2026', 'Tahunan', NULL, 23.00, 'Low', NULL, 'SUBMITTED', '2026-02-02 08:41:05', '2026-02-04 02:32:13'),
(57, 3, NULL, '2025', 'Tahunan', NULL, 0.00, NULL, NULL, 'DRAFT', '2026-02-05 04:28:34', '2026-02-05 04:28:34'),
(70, 6, NULL, '2026', 'Tahunan', NULL, 0.00, NULL, NULL, 'DRAFT', '2026-02-06 03:37:58', '2026-02-06 03:37:58');

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
  `units` varchar(255) DEFAULT NULL,
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

INSERT INTO `kpi_items` (`id_kpi_item`, `kpi_assessment_id`, `perspektif`, `key_result_area`, `key_performance_indicator`, `polaritas`, `units`, `realisasi`, `skor`, `skor_akhir`, `bobot`, `target`, `target_tahunan`, `created_at`, `updated_at`) VALUES
(61, 29, 'Financial', 'Pengelolaan Kas & Bank', 'HAHAHA', 'Maximize', '%', '0', 0.00, 0.00, 100.00, '98', NULL, '2026-01-23 01:56:37', '2026-01-23 01:56:37'),
(68, 30, 'Financial', 'Produktivitas & Output', 'Fitur baru selesai per kuartal', 'Maximize', '%', '0', 0.00, 0.00, 100.00, '5', NULL, '2026-01-23 02:49:23', '2026-01-23 02:49:23'),
(70, 34, 'Financial', 'Pengelolaan Keuangan & Kas', 'Efisiensi arus kas (cash flow efficiency)', 'Maximize', '%', '0', 0.00, 0.00, 100.00, '70', NULL, '2026-01-27 04:13:30', '2026-01-27 04:13:30'),
(71, 35, 'Financial', 'Pengelolaan Kas & Bank', 'Ketepatan pencatatan transaksi kas & bank ≥ 98%', 'Maximize', '%', '0', 0.00, 0.00, 100.00, '100', NULL, '2026-01-28 04:02:16', '2026-01-28 04:02:16'),
(74, 46, 'Financial', 'apa ya', 'adalah pokoknya', 'Maximize', 'presentase', '0', 0.00, 0.00, 100.00, '100', NULL, '2026-02-02 08:41:05', '2026-02-03 06:47:26'),
(85, 58, 'Financial', 'Pengembangan fitur baru aplikasi', 'Jumlah fitur baru yang dirilis per kuartal (Target: 3–4 fitur)', 'MAX', NULL, '0', 0.00, 0.00, 100.00, '80', NULL, '2026-02-05 04:42:29', '2026-02-05 04:42:29'),
(86, 59, 'Financial', 'Pengembangan fitur baru aplikasi', 'Jumlah fitur baru yang dirilis per kuartal (Target: 3–4 fitur)', 'MAX', NULL, '0', 0.00, 0.00, 100.00, '80', NULL, '2026-02-05 04:42:29', '2026-02-05 04:42:29'),
(88, 61, 'Learning & Growth', 'Pengembangan fitur baru aplikasi', 'Jumlah fitur baru yang dirilis per kuartal (Target: 3–4 fitur)', 'MAX', NULL, '0', 0.00, 0.00, 100.00, '100', NULL, '2026-02-05 06:50:34', '2026-02-05 06:50:34'),
(90, 62, 'Internal Business Process', 'Pengembangan fitur baru aplikasi', 'Jumlah fitur baru yang dirilis per kuartal (Target: 3–4 fitur)', 'MAX', NULL, '0', 0.00, 0.00, 100.00, '100', NULL, '2026-02-05 07:52:18', '2026-02-05 07:52:18'),
(91, 63, 'Internal Business Process', 'Pengembangan fitur baru aplikasi', 'Jumlah fitur baru yang dirilis per kuartal (Target: 3–4 fitur)', 'MAX', NULL, '0', 0.00, 0.00, 100.00, '100', NULL, '2026-02-05 07:52:18', '2026-02-05 07:52:18'),
(102, 70, 'Financial', 'Penambahan fitur', 'pengembangan', 'Maximize', 'presentase', '0', 0.00, 0.00, 100.00, '0', NULL, '2026-02-06 03:38:16', '2026-02-06 03:38:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_perspectives`
--

CREATE TABLE `kpi_perspectives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kpi_perspectives`
--

INSERT INTO `kpi_perspectives` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Internal Business Process', 1, '2026-02-04 03:08:42', '2026-02-04 03:08:52'),
(2, 'Financial', 1, '2026-02-04 03:09:07', '2026-02-05 07:58:54'),
(3, 'Learning & Growth', 1, '2026-02-04 03:09:21', '2026-02-04 03:09:21');

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
  `skor` decimal(8,2) DEFAULT NULL,
  `skor_akhir` decimal(10,0) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `target_smt1` double DEFAULT NULL,
  `real_smt1` double DEFAULT NULL,
  `adjustment_real_smt1` double DEFAULT NULL,
  `adjustment_smt1` double DEFAULT NULL,
  `target_jul` double DEFAULT NULL,
  `real_jul` double DEFAULT NULL,
  `target_aug` double DEFAULT NULL,
  `real_aug` double DEFAULT NULL,
  `target_sep` double DEFAULT NULL,
  `real_sep` double DEFAULT NULL,
  `target_okt` double DEFAULT NULL,
  `real_okt` double DEFAULT NULL,
  `target_nov` double DEFAULT NULL,
  `real_nov` double DEFAULT NULL,
  `target_des` double DEFAULT NULL,
  `real_des` double DEFAULT NULL,
  `total_target_smt2` double DEFAULT NULL,
  `total_real_smt2` double DEFAULT NULL,
  `adjustment_target_smt2` double DEFAULT NULL,
  `adjustment_real_smt2` double DEFAULT NULL,
  `adjustment_smt2` double DEFAULT NULL,
  `target_jan` double DEFAULT NULL,
  `real_jan` double DEFAULT NULL,
  `target_feb` double DEFAULT NULL,
  `real_feb` double DEFAULT NULL,
  `target_mar` double DEFAULT NULL,
  `real_mar` double DEFAULT NULL,
  `target_apr` double DEFAULT NULL,
  `real_apr` double DEFAULT NULL,
  `target_mei` double DEFAULT NULL,
  `real_mei` double DEFAULT NULL,
  `target_jun` double DEFAULT NULL,
  `real_jun` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kpi_scores`
--

INSERT INTO `kpi_scores` (`id_kpi_score`, `kpi_item_id`, `tipe_periode`, `nama_periode`, `bulan_urutan`, `target`, `realisasi`, `skor`, `skor_akhir`, `created_at`, `updated_at`, `target_smt1`, `real_smt1`, `adjustment_real_smt1`, `adjustment_smt1`, `target_jul`, `real_jul`, `target_aug`, `real_aug`, `target_sep`, `real_sep`, `target_okt`, `real_okt`, `target_nov`, `real_nov`, `target_des`, `real_des`, `total_target_smt2`, `total_real_smt2`, `adjustment_target_smt2`, `adjustment_real_smt2`, `adjustment_smt2`, `target_jan`, `real_jan`, `target_feb`, `real_feb`, `target_mar`, `real_mar`, `target_apr`, `real_apr`, `target_mei`, `real_mei`, `target_jun`, `real_jun`) VALUES
(58, 61, 'SEMESTER', 'Semester 1', NULL, '98', '0', NULL, 46, '2026-01-23 01:56:37', '2026-01-23 01:57:58', 98, 90, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 68, 'SEMESTER', 'Semester 1', NULL, '5', '0', NULL, 40, '2026-01-23 02:49:23', '2026-01-23 08:06:11', 5, 5, 4, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 70, 'SEMESTER', 'Semester 1', NULL, '70', '0', NULL, 36, '2026-01-27 04:13:30', '2026-01-27 04:14:01', 70, 50, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 71, 'SEMESTER', 'Semester 1', NULL, '100', '0', NULL, 89, '2026-01-28 04:02:16', '2026-01-28 04:04:12', 100, 90, 90, NULL, 100, 80, 100, 91, 100, 85, 100, 79, 100, 90, 100, 87, 100, 95, 100, 87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 74, 'SEMESTER', 'Semester 1', NULL, '100', '0', NULL, 23, '2026-02-02 08:41:05', '2026-02-04 02:32:13', 600, 270, NULL, NULL, 100, 0, 100, 0, 100, 0, 100, 0, 100, 0, 100, 0, 600, 0, NULL, NULL, NULL, 100, 80, 100, 90, 100, 100, 100, 0, 100, 0, 100, 0),
(82, 85, 'SEMESTER', 'Semester 1', NULL, '0', '0', NULL, NULL, '2026-02-05 04:42:29', '2026-02-05 04:42:29', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 86, 'SEMESTER', 'Semester 1', NULL, '0', '0', NULL, NULL, '2026-02-05 04:42:29', '2026-02-05 04:42:29', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 88, 'SEMESTER', 'Semester 1', NULL, '0', '0', NULL, NULL, '2026-02-05 06:50:34', '2026-02-05 06:50:34', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 90, 'SEMESTER', 'Semester 1', NULL, '0', '0', NULL, NULL, '2026-02-05 07:52:18', '2026-02-05 07:52:18', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 91, 'SEMESTER', 'Semester 1', NULL, '0', '0', NULL, NULL, '2026-02-05 07:52:18', '2026-02-05 07:52:18', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 102, 'SEMESTER', 'Semester 1', NULL, '0', '0', NULL, NULL, '2026-02-06 03:38:16', '2026-02-06 03:38:16', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `levels`
--

CREATE TABLE `levels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `level_order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `levels`
--

INSERT INTO `levels` (`id`, `name`, `level_order`, `created_at`, `updated_at`) VALUES
(1, 'Director', 1, NULL, NULL),
(2, 'Senior Manager', 2, NULL, '2026-01-29 04:15:38'),
(3, 'Manager', 3, NULL, NULL),
(4, 'Supervisor', 4, NULL, NULL),
(5, 'Section Head', 5, NULL, NULL),
(6, 'Staff', 6, NULL, '2026-01-29 04:05:22');

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
(4, '2025_12_20_022325_add_status_to_posisi_table', 4),
(5, '2025_12_22_132758_add_monthly_columns_to_kpi_scores_table', 5),
(6, '2025_12_30_040824_add_total_smt2_to_kpi_scores_table', 6),
(7, '2025_12_30_042614_add_adjustment_columns_to_kpi_scores', 7),
(8, '2025_12_30_102541_add_real_adjustment_to_kpi_scores', 8),
(9, '2025_12_31_060700_create_kbi_tables', 9);

-- --------------------------------------------------------

--
-- Struktur dari tabel `onboarding_karyawan`
--

CREATE TABLE `onboarding_karyawan` (
  `id_onboarding` int(11) NOT NULL,
  `kandidat_id` int(11) NOT NULL,
  `posisi_id` int(11) NOT NULL,
  `pendidikan_terakhir` varchar(150) DEFAULT NULL,
  `nama_sekolah` varchar(150) DEFAULT NULL,
  `alamat_domisili` text DEFAULT NULL,
  `nomor_wa` varchar(20) DEFAULT NULL,
  `jadwal_ttd_kontrak` date DEFAULT NULL,
  `tanggal_resign` date DEFAULT NULL,
  `masa_kerja_hari` int(11) DEFAULT NULL,
  `status_turnover` enum('turnover','lolos','belum') DEFAULT 'belum',
  `tanggal_lolos_probation` date DEFAULT NULL,
  `alasan_resign` text DEFAULT NULL,
  `id_card_status` enum('proses','jadi','diambil') DEFAULT 'proses',
  `id_card_proses` date DEFAULT NULL,
  `id_card_jadi` date DEFAULT NULL,
  `id_card_diambil` date DEFAULT NULL,
  `no_rekening` varchar(50) DEFAULT NULL,
  `fingerprint_status` enum('belum','sudah') DEFAULT 'belum',
  `fingerprint_sudah` date DEFAULT NULL,
  `link_data_dikirim_hr` date DEFAULT NULL,
  `link_data_dilengkapi_karyawan` date DEFAULT NULL,
  `ijazah_diterima_hr` date DEFAULT NULL,
  `kontrak_ttd_pusat` date DEFAULT NULL,
  `visi_misi` tinyint(1) DEFAULT 0,
  `wadja_philosophy` tinyint(1) DEFAULT 0,
  `sejarah_perusahaan` tinyint(1) DEFAULT 0,
  `kondisi_perizinan` tinyint(1) DEFAULT 0,
  `tata_tertib` tinyint(1) DEFAULT 0,
  `bpjs` tinyint(1) DEFAULT 0,
  `k3` tinyint(1) DEFAULT 0,
  `tanggal_induction` date DEFAULT NULL,
  `evaluasi` text DEFAULT NULL,
  `status_onboarding` enum('draft','progress','selesai') DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pekerjaan`
--

CREATE TABLE `pekerjaan` (
  `id_pekerjaan` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `level_id` bigint(20) UNSIGNED NOT NULL,
  `Jabatan` varchar(255) DEFAULT NULL,
  `Jenis_Kontrak` enum('PKWT','PKWTT') DEFAULT NULL,
  `Perjanjian` enum('Harian Lepas','Kontrak','Tetap') DEFAULT NULL,
  `Lokasi_Kerja` enum('Central Java - Pati','Central Java - Pekalongan','Central Java - Purwokerto','Central Java - Surakarta','Central Java - Magelang','Central Java - Semarang','Central Java - Tegal','West Java - Purwakarta','West Java - Cianjur','West Java - Bandung','West Java - Bogor','West Java - Cirebon','West Java - Tangerang','West Java - Bekasi','West Java - Tasikmalaya','Banten - Serang','East Java - Bojonegoro','East Java - Jember','East Java - Madiun','East Java - Madura','East Java - Sidoarjo','Bali - Bali','East Java - Malang','East Java - Kediri','South Sumatra - Lampung','South Sumatra - Palembang','DIY - Yogyakarta','South Sulawesi - Makassar') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pekerjaan`
--

INSERT INTO `pekerjaan` (`id_pekerjaan`, `id_karyawan`, `company_id`, `division_id`, `department_id`, `unit_id`, `position_id`, `level_id`, `Jabatan`, `Jenis_Kontrak`, `Perjanjian`, `Lokasi_Kerja`, `created_at`, `updated_at`) VALUES
(57, 6, 2, 6, 6, 6, NULL, 4, 'Staff', NULL, NULL, 'Central Java - Pati', '2026-02-02 06:57:11', '2026-02-08 09:32:30'),
(58, 3, 2, 6, 6, 6, NULL, 3, 'Manager HR', 'PKWT', 'Kontrak', 'Central Java - Pati', '2026-02-02 06:57:49', '2026-02-07 06:14:45'),
(59, 2, 2, 1, 1, 1, NULL, 6, 'UI/UX', NULL, NULL, 'Central Java - Pati', '2026-02-02 07:34:38', '2026-02-04 01:20:09'),
(61, 7, 2, 6, 6, 6, NULL, 6, 'Staff', NULL, NULL, 'Central Java - Pati', '2026-02-04 02:39:00', '2026-02-08 09:29:58'),
(62, 8, 2, NULL, NULL, NULL, NULL, 1, 'Direktur Utama WKD', NULL, NULL, 'Central Java - Pati', '2026-02-06 02:18:41', '2026-02-08 09:26:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemberkasan`
--

CREATE TABLE `pemberkasan` (
  `id_pemberkasan` int(11) NOT NULL,
  `kandidat_id` int(11) DEFAULT NULL,
  `posisi_id` int(11) DEFAULT NULL,
  `follow_up` date DEFAULT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendidikan`
--

INSERT INTO `pendidikan` (`id_pendidikan`, `id_karyawan`, `Pendidikan_Terakhir`, `Nama_Lengkap_Tempat_Pendidikan_Terakhir`, `Jurusan`, `created_at`, `updated_at`) VALUES
(1, 3, 'S2', 'Universitas Diponegoro', 'Manajemen', '2025-12-10 06:59:33', '2025-12-29 04:19:43'),
(2, 5, 'S1', 'Universitas PGRI Semarang', 'Informatika', '2025-12-12 03:17:08', '2025-12-29 04:19:43'),
(4, 8, 'S1', 'Universitas Negeri Semarang', 'Manajemen', '2025-12-14 08:42:52', '2025-12-29 04:19:43'),
(5, 9, 'S1', 'Universitas Diponegoro', 'Manajemen', '2025-12-14 10:21:22', '2025-12-29 04:19:43'),
(6, 2, 'S1', 'Universitas Diponegoro', 'Akuntansi', '2025-12-14 15:11:34', '2025-12-29 06:07:59'),
(11, 242, NULL, NULL, NULL, '2026-01-09 07:22:28', '2026-01-09 07:22:28'),
(12, 18, NULL, NULL, NULL, '2026-01-12 08:43:23', '2026-01-12 08:43:23'),
(13, 82, NULL, NULL, NULL, '2026-01-12 08:44:30', '2026-01-12 08:44:30'),
(14, 14, NULL, NULL, NULL, '2026-01-19 03:52:11', '2026-01-19 03:52:11'),
(15, 92, NULL, NULL, NULL, '2026-01-19 06:21:30', '2026-01-19 06:21:30'),
(16, 654, NULL, NULL, NULL, '2026-01-19 07:08:26', '2026-01-19 07:08:26'),
(17, 16, NULL, NULL, NULL, '2026-01-19 07:10:33', '2026-01-19 07:10:33'),
(18, 256, NULL, NULL, NULL, '2026-01-19 07:11:27', '2026-01-19 07:11:27'),
(19, 395, NULL, NULL, NULL, '2026-01-24 02:16:12', '2026-01-24 02:16:12'),
(20, 593, NULL, NULL, NULL, '2026-01-31 03:06:54', '2026-01-31 03:06:54'),
(21, 903, NULL, NULL, NULL, '2026-01-31 03:48:41', '2026-01-31 03:48:41'),
(22, 6, NULL, NULL, NULL, '2026-02-02 03:45:40', '2026-02-02 03:45:40'),
(24, 7, NULL, NULL, NULL, '2026-02-04 02:39:00', '2026-02-04 02:39:00'),
(25, 8, NULL, NULL, NULL, '2026-02-06 02:18:41', '2026-02-06 02:18:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `perusahaan`
--

CREATE TABLE `perusahaan` (
  `id_perusahaan` bigint(20) NOT NULL,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Perusahaan` enum('CV BERKAH NEGERI MULIA','PT INTI DUNIA MANDIRI','PT SOCHA INTI INFORMATIKA','PT TIMUR SEMESTA ABADI','PT WADJA INTI MULIA','PT WADJA INTI MULIA PERSADA','PT WADJA KARYA DUNIA','TAMANSARI EQUESTRIAN PARK') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `perusahaan`
--

INSERT INTO `perusahaan` (`id_perusahaan`, `id_karyawan`, `Perusahaan`, `created_at`, `updated_at`) VALUES
(1, 3, 'PT WADJA INTI MULIA', '2025-12-10 07:04:07', '2025-12-29 04:20:19'),
(2, 5, 'PT WADJA KARYA DUNIA', '2025-12-12 03:17:08', '2025-12-29 04:20:19'),
(4, 8, 'PT WADJA INTI MULIA', '2025-12-14 08:42:52', '2025-12-29 04:20:19'),
(5, 9, 'PT WADJA KARYA DUNIA', '2025-12-14 10:21:22', '2025-12-29 04:20:19'),
(6, 2, 'PT WADJA KARYA DUNIA', '2025-12-14 14:34:16', '2025-12-29 04:20:19'),
(10, 242, NULL, '2026-01-09 07:22:28', '2026-01-09 07:22:28'),
(11, 18, 'PT WADJA KARYA DUNIA', '2026-01-12 08:43:23', '2026-01-19 08:14:36'),
(12, 82, 'PT WADJA INTI MULIA', '2026-01-12 08:44:30', '2026-01-12 08:44:30'),
(13, 14, NULL, '2026-01-19 03:52:11', '2026-01-19 03:52:11'),
(14, 92, 'PT WADJA INTI MULIA', '2026-01-19 06:21:30', '2026-01-19 07:46:08'),
(15, 654, NULL, '2026-01-19 07:08:26', '2026-01-19 07:08:26'),
(16, 16, 'PT WADJA KARYA DUNIA', '2026-01-19 07:10:33', '2026-01-19 08:12:37'),
(17, 256, NULL, '2026-01-19 07:11:27', '2026-01-19 07:11:27'),
(18, 395, NULL, '2026-01-24 02:16:12', '2026-01-24 02:16:12'),
(19, 593, NULL, '2026-01-31 03:06:54', '2026-01-31 03:06:54'),
(20, 903, NULL, '2026-01-31 03:48:41', '2026-01-31 03:48:41'),
(21, 6, NULL, '2026-02-02 03:45:40', '2026-02-02 03:45:40'),
(23, 7, NULL, '2026-02-04 02:39:00', '2026-02-04 02:39:00'),
(24, 8, NULL, '2026-02-06 02:18:41', '2026-02-06 02:18:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `posisi`
--

CREATE TABLE `posisi` (
  `id_posisi` int(11) NOT NULL,
  `nama_posisi` varchar(150) NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `progress_rekrutmen` varchar(50) DEFAULT 'Menerima Kandidat',
  `total_pelamar` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `posisi`
--

INSERT INTO `posisi` (`id_posisi`, `nama_posisi`, `status`, `created_at`, `updated_at`, `progress_rekrutmen`, `total_pelamar`) VALUES
(1, 'Software Engineer', 'Aktif', '2025-12-16 21:53:11', '2026-01-20 08:05:53', 'Pre Interview', 1),
(2, 'Quality Assurance', 'Aktif', '2025-12-16 21:53:11', '2026-01-28 06:26:26', 'Interview User', 1),
(3, 'Product Manager', 'Aktif', '2025-12-16 21:53:11', '2025-12-31 01:41:57', 'Pre Interview', 1),
(4, 'UI/UX Designer', 'Aktif', '2025-12-16 21:53:11', '2026-01-02 07:25:12', 'Interview HR', 2),
(5, 'Sales Tangerang', 'Aktif', '2025-12-16 21:53:11', '2025-12-31 01:42:28', 'Pre Interview', 1),
(6, 'Smoke Test Position', 'Aktif', '2025-12-16 23:26:27', '2025-12-31 03:27:12', 'Menerima Kandidat', 0),
(7, 'Sales Pati', 'Aktif', '2025-12-16 23:26:27', '2026-01-02 04:19:19', 'Pemberkasan', 3),
(11, 'Sales Bali', 'Aktif', '2025-12-16 23:33:58', '2026-01-09 03:35:31', 'Interview HR', 1),
(12, 'Sales Bandung', 'Aktif', '2025-12-17 19:03:24', '2026-01-05 02:04:27', 'Interview HR', 1),
(13, 'Sales Jakarta', 'Aktif', '2025-12-18 20:09:45', '2026-01-09 03:34:09', 'Pre Interview', 1),
(15, 'Sales Lampung', 'Aktif', '2025-12-21 20:06:55', '2025-12-21 20:06:55', 'Menerima Kandidat', 0),
(21, 'Sales Makassar', 'Nonaktif', '2025-12-21 21:03:08', '2025-12-31 03:26:08', 'Tidak Menerima Kandidat', 0),
(22, 'Manager Information Technology', 'Aktif', '2026-01-10 04:25:22', '2026-01-10 04:25:22', 'Menerima Kandidat', 0),
(23, 'Backend Developer', 'Aktif', '2026-01-20 04:47:11', '2026-01-20 04:47:11', 'Menerima Kandidat', 0),
(24, 'Frontend Developer', 'Aktif', '2026-01-20 04:47:12', '2026-01-20 04:47:12', 'Menerima Kandidat', 0),
(25, 'HR Staff', 'Aktif', '2026-01-20 04:47:12', '2026-01-20 04:47:12', 'Menerima Kandidat', 0),
(26, 'Sales Manager', 'Aktif', '2026-01-20 04:47:12', '2026-01-20 04:47:12', 'Menerima Kandidat', 0),
(27, 'Backend Developer', 'Aktif', '2026-01-20 04:48:23', '2026-01-20 04:48:23', 'Menerima Kandidat', 0),
(28, 'Frontend Developer', 'Aktif', '2026-01-20 04:48:23', '2026-01-20 04:48:23', 'Menerima Kandidat', 0),
(29, 'HR Staff', 'Aktif', '2026-01-20 04:48:23', '2026-01-20 04:48:23', 'Menerima Kandidat', 0),
(30, 'Sales Manager', 'Aktif', '2026-01-20 04:48:23', '2026-01-20 04:48:23', 'Menerima Kandidat', 0),
(31, 'Backend Developer', 'Aktif', '2026-01-20 04:48:45', '2026-01-20 04:48:45', 'Menerima Kandidat', 0),
(32, 'Frontend Developer', 'Aktif', '2026-01-20 04:48:45', '2026-01-20 04:48:45', 'Menerima Kandidat', 0),
(33, 'HR Staff', 'Aktif', '2026-01-20 04:48:45', '2026-01-20 04:48:45', 'Menerima Kandidat', 0),
(34, 'Sales Manager', 'Aktif', '2026-01-20 04:48:45', '2026-01-20 04:48:45', 'Menerima Kandidat', 0),
(35, 'Backend Developer', 'Aktif', '2026-01-20 04:48:58', '2026-01-20 04:48:58', 'Menerima Kandidat', 0),
(36, 'Frontend Developer', 'Aktif', '2026-01-20 04:48:58', '2026-01-20 04:48:58', 'Menerima Kandidat', 0),
(37, 'HR Staff', 'Aktif', '2026-01-20 04:48:58', '2026-01-20 04:48:58', 'Menerima Kandidat', 0),
(38, 'Sales Manager', 'Aktif', '2026-01-20 04:48:58', '2026-01-20 04:48:58', 'Menerima Kandidat', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `positions`
--

CREATE TABLE `positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `positions`
--

INSERT INTO `positions` (`id`, `company_id`, `division_id`, `department_id`, `unit_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1, 1, 'Frontend Developer Senior', '2026-01-22 07:34:37', '2026-01-22 07:34:37'),
(2, 2, 1, 1, 1, 'Frontend Developer Junior', '2026-01-22 07:34:37', '2026-01-22 07:34:37'),
(3, 2, 1, 1, 2, 'Backend Developer Senior', '2026-01-22 07:34:38', '2026-01-22 07:34:38'),
(4, 2, 1, 1, 2, 'Backend Developer Junior', '2026-01-22 07:34:38', '2026-01-22 07:34:38'),
(5, 2, 1, 2, 3, 'Database Administrator', '2026-01-22 07:34:38', '2026-01-22 07:34:38'),
(7, 2, 2, 3, 5, 'Payroll Specialist', '2026-01-22 07:34:38', '2026-01-22 07:34:38'),
(9, 2, 6, 6, 6, 'Staff', '2026-01-23 03:32:55', '2026-01-23 03:32:55'),
(10, 8, 8, 8, 7, 'Manager', '2026-01-24 02:33:53', '2026-01-24 02:34:13'),
(11, 2, 6, 6, 6, 'Manager', '2026-01-26 04:08:46', '2026-01-26 04:39:47'),
(12, 2, 1, 1, 1, 'General Manager', '2026-01-26 04:40:19', '2026-01-26 04:40:19'),
(13, 2, 2, 3, 5, 'General Manager', '2026-01-26 04:43:27', '2026-01-26 04:43:27'),
(14, 2, 6, 6, 6, 'Staff HR TA', '2026-01-26 08:16:29', '2026-01-26 08:16:29'),
(15, 2, 1, 1, 2, 'manager', '2026-01-27 03:54:58', '2026-01-27 03:54:58'),
(16, 8, 8, 8, 7, 'Staff', '2026-01-28 04:20:54', '2026-01-28 04:20:54');

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

--
-- Dumping data untuk tabel `proses_rekrutmen`
--

INSERT INTO `proses_rekrutmen` (`id_proses_rekrutmen`, `kandidat_id`, `cv_lolos`, `tanggal_cv`, `psikotes_lolos`, `tanggal_psikotes`, `tes_kompetensi_lolos`, `tanggal_tes_kompetensi`, `interview_hr_lolos`, `tanggal_interview_hr`, `interview_user_lolos`, `tanggal_interview_user`, `tahap_terakhir`, `created_at`, `updated_at`) VALUES
(1, 66, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(2, 67, 1, '2026-01-07', 1, '2026-01-12', 1, '2026-01-17', 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(3, 68, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(4, 69, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(5, 70, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(6, 71, 1, '2026-01-06', 1, '2026-01-13', 1, '2026-01-16', 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(7, 72, 1, '2026-01-10', 1, '2026-01-15', 1, '2026-01-16', 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(8, 73, 1, '2026-01-07', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(9, 74, 1, '2026-01-01', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(10, 75, 1, '2026-01-02', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(11, 76, 1, '2026-01-09', 1, '2026-01-14', 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(12, 77, 1, '2026-01-06', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(13, 78, 1, '2026-01-07', 1, '2026-01-15', 1, '2026-01-17', 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(14, 79, 1, '2026-01-03', 1, '2026-01-14', 1, '2026-01-16', 1, '2026-01-17', 1, '2026-01-19', NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(15, 80, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(16, 81, 1, '2026-01-05', 1, '2026-01-13', 1, '2026-01-17', 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(17, 82, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(18, 83, 1, '2026-01-08', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(19, 84, 1, '2026-01-09', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(20, 85, 1, '2026-01-01', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(21, 86, 1, '2025-12-31', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(22, 87, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(23, 88, 1, '2026-01-01', 1, '2026-01-12', 1, '2026-01-16', 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(24, 89, 1, '2026-01-10', 1, '2026-01-13', 1, '2026-01-16', 1, '2026-01-17', 1, '2026-01-19', NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(25, 90, 1, '2026-01-01', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(26, 91, 1, '2026-01-08', 1, '2026-01-15', 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(27, 92, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(28, 93, 1, '2026-01-07', 1, '2026-01-11', 1, '2026-01-17', 1, '2026-01-18', 1, '2026-01-19', NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(29, 94, 1, '2026-01-04', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(30, 95, 1, '2025-12-31', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(31, 96, 1, '2026-01-09', 1, '2026-01-15', 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(32, 97, 1, '2026-01-09', 1, '2026-01-13', 1, '2026-01-16', 1, '2026-01-18', 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(33, 98, 1, '2026-01-01', 1, '2026-01-15', 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(34, 99, 1, '2026-01-08', 1, '2026-01-11', 1, '2026-01-16', 1, '2026-01-18', 1, '2026-01-19', NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(35, 100, 1, '2026-01-08', 1, '2026-01-13', 1, '2026-01-16', 1, '2026-01-17', 1, '2026-01-19', NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(36, 101, 1, '2026-01-08', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(37, 102, 1, '2026-01-05', 1, '2026-01-14', 1, '2026-01-16', 1, '2026-01-17', 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(38, 103, 1, '2026-01-10', 1, '2026-01-15', 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(39, 104, 1, '2026-01-08', 1, '2026-01-13', 1, '2026-01-16', 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(40, 105, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(41, 106, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(42, 107, 1, '2026-01-10', 1, '2026-01-13', 1, '2026-01-17', 1, '2026-01-18', 1, '2026-01-19', NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(43, 108, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(44, 109, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(45, 110, 0, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(46, 111, 1, '2026-01-09', 1, '2026-01-11', 1, '2026-01-16', 1, '2026-01-18', 1, '2026-01-19', NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(47, 112, 1, '2026-01-05', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(48, 113, 1, '2026-01-09', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(49, 114, 1, '2026-01-10', 0, NULL, 0, NULL, 0, NULL, 0, NULL, NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58'),
(50, 115, 1, '2026-01-01', 1, '2026-01-12', 1, '2026-01-17', 1, '2026-01-17', 1, '2026-01-19', NULL, '2026-01-20 04:48:58', '2026-01-20 04:48:58');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `rekrutmen_daily`
--

INSERT INTO `rekrutmen_daily` (`id`, `posisi_id`, `date`, `total_pelamar`, `lolos_cv`, `lolos_psikotes`, `lolos_kompetensi`, `lolos_hr`, `lolos_user`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, '2025-12-22', 0, 1, 0, 0, 0, 0, NULL, NULL, '2025-12-31 07:57:36', '2026-01-02 02:05:57'),
(2, 3, '2025-12-03', 0, 0, 1, 0, 0, 0, NULL, NULL, '2025-12-31 07:57:36', '2026-01-02 02:05:58'),
(3, 4, '2025-12-31', 0, 1, 0, 0, 0, 0, NULL, NULL, '2025-12-31 07:57:36', '2026-01-02 02:05:57'),
(4, 7, '2025-12-30', 0, 0, 0, 0, 0, 1, NULL, NULL, '2025-12-31 07:57:36', '2026-01-02 02:05:58'),
(5, 12, '2025-12-29', 0, 0, 0, 0, 0, 0, NULL, NULL, '2025-12-31 07:57:36', '2026-01-02 02:05:56'),
(6, 13, '2025-12-22', 0, 0, 0, 1, 0, 0, NULL, NULL, '2025-12-31 07:57:36', '2026-01-02 02:05:58'),
(7, 3, '2025-12-01', 1, 0, 0, 0, 0, 0, NULL, 3, '2025-12-31 07:58:12', '2026-01-02 02:05:56'),
(8, 2, '2025-12-01', 1, 0, 0, 0, 0, 0, NULL, 3, '2025-12-31 07:58:20', '2026-01-02 02:05:56'),
(9, 7, '2025-12-22', 0, 1, 0, 0, 0, 0, NULL, NULL, '2025-12-31 07:59:33', '2026-01-02 02:05:57'),
(10, 7, '2025-12-23', 0, 1, 0, 0, 0, 0, NULL, NULL, '2025-12-31 08:02:59', '2026-01-02 02:05:57'),
(11, 12, '2026-01-02', 0, 0, 1, 0, 0, 0, NULL, NULL, '2026-01-02 01:57:11', '2026-01-02 02:05:58'),
(12, 7, '2026-01-02', 0, 1, 1, 0, 2, 0, NULL, NULL, '2026-01-02 01:57:56', '2026-01-02 03:52:24'),
(13, 12, '2026-01-05', 0, 1, 0, 0, 0, 0, NULL, NULL, '2026-01-05 02:04:27', '2026-01-05 02:04:27'),
(14, 1, '2026-01-05', 0, 1, 0, 0, 1, 0, NULL, NULL, '2026-01-05 13:08:16', '2026-01-05 13:09:08'),
(15, 2, '2026-01-02', 0, 0, 0, 0, 0, 0, NULL, 16, '2026-01-06 03:41:25', '2026-01-20 08:04:27'),
(16, 1, '2026-01-20', 0, 0, 0, 1, 1, 0, NULL, NULL, '2026-01-20 03:05:43', '2026-01-20 08:05:53'),
(17, 2, '2026-01-28', 0, 0, 0, 0, 1, 0, NULL, NULL, '2026-01-28 06:26:26', '2026-01-28 06:26:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', NULL, NULL),
(2, 'admin', NULL, NULL),
(3, 'manager', NULL, NULL),
(4, 'staff', NULL, NULL),
(8, 'ketua_tempa', NULL, NULL),
(9, 'GM', NULL, NULL),
(10, 'direktur', '2026-01-24 03:39:58', '2026-01-24 03:39:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_user`
--

CREATE TABLE `role_user` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(1, 1),
(26, 10),
(27, 3),
(28, 4);

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
('Ov4EajKfncr3BnSINgNuM5YjCZKV7iLwjTUZSgNl', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRG53T2VkRmJDN1EzckNRVEVOZ2Jvcm41TExQVnVFNm5tT0JSYWwxbyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9rYXJ5YXdhbiI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1770543945),
('sBarnmPiLeISTmQG1CgskvzVCU9xcSR3N2S9B86c', 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoienZYbHV0SVczR2FOTnhuT1FoYjJad0FJNWREUjR0TkVMaTk4QnlIOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9rYmkvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjg7fQ==', 1770544213);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `status_karyawan`
--

INSERT INTO `status_karyawan` (`id_status`, `id_karyawan`, `Tanggal_Non_Aktif`, `Alasan_Non_Aktif`, `Ijazah_Dikembalikan`, `Bulan`, `created_at`, `updated_at`) VALUES
(1, 3, NULL, '-', NULL, NULL, '2025-12-10 07:05:00', '2025-12-29 04:22:37'),
(3, 8, '2025-10-16', 'Cuti Melahirkan', 'Tidak', NULL, '2025-12-14 08:42:52', '2025-12-29 04:22:37'),
(4, 2, NULL, NULL, NULL, NULL, '2025-12-14 14:03:42', '2025-12-29 05:03:24'),
(8, 242, NULL, NULL, NULL, NULL, '2026-01-09 07:22:28', '2026-01-09 07:22:28'),
(9, 18, NULL, NULL, NULL, NULL, '2026-01-12 08:43:23', '2026-01-12 08:43:23'),
(10, 82, NULL, NULL, NULL, NULL, '2026-01-12 08:44:30', '2026-01-12 08:44:30'),
(11, 14, NULL, NULL, NULL, NULL, '2026-01-19 03:52:11', '2026-01-19 03:52:11'),
(12, 92, NULL, NULL, NULL, NULL, '2026-01-19 06:21:30', '2026-01-19 06:21:30'),
(13, 654, NULL, NULL, NULL, NULL, '2026-01-19 07:08:26', '2026-01-19 07:08:26'),
(14, 16, NULL, NULL, NULL, NULL, '2026-01-19 07:10:33', '2026-01-19 07:10:33'),
(15, 256, NULL, NULL, NULL, NULL, '2026-01-19 07:11:27', '2026-01-19 07:11:27'),
(16, 5, NULL, NULL, NULL, NULL, '2026-01-19 07:27:28', '2026-01-19 07:27:28'),
(17, 9, NULL, NULL, NULL, NULL, '2026-01-23 04:28:38', '2026-01-23 04:28:38'),
(18, 395, NULL, NULL, NULL, NULL, '2026-01-24 02:16:12', '2026-01-24 02:16:12'),
(19, 593, NULL, NULL, NULL, NULL, '2026-01-31 03:06:54', '2026-01-31 03:06:54'),
(20, 903, NULL, NULL, NULL, NULL, '2026-01-31 03:48:41', '2026-01-31 03:48:41'),
(21, 6, NULL, NULL, NULL, NULL, '2026-02-02 03:45:40', '2026-02-02 03:45:40'),
(23, 7, NULL, NULL, NULL, NULL, '2026-02-04 02:39:00', '2026-02-04 02:39:00'),
(24, 8, NULL, NULL, NULL, NULL, '2026-02-06 02:18:41', '2026-02-06 02:18:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tempa_absensi`
--

CREATE TABLE `tempa_absensi` (
  `id_absensi` int(11) NOT NULL,
  `id_peserta` int(11) NOT NULL,
  `tahun_absensi` year(4) NOT NULL,
  `absensi_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`absensi_data`)),
  `total_hadir` int(11) NOT NULL DEFAULT 0,
  `total_pertemuan` int(11) NOT NULL DEFAULT 0,
  `persentase` decimal(5,2) NOT NULL DEFAULT 0.00,
  `bulan` tinyint(4) DEFAULT NULL,
  `tahun` year(4) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `pertemuan_ke` tinyint(4) DEFAULT NULL,
  `status_hadir` varchar(255) NOT NULL DEFAULT 'hadir',
  `bukti_foto` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tempa_absensi`
--

INSERT INTO `tempa_absensi` (`id_absensi`, `id_peserta`, `tahun_absensi`, `absensi_data`, `total_hadir`, `total_pertemuan`, `persentase`, `bulan`, `tahun`, `tanggal`, `pertemuan_ke`, `status_hadir`, `bukti_foto`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, '2024', NULL, 51, 60, 85.00, 1, '2024', '2024-01-01', 1, '1', NULL, 1, '2026-01-19 06:39:01', '2026-01-19 06:39:01'),
(3, 1, '2024', NULL, 51, 60, 85.00, NULL, NULL, NULL, NULL, 'hadir', NULL, NULL, '2026-01-19 07:54:40', '2026-01-19 07:54:40'),
(4, 2, '2024', NULL, 45, 60, 75.00, NULL, NULL, NULL, NULL, 'hadir', NULL, NULL, '2026-01-19 07:54:40', '2026-01-19 07:54:40'),
(5, 3, '2024', NULL, 60, 60, 100.00, NULL, NULL, NULL, NULL, 'hadir', NULL, NULL, '2026-01-19 07:54:41', '2026-01-19 07:54:41'),
(7, 1, '2024', NULL, 51, 60, 85.00, NULL, NULL, NULL, NULL, 'hadir', NULL, NULL, '2026-01-19 07:56:38', '2026-01-19 07:56:38'),
(8, 2, '2024', NULL, 45, 60, 75.00, NULL, NULL, NULL, NULL, 'hadir', NULL, NULL, '2026-01-19 07:56:38', '2026-01-19 07:56:38'),
(9, 3, '2024', NULL, 60, 60, 100.00, NULL, NULL, NULL, NULL, 'hadir', NULL, NULL, '2026-01-19 07:56:38', '2026-01-19 07:56:38'),
(10, 5, '2024', NULL, 30, 60, 50.00, NULL, NULL, NULL, NULL, 'hadir', NULL, NULL, '2026-01-19 07:56:38', '2026-01-19 07:56:38'),
(11, 8, '2024', NULL, 6, 60, 10.00, NULL, NULL, NULL, NULL, 'hadir', NULL, NULL, '2026-01-19 07:56:39', '2026-01-19 07:56:39'),
(20, 36, '2026', '{\"jan\":{\"1\":\"hadir\",\"2\":\"tidak_hadir\",\"3\":\"hadir\",\"4\":\"hadir\",\"5\":null},\"feb\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"mar\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"apr\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"mei\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"jun\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"jul\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"agu\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"sep\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"okt\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"nov\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"des\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null}}', 3, 4, 75.00, NULL, NULL, NULL, 1, 'hadir', NULL, 16, '2026-01-21 03:35:11', '2026-01-21 05:02:45'),
(21, 37, '2026', '{\"jan\":{\"1\":\"hadir\",\"2\":\"hadir\",\"3\":\"tidak_hadir\",\"4\":\"hadir\",\"5\":null},\"feb\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"mar\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"apr\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"mei\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"jun\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"jul\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"agu\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"sep\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"okt\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"nov\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"des\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null}}', 3, 4, 75.00, NULL, NULL, NULL, 1, 'hadir', NULL, 16, '2026-01-22 02:19:54', '2026-01-22 02:20:16'),
(23, 47, '2026', '{\"jan\":{\"1\":\"hadir\",\"2\":\"hadir\",\"3\":\"tidak_hadir\",\"4\":\"hadir\",\"5\":null},\"feb\":{\"1\":\"hadir\",\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"mar\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"apr\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"mei\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"jun\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"jul\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"agu\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"sep\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"okt\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"nov\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"des\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null}}', 4, 5, 80.00, NULL, NULL, NULL, 1, 'hadir', 'absensi_tempa/Ilxj7plDCRecOZBn4X1siqxwugwkG6n2UDuNWgqy.jpg', 19, '2026-01-22 03:34:38', '2026-01-22 03:36:24'),
(24, 48, '2026', '{\"jan\":{\"1\":\"hadir\",\"2\":\"hadir\",\"3\":\"tidak_hadir\",\"4\":\"hadir\",\"5\":null},\"feb\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"mar\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"apr\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"mei\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"jun\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"jul\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"agu\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"sep\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"okt\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"nov\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null},\"des\":{\"1\":null,\"2\":null,\"3\":null,\"4\":null,\"5\":null}}', 3, 4, 75.00, NULL, NULL, NULL, 1, 'hadir', 'absensi_tempa/tKqaY39bmncmbNpRTDRnVeH8dvhnjtAtFMJWOGKT.jpg', 16, '2026-01-22 04:04:04', '2026-01-22 04:04:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tempa_kelompok`
--

CREATE TABLE `tempa_kelompok` (
  `id_kelompok` bigint(20) UNSIGNED NOT NULL,
  `nama_kelompok` varchar(255) NOT NULL,
  `nama_mentor` varchar(255) NOT NULL,
  `created_by_tempa` bigint(20) UNSIGNED DEFAULT NULL,
  `tempat` enum('pusat','cabang') NOT NULL DEFAULT 'pusat',
  `keterangan_cabang` varchar(255) DEFAULT NULL,
  `ketua_tempa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tempa_kelompok`
--

INSERT INTO `tempa_kelompok` (`id_kelompok`, `nama_kelompok`, `nama_mentor`, `created_by_tempa`, `tempat`, `keterangan_cabang`, `ketua_tempa_id`, `created_at`, `updated_at`) VALUES
(3, 'Kelompok A', 'Eko', 3, 'pusat', NULL, 18, '2026-01-17 06:52:42', '2026-01-17 06:52:42'),
(4, 'Kelompok B', 'Ria', 3, 'pusat', NULL, 18, '2026-01-19 01:39:50', '2026-01-19 01:39:50'),
(7, 'Cooldown', 'Irfan', 3, 'cabang', 'Jakarta', 19, '2026-01-19 02:43:35', '2026-01-19 02:43:35'),
(9, 'Brother', 'Bambang', 3, 'pusat', NULL, 19, '2026-01-19 03:07:11', '2026-01-19 03:07:11'),
(10, 'Kelompok Bekasi', 'Pak Ariel', 3, 'cabang', 'Bekasi', 16, '2026-01-21 03:17:16', '2026-01-22 02:41:01'),
(13, 'Angkasa', 'Vino', NULL, 'cabang', 'Palembang', 16, '2026-01-22 02:03:38', '2026-01-22 02:04:48'),
(22, 'Tes', 'Bambang Pamungkas', NULL, 'cabang', 'Palembang', 19, '2026-01-22 03:31:02', '2026-01-22 03:31:19'),
(23, 'Brotherhood', 'Pak Ali', NULL, 'cabang', 'Jakarta', 16, '2026-01-22 04:01:16', '2026-01-22 04:01:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tempa_materi`
--

CREATE TABLE `tempa_materi` (
  `id_materi` int(11) NOT NULL,
  `judul_materi` varchar(150) NOT NULL,
  `file_materi` varchar(255) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tempa_materi`
--

INSERT INTO `tempa_materi` (`id_materi`, `judul_materi`, `file_materi`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 'Puasa', 'tempa/materi/kt5hw2A9EgOjVQvW5FANtlb021p1TmCuXctkjXNT.pdf', 3, '2026-01-15 03:52:42', '2026-01-22 03:46:31'),
(4, 'Ramadhan', 'tempa/materi/Bz0IVT1hb6bKVi33kOuCMadLrbdAu7LM3hIWmqM8.pdf', 16, '2026-01-22 03:07:24', '2026-01-22 03:19:43'),
(5, 'dummy', 'tempa/materi/ktazrw4TWUiu1fufhKSauXIVTm9MD1XXNNjdimuZ.pdf', 16, '2026-01-24 04:18:58', '2026-01-24 04:18:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tempa_peserta`
--

CREATE TABLE `tempa_peserta` (
  `id_peserta` int(11) NOT NULL,
  `id_kelompok` int(11) NOT NULL,
  `status_peserta` int(11) NOT NULL DEFAULT 1,
  `keterangan_pindah` text DEFAULT NULL,
  `nama_peserta` varchar(150) NOT NULL,
  `nik_karyawan` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tempa_peserta`
--

INSERT INTO `tempa_peserta` (`id_peserta`, `id_kelompok`, `status_peserta`, `keterangan_pindah`, `nama_peserta`, `nik_karyawan`, `created_at`, `updated_at`) VALUES
(1, 3, 1, NULL, 'John Doe', '1234567890', '2026-01-15 03:52:42', '2026-01-21 02:29:54'),
(2, 1, 1, NULL, 'Jane Smith', '1234567891', '2026-01-15 03:52:42', '2026-01-17 03:59:02'),
(3, 2, 1, NULL, 'Bob Johnson', '1234567892', '2026-01-15 03:52:42', '2026-01-17 03:59:02'),
(5, 1, 1, NULL, 'Nashrul Ihsan', 'NIK001', '2026-01-15 07:28:07', '2026-01-17 03:59:02'),
(8, 1, 2, NULL, 'Nurul Fatah', 'NIK002', '2026-01-15 07:29:20', '2026-01-17 03:59:02'),
(9, 1, 0, NULL, 'Zaenal Marlis', 'NIK003', '2026-01-15 07:29:21', '2026-01-17 03:59:02'),
(10, 1, 2, NULL, 'Bagus Cahyo', 'NIK004', '2026-01-15 07:29:21', '2026-01-17 03:59:02'),
(11, 1, 1, NULL, 'M Syaiful Ulum', 'NIK005', '2026-01-15 07:29:21', '2026-01-17 03:59:02'),
(12, 1, 1, NULL, 'Unggul Wahyu Leksono', 'NIK006', '2026-01-15 07:29:21', '2026-01-17 03:59:02'),
(14, 3, 0, NULL, 'Nurul Fatah', 'NIK002', '2026-01-17 02:33:18', '2026-01-21 06:49:34'),
(15, 1, 0, NULL, 'Zaenal Marlis', 'NIK003', '2026-01-17 02:33:18', '2026-01-17 03:59:02'),
(16, 1, 2, NULL, 'Bagus Cahyo', 'NIK004', '2026-01-17 02:33:18', '2026-01-17 03:59:02'),
(17, 1, 1, NULL, 'M Syaiful Ulum', 'NIK005', '2026-01-17 02:33:18', '2026-01-17 03:59:02'),
(18, 1, 1, NULL, 'Unggul Wahyu Leksono', 'NIK006', '2026-01-17 02:33:18', '2026-01-17 03:59:02'),
(20, 1, 2, NULL, 'Nurul Fatah', 'NIK002', '2026-01-17 02:42:15', '2026-01-17 03:59:02'),
(21, 1, 0, NULL, 'Zaenal Marlis', 'NIK003', '2026-01-17 02:42:15', '2026-01-17 03:59:02'),
(22, 1, 2, NULL, 'Bagus Cahyo', 'NIK004', '2026-01-17 02:42:15', '2026-01-17 03:59:02'),
(23, 1, 1, NULL, 'M Syaiful Ulum', 'NIK005', '2026-01-17 02:42:16', '2026-01-17 03:59:02'),
(24, 1, 1, NULL, 'Unggul Wahyu Leksono', 'NIK006', '2026-01-17 02:42:16', '2026-01-17 03:59:02'),
(28, 3, 1, 'F2', 'Tokio', '12345', '2026-01-17 06:35:52', '2026-01-21 02:29:54'),
(29, 3, 2, 'f2', 'Claude', '1234567890', '2026-01-17 06:52:42', '2026-01-21 02:29:54'),
(30, 3, 1, NULL, 'Miya', '12345678', '2026-01-19 01:38:31', '2026-01-21 02:29:54'),
(31, 7, 2, 'Pindah F2', 'qwerty', '11111111', '2026-01-19 01:39:50', '2026-01-21 02:29:54'),
(34, 7, 1, 'Lorem ipsum, atau ringkasnya lipsum, adalah teks standar yang ditempatkan untuk mendemostrasikan elemen grafis atau presentasi visual seperti font, tipografi, dan tata letak.', 'Arjuna', '123321', '2026-01-19 02:53:03', '2026-01-21 02:29:54'),
(35, 9, 1, NULL, 'Apip', '111111222', '2026-01-21 02:47:17', '2026-01-21 02:47:38'),
(36, 10, 1, NULL, 'Matthew', '222222222', '2026-01-21 03:25:57', '2026-01-21 03:25:57'),
(37, 13, 1, 'Pindah Cabang Bekasi', 'Aldous', '232323', '2026-01-22 02:10:33', '2026-01-22 02:19:10'),
(38, 16, 1, NULL, 'John Doe', '1234567890', '2026-01-22 03:03:58', '2026-01-22 03:03:58'),
(39, 16, 1, NULL, 'Jane Smith', '1234567891', '2026-01-22 03:03:59', '2026-01-22 03:03:59'),
(40, 17, 1, NULL, 'Bob Johnson', '1234567892', '2026-01-22 03:03:59', '2026-01-22 03:03:59'),
(41, 18, 1, NULL, 'John Doe', '1234567890', '2026-01-22 03:04:22', '2026-01-22 03:04:22'),
(42, 18, 1, NULL, 'Jane Smith', '1234567891', '2026-01-22 03:04:22', '2026-01-22 03:04:22'),
(43, 19, 1, NULL, 'Bob Johnson', '1234567892', '2026-01-22 03:04:22', '2026-01-22 03:04:22'),
(44, 20, 1, NULL, 'John Doe', '1234567890', '2026-01-22 03:05:40', '2026-01-22 03:05:40'),
(45, 20, 1, NULL, 'Jane Smith', '1234567891', '2026-01-22 03:05:40', '2026-01-22 03:05:40'),
(46, 21, 1, NULL, 'Bob Johnson', '1234567892', '2026-01-22 03:05:40', '2026-01-22 03:05:40'),
(47, 22, 1, NULL, 'Marcelo', '12345678', '2026-01-22 03:31:57', '2026-01-22 03:31:57'),
(48, 23, 1, NULL, 'Hamzah', '123123123', '2026-01-22 04:02:49', '2026-01-22 04:02:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `training`
--

CREATE TABLE `training` (
  `id_training` int(11) NOT NULL,
  `kandidat_id` int(11) NOT NULL,
  `posisi_id` int(11) NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `jadwal_ttd_kontrak` date DEFAULT NULL,
  `hasil_evaluasi` enum('LULUS TRAINING','TIDAK LULUS TRAINING','MENGUNDURKAN DIRI') DEFAULT NULL,
  `keterangan_tambahan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `units`
--

INSERT INTO `units` (`id`, `company_id`, `division_id`, `department_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1, 'Unit Frontend Development', '2026-01-22 07:33:46', '2026-01-22 07:33:46'),
(2, 2, 1, 1, 'Unit Backend Development', '2026-01-22 07:33:46', '2026-01-22 07:33:46'),
(3, 2, 1, 2, 'Unit Database Administration', '2026-01-22 07:33:46', '2026-01-22 07:33:46'),
(4, 2, 1, 2, 'Unit Sistem Analisis', '2026-01-22 07:33:46', '2026-01-22 07:33:46'),
(5, 2, 2, 3, 'Unit Payroll', '2026-01-22 07:33:46', '2026-01-22 07:33:46'),
(6, 2, 6, 6, 'Talent Managemen', '2026-01-23 02:36:44', '2026-01-23 02:51:36'),
(7, 8, 8, 8, 'Markcom', '2026-01-24 02:26:51', '2026-01-24 02:27:47');

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
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `nik`, `jabatan`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Yudha', 'yudha@gmail.com', '220022307162', 'Staff HR', '$2y$12$DN29aaUuK9gSN9jkjB3.Fu9fTHn9mtp1JmPz.EpKzYDrnYWnMZ0Bq', '2026-02-02 03:22:29', '2026-02-02 06:10:02'),
(26, 'Sona Ardhyan', 'ardhyansona@gmail.com', '220022209070', 'Supervisor', '$2y$12$b7mFI.m8gjE8KcH/EWQWxuR1Lvg11JJQ88HBTUJnNnR0yNlmjUBmC', '2026-02-02 03:45:40', '2026-02-03 04:35:01'),
(27, 'Subroto', 'subroto@gmail.com', '220021901558', 'Manager', '$2y$12$Z985sGg54c7to9xhQjiCueOv1BAJAInbSV/PBnjeJx4xus1hagMQW', '2026-02-02 04:45:12', '2026-02-02 04:45:12'),
(28, 'Bagus Aji', 'bagusaji@gmail.com', '120021803065', 'Staff', '$2y$12$gZD9.46gZ51Hqk4wcjCkdOlTVADb/e0/tO3h2MY5dSVKEFJiSJMNW', '2026-02-04 02:39:00', '2026-02-04 02:39:38');

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
  ADD PRIMARY KEY (`id_bpjs`);

--
-- Indeks untuk tabel `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `data_keluarga`
--
ALTER TABLE `data_keluarga`
  ADD PRIMARY KEY (`id_keluarga`);

--
-- Indeks untuk tabel `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departments_company_id_foreign` (`company_id`),
  ADD KEY `departments_division_id_foreign` (`division_id`);

--
-- Indeks untuk tabel `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `divisions_company_id_foreign` (`company_id`);

--
-- Indeks untuk tabel `interview_hr`
--
ALTER TABLE `interview_hr`
  ADD PRIMARY KEY (`id_interview_hr`);

--
-- Indeks untuk tabel `kandidat`
--
ALTER TABLE `kandidat`
  ADD PRIMARY KEY (`id_kandidat`);

--
-- Indeks untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indeks untuk tabel `kbi_assessments`
--
ALTER TABLE `kbi_assessments`
  ADD PRIMARY KEY (`id_kbi_assessment`);

--
-- Indeks untuk tabel `kbi_items`
--
ALTER TABLE `kbi_items`
  ADD PRIMARY KEY (`id_kbi_item`);

--
-- Indeks untuk tabel `kbi_scores`
--
ALTER TABLE `kbi_scores`
  ADD PRIMARY KEY (`id_kbi_score`);

--
-- Indeks untuk tabel `kontrak`
--
ALTER TABLE `kontrak`
  ADD PRIMARY KEY (`id_kontrak`);

--
-- Indeks untuk tabel `kpi_assessments`
--
ALTER TABLE `kpi_assessments`
  ADD PRIMARY KEY (`id_kpi_assessment`);

--
-- Indeks untuk tabel `kpi_items`
--
ALTER TABLE `kpi_items`
  ADD PRIMARY KEY (`id_kpi_item`);

--
-- Indeks untuk tabel `kpi_perspectives`
--
ALTER TABLE `kpi_perspectives`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kpi_perspectives_name_unique` (`name`);

--
-- Indeks untuk tabel `kpi_scores`
--
ALTER TABLE `kpi_scores`
  ADD PRIMARY KEY (`id_kpi_score`);

--
-- Indeks untuk tabel `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `onboarding_karyawan`
--
ALTER TABLE `onboarding_karyawan`
  ADD PRIMARY KEY (`id_onboarding`),
  ADD KEY `fk_onboarding_kandidat` (`kandidat_id`),
  ADD KEY `fk_onboarding_posisi` (`posisi_id`);

--
-- Indeks untuk tabel `pekerjaan`
--
ALTER TABLE `pekerjaan`
  ADD PRIMARY KEY (`id_pekerjaan`),
  ADD KEY `id_karyawan` (`id_karyawan`),
  ADD KEY `pekerjaan_company_id_foreign` (`company_id`),
  ADD KEY `pekerjaan_division_id_foreign` (`division_id`),
  ADD KEY `pekerjaan_department_id_foreign` (`department_id`),
  ADD KEY `pekerjaan_unit_id_foreign` (`unit_id`),
  ADD KEY `pekerjaan_position_id_foreign` (`position_id`),
  ADD KEY `fk_pekerjaan_level` (`level_id`);

--
-- Indeks untuk tabel `pemberkasan`
--
ALTER TABLE `pemberkasan`
  ADD PRIMARY KEY (`id_pemberkasan`);

--
-- Indeks untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  ADD PRIMARY KEY (`id_pendidikan`);

--
-- Indeks untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD PRIMARY KEY (`id_perusahaan`);

--
-- Indeks untuk tabel `posisi`
--
ALTER TABLE `posisi`
  ADD PRIMARY KEY (`id_posisi`);

--
-- Indeks untuk tabel `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `positions_company_id_foreign` (`company_id`),
  ADD KEY `positions_division_id_foreign` (`division_id`),
  ADD KEY `positions_department_id_foreign` (`department_id`),
  ADD KEY `positions_unit_id_foreign` (`unit_id`);

--
-- Indeks untuk tabel `proses_rekrutmen`
--
ALTER TABLE `proses_rekrutmen`
  ADD PRIMARY KEY (`id_proses_rekrutmen`);

--
-- Indeks untuk tabel `rekrutmen_daily`
--
ALTER TABLE `rekrutmen_daily`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indeks untuk tabel `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `status_karyawan`
--
ALTER TABLE `status_karyawan`
  ADD PRIMARY KEY (`id_status`);

--
-- Indeks untuk tabel `tempa_absensi`
--
ALTER TABLE `tempa_absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD UNIQUE KEY `unik_absensi` (`id_peserta`,`bulan`,`pertemuan_ke`);

--
-- Indeks untuk tabel `tempa_kelompok`
--
ALTER TABLE `tempa_kelompok`
  ADD PRIMARY KEY (`id_kelompok`),
  ADD KEY `tempa_kelompok_created_by_tempa_foreign` (`created_by_tempa`);

--
-- Indeks untuk tabel `tempa_materi`
--
ALTER TABLE `tempa_materi`
  ADD PRIMARY KEY (`id_materi`);

--
-- Indeks untuk tabel `tempa_peserta`
--
ALTER TABLE `tempa_peserta`
  ADD PRIMARY KEY (`id_peserta`),
  ADD KEY `id_kelompok` (`id_kelompok`);

--
-- Indeks untuk tabel `training`
--
ALTER TABLE `training`
  ADD PRIMARY KEY (`id_training`);

--
-- Indeks untuk tabel `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `units_company_id_foreign` (`company_id`),
  ADD KEY `units_division_id_foreign` (`division_id`),
  ADD KEY `units_department_id_foreign` (`department_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bpjs`
--
ALTER TABLE `bpjs`
  MODIFY `id_bpjs` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `data_keluarga`
--
ALTER TABLE `data_keluarga`
  MODIFY `id_keluarga` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `interview_hr`
--
ALTER TABLE `interview_hr`
  MODIFY `id_interview_hr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `kandidat`
--
ALTER TABLE `kandidat`
  MODIFY `id_kandidat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `kbi_assessments`
--
ALTER TABLE `kbi_assessments`
  MODIFY `id_kbi_assessment` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `kbi_items`
--
ALTER TABLE `kbi_items`
  MODIFY `id_kbi_item` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kbi_scores`
--
ALTER TABLE `kbi_scores`
  MODIFY `id_kbi_score` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kontrak`
--
ALTER TABLE `kontrak`
  MODIFY `id_kontrak` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `kpi_assessments`
--
ALTER TABLE `kpi_assessments`
  MODIFY `id_kpi_assessment` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT untuk tabel `kpi_items`
--
ALTER TABLE `kpi_items`
  MODIFY `id_kpi_item` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT untuk tabel `kpi_perspectives`
--
ALTER TABLE `kpi_perspectives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kpi_scores`
--
ALTER TABLE `kpi_scores`
  MODIFY `id_kpi_score` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT untuk tabel `levels`
--
ALTER TABLE `levels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `onboarding_karyawan`
--
ALTER TABLE `onboarding_karyawan`
  MODIFY `id_onboarding` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `pekerjaan`
--
ALTER TABLE `pekerjaan`
  MODIFY `id_pekerjaan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT untuk tabel `pemberkasan`
--
ALTER TABLE `pemberkasan`
  MODIFY `id_pemberkasan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  MODIFY `id_pendidikan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id_perusahaan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `posisi`
--
ALTER TABLE `posisi`
  MODIFY `id_posisi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `positions`
--
ALTER TABLE `positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `proses_rekrutmen`
--
ALTER TABLE `proses_rekrutmen`
  MODIFY `id_proses_rekrutmen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `rekrutmen_daily`
--
ALTER TABLE `rekrutmen_daily`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `status_karyawan`
--
ALTER TABLE `status_karyawan`
  MODIFY `id_status` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `tempa_absensi`
--
ALTER TABLE `tempa_absensi`
  MODIFY `id_absensi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `tempa_kelompok`
--
ALTER TABLE `tempa_kelompok`
  MODIFY `id_kelompok` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `tempa_materi`
--
ALTER TABLE `tempa_materi`
  MODIFY `id_materi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tempa_peserta`
--
ALTER TABLE `tempa_peserta`
  MODIFY `id_peserta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `training`
--
ALTER TABLE `training`
  MODIFY `id_training` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `onboarding_karyawan`
--
ALTER TABLE `onboarding_karyawan`
  ADD CONSTRAINT `fk_onboarding_kandidat` FOREIGN KEY (`kandidat_id`) REFERENCES `kandidat` (`id_kandidat`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_onboarding_posisi` FOREIGN KEY (`posisi_id`) REFERENCES `posisi` (`id_posisi`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tempa_absensi`
--
ALTER TABLE `tempa_absensi`
  ADD CONSTRAINT `tempa_absensi_ibfk_1` FOREIGN KEY (`id_peserta`) REFERENCES `tempa_peserta` (`id_peserta`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
