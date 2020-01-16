-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 16, 2020 at 10:20 AM
-- Server version: 5.7.26
-- PHP Version: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cafevariomenet`
--

-- --------------------------------------------------------

--
-- Table structure for table `installations`
--

DROP TABLE IF EXISTS `installations`;
CREATE TABLE IF NOT EXISTS `installations` (
  `installation_key` varchar(32) NOT NULL,
  `name` varchar(256) NOT NULL,
  `base_url` varchar(256) NOT NULL,
  `status` smallint(6) NOT NULL,
  PRIMARY KEY (`installation_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `installations_networks`
--

DROP TABLE IF EXISTS `installations_networks`;
CREATE TABLE IF NOT EXISTS `installations_networks` (
  `installation_key` varchar(100) NOT NULL,
  `network_key` int(15) NOT NULL,
  PRIMARY KEY (`installation_key`,`network_key`),
  KEY `NetworkKey_FK` (`network_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `networks`
--

DROP TABLE IF EXISTS `networks`;
CREATE TABLE IF NOT EXISTS `networks` (
  `network_key` int(15) NOT NULL AUTO_INCREMENT,
  `network_name` varchar(256) NOT NULL,
  `network_type` tinyint(4) NOT NULL,
  `network_threshold` int(11) NOT NULL,
  `network_status` tinyint(4) NOT NULL,
  PRIMARY KEY (`network_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `network_requests`
--

DROP TABLE IF EXISTS `network_requests`;
CREATE TABLE IF NOT EXISTS `network_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `network_key` int(11) NOT NULL,
  `installation_key` varchar(32) NOT NULL,
  `token` varchar(32) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `NetworkRequest_NetworkKey_FK` (`network_key`),
  KEY `NetworkRequest_InstallationKey_FK` (`installation_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `installations_networks`
--
ALTER TABLE `installations_networks`
  ADD CONSTRAINT `InstallationKey_FK` FOREIGN KEY (`installation_key`) REFERENCES `installations` (`installation_key`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NetworkKey_FK` FOREIGN KEY (`network_key`) REFERENCES `networks` (`network_key`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `network_requests`
--
ALTER TABLE `network_requests`
  ADD CONSTRAINT `NetworkRequest_InstallationKey_FK` FOREIGN KEY (`installation_key`) REFERENCES `installations` (`installation_key`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NetworkRequest_NetworkKey_FK` FOREIGN KEY (`network_key`) REFERENCES `networks` (`network_key`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
