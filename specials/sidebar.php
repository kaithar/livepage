<?php

require_once("includes/cat_tree.php");
require_once("templates/".$site_config['template']."/template_config.php");

$admining = 1;

$tree = build_cat_tree();

$menu = mysql_do_query(
    "SELECT * 
       FROM `cms_menu`
  LEFT JOIN `cms_template_menu_config` ON `template_menu_id` = `item_id`
      WHERE `template_name` IS NULL
         OR `template_name` = '".mysql_real_escape_string($site_config['template'])."'
   ORDER BY `item_order` ASC");
   
$length = mysql_num_rows($menu) - 1;

if (isset($page['params'][1]))
{
  /*
   * Swap requested... 
   */
  if (($page['params'][1] == "swap")&&isset($page['params'][2])&&$page['params'][3])
	{
		$lower = min($page['params'][2],$page['params'][3]);
		$upper = max($page['params'][2],$page['params'][3]);
		
		if (
			($lower < 0)||
			($upper > $length)||
			(($lower+1) != $upper))
		{
			$content .= "Sorry, that isn't valid. $lower $upper $last<br/><br/>";
		}
		else
		{
      /* Swap the two numbers round in the order using -1 as a temp
       */
			mysql_do_query("UPDATE `cms_menu`
												SET `item_order`= -1 
											WHERE `item_order`='".mysql_real_escape_string($lower)."'");
	
			mysql_do_query("UPDATE `cms_menu`
												SET `item_order`= '".mysql_real_escape_string($lower)."'
											WHERE `item_order`='".mysql_real_escape_string($upper)."'");
	
			mysql_do_query("UPDATE `cms_menu`
												SET `item_order`= '".mysql_real_escape_string($upper)."'
											WHERE `item_order`='-1'");
		} // End If ($lower & $upper == good) {} Else
    header("location: ".$page['path'].".sidebar");
	}
  else if ($page['params'][1] == "add")
  {
    /*
     * Add an entry ... use the submit button used to decide what to insert.
     */
    if (isset($_POST['link']) && ($_POST['link'] == "Add as link"))
    {
      /* Add a normal link to the menu */
      mysql_do_query("INSERT INTO `cms_menu`
                              SET `item_text` = '".mysql_real_escape_string($_POST['mtext'])."',
                                  `item_url` = '".mysql_real_escape_string($_POST['murl'])."',
                                  `item_category` = '".mysql_real_escape_string($_POST['mcategory'])."',
                                  `item_order` = '".mysql_real_escape_string($length+1)."',
                                  `item_separator` = '0',
                                  `item_header` = '0'");
    }
    else if (isset($_POST['separator']) && ($_POST['separator'] == "Add as separator"))
    {
      /* Add a separator to the menu */
      mysql_do_query("INSERT INTO `cms_menu`
                              SET `item_text` = 'Separator',
                                  `item_url` = 'Separator',
                                  `item_category` = '".mysql_real_escape_string($_POST['mcategory'])."',
                                  `item_order` = '".mysql_real_escape_string($length+1)."',
                                  `item_separator` = '1',
                                  `item_header` = '0'");
    }
    else if (isset($_POST['header']) && ($_POST['header'] == "Add as header"))
    {
      /* Add a header to the menu */
      mysql_do_query("INSERT INTO `cms_menu`
                              SET `item_text` = '".mysql_real_escape_string($_POST['mtext'])."',
                                  `item_url` = 'Header',
                                  `item_category` = '".mysql_real_escape_string($_POST['mcategory'])."',
                                  `item_order` = '".mysql_real_escape_string($length+1)."',
                                  `item_separator` = '0',
                                  `item_header` = '1'");
    }
    /* In any case, go back to the sidebar editing page */
    header("location: ".$page['path'].".sidebar");
    die();
  }
  else if (($page['params'][1] == "edit")&&(isset($page['params'][2])))
  {
    /*
     * Edit an entry.
     * Depending on the type, hide certain parts of the form.
     */
    
    while ($menuitem = mysql_fetch_assoc($menu))
    {
      if ($menuitem['item_id'] == $page['params'][2])
        break;
    }
    
    if (isset($_POST['submit']) && ($_POST['submit'] == "Submit"))
    {
      /*
       * Submitted form?!
       */
      mysql_do_query("UPDATE `cms_menu` 
                         SET `item_text` = '".mysql_real_escape_string($_POST['mtext'])."',
                             `item_url` = '".mysql_real_escape_string($_POST['murl'])."',
                             `item_category` = '".mysql_real_escape_string($_POST['mcategory'])."'
                       WHERE `item_id` = '".mysql_real_escape_string($page['params'][2])."'");
      
      $template_data = template_menu_config_post($_POST);
      
      if (!is_array($template_data))
      {
        if ($template_data != $menuitem['template_data'])
        {
          if ($template_data == "")
          {
            mysql_do_query("DELETE FROM `cms_template_menu_config` 
                                  WHERE `template_name` = '".mysql_real_escape_string($site_config['template'])."'
                                    AND `template_menu_id` = '".mysql_real_escape_string($menuitem['item_id'])."'");
          }
          else
          {
            mysql_do_query("UPDATE `cms_template_menu_config` 
                               SET `template_data`='".mysql_real_escape_string($template_data)."'
                             WHERE `template_name` = '".mysql_real_escape_string($site_config['template'])."'
                               AND `template_menu_id` = '".mysql_real_escape_string($menuitem['item_id'])."'");
        
            if (mysql_affected_rows() == 0)
            {
              mysql_do_query("INSERT INTO `cms_template_menu_config` 
                                      SET `template_data`='".mysql_real_escape_string($template_data)."',
                                          `template_menu_id` = '".mysql_real_escape_string($menuitem['item_id'])."',
                                          `template_name` = '".mysql_real_escape_string($site_config['template'])."'");
            }
          }
        }
      }
      
      header("location: ".$page['path'].".sidebar");
      die();
    }
    
    $c = "<form action=\"{$page['path']}.sidebar.edit.{$page['params'][2]}\" method=\"POST\">";
    $c .= '<table border="0" cellpadding="5" cellspacing="0">';
    
    /*
     * Always display the category.
     */
    $c .= "<tr><td>Category:</td>";
    $c .= "<td><select name=\"mcategory\" size=\"1\"/>";
    $c .= return_cat_tree_select($tree['tree'], $menuitem['item_category'])."</select></td></tr>";
    
    $h = "";
    
    /*
     * Separators don't have menu text, headers and links do.
     */
    if ($menuitem['item_separator'] == 1)
    {
      $h .= "<input type=\"hidden\" name=\"mtext\" value=\"{$menuitem['item_text']}\" size=\"50\"/>";
    }
    else
    {
      $c .= "<tr><td>Menu text:</td>";
      $c .= "<td><input type=\"text\" name=\"mtext\" value=\"{$menuitem['item_text']}\" size=\"50\"/></td></tr>";
    }
    
    /*
     * Separators and headers don't have urls, links do.
     */
    if (($menuitem['item_separator'] == 1)||($menuitem['item_header'] == 1))
    {
      $h .= "<input type=\"hidden\" name=\"murl\" value=\"{$menuitem['item_url']}\" size=\"50\"/>";
    }
    else
    {
      $c .= "<tr><td>Menu link:</td>";
      $c .= "<td><input type=\"text\" name=\"murl\" value=\"{$menuitem['item_url']}\" size=\"50\"/></td></tr>";
    }
    
    /*
     * Templating stuff
     */
    
    $c .= "<tr><td>Templating:</td><td>".template_menu_config_form($menuitem)."</td></tr>";

    
    /*
     * Finish the form!
     */

    $c .= "<tr><td colspan=\"2\">{$h}<input type=\"submit\" name=\"submit\" value=\"Submit\"/></td></tr></table></form>";
    
    $title = "Link";
    if ($menuitem['item_header'] == 1) $title = "Header";
    if ($menuitem['item_separator'] == 1) $title = "Separator";
    
    $content .= section("Edit menu item: ".$title,$c);
  }
  else if (($page['params'][1] == "delete")&&(isset($page['params'][2])))
  {
    /*
     * Remove an item from the menu.
     */
    $menuitem = mysql_do_query("SELECT * FROM `cms_menu`
        WHERE `item_id` = '".mysql_real_escape_string($page['params'][2])."'");

    if (mysql_num_rows($menuitem) == 0) {
      header("location: ".$page['path'].".sidebar");
      die();
    }

    $menuitem = mysql_fetch_assoc($menuitem);
  
    mysql_do_query("DELETE FROM `cms_menu` 
        WHERE `item_id`='".mysql_real_escape_string($page['params'][2])."'
        LIMIT 1");
    mysql_do_query("UPDATE `cms_menu`
        SET `item_order` = `item_order` - 1
        WHERE `item_order`>='".mysql_real_escape_string($menuitem['item_order'])."'");

    header("location: ".$page['path'].".sidebar");
    die();
  }
}
else
{
  /*
   * Render the sidebar overview page.
   */
  
  $c = "<table border=\"1\" cellpadding=\"5\">";
  $c .= "<tr><th>Category</th><th>Menu Text</th><th>Target url</th><th>Actions</th></tr>";
  
  while ($item = mysql_fetch_assoc($menu))
  {
    $c .= "<tr><td>{$tree['ids'][$item['item_category']]['flat_path']}</td>";
    
    if ($item['item_separator'] == 1)
    {
      /* Separators don't have content...*/
      $c .= "<td colspan=\"2\"><i>Separator</i></td><td>";
    }
    else if ($item['item_header'] == 1)
    {
      /* Headers only have text...*/
      $c .= "<td colspan=\"2\"><b>{$item['item_text']}</b></td><td>";
    }
    else
    {
      /* Links have text and a link...*/
      $c .= "<td>{$item['item_text']}</td><td>{$item['item_url']}</td><td>";
    }
    
    if ($item['item_order'] > 0) 
      $c .= "<a href=\"{$page['path']}.sidebar.swap.".($item['item_order']-1).".{$item['item_order']}\">Move up</a> / ";
    else
      $c .= "Move up / ";
    
    if ($item['item_order'] < $length)
      $c .= "<a href=\"{$page['path']}.sidebar.swap.{$item['item_order']}.".($item['item_order']+1)."\">Move down</a> / ";
    else
      $c .= "Move down / ";
    
    $c .= "<a href=\"{$page['path']}.sidebar.edit.{$item['item_id']}\">Edit</a> / ";
    $c .= "<a href=\"{$page['path']}.sidebar.delete.{$item['item_id']}\">Delete</a>";
  }
  
  $c .= "</table>";
  
  /*---------------
   * New sidebar entry
   */
    
  $c .= "<br/><br/><b>New Entry</b>";
  $c .= "<form action=\"{$page['path']}.sidebar.add\" method=\"POST\">";
  $c .= '<table border="0" cellpadding="5" cellspacing="0">';
  
  $c .= "<tr><td>Category:</td>";
  $c .= "<td><select name=\"mcategory\" size=\"1\"/>".return_cat_tree_select($tree['tree'])."</select></td>";
  $c .= "<td>-- Category to display in.  Used for all three sidebar entry types.</td></tr>";
  
  $c .= "<tr><td>Menu text:</td>";
  $c .= "<td><input type=\"text\" name=\"mtext\" size=\"50\"/></td>";
  $c .= "<td>-- The text displayed for this entry.  Used for links and headers.</td></tr>";
  
  $c .= "<tr><td>Menu link:</td>";
  $c .= "<td><input type=\"text\" name=\"murl\" size=\"50\"/></td>";
  $c .= "<td>-- The location the link points to.  Only used for links.</td></tr>";
  
  $c .= "<tr><td colspan=\"3\"><input type=\"submit\" name=\"separator\" value=\"Add as separator\"/>
                               <input type=\"submit\" name=\"header\" value=\"Add as header\"/>
                               <input type=\"submit\" name=\"link\" value=\"Add as link\"/>
      </td></tr></table></form>";
  
  
  $content .= section("Sidebar config",$c);
}
?>