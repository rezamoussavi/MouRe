<?php
	/*
	*	This file provide for VIEWS project
	*	to get publink id and increment views
	*/
	include_once "db.php";
	$osdbcon = mysql_connect ($ServerAddress, $UN, $Pass);
	$result=false;

	if ($osdbcon && isset($_GET['id'])){
		mysql_select_db($DataBase,$osdbcon);
		$id=$_GET['id'];
		/*
		*	Fetch Publisher and adUID
		*	If the id exists
		*/
		if($row=SELCET(" * FROM publink_info WHERE pubUID=".$id)){
			$adUID=$row['adLinkUID'];
			/* Fetch User country code, country name and IP */
			$ip="127.0.0.1"; /* TEST */ 
			$countryCode=isset($_GET['countryCode'])? $countryCode=$_GET['countryCode']:"SE"; /* TEST */
			$countryName="Sweden"; /* TEST */ 
			/*  UPDATE [publink] number of views*/
			UPDATE(" publink_info SET totalView=totalView+1 WHERE pubUID=".$id);
			/*  UPDATE [adlink] number of views*/
			UPDATE(" adlink_info SET viewed=viewed+1 WHERE adUID=".$adUID);
			/* If the country for this adLink exist just update number of views */
			if($row=SELECT(" * FROM publink_stat WHERE adUID=".$adUID." AND countryName='".$countryName."'")){
				UPDATE(" publink_stat SET views=views+1 WHERE adUID=".$adUID." AND countryName='".$countryName."'");
			}else{/* If the country for this adLink does not exist add a row and set it to 1 (number of views */
				INSERT(" INTO publink_stat VALUES('".$adUID."','".$countryCode."','".$countryName."',1)");
			}
		}
	}

	/*
	*	Database Interfaces
	*/
	function SELECT($s){	query("SELECT ".$s);	return fetch();	}
	function UPDATE($s){	query("UPDATE ".$s);	}
	function INSERT($s){	query("INSERT ".$s);	}

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