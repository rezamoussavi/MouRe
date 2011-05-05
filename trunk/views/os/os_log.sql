-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 05, 2011 at 11:40 AM
-- Server version: 5.0.92
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `samrad_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `os_log`
--

CREATE TABLE IF NOT EXISTS `os_log` (
  `logID` int(11) NOT NULL auto_increment,
  `TimeStamp` varchar(255) NOT NULL,
  `Biz` varchar(255) NOT NULL,
  `NodeID` varchar(255) NOT NULL,
  `Message` text NOT NULL,
  PRIMARY KEY  (`logID`),
  UNIQUE KEY `logID` (`logID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=737 ;
