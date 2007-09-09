<?php

function select_header ()
{
  global $domain, $site_config;
  if (file_exists('files/'.$domain.'/templates/header.php'))
  {
    return 'files/'.$domain.'/templates/header.php';
  }
  else
  {
    return "templates/".$site_config['template']."/header.php";
  }
}

function select_footer ()
{
  global $domain, $site_config;
  if (file_exists('files/'.$domain.'/templates/footer.php'))
  {
    return 'files/'.$domain.'/templates/footer.php';
  }
  else
  {
    return "templates/".$site_config['template']."/footer.php";
  }
}

if (file_exists('files/'.$domain.'/templates/section.php'))
{
  require_once('files/'.$domain.'/templates/section.php');
}
else
{
  require_once("templates/".$site_config['template']."/section.php");
}

?>