
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
		document.getElementById("theTotal").innerHTML = total;
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
