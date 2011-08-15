<?php
	$code=13;
	$embed="img not found tag";
	foreach($_GET as $a=>$b){
		if($a=='v'){
			$code= "1x".$b;
		}else{
			$code= $a;
			break;
		}
	}
	$id=(int)$code;
	$link=($code==13)?"RocketViews.com":substr($code,strlen("a".$id));
	$embed=<<<PHTML
		<EMBED	SRC='http://www.RocketViews.com/YouTubePlayer.swf'
		FlashVars='id={$id}&link={$link}?version=3'
		WIDTH='960' HEIGHT='540' allowfullscreen='true' scale='noscale'/>
PHTML;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	</head>
	<body>
		<div style="margin:100px auto auto auto;width:960px;">
			<?=$embed?>
		</div>
	</body>
<html>
<!-- <div class="instructions" title="Copy content in the text area and paste it in your weblog/website">?</div> -->

