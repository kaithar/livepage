<?php 

require_once("includes/cat_tree.php");

$admining = 1;

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
  			'[<a href="'.$cat['path'].'/'.$row['page_key'].'.move">Move</a>] '.
  			'[<a href="'.$cat['path'].'/'.$row['page_key'].'.pageconfig">Settings</a>] </div>'.
  			'<div style="float: left; width: 200px">
  			-- &nbsp; <a href="'.$cat['path'].'/'.$row['page_key'].'">'.$row['page_key']."</a></div> -- &nbsp; ".
  			$row['page_title'].''.
  			'</li>';
  }
  $c .= "</ul>";
  return $c;
}

function makeCatListDiv($cat)
{
  $c = '<div id="cat'.$cat['cat_parent'].'" style="padding: 3px 3px 0px 15px; border-left: 1px solid #999;">';
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

$content .= '<script type="text/javascript">
function toggleVis(num)
{
   var e = document.getElementById("cat"+num);
   var a = document.getElementById("a"+num);
   if (e != null)
   {
     if (e.style.display == "none")
     {
       e.style.display = "block";
       a.innerHTML = "-";
     }
     else
     {
       e.style.display = "none";
       a.innerHTML = "+";
     }
   }
}


document.onmousemove = mouseMove;
document.onmouseup   = mouseUp;

var dragObject  = null;
var mouseOffset = null;

function mouseCoords(ev){
	if(ev.pageX || ev.pageY){
		return {x:ev.pageX, y:ev.pageY};
	}
	return {
		x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
		y:ev.clientY + document.body.scrollTop  - document.body.clientTop
	};
}

function getPosition(e){
	var left = 0;
	var top  = 0;

	while (e.offsetParent){
		left += e.offsetLeft;
		top  += e.offsetTop;
		e     = e.offsetParent;
	}

	left += e.offsetLeft;
	top  += e.offsetTop;

	return {x:left, y:top};
}


function getMouseOffset(target, ev){
	ev = ev || window.event;

	var docPos    = getPosition(target);
	var mousePos  = mouseCoords(ev);
	return {x:mousePos.x - docPos.x, y:mousePos.y - docPos.y};
}

function mouseMove(ev){
	ev           = ev || window.event;
	var mousePos = mouseCoords(ev);

	if(dragObject){
		dragObject.style.position = \'absolute\';
		dragObject.style.top      = mousePos.y - mouseOffset.y + "px";
		dragObject.style.left     = mousePos.x - mouseOffset.x + "px";

		return false;
	}
}

function mouseUp(){
	dragObject = null;
}

function makeDraggable(id){
	var item = document.getElementById(id);
	if(!item) return;
	item.onmousedown = function(ev){
		dragObject  = this;
		mouseOffset = getMouseOffset(this, ev);
		return false;
	}
}

function handler() {
 if(this.readyState == 4 && this.status == 200) {
  // so far so good
	var e = document.getElementById("pagesDiv");

	if (e != null)
		e.innerHTML = this.responseText;

 } else if (this.readyState == 4 && this.status != 200) {
  // fetched the wrong page or network error...
 }
}

function showCat(id)
{
  var client = new XMLHttpRequest();
  client.onreadystatechange = handler;
  client.open("GET", "'.$vfile[0].'.structure."+id);
  client.setRequestHeader("Connection", "close");
  client.send("");
}

</script>';

$content .= '<div style="float: left; width: 200px; border: 1px solid #999;">'.makeCatListDiv($tree['tree']).'</div>';
$content .= '<div id="pagesDiv" style="margin: 0px 0px 0px 210px; border: 1px solid #999; padding: 20px;">'.makePagesDiv($tree['tree']).'</div>';

//$content .= '<script type="text/javascript">'.$js.'</script>';

$content .= "<br/><br/><pre>".print_r($tree['tree'],true)."</pre>";

?>