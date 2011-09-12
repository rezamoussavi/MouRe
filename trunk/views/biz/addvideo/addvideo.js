
//js.begin.addvideo
	var okTitle=false;
	var okYLink=false;
	var okAOPV=false;
	var okNOV=false;
	function isNumber(num){
		size=(num+"").length;
		for(i=0;i<size;i++)
			if( (num.charAt(i)<'0' || num.charAt(i)>'9') && (num.charAt(i)!='.') )
				return false;
		return true;
	}
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
		var publisher="0";
		if(!isNumber(_eGetVal('theAOPV')) || _eGetVal('theAOPV') < minAOPV){
			msg="<font color=red>Invalid</font>";// ERROR
			okAOPV=false;
		}else{
			publisher=Math.round(_eGetVal('APRatio')*_eGetVal('theAOPV')*100)/100;
		}
		document.getElementById('msgAOPV').innerHTML = msg;
		_eSetHTML("publisher_will_earn",publisher);
		checkAll();
	}
	function checkNOV(minNOV){
		okNOV=true;
		var msg="<font color=green>OK</font>";// OK
		if(!isNumber(_eGetVal('theNOV')) || _eGetVal('theNOV') < minNOV){
			msg="<font color=red>Invalid</font>";// ERROR
			okNOV=false;
		}
		document.getElementById("msgNOV").innerHTML = msg;
		checkAll();
	}
	function checkAll(){
		var total=document.getElementById('theAOPV').value * document.getElementById('theNOV').value;
		var balance=document.getElementById('theBalance').innerHTML;
		_eSetVal("theTotal",total);
		_eSetHTML("theTotalShow",total);
		var msgTotal="";
		if( balance<total)
			msgTotal="<font color=red>Insufficient Balance</font><a href='/?p=Myacc_balance'>charge</a>";
		document.getElementById("msgTotal").innerHTML = msgTotal;
		if(okTitle && okYLink && okAOPV && okNOV && ( balance>=total))
			document.getElementById('theButton').disabled=0;
		else
			document.getElementById('theButton').disabled=1;
	}

//js.end.addvideo
