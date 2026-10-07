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

 Date: 22/05/2026 10:53:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for m_cashflow_detail
-- ----------------------------
DROP TABLE IF EXISTS `m_cashflow_detail`;
CREATE TABLE `m_cashflow_detail`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'TRIAL',
  `id_cf` int NOT NULL COMMENT 'TRIAL',
  `id_ac` int NOT NULL COMMENT 'TRIAL',
  `tipe` varchar(16) CHARACTER SET ascii COLLATE ascii_general_ci NULL DEFAULT NULL COMMENT 'TRIAL',
  `id_perush` int NOT NULL COMMENT 'TRIAL',
  `id_user` int NULL DEFAULT NULL COMMENT 'TRIAL',
  `created_at` datetime NULL DEFAULT NULL COMMENT 'TRIAL',
  `updated_at` datetime NULL DEFAULT NULL COMMENT 'TRIAL',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1100 CHARACTER SET = ascii COLLATE = ascii_general_ci COMMENT = 'TRIAL' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of m_cashflow_detail
-- ----------------------------
INSERT INTO `m_cashflow_detail` VALUES (727, 8, 4010, '1', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (728, 9, 4020, '1', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (729, 9, 4030, '1', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (731, 12, 1021, '1', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (732, 12, 2000, '1', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (733, 13, 2120, '1', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (734, 14, 1040, '1', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (735, 14, 1041, '1', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (736, 16, 5010, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (737, 16, 5011, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (738, 16, 2020, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (739, 17, 5020, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (740, 17, 5021, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (741, 17, 5022, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (742, 17, 5023, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (743, 17, 2030, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (744, 17, 2031, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (745, 17, 2032, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (746, 17, 2033, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (747, 17, 2034, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (748, 18, 5030, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (749, 18, 5040, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (750, 18, 5041, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (751, 18, 5042, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (752, 18, 5043, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (753, 18, 5044, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (754, 18, 5045, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (755, 18, 5046, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (756, 18, 5047, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (757, 18, 5048, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (758, 18, 5049, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (759, 18, 50410, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (760, 18, 50411, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (761, 18, 2040, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (762, 20, 2050, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (763, 21, 2001, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (764, 22, 5110, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (765, 22, 5111, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (766, 22, 5112, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (767, 22, 5113, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (768, 22, 5114, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (769, 22, 5115, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (770, 22, 5116, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (771, 23, 5120, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (772, 23, 5121, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (773, 23, 5122, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (774, 23, 5123, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (775, 23, 5124, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (776, 23, 5125, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (777, 23, 5126, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (778, 23, 5127, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (779, 23, 5128, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (780, 23, 5129, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (781, 23, 51210, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (782, 23, 51211, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (783, 23, 51212, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (784, 23, 51213, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (785, 23, 51214, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (786, 23, 51215, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (787, 23, 51216, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (788, 23, 51217, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (789, 24, 5130, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (790, 25, 5131, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (791, 26, 2120, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (792, 27, 1040, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (793, 27, 1041, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (794, 28, 1021, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (795, 28, 2000, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (796, 29, 4110, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (797, 29, 4011, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (798, 30, 1310, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (799, 30, 1311, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (800, 30, 1312, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (801, 31, 1320, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (802, 31, 1321, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (803, 31, 1322, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (804, 31, 1323, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (805, 31, 1324, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (806, 32, 1330, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (807, 32, 1331, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (808, 32, 1332, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (809, 32, 1333, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (810, 32, 1334, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (811, 32, 1335, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (812, 32, 1336, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (813, 33, 1340, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (814, 33, 1341, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (815, 33, 1350, '2', 29, 3, NULL, NULL);
INSERT INTO `m_cashflow_detail` VALUES (1099, 11, 4040, '1', 29, 6, '2024-06-13 10:40:19', '2024-06-13 10:40:19');

SET FOREIGN_KEY_CHECKS = 1;
