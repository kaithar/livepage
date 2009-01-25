<?php

$settings = Array (
  "title_bg" => "",
  "menu_bg"=> ""
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

if ($page['template_data'] != "")
{
  $s = explode(";", $page['template_data']);
  foreach ($s as $ss)
  {
    $ss = explode(":",$ss);
    $settings[$ss[0]] = $ss[1];
  } 
}

$menu_links = Array();
$menu_css = "";
$mylinks = mysql_do_query(
    "SELECT * 
       FROM `cms_menu`
  LEFT JOIN `cms_template_menu_config` ON `template_menu_id` = `item_id`
      WHERE `template_name` IS NULL
         OR `template_name` = '".mysql_real_escape_string($site_config['template'])."'
   ORDER BY `item_order` ASC");

while ($item = mysql_fetch_assoc($mylinks))
{
  if (isset($visible_categories[$item['item_category']]))
  {            
    /*
     * Per menu item css
     */
    $menu_item_css = "";
    
    if ($item['template_data'] != "")
    {
      $s = explode(";", $item['template_data']);
      foreach ($s as $ss)
      {
        $ss = explode(":",$ss);
        switch ($ss[0])
        {
          case "bg":
            $menu_item_css .= "background: ".$ss[1].";";
            break;
        }
      }
    }
    
    if ($menu_item_css != "")
    {
      if ($item['item_header'])
        $menu_css .= "div.linkbox span.linkbar".$item['item_id']." { ".$menu_item_css." }\n";
      else
        $menu_css .= "div.links a.linkbar".$item['item_id'].":hover { ".$menu_item_css." }\n";
    }
    
    $menu_links[] = $item;
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="Content-Type" content="text/html;"/>
  <link rel="stylesheet" href="/style.css" type="text/css"/>
  <title><?php echo $site_config['site_name']." - ".$page['page_title'];?></title>
  <?php
    if (file_exists('files/'.$domain.'/templates/html_head.php'))
      require_once('files/'.$domain.'/templates/html_head.php');
  ?>
  <style>
    <?php
      if ($settings['title_bg'] != "")
        echo "div.section div.title { background: ".$settings['title_bg']."; }\n";
      if ($settings['menu_bg'] != "")
        echo "div.links a:hover { background: ".$settings['menu_bg']."; }\n";
      echo $menu_css;
    ?>
  </style>
 </head>
 <body>
  <?php if (file_exists('files/'.$domain.'/templates/banner.php'))
     require_once('files/'.$domain.'/templates/banner.php'); ?>
  <table id="layout" cellspacing="0" cellpadding="0" width="100%">
   <tr>
    <td rowspan="3" id="td_sidebar">
     <a href="/"><img src="<?php echo $site_config['logo']?>" alt="The Cell" id="logo"/></a><br/>
     <div class="linkbox">
       <div class="links">
        <?php 
          foreach ($menu_links as $item)
          {
            /*
             * On with the displaying
             */
            if ($item['item_separator'] == 1)
            {
              echo "</div>\n<br/>\n<div class=\"links\">\n";
            }
            else if ($item['item_header'] == 1)
            {
              echo "</div>\n<br/>\n<span class=\"header linkbar{$item['item_id']}\">{$item['item_text']}</span>\n<div class=\"links\">\n";
            }
            else
            {
              echo "<a class=\"linkbar{$item['item_id']}\" href=\"{$item['item_url']}\">{$item['item_text']}</a>\n";
            }
          }
        ?>
      </div>
     </div>
     <?php
       if ($user["editcontent"] == 1)
       {
         ?>
         <br/>
         <br/>
         <div class="linkbox">
          <center><b>Admin</b></center>
          <div class="links">
					  <a href="<?php echo $page['path'];?>.config">Site Config</a>
					  <a href="/lp-admin">Site Structure</a>
						<a href="<?php echo $page['path'];?>.sidebar">Edit Sidebar</a>
			    </div>
					<?php if ($admining == 0) { ?>
					  <br/>
					  <center><b>Page editing</b></center>
					  <div class="links">
					    <?php if ($page['found'])
				      { ?>
                <a href="<?php echo $page['path'];?>.createsection">New Section</a>
                <a href="<?php echo $page['path'];?>.pageconfig">Page Settings</a>
                <a href="<?php echo $page['path'];?>.move">Move</a>
                <a href="<?php echo $page['path'];?>.delpage">Nuke Page</a>
					    <?php } ?>
            </div>
					<?php } ?>
         </div>
         <?php
       }
     ?>
    </td>
    <td id="td_content">
     <?php
      if ($path)
      {
       $c = "";
       foreach ($path as $v)
       {
         $c .= $v["cat_key"]."/";
         echo " &raquo; <a href=\"$c\">{$v["cat_title"]}</a>";
       }
			 if ($page["page_key"] != "index")
			 	echo " &raquo; <a href=\"$c{$page["page_key"]}\">{$page["page_title"]}</a><br/><br/>";
      }
     ?>