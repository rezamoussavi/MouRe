
//js.begin.addvideo
	var okTitle=false;
	var okYLink=false;
	var okAOPV=false;
	var okNOV=false;
	function checkTitle(){
		var msg="<font color=green>OK</font>";
		okTitle=true;
		if(document.getElementById('theTitle').value.length<1){
			msg = "<font color=red>Invalid Name</font>";// ERROR
			okTitle=false;
		}
		document.getElementById('msgTitle').innerHTML = msg;
		checkAll();
	}
	function checkYLink(){
		var yl=document.getElementById('theYLink').value;
		okYLink=true;
		var msg="<font color=green>OK</font>";// OK
		if(yl.indexOf("www.youtube.com/watch?v=")==-1 && yl.indexOf("youtu.be/")==-1){
			msg="<font color=red>Invalid</font>";// ERROR
			okYLink=false;
		}
		document.getElementById("msgYLink").innerHTML = msg;
		checkAll();
	}
	function checkAOPV(minAOPV){
		okAOPV=true;
		var msg="<font color=green>OK</font>";// OK
		if(document.getElementById('theAOPV').value < minAOPV){
			msg="<font color=red>Invalid</font>";// ERROR
			okAOPV=false;
		}
		document.getElementById("msgAOPV").innerHTML = msg;
		checkAll();
	}
	function checkNOV(minNOV){
		okNOV=true;
		var msg="<font color=green>OK</font>";// OK
		if(document.getElementById('theNOV').value < minNOV){
			msg="<font color=red>Invalid</font>";// ERROR
			okNOV=false;
		}
		document.getElementById("msgNOV").innerHTML = msg;
		checkAll();
	}
	function checkAll(){
		var total=document.getElementById('theAOPV').value * document.getElementById('theNOV').value;
		var balance=document.getElementById('theBalance').innerHTML;
		document.getElementById("theTotal").value = total;
		var msgTotal="";
		if( balance<total)
			msgTotal="<font color=red>Insufficient Balance</font><a href='/paypal'>charge</a>";
		document.getElementById("msgTotal").innerHTML = msgTotal;
		if(okTitle && okYLink && okAOPV && okNOV && ( balance>=total))
			document.getElementById('theButton').disabled=0;
		else
			document.getElementById('theButton').disabled=1;
	}

//js.end.addvideo
         
//js.begin.myaccviewer
	function menu_hover(elem)
	{
		document.getElementById(elem).style.backgroundColor = '#F0EDEE';
	}
	function menu_out(elem,color)
	{
		document.getElementById(elem).style.backgroundColor = color;
	}

//js.end.myaccviewer
       
//js.begin.scriptviewer
	var nodeID;
	function onWChange(nID){
		nodeID=nID;
		setTimeout("onWChanged()",100);
	}
	function onWChanged(){
		var W=document.getElementById(nodeID+'W').value;
		document.getElementById(nodeID+'H').value=Math.floor(W*9/16);
		generateScript();
	}
	function onHChange(nID){
		nodeID=nID;
		setTimeout("onHChanged()",100);
	}
	function onHChanged(){
		var H=document.getElementById(nodeID+'H').value;
		document.getElementById(nodeID+'W').value=Math.floor(H*16/9);
		generateScript();
	}
	function generateScript(){
		var id=document.getElementById(nodeID+'id').value;
		var link=document.getElementById(nodeID+'link').value;
		var W=document.getElementById(nodeID+'W').value;
		var H=document.getElementById(nodeID+'H').value;
		document.getElementById(nodeID+'scriptarea').value="<EMBED SRC='http://www.sam-rad.com/YouTubePlayer.swf' FlashVars='id="+id+"&link="+link+"?version=3' WIDTH='"+W+"' HEIGHT='"+H+"' allowfullscreen='true' scale='noscale'/>";
	}

//js.end.scriptviewer
         