<?php
	$code=13;
	$embed="img not found tag";
	foreach($_GET as $a=>$b){
		if($a=='v'){
			$code= "1x".$b;
			$autoplay="&autoplay=1";
		}else{
			$code= $a;
			$autoplay="&autoplay=0";
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
	include_once "../db.php";
	$osdbcon = mysql_connect ($ServerAddress, $UN, $Pass);
	$result=false;
	mysql_select_db($DataBase,$osdbcon);
	$q="SELECT ad.title as title, ad.img as img FROM adlink_info as ad inner join publink_info as pub ON pub.adLinkUID=ad.adUID WHERE pub.pubUID=".$id;
	$result= $osdbcon? mysql_query($q,$osdbcon):false;
	if($result) $row=mysql_fetch_assoc($result);
	if($row){
		$title=$row['title'];
		$img=$row['img'];
	}else{
		$title="Rocket Views ".$id;
		$img="http://rocketviews.com/img/logo.png";
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Rocket Views - <?=$title?></title>
		<meta property="og:title" content="<?=$title?>" />
		<meta property="og:description" content="" />
		<meta property="og:image" content="<?=$img?>" />
	</head>
	<body>
		<div style="margin:100px auto auto auto;width:960px;">
			<?=$embed?>
		</div>
	</body>
<html>
<!-- <div class="instructions" title="Copy content in the text area and paste it in your weblog/website">?</div> -->

