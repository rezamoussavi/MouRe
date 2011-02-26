<?php
	session_start();
/***************************************
	// DEBUG

	function echoarr($prev,$a){
		if(is_array($a)){
			foreach($a as $k=>$v){
				echo "<font color=#dddddd>".$prev."</font> <font color=black>=</font> <font color=red>[".$k."]</font><br />";
				echoarr($prev." -> ".$k,$v);
			}
		}else if(is_object($a)){
			echo "<font color=#dddddd>".$prev."</font> <font color=black>=</font> <font color=yellow>Object</font><br />";
		}else{
			echo "<font color=#dddddd>".$prev."</font> <font color=black>=</font> <font color=green>'".$a."'</font><br />";
		}
	}

	echo "<hr>";
	echoarr("_",$_SESSION);
	echo "<hr>END<br>";
*/

	function myErrorHandler($errno, $errstr, $errfile, $errline){
		echo "<hr /> $errno, $errstr, $errfile, $errline <hr />";
	}
	set_error_handler("myErrorHandler");
	date_default_timezone_set('GMT');
	require_once "../bizbank/kopon/kopon.php";
	require_once "db.php";
	require_once "core.php";

	unset($bizbank);
	if(isset($_GET['kill'])){
		$_SESSION['osNodes']=array();
		$_SESSION['osMsg']=array();
		$_SESSION['osLink']=array();
		unset($_SESSION['user']);
	}

	if(!isset($_SESSION['user'])){
		$_SESSION['user']=array();
		$_SESSION['user']['UID']=-1;
		$_SESSION['user']['Email']='';
		$_SESSION['user']['Name']='';
	}

	if(!isset($_SESSION['osNodes'])){
		$_SESSION['osNodes']=array();
	}

	if(! isset($_POST['_message'])){
		$bizbank=new kopon("K");
	}

	$_SESSION['silentmode']=false;
	if(isset($_POST['_message'])){
		if($_POST['_target']=="_broadcast"){
			osBroadcast($_POST['_message'],$_POST);
		}
		else{
			osMessage($_POST['_target'],$_POST['_message'],$_POST);
		}
	}else{
		if(count($_GET)>0){
			$_SESSION['osLink']=$_GET;
			unset($_SESSION['osLink']['kill']);
			$_SESSION['silentmode']=true;
			foreach($_SESSION['osLink'] as $a=>$b){
				osMessage($a,"client_".$b,array());
			}
			$_SESSION['silentmode']=false;
		}
		if(isset($bizbank)){
			$bizbank->show(true);
		}
		require_once "ajax.php";
	}

	foreach($_SESSION['osNodes'] as $node){
		if(isset($node['sleep']))
			if(!$node['sleep'])
				if(isset($node['node']))
					if(is_object($node['node']))
						$node['node']->gotoSleep();
	}
/***************************************
	// DEBUG

	echo "<hr>";
	echoarr("_",$_SESSION);
	echo "<hr>END<br>";
*/

?>