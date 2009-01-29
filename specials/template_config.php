<?php 

require_once("templates/".$site_config['template']."/template_config.php");

if (isset($_POST['submit']) && $_POST['submit'] == "Submit")
{
  $template_data = template_global_config_post($_POST);
  
  if (is_array($template_data))
  {
    $content .= $template_data['error'];
  }
  else
  {
    if ($template_data != $site_config['template_data'])
    {
      if ($template_data == "")
      {
        mysql_do_query("DELETE FROM `cms_template_config` 
                              WHERE `template_name` = '".mysql_real_escape_string($site_config['template'])."'");
      }
      else
      {
        mysql_do_query("UPDATE `cms_template_config` 
                           SET `template_data`='".mysql_real_escape_string($template_data)."'
                         WHERE `template_name` = '".mysql_real_escape_string($site_config['template'])."'");
        
        if (mysql_affected_rows() == 0)
        {
          mysql_do_query("INSERT INTO `cms_template_config` 
                             SET `template_data`='".mysql_real_escape_string($template_data)."',
                                 `template_name` = '".mysql_real_escape_string($site_config['template'])."'");
        }
      }
    }
    #<link rel="stylesheet" href="/style.css" type="text/css"/>
    die('$("link[rel*=\'style\'][href^=\'/style.css\']").replaceWith(\'<link rel="stylesheet" href="/style.css?fr='.time().'" type="text/css"/>\');');
  }
}

$c = '<form action="/lp-admin.template" method="POST" id="config">';
$c .= template_global_config_form();
$c .= "<input type=\"button\" name=\"submit\" value=\"Submit\" onClick=\"postForm('config')\"></form>";

$content .= section("Global Template Config",$c);

?>