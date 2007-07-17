-- LivePage Schema ... pending installer...

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------
--
-- Structure for cms_categories
--

CREATE TABLE `cms_categories` (
  `cat_id` int(10) unsigned NOT NULL auto_increment,
  `cat_parent` int(10) unsigned NOT NULL default '1',
  `cat_key` varchar(255) collate utf8_unicode_ci NOT NULL,
  `cat_title` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`cat_id`),
  UNIQUE KEY `failsafe` (`cat_parent`,`cat_key`),
  KEY `cat_key` (`cat_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cms_categories`
        (`cat_id` ,`cat_parent` ,`cat_key` ,`cat_title`)
        VALUES ('1', '0', '', 'Home');

-- --------------------------------------------------------
--
-- Table cms_config
-- 

CREATE TABLE `cms_config` (
  `db_revision` int(10) unsigned NOT NULL default '1',
  `site_name` varchar(255) collate utf8_unicode_ci NOT NULL default 'LivePage',
  `lock_message` varchar(255) collate utf8_unicode_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cms_config`
	(`db_revision` ,`site_name` ,`lock_message`)
	VALUES ('1', 'LivePage', NULL);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

-- 
-- Table structure for table `cms_pages`
-- 

CREATE TABLE `cms_pages` (
  `page_id` int(10) unsigned NOT NULL auto_increment,
  `page_category` int(10) NOT NULL default '1',
  `page_key` varchar(255) NOT NULL default '',
  `page_title` varchar(255) NOT NULL default '',
  `page_include` varchar(255) default NULL,
  PRIMARY KEY  (`page_id`),
  UNIQUE KEY `failsafe` (`page_category`,`page_key`),
  KEY `page_key` (`page_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cms_pages`
	(`page_id`,`page_category` ,`page_key` ,`page_title`, `page_include`)
	VALUES
	('1','1', 'index', "Welcome", NULL),
	('2','1', 'login', "Login", "pages/login.php");

-- --------------------------------------------------------

-- 
-- Table structure for table `cms_sections`
-- 

CREATE TABLE `cms_sections` (
  `section_id` int(10) unsigned NOT NULL auto_increment,
  `page_id` int(11) NOT NULL default '0',
  `order` smallint(6) NOT NULL,
  `section_title` varchar(255) NOT NULL,
  `section_text` longtext NOT NULL,
  PRIMARY KEY  (`section_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cms_sections`
	(`page_id`, `order`, `section_title`, `section_text`)
	VALUES ('1', '0', "Welcome...", "LivePage has been sucessfully installed.<br/>Enjoy!.");

-- --------------------------------------------------------

-- 
-- Table structure for table `cms_sessions`
-- 

CREATE TABLE `cms_sessions` (
  `user_id` int(11) NOT NULL default '0',
  `session_id` varchar(255) NOT NULL default '',
  `lastview` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `cms_users`
-- 

CREATE TABLE `cms_users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `uname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `displayname` varchar(255) NOT NULL,
  `fails` int(11) NOT NULL default '0',
  `lastfail` int(11) NOT NULL default '0',
  `editcontent` tinyint(4) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `cms_users`
	(`uname`, `password`, `displayname`, `editcontent`)
	VALUES ('admin', MD5("admin"), "Administrator", 1);

