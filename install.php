<?php

define("INSTALLER",true);

/* This could be interesting...
 * First thing we need to do is see if we can actually find a database config...
 */

print "<b>Attempting to load config data...</b><br/>";

require_once("includes/env_init.php");

/* Is it empty? */

print "<b>Looking for existing tables...</b><br/>";

$extables = Array(
  "cms_categories",
  "cms_config",
  "cms_menu",
  "cms_pages",
  "cms_sections",
  "cms_sessions",
  "cms_users"
);

$tablesql = mysql_do_query("SHOW TABLES");
$tables = Array();

while ($table = mysql_fetch_array($tablesql))
  $tables[$table[0]] = true;
  
foreach ($extables as $v)
  if (isset($tables[$v]))
    die("Existing tables found!");

/* Appropriate file storage? */

print "<b>Checking file stores...</b><br/>";

if (!file_exists("files/"))
  die("files/ doesn't exist.");

if (!file_exists("files/".$config['domain']."/"))
{
  if (!is_writable("files/"))
    die("files/ isn't writable and the file store for this domain doesn't exist.");
  mkdir("files/".$config['domain']);
}

if (!file_exists("files/".$config['domain']."/images/"))
{
  if (!is_writable("files/".$config['domain']."/"))
    die("images folder doesn't exist and file store isn't writable.");
  mkdir("files/".$config['domain']."/images/");
}

if (!is_writable("files/".$config['domain']."/images"))
  die ("images folder in file store isn't writable.");

/* Okie, lets create some tables! */

print "<b>Starting table creation...</b><br/>";
print "Creating Categories... ";

