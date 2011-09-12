
//js.begin.mainpageviewer
	$(document).ready(function(){
	   	$("#showbox_content").click(function(e){
			var x = e.pageX - this.offsetLeft;
			var y = e.pageY - this.offsetTop;
			if(x>0 && x<475 && y>335 && y<420){
				window.location.href = "http://rocketviews.com/?p=PubVideo";
			}else if (x>475 && x<950 && y>335 && y<420){
				window.location.href = "http://rocketviews.com/?p=AdVideo";
			}
		});
		$("#showbox_content").mousemove(function(e){
			var x = e.pageX - this.offsetLeft;
			var y = e.pageY - this.offsetTop;
			if((x>0 && x<475 && y>335 && y<420) || (x>475 && x<950 && y>335 && y<420)){
				$(this).css('cursor','pointer');
			}else{
				$(this).css('cursor','auto');
			}
		});
	});

//js.end.mainpageviewer
