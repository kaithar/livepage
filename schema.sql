-- phpMyAdmin SQL Dump
-- version 2.10.0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: May 14, 2007 at 07:06 AM
-- Server version: 5.0.38
-- PHP Version: 5.2.2-pl1-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `lp_livepage_test`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `cms_menu`
-- 

CREATE TABLE `cms_menu` (
  `item_id` int(10) unsigned NOT NULL auto_increment,
  `item_order` int(11) NOT NULL default '0',
  `item_text` varchar(255) NOT NULL default '',
  `item_url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `cms_pages`
-- 

CREATE TABLE `cms_pages` (
  `page_id` int(10) unsigned NOT NULL auto_increment,
  `page_parent` int(10) NOT NULL default '1',
  `page_key` varchar(255) NOT NULL default '',
  `page_title` varchar(255) NOT NULL default '',
  `page_include` varchar(255) default NULL,
  PRIMARY KEY  (`page_id`),
  UNIQUE KEY `page_key` (`page_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `cms_sections`
-- 

CREATE TABLE `cms_sections` (
  `section_id` int(10) unsigned NOT NULL auto_increment,
  `page_id` int(11) NOT NULL default '0',
  `order` smallint(6) NOT NULL,
  `section_title` varchar(255) NOT NULL default '',
  `section_text` longtext NOT NULL,
  PRIMARY KEY  (`section_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `cms_sessions`
-- 

CREATE TABLE `cms_sessions` (
  `user_id` int(11) NOT NULL default '0',
  `session_id` varchar(255) NOT NULL default '',
  `lastview` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `cms_users`
-- 

CREATE TABLE `cms_users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `uname` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `displayname` varchar(255) NOT NULL default '',
  `fails` int(11) NOT NULL default '0',
  `lastfail` int(11) NOT NULL default '0',
  `editcontent` tinyint(4) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
