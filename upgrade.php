<?php

define("INSTALLER", true);

print "<b>Attempting to load configs...</b><br/>";

require_once("includes/env_init.php");

print "<b>Grabbing config data...</b><br/>";

$sql = mysql_query("SELECT * FROM `cms_config`") or die("Failed!");
$site_config = mysql_fetch_assoc($sql);

print "<b>Attempting to upgrade...</b><br/>";
print "<i>Database at version: ".$site_config["db_revision"]."</i><br/><br/>";

switch($site_config["db_revision"])
{
  case 1:
    
    print "Database upto date.<br/>";
    break;
  default: die ("Unknown db version!!!");
}
?>
