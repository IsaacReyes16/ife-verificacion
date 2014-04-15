/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50516
Source Host           : localhost:3306
Source Database       : ife_vnm2014

Target Server Type    : MYSQL
Target Server Version : 50516
File Encoding         : 65001

Date: 2014-04-15 13:31:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `rep_termino`
-- ----------------------------
DROP TABLE IF EXISTS `rep_termino`;
CREATE TABLE `rep_termino` (
  `id_rep` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('REPORTE','CAPTURA') DEFAULT 'CAPTURA',
  `zona` enum('URBANA','RURAL','MIXTA') DEFAULT 'URBANA',
  `ent` tinyint(2) DEFAULT NULL,
  `dto` tinyint(2) DEFAULT NULL,
  `secc` mediumint(4) DEFAULT NULL,
  `mza` mediumint(4) DEFAULT NULL,
  `folio` int(11) DEFAULT NULL,
  `reemplazo` enum('SI','NO') DEFAULT NULL,
  `justificacion` text,
  `archivo` tinyint(1) DEFAULT '0',
  `archivo_nombre` varchar(50) DEFAULT NULL,
  `archivo_ruta` varchar(200) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id_rep`),
  KEY `folio` (`folio`),
  KEY `ed` (`ent`,`dto`),
  KEY `edsm` (`ent`,`dto`,`secc`,`mza`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of rep_termino
-- ----------------------------
