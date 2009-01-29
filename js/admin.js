function structureHandler() {
 if(this.readyState == 4 && this.status == 200) {
  // so far so good
	var e = document.getElementById("adminbody");

	if (e != null)
		e.innerHTML = this.responseText;

 } else if (this.readyState == 4 && this.status != 200) {
  // fetched the wrong page or network error...
 }
}

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

// Helpers...
function setHTML(id,str)
{
	var e = document.getElementById(id);
	if (e != null)
	{
		e.innerHTML = str;
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
		dragObject.style.position = 'absolute';
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
  client.open("GET", "/lp-admin.structure."+id);
  client.setRequestHeader("Connection", "close");
  client.send("");
}
