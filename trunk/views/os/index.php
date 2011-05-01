<?php
	session_cache_expire(10);
	session_start();
	function myErrorHandler($errno, $errstr, $errfile, $errline){
		echo "<hr /><font color=red> bizError:$errno - ERROR:$errstr - FILE:$errfile - LINE:$errline </font><hr />";
	}
	set_error_handler("myErrorHandler");
	date_default_timezone_set('GMT');
	$UN="_biz";
	$Pass="";
	$DataBase="_biz";
	$ServerAddress="localhost";	
	require_once "biz/bizbank/bizbank.php";
	if(file_exists("db.php")) include_once "db.php";
	require_once "core.php";

	unset($bizbank);
	if(isset($_GET['kill'])){
		$_SESSION['osNodes']=array();
		$_SESSION['osMsg']=array();
		$_SESSION['osLink']=array();
		unset($_SESSION['user']);
	}
	if(isset($_GET['regdb'])){
		$bizdb=$_GET['regdb'];
		require_once "biz/".$bizdb."/".$bizdb.".sql";
		bizsql();
		echo 'ok';
	}else{
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
			$bizbank=new bizbank("B");
		}
		$JQueryCode=<<<JQUERY
			<script src="http://www.sam-rad.com/jquery.js" type="text/javascript"></script>
			<script type="text/javascript">
				$(document).ready(function(){
JQUERY;

		$msgMode=false;
		$_SESSION['silentmode']=false;
		if(isset($_POST['_message'])){
			if($_POST['_target']=="_broadcast"){
				osBroadcast($_POST['_message'],$_POST);
			}
			else{
				osMessage($_POST['_target'],$_POST['_message'],$_POST);
			}
			$msgMode=true;
		}else{
			if(count($_GET)>0){
				$_SESSION['osLink']=$_GET;
				unset($_SESSION['osLink']['kill']);
				$_SESSION['silentmode']=true;
				foreach($_SESSION['osLink'] as $a=>$b){
					$p=osParse($b);
					osMessage($a,"client_".$p[0],$p[1]);
				}
				$_SESSION['silentmode']=false;
			}
			if(isset($bizbank)){
				$bizbank->show(true);
				foreach($_SESSION['osNodes'] as $nodeFN=>$node){
					if(is_object($node['node'])){
						$JQueryCode.=$nodeFN."();";
					}
				}
			}
			echo $JQueryCode."});</script>";
			require_once "ajax.php";
		}

		foreach($_SESSION['osNodes'] as $node){
			if(isset($node['sleep'])){
				if(!$node['sleep']){
					if(isset($node['node'])){
						if(is_object($node['node'])){
							$node['node']->gotoSleep();
							if($msgMode){
								$node['node']->show(true);
							}
						}
					}
				}
			}
		}
	}
?>