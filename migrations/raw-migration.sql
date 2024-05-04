DROP TABLE IF EXISTS `box`;

CREATE TABLE `box` (
  `box_id` int(11) NOT NULL AUTO_INCREMENT,
  `box_name` varchar(50) NOT NULL,
  `prayer_zone` varchar(5) NOT NULL,
  `prayer_time_option` tinyint(1) NOT NULL DEFAULT '1',
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`box_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Table structure for table `box_song` */

DROP TABLE IF EXISTS `box_song`;

CREATE TABLE `box_song` (
  `box_song_id` int(11) NOT NULL AUTO_INCREMENT,
  `box_id` int(11) NOT NULL,
  `song_title` varchar(100) NOT NULL,
  `prayer_date` date NOT NULL,
  `prayer_time` time NOT NULL,
  `prayer_time_seq` bigint(20) NOT NULL,
  `audio_file_path` varchar(200) NOT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`box_song_id`),
  KEY `UX_box_song__box_id` (`box_id`),
  CONSTRAINT `FK_box_song__box` FOREIGN KEY (`box_id`) REFERENCES `box` (`box_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Table structure for table `cron` */

DROP TABLE IF EXISTS `cron`;

CREATE TABLE `cron` (
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `box_id` int(11) NOT NULL,
  `prayer_zone` varchar(5) NOT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`start_date`,`end_date`,`box_id`,`prayer_zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Table structure for table `subscriber` */

DROP TABLE IF EXISTS `subscriber`;

CREATE TABLE `subscriber` (
  `subscriber_id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_name` varchar(50) NOT NULL,
  `password` varchar(150) NOT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Table structure for table `subscriber_box` */

DROP TABLE IF EXISTS `subscriber_box`;

CREATE TABLE `subscriber_box` (
  `subscriber_id` int(11) NOT NULL,
  `box_id` int(11) NOT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`subscriber_id`,`box_id`),
  KEY `UX_subscriber_box__box_id` (`box_id`),
  KEY `UX_subscriber_box__subscribe_id` (`subscriber_id`),
  CONSTRAINT `FK_subscriber_box__box` FOREIGN KEY (`box_id`) REFERENCES `box` (`box_id`),
  CONSTRAINT `FK_subscriber_box__subscriber` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;