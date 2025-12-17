-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Des 2025 pada 01.55
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
(3, 7, 'Aktif', 'Aktif', '2025-12-13 06:46:32', NULL),
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
(3, 7, 'rajala', 'ratula', 'ratu', '12345', 'Kudus', '2001-01-13', '081222222222', 'S1', '[{\"nama\":\"Anak1\",\"tempat_lahir\":\"Kudus\",\"tanggal_lahir\":\"2024-06-14\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"Belum Sekolah\"}]', '2025-12-13 06:46:31', NULL),
(4, 8, 'Ayah Karyawan 1', 'Ibu Karyawan 1', 'Suami Karyawan 1', '22222222222', 'Pati', '1996-10-14', '081222222222', 'S1', '[{\"nama\":\"Anak 1 Karyawan 1\",\"tempat_lahir\":\"Pati\",\"tanggal_lahir\":\"2020-01-14\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"SD\"},{\"nama\":\"Anak 2 Karyawan 1\",\"tempat_lahir\":\"Pati\",\"tanggal_lahir\":\"2024-01-14\",\"jenis_kelamin\":\"P\",\"pendidikan\":\"Belum Sekolah\"}]', '2025-12-14 08:42:52', NULL),
(5, 9, 'Ahmad Dahlan', 'Lusi', 'Mirai', '123456', 'Batam', '2002-10-14', '081222222222', 'S1', '[{\"nama\":\"Bas\",\"tempat_lahir\":\"Batam\",\"tanggal_lahir\":\"2024-01-14\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"Belum Sekolah\"}]', '2025-12-14 10:21:21', NULL),
(6, 2, 'a', 'b', 'moji', '1234', 'Jakarta', '2001-10-28', '081432432432', 'SMA', '[{\"nama\":\"Dewa\",\"tempat_lahir\":\"Kudus\",\"tanggal_lahir\":\"2023-01-16\",\"jenis_kelamin\":\"L\",\"pendidikan\":\"Belum Sekolah\"},{\"nama\":\"Dewi\",\"tempat_lahir\":\"Kudus\",\"tanggal_lahir\":\"2025-02-10\",\"jenis_kelamin\":\"P\",\"pendidikan\":\"Belum Sekolah\"}]', '2025-12-14 14:03:42', NULL);

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
(7, 123456, '1', 'Aktif', 'raja', '1234567890', 'raja', 'Kudus', '2000-01-13', '25 Tahun', 'L', 'Menikah', 'O', '081234567890', 'raja@gmail.com', 'Kudus', '001', '003', 'DEMAAN', 'KOTA KUDUS', 'KABUPATEN KUDUS', 'JAWA TENGAH', 'Kudus', '001', '003', 'DEMAAN', 'KOTA KUDUS', 'KABUPATEN KUDUS', 'JAWA TENGAH', 'Kudus', '2025-12-13 06:46:31', NULL),
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
(7, 8, '2021-03-15', NULL, NULL, NULL, NULL, NULL, NULL, '4 Tahun 9 Bulan 1 Hari', NULL, NULL, '2025-12-16 03:25:51', NULL),
(8, 7, '2019-10-16', NULL, NULL, NULL, NULL, NULL, NULL, '6 Tahun 2 Bulan 0 Hari', NULL, NULL, '2025-12-16 03:27:14', NULL);

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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pekerjaan`
--

INSERT INTO `pekerjaan` (`id_pekerjaan`, `id_karyawan`, `Jabatan`, `Bagian`, `Departement`, `Divisi`, `Unit`, `Jenis_Kontrak`, `Perjanjian`, `Lokasi_Kerja`, `created_at`, `updated_at`) VALUES
(1, 3, 'General Manager (GM)', 'Sales & Marketing', 'MARKETING & SALES', 'Marketing', 'Factory 1', 'PKWTT', 'Tetap', 'Central Java - Pati', '2025-12-10 06:54:30', NULL),
(2, 5, 'Staff', 'Web Dev', 'BUSINESS DEVELOPMENT', 'HRD', 'Factory 1', NULL, 'Kontrak', 'Central Java - Pati', '2025-12-12 03:17:08', NULL),
(3, 7, 'Staff', 'HR', 'BUSINESS DEVELOPMENT', 'HRD', 'Factory 1', NULL, 'Kontrak', 'Central Java - Pati', '2025-12-13 06:46:31', NULL),
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
(6, 2, 'S1', 'Universitas Sebelas Maret', 'Akuntansi', '2025-12-14 15:11:34', NULL),
(7, 7, 'SLTA', 'SMA N 1 PATI', 'IPA', '2025-12-16 03:27:12', NULL);

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
(3, 7, 'PT WADJA KARYA DUNIA', '2025-12-13 06:46:31', NULL),
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
('CJwaypj0M3bLGK4lFLaWbkhyr37iRLbEBYhSgRe0', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidXBxcmxKazh3elRyRjBNb0ZIS2VEYm5tUWw2cU5Vdnc0d0JEdXU1MyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1765874576),
('w9U4MIRbD7wUF4VAwY11eaFzQ3ff3RZOUqhAnrAL', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSm1lR282ZXI2cTY5dTFSeExsMHYxeTdrM092N2huaHlLSXkzUW9GaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9rYXJ5YXdhbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1765861051);

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
(2, 7, '2027-12-13', 'asdasdad', 'Ya', NULL, '2025-12-13 06:46:32', NULL),
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
-- AUTO_INCREMENT untuk tabel `kandidat`
--
ALTER TABLE `kandidat`
  MODIFY `id_kandidat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kandidat_lanjut_user`
--
ALTER TABLE `kandidat_lanjut_user`
  MODIFY `id_kandidat_lanjut_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `kontrak`
--
ALTER TABLE `kontrak`
  MODIFY `id_kontrak` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pekerjaan`
--
ALTER TABLE `pekerjaan`
  MODIFY `id_pekerjaan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pemberkasan`
--
ALTER TABLE `pemberkasan`
  MODIFY `id_pemberkasan` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_posisi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `proses_rekrutmen`
--
ALTER TABLE `proses_rekrutmen`
  MODIFY `id_proses_rekrutmen` int(11) NOT NULL AUTO_INCREMENT;

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
