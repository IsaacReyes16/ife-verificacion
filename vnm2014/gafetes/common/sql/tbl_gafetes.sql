/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50516
Source Host           : localhost:3306
Source Database       : ife_vnm2014

Target Server Type    : MYSQL
Target Server Version : 50516
File Encoding         : 65001

Date: 2014-03-11 13:54:09
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tbl_gafetes`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_gafetes`;
CREATE TABLE `tbl_gafetes_copy` (
  `id_gafete` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('ENUMERACION','COBERTURA','ACTUALIZACION') DEFAULT NULL,
  `ent` smallint(2) DEFAULT NULL,
  `dto` smallint(2) DEFAULT NULL,
  `puesto` varchar(30) DEFAULT NULL,
  `nombre` varchar(32) DEFAULT NULL,
  `paterno` varchar(32) DEFAULT NULL,
  `materno` varchar(32) DEFAULT NULL,
  `cve_elector` varchar(18) DEFAULT NULL,
  `clave` varchar(15) DEFAULT NULL,
  `vocal_nombre` varchar(150) DEFAULT NULL,
  `vocal_puesto` varchar(150) DEFAULT NULL,
  `vigencia` varchar(80) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id_gafete`),
  KEY `ent` (`ent`) USING BTREE,
  KEY `ed` (`ent`,`dto`) USING BTREE,
  KEY `id_usuario` (`id_usuario`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tbl_gafetes
-- ----------------------------
