function viewPage (name)
{
  $("#td_content").html("<i>Loading...</i>");

  $.ajax({
    type: "GET",
    url: '/raw'+name,
    cache: false,
    dataType: "html",
    success: function (data)
    {
	  $("#td_content").html(data)
    }
  });
}

var dataString = '';
function postForm (name)
{
	dataString = 'submit=Submit';
	$("#"+name+ " input:text").each(function (i) { dataString = dataString + "&" + this.name + "=" + escape(this.value); } );
	$("#"+name+ " select").each(function (i) {
		dataString += "&" + this.name + "=" + $("#"+name+ " select[name="+ this.name+"] option:selected").attr("value");
	});
	$("#"+name+ " input:checkbox:checked").each(function (i) { dataString = dataString + "&" + this.name + "=" + escape(this.value); } );
	
	  $.ajax({
	    type: "POST",
	    url: "/raw"+$("#"+name).attr("action"),
	    data: dataString,
	    dataType: "html",
			success: function (data)
			{
				$("#td_content").html(data)
			}
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

function toggleDetails(id) {	$('#'+id+" div.controls").slideToggle(200); $('#'+id+" div.move").slideUp(200); }
function toggleMove(id) { $('#'+id+" div.move").slideToggle(200); }
function toggleNuke(id) { $('#'+id+" div.nuke").slideToggle(200); }
function showAllDetails() {	$('#pagesDiv li div.controls').slideDown(200); }
function hideAllDetails() {	$('#pagesDiv li div').slideUp(200); }

function showNewFolder() {	$('#newFolder').slideToggle(200); }
function showNewPage() {	$('#newPage').slideToggle(200); }
function showTitleCat() {	$('#titleCat').slideToggle(200); }
function showMoveCat() {	$('#moveCat').slideToggle(200); }
function showNukeCat() {	$('#nukeCat').slideToggle(200); }

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

function reloadCat (id)
{
	  $.ajax({
		    type: "GET",
		    url: "/lp-admin.structure."+id,
		    cache: false,
		    dataType: "html",
		    success: function (data)
		    {
	  			var newCats = $(data);
	  			$("div#pagesDiv li div:visible").each(function ()
	  				{
	  					newCats.find('li#'+$(this).parent().attr('id')+' div.'+$(this).attr('class')).show();
	  				});
	  			$("div#pagesDiv li div form :text").each(function () {
  					newCats
  						.find(
  								'li#' + $(this).parent().parent().parent().attr('id')
  								+' div.' + $(this).parent().parent().attr('class')
  								+' :text[name='+$(this).attr('name')+']'
  						)
  						.attr(
  								'value',
  								$(this).attr('value')
  						);
		  			});
	  			
	  			newCats.find("option").removeAttr('selected');
	  			$("div#pagesDiv li div form select option:selected").each(function () {
  					newCats
  						.find(
  								'li#' + $(this).parent().parent().parent().parent().attr('id')
  								+' div.' + $(this).parent().parent().parent().attr('class')
  								+' select[name='+$(this).parent().attr('name')+']'
  								+' option[value='+$(this).attr('value')+']'
  						)
  						.attr(
  								'selected',
  								$(this).attr('selected')
  						);
		  			});
	  			

	  			
	  			
	  			$("div#pagesDiv").html(newCats);
		    }
	  });

}


function sidebarsSortable ()
{
	$("ul.miList").sortable({connectWith: ['.miList']});
}

var nextMi = 1;

function newSep()
{
	$("ul#trash").append('<li id="mi_'+nextMi+'" class="mi" style="border: 1px solid #999; margin: 4px; padding: 2px; width: 133px;"><i>---Separator---</i></li>');
	nextMi += 1;
	$("ul#trash").sortable("refresh");
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
