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

 Date: 22/05/2026 10:52:55
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for m_cashflow
-- ----------------------------
DROP TABLE IF EXISTS `m_cashflow`;
CREATE TABLE `m_cashflow`  (
  `id_cf` int NOT NULL AUTO_INCREMENT COMMENT 'TRIAL',
  `nama_cashflow` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'TRIAL',
  `tipe` varchar(16) CHARACTER SET ascii COLLATE ascii_general_ci NULL DEFAULT NULL COMMENT 'TRIAL',
  `head` varchar(16) CHARACTER SET ascii COLLATE ascii_general_ci NULL DEFAULT NULL COMMENT 'TRIAL',
  `level` varchar(16) CHARACTER SET ascii COLLATE ascii_general_ci NULL DEFAULT NULL COMMENT 'TRIAL',
  `id_user` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NULL DEFAULT NULL COMMENT 'TRIAL',
  `created_at` datetime NULL DEFAULT NULL COMMENT 'TRIAL',
  `updated_at` datetime NULL DEFAULT NULL COMMENT 'TRIAL',
  PRIMARY KEY (`id_cf`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 35 CHARACTER SET = ascii COLLATE = ascii_general_ci COMMENT = 'TRIAL' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of m_cashflow
-- ----------------------------
INSERT INTO `m_cashflow` VALUES (1, 'PENDAPATAN', '1', '0', '1', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (2, 'HARGA POKOK', '2', '0', '1', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (3, 'BIAYA HANDLING', '2', '0', '1', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (4, 'BIAYA OPEARASIONAL', '2', '0', '1', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (5, 'BIAYA GAJI', '2', '0', '1', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (6, 'BIAYA LAIN-LAIN', '2', '0', '1', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (7, 'INVESTASI', '2', '0', '1', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (8, 'Pendapatan Trucking', '1', '1', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (9, 'Pendapatan Container', '1', '1', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (10, 'Pendapatan Udara', '1', '1', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (11, 'Pendapatan Handling', '1', '1', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (12, 'Pinjaman Cabang', '1', '1', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (13, 'Pinjaman Pihak Ketiga', '1', '1', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (14, 'Piutang Karyawan', '1', '1', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (15, 'Lain-lain', '1', '1', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (16, 'Biaya Kapal', '2', '2', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (17, 'Biaya Angkutan', '2', '2', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (18, 'Biaya Hpp Operasional', '2', '2', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (19, 'Biaya Overhead', '2', '2', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (20, 'Biaya Vendor', '2', '2', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (21, 'Biaya Handling', '2', '3', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (22, 'Biaya Pemasaran', '2', '4', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (23, 'Administrasi dan Umum', '2', '4', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (24, 'Biaya Gaji', '2', '5', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (25, 'Bonus Karyawan', '2', '5', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (26, 'Pinjaman Pihak Ketiga', '2', '6', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (27, 'Piutang Karyawan', '2', '6', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (28, 'Pinjaman Cabang', '2', '6', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (29, 'Claim, Retur, Revisi Harga', '2', '6', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (30, 'Tanah', '2', '7', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (31, 'Kendaraan', '2', '7', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (32, 'Inventaris', '2', '7', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (33, 'Lain-lain', '2', '7', '2', '6', NULL, NULL);
INSERT INTO `m_cashflow` VALUES (34, 'Pendapatan Asuransi', '1', '1', '2', '26388', '2022-12-08 11:13:18', '2022-12-08 11:13:18');

SET FOREIGN_KEY_CHECKS = 1;
