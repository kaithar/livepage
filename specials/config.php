<?php

if (isset($_POST['Submit']) && $_POST['Submit'] == "Submit")
{
  $site_name = mysql_real_escape_string($_POST['site_name']);
	
  mysql_do_query("UPDATE `cms_config` 
                     SET `site_name`='$site_name'");
  header("location: ".$page['path'].".config");
  die();
}

$c = "<form action=\"{$page['path']}.config\" method=\"POST\">";
$c .= "Site name: <input type=\"text\" name=\"site_name\" size=\"95\" value=\"{$site_config['site_name']}\"><br/><br/>";
$c .= "<input type=\"Submit\" name=\"Submit\" value=\"Submit\"></form>";

$content .= section("General Config",$c);

?>