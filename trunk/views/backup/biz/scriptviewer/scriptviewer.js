
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
