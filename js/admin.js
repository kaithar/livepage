function viewPage (name)
{
  $("#adminbody").html("<i>Loading...</i>");

  $.ajax({
    type: "GET",
    url: "/lp-admin."+name,
    cache: false,
    dataType: "html",
    success: function (data)
    {
	  $("#adminbody").html(data)
    }
  });
}

var dataString = '';
function postForm (name)
{
	dataString = 'submit=Submit';
	$("#"+name+ " :text").each(function (i) { dataString = dataString + "&" + this.name + "=" + escape(this.value); } );
	  $.ajax({
	    type: "POST",
	    url: $("#"+name).attr("action"),
	    data: dataString,
	    dataType: "script"
	  });
	  return false;

}

function toggleVis(num)
{
   var a = document.getElementById("a"+num);
   if (a != null)
   {
     if (a.innerHTML == "+")
     {
       $('#cat'+num).slideDown(200);
       a.innerHTML = "-";
     }
     else
     {
       $('#cat'+num).slideUp(200);    	 
       a.innerHTML = "+";
     }
   }
}

// Helpers...
function setHTML(id,str)
{
	var e = document.getElementById(id);
	if (e != null)
	{
		e.innerHTML = str;
	}
}

function showCat(id)
{
  $.ajax({
    type: "GET",
    url: "/lp-admin.structure."+id,
    cache: false,
    dataType: "html",
    success: function (data)
    {
	  $("#pagesDiv").html(data)
    }
  });
}

function toggleDetails(id) {	$('#'+id+" div").slideToggle(100); }
function showAllDetails() {	$('#pagesDiv li div').slideDown(200); }
function hideAllDetails() {	$('#pagesDiv li div').slideUp(200); }
function showNewFolder() {	$('#newFolder').slideToggle(100); }
function showNewPage() {	$('#newPage').slideToggle(100); }

function showAllCats() {	$('#catsDiv div[id*=cat]').slideDown(200); $('#catsDiv a[id*=a]').html("-"); }
function hideAllCats() {	$('#catsDiv div[id*=cat]').slideUp(200); $('#catsDiv a[id*=a]').html("+"); }


function reloadCats ()
{
  $.ajax({
	    type: "GET",
	    url: "/lp-admin.structure.catList",
	    cache: false,
	    dataType: "html",
	    success: function (data)
	    {
	  		var newCats = $(data);
	  		$("div#catsDiv div:hidden").each(function ()
	  				{
	  					newCats.find('div#'+this.id).hide();
	  					newCats.find('a#'+this.id.replace(/cat/,"a")).html("+");
	  				} );
	  		$("div#catsDiv").html(newCats);
	    }
  });
}

//document.onmousemove = mouseMove;
//document.onmouseup   = mouseUp;
//
//var dragObject  = null;
//var mouseOffset = null;
//
//function mouseCoords(ev){
//	if(ev.pageX || ev.pageY){
//		return {x:ev.pageX, y:ev.pageY};
//	}
//	return {
//		x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
//		y:ev.clientY + document.body.scrollTop  - document.body.clientTop
//	};
//}
//
//function getPosition(e){
//	var left = 0;
//	var top  = 0;
//
//	while (e.offsetParent){
//		left += e.offsetLeft;
//		top  += e.offsetTop;
//		e     = e.offsetParent;
//	}
//
//	left += e.offsetLeft;
//	top  += e.offsetTop;
//
//	return {x:left, y:top};
//}
//
//
//function getMouseOffset(target, ev){
//	ev = ev || window.event;
//
//	var docPos    = getPosition(target);
//	var mousePos  = mouseCoords(ev);
//	return {x:mousePos.x - docPos.x, y:mousePos.y - docPos.y};
//}
//
//function mouseMove(ev){
//	ev           = ev || window.event;
//	var mousePos = mouseCoords(ev);
//
//	if(dragObject){
//		dragObject.style.position = 'absolute';
//		dragObject.style.top      = mousePos.y - mouseOffset.y + "px";
//		dragObject.style.left     = mousePos.x - mouseOffset.x + "px";
//
//		return false;
//	}
//}
//
//function mouseUp(){
//	dragObject = null;
//}
//
//function makeDraggable(id){
//	var item = document.getElementById(id);
//	if(!item) return;
//	item.onmousedown = function(ev){
//		dragObject  = this;
//		mouseOffset = getMouseOffset(this, ev);
//		return false;
//	}
//}
