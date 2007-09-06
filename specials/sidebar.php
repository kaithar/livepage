<?php

require_once("includes/cat_tree.php");

$admining = 1;

$tree = build_cat_tree();

$menu = mysql_do_query("SELECT * FROM `cms_menu` ORDER BY `item_order` ASC");
$length = mysql_num_rows($menu) - 1;

if (isset($page['params'][1]))
{
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
    if (isset($_POST['submit']) && ($_POST['submit'] == "Add"))
    {
      mysql_do_query("INSERT INTO `cms_menu`
                              SET `item_text` = '".mysql_real_escape_string($_POST['mtext'])."',
                                  `item_url` = '".mysql_real_escape_string($_POST['murl'])."',
                                  `item_category` = '".mysql_real_escape_string($_POST['mcategory'])."',
                                  `item_order` = '".mysql_real_escape_string($length+1)."',
                                  `item_separator` = '0'");
    }
    header("location: ".$page['path'].".sidebar");
    die();
  }
  else if (($page['params'][1] == "separator") && (isset($page['params'][2])) && ($page['params'][2] == "add"))
  {
    mysql_do_query("INSERT INTO `cms_menu`
                            SET `item_text` = 'Separator',
                                `item_url` = 'Separator',
                                `item_category` = '".mysql_real_escape_string($_POST['mcategory'])."',
                                `item_order` = '".mysql_real_escape_string($length+1)."',
                                `item_separator` = '1'");
    header("location: ".$page['path'].".sidebar");
    die();
  }
  else if (($page['params'][1] == "edit")&&(isset($page['params'][2])))
  {
    if (isset($_POST['submit']) && ($_POST['submit'] == "Submit"))
    {
      mysql_do_query("UPDATE `cms_menu` 
                         SET `item_text` = '".mysql_real_escape_string($_POST['mtext'])."',
                             `item_url` = '".mysql_real_escape_string($_POST['murl'])."',
                             `item_category` = '".mysql_real_escape_string($_POST['mcategory'])."'
                       WHERE `item_id` = '".mysql_real_escape_string($page['params'][2])."'");
      header("location: ".$page['path'].".sidebar");
      die();
    }
    $menuitem = mysql_do_query("SELECT *
                                  FROM `cms_menu`
                                 WHERE `item_id` = '".mysql_real_escape_string($page['params'][2])."'");
    $menuitem = mysql_fetch_assoc($menuitem);
    
    $c = "<form action=\"{$page['path']}.sidebar.edit.{$page['params'][2]}\" method=\"POST\">";
    $c .= '<table border="0" cellpadding="5" cellspacing="0">';
    
    $c .= "<tr><td>Category:</td>";
    $c .= "<td><select name=\"mcategory\" size=\"1\"/>";
    $c .= return_cat_tree_select($tree['tree'], $menuitem['item_category'])."</select></td></tr>";
    
    $c .= "<tr><td>Menu text:</td>";
    $c .= "<td><input type=\"text\" name=\"mtext\" value=\"{$menuitem['item_text']}\" size=\"50\"/></td></tr>";
    
    $c .= "<tr><td>Menu link:</td>";
    $c .= "<td><input type=\"text\" name=\"murl\" value=\"{$menuitem['item_url']}\" size=\"50\"/></td></tr>";
    
    $c .= "<tr><td colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Submit\"/></td></tr></table></form>";
    $content .= section("Edit menu item",$c);
  }
  else if (($page['params'][1] == "delete")&&(isset($page['params'][2])))
  {
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
  $c = "<table border=\"1\" cellpadding=\"5\">";
  $c .= "<tr><th>Category</th><th>Menu Text</th><th>Target url</th><th>Actions</th></tr>";
  
  while ($item = mysql_fetch_assoc($menu))
  {
    $c .= "<tr><td>{$tree['ids'][$item['item_category']]['flat_path']}</td>";
    if ($item['item_separator'] == 0)
    {
      $c .= "<td>{$item['item_text']}</td><td>{$item['item_url']}</td><td>";
    }
    else
    {
      $c .= "<td colspan=\"2\">Separator</td><td>";
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
  
  /*----------------
   * New separator
   */
  $c .= "<br/><br/><b>New Separator</b>";
  $c .= "<form action=\"{$page['path']}.sidebar.separator.add\" method=\"POST\">";
  $c .= '<table border="0" cellpadding="5" cellspacing="0">';
  
  $c .= "<tr><td>Category:</td>";
  $c .= "<td><select name=\"mcategory\" size=\"1\"/>".return_cat_tree_select($tree['tree'])."</select></td></tr>";
  
  $c .= "<tr><td colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Add\"/></td></tr></table></form>";
  
  /*---------------
   * New sidebar entry
   */
    
  $c .= "<br/><br/><b>New Entry</b>";
  $c .= "<form action=\"{$page['path']}.sidebar.add\" method=\"POST\">";
  $c .= '<table border="0" cellpadding="5" cellspacing="0">';
  
  $c .= "<tr><td>Category:</td>";
  $c .= "<td><select name=\"mcategory\" size=\"1\"/>".return_cat_tree_select($tree['tree'])."</select></td></tr>";
  
  $c .= "<tr><td>Menu text:</td>";
  $c .= "<td><input type=\"text\" name=\"mtext\" size=\"50\"/></td></tr>";
  
  $c .= "<tr><td>Menu link:</td>";
  $c .= "<td><input type=\"text\" name=\"murl\" size=\"50\"/></td></tr>";
  
  $c .= "<tr><td colspan=\"2\"><input type=\"submit\" name=\"submit\" value=\"Add\"/></td></tr></table></form>";
  
  
  $content .= section("Sidebar config",$c);
}
?>