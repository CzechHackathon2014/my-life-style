
-- CREATE DATABASE `mylifestyle_devel` COLLATE 'utf8_czech_ci';
-- CREATE USER 'mylifestyle'@'localhost' IDENTIFIED BY 'strongpassword';
-- GRANT USAGE ON * . * TO 'mylifestyle'@'localhost' IDENTIFIED BY 'strongpassword' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
-- GRANT ALL PRIVILEGES ON `mylifestyle\_%` . * TO 'mylifestyle'@'localhost';
-- FLUSH PRIVILEGES;



-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `expirience_category`;
CREATE TABLE `expirience_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `day`;
CREATE TABLE `day` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `mood` int(1) unsigned DEFAULT NULL,
  `expirience_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `day_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `expirience`;
CREATE TABLE `expirience` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `day_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT '',
  `expirience_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `day_id` (`day_id`),
  KEY `expirience_id` (`expirience_id`),
  CONSTRAINT `expirience_ibfk_2` FOREIGN KEY (`expirience_id`) REFERENCES `expirience` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `expirience_ibfk_1` FOREIGN KEY (`day_id`) REFERENCES `day` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET foreign_key_checks = 1;


-- 2014-07-08 15:22:20