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
		WIDTH='960' HEIGHT='540' allowfullscreen='true' scale='noscale' wmode='transparent'/>
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
	<body bgcolor="black">
		<div style="margin:50px auto auto auto;width:960px;">
			<?=$embed?>
		</div>
		<div style="margin:10px auto auto auto;width:960px;">
			<!-- AddThis Button BEGIN -->
			<div class="addthis_toolbox addthis_default_style" style="float:right;">
				<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
				<a class="addthis_button_tweet"></a>
				<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
				<a class="addthis_counter addthis_pill_style"></a>
			</div>
			<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4df136bc128feddc"></script>
			<script type="text/javascript"> var addthis_share = { templates: { twitter: '{{title}} - {{url}}' } } </script>
			<!-- AddThis Button END -->
		</div>
	</body>
<html>
<!-- <div class="instructions" title="Copy content in the text area and paste it in your weblog/website">?</div> -->

