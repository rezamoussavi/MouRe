<?php
	session_start();
	require_once "db.php";
	require_once "core.php";

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
		$bizbank->show();
		require_once "ajax.php";
	}
echo "<hr>Sleeping among ".count($_SESSION['osNodes'])." bizes";
$sindex=0;
	foreach($_SESSION['osNodes'] as $bizname=>$val){
if($bizname=="frm_tabbar0") echo "#1 : ".$_SESSION['osNodes']['frm_tabbar0']['title']." - ".$_SESSION['osNodes']['frm_tabbar0']['UID']."<br>";
		if($val['node']!=NULL)
			if(is_object($val['node'])){
				$sindex++;
				$val['node']->sleep();
			}
	}
echo "<br> $sindex bizes went to sleep<hr>";
?>