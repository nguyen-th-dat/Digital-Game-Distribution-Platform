-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2026 at 06:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nen_tang_game_doc_lap_vi_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `bai_du_thi`
--

CREATE TABLE `bai_du_thi` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `cuoc_thi_id` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_nop_id` bigint(20) UNSIGNED NOT NULL,
  `trang_thai` enum('submitted','withdrawn','disqualified') NOT NULL DEFAULT 'submitted',
  `ngay_nop` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bai_viet_thao_luan`
--

CREATE TABLE `bai_viet_thao_luan` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `chu_de_id` bigint(20) UNSIGNED NOT NULL,
  `tac_gia_nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `bai_viet_cha_id` bigint(20) UNSIGNED DEFAULT NULL,
  `noi_dung` mediumtext NOT NULL,
  `trang_thai` enum('published','hidden','removed') NOT NULL DEFAULT 'published',
  `ngay_chinh_sua` datetime DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bai_viet_thao_luan`
--

INSERT INTO `bai_viet_thao_luan` (`ma`, `chu_de_id`, `tac_gia_nguoi_dung_id`, `bai_viet_cha_id`, `noi_dung`, `trang_thai`, `ngay_chinh_sua`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, 2, NULL, 'hello', 'published', NULL, '2026-10-30 04:12:33', '2026-10-30 04:12:33'),
(2, 2, 3, NULL, 'hello', 'published', NULL, '2026-10-30 04:31:59', '2026-10-30 04:31:59'),
(3, 2, 3, NULL, 'fgdg', 'published', NULL, '2026-10-30 04:32:16', '2026-10-30 04:32:16');

-- --------------------------------------------------------

--
-- Table structure for table `bao_cao_noi_dung`
--

CREATE TABLE `bao_cao_noi_dung` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_bao_cao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `loai_doi_tuong` enum('user','game','review','game_comment','devlog','community_topic','community_post') NOT NULL,
  `doi_tuong_id` bigint(20) UNSIGNED NOT NULL,
  `ma_ly_do` varchar(60) NOT NULL,
  `chi_tiet` mediumtext DEFAULT NULL,
  `trang_thai` enum('open','investigating','resolved','rejected') NOT NULL DEFAULT 'open',
  `nguoi_duoc_phan_cong_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_giai_quyet` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `binh_chon_bai_du_thi`
--

