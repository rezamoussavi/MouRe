
//js.begin.mainpageviewer
	$(document).ready(function(){
	   	$("#showbox_content_home").click(function(e){
			var x = e.pageX - this.offsetLeft;
			var y = e.pageY - this.offsetTop;
			if(x>0 && x<475 && y>335 && y<420){
				window.location.href = "http://rocketviews.com/?p=PubVideo";
			}else if (x>475 && x<950 && y>335 && y<420){
				window.location.href = "http://rocketviews.com/?p=AdVideo";
			}
		});
		$("#showbox_content_home").mousemove(function(e){
			var x = e.pageX - this.offsetLeft;
			var y = e.pageY - this.offsetTop;
			if((x>0 && x<475 && y>335 && y<420) || (x>475 && x<950 && y>335 && y<420)){
				$(this).css('cursor','pointer');
			}else{
				$(this).css('cursor','auto');
			}
		});
	   	$("#showbox_content_addvideo").click(function(e){
			var x = e.pageX - this.offsetLeft;
			var y = e.pageY - this.offsetTop;
			if(x>0 && x<475 && y>405 && y<490){
				window.location.href = "http://rocketviews.com/?p=PubVideo";
			}
		});
		$("#showbox_content_addvideo").mousemove(function(e){
			var x = e.pageX - this.offsetLeft;
			var y = e.pageY - this.offsetTop;
			if(x>0 && x<475 && y>405 && y<490){
				$(this).css('cursor','pointer');
			}else{
				$(this).css('cursor','auto');
			}
		});
	   	$("#showbox_content_pubvideo").click(function(e){
			var x = e.pageX - this.offsetLeft;
			var y = e.pageY - this.offsetTop;
			if (x>475 && x<950 && y>335 && y<420){
				window.location.href = "http://rocketviews.com/?p=AdVideo";
			}
		});
		$("#showbox_content_pubvideo").mousemove(function(e){
			var x = e.pageX - this.offsetLeft;
			var y = e.pageY - this.offsetTop;
			if(x>475 && x<950 && y>335 && y<420){
				$(this).css('cursor','pointer');
			}else{
				$(this).css('cursor','auto');
			}
		});
	});

//js.end.mainpageviewer
