<?php

if (isset($_POST['Submit']) && ($_POST['Submit'] == "Submit") && isset($page['params'][1]))
{
  $page_id = mysql_real_escape_string($page['page_id']);

  $title = mysql_real_escape_string($_POST['title']);
  $content = mysql_real_escape_string($_POST['content']);

  mysql_do_query("UPDATE `cms_sections` SET `section_title`='$title', `section_text`='$content' 
                   WHERE `section_id`='".mysql_real_escape_string($page['params'][1])."'");
  header("location: ".$page['path']);
  die();
}

$page_id = mysql_real_escape_string($page['page_id']);

$section = mysql_do_query("SELECT *
                             FROM `cms_sections` 
                            WHERE `page_id` = '$page_id'
                              AND `section_id` = '".mysql_real_escape_string($page['params'][1])."'");

if (mysql_num_rows($section) == 0) {
  header("location: ".$page['path']);
  die();
}

$section = mysql_fetch_assoc($section);

$foo = "<form action=\"{$page['path']}.edit.{$page['params'][1]}\" method=\"POST\">";
$foo .= "Section title:<br><input type=\"text\" name=\"title\" size=\"95\" value=\"{$section['section_title']}\"><br><br>";
$foo .= "Content:<br><textarea name=\"content\" cols=\"80\" rows=\"10\">{$section['section_text']}</textarea><br>";
$foo .= "<input type=\"Submit\" name=\"Submit\" value=\"Submit\"></form>"; 

$content .= section("Edit...", $foo);

?>
