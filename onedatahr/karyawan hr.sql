-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Feb 2026 pada 08.59
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
(2, NULL, NULL, 220022307162, '1', 'Aktif', 'Rafif Adhitya', '3319021607890000', 'Rafif Adhitya', 'Kudus', '1989-07-16', '36 Tahun', 'L', 'Menikah', 'B', '81575075043', 'yudha.yycell@gmail.com', 'Dk.Randangan rt.002/003 Ds.Semirejo Kec.Gembong Kab.Pati', '2', '3', 'Semirejo', 'Gembong', 'Pati', 'Jawa Tengah', 'Dk.Randangan rt.002/003 Ds.Semirejo Kec.Gembong Kab.Pati', '002', '003', 'Semirejo', 'Gembong', 'Pati', 'Jawa Tengah', 'Dk. Randangan RT/RW 02/03 Desa Samirejo, Kec. Gembong, Kab. Pati', '2025-12-22 20:48:30', '2026-02-08 10:56:16'),
(3, NULL, NULL, 220021901558, '1', 'Aktif', 'Subroto, S.Farm., Apt', '3318190308850000', 'Subroto, S.Farm., Apt', 'Pati', '1985-08-03', '40 Tahun', 'L', 'Menikah', 'A', '82221204444', 'SUBROTOAPOTEKER@GMAIL.COM', 'Tayukulon', '4', '3', 'Tayukulon', 'Tayu', 'Pati', 'Jawa Tengah', 'Perum Kutoharjo', '001', '007', 'Kutoharjo', 'Pati', 'Pati', 'Jawa Tengah', 'Jl. Yudistira 6, No. 2, Perum Kutoharjo, Rt. 001/007, Kec. Pati, Kab. Pati', '2025-12-22 20:48:30', '2026-02-02 04:43:30'),
(6, NULL, NULL, 220022209070, '1', 'Aktif', 'Sona Ardhyan', '3275032001940020', 'Sona Ardhyan', 'Jakarta', '1998-11-18', '27 Tahun', 'L', 'Menikah', 'A', '83897776438', 'ardhyansona@gmail.com', 'Jl. Bulustalan Gang 3A No. 362C', '4', '3', 'BULUSTALAN', 'SEMARANG SELATAN', 'KOTA SEMARANG', 'JAWA TENGAH', 'Jl. Bulustalan Gang 3A No. 362C', '4', '3', 'BULUSTALAN', 'SEMARANG SELATAN', 'KOTA SEMARANG', 'JAWA TENGAH', 'Jl. Bulustalan Gang 3A No. 362C', '2026-02-02 03:45:40', '2026-02-02 06:14:09'),
(7, NULL, 14, 120021803065, '1', 'Aktif', 'Muhammad Bagus Setiaji', '3374110204790010', 'Bagus Aji', 'Selatpanjang-(Riau)', '1979-04-02', '46 Tahun', 'L', 'Menikah', 'A', '81226126797', 'bagusaji@gmail.com', NULL, '6', '5', NULL, NULL, NULL, NULL, NULL, '006', '007', NULL, NULL, NULL, NULL, NULL, '2026-02-04 02:39:00', '2026-02-09 06:30:05'),
(8, NULL, NULL, 120020304001, '1', 'Aktif', 'Abu Naim', '3318140210780000', 'Abu Naim', 'Pati', '1978-10-02', '47 Tahun', 'L', 'Menikah', NULL, '8122826537', 'abunaimku@yahoo.co.id', 'Ds. Tamansari', '01', '01', 'TAMANSARI', 'TLOGOWUNGU', 'KABUPATEN PATI', 'JAWA TENGAH', 'Ds. Tamansari', '01', '01', 'TAMANSARI', 'TLOGOWUNGU', 'KABUPATEN PATI', 'JAWA TENGAH', 'Ds. Tamansari', '2026-02-06 02:18:41', '2026-02-06 02:18:41'),
(9, NULL, 14, 220022205007, '1', 'Aktif', 'Okky Zaenur Endrawan', '3318050111990000', 'Okky Zaenur Endrawan, A.Md.', 'Pati', '1999-11-01', '26 Tahun', 'L', 'Belum Menikah', 'A', '082210354079', 'okkyzaenur@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-08 10:37:27', '2026-02-09 03:59:43'),
(10, NULL, NULL, 220022205006, '1', 'Aktif', 'Fitria Khoirun Nisa', '3318136012990000', 'Fitria Khoirun Nisa', 'Pati', '1999-12-20', '26 Tahun', 'P', 'Belum Menikah', 'O', '85875543842', 'fitriakhoirun99@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-08 10:40:25', '2026-02-08 10:40:25'),
(11, NULL, NULL, 220022206015, '1', 'Aktif', 'Nur Alviah', '1409025405960000', 'Nur Alviah', 'Pati', '1996-05-14', '29 Tahun', 'P', 'Menikah', 'B', '81365394951', 'nuralviah15@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-08 10:46:57', '2026-02-08 10:46:57'),
(12, NULL, NULL, 220022310192, '1', 'Aktif', 'Almar\'atul Afuwwah Q. A. Mubarak', '6472025412000050', 'Almar\'atul Afuwwah Q. A. Mubarak', 'Sleman', '2000-12-14', '25 Tahun', 'P', 'Belum Menikah', 'A', '82382247833', 'ain3282@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-08 11:10:39', '2026-02-08 11:10:39'),
(13, NULL, NULL, 220021811553, '1', 'Aktif', 'Khoirul Azman Sulkan', '3318040109990000', 'Khoirul Azman Sulkan', 'Pati', '1999-09-01', '26 Tahun', 'L', 'Menikah', 'O', '085647575506', 'azmanirul123@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-09 01:20:30', '2026-02-09 01:20:30'),
(14, NULL, 3, 120021711061, '1', 'Aktif', 'Kusdiyanto', '3318141306850000', 'Kusdiyanto', 'Pati', '1985-06-13', '40 Tahun', 'L', 'Menikah', 'O', '82322824948', 'kusdiyanto@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-09 01:24:54', '2026-02-09 03:55:53');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
