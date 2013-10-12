-- phpMyAdmin SQL Dump
-- version 3.5.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 01, 2013 at 09:23 AM
-- Server version: 5.0.91
-- PHP Version: 5.3.5-pl1-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `brewskii187_bluebans`
--

-- --------------------------------------------------------

--
-- Table structure for table `tf2jail_web_admins`
--

CREATE TABLE IF NOT EXISTS `tf2jail_web_admins` (
  `id` int(4) NOT NULL auto_increment,
  `username` varchar(65) NOT NULL default '',
  `password` varchar(65) NOT NULL default '',
  `email` varchar(128) default NULL,
  `authlevel` varchar(10) default NULL,
  `steamid` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `TF2Jail_web_protests`
--

CREATE TABLE IF NOT EXISTS `TF2Jail_web_protests` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) default NULL,
  `steamid` varchar(32) NOT NULL,
  `steam_link` varchar(128) default NULL,
  `email` varchar(128) NOT NULL,
  `reason` varchar(1000) NOT NULL,
  `ban_admin` varchar(64) default NULL,
  `ban_reason` varchar(200) default NULL,
  `date` varchar(16) default NULL,
  `archived` varchar(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