mysql_query("CREATE TABLE `cms_categories` 
(
  `cat_id` int(10) unsigned NOT NULL auto_increment,
  `cat_parent` int(10) unsigned NOT NULL default '1',
  `cat_key` varchar(255) NOT NULL,
  `cat_title` varchar(255) NOT NULL,
  PRIMARY KEY  (`cat_id`),
  UNIQUE KEY `failsafe` (`cat_parent`,`cat_key`),
  KEY `cat_key` (`cat_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci") or die("Failed!");

print "Ok!<br/>Creating Config... ";

mysql_query("CREATE TABLE `cms_config`
(
  `db_revision` int(10) unsigned NOT NULL default '1',
  `site_name` varchar(255) NOT NULL default 'LivePage',
  `logo` varchar(255) NOT NULL default 'images/logo.png',
  `footer` varchar(255) NOT NULL default '',
  `lock_message` varchar(255) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci") or die("Failed!");

print "Ok!<br/>Creating main template config... ";

/* templating! */

mysql_query("CREATE TABLE `cms_template_config`
(
  `template_name` varchar(255) NOT NULL default '',
  `template_data` text NOT NULL default '',
  PRIMARY KEY (`template_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci") or die("Failed!");

print "Ok!<br/>Creating template page config... ";

mysql_query("CREATE TABLE `cms_template_page_config`
(
  `template_name` varchar(255) NOT NULL default '',
  `template_page_id` int (10) NOT NULL default '1',
  `template_data` text NOT NULL default '',
  INDEX (`template_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci") or die("Failed!");

print "Ok!<br/>Creating template section config... ";

mysql_query("CREATE TABLE `cms_template_section_config`
(
  `template_name` varchar(255) NOT NULL default '',
  `template_section_id` int (10) NOT NULL default '1',
  `template_data` text NOT NULL default '',
  INDEX (`template_section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci") or die("Failed!");

print "Ok!<br/>Creating main template config... ";

mysql_query("CREATE TABLE `cms_template_sidebar_config`
(
  `template_name` varchar(255) NOT NULL default '',
  `template_sidebar_id` int(5) NOT NULL default '1',
  `template_data` text NOT NULL default '',
  INDEX (`template_sidebar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci") or die("Failed!");

/*-----*/

print "Ok!<br/>Creating Menu... ";

mysql_query("CREATE TABLE `cms_menu`
(
  `item_id` int(10) unsigned NOT NULL auto_increment,
  `item_order` int(11) NOT NULL default '0',
  `item_category` int(10) unsigned NOT NULL default '0',
  `item_text` varchar(255) NOT NULL default '',
  `item_url` varchar(255) NOT NULL default '',
  `item_separator` tinyint(1) unsigned NOT NULL default '0',
  `item_header` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci") or die("Failed!");

print "Ok!<br/>Creating Pages... ";

mysql_query("CREATE TABLE `cms_pages`
(
  `page_id` int(10) unsigned NOT NULL auto_increment,
  `page_category` int(10) NOT NULL default '1',
  `page_key` varchar(255) NOT NULL default '',
  `page_title` varchar(255) NOT NULL default '',
  `page_include` varchar(255) default NULL,
  PRIMARY KEY  (`page_id`),
  UNIQUE KEY `failsafe` (`page_category`,`page_key`),
  KEY `page_key` (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci") or die("Failed!");

print "Ok!<br/>Creating Sections... ";

mysql_query("CREATE TABLE `cms_sections`
(
  `section_id` int(10) unsigned NOT NULL auto_increment,
  `page_id` int(11) NOT NULL default '0',
  `order` smallint(6) NOT NULL,
  `section_title` varchar(255) NOT NULL,
  `section_text` longtext NOT NULL,
  PRIMARY KEY  (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci") or die("Failed!");

print "Ok!<br/>Creating Sessions... ";

mysql_query("CREATE TABLE `cms_sessions`
(
  `user_id` int(11) NOT NULL default '0',
  `session_id` varchar(255) NOT NULL default '',
  `lastview` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci") or die("Failed!");

print "Ok!<br/>Creating Users... ";

mysql_query("CREATE TABLE `cms_users`
(
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `uname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `displayname` varchar(255) NOT NULL,
  `fails` int(11) NOT NULL default '0',
  `lastfail` int(11) NOT NULL default '0',
  `editcontent` tinyint(4) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci") or die("Failed!");

print "Ok!<br/>";

/* Now for a default state... */

print "<b>Inserting default data...</b></br>";

print "Inserting initial user...";

mysql_query("
INSERT INTO `cms_users`
           (`uname`, `password`, `displayname`, `editcontent`)
    VALUES ('admin', MD5('admin'), 'Administrator', 1)") or die("Failed!");

print "Ok!<br/>";

print "Inserting root category...";

mysql_query("
INSERT INTO `cms_categories`
           (`cat_id` ,`cat_parent` ,`cat_key` ,`cat_title`)
    VALUES ('1', '0', '', 'Home')") or die("Failed!");

print "Ok!<br/>";

print "Inserting site config...";

/*
 * When updating the db_revision, don't forget to update includes/db_revision_test.php and upgrade.php please.
 */

mysql_query("
INSERT INTO `cms_config`
           (`db_revision`, `site_name`, `logo`, `lock_message`)
    VALUES ('8', 'LivePage', '/images/logo.png', NULL)") or die("Failed!");

print "Ok!<br/>";

print "Inserting initial index and login pages...";

mysql_query("
INSERT INTO `cms_pages`
           (`page_id`,`page_category` ,`page_key` ,`page_title`, `page_include`)
     VALUES
           ('1','1', 'index', 'Welcome', NULL),
           ('2','1', 'login', 'Login', 'pages/login.php')") or die("Failed!");

print "Ok!<br/>";

print "Inserting index page content...";

mysql_query("
INSERT INTO `cms_sections`
           (`page_id`, `order`, `section_title`, `section_text`)
    VALUES ('1', '0', 'Welcome...', 'LivePage has been sucessfully installed.<br/>Enjoy!.')") or die("Failed!");

print "Ok!<br/>";

/* Okie, done! */
?>
<br/>
<br/>
Livepage installer done.<br/>
<br/>
Click <a href="/">here</a> to start using LivePage</br>
