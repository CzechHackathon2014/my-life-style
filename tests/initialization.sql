-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` char(60) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `role` varchar(30) COLLATE utf8_czech_ci NOT NULL DEFAULT 'user',
  `active` char(1) COLLATE utf8_czech_ci NOT NULL DEFAULT '1',
  `name` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `avatar` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `active`) VALUES
(1,	'architect',	'$2y$10$YAEX72RgtpUFwCPjHovOyOGf84eGwLqahldPoZtLjttX/zD9ZsgFa',	'architect@test.aprila.cz',	'root',	'1'),
(2,	'user',	'$2y$10$vJUBtSxm6HVp9E9EzK5/zOl7l.06s3fa3PUpQOI3CJudJnOIVpis2',	'user@test.aprila.cz',	'user',	'1'),
(4,	'tester',	'$2y$10$vJUBtSxm6HVp9E9EzK5/zOl7l.06s3fa3PUpQOI3CJudJnOIVpis2',	'tester@test.aprila.cz',	'user',	'1'),
(6,	'admin',	'$2y$10$vJUBtSxm6HVp9E9EzK5/zOl7l.06s3fa3PUpQOI3CJudJnOIVpis2',	'admin@test.aprila.cz',	'admin',	'1'),
(7,	'foo',	'$2y$10$vJUBtSxm6HVp9E9EzK5/zOl7l.06s3fa3PUpQOI3CJudJnOIVpis2',	'foo@test.aprila.cz',	'user',	'0'),
(9,	'bar',	'$2y$10$vJUBtSxm6HVp9E9EzK5/zOl7l.06s3fa3PUpQOI3CJudJnOIVpis2',	'bar@test.aprila.cz',	'user',	'0'),
(11,	'john',	'$2y$10$vJUBtSxm6HVp9E9EzK5/zOl7l.06s3fa3PUpQOI3CJudJnOIVpis2',	'john@test.aprila.cz',	'user',	'1'),
(12,	'dee',	'$2y$10$YAEX72RgtpUFwCPjHovOyOGf84eGwLqahldPoZtLjttX/zD9ZsgFa',	'dee@test.aprila.cz',	'admin',	'1'),
(13,	'office',	'$2y$10$vJUBtSxm6HVp9E9EzK5/zOl7l.06s3fa3PUpQOI3CJudJnOIVpis2',	'office@test.aprila.cz',	'user',	'1');

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


-- 2014-07-06 10:45:57

-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `reference`;
CREATE TABLE `reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `company` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  `stars` tinyint(4) NOT NULL,
  `image` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `onlyLogo` char(1) COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `reference` (`id`, `name`, `company`, `content`, `stars`, `image`) VALUES
(1,	'Honza Cerny',	'Kreativní Laboratoř s.r.o.',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam officiis, id necessitatibus, eum sed similique harum deserunt voluptates, culpa illum eaque fuga fugiat quo quis, officia ut facilis vel. Eaque!',	4,	'/data/reference/glitched-image-1.jpg'),
(2,	'Judr. Jaroslav Vopelka',	'ad. kanc. Vopelka a syn',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam officiis, id necessitatibus, eum sed similique harum deserunt voluptates, culpa illum eaque fuga fugiat quo quis, officia ut facilis vel. Eaque!',	0,	'/data/reference/shadowbox.jpg'),
(5,	'Honza Cerny',	'Kreativní Laboratoř s.r.o.',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam officiis, id necessitatibus, eum sed similique harum deserunt voluptates, culpa illum eaque fuga fugiat quo quis, officia ut facilis vel. Eaque!',	4,	'/data/reference/download-10-.png'),
(7,	'Judr. Jaroslav Vopelka',	'Kreativní Laboratoř s.r.o.',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam officiis, id necessitatibus, eum sed similique harum deserunt voluptates, culpa illum eaque fuga fugiat quo quis, officia ut facilis vel. Eaque!',	5,	'/data/reference/9.jpg'),
(12,	'Honza Cerny',	'Kreativní Laboratoř s.r.o.',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam officiis, id necessitatibus, eum sed similique harum deserunt voluptates, culpa illum eaque fuga fugiat quo quis, officia ut facilis vel. Eaque!',	3,	'/data/reference/download-3-.png'),
(13,	'Judr. Jaroslav Vopelka',	'Kreativní Laboratoř s.r.o.',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam officiis, id necessitatibus, eum sed similique harum deserunt voluptates, culpa illum eaque fuga fugiat quo quis, officia ut facilis vel. Eaque!',	5,	'/data/reference/tvlrdr2ni9_download-8-.png'),
(14,	'Honza Cerny',	'Kreativní Laboratoř s.r.o.',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam officiis, id necessitatibus, eum sed similique harum deserunt voluptates, culpa illum eaque fuga fugiat quo quis, officia ut facilis vel. Eaque!',	4,	'/data/reference/download-11-.png'),
(15,	'Judr. Jaroslav Vopelka',	'Kreativní Laboratoř s.r.o.',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam officiis, id necessitatibus, eum sed similique harum deserunt voluptates, culpa illum eaque fuga fugiat quo quis, officia ut facilis vel. Eaque!',	0,	'/data/reference/download-9-.png'),
(16,	'Honza Cerny',	'Kreativní Laboratoř s.r.o.',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam officiis, id necessitatibus, eum sed similique harum deserunt voluptates, culpa illum eaque fuga fugiat quo quis, officia ut facilis vel. Eaque!',	0,	'/data/reference/on-cm.jpg'),
(17,	'Judr. Jaroslav Vopelka',	'Kreativní Laboratoř s.r.o.',	'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam officiis, id necessitatibus, eum sed similique harum deserunt voluptates, culpa illum eaque fuga fugiat quo quis, officia ut facilis vel. Eaque!',	5,	'/data/reference/glitched-image-2.jpg');

-- 2014-07-15 21:02:55