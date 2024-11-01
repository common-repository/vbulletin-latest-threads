<?php
if (!function_exists('add_action'))
{
	require_once("../../../../wp-config.php");
}
?>
function vbulletin_lt_addLoadEvent(func) {
	  var oldonload = window.onload;
	  if (typeof window.onload != "function") {
	    window.onload = func;
	  } else {
	    window.onload = function() {
	      if (oldonload) {
		oldonload();
	      }
	      func();
	    }
	  }
	}


function vbulletin_lt_CreateXmlHttpReq(handler) {

	var xmlhttp = null;
	try {
	    xmlhttp = new XMLHttpRequest();
	} catch(e) {
	    try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	    } catch(e) {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	    }
	}
	vbulletin_lt_loading();
	xmlhttp.onreadystatechange = handler;
	return xmlhttp;
}

function vbulletin_lt_Handler()
	{
	if (myRequest.readyState == 4 && myRequest.status == 200)
	{
		vbulletin_lt_populate(myRequest.responseText);

	}
	else if(myRequest.readyState == 4 && myRequest.status == 404){
		vbulletin_lt_populate("Page not found, check configuration file");
	}
	else if(myRequest.readyState == 4){
		vbulletin_lt_populate("Server Error");
	}

}

function vbulletin_lt_loading(){
	document.getElementById("vbulletin_lt_content").innerHTML="<div id='vbulletin_lt_loading' style='text-align: center;'><br/>Loading threads<br/><br/><img src='<?php bloginfo('wpurl') ?>/wp-content/plugins/vbulletin-latest-threads/images/loading.gif\' alt='loading'></div>";
}

function vbulletin_lt_populate(string){
	document.getElementById("vbulletin_lt_content").innerHTML=string;
}


function vbulletin_lt_load_content() {
	var url =  "<?php bloginfo('wpurl') ?>/wp-content/plugins/vbulletin-latest-threads/async/get_content.php";
	myRequest = vbulletin_lt_CreateXmlHttpReq(vbulletin_lt_Handler);
	myRequest.open("GET",url);
	myRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	myRequest.send("&t=c");

}

vbulletin_lt_addLoadEvent(vbulletin_lt_load_content);
