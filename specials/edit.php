<?php

require_once("templates/".$site_config['template']."/template_config.php");

$page_id = mysql_real_escape_string($page['page_id']);

$section = mysql_do_query("SELECT *
    FROM `cms_sections`
    LEFT JOIN `cms_template_section_config` ON `template_section_id` = `section_id`
    WHERE `section_id` = '".mysql_real_escape_string($page['params'][1])."'
    AND ( `template_name` IS NULL
    OR `template_name` = '".mysql_real_escape_string($site_config['template'])."'
                         )");

if (mysql_num_rows($section) == 0) {
  header("location: ".$page['path']);
  die();
}

$section = mysql_fetch_assoc($section);

if (isset($_POST['Submit']) && ($_POST['Submit'] == "Submit") && isset($page['params'][1]))
{
  $page_id = mysql_real_escape_string($page['page_id']);

  $title = mysql_real_escape_string($_POST['title']);
  $content = mysql_real_escape_string($_POST['content']);

  mysql_do_query("UPDATE `cms_sections` SET `section_title`='$title', `section_text`='$content' 
                   WHERE `section_id`='".mysql_real_escape_string($page['params'][1])."'");
  
  $template_data = template_section_config_post($_POST);
  
  if (!is_array($template_data))
  {
    if ($template_data != $section['template_data'])
    {
      if ($template_data == "")
      {
        mysql_do_query("DELETE FROM `cms_template_section_config` 
                              WHERE `template_name` = '".mysql_real_escape_string($site_config['template'])."'
                                AND `template_section_id` = '".mysql_real_escape_string($section['section_id'])."'");
      }
      else
      {
        mysql_do_query("UPDATE `cms_template_section_config` 
                          SET `template_data`='".mysql_real_escape_string($template_data)."'
                        WHERE `template_name` = '".mysql_real_escape_string($site_config['template'])."'
                          AND `template_section_id` = '".mysql_real_escape_string($section['section_id'])."'");
        
        if (mysql_affected_rows() == 0)
        {
          mysql_do_query("INSERT INTO `cms_template_section_config` 
                                  SET `template_data`='".mysql_real_escape_string($template_data)."',
                                      `template_section_id` = '".mysql_real_escape_string($section['section_id'])."',
                                      `template_name` = '".mysql_real_escape_string($site_config['template'])."'");
        }
      }
    }
  }
  
  header("location: ".$page['path']);
  die();
}

$foo = "<form action=\"{$page['path']}.edit.{$page['params'][1]}\" method=\"POST\">";
$foo .= "Section title:<br><input type=\"text\" name=\"title\" size=\"95\" value=\"{$section['section_title']}\"><br><br/>";
$foo .= "Content:<br><textarea name=\"content\" cols=\"80\" rows=\"10\">{$section['section_text']}</textarea><br/><br/>";
$foo .= template_section_config_form($section);
$foo .= "<input type=\"Submit\" name=\"Submit\" value=\"Submit\"></form>"; 

$content .= section("Edit...", $foo);

?>
