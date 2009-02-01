<?php 

require_once("includes/cat_tree.php");

$tree = build_cat_tree();

function makePagesDiv($cat)
{
	global $tree;
	$cts = "";
	$scts = "";
	foreach ($tree['flat'] as $tcat)
	{
		$cts .= "<option value=\"{$tcat['cat_id']}\"".(($tcat['cat_id'] == $cat['cat_id'])?" selected=\"selected\"":"").">";
		$cts .= $tcat['path']."</option>";
		$foo = strpos($tcat['path'], $cat['path']);
		if (($foo === FALSE) || ($foo > 1))
		{
			$scts .= "<option value=\"{$tcat['cat_id']}\"".(($tcat['cat_id'] == $cat['cat_parent'])?" selected=\"selected\"":"").">";
			$scts .= $tcat['path']."</option>";
		}
	}
	
  $result = mysql_do_query("SELECT *
		                      FROM `cms_pages` 
				             WHERE `page_category` = '".mysql_real_escape_string($cat['cat_id'])."'");
  $c = "<div>[<a href=\"javascript:showAllDetails()\">Expand all</a>] ".
  		"[<a href=\"javascript:hideAllDetails()\">Collapse all</a>] ".
		"[<a href=\"javascript:showNewFolder()\">New Subfolder</a>] ".
		"[<a href=\"javascript:showNewPage()\">New Page</a>] ".
		($cat['cat_id'] == 1 ? '' :
			"[<a href=\"javascript:showMoveCat()\">Move folder</a>] ".
  			"[<a href=\"javascript:showNukeCat()\">Delete folder</a>] "
		).
  		"<br/>".
  	'<div id="newFolder" style="display: none; padding: 10px;">'.
  		'<form action="/lp-admin.structure.newFolder.'.$cat['cat_id'].'" method="POST" id="newFolderForm">'.
			"Create folder ".$cat['path']." <input type=\"text\" name=\"folder_name\" size=\"35\" value=\"\"/> ".
			"<input type=\"button\" name=\"submit\" value=\"Submit\" onClick=\"postForm('newFolderForm')\"/></form>".
	'</div>'.
  	'<div id="newPage" style="display: none; padding: 10px;">'.
  		'<form action="/lp-admin.structure.newPage.'.$cat['cat_id'].'" method="POST" id="newPageForm">'.
			"Create page ".$cat['path']." <input type=\"text\" name=\"page_name\" size=\"35\" value=\"\"/> ".
			"<input type=\"button\" name=\"submit\" value=\"Submit\" onClick=\"postForm('newPageForm')\"/></form>".
	'</div>'.
	'<div id="moveCat" style="display: none; padding: 0px 0px 0px 50px;">'.
		'<form action="/lp-admin.structure.moveCat.'.$cat['cat_id'].'" method="POST" id="mvCatfrm">'.
				'Move to: <select name="category">'.$scts.'</select> '.
				'<input type="text" name="location" size="35" value="'.$cat['cat_key'].'"> '.
  				'<input type="button" name="submit" value="Submit" onClick="postForm(\'mvCatfrm\')"/></form>'.
	'</div>'.
	'<div id="nukeCat" style="display: none; padding: 0px 0px 0px 50px;">'.
		'<form action="/lp-admin.structure.nukeCat.'.$cat['cat_id'].'" method="POST" id="nukeCatFrm">'.
		'Are you sure you want to <b>DELETE</b> this folder? Yes: <input name="sure" type="checkbox" value="1"/> '.
		'<input type="button" name="submit" value="Submit" onClick="postForm(\'nukeCatFrm\')"/></form>'.
	'</div>'.
  "<br/>".
  	"Trail: ".$cat['flat_path']."<br/>".
	"Path: ".$cat['path']."<br/><ul>";
  while ($row = mysql_fetch_assoc($result))
  {
  	$c .= '<li id="pageli'.$row['page_id'].'"><a class="pagekey" href="javascript:toggleDetails(\'pageli'.$row['page_id'].'\')">'.$row['page_key'].'</a>'.
  			'<div class="controls" style="display: none; padding: 0px 0px 0px 50px;">'.
				'Title: '.$row['page_title'].'<br/>'.
  				'[<a href="'.$cat['path'].$row['page_key'].'">Goto</a>] '.
  				'[<a href="javascript:toggleMove(\'pageli'.$row['page_id'].'\')">Move</a>] '.
  				'[<a href="javascript:toggleNuke(\'pageli'.$row['page_id'].'\')">Delete</a>] '.
  				'[<a href="'.$cat['path'].$row['page_key'].'.pageconfig">Settings</a>]</div>'.
  			'<div class="move" style="display: none; padding: 0px 0px 0px 50px;">'.
				'<form action="/lp-admin.structure.move.'.$row['page_id'].'" method="POST" id="mvfrm'.$row['page_id'].'">'.
				'Move to: <select name="category">'.$cts.'</select> '.
				'<input type="text" name="location" size="35" value="'.$row['page_key'].'"> '.
  				'<input type="button" name="submit" value="Submit" onClick="postForm(\'mvfrm'.$row['page_id'].'\')"/></form>'.
  			'</div>'.
  			'<div class="nuke" style="display: none; padding: 0px 0px 0px 50px;">'.
				'<form action="/lp-admin.structure.nuke.'.$row['page_id'].'" method="POST" id="nkfrm'.$row['page_id'].'">'.
				'Are you sure you want to <b>DELETE</b> this page? Yes: <input name="sure" type="checkbox" value="1"/> '.
  				'<input type="button" name="submit" value="Submit" onClick="postForm(\'nkfrm'.$row['page_id'].'\')"/></form>'.
  			'</div>'.
  			'</li>';
  }
  $c .= "</ul></div>";
  return $c;
}

function makeCatListDiv($cat, $root = false)
{
  $c = '<span style="font-family: monospace; font-size: 0.7em; font-weight: bold;">';
  if ($cat['children'])
  	$c .= '<a style="text-decoration: none; border: 1px solid #999; padding: 0px 3px 0px 3px;" id="a'.$cat['cat_id'].'" href="javascript:toggleVis('.$cat['cat_id'].');">-</a> </span>';
  else
  	$c .= '<span style="padding: 0px 3px 0px 3px;">&gt;</span> </span>';
  $c .= '<a href="javascript:showCat('.$cat['cat_id'].');">'.$cat['cat_title'].'</a><br/>';
  if ($cat['children'])
  {
	$c .= '<div id="cat'.$cat['cat_id'].'" style="padding: 3px 3px 0px 15px; border-left: 1px solid #999;">';
  	foreach ($cat['children'] as $k => $v)
  		$c .= makeCatListDiv($v);
  	$c .= "</div>";
  }
  return $c;
}

if (isset($vfile[2]))
{
	switch ($vfile[2])
	{
		case "newFolder":
			if (!isset($vfile[3]))
				die("alert('Error: Missing parent id');");
			$key = trim($_POST['folder_name']);
			if ($key == "")
				die("alert('Please supply a folder name');");
			$key = mysql_real_escape_string($key);

			if (!preg_match("/^[a-zA-Z0-9_\!()\^]+$/", $key))
		    	die("alert(\"Please use only letters (a to z), numbers (0-9), '_', '!', '(', ')' and '^' in key names.<br/>".
		    		"If you feel that this range is insufficent, please file a bug report.\")");
			
			$results = mysql_do_query("SELECT * FROM `cms_pages`
				WHERE `page_key` = '".mysql_real_escape_string($key)."'
				  AND `page_category` = '".mysql_real_escape_string($vfile[3])."'");
			if (mysql_num_rows($results) != 0)
				die("alert('Page exists with that name');");
				
			$results = mysql_do_query("SELECT * FROM `cms_categories`
				WHERE `cat_key` = '".mysql_real_escape_string($key)."'
				  AND `cat_parent` = '".mysql_real_escape_string($vfile[3])."'");
			if (mysql_num_rows($results) != 0)
				die("alert('Sub Folder exists');");

			mysql_do_query("INSERT INTO `cms_categories`
                              SET `cat_parent` = '".mysql_real_escape_string($vfile[3])."',
                                  `cat_key` = '$key',
                                  `cat_title` = '$key'");
			die('reloadCats();reloadCat('.$vfile[3].');');
		
		case "newPage":
			if (!isset($vfile[3]))
				die("alert('Error: Missing parent id');");
			$key = trim($_POST['page_name']);
			if ($key == "")
				die("alert('Please supply a page name');");
			$key = mysql_real_escape_string($key);

			if (!preg_match("/^[a-zA-Z0-9_\!()\^]+$/", $key))
		    	die("alert(\"Please use only letters (a to z), numbers (0-9), '_', '!', '(', ')' and '^' in key names.<br/>".
		    		"If you feel that this range is insufficent, please file a bug report.\")");
			
			$results = mysql_do_query("SELECT * FROM `cms_pages`
				WHERE `page_key` = '".mysql_real_escape_string($key)."'
				  AND `page_category` = '".mysql_real_escape_string($vfile[3])."'");
			if (mysql_num_rows($results) != 0)
				die("alert('Page exists');");
			$results = mysql_do_query("SELECT * FROM `cms_categories`
				WHERE `cat_key` = '".mysql_real_escape_string($key)."'
				  AND `cat_parent` = '".mysql_real_escape_string($vfile[3])."'");
			if (mysql_num_rows($results) != 0)
				die("alert('Folder exists with that name');");
			mysql_do_query("INSERT INTO `cms_pages`
                          SET `page_key` = '".mysql_real_escape_string($key)."',
                              `page_category` = '".mysql_real_escape_string($vfile[3])."',
                              `page_title` = 'Under Construction'");

			die('reloadCat('.$vfile[3].');');
			
		case "move":
			if (!isset($tree['ids'][$_POST['category']]))
				die('alert("Unknown category");');

			$page_id = mysql_real_escape_string($vfile[3]);
			
			$key = mysql_real_escape_string(preg_replace("/\s+/","_",$_POST['location']));
				
			$cat = mysql_real_escape_string($_POST['category']);
			  
		  if (!preg_match("/^[a-zA-Z0-9_\!()\^]+$/", $key))
		    die("alert(\"Please use only letters (a to z), numbers (0-9), '_', '!', '(', ')' and '^' in key names.<br/>".
		    	"If you feel that this range is insufficent, please file a bug report.\")");

   			$results = mysql_do_query("SELECT * FROM `cms_pages`
				WHERE `page_key` = '".$key."'
				  AND `page_category` = '".$cat."'");
			if (mysql_num_rows($results) != 0)
				die("alert('Page exists');");
			$results = mysql_do_query("SELECT * FROM `cms_categories`
				WHERE `cat_key` = '".$key."'
				  AND `cat_parent` = '".$cat."'");
			if (mysql_num_rows($results) != 0)
				die("alert('Folder exists with that name');");
		    
			$results = mysql_do_query("SELECT * FROM `cms_pages` WHERE `page_id` = '".$page_id."'");
			$target = mysql_fetch_assoc($results);

			mysql_do_query("UPDATE `cms_pages` SET `page_key`='$key', `page_category`='$cat'
		                       WHERE `page_id`='".$page_id."'");
		    
			die('reloadCat('.$target['page_category'].');');
			
		case "nuke":
			if (!isset($_POST['sure']) || $_POST['sure'] != 1)
				die();
			
			$page_id = mysql_real_escape_string($vfile[3]);

			$results = mysql_do_query("SELECT * FROM `cms_pages` WHERE `page_id` = '".$page_id."'");
			if (mysql_num_rows($results) != 1)
				die();
				
			$target = mysql_fetch_assoc($results);
			
			mysql_do_query("DELETE FROM `cms_sections` WHERE `page_id`='$page_id'");
			mysql_do_query("DELETE FROM `cms_pages` WHERE `page_id`='$page_id'");
		
			die('reloadCat('.$target['page_category'].');');

		case "nukeCat":
			if (!isset($_POST['sure']) || $_POST['sure'] != 1)
				die();
			
			$cat_id = mysql_real_escape_string($vfile[3]);

			$results = mysql_do_query("SELECT * FROM `cms_categories` WHERE `cat_id` = '".$cat_id."'");
			if (mysql_num_rows($results) != 1)
				die();

			$target = mysql_fetch_assoc($results);
			
			if ($target['cat_id'] == 1)
				die("alert('You can\'t delete the root folder.  That would be silly.');");
				
			$results = mysql_do_query("SELECT * FROM `cms_pages` WHERE `page_category` = '".$cat_id."'");
			$pages = mysql_num_rows($results);
			if ($pages != 0)
				die("alert('Folder is not empty.  ".$pages." pages remaining.');");

			$results = mysql_do_query("SELECT * FROM `cms_categories` WHERE `cat_parent` = '".$cat_id."'");
			$pages = mysql_num_rows($results);
			if ($pages != 0)
				die("alert('Folder is not empty.  ".$pages." direct subfolders remaining.');");

			$results = mysql_do_query("SELECT * FROM `cms_menu` WHERE `item_category` = '".$cat_id."'");
			$pages = mysql_num_rows($results);
			if ($pages != 0)
				die("alert('Folder is not empty.  ".$pages." menu items remaining.');");
				
			mysql_do_query("DELETE FROM `cms_categories` WHERE `cat_id`='$cat_id'");
		
			die('reloadCats(); showCat('.$target['cat_parent'].');');
		
		case "moveCat":
			$cat_id = mysql_real_escape_string($vfile[3]);

			$results = mysql_do_query("SELECT * FROM `cms_categories` WHERE `cat_id` = '".$cat_id."'");
			if (mysql_num_rows($results) != 1)
				die();

			$target = mysql_fetch_assoc($results);
			
			if ($target['cat_id'] == 1)
				die("alert('You can\'t move or rename the root folder.  That would be silly.');");
				
			$category = mysql_real_escape_string($_POST['category']);
			$location = mysql_real_escape_string($_POST['location']);
			if (!preg_match("/^[a-zA-Z0-9_\!()\^]+$/", $location))
		    	die("alert(\"Please use only letters (a to z), numbers (0-9), '_', '!', '(', ')' and '^' in key names.<br/>".
		    		"If you feel that this range is insufficent, please file a bug report.\")");
			
			
			$results = mysql_do_query("SELECT * FROM `cms_pages` WHERE `page_key` = '".$location."' AND `page_category` = '".$category."'");
			$pages = mysql_num_rows($results);
			if ($pages != 0)
				die("alert('A page already exists at that location.');");

			$results = mysql_do_query("SELECT * FROM `cms_categories` WHERE `cat_key` = '".$location."' AND `cat_parent` = '".$category."'");
			$pages = mysql_num_rows($results);
			if ($pages != 0)
				die("alert('A folder already exists at that location.');");
				
			mysql_do_query("UPDATE `cms_categories` SET `cat_key` = '".$location."', `cat_parent` = '".$category."' WHERE `cat_id`='$cat_id'");
		
			die('reloadCats(); showCat('.$cat_id.');');
		
			$f = str_replace("\n"," ",print_r($_POST,true));
			die('alert(\''.addslashes($f).'\');');	
			
		case "catList":
			die(makeCatListDiv($tree['tree']));
			
		default:
			die(makePagesDiv($tree['ids'][$vfile[2]]));
			
	}
}

$content .= section("Site Structure", '<div style="float: left; width: 190px; border: 1px solid #999; padding: 5px;">'.
		'[<a href="javascript:showAllCats()">Expand all</a>] '.
  		'[<a href="javascript:hideAllCats()">Collapse all</a>]<br/><br/><div id="catsDiv">'.makeCatListDiv($tree['tree'], true).'</div></div>'.
	'<div id="pagesDiv" style="margin: 0px 0px 0px 210px; border: 1px solid #999; padding: 5px 20px 20px 20px;">'.makePagesDiv($tree['tree']).'</div>'.
	'<div style="clear: both">&nbsp;</div>');

//$content .= '<script type="text/javascript">'.$js.'</script>';

//$content .= "<br/><br/><pre>".print_r($tree['tree'],true)."</pre>";
?>