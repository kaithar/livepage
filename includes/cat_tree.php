<?php

require_once('includes/db.php');

function recursive_tree_path($flat, $node, $flat_path, $path)
{
	$flat_path .= $node['cat_title'];
	$path .= $node['cat_key'];
	$node['flat_path'] = $flat_path;
	$node['path'] = $path;
	$flat[] = $node;
	$flat_path .= " &raquo; ";
	$path .= "/";
	foreach ($node['children'] as $child)
		recursive_tree_path($flat, $child, $flat_path, $path);
}

function build_cat_tree()
{
  $tree = Array();
	$tree['flat'] = Array();
	$tree['ids'] = Array();
	
	$ids = mysql_do_query("SELECT * FROM `categories`");
	while ($cat = mysql_fetch_assoc($ids))
	{
		$cat['children'] = Array();
		$tree['ids'][$cat['cat_id']] = $cat;
	}
	
	foreach ($tree['ids'] as $cat)
	{
		if ($cat['parent'] == 0)
			$tree['tree'] = $cat;
		else
			$tree['ids'][$cat['parent']]['children'] = $cat;
	}
	
	recursive_tree_path($tree['flat'],$tree['tree'],"","");
	return $tree;
}

?>