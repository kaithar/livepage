<?
if ($vfile[2] == "CONFIRM")
{
  $page_id = mysql_real_escape_string($page['page_id']);

  mysql_do_query("DELETE FROM `cms_sections` WHERE `page_id`='$page_id'");
  mysql_do_query("DELETE FROM `cms_pages` WHERE `page_id`='$page_id'");
  mysql_do_query("UPDATE `cms_pages`
                     SET `page_parent` = '".mysql_real_escape_string($page['page_parent'])."'
                   WHERE `page_parent` = '$page_id'");
  
  header("location: {$page['parent_url']}");
  die();
}

$content .= section("Delete PAGE...", "Are you sure you want to delete this PAGE? <a href=\"$request.delpage.CONFIRM\">YES</a> / <a href=\"$request\">NO</a>");

?>

