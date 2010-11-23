<?php
	session_start();
	
	echo <<<EOF
	<?xml version="1.0" encoding="UTF-8"?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" src="css/layout.css" />
	</head>
	<body>
EOF;
	

	require_once "db.php";
	require_once "core.php";
	require_once "bizbank.php"; //make a new object from propper bizbank class and set into $bizbank

	if(isset($_POST['_message'])){
		if($_POST['_target']=="_broadcast"){
			os_broadcast($_POST['_message'],$_POST);
		}
		else{
			$bizbank->message($_POST['_target'],$_POST['_message'],$_POST);
		}
	}else{
		$bizbank->show();
		require_once "ajax.php";
	}
	
	
	echo <<<EOF
	</body>
	</html>
EOF;
?>
