<?php

if (!defined("MASSUPGRADE"))
{
  define("INSTALLER", true);

  print "<b>Attempting to load configs...</b><br/>";

  require_once("includes/env_init.php");
}

print "<b>Grabbing config data...</b><br/>";

$sql = mysql_query("SELECT * FROM `cms_config`") or die("Failed!");
$site_config = mysql_fetch_assoc($sql);

print "<b>Attempting to upgrade...</b><br/>";
print "<i>Database at version: ".$site_config["db_revision"]."</i><br/><br/>";

function mysql_f_query ($query)
{
  mysql_query($query) or die("Failed");
}

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
    
    print "<li>Database upto date.</li>";
    break;
    
    
  default:
    
    die ("Unknown db version!!!");
}
print "</ul>";
?>
