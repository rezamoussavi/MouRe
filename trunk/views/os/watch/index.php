<?php
	session_start();
	include_once "../db.php";
	if(isset($_GET['id']) && isset($_GET['link'])){
		$id=isset($_GET['id']);
		$link=isset($_GET['link']);
	}else{
		$id=1;
		$link="lLjYV3rMDPM";
	}
	$embed=<<<EMBD
		<EMBED	SRC='http://www.sam-rad.com/YouTubePlayer.swf'
				FlashVars='id={$id}&link={$link}?version=3'
				WIDTH='960' HEIGHT='540' allowfullscreen='true' scale='noscale'/>
EMBD;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	</head>
	<body>
		<div stle="margin-left:auto;margin-right:auto;margin-top:100px;width:1000px;">
			<?php echo $embed; ?>
		</div>
	</body>
</html>
<!-- <div class="instructions" title="Copy content in the text area and paste it in your weblog/website">?</div> -->

<?php
	/*
	*	Database functions
	*/
	function query($s){
		global $osdbcon,$result;
		$result= $osdbcon? mysql_query($s,$osdbcon):false;
		return $result;
	}
	function fetch(){
		global $result;
		if(!$result) return false;
		return mysql_fetch_assoc($result);
	}
?>
