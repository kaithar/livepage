<?php

/*** Global config ****/

function template_global_config_form ()
{
  global $site_config;
  
  $settings = Array (
    "title_bg" => "",
    "menu_bg" => ""
  );

  if ($site_config['template_data'] != "")
  {
    $s = explode(";", $site_config['template_data']);
    foreach ($s as $ss)
    {
      $ss = explode(":",$ss);
      $settings[$ss[0]] = $ss[1];
    }
  }
  
  $c = "Section title background colour: ";
  $c .= "<input type=\"text\" name=\"title_bg\" size=\"95\" value=\"{$settings['title_bg']}\"/><br/><br/>";
  $c .= "Menu hover background colour: ";
  $c .= "<input type=\"text\" name=\"menu_bg\" size=\"95\" value=\"{$settings['menu_bg']}\"/><br/><br/>";
  return $c;
}

function template_global_config_post($post)
{
  global $site_config;
  $template_data = Array();
  
  $title_bg = trim($post['title_bg']);
  $menu_bg = trim($post['menu_bg']);
  
  if ($title_bg)
  {
    if (preg_match("/^#[0-9a-fA-F]{1,6}$/", $title_bg))
      $template_data[] = "title_bg:".$title_bg;
    else
      return Array('error' => "Please use only standard html hex notation colours.<br/><br/>");
  }
  
  if ($menu_bg)
  {
    if (preg_match("/^#[0-9a-fA-F]{1,6}$/", $menu_bg))
      $template_data[] = "menu_bg:".$menu_bg;
    else
      return Array('error' => "Please use only standard html hex notation colours.<br/><br/>");
  }
  
  return implode(";", $template_data);
}

/***** Page config ****/
    
function template_page_config_form ($page)
{
  $settings = Array (
      "title_bg" => "",
      "menu_bg" => ""
  );

  if ($page['template_data'] != "")
  {
    $s = explode(";", $page['template_data']);
    foreach ($s as $ss)
    {
      $ss = explode(":",$ss);
      $settings[$ss[0]] = $ss[1];
    }
  }
  
  $c = "Section title background colour: ";
  $c .= "<input type=\"text\" name=\"title_bg\" size=\"95\" value=\"{$settings['title_bg']}\"/><br/><br/>";
  $c .= "Menu hover background colour: ";
  $c .= "<input type=\"text\" name=\"menu_bg\" size=\"95\" value=\"{$settings['menu_bg']}\"/><br/><br/>";
  return $c;
}

function template_page_config_post($post)
{
  global $site_config;
  $template_data = Array();
  
  $title_bg = trim($post['title_bg']);
  $menu_bg = trim($post['menu_bg']);
  
  if ($title_bg)
  {
    if (preg_match("/^#[0-9a-fA-F]{1,6}$/", $title_bg))
      $template_data[] = "title_bg:".$title_bg;
    else
      return Array('error' => "Please use only standard html hex notation colours.<br/><br/>");
  }
  
  if ($menu_bg)
  {
    if (preg_match("/^#[0-9a-fA-F]{1,6}$/", $menu_bg))
      $template_data[] = "menu_bg:".$menu_bg;
    else
      return Array('error' => "Please use only standard html hex notation colours.<br/><br/>");
  }
  
  return implode(";", $template_data);
}

/***** Section config ****/
    
function template_section_config_form ($section)
{
  $settings = Array (
    "title_bg" => "",
    "noborder" => 0
  );

  if ($section['template_data'] != "")
  {
    $s = explode(";", $section['template_data']);
    foreach ($s as $ss)
    {
      $ss = explode(":",$ss);
      $settings[$ss[0]] = $ss[1];
    }
  }
  
  $c = "Section title background colour (Optional): ";
  $c .= "<input type=\"text\" name=\"title_bg\" size=\"25\" value=\"{$settings['title_bg']}\"/><br/><br/>";
  $c .= "Hide borders: <input type=\"checkbox\" name=\"noborder\" size=\"25\"".($settings['noborder']?" CHECKED":"")."/><br/><br/>";
  return $c;
}

function template_section_config_post($post)
{
  $template_data = Array();
  
  $title_bg = trim($post['title_bg']);
  $noborder = isset($post['noborder'])?true:false;
  
  if ($title_bg)
  {
    if (preg_match("/^#[0-9a-fA-F]{1,6}$/", $title_bg))
      $template_data[] = "title_bg:".$title_bg;
    else
      return Array('error' => "Please use only standard html hex notation colours.<br/><br/>");
  }
  
  if ($noborder)
    $template_data[] = "noborder:1";
  
  return implode(";", $template_data);
}

/***** Menu config ****/

function template_menu_config_form ($item)
{
  $settings = Array (
    "bg" => ""
  );

  if ($item['template_data'] != "")
  {
    $s = explode(";", $item['template_data']);
    foreach ($s as $ss)
    {
      $ss = explode(":",$ss);
      $settings[$ss[0]] = $ss[1];
    }
  }
  
  $c = "Background colour, hover on links, normal on headers (Optional): ";
  $c .= "<input type=\"text\" name=\"bg\" size=\"25\" value=\"{$settings['bg']}\"/>";
  return $c;
}

function template_menu_config_post($post)
{
  $template_data = Array();
  
  $bg = trim($post['bg']);
  
  if ($bg)
  {
    if (preg_match("/^#[0-9a-fA-F]{1,6}$/", $bg))
      $template_data[] = "bg:".$bg;
    else
      return Array('error' => "Please use only standard html hex notation colours.<br/><br/>");
  }
  
  return implode(";", $template_data);
}

?>