/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `biaya_promote_ta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `biaya_promote_ta` (
  `id_biaya_promote_ta` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `keperluan` varchar(150) DEFAULT NULL,
  `biaya` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_biaya_promote_ta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bpjs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bpjs` (
  `id_bpjs` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Status_BPJS_KT` enum('Aktif','Tidak Aktif') DEFAULT NULL,
  `Status_BPJS_KS` enum('Aktif','Tidak Aktif') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_bpjs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_keluarga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `data_keluarga` (
  `id_keluarga` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_keluarga`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `division_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_company_id_foreign` (`company_id`),
  KEY `departments_division_id_foreign` (`division_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `divisions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `divisions_company_id_foreign` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interview_hr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `interview_hr` (
  `id_interview_hr` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_interview_hr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kandidat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kandidat` (
  `id_kandidat` int(11) NOT NULL AUTO_INCREMENT,
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
  `tgl_lolos_user` date DEFAULT NULL,
  PRIMARY KEY (`id_kandidat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kandidat_lanjut_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `karyawan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `karyawan` (
  `id_karyawan` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_karyawan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kbi_assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kbi_assessments` (
  `id_kbi_assessment` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `karyawan_id` bigint(20) unsigned NOT NULL,
  `penilai_id` bigint(20) unsigned NOT NULL,
  `tipe_penilai` enum('DIRI_SENDIRI','ATASAN','BAWAHAN') NOT NULL,
  `tahun` year(4) NOT NULL,
  `periode` varchar(255) NOT NULL DEFAULT 'Semester 1',
  `rata_rata_akhir` double NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'DRAFT',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kbi_assessment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kbi_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kbi_items` (
  `id_kbi_item` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kategori` varchar(255) NOT NULL,
  `perilaku` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kbi_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kbi_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kbi_scores` (
  `id_kbi_score` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kbi_assessment_id` bigint(20) unsigned NOT NULL,
  `kbi_item_id` bigint(20) unsigned NOT NULL,
  `skor` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kbi_score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kontrak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kontrak` (
  `id_kontrak` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_kontrak`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kpi_assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kpi_assessments` (
  `id_kpi_assessment` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kpi_assessment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kpi_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kpi_items` (
  `id_kpi_item` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kpi_assessment_id` bigint(20) unsigned NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kpi_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kpi_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kpi_scores` (
  `id_kpi_score` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kpi_item_id` bigint(20) unsigned NOT NULL,
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
  PRIMARY KEY (`id_kpi_score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `onboarding_karyawan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `onboarding_karyawan` (
  `id_onboarding` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_onboarding`),
  KEY `fk_onboarding_kandidat` (`kandidat_id`),
  KEY `fk_onboarding_posisi` (`posisi_id`),
  CONSTRAINT `fk_onboarding_kandidat` FOREIGN KEY (`kandidat_id`) REFERENCES `kandidat` (`id_kandidat`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_onboarding_posisi` FOREIGN KEY (`posisi_id`) REFERENCES `posisi` (`id_posisi`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pekerjaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pekerjaan` (
  `id_pekerjaan` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `division_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` bigint(20) unsigned DEFAULT NULL,
  `position_id` bigint(20) unsigned DEFAULT NULL,
  `Jabatan` enum('Staff','Kepala Regu','Kepala Unit','Kepala Shift','Head Of Brances (HOB)','Supervisor','Manager','General Manager (GM)','Direktur') DEFAULT NULL,
  `Bagian` varchar(255) DEFAULT NULL,
  `Departement` enum('BUSINESS','BUSINESS DEVELOPMENT','DIREKSI','FINANCE & ACCOUNTING','MARKETING & SALES','OPERASIONAL','RESEARCH & ENGINEERING','STABLE') DEFAULT NULL,
  `Divisi` enum('Accounting','Branches Bali','Branches Bandung','Branches Bekasi','Branches Bogor','Branches Cianjur','Branches Cirebon','Branches Jember','Branches Kediri','Branches Lampung','Branches Latubo','Branches Madiun','Branches Madura','Branches Magelang','Branches Malang','Branches Pati','Branches Purwakarta','Branches Purwokerto','Branches Semarang','Branches Serang','Branches Sidoarjo','Branches Surakarta','Branches Tangerang','Branches Tasikmalaya','Branches Tegal','Business','Design Product','Development','Direktur Umum','Div. Business Development','Div. Marketing & Sales','Div. Research & Engineering','Divisi Operasional','East Area Sales','Engineering Service','Fabrikasi F1 (WKD)','Fabrikasi F2 (Residensial Door)','Fabrikasi F2 (Wadja)','Factory','Finance','Finance & Accounting','Finance & Invesment','Finance Center','General Affair & Logistik','General Affairs','HR Center','HRD','Logistik','Logistik Marketing & Sales','Marcom','Marketing','Online Direct Selling','Project Spesialist','Prototype','Research','Research & Engineering','West Area Sales') DEFAULT NULL,
  `Unit` enum('Branches Bali','Branches Bandung','Branches Bekasi','Branches Bogor','Branches Bojonegoro','Branches Cianjur','Branches Cirebon','Branches Jember','Branches Kediri','Branches Lampung','Branches Latubo','Branches Madiun','Branches Madura','Branches Magelang','Branches Makassar','Branches Malang','Branches Palembang','Branches Pati','Branches Purwakarta','Branches Purwokerto','Branches Semarang','Branches Serang','Branches Sidoarjo','Branches Surakarta','Branches Tangerang','Branches Tasikmalaya','Branches Tegal','Branches Yogyakarta','Divisi Operasional','Factory','Factory 1','Factory 2','Factory 2 Residensial & Project','Factory 3','Factory 4','Finance','General Affair','Logistic','Maintanance','Online & Project Selling','Organization Development','Specialist K3') DEFAULT NULL,
  `Jenis_Kontrak` enum('PKWT','PKWTT') DEFAULT NULL,
  `Perjanjian` enum('Harian Lepas','Kontrak','Tetap') DEFAULT NULL,
  `Lokasi_Kerja` enum('Central Java - Pati','Central Java - Pekalongan','Central Java - Purwokerto','Central Java - Surakarta','Central Java - Magelang','Central Java - Semarang','Central Java - Tegal','West Java - Purwakarta','West Java - Cianjur','West Java - Bandung','West Java - Bogor','West Java - Cirebon','West Java - Tangerang','West Java - Bekasi','West Java - Tasikmalaya','Banten - Serang','East Java - Bojonegoro','East Java - Jember','East Java - Madiun','East Java - Madura','East Java - Sidoarjo','Bali - Bali','East Java - Malang','East Java - Kediri','South Sumatra - Lampung','South Sumatra - Palembang','DIY - Yogyakarta','South Sulawesi - Makassar') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pekerjaan`),
  KEY `pekerjaan_company_id_foreign` (`company_id`),
  KEY `pekerjaan_division_id_foreign` (`division_id`),
  KEY `pekerjaan_department_id_foreign` (`department_id`),
  KEY `pekerjaan_unit_id_foreign` (`unit_id`),
  KEY `pekerjaan_position_id_foreign` (`position_id`),
  CONSTRAINT `pekerjaan_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `pekerjaan_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `pekerjaan_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`),
  CONSTRAINT `pekerjaan_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`),
  CONSTRAINT `pekerjaan_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pemberkasan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pemberkasan` (
  `id_pemberkasan` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pemberkasan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pendidikan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pendidikan` (
  `id_pendidikan` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Pendidikan_Terakhir` enum('SD','SLTP','SLTA','DIPLOMA I','DIPLOMA II','DIPLOMA III','DIPLOMA IV','S1','S2') DEFAULT NULL,
  `Nama_Lengkap_Tempat_Pendidikan_Terakhir` varchar(255) DEFAULT NULL,
  `Jurusan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pendidikan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `perusahaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perusahaan` (
  `id_perusahaan` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Perusahaan` enum('CV BERKAH NEGERI MULIA','PT INTI DUNIA MANDIRI','PT SOCHA INTI INFORMATIKA','PT TIMUR SEMESTA ABADI','PT WADJA INTI MULIA','PT WADJA INTI MULIA PERSADA','PT WADJA KARYA DUNIA','TAMANSARI EQUESTRIAN PARK') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_perusahaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `posisi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posisi` (
  `id_posisi` int(11) NOT NULL AUTO_INCREMENT,
  `nama_posisi` varchar(150) NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `progress_rekrutmen` varchar(50) DEFAULT 'Menerima Kandidat',
  `total_pelamar` int(11) DEFAULT 0,
  PRIMARY KEY (`id_posisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `division_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `positions_company_id_foreign` (`company_id`),
  KEY `positions_division_id_foreign` (`division_id`),
  KEY `positions_department_id_foreign` (`department_id`),
  KEY `positions_unit_id_foreign` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `proses_rekrutmen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proses_rekrutmen` (
  `id_proses_rekrutmen` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_proses_rekrutmen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rekrutmen_daily`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rekrutmen_daily` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `posisi_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `total_pelamar` int(11) NOT NULL DEFAULT 0,
  `lolos_cv` int(11) NOT NULL DEFAULT 0,
  `lolos_psikotes` int(11) NOT NULL DEFAULT 0,
  `lolos_kompetensi` int(11) NOT NULL DEFAULT 0,
  `lolos_hr` int(11) NOT NULL DEFAULT 0,
  `lolos_user` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_user` (
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(80) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `status_karyawan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_karyawan` (
  `id_status` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_karyawan` bigint(20) DEFAULT NULL,
  `Tanggal_Non_Aktif` date DEFAULT NULL,
  `Alasan_Non_Aktif` varchar(255) DEFAULT NULL,
  `Ijazah_Dikembalikan` varchar(255) DEFAULT NULL,
  `Bulan` double DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tempa_absensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tempa_absensi` (
  `id_absensi` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_absensi`),
  UNIQUE KEY `unik_absensi` (`id_peserta`,`bulan`,`pertemuan_ke`),
  CONSTRAINT `tempa_absensi_ibfk_1` FOREIGN KEY (`id_peserta`) REFERENCES `tempa_peserta` (`id_peserta`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tempa_kelompok`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tempa_kelompok` (
  `id_kelompok` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kelompok` varchar(255) NOT NULL,
  `nama_mentor` varchar(255) NOT NULL,
  `created_by_tempa` bigint(20) unsigned DEFAULT NULL,
  `tempat` enum('pusat','cabang') NOT NULL DEFAULT 'pusat',
  `keterangan_cabang` varchar(255) DEFAULT NULL,
  `ketua_tempa_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kelompok`),
  KEY `tempa_kelompok_created_by_tempa_foreign` (`created_by_tempa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tempa_materi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tempa_materi` (
  `id_materi` int(11) NOT NULL AUTO_INCREMENT,
  `judul_materi` varchar(150) NOT NULL,
  `file_materi` varchar(255) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_materi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tempa_peserta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tempa_peserta` (
  `id_peserta` int(11) NOT NULL AUTO_INCREMENT,
  `id_kelompok` int(11) NOT NULL,
  `status_peserta` int(11) NOT NULL DEFAULT 1,
  `keterangan_pindah` text DEFAULT NULL,
  `nama_peserta` varchar(150) NOT NULL,
  `nik_karyawan` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_peserta`),
  KEY `id_kelompok` (`id_kelompok`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `training`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training` (
  `id_training` int(11) NOT NULL AUTO_INCREMENT,
  `kandidat_id` int(11) NOT NULL,
  `posisi_id` int(11) NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `jadwal_ttd_kontrak` date DEFAULT NULL,
  `hasil_evaluasi` enum('LULUS TRAINING','TIDAK LULUS TRAINING','MENGUNDURKAN DIRI') DEFAULT NULL,
  `keterangan_tambahan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_training`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `division_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `units_company_id_foreign` (`company_id`),
  KEY `units_division_id_foreign` (`division_id`),
  KEY `units_department_id_foreign` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `jabatan` varchar(200) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wig_rekrutmen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2025_12_17_000000_create_rekrutmen_daily_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2025_12_17_074546_create_kpi_tables',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2025_12_18_042424_add_columns_to_kpi_items_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_12_20_022325_add_status_to_posisi_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_12_22_132758_add_monthly_columns_to_kpi_scores_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_12_30_040824_add_total_smt2_to_kpi_scores_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_12_30_042614_add_adjustment_columns_to_kpi_scores',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_12_30_102541_add_real_adjustment_to_kpi_scores',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_12_31_060700_create_kbi_tables',9);
