<?
if (isset($_POST['Submit']) && $_POST['Submit'] == "Submit")
{
  $page_id = mysql_real_escape_string($page['page_id']);

  $title = mysql_real_escape_string($_POST['title']);

  mysql_do_query("UPDATE `cms_pages` SET `page_title`='$title'
                   WHERE `page_id`='".mysql_real_escape_string($page_id)."'");
  header("location: ".$page['path']);
  die();
}

$c = "<form action=\"{$page['path']}.edittitle\" method=\"POST\">";
$c .= "Page title:<br><input type=\"text\" name=\"title\" size=\"95\" value=\"{$page['page_title']}\"><br>";
$c .= "<input type=\"Submit\" name=\"Submit\" value=\"Submit\"></form>";

$content .= section("Edit title...",$c);
?>
