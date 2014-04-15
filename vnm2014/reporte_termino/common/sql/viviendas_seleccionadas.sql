/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50516
Source Host           : localhost:3306
Source Database       : ife_vnm2014

Target Server Type    : MYSQL
Target Server Version : 50516
File Encoding         : 65001

Date: 2014-04-15 13:31:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `viviendas_seleccionadas`
-- ----------------------------
DROP TABLE IF EXISTS `viviendas_seleccionadas`;
CREATE TABLE `viviendas_seleccionadas` (
  `folio` int(14) NOT NULL AUTO_INCREMENT,
  `identificador` int(11) DEFAULT NULL,
  `consecutivo` int(6) DEFAULT NULL,
  `id_ent` int(2) DEFAULT NULL,
  `estado` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `id_dis` int(2) DEFAULT NULL,
  `seccion` int(4) DEFAULT NULL,
  `manzana` int(4) DEFAULT NULL,
  `id_loc` int(5) DEFAULT NULL,
  `localidad` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `colonia` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `id_mun` int(5) DEFAULT NULL,
  `municipio` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `calle` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `exterior` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `interior` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `tipo` int(1) DEFAULT NULL,
  `es_habitada` int(1) DEFAULT NULL,
  `jefe_familia` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `caracteristicas` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `es_remplazo` int(1) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  PRIMARY KEY (`folio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of viviendas_seleccionadas
-- ----------------------------
