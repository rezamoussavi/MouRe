
//js.begin.viewslogin
	function sndmsgOnEnterKey(evt,msg){
		var charCode = (evt.which) ? evt.which : event.keyCode;
		if(charCode==13)
			setTimeout("sndmsg('"+msg+"');",100);
		return true;
	}

//js.end.viewslogin
