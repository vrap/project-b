-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 29, 2014 at 11:07 PM
-- Server version: 5.5.35-0ubuntu0.13.10.2
-- PHP Version: 5.5.3-1ubuntu2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `project-b`
--

-- --------------------------------------------------------

--
-- Table structure for table `Corporation`
--

CREATE TABLE IF NOT EXISTS `Corporation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `Corporation`
--

INSERT INTO `Corporation` (`id`, `name`, `email`, `phone`) VALUES
(4, 'Seniormedia', 'seniormedia@gmail.com', '');

-- --------------------------------------------------------

--
-- Table structure for table `Formation`
--

CREATE TABLE IF NOT EXISTS `Formation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `Formation`
--

INSERT INTO `Formation` (`id`, `name`) VALUES
(4, 'Licence professionnelle');

-- --------------------------------------------------------

--
-- Table structure for table `Lesson`
--

CREATE TABLE IF NOT EXISTS `Lesson` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  `timecard` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `Lesson`
--

INSERT INTO `Lesson` (`id`, `name`, `startDate`, `endDate`, `timecard`) VALUES
(3, 'Wordpress', '2014-01-29 22:07:43', '2014-01-29 22:07:43', 0);

-- --------------------------------------------------------

--
-- Table structure for table `Manager`
--

CREATE TABLE IF NOT EXISTS `Manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_35991C25A76ED395` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `Manager`
--

INSERT INTO `Manager` (`id`, `user_id`) VALUES
(1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `Module`
--

CREATE TABLE IF NOT EXISTS `Module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `Module`
--

INSERT INTO `Module` (`id`, `name`) VALUES
(1, 'CMS');

-- --------------------------------------------------------

--
-- Table structure for table `Promotion`
--

CREATE TABLE IF NOT EXISTS `Promotion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `Promotion`
--

INSERT INTO `Promotion` (`id`, `startDate`, `endDate`) VALUES
(1, '2014-01-29 22:07:43', '2014-01-29 22:07:43');

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `surname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_2DA1797792FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_2DA17977A0D96FBF` (`email_canonical`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`id`, `surname`, `email`, `phone`, `login`, `password`, `username`, `username_canonical`, `email_canonical`, `enabled`, `salt`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`) VALUES
(5, 'Flauder', 'pflauder@gmail.com', '', 'pierre', 'pierre', '', '', '', 0, '', NULL, 0, 0, NULL, NULL, NULL, '', 0, NULL),
(6, NULL, 'test@test', NULL, NULL, 'QTjm3Upc4LhUUKfbrxbjP7tWlUD5eDIlwOnI1OxFVMTVk3HA0IJETLYLMQp0NuItgS/NWDliQf4WA1NOC4I7EQ==', 'test', 'test', 'test@test', 1, '58ybkjvas3k0gk8wk840ks48s4okcok', NULL, 0, 0, NULL, NULL, NULL, 'a:0:{}', 0, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Manager`
--
ALTER TABLE `Manager`
  ADD CONSTRAINT `FK_35991C25A76ED395` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
