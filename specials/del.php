<?
if (isset($page['params'][1]) && isset($page['params'][2]) && ($page['params'][2] == "CONFIRM"))
{
  $page_id = mysql_real_escape_string($page['page_id']);

  $section = mysql_do_query("SELECT * FROM `cms_sections`
                              WHERE `page_id` = '$page_id'
                                AND `section_id` = '".mysql_real_escape_string($page['params'][1])."'");

  if (mysql_num_rows($section) == 0) {
    header("location: ".$page['path']);
    die();
  }

  $section = mysql_fetch_assoc($section);
  
  mysql_do_query("DELETE FROM `cms_sections` 
                        WHERE `section_id`='".mysql_real_escape_string($page['params'][1])."'
                        LIMIT 1");
  mysql_do_query("UPDATE `cms_sections`
                     SET `order` = `order` - 1
                   WHERE `page_id`='$page_id'
                     AND `order`>='".mysql_real_escape_string($section['order'])."'");

  header("location: ".$page['path']);
  die();
}

$content .= section("Delete section...", "Are you sure you want to delete this section? <a href=\"{$page['path']}.del.{$page['params'][1]}.CONFIRM\">YES</a> / <a href=\"{$page['path']}\">NO</a>");

?>

