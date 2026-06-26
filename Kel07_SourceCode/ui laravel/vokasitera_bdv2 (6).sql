-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 26 Jun 2026 pada 09.41
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
-- Database: `vokasitera_bdv2`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `artefaks`
--

CREATE TABLE `artefaks` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
-- Struktur dari tabel `device_token`
--

CREATE TABLE `device_token` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token_device` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen`
--

CREATE TABLE `dosen` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pegawai_id` int(11) DEFAULT NULL,
  `dosen_id` int(11) DEFAULT NULL,
  `nip` varchar(255) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `email` text DEFAULT NULL,
  `prodi_id` int(11) DEFAULT NULL,
  `prodi` varchar(255) DEFAULT NULL,
  `jabatan_akademik` varchar(255) DEFAULT NULL,
  `jabatan_akademik_desc` varchar(255) DEFAULT NULL,
  `jenjang_pendidikan` varchar(255) DEFAULT NULL,
  `nomor_telepon` varchar(255) DEFAULT NULL,
  `nidn` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dosen`
--

INSERT INTO `dosen` (`id`, `pegawai_id`, `dosen_id`, `nip`, `nama`, `email`, `prodi_id`, `prodi`, `jabatan_akademik`, `jabatan_akademik_desc`, `jenjang_pendidikan`, `nomor_telepon`, `nidn`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 246, 132, NULL, '-', NULL, 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Master, ', NULL, '3123', 2639, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(2, 276, 149, '0307180285', 'Abdul Haris Sinaga', NULL, 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Sarjana, SMA / SMK / Sederajat, ', NULL, '12323545', 3610, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(3, 9, 9, NULL, 'Abhishek Chadha', 'abhi@del.ac.id, abhishek_chadha_2000@yahoo.com', 3, 'DIII Teknologi Komputer', '-', '-', '', NULL, '-', 1388, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(4, 382, 135, '0301190334', 'Ahmad Zatnika Purwalaksana, S.Si., M.Si.', NULL, 3, 'DIII Teknologi Komputer', 'B', 'Asisten Ahli', 'Master, ', NULL, '0103029402', 3715, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(5, 594, 184, '0311230509', 'Ana Muliyana, M.Pd.', 'ana.muliyana@del.ac.id, muliyanajait@gmail.com', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'B', 'Asisten Ahli', 'Master, ', NULL, '-', 7535, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(6, 156, 76, '0302150134', 'Anita Emi Kurniawati, S.Pd, M.Si', 'anita.kurniawati@del.ac.id', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Sarjana, Master, ', NULL, '0111058901', 1501, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(7, 481, 164, '0303220438', 'Ardiles Sinaga, S.T., M.T.', 'ardiles.sinaga@del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'D', 'Lektor Kepala', 'Master, Sarjana, ', NULL, '0420098501', 5757, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(8, 574, 177, '0308230496', 'Asyraf Atthariq Putra . G, S.Pd., M.T.', 'asyraf.putra@del.ac.id , asyrafgary@gmail.com', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'A', 'Tenaga Pengajar', 'Master, Sarjana, ', NULL, '-', 6396, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(9, 640, 191, '0301250535', 'Bella Wahmilyana Asril, S.T., M.T.', 'bella.asril@del.ac.id , ', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Master, ', NULL, '-', 8146, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(10, 34, 27, '030111H004', 'Bonar Lumban Tobing', NULL, 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Diploma 3, Diploma 4, ', NULL, '-', 3623, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(11, 58, 42, NULL, 'Bony Parulian Josaphat Marbun', 'bony@del.ac.id', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Sarjana, Diploma 4, ', NULL, '-', 1424, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(12, 3, 3, NULL, 'Candra Taufik', 'ctaufik@lycos.com, candra@del.ac.id', 1, 'DIII Teknologi Informasi', 'B', 'Asisten Ahli', 'Diploma 3, ', NULL, '-', 1383, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(13, 262, 153, '0309180303', 'Cynthia Deborah Nababan, S.Tr.Kom.', NULL, 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'A', 'Tenaga Pengajar', 'Diploma 4, Master, Master, ', NULL, '123', 3602, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(14, 99, 58, '309120065', 'Danang Junaedi, ST, MT', 'danang@del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'B', 'Asisten Ahli', 'Diploma 3, Diploma 4, ', NULL, '-', 1460, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(15, 464, 160, NULL, 'Dian Ira Putri Hutasoit, S.Tr.Kom', 'dian.ira@del.ac.id , dian.hutasoit@gmail.com', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'A', 'Tenaga Pengajar', 'Master, Diploma 4, ', NULL, '-', 5636, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(16, 391, 148, NULL, 'Ditenun', NULL, 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Diploma 3, ', NULL, '-', 3727, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(17, 72, 50, NULL, 'Doni Arzinal', 'doni@del.ac.id', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', '', NULL, '-', 1437, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(18, 7, 7, '0308010001', 'Dr. Arnaldo Marulitua Sinaga, ST., M.InfoTech.', 'arnaldo.s@lycos.com, aldo@del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'D', 'Lektor Kepala', 'Sarjana, Master, Doktor, ', NULL, '0115017701', 1386, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(19, 89, 84, '0308110041', 'Edwin Swandi Sijabat, SST', NULL, 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Diploma 3, Diploma 4, ', NULL, '9931000088', 1451, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(20, 63, 46, '0309080019', 'Eka Stephani Sinambela, SST., M.Sc.', NULL, 3, 'DIII Teknologi Komputer', 'B', 'Asisten Ahli', 'Diploma 3, Diploma 4, Master, ', NULL, '0117078706', 1428, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(21, 171, 86, '0308150145', 'Ernie Bertha Nababan, S.Pd, M.Pd', NULL, 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'A', 'Tenaga Pengajar', 'Sarjana, Master, ', NULL, '0130058604', 1919, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(22, 449, 194, '0303210402', 'Febrian Winston Hutagalung, S.T.', 'febrian.hutagalung@del.ac.id , febrianhtg24@gmail.com', 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Sarjana, Master, ', NULL, '-', 5109, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(23, 380, 133, '0301190332', 'Fica Aida Nadhifatul Aini, S.ST.,M.Sc', NULL, 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Master, ', NULL, '-', 3713, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(24, 136, 69, '0308140103', 'Fitriani Tupa Ronauli Silalahi, S.Si, M.Si', 'fitri.silalahi@del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'B', 'Asisten Ahli', 'Master, Sarjana, Doktor, ', NULL, '0112039001', 1486, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(25, 40, 32, NULL, 'Fredy Sibarani', 'fredy@del.ac.id,fredy_sibarani@yahoo.com', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Diploma 3, Diploma 4, ', NULL, '-', 1413, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(26, 213, 152, '0309160183', 'Frengki Sardion Simatupang, S.Tr. Kom', NULL, 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Diploma 3, Master, Master, ', NULL, '5234726424', 2621, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(27, 88, 83, '0309110047', 'Gerry Italiano Wowiling, S.Tr.Kom', 'gerry@del.ac.id', 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Diploma 3, Diploma 4, Master, ', NULL, '-', 1450, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(28, 116, 143, '0309130081', 'Goklas Henry Agus Panjaitan, S.Tr.Kom', 'goklas.panjaitan@del.ac.id;goklasif10029@gmail.com', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', '', NULL, '-', 1475, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(29, 10, 10, NULL, 'Graham Neil Hornby', 'leeds2836@yahoo.com', 1, 'DIII Teknologi Informasi', '-', '-', '', NULL, '-', 1389, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(30, 38, 31, NULL, 'Henry Edison Sitorus', 'henry@del.ac.id, henry_sitorus@yahoo.com', 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Diploma 3, ', NULL, '-', 1411, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(31, 75, 136, '0309100028', 'Hernawati Susanti Samosir, SST., M.Kom.', NULL, 1, 'DIII Teknologi Informasi', 'B', 'Asisten Ahli', 'Master, ', NULL, '0124098904', 1440, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(32, 198, 98, '0303160167', 'Ike Fitriyaningsih, S.Si., M.Si', NULL, 1, 'DIII Teknologi Informasi', 'C', 'Lektor', 'Master, Sarjana, ', NULL, '0109049001', 1977, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(33, 110, 88, '0303130070', 'Immanuel Panjaitan, S.Kom', 'immanuel@del.ac.id', 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Sarjana, ', NULL, '9931000089', 1470, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(34, 73, 51, NULL, 'Indra Siregar', 'indra@del.ac.id', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', '', NULL, '-', 1438, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(35, 257, 123, '0308180288', 'Istas Manalu, S.Si., M.Sc', NULL, 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Master, ', NULL, '0104088902', 3579, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(36, 108, 59, '0302150133', 'Kisno, S.Pd, M.Pd', 'kisno@del.ac.id', 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Sarjana, Master, ', NULL, '0116108503', 1468, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(37, 5, 5, NULL, 'Kurnia Djaja', 'kurnia@del.ac.id, kurenia_djaja@msn.com', 3, 'DIII Teknologi Komputer', '-', '-', 'Diploma 3, ', NULL, '-', 1385, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(38, 219, 107, '0302170241', 'Maisevli Harika, M.T, M.Eng.', NULL, 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Master, Diploma 4, Master, ', NULL, '0121048604', 2632, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(39, 55, 39, '0303070014', 'Marojahan MT. Sigiro, ST., M.Sc', 'marojahan@gmail.com', 3, 'DIII Teknologi Komputer', 'B', 'Asisten Ahli', 'Diploma 3, Sarjana, Master, ', NULL, '0108098301', 1421, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(40, 18, 18, NULL, 'Mauritz Panggabean', NULL, 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Diploma 3, ', NULL, '-', 1397, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(41, 256, 122, '0306180281', 'Monalisa Pasaribu, SS., M.Ed (TESOL)', NULL, 1, 'DIII Teknologi Informasi', 'B', 'Asisten Ahli', 'Master, ', NULL, '0113118602', 3102, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(42, 191, 92, '0311150160', 'Mukhammad Solikhin, S.Si, M.Si', NULL, 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'B', 'Asisten Ahli', 'Master, Sarjana, ', NULL, '0131089001', 1958, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(43, 142, 166, '0309140122', 'Novalina Hutabarat, A.Md', 'novalina.hutabarat@del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'A', 'Tenaga Pengajar', 'Diploma 4, ', NULL, '-', 1492, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(44, 487, 168, '0301xxxx', 'Novalina Hutabarat, S.Kom., M.T.I', 'novalina@del.ac.id', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Diploma 3, ', NULL, '-', 5766, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(45, 197, 97, '0303160166', 'Olnes Yosefa Hutajulu, S.Pd., M.Eng', NULL, 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Sarjana, Master, ', NULL, '0130088902', 1976, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(46, 12, 12, NULL, 'Ondy Dharma Indra Sukma', 'ondy_dharma@yahoo.com', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Diploma 3, ', NULL, '-', 1391, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(47, 273, 180, '03101803113', 'Oppir Hutapea, S.Tr.Kom', NULL, 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'B', 'Asisten Ahli', 'Diploma 4, Master, ', NULL, '0107049603', 3607, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(48, 192, 96, '0301160162', 'Pandapotan Napitupulu, S.T, M.T', NULL, 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Master, Sarjana, ', NULL, '0130128202', 1959, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(49, 31, 24, '030919K038', 'Prof. Dr. Ir. Saswinadi Sasmojo, M.Sc, Ph.D', NULL, 1, 'DIII Teknologi Informasi', 'E', 'Guru Besar', 'Diploma 3, Diploma 4, Sarjana, ', NULL, '-', 1406, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(50, 11, 11, NULL, 'Ramot Lubis', 'lubis@del.ac.id, motssl@yahoo.com', 3, 'DIII Teknologi Komputer', 'B', 'Asisten Ahli', 'Diploma 3, Diploma 4, ', NULL, '-', 1390, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(51, 267, 126, '0309180301', 'Regina Ayunita Tarigan, S.Si., M.Sc', NULL, 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'B', 'Asisten Ahli', 'Master, Sarjana, ', NULL, '0125109101', 3594, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(52, 115, 146, '0309130080', 'Rini Juliana Sipahutar, S.Tr. Kom', 'rini.sipahutar@del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'A', 'Tenaga Pengajar', 'Diploma 1, Master, ', NULL, '-', 1474, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(53, 81, 52, '0303110035', 'Riyanthi Angrainy Sianturi, S.Sos, M.Ds', 'riyanthi@del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'C', 'Lektor', 'Diploma 3, Sarjana, Master, ', NULL, '0121058503', 1445, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(54, 62, 45, NULL, 'Roberto', 'roberto@del.ac.id', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', '', NULL, '-', 1427, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(55, 66, 47, '0309090020', 'Roy Deddy Hasiholan Lumban Tobing, S.T., M.ICT', 'roy.deddy@del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'B', 'Asisten Ahli', 'Sarjana, Master, Diploma 4, ', NULL, '0121038401', 1431, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(56, 479, 162, '0301220432', 'Rudy Chandra, S.Kom., M.Kom', 'rudy.chandra@del.ac.id , rudychandra@gmail.com', 1, 'DIII Teknologi Informasi', 'C', 'Lektor', 'Sarjana, Master, ', NULL, '0120039502', 5755, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(57, 113, 60, '0308130076', 'Rumondang Miranda Marsaulina, S.P, M.Si', 'rumondang.naiborhu@del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'C', 'Lektor', 'Sarjana, Master, ', NULL, '0108057601', 1473, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(58, 483, 165, '0302220426', 'Sahat Pandapotan Nainggolan, S.Mat., M.PMat', 'sahat.nainggolan@del.ac.id , ', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'A', 'Tenaga Pengajar', 'Master, Sarjana, ', NULL, '0108089402', 5759, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(59, 459, 154, '21o387128419odshjns;kdcjwodhclpw', 'Samuel Christian Silalahi', 'samuel@del.ac.id , samuelsamuel@gmail.com', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Sarjana, Master, ', NULL, '-', 5505, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(60, 244, 121, '0304180277', 'Sari Muthia Silalahi, S.Pd., M.Ed', NULL, 3, 'DIII Teknologi Komputer', 'B', 'Asisten Ahli', 'Master, Sarjana, ', NULL, '0117109301', 3099, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(61, 230, 115, '0308170259', 'Teamsar Muliadi Panggabean, S.Kom, PGCert', NULL, 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Master, Sarjana, ', NULL, '0101098801', 3078, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(62, 441, 145, '0312200395', 'Tegar Arifin Prasetyo, S.Si., M.Si.', 'tegar.prasetyo@del.ac.id', 1, 'DIII Teknologi Informasi', 'A', 'Tenaga Pengajar', 'Master, ', NULL, '0112079601', 5101, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(63, 71, 49, NULL, 'Timbang Pangaribuan', NULL, 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', '', NULL, '-', 1436, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(64, 25, 116, '0309010005', 'Tiurma Lumban Gaol, SP., M.P', 'tiur@del.ac.id, tiurlg@yahoo.com', 1, 'DIII Teknologi Informasi', 'B', 'Asisten Ahli', 'Diploma 3, Master, ', NULL, '0108037605', 1402, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(65, 80, 104, '0309100024', 'Togu Novriansyah Turnip, S.S.T., M.IM', NULL, 1, 'DIII Teknologi Informasi', 'C', 'Lektor', 'Master, ', NULL, '0129118901', 1444, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(66, 86, 85, '0309110046', 'Tulus Pardamean Simanjuntak, SST', NULL, 3, 'DIII Teknologi Komputer', 'A', 'Tenaga Pengajar', 'Diploma 3, Diploma 4, ', NULL, '-', 1448, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(67, 92, 89, '0302120051', 'Verawaty Situmorang, S.Kom., M.T.I', 'verawaty@del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'B', 'Asisten Ahli', 'Diploma 3, Sarjana, Master, ', NULL, '0112058504', 1454, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(68, 8, 8, NULL, 'Vidya Vrat Agarwal', 'vidyavrat@rediffmail..com', 3, 'DIII Teknologi Komputer', '-', '-', 'Diploma 3, ', NULL, '-', 1387, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(69, 189, 90, '0309150151', 'Yohanssen Pratama, S.Si, M.T', NULL, 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'C', 'Lektor', 'Master, Sarjana, ', NULL, '0121058702', 1956, '2026-06-12 03:59:18', '2026-06-12 05:21:14'),
(70, 173, 87, '0309150148', 'Yuniarta Basani, S.Si, M.Si', NULL, 1, 'DIII Teknologi Informasi', 'B', 'Asisten Ahli', 'Master, Sarjana, ', NULL, '0117068901', 1925, '2026-06-12 03:59:18', '2026-06-12 05:21:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen_roles`
--

CREATE TABLE `dosen_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `KPA_id` bigint(20) UNSIGNED NOT NULL,
  `prodi_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `TM_id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('Aktif','Tidak-Aktif') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dosen_roles`
--

INSERT INTO `dosen_roles` (`id`, `created_at`, `updated_at`, `user_id`, `nama`, `KPA_id`, `prodi_id`, `role_id`, `TM_id`, `tahun_ajaran_id`, `status`) VALUES
(1, '2026-06-12 03:22:06', '2026-06-12 03:22:06', 3602, 'Cynthia Deborah Nababan, S.Tr.Kom.', 3, 4, 1, 1, 1, 'Aktif'),
(2, '2026-06-12 03:23:18', '2026-06-12 03:23:18', 3607, 'Oppir Hutapea, S.Tr.Kom', 1, 4, 1, 2, 1, 'Aktif'),
(3, '2026-06-14 02:56:12', '2026-06-14 02:56:12', 3602, NULL, 3, 4, 3, 1, 1, 'Aktif'),
(4, '2026-06-14 02:56:12', '2026-06-14 02:56:12', 3607, NULL, 3, 4, 5, 1, 1, 'Aktif'),
(5, '2026-06-14 03:14:55', '2026-06-14 03:14:55', 3607, NULL, 3, 4, 2, 1, 1, 'Aktif'),
(6, '2026-06-14 03:15:33', '2026-06-14 03:15:33', 5757, NULL, 3, 4, 4, 1, 1, 'Aktif'),
(7, '2026-06-14 03:34:16', '2026-06-14 03:34:16', 3602, NULL, 1, 4, 2, 2, 1, 'Aktif'),
(8, '2026-06-14 03:34:16', '2026-06-14 03:34:16', 3607, NULL, 1, 4, 4, 2, 1, 'Aktif');

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

--
-- Dumping data untuk tabel `failed_jobs`
--

INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(1, '6e32a13e-03fc-4e95-961f-3bbc67fb51c4', 'database', 'default', '{\"uuid\":\"6e32a13e-03fc-4e95-961f-3bbc67fb51c4\",\"displayName\":\"App\\\\Jobs\\\\SyncMatkulJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SyncMatkulJob\",\"command\":\"O:22:\\\"App\\\\Jobs\\\\SyncMatkulJob\\\":2:{s:8:\\\"\\u0000*\\u0000token\\\";s:320:\\\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6IlVOSVFVRS1KV1QtSURFTlRJRklFUiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpLmV4YW1wbGUuY29tIiwiYXVkIjoiaHR0cHM6XC9cL2Zyb250ZW5kLmV4YW1wbGUuY29tIiwianRpIjoiVU5JUVVFLUpXVC1JREVOVElGSUVSIiwiaWF0IjoxNzgxMjQxNTcxLCJleHAiOjE3ODEyNDQ1NzEsInVpZCI6NTA4Mn0.Il9a-XSXSOhxk3GpK38cyOWpSTz7rEo9Z7pZjKIFbdE\\\";s:19:\\\"chainCatchCallbacks\\\";a:0:{}}\",\"batchId\":null},\"createdAt\":1781241591,\"delay\":null}', 'GuzzleHttp\\Exception\\RequestException: cURL error 56: Recv failure: Connection was reset (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for https://cis-dev.del.ac.id/api/library-api/matkul-by-prodi-sem-ta?prodi_id=4&ta=2020&sem_ta=1 in D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\Handler\\CurlFactory.php:278\nStack trace:\n#0 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\Handler\\CurlFactory.php(207): GuzzleHttp\\Handler\\CurlFactory::createRejection(Object(GuzzleHttp\\Handler\\EasyHandle), Array)\n#1 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\Handler\\CurlFactory.php(159): GuzzleHttp\\Handler\\CurlFactory::finishError(Object(GuzzleHttp\\Handler\\CurlHandler), Object(GuzzleHttp\\Handler\\EasyHandle), Object(GuzzleHttp\\Handler\\CurlFactory))\n#2 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\Handler\\CurlHandler.php(47): GuzzleHttp\\Handler\\CurlFactory::finish(Object(GuzzleHttp\\Handler\\CurlHandler), Object(GuzzleHttp\\Handler\\EasyHandle), Object(GuzzleHttp\\Handler\\CurlFactory))\n#3 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\Handler\\Proxy.php(28): GuzzleHttp\\Handler\\CurlHandler->__invoke(Object(GuzzleHttp\\Psr7\\Request), Array)\n#4 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\Handler\\Proxy.php(48): GuzzleHttp\\Handler\\Proxy::GuzzleHttp\\Handler\\{closure}(Object(GuzzleHttp\\Psr7\\Request), Array)\n#5 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Client\\PendingRequest.php(1521): GuzzleHttp\\Handler\\Proxy::GuzzleHttp\\Handler\\{closure}(Object(GuzzleHttp\\Psr7\\Request), Array)\n#6 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Client\\PendingRequest.php(1480): Illuminate\\Http\\Client\\PendingRequest->Illuminate\\Http\\Client\\{closure}(Object(GuzzleHttp\\Psr7\\Request), Array)\n#7 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Client\\PendingRequest.php(1466): Illuminate\\Http\\Client\\PendingRequest->Illuminate\\Http\\Client\\{closure}(Object(GuzzleHttp\\Psr7\\Request), Array)\n#8 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\PrepareBodyMiddleware.php(35): Illuminate\\Http\\Client\\PendingRequest->Illuminate\\Http\\Client\\{closure}(Object(GuzzleHttp\\Psr7\\Request), Array)\n#9 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\Middleware.php(38): GuzzleHttp\\PrepareBodyMiddleware->__invoke(Object(GuzzleHttp\\Psr7\\Request), Array)\n#10 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\RedirectMiddleware.php(71): GuzzleHttp\\Middleware::GuzzleHttp\\{closure}(Object(GuzzleHttp\\Psr7\\Request), Array)\n#11 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\Middleware.php(63): GuzzleHttp\\RedirectMiddleware->__invoke(Object(GuzzleHttp\\Psr7\\Request), Array)\n#12 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\HandlerStack.php(75): GuzzleHttp\\Middleware::GuzzleHttp\\{closure}(Object(GuzzleHttp\\Psr7\\Request), Array)\n#13 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\Client.php(333): GuzzleHttp\\HandlerStack->__invoke(Object(GuzzleHttp\\Psr7\\Request), Array)\n#14 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\Client.php(169): GuzzleHttp\\Client->transfer(Object(GuzzleHttp\\Psr7\\Request), Array)\n#15 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\guzzlehttp\\guzzle\\src\\Client.php(189): GuzzleHttp\\Client->requestAsync(\'GET\', Object(GuzzleHttp\\Psr7\\Uri), Array)\n#16 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Client\\PendingRequest.php(1306): GuzzleHttp\\Client->request(\'GET\', \'https://cis-dev...\', Array)\n#17 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Client\\PendingRequest.php(1043): Illuminate\\Http\\Client\\PendingRequest->sendRequest(\'GET\', \'https://cis-dev...\', Array)\n#18 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Support\\helpers.php(327): Illuminate\\Http\\Client\\PendingRequest->Illuminate\\Http\\Client\\{closure}(1)\n#19 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Client\\PendingRequest.php(1041): retry(0, Object(Closure), 100, Object(Closure))\n#20 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Client\\PendingRequest.php(849): Illuminate\\Http\\Client\\PendingRequest->send(\'GET\', \'https://cis-dev...\', Array)\n#21 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\app\\Services\\MatkulSyncService.php(24): Illuminate\\Http\\Client\\PendingRequest->get(\'https://cis-dev...\', Array)\n#22 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\app\\Jobs\\SyncMatkulJob.php(28): App\\Services\\MatkulSyncService->sync(\'eyJ0eXAiOiJKV1Q...\')\n#23 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): App\\Jobs\\SyncMatkulJob->handle(Object(App\\Services\\MatkulSyncService))\n#24 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#25 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#26 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#27 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#28 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(129): Illuminate\\Container\\Container->call(Array)\n#29 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}(Object(App\\Jobs\\SyncMatkulJob))\n#30 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\SyncMatkulJob))\n#31 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(133): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#32 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(136): Illuminate\\Bus\\Dispatcher->dispatchNow(Object(App\\Jobs\\SyncMatkulJob), false)\n#33 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}(Object(App\\Jobs\\SyncMatkulJob))\n#34 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\SyncMatkulJob))\n#35 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(129): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#36 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(App\\Jobs\\SyncMatkulJob))\n#37 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#38 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(485): Illuminate\\Queue\\Jobs\\Job->fire()\n#39 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(435): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#40 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(201): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#41 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#42 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#43 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#44 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#45 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#46 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#47 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#48 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(211): Illuminate\\Container\\Container->call(Array)\n#49 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\symfony\\console\\Command\\Command.php(341): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#50 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(180): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#51 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\symfony\\console\\Application.php(1102): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#52 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\symfony\\console\\Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#53 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\symfony\\console\\Application.php(195): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#54 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#55 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#56 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#57 {main}\n\nNext Illuminate\\Http\\Client\\ConnectionException: cURL error 56: Recv failure: Connection was reset (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for https://cis-dev.del.ac.id/api/library-api/matkul-by-prodi-sem-ta?prodi_id=4&ta=2020&sem_ta=1 in D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Client\\PendingRequest.php:1822\nStack trace:\n#0 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Client\\PendingRequest.php(1085): Illuminate\\Http\\Client\\PendingRequest->marshalRequestExceptionWithoutResponse(Object(GuzzleHttp\\Exception\\RequestException))\n#1 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Support\\helpers.php(327): Illuminate\\Http\\Client\\PendingRequest->Illuminate\\Http\\Client\\{closure}(1)\n#2 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Client\\PendingRequest.php(1041): retry(0, Object(Closure), 100, Object(Closure))\n#3 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Client\\PendingRequest.php(849): Illuminate\\Http\\Client\\PendingRequest->send(\'GET\', \'https://cis-dev...\', Array)\n#4 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\app\\Services\\MatkulSyncService.php(24): Illuminate\\Http\\Client\\PendingRequest->get(\'https://cis-dev...\', Array)\n#5 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\app\\Jobs\\SyncMatkulJob.php(28): App\\Services\\MatkulSyncService->sync(\'eyJ0eXAiOiJKV1Q...\')\n#6 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): App\\Jobs\\SyncMatkulJob->handle(Object(App\\Services\\MatkulSyncService))\n#7 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#8 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#9 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#10 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#11 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(129): Illuminate\\Container\\Container->call(Array)\n#12 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}(Object(App\\Jobs\\SyncMatkulJob))\n#13 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\SyncMatkulJob))\n#14 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(133): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#15 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(136): Illuminate\\Bus\\Dispatcher->dispatchNow(Object(App\\Jobs\\SyncMatkulJob), false)\n#16 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}(Object(App\\Jobs\\SyncMatkulJob))\n#17 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(App\\Jobs\\SyncMatkulJob))\n#18 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(129): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#19 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(App\\Jobs\\SyncMatkulJob))\n#20 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#21 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(485): Illuminate\\Queue\\Jobs\\Job->fire()\n#22 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(435): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#23 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(201): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#24 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#25 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#26 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#27 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#28 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#29 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#30 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#31 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(211): Illuminate\\Container\\Container->call(Array)\n#32 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\symfony\\console\\Command\\Command.php(341): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#33 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(180): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#34 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\symfony\\console\\Application.php(1102): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#35 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\symfony\\console\\Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#36 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\symfony\\console\\Application.php(195): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#37 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#38 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#39 D:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\ui laravel\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#40 {main}', '2026-06-12 05:20:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `waktu_mulai` datetime NOT NULL,
  `waktu_selesai` datetime NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `ruangan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `KPA_id` bigint(20) UNSIGNED DEFAULT NULL,
  `prodi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `TM_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
-- Struktur dari tabel `judul_proyek_akhir`
--

CREATE TABLE `judul_proyek_akhir` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `judul_proyek_akhir`
--

INSERT INTO `judul_proyek_akhir` (`id`, `kelompok_id`, `judul`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 10, 'fewef', 'wfeer', NULL, NULL),
(2, 2, 'Pengembangan Sistem Agent-Based berbasis Large Language Model untuk Otomatisasi Administrasi Proyek Akhir Mahasiswa Institut Teknologi Del', 'Proses administrasi, seperti pembentukan kelompok, penentuan dosen pembimbing, penentuan dosen penguji, dan penjadwalan seminar, dapat dilakukan secara lebih terstruktur dan terdokumentasi. Sistem membantu mengurangi potensi kesalahan yang muncul akibat proses manual, pencatatan yang tidak terdokumentasi, serta komunikasi yang tidak terkoordinasi. Dengan demikian, proses administrasi Proyek Akhir menjadi lebih efektif, transparan, dan mudah diawasi oleh pihak terkait.\n', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kartu_bimbingan`
--

CREATE TABLE `kartu_bimbingan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_bimbingan_id` bigint(20) UNSIGNED NOT NULL,
  `pembimbing_id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_bimbingan` date NOT NULL,
  `hasil_bimbingan` text NOT NULL,
  `tanda_tangan_pembimbing` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_pa`
--

CREATE TABLE `kategori_pa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kategori_pa` enum('PA-1','PA-2','PA-3') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori_pa`
--

INSERT INTO `kategori_pa` (`id`, `kategori_pa`, `created_at`, `updated_at`) VALUES
(1, 'PA-1', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(2, 'PA-2', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(3, 'PA-3', '2026-06-12 02:55:48', '2026-06-12 02:55:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelompok`
--

CREATE TABLE `kelompok` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomor_kelompok` varchar(100) NOT NULL,
  `KPA_id` bigint(20) UNSIGNED NOT NULL,
  `prodi_id` bigint(20) UNSIGNED NOT NULL,
  `TM_id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('Aktif','Tidak-Aktif') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kelompok`
--

INSERT INTO `kelompok` (`id`, `nomor_kelompok`, `KPA_id`, `prodi_id`, `TM_id`, `tahun_ajaran_id`, `status`, `created_at`, `updated_at`) VALUES
(1, '1', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(2, '2', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(3, '3', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(4, '4', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(5, '5', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(6, '6', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(7, '7', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(8, '8', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(9, '9', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(10, '10', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(11, '11', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(12, '12', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(13, '13', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(14, '14', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(15, '15', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(16, '16', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(17, '17', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(18, '18', 1, 4, 2, 1, 'Aktif', '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(19, '1', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(20, '2', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(21, '3', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(22, '4', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(23, '5', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(24, '6', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(25, '7', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(26, '8', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(27, '9', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(28, '10', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(29, '11', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(30, '12', 3, 4, 1, 1, 'Aktif', '2026-06-12 04:04:57', '2026-06-12 04:04:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelompok_mahasiswa`
--

CREATE TABLE `kelompok_mahasiswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kelompok_mahasiswa`
--

INSERT INTO `kelompok_mahasiswa` (`id`, `user_id`, `kelompok_id`, `created_at`, `updated_at`) VALUES
(1, 5038, 1, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(2, 5030, 1, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(3, 5073, 1, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(4, 4680, 1, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(5, 4703, 1, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(6, 5026, 2, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(7, 5069, 2, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(8, 5039, 2, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(9, 5053, 2, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(10, 5082, 2, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(11, 4692, 3, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(12, 5048, 3, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(13, 4704, 3, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(14, 5080, 3, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(15, 4685, 3, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(16, 5059, 4, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(17, 5035, 4, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(18, 5055, 4, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(19, 5057, 4, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(20, 5023, 4, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(21, 5024, 5, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(22, 4690, 5, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(23, 4683, 5, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(24, 5078, 5, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(25, 5074, 5, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(26, 4701, 6, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(27, 5065, 6, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(28, 4688, 6, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(29, 5063, 6, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(30, 5036, 6, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(31, 4691, 7, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(32, 5071, 7, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(33, 5022, 7, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(34, 5045, 7, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(35, 4695, 7, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(36, 5058, 8, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(37, 4702, 8, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(38, 4697, 8, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(39, 5075, 8, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(40, 5037, 8, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(41, 4694, 9, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(42, 4681, 9, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(43, 5083, 9, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(44, 5070, 9, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(45, 4700, 9, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(46, 5066, 10, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(47, 5072, 10, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(48, 5067, 10, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(49, 5034, 10, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(50, 4689, 10, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(51, 5050, 11, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(52, 5040, 11, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(53, 5043, 11, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(54, 5042, 11, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(55, 4687, 11, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(56, 5033, 12, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(57, 5047, 12, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(58, 5031, 12, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(59, 5025, 12, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(60, 4693, 12, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(61, 5077, 13, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(62, 5051, 13, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(63, 5061, 13, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(64, 5046, 13, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(65, 4682, 13, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(66, 5041, 14, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(67, 5028, 14, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(68, 5044, 14, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(69, 5056, 14, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(70, 4684, 14, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(71, 5062, 15, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(72, 5076, 15, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(73, 5060, 15, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(74, 4699, 15, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(75, 5079, 15, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(76, 4698, 16, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(77, 5068, 16, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(78, 4686, 16, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(79, 4696, 16, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(80, 5081, 17, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(81, 5054, 17, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(82, 5064, 17, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(83, 5029, 17, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(84, 5027, 18, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(85, 5052, 18, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(86, 5032, 18, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(87, 5049, 18, '2026-06-12 04:00:53', '2026-06-12 04:00:53'),
(88, 4534, 19, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(89, 4531, 19, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(90, 4527, 19, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(91, 4569, 19, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(92, 4573, 19, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(93, 4572, 19, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(94, 4585, 20, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(95, 4550, 20, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(96, 4524, 20, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(97, 4559, 20, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(98, 4579, 20, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(99, 4535, 20, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(100, 4582, 21, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(101, 4578, 21, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(102, 4551, 21, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(103, 4545, 21, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(104, 4548, 21, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(105, 4546, 21, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(106, 4522, 22, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(107, 4570, 22, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(108, 4562, 22, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(109, 4533, 22, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(110, 4525, 22, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(111, 4575, 22, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(112, 4528, 23, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(113, 4558, 23, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(114, 4584, 23, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(115, 4549, 23, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(116, 4571, 23, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(117, 4537, 23, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(118, 4577, 24, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(119, 4587, 24, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(120, 4624, 24, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(121, 4576, 24, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(122, 4561, 24, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(123, 4565, 24, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(124, 4560, 25, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(125, 4536, 25, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(126, 4539, 25, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(127, 4544, 25, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(128, 4574, 25, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(129, 4552, 25, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(130, 4566, 26, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(131, 4555, 26, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(132, 4543, 26, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(133, 4581, 26, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(134, 4567, 26, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(135, 4526, 26, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(136, 4553, 27, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(137, 4580, 27, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(138, 4564, 27, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(139, 4541, 27, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(140, 4586, 27, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(141, 4529, 27, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(142, 4530, 28, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(143, 4542, 28, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(144, 4547, 28, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(145, 4556, 28, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(146, 4540, 28, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(147, 4583, 29, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(148, 4557, 29, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(149, 4538, 29, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(150, 4554, 29, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(151, 4532, 29, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(152, 4521, 30, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(153, 4568, 30, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(154, 4523, 30, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(155, 4563, 30, '2026-06-12 04:04:57', '2026-06-12 04:04:57'),
(156, 4588, 30, '2026-06-12 04:04:57', '2026-06-12 04:04:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dim_id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `nim` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `prodi_id` int(11) NOT NULL,
  `prodi_name` varchar(255) NOT NULL,
  `fakultas` varchar(255) NOT NULL,
  `angkatan` int(11) NOT NULL,
  `nomor_telepon` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `asrama` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `dim_id`, `user_id`, `user_name`, `nim`, `nama`, `email`, `prodi_id`, `prodi_name`, `fakultas`, `angkatan`, `nomor_telepon`, `status`, `asrama`, `created_at`, `updated_at`) VALUES
(1, 727, 725, 'if11082', '11111082', 'Johannes Fernando Pasaribu', 'if11082@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(2, 683, 681, 'if11035', '11111035', 'Maranatha Purba', 'if11035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(3, 66, 67, 'if02067', '11102067', 'A.Farizal P.Tambunan', 'if02067@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(4, 2134, 2537, 'if416013', '11416013', 'Abdi Elman D A', 'if416013@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(5, 817, 815, 'if312039', '11112039', 'Abdi Marulitua Sipahutar', 'if312039@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(6, 1596, 1737, 'if315020', '11315020', 'Abed Nego Lubis', 'if315020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(7, 1140, 1138, 'ce314023', '13314023', 'Abednego Ginting', 'ce314023@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(8, 2139, 2542, 'if416018', '11416018', 'Abi Gail Simatupang', 'if416018@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(9, 2697, 3195, 'ce318020', '13318020', 'Abonando Martua Raja Simarmata', 'ce318020@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(10, 4328, 5038, 'if420042', '11420042', 'ABRAM WIRAYUDA PANE', 'if420042@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(11, 4015, 4725, 'if320019', '11320019', 'Abriel Yosua Christofel', 'if320019@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(12, 531, 530, 'if09072', '11109072', 'Ade Candra Immanuel Simamora', 'if09072@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(13, 2267, 2733, 'ce317006', '13317006', 'Ade Kurniawan', 'ce317006@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(14, 2736, 3235, 'if418050', '11418050', 'Ade Putra Rejeki', 'if418050@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(15, 1760, 1902, 'ce315017', '13315017', 'Adedimita', 'ce315017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(16, 1132, 1130, 'ce314015', '13314015', 'Adelina Bunga Orion', 'ce314015@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(17, 347, 348, 'if06043', '11106043', 'Adelina Irmadewita Siagian', 'if06043@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(18, 2240, 2706, 'if317047', '11317047', 'Adelya Putri Sitanggang', 'if317047@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(19, 3731, 4405, 'ce319015', '13319015', 'Adi Boy Sitorus', 'ce319015@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(20, 869, 867, 'if312096', '11112096', 'Adi Gunawan', 'if312096@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(21, 418, 418, 'if07096', '11107096', 'Adi Hendra Sitorus Pane', 'if07096@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(22, 2142, 2545, 'if416021', '11416021', 'Adi Siagian', 'if416021@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(23, 893, 891, 'if413006', '21113006', 'Adi Wibowo P.', 'if413006@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(24, 4459, 5197, 'if322003', '11322003', 'ADINDA HUTASOIT', 'if322003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(25, 3705, 4379, 'if319046', '11319046', 'Adinda Ramadani', 'if319046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(26, 1114, 1112, 'if314058', '11314058', 'Aditya Pranata Siregar', 'if314058@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(27, 528, 527, 'if09052', '11109052', 'Aditya Yedija Marojahan Situmeang', 'if09052@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(28, 788, 786, 'if412022', '21112022', 'Adreani Theresia Manalu', 'if412022@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(29, 849, 847, 'if312074', '11112074', 'Adventina Oikumene Nababan', 'if312074@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(30, 2699, 3197, 'ce318025', '13318025', 'Ady D Aruan', 'ce318025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(31, 230, 231, 'if04040', '11104040', 'AFRILIANDO MALAU', 'if04040@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(32, 4316, 5026, 'if420030', '11420030', 'Agnes Feni Rosalina Naibaho', 'if420030@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(33, 2626, 3123, 'if318062', '11318062', 'Agnes Friska Gultom', 'if318062@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(34, 1082, 1080, 'if314026', '11314026', 'Agnes Lasma Siregar', 'if314026@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(35, 4048, 4758, 'if320052', '11320052', 'Agnes Mebyolla Turnip', 'if320052@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(36, 4045, 4755, 'if320049', '11320049', 'Agnes Pesta Rani Silalahi', 'if320049@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(37, 897, 895, 'if413070', '21113070', 'Agny J. M. Pardosi', 'if413070@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(38, 1026, 1024, 'if313088', '11113088', 'Agung Nadapdap', 'if313088@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(39, 3891, 4565, 'if419045', '11419045', 'Agus Rokyanto S', 'if419045@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(40, 4391, 5126, 'if321010', '11321010', 'Agus Sitorus', 'if321010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(41, 2100, 2502, 'ce316010', '13316010', 'Agustin Tampubolon', 'ce316010@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(42, 917, 915, 'if413072', '21113072', 'Agustinus M. Tua Sijabat', 'if413072@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(43, 3984, 4692, 'if420013', '11420013', 'Ajipon Kogoya', 'if420013@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(44, 2453, 2920, 'if319058', '11319058', 'Ajuanda Sitorus', 'if319058@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(45, 4415, 5150, 'if321034', '11321034', 'AKDES SIMON SIMAMORA', 'if321034@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(46, 4397, 5132, 'if321016', '11321016', 'ALBERT ARTA DANYOAN MANIK', 'if321016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(47, 4349, 5059, 'if420063', '11420063', 'Albert Butarbutar', 'if420063@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(48, 950, 948, 'if413071', '21113071', 'Albert K. Waruwu', 'if413071@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(49, 4412, 5147, 'if321031', '11321031', 'Albert Rapael Aritonang', 'if321031@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(50, 176, 177, 'if03086', '11103086', 'Albert Risflo H', 'if03086@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(51, 2713, 3211, 'ce318057', '13318057', 'Albertinus D. Siahaan', 'ce318057@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(52, 1161, 1159, 'if414014', '11414014', 'Alders Hutabarat', 'if414014@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(53, 2761, 3260, 'if418039', '11418039', 'Aldy Oki Jatnika Putra', 'if418039@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(54, 996, 994, 'if313053', '11113053', 'Alex Fransiscus Manihuruk', 'if313053@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(55, 3673, 4347, 'if319014', '11319014', 'Alex Sander Hutapea', 'if319014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(56, 80, 81, 'if02085', '11102085', 'Alexander Hutapea', 'if02085@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(57, 162, 163, 'if03050', '11103050', 'Alexander Jaya Pardomuan Sibarani', 'if03050@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(58, 1776, 1965, 'if415035', '11415035', 'Alexander Lumban Tobing', 'if415035@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(59, 763, 761, 'if11101', '11111101', 'Alexander Lumban Tobing', 'if11101@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(60, 2153, 2561, 'IF416022', '11416022', 'Alexander Nicholas Salim', 'if416022@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(61, 2694, 3191, 'ce318007', '13318007', 'Alexius Humbang Manik', 'ce318007@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(62, 863, 861, 'if312090', '11112090', 'Alfin Rholas Jonathan Situngkir', 'if312090@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(63, 2131, 2534, 'if416010', '11416010', 'Alfred Chrisdianto Simanjuntak', 'if416010@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(64, 3684, 4358, 'if319025', '11319025', 'Alfredo Calvin Manalu', 'if319025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(65, 4314, 5024, 'if420028', '11420028', 'Alfredo Jeremy Eksaudi Hutagalung', 'if420028@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(66, 2101, 2503, 'ce316011', '13316011', 'Alfredo Syah Putra Sitanggang', 'ce316011@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(67, 841, 839, 'if312066', '11112066', 'Ali Chohen Samosir', 'if312066@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(68, 414, 414, 'if07089', '11107089', 'Allan Basthian Pinem', 'if07089@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(69, 2159, 2595, 'if316042', '11316042', 'Almanus Wanena', 'if316042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(70, 2707, 3205, 'ce318035', '13318035', 'Almino Estomihi Siregar', 'ce318035@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(71, 3993, 4701, 'if420022', '11420022', 'Alur Yigibalom', 'if420022@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(72, 2757, 3256, 'if418021', '11418021', 'Alvin Immanuel Simbolon', 'if418021@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(73, 4436, 5171, 'if321055', '11321055', 'Amanda Artha Regina Simbolon', 'if321055@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(74, 2232, 2698, 'if317022', '11317022', 'Amelia Arta Rezki Manurung', 'if317022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(75, 116, 117, 'if01002', '11101002', 'Amelia Irna Mayarni Sitohang', 'if01002@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(76, 3674, 4348, 'if319015', '11319015', 'Amelia Yusnita Sitanggang', 'if319015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(77, 1743, 1885, 'if415034', '11415034', 'Amendo Mariesto Sitinjak', 'if415034@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(78, 3983, 4691, 'if420012', '11420012', 'Amiton Wanimbo', 'if420012@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(79, 1176, 1174, 'if414029', '11414029', 'Amos Suyanto Sitorus', 'if414029@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(80, 2182, 2615, 'ce316003', '13316003', 'Amran Manurung', 'ce316003@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(81, 2216, 2682, 'if317036', '11317036', 'Amri Simanjuntak', 'if317036@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(82, 43, 44, 'if02043', '11102043', 'Amrin Salomo Pandapotan Sinaga', 'if02043@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(83, 2187, 2653, 'if317046', '11317046', 'Amsal Marulitua Sianipar', 'if317046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(84, 2215, 2681, 'if317035', '11317035', 'Amsal Sugihan Situmorang', 'if317035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(85, 298, 299, 'if05081', '11105081', 'AMSON UJUNG', 'if05081@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(86, 320, 321, 'if06014', '11106014', 'Ana Maria Agustina Nainggolan', 'if06014@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(87, 4462, 5200, 'if322006', '11322006', 'Anastasya Capritiani Marpaung', 'if322006@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(88, 832, 830, 'if312057', '11112057', 'Anastasya Hutasoit', 'if312057@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(89, 1731, 1873, 'if415021', '11415021', 'Anastasya Pehulisa Manullang', 'if415021@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(90, 2688, 3185, 'ce318051', '13318051', 'Andi Manjulang  Lumban Gaol', 'ce318051@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(91, 626, 624, 'if10013', '11110013', 'Andi Parlindungan Tampubolon', 'if10013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(92, 153, 154, 'if03094', '11103094', 'Andi Prasetya Marpaung', 'if03094@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(93, 2294, 2760, 'if417012', '11417012', 'Andika Simangunsong', 'if417012@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(94, 4012, 4722, 'if320016', '11320016', 'Andini Yosepha Panjaitan', 'if320016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(95, 2280, 2746, 'if417016', '11417016', 'Andre Deberva Montesque Sipayung', 'if417016@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(96, 1044, 1042, 'if313099', '11113099', 'Andre Fernando Partogi Sianipar', 'if313099@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(97, 3078, 3584, 'ce318016', '13318016', 'Andre Martua Manurung', 'ce318016@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(98, 4029, 4739, 'if320033', '11320033', 'Andre Pernando Hutabarat', 'if320033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(99, 4385, 5120, 'if321004', '11321004', 'Andre Rajagukguk', 'if321004@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(100, 2125, 2527, 'if416004', '11416004', 'Andre Reynaldo Sihombing', 'if416004@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(101, 2146, 2550, 'if416027', '11416027', 'Andre Samuel Panjaitan', 'if416027@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(102, 3677, 4351, 'if319018', '11319018', 'Andre Yohanes Jaya Siregar', 'if319018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(103, 1104, 1102, 'if314048', '11314048', 'Andreas Capri Panggabean', 'if314048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(104, 1006, 1004, 'if413079', '21113079', 'Andreas Hamonangan Sibuea', 'if413079@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(105, 2284, 2750, 'if417005', '11417005', 'Andreas Jansen Ramot Tampubolon', 'if417005@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(106, 4023, 4733, 'if320027', '11320027', 'ANDREAS JEREMY VITO BANJARNAHOR', 'if320027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(107, 3860, 4534, 'if419014', '11419014', 'Andree Panjaitan', 'if419014@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(108, 410, 410, 'if07085', '11107085', 'Andri Darmansyah', 'if07085@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(109, 1755, 1897, 'ce315011', '13315011', 'Andro Eriel Tambun', 'ce315011@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(110, 2266, 2732, 'ce317005', '13317005', 'Andronikus Silitonga', 'ce317005@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(111, 286, 287, 'if05066', '11105066', 'ANDRY DOLLY S.', 'if05066@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(112, 2066, 2466, 'if316006', '11316006', 'Andry F. Hutapea', 'if316006@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(113, 578, 577, 'if09024', '11109024', 'Andry Leonardo Hutagaol', 'if09024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(114, 497, 497, 'if08018', '11108018', 'Andy Leonard Amenity Siahaan', 'if08018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(115, 828, 826, 'if312053', '11112053', 'Andyka F. Haryanto Tampubolon', 'if312053@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(116, 4042, 4752, 'if320046', '11320046', 'Angel Margaretha', 'if320046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(117, 2668, 3165, 'if318044', '11318044', 'Angel Monapesta Lumban Gaol', 'if318044@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(118, 2109, 2511, 'ce316018', '13316018', 'Angela Cicilia Br. Silalahi', 'ce316018@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(119, 4348, 5058, 'if420062', '11420062', 'Angela One Erika', 'if420062@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(120, 780, 778, 'if412013', '21112013', 'Angelia Agustina Telaumbanua', 'if412013@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(121, 3741, 4415, 'ce319025', '13319025', 'Angelia Sondang Simanjuntak', 'ce319025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(122, 4388, 5123, 'if321007', '11321007', 'ANGELICA THERESIA MANURUNG', 'if321007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(123, 348, 349, 'if06044', '11106044', 'Angga Sanjaya Lingga', 'if06044@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(124, 2712, 3210, 'ce318052', '13318052', 'Anggiat Pangaribuan', 'ce318052@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(125, 1717, 1859, 'if415008', '11415008', 'Anggiat Saud Parulian', 'if415008@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(126, 465, 465, 'if08051', '11108051', 'Anggraini Christin Marpaung', 'if08051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(127, 4052, 4762, 'if320056', '11320056', 'Anggun Prihatini Napitupulu', 'if320056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(128, 2722, 3220, 'ce318024', '13318024', 'Angreny Neta R Silalahi', 'ce318024@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(129, 649, 647, 'if10063', '11110063', 'Angripa Almatota Nadapdap', 'if10063@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(130, 1007, 1005, 'if313056', '11113056', 'Anindya Romauli Manalu', 'if313056@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(131, 4120, 4830, 'ce320016', '13320016', 'Anisa Gultom', 'ce320016@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(132, 929, 927, 'if313022', '11113022', 'Anita Antonia Ginting', 'if313022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(133, 1012, 1010, 'if313043', '11113043', 'Anita Carolina Aritonang', 'if313043@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(134, 859, 857, 'if412027', '21112027', 'Anita Intansia Hutagaol', 'if412027@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(135, 3672, 4346, 'if319013', '11319013', 'Anita Lasmaria Siagian', 'if319013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(136, 1174, 1172, 'if414027', '11414027', 'Anita Tinurbasa Januarinda Situmorang', 'if414027@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(137, 2108, 2510, 'ce316019', '13316019', 'Anjas Joshua', 'ce316019@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(138, 2623, 3120, 'if318038', '11318038', 'Anjelin Hutauruk', 'if318038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(139, 2273, 2739, 'ce317003', '13317003', 'Anjelina Putri Napitu', 'ce317003@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(140, 3909, 4583, 'if419063', '11419063', 'Anjelina Sihombing', 'if419063@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(141, 2775, 3274, 'if418020', '11418020', 'Anna Sulastri Simanjuntak', 'if418020@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(142, 4143, 4853, 'ce320039', '13320039', 'Antonel Basa Manurung', 'ce320039@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(143, 321, 322, 'if06015', '11106015', 'Antony Pardomuan Siagian', 'if06015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(144, 3986, 4694, 'if420015', '11420015', 'Aper Kogoya', 'if420015@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(145, 1009, 1007, 'if313063', '11113063', 'Aprelia Maisara Tarihoran', 'if313063@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(146, 2116, 2518, 'ce316028', '13316028', 'Aprilda M Panjaitan', 'ce316028@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(147, 3713, 4387, 'if319054', '11319054', 'Aprilia Lestari Naibaho', 'if319054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(148, 1120, 1118, 'ce314003', '13314003', 'Aprilonita Simanjuntak', 'ce314003@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(149, 628, 626, 'if10011', '11110011', 'Apriwin Frans Sunggul Tamartmo Pangaribuan', 'if10011@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(150, 2303, 2769, 'if417004', '11417004', 'Apriyanti Sijabat', 'if417004@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(151, 942, 940, 'if413029', '21113029', 'Apriyanti Sitorus', 'if413029@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(152, 4482, 5220, 'if322026', '11322026', 'AQUSTIN ANGEL D TAMBUNAN', 'if322026@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(153, 3908, 4582, 'if419062', '11419062', 'Ares J. M. Pardosi', 'if419062@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(154, 1171, 1169, 'if414024', '11414024', 'Arief Edy Putra Dolitua Sibuea', 'if414024@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(155, 158, 159, 'if03066', '11103066', 'Arief Grando', 'if03066@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(156, 699, 697, 'if11051', '11111051', 'Aries Franata I. Sembiring', 'if11051@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(157, 898, 896, 'if413009', '21113009', 'Ariestoni S. Silalahi', 'if413009@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(158, 3880, 4554, 'if419034', '11419034', 'Arijona Purba', 'if419034@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(159, 226, 227, 'if04035', '11104035', 'ARIS J. HASIONELLIS MARPAUNG', 'if04035@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(160, 203, 204, 'if04006', '11104006', 'ARJUNA SAMUEL PARDEDE', 'if04006@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(161, 566, 565, 'if09012', '11109012', 'Armando Siagian', 'if09012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(162, 486, 486, 'if08007', '11108007', 'ARNI WENDERI SIHOMBING', 'if08007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(163, 1739, 1881, 'if415030', '11415030', 'Arren Rediman Yosafat Situngkir', 'if415030@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(164, 3735, 4409, 'ce319019', '13319019', 'Arta Hutapea', 'ce319019@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(165, 3709, 4383, 'if319050', '11319050', 'Artasya Natalia Simatupang', 'if319050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(166, 494, 494, 'if08015', '11108015', 'Artati Caroline Ruth Tampubolon', 'if08015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(167, 3702, 4376, 'if319043', '11319043', 'Artha Tessalonika Pardede', 'if319043@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(168, 3661, 4335, 'if319002', '11319002', 'Asido Agripo Panjaitan', 'if319002@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(169, 1609, 1751, 'if315014', '11315014', 'Asido Christian Panjaitan', 'if315014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(170, 1127, 1125, 'ce314010', '13314010', 'Asido M Pardosi', 'ce314010@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(171, 46, 47, 'if02046', '11102046', 'Asina Saut Marulitua Veronika Siagian', 'if02046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(172, 342, 343, 'if06038', '11106038', 'Aslon Damanik', 'if06038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(173, 325, 326, 'if06021', '11106021', 'Asrina Sianipar', 'if06021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(174, 316, 317, 'if06010', '11106010', 'Asry Munthe', 'if06010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(175, 445, 445, 'if07025', '11107025', 'Astina Rani Jusriny Nainggolan', 'if07025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(176, 420, 420, 'if07099', '11107099', 'Atur Cristina Pangaribuan', 'if07099@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(177, 2698, 3196, 'ce318021', '13318021', 'Awaldo Putra Marpaung', 'ce318021@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(178, 2120, 2522, 'ce316031', '13316031', 'Axel Nugraha Sianturi', 'ce316031@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(179, 2115, 2517, 'ce316026', '13316026', 'Ayu Asriani Tanjung', 'ce316026@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(180, 1773, 1915, 'ce315030', '13315030', 'Ayu Crismasela Butarbutar', 'ce315030@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(181, 2717, 3215, 'ce318008', '13318008', 'Ayu Hotmaida Naiborhu', 'ce318008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(182, 811, 809, 'if312033', '11112033', 'Ayu Musfita Nainggolan', 'if312033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(183, 2241, 2707, 'if317048', '11317048', 'Ayu Novita Ningsi Lumbantobing', 'if317048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(184, 1146, 1144, 'ce314029', '13314029', 'Ayu Putri Hijau', 'ce314029@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(185, 4002, 4712, 'if320006', '11320006', 'Ayuly Sari Sinambela', 'if320006@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(186, 113, 114, 'if01005', '11101005', 'Azizah Putri Nainggolan', 'if01005@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(187, 771, 769, 'if412005', '21112005', 'B. M. Yanti Siregar', 'if412005@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(188, 2704, 3202, 'ce318037', '13318037', 'Baginda Soemantri Siahaan', 'ce318037@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(189, 1756, 1898, 'ce315012', '13315012', 'Baktiar Gultom', 'ce315012@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(190, 239, 240, 'if05009', '11105009', 'Banirizki Telaumbanua', 'if05009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(191, 1103, 1101, 'if314047', '11314047', 'Banisar', 'if314047@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(192, 2740, 3239, 'if418058', '11418058', 'Banta Solagratia', 'if418058@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(193, 3872, 4546, 'if419026', '11419026', 'Bastian Aruan', 'if419026@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(194, 725, 723, 'if11080', '11111080', 'Bastian Paskal Situmorang', 'if11080@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(195, 2087, 2487, 'if316026', '11316026', 'Bellina Murniasi', 'if316026@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(196, 332, 333, 'if06028', '11106028', 'Benardo Siregar', 'if06028@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(197, 237, 238, 'if05006', '11105006', 'Benget Uli Basa Silitonga', 'if05006@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(198, 4413, 5148, 'if321032', '11321032', 'Bennedict Tambunan', 'if321032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(199, 1068, 1066, 'if314012', '11314012', 'Benni Dolles Pardosi', 'if314012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(200, 796, 794, 'if312012', '11112012', 'Benni Luasti Sinurat', 'if312012@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(201, 93, 94, 'if02099', '11102099', 'Benno Geimkeiweiz Haposan Silaen', 'if02099@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(202, 529, 528, 'if09049', '11109049', 'Benno Putra Sitinjak', 'if09049@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(203, 2185, 2651, 'if317034', '11317034', 'Beny Luis Fernando Tampubolon', 'if317034@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(204, 1909, 2308, 'ce318058', '13318058', 'Berkat Laia', 'ce318058@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(205, 3862, 4536, 'if419016', '11419016', 'Berliana Laurenza Br Simamora', 'if419016@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(206, 224, 225, 'if04033', '11104033', 'BERNARD SAMUEL ARITONANG', 'if04033@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(207, 886, 884, 'if312016', '11112016', 'Bernard Siahaan', 'if312016@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(208, 2246, 2712, 'if317062', '11317062', 'Bernika Arni Siahaan', 'if317062@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(209, 1113, 1111, 'if314057', '11314057', 'Bernike Christina Sitanggang', 'if314057@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(210, 427, 427, 'if07001', '11107001', 'Bernovan Munte', 'if07001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(211, 1603, 1744, 'if315027', '11315027', 'Berta Novalin Nainggolan', 'if315027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(212, 2279, 2745, 'ce317025', '13317025', 'Betric Amanda Rachelita', 'ce317025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(213, 3727, 4401, 'ce319011', '13319011', 'Bien Deroman Simatupang', 'ce319011@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(214, 994, 992, 'if313072', '11113072', 'Bima Pandapotan Sinaga', 'if313072@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(215, 2107, 2509, 'ce316021', '13316021', 'Bimo Amarullah', 'ce316021@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(216, 736, 734, 'if11009', '11111009', 'Binsar Fransisco Siahaan', 'if11009@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(217, 924, 922, 'if413027', '21113027', 'Bintang Thunder Rolintua Lumban Gaol', 'if413027@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Keluar', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(218, 596, 595, 'if09042', '11109042', 'Bintang Togi Marito  Siahaan', 'if09042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(219, 4508, 5246, 'if322052', '11322052', 'Blessherin Gabriela Pangaribuan', 'if322052@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(220, 4356, 5066, 'if420070', '11420070', 'Boby Heryanto H.S', 'if420070@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(221, 4109, 4819, 'ce320005', '13320005', 'Boby Samuel Haposan Aritonang', 'ce320005@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(222, 1092, 1090, 'if314036', '11314036', 'Bona Juliana Simanullang', 'if314036@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(223, 302, 303, 'if05085', '11105085', 'BONAR TYSON GULTOM', 'if05085@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(224, 51, 52, 'if02051', '11102051', 'Bonatua Parulian Lumban Tobing', 'if02051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(225, 400, 400, 'if07064', '11107064', 'Bonggar situmorang', 'if07064@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(226, 402, 402, 'if07070', '11107070', 'Bongguk Malatang Samuel Pangaribuan', 'if07070@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(227, 2754, 3253, 'if418015', '11418015', 'Bongson Pane', 'if418015@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(228, 1753, 1895, 'ce315009', '13315009', 'Bornok Juntukko Situmorang', 'ce315009@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(229, 2214, 2680, 'if317028', '11317028', 'Boy S. A. Hutagaol', 'if317028@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(230, 4430, 5165, 'if321049', '11321049', 'Boy Tri Anugrah', 'if321049@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(231, 913, 911, 'if413078', '21113078', 'Brams Pande Gorga Tua Silitonga', 'if413078@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(232, 4110, 4820, 'ce320006', '13320006', 'Bryan Oliver Batuara', 'ce320006@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(233, 2632, 3129, 'if318019', '11318019', 'Bryan Primus Exaudi Lumbantobing', 'if318019@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(234, 1151, 1149, 'if414004', '11414004', 'Budianto A. Hutauruk', 'if414004@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(235, 851, 849, 'if312076', '11112076', 'Bunga Arta Hutagaol', 'if312076@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11');
INSERT INTO `mahasiswa` (`id`, `dim_id`, `user_id`, `user_name`, `nim`, `nama`, `email`, `prodi_id`, `prodi_name`, `fakultas`, `angkatan`, `nomor_telepon`, `status`, `asrama`, `created_at`, `updated_at`) VALUES
(236, 2259, 2725, 'ce317026', '13317026', 'Bunga Jelita Silaen', 'ce317026@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(237, 762, 760, 'if11099', '11111099', 'Bylardo Putra Manalu', 'if11099@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(238, 982, 980, 'if313075', '11113075', 'Candra Butarbutar', 'if313075@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(239, 1545, 1674, 'if315007', '11315007', 'Candra Michael Panjaitan', 'if315007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(240, 436, 436, 'if07014', '11107014', 'Canggih Pramono Gultom', 'if07014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(241, 1133, 1131, 'ce314016', '13314016', 'Carl Lewis Eben Ezer Surbakti', 'ce314016@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(242, 4468, 5206, 'if322012', '11322012', 'CARLOKA BOAS ALBERTO S MELIALA', 'if322012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(243, 4485, 5223, 'if322029', '11322029', 'CARLOS MICHAEL MARPAUNG', 'if322029@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(244, 1054, 1052, 'if413061', '21113061', 'Carolina Sitorus', 'if413061@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(245, 4340, 5050, 'if420054', '11420054', 'CASANDRA RISYAH MARIA NAPITUPULU', 'if420054@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(246, 4497, 5235, 'if322041', '11322041', 'CECILIA LIMASTI CINTA SITUMORANG', 'if322041@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(247, 848, 846, 'if412031', '21112031', 'Cesario Putera Hasiholan Siringoringo', 'if412031@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(248, 4515, 5253, 'if322059', '11322059', 'Cesia Sauria Butar-Butar', 'if322059@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(249, 515, 515, 'if08042', '11108042', 'Chairul Friks Gunawan Manalu', 'if08042@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(250, 1108, 1106, 'if314052', '11314052', 'Chandra Hartono Hutauruk', 'if314052@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(251, 2642, 3139, 'if318041', '11318041', 'Chandra Lomo', 'if318041@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(252, 3671, 4345, 'if319012', '11319012', 'Chani Leirisa Siburian', 'if319012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(253, 887, 885, 'if313104', '11113104', 'Charlie Perdana Surya Siagian', 'if313104@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(254, 798, 796, 'if312015', '11112015', 'Charly Micolas Butarbutar', 'if312015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(255, 2667, 3164, 'if318043', '11318043', 'Chatrine F. Manurung', 'if318043@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(256, 2621, 3118, 'if318035', '11318035', 'Chelsy Situmorang', 'if318035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(257, 1726, 1868, 'if415017', '11415017', 'Chika Youlanda Hutapea', 'if415017@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(258, 2747, 3246, 'if418054', '11418054', 'Chindy Hutapea', 'if418054@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(259, 2237, 2703, 'if317041', '11317041', 'Chorintians Lucky Panjaitan', 'if317041@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(260, 878, 876, 'if312105', '11112105', 'Chris Van Basten Siahaan', 'if312105@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(261, 4107, 4817, 'ce320003', '13320003', 'Christian Andres Lumbantobing', 'ce320003@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(262, 4396, 5131, 'if321015', '11321015', 'Christian Benedict Lumbantoruan', 'if321015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(263, 408, 408, 'if07083', '11107083', 'Christian Bonggal Alaptua Sihombing', 'if07083@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(264, 4484, 5222, 'if322028', '11322028', 'Christian Jhon pranata Panjaitan', 'if322028@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(265, 4323, 5033, 'if420037', '11420037', 'Christian Laurens Sihotang', 'if420037@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(266, 4492, 5230, 'if322036', '11322036', 'CHRISTIAN YEHEZKIL GULTOM', 'if322036@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(267, 966, 964, 'if313050', '11113050', 'Christin Seventina Situmorang', 'if313050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(268, 750, 748, 'if11073', '11111073', 'Christine Cecylia Munthe', 'if11073@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(269, 2679, 3176, 'ce318003', '13318003', 'Christine Natal Anjelina Sitorus', 'ce318003@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(270, 2780, 3279, 'if418032', '11418032', 'Christine Nathasya Hutajulu', 'if418032@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(271, 4152, 4862, 'ce320048', '13320048', 'Christine Nova Hutahaean', 'ce320048@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(272, 4367, 5077, 'if420081', '11420081', 'Christine P. Manurung', 'if420081@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(273, 1770, 1912, 'ce315026', '13315026', 'Christopher Mika Andrew Siahaan', 'ce315026@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(274, 2610, 3107, 'if318042', '11318042', 'Christy Riris Talenta Situmorang', 'if318042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(275, 2188, 2654, 'if317013', '11317013', 'Cicasmi Hasibuan', 'if317013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(276, 2064, 2464, 'if316004', '11316004', 'Cici Damayanti Munthe', 'if316004@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(277, 4390, 5125, 'if321009', '11321009', 'Cici Yanti Lubis', 'if321009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(278, 2256, 2722, 'ce317007', '13317007', 'Cindy Andini Yuliana Sitorus', 'ce317007@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(279, 2226, 2692, 'if317009', '11317009', 'Cindy Claudia Sitanggang', 'if317009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(280, 1098, 1096, 'if314042', '11314042', 'Cindy Julisye Sihombing', 'if314042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(281, 2283, 2749, 'if417032', '11417032', 'Cinthya M Gurning', 'if417032@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(282, 4513, 5251, 'if322057', '11322057', 'Citra Grace Asri Nainggolan', 'if322057@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(283, 2651, 3148, 'if318003', '11318003', 'Citra Situmorang', 'if318003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(284, 931, 929, 'if313021', '11113021', 'Clara Mathilda Stephanie Manik', 'if313021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(285, 3913, 4587, 'if419067', '11419067', 'Clarita Butarbutar', 'if419067@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(286, 4407, 5142, 'if321026', '11321026', 'Claudia Panjaitan', 'if321026@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(287, 801, 799, 'if312022', '11112022', 'Claurensia Richa Sinaga', 'if312022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(288, 825, 823, 'if312049', '11112049', 'Conrad Winardo Siahaan', 'if312049@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(289, 2138, 2541, 'if416017', '11416017', 'Credichio Redemtus Tua Sihombing', 'if416017@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, '', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(290, 4140, 4850, 'ce320036', '13320036', 'Cristian Frans Pelly Nainggolan', 'ce320036@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(291, 4496, 5234, 'if322040', '11322040', 'CRISTIAN NICOLAS TAMBUNAN', 'if322040@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(292, 1162, 1160, 'if414015', '11414015', 'Cynthia Deborah Nababan', 'if414015@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(293, 3911, 4585, 'if419065', '11419065', 'Cyntia Evelin Simamora', 'if419065@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(294, 3714, 4388, 'if319055', '11319055', 'Cyntia Selvanda Panggabean', 'if319055@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(295, 1069, 1067, 'if314013', '11314013', 'D. Noverina Silaen', 'if314013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(296, 4027, 4737, 'if320031', '11320031', 'DAFFA NAUFAL LOKANANTA', 'if320031@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(297, 4387, 5122, 'if321006', '11321006', 'dafne yosephine simanjuntak', 'if321006@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(298, 372, 373, 'if06069', '11106069', 'Dahlan Faroga Sirait', 'if06069@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(299, 2691, 3188, 'ce318032', '13318032', 'Dahlia Wahyu Silaen', 'ce318032@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(300, 2163, 2599, 'if316049', '11316049', 'Dakison Yigibalom', 'if316049@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(301, 4409, 5144, 'if321028', '11321028', 'Dame Sisri Haryati Katarina Rumapea', 'if321028@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(302, 143, 144, 'if03043', '11103043', 'Dameria Veronika Manurung', 'if03043@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(303, 600, 598, 'if10039', '11110039', 'Dandes Mikael Sagito Hutapea', 'if10039@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(304, 877, 875, 'if312104', '11112104', 'Dani Novita Pratiwi', 'if312104@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(305, 612, 610, 'if10027', '11110027', 'Dani Royman Simanjuntak', 'if10027@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(306, 3847, 4521, 'if419001', '11419001', 'Daniel Alex Candra Simamora', 'if419001@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Skorsing', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(307, 1053, 1051, 'if313094', '11113094', 'Daniel Boy Marpaung', 'if313094@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(308, 3717, 4391, 'ce319001', '13319001', 'Daniel Desman Parasian Simangunsong', 'ce319001@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(309, 4331, 5041, 'if420045', '11420045', 'DANIEL EXAUDI PASARIBU', 'if420045@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(310, 587, 586, 'if09033', '11109033', 'Daniel Fanny Judika Pices', 'if09033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(311, 3890, 4564, 'if419044', '11419044', 'Daniel Napitupulu', 'if419044@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(312, 2686, 3183, 'ce318046', '13318046', 'Daniel Nugraha H.S', 'ce318046@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(313, 513, 513, 'if08040', '11108040', 'Daniel Oslanto', 'if08040@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(314, 4487, 5225, 'if322031', '11322031', 'DANIEL PANDAPOTAN MANALU', 'if322031@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(315, 881, 879, 'if312108', '11112108', 'Daniel Pandu Siregar', 'if312108@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(316, 3893, 4567, 'if419047', '11419047', 'Daniel Pangihutan Naibaho', 'if419047@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(317, 896, 894, 'if313003', '11113003', 'Daniel Panjaitan', 'if313003@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(318, 2647, 3144, 'if318056', '11318056', 'Daniel Pardomuan Sarumpaet', 'if318056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(319, 4470, 5208, 'if322014', '11322014', 'Daniel Siahaan', 'if322014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(320, 4003, 4713, 'if320007', '11320007', 'Daniel Silalahi', 'if320007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(321, 630, 628, 'if10009', '11110009', 'Daniel Sinurat', 'if10009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(322, 604, 602, 'if10035', '11110035', 'Daniel Sitorus', 'if10035@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(323, 1117, 1115, 'if314061', '11314061', 'Daniel Tua Hartopo Manullang', 'if314061@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(324, 3889, 4563, 'if419043', '11419043', 'Danuri Nainggolan', 'if419043@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(325, 3664, 4338, 'if319005', '11319005', 'Dany Panegratia Silaen', 'if319005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(326, 3875, 4549, 'if419029', '11419029', 'Darwin Sibarani', 'if419029@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(327, 4013, 4723, 'if320017', '11320017', 'DARWIS BUTARBUTAR', 'if320017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(328, 3660, 4334, 'if319001', '11319001', 'Daud Manurung', 'if319001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(329, 152, 153, 'if03096', '11103096', 'Daut Togu  Tua Sihombing', 'if03096@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(330, 653, 651, 'if10059', '11110059', 'David Bornog Tuah', 'if10059@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(331, 2684, 3181, 'ce318038', '13318038', 'David Calvin Lbn Tobing', 'ce318038@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(332, 2210, 2676, 'if317010', '11317010', 'David Christian Sitorus', 'if317010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(333, 1001, 999, 'if313081', '11113081', 'David Elkana Sitompul', 'if313081@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(334, 1728, 1870, 'if415022', '11415022', 'David Firdaus B Sianturi', 'if415022@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(335, 1727, 1869, 'if415018', '11415018', 'David Firdaus Bijaksana Sianturi', 'if415018@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(336, 1094, 1092, 'if314038', '11314038', 'David Frietz Pangaribuan', 'if314038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(337, 2645, 3142, 'if318053', '11318053', 'David Muliadi Butar-Butar', 'if318053@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(338, 2637, 3134, 'if318027', '11318027', 'David Pardamean Simatupang', 'if318027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(339, 802, 800, 'if312023', '11112023', 'David R. M. Panjaitan', 'if312023@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(340, 640, 638, 'if10072', '11110072', 'David Tomy Samosir', 'if10072@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(341, 2243, 2709, 'if317055', '11317055', 'Dayani Sihombing', 'if317055@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(342, 911, 909, 'if313016', '11113016', 'Debby Melfina Butarbutar', 'if313016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(343, 783, 781, 'if412017', '21112017', 'Debby Sischa Pardede', 'if412017@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(344, 4352, 5062, 'if420066', '11420066', 'Debi Elprina Silitonga', 'if420066@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(345, 2238, 2704, 'if317042', '11317042', 'Debi Yanti Simatupang', 'if317042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(346, 955, 953, 'if313102', '11113102', 'Debora Apriani Panggabean', 'if313102@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(347, 755, 753, 'if11091', '11111091', 'Debora Lovita Christy Pakpahan', 'if11091@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(348, 706, 704, 'if11008', '11111008', 'Debora Putri Tambunan', 'if11008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(349, 594, 593, 'if09040', '11109040', 'Deddy Permadi', 'if09040@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(350, 365, 366, 'if06061', '11106061', 'Dede Wahini Meantini', 'if06061@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(351, 2298, 2764, 'if417021', '11417021', 'Dedi Chandra', 'if417021@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(352, 232, 233, 'if05027', '11105027', 'DEDY JULIUS  NAINGGOLAN', 'if05027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(353, 2702, 3200, 'ce318031', '13318031', 'Dedy Samhaz Romulus Pardede', 'ce318031@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(354, 2166, 2602, 'if316036', '11316036', 'Dekiles Wanimbo', 'if316036@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(355, 2165, 2601, 'if316037', '11316037', 'Dekinus Wandik', 'if316037@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(356, 3990, 4698, 'if420019', '11420019', 'Deky Kristian  Yikwa', 'if420019@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(357, 2233, 2699, 'if317029', '11317029', 'Delta Pangaribuan', 'if317029@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(358, 635, 633, 'if10004', '11110004', 'Denni Prima Putra Roli Sembiring', 'if10004@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(359, 3882, 4556, 'if419036', '11419036', 'Denny Abraham Sinaga', 'if419036@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(360, 591, 590, 'if09037', '11109037', 'Deppy Erita Moraya Simarmata', 'if09037@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(361, 4521, 5509, 'if423001', '11423001', 'Dera Kogoya', 'if423001@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2023, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(362, 2167, 2603, 'if316046', '11316046', 'Derinus Wandik', 'if316046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(363, 842, 840, 'if312067', '11112067', 'Derseli Enjelina Marpaung', 'if312067@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(364, 2308, 2774, 'if417018', '11417018', 'Desi Chrisdamayanti Lubis', 'if417018@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(365, 2089, 2489, 'if316028', '11316028', 'Desi Enjelina Lubis', 'if316028@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(366, 2773, 3272, 'if418014', '11418014', 'Desi Sri Pasaribu', 'if418014@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(367, 308, 309, 'if06002', '11106002', 'Desi Winta Lora Sitanggang', 'if06002@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(368, 9, 10, 'if02009', '11102009', 'Desma Jagar Pangaribuan', 'if02009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(369, 1073, 1071, 'if314017', '11314017', 'Dessy Christin Sitorus', 'if314017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(370, 1072, 1070, 'if314016', '11314016', 'Dessy Dosma F. Sihombing', 'if314016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(371, 804, 802, 'if312025', '11112025', 'Dessy Grace Natalia Hutajulu', 'if312025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(372, 4460, 5198, 'if322004', '11322004', 'Destina Manurung', 'if322004@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(373, 273, 274, 'if05048', '11105048', 'DESY ARISANDI SIBAGARIANG', 'if05048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(374, 957, 955, 'if313040', '11113040', 'Desy Christy DeVega Munte', 'if313040@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(375, 2123, 2525, 'if416002', '11416002', 'Desy Isabel Nadya', 'if416002@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', 'Nazareth', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(376, 2727, 3225, 'ce318045', '13318045', 'Devi Azhari', 'ce318045@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(377, 1090, 1088, 'if314034', '11314034', 'Devi Stephanie Sihombing', 'if314034@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(378, 4031, 4741, 'if320035', '11320035', 'Dewa Sembiring', 'if320035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(379, 2720, 3218, 'ce318014', '13318014', 'Dewi Sartika Siburian', 'ce318014@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(380, 997, 995, 'if313070', '11113070', 'Dewika Silitonga', 'if313070@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(381, 775, 773, 'if412008', '21112008', 'Dewy Chaterina Sinaga', 'if412008@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(382, 2272, 2738, 'ce317029', '13317029', 'Dhandy Muham', 'ce317029@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(383, 3694, 4368, 'if319035', '11319035', 'Diah Yohanna Mentari Sirait', 'if319035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(384, 2088, 2488, 'if316027', '11316027', 'Dian Agnes Sirait', 'if316027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(385, 4514, 5252, 'if322058', '11322058', 'Dian Anggi Bellita Sitanggang', 'if322058@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(386, 1067, 1065, 'if314011', '11314011', 'Dian Christine Hutasoit', 'if314011@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(387, 4049, 4759, 'if320053', '11320053', 'Dian Esra Vitania Hasibuan', 'if320053@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(388, 2147, 2551, 'if416028', '11416028', 'Dian Gilbert Putra Marbun', 'if416028@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(389, 731, 729, 'if11087', '11111087', 'Dian Ira Putri Hutasoit', 'if11087@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(390, 1777, 1966, 'if415036', '11415036', 'Dian Ira Putri Hutasoit', 'if415036@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(391, 2224, 2690, 'if317005', '11317005', 'Dian P. S. Simanullang', 'if317005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(392, 3712, 4386, 'if319053', '11319053', 'Dian Permatasari Sitanggang', 'if319053@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(393, 1177, 1175, 'if414030', '11414030', 'Dian Pratiwi Simanjuntak', 'if414030@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(394, 2777, 3276, 'if418027', '11418027', 'Diana Grace Marbun', 'if418027@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(395, 2223, 2689, 'if317007', '11317007', 'Diana Novita Sitio', 'if317007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(396, 2657, 3154, 'if318012', '11318012', 'Diana Octaviana Naibaho', 'if318012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(397, 1125, 1123, 'ce314008', '13314008', 'Diana Ratnasari Manurung', 'ce314008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(398, 3737, 4411, 'ce319021', '13319021', 'Diantry A.S Pandiangan', 'ce319021@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(399, 2221, 2687, 'if317060', '11317060', 'Dicky Gabriel Kristoffer Gultom', 'if317060@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(400, 1041, 1039, 'if313096', '11113096', 'Dimas Daniel Jordan Simanjuntak', 'if313096@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(401, 2726, 3224, 'ce318042', '13318042', 'Dina Valianty Sitorus', 'ce318042@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Keluar', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(402, 1157, 1155, 'if414010', '11414010', 'Dina Veronica LumbanTobing', 'if414010@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(403, 2701, 3199, 'ce318030', '13318030', 'Dio Ephipanias', 'ce318030@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(404, 517, 517, 'if08049', '11108049', 'Dita Madonna Simanjuntak', 'if08049@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(405, 977, 975, 'if313062', '11113062', 'Doan Pratama Sinaga', 'if313062@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(406, 2083, 2483, 'if316023', '11316023', 'Dodi Agustin R Pakpahan', 'if316023@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(407, 4144, 4854, 'ce320040', '13320040', 'Dody Irman Josua Nadapdap', 'ce320040@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(408, 4381, 5092, 'if320060', '11320060', 'Dolly Van Sander Silalahi', 'if320060@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(409, 299, 300, 'if05082', '11105082', 'DOLY PUTRA  SARAGIH', 'if05082@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(410, 964, 962, 'if313052', '11113052', 'Dominika J. Siahaan', 'if313052@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(411, 562, 561, 'if09008', '11109008', 'Dominika Manihuruk', 'if09008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(412, 1136, 1134, 'ce314019', '13314019', 'Doni T. Sinaga', 'ce314019@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(413, 4034, 4744, 'if320038', '11320038', 'Donianto Siahaan', 'if320038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(414, 993, 991, 'if413047', '21113047', 'Dony Alfonso Sinaga', 'if413047@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(415, 4035, 4745, 'if320039', '11320039', 'DONY EDY BASRAH SIMANJUNTAK', 'if320039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(416, 462, 462, 'if08046', '11108046', 'Duma Marsauly Lastiurma', 'if08046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(417, 1017, 1015, 'if413046', '21113046', 'Dumaria Sartika Putri Sitinjak', 'if413046@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(418, 2257, 2723, 'ce317011', '13317011', 'Dumayangsari Manurung', 'ce317011@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(419, 4043, 4753, 'if320047', '11320047', 'Dwi Dora Panjaitan', 'if320047@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(420, 2244, 2710, 'if317057', '11317057', 'Dwi Putri Anatasya Sibarani', 'if317057@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(421, 875, 873, 'if412025', '21112025', 'Dwi Putri Ekonita Butarbutar', 'if412025@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(422, 3998, 4708, 'if320002', '11320002', 'dwiki moses somalin sirait', 'if320002@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(423, 3850, 4524, 'if419004', '11419004', 'Dwiky Febrian Nahottua Sitorus', 'if419004@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(424, 790, 788, 'if312005', '11112005', 'Dwina Krisdayanti Sidabutar', 'if312005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(425, 1598, 1739, 'if315022', '11315022', 'Dwinata Saragih', 'if315022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(426, 2723, 3221, 'ce318027', '13318027', 'Earth Chi', 'ce318027@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(427, 4371, 5081, 'if420085', '11420085', 'Easter jeconia marito sianipar', 'if420085@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(428, 292, 293, 'if05074', '11105074', 'EBEN H. E. MUAL', 'if05074@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(429, 27, 28, 'if02027', '11102027', 'Ecko Fernando Basarah Manalu', 'if02027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(430, 74, 75, 'if02078', '11102078', 'Eddy Hendra Sirait', 'if02078@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(431, 544, 543, 'if09063', '11109063', 'Eddyson Taniwan', 'if09063@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(432, 551, 550, 'if09065', '11109065', 'Edelina Parhusip', 'if09065@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(433, 396, 396, 'if07058', '11107058', 'Edelo Marusaha Tambunan', 'if07058@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(434, 356, 357, 'if06052', '11106052', 'Edison Marpaung', 'if06052@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(435, 344, 345, 'if06040', '11106040', 'Edison Parluhutan  Sihotang', 'if06040@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(436, 4005, 4715, 'if320009', '11320009', 'Edith Favian Daniel Silalahi', 'if320009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(437, 4112, 4822, 'ce320008', '13320008', 'Edrick Ernest Sinaga', 'ce320008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(438, 1168, 1166, 'if414021', '11414021', 'Eduardo Silalahi', 'if414021@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(439, 3729, 4403, 'ce319013', '13319013', 'Eduward S', 'ce319013@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(440, 1737, 1879, 'if415029', '11415029', 'Edward Hulman Simarmata', 'if415029@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(441, 3685, 4359, 'if319026', '11319026', 'Edwardo Maranatha Marpaung', 'if319026@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(442, 3855, 4529, 'if419009', '11419009', 'Edwin Gratia Hutagalung', 'if419009@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(443, 3857, 4531, 'if419011', '11419011', 'Edwin Immanuel Damanik', 'if419011@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(444, 2281, 2747, 'if417013', '11417013', 'Edwinda Friska Fabrianty', 'if417013@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(445, 1016, 1014, 'if413034', '21113034', 'Eflianto Butar-Butar', 'if413034@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(446, 109, 110, 'if01009', '11101009', 'Efran Richard Pardomuan Pasaribu', 'if01009@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(447, 793, 791, 'if312008', '11112008', 'Efrida Royani Gultom', 'if312008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(448, 2692, 3189, 'ce318039', '13318039', 'Ein Martini Sinaga', 'ce318039@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(449, 2085, 2485, 'if316025', '11316025', 'Eirene  Claudia Hutasoit', 'if316025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(450, 2724, 3222, 'ce318041', '13318041', 'Eirene Yohana V Tambunan', 'ce318041@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(451, 304, 305, 'if05088', '11105088', 'EKA STEPHANI SINAMBELA', 'if05088@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(452, 4489, 5227, 'if322033', '11322033', 'EKA SYAHPUTRA LUMBANRAJA', 'if322033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(453, 2274, 2740, 'ce317004', '13317004', 'Ekaristi Simorangkir', 'ce317004@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(454, 1075, 1073, 'if314019', '11314019', 'Eki Yusina Maduna Simanjuntak', 'if314019@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(455, 443, 443, 'if07023', '11107023', 'Eko Andreas  Silitonga', 'if07023@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(456, 488, 488, 'if08009', '11108009', 'Eko Augustra Ryanda', 'if08009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(457, 826, 824, 'if312050', '11112050', 'Eko Januardy Manurung', 'if312050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(458, 1718, 1860, 'if415009', '11415009', 'Eko Priono Fikranta Simanjuntak', 'if415009@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(459, 108, 109, 'if01010', '11101010', 'Eko Yudis Parlin Rajagukguk', 'if01010@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(460, 2290, 2756, 'if417001', '11417001', 'Ekonaldi Hutabarat', 'if417001@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(461, 4317, 5027, 'if420031', '11420031', 'ELADITA NADEAK', 'if420031@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(462, 1778, 1967, 'if415037', '11415037', 'Elfira Utami Gultom', 'if415037@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(463, 432, 432, 'if07009', '11107009', 'Elfrida B. A. Siahaan', 'if07009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(464, 3669, 4343, 'if319010', '11319010', 'Elfrida R.D. Tampubolon', 'if319010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(465, 1752, 1894, 'ce315008', '13315008', 'Elfrida Siburian', 'ce315008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(466, 248, 249, 'if05022', '11105022', 'ELGA F.  SILABAN', 'if05022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(467, 1074, 1072, 'if314018', '11314018', 'Elieser Forwin', 'if314018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(468, 729, 727, 'if11085', '11111085', 'Elina Sumanti Sihombing', 'if11085@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(469, 2650, 3147, 'if318002', '11318002', 'Elisa Agustina Simorangkir', 'if318002@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11');
INSERT INTO `mahasiswa` (`id`, `dim_id`, `user_id`, `user_name`, `nim`, `nama`, `email`, `prodi_id`, `prodi_name`, `fakultas`, `angkatan`, `nomor_telepon`, `status`, `asrama`, `created_at`, `updated_at`) VALUES
(470, 797, 795, 'if312013', '11112013', 'Elisa Paulina Pangaribuan', 'if312013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(471, 4449, 5184, 'if321068', '11321068', 'ELISA REGINA SIMANJUNTAK', 'if321068@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(472, 2935, 3435, 'ce319030', '13319030', 'Elisabeth Sri Lestari Siahaan', 'ce319030@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(473, 4506, 5244, 'if322050', '11322050', 'Elisabeth Uli Tambunan', 'if322050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(474, 2655, 3152, 'if318009', '11318009', 'Elizabeth Sihaloho', 'if318009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(475, 431, 431, 'if07005', '11107005', 'Elizabeth V. Pardede', 'if07005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(476, 303, 304, 'if05086', '11105086', 'ELLSA MARIHOT BERLIANA  MARINTAN  SIBUEA', 'if05086@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(477, 684, 682, 'if11036', '11111036', 'Elni Enita Manurung', 'if11036@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(478, 556, 555, 'if09001', '11109001', 'Elsa Gultom', 'if09001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(479, 4399, 5134, 'if321018', '11321018', 'Elsa Klariza Silalahi', 'if321018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(480, 1153, 1151, 'if414006', '11414006', 'Elsa Monika Hartini', 'if414006@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(481, 2776, 3275, 'if418025', '11418025', 'Elsa Pitalita Sihombing', 'if418025@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(482, 4400, 5135, 'if321019', '11321019', 'ELSADAY SIANTURI', 'if321019@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(483, 4342, 5052, 'if420056', '11420056', 'Elsha T P Sitorus', 'if420056@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(484, 2164, 2600, 'if316041', '11316041', 'Elvira Wenda', 'if316041@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(485, 908, 906, 'if313033', '11113033', 'Elwin Jusuf Togatorop', 'if313033@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(486, 844, 842, 'if312069', '11112069', 'Emi Fentiny Bakara', 'if312069@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(487, 979, 977, 'if313083', '11113083', 'Eminarti Yuliasi Sianturi', 'if313083@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(488, 4404, 5139, 'if321023', '11321023', 'Emy Sonia Sinambela', 'if321023@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(489, 937, 935, 'if313015', '11113015', 'Enjelyna Pardede', 'if313015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(490, 576, 575, 'if09022', '11109022', 'Ennitan Octavia', 'if09022@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(491, 1147, 1145, 'ce314030', '13314030', 'Enny Sriwi Juita Siahaan', 'ce314030@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(492, 441, 441, 'if07021', '11107021', 'Enti Dahlia Gultom', 'if07021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(493, 808, 806, 'if312030', '11112030', 'Epelin Manurung', 'if312030@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(494, 918, 916, 'if413016', '21113016', 'Ephraim Kesaba Silalahi', 'if413016@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(495, 1085, 1083, 'if314029', '11314029', 'Erickson Lumban Gaol', 'if314029@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(496, 4030, 4740, 'if320034', '11320034', 'Erik Parsaoran Manalu', 'if320034@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(497, 2268, 2734, 'ce317018', '13317018', 'Erik Simanjuntak', 'ce317018@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(498, 2092, 2492, 'ce316001', '13316001', 'Erika Florensia Sihombing', 'ce316001@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(499, 938, 936, 'if313012', '11113012', 'Erisha Gustanti Sitorus', 'if313012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(500, 968, 966, 'if413036', '21113036', 'Erjan Sarwono Sirait', 'if413036@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(501, 4458, 5196, 'if322002', '11322002', 'Erlangga Abel Napitupulu', 'if322002@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(502, 1548, 1677, 'if315010', '11315010', 'Ernestia Dwiarta Manurung', 'if315010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(503, 32, 33, 'if02032', '11102032', 'Ernist Simangunsong', 'if02032@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(504, 3912, 4586, 'if419066', '11419066', 'Ervina Sipahutar', 'if419066@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(505, 1761, 1903, 'ce315018', '13315018', 'Erwin Manorang Simamora', 'ce315018@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(506, 16, 17, 'if02016', '11102016', 'Erwin Parda Umri Manurung', 'if02016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(507, 4455, 5190, 'if321074', '11321074', 'Ester Anastasia Marsada Uli Simamora', 'if321074@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(508, 1719, 1861, 'if415010', '11415010', 'Ester Enjela Marbun', 'if415010@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(509, 4435, 5170, 'if321054', '11321054', 'Ester Krismayani Sinaga', 'if321054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(510, 940, 938, 'if313032', '11113032', 'Ester Marta Tambunan', 'if313032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(511, 971, 969, 'if413037', '21113037', 'Ester Melati Manalu', 'if413037@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(512, 3698, 4372, 'if319039', '11319039', 'Ester Saulina Hutabarat', 'if319039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(513, 1060, 1058, 'if314004', '11314004', 'ESTER SIMAMORA', 'if314004@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(514, 984, 982, 'if313077', '11113077', 'Ester Situmorang', 'if313077@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(515, 2260, 2726, 'ce317027', '13317027', 'Ester Veronica Putri Sinambela', 'ce317027@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(516, 4344, 5054, 'if420058', '11420058', 'Ester Yolanda Berutu', 'if420058@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(517, 2680, 3177, 'ce318006', '13318006', 'Eva Lelita Panjaitan', 'ce318006@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(518, 401, 401, 'if07068', '11107068', 'Eva Mavliani Siahaan', 'if07068@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(519, 840, 838, 'if312065', '11112065', 'Evalina Simangunsong', 'if312065@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(520, 3848, 4522, 'if419002', '11419002', 'Evan Hutagaol', 'if419002@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(521, 3678, 4352, 'if319019', '11319019', 'Evan Richardo Sianipar', 'if319019@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(522, 724, 722, 'if11065', '11111065', 'Evan Y. A. Sihombing Nababan', 'if11065@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(523, 1764, 1906, 'ce315021', '13315021', 'Evander Stone', 'ce315021@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(524, 3902, 4576, 'if419056', '11419056', 'Evelin Sinurat', 'if419056@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(525, 4113, 4823, 'ce320009', '13320009', 'Evi RosaLinda Silaen', 'ce320009@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(526, 2251, 2717, 'ce317008', '13317008', 'Evi Yolenta Silalahi', 'ce317008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(527, 3743, 4417, 'ce319027', '13319027', 'Evita Silaen', 'ce319027@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(528, 2127, 2530, 'if416007', '11416007', 'Evita Veronika Sihombing', 'if416007@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(529, 629, 627, 'if10010', '11110010', 'Evy Hernyta Panggabean', 'if10010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(530, 2746, 3245, 'if418038', '11418038', 'Exalanty Hutabarat', 'if418038@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(531, 2755, 3254, 'if418016', '11418016', 'Exsayudy Manalu', 'if418016@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(532, 399, 399, 'if07062', '11107062', 'EZRA HADI RIBO SINAGA', 'if07062@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(533, 4132, 4842, 'ce320028', '13320028', 'Ezri Jeremi', 'ce320028@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(534, 57, 58, 'if02057', '11102057', 'Ezron Yotham Sinaga', 'if02057@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(535, 229, 230, 'if04038', '11104038', 'Faber Oktavianus Siagian', 'if04038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(536, 568, 567, 'if09014', '11109014', 'Faisal Ibrahim Silitonga', 'if09014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(537, 4138, 4848, 'ce320034', '13320034', 'Fajar Maliki Sianipar', 'ce320034@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(538, 2194, 2660, 'if317024', '11317024', 'Fandi Fladimir Dachi', 'if317024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(539, 1025, 1023, 'if413059', '21113059', 'Fanny Tambunan', 'if413059@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(540, 165, 166, 'if03027', '11103027', 'Farel Panjaitan', 'if03027@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(541, 2734, 3233, 'if418034', '11418034', 'Faustine B A Ompusunggu', 'if418034@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(542, 1605, 1746, 'if315029', '11315029', 'Febby Natasia Gurning', 'if315029@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(543, 2663, 3160, 'if318030', '11318030', 'Febiola Simangunsong', 'if318030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(544, 4125, 4835, 'ce320021', '13320021', 'Febri Sinaga', 'ce320021@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(545, 4108, 4818, 'ce320004', '13320004', 'Febri Yanti Lintar Purba', 'ce320004@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(546, 2255, 2721, 'ce317023', '13317023', 'Febriend B.R.C Sigalingging', 'ce317023@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(547, 255, 256, 'if05032', '11105032', 'FEBRIN A.  R.  SITOHANG', 'if05032@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(548, 636, 634, 'if10003', '11110003', 'Febry P. J. Sibuea', 'if10003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(549, 3907, 4581, 'if419061', '11419061', 'Febryanti Melati', 'if419061@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(550, 3690, 4364, 'if319031', '11319031', 'Feby T K Sitinjak', 'if319031@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(551, 4105, 4815, 'ce320001', '13320001', 'Fedrick Samuel Pasaribu', 'ce320001@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(552, 2217, 2683, 'if317049', '11317049', 'Fedrick Sulaiman', 'if317049@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(553, 4469, 5207, 'if322013', '11322013', 'Felix Aldi I Simanjuntak', 'if322013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(554, 4135, 4845, 'ce320031', '13320031', 'Felix Simanjuntak', 'ce320031@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(555, 1772, 1914, 'ce315029', '13315029', 'Fensius Musa S. B. Aritonang', 'ce315029@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(556, 2236, 2702, 'if317040', '11317040', 'Feny Bertarida Melpa Sari Simanjuntak', 'if317040@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(557, 2271, 2737, 'ce317028', '13317028', 'Ferdiando', 'ce317028@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(558, 277, 278, 'if05052', '11105052', 'FERDINAN PAKPAHAN', 'if05052@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(559, 35, 36, 'if02035', '11102035', 'Ferdinand Rosco Silitonga', 'if02035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(560, 540, 539, 'if09060', '11109060', 'Feri Indryani Sirait', 'if09060@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(561, 779, 777, 'if412012', '21112012', 'Fernando Simbolon', 'if412012@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(562, 4053, 4763, 'if320057', '11320057', 'Feronika Simanjuntak', 'if320057@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(563, 274, 275, 'if05049', '11105049', 'FERRY DINGIN SINAGA', 'if05049@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(564, 1886, 2285, 'ce317031', '13317031', 'Ferry Vernando Hezron Siagian', 'ce317031@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(565, 460, 460, 'if08044', '11108044', 'Fery Rinaldo P', 'if08044@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(566, 2105, 2507, 'ce316015', '13316015', 'Fibonaccy Elisabeth Gultom', 'ce316015@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(567, 634, 632, 'if10005', '11110005', 'Fifianty Hutapea', 'if10005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(568, 483, 483, 'if08004', '11108004', 'Fina Oktavia Manurung', 'if08004@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(569, 4358, 5068, 'if420072', '11420072', 'Firman Moedardo Panjaitan', 'if420072@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(570, 259, 260, 'if05028', '11105028', 'FIRMATOGU NAINGGOLAN', 'if05028@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(571, 787, 785, 'if412021', '21112021', 'Fitri Juliana Manurung', 'if412021@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(572, 2617, 3114, 'if318007', '11318007', 'Fitri Purnama Hutabarat', 'if318007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(573, 855, 853, 'if312080', '11112080', 'Fittry Estomi Simarmata', 'if312080@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(574, 264, 265, 'if05039', '11105039', 'FRANCISCUS M.  SIANTURI', 'if05039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(575, 2071, 2471, 'if316010', '11316010', 'Franciskus Partogu Hamonangan Napitupulu', 'if316010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(576, 682, 680, 'if11034', '11111034', 'Franky Parulian Silalahi', 'if11034@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(577, 187, 188, 'if03012', '11103012', 'Frans Andrew Sinaga', 'if03012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(578, 3886, 4560, 'if419040', '11419040', 'Frans Naipospos', 'if419040@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(579, 3722, 4396, 'ce319006', '13319006', 'Frans Peter Josua Naibaho', 'ce319006@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(580, 561, 560, 'if09007', '11109007', 'Frans Tuani Ryerson Siburian', 'if09007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(581, 2760, 3259, 'if418035', '11418035', 'Frans Z Siregar', 'if418035@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(582, 205, 206, 'if04003', '11104003', 'FRANSISCO', 'if04003@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(583, 4386, 5121, 'if321005', '11321005', 'Fransiska Maria L. Simanungkalit', 'if321005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(584, 2136, 2539, 'if416014', '11416014', 'Franz Aditya Natanael Sinaga', 'if416014@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(585, 4431, 5166, 'if321050', '11321050', 'Frayogi Sitorus', 'if321050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(586, 595, 594, 'if09041', '11109041', 'Fred Dryer Ranov Hunter Nainggolan', 'if09041@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(587, 1, 2, 'if02003', '11102003', 'Frederik Bungaran Ishak Situmeang', 'if02003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(588, 2086, 2486, 'if316030', '11316030', 'Fredrick Mangampu Theodorus Pardosi', 'if316030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(589, 209, 210, 'if04015', '11104015', 'FRENGKI ANDI NOVA SINAGA', 'if04015@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(590, 906, 904, 'if313008', '11113008', 'Frengki Simatupang', 'if313008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(591, 3903, 4577, 'if419057', '11419057', 'Fretty L M Silalahi', 'if419057@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(592, 809, 807, 'if312031', '11112031', 'Frisca Lestari Novalinda Sagala', 'if312031@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(593, 258, 259, 'if05029', '11105029', 'FRISCA RUNY  PANJAITAN', 'if05029@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(594, 2665, 3162, 'if318037', '11318037', 'Frisda Sianipar', 'if318037@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(595, 2202, 2668, 'if317027', '11317027', 'Friska L. Sianturi', 'if317027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(596, 1141, 1139, 'ce314024', '13314024', 'Friska Manalu', 'ce314024@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(597, 4416, 5151, 'if321035', '11321035', 'Fritz Tri Yofanka Anggito Marpaung', 'if321035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(598, 2297, 2763, 'if417020', '11417020', 'Gabriel Benni Pernadi Panjaitan', 'if417020@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(599, 4384, 5119, 'if321003', '11321003', 'Gabriel Sigalingging', 'if321003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(600, 2788, 3287, 'if418052', '11418052', 'Gabriela Melva Naibaho', 'if418052@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(601, 3894, 4568, 'if419048', '11419048', 'Gahasa Timothius B.P. Purba', 'if419048@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(602, 4137, 4847, 'ce320033', '13320033', 'Ganda Patar Nadeak', 'ce320033@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(603, 91, 92, 'if02097', '11102097', 'Ganda Yohannes Situmorang', 'if02097@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(604, 4422, 5157, 'if321041', '11321041', 'Gavin Nathanael Nababan', 'if321041@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(605, 3914, 4588, 'if419068', '11419068', 'Geby Widyawati Putri Lumban Gaol', 'if419068@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(606, 407, 407, 'if07081', '11107081', 'Genesis Saur Gabe Banjarnahor', 'if07081@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(607, 4032, 4742, 'if320036', '11320036', 'Gennesis Hairul Anwar Sinaga', 'if320036@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(608, 741, 739, 'if11027', '11111027', 'Genti Panjaitan', 'if11027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(609, 2143, 2547, 'if416024', '11416024', 'Gerald Bendry Andre Sihotang', 'if416024@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(610, 4457, 5195, 'if322001', '11322001', 'Gerald Renhart Aditia Sitio', 'if322001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(611, 469, 469, 'if08056', '11108056', 'Gerry Italiano Wowiling', 'if08056@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, '', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(612, 507, 507, 'if08030', '11108030', 'Gesner Holoan Tampubolon', 'if08030@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(613, 1715, 1857, 'if415006', '11415006', 'Gideon Panjaitan', 'if415006@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(614, 2643, 3140, 'if318049', '11318049', 'Ginanjar Siagian', 'if318049@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(615, 1158, 1156, 'if414011', '11414011', 'Gita Christy Purba', 'if414011@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(616, 2652, 3149, 'if318004', '11318004', 'Gita Juwito Siahaan', 'if318004@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(617, 2313, 2779, 'if417030', '11417030', 'Gita Pratti Nadapdap', 'if417030@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(618, 3670, 4344, 'if319011', '11319011', 'Gladys Cindana Asri Pardosi', 'if319011@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(619, 168, 169, 'if03010', '11103010', 'Gloria Nathalina Limbong', 'if03010@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(620, 1105, 1103, 'if314049', '11314049', 'Gloria Tri Suci Limbong', 'if314049@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(621, 4106, 4816, 'ce320002', '13320002', 'Glorian Johan Einstein Purba', 'ce320002@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(622, 1779, 1968, 'if415038', '11415038', 'Goklas Henry Agus Panjaitan', 'if415038@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Keluar', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(623, 2301, 2767, 'if417034', '11417034', 'Golfrid Heraldi Simatupang', 'if417034@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(624, 1049, 1047, 'if413065', '21113065', 'Gomgom', 'if413065@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(625, 4124, 4834, 'ce320020', '13320020', 'Grace Agnes Kesya', 'ce320020@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(626, 2201, 2667, 'if317017', '11317017', 'Grace Anastasya Megawati Sihombing', 'if317017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(627, 2782, 3281, 'if418042', '11418042', 'Grace Ayuni Sinta Sitorus', 'if418042@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(628, 4115, 4825, 'ce320011', '13320011', 'Grace Chaca Nani Natalia Siburian', 'ce320011@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(629, 2771, 3270, 'if418010', '11418010', 'Grace D. Sitanggang', 'if418010@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(630, 362, 363, 'if06058', '11106058', 'Grace Dona Harlita Tarihoran', 'if06058@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(631, 1066, 1064, 'if314010', '11314010', 'Grace Elvioretha Pasaribu', 'if314010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(632, 730, 728, 'if11086', '11111086', 'Grace Friskilla Purba', 'if11086@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(633, 333, 334, 'if06029', '11106029', 'Grace Hanna Loide Lumban Tobing', 'if06029@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(634, 300, 301, 'if05083', '11105083', 'GRACE ISABELLA ROMAITO NAIPOSPOS', 'if05083@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(635, 1608, 1749, 'if315032', '11315032', 'Grace Naomi Damanik', 'if315032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(636, 2103, 2505, 'ce316013', '13316013', 'Grace Sukmawaty br. Naibaho', 'ce316013@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(637, 748, 746, 'if11071', '11111071', 'Grace Yanni Sibarani', 'if11071@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(638, 1711, 1853, 'if415002', '11415002', 'Greace Maulina Situmorang', 'if415002@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(639, 1032, 1030, 'if413054', '21113054', 'Gustav Manuel Dasma Sihombing', 'if413054@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(640, 944, 942, 'if313025', '11113025', 'Gustina Malinda', 'if313025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(641, 2196, 2662, 'if317039', '11317039', 'Hagai Belfri Kristyadi Sitanggang', 'if317039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(642, 4519, 5257, 'if322063', '11322063', 'Hagai Natasha Sianturi', 'if322063@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(643, 803, 801, 'if312024', '11112024', 'Halasson Martamba Simatupang', 'if312024@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(644, 101, 102, 'if01017', '11101017', 'Halomoan Pardamean Hutagalung', 'if01017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(645, 3723, 4397, 'ce319007', '13319007', 'Hamora Hadi', 'ce319007@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(646, 1050, 1048, 'if413062', '21113062', 'Handayani T.N.S', 'if413062@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(647, 571, 570, 'if09017', '11109017', 'Hanna Theresia', 'if09017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(648, 882, 880, 'if312109', '11112109', 'Hanna Tria Stephani Silitonga', 'if312109@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(649, 1742, 1884, 'if415033', '11415033', 'Hans Amanda Purba', 'if415033@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(650, 475, 475, 'if08064', '11108064', 'Hans Elon Saragih', 'if08064@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(651, 2176, 2612, 'if316052', '11316052', 'Hans Marthen Yikwa', 'if316052@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(652, 4414, 5149, 'if321033', '11321033', 'HANS PRANATA KARO SEKALI', 'if321033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(653, 1600, 1741, 'if315024', '11315024', 'Hans Theo Christianson Simorangkir', 'if315024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(654, 2646, 3143, 'if318055', '11318055', 'Hansen Henok Oktavianus Situmorang', 'if318055@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(655, 2270, 2736, 'ce317024', '13317024', 'Hardiman Utama Hotlas Tambun', 'ce317024@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(656, 498, 498, 'if08021', '11108021', 'Harisen', 'if08021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(657, 4158, 4868, 'ce320054', '13320054', 'Harli Juita Sinabutar', 'ce320054@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(658, 760, 758, 'if11096', '11111096', 'Harris C. S. Silitonga', 'if11096@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(659, 894, 892, 'if413007', '21113007', 'Harris V. Sibuea', 'if413007@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(660, 1080, 1078, 'if314024', '11314024', 'Harry Leonardo Lumbanraja', 'if314024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(661, 164, 165, 'if03031', '11103031', 'Harry Panusunan Pasaribu', 'if03031@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(662, 290, 291, 'if05070', '11105070', 'HARTANTI EVA  LESTARI  TAMBA', 'if05070@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(663, 1599, 1740, 'if315023', '11315023', 'Hartanti Saragih', 'if315023@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(664, 4004, 4714, 'if320008', '11320008', 'Hartditya Lumbantoruan', 'if320008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(665, 1734, 1876, 'if415025', '11415025', 'Hary Naek Marpaung', 'if415025@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(666, 4493, 5231, 'if322037', '11322037', 'Hasan Sinaga', 'if322037@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(667, 2609, 3106, 'if318015', '11318015', 'Hasoloan Davinson Hamonangan Hutapea', 'if318015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(668, 671, 669, 'if10041', '11110041', 'Hedy Simamora', 'if10041@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(669, 4509, 5247, 'if322053', '11322053', 'Helen Yohana Sihombing', 'if322053@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(670, 792, 790, 'if312007', '11112007', 'Helen Yulitricia Christien Lingga', 'if312007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(671, 4014, 4724, 'if320018', '11320018', 'HELENA RONAULI TAMPUBOLON', 'if320018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(672, 2295, 2761, 'if417017', '11417017', 'Helmuth Simon Tampubolon', 'if417017@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(673, 185, 186, 'if03016', '11103016', 'Hendra Manto Sitorus', 'if03016@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(674, 29, 30, 'if02029', '11102029', 'Hendra Rikardo Simbolon', 'if02029@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(675, 1135, 1133, 'ce314018', '13314018', 'Hendrick Marulitua Sinambela', 'ce314018@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(676, 285, 286, 'if05064', '11105064', 'HENDRO PALMER  SIAHAAN', 'if05064@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(677, 2763, 3262, 'if418041', '11418041', 'Hengki A.P Hutahaean', 'if418041@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(678, 100, 101, 'if01018', '11101018', 'Hengky T. Sihotang', 'if01018@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(679, 4040, 4750, 'if320044', '11320044', 'Heni Ernita Lumbangaol', 'if320044@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(680, 3699, 4373, 'if319040', '11319040', 'Henny Flora Panjaitan', 'if319040@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(681, 3865, 4539, 'if419019', '11419019', 'HEPNIWER NUR AISAH PURBA', 'if419019@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(682, 314, 315, 'if06008', '11106008', 'Hepriyanti Fransiska Siahaan', 'if06008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(683, 2137, 2540, 'if416015', '11416015', 'Herbert Habrindo Silalahi', 'if416015@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, '', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(684, 4428, 5163, 'if321047', '11321047', 'HERBETH AUGUSTINUS NAPITUPULU', 'if321047@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(685, 370, 371, 'if06067', '11106067', 'Heri Ganti', 'if06067@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(686, 1058, 1056, 'if314001', '11314001', 'Herlina Mariana Pardede', 'if314001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(687, 4454, 5189, 'if321073', '11321073', 'HERLINA NIKITA  BR PURBA', 'if321073@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(688, 424, 424, 'if07103', '11107103', 'Herlina Valentina Pasaribu', 'if07103@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(689, 1018, 1016, 'if313041', '11113041', 'Herman Fernandes Situmorang', 'if313041@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(690, 2662, 3159, 'if318029', '11318029', 'Hernael Grecika Sihombing', 'if318029@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(691, 2197, 2663, 'if317056', '11317056', 'Hernan Crespo Panjaitan', 'if317056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(692, 550, 549, 'if09074', '11109074', 'Hernando Valdy Saragih', 'if09074@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(693, 425, 425, 'if07104', '11107104', 'Hernawati Susanti Samosir', 'if07104@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(694, 357, 358, 'if06053', '11106053', 'Hertati Sarma Adelima  Simanjuntak', 'if06053@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(695, 983, 981, 'if313080', '11113080', 'Hesty Thamara Marbun', 'if313080@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(696, 818, 816, 'if312040', '11112040', 'Hierony Manurung', 'if312040@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(697, 3871, 4545, 'if419025', '11419025', 'Hilman Sijabat', 'if419025@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(698, 4394, 5129, 'if321013', '11321013', 'HISKIA ANDAR BANGGA PARHUSIP', 'if321013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(699, 2141, 2544, 'if416020', '11416020', 'Hokkop A M Purba', 'if416020@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(700, 697, 695, 'if11049', '11111049', 'Holong M. Situmorang', 'if11049@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(701, 4432, 5167, 'if321051', '11321051', 'Horas Marolop Amsal Siregar', 'if321051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(702, 4472, 5210, 'if322016', '11322016', 'Horas MP Saragih Sidabalok', 'if322016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(703, 242, 243, 'if05015', '11105015', 'Horas Octavianus Sihotang', 'if05015@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(704, 2676, 3173, 'ce318013', '13318013', 'Horas Yusuf Riski Simanullang', 'ce318013@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11');
INSERT INTO `mahasiswa` (`id`, `dim_id`, `user_id`, `user_name`, `nim`, `nama`, `email`, `prodi_id`, `prodi_name`, `fakultas`, `angkatan`, `nomor_telepon`, `status`, `asrama`, `created_at`, `updated_at`) VALUES
(705, 2764, 3263, 'if418046', '11418046', 'Hosea Felix Hutahuruk', 'if418046@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(706, 1052, 1050, 'if313093', '11113093', 'Hosea Paskahadi Riquel', 'if313093@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(707, 4146, 4856, 'ce320042', '13320042', 'HOTBEN DIMPOS BUTAR BUTAR', 'ce320042@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(708, 3701, 4375, 'if319042', '11319042', 'Hotma Aruan', 'if319042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(709, 2307, 2773, 'if417015', '11417015', 'Hotni Maria Simatupang', 'if417015@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(710, 3869, 4543, 'if419023', '11419023', 'Hotnida Siagian', 'if419023@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(711, 662, 660, 'if10050', '11110050', 'Humuntar Stephanus Siallagan', 'if10050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(712, 589, 588, 'if09035', '11109035', 'Ian Petrus Sinaga', 'if09035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(713, 773, 771, 'if412006', '21112006', 'Ian Raj L. Sembiring', 'if412006@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(714, 2768, 3267, 'if418001', '11418001', 'Icca Riris Siallagan', 'if418001@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(715, 1022, 1020, 'if313086', '11113086', 'Ida Christy Hutagaol', 'if313086@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(716, 1000, 998, 'if313065', '11113065', 'Idola Sari Manurung', 'if313065@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(717, 2774, 3273, 'if418018', '11418018', 'Ika Marsaulina Silaban', 'if418018@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(718, 1746, 1888, 'ce315003', '13315003', 'Ika Monica Telaumbanua', 'ce315003@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(719, 990, 988, 'if313064', '11113064', 'Iman Syahputra Situmorang', 'if313064@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(720, 884, 882, 'if412026', '21112026', 'Imanuel Pardosi', 'if412026@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(721, 98, 99, 'if01020', '11101020', 'Imelda Doharta Aritonang', 'if01020@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(722, 4477, 5215, 'if322021', '11322021', 'imelda olivia morenza tambun', 'if322021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(723, 177, 178, 'if03082', '11103082', 'Imelda Roswara', 'if03082@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(724, 1039, 1037, 'if313095', '11113095', 'Imelda Yohana Uli Rastra Lingga', 'if313095@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(725, 281, 282, 'if05057', '11105057', 'IMMANUEL PANJAITAN', 'if05057@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(726, 4480, 5218, 'if322024', '11322024', 'IMMANUEL PARTOGI PARDEDE', 'if322024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(727, 1606, 1747, 'if315031', '11315031', 'Immanuel Saragih', 'if315031@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(728, 4024, 4734, 'if320028', '11320028', 'Immanuel Siahaan', 'if320028@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(729, 2695, 3193, 'ce318015', '13318015', 'Immanuel Soaloon Sianturi', 'ce318015@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(730, 4366, 5076, 'if420080', '11420080', 'Indah Chris Sarah Sinurat', 'if420080@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(731, 4502, 5240, 'if322046', '11322046', 'INDAH PERMATA SITORUS', 'if322046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(732, 2305, 2771, 'if417008', '11417008', 'Indah Trivena Tampubolon', 'if417008@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(733, 2183, 2618, 'ce316017', '13316017', 'Indra Daniel Butar-Butar', 'ce316017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(734, 2098, 2500, 'ce316008', '13316008', 'Indra G Pardosi', 'ce316008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(735, 8, 9, 'if02008', '11102008', 'Indra Siregar', 'if02008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(736, 1543, 1672, 'if315005', '11315005', 'Indra Vincentius Manik', 'if315005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(737, 946, 944, 'if313017', '11113017', 'Indri Goldwinda Situmorang', 'if313017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(738, 834, 832, 'if312059', '11112059', 'Indri Novita Hutabalian', 'if312059@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(739, 2200, 2666, 'if317006', '11317006', 'Inez Cecilia Tiurma Yuliana', 'if317006@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(740, 1045, 1043, 'if413069', '21113069', 'Inggrid Sylvia Simanjuntak', 'if413069@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(741, 989, 987, 'if313079', '11113079', 'Inten Sherley Panjaitan', 'if313079@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(742, 4465, 5203, 'if322009', '11322009', 'IQBAL PANCA RAHMAT SIAGIAN', 'if322009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(743, 2130, 2533, 'if416009', '11416009', 'Ira Elysa Gurning', 'if416009@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(744, 1341, 1339, 'if316057', '11316057', 'Ira Mannawaty br. Simanullang', 'if316057@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(745, 2239, 2705, 'if317043', '11317043', 'Irbana Ambarita', 'if317043@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(746, 4318, 5028, 'if420032', '11420032', 'IRENE CARMENITA AGATHA SIMATUPANG', 'if420032@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(747, 1721, 1863, 'if415012', '11415012', 'Irene Debora Panjaitan', 'if415012@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(748, 1089, 1087, 'if314033', '11314033', 'Irma Charisah R. Sihotang', 'if314033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, '', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(749, 3711, 4385, 'if319052', '11319052', 'Irma Gracia Siagian', 'if319052@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(750, 280, 281, 'if05056', '11105056', 'Irma Sari Br Barus', 'if05056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(751, 4341, 5051, 'if420055', '11420055', 'IRMA TRIANA LBN TOBING', 'if420055@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(752, 608, 606, 'if10031', '11110031', 'Irmandes Roy Mangapul Tambunan', 'if10031@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(753, 1780, 1969, 'if415039', '11415039', 'Irmandes Roy Mangapul Tambunan', 'if415039@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(754, 866, 864, 'if312093', '11112093', 'Iroma Pesta Esra Situmorang', 'if312093@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(755, 580, 579, 'if09026', '11109026', 'Irvan A. J. Butarbutar', 'if09026@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(756, 1115, 1113, 'if314059', '11314059', 'Irvan Mangolo Panggabean', 'if314059@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(757, 3854, 4528, 'if419008', '11419008', 'Irvandi Pransena Hutapea', 'if419008@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(758, 3726, 4400, 'ce319010', '13319010', 'Irwan Rivandy Siagian', 'ce319010@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(759, 574, 573, 'if09020', '11109020', 'Isma Romanti Napitupulu', 'if09020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(760, 4337, 5047, 'if420051', '11420051', 'Ita Anjelly P Sirait', 'if420051@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(761, 2114, 2516, 'ce316024', '13316024', 'Ivan Fransiskus Simatupang', 'ce316024@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(762, 295, 296, 'if05077', '11105077', 'IVANNA RICA', 'if05077@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(763, 3876, 4550, 'if419030', '11419030', 'Ivanowsky Fernandes Habeahan', 'if419030@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(764, 768, 766, 'if312001', '11112001', 'Ivo Andriani Manurung', 'if312001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(765, 2300, 2766, 'if417033', '11417033', 'Jacky Stevanus Hutajulu', 'if417033@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(766, 3732, 4406, 'ce319016', '13319016', 'Jacob Edward Ginting', 'ce319016@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(767, 334, 335, 'if06030', '11106030', 'Jaka Putra Lesmana', 'if06030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(768, 2106, 2508, 'ce316016', '13316016', 'Jaksiwa Idaman Putra Siregar', 'ce316016@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(769, 772, 770, 'if412030', '21112030', 'Jakson Simamora', 'if412030@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(770, 2097, 2498, 'ce316006', '13316006', 'Jan David L. N. Silalahi', 'ce316006@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(771, 2620, 3117, 'if318028', '11318028', 'Jane Mitaria Sinambela', 'if318028@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(772, 652, 650, 'if10060', '11110060', 'Janesa Mark Viktor Perkasa Tarigan', 'if10060@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(773, 1774, 1916, 'ce315031', '13315031', 'Jansutris Apriten Purba', 'ce315031@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(774, 2207, 2673, 'if317001', '11317001', 'Januar Simson Tampubolon', 'if317001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(775, 1070, 1068, 'if314014', '11314014', 'Januari Panjaitan', 'if314014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(776, 149, 150, 'if03023', '11103023', 'Jaya Afrianto Saragih', 'if03023@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(777, 1754, 1896, 'ce315010', '13315010', 'Jed Abner Sihombing', 'ce315010@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(778, 236, 237, 'if05005', '11105005', 'Jeflin Wandi Sagala', 'if05005@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(779, 161, 162, 'if03051', '11103051', 'Jefri B. F. Sihotang', 'if03051@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(780, 2090, 2490, 'if316029', '11316029', 'Jefri Lamhot Malau', 'if316029@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(781, 323, 324, 'if06017', '11106017', 'Jefry Supriady Pasaribu', 'if06017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(782, 1055, 1053, 'if413076', '21113076', 'Jen Presly Samosir', 'if413076@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(783, 1137, 1135, 'ce314020', '13314020', 'Jenius Kalpin', 'ce314020@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(784, 1602, 1743, 'if315026', '11315026', 'Jenny Oktavia Doloksaribu', 'if315026@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(785, 885, 883, 'if312112', '11112112', 'Jenny Pasaribu', 'if312112@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(786, 3667, 4341, 'if319008', '11319008', 'Jennyfer Christine Sitorus', 'if319008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(787, 812, 810, 'if312034', '11112034', 'Jepta Valentino Sinaga', 'if312034@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(788, 3721, 4395, 'ce319005', '13319005', 'Jeremia Agung Panjaitan', 'ce319005@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(789, 2269, 2735, 'ce317021', '13317021', 'Jeremia Sibarani', 'ce317021@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(790, 4330, 5040, 'if420044', '11420044', 'Jericho Binsar Michael Silaen', 'if420044@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(791, 4362, 5072, 'if420076', '11420076', 'Jerico Geraldo Situmorang', 'if420076@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(792, 3898, 4572, 'if419052', '11419052', 'Jerikho Simon Rafael Silaban', 'if419052@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(793, 3892, 4566, 'if419046', '11419046', 'Jerry Andrianto Pangaribuan', 'if419046@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(794, 778, 776, 'if412011', '21112011', 'Jerry Corbert', 'if412011@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(795, 4009, 4719, 'if320013', '11320013', 'Jesica Ananda Anastasya', 'if320013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(796, 2654, 3151, 'if318008', '11318008', 'Jesica Sianturi', 'if318008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(797, 4119, 4829, 'ce320015', '13320015', 'Jesika Laprina Manurung', 'ce320015@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(798, 2205, 2671, 'if317031', '11317031', 'Jesika Romaito Panjaitan', 'if317031@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(799, 4473, 5211, 'if322017', '11322017', 'Jessica Pasaribu', 'if322017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(800, 2742, 3241, 'if418012', '11418012', 'Jessica Ruth Natalia Siburian', 'if418012@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(801, 1763, 1905, 'ce315020', '13315020', 'Jesvika Sari Melyana Sihombing', 'ce315020@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(802, 28, 29, 'if02028', '11102028', 'Jetbar Runggu Hamonangan Dolok Saribu', 'if02028@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(803, 364, 365, 'if06060', '11106060', 'Jhon Apriando Saragih', 'if06060@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(804, 666, 664, 'if10046', '11110046', 'Jhon Boy Sibuea', 'if10046@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(805, 1720, 1862, 'if415011', '11415011', 'Jhon Charles Sipahutar', 'if415011@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(806, 1736, 1878, 'if415027', '11415027', 'Jhon Harry Ikara Putra Tampubolon', 'if415027@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(807, 2211, 2677, 'if317012', '11317012', 'Jhon Mejer Panjaitan', 'if317012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(808, 4145, 4855, 'ce320041', '13320041', 'JHON REIMON SIAGIAN', 'ce320041@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(809, 3973, 4681, 'if420002', '11420002', 'Jhon Yigibalom', 'if420002@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(810, 2152, 2560, 'if416005', '11416005', 'Jhonson Samuel Tua Hutagaol', 'if416005@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(811, 2186, 2652, 'if317044', '11317044', 'Jhosua Sinambela', 'if317044@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(812, 7, 8, 'if02007', '11102007', 'Jimmy Fines L. Tobing', 'if02007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(813, 2154, 2590, 'if316053', '11316053', 'Jimrali Yikwa', 'if316053@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(814, 784, 782, 'if412018', '21112018', 'Joas Putra Saragih', 'if412018@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(815, 704, 702, 'if11003', '11111003', 'Joel Hunter Siringoringo', 'if11003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(816, 2104, 2506, 'ce316014', '13316014', 'Joel Verdy Stevan Sipayung', 'ce316014@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(817, 1722, 1864, 'if415013', '11415013', 'Joel Wiranto Marpaung', 'if415013@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(818, 387, 387, 'if07035', '11107035', 'Jogi Henra Ersa Silalahi', 'if07035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(819, 2631, 3128, 'if318018', '11318018', 'Johan A Rajagukguk', 'if318018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Keluar', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(820, 4020, 4730, 'if320024', '11320024', 'Johan Immanuel Sianipar', 'if320024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(821, 1724, 1866, 'if415015', '11415015', 'Johan Radot Mangaratua Lumban Batu', 'if415015@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(822, 1758, 1900, 'ce315014', '13315014', 'Johan Reynaldi Sirait', 'ce315014@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(823, 870, 868, 'if312097', '11112097', 'Johanna Estrelita Simatupang', 'if312097@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(824, 4517, 5255, 'if322061', '11322061', 'Johanna Romauli Siagian', 'if322061@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(825, 638, 636, 'if10001', '11110001', 'Johannes Andi Simanjuntak', 'if10001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(826, 26, 27, 'if02026', '11102026', 'Johannes Christian Sitorus', 'if02026@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(827, 1170, 1168, 'if414023', '11414023', 'Johannes Christian Sitorus', 'if414023@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(828, 4133, 4843, 'ce320029', '13320029', 'Johannes Fransiskus Sitompul', 'ce320029@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(829, 2758, 3257, 'if418022', '11418022', 'Johannes P M Manurung', 'if418022@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(830, 182, 183, 'if03020', '11103020', 'Johannes Rapenus Manurung', 'if03020@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(831, 1149, 1147, 'if414002', '11414002', 'Johannes Waruhu', 'if414002@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(832, 39, 40, 'if02039', '11102039', 'John Elvis Sung Sitorus', 'if02039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(833, 3859, 4533, 'if419013', '11419013', 'John Ryan Siallagan', 'if419013@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(834, 4490, 5228, 'if322034', '11322034', 'JOI DIEGO NAPITUPULU', 'if322034@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(835, 150, 151, 'if03008', '11103008', 'Joice D. Simangunsong', 'if03008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(836, 846, 844, 'if312071', '11112071', 'Jojo Viet Bunder Hutagalung', 'if312071@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(837, 1076, 1074, 'if314020', '11314020', 'Joko Liber Servacius Banjarnahor', 'if314020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(838, 614, 612, 'if10025', '11110025', 'Joko Sintong Siagian', 'if10025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(839, 2140, 2543, 'if416019', '11416019', 'Jonatan Sihombing', 'if416019@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(840, 3680, 4354, 'if319021', '11319021', 'Jonathan B Hutajulu', 'if319021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(841, 764, 762, 'if11102', '11111102', 'Jonathan Borisman Tambun', 'if11102@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(842, 880, 878, 'if312107', '11112107', 'Jonathan Natanael Siahaan', 'if312107@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(843, 2220, 2686, 'if317054', '11317054', 'Joni Mustova Nababan', 'if317054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(844, 732, 730, 'if11088', '11111088', 'Jonni Fridles Silaban', 'if11088@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(845, 512, 512, 'if08038', '11108038', 'Jonny Roy', 'if08038@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(846, 1145, 1143, 'ce314028', '13314028', 'Jonson Enos Evrinando Panjaitan', 'ce314028@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(847, 1159, 1157, 'if414012', '11414012', 'Jordi Septian Hasibuan', 'if414012@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(848, 1005, 1003, 'if313071', '11113071', 'Josefh Hasudungan Simanjuntak', 'if313071@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(849, 3999, 4709, 'if320003', '11320003', 'Joshua Pratama Silitonga', 'if320003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(850, 3688, 4362, 'if319029', '11319029', 'Joshua Ryandafres Pangaribuan', 'if319029@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(851, 2079, 2479, 'if316018', '11316018', 'Josua Atmaja Sembiring', 'if316018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(852, 4141, 4851, 'ce320037', '13320037', 'JOSUA FANTER HASIHOLAN RAJA GUK-GUK', 'ce320037@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(853, 2563, 3031, 'if318067', '11318067', 'Josua Fredy Gilbert Baringbing', 'if318067@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(854, 3878, 4552, 'if419032', '11419032', 'Josua Gladson Justin Simbolon', 'if419032@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(855, 2212, 2678, 'if317020', '11317020', 'Josua Ishak Franklin Marpaung', 'if317020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(856, 1095, 1093, 'if314039', '11314039', 'Josua Lodewiyk Rumahorbo', 'if314039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(857, 4111, 4821, 'ce320007', '13320007', 'Josua Panggabean', 'ce320007@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(858, 702, 700, 'if11078', '11111078', 'Josua Putra Silitonga', 'if11078@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(859, 2633, 3130, 'if318022', '11318022', 'Josua Rinoldi Silalahi', 'if318022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(860, 2122, 2524, 'if416001', '11416001', 'Josua Sinaga', 'if416001@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(861, 4393, 5128, 'if321012', '11321012', 'JOSUA SIREGAR', 'if321012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(862, 3867, 4541, 'if419021', '11419021', 'Jovan Imanuel Sigalingging', 'if419021@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(863, 2113, 2515, 'ce316025', '13316025', 'Joy Vinensius Meliala', 'ce316025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(864, 892, 890, 'if413005', '21113005', 'Joyce Rotua Natalia Manurung', 'if413005@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(865, 4392, 5127, 'if321011', '11321011', 'Juan Carlos Munthe', 'if321011@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(866, 2751, 3250, 'if418002', '11418002', 'Juan Marihot Siallagan', 'if418002@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(867, 4481, 5219, 'if322025', '11322025', 'Juan Saut Pandapotan Sitorus', 'if322025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(868, 2062, 2462, 'if316002', '11316002', 'Jubelinda F. Silaen', 'if316002@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(869, 202, 203, 'if04005', '11104005', 'Jubliandi Napitupulu', 'if04005@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(870, 1011, 1009, 'if313109', '11113109', 'Julia A. Butarbutar', 'if313109@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(871, 464, 464, 'if08050', '11108050', 'Julia Florence Sibarani', 'if08050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(872, 1591, 1732, 'if315015', '11315015', 'Juliana Christin Siagian', 'if315015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(873, 2126, 2529, 'if416006', '11416006', 'Juliana Siahaan', 'if416006@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(874, 2745, 3244, 'if418036', '11418036', 'Juliana Situmorang', 'if418036@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Keluar', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(875, 2302, 2768, 'if417002', '11417002', 'Juliana Tiurmauli Turnip', 'if417002@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(876, 250, 251, 'if05024', '11105024', 'JULIANTI MUNTHE', 'if05024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(877, 4437, 5172, 'if321056', '11321056', 'Julianti Sitorus', 'if321056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(878, 2299, 2765, 'if417024', '11417024', 'Julio Yeremia Panjaitan', 'if417024@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(879, 1723, 1865, 'if415014', '11415014', 'Juliper Simanjuntak', 'if415014@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(880, 3665, 4339, 'if319006', '11319006', 'Julius Martogi Hamonangan Samosir', 'if319006@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(881, 95, 96, 'if01024', '11101024', 'Jumadi Simangunsong', 'if01024@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(882, 1759, 1901, 'ce315016', '13315016', 'Junedi Rajagukguk', 'ce315016@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(883, 543, 542, 'if09066', '11109066', 'Juniaty  Elisabeth Manihuruk', 'if09066@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(884, 2715, 3213, 'ce318005', '13318005', 'Junika H Tobing', 'ce318005@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(885, 437, 437, 'if07016', '11107016', 'Junita Napitupulu', 'if07016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(886, 2234, 2700, 'if317032', '11317032', 'Junita Siregar', 'if317032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(887, 297, 298, 'if05080', '11105080', 'JUNITA SITORUS', 'if05080@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(888, 13, 14, 'if02013', '11102013', 'Jurandi Lumban Gaol', 'if02013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(889, 4038, 4748, 'if320042', '11320042', 'Juwita D. Sitorus', 'if320042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(890, 2110, 2512, 'ce316020', '13316020', 'Kamna Natalia Siahaan', 'ce316020@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(891, 4151, 4861, 'ce320047', '13320047', 'Kania Reski Amalya S.', 'ce320047@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(892, 701, 699, 'if11053', '11111053', 'Kartika Karianta Pardede', 'if11053@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(893, 852, 850, 'if312077', '11112077', 'Kartika Sari Sitorus', 'if312077@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(894, 4116, 4826, 'ce320012', '13320012', 'KARYN LEONI MANIK', 'ce320012@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(895, 1062, 1060, 'if314006', '11314006', 'Katri Hutabarat', 'if314006@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(896, 2175, 2611, 'if316054', '11316054', 'Kelabur Kogoya', 'if316054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(897, 2091, 2491, 'if316031', '11316031', 'Kelvin Rayner Christian Nainggolan', 'if316031@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(898, 1173, 1171, 'if414026', '11414026', 'Kemas Muhammad Rouf', 'if414026@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(899, 4495, 5233, 'if322039', '11322039', 'KENAN TOMFIE BUKIT', 'if322039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(900, 4478, 5216, 'if322022', '11322022', 'Keren Simanjuntak', 'if322022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(901, 4498, 5236, 'if322042', '11322042', 'KESIA ROTUA SIHOMBING', 'if322042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(902, 2102, 2504, 'ce316012', '13316012', 'Kevin Aprilio Turnip', 'ce316012@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(903, 1042, 1040, 'if313100', '11113100', 'Kevin Godrikus Archibald Tagading Pardosi', 'if313100@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(904, 4128, 4838, 'ce320024', '13320024', 'Kevin Immanuel Harefa', 'ce320024@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(905, 3888, 4562, 'if419042', '11419042', 'Kevin Johannes Pakpahan', 'if419042@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(906, 1604, 1745, 'if315028', '11315028', 'Kevin Jordan Lumban Raja', 'if315028@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(907, 786, 784, 'if412020', '21112020', 'Kevin Kahal Paulus Siregar', 'if412020@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(908, 2737, 3236, 'if418051', '11418051', 'Kevin Martua Aruan', 'if418051@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(909, 2687, 3184, 'ce318050', '13318050', 'Kevin Nabasha Nainggolan', 'ce318050@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(910, 2678, 3175, 'ce318054', '13318054', 'Kevin Polin Hutabarat', 'ce318054@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(911, 340, 341, 'if06036', '11106036', 'Kevin Pratama Tinambunan', 'if06036@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(912, 934, 932, 'if313035', '11113035', 'Kevin Rozaldo Christo Pakpahan', 'if313035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(913, 4017, 4727, 'if320021', '11320021', 'KEVIN SORI MUDA NAINGGOLAN', 'if320021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(914, 2711, 3209, 'ce318049', '13318049', 'Kevin Timothy Oloando Manalu', 'ce318049@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(915, 2208, 2674, 'if317002', '11317002', 'Kevin Veros Hamonangan', 'if317002@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(916, 3730, 4404, 'ce319014', '13319014', 'Kevin Winterlu', 'ce319014@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(917, 2683, 3180, 'ce318028', '13318028', 'Kevin Y A Siahaan', 'ce318028@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(918, 3879, 4553, 'if419033', '11419033', 'Kevin Yoyada Tambunan', 'if419033@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(919, 2778, 3277, 'if418030', '11418030', 'Kiki Ferawati Sianipar', 'if418030@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(920, 178, 179, 'if03069', '11103069', 'Koko Surya Lingga', 'if03069@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(921, 2195, 2661, 'if317033', '11317033', 'Kornelius Septajasa Sipayung', 'if317033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(922, 3994, 4702, 'if420023', '11420023', 'Kris Wenda', 'if420023@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(923, 4382, 5117, 'if321001', '11321001', 'KRISNA PANDY WINATA SARAGIH', 'if321001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(924, 2063, 2463, 'if316003', '11316003', 'Krisnomi Nainggolan', 'if316003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(925, 92, 93, 'if02098', '11102098', 'Kristian Butar-Butar', 'if02098@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(926, 800, 798, 'if412033', '21112033', 'Kristian Sibarani', 'if412033@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(927, 2178, 2614, 'if316056', '11316056', 'Kristian Weya', 'if316056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(928, 713, 711, 'if11054', '11111054', 'Kristiani Sirait', 'if11054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(929, 2258, 2724, 'ce317012', '13317012', 'Kristina  Natalia Sitinjak', 'ce317012@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(930, 1014, 1012, 'if313076', '11113076', 'Kristina Bakara', 'if313076@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(931, 4500, 5238, 'if322044', '11322044', 'Kristina Sitorus', 'if322044@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(932, 1169, 1167, 'if414022', '11414022', 'Kristine Pangaribuan', 'if414022@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(933, 2291, 2757, 'if417003', '11417003', 'Kristopel Lumbantoruan', 'if417003@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(934, 839, 837, 'if412028', '21112028', 'Kristopel Martin Lumbantoruan', 'if412028@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(935, 506, 506, 'if08029', '11108029', 'Kristyna Simanjuntak', 'if08029@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(936, 2661, 3158, 'if318020', '11318020', 'Kyrie Cettyara Eleison Purba', 'if318020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(937, 740, 738, 'if11025', '11111025', 'Laborawaty Rajagukguk', 'if11025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11');
INSERT INTO `mahasiswa` (`id`, `dim_id`, `user_id`, `user_name`, `nim`, `nama`, `email`, `prodi_id`, `prodi_name`, `fakultas`, `angkatan`, `nomor_telepon`, `status`, `asrama`, `created_at`, `updated_at`) VALUES
(938, 4361, 5071, 'if420075', '11420075', 'Laksamana Yosua Parasian S.', 'if420075@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(939, 2250, 2716, 'ce317016', '13317016', 'Lambok Parsaulian Silitonga', 'ce317016@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(940, 1765, 1907, 'ce315022', '13315022', 'Lambok Sinaga', 'ce315022@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(941, 821, 819, 'if312044', '11112044', 'Lamhot J. M. Siagian', 'if312044@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(942, 2762, 3261, 'if418040', '11418040', 'Lamhot Sion Hasudungan Pardede', 'if418040@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(943, 2750, 3249, 'if418009', '11418009', 'Lamsihar Sirait', 'if418009@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(944, 2770, 3269, 'if418006', '11418006', 'Lamtiur Tarida Sianipar', 'if418006@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(945, 820, 818, 'if312043', '11112043', 'Land Rain Hard Siregar', 'if312043@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(946, 2440, 2907, 'ce319028', '13319028', 'Lando Basana Marpaung', 'ce319028@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(947, 1100, 1098, 'if314044', '11314044', 'Lanris Pandapotan Napitupulu', 'if314044@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(948, 491, 491, 'if08012', '11108012', 'Lasdiarion A. Simanjuntak', 'if08012@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(949, 2124, 2526, 'if416003', '11416003', 'Lasma R. Pardosi', 'if416003@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(950, 816, 814, 'if312038', '11112038', 'Lasma Silalahi', 'if312038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(951, 690, 688, 'if11042', '11111042', 'Lasria Wenny Wulan Silalahi', 'if11042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(952, 3906, 4580, 'if419060', '11419060', 'Lastri Rohani Nababan', 'if419060@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(953, 4511, 5249, 'if322055', '11322055', 'Laura L Naiborhu', 'if322055@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(954, 2729, 3227, 'ce318055', '13318055', 'Laura oliphia sianturi', 'ce318055@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(955, 4442, 5177, 'if321061', '11321061', 'Laura Prilia Sipahutar', 'if321061@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(956, 765, 763, 'if412001', '21112001', 'Laurensius Sakti Lubis', 'if412001@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(957, 4411, 5146, 'if321030', '11321030', 'Lawy Xenna L.Gaol', 'if321030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(958, 2245, 2711, 'if317061', '11317061', 'Layla Hafni Ainun Hutasuhut', 'if317061@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(959, 805, 803, 'if312026', '11112026', 'Lekjon Julianto Sinurat', 'if312026@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(960, 2168, 2604, 'if316047', '11316047', 'Lemi Wenda', 'if316047@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(961, 554, 553, 'if09068', '11109068', 'Leney Nadeak', 'if09068@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(962, 501, 501, 'if08024', '11108024', 'Lengmay Simamora', 'if08024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(963, 2144, 2548, 'if416025', '11416025', 'Leni Maya Sihombing', 'if416025@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(964, 2781, 3280, 'if418033', '11418033', 'Lenisa Simangunsong', 'if418033@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(965, 4483, 5221, 'if322027', '11322027', 'Lenni Marpaung', 'if322027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(966, 1593, 1734, 'if315017', '11315017', 'Leo Parhaposan Pakpahan', 'if315017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(967, 678, 676, 'if11016', '11111016', 'Leo Pripos Marbun', 'if11016@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(968, 1071, 1069, 'if314015', '11314015', 'Leo Torivan Siburian', 'if314015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(969, 2150, 2554, 'if416031', '11416031', 'Leonaldo Jose Nathanael Pasaribu', 'if416031@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(970, 2733, 3232, 'if418017', '11418017', 'Leonard Halomoan Sihombing', 'if418017@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(971, 3725, 4399, 'ce319009', '13319009', 'Leonardo Saragih', 'ce319009@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(972, 3676, 4350, 'if319017', '11319017', 'Leonardo Siagian', 'if319017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(973, 2155, 2591, 'if316038', '11316038', 'Les Wakur Wendanak', 'if316038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(974, 480, 480, 'if08072', '11108072', 'Lestari Lumbanbatu', 'if08072@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(975, 1595, 1736, 'if315019', '11315019', 'Lestari Natalia Hasibuan', 'if315019@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(976, 346, 347, 'if06042', '11106042', 'Lestari Sirait', 'if06042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(977, 1735, 1877, 'if415026', '11415026', 'Lestari Siregar', 'if415026@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(978, 835, 833, 'if312060', '11112060', 'Lestari Tambunan', 'if312060@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(979, 2611, 3108, 'if318054', '11318054', 'Lestari Uli Lumban Gaol', 'if318054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Lulus', 'Pniel', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(980, 118, 119, 'if01026', '11101026', 'Lestina Hariana Sihombing', 'if01026@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(981, 847, 845, 'if312072', '11112072', 'Lexys Jayanta', 'if312072@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(982, 341, 342, 'if06037', '11106037', 'Lia Martina Pardede', 'if06037@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(983, 1106, 1104, 'if314050', '11314050', 'Liana Diantri Sianturi', 'if314050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(984, 4155, 4865, 'ce320051', '13320051', 'Lidia kesvina pasaribu', 'ce320051@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(985, 2206, 2672, 'if317053', '11317053', 'Lidia Siahaan', 'if317053@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(986, 1539, 1668, 'if315001', '11315001', 'Lidya Christine Marsaulina Silitonga', 'if315001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(987, 2235, 2701, 'if317037', '11317037', 'Lidya Pebrina Manurung', 'if317037@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(988, 2289, 2755, 'if417006', '11417006', 'Lilis Lestari Sinurat', 'if417006@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(989, 3861, 4535, 'if419015', '11419015', 'Lilis Marito Pardosi', 'if419015@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(990, 1733, 1875, 'if415024', '11415024', 'Lily Anastasia Naibaho', 'if415024@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(991, 172, 173, 'if03092', '11103092', 'Linar Siboro', 'if03092@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(992, 945, 943, 'if313013', '11113013', 'Liza Venita Debora Pardede', 'if313013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(993, 3681, 4355, 'if319022', '11319022', 'Loise Michael Lumban Raja', 'if319022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(994, 715, 713, 'if11056', '11111056', 'Lolicha Napitupulu', 'if11056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(995, 2072, 2472, 'if316011', '11316011', 'Loni Miranda Doloksaribu', 'if316011@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(996, 2248, 2714, 'if317059', '11317059', 'Lorennia Hasugian', 'if317059@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(997, 777, 775, 'if412010', '21112010', 'Louis Dwy Sevrey Ompusunggu', 'if412010@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(998, 1107, 1105, 'if314051', '11314051', 'Louis Onike Munte', 'if314051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(999, 2624, 3121, 'if318045', '11318045', 'Loveleen Margareth Roose Sinaga', 'if318045@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1000, 4452, 5187, 'if321071', '11321071', 'Lovinta Oktavia Hutagalung', 'if321071@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1001, 4402, 5137, 'if321021', '11321021', 'Luana Breka Manuela Banjarnahor', 'if321021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1002, 2690, 3187, 'ce318023', '13318023', 'Lucky Marito Siregar', 'ce318023@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1003, 2628, 3125, 'if318065', '11318065', 'Lucy Marito Fransisca Sihite', 'if318065@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1004, 3715, 4389, 'if319056', '11319056', 'Lucy Patrecia Butar-Butar', 'if319056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1005, 2118, 2520, 'ce316029', '13316029', 'Lukas Reinhard Sinambela', 'ce316029@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1006, 974, 972, 'if313078', '11113078', 'Lusi Indah', 'if313078@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1007, 1110, 1108, 'if314054', '11314054', 'Luzerna Putri Sihombing', 'if314054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1008, 1047, 1045, 'if413063', '21113063', 'Lydia Natalia Panjaitan', 'if413063@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1009, 4355, 5065, 'if420069', '11420069', 'Lydwina Gracella Purba', 'if420069@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1010, 2112, 2514, 'ce316023', '13316023', 'Lyocy Hotria Sitohang', 'ce316023@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1011, 845, 843, 'if312070', '11112070', 'M. H. Fransisca N.', 'if312070@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1012, 61, 62, 'if03055', '11103055', 'M. H. Vascaranto', 'if03055@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1013, 3695, 4369, 'if319036', '11319036', 'Madelin Panjaitan', 'if319036@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1014, 2067, 2467, 'if316007', '11316007', 'Magdalena Simamora', 'if316007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1015, 49, 50, 'if02049', '11102049', 'Maince Panca Wirda Batubara', 'if02049@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1016, 619, 617, 'if10020', '11110020', 'Maldini Dona Doni Hutapea', 'if10020@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1017, 3885, 4559, 'if419039', '11419039', 'Malino Win Crisnando Sihotang', 'if419039@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1018, 3982, 4690, 'if420011', '11420011', 'Maluk Morip', 'if420011@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1019, 4479, 5217, 'if322023', '11322023', 'Mananda Atalya Tambun', 'if322023@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1020, 2174, 2610, 'if316039', '11316039', 'Mando Kogoya', 'if316039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1021, 50, 51, 'if02050', '11102050', 'Mangambas Siahaan', 'if02050@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1022, 25, 26, 'if02025', '11102025', 'Mangapul Siahaan', 'if02025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1023, 4114, 4824, 'ce320010', '13320010', 'MANGINAR NAPITUPULU', 'ce320010@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1024, 1144, 1142, 'ce314027', '13314027', 'Manogi Pardomuan Rumahorbo', 'ce314027@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1025, 2767, 3266, 'if418059', '11418059', 'Manogunawan Resqi Gultom', 'if418059@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1026, 17, 18, 'if02017', '11102017', 'Manumpak Ricardo Tambunan', 'if02017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1027, 4474, 5212, 'if322018', '11322018', 'Maranatha Siahaan', 'if322018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1028, 873, 871, 'if312100', '11112100', 'Marcelina Panggabean', 'if312100@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1029, 4325, 5035, 'if420039', '11420039', 'Marcellino Kelly N. Lumban Gaol', 'if420039@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1030, 2074, 2474, 'if316013', '11316013', 'Marchel Pirma Sakti Hutagalung', 'if316013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1031, 4041, 4751, 'if320045', '11320045', 'Marchellya Dwi Zevanya Lumban Gaol', 'if320045@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1032, 891, 889, 'if313002', '11113002', 'Margaret Teacher Banjarnahor', 'if313002@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1033, 2135, 2538, 'if416016', '11416016', 'Margaretta Ruth Verawati Simanjuntak', 'if416016@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1034, 2741, 3240, 'if418005', '11418005', 'Margrieta Sidabutar', 'if418005@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1035, 4338, 5048, 'if420052', '11420052', 'Maria Chrisyanti Sitanggang', 'if420052@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1036, 4461, 5199, 'if322005', '11322005', 'Maria Elimadona Sibarani', 'if322005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1037, 656, 654, 'if10056', '11110056', 'Maria Fazrina Nainggolan', 'if10056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1038, 4451, 5186, 'if321070', '11321070', 'MARIA FRANSISKA GIAWA', 'if321070@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1039, 889, 887, 'if313001', '11113001', 'Maria Magdalena Panjaitan', 'if313001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1040, 4464, 5202, 'if322008', '11322008', 'Maria Pangaribuan', 'if322008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1041, 2261, 2727, 'ce317030', '13317030', 'Maria S Sitanggang', 'ce317030@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1042, 4050, 4760, 'if320054', '11320054', 'Maria sopia purba', 'if320054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1043, 2311, 2777, 'if417028', '11417028', 'Mariana Putri Sinaga', 'if417028@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1044, 958, 956, 'if313059', '11113059', 'Mariana Sisilia Tambunan', 'if313059@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1045, 998, 996, 'if413049', '21113049', 'Mariani Febirianti', 'if413049@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1046, 4445, 5180, 'if321064', '11321064', 'Marianne Wensesla Solang', 'if321064@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1047, 4486, 5224, 'if322030', '11322030', 'Mario Andreas Manurung', 'if322030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1048, 4359, 5069, 'if420073', '11420073', 'Mario Christian Raydavey Tangkas', 'if420073@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1049, 3896, 4570, 'if419050', '11419050', 'Mario Wira Pratama Purba', 'if419050@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1050, 3910, 4584, 'if419064', '11419064', 'Maristella Sere Viona Sitanggang', 'if419064@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1051, 4320, 5030, 'if420034', '11420034', 'Maristo Pane', 'if420034@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1052, 1131, 1129, 'ce314014', '13314014', 'Maritia Pangaribuan', 'ce314014@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1053, 813, 811, 'if312035', '11112035', 'Mariyani Pardede', 'if312035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1054, 644, 642, 'if10068', '11110068', 'Markus Mulia Marsada Panjaitan', 'if10068@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1055, 1747, 1889, 'ce315004', '13315004', 'Marni Panjaitan', 'ce315004@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1056, 119, 120, 'if01027', '11101027', 'Marojahan Mula Timbul Sigiro', 'if01027@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1057, 827, 825, 'if312051', '11112051', 'Marta Gresi Septini Sitanggang', 'if312051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1058, 932, 930, 'if313038', '11113038', 'Martalina Tabita Sitorus', 'if313038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1059, 62, 63, 'if02062', '11102062', 'Martha Aprilina Simanungkalit', 'if02062@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1060, 1150, 1148, 'if414003', '11414003', 'Martha Yosephine Tampubolon', 'if414003@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1061, 1892, 2291, 'if317067', '11317067', 'Marthin Halomoan Tampubolon', 'if317067@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1062, 766, 764, 'if412002', '21112002', 'Marthin M. H. Pakpahan', 'if412002@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1063, 923, 921, 'if413073', '21113073', 'Marthin Satrya Pasaribu', 'if413073@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1064, 1744, 1886, 'ce315001', '13315001', 'Martin Silitonga', 'ce315001@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1065, 2735, 3234, 'if418045', '11418045', 'Martin Simanjuntak', 'if418045@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1066, 2080, 2480, 'if316019', '11316019', 'Martina Grace  Panjaitan', 'if316019@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1067, 927, 925, 'if313011', '11113011', 'Martinus Iron Sijabat', 'if313011@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1068, 2638, 3135, 'if318031', '11318031', 'Martinus Yudha Chrisanto Sitinjak', 'if318031@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1069, 3897, 4571, 'if419051', '11419051', 'Martuani Sitohang', 'if419051@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1070, 1732, 1874, 'if415023', '11415023', 'Martupa H.S Lumbantoruan', 'if415023@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1071, 336, 337, 'if06032', '11106032', 'Marudut Manullang', 'if06032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1072, 669, 667, 'if10043', '11110043', 'Marudut Sihar P. Sitorus', 'if10043@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1073, 622, 620, 'if10017', '11110017', 'Marudut Try Putra Marpaung', 'if10017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1074, 4379, 5090, 'ce320056', '13320056', 'Maruli Agustina Siagian', 'ce320056@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1075, 4025, 4735, 'if320029', '11320029', 'Maruli Tua Hasian Siagian', 'if320029@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1076, 2078, 2478, 'if316017', '11316017', 'Marya Delima Simanjuntak', 'if316017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1077, 659, 657, 'if10053', '11110053', 'Maryo Sandoz Yudha Nababan', 'if10053@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1078, 4423, 5158, 'if321042', '11321042', 'Maryono Marpaung', 'if321042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1079, 1010, 1008, 'if313051', '11113051', 'Masita Sesilia Pasaribu', 'if313051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1080, 1119, 1117, 'ce314002', '13314002', 'Masliana Simanjuntak', 'ce314002@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1081, 1065, 1063, 'if314009', '11314009', 'Masri Pakpahan', 'if314009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1082, 496, 496, 'if08017', '11108017', 'MASTO SITORUS', 'if08017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1083, 2696, 3194, 'ce318018', '13318018', 'Matius Agung Nugraha Purba', 'ce318018@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1084, 4006, 4716, 'if320010', '11320010', 'Matthew Alfredo', 'if320010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1085, 4510, 5248, 'if322054', '11322054', 'Maudy Octavia S', 'if322054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1086, 197, 198, 'if03085', '11103085', 'Maurice Andreas Saragih', 'if03085@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1087, 1116, 1114, 'if314060', '11314060', 'May Kana Sagala', 'if314060@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1088, 2121, 2523, 'ce316032', '13316032', 'Maya Sabrina A Simanjuntak', 'ce316032@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1089, 121, 122, 'if01029', '11101029', 'Mayer Antono Situmeang', 'if01029@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1090, 575, 574, 'if09021', '11109021', 'Mazmur Daniel Sitanggang', 'if09021@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1091, 2739, 3238, 'if418057', '11418057', 'Mazmur Noverich Batuara', 'if418057@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1092, 2765, 3264, 'if418047', '11418047', 'Medianto Saragih', 'if418047@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1093, 4401, 5136, 'if321020', '11321020', 'MEGA KRISTINA MARBUN', 'if321020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1094, 4439, 5174, 'if321058', '11321058', 'MEGARIA NAPITUPULU', 'if321058@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1095, 770, 768, 'if312003', '11112003', 'Mei Fanora Samosir', 'if312003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1096, 3852, 4526, 'if419006', '11419006', 'Mei Pane', 'if419006@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1097, 2065, 2465, 'if316005', '11316005', 'Mei Romauli Sagala', 'if316005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1098, 2478, 2945, 'ce318059', '13318059', 'Melani Sarah Siagian', 'ce318059@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1099, 1751, 1893, 'ce315007', '13315007', 'Melati Panjaitan', 'ce315007@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1100, 2093, 2493, 'ce316007', '13316007', 'Melda Rina Manik', 'ce316007@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1101, 472, 472, 'if08059', '11108059', 'Melisa Doana Napitupulu', 'if08059@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1102, 2783, 3282, 'if418037', '11418037', 'Melisa G D Simanjuntak', 'if418037@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Keluar', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1103, 2203, 2669, 'if317038', '11317038', 'Melisa Pangaribuan', 'if317038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1104, 2622, 3119, 'if318036', '11318036', 'Melva Panjaitan', 'if318036@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1105, 833, 831, 'if312058', '11112058', 'Melyana Sari Fransisca', 'if312058@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1106, 2779, 3278, 'if418031', '11418031', 'Melysa Tampubolon', 'if418031@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1107, 686, 684, 'if11038', '11111038', 'Menpan Mediator Sidabutar', 'if11038@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1108, 4363, 5073, 'if420077', '11420077', 'Mentari T. Sihombing', 'if420077@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1109, 2732, 3231, 'if418013', '11418013', 'Meriati Gabriella Pane', 'if418013@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1110, 2156, 2592, 'if316048', '11316048', 'Merinus Kogoya', 'if316048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1111, 223, 224, 'if04032', '11104032', 'MERLAND RICARDO B.  E.  SIANTURI', 'if04032@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1112, 815, 813, 'if312037', '11112037', 'Merlin Palentine Sidabutar', 'if312037@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1113, 4117, 4827, 'ce320013', '13320013', 'Mersi Suryani Siagian', 'ce320013@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1114, 639, 637, 'if10073', '11110073', 'Meryana Siringoringo', 'if10073@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1115, 1130, 1128, 'ce314013', '13314013', 'Mesdi Silitonga', 'ce314013@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1116, 1109, 1107, 'if314053', '11314053', 'Mesmer Messier Satya Immanuel Sinaga', 'if314053@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1117, 4448, 5183, 'if321067', '11321067', 'Mesya Angeliqa Hutagalung', 'if321067@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1118, 4150, 4860, 'ce320046', '13320046', 'Methylda Fiorentina Sirait', 'ce320046@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1119, 643, 641, 'if10069', '11110069', 'Metilova Sitorus', 'if10069@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1120, 3904, 4578, 'if419058', '11419058', 'Meyliza Veronica Br Siregar', 'if419058@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1121, 3708, 4382, 'if319049', '11319049', 'Mia Audina Gultom', 'if319049@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1122, 3683, 4357, 'if319024', '11319024', 'Michael Anwar Siregar', 'if319024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1123, 4329, 5039, 'if420043', '11420043', 'Michael atur tito sitorus', 'if420043@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1124, 3858, 4532, 'if419012', '11419012', 'Michael Binsar Tua Sinaga', 'if419012@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1125, 3851, 4525, 'if419005', '11419005', 'Michael Joseph Christian Situmorang', 'if419005@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1126, 2700, 3198, 'ce318029', '13318029', 'Michael Ollifarel Pangihutan Sagala', 'ce318029@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1127, 665, 663, 'if10047', '11110047', 'Michael Tobby Sembiring', 'if10047@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1128, 2644, 3141, 'if318050', '11318050', 'Michael Yulian Hutagalung', 'if318050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1129, 1121, 1119, 'ce314004', '13314004', 'Mika Sularti Silaen', 'ce314004@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1130, 3883, 4557, 'if419037', '11419037', 'Mikhael Hutapea', 'if419037@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1131, 654, 652, 'if10058', '11110058', 'Milca Satriyani Sagala', 'if10058@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1132, 60, 61, 'if02060', '11102060', 'Milton Vandalen Siahaan', 'if02060@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1133, 837, 835, 'if412024', '21112024', 'Mindo F. Panjaitan', 'if412024@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1134, 1040, 1038, 'if413075', '21113075', 'Mindo Parsaulian Sormin', 'if413075@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1135, 2769, 3268, 'if418003', '11418003', 'Miranti Sinaga', 'if418003@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1136, 1167, 1165, 'if414020', '11414020', 'Monalisa Paulima Artha Pasaribu', 'if414020@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1137, 4518, 5256, 'if322062', '11322062', 'Monica Silaban', 'if322062@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1138, 756, 754, 'if11092', '11111092', 'Monica Sitinjak', 'if11092@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1139, 991, 989, 'if313067', '11113067', 'Monika Theresia Siahaan', 'if313067@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1140, 3733, 4407, 'ce319017', '13319017', 'Moses Prisarsta Manurung', 'ce319017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1141, 435, 435, 'if07013', '11107013', 'Mucktar Pakpahan', 'if07013@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1142, 632, 630, 'if10007', '11110007', 'Muhammad Hendry Nadial', 'if10007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1143, 2705, 3203, 'ce318034', '13318034', 'Muliando Marpaung', 'ce318034@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1144, 222, 223, 'if04031', '11104031', 'MUSTAFA TAMBUNAN', 'if04031@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1145, 3704, 4378, 'if319045', '11319045', 'Mustika Marito Siahaan', 'if319045@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1146, 712, 710, 'if11032', '11111032', 'Muti Insani Siahaan', 'if11032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1147, 2242, 2708, 'if317051', '11317051', 'Mutiara Magdalena Simamora', 'if317051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1148, 3870, 4544, 'if419024', '11419024', 'Naldo Tua Samosir', 'if419024@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1149, 2664, 3161, 'if318034', '11318034', 'Nancy Aprelia Sibarani', 'if318034@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1150, 1738, 1880, 'if415028', '11415028', 'Nancymona Situmorang', 'if415028@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1151, 2129, 2532, 'if416023', '11416023', 'Nani Renova Hutagaol', 'if416023@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1152, 4501, 5239, 'if322045', '11322045', 'Nania avantika oligiviana Pangaribuan', 'if322045@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1153, 2937, 3437, 'if319059', '11319059', 'Naomi Grasella Simangunsong', 'if319059@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1154, 981, 979, 'if313028', '11113028', 'Naomi Olga Panjaitan', 'if313028@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1155, 4380, 5091, 'ce320057', '13320057', 'Naomi Theresia U. Silitonga', 'ce320057@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1156, 1549, 1678, 'if315011', '11315011', 'Naomi Zabrina Lumbantobing', 'if315011@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1157, 1096, 1094, 'if314040', '11314040', 'Narodo Mario Lumban Tobing', 'if314040@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1158, 3692, 4366, 'if319033', '11319033', 'Nasrani Meilan Sitorus', 'if319033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1159, 973, 971, 'if313054', '11113054', 'Natalia Desfri Hutabarat', 'if313054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1160, 3996, 4704, 'if420025', '11420025', 'Natalia Merlin Genongga', 'if420025@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1161, 4118, 4828, 'ce320014', '13320014', 'NATASHA GABRIELA SINAGA', 'ce320014@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1162, 3849, 4523, 'if419003', '11419003', 'Nathan Fernando Lumban Tobing', 'if419003@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1163, 2082, 2482, 'if316021', '11316021', 'Nathan Mora Tua Nainggolan', 'if316021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1164, 3866, 4540, 'if419020', '11419020', 'Nazir Manahan Manurung', 'if419020@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1165, 1142, 1140, 'ce314025', '13314025', 'Nefty Novia Aritonang', 'ce314025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1166, 4345, 5055, 'if420059', '11420059', 'NEHEMY THERESIA SIHOMBING', 'if420059@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1167, 3975, 4683, 'if420004', '11420004', 'Nendi Kogoya', 'if420004@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1168, 3980, 4688, 'if420009', '11420009', 'Nendius Wenda', 'if420009@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1169, 1166, 1164, 'if414019', '11414019', 'Nepy Esterliani Gulo', 'if414019@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1170, 2640, 3137, 'if318047', '11318047', 'Nesta Waldemar Binardo Tambunan', 'if318047@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11');
INSERT INTO `mahasiswa` (`id`, `dim_id`, `user_id`, `user_name`, `nim`, `nama`, `email`, `prodi_id`, `prodi_name`, `fakultas`, `angkatan`, `nomor_telepon`, `status`, `asrama`, `created_at`, `updated_at`) VALUES
(1171, 4312, 5022, 'if420026', '11420026', 'Nesty Gloria Tampubolon', 'if420026@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1172, 2653, 3150, 'if318006', '11318006', 'Nevi Aktasia Banjarnahor', 'if318006@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1173, 581, 580, 'if09027', '11109027', 'Nia H. Chkristiani Banjar Nahor', 'if09027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1174, 4129, 4839, 'ce320025', '13320025', 'Nicholas Alexander', 'ce320025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1175, 4147, 4857, 'ce320043', '13320043', 'Nicholas Canakya Pardosi', 'ce320043@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1176, 1766, 1908, 'ce315023', '13315023', 'Nickholas Septian Pangaribuan', 'ce315023@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1177, 2693, 3190, 'ce318002', '13318002', 'Nico Ardi Panjaitan', 'ce318002@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1178, 4033, 4743, 'if320037', '11320037', 'Nico Felix Sipahutar', 'if320037@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1179, 954, 952, 'if313009', '11113009', 'Nico July Habonaran Saragih', 'if313009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1180, 2635, 3132, 'if318025', '11318025', 'Nicolas Martinus Manurung', 'if318025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1181, 4427, 5162, 'if321046', '11321046', 'NICOLAS NAPITUPULU', 'if321046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1182, 3989, 4697, 'if420018', '11420018', 'Niel Penggu', 'if420018@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1183, 4491, 5229, 'if322035', '11322035', 'Niko alvin simanjuntak', 'if322035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1184, 312, 313, 'if06006', '11106006', 'Nikson Patar Tampubolon', 'if06006@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1185, 521, 521, 'if08065', '11108065', 'Nindhia Hutagaol', 'if08065@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1186, 2252, 2718, 'ce317014', '13317014', 'Nindy Pitta Erika Panjaitan', 'ce317014@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1187, 4512, 5250, 'if322056', '11322056', 'Nita Herlinda Kurnyawati Simangunsong', 'if322056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1188, 890, 888, 'if413004', '21113004', 'Nita Yolanda Lumbantobing', 'if413004@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1189, 4373, 5083, 'if420087', '11420087', 'Nivshea Estetica', 'if420087@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1190, 967, 965, 'if313058', '11113058', 'Nobel Lina Sidabutar', 'if313058@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1191, 4357, 5067, 'if420071', '11420071', 'NOEL ALEX MANOGARI SIMANJUNTAK', 'if420071@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1192, 2128, 2531, 'if416008', '11416008', 'Noni Sari Tambunan', 'if416008@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1193, 620, 618, 'if10019', '11110019', 'Nonie Sintayani Purba', 'if10019@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1194, 428, 428, 'if07002', '11107002', 'Nopelina Posma Rotua Simamora', 'if07002@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1195, 2618, 3115, 'if318014', '11318014', 'Noplin Siagian', 'if318014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1196, 2227, 2693, 'if317011', '11317011', 'Nopri Yendry S', 'if317011@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1197, 2756, 3255, 'if418019', '11418019', 'Norbert Ade Sanagsi Simanungkalit', 'if418019@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1198, 238, 239, 'if05008', '11105008', 'Normasari Ritonga', 'if05008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1199, 426, 426, 'if07105', '11107105', 'Nova Adriani Sinaga', 'if07105@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1200, 1037, 1035, 'if313106', '11113106', 'Nova Floren Panjaitan', 'if313106@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1201, 4046, 4756, 'if320050', '11320050', 'Nova sterhani sidabutar', 'if320050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1202, 3662, 4336, 'if319003', '11319003', 'Nova V Siringoringo', 'if319003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1203, 2133, 2536, 'if416012', '11416012', 'Nova Yanti Naipospos', 'if416012@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1204, 999, 997, 'if313068', '11113068', 'Novanti Lumban Tobing', 'if313068@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1205, 2641, 3138, 'if318046', '11318046', 'Novencus Sinambela', 'if318046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1206, 326, 327, 'if06022', '11106022', 'Noverdy Mangara Panjaitan', 'if06022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1207, 1031, 1029, 'if313090', '11113090', 'Novita Sijabat', 'if313090@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1208, 776, 774, 'if412009', '21112009', 'Novitasari Tamba', 'if412009@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1209, 4410, 5145, 'if321029', '11321029', 'Novrael Gabriel Louis Marbun', 'if321029@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1210, 1762, 1904, 'ce315019', '13315019', 'Nugraha Herianto Pangihutan S', 'ce315019@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1211, 4121, 4831, 'ce320017', '13320017', 'Nurcahaya Kerentryna S', 'ce320017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1212, 3693, 4367, 'if319034', '11319034', 'Nursista Nainggolan', 'if319034@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1213, 2219, 2685, 'if317052', '11317052', 'Obrian Rao L.Tobing', 'if317052@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1214, 696, 694, 'if11048', '11111048', 'Octarina D. Panjaitan', 'if11048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1215, 2111, 2513, 'ce316022', '13316022', 'Ojak Hotmatua Sinaga', 'ce316022@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1216, 2730, 3228, 'ce318056', '13318056', 'Okaria Veronicha Simanjuntak', 'ce318056@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1217, 271, 272, 'if05046', '11105046', 'OKTA PARIS SIHOTANG', 'if05046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1218, 1081, 1079, 'if314025', '11314025', 'Okta Pratama Nainggolan', 'if314025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1219, 4476, 5214, 'if322020', '11322020', 'Oktavia Letisya Simatupang', 'if322020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1220, 1768, 1910, 'ce315024', '13315024', 'Oktavianus V. Simanjuntak', 'ce315024@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1221, 461, 461, 'if08045', '11108045', 'Oktis Moy Hasahatan Lumbantoruan', 'if08045@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1222, 618, 616, 'if10021', '11110021', 'Olga Minar Viona Sianturi', 'if10021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1223, 1099, 1097, 'if314043', '11314043', 'Olga Slamona', 'if314043@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1224, 1139, 1137, 'ce314022', '13314022', 'Olivani Prisila Hutahaean', 'ce314022@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1225, 4021, 4731, 'if320025', '11320025', 'Oliver Nathan Sianipar', 'if320025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1226, 4503, 5241, 'if322047', '11322047', 'Olivia Apriani', 'if322047@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1227, 454, 454, 'if07034', '11107034', 'Olivia Irma Sari', 'if07034@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1228, 1056, 1054, 'if413067', '21113067', 'Olivia Tampubolon', 'if413067@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1229, 4467, 5205, 'if322011', '11322011', 'Olivier Marcus Siahaan', 'if322011@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1230, 883, 881, 'if312110', '11112110', 'Olvi Lora S', 'if312110@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1231, 4444, 5179, 'if321063', '11321063', 'OLYVIA SIAHAAN', 'if321063@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1232, 1084, 1082, 'if314028', '11314028', 'Omega Basana Samosir', 'if314028@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1233, 4333, 5043, 'if420047', '11420047', 'ONAI NADAPDAP', 'if420047@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1234, 4321, 5031, 'if420035', '11420035', 'Onra Imanuel Sihombing', 'if420035@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1235, 1156, 1154, 'if414009', '11414009', 'Oppir Hutapea', 'if414009@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1236, 3864, 4538, 'if419018', '11419018', 'Oriza sitanggang', 'if419018@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1237, 15, 16, 'if02015', '11102015', 'Osborn Sugianto Simanjuntak', 'if02015@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1238, 920, 918, 'if313030', '11113030', 'Oscar Daniel Hutajulu', 'if313030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1239, 351, 352, 'if06047', '11106047', 'Oshin  Margaretta Napitupulu', 'if06047@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1240, 1551, 1680, 'if315012', '11315012', 'Otniel Binsar Hamonangan Turnip', 'if315012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1241, 4351, 5061, 'if420065', '11420065', 'OTNIEL WIBOWO TAMBUNAN', 'if420065@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1242, 2162, 2598, 'if316034', '11316034', 'Otomi Wenda', 'if316034@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1243, 2753, 3252, 'if418008', '11418008', 'Ova Ferdinan Marbun', 'if418008@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1244, 767, 765, 'if412003', '21112003', 'Ovryenni Nosyera Pandiangan', 'if412003@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1245, 389, 389, 'if07041', '11107041', 'P. Pahalatua Beatrik Parhusip', 'if07041@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1246, 3686, 4360, 'if319027', '11319027', 'Pahala Picauly Sagala', 'if319027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1247, 2158, 2594, 'if316035', '11316035', 'Pailes Narek', 'if316035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1248, 4007, 4717, 'if320011', '11320011', 'Pakhomios Havel Situmorang', 'if320011@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1249, 1725, 1867, 'if415016', '11415016', 'Palti Gorat Christian Sinaga', 'if415016@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1250, 2613, 3110, 'if318001', '11318001', 'Palti Mangaruhut Gudmen Siregar', 'if318001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1251, 1749, 1891, 'ce315005', '13315005', 'Panca Putra Simanjuntak', 'ce315005@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1252, 909, 907, 'if313014', '11113014', 'Pance Satria Naibaho', 'if313014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1253, 2766, 3265, 'if418053', '11418053', 'Pande Raja Hutagaol', 'if418053@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1254, 4131, 4841, 'ce320027', '13320027', 'Pandu Navaldi Sipahutar', 'ce320027@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1255, 1750, 1892, 'ce315006', '13315006', 'Pangeran Tiurniari Napitupulu', 'ce315006@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1256, 510, 510, 'if08035', '11108035', 'Pangidoan Butar - Butar', 'if08035@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1257, 905, 903, 'if313105', '11113105', 'Pardin Siregar', 'if313105@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1258, 86, 87, 'if02092', '11102092', 'Paskah Wiska Parlindungan Manurung', 'if02092@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1259, 1741, 1883, 'if415031', '11415031', 'Patota Adi Petro Siahaan', 'if415031@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1260, 1775, 1917, 'ce315032', '13315032', 'PATRICIA LUITO BR MUNTE', 'ce315032@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1261, 1079, 1077, 'if314023', '11314023', 'Patrick Nabasa Manurung', 'if314023@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1262, 4334, 5044, 'if420048', '11420048', 'Patuan Garcia Situmorang', 'if420048@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1263, 1038, 1036, 'if413055', '21113055', 'Paul Marten Simanjuntak', 'if413055@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1264, 4127, 4837, 'ce320023', '13320023', 'Paul Martin Parsaulian Nainggolan', 'ce320023@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1265, 337, 338, 'if06033', '11106033', 'Paulsen Jupiter Siahaan', 'if06033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1266, 4139, 4849, 'ce320035', '13320035', 'PEDRO AGUNG MANURUNG', 'ce320035@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1267, 2254, 2720, 'ce317022', '13317022', 'Pedro Ozora Barus', 'ce317022@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1268, 2172, 2608, 'if316032', '11316032', 'Peles Yikwa', 'if316032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1269, 1594, 1735, 'if315018', '11315018', 'Pembina D.A.N. Siahaan', 'if315018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1270, 751, 749, 'if11074', '11111074', 'Perdana Martinus Situmorang', 'if11074@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1271, 1781, 1970, 'if415040', '11415040', 'Perdana Martinus Situmorang', 'if415040@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1272, 2748, 3247, 'if418028', '11418028', 'Permana', 'if418028@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1273, 125, 126, 'if01033', '11101033', 'Pesta Ferdinan Sitohang', 'if01033@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1274, 338, 339, 'if06034', '11106034', 'Pesta Horas Situmorang', 'if06034@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1275, 795, 793, 'if312011', '11112011', 'Peterson Martua Napitupulu', 'if312011@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1276, 530, 529, 'if09048', '11109048', 'Petry Aprilia Magdalena Br. Sianipar', 'if09048@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1277, 40, 41, 'if02040', '11102040', 'Philippus Manurung', 'if02040@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1278, 709, 707, 'if11017', '11111017', 'Philips', 'if11017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1279, 2455, 2922, 'ce319029', '13319029', 'Pijor M. Erianto Tobing', 'ce319029@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1280, 822, 820, 'if312046', '11112046', 'Pintor Jonathan Silitonga', 'if312046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1281, 4047, 4757, 'if320051', '11320051', 'PITA DAME', 'if320051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1282, 1059, 1057, 'if314002', '11314002', 'Pita Sri Ayu Sitorus', 'if314002@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1283, 3696, 4370, 'if319037', '11319037', 'Poibe Leny Naomi', 'if319037@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1284, 4350, 5060, 'if420064', '11420064', 'Polin Do Samuel Hutagalung', 'if420064@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1285, 73, 74, 'if02076', '11102076', 'Poltak Reynold Priyadi', 'if02076@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1286, 3978, 4686, 'if420007', '11420007', 'Ponalisa Yikwa', 'if420007@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1287, 647, 645, 'if10065', '11110065', 'Ponel Panjaitan', 'if10065@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1288, 2076, 2476, 'if316015', '11316015', 'Pratiwi Lasniate Pandiangan', 'if316015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1289, 1714, 1856, 'if415005', '11415005', 'Pratiwi Okuli Manik', 'if415005@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1290, 3668, 4342, 'if319009', '11319009', 'Pratiwi Sibarani', 'if319009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1291, 4019, 4729, 'if320023', '11320023', 'PRAYOGA COMMANDO MOSES SAMOSIR', 'if320023@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1292, 3916, 4624, 'if419801', '11419801', 'Premi Yigibalom', 'if419801@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1293, 2160, 2596, 'if316045', '11316045', 'Premi Yigibalom', 'if316045@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1294, 1112, 1110, 'if314056', '11314056', 'Prety Natalia Girsang', 'if314056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1295, 853, 851, 'if312078', '11112078', 'Prima Elgania', 'if312078@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1296, 2096, 2497, 'ce316005', '13316005', 'Prima Sinaga', 'ce316005@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1297, 251, 252, 'if05025', '11105025', 'PRIMADONA PARDEDE', 'if05025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1298, 3700, 4374, 'if319041', '11319041', 'Priskila Apriliana Nababan', 'if319041@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1299, 1111, 1109, 'if314055', '11314055', 'Purnama Pasaribu', 'if314055@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1300, 2173, 2609, 'if316033', '11316033', 'Pusi Weya', 'if316033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1301, 2264, 2730, 'ce317001', '13317001', 'Putra Bakti Butarbutar', 'ce317001@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1302, 4420, 5155, 'if321039', '11321039', 'PUTRA JAYA MANURUNG', 'if321039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1303, 965, 963, 'if413040', '21113040', 'Putra Setiawan Simaremare', 'if413040@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1304, 4126, 4836, 'ce320022', '13320022', 'Putra Toba Tampubolon', 'ce320022@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1305, 2744, 3243, 'if418024', '11418024', 'Putri Anastasya Lumbantoruan', 'if418024@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1306, 3739, 4413, 'ce319023', '13319023', 'Putri Anjelia Pasaribu', 'ce319023@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1307, 2068, 2468, 'if316022', '11316022', 'Putri Ayu Elisabeth Sihotang', 'if316022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1308, 4122, 4832, 'ce320018', '13320018', 'Putri Damayanti Sitinjak', 'ce320018@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1309, 986, 984, 'if313069', '11113069', 'Putri Elisabeth Sibuea', 'if313069@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1310, 1540, 1669, 'if315002', '11315002', 'Putri Indah Sari Matondang', 'if315002@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1311, 4354, 5064, 'if420068', '11420068', 'Putri Kesuma Indah Jawak', 'if420068@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1312, 4148, 4858, 'ce320044', '13320044', 'Putri Kezia Nababan', 'ce320044@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1313, 3710, 4384, 'if319051', '11319051', 'Putri Octavia Sitompul', 'if319051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1314, 4463, 5201, 'if322007', '11322007', 'Putri Tamara Gultom', 'if322007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1315, 4453, 5188, 'if321072', '11321072', 'Putri Wita Marito. N', 'if321072@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1316, 584, 583, 'if09030', '11109030', 'Putri Yoceline', 'if09030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1317, 404, 404, 'if07073', '11107073', 'Qwantes Soaduon Simatupang', 'if07073@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1318, 2671, 3168, 'if318060', '11318060', 'Rachel Gultom', 'if318060@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1319, 3742, 4416, 'ce319026', '13319026', 'Radema Panjaitan', 'ce319026@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1320, 4022, 4732, 'if320026', '11320026', 'Rafael Steven Alexander Munson Sihombing', 'if320026@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1321, 2619, 3116, 'if318016', '11318016', 'Rafika Tampubolon', 'if318016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1322, 4438, 5173, 'if321057', '11321057', 'Rahel Amelia Vega Sianipar', 'if321057@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1323, 1740, 1882, 'if415032', '11415032', 'Rahel Melinda Purba', 'if415032@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1324, 3682, 4356, 'if319023', '11319023', 'Rahul Stepen Sinaga', 'if319023@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1325, 2229, 2695, 'if317015', '11317015', 'Raissa Miranda Anastasya Situmorang', 'if317015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1326, 526, 525, 'if09051', '11109051', 'Raja Yudha Pratama Sihombing', 'if09051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1327, 189, 190, 'if03009', '11103009', 'Rakhelina Christiani Siagian', 'if03009@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1328, 2709, 3207, 'ce318047', '13318047', 'Raldo Kelvin Sihombing', 'ce318047@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1329, 836, 834, 'if312061', '11112061', 'Ramperto Parlin Pasaribu', 'if312061@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1330, 167, 168, 'if03013', '11103013', 'Ramses Freddy Rajagukguk', 'if03013@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1331, 126, 127, 'if01034', '11101034', 'Ramses Hutahaean', 'if01034@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1332, 1083, 1081, 'if314027', '11314027', 'Randy Saputra Purba', 'if314027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1333, 468, 468, 'if08055', '11108055', 'Rasdiana Sari', 'if08055@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1334, 2674, 3171, 'if318063', '11318063', 'Ratu Aryella Johana', 'if318063@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1335, 4322, 5032, 'if420036', '11420036', 'Raymond G. Saor Simamora', 'if420036@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1336, 1029, 1027, 'if313085', '11113085', 'Raymond Sitepu', 'if313085@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1337, 4405, 5140, 'if321024', '11321024', 'Refina Marpaung', 'if321024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1338, 1134, 1132, 'ce314017', '13314017', 'Refni Melisa M. Sitompul', 'ce314017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1339, 1152, 1150, 'if414005', '11414005', 'Reka Marsaulina Panggabean', 'if414005@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1340, 857, 855, 'if312082', '11112082', 'Rendi Manuel Halason Sinaga', 'if312082@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1341, 128, 129, 'if01036', '11101036', 'Renold Siregar', 'if01036@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1342, 4450, 5185, 'if321069', '11321069', 'Renova Gultom', 'if321069@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1343, 2070, 2470, 'if316009', '11316009', 'Renta Yustika Damanik', 'if316009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1344, 4504, 5242, 'if322048', '11322048', 'RESA HALEN MANURUNG', 'if322048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1345, 961, 959, 'if313084', '11113084', 'Retno Tambunan', 'if313084@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1346, 3900, 4574, 'if419054', '11419054', 'Revi Angeli Siahaan', 'if419054@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1347, 2095, 2496, 'ce316004', '13316004', 'Rexy Pebe Sihombing', 'ce316004@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1348, 1077, 1075, 'if314021', '11314021', 'Reynaldo Andreas Parangin Angin', 'if314021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1349, 1175, 1173, 'if414028', '11414028', 'Reynaldo Leoricci Mikhael', 'if414028@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1350, 565, 564, 'if09011', '11109011', 'Rian Falam Simanjuntak', 'if09011@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1351, 4429, 5164, 'if321048', '11321048', 'Rian Shaputra Naibaho', 'if321048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1352, 753, 751, 'if11076', '11111076', 'Rianita Manik', 'if11076@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1353, 1126, 1124, 'ce314009', '13314009', 'Rianto Napitupulu', 'ce314009@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1354, 3697, 4371, 'if319038', '11319038', 'Ribka W N Tambunan', 'if319038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1355, 569, 568, 'if09015', '11109015', 'Rich Samuel Simamora', 'if09015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1356, 995, 993, 'if413044', '21113044', 'Richa Marchelina Purba', 'if413044@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1357, 1771, 1913, 'ce315028', '13315028', 'Richad Harianja', 'ce315028@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1358, 862, 860, 'if312089', '11112089', 'Richan Siallagan', 'if312089@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1359, 4466, 5204, 'if322010', '11322010', 'Richard Paulus Aritonang', 'if322010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1360, 246, 247, 'if05020', '11105020', 'RICHARD SINABARIBA', 'if05020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1361, 2630, 3127, 'if318032', '11318032', 'Richye Calvin Manik', 'if318032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1362, 1541, 1670, 'if315003', '11315003', 'Ricki Fernando Hutagaol', 'if315003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1363, 2636, 3133, 'if318026', '11318026', 'Ricky Alexander Lumban Tobing', 'if318026@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Keluar', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1364, 4028, 4738, 'if320032', '11320032', 'Ricky Ananda Pardomuan Sitorus', 'if320032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1365, 4417, 5152, 'if321036', '11321036', 'RICKY BILBAO SAMOSIR', 'if321036@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1366, 451, 451, 'if07031', '11107031', 'Ricky V. Charisma Brahmana', 'if07031@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1367, 1051, 1049, 'if413066', '21113066', 'Ricoh Richardo Nababan', 'if413066@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1368, 1128, 1126, 'ce314011', '13314011', 'Ridawati Justina Simanullang', 'ce314011@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1369, 1063, 1061, 'if314007', '11314007', 'Ridho Alfian H. Tampubolon', 'if314007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1370, 220, 221, 'if04027', '11104027', 'RIDWAN SIAGIAN', 'if04027@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1371, 1101, 1099, 'if314045', '11314045', 'Rifka Diana', 'if314045@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1372, 4134, 4844, 'ce320030', '13320030', 'Riki Yoga Situmorang', 'ce320030@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1373, 1155, 1153, 'if414008', '11414008', 'Rikky Salo', 'if414008@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1374, 279, 280, 'if05055', '11105055', 'RIMHOT PARULIAN  SITORUS', 'if05055@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1375, 2759, 3258, 'if418026', '11418026', 'Rinaldi Tua Karokaro', 'if418026@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1376, 2789, 3288, 'if418056', '11418056', 'Rince Septriana Parhusip', 'if418056@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1377, 548, 547, 'if09075', '11109075', 'Rindang Hartana Silaen', 'if09075@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1378, 1122, 1120, 'ce314005', '13314005', 'Rindella', 'ce314005@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1379, 1782, 1971, 'if415041', '11415041', 'Rini Juliana Sipahutar', 'if415041@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1380, 4339, 5049, 'if420053', '11420053', 'RINI MEYCIA PANJAITAN', 'if420053@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1381, 1713, 1855, 'if415004', '11415004', 'Rinto Daud Tambunan', 'if415004@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Meninggal', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1382, 129, 130, 'if01037', '11101037', 'Rinto Harianja', 'if01037@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1383, 1757, 1899, 'ce315015', '13315015', 'Rio Lambert Malau', 'ce315015@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1384, 4142, 4852, 'ce320038', '13320038', 'Rio Putrawan Zalukhu', 'ce320038@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1385, 130, 131, 'if01038', '11101038', 'Rionardo A. Simanjuntak', 'if01038@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1386, 260, 261, 'if05035', '11105035', 'RIPKA FERIANI GINTING', 'if05035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1387, 546, 545, 'if09056', '11109056', 'Ririn Afrianne Napitupulu', 'if09056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1388, 3868, 4542, 'if419022', '11419022', 'Riris Lasmarito Malau', 'if419022@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1389, 758, 756, 'if11094', '11111094', 'Riris Manik', 'if11094@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1390, 2785, 3284, 'if418044', '11418044', 'Risa Jessica Sitohang', 'if418044@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1391, 2249, 2715, 'if317064', '11317064', 'Risdo Marisi Tesalonika Hutasoit', 'if317064@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1392, 4026, 4736, 'if320030', '11320030', 'Risky Saputra Siahaan', 'if320030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1393, 2728, 3226, 'ce318053', '13318053', 'Risna Elisa Sihaloho', 'ce318053@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1394, 4499, 5237, 'if322043', '11322043', 'RISNA FEBRIYANTI SIRINGORINGO', 'if322043@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1395, 1154, 1152, 'if414007', '11414007', 'Rita Asima Manurung', 'if414007@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1396, 1024, 1022, 'if313089', '11113089', 'Ritcan Maruli Pandapotan Hutahaean', 'if313089@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1397, 4471, 5209, 'if322015', '11322015', 'Rivael Hasiholan Manurung', 'if322015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1398, 3724, 4398, 'ce319008', '13319008', 'Rivaldo Silalahi', 'ce319008@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1399, 4319, 5029, 'if420033', '11420033', 'Rizki Okto S', 'if420033@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1400, 1712, 1854, 'if415003', '11415003', 'Rizky Manurung', 'if415003@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1401, 2218, 2684, 'if317050', '11317050', 'Rizky Martin Sianturi', 'if317050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1402, 2682, 3179, 'ce318019', '13318019', 'Rizky Romuel Sitorus', 'ce318019@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1403, 916, 914, 'if413025', '21113025', 'Roberto Tambunan', 'if413025@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1404, 1597, 1738, 'if315021', '11315021', 'Rocto Bertonius Hamonangan Sidauruk', 'if315021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11');
INSERT INTO `mahasiswa` (`id`, `dim_id`, `user_id`, `user_name`, `nim`, `nama`, `email`, `prodi_id`, `prodi_name`, `fakultas`, `angkatan`, `nomor_telepon`, `status`, `asrama`, `created_at`, `updated_at`) VALUES
(1405, 1172, 1170, 'if414025', '11414025', 'Rodes Pria Yuter Sirait', 'if414025@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1406, 3988, 4696, 'if420017', '11420017', 'Rody Towolom', 'if420017@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1407, 3991, 4699, 'if420020', '11420020', 'Rohkid Kogoya', 'if420020@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1408, 907, 905, 'if313103', '11113103', 'Roky Sarasi Manalu', 'if313103@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1409, 3728, 4402, 'ce319012', '13319012', 'Rolando Artha Napitupulu', 'ce319012@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1410, 872, 870, 'if312099', '11112099', 'Rolastri Sitanggang', 'if312099@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1411, 3736, 4410, 'ce319020', '13319020', 'Roma Asi Simamora', 'ce319020@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1412, 1046, 1044, 'if413064', '21113064', 'Romasi Sunrise Silaban', 'if413064@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1413, 163, 164, 'if03044', '11103044', 'Romauli Butarbutar', 'if03044@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1414, 2247, 2713, 'if317065', '11317065', 'Romauli Feronica Siregar', 'if317065@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1415, 3703, 4377, 'if319044', '11319044', 'Romauli Sianipar', 'if319044@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1416, 1783, 1972, 'if415042', '11415042', 'Rominda Gurning', 'if415042@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1417, 1064, 1062, 'if314008', '11314008', 'Romita Sinurat', 'if314008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1418, 409, 409, 'if07084', '11107084', 'Ronal Daniel Panjaitan', 'if07084@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1419, 88, 89, 'if02094', '11102094', 'Ronald Gerhan Silitonga', 'if02094@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1420, 2198, 2664, 'if317058', '11317058', 'Ronaldo Sitanggang', 'if317058@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1421, 2199, 2665, 'if317004', '11317004', 'Ronatiur Febriani Lumbangaol', 'if317004@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1422, 910, 908, 'if313020', '11113020', 'Rony David Ferdinand S.', 'if313020@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1423, 478, 478, 'if08069', '11108069', 'Rony Pasca Simamora', 'if08069@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1424, 1745, 1887, 'ce315002', '13315002', 'Ropelita Sihombing', 'ce315002@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1425, 4446, 5181, 'if321065', '11321065', 'ROSA LINDA BR MANIK', 'if321065@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1426, 4346, 5056, 'if420060', '11420060', 'Rosa Stefani Sinaga', 'if420060@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1427, 4336, 5046, 'if420050', '11420050', 'Rosani Elysa Sitinjak', 'if420050@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1428, 1088, 1086, 'if314032', '11314032', 'Rosdiana Sitinjak', 'if314032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1429, 2672, 3169, 'if318061', '11318061', 'Rosida Octavia Sitorus', 'if318061@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1430, 2161, 2597, 'if316044', '11316044', 'Rosita V. Wonda', 'if316044@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1431, 132, 133, 'if01040', '11101040', 'Roslemi Sihotang', 'if01040@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1432, 315, 316, 'if06009', '11106009', 'Roslin Situmorang', 'if06009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1433, 59, 60, 'if02059', '11102059', 'Rossa Dame Hasian Sarumaha', 'if02059@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1434, 2772, 3271, 'if418011', '11418011', 'Rotua Gita Simanjuntak', 'if418011@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', 'Pniel', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1435, 1023, 1021, 'if413052', '21113052', 'Rotua Pasaribu', 'if413052@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1436, 2708, 3206, 'ce318044', '13318044', 'Roy Blesson Siboro', 'ce318044@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1437, 30, 31, 'if02030', '11102030', 'Roy Deddy Hasiholan Lumban Tobing', 'if02030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1438, 535, 534, 'if09050', '11109050', 'Roy Eska Marpaung', 'if09050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1439, 542, 541, 'if09062', '11109062', 'Roy Inganta Ginting', 'if09062@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1440, 2073, 2473, 'if316012', '11316012', 'Roy Junedi Simamora', 'if316012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1441, 397, 397, 'if07060', '11107060', 'Roy Tambunan', 'if07060@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1442, 2262, 2728, 'ce317013', '13317013', 'Royda Venny Sitorus', 'ce317013@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1443, 154, 155, 'if03088', '11103088', 'Ruben Christof Teddy Hasiholan Ompusunggu', 'if03088@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1444, 1030, 1028, 'if313107', '11113107', 'Ruben Ferry Christian Panjaitan', 'if313107@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1445, 2222, 2688, 'if317066', '11317066', 'Ruben Manurung', 'if317066@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1446, 2706, 3204, 'ce318036', '13318036', 'Ruben Mual Siregar', 'ce318036@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1447, 135, 136, 'if01043', '11101043', 'Rudi R. H. Hutasoit', 'if01043@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1448, 710, 708, 'if11022', '11111022', 'Rudy Samuel Pardosi', 'if11022@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1449, 743, 741, 'if11066', '11111066', 'Ruhut Adventri Tambunan', 'if11066@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1450, 651, 649, 'if10061', '11110061', 'Rumi Lisa Riani Tambunan', 'if10061@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1451, 716, 714, 'if11057', '11111057', 'Ruminta Astri Agustini Manurung', 'if11057@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1452, 1097, 1095, 'if314041', '11314041', 'Rumiris M. Hutagalung', 'if314041@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1453, 749, 747, 'if11072', '11111072', 'Rusman Febry Hermanto Marpaung', 'if11072@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1454, 4039, 4749, 'if320043', '11320043', 'Rut Ferwati Lumbantoruan', 'if320043@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1455, 3718, 4392, 'ce319002', '13319002', 'Ruth Angel Norisma Pasaribu', 'ce319002@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1456, 2277, 2743, 'ce317015', '13317015', 'Ruth Cindy F. Panjaitan', 'ce317015@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1457, 2309, 2775, 'if417023', '11417023', 'Ruth Elvin Harianja', 'if417023@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1458, 752, 750, 'if11075', '11111075', 'Ruth Marlina Hutabarat', 'if11075@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1459, 1785, 1974, 'if415044', '11415044', 'Ruth Marlina Hutabarat', 'if415044@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1460, 1034, 1032, 'if313092', '11113092', 'Ruth Nolytha Putri Sarti Pangaribuan', 'if313092@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1461, 1004, 1002, 'if313055', '11113055', 'Ruth Tabita Hutahaean', 'if313055@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1462, 648, 646, 'if10064', '11110064', 'Ruth Viodetta', 'if10064@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1463, 2677, 3174, 'ce318017', '13318017', 'Sabam Sianturi', 'ce318017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1464, 4488, 5226, 'if322032', '11322032', 'SABAR MARTUA TAMBA', 'if322032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1465, 403, 403, 'if07072', '11107072', 'Sabar Maruba Tampubolon', 'if07072@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1466, 261, 262, 'if05036', '11105036', 'SABAR SAPUTRA  SIMANJUNTAK', 'if05036@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1467, 633, 631, 'if10006', '11110006', 'Sahat Manahan Sinaga', 'if10006@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1468, 675, 673, 'if11006', '11111006', 'Sahat Maruhum Gultom', 'if11006@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1469, 3856, 4530, 'if419010', '11419010', 'Sahat Parulian Hutauruk', 'if419010@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1470, 175, 176, 'if03087', '11103087', 'Sahat Perdana Silitonga', 'if03087@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1471, 83, 84, 'if02088', '11102088', 'Sahat S. Silalahi', 'if02088@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1472, 136, 137, 'if01044', '11101044', 'Sahatma G.H.T. Siallagan', 'if01044@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1473, 4440, 5175, 'if321059', '11321059', 'Saimarito Simanullang', 'if321059@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1474, 2149, 2553, 'if416029', '11416029', 'Salem Febi Dominggo Hutauruk', 'if416029@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1475, 147, 148, 'if03032', '11103032', 'Samson A.  E. Sinaga', 'if03032@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1476, 4016, 4726, 'if320020', '11320020', 'SAMTO ELI SIMAMORA', 'if320020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1477, 4494, 5232, 'if322038', '11322038', 'Samuel Albi Pulo Sibarani', 'if322038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1478, 2629, 3126, 'if318005', '11318005', 'Samuel Alfredy Ambarita', 'if318005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1479, 953, 951, 'if413030', '21113030', 'Samuel Christian Silalahi', 'if413030@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1480, 3887, 4561, 'if419041', '11419041', 'Samuel Halomoan Manalu', 'if419041@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1481, 4418, 5153, 'if321037', '11321037', 'Samuel Jefri Saputra Siahaan', 'if321037@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1482, 744, 742, 'if11067', '11111067', 'Samuel Marpaung', 'if11067@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1483, 4123, 4833, 'ce320019', '13320019', 'Samuel Natanael Patuan Sianipar', 'ce320019@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1484, 3689, 4363, 'if319030', '11319030', 'Samuel Panuturi Silaban', 'if319030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1485, 4036, 4746, 'if320040', '11320040', 'SAMUEL PARULIAN SIMAMORA', 'if320040@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1486, 4315, 5025, 'if420029', '11420029', 'SAMUEL PRAYOGA TAMPUBOLON', 'if420029@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1487, 2286, 2752, 'if417025', '11417025', 'Samuel Sanjaya Siahaan', 'if417025@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1488, 4383, 5118, 'if321002', '11321002', 'Samuel Sibuea', 'if321002@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1489, 928, 926, 'if413017', '21113017', 'Samuel Stefanus Napitupulu', 'if413017@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1490, 244, 245, 'if05018', '11105018', 'SAMUEL TAMBUNAN', 'if05018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1491, 4332, 5042, 'if420046', '11420046', 'samuel toga maruli sihombing', 'if420046@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1492, 4324, 5034, 'if420038', '11420038', 'Samuel W.L Simanjuntak', 'if420038@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1493, 4419, 5154, 'if321038', '11321038', 'Sandro Pangihutan Panjaitan', 'if321038@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1494, 2287, 2753, 'if417031', '11417031', 'Sandy Manutur Sihotang', 'if417031@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1495, 1148, 1146, 'if414001', '11414001', 'Sandy S. Ambarita', 'if414001@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1496, 3666, 4340, 'if319007', '11319007', 'Sandy Samuel Theophani Hutagalung', 'if319007@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1497, 374, 375, 'if06071', '11106071', 'Sanhenra Sinaga', 'if06071@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1498, 2681, 3178, 'ce318026', '13318026', 'Sanita Magdalena Simamora', 'ce318026@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1499, 1015, 1013, 'if313047', '11113047', 'Sanny Chiudra Simarmata', 'if313047@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1500, 2312, 2778, 'if417029', '11417029', 'Sanny Naomi Sinaga', 'if417029@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1501, 824, 822, 'if312048', '11112048', 'Sanny Permatasari Silalahi', 'if312048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1502, 4443, 5178, 'if321062', '11321062', 'Santa Bundaresha Sinaga', 'if321062@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1503, 735, 733, 'if11004', '11111004', 'Santi Hutapea', 'if11004@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1504, 769, 767, 'if412004', '21112004', 'Santi Oktavia Pangaribuan', 'if412004@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1505, 3873, 4547, 'if419027', '11419027', 'Santo Lamsar Harianja', 'if419027@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1506, 2670, 3167, 'if318058', '11318058', 'Sarah Christine Tampubolon', 'if318058@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1507, 4441, 5176, 'if321060', '11321060', 'Sarah Elfiana Tobing', 'if321060@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1508, 2263, 2729, 'ce317020', '13317020', 'Sarah Elvris Mawati Barasa', 'ce317020@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1509, 4507, 5245, 'if322051', '11322051', 'Sarah Meilani Butar Butar', 'if322051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1510, 3863, 4537, 'if419017', '11419017', 'Sarah Omega Yulie Simorangkir', 'if419017@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1511, 3853, 4527, 'if419007', '11419007', 'Sarah Susanty Olyvia Tampubolon', 'if419007@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1512, 2148, 2552, 'if416030', '11416030', 'Sarah Try Novelitha N', 'if416030@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1513, 2310, 2776, 'if417027', '11417027', 'Sarah Winarsih Simanjuntak', 'if417027@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1514, 4037, 4747, 'if320041', '11320041', 'Sardion Lubis', 'if320041@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1515, 1542, 1671, 'if315004', '11315004', 'SARI THREE MAYSSI SIAHAAN', 'if315004@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1516, 2666, 3163, 'if318040', '11318040', 'Sari Uli Ingrid Hutahaean', 'if318040@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1517, 1547, 1676, 'if315009', '11315009', 'Sariaman Situmorang', 'if315009@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1518, 871, 869, 'if412032', '21112032', 'Sartika Sari Hasibuan', 'if412032@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1519, 525, 524, 'if09057', '11109057', 'Sartika Simanami Sianipar', 'if09057@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1520, 4360, 5070, 'if420074', '11420074', 'Satrio holmes afrido situmorang', 'if420074@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1521, 137, 138, 'if01045', '11101045', 'Sauria Beatrix Napitupulu', 'if01045@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1522, 190, 191, 'if03005', '11103005', 'Saut Horas Ramses Samosir', 'if03005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1523, 2738, 3237, 'if418055', '11418055', 'Saut Raja Marihot Tua Sihotang', 'if418055@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1524, 4001, 4711, 'if320005', '11320005', 'Scintya Leony Geraldine Lumban Tobing', 'if320005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1525, 2081, 2481, 'if316020', '11316020', 'Sehat Maruli Tua Samosir', 'if316020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1526, 2077, 2477, 'if316016', '11316016', 'Selvia Pratiwi Situmorang', 'if316016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1527, 925, 923, 'if313010', '11113010', 'Sembiring Ivandra Oktovan', 'if313010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1528, 1043, 1041, 'if313097', '11113097', 'Septian Putra Siahaan', 'if313097@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1529, 4149, 4859, 'ce320045', '13320045', 'Septiany Princess Silalahi', 'ce320045@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1530, 850, 848, 'if312075', '11112075', 'Septika Sitorus', 'if312075@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1531, 3679, 4353, 'if319020', '11319020', 'Sharon C Siahaan', 'if319020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1532, 2117, 2519, 'ce316027', '13316027', 'Sharon Dion Simorangkir', 'ce316027@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1533, 2230, 2696, 'if317018', '11317018', 'Shintya Angelica Simatupang', 'if317018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1534, 4154, 4864, 'ce320050', '13320050', 'Shopia Magdalena Sibarani', 'ce320050@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1535, 343, 344, 'if06039', '11106039', 'Sihar Johansen Purba', 'if06039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1536, 219, 220, 'if04025', '11104025', 'SIHAR WILLIAM JUBILANT SIMBOLON', 'if04025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1537, 4475, 5213, 'if322019', '11322019', 'Silvi Agustina Sitohang', 'if322019@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1538, 935, 933, 'if413001', '21113001', 'Silvia Bernadetta Sinaga', 'if413001@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1539, 459, 459, 'if08043', '11108043', 'Silvia Novriwani Sitompul', 'if08043@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1540, 4365, 5075, 'if420079', '11420079', 'SILVIA SARI REZEKI LUBIS', 'if420079@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1541, 2703, 3201, 'ce318033', '13318033', 'Simon Jayama Sinaga', 'ce318033@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1542, 2616, 3113, 'if318039', '11318039', 'Simon Mangasi Hutajulu', 'if318039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1543, 2685, 3182, 'ce318043', '13318043', 'Simon Martua Manullang', 'ce318043@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Tunda Unri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1544, 4335, 5045, 'if420049', '11420049', 'Simon Natanael Siahaan', 'if420049@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1545, 902, 900, 'if413011', '21113011', 'Sinta Ida Patona Sianipar', 'if413011@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1546, 2614, 3111, 'if318010', '11318010', 'Sintong D.P. Lumbantobing', 'if318010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1547, 939, 937, 'if313018', '11113018', 'Sintong Panjaitan', 'if313018@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1548, 2675, 3172, 'ce318009', '13318009', 'Sintong Sahala Mardongan Siahaan', 'ce318009@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1549, 4408, 5143, 'if321027', '11321027', 'Sio Alexandra Siahaan', 'if321027@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1550, 864, 862, 'if312091', '11112091', 'Siska Adelina Damanik', 'if312091@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1551, 2716, 3214, 'ce318004', '13318004', 'Siska Vinia Sihite', 'ce318004@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1552, 4157, 4867, 'ce320053', '13320053', 'Siti Holijah Sitorus', 'ce320053@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1553, 856, 854, 'if312081', '11112081', 'Sitta Endah Pricilia Simanjuntak', 'if312081@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1554, 56, 57, 'if02056', '11102056', 'Slamat Archinius Partogi', 'if02056@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1555, 3716, 4390, 'if319057', '11319057', 'Sofhia Christie Tambun', 'if319057@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1556, 2292, 2758, 'if417010', '11417010', 'Sogumontar H Simangunsong', 'if417010@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1557, 2231, 2697, 'if317019', '11317019', 'Sondang Jelita Sipayung', 'if317019@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1558, 2075, 2475, 'if316014', '11316014', 'Sondang Sartika Siahaan', 'if316014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1559, 1767, 1909, 'ce315025', '13315025', 'Soni Pratama', 'ce315025@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1560, 2276, 2742, 'ce317010', '13317010', 'Sonia Karolina Lumbantoruan', 'ce317010@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1561, 4353, 5063, 'if420067', '11420067', 'Sonia Magdalena Pasaribu', 'if420067@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1562, 2145, 2549, 'if416026', '11416026', 'Sonny Immanuel Hutabarat', 'if416026@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1563, 3707, 4381, 'if319048', '11319048', 'Sonya Yanti Karunia Sipahutar', 'if319048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1564, 4044, 4754, 'if320048', '11320048', 'Sophia Lorentz Julinar Tambunan', 'if320048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1565, 2634, 3131, 'if318024', '11318024', 'Sopian Manurung', 'if318024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1566, 4368, 5078, 'if420082', '11420082', 'Sopianna Siagian', 'if420082@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1567, 3720, 4394, 'ce319004', '13319004', 'Sotar Dodo', 'ce319004@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1568, 4347, 5057, 'if420061', '11420061', 'Sri Hartati A. Panjaitan', 'if420061@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1569, 2743, 3242, 'if418023', '11418023', 'Sri Hartini Manurung', 'if418023@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1570, 858, 856, 'if312083', '11112083', 'Sriayu Manalu', 'if312083@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1571, 4370, 5080, 'if420084', '11420084', 'Srinesia Cecilia Sitorus', 'if420084@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1572, 4378, 5089, 'ce320055', '13320055', 'Steven Benjamin Nicholas Situmorang', 'ce320055@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1573, 774, 772, 'if412007', '21112007', 'Steven Jhonson Haposan Siahaan', 'if412007@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1574, 1769, 1911, 'ce315027', '13315027', 'Steven Sebastian Sitanggang', 'ce315027@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1575, 4424, 5159, 'if321043', '11321043', 'Suandika Napitupulu', 'if321043@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1576, 830, 828, 'if312055', '11112055', 'Suheri Manuturi Rajoki Marpaung', 'if312055@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1577, 838, 836, 'if312063', '11112063', 'Sukmawati T. N. Lumban Gaol', 'if312063@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1578, 583, 582, 'if09029', '11109029', 'Sumiati Hutagalung', 'if09029@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1579, 322, 323, 'if06016', '11106016', 'Sunardo Panjaitan', 'if06016@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1580, 2275, 2741, 'ce317009', '13317009', 'Sunarti Barasa', 'ce317009@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1581, 695, 693, 'if11047', '11111047', 'Sunday Yusuf', 'if11047@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1582, 4051, 4761, 'if320055', '11320055', 'Sunkrista Meyriana D Gultom', 'if320055@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1583, 829, 827, 'if312054', '11112054', 'Sunlike Siagian', 'if312054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1584, 42, 43, 'if02042', '11102042', 'Suranta Petrus Sinuraya', 'if02042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1585, 2787, 3286, 'if418048', '11418048', 'Surtina a pardede', 'if418048@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1586, 781, 779, 'if412014', '21112014', 'Surya Seven Y. Simangunsong', 'if412014@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1587, 2084, 2484, 'if316024', '11316024', 'Suryaningsih Sinaga', 'if316024@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1588, 3877, 4551, 'if419031', '11419031', 'Suryanto Ray S Panjaitan', 'if419031@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1589, 879, 877, 'if412029', '21112029', 'Susy Pangaribuan', 'if412029@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1590, 717, 715, 'if11058', '11111058', 'Suvander Siregar', 'if11058@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1591, 200, 201, 'if04007', '11104007', 'Swandi Pangaribuan', 'if04007@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2004, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1592, 1784, 1973, 'if415043', '11415043', 'Swandi Pangaribuan', 'if415043@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1593, 1786, 1975, 'if415045', '11415045', 'Swandi Pangaribuan', 'if415045@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Keluar', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1594, 2225, 2691, 'if317008', '11317008', 'Sweta Marito Hutauruk', 'if317008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1595, 1748, 1890, 'ce315013', '13315013', 'Swinsikya Sitohang', 'ce315013@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1596, 69, 70, 'if02071', '11102071', 'Syahrianto Hutagaol', 'if02071@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1597, 670, 668, 'if10042', '11110042', 'Syarif Alexsander Silalahi', 'if10042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1598, 1091, 1089, 'if314035', '11314035', 'Sylphany Novita Masdiana Sibarani', 'if314035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1599, 2204, 2670, 'if317025', '11317025', 'Sylvia Kornelina Sihombing', 'if317025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1600, 4343, 5053, 'if420057', '11420057', 'Tahnia Viona Hartanti', 'if420057@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1601, 2253, 2719, 'ce317019', '13317019', 'Talenta Mesianna Sitorus', 'ce317019@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1602, 2718, 3216, 'ce318011', '13318011', 'Talenta Sri Manurung', 'ce318011@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1603, 37, 38, 'if02037', '11102037', 'Tambatua Hamonangan Pasaribu', 'if02037@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1604, 2061, 2461, 'if316001', '11316001', 'Tami Thressa Tambunan', 'if316001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1605, 987, 985, 'if313057', '11113057', 'Tamy Julyanty Sihotang', 'if313057@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1606, 992, 990, 'if313066', '11113066', 'Tania Sulastri Munte', 'if313066@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1607, 2132, 2535, 'if416011', '11416011', 'Tanti Yeyen Sinaga', 'if416011@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1608, 3905, 4579, 'if419059', '11419059', 'Tantri Harianti Silaen', 'if419059@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1609, 1546, 1675, 'if315008', '11315008', 'Taruli Ester Gurning', 'if315008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1610, 2721, 3219, 'ce318022', '13318022', 'Tasya Mian Asina Panggabean', 'ce318022@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1611, 3738, 4412, 'ce319022', '13319022', 'Tata Risa Panjaitan', 'ce319022@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1612, 1160, 1158, 'if314062', '11314062', 'Taufan Vaisal Partomuan Silitonga', 'if414013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1613, 339, 340, 'if06035', '11106035', 'Teamsar Muliadi Panggabean', 'if06035@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1614, 3972, 4680, 'if420001', '11420001', 'Temiton Lambe', 'if420001@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1615, 3995, 4703, 'if420024', '11420024', 'Tepi Yikwa', 'if420024@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1616, 2625, 3122, 'if318048', '11318048', 'Teresha Jesika Tampubolon', 'if318048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1617, 4008, 4718, 'if320012', '11320012', 'Tessalonika Siahaan', 'if320012@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1618, 3059, 3560, 'if319060', '11319060', 'Tessalonika Sibarani', 'if319060@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1619, 4456, 5191, 'ptk1234', '12345', 'Test pertukaran pelajar', 'ptk@gmail.com', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1620, 4389, 5124, 'if321008', '11321008', 'TESYA NOPIANA SIAHAAN', 'if321008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1621, 4136, 4846, 'ce320032', '13320032', 'THEO SABRIEN PURBA', 'ce320032@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1622, 4433, 5168, 'if321052', '11321052', 'Theofil Oktavia Nainggolan', 'if321052@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1623, 785, 783, 'if412019', '21112019', 'Theophanie Oktrianti A. L. Solin', 'if412019@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1624, 3740, 4414, 'ce319024', '13319024', 'Theresia Devina Rumahorbo', 'ce319024@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1625, 1118, 1116, 'ce314001', '13314001', 'Theresia Febrianti Mahdalena Butarbutar', 'ce314001@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1626, 4406, 5141, 'if321025', '11321025', 'Theresia Herlita Sinaga', 'if321025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1627, 1078, 1076, 'if314022', '11314022', 'Theresia Yoslin Tambunan', 'if314022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1628, 4372, 5082, 'if420086', '11420086', 'Theresya Gurning', 'if420086@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1629, 657, 655, 'if10055', '11110055', 'Thomson Marolop Sitohang', 'if10055@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1630, 719, 717, 'if11060', '11111060', 'Thomson Palito Napitupulu', 'if11060@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1631, 3881, 4555, 'if419035', '11419035', 'Thumphak Adhitio Aritonang', 'if419035@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1632, 941, 939, 'if413024', '21113024', 'Tia Elyani', 'if413024@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1633, 1138, 1136, 'ce314021', '13314021', 'Tia Monica Silalahi', 'ce314021@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1634, 3719, 3754, 'ce319003', '13319003', 'Tiarma Elfrida Gurning', 'ce319003@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1635, 2659, 3156, 'if318017', '11318017', 'Tiarro Elprida Tamba', 'if318017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1636, 3977, 4685, 'if420006', '11420006', 'Timiron Kogoya', 'if420006@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1637, 3899, 4573, 'if419053', '11419053', 'Timothy', 'if419053@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11');
INSERT INTO `mahasiswa` (`id`, `dim_id`, `user_id`, `user_name`, `nim`, `nama`, `email`, `prodi_id`, `prodi_name`, `fakultas`, `angkatan`, `nomor_telepon`, `status`, `asrama`, `created_at`, `updated_at`) VALUES
(1638, 3874, 4548, 'if419028', '11419028', 'Timothy J F Henan', 'if419028@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1639, 2099, 2501, 'ce316009', '13316009', 'Tio Pilus Lumbantobing', 'ce316009@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1640, 367, 368, 'if06063', '11106063', 'Tiopan Rangkuti', 'if06063@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1641, 4313, 5023, 'if420027', '11420027', 'Tito Simatupang', 'if420027@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1642, 2615, 3112, 'if318023', '11318023', 'Titus Boraspati Hatigoran Siagian', 'if318023@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1643, 664, 662, 'if10048', '11110048', 'Titus Nainggolan', 'if10048@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2010, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1644, 794, 792, 'if312010', '11112010', 'Titus Sihotang', 'if312010@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1645, 442, 442, 'if07022', '11107022', 'Toga Bonar Gultom', 'if07022@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1646, 691, 689, 'if11043', '11111043', 'Togar lamhot Rointra Butarbutar', 'if11043@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1647, 10, 11, 'if02010', '11102010', 'Togi Josua Hutapea', 'if02010@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2002, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1648, 345, 346, 'if06041', '11106041', 'Togu Muara Sianturi', 'if06041@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1649, 423, 423, 'if07102', '11107102', 'TOGU NOVRIANSYAH  TURNIP', 'if07102@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1650, 2170, 2606, 'if316050', '11316050', 'Toiles Weya', 'if316050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1651, 2177, 2613, 'if316043', '11316043', 'Toli Yikwa', 'if316043@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1652, 463, 463, 'if08048', '11108048', 'Toman Irfan', 'if08048@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1653, 903, 901, 'if313007', '11113007', 'Tommy Pernandez Munte', 'if313007@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1654, 489, 489, 'if08010', '11108010', 'Toyo Nurani Silitonga', 'if08010@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1655, 810, 808, 'if312032', '11112032', 'Tri Arta Simanjuntak', 'if312032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1656, 508, 508, 'if08031', '11108031', 'Tri Artha Uli Siringoringo', 'if08031@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1657, 4364, 5074, 'if420078', '11420078', 'Tria Jessica Tampubolon', 'if420078@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1658, 1544, 1673, 'if315006', '11315006', 'Triana C Baringbing', 'if315006@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1659, 2689, 3186, 'ce318010', '13318010', 'Trifani Febrina Hasibuan', 'ce318010@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1660, 1027, 1025, 'if413060', '21113060', 'Trima Wahyuni Manurung', 'if413060@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1661, 4505, 5243, 'if322049', '11322049', 'Trinita Situmorang', 'if322049@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1662, 4326, 5036, 'if420040', '11420040', 'Trisatya Elisa Mintar Manurung', 'if420040@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1663, 3997, 4707, 'if320001', '11320001', 'Trito Exaudi Manik', 'if320001@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1664, 2660, 3157, 'if318021', '11318021', 'Tryda Aurani Sijabat', 'if318021@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1665, 2069, 2469, 'if316008', '11316008', 'Tulus Aldrian Siregar', 'if316008@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1666, 823, 821, 'if312047', '11112047', 'Tulus Anreanto Lumbantobing', 'if312047@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1667, 511, 511, 'if08036', '11108036', 'Tulus Pardamean Simanjuntak', 'if08036@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1668, 2119, 2521, 'ce316030', '13316030', 'Tumbur Ricky Simarmata', 'ce316030@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1669, 2710, 3208, 'ce318048', '13318048', 'Tumbur Rumapea', 'ce318048@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1670, 453, 453, 'if07033', '11107033', 'Tumpal Pernando Simanjuntak', 'if07033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1671, 1061, 1059, 'if314005', '11314005', 'Tumpal Tambunan', 'if314005@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1672, 1002, 1000, 'if313045', '11113045', 'Uci Arahito Lubis', 'if313045@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1673, 1129, 1127, 'ce314012', '13314012', 'Ucok Roles Situmorang', 'ce314012@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1674, 499, 499, 'if08022', '11108022', 'Ucok Sugianto Maradona Tambunan', 'if08022@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1675, 985, 983, 'if313082', '11113082', 'Ulina T. Simanjuntak', 'if313082@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1676, 807, 805, 'if312028', '11112028', 'Ulva Helena Sianturi', 'if312028@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1677, 180, 181, 'if03054', '11103054', 'Vadila Winanda', 'if03054@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2003, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1678, 1048, 1046, 'if413068', '21113068', 'Valentina Siregar', 'if413068@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1679, 2731, 3230, 'if418007', '11418007', 'Valentine Trihandayani', 'if418007@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1680, 3734, 4408, 'ce319018', '13319018', 'Valentino Ibrahim', 'ce319018@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1681, 980, 978, 'if313060', '11113060', 'Valentino Sihombing', 'if313060@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1682, 557, 556, 'if09002', '11109002', 'Vanderwyk Muba Henry Siahaan', 'if09002@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1683, 4516, 5254, 'if322060', '11322060', 'Vanessa Siahaan', 'if322060@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2022, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1684, 876, 874, 'if312103', '11112103', 'Vanjul Christian Hutajulu', 'if312103@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1685, 1601, 1742, 'if315025', '11315025', 'Vedtra Crisnanda Purba', 'if315025@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1686, 854, 852, 'if312079', '11112079', 'Veni Feronika Manurung', 'if312079@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1687, 960, 958, 'if313061', '11113061', 'Veni Vici Raya N.', 'if313061@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1688, 4055, 4765, 'if320059', '11320059', 'Veny Siahaan', 'if320059@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1689, 2719, 3217, 'ce318012', '13318012', 'Verayani Fronika Simbolon', 'ce318012@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1690, 899, 897, 'if313005', '11113005', 'Veronica G. R. Napitupulu', 'if313005@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1691, 1021, 1019, 'if413042', '21113042', 'Veronika H. Purba', 'if413042@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1692, 2627, 3124, 'if318052', '11318052', 'Veronika Oktafia Marpaung', 'if318052@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1693, 2784, 3283, 'if418043', '11418043', 'Vetra Febriyanti Tampubolon', 'if418043@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1694, 471, 471, 'if08058', '11108058', 'Vicca Patricia', 'if08058@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1695, 2193, 2659, 'if317016', '11317016', 'Vicki Frendika Manurung', 'if317016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1696, 3687, 4361, 'if319028', '11319028', 'VICKTOR LAMBOK DESRONY', 'if319028@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1697, 470, 470, 'if08057', '11108057', 'Victor Oloan Alexander Doloksaribu', 'if08057@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1698, 388, 388, 'if07038', '11107038', 'Vidi Okky Immanuel Hutagaol', 'if07038@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1699, 1093, 1091, 'if314037', '11314037', 'Viko Andri Bastian Manurung', 'if314037@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1700, 1607, 1748, 'if315030', '11315030', 'Violinna Hutagalung', 'if315030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1701, 1102, 1100, 'if314046', '11314046', 'Virnanda Cindya Simanjuntak', 'if314046@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1702, 3663, 4337, 'if319004', '11319004', 'Visgha Olivia Sipayung', 'if319004@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1703, 4054, 4764, 'if320058', '11320058', 'Vivi Nessa Tampubolon', 'if320058@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1704, 429, 429, 'if07003', '11107003', 'Vivi Yolanda Pakpahan', 'if07003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2007, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1705, 2749, 3248, 'if418029', '11418029', 'Wahyu Bintang Marsilam Sitepu', 'if418029@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1706, 353, 354, 'if06049', '11106049', 'Walanstar Alimcan  Sitorus', 'if06049@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2006, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1707, 867, 865, 'if312094', '11112094', 'Wati Purnamasari Siahaan', 'if312094@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1708, 2157, 2593, 'if316051', '11316051', 'Wekiles Wenda', 'if316051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1709, 2171, 2607, 'if316040', '11316040', 'Welince Gurik', 'if316040@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1710, 266, 267, 'if05041', '11105041', 'WELLY  MADYA PUTRA TAMBUNAN', 'if05041@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1711, 1123, 1121, 'ce314006', '13314006', 'Welly Mikha Simanjuntak', 'ce314006@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1712, 3987, 4695, 'if420016', '11420016', 'Wemiles Yikwa', 'if420016@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1713, 2265, 2731, 'ce317002', '13317002', 'Wendi Martin Situmeang', 'ce317002@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1714, 2191, 2657, 'if317045', '11317045', 'Wenny Adinda Siagian', 'if317045@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1715, 4011, 4721, 'if320015', '11320015', 'WENY ARI SINARNI PURBA', 'if320015@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1716, 814, 812, 'if312036', '11112036', 'Wesly A. Simanjuntak', 'if312036@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1717, 306, 307, 'if05090', '11105090', 'Whidia Kristina Siahaan', 'if05090@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1718, 861, 859, 'if312088', '11112088', 'Widya Brigita Sihombing', 'if312088@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1719, 843, 841, 'if312068', '11112068', 'Widyaningsih Limbong', 'if312068@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1720, 2209, 2675, 'if317003', '11317003', 'William Suarez Lumbantobing', 'if317003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1721, 2673, 3170, 'if318064', '11318064', 'Winanda Sisilia Sinaga', 'if318064@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1722, 926, 924, 'if313108', '11113108', 'Winda Dumaria Simanjuntak', 'if313108@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1723, 2669, 3166, 'if318051', '11318051', 'Winda Lorenza Sinurat', 'if318051@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1724, 2656, 3153, 'if318011', '11318011', 'Winda Mariana Pasaribu', 'if318011@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1725, 1716, 1858, 'if415007', '11415007', 'Winda Natalia Sianipar', 'if415007@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1726, 799, 797, 'if312020', '11112020', 'Windiany Lestari Sitorus', 'if312020@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1727, 721, 719, 'if11062', '11111062', 'Wironi Andromeda Rajagukguk', 'if11062@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1728, 2278, 2744, 'ce317017', '13317017', 'Wisdha E Panjaitan', 'ce317017@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1729, 4156, 4866, 'ce320052', '13320052', 'WITASARAH SITINJAK', 'ce320052@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1730, 912, 910, 'if313023', '11113023', 'Witri Zakia Manurung', 'if313023@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1731, 2612, 3109, 'if318057', '11318057', 'Wiwin Putri Gulo', 'if318057@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1732, 4327, 5037, 'if420041', '11420041', 'Wordyka Yehezkiel Nainggolan', 'if420041@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1733, 272, 273, 'if05047', '11105047', 'YANNY BERLIANA SIMAMORA', 'if05047@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1734, 4010, 4720, 'if320014', '11320014', 'YANTI SOPIA RUTH HUTASOIT', 'if320014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1735, 3675, 4349, 'if319016', '11319016', 'Yedija Epipanya M S', 'if319016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1736, 4018, 4728, 'if320022', '11320022', 'YEHEZKHIEL G.P SIBARANI', 'if320022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1737, 3992, 4700, 'if420021', '11420021', 'Yekies Kogoya', 'if420021@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1738, 4153, 4863, 'ce320049', '13320049', 'Yemima Sri Rezeki Damanik', 'ce320049@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1739, 2094, 2494, 'ce316002', '13316002', 'Yenni Juliana Sibarani', 'ce316002@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2016, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1740, 2752, 3251, 'if418004', '11418004', 'Yepta Zagarino Samosir', 'if418004@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1741, 2725, 3223, 'ce318040', '13318040', 'Yeremia W. Tambunan', 'ce318040@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1742, 1087, 1085, 'if314031', '11314031', 'Yesi Relita Butar Butar', 'if314031@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1743, 2786, 3285, 'if418049', '11418049', 'Yesiska Romauli Gultom', 'if418049@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1744, 573, 572, 'if09019', '11109019', 'Yessi Febrina Sinaga', 'if09019@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2009, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1745, 1729, 1871, 'if415019', '11415019', 'Yessica M Sitorus', 'if415019@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1746, 746, 744, 'if11069', '11111069', 'Yessika Floriana Manalu', 'if11069@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1747, 2169, 2605, 'if316055', '11316055', 'Yetam Tabo', 'if316055@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2016, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1748, 685, 683, 'if11037', '11111037', 'Yoan Christian Situmeang', 'if11037@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1749, 4426, 5161, 'if321045', '11321045', 'Yoas Sahat Marulitua Hutapea', 'if321045@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1750, 860, 858, 'if312087', '11112087', 'Yoel Rolas Simanjuntak', 'if312087@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1751, 2639, 3136, 'if318033', '11318033', 'Yogi Septian Lubis', 'if318033@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1752, 1165, 1163, 'if414018', '11414018', 'Yohana Ade Inriani Situmorang', 'if414018@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1753, 936, 934, 'if413022', '21113022', 'Yohana Adelina Pasaribu', 'if413022@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1754, 2288, 2754, 'if417014', '11417014', 'Yohana Christina Manullang', 'if417014@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1755, 3706, 4380, 'if319047', '11319047', 'Yohana E. Simanungkalit', 'if319047@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1756, 930, 928, 'if413077', '21113077', 'Yohana Gultom', 'if413077@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1757, 1164, 1162, 'if414017', '11414017', 'Yohana Hutahaean', 'if414017@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1758, 2228, 2694, 'if317014', '11317014', 'Yohana Purba', 'if317014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2017, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1759, 2304, 2770, 'if417007', '11417007', 'Yohana Rosa Amelia B', 'if417007@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1760, 3901, 4575, 'if419055', '11419055', 'Yohana Sihombing', 'if419055@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1761, 4434, 5169, 'if321053', '11321053', 'Yohana Tambunan', 'if321053@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1762, 921, 919, 'if413019', '21113019', 'Yohanes Marthin Hutabarat', 'if413019@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1763, 733, 731, 'if11089', '11111089', 'Yohanes Polin Bakara', 'if11089@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1764, 1592, 1733, 'if315016', '11315016', 'Yohannes C. Silalahi', 'if315016@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1765, 868, 866, 'if312095', '11112095', 'Yohannes Sakti Panggabean', 'if312095@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1766, 2658, 3155, 'if318013', '11318013', 'Yolan Puspa Sari Sihombing', 'if318013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1767, 1710, 1852, 'if415001', '11415001', 'Yolanda H.', 'if415001@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1768, 2714, 3212, 'ce318001', '13318001', 'Yolanda Magdalena', 'ce318001@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1769, 3691, 4365, 'if319032', '11319032', 'Yolanda Manurung', 'if319032@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1770, 3981, 4689, 'if420010', '11420010', 'Yomiton Wanimbo', 'if420010@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1771, 976, 974, 'if413039', '21113039', 'Yona Yunisefin Esdaria Lubis', 'if413039@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1772, 3884, 4558, 'if419038', '11419038', 'Yonatan Andreas Parsaoran Lumban Tobing', 'if419038@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1773, 1730, 1872, 'if415020', '11415020', 'Yonatan Vikario Resha Parapat', 'if415020@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1774, 4395, 5130, 'if321014', '11321014', 'Yosafat Hazael Tambun', 'if321014@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1775, 2648, 3145, 'if318059', '11318059', 'Yose Fernando Simamora', 'if318059@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1776, 1124, 1122, 'ce314007', '13314007', 'Yosep Ermanto Simanjuntak', 'ce314007@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1777, 4000, 4710, 'if320004', '11320004', 'Yoseph Domininggus Estomihi H. Naibaho', 'if320004@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2020, NULL, 'Mengundurkan Diri', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1778, 4447, 5182, 'if321066', '11321066', 'YOSEPHINE HERLINA MEGAWATI SIBURIAN', 'if321066@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1779, 4398, 5133, 'if321017', '11321017', 'Yoseplin Anggunsari Hutauruk', 'if321017@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1780, 2649, 3146, 'if318066', '11318066', 'Yosepri Disyandro Berutu', 'if318066@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2018, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1781, 819, 817, 'if312042', '11112042', 'Yoseva Maya Sitorus', 'if312042@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2012, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1782, 518, 518, 'if08053', '11108053', 'Yosevan Andrianto Sinaga', 'if08053@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2008, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1783, 4403, 5138, 'if321022', '11321022', 'Yosevyn Reginae Sipahutar', 'if321022@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1784, 4421, 5156, 'if321040', '11321040', 'Yosua Christian Sitanggang', 'if321040@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1785, 1550, 1679, 'if315013', '11315013', 'Yosua Jan Tuahman Sirait', 'if315013@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2015, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1786, 1163, 1161, 'if414016', '11414016', 'Yosua Noptria Saragih', 'if414016@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1787, 4425, 5160, 'if321044', '11321044', 'YUDHI DIKY GOKLAS PURBA', 'if321044@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2021, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1788, 3979, 4687, 'if420008', '11420008', 'Yudi Tabo', 'if420008@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1789, 3985, 4693, 'if420014', '11420014', 'Yuiron Wanimbo', 'if420014@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1790, 962, 960, 'if413033', '21113033', 'Yuli Arantxa Vicario Sinulingga', 'if413033@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1791, 3974, 4682, 'if420003', '11420003', 'Yuli Kogoya', 'if420003@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1792, 2306, 2772, 'if417009', '11417009', 'Yulianti Masta Rotua Simatupang', 'if417009@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2017, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1793, 1057, 1055, 'if314003', '11314003', 'Yulianty Sihombing', 'if314003@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1794, 956, 954, 'if313039', '11113039', 'Yuni Evalin Sibarani', 'if313039@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2013, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1795, 1086, 1084, 'if314030', '11314030', 'Yunita Br Hutajulu', 'if314030@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1796, 141, 142, 'if01049', '11101049', 'Yunita Renta Hutagaol', 'if01049@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2001, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1797, 293, 294, 'if05075', '11105075', 'YUNITA SITINJAK', 'if05075@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2005, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1798, 3976, 4684, 'if420005', '11420005', 'Yupiter Lambe', 'if420005@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1799, 745, 743, 'if11068', '11111068', 'Yusfi Apriyanti', 'if11068@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1800, 4130, 4840, 'ce320026', '13320026', 'Yusuf Relano Panjaitan', 'ce320026@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1801, 698, 696, 'if11050', '11111050', 'Zanna Sukses Simarmata', 'if11050@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1802, 742, 740, 'if11031', '11111031', 'Zepri Hasiholan Togatorop', 'if11031@students.del.ac.id', 1, 'DIII Teknologi Informasi', 'Vokasi', 2011, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1803, 1143, 1141, 'ce314026', '13314026', 'Zhendro Hatma Wijaya Haloho', 'ce314026@students.del.ac.id', 3, 'DIII Teknologi Komputer', 'Vokasi', 2014, NULL, 'Lulus', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1804, 3895, 4569, 'if419049', '11419049', 'ZICO ANDREAS ARITONANG', 'if419049@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2019, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11'),
(1805, 4369, 5079, 'if420083', '11420083', 'Ziva Amanda Tampubolon', 'if420083@students.del.ac.id', 4, 'DIV Teknologi Rekayasa Perangkat Lunak', 'Vokasi', 2020, NULL, 'Aktif', '', '2026-06-12 03:59:16', '2026-06-12 05:21:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mata_kuliah`
--

CREATE TABLE `mata_kuliah` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kuliah_id` int(11) NOT NULL,
  `kode_mk` varchar(255) NOT NULL,
  `nama_matkul` varchar(255) NOT NULL,
  `sks` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `prodi_id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran` year(4) NOT NULL,
  `semester_ta` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `mata_kuliah`
--

INSERT INTO `mata_kuliah` (`id`, `kuliah_id`, `kode_mk`, `nama_matkul`, `sks`, `semester`, `prodi_id`, `tahun_ajaran`, `semester_ta`, `created_at`, `updated_at`) VALUES
(1, 943, 'KU41101', 'Pembentukan Karakter Del', 2, 1, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(2, 950, '1141105', 'Pengenalan Rekayasa Perangkat Lunak', 3, 1, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:21'),
(3, 949, '1141104', 'Pengembangan Situs Web I', 3, 1, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(4, 948, '1041103', 'Arsitektur dan Organisasi Komputer', 2, 1, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(5, 947, '1041102', 'Matematika Diskrit', 3, 1, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(6, 946, '1041101', 'Dasar Pemrograman', 3, 1, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(7, 945, 'TI41101', 'Inovasi Digital', 2, 1, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(8, 944, 'KU41102', 'Bahasa Inggris I', 2, 1, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(9, 964, '1142106', 'Logika Informatika', 2, 3, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(10, 963, '1142105', 'Sistem Basis Data', 3, 3, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(11, 962, '1142104', 'Perancangan Antarmuka Pengguna', 3, 3, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:21'),
(12, 961, '1142103', 'Pengembangan Perangkat Lunak Berorientasi Objek', 3, 3, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(13, 960, '1042102', 'Algoritma dan Struktur Data', 3, 3, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(14, 959, '1042101', 'Jaringan Komputer', 3, 3, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(15, 958, 'KU42101', 'Bahasa Inggris III', 2, 3, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(16, 983, '1143203', 'Pembelajaran Mesin', 3, 5, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(17, 977, '1143106', 'Metodologi Penelitian', 2, 5, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(18, 976, '1143105', 'Automata', 3, 5, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(19, 975, '1143104', 'Keamanan Perangkat Lunak', 3, 5, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(20, 974, '1143103', 'Kreativitas dan Inovasi', 3, 5, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(21, 973, '1143102', 'Algoritma Lanjut', 3, 5, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(22, 972, '1143101', 'Pengujian Perangkat Lunak', 3, 5, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(23, 971, 'KU43101', 'Bahasa Inggris V', 2, 5, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(24, 991, '1144190', 'Tugas Akhir I', 3, 7, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(25, 990, '1144104', 'Reenginering Perangkat Lunak', 2, 7, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(26, 989, '1144103', 'Kualitas Perangkat Lunak', 2, 7, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(27, 988, '1144102', 'Manajemen Proyek', 3, 7, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(28, 987, '1144101', 'Arsitektur dan Perancangan Perangkat Lunak', 3, 7, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(29, 986, 'TI44101', 'Keteknowiraan', 3, 7, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(30, 1072, '1144105', 'Kecerdasan Buatan', 3, 7, 4, '2020', 1, '2026-06-12 03:59:21', '2026-06-12 05:21:20'),
(31, 957, '1141290', 'Proyek Akhir Tahun I', 3, 2, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(32, 956, '1141205', 'Pengenalan Basis Data', 3, 2, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(33, 955, '1141204', 'Pengembangan Situs Web II', 3, 2, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(34, 954, '1141203', 'Analisis Kebutuhan Perangkat Lunak', 3, 2, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(35, 953, '1041202', 'Sistem Operasi', 3, 2, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(36, 952, 'KU41202', 'Penulisan Karya Ilmiah', 2, 2, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(37, 951, 'KU41201', 'Bahasa Inggris II', 2, 2, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(38, 970, '1142290', 'Proyek Akhir Tahun II', 4, 4, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(39, 969, '1142203', 'Pengembangan Aplikasi Mobile', 3, 4, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(40, 968, '1142202', 'Pengembangan Aplikasi Terdistribusi', 3, 4, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(41, 967, '1142201', 'Pemrograman Berorientasi Objek', 3, 4, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(42, 966, 'MA42201', 'Probabilitas dan Statistik', 3, 4, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(43, 965, 'KU42201', 'Bahasa Inggris IV', 2, 4, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(44, 985, '1143290', 'Proyek Akhir Tahun III', 4, 6, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(45, 984, '1143204', 'Komputer dan Masyarakat', 2, 6, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(46, 982, '1143202', 'Design Thinking', 2, 6, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(47, 981, '1143201', 'Etika Profesi', 2, 6, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(48, 980, 'KU43203', 'Pancasila dan Kewarganegaraan', 2, 6, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(49, 979, 'KU43202', 'Agama dan Etika', 2, 6, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(50, 978, 'KU43201', 'Bahasa Inggris VI', 2, 6, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(51, 992, '1144201', 'Studi Mandiri / Sertifikasi Profesional', 3, 8, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(52, 993, '1144290', 'Tugas Akhir II', 4, 8, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21'),
(53, 994, '1144291', 'Kerja Praktik Industri', 6, 8, 4, '2020', 2, '2026-06-12 03:59:22', '2026-06-12 05:21:21');

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
(4, '0001_01_01_000002_create_tahun_ajaran_table', 1),
(5, '2025_03_12_082302_create_tahun_masuk_table', 1),
(6, '2025_03_13_060113_create_kategori_pa_table', 1),
(7, '2025_04_02_102725_create_prodi_table', 1),
(8, '2025_04_03_100904_create_roles_table', 1),
(9, '2025_04_04_000540_create_dosen_roles_table', 1),
(10, '2025_04_09_090711_create_kelompok_table', 1),
(11, '2025_04_10_234913_create_mahasiswa_table', 1),
(12, '2025_04_10_234914_create_kelompok_mahasiswa_table', 1),
(13, '2025_04_12_133601_create_artefaks_table', 1),
(14, '2025_04_12_140007_create_tugas_table', 1),
(15, '2025_04_13_104618_create_ruangan_table', 1),
(16, '2025_04_13_154817_create_pengumuman_table', 1),
(17, '2025_04_14_133405_create_jadwal_table', 1),
(18, '2025_04_15_023936_create_bimbingan_table', 1),
(19, '2025_04_16_063911_create_pembimbing_table', 1),
(20, '2025_04_20_171924_create_pengumpulan_tugas_table', 1),
(21, '2025_04_24_142516_create_nilai_kelompok_table', 1),
(22, '2025_04_26_215113_create_penguji_table', 1),
(23, '2025_04_28_132404_create_nilai_individu_table', 1),
(24, '2025_04_29_150052_create_kartu_bimbingan_table', 1),
(25, '2025_04_29_184453_create_pengajuan_seminar_table', 1),
(26, '2025_04_30_171909_create_nilai_administrasi_table', 1),
(27, '2025_04_30_210053_create_nilai_bimbingan_table', 1),
(28, '2025_05_01_213432_create_pengajuan_seminar_files_table', 1),
(29, '2025_05_02_192841_create_nilai_seminar_table', 1),
(30, '2025_05_03_192756_create_nilai_mahasiswa_table', 1),
(31, '2025_05_30_112700_create_device_token_table', 1),
(32, '2026_02_19_145131_update_dosen_roles_replace_tahun_ajaran_column', 1),
(33, '2026_03_05_160911_create_dosen_table', 1),
(34, '2026_03_08_152545_create_mata_kuliah_table', 1),
(35, '2026_03_08_152745_create_nilai_matkul_mahasiswa_table', 1),
(36, '2026_05_06_add_nama_to_dosen_roles_table', 1),
(37, '2026_06_12_091913_create_judul_proyek_akhir_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_administrasi`
--

CREATE TABLE `nilai_administrasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `Administrasi` double NOT NULL,
  `Pameran` double NOT NULL,
  `Total` double NOT NULL,
  `C1` double DEFAULT NULL,
  `C2` double DEFAULT NULL,
  `C3` double DEFAULT NULL,
  `C4` double DEFAULT NULL,
  `C5` double DEFAULT NULL,
  `C_total` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `nilai_administrasi`
--

INSERT INTO `nilai_administrasi` (`id`, `kelompok_id`, `user_id`, `Administrasi`, `Pameran`, `Total`, `C1`, `C2`, `C3`, `C4`, `C5`, `C_total`, `created_at`, `updated_at`) VALUES
(1, 19, 3602, 78.6, 80, 78.6, 80, 80, 55, 80, 98, 78.6, '2026-06-14 02:24:24', '2026-06-14 02:24:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_bimbingan`
--

CREATE TABLE `nilai_bimbingan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `penilai_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `A1` double NOT NULL,
  `A2` double NOT NULL,
  `A3` double NOT NULL,
  `A4` double NOT NULL,
  `A5` double NOT NULL,
  `Total` double DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `nilai_bimbingan`
--

INSERT INTO `nilai_bimbingan` (`id`, `penilai_id`, `role_id`, `A1`, `A2`, `A3`, `A4`, `A5`, `Total`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 3602, 3, 80, 80, 79, 80, 80, 79.85, 4527, '2026-06-14 02:57:37', '2026-06-14 02:57:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_individu`
--

CREATE TABLE `nilai_individu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `penilai_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `B11` double NOT NULL,
  `B12` double NOT NULL,
  `B13` double NOT NULL,
  `B14` double NOT NULL,
  `B15` double NOT NULL,
  `B1_total` double DEFAULT NULL,
  `B21` double NOT NULL,
  `B22` double NOT NULL,
  `B23` double NOT NULL,
  `B24` double NOT NULL,
  `B25` double NOT NULL,
  `B2_total` double DEFAULT NULL,
  `B31` double NOT NULL,
  `B3_total` double DEFAULT NULL,
  `D1` double DEFAULT NULL,
  `B_total` double DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `nilai_individu`
--

INSERT INTO `nilai_individu` (`id`, `kelompok_id`, `penilai_id`, `role_id`, `B11`, `B12`, `B13`, `B14`, `B15`, `B1_total`, `B21`, `B22`, `B23`, `B24`, `B25`, `B2_total`, `B31`, `B3_total`, `D1`, `B_total`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 19, 3602, 3, 80, 80, 80, 80, 79, 7.98, 80, 80, 80, 80, 80, 8, 80, 20, NULL, 35.98, 4527, '2026-06-14 02:58:45', '2026-06-14 02:59:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_kelompok`
--

CREATE TABLE `nilai_kelompok` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `A11` double NOT NULL,
  `A12` double NOT NULL,
  `A13` double NOT NULL,
  `A1_total` double DEFAULT NULL,
  `A21` double NOT NULL,
  `A22` double NOT NULL,
  `A23` double NOT NULL,
  `A2_total` double DEFAULT NULL,
  `A_total` double DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `nilai_kelompok`
--

INSERT INTO `nilai_kelompok` (`id`, `kelompok_id`, `role_id`, `A11`, `A12`, `A13`, `A1_total`, `A21`, `A22`, `A23`, `A2_total`, `A_total`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 19, 3, 80, 80, 80, 20, 80, 80, 80, 24, 44, 3602, '2026-06-14 04:02:58', '2026-06-14 04:02:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_mahasiswa`
--

CREATE TABLE `nilai_mahasiswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `nilai_akhir` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `nilai_mahasiswa`
--

INSERT INTO `nilai_mahasiswa` (`id`, `user_id`, `kelompok_id`, `nilai_akhir`, `created_at`, `updated_at`) VALUES
(1, 4527, 19, 43.8, NULL, NULL),
(2, 4531, 19, 11.86, NULL, NULL),
(3, 4534, 19, 11.86, NULL, NULL),
(4, 4569, 19, 11.86, NULL, NULL),
(5, 4572, 19, 11.86, NULL, NULL),
(6, 4573, 19, 11.86, NULL, NULL),
(7, 4524, 20, 0, NULL, NULL),
(8, 4535, 20, 0, NULL, NULL),
(9, 4550, 20, 0, NULL, NULL),
(10, 4559, 20, 0, NULL, NULL),
(11, 4579, 20, 0, NULL, NULL),
(12, 4585, 20, 0, NULL, NULL),
(13, 4545, 21, 0, NULL, NULL),
(14, 4546, 21, 0, NULL, NULL),
(15, 4548, 21, 0, NULL, NULL),
(16, 4551, 21, 0, NULL, NULL),
(17, 4578, 21, 0, NULL, NULL),
(18, 4582, 21, 0, NULL, NULL),
(19, 4522, 22, 0, NULL, NULL),
(20, 4525, 22, 0, NULL, NULL),
(21, 4533, 22, 0, NULL, NULL),
(22, 4562, 22, 0, NULL, NULL),
(23, 4570, 22, 0, NULL, NULL),
(24, 4575, 22, 0, NULL, NULL),
(25, 4528, 23, 0, NULL, NULL),
(26, 4537, 23, 0, NULL, NULL),
(27, 4549, 23, 0, NULL, NULL),
(28, 4558, 23, 0, NULL, NULL),
(29, 4571, 23, 0, NULL, NULL),
(30, 4584, 23, 0, NULL, NULL),
(31, 4561, 24, 0, NULL, NULL),
(32, 4565, 24, 0, NULL, NULL),
(33, 4576, 24, 0, NULL, NULL),
(34, 4577, 24, 0, NULL, NULL),
(35, 4587, 24, 0, NULL, NULL),
(36, 4624, 24, 0, NULL, NULL),
(37, 4536, 25, 0, NULL, NULL),
(38, 4539, 25, 0, NULL, NULL),
(39, 4544, 25, 0, NULL, NULL),
(40, 4552, 25, 0, NULL, NULL),
(41, 4560, 25, 0, NULL, NULL),
(42, 4574, 25, 0, NULL, NULL),
(43, 4526, 26, 0, NULL, NULL),
(44, 4543, 26, 0, NULL, NULL),
(45, 4555, 26, 0, NULL, NULL),
(46, 4566, 26, 0, NULL, NULL),
(47, 4567, 26, 0, NULL, NULL),
(48, 4581, 26, 0, NULL, NULL),
(49, 4529, 27, 0, NULL, NULL),
(50, 4541, 27, 0, NULL, NULL),
(51, 4553, 27, 0, NULL, NULL),
(52, 4564, 27, 0, NULL, NULL),
(53, 4580, 27, 0, NULL, NULL),
(54, 4586, 27, 0, NULL, NULL),
(55, 4530, 28, 0, NULL, NULL),
(56, 4540, 28, 0, NULL, NULL),
(57, 4542, 28, 0, NULL, NULL),
(58, 4547, 28, 0, NULL, NULL),
(59, 4556, 28, 0, NULL, NULL),
(60, 4532, 29, 0, NULL, NULL),
(61, 4538, 29, 0, NULL, NULL),
(62, 4554, 29, 0, NULL, NULL),
(63, 4557, 29, 0, NULL, NULL),
(64, 4583, 29, 0, NULL, NULL),
(65, 4521, 30, 0, NULL, NULL),
(66, 4523, 30, 0, NULL, NULL),
(67, 4563, 30, 0, NULL, NULL),
(68, 4568, 30, 0, NULL, NULL),
(69, 4588, 30, 0, NULL, NULL),
(70, 4680, 1, 0, NULL, NULL),
(71, 4703, 1, 0, NULL, NULL),
(72, 5030, 1, 0, NULL, NULL),
(73, 5038, 1, 0, NULL, NULL),
(74, 5073, 1, 0, NULL, NULL),
(75, 4689, 10, 0, NULL, NULL),
(76, 5034, 10, 0, NULL, NULL),
(77, 5066, 10, 0, NULL, NULL),
(78, 5067, 10, 0, NULL, NULL),
(79, 5072, 10, 0, NULL, NULL),
(80, 4687, 11, 0, NULL, NULL),
(81, 5040, 11, 0, NULL, NULL),
(82, 5042, 11, 0, NULL, NULL),
(83, 5043, 11, 0, NULL, NULL),
(84, 5050, 11, 0, NULL, NULL),
(85, 4693, 12, 0, NULL, NULL),
(86, 5025, 12, 0, NULL, NULL),
(87, 5031, 12, 0, NULL, NULL),
(88, 5033, 12, 0, NULL, NULL),
(89, 5047, 12, 0, NULL, NULL),
(90, 4682, 13, 0, NULL, NULL),
(91, 5046, 13, 0, NULL, NULL),
(92, 5051, 13, 0, NULL, NULL),
(93, 5061, 13, 0, NULL, NULL),
(94, 5077, 13, 0, NULL, NULL),
(95, 4684, 14, 0, NULL, NULL),
(96, 5028, 14, 0, NULL, NULL),
(97, 5041, 14, 0, NULL, NULL),
(98, 5044, 14, 0, NULL, NULL),
(99, 5056, 14, 0, NULL, NULL),
(100, 4699, 15, 0, NULL, NULL),
(101, 5060, 15, 0, NULL, NULL),
(102, 5062, 15, 0, NULL, NULL),
(103, 5076, 15, 0, NULL, NULL),
(104, 5079, 15, 0, NULL, NULL),
(105, 4686, 16, 0, NULL, NULL),
(106, 4696, 16, 0, NULL, NULL),
(107, 4698, 16, 0, NULL, NULL),
(108, 5068, 16, 0, NULL, NULL),
(109, 5029, 17, 0, NULL, NULL),
(110, 5054, 17, 0, NULL, NULL),
(111, 5064, 17, 0, NULL, NULL),
(112, 5081, 17, 0, NULL, NULL),
(113, 5027, 18, 0, NULL, NULL),
(114, 5032, 18, 0, NULL, NULL),
(115, 5049, 18, 0, NULL, NULL),
(116, 5052, 18, 0, NULL, NULL),
(117, 5026, 2, 0, NULL, NULL),
(118, 5039, 2, 0, NULL, NULL),
(119, 5053, 2, 0, NULL, NULL),
(120, 5069, 2, 0, NULL, NULL),
(121, 5082, 2, 0, NULL, NULL),
(122, 4685, 3, 0, NULL, NULL),
(123, 4692, 3, 0, NULL, NULL),
(124, 4704, 3, 0, NULL, NULL),
(125, 5048, 3, 0, NULL, NULL),
(126, 5080, 3, 0, NULL, NULL),
(127, 5023, 4, 0, NULL, NULL),
(128, 5035, 4, 0, NULL, NULL),
(129, 5055, 4, 0, NULL, NULL),
(130, 5057, 4, 0, NULL, NULL),
(131, 5059, 4, 0, NULL, NULL),
(132, 4683, 5, 0, NULL, NULL),
(133, 4690, 5, 0, NULL, NULL),
(134, 5024, 5, 0, NULL, NULL),
(135, 5074, 5, 0, NULL, NULL),
(136, 5078, 5, 0, NULL, NULL),
(137, 4688, 6, 0, NULL, NULL),
(138, 4701, 6, 0, NULL, NULL),
(139, 5036, 6, 0, NULL, NULL),
(140, 5063, 6, 0, NULL, NULL),
(141, 5065, 6, 0, NULL, NULL),
(142, 4691, 7, 0, NULL, NULL),
(143, 4695, 7, 0, NULL, NULL),
(144, 5022, 7, 0, NULL, NULL),
(145, 5045, 7, 0, NULL, NULL),
(146, 5071, 7, 0, NULL, NULL),
(147, 4697, 8, 0, NULL, NULL),
(148, 4702, 8, 0, NULL, NULL),
(149, 5037, 8, 0, NULL, NULL),
(150, 5058, 8, 0, NULL, NULL),
(151, 5075, 8, 0, NULL, NULL),
(152, 4681, 9, 0, NULL, NULL),
(153, 4694, 9, 0, NULL, NULL),
(154, 4700, 9, 0, NULL, NULL),
(155, 5070, 9, 0, NULL, NULL),
(156, 5083, 9, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_matkul_mahasiswa`
--

CREATE TABLE `nilai_matkul_mahasiswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mahasiswa_id` int(10) UNSIGNED NOT NULL,
  `kode_mk` varchar(255) NOT NULL,
  `nilai_angka` decimal(5,2) DEFAULT NULL,
  `nilai_huruf` varchar(2) DEFAULT NULL,
  `bobot_nilai` decimal(3,2) DEFAULT NULL,
  `semester` int(11) NOT NULL,
  `tahun_ajaran` year(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_seminar`
--

CREATE TABLE `nilai_seminar` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `nilai_kelompok_role_2` double NOT NULL,
  `nilai_individu_role_2` double NOT NULL,
  `total_role_2` double NOT NULL,
  `nilai_kelompok_role_3` double NOT NULL,
  `nilai_individu_role_3` double NOT NULL,
  `total_role_3` double NOT NULL,
  `nilai_kelompok_role_4` double NOT NULL,
  `nilai_individu_role_4` double NOT NULL,
  `total_role_4` double NOT NULL,
  `nilai_kelompok_role_5` double NOT NULL,
  `nilai_individu_role_5` double NOT NULL,
  `total_role_5` double NOT NULL,
  `nilai_seminar` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Struktur dari tabel `pembimbing`
--

CREATE TABLE `pembimbing` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pembimbing`
--

INSERT INTO `pembimbing` (`id`, `user_id`, `kelompok_id`, `created_at`, `updated_at`) VALUES
(1, 3602, 19, '2026-06-14 02:56:12', '2026-06-14 02:56:12'),
(2, 3607, 19, '2026-06-14 02:56:12', '2026-06-14 02:56:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_seminar`
--

CREATE TABLE `pengajuan_seminar` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `pembimbing_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_seminar_files`
--

CREATE TABLE `pengajuan_seminar_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengajuan_seminar_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penguji`
--

CREATE TABLE `penguji` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `penguji`
--

INSERT INTO `penguji` (`id`, `user_id`, `kelompok_id`, `created_at`, `updated_at`) VALUES
(1, 3607, 19, '2026-06-14 03:14:55', '2026-06-14 03:14:55'),
(2, 5757, 19, '2026-06-14 03:15:33', '2026-06-14 03:15:33'),
(3, 3602, 1, '2026-06-14 03:34:16', '2026-06-14 03:34:16'),
(4, 3607, 1, '2026-06-14 03:34:16', '2026-06-14 03:34:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumpulan_tugas`
--

CREATE TABLE `pengumpulan_tugas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `tugas_id` bigint(20) UNSIGNED NOT NULL,
  `waktu_submit` datetime DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('Submitted','Late','Belum') NOT NULL DEFAULT 'Submitted',
  `feedback` text DEFAULT NULL,
  `feedback_pembimbing` text DEFAULT NULL,
  `feedback_penguji` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `tanggal_penulisan` datetime NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` enum('aktif','non-aktif') NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `KPA_id` bigint(20) UNSIGNED NOT NULL,
  `prodi_id` bigint(20) UNSIGNED NOT NULL,
  `TM_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `prodi`
--

CREATE TABLE `prodi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_prodi` varchar(255) NOT NULL,
  `maks_project` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `prodi`
--

INSERT INTO `prodi` (`id`, `nama_prodi`, `maks_project`, `created_at`, `updated_at`) VALUES
(1, 'DIII Teknologi Informasi', 2, '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(3, 'DIII Teknologi Komputer', 2, '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(4, 'DIV Teknologi Rekayasa Perangkat Lunak', 3, '2026-06-12 02:55:48', '2026-06-12 02:55:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `request_bimbingan`
--

CREATE TABLE `request_bimbingan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kelompok_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `keperluan` varchar(255) NOT NULL,
  `rencana_mulai` datetime NOT NULL,
  `rencana_selesai` datetime NOT NULL,
  `ruangan_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('menunggu','selesai','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `hasil_bimbingan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `created_at`, `updated_at`) VALUES
(1, 'Koordinator', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(2, 'Penguji 1', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(3, 'Pembimbing 1', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(4, 'Penguji 2', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(5, 'Pembimbing 2', '2026-06-12 02:55:48', '2026-06-12 02:55:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ruangan`
--

CREATE TABLE `ruangan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ruangan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `ruangan`
--

INSERT INTO `ruangan` (`id`, `ruangan`, `created_at`, `updated_at`) VALUES
(1, 'Auditorium', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(2, 'Common Room', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(3, 'Lab', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(4, 'Ruang Rapat Dekan FITE', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(5, 'GD421', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(6, 'GD422', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(7, 'GD423', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(8, 'GD511', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(9, 'GD512', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(10, 'GD513', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(11, 'GD514', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(12, 'GD515', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(13, 'GD516', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(14, 'GD517', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(15, 'GD521', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(16, 'GD522', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(17, 'GD523', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(18, 'GD524', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(19, 'GD525', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(20, 'GD526', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(21, 'GD527', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(22, 'GD711', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(23, 'GD712', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(24, 'GD713', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(25, 'GD714', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(26, 'GD721', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(27, 'GD722', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(28, 'GD723', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(29, 'GD724', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(30, 'GD725', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(31, 'GD726', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(32, 'GD911', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(33, 'GD912', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(34, 'GD914', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(35, 'GD923', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(36, 'GD924', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(37, 'GD925', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(38, 'GD927', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(39, 'GD928', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(40, 'GD929', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(41, 'GD933', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(42, 'GD934', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(43, 'GD935', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(44, 'GD937', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(45, 'GD938', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(46, 'GD939', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(47, 'GD942', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(48, 'GD943', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(49, 'GD944', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(50, 'Ruang Rapat Vokasi Lt-1', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(51, 'Ruang Rapat Vokasi Lt-2', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(52, 'Penggunaan HPC', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(53, 'Kantin', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(54, 'Open Theater', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(55, 'Ruang Meeting Gedung Rektorat Lantai 2', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(56, 'Meeting Room - Ruang Kaca', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(57, 'Ruang Meeting Kecil', '2026-06-12 02:55:48', '2026-06-12 02:55:48');

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
('hSJGr3Qh6fC2MVLtdk3WG1zlB5eLMaXCHFg06ADP', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YToxNTp7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo2OiJfdG9rZW4iO3M6NDA6IlpxT28xMllxV21pdlhqcVBWYlVROWtIVzJPaGl5VG92c2RYTmpMR0YiO3M6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ4OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvTmlsYWlJbmRpdmlkdS9wZW1iaW1iaW5nLTEiO3M6NToicm91dGUiO3M6MzE6InBlbWJpbWJpbmcxLk5pbGFpSW5kaXZpZHUuaW5kZXgiO31zOjc6InVzZXJfaWQiO2k6MzYwMjtzOjQ6InJvbGUiO3M6NToiRG9zZW4iO3M6NToidG9rZW4iO3M6MzIwOiJleUowZVhBaU9pSktWMVFpTENKaGJHY2lPaUpJVXpJMU5pSXNJbXAwYVNJNklsVk9TVkZWUlMxS1YxUXRTVVJGVGxSSlJrbEZVaUo5LmV5SnBjM01pT2lKb2RIUndjenBjTDF3dllYQnBMbVY0WVcxd2JHVXVZMjl0SWl3aVlYVmtJam9pYUhSMGNITTZYQzljTDJaeWIyNTBaVzVrTG1WNFlXMXdiR1V1WTI5dElpd2lhblJwSWpvaVZVNUpVVlZGTFVwWFZDMUpSRVZPVkVsR1NVVlNJaXdpYVdGMElqb3hOemd4TkRBNE5Ea3lMQ0psZUhBaU9qRTNPREUwTVRFME9USXNJblZwWkNJNk16WXdNbjAuOVZ1bjR2MHlkckd2eFB3VW9oNUJuNWk2UFpLYWhqT0JhM0ttQk5mVnFWSSI7czo0OiJuYW1lIjtzOjM0OiJDeW50aGlhIERlYm9yYWggTmFiYWJhbiwgUy5Uci5Lb20uIjtzOjU6ImVtYWlsIjtzOjE6Ii0iO3M6ODoiaXNMb2dnaW4iO2I6MTtzOjg6InByb2RpX2lkIjtpOjQ7czo2OiJLUEFfaWQiO2k6MztzOjU6IlRNX2lkIjtpOjE7czo3OiJyb2xlX2lkIjtpOjE7czoxNToidGFodW5fYWphcmFuX2lkIjtpOjE7czoxMToiZG9zZW5fcm9sZXMiO2E6Mzp7aTowO2k6MTtpOjE7aToyO2k6MjtpOjM7fX0=', 1781409803),
('QoRuF8YgKyx6L7athuVoQwMS9V3e7MDivCQYdbDP', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YToxNTp7czo2OiJfdG9rZW4iO3M6NDA6IkY3QkNzNGZGejZKeXdjM21nbEZKUTUxZVp1RWcxd0dnNmZMNzhpbksiO3M6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ3OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvTmlsYWlCaW1iaW5nYW4vTmlsYWlBS2hpciI7czo1OiJyb3V0ZSI7czoxNjoiTmlsYWlBa2hpci5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NzoidXNlcl9pZCI7aTozNjAyO3M6NDoicm9sZSI7czo1OiJEb3NlbiI7czo1OiJ0b2tlbiI7czozMjA6ImV5SjBlWEFpT2lKS1YxUWlMQ0poYkdjaU9pSklVekkxTmlJc0ltcDBhU0k2SWxWT1NWRlZSUzFLVjFRdFNVUkZUbFJKUmtsRlVpSjkuZXlKcGMzTWlPaUpvZEhSd2N6cGNMMXd2WVhCcExtVjRZVzF3YkdVdVkyOXRJaXdpWVhWa0lqb2lhSFIwY0hNNlhDOWNMMlp5YjI1MFpXNWtMbVY0WVcxd2JHVXVZMjl0SWl3aWFuUnBJam9pVlU1SlVWVkZMVXBYVkMxSlJFVk9WRWxHU1VWU0lpd2lhV0YwSWpveE56Z3hOREl3TmpJM0xDSmxlSEFpT2pFM09ERTBNak0yTWpjc0luVnBaQ0k2TXpZd01uMC5Wc3l1TmFkVTN5NDRvanlYQWRHaFRKeDJCNzFaNWx5MUlWei10WTE0ZWlNIjtzOjQ6Im5hbWUiO3M6MzQ6IkN5bnRoaWEgRGVib3JhaCBOYWJhYmFuLCBTLlRyLktvbS4iO3M6NToiZW1haWwiO3M6MToiLSI7czo4OiJpc0xvZ2dpbiI7YjoxO3M6ODoicHJvZGlfaWQiO2k6NDtzOjY6IktQQV9pZCI7aTozO3M6NToiVE1faWQiO2k6MTtzOjc6InJvbGVfaWQiO2k6MTtzOjE1OiJ0YWh1bl9hamFyYW5faWQiO2k6MTtzOjExOiJkb3Nlbl9yb2xlcyI7YTozOntpOjA7aToxO2k6MTtpOjI7aToyO2k6Mzt9fQ==', 1781420691);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tahun_ajaran`
--

CREATE TABLE `tahun_ajaran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tahun_mulai` year(4) NOT NULL,
  `tahun_selesai` year(4) NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Nonaktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tahun_ajaran`
--

INSERT INTO `tahun_ajaran` (`id`, `tahun_mulai`, `tahun_selesai`, `status`, `created_at`, `updated_at`) VALUES
(1, '2025', '2026', 'Aktif', '2026-06-12 03:21:27', '2026-06-12 03:21:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tahun_masuk`
--

CREATE TABLE `tahun_masuk` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `Tahun_Masuk` year(4) NOT NULL,
  `Status` enum('Aktif','Tidak-Aktif') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tahun_masuk`
--

INSERT INTO `tahun_masuk` (`id`, `Tahun_Masuk`, `Status`, `created_at`, `updated_at`) VALUES
(1, '2019', 'Aktif', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(2, '2020', 'Aktif', '2026-06-12 02:55:48', '2026-06-12 03:22:35'),
(3, '2021', 'Aktif', '2026-06-12 02:55:48', '2026-06-12 03:22:41'),
(4, '2022', 'Aktif', '2026-06-12 02:55:48', '2026-06-12 02:55:48'),
(5, '2023', 'Aktif', '2026-06-12 02:55:48', '2026-06-12 02:55:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tugas`
--

CREATE TABLE `tugas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `Judul_Tugas` varchar(500) NOT NULL,
  `Deskripsi_Tugas` varchar(1000) NOT NULL,
  `KPA_id` bigint(20) UNSIGNED NOT NULL,
  `prodi_id` bigint(20) UNSIGNED NOT NULL,
  `TM_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_pengumpulan` datetime NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` enum('selesai','berlangsung') NOT NULL,
  `kategori_tugas` enum('Tugas','Revisi','Artefak') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Chandra Boike Simanjuntak, S.E.', '-', NULL, '$2y$12$H3a2VKSyMwhu7V7GkwyCOe7PknTV/isxNhLJ9KYS2F8MRthoRKFdW', NULL, '2026-06-12 03:15:15', '2026-06-12 03:15:15'),
(2, 'Theresya Gurning', 'if420086@students.del.ac.id', NULL, '$2y$12$B4ILD14T2BjOlSmpsID3au2G9e6TqYBNFBUJEJryMaydsKc7tIFQq', NULL, '2026-06-12 04:06:34', '2026-06-12 04:06:34');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `artefaks`
--
ALTER TABLE `artefaks`
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
-- Indeks untuk tabel `device_token`
--
ALTER TABLE `device_token`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dosen_user_id_unique` (`user_id`);

--
-- Indeks untuk tabel `dosen_roles`
--
ALTER TABLE `dosen_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dr_unique` (`user_id`,`role_id`,`prodi_id`,`KPA_id`,`TM_id`,`tahun_ajaran_id`),
  ADD KEY `dosen_roles_kpa_id_foreign` (`KPA_id`),
  ADD KEY `dosen_roles_prodi_id_foreign` (`prodi_id`),
  ADD KEY `dosen_roles_role_id_foreign` (`role_id`),
  ADD KEY `dosen_roles_tm_id_foreign` (`TM_id`),
  ADD KEY `dosen_roles_tahun_ajaran_id_foreign` (`tahun_ajaran_id`),
  ADD KEY `dr_multi_index` (`user_id`,`role_id`,`prodi_id`,`KPA_id`,`TM_id`,`tahun_ajaran_id`,`status`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jadwal_kelompok_id_foreign` (`kelompok_id`),
  ADD KEY `jadwal_ruangan_id_foreign` (`ruangan_id`),
  ADD KEY `jadwal_kpa_id_foreign` (`KPA_id`),
  ADD KEY `jadwal_prodi_id_foreign` (`prodi_id`),
  ADD KEY `jadwal_tm_id_foreign` (`TM_id`),
  ADD KEY `jadwal_user_id_index` (`user_id`);

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
-- Indeks untuk tabel `judul_proyek_akhir`
--
ALTER TABLE `judul_proyek_akhir`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `judul_proyek_akhir_kelompok_id_unique` (`kelompok_id`);

--
-- Indeks untuk tabel `kartu_bimbingan`
--
ALTER TABLE `kartu_bimbingan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kartu_bimbingan_request_bimbingan_id_index` (`request_bimbingan_id`),
  ADD KEY `kartu_bimbingan_pembimbing_id_index` (`pembimbing_id`),
  ADD KEY `kartu_bimbingan_kelompok_id_index` (`kelompok_id`);

--
-- Indeks untuk tabel `kategori_pa`
--
ALTER TABLE `kategori_pa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kelompok`
--
ALTER TABLE `kelompok`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kelompok_unique` (`nomor_kelompok`,`KPA_id`,`prodi_id`,`TM_id`,`tahun_ajaran_id`),
  ADD KEY `kelompok_kpa_id_foreign` (`KPA_id`),
  ADD KEY `kelompok_prodi_id_foreign` (`prodi_id`),
  ADD KEY `kelompok_tm_id_foreign` (`TM_id`),
  ADD KEY `kelompok_status_index` (`status`),
  ADD KEY `kelompok_tahun_ajaran_id_index` (`tahun_ajaran_id`);

--
-- Indeks untuk tabel `kelompok_mahasiswa`
--
ALTER TABLE `kelompok_mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kelompok_mahasiswa_kelompok_id_user_id_unique` (`kelompok_id`,`user_id`),
  ADD KEY `kelompok_mahasiswa_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mahasiswa_dim_id_unique` (`dim_id`),
  ADD UNIQUE KEY `mahasiswa_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `mahasiswa_nim_unique` (`nim`),
  ADD KEY `mahasiswa_angkatan_index` (`angkatan`),
  ADD KEY `mahasiswa_prodi_id_index` (`prodi_id`),
  ADD KEY `mahasiswa_status_index` (`status`);

--
-- Indeks untuk tabel `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mata_kuliah_kuliah_id_unique` (`kuliah_id`),
  ADD KEY `mata_kuliah_prodi_id_foreign` (`prodi_id`),
  ADD KEY `mata_kuliah_kode_mk_index` (`kode_mk`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `nilai_administrasi`
--
ALTER TABLE `nilai_administrasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nilai_administrasi_kelompok_id_foreign` (`kelompok_id`);

--
-- Indeks untuk tabel `nilai_bimbingan`
--
ALTER TABLE `nilai_bimbingan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nilai_bimbingan_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `nilai_individu`
--
ALTER TABLE `nilai_individu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nilai_individu_kelompok_id_user_id_unique` (`kelompok_id`,`user_id`),
  ADD KEY `nilai_individu_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `nilai_kelompok`
--
ALTER TABLE `nilai_kelompok`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nilai_kelompok_kelompok_id_foreign` (`kelompok_id`),
  ADD KEY `nilai_kelompok_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `nilai_mahasiswa`
--
ALTER TABLE `nilai_mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nilai_mahasiswa_kelompok_id_foreign` (`kelompok_id`);

--
-- Indeks untuk tabel `nilai_matkul_mahasiswa`
--
ALTER TABLE `nilai_matkul_mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nilai_matkul_mahasiswa_mahasiswa_id_index` (`mahasiswa_id`),
  ADD KEY `nilai_matkul_mahasiswa_kode_mk_index` (`kode_mk`);

--
-- Indeks untuk tabel `nilai_seminar`
--
ALTER TABLE `nilai_seminar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nilai_seminar_kelompok_id_foreign` (`kelompok_id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `pembimbing`
--
ALTER TABLE `pembimbing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembimbing_kelompok_id_foreign` (`kelompok_id`);

--
-- Indeks untuk tabel `pengajuan_seminar`
--
ALTER TABLE `pengajuan_seminar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_seminar_kelompok_id_index` (`kelompok_id`),
  ADD KEY `pengajuan_seminar_pembimbing_id_index` (`pembimbing_id`),
  ADD KEY `pengajuan_seminar_status_index` (`status`);

--
-- Indeks untuk tabel `pengajuan_seminar_files`
--
ALTER TABLE `pengajuan_seminar_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_seminar_files_pengajuan_seminar_id_foreign` (`pengajuan_seminar_id`);

--
-- Indeks untuk tabel `penguji`
--
ALTER TABLE `penguji`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penguji_kelompok_id_foreign` (`kelompok_id`);

--
-- Indeks untuk tabel `pengumpulan_tugas`
--
ALTER TABLE `pengumpulan_tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengumpulan_tugas_kelompok_id_foreign` (`kelompok_id`),
  ADD KEY `pengumpulan_tugas_tugas_id_foreign` (`tugas_id`);

--
-- Indeks untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengumuman_kpa_id_foreign` (`KPA_id`),
  ADD KEY `pengumuman_prodi_id_foreign` (`prodi_id`),
  ADD KEY `pengumuman_tm_id_foreign` (`TM_id`);

--
-- Indeks untuk tabel `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `request_bimbingan`
--
ALTER TABLE `request_bimbingan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_bimbingan_ruangan_id_foreign` (`ruangan_id`),
  ADD KEY `request_bimbingan_kelompok_id_index` (`kelompok_id`),
  ADD KEY `request_bimbingan_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_role_name_unique` (`role_name`);

--
-- Indeks untuk tabel `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tahun_masuk`
--
ALTER TABLE `tahun_masuk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tugas`
--
ALTER TABLE `tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tugas_kpa_id_foreign` (`KPA_id`),
  ADD KEY `tugas_prodi_id_foreign` (`prodi_id`),
  ADD KEY `tugas_tm_id_foreign` (`TM_id`);

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
-- AUTO_INCREMENT untuk tabel `artefaks`
--
ALTER TABLE `artefaks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `device_token`
--
ALTER TABLE `device_token`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=434;

--
-- AUTO_INCREMENT untuk tabel `dosen_roles`
--
ALTER TABLE `dosen_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `judul_proyek_akhir`
--
ALTER TABLE `judul_proyek_akhir`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kartu_bimbingan`
--
ALTER TABLE `kartu_bimbingan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori_pa`
--
ALTER TABLE `kategori_pa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kelompok`
--
ALTER TABLE `kelompok`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `kelompok_mahasiswa`
--
ALTER TABLE `kelompok_mahasiswa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10832;

--
-- AUTO_INCREMENT untuk tabel `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=277;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `nilai_administrasi`
--
ALTER TABLE `nilai_administrasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `nilai_bimbingan`
--
ALTER TABLE `nilai_bimbingan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `nilai_individu`
--
ALTER TABLE `nilai_individu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `nilai_kelompok`
--
ALTER TABLE `nilai_kelompok`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `nilai_mahasiswa`
--
ALTER TABLE `nilai_mahasiswa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT untuk tabel `nilai_matkul_mahasiswa`
--
ALTER TABLE `nilai_matkul_mahasiswa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `nilai_seminar`
--
ALTER TABLE `nilai_seminar`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pembimbing`
--
ALTER TABLE `pembimbing`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_seminar`
--
ALTER TABLE `pengajuan_seminar`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_seminar_files`
--
ALTER TABLE `pengajuan_seminar_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penguji`
--
ALTER TABLE `penguji`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengumpulan_tugas`
--
ALTER TABLE `pengumpulan_tugas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `request_bimbingan`
--
ALTER TABLE `request_bimbingan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT untuk tabel `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tahun_masuk`
--
ALTER TABLE `tahun_masuk`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tugas`
--
ALTER TABLE `tugas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dosen_roles`
--
ALTER TABLE `dosen_roles`
  ADD CONSTRAINT `dosen_roles_kpa_id_foreign` FOREIGN KEY (`KPA_id`) REFERENCES `kategori_pa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dosen_roles_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dosen_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dosen_roles_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dosen_roles_tm_id_foreign` FOREIGN KEY (`TM_id`) REFERENCES `tahun_masuk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_kpa_id_foreign` FOREIGN KEY (`KPA_id`) REFERENCES `kategori_pa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_ruangan_id_foreign` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_tm_id_foreign` FOREIGN KEY (`TM_id`) REFERENCES `tahun_masuk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `judul_proyek_akhir`
--
ALTER TABLE `judul_proyek_akhir`
  ADD CONSTRAINT `judul_proyek_akhir_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kartu_bimbingan`
--
ALTER TABLE `kartu_bimbingan`
  ADD CONSTRAINT `kartu_bimbingan_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kartu_bimbingan_pembimbing_id_foreign` FOREIGN KEY (`pembimbing_id`) REFERENCES `pembimbing` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kartu_bimbingan_request_bimbingan_id_foreign` FOREIGN KEY (`request_bimbingan_id`) REFERENCES `request_bimbingan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kelompok`
--
ALTER TABLE `kelompok`
  ADD CONSTRAINT `kelompok_kpa_id_foreign` FOREIGN KEY (`KPA_id`) REFERENCES `kategori_pa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kelompok_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kelompok_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kelompok_tm_id_foreign` FOREIGN KEY (`TM_id`) REFERENCES `tahun_masuk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kelompok_mahasiswa`
--
ALTER TABLE `kelompok_mahasiswa`
  ADD CONSTRAINT `kelompok_mahasiswa_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kelompok_mahasiswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `mahasiswa` (`user_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  ADD CONSTRAINT `mata_kuliah_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`);

--
-- Ketidakleluasaan untuk tabel `nilai_administrasi`
--
ALTER TABLE `nilai_administrasi`
  ADD CONSTRAINT `nilai_administrasi_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `nilai_bimbingan`
--
ALTER TABLE `nilai_bimbingan`
  ADD CONSTRAINT `nilai_bimbingan_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `nilai_individu`
--
ALTER TABLE `nilai_individu`
  ADD CONSTRAINT `nilai_individu_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_individu_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `nilai_kelompok`
--
ALTER TABLE `nilai_kelompok`
  ADD CONSTRAINT `nilai_kelompok_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_kelompok_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `nilai_mahasiswa`
--
ALTER TABLE `nilai_mahasiswa`
  ADD CONSTRAINT `nilai_mahasiswa_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `nilai_matkul_mahasiswa`
--
ALTER TABLE `nilai_matkul_mahasiswa`
  ADD CONSTRAINT `nilai_matkul_mahasiswa_kode_mk_foreign` FOREIGN KEY (`kode_mk`) REFERENCES `mata_kuliah` (`kode_mk`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_matkul_mahasiswa_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`user_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `nilai_seminar`
--
ALTER TABLE `nilai_seminar`
  ADD CONSTRAINT `nilai_seminar_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembimbing`
--
ALTER TABLE `pembimbing`
  ADD CONSTRAINT `pembimbing_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengajuan_seminar`
--
ALTER TABLE `pengajuan_seminar`
  ADD CONSTRAINT `pengajuan_seminar_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengajuan_seminar_pembimbing_id_foreign` FOREIGN KEY (`pembimbing_id`) REFERENCES `pembimbing` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengajuan_seminar_files`
--
ALTER TABLE `pengajuan_seminar_files`
  ADD CONSTRAINT `pengajuan_seminar_files_pengajuan_seminar_id_foreign` FOREIGN KEY (`pengajuan_seminar_id`) REFERENCES `pengajuan_seminar` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penguji`
--
ALTER TABLE `penguji`
  ADD CONSTRAINT `penguji_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengumpulan_tugas`
--
ALTER TABLE `pengumpulan_tugas`
  ADD CONSTRAINT `pengumpulan_tugas_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengumpulan_tugas_tugas_id_foreign` FOREIGN KEY (`tugas_id`) REFERENCES `tugas` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD CONSTRAINT `pengumuman_kpa_id_foreign` FOREIGN KEY (`KPA_id`) REFERENCES `kategori_pa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengumuman_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengumuman_tm_id_foreign` FOREIGN KEY (`TM_id`) REFERENCES `tahun_masuk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `request_bimbingan`
--
ALTER TABLE `request_bimbingan`
  ADD CONSTRAINT `request_bimbingan_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `kelompok` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_bimbingan_ruangan_id_foreign` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tugas`
--
ALTER TABLE `tugas`
  ADD CONSTRAINT `tugas_kpa_id_foreign` FOREIGN KEY (`KPA_id`) REFERENCES `kategori_pa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tugas_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tugas_tm_id_foreign` FOREIGN KEY (`TM_id`) REFERENCES `tahun_masuk` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
