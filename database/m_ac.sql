/*
 Navicat Premium Data Transfer

 Source Server         : persada
 Source Server Type    : MySQL
 Source Server Version : 110806
 Source Host           : 153.92.15.37:3306
 Source Schema         : u434772285_persada

 Target Server Type    : MySQL
 Target Server Version : 110806
 File Encoding         : 65001

 Date: 22/05/2026 10:52:12
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for m_ac
-- ----------------------------
DROP TABLE IF EXISTS `m_ac`;
CREATE TABLE `m_ac`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_ac` bigint UNSIGNED NOT NULL,
  `id_parent` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` bigint UNSIGNED NOT NULL,
  `tipe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `jenis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_aktif` int NOT NULL,
  `id_user` bigint UNSIGNED NULL DEFAULT NULL,
  `trial568` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 257 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of m_ac
-- ----------------------------
INSERT INTO `m_ac` VALUES (174, 1, 0, 'AKTIVA', 1, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (175, 2, 0, 'PASIVA', 1, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (176, 3, 0, 'MODAL', 1, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (177, 4, 0, 'PENDAPATAN', 1, 'K', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (178, 5, 0, 'BIAYA', 1, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (179, 10, 1, 'Aktiva Lancar', 2, NULL, 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (180, 11, 1, 'Pernyataan Modal', 2, NULL, 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (181, 12, 1, 'Brand Equity', 2, NULL, 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (182, 13, 1, 'Aktiva Tetap', 2, NULL, 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (183, 20, 2, 'Hutang Lancar', 2, NULL, 'N', 1, 3, NULL, NULL, '2023-03-04 17:57:28', NULL);
INSERT INTO `m_ac` VALUES (184, 21, 2, 'Hutang Jk Panjang', 2, NULL, 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (185, 30, 3, 'Modal Dasar', 2, NULL, 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (186, 31, 3, 'Saldo Laba', 2, NULL, 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (187, 32, 3, 'Laba Berjalan', 2, NULL, 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (188, 40, 4, 'PENDAPATAN', 2, NULL, 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (189, 41, 4, 'NOTA RETUR', 2, NULL, 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (190, 50, 5, 'HARGA POKOK PENJUALAN', 2, NULL, 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (191, 51, 5, 'OPERASIONAL', 2, NULL, 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (192, 52, 5, 'POKOK DAN BUNGA', 2, NULL, 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (193, 53, 5, 'PENDAPATAN DAN BIAYA LAIN-LAIN', 2, NULL, 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (194, 54, 5, 'PAJAK', 2, NULL, 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (195, 101, 10, 'Kas', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (196, 102, 10, 'Bank', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (197, 103, 10, 'Piutang Dagang', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (198, 104, 10, 'Piutang Lain - lain', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (199, 105, 10, 'Uang Muka', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (200, 111, 11, 'Kepemilikan Saham', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (201, 112, 11, 'Pernyataan Modal Lain - lain', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (202, 121, 12, 'Brand Equity', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (203, 131, 13, 'Tanah dan Bangunan', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (204, 132, 13, 'Kendaraan', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (205, 133, 13, 'Inventaris', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (206, 134, 13, 'Akum Penyusutan', 3, 'D', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (207, 201, 20, 'Hutang Usaha', 3, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (208, 202, 20, 'Hutang Pajak', 3, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (209, 203, 20, 'Hutang Karyawan', 3, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (210, 204, 20, 'Hutang Vendor', 3, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (211, 205, 20, 'Hutang Lain - lain', 3, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (212, 211, 21, 'Hutang Bank', 3, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (213, 212, 21, 'Hutang Pihak Ketiga', 3, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (214, 213, 21, 'Hutang lain', 3, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (215, 301, 30, 'Modal Dasar', 3, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (216, 311, 31, 'Saldo Laba', 3, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (217, 321, 32, 'Laba Berjalan', 3, 'K', 'N', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (218, 401, 40, 'Pendapatan Usaha', 3, 'K', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (219, 411, 41, 'Claim Barang', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (220, 412, 41, 'Discount Harga', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (222, 501, 50, 'Biaya UJ Sopir', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (223, 502, 50, 'Biaya Tambahan Diluar UJ Awal', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (224, 503, 50, 'Biaya Kuli Bongkar', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (225, 504, 50, 'Biaya Claim Barang Customer', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (226, 505, 50, 'Biaya Jasa Montir / Bengkel', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (227, 506, 50, 'Biaya Lain - lain Kendaraan', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (229, 511, 51, 'Biaya Gaji Karyawan', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (230, 512, 51, 'Biaya Incentive Karyawan', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (231, 513, 51, 'Biaya Transportasi Karyawan', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (232, 514, 51, 'Biaya BPJS', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (233, 515, 51, 'Biaya UM Makan Minum Karyawan', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (234, 516, 51, 'Biaya Polisi Tilang', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (235, 517, 51, 'Biaya Alat Tulis (ATK)', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (236, 518, 51, 'Biaya Telepon Pulsa', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (237, 519, 51, 'Biaya PDAM, PLN', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (238, 5110, 51, 'Biaya Asuransi', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (239, 5111, 51, 'Biaya Sumbangan Sosial (CSR)', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (240, 5112, 51, 'Biaya Iuran Keamanan Kantor', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (241, 5113, 51, 'Biaya Manajemen dan Konsultasi', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (242, 5114, 51, 'Biaya Lain-lain Admin dan Umum', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (243, 5115, 51, 'Biaya Fasilitas Direksi', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (244, 5116, 51, 'Biaya Parkir Tol', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (245, 5117, 51, 'Biaya Perjalanan Dinas', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (247, 521, 52, 'Biaya Pokok Pinjaman', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (248, 522, 52, 'Bunga Pinjaman', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (250, 531, 53, 'Pendapatan Bunga Bank', 3, 'K', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (251, 532, 53, 'Penjualan Barang Bekas', 3, 'K', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (252, 533, 53, 'Pendapatan Lain-lain', 3, 'K', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (253, 534, 53, 'Biaya Administrasi Bank', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (254, 535, 53, 'Biaya Lain-lain', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);
INSERT INTO `m_ac` VALUES (256, 541, 54, 'Pajak', 3, 'D', 'R', 1, 3, NULL, NULL, NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
