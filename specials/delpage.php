<?php

if (isset($page['params'][1]) && isset($page['params'][2]) && ($page['params'][2] == "CONFIRM"))
{
  $page_id = mysql_real_escape_string($page['page_id']);

  mysql_do_query("DELETE FROM `cms_sections` WHERE `page_id`='$page_id'");
  mysql_do_query("DELETE FROM `cms_pages` WHERE `page_id`='$page_id'");
  
  header("location: {$page['parent_path']}");
  die();
}

$content .= section("Delete PAGE...", "Are you sure you want to delete this PAGE? <a href=\"{$page['path']}.delpage.CONFIRM\">YES</a> / <a href=\"{$page['path']}\">NO</a>");

?>

