
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

DROP TABLE IF EXISTS `day`;
CREATE TABLE `day` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `company` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  `stars` tinyint(4) NOT NULL,
  `image` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `onlyLogo` char(1) COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2014-07-08 15:22:20