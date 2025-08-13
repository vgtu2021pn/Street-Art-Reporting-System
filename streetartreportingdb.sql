-- MariaDB dump 10.19  Distrib 10.5.28-MariaDB, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: streetartreportingdb
-- ------------------------------------------------------
-- Server version	10.5.28-MariaDB-0+deb11u2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `street_art`
--

DROP TABLE IF EXISTS `street_art`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `street_art` (
  `street_art_uno` bigint(20) unsigned NOT NULL,
  `author` char(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` char(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lat` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lng` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`street_art_uno`),
  UNIQUE KEY `street_art_uno` (`street_art_uno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `street_art_photo`
--

DROP TABLE IF EXISTS `street_art_photo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `street_art_photo` (
  `uno` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `street_art_uno` bigint(20) unsigned NOT NULL,
  `photo_name` char(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_size` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_type` char(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_data` mediumblob,
  PRIMARY KEY (`uno`),
  UNIQUE KEY `uno` (`uno`),
  KEY `street_art_uno` (`street_art_uno`),
  CONSTRAINT `street_art_photo_ibfk_1` FOREIGN KEY (`street_art_uno`) REFERENCES `street_art` (`street_art_uno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `street_art_damage`
--

DROP TABLE IF EXISTS `street_art_damage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `street_art_damage` (
  `uno` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `street_art_uno` bigint(20) unsigned NOT NULL,
  `explanation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sticker_status` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`uno`),
  UNIQUE KEY `uno` (`uno`),
  KEY `street_art_uno` (`street_art_uno`),
  CONSTRAINT `street_art_damage_ibfk_1` FOREIGN KEY (`street_art_uno`) REFERENCES `street_art` (`street_art_uno`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registrar`
--

DROP TABLE IF EXISTS `registrar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `registrar` (
  `uno` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `street_art_uno` bigint(20) unsigned NOT NULL,
  `registrar_number` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `registrar_date` date NOT NULL,
  `registrar_status` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`uno`),
  UNIQUE KEY `uno` (`uno`),
  KEY `street_art_uno` (`street_art_uno`),
  CONSTRAINT `registrar_ibfk_1` FOREIGN KEY (`street_art_uno`) REFERENCES `street_art` (`street_art_uno`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `uno` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `fname` char(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lname` char(90) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` char(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usertype` tinyint(1) unsigned NOT NULL,
  `password` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `adulthood` tinyint(1) unsigned NOT NULL,
  `termsofservice` tinyint(1) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`uno`),
  UNIQUE KEY `uno` (`uno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_contact`
--

DROP TABLE IF EXISTS `user_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_contact` (
  `uno` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `user_uno` bigint(10) unsigned NOT NULL,
  `contact_type` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`uno`),
  UNIQUE KEY `uno` (`uno`),
  KEY `user_uno` (`user_uno`),
  CONSTRAINT `user_contact_ibfk_1` FOREIGN KEY (`user_uno`) REFERENCES `user` (`uno`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_family`
--

DROP TABLE IF EXISTS `user_family`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_family` (
  `uno` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `user_uno` bigint(10) unsigned NOT NULL,
  `user_family_uno` bigint(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`uno`),
  UNIQUE KEY `uno` (`uno`),
  UNIQUE KEY `user_family_uno` (`user_family_uno`),
  KEY `user_uno` (`user_uno`),
  CONSTRAINT `user_family_ibfk_1` FOREIGN KEY (`user_uno`) REFERENCES `user` (`uno`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_family_ibfk_2` FOREIGN KEY (`user_family_uno`) REFERENCES `user` (`uno`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_street_art`
--

DROP TABLE IF EXISTS `user_street_art`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_street_art` (
  `uno` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `street_art_uno` bigint(20) unsigned NOT NULL,
  `user_uno` bigint(10) unsigned NOT NULL,
  PRIMARY KEY (`uno`),
  UNIQUE KEY `uno` (`uno`),
  UNIQUE KEY `street_art_uno` (`street_art_uno`),
  KEY `user_uno` (`user_uno`),
  CONSTRAINT `user_street_art_ibfk_1` FOREIGN KEY (`street_art_uno`) REFERENCES `street_art` (`street_art_uno`),
  CONSTRAINT `user_street_art_ibfk_2` FOREIGN KEY (`user_uno`) REFERENCES `user` (`uno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-30 11:54:09
