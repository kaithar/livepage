<?
require_once ("includes/env_init.php");
require_once("templates/section.php");

$vpath = explode("/","/index".$_SERVER['REQUEST_URI']);

$parent = 0;
$special_include = 0;

$path = Array();
$parent_path = "";

while (count($vpath) > 1)
{
  // First pass removes a blank, each sucessive pass removes the previous node...
  array_shift(&$vpath);

  // Explode the current first one...
  $vfile_ = explode(".",$vpath[0]);

  if (($vfile_[0] == "") || (($vfile_[0] == "index") && ($parent != 0))) 
  {
    $vfile_[0] = $vfile[0];
    $vfile = $vfile_;
    break;
  }

  $vfile = $vfile_;
  $request = $vfile[0];

  $mypage = mysql_do_query("SELECT * FROM `cms_pages` 
				  WHERE `page_key`='".mysql_real_escape_string($request)."' 
				    AND `page_parent` = '".mysql_real_escape_string($parent)."'
				  LIMIT 1");

  if (mysql_num_rows($mypage) != 1)
  {
    if ($vfile[1] != "create")
      $special_include = "pages/404.php";
    break;
  }
 
  $page = mysql_fetch_assoc($mypage);

  $page['parent_url'] = $parent_path ? $parent_path : "/";
  $path[] = $page;

  $parent_path .= "/".$page['page_key'];
  if ($parent_path == "/index") $parent_path = "";

  if ($page["page_include"])
  {
    $special_include = $page["page_include"];
    break;
  }
  $parent = $page['page_id'];
}

//echo $parent_path. " | ";
$parent_path = preg_replace("|/[^/]*$|","",$parent_path);
//echo $parent_path;

$content = "";

if ($special_include)
{
  include($special_include);
}
else
{
  $showpage = 1;
  if (($vfile[1]) && ($user['editcontent'] == 1))
  {
    $showpage = 0;
    switch ($vfile[1])
    {
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
        $showpage = 1;
    }
  }
  if ($showpage)
  {
    $mysections = mysql_do_query("SELECT * FROM `cms_sections`
					WHERE `page_id`='".mysql_real_escape_string($page['page_id'])."' ORDER BY `order` ASC");
    if (mysql_num_rows($mysections) == 0)
    {
      $content .= "This page appears to be empty...";
    }
    else
    {
//    $links = "&nbsp;&nbsp;&nbsp;Content list:<br><br>";
      $links = "";
      $body = "";
      $last = mysql_num_rows($mysections)-1;
      while ($section = mysql_fetch_assoc($mysections)) 
      {
//      $links .= '<a class="contentmenuitem" href="#s'.$section['order'].'">'.($section['order']+1).'. '.$section['section_title'].'</a>';
        $body .= section(
          '<a name="s'.$section['order'].'"/>'.$section['section_title'].
          (
            ($user['editcontent'] == 1)
            ?'<div style="float:right;position:relative;top:-1.2em;">(Move '.
              (
                ($section['order']>0)
                ?'<a href="'.$request.'.swap.'.($section['order']-1).'.'.$section['order'].'">Up</a>'
                :'Up'
              ).
              ' or '.
              (
                ($section['order'] != $last)
                ?'<a href="'.$request.'.swap.'.$section['order'].'.'.($section['order']+1).'">Down</a>'
                :'Down'
              ).
              ', <a href="'.$request.'.edit.'.$section['section_id'].'">Edit</a>'.
              ', <a href="'.$request.'.del.'.$section['section_id'].'">Del</a>)</div>'
            :''
          ).
          '&nbsp;',
          nl2br($section['section_text'])
        );
      }
      $content .= $links . $body;
    }
  }
}

include("templates/header.php");
echo $content;
include("templates/footer.php");
dbclose();
?>
