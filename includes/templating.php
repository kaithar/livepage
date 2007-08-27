<?php

function select_header ()
{
  if (file_exists('files/'.$domain.'/templates/header.php'))
  {
    return 'files/'.$domain.'/templates/header.php';
  }
  else
  {
    return "templates/header.php";
  }
}

function select_footer ()
{
  if (file_exists('files/'.$domain.'/templates/footer.php'))
  {
    return 'files/'.$domain.'/templates/footer.php';
  }
  else
  {
    return "templates/footer.php";
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