<?php

header("Content-Type: text/css");

if (file_exists('files/'.$domain.'/templates/style.php'))
{
  require_once('files/'.$domain.'/templates/style.php');
}
elseif (file_exists('files/'.$domain.'/templates/style.css'))
{
  require_once('files/'.$domain.'/templates/style.css');
}
else
{
  require_once("templates/".$site_config['template']."/style.php");
}

die();

?>