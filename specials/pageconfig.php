<?

require_once("templates/".$site_config['template']."/template_config.php");

if (isset($_POST['Submit']) && $_POST['Submit'] == "Submit")
{
  $page_id = mysql_real_escape_string($page['page_id']);

  $title = mysql_real_escape_string($_POST['title']);

  mysql_do_query("UPDATE `cms_pages` SET `page_title`='$title'
                   WHERE `page_id`='".mysql_real_escape_string($page_id)."'");
  header("location: ".$page['path']);
  die();
}

if (isset($_POST['template']) && $_POST['template'] == "Submit")
{
  $template_data = template_page_config_post($_POST);
  if (is_array($template_data))
  {
    $content .= $template_data['error'];
  }
  else
  {
    if ($template_data != $page['template_data'])
    {
      if ($template_data == "")
      {
        mysql_do_query("DELETE FROM `cms_template_page_config` 
                              WHERE `template_name` = '".mysql_real_escape_string($site_config['template'])."'
                                AND `template_page_id` = '".mysql_real_escape_string($page['page_id'])."'");
      }
      else
      {
        mysql_do_query("UPDATE `cms_template_page_config` 
                           SET `template_data`='".mysql_real_escape_string($template_data)."'
                         WHERE `template_name` = '".mysql_real_escape_string($site_config['template'])."'
                           AND `template_page_id` = '".mysql_real_escape_string($page['page_id'])."'");
        
        if (mysql_affected_rows() == 0)
        {
          mysql_do_query("INSERT INTO `cms_template_page_config` 
                                  SET `template_data`='".mysql_real_escape_string($template_data)."',
                                      `template_page_id` = '".mysql_real_escape_string($page['page_id'])."',
                                      `template_name` = '".mysql_real_escape_string($site_config['template'])."'");
        }
      }
    }
    header("location: ".$page['path'].".pageconfig");
    die();
  }
}

$c = "<form action=\"{$page['path']}.pageconfig\" method=\"POST\">";
$c .= "Page title:<br><input type=\"text\" name=\"title\" size=\"95\" value=\"{$page['page_title']}\"><br>";
$c .= "<input type=\"Submit\" name=\"Submit\" value=\"Submit\"></form>";

$content .= section("Edit title...",$c);

$c = "<form action=\"{$page['path']}.pageconfig\" method=\"POST\">";
$c .= template_page_config_form($page);
$c .= "<input type=\"Submit\" name=\"template\" value=\"Submit\"></form>";

$content .= section("Page Template Config",$c);

?>
