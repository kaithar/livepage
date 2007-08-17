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

print "<ul>";
switch($site_config["db_revision"])
{
  case 1:
    print "<li>01 -> 02) Fixing broken default logo's</li>";
    mysql_query("UPDATE `cms_config` SET logo='/images/logo.png' WHERE logo='images/logo.png'");
    mysql_query("UPDATE `cms_config` SET db_revision='2'");
  case 2:
    print "<li>Database upto date.</li>";
    break;
  default:
    die ("Unknown db version!!!");
}
print "</ul>";
?>
