-- phpMyAdmin SQL Dump
-- version 2.10.0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Dec 06, 2008 at 02:40 PM
-- Server version: 5.0.37
-- PHP Version: 5.2.1

SET FOREIGN_KEY_CHECKS=0;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `twerm`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `time_period`
-- 

DROP TABLE IF EXISTS `time_period`;
CREATE TABLE `time_period` (
  `time_period_id` int(10) unsigned NOT NULL auto_increment,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY  (`time_period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `time_period_term`
-- 

DROP TABLE IF EXISTS `time_period_term`;
CREATE TABLE `time_period_term` (
  `time_period_id` int(10) unsigned NOT NULL,
  `term` varchar(140) NOT NULL,
  `count` int(10) unsigned NOT NULL default '0',
  `tf_idf` float NOT NULL,
  `rank` int(10) unsigned NOT NULL,
  `power_rank` int(11) NOT NULL,
  PRIMARY KEY  (`time_period_id`,`term`),
  KEY `term` (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `time_period_twitter_user`
-- 

DROP TABLE IF EXISTS `time_period_twitter_user`;
CREATE TABLE `time_period_twitter_user` (
  `time_period_id` int(10) unsigned NOT NULL,
  `twitter_user_name` varchar(15) NOT NULL,
  `rank` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`time_period_id`,`twitter_user_name`),
  KEY `twitter_user_name` (`twitter_user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `twitter_post`
-- 

DROP TABLE IF EXISTS `twitter_post`;
CREATE TABLE `twitter_post` (
  `twitter_post_id` int(15) unsigned NOT NULL,
  `time_period_id` int(10) unsigned NOT NULL,
  `twitter_user_name` varchar(15) NOT NULL,
  `content` varchar(140) NOT NULL,
  `published_datetime` datetime NOT NULL,
  PRIMARY KEY  (`twitter_post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `twitter_post_term`
-- 

DROP TABLE IF EXISTS `twitter_post_term`;
CREATE TABLE `twitter_post_term` (
  `twitter_post_id` int(15) unsigned NOT NULL,
  `term` varchar(140) NOT NULL,
  `count` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`twitter_post_id`,`term`),
  KEY `term` (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `twitter_term`
-- 

DROP TABLE IF EXISTS `twitter_term`;
CREATE TABLE `twitter_term` (
  `term` varchar(140) NOT NULL,
  PRIMARY KEY  (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `twitter_user`
-- 

DROP TABLE IF EXISTS `twitter_user`;
CREATE TABLE `twitter_user` (
  `twitter_user_name` varchar(15) NOT NULL,
  `twitter_profile_image_url` varchar(140) NOT NULL,
  PRIMARY KEY  (`twitter_user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `time_period_term`
-- 
ALTER TABLE `time_period_term`
  ADD CONSTRAINT `time_period_term_ibfk_1` FOREIGN KEY (`time_period_id`) REFERENCES `time_period` (`time_period_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `time_period_term_ibfk_2` FOREIGN KEY (`term`) REFERENCES `twitter_term` (`term`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `time_period_twitter_user`
-- 
ALTER TABLE `time_period_twitter_user`
  ADD CONSTRAINT `time_period_twitter_user_ibfk_1` FOREIGN KEY (`time_period_id`) REFERENCES `time_period` (`time_period_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `time_period_twitter_user_ibfk_2` FOREIGN KEY (`twitter_user_name`) REFERENCES `twitter_user` (`twitter_user_name`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `twitter_post_term`
-- 
ALTER TABLE `twitter_post_term`
  ADD CONSTRAINT `twitter_post_term_ibfk_2` FOREIGN KEY (`term`) REFERENCES `twitter_term` (`term`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `twitter_post_term_ibfk_3` FOREIGN KEY (`twitter_post_id`) REFERENCES `twitter_post` (`twitter_post_id`) ON DELETE CASCADE ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS=1;
