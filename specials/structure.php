<?php 

require_once("includes/cat_tree.php");

$tree = build_cat_tree();

function makePagesDiv($cat)
{
  $result = mysql_do_query("SELECT *
		                      FROM `cms_pages` 
				             WHERE `page_category` = '".mysql_real_escape_string($cat['cat_id'])."'");
  $c = $cat['flat_path']."<br/>".$cat['path']."<br/><ul>";
  while ($row = mysql_fetch_assoc($result))
  {
  	$c .= '<li id="pageli'.$row['page_id'].'"><div style="float: left; width: 150px">'.
  			'[<a href="'.$cat['path'].$row['page_key'].'.move">Move</a>] '.
  			'[<a href="'.$cat['path'].$row['page_key'].'.pageconfig">Settings</a>] </div>'.
  			'<div style="float: left; width: 200px">
  			-- &nbsp; <a href="'.$cat['path'].'/'.$row['page_key'].'">'.$row['page_key']."</a></div> -- &nbsp; ".
  			$row['page_title'].''.
  			'</li>';
  }
  $c .= "</ul>";
  return $c;
}

function makeCatListDiv($cat, $root = false)
{
  $c = '<div id="cat'.$cat['cat_parent'].'" style="padding: 3px 3px 0px 15px; '.($root?'':'border-left: 1px solid #999;').'">';
  $c .= '<span style="font-family: monospace; font-size: 0.7em; font-weight: bold;">';
  if ($cat['children'])
  	$c .= '<a style="text-decoration: none; border: 1px solid #999; padding: 0px 3px 0px 3px;" id="a'.$cat['cat_id'].'" href="javascript:toggleVis('.$cat['cat_id'].');">-</a> </span>';
  else
  	$c .= '<span style="padding: 0px 3px 0px 3px;">&gt;</span> </span>';
  $c .= '<a href="javascript:showCat('.$cat['cat_id'].');">'.$cat['cat_title'].'</a>';
  if ($cat['children'])
  {
  	foreach ($cat['children'] as $k => $v)
  		$c .= makeCatListDiv($v);
  }
  $c .= "</div>";
  return $c;
}

if (isset($vfile[2]))
  die(makePagesDiv($tree['ids'][$vfile[2]]));


$content .= section("Site Structure", '<div style="float: left; width: 190px; border: 1px solid #999; padding: 5px;">'.makeCatListDiv($tree['tree'], true).'</div>'.
	'<div id="pagesDiv" style="margin: 0px 0px 0px 210px; border: 1px solid #999; padding: 20px;">'.makePagesDiv($tree['tree']).'</div>');

//$content .= '<script type="text/javascript">'.$js.'</script>';

//$content .= "<br/><br/><pre>".print_r($tree['tree'],true)."</pre>";
?>