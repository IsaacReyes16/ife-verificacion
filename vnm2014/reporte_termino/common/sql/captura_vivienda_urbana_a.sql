/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50516
Source Host           : localhost:3306
Source Database       : ife_vnm2014

Target Server Type    : MYSQL
Target Server Version : 50516
File Encoding         : 65001

Date: 2014-04-15 13:32:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `captura_vivienda_urbana_a`
-- ----------------------------
DROP TABLE IF EXISTS `captura_vivienda_urbana_a`;
CREATE TABLE `captura_vivienda_urbana_a` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `folio` int(10) DEFAULT NULL,
  `id_edo` int(2) DEFAULT NULL,
  `nom_edo` varchar(16) DEFAULT NULL,
  `id_dtto` int(2) DEFAULT NULL,
  `seccion` int(4) DEFAULT NULL,
  `mpio_clave` int(5) DEFAULT NULL,
  `mpio_nombre` varchar(50) DEFAULT NULL,
  `loc_clave` int(5) DEFAULT NULL,
  `loc_nombre` varchar(100) DEFAULT NULL,
  `calle` varchar(250) DEFAULT NULL,
  `num_ext` varchar(25) DEFAULT NULL,
  `num_int` varchar(25) DEFAULT NULL,
  `mz` int(5) DEFAULT NULL,
  `col_loc` varchar(100) DEFAULT NULL,
  `consecutivo_vivienda` int(10) DEFAULT NULL,
  `reemplazo` int(1) DEFAULT NULL,
  `usuario` int(8) DEFAULT NULL,
  `p3` int(1) DEFAULT NULL,
  `p3_especifica` varchar(50) DEFAULT NULL,
  `p4` int(1) DEFAULT NULL,
  `p5_1` int(3) DEFAULT NULL,
  `p5_2` int(3) DEFAULT NULL,
  `p5_3` int(6) DEFAULT NULL,
  `responsable_llenado` varchar(100) DEFAULT NULL,
  `puesto` varchar(100) DEFAULT NULL,
  `id_remplaza_a` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of captura_vivienda_urbana_a
-- ----------------------------
