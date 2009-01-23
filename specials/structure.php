<?php 

require_once("includes/cat_tree.php");

$admining = 1;

$tree = build_cat_tree();

$js = "";

function makeCatDiv($cat, $root = false)
{
  global $js;
  $c = '<div id="cat'.$cat['cat_id'].'" style="margin: 10px; padding: 5px; border: 1px solid #999; '.($root?'':'display: none;').'"><ul>';
  $result = mysql_do_query("SELECT *
		                      FROM `cms_pages` 
				             WHERE `page_category` = '".mysql_real_escape_string($cat['cat_id'])."'");
  while ($row = mysql_fetch_assoc($result))
  {
  	$js .= "makeDraggable('pageli".$row['page_id']."');";
  	$c .= '<li id="pageli'.$row['page_id'].'"><div style="float: left; width: 150px">'.
  			'[<a href="'.$cat['path'].'/'.$row['page_key'].'.move">Move</a>] '.
  			'[<a href="'.$cat['path'].'/'.$row['page_key'].'.pageconfig">Settings</a>] </div>'.
  			'<div style="float: left; width: 200px">
  			-- &nbsp; <a href="'.$cat['path'].'/'.$row['page_key'].'">'.$row['page_key']."</a></div> -- &nbsp; ".
  			$row['page_title'].''.
  			'</li>';
  }
  foreach ($cat['children'] as $k => $v)
  {
   	$js .= "makeDraggable('catli".$v['cat_id']."');";
  	$c .= '<li id="catli'.$v['cat_id'].'">
  		<div style="float: left; width: 150px">[<a id="a'.$v['cat_id'].'" href="javascript:toggleVis('.$v['cat_id'].');">Expand</a>]</div>
  		<div style="float: left; width: 200px"> -- &nbsp; '.$v['cat_key']."</div> -- &nbsp; ".$v['cat_title'].
  		'<br/>'.makeCatDiv($v).'</li>';
  }
  $c .= "</ul></div>";
  return $c;
}

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
       a.innerHTML = "Hide";
     }
     else
     {
       e.style.display = "none";
       a.innerHTML = "Expand";
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

</script>';

$content .= "Home<br/>".makeCatDiv($tree['tree'], true);

//$content .= '<script type="text/javascript">'.$js.'</script>';

//$content .= "<br/><br/><pre>".print_r($tree['tree'],true)."</pre>";

?>