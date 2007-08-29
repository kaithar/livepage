<?php

/*****************************************************************************************************
 * Set 'em up, boys.
 */

require_once ("includes/env_init.php");
require_once ("includes/templating.php");


// $_SERVER['REQUEST_URI'] is like "/foo/bar"
$requested = $_SERVER['REQUEST_URI'];

// For the purpose of this, see templates/header.php and specials/config.php
$admining = 0;

/*****************************************************************************************************
 * Bypass the normal code f the request is for /files/
 * Such requests are simple rewrites.
 */
if (preg_match("|^/files/|", $requested))
{
	require_once("file_rewriter.php");
}

/*****************************************************************************************************
 * This code is designed to match "/foo/" and "/foo/.handler".
 * It inserts index, converting the url to "/foo/index" and "/foo/index.handler" respectively.
 */

if (preg_match("|^((?:.*/)+)(\.[^/]*)?$|U", $requested, $matches))
{
	if (count($matches) == 2)
		$requested = $matches[1]."index";
	else
		$requested = $matches[1]."index".$matches[2];
}

/*****************************************************************************************************
 * Process the request, building an array of it's components.
 */

$vpath = explode("/",$requested);
$path = Array();
$parent = 0;
$parent_path = "";

$visible_categories = Array();

while (count($vpath) > 1)
{
  $vfile = explode(".", array_shift(&$vpath)); // Explode the next path component
  
  // Try to get said path component (note, the root category has a blank key and 0 parent)
  $mycat = mysql_do_query("SELECT *
			                       FROM `cms_categories` 
                            WHERE `cat_key`='".mysql_real_escape_string($vfile[0])."' 
                              AND `cat_parent` = '".mysql_real_escape_string($parent)."'
                            LIMIT 1");

  if (mysql_num_rows($mycat) != 1)
  {
		// Category doesn't exist ... fake it.
		$cat = Array();
		$cat['found'] = false;
		$cat['cat_key'] = $vfile[0];
		$cat['cat_title'] = $vfile[0];
		$cat['cat_id'] = -1;
		$cat['parent'] = $parent;
  }
	else
	{
  	// We have a cat, so it needs to be properly treated... with cat nip :P
  	$cat = mysql_fetch_assoc($mycat);
		$cat['found'] = true;
    $visible_categories[$cat['cat_id']] = true;
	}

	$cat['parent_path'] = $parent_path ? $parent_path : "/";
  $path[] = $cat;
  $parent_path .= $cat['cat_key']."/";
	$cat['path'] = $parent_path;

  // And on to the next component we go.
  $parent = $cat['cat_id']; 
}

/*****************************************************************************************************
 * Process the page request
 */

// If we're rendering a normal page...
$vfile = explode(".",$vpath[0]);

$mypage = mysql_do_query("SELECT *
		                        FROM `cms_pages` 
				                   WHERE `page_key`='".mysql_real_escape_string($vfile[0])."' 
				                     AND `page_category` = '".mysql_real_escape_string($parent)."'
				                   LIMIT 1");

if (mysql_num_rows($mypage) != 1)
{
	// Fake it baby!
  $page = Array();
	$page['found'] = false;
	$page['page_key'] = $vfile[0];
	$page['page_title'] = $vfile[0];
	$page['page_id'] = -1;
	$page['parent'] = $parent;
	$page['page_include'] = "pages/404.php";
}
else
{
  $page = mysql_fetch_assoc($mypage);
	$page['found'] = true;
}

$page['parent_path'] = $parent_path ? $parent_path : "/";
$page['path'] = $parent_path.$page['page_key'];
$page['params'] = array_slice($vfile,1);

/*****************************************************************************************************
 * Process specials
 */

$content = "";

/* showpage is there to allow the specials to override page display.
 * Set to 1 it renders, set it to 0 to not show it.
 */
$showpage = 1;

if ($page['params'])
{
  // If you can't edit the page, you can't use most of the handlers.
  if ($user['editcontent'] == 1)
  {
    // Unless the special otherwise instructs it, the page won't display
    $showpage = 0;
    switch ($page['params'][0])
    {
      case "config":
        include("specials/config.php");
        break;
      case "sidebar":
        include("specials/sidebar.php");
        break;
      case "create":
        include("specials/create.php");
        break;
      case "createsection":
        include("specials/createsection.php");
        break;
      case "swap":
        include("specials/swap.php");
        break;
      case "edit":
        include("specials/edit.php");
        break;
      case "del":
        include("specials/del.php");
        break;
      case "delpage":
        include("specials/delpage.php");
        break;
      case "edittitle":
        include("specials/edittitle.php");
        break;
      case "move":
        include("specials/move.php");
        break;
      default:
        // Okie, so it isn't an admin header... but we're not giving up yet...
        $showpage = 1;
    }
  }
  /* One last possibility before we assume it's .html or something...
   * Note: These don't want the templating used.
   */
  switch ($page['params'][0])
  {
    case "css":
      include("specials/style.php");
      die();
  }
}

/*****************************************************************************************************
 * Call out for special includes
 * TODO: This represents a potential security hole and should be removed.
 */

if ($showpage && $page["page_include"])
{
	$showpage = 0;
  include($page["page_include"]);
}

/*****************************************************************************************************
 * Render the page if required.
 */

if ($showpage)
{
  $mysections = mysql_do_query("SELECT *
                                  FROM `cms_sections`
                                 WHERE `page_id`='".mysql_real_escape_string($page['page_id'])."'
                              ORDER BY `order` ASC");
	
	if (mysql_num_rows($mysections) == 0)
	{
		$content .= "This page appears to be empty...";
	}
	else
	{
    // $links = "&nbsp;&nbsp;&nbsp;Content list:<br><br>";
		$links = "";
		$body = "";
		$last = mysql_num_rows($mysections)-1;
		while ($section = mysql_fetch_assoc($mysections)) 
		{
      // $links .= '<a class="contentmenuitem" href="#s'.$section['order'].'">'.($section['order']+1).'. '.$section['section_title'].'</a>';
			$body .= section(
				'<a name="s'.$section['order'].'"/>'.$section['section_title'].
				(
					($user['editcontent'] == 1)
					?'<div style="float:right;position:relative;top:-1.2em;">(Move '.
						(
							($section['order']>0)
							?'<a href="'.$page['path'].'.swap.'.($section['order']-1).'.'.$section['order'].'">Up</a>'
							:'Up'
						).
						' or '.
						(
							($section['order'] != $last)
							?'<a href="'.$page['path'].'.swap.'.$section['order'].'.'.($section['order']+1).'">Down</a>'
							:'Down'
						).
						', <a href="'.$page['path'].'.edit.'.$section['section_id'].'">Edit</a>'.
						', <a href="'.$page['path'].'.del.'.$section['section_id'].'">Del</a>)</div>'
					:''
				).
				'&nbsp;',
				nl2br($section['section_text'])
			);
		}
		$content .= $links . $body;
	}
}

/*****************************************************************************************************
 * We should have some content, render it then close the database.
 */

require_once(select_header());
echo $content;
require_once(select_footer());
dbclose();

?>
