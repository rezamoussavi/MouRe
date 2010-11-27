<script language="Javascript">
function sndmsg(bizname) {
    
	 //create xmlHttp setup...
	 var xmlHttpReq = false;
    // Mozilla/Safari
    if (window.XMLHttpRequest) {xmlHttpReq = new XMLHttpRequest();}
    // IE
    else if (window.ActiveXObject) {xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");}
	 
    xmlHttpReq.open('POST', "index.php", true);
    xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlHttpReq.onreadystatechange = function() {
        if (xmlHttpReq.readyState == 4 && xmlHttpReq.status==200) {
			document.getElementById("os_message_box").innerHTML = xmlHttpReq.responseText;
			var mails=document.getElementById("os_message_box").childNodes;
			for(var i=0;i<mails.length;i++)
				if(mails[i].tagName=="DIV")
					document.getElementById(mails[i].id).innerHTML=mails[i].innerHTML;
			//document.getElementById("os_message_box").innerHTML =" ";
		}
    }
    xmlHttpReq.send(getquerystring(bizname));
}

function getquerystring(bizname) {
    var form = document.forms[bizname];
    var size = form.elements.length;
    var qstr ="";
	for(var i=0;i<size;i=i+1)
		qstr = qstr+form.elements[i].name+"="+escape(form.elements[i].value)+"&";
    return qstr;
}
</script>
<div id="old_os_message_box" style="visibility:hidden"> </div>
<div id="os_message_box" style="visibility:hidden"> </div>
