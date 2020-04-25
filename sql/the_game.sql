-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `question`;
CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_czech_ci NOT NULL,
  `date` date NOT NULL,
  `image` text COLLATE utf8_czech_ci NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `question` (`id`, `name`, `date`, `image`, `x`, `y`) VALUES
(2,	'PRvní otázka, začnem z lehka',	'2020-03-22',	'/images/questions/abstract-1238246.jpg',	3,	6);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` text COLLATE utf8_czech_ci NOT NULL,
  `name` text COLLATE utf8_czech_ci NOT NULL,
  `role` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `user` (`id`, `email`, `password`, `name`, `role`) VALUES
(5,	'rajmund.hutar@gmail.com',	'$2y$10$NtL58sBtv5rrVDwfUPj1g.lVhJS/0r.J0yXEcRhJJpf/IV/uHvtRa',	'Jaroslav Hutař',	'admin');

ALTER TABLE `question`
CHANGE `name` `description` text COLLATE 'utf8_czech_ci' NOT NULL AFTER `id`,
CHANGE `image` `image` text COLLATE 'utf8_czech_ci' NOT NULL AFTER `description`,
ADD `answer_description` text COLLATE 'utf8_czech_ci' NULL AFTER `image`,
ADD `answer_images` text COLLATE 'utf8_czech_ci' NULL AFTER `answer_description`,
CHANGE `date` `date` date NOT NULL AFTER `answer_images`;

ALTER TABLE `question`
ADD `name` text NOT NULL AFTER `id`;
-- 2020-03-23 20:54:29

ALTER TABLE `user`
CHANGE `name` `name` varchar(255) COLLATE 'utf8_czech_ci' NOT NULL AFTER `password`;
ALTER TABLE `user`
ADD UNIQUE `name` (`name`);

ALTER TABLE `user`
ADD `password_hash` varchar(255) COLLATE 'utf8_czech_ci' NULL,
ADD `password_time` datetime NULL AFTER `password_hash`;