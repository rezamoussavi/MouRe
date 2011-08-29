<?php
	session_cache_expire(10);
	session_start();
ini_set('display_errors', 1);
	function myErrorHandler($errno, $errstr, $errfile, $errline){
		$m="$errstr<hr width=100px align=left>$errfile<br>(line $errline)";
		$ip=getenv(HTTP_X_FORWARDED_FOR)?getenv(HTTP_X_FORWARDED_FOR):getenv(REMOTE_ADDR);
		osLog("OS","<font color=red>ERROR</font> ($errno) <br> ip: $ip",$m);
	}
//	set_error_handler("myErrorHandler");
	date_default_timezone_set('GMT');
	$UN="_biz";
	$Pass="";
	$DataBase="_biz";
	$ServerAddress="localhost";	
	require_once "biz/bizbank/bizbank.php";
	if(file_exists("db.php")) include_once "db.php";
	require_once "core.php";
	unset($bizbank);
	if(!isset($_SESSION['logMessages'])){
		$_SESSION['logMessages']=false;
	}
	if(isset($_GET['message'])){
		$_SESSION['logMessages']=($_GET['message']=='on')?true:false;
		unset($_GET['message']);
	}
	if(isset($_GET['kill'])){
		$_SESSION['osNodes']=array();
		$_SESSION['osMsg']=array();
		$_SESSION['osLink']=array();
		unset($_SESSION['user']);
		$_GET=array();
	}
	/*
	*	MODE: regdb
	*/
	if(isset($_GET['regdb'])){
		$bizdb=$_GET['regdb'];
		require_once "biz/".$bizdb."/".$bizdb.".sql";
		bizsql();
		echo 'ok';	
	/*
	*	MODE: appendJS
	*/
	}elseif(isset($_GET['appendJS'])){
		// remove old biz.js file
		if(file_exists('biz.js')){
//			chmod('biz.js', 0666);
			unlink('biz.js');
		}
		//list of all bizes
		$bizes=scandir("biz/");
		//append all together in biz.js
		foreach($bizes as $js){
			$source="biz/".$js."/".$js.".js";
			if(file_exists($source))
				file_put_contents("biz.js",file_get_contents($source),FILE_APPEND | LOCK_EX);
		}
		echo 'ok';
	/*
	*	MODE: appendCSS
	*/
	}elseif(isset($_GET['appendCSS'])){
		// remove old biz.js file
		if(file_exists('biz.css')){
			//chmod('biz.css', 0666);
			unlink('biz.css');
		}
		//list of all bizes
		$bizes=scandir("biz/");
		//append all together in biz.js
		foreach($bizes as $cs){
			$source="biz/".$cs."/".$cs.".css";
			if(file_exists($source)){
				file_put_contents("biz.css",file_get_contents($source),FILE_APPEND | LOCK_EX);
			}
		}
		echo 'ok';
	/*
	*	MODE: log
	*/
	}elseif(isset($_GET['log'])){
		showLogPage();
	/*
	*	MODE: session_report
	*/
	}elseif(isset($_GET['session_report'])){
		$session_content=arr2strOpt($_SESSION);
		echo <<<PHTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	</head>
	<body>
	$session_content
	<div id="os_message_box" style="visibility:hidden"> </div>
	</body>
</html>
<!-- <div class="instructions" title="Copy content in the text area and paste it in your weblog/website">?</div> -->
PHTML;
	/*
	*	MODE: normal
	*/
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
		$page_content="";
		$JQueryCode=<<<JQUERY
			<script src="./jquery.js" type="text/javascript"></script>
			<script src="./biz.js" type="text/javascript"></script>
			<script src="./ajax.js" type="text/javascript"></script>
			<link rel="stylesheet" type="text/css" href="./biz.css" />
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
			if(isset($_GET['p'])){
				$emptyar=array();
				osBroadcast("page_".$_GET['p'],$emptyar);
			}elseif(count($_GET)>0){
				$_SESSION['osLink']=$_GET;
				unset($_SESSION['osLink']['kill']);
				$_SESSION['silentmode']=true;
				if(isset($_SESSION['osLink']))if(is_array($_SESSION['osLink'])){
					foreach($_SESSION['osLink'] as $a=>$b){
						$p=osParse($b);
						osMessage($a,"client_".$p[0],$p[1]);
					}
				}
				$_SESSION['silentmode']=false;
			}
			if(isset($bizbank)){
				$page_content=$bizbank->show(false);
				foreach($_SESSION['osNodes'] as $nodeFN=>$node){
					if(isset($node['node'])){
						if(is_object($node['node'])){
//						$JQueryCode.="if(window.".$nodeFN.")".$nodeFN."();";
						}
					}
				}
			}
			echo <<<PHTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<!-- Quantcast Tag -->
			<script type="text/javascript">
			var _qevents = _qevents || [];

			(function() {
			var elem = document.createElement('script');
			elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
			elem.async = true;
			elem.type = "text/javascript";
			var scpt = document.getElementsByTagName('script')[0];
			scpt.parentNode.insertBefore(elem, scpt);
			})();

			_qevents.push({
			qacct:"p-85opaofliuE9o"
			});
			</script>

			<noscript>
			<div style="display:none;">
			<img src="//pixel.quantserve.com/pixel/p-85opaofliuE9o.gif" border="0" height="1" width="1" alt="Quantcast"/>
			</div>
			</noscript>
		<!-- End Quantcast tag -->
		$JQueryCode });
		</script>
		<script type="text/javascript">
			function _e(elem){return document.getElementById(elem);}
			function _eGetHTML(elem){return _e(elem).innerHTML;}
			function _eSetHTML(elem,HTML){_e(elem).innerHTML=HTML;}
			function _eGetVal(elem){return _e(elem).value;}
			function _eSetVal(elem,val){_e(elem).value=val;}
			function _focus(elem){setTimeout("_doSetFocus('"+elem+"');",500);}
			function _doSetFocus(elem){_e(elem).focus();}
		</script>
	</head>
	<body>
	$page_content
	<div id="os_message_box" style="visibility:hidden"> </div>
	</body>
</html>
<!-- <div class="instructions" title="Copy content in the text area and paste it in your weblog/website">?</div> -->
PHTML;
		}

		foreach($_SESSION['osNodes'] as $node){
			if(isset($node['node'])){
				if(is_object($node['node'])){
					if($msgMode){
						$node['node']->show(true);
					}
					$node['node']->gotoSleep();
				}
			}
		}
	}
	/*
	*	END OF MODE: normal
	*/
?>
