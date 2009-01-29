<?php

if (isset($_POST['submit']) && $_POST['submit'] == "Submit")
{
  $site_name = mysql_real_escape_string($_POST['site_name']);
  $footer = mysql_real_escape_string($_POST['footer']);
	
  mysql_do_query("UPDATE `cms_config` 
                     SET `site_name`='$site_name',
                         `footer` = '$footer'");

  die('setHTML("footerDiv","'.$footer.'");window.top.document.title = "'.$site_name.' - Admin Interface";');
}

$c = '<form action="/lp-admin.config" method="POST" id="config">';
$c .= "Site name: <input type=\"text\" name=\"site_name\" size=\"95\" value=\"{$site_config['site_name']}\"><br/><br/>";
$c .= "Site footer: <input type=\"text\" name=\"footer\" size=\"95\" value=\"{$site_config['footer']}\"><br/><br/>";
$c .= "<input type=\"button\" name=\"submit\" value=\"Submit\" onClick=\"postForm('config')\"></form>";

$content .= section("General Config",$c);
?>