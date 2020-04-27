-- Table structure for table `guess`
DROP TABLE IF EXISTS `guess`;
CREATE TABLE IF NOT EXISTS `guess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=3803 ;

-- Table structure for table `question`
DROP TABLE IF EXISTS `question`;
CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_czech_ci NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `image` text COLLATE utf8_czech_ci NOT NULL,
  `answer_description` text COLLATE utf8_czech_ci,
  `answer_images` text COLLATE utf8_czech_ci,
  `date` date NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=29 ;

-- Table structure for table `user`
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` text COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `role` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `notify` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=367 ;

--
-- Constraints for table `guess`
--
ALTER TABLE `guess`
  ADD CONSTRAINT `guess_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guess_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE;