CREATE TABLE `binh_chon_bai_du_thi` (
  `bai_du_thi_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_binh_chon_id` bigint(20) UNSIGNED NOT NULL,
  `tieu_chi` varchar(80) NOT NULL DEFAULT 'overall',
  `diem_so` tinyint(3) UNSIGNED NOT NULL,
  `binh_luan` varchar(1000) DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `binh_chon_danh_gia`
--

CREATE TABLE `binh_chon_danh_gia` (
  `danh_gia_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `co_ich` tinyint(1) NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `binh_luan_nhat_ky`
--

CREATE TABLE `binh_luan_nhat_ky` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nhat_ky_phat_trien_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `binh_luan_cha_id` bigint(20) UNSIGNED DEFAULT NULL,
  `noi_dung` text NOT NULL,
  `trang_thai` enum('published','hidden','removed') NOT NULL DEFAULT 'published',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `binh_luan_tro_choi`
--

CREATE TABLE `binh_luan_tro_choi` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `binh_luan_cha_id` bigint(20) UNSIGNED DEFAULT NULL,
  `noi_dung` mediumtext NOT NULL,
  `trang_thai` enum('published','hidden','removed') NOT NULL DEFAULT 'published',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bo_suu_tap`
--

CREATE TABLE `bo_suu_tap` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_so_huu_id` bigint(20) UNSIGNED NOT NULL,
  `tieu_de` varchar(180) NOT NULL,
  `duong_dan_rut_gon` varchar(200) NOT NULL,
  `mo_ta` mediumtext DEFAULT NULL,
  `pham_vi_hien_thi` enum('public','unlisted','private') NOT NULL DEFAULT 'public',
  `bo_cuc` enum('grid','list') NOT NULL DEFAULT 'grid',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cai_dat_nguoi_dung`
--

CREATE TABLE `cai_dat_nguoi_dung` (
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `pham_vi_hien_thi_ho_so` enum('public','private') NOT NULL DEFAULT 'public',
  `hien_thi_game_so_huu` tinyint(1) NOT NULL DEFAULT 1,
  `cho_phep_nhan_tin` tinyint(1) NOT NULL DEFAULT 1,
  `email_cap_nhat_san_pham` tinyint(1) NOT NULL DEFAULT 1,
  `email_cap_nhat_cong_dong` tinyint(1) NOT NULL DEFAULT 1,
  `ngon_ngu_uu_tien` varchar(10) NOT NULL DEFAULT 'vi',
  `tien_te_uu_tien` char(3) NOT NULL DEFAULT 'VND',
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cai_dat_nguoi_dung`
--

INSERT INTO `cai_dat_nguoi_dung` (`nguoi_dung_id`, `pham_vi_hien_thi_ho_so`, `hien_thi_game_so_huu`, `cho_phep_nhan_tin`, `email_cap_nhat_san_pham`, `email_cap_nhat_cong_dong`, `ngon_ngu_uu_tien`, `tien_te_uu_tien`, `ngay_cap_nhat`) VALUES
(1, 'public', 1, 1, 1, 1, 'vi', 'VND', '2026-10-27 11:36:25'),
(2, 'public', 1, 1, 1, 1, 'vi', 'VND', '2026-10-30 04:00:28'),
(3, 'public', 1, 1, 1, 1, 'vi', 'VND', '2026-10-30 04:25:43'),
(4, 'public', 1, 1, 1, 1, 'vi', 'VND', '2026-10-30 04:33:19');

-- --------------------------------------------------------

--
-- Table structure for table `cai_dat_trang_tro_choi`
--

CREATE TABLE `cai_dat_trang_tro_choi` (
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `mau_nen` varchar(20) DEFAULT NULL,
  `mau_chu` varchar(20) DEFAULT NULL,
  `mau_lien_ket` varchar(20) DEFAULT NULL,
  `bo_cuc_trang` enum('default','wide','minimal') NOT NULL DEFAULT 'default',
  `bat_cong_dong` tinyint(1) NOT NULL DEFAULT 1,
  `hien_thi_nha_sang_tao` tinyint(1) NOT NULL DEFAULT 1,
  `css_tuy_chinh` mediumtext DEFAULT NULL,
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chien_dich_giam_gia`
--

CREATE TABLE `chien_dich_giam_gia` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED NOT NULL,
  `tieu_de` varchar(200) NOT NULL,
  `mo_ta` mediumtext DEFAULT NULL,
  `phan_tram_giam_gia` decimal(5,2) NOT NULL,
  `trang_thai` enum('draft','scheduled','active','ended','cancelled') NOT NULL DEFAULT 'draft',
  `ngay_bat_dau` datetime NOT NULL,
  `ngay_ket_thuc` datetime NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_chi_tra`
--

CREATE TABLE `chi_tiet_chi_tra` (
  `dot_chi_tra_id` bigint(20) UNSIGNED NOT NULL,
  `so_ke_toan_id` bigint(20) UNSIGNED NOT NULL,
  `so_tien` decimal(12,2) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_don_hang`
--

CREATE TABLE `chi_tiet_don_hang` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `don_hang_id` bigint(20) UNSIGNED NOT NULL,
  `loai_muc` enum('game','bundle') NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `goi_game_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nguoi_nhan_qua_id` bigint(20) UNSIGNED DEFAULT NULL,
  `trang_thai_qua` enum('not_gift','pending','claimed','cancelled') NOT NULL DEFAULT 'not_gift',
  `tieu_de_luc_mua` varchar(220) NOT NULL,
  `so_luong` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `don_gia` decimal(12,2) NOT NULL,
  `so_tien_giam` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tien_thue` decimal(12,2) NOT NULL DEFAULT 0.00,
  `thanh_tien_dong` decimal(12,2) NOT NULL,
  `ma_tien_te` char(3) NOT NULL
) ;

--
-- Dumping data for table `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`ma`, `don_hang_id`, `loai_muc`, `tro_choi_id`, `goi_game_id`, `nguoi_nhan_qua_id`, `trang_thai_qua`, `tieu_de_luc_mua`, `so_luong`, `don_gia`, `so_tien_giam`, `tien_thue`, `thanh_tien_dong`, `ma_tien_te`) VALUES
(1, 1, 'game', 1, NULL, NULL, 'not_gift', 'gfdg', 1, 0.00, 0.00, 0.00, 0.00, 'USD'),
(2, 2, 'game', 2, NULL, NULL, 'not_gift', 'trưởng nhóm nè', 1, 2.00, 0.00, 0.00, 2.00, 'USD');

-- --------------------------------------------------------

--
-- Table structure for table `chu_de_thao_luan`
--

CREATE TABLE `chu_de_thao_luan` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `dien_dan_id` bigint(20) UNSIGNED NOT NULL,
  `tac_gia_nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `tieu_de` varchar(220) NOT NULL,
  `noi_dung_mo_dau` mediumtext NOT NULL,
  `trang_thai` enum('published','hidden','removed') NOT NULL DEFAULT 'published',
  `da_ghim` tinyint(1) NOT NULL DEFAULT 0,
  `da_khoa` tinyint(1) NOT NULL DEFAULT 0,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chu_de_thao_luan`
--

INSERT INTO `chu_de_thao_luan` (`ma`, `dien_dan_id`, `tac_gia_nguoi_dung_id`, `tieu_de`, `noi_dung_mo_dau`, `trang_thai`, `da_ghim`, `da_khoa`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, 2, 'fix lỗi 404 game baba is you', 'fdsf', 'published', 0, 0, '2026-10-30 04:12:14', '2026-10-30 04:12:14'),
(2, 1, 2, 'fix lỗi 503 game baba is you', 'fsdfsf', 'published', 0, 0, '2026-10-30 04:17:23', '2026-10-30 04:17:23');

-- --------------------------------------------------------

--
-- Table structure for table `cuoc_thi_lam_game`
--

CREATE TABLE `cuoc_thi_lam_game` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED NOT NULL,
  `tieu_de` varchar(200) NOT NULL,
  `duong_dan_rut_gon` varchar(220) NOT NULL,
  `mo_ta` mediumtext DEFAULT NULL,
  `the_le` mediumtext DEFAULT NULL,
  `ngay_bat_dau` datetime NOT NULL,
  `han_nop_bai` datetime NOT NULL,
  `ngay_bat_dau_binh_chon` datetime DEFAULT NULL,
  `ngay_ket_thuc_binh_chon` datetime DEFAULT NULL,
  `trang_thai` enum('draft','published','active','judging','completed','cancelled') NOT NULL DEFAULT 'draft',
  `bat_binh_chon` tinyint(1) NOT NULL DEFAULT 0,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `danh_gia`
--

CREATE TABLE `danh_gia` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `diem_danh_gia` tinyint(3) UNSIGNED NOT NULL,
  `tieu_de` varchar(200) DEFAULT NULL,
  `noi_dung_danh_gia` mediumtext DEFAULT NULL,
  `da_xac_minh_so_huu` tinyint(1) NOT NULL DEFAULT 0,
  `trang_thai` enum('published','hidden','removed') NOT NULL DEFAULT 'published',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `danh_gia`
--

INSERT INTO `danh_gia` (`ma`, `tro_choi_id`, `nguoi_dung_id`, `diem_danh_gia`, `tieu_de`, `noi_dung_danh_gia`, `da_xac_minh_so_huu`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 2, 2, 5, '', 'tuyet voi', 0, 'published', '2026-10-30 04:01:37', '2026-10-30 04:01:37');

-- --------------------------------------------------------

--
-- Table structure for table `dien_dan`
--

CREATE TABLE `dien_dan` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `loai_dien_dan` enum('platform','game','jam') NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cuoc_thi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tieu_de` varchar(180) NOT NULL,
  `mo_ta` varchar(1000) DEFAULT NULL,
  `cong_khai` tinyint(1) NOT NULL DEFAULT 1,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `dien_dan`
--

INSERT INTO `dien_dan` (`ma`, `loai_dien_dan`, `tro_choi_id`, `cuoc_thi_id`, `tieu_de`, `mo_ta`, `cong_khai`, `ngay_tao`) VALUES
(1, 'platform', NULL, NULL, 'Hướng dẫn fix game', 'fdsf', 1, '2026-10-30 04:07:35');

-- --------------------------------------------------------

--
-- Table structure for table `don_hang`
--

CREATE TABLE `don_hang` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `ma_don_hang` varchar(40) NOT NULL,
  `nguoi_mua_id` bigint(20) UNSIGNED NOT NULL,
  `trang_thai` enum('pending','paid','partially_refunded','refunded','cancelled','failed') NOT NULL DEFAULT 'pending',
  `ma_tien_te` char(3) NOT NULL,
  `tong_tien_hang` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tong_tien_giam` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tong_tien_thue` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tong_thanh_toan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quoc_gia_thanh_toan` char(2) DEFAULT NULL,
  `ngay_dat_hang` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_thanh_toan` datetime DEFAULT NULL
) ;

--
-- Dumping data for table `don_hang`
--

INSERT INTO `don_hang` (`ma`, `ma_don_hang`, `nguoi_mua_id`, `trang_thai`, `ma_tien_te`, `tong_tien_hang`, `tong_tien_giam`, `tong_tien_thue`, `tong_thanh_toan`, `quoc_gia_thanh_toan`, `ngay_dat_hang`, `ngay_thanh_toan`) VALUES
(1, 'DEMO-F9F07298B5A5', 1, 'paid', 'USD', 0.00, 0.00, 0.00, 0.00, NULL, '2026-10-27 11:48:28', '2026-10-27 18:48:28'),
(2, 'DEMO-077C58E91F4D', 2, 'paid', 'USD', 2.00, 0.00, 0.00, 2.00, NULL, '2026-10-30 04:15:26', '2026-10-30 11:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `dot_chi_tra`
--

CREATE TABLE `dot_chi_tra` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `tai_khoan_nhan_tien_id` bigint(20) UNSIGNED NOT NULL,
  `so_tien` decimal(12,2) NOT NULL,
  `ma_tien_te` char(3) NOT NULL,
  `trang_thai` enum('requested','processing','paid','failed','cancelled') NOT NULL DEFAULT 'requested',
  `ma_chi_tra_nha_cung_cap` varchar(190) DEFAULT NULL,
  `ngay_yeu_cau` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_xu_ly` datetime DEFAULT NULL,
  `ly_do_that_bai` varchar(1000) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `gio_hang`
--

CREATE TABLE `gio_hang` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `trang_thai` enum('active','checked_out','abandoned') NOT NULL DEFAULT 'active',
  `ma_tien_te` char(3) NOT NULL DEFAULT 'USD',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gio_hang`
--

INSERT INTO `gio_hang` (`ma`, `nguoi_dung_id`, `trang_thai`, `ma_tien_te`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, 'active', 'USD', '2026-10-27 11:38:40', '2026-10-27 11:38:40'),
(2, 2, 'active', 'USD', '2026-10-30 04:07:02', '2026-10-30 04:07:02');

-- --------------------------------------------------------

--
-- Table structure for table `goi_game`
--

CREATE TABLE `goi_game` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED NOT NULL,
  `tieu_de` varchar(200) NOT NULL,
  `duong_dan_rut_gon` varchar(220) NOT NULL,
  `mo_ta` mediumtext DEFAULT NULL,
  `loai_goi_game` enum('creator_created','platform_curated') NOT NULL DEFAULT 'creator_created',
  `gia` decimal(12,2) NOT NULL DEFAULT 0.00,
  `ma_tien_te` char(3) NOT NULL DEFAULT 'USD',
  `trang_thai` enum('draft','published','hidden','ended') NOT NULL DEFAULT 'draft',
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `hanh_dong_kiem_duyet`
--

CREATE TABLE `hanh_dong_kiem_duyet` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_kiem_duyet_id` bigint(20) UNSIGNED NOT NULL,
  `loai_doi_tuong` enum('user','game','review','game_comment','devlog','community_topic','community_post') NOT NULL,
  `doi_tuong_id` bigint(20) UNSIGNED NOT NULL,
  `loai_hanh_dong` enum('warning','hide','remove','suspend','restore','other') NOT NULL,
  `ly_do` text DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hoan_tien`
--

CREATE TABLE `hoan_tien` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `thanh_toan_id` bigint(20) UNSIGNED NOT NULL,
  `chi_tiet_don_hang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nguoi_yeu_cau_id` bigint(20) UNSIGNED DEFAULT NULL,
  `so_tien` decimal(12,2) NOT NULL,
  `ma_tien_te` char(3) NOT NULL,
  `ly_do` text DEFAULT NULL,
  `trang_thai` enum('requested','approved','rejected','processing','completed','failed') NOT NULL DEFAULT 'requested',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_xu_ly` datetime DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `luot_dung_ma_giam_gia`
--

CREATE TABLE `luot_dung_ma_giam_gia` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `ma_giam_gia_id` bigint(20) UNSIGNED NOT NULL,
  `don_hang_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `so_tien_giam` decimal(12,2) NOT NULL,
  `ngay_su_dung` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `ma_giam_gia`
--

CREATE TABLE `ma_giam_gia` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ma_uu_dai` varchar(80) NOT NULL,
  `loai_giam_gia` enum('percent','fixed_amount') NOT NULL,
  `gia_tri_giam_gia` decimal(12,2) NOT NULL,
  `ma_tien_te` char(3) DEFAULT NULL,
  `so_luot_dung_toi_da` int(10) UNSIGNED DEFAULT NULL,
  `so_luot_da_dung` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ngay_bat_dau` datetime DEFAULT NULL,
  `ngay_ket_thuc` datetime DEFAULT NULL,
  `dang_hoat_dong` tinyint(1) NOT NULL DEFAULT 1,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `ma_truy_cap_tro_choi`
--

CREATE TABLE `ma_truy_cap_tro_choi` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_tao_ma_id` bigint(20) UNSIGNED NOT NULL,
  `ma_bam` char(64) NOT NULL,
  `trang_thai` enum('available','claimed','revoked','expired') NOT NULL DEFAULT 'available',
  `nguoi_nhan_ma_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_nhan_ma` datetime DEFAULT NULL,
  `ngay_het_han` datetime DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media_tro_choi`
--

CREATE TABLE `media_tro_choi` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `loai_media` enum('cover','thumbnail','screenshot','trailer','external_link','other') NOT NULL,
  `url_media` varchar(1500) NOT NULL,
  `chu_thich` varchar(300) DEFAULT NULL,
  `van_ban_thay_the` varchar(300) DEFAULT NULL,
  `thu_tu_sap_xep` int(11) NOT NULL DEFAULT 0,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media_tro_choi`
--

INSERT INTO `media_tro_choi` (`ma`, `tro_choi_id`, `loai_media`, `url_media`, `chu_thich`, `van_ban_thay_the`, `thu_tu_sap_xep`, `ngay_tao`) VALUES
(1, 2, 'cover', '/demo/BTT/public/uploads/game-covers/2d6ce37269abb5156daacd953b40ac08.webp', '', 'trưởng nhóm nè', 0, '2026-10-27 12:01:24');

-- --------------------------------------------------------

--
-- Table structure for table `muc_gio_hang`
--

CREATE TABLE `muc_gio_hang` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `gio_hang_id` bigint(20) UNSIGNED NOT NULL,
  `loai_muc` enum('game','bundle') NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `goi_game_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_them` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `nen_tang`
--

CREATE TABLE `nen_tang` (
  `ma` smallint(5) UNSIGNED NOT NULL,
  `ma_nen_tang` varchar(40) NOT NULL,
  `ten_nen_tang` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nen_tang`
--

INSERT INTO `nen_tang` (`ma`, `ma_nen_tang`, `ten_nen_tang`) VALUES
(1, 'windows', 'Windows'),
(2, 'linux', 'Linux'),
(3, 'macos', 'macOS'),
(4, 'android', 'Android'),
(5, 'ios', 'iOS'),
(6, 'browser', 'Trình duyệt web'),
(7, 'web3d', 'WebGL / WebGPU'),
(8, 'other', 'Khác');

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `ten_dang_nhap` varchar(50) NOT NULL,
  `email` varchar(254) NOT NULL,
  `mat_khau_da_bam` varchar(255) NOT NULL,
  `ten_hien_thi` varchar(100) NOT NULL,
  `gioi_thieu` text DEFAULT NULL,
  `url_anh_dai_dien` varchar(1000) DEFAULT NULL,
  `url_anh_bia` varchar(1000) DEFAULT NULL,
  `url_website` varchar(500) DEFAULT NULL,
  `ma_quoc_gia` char(2) DEFAULT NULL,
  `ngay_xac_minh_email` datetime DEFAULT NULL,
  `trang_thai` enum('active','suspended','deleted') NOT NULL DEFAULT 'active',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`ma`, `ten_dang_nhap`, `email`, `mat_khau_da_bam`, `ten_hien_thi`, `gioi_thieu`, `url_anh_dai_dien`, `url_anh_bia`, `url_website`, `ma_quoc_gia`, `ngay_xac_minh_email`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 'zaloi', 'h@gmail.com', '$2y$10$pHmKSwZJqGQBlOQZOFxou.Zse8QXZ8UkRgRNEXh92Fzlff8ukhlfi', 'zaloi', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-27 11:36:25', '2026-10-27 11:36:25'),
(2, 'ppp', 'c@gmail.com', '$2y$10$MdbiJS4FXL5QeQ5IegOhbOYkClae.PXMOntnTb5h2YnhoEQyVdt9m', 'ppp', NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2026-10-30 04:00:28', '2026-10-30 04:00:28'),
(3, 'ugu', 'bn@gmail.com', '$2y$10$1wMjUUiHZ5vKsXsW8g.aruL2Ro0u1GhnNps0zQkEEX4WjV2xx86ae', 'uugugh', NULL, '/demo/BTT/avatars/3', NULL, NULL, NULL, NULL, 'active', '2026-10-30 04:25:43', '2026-10-30 04:25:43'),
(4, 'lll', 'l@gmail.com', '$2y$10$2G61mXn5ZJva3PkBdAFOEedE2zAYWGUi10riECZUSYvtYAaI0yiLO', 'lll', NULL, '/demo/BTT/avatars/4', NULL, NULL, NULL, NULL, 'active', '2026-10-30 04:33:19', '2026-10-30 04:33:19');

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung_vai_tro`
--

CREATE TABLE `nguoi_dung_vai_tro` (
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `vai_tro_id` smallint(5) UNSIGNED NOT NULL,
  `ngay_phan_vai` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoi_dung_vai_tro`
--

INSERT INTO `nguoi_dung_vai_tro` (`nguoi_dung_id`, `vai_tro_id`, `ngay_phan_vai`) VALUES
(1, 1, '2026-10-27 11:36:25'),
(2, 1, '2026-10-30 04:00:28'),
(3, 1, '2026-10-30 04:25:43'),
(4, 1, '2026-10-30 04:33:19');

-- --------------------------------------------------------

--
-- Table structure for table `nhan`
--

CREATE TABLE `nhan` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `ten` varchar(80) NOT NULL,
  `duong_dan_rut_gon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nhat_ky_he_thong`
--

CREATE TABLE `nhat_ky_he_thong` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ma_hanh_dong` varchar(100) NOT NULL,
  `loai_thuc_the` varchar(80) DEFAULT NULL,
  `thuc_the_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dia_chi_ip` varchar(45) DEFAULT NULL,
  `trinh_duyet_nguoi_dung` varchar(500) DEFAULT NULL,
  `du_lieu_bo_sung` text DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nhat_ky_phat_trien`
--

CREATE TABLE `nhat_ky_phat_trien` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `tac_gia_nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `tieu_de` varchar(200) NOT NULL,
  `duong_dan_rut_gon` varchar(220) NOT NULL,
  `noi_dung` mediumtext NOT NULL,
  `trang_thai` enum('draft','published','hidden') NOT NULL DEFAULT 'draft',
  `ngay_cong_bo` datetime DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phien_ban_phat_hanh`
--

CREATE TABLE `phien_ban_phat_hanh` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `ten_phien_ban` varchar(60) NOT NULL,
  `kenh_phat_hanh` enum('stable','beta','alpha','preview') NOT NULL DEFAULT 'stable',
  `ghi_chu_phat_hanh` mediumtext DEFAULT NULL,
  `cong_khai` tinyint(1) NOT NULL DEFAULT 1,
  `ngay_phat_hanh` datetime DEFAULT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quyen_so_huu_tro_choi`
--

CREATE TABLE `quyen_so_huu_tro_choi` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `chi_tiet_don_hang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `loai_nhan_quyen` enum('purchase','bundle','gift','free_claim','access_key','admin_grant') NOT NULL,
  `trang_thai` enum('active','revoked','refunded') NOT NULL DEFAULT 'active',
  `ngay_nhan_quyen` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_thu_hoi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quyen_so_huu_tro_choi`
--

INSERT INTO `quyen_so_huu_tro_choi` (`ma`, `nguoi_dung_id`, `tro_choi_id`, `chi_tiet_don_hang_id`, `loai_nhan_quyen`, `trang_thai`, `ngay_nhan_quyen`, `ngay_thu_hoi`) VALUES
(1, 1, 1, 1, 'free_claim', 'active', '2026-10-27 11:48:28', NULL),
(2, 2, 2, 2, 'purchase', 'active', '2026-10-30 04:15:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `so_ke_toan_tac_gia`
--

CREATE TABLE `so_ke_toan_tac_gia` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `chi_tiet_don_hang_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `phan_tram_huong_doanh_thu` decimal(5,2) NOT NULL,
  `doanh_thu_truoc_phi` decimal(12,2) NOT NULL,
  `phi_nen_tang` decimal(12,2) NOT NULL DEFAULT 0.00,
  `thue_da_khau_tru` decimal(12,2) NOT NULL DEFAULT 0.00,
  `so_tien_thuc_nhan` decimal(12,2) NOT NULL,
  `ma_tien_te` char(3) NOT NULL,
  `trang_thai` enum('pending','available','held','paid','reversed') NOT NULL DEFAULT 'pending',
  `ngay_co_the_chi_tra` datetime DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tac_gia_tro_choi`
--

CREATE TABLE `tac_gia_tro_choi` (
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `phan_tram_huong_doanh_thu` decimal(5,2) NOT NULL,
  `thu_tu_hien_thi` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `ngay_them` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `tac_gia_tro_choi`
--

INSERT INTO `tac_gia_tro_choi` (`tro_choi_id`, `nguoi_dung_id`, `phan_tram_huong_doanh_thu`, `thu_tu_hien_thi`, `ngay_them`) VALUES
(1, 1, 100.00, 0, '2026-10-27 11:38:58'),
(2, 1, 100.00, 0, '2026-10-27 12:01:24');

-- --------------------------------------------------------

--
-- Table structure for table `tai_khoan_nhan_tien_nguoi_dung`
--

CREATE TABLE `tai_khoan_nhan_tien_nguoi_dung` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `nha_cung_cap` varchar(80) NOT NULL,
  `ma_tai_khoan_ben_ngoai` varchar(255) NOT NULL,
  `ten_tai_khoan` varchar(120) DEFAULT NULL,
  `ma_tien_te` char(3) NOT NULL,
  `mac_dinh` tinyint(1) NOT NULL DEFAULT 0,
  `trang_thai` enum('pending_verification','active','disabled') NOT NULL DEFAULT 'pending_verification',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `don_hang_id` bigint(20) UNSIGNED NOT NULL,
  `nha_cung_cap` varchar(60) NOT NULL,
  `ma_giao_dich_nha_cung_cap` varchar(190) DEFAULT NULL,
  `so_tien` decimal(12,2) NOT NULL,
  `ma_tien_te` char(3) NOT NULL,
  `trang_thai` enum('initiated','pending','succeeded','failed','cancelled') NOT NULL DEFAULT 'initiated',
  `ma_phan_hoi_nha_cung_cap` varchar(100) DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_thanh_toan` datetime DEFAULT NULL
) ;

--
-- Dumping data for table `thanh_toan`
--

INSERT INTO `thanh_toan` (`ma`, `don_hang_id`, `nha_cung_cap`, `ma_giao_dich_nha_cung_cap`, `so_tien`, `ma_tien_te`, `trang_thai`, `ma_phan_hoi_nha_cung_cap`, `ngay_tao`, `ngay_thanh_toan`) VALUES
(1, 1, 'demo', 'DEMO-CD96E51AF45D9A36B0D0EE4E37C3D589', 0.00, 'USD', 'succeeded', 'demo-checkout', '2026-10-27 11:48:28', '2026-10-27 18:48:28'),
(2, 2, 'demo', 'DEMO-1EB5EC0061B9CD299C4656D9E919E54F', 2.00, 'USD', 'succeeded', 'demo-checkout', '2026-10-30 04:15:26', '2026-10-30 11:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `thanh_vien_cuoc_thi`
--

CREATE TABLE `thanh_vien_cuoc_thi` (
  `cuoc_thi_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `vai_tro_thanh_vien` enum('host','admin','judge','moderator') NOT NULL DEFAULT 'moderator',
  `ngay_tham_gia` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `theo_doi_nguoi_dung`
--

CREATE TABLE `theo_doi_nguoi_dung` (
  `nguoi_theo_doi_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_duoc_theo_doi_id` bigint(20) UNSIGNED NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `theo_doi_tro_choi`
--

CREATE TABLE `theo_doi_tro_choi` (
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `thong_bao_cap_nhat` tinyint(1) NOT NULL DEFAULT 1,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `the_loai`
--

CREATE TABLE `the_loai` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `ten` varchar(100) NOT NULL,
  `duong_dan_rut_gon` varchar(120) NOT NULL,
  `mo_ta` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `the_loai`
--

INSERT INTO `the_loai` (`ma`, `ten`, `duong_dan_rut_gon`, `mo_ta`) VALUES
(1, 'Hành động', 'action', 'Game hành động'),
(2, 'Phiêu lưu', 'adventure', 'Game phiêu lưu'),
(3, 'Nhập vai', 'role-playing', 'Game nhập vai'),
(4, 'Kinh dị', 'horror', 'Game kinh dị'),
(5, 'Giải đố', 'puzzle', 'Game giải đố'),
(6, 'Chiến thuật', 'strategy', 'Game chiến thuật'),
(7, 'Mô phỏng', 'simulation', 'Game mô phỏng'),
(8, 'Đi cảnh', 'platformer', 'Game đi cảnh'),
(9, 'Tiểu thuyết tương tác', 'visual-novel', 'Tiểu thuyết tương tác'),
(10, 'Khác', 'other', 'Thể loại khác');

-- --------------------------------------------------------

--
-- Table structure for table `thong_bao`
--

CREATE TABLE `thong_bao` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `loai_thong_bao` varchar(60) NOT NULL,
  `tieu_de` varchar(200) NOT NULL,
  `noi_dung_thong_bao` text DEFAULT NULL,
  `url_dich` varchar(1000) DEFAULT NULL,
  `da_doc` tinyint(1) NOT NULL DEFAULT 0,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_doc` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thong_bao`
--

INSERT INTO `thong_bao` (`ma`, `nguoi_dung_id`, `loai_thong_bao`, `tieu_de`, `noi_dung_thong_bao`, `url_dich`, `da_doc`, `ngay_tao`, `ngay_doc`) VALUES
(1, 2, 'review', 'Bài đánh giá của bạn đã được đăng', 'Bài đánh giá #1', '/games/ytryyyy', 0, '2026-10-30 04:01:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `thong_ke_tro_choi_hang_ngay`
--

CREATE TABLE `thong_ke_tro_choi_hang_ngay` (
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `ngay_thong_ke` date NOT NULL,
  `luot_xem_trang` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `luot_nhan_tai_xuong` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `so_luot_mua` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `doanh_thu_gop` decimal(14,2) NOT NULL DEFAULT 0.00,
  `luot_them_yeu_thich` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `so_luong_binh_luan` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ;

-- --------------------------------------------------------

--
-- Table structure for table `tro_choi`
--

CREATE TABLE `tro_choi` (
  `ma` bigint(20) UNSIGNED NOT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED NOT NULL,
  `tieu_de` varchar(200) NOT NULL,
  `duong_dan_rut_gon` varchar(220) NOT NULL,
  `mo_ta_ngan` varchar(500) DEFAULT NULL,
  `mo_ta` mediumtext DEFAULT NULL,
  `loai_du_an` enum('game','asset','tool','soundtrack','comic','book','physical','other') NOT NULL DEFAULT 'game',
  `trang_thai` enum('draft','published','unlisted','hidden','under_review','suspended') NOT NULL DEFAULT 'draft',
  `trang_thai_phat_hanh` enum('prototype','in_development','early_access','released','cancelled') NOT NULL DEFAULT 'in_development',
  `mo_hinh_gia` enum('free','paid','pay_what_you_want','donation') NOT NULL DEFAULT 'free',
  `gia_toi_thieu` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gia_de_xuat` decimal(12,2) DEFAULT NULL,
  `ma_tien_te` char(3) NOT NULL DEFAULT 'USD',
  `phan_loai_do_tuoi` varchar(40) DEFAULT NULL,
  `ten_giay_phep` varchar(120) DEFAULT NULL,
  `url_choi_ngoai` varchar(1000) DEFAULT NULL,
  `cho_phep_binh_luan` tinyint(1) NOT NULL DEFAULT 1,
  `cho_phep_danh_gia` tinyint(1) NOT NULL DEFAULT 1,
  `ngay_cong_bo` datetime DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `tro_choi`
--

INSERT INTO `tro_choi` (`ma`, `nguoi_tao_id`, `tieu_de`, `duong_dan_rut_gon`, `mo_ta_ngan`, `mo_ta`, `loai_du_an`, `trang_thai`, `trang_thai_phat_hanh`, `mo_hinh_gia`, `gia_toi_thieu`, `gia_de_xuat`, `ma_tien_te`, `phan_loai_do_tuoi`, `ten_giay_phep`, `url_choi_ngoai`, `cho_phep_binh_luan`, `cho_phep_danh_gia`, `ngay_cong_bo`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, 'gfdg', 'gdfg', 'g', 'gfdgd', 'game', 'published', 'in_development', 'free', 0.00, NULL, 'USD', NULL, NULL, NULL, 1, 1, '2026-10-27 18:38:58', '2026-10-27 11:38:58', '2026-10-27 11:38:58'),
(2, 1, 'trưởng nhóm nè', 'ytryyyy', 'tểtt', 'pppppppppppp', 'game', 'published', 'in_development', 'paid', 2.00, NULL, 'USD', NULL, NULL, NULL, 1, 1, '2026-10-27 19:01:24', '2026-10-27 12:01:24', '2026-10-27 12:01:24');

-- --------------------------------------------------------

--
-- Table structure for table `tro_choi_bo_suu_tap`
--

CREATE TABLE `tro_choi_bo_suu_tap` (
  `bo_suu_tap_id` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_them_id` bigint(20) UNSIGNED NOT NULL,
  `vi_tri` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tro_choi_giam_gia`
--

CREATE TABLE `tro_choi_giam_gia` (
  `chien_dich_id` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tro_choi_goi_game`
--

CREATE TABLE `tro_choi_goi_game` (
  `goi_game_id` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `vi_tri` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tro_choi_nen_tang`
--

CREATE TABLE `tro_choi_nen_tang` (
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `nen_tang_id` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tro_choi_nen_tang`
--

INSERT INTO `tro_choi_nen_tang` (`tro_choi_id`, `nen_tang_id`) VALUES
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 8),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 8);

-- --------------------------------------------------------

--
-- Table structure for table `tro_choi_nhan`
--

CREATE TABLE `tro_choi_nhan` (
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `nhan_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tro_choi_the_loai`
--

CREATE TABLE `tro_choi_the_loai` (
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `the_loai_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tro_choi_the_loai`
--

INSERT INTO `tro_choi_the_loai` (`tro_choi_id`, `the_loai_id`) VALUES
(1, 1),
(1, 4),
(1, 5),
(1, 7),
(1, 8),
(1, 10),
(2, 1),
(2, 5),
(2, 6),
(2, 8),
(2, 10);

-- --------------------------------------------------------

--
-- Table structure for table `tro_choi_yeu_thich`
--

CREATE TABLE `tro_choi_yeu_thich` (
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `tro_choi_id` bigint(20) UNSIGNED NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tuong_tac_bai_viet`
--

CREATE TABLE `tuong_tac_bai_viet` (
  `bai_viet_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `phan_ung` enum('like','helpful','love','laugh') NOT NULL DEFAULT 'like',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vai_tro`
--

CREATE TABLE `vai_tro` (
  `ma` smallint(5) UNSIGNED NOT NULL,
  `ma_vai_tro` varchar(40) NOT NULL,
  `ten_vai_tro` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vai_tro`
--

INSERT INTO `vai_tro` (`ma`, `ma_vai_tro`, `ten_vai_tro`) VALUES
(1, 'player', 'Người chơi'),
(2, 'developer', 'Nhà phát triển'),
(3, 'moderator', 'Điều hành viên'),
(4, 'admin', 'Quản trị viên');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bai_du_thi`
--
ALTER TABLE `bai_du_thi`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_jam_game` (`cuoc_thi_id`,`tro_choi_id`),
  ADD KEY `tro_choi_id` (`tro_choi_id`),
  ADD KEY `nguoi_nop_id` (`nguoi_nop_id`);

--
-- Indexes for table `bai_viet_thao_luan`
--
ALTER TABLE `bai_viet_thao_luan`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `tac_gia_nguoi_dung_id` (`tac_gia_nguoi_dung_id`),
  ADD KEY `idx_community_posts_topic_date` (`chu_de_id`,`ngay_tao`),
  ADD KEY `idx_community_posts_parent` (`bai_viet_cha_id`);

--
-- Indexes for table `bao_cao_noi_dung`
--
ALTER TABLE `bao_cao_noi_dung`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `nguoi_bao_cao_id` (`nguoi_bao_cao_id`),
  ADD KEY `nguoi_duoc_phan_cong_id` (`nguoi_duoc_phan_cong_id`),
  ADD KEY `idx_reports_status_date` (`trang_thai`,`ngay_tao`),
  ADD KEY `idx_reports_target` (`loai_doi_tuong`,`doi_tuong_id`);

--
-- Indexes for table `binh_chon_bai_du_thi`
--
ALTER TABLE `binh_chon_bai_du_thi`
  ADD PRIMARY KEY (`bai_du_thi_id`,`nguoi_binh_chon_id`,`tieu_chi`),
  ADD KEY `nguoi_binh_chon_id` (`nguoi_binh_chon_id`);

--
-- Indexes for table `binh_chon_danh_gia`
--
ALTER TABLE `binh_chon_danh_gia`
  ADD PRIMARY KEY (`danh_gia_id`,`nguoi_dung_id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`);

--
-- Indexes for table `binh_luan_nhat_ky`
--
ALTER TABLE `binh_luan_nhat_ky`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`),
  ADD KEY `binh_luan_cha_id` (`binh_luan_cha_id`),
  ADD KEY `idx_devlog_comments_post` (`nhat_ky_phat_trien_id`,`ngay_tao`);

--
-- Indexes for table `binh_luan_tro_choi`
--
ALTER TABLE `binh_luan_tro_choi`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`),
  ADD KEY `idx_game_comments_game_date` (`tro_choi_id`,`ngay_tao`),
  ADD KEY `idx_game_comments_parent` (`binh_luan_cha_id`);

--
-- Indexes for table `bo_suu_tap`
--
ALTER TABLE `bo_suu_tap`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_collection_owner_slug` (`nguoi_dung_so_huu_id`,`duong_dan_rut_gon`);

--
-- Indexes for table `cai_dat_nguoi_dung`
--
ALTER TABLE `cai_dat_nguoi_dung`
  ADD PRIMARY KEY (`nguoi_dung_id`);

--
-- Indexes for table `cai_dat_trang_tro_choi`
--
ALTER TABLE `cai_dat_trang_tro_choi`
  ADD PRIMARY KEY (`tro_choi_id`);

--
-- Indexes for table `chien_dich_giam_gia`
--
ALTER TABLE `chien_dich_giam_gia`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `nguoi_tao_id` (`nguoi_tao_id`),
  ADD KEY `idx_discount_campaign_dates` (`trang_thai`,`ngay_bat_dau`,`ngay_ket_thuc`);

--
-- Indexes for table `chi_tiet_chi_tra`
--
ALTER TABLE `chi_tiet_chi_tra`
  ADD PRIMARY KEY (`dot_chi_tra_id`,`so_ke_toan_id`),
  ADD KEY `so_ke_toan_id` (`so_ke_toan_id`);

--
-- Indexes for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `nguoi_nhan_qua_id` (`nguoi_nhan_qua_id`),
  ADD KEY `idx_order_items_order` (`don_hang_id`),
  ADD KEY `idx_order_items_game` (`tro_choi_id`),
  ADD KEY `idx_order_items_bundle` (`goi_game_id`);

--
-- Indexes for table `chu_de_thao_luan`
--
ALTER TABLE `chu_de_thao_luan`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `tac_gia_nguoi_dung_id` (`tac_gia_nguoi_dung_id`),
  ADD KEY `idx_community_topics_board` (`dien_dan_id`,`da_ghim`,`ngay_tao`);

--
-- Indexes for table `cuoc_thi_lam_game`
--
ALTER TABLE `cuoc_thi_lam_game`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `duong_dan_rut_gon` (`duong_dan_rut_gon`),
  ADD KEY `nguoi_tao_id` (`nguoi_tao_id`),
  ADD KEY `idx_jams_status_dates` (`trang_thai`,`ngay_bat_dau`,`han_nop_bai`);

--
-- Indexes for table `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_reviews_user_game` (`nguoi_dung_id`,`tro_choi_id`),
  ADD KEY `idx_reviews_game_date` (`tro_choi_id`,`ngay_tao`);

--
-- Indexes for table `dien_dan`
--
ALTER TABLE `dien_dan`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_board_game` (`tro_choi_id`),
  ADD UNIQUE KEY `uq_board_jam` (`cuoc_thi_id`);

--
-- Indexes for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `ma_don_hang` (`ma_don_hang`),
  ADD KEY `idx_orders_buyer_date` (`nguoi_mua_id`,`ngay_dat_hang`),
  ADD KEY `idx_orders_status_date` (`trang_thai`,`ngay_dat_hang`);

--
-- Indexes for table `dot_chi_tra`
--
ALTER TABLE `dot_chi_tra`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `tai_khoan_nhan_tien_id` (`tai_khoan_nhan_tien_id`),
  ADD KEY `idx_payouts_user_status` (`nguoi_dung_id`,`trang_thai`,`ngay_yeu_cau`);

--
-- Indexes for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_carts_user` (`nguoi_dung_id`);

--
-- Indexes for table `goi_game`
--
ALTER TABLE `goi_game`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `duong_dan_rut_gon` (`duong_dan_rut_gon`),
  ADD KEY `nguoi_tao_id` (`nguoi_tao_id`),
  ADD KEY `idx_bundles_status_dates` (`trang_thai`,`ngay_bat_dau`,`ngay_ket_thuc`);

--
-- Indexes for table `hanh_dong_kiem_duyet`
--
ALTER TABLE `hanh_dong_kiem_duyet`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `idx_moderation_target` (`loai_doi_tuong`,`doi_tuong_id`),
  ADD KEY `idx_moderation_moderator_date` (`nguoi_kiem_duyet_id`,`ngay_tao`);

--
-- Indexes for table `hoan_tien`
--
ALTER TABLE `hoan_tien`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `thanh_toan_id` (`thanh_toan_id`),
  ADD KEY `chi_tiet_don_hang_id` (`chi_tiet_don_hang_id`),
  ADD KEY `nguoi_yeu_cau_id` (`nguoi_yeu_cau_id`),
  ADD KEY `idx_refunds_status_date` (`trang_thai`,`ngay_tao`);

--
-- Indexes for table `luot_dung_ma_giam_gia`
--
ALTER TABLE `luot_dung_ma_giam_gia`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_coupon_order` (`ma_giam_gia_id`,`don_hang_id`),
  ADD KEY `don_hang_id` (`don_hang_id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`);

--
-- Indexes for table `ma_giam_gia`
--
ALTER TABLE `ma_giam_gia`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `ma_uu_dai` (`ma_uu_dai`),
  ADD KEY `idx_coupon_creator` (`nguoi_tao_id`);

--
-- Indexes for table `ma_truy_cap_tro_choi`
--
ALTER TABLE `ma_truy_cap_tro_choi`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `ma_bam` (`ma_bam`),
  ADD KEY `nguoi_tao_ma_id` (`nguoi_tao_ma_id`),
  ADD KEY `nguoi_nhan_ma_id` (`nguoi_nhan_ma_id`),
  ADD KEY `idx_access_keys_game_status` (`tro_choi_id`,`trang_thai`);

--
-- Indexes for table `media_tro_choi`
--
ALTER TABLE `media_tro_choi`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `idx_game_media_order` (`tro_choi_id`,`loai_media`,`thu_tu_sap_xep`);

--
-- Indexes for table `muc_gio_hang`
--
ALTER TABLE `muc_gio_hang`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_cart_game` (`gio_hang_id`,`tro_choi_id`),
  ADD UNIQUE KEY `uq_cart_bundle` (`gio_hang_id`,`goi_game_id`),
  ADD KEY `tro_choi_id` (`tro_choi_id`),
  ADD KEY `goi_game_id` (`goi_game_id`);

--
-- Indexes for table `nen_tang`
--
ALTER TABLE `nen_tang`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `ma_nen_tang` (`ma_nen_tang`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_users_username` (`ten_dang_nhap`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_status` (`trang_thai`);

--
-- Indexes for table `nguoi_dung_vai_tro`
--
ALTER TABLE `nguoi_dung_vai_tro`
  ADD PRIMARY KEY (`nguoi_dung_id`,`vai_tro_id`),
  ADD KEY `vai_tro_id` (`vai_tro_id`);

--
-- Indexes for table `nhan`
--
ALTER TABLE `nhan`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_tags_name` (`ten`),
  ADD UNIQUE KEY `uq_tags_slug` (`duong_dan_rut_gon`);

--
-- Indexes for table `nhat_ky_he_thong`
--
ALTER TABLE `nhat_ky_he_thong`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `idx_audit_user_date` (`nguoi_dung_id`,`ngay_tao`),
  ADD KEY `idx_audit_entity` (`loai_thuc_the`,`thuc_the_id`);

--
-- Indexes for table `nhat_ky_phat_trien`
--
ALTER TABLE `nhat_ky_phat_trien`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_devlog_game_slug` (`tro_choi_id`,`duong_dan_rut_gon`),
  ADD KEY `tac_gia_nguoi_dung_id` (`tac_gia_nguoi_dung_id`),
  ADD KEY `idx_devlogs_game_published` (`tro_choi_id`,`trang_thai`,`ngay_cong_bo`);

--
-- Indexes for table `phien_ban_phat_hanh`
--
ALTER TABLE `phien_ban_phat_hanh`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_game_release_version` (`tro_choi_id`,`ten_phien_ban`,`kenh_phat_hanh`),
  ADD KEY `nguoi_tao_id` (`nguoi_tao_id`),
  ADD KEY `idx_releases_game_date` (`tro_choi_id`,`ngay_phat_hanh`);

--
-- Indexes for table `quyen_so_huu_tro_choi`
--
ALTER TABLE `quyen_so_huu_tro_choi`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_ownership_user_game` (`nguoi_dung_id`,`tro_choi_id`),
  ADD KEY `idx_ownership_game` (`tro_choi_id`),
  ADD KEY `idx_ownership_order_item` (`chi_tiet_don_hang_id`);

--
-- Indexes for table `so_ke_toan_tac_gia`
--
ALTER TABLE `so_ke_toan_tac_gia`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_ledger_order_author` (`chi_tiet_don_hang_id`,`nguoi_dung_id`),
  ADD KEY `idx_creator_ledger_user_status` (`nguoi_dung_id`,`trang_thai`,`ngay_tao`);

--
-- Indexes for table `tac_gia_tro_choi`
--
ALTER TABLE `tac_gia_tro_choi`
  ADD PRIMARY KEY (`tro_choi_id`,`nguoi_dung_id`),
  ADD KEY `idx_game_authors_user` (`nguoi_dung_id`,`tro_choi_id`),
  ADD KEY `idx_game_authors_order` (`tro_choi_id`,`thu_tu_hien_thi`);

--
-- Indexes for table `tai_khoan_nhan_tien_nguoi_dung`
--
ALTER TABLE `tai_khoan_nhan_tien_nguoi_dung`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `idx_payout_accounts_user` (`nguoi_dung_id`,`trang_thai`);

--
-- Indexes for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_payment_provider_tx` (`nha_cung_cap`,`ma_giao_dich_nha_cung_cap`),
  ADD KEY `idx_payments_order_status` (`don_hang_id`,`trang_thai`);

--
-- Indexes for table `thanh_vien_cuoc_thi`
--
ALTER TABLE `thanh_vien_cuoc_thi`
  ADD PRIMARY KEY (`cuoc_thi_id`,`nguoi_dung_id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`);

--
-- Indexes for table `theo_doi_nguoi_dung`
--
ALTER TABLE `theo_doi_nguoi_dung`
  ADD PRIMARY KEY (`nguoi_theo_doi_id`,`nguoi_duoc_theo_doi_id`),
  ADD KEY `nguoi_duoc_theo_doi_id` (`nguoi_duoc_theo_doi_id`);

--
-- Indexes for table `theo_doi_tro_choi`
--
ALTER TABLE `theo_doi_tro_choi`
  ADD PRIMARY KEY (`nguoi_dung_id`,`tro_choi_id`),
  ADD KEY `tro_choi_id` (`tro_choi_id`);

--
-- Indexes for table `the_loai`
--
ALTER TABLE `the_loai`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_categories_name` (`ten`),
  ADD UNIQUE KEY `uq_categories_slug` (`duong_dan_rut_gon`);

--
-- Indexes for table `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD PRIMARY KEY (`ma`),
  ADD KEY `idx_notifications_user_read` (`nguoi_dung_id`,`da_doc`,`ngay_tao`);

--
-- Indexes for table `thong_ke_tro_choi_hang_ngay`
--
ALTER TABLE `thong_ke_tro_choi_hang_ngay`
  ADD PRIMARY KEY (`tro_choi_id`,`ngay_thong_ke`);

--
-- Indexes for table `tro_choi`
--
ALTER TABLE `tro_choi`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `uq_games_slug` (`duong_dan_rut_gon`),
  ADD KEY `idx_games_creator_status` (`nguoi_tao_id`,`trang_thai`),
  ADD KEY `idx_games_status_published` (`trang_thai`,`ngay_cong_bo`),
  ADD KEY `idx_games_title` (`tieu_de`);

--
-- Indexes for table `tro_choi_bo_suu_tap`
--
ALTER TABLE `tro_choi_bo_suu_tap`
  ADD PRIMARY KEY (`bo_suu_tap_id`,`tro_choi_id`),
  ADD KEY `tro_choi_id` (`tro_choi_id`),
  ADD KEY `nguoi_them_id` (`nguoi_them_id`),
  ADD KEY `idx_collection_games_position` (`bo_suu_tap_id`,`vi_tri`);

--
-- Indexes for table `tro_choi_giam_gia`
--
ALTER TABLE `tro_choi_giam_gia`
  ADD PRIMARY KEY (`chien_dich_id`,`tro_choi_id`),
  ADD KEY `tro_choi_id` (`tro_choi_id`);

--
-- Indexes for table `tro_choi_goi_game`
--
ALTER TABLE `tro_choi_goi_game`
  ADD PRIMARY KEY (`goi_game_id`,`tro_choi_id`),
  ADD KEY `idx_bundle_games_game` (`tro_choi_id`);

--
-- Indexes for table `tro_choi_nen_tang`
--
ALTER TABLE `tro_choi_nen_tang`
  ADD PRIMARY KEY (`tro_choi_id`,`nen_tang_id`),
  ADD KEY `nen_tang_id` (`nen_tang_id`);

--
-- Indexes for table `tro_choi_nhan`
--
ALTER TABLE `tro_choi_nhan`
  ADD PRIMARY KEY (`tro_choi_id`,`nhan_id`),
  ADD KEY `nhan_id` (`nhan_id`);

--
-- Indexes for table `tro_choi_the_loai`
--
ALTER TABLE `tro_choi_the_loai`
  ADD PRIMARY KEY (`tro_choi_id`,`the_loai_id`),
  ADD KEY `the_loai_id` (`the_loai_id`);

--
-- Indexes for table `tro_choi_yeu_thich`
--
ALTER TABLE `tro_choi_yeu_thich`
  ADD PRIMARY KEY (`nguoi_dung_id`,`tro_choi_id`),
  ADD KEY `tro_choi_id` (`tro_choi_id`);

--
-- Indexes for table `tuong_tac_bai_viet`
--
ALTER TABLE `tuong_tac_bai_viet`
  ADD PRIMARY KEY (`bai_viet_id`,`nguoi_dung_id`,`phan_ung`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`);

--
-- Indexes for table `vai_tro`
--
ALTER TABLE `vai_tro`
  ADD PRIMARY KEY (`ma`),
  ADD UNIQUE KEY `ma_vai_tro` (`ma_vai_tro`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bai_du_thi`
--
ALTER TABLE `bai_du_thi`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bai_viet_thao_luan`
--
ALTER TABLE `bai_viet_thao_luan`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bao_cao_noi_dung`
--
ALTER TABLE `bao_cao_noi_dung`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `binh_luan_nhat_ky`
--
ALTER TABLE `binh_luan_nhat_ky`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `binh_luan_tro_choi`
--
ALTER TABLE `binh_luan_tro_choi`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bo_suu_tap`
--
ALTER TABLE `bo_suu_tap`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chien_dich_giam_gia`
--
ALTER TABLE `chien_dich_giam_gia`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chu_de_thao_luan`
--
ALTER TABLE `chu_de_thao_luan`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cuoc_thi_lam_game`
--
ALTER TABLE `cuoc_thi_lam_game`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dien_dan`
--
ALTER TABLE `dien_dan`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dot_chi_tra`
--
ALTER TABLE `dot_chi_tra`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gio_hang`
--
ALTER TABLE `gio_hang`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `goi_game`
--
ALTER TABLE `goi_game`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hanh_dong_kiem_duyet`
--
ALTER TABLE `hanh_dong_kiem_duyet`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hoan_tien`
--
ALTER TABLE `hoan_tien`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `luot_dung_ma_giam_gia`
--
ALTER TABLE `luot_dung_ma_giam_gia`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ma_giam_gia`
--
ALTER TABLE `ma_giam_gia`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ma_truy_cap_tro_choi`
--
ALTER TABLE `ma_truy_cap_tro_choi`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media_tro_choi`
--
ALTER TABLE `media_tro_choi`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `muc_gio_hang`
--
ALTER TABLE `muc_gio_hang`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nen_tang`
--
ALTER TABLE `nen_tang`
  MODIFY `ma` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `nhan`
--
ALTER TABLE `nhan`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nhat_ky_he_thong`
--
ALTER TABLE `nhat_ky_he_thong`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nhat_ky_phat_trien`
--
ALTER TABLE `nhat_ky_phat_trien`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phien_ban_phat_hanh`
--
ALTER TABLE `phien_ban_phat_hanh`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quyen_so_huu_tro_choi`
--
ALTER TABLE `quyen_so_huu_tro_choi`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `so_ke_toan_tac_gia`
--
ALTER TABLE `so_ke_toan_tac_gia`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tai_khoan_nhan_tien_nguoi_dung`
--
ALTER TABLE `tai_khoan_nhan_tien_nguoi_dung`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `the_loai`
--
ALTER TABLE `the_loai`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `thong_bao`
--
ALTER TABLE `thong_bao`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tro_choi`
--
ALTER TABLE `tro_choi`
  MODIFY `ma` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vai_tro`
--
ALTER TABLE `vai_tro`
  MODIFY `ma` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bai_du_thi`
--
ALTER TABLE `bai_du_thi`
  ADD CONSTRAINT `bai_du_thi_ibfk_1` FOREIGN KEY (`cuoc_thi_id`) REFERENCES `cuoc_thi_lam_game` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `bai_du_thi_ibfk_2` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `bai_du_thi_ibfk_3` FOREIGN KEY (`nguoi_nop_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `bai_viet_thao_luan`
--
ALTER TABLE `bai_viet_thao_luan`
  ADD CONSTRAINT `bai_viet_thao_luan_ibfk_1` FOREIGN KEY (`chu_de_id`) REFERENCES `chu_de_thao_luan` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `bai_viet_thao_luan_ibfk_2` FOREIGN KEY (`tac_gia_nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `bai_viet_thao_luan_ibfk_3` FOREIGN KEY (`bai_viet_cha_id`) REFERENCES `bai_viet_thao_luan` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `bao_cao_noi_dung`
--
ALTER TABLE `bao_cao_noi_dung`
  ADD CONSTRAINT `bao_cao_noi_dung_ibfk_1` FOREIGN KEY (`nguoi_bao_cao_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE SET NULL,
  ADD CONSTRAINT `bao_cao_noi_dung_ibfk_2` FOREIGN KEY (`nguoi_duoc_phan_cong_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE SET NULL;

--
-- Constraints for table `binh_chon_bai_du_thi`
--
ALTER TABLE `binh_chon_bai_du_thi`
  ADD CONSTRAINT `binh_chon_bai_du_thi_ibfk_1` FOREIGN KEY (`bai_du_thi_id`) REFERENCES `bai_du_thi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `binh_chon_bai_du_thi_ibfk_2` FOREIGN KEY (`nguoi_binh_chon_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `binh_chon_danh_gia`
--
ALTER TABLE `binh_chon_danh_gia`
  ADD CONSTRAINT `binh_chon_danh_gia_ibfk_1` FOREIGN KEY (`danh_gia_id`) REFERENCES `danh_gia` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `binh_chon_danh_gia_ibfk_2` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `binh_luan_nhat_ky`
--
ALTER TABLE `binh_luan_nhat_ky`
  ADD CONSTRAINT `binh_luan_nhat_ky_ibfk_1` FOREIGN KEY (`nhat_ky_phat_trien_id`) REFERENCES `nhat_ky_phat_trien` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `binh_luan_nhat_ky_ibfk_2` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `binh_luan_nhat_ky_ibfk_3` FOREIGN KEY (`binh_luan_cha_id`) REFERENCES `binh_luan_nhat_ky` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `binh_luan_tro_choi`
--
ALTER TABLE `binh_luan_tro_choi`
  ADD CONSTRAINT `binh_luan_tro_choi_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `binh_luan_tro_choi_ibfk_2` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `binh_luan_tro_choi_ibfk_3` FOREIGN KEY (`binh_luan_cha_id`) REFERENCES `binh_luan_tro_choi` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `bo_suu_tap`
--
ALTER TABLE `bo_suu_tap`
  ADD CONSTRAINT `bo_suu_tap_ibfk_1` FOREIGN KEY (`nguoi_dung_so_huu_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `cai_dat_nguoi_dung`
--
ALTER TABLE `cai_dat_nguoi_dung`
  ADD CONSTRAINT `cai_dat_nguoi_dung_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `cai_dat_trang_tro_choi`
--
ALTER TABLE `cai_dat_trang_tro_choi`
  ADD CONSTRAINT `cai_dat_trang_tro_choi_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `chien_dich_giam_gia`
--
ALTER TABLE `chien_dich_giam_gia`
  ADD CONSTRAINT `chien_dich_giam_gia_ibfk_1` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `chi_tiet_chi_tra`
--
ALTER TABLE `chi_tiet_chi_tra`
  ADD CONSTRAINT `chi_tiet_chi_tra_ibfk_1` FOREIGN KEY (`dot_chi_tra_id`) REFERENCES `dot_chi_tra` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `chi_tiet_chi_tra_ibfk_2` FOREIGN KEY (`so_ke_toan_id`) REFERENCES `so_ke_toan_tac_gia` (`ma`);

--
-- Constraints for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`ma`),
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_2` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`),
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_3` FOREIGN KEY (`goi_game_id`) REFERENCES `goi_game` (`ma`),
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_4` FOREIGN KEY (`nguoi_nhan_qua_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `chu_de_thao_luan`
--
ALTER TABLE `chu_de_thao_luan`
  ADD CONSTRAINT `chu_de_thao_luan_ibfk_1` FOREIGN KEY (`dien_dan_id`) REFERENCES `dien_dan` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `chu_de_thao_luan_ibfk_2` FOREIGN KEY (`tac_gia_nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `cuoc_thi_lam_game`
--
ALTER TABLE `cuoc_thi_lam_game`
  ADD CONSTRAINT `cuoc_thi_lam_game_ibfk_1` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD CONSTRAINT `danh_gia_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `danh_gia_ibfk_2` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `dien_dan`
--
ALTER TABLE `dien_dan`
  ADD CONSTRAINT `dien_dan_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `dien_dan_ibfk_2` FOREIGN KEY (`cuoc_thi_id`) REFERENCES `cuoc_thi_lam_game` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`nguoi_mua_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `dot_chi_tra`
--
ALTER TABLE `dot_chi_tra`
  ADD CONSTRAINT `dot_chi_tra_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`),
  ADD CONSTRAINT `dot_chi_tra_ibfk_2` FOREIGN KEY (`tai_khoan_nhan_tien_id`) REFERENCES `tai_khoan_nhan_tien_nguoi_dung` (`ma`);

--
-- Constraints for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `goi_game`
--
ALTER TABLE `goi_game`
  ADD CONSTRAINT `goi_game_ibfk_1` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `hanh_dong_kiem_duyet`
--
ALTER TABLE `hanh_dong_kiem_duyet`
  ADD CONSTRAINT `hanh_dong_kiem_duyet_ibfk_1` FOREIGN KEY (`nguoi_kiem_duyet_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `hoan_tien`
--
ALTER TABLE `hoan_tien`
  ADD CONSTRAINT `hoan_tien_ibfk_1` FOREIGN KEY (`thanh_toan_id`) REFERENCES `thanh_toan` (`ma`),
  ADD CONSTRAINT `hoan_tien_ibfk_2` FOREIGN KEY (`chi_tiet_don_hang_id`) REFERENCES `chi_tiet_don_hang` (`ma`),
  ADD CONSTRAINT `hoan_tien_ibfk_3` FOREIGN KEY (`nguoi_yeu_cau_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE SET NULL;

--
-- Constraints for table `luot_dung_ma_giam_gia`
--
ALTER TABLE `luot_dung_ma_giam_gia`
  ADD CONSTRAINT `luot_dung_ma_giam_gia_ibfk_1` FOREIGN KEY (`ma_giam_gia_id`) REFERENCES `ma_giam_gia` (`ma`),
  ADD CONSTRAINT `luot_dung_ma_giam_gia_ibfk_2` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`ma`),
  ADD CONSTRAINT `luot_dung_ma_giam_gia_ibfk_3` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `ma_giam_gia`
--
ALTER TABLE `ma_giam_gia`
  ADD CONSTRAINT `ma_giam_gia_ibfk_1` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE SET NULL;

--
-- Constraints for table `ma_truy_cap_tro_choi`
--
ALTER TABLE `ma_truy_cap_tro_choi`
  ADD CONSTRAINT `ma_truy_cap_tro_choi_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `ma_truy_cap_tro_choi_ibfk_2` FOREIGN KEY (`nguoi_tao_ma_id`) REFERENCES `nguoi_dung` (`ma`),
  ADD CONSTRAINT `ma_truy_cap_tro_choi_ibfk_3` FOREIGN KEY (`nguoi_nhan_ma_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE SET NULL;

--
-- Constraints for table `media_tro_choi`
--
ALTER TABLE `media_tro_choi`
  ADD CONSTRAINT `media_tro_choi_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `muc_gio_hang`
--
ALTER TABLE `muc_gio_hang`
  ADD CONSTRAINT `muc_gio_hang_ibfk_1` FOREIGN KEY (`gio_hang_id`) REFERENCES `gio_hang` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `muc_gio_hang_ibfk_2` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `muc_gio_hang_ibfk_3` FOREIGN KEY (`goi_game_id`) REFERENCES `goi_game` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `nguoi_dung_vai_tro`
--
ALTER TABLE `nguoi_dung_vai_tro`
  ADD CONSTRAINT `nguoi_dung_vai_tro_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `nguoi_dung_vai_tro_ibfk_2` FOREIGN KEY (`vai_tro_id`) REFERENCES `vai_tro` (`ma`);

--
-- Constraints for table `nhat_ky_he_thong`
--
ALTER TABLE `nhat_ky_he_thong`
  ADD CONSTRAINT `nhat_ky_he_thong_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE SET NULL;

--
-- Constraints for table `nhat_ky_phat_trien`
--
ALTER TABLE `nhat_ky_phat_trien`
  ADD CONSTRAINT `nhat_ky_phat_trien_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `nhat_ky_phat_trien_ibfk_2` FOREIGN KEY (`tac_gia_nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `phien_ban_phat_hanh`
--
ALTER TABLE `phien_ban_phat_hanh`
  ADD CONSTRAINT `phien_ban_phat_hanh_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `phien_ban_phat_hanh_ibfk_2` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `quyen_so_huu_tro_choi`
--
ALTER TABLE `quyen_so_huu_tro_choi`
  ADD CONSTRAINT `quyen_so_huu_tro_choi_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`),
  ADD CONSTRAINT `quyen_so_huu_tro_choi_ibfk_2` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`),
  ADD CONSTRAINT `quyen_so_huu_tro_choi_ibfk_3` FOREIGN KEY (`chi_tiet_don_hang_id`) REFERENCES `chi_tiet_don_hang` (`ma`) ON DELETE SET NULL;

--
-- Constraints for table `so_ke_toan_tac_gia`
--
ALTER TABLE `so_ke_toan_tac_gia`
  ADD CONSTRAINT `so_ke_toan_tac_gia_ibfk_1` FOREIGN KEY (`chi_tiet_don_hang_id`) REFERENCES `chi_tiet_don_hang` (`ma`),
  ADD CONSTRAINT `so_ke_toan_tac_gia_ibfk_2` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `tac_gia_tro_choi`
--
ALTER TABLE `tac_gia_tro_choi`
  ADD CONSTRAINT `tac_gia_tro_choi_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `tac_gia_tro_choi_ibfk_2` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `tai_khoan_nhan_tien_nguoi_dung`
--
ALTER TABLE `tai_khoan_nhan_tien_nguoi_dung`
  ADD CONSTRAINT `tai_khoan_nhan_tien_nguoi_dung_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD CONSTRAINT `thanh_toan_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`ma`);

--
-- Constraints for table `thanh_vien_cuoc_thi`
--
ALTER TABLE `thanh_vien_cuoc_thi`
  ADD CONSTRAINT `thanh_vien_cuoc_thi_ibfk_1` FOREIGN KEY (`cuoc_thi_id`) REFERENCES `cuoc_thi_lam_game` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `thanh_vien_cuoc_thi_ibfk_2` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `theo_doi_nguoi_dung`
--
ALTER TABLE `theo_doi_nguoi_dung`
  ADD CONSTRAINT `theo_doi_nguoi_dung_ibfk_1` FOREIGN KEY (`nguoi_theo_doi_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `theo_doi_nguoi_dung_ibfk_2` FOREIGN KEY (`nguoi_duoc_theo_doi_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `theo_doi_tro_choi`
--
ALTER TABLE `theo_doi_tro_choi`
  ADD CONSTRAINT `theo_doi_tro_choi_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `theo_doi_tro_choi_ibfk_2` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `thong_bao`
--
ALTER TABLE `thong_bao`
  ADD CONSTRAINT `thong_bao_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `thong_ke_tro_choi_hang_ngay`
--
ALTER TABLE `thong_ke_tro_choi_hang_ngay`
  ADD CONSTRAINT `thong_ke_tro_choi_hang_ngay_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `tro_choi`
--
ALTER TABLE `tro_choi`
  ADD CONSTRAINT `tro_choi_ibfk_1` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `nguoi_dung` (`ma`);

--
-- Constraints for table `tro_choi_bo_suu_tap`
--
ALTER TABLE `tro_choi_bo_suu_tap`
  ADD CONSTRAINT `tro_choi_bo_suu_tap_ibfk_1` FOREIGN KEY (`bo_suu_tap_id`) REFERENCES `bo_suu_tap` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `tro_choi_bo_suu_tap_ibfk_2` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `tro_choi_bo_suu_tap_ibfk_3` FOREIGN KEY (`nguoi_them_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `tro_choi_giam_gia`
--
ALTER TABLE `tro_choi_giam_gia`
  ADD CONSTRAINT `tro_choi_giam_gia_ibfk_1` FOREIGN KEY (`chien_dich_id`) REFERENCES `chien_dich_giam_gia` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `tro_choi_giam_gia_ibfk_2` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `tro_choi_goi_game`
--
ALTER TABLE `tro_choi_goi_game`
  ADD CONSTRAINT `tro_choi_goi_game_ibfk_1` FOREIGN KEY (`goi_game_id`) REFERENCES `goi_game` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `tro_choi_goi_game_ibfk_2` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`);

--
-- Constraints for table `tro_choi_nen_tang`
--
ALTER TABLE `tro_choi_nen_tang`
  ADD CONSTRAINT `tro_choi_nen_tang_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `tro_choi_nen_tang_ibfk_2` FOREIGN KEY (`nen_tang_id`) REFERENCES `nen_tang` (`ma`);

--
-- Constraints for table `tro_choi_nhan`
--
ALTER TABLE `tro_choi_nhan`
  ADD CONSTRAINT `tro_choi_nhan_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `tro_choi_nhan_ibfk_2` FOREIGN KEY (`nhan_id`) REFERENCES `nhan` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `tro_choi_the_loai`
--
ALTER TABLE `tro_choi_the_loai`
  ADD CONSTRAINT `tro_choi_the_loai_ibfk_1` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `tro_choi_the_loai_ibfk_2` FOREIGN KEY (`the_loai_id`) REFERENCES `the_loai` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `tro_choi_yeu_thich`
--
ALTER TABLE `tro_choi_yeu_thich`
  ADD CONSTRAINT `tro_choi_yeu_thich_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `tro_choi_yeu_thich_ibfk_2` FOREIGN KEY (`tro_choi_id`) REFERENCES `tro_choi` (`ma`) ON DELETE CASCADE;

--
-- Constraints for table `tuong_tac_bai_viet`
--
ALTER TABLE `tuong_tac_bai_viet`
  ADD CONSTRAINT `tuong_tac_bai_viet_ibfk_1` FOREIGN KEY (`bai_viet_id`) REFERENCES `bai_viet_thao_luan` (`ma`) ON DELETE CASCADE,
  ADD CONSTRAINT `tuong_tac_bai_viet_ibfk_2` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`ma`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
