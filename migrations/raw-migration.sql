/*
SQLyog Ultimate v9.63 
MySQL - 8.0.11 : Database - praying_time
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `box` */

DROP TABLE IF EXISTS `box`;

CREATE TABLE `box` (
  `box_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `box_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `prayer_zone` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `prayer_time_option` tinyint(1) DEFAULT '1',
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`box_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

/*Table structure for table `box_song` */

DROP TABLE IF EXISTS `box_song`;

CREATE TABLE `box_song` (
  `box_song_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `box_id` bigint(20) NOT NULL,
  `song_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `prayer_date` date NOT NULL,
  `prayer_time` time NOT NULL,
  `prayer_time_seq` smallint(6) NOT NULL,
  `audio_file_path` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`box_song_id`),
  KEY `box_song_ibfk_1` (`box_id`),
  CONSTRAINT `box_song_ibfk_1` FOREIGN KEY (`box_id`) REFERENCES `box` (`box_id`)
) ENGINE=InnoDB AUTO_INCREMENT=687 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

/*Table structure for table `cron` */

DROP TABLE IF EXISTS `cron`;

CREATE TABLE `cron` (
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `box_id` bigint(20) NOT NULL,
  `prayer_zone` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_update` datetime DEFAULT NULL,
  PRIMARY KEY (`start_date`,`end_date`,`box_id`,`prayer_zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

/*Table structure for table `doctrine_migration_versions` */

DROP TABLE IF EXISTS `doctrine_migration_versions`;

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

/*Table structure for table `subscriber` */

DROP TABLE IF EXISTS `subscriber`;

CREATE TABLE `subscriber` (
  `subscriber_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subscriber_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `password` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`subscriber_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

/*Table structure for table `subscriber_box` */

DROP TABLE IF EXISTS `subscriber_box`;

CREATE TABLE `subscriber_box` (
  `subscriber_id` bigint(20) NOT NULL,
  `box_id` bigint(20) NOT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`subscriber_id`,`box_id`),
  KEY `subscriber_box_ibfk_1` (`subscriber_id`),
  KEY `subscriber_box_ibfk_2` (`box_id`),
  CONSTRAINT `subscriber_box_ibfk_1` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`subscriber_id`),
  CONSTRAINT `subscriber_box_ibfk_2` FOREIGN KEY (`box_id`) REFERENCES `box` (`box_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
