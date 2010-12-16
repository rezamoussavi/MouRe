<?php
	session_start();
	
	require_once "db.php";
	require_once "core.php";
	require_once "bizbank.php"; //make a new object from propper bizbank class and set into $bizbank

	if(isset($_POST['_message'])){
		if($_POST['_target']=="_broadcast"){
			osBroadcast($_POST['_message'],$_POST);
		}
		else{
			osMessage($_POST['_target'],$_POST['_message'],$_POST);
		}
	}else{
		$bizbank->show();
		require_once "ajax.php";
	}
?>
