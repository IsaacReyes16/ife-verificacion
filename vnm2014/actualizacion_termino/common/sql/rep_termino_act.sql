-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: ife_vnm2014
-- ------------------------------------------------------
-- Server version	5.5.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `rep_termino_act`
--

DROP TABLE IF EXISTS `rep_termino_act`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rep_termino_act` (
  `id_rep` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('REPORTE','CAPTURA') DEFAULT 'CAPTURA',
  `zona` enum('URBANA','RURAL','MIXTA') DEFAULT 'URBANA',
  `ent` tinyint(2) DEFAULT NULL,
  `dto` tinyint(2) DEFAULT NULL,
  `secc` mediumint(4) DEFAULT NULL,
  `mza` mediumint(4) DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `reemplazo` enum('SI','NO') DEFAULT NULL,
  `justificacion` text,
  `archivo` tinyint(1) DEFAULT '0',
  `archivo_nombre` varchar(50) DEFAULT NULL,
  `archivo_ruta` varchar(200) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id_rep`),
  KEY `folio` (`consecutivo`),
  KEY `ed` (`ent`,`dto`),
  KEY `edsm` (`ent`,`dto`,`secc`,`mza`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rep_termino_act`
--

LOCK TABLES `rep_termino_act` WRITE;
/*!40000 ALTER TABLE `rep_termino_act` DISABLE KEYS */;
/*!40000 ALTER TABLE `rep_termino_act` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-08 12:01:05
