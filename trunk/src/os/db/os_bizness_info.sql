-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 02, 2010 at 05:03 PM
-- Server version: 5.0.91
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `salamroo_os`
--

-- --------------------------------------------------------

--
-- Table structure for table `os_bizness_info`
--

CREATE TABLE IF NOT EXISTS `os_bizness_info` (
  `biznessUID` int(11) NOT NULL auto_increment,
  `domain` varchar(128) NOT NULL,
  `bizbankname` varchar(128) NOT NULL,
  `contactperson` varchar(128) NOT NULL,
  `contactemail` varchar(128) NOT NULL,
  PRIMARY KEY  (`biznessUID`),
  KEY `domain` (`domain`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `os_bizness_info`
--

INSERT INTO `os_bizness_info` (`biznessUID`, `domain`, `bizbankname`, `contactperson`, `contactemail`) VALUES
(1, 'salamrooz.com', 'eBoardPortal', 'Erik', 'erikkinding@gmail.com');
