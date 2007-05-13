<?
$showpage = 1;
$page_id = mysql_real_escape_string($page['page_id']);

$sections = mysql_do_query("SELECT * FROM `cms_sections` WHERE `page_id` = '$page_id'");
$last = mysql_num_rows($sections);

$lower = min($vfile[2],$vfile[3]);
$upper = max($vfile[2],$vfile[3]);
if (
  ($lower < 0)||
  ($upper > $last)||
  (($lower+1) != $upper))
{
  $content .= "Sorry, that isn't valid. $lower $upper $last";
}
else
{
  mysql_do_query("UPDATE `cms_sections`
                     SET `order`= -1 
                   WHERE `page_id` = '$page_id'
                     AND `order`='".mysql_real_escape_string($lower)."'");

  mysql_do_query("UPDATE `cms_sections`
                     SET `order`= '".mysql_real_escape_string($lower)."'
                   WHERE `page_id` = '$page_id'
                     AND `order`='".mysql_real_escape_string($upper)."'");

  mysql_do_query("UPDATE `cms_sections`
                     SET `order`= '".mysql_real_escape_string($upper)."'
                   WHERE `page_id` = '$page_id'
                     AND `order`='-1'");
} // End If ($lower & $upper == good) {} Else
?>
