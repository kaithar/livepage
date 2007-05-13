<?
if ($vfile[3] == "CONFIRM")
{
  $page_id = mysql_real_escape_string($page['page_id']);

  $section = mysql_do_query("SELECT * FROM `cms_sections`
                              WHERE `page_id` = '$page_id'
                                AND `section_id` = '".mysql_real_escape_string($vfile[2])."'");

  if (mysql_num_rows($section) == 0) {
    header("location: ".$request);
    die();
  }

  $section = mysql_fetch_assoc($section);
  
  mysql_do_query("DELETE FROM `cms_sections` WHERE `section_id`='".mysql_real_escape_string($vfile[2])."' LIMIT 1");
  mysql_do_query("UPDATE `cms_sections` SET `order` = `order` - 1
                   WHERE `page_id`='$page_id'
                     AND `order`>='".mysql_real_escape_string($section['order'])."'");

  header("location: ".$request);
  die();
}

$content .= section("Delete section...", "Are you sure you want to delete this section? <a href=\"$request.del.$vfile[2].CONFIRM\">YES</a> / <a href=\"$request\">NO</a>");

?>

