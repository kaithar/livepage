<?php

if (!defined("MASSUPGRADE"))
{
  define("INSTALLER", true);

  print "<b>Attempting to load configs...</b><br/>";

  require_once("includes/env_init.php");
}

if (!function_exists("mysql_f_query"))
{
  function mysql_f_query ($query)
  {
    global $oldlock;
    if (!mysql_query($query))
    {
      print "Failed! Attempting to undo lock.  ";
      mysql_query("UPDATE `cms_config` set lock_message='".mysql_real_escape_string($oldlock)."'") or die("Failed!!!!");
      die("Done");
    }
  }
}

print "<b>Grabbing config data...</b><br/>";

$sql = mysql_query("SELECT * FROM `cms_config`") or die("Failed.");
$site_config = mysql_fetch_assoc($sql);

print "<b>Locking database...</b><br/>";

$oldlock = $site_config['lock_message'];
mysql_query("UPDATE `cms_config` set lock_message='Upgrade in progress, please stand by.'") or die("Failed.");

print "<b>Attempting to upgrade...</b><br/>";
print "<i>Database at version: ".$site_config["db_revision"]."</i><br/><br/>";

print "<ul>";
switch($site_config["db_revision"])
{
  case 1:
    
    print "<li>01 -> 02 --- Fixing broken default logo's</li>";
    mysql_f_query("START TRANSACTION");
    mysql_f_query("UPDATE `cms_config` SET logo='/images/logo.png' WHERE logo='images/logo.png'");
    mysql_f_query("UPDATE `cms_config` SET db_revision='2'");
    mysql_f_query("COMMIT");
    
    
  case 2:
    
    print "<li>02 -> 03 --- Adding separater flag to sidebar</li>";
    mysql_f_query("ALTER TABLE `cms_menu` 
                           ADD `item_separator` tinyint(1) unsigned NOT NULL default '0'
                         AFTER `item_url`");
    mysql_f_query("UPDATE `cms_config` SET db_revision='3'");
    
    
  case 3:
    
    print "<li>03 -> 04 --- Add category field to sidebar</li>";
    mysql_f_query("ALTER TABLE `cms_menu`
                         ADD `item_category` int(10) unsigned NOT NULL default '1'
                       AFTER `item_order`");
    mysql_f_query("UPDATE `cms_config` SET db_revision='4'");
    
    
  case 4:
    
    print "<li>04 -> 05 --- Add footer line</li>";
    mysql_f_query("ALTER TABLE `cms_config`
                         ADD `footer` varchar(255) NOT NULL default ''
                       AFTER `logo`");
    mysql_f_query("UPDATE `cms_config` SET db_revision='5'");
    
    
  case 5:
    
    print "<li>05 -> 06 --- Create template config table</li>";
    mysql_f_query("CREATE TABLE `cms_template_config`
    (
      `template_name` varchar(255) NOT NULL default '',
      `template_data` text NOT NULL default '',
      PRIMARY KEY (`template_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
    mysql_f_query("UPDATE `cms_config` SET db_revision='6'");
    
    
  case 6:
    
    print "<li>06 -> 07 --- Create more template config tables (page, section, sidebar)</li>";
    
    mysql_f_query("CREATE TABLE `cms_template_page_config`
    (
      `template_name` varchar(255) NOT NULL default '',
      `template_page_id` int (10) NOT NULL default '1',
      `template_data` text NOT NULL default '',
      INDEX (`template_page_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

    mysql_f_query("CREATE TABLE `cms_template_section_config`
    (
      `template_name` varchar(255) NOT NULL default '',
      `template_section_id` int (10) NOT NULL default '1',
      `template_data` text NOT NULL default '',
      INDEX (`template_section_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

    mysql_f_query("CREATE TABLE `cms_template_sidebar_config`
    (
      `template_name` varchar(255) NOT NULL default '',
      `template_sidebar_id` int(5) NOT NULL default '1',
      `template_data` text NOT NULL default '',
      INDEX (`template_sidebar_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
    mysql_f_query("UPDATE `cms_config` SET db_revision='7'");
    
    
  case 7:
    
    print "<li>07 -> 08 --- Add header flag to the menu table</li>";
    
    mysql_f_query("ALTER TABLE `cms_menu`
                           ADD `item_header` tinyint(1) unsigned NOT NULL default '0'
                         AFTER `item_separator`");
    mysql_f_query("UPDATE `cms_config` SET db_revision='8'");
    
    
  case 8:
    
    print "<li>08 -> 09 --- Add template choice to config table</li>";
    
    mysql_f_query("ALTER TABLE `cms_config`
                           ADD `template` varchar(255) NOT NULL default 'simplicity'
                         AFTER `site_name`");
    mysql_f_query("UPDATE `cms_config` SET db_revision='9', template='simplicity'");
    
    
  case 9:
    
    print "<li>09 -> 10 -- Rename cms_template_sidebar_config to cms_template_menu_config</li>";

    mysql_f_query("CREATE TABLE `cms_template_menu_config`
    (
      `template_name` varchar(255) NOT NULL default '',
      `template_menu_id` int(5) NOT NULL default '1',
      `template_data` text NOT NULL default '',
      INDEX (`template_menu_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

    mysql_f_query("INSERT INTO `cms_template_menu_config`
                        SELECT *
                          FROM `cms_template_sidebar_config`");
    
    mysql_f_query("DROP TABLE `cms_template_sidebar_config`");

    mysql_f_query("UPDATE `cms_config` SET db_revision='10'");
    
  case 10:
    
    /*
     * When updating the current db_revision, don't forget to update includes/db_revision_test.php and install.php please.
     */
    
    print "<li>Database upto date.</li>";
    break;
    
    
  default:
    
    print "Unknown db version!!!";
    break;
}
print "</ul>";

print "<b>Unlocking database...</b><br/>";

mysql_f_query("UPDATE `cms_config` set lock_message='".mysql_real_escape_string($oldlock)."'");

?>
