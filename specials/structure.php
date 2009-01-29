<?php 

require_once("includes/cat_tree.php");

function makePagesDiv($cat)
{
  $result = mysql_do_query("SELECT *
		                      FROM `cms_pages` 
				             WHERE `page_category` = '".mysql_real_escape_string($cat['cat_id'])."'");
  $c = "[<a href=\"javascript:showAllDetails()\">Expand all</a>] ".
  		"[<a href=\"javascript:hideAllDetails()\">Collapse all</a>] ".
		"[<a href=\"javascript:showNewFolder()\">New Subfolder</a>] ".
		"[<a href=\"javascript:showNewPage()\">New Page</a>] ".
  		"<br/>".
  	'<div id="newFolder" style="display: none; padding: 10px;">'.
  		'<form action="/lp-admin.structure.newFolder.'.$cat['cat_id'].'" method="POST" id="newFolderForm">'.
			"Create folder ".rtrim($cat['path'],'/')."/ <input type=\"text\" name=\"folder_name\" size=\"35\" value=\"\"/> ".
			"<input type=\"button\" name=\"submit\" value=\"Submit\" onClick=\"postForm('newFolderForm')\"/></form>".
	'</div>'.
  	'<div id="newPage" style="display: none; padding: 10px;">'.
  		'<form action="/lp-admin.structure.newPage.'.$cat['cat_id'].'" method="POST" id="newPageForm">'.
			"Create page ".rtrim($cat['path'],'/')."/ <input type=\"text\" name=\"page_name\" size=\"35\" value=\"\"/> ".
			"<input type=\"button\" name=\"submit\" value=\"Submit\" onClick=\"postForm('newPageForm')\"/></form>".
	'</div>'.
	"<br/>".
  	"Trail: ".$cat['flat_path']."<br/>".
	"Path: ".$cat['path']."<br/><ul>";
  while ($row = mysql_fetch_assoc($result))
  {
  	$c .= '<li id="pageli'.$row['page_id'].'"><a class="pagekey" href="javascript:toggleDetails(\'pageli'.$row['page_id'].'\')">'.$row['page_key'].'</a>'.
  			'<div style="display: none; padding: 0px 0px 0px 50px;">'.
  				'[<a href="'.rtrim($cat['path'],"/").'/'.$row['page_key'].'.move">Move</a>] '.
  				'[<a href="'.rtrim($cat['path'],"/").'/'.$row['page_key'].'.pageconfig">Settings</a>] '.
  				'[<a href="'.rtrim($cat['path'],"/").'/'.$row['page_key'].'">Goto</a>] &nbsp; -- &nbsp; Title: '.
  			$row['page_title'].'</div>'.
  			'</li>';
  }
  $c .= "</ul>";
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

$tree = build_cat_tree();

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
			die('reloadCats();$("#newFolder").slideUp(100);');
		
		case "newPage":
			if (!isset($vfile[3]))
				die("alert('Error: Missing parent id');");
			$key = trim($_POST['page_name']);
			if ($key == "")
				die("alert('Please supply a page name');");
			$key = mysql_real_escape_string($key);
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

			die('showCat('.$vfile[3].');');
			
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