
-- CREATE DATABASE `mylifestyle_devel` COLLATE 'utf8_czech_ci';
-- CREATE USER 'mylifestyle'@'localhost' IDENTIFIED BY 'strongpassword';
-- GRANT USAGE ON * . * TO 'mylifestyle'@'localhost' IDENTIFIED BY 'strongpassword' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
-- GRANT ALL PRIVILEGES ON `mylifestyle\_%` . * TO 'mylifestyle'@'localhost';
-- FLUSH PRIVILEGES;


SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';


-- ----------------------------------------------------------------------------------------------------
-- ----------------------------------------------------------------------------------------------------
-- ----------------------------------------------------------------------------------------------------


-- System Users
-- default user  architect : kreslo

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `facebook_id` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` char(60) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `role` varchar(30) COLLATE utf8_czech_ci NOT NULL DEFAULT 'user',
  `active` char(1) COLLATE utf8_czech_ci NOT NULL DEFAULT '1',
  `name` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `avatar` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `change_email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `change_email_requested` datetime NOT NULL,
  `change_email_tokenOne` char(60) COLLATE utf8_czech_ci NOT NULL,
  `change_email_tokenTwo` char(60) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `active`, `name`, `avatar`) VALUES
  (1, 'architect',  '$2y$10$K.JEAIhI/bmk2Kas2uxFi.3Y.qJ6LZNw44X5k9Lq81R27wgcZqsSu', 'info@aprila.cz', 'root', '1',  'Architect',  '');

DROP TABLE IF EXISTS `users_password_reset`;
CREATE TABLE `users_password_reset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `salt` char(32) COLLATE utf8_czech_ci NOT NULL,
  `token` char(64) COLLATE utf8_czech_ci NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  CONSTRAINT `userId` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;




-- ----------------------------------------------------------------------------------------------------
-- ----------------------------------------------------------------------------------------------------
-- ----------------------------------------------------------------------------------------------------



-- My Life Style - Day and Experience


DROP TABLE IF EXISTS `experience_category`;
CREATE TABLE `experience_category` (
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
  `experience_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `day_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `experience`;
CREATE TABLE `experience` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `day_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(255) DEFAULT '',
  `category_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `day_id` (`day_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `experience_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `experience_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `experience_ibfk_1` FOREIGN KEY (`day_id`) REFERENCES `day` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET foreign_key_checks = 1;



-- ----------------------------------------------------------------------------------------------------
-- ----------------------------------------------------------------------------------------------------
-- ----------------------------------------------------------------------------------------------------



-- Insert default categories

INSERT INTO `experience_category` (`id`, `name`) VALUES
(1,	'Sportovní aktivita'),
(2,	'Večírek'),
(3,	'Setkání se s přáteli'),
(4,	'Potkání nových lidí'),
(5,	'Kulnářský zážitek'),
(6,	'Překonání strachu'),
(7,	'Tvořivost');
