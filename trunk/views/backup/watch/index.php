<?php
	$code=13;
	$embed="img not found tag";
	foreach($_GET as $a=>$b){
		$code= $a;
		break;
	}
	$id=(int)$code;
	$link=($code==13)?"RocketViews.com":substr($code,strlen("a".$id));
	$embed=<<<PHTML
		<EMBED	SRC='http://www.sam-rad.com/YouTubePlayer.swf'
		FlashVars='id={$id}&link={$link}?version=3'
		WIDTH='960' HEIGHT='540' allowfullscreen='true' scale='noscale'/>
PHTML;
?>
<html>
	<head>
	</head>
	<body>
		<div style="margin:100px auto auto auto;width:960px;">
			<?=$embed?>
		</div>
	</body>
<html>
