<?php

function include_header ()
{
  if (file_exists('files/'.$domain.'/templates/header.php'))
  {
    require_once('files/'.$domain.'/templates/header.php');
  }
  else
  {
    require_once("templates/header.php");
  }
}

function include_footer ()
{
  if (file_exists('files/'.$domain.'/templates/footer.php'))
  {
    require_once('files/'.$domain.'/templates/footer.php');
  }
  else
  {
    require_once("templates/footer.php");
  }
}

if (file_exists('files/'.$domain.'/templates/section.php'))
{
  require_once('files/'.$domain.'/templates/section.php');
}
else
{
  require_once("templates/section.php");
}

?>