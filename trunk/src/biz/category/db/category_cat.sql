-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 02, 2010 at 06:45 PM
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
-- Table structure for table `category_cat`
--

CREATE TABLE IF NOT EXISTS `category_cat` (
  `catUID` int(11) NOT NULL auto_increment,
  `Lable` varchar(128) NOT NULL,
  `owner_type` varchar(128) NOT NULL,
  `owner_name` varchar(128) NOT NULL,
  `owner_UID` int(11) NOT NULL,
  `typeUID` int(11) NOT NULL,
  PRIMARY KEY  (`catUID`),
  KEY `typeUID` (`typeUID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `category_cat`
--

