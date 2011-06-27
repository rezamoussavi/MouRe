<?php
	/*
	*	This file provide for VIEWS project
	*	to get publink id and increment views
	*/
	include_once "../db.php";
	$osdbcon = mysql_connect ($ServerAddress, $UN, $Pass);
	$result=false;

	if ($osdbcon && isset($_GET['id']) && isset($_GET['link'])){
		mysql_select_db($DataBase,$osdbcon);
		$id=$_GET['id'];
		$link=substr($_GET['link'],0,strlen($_GET['link'])-10);
		/*
		*	Fetch Publisher and adUID
		*	If the id exists
		*/
		if($row=SELECT(" * FROM publink_info WHERE pubUID=".$id)){
			$adUID=$row['adLinkUID'];
			/* check if custom country has been set */
			if(isset($_GET['countryCode']) && isset($_GET['countryName'])){
				$countryCode=$_GET['countryCode'];
				$countryName=$_GET['countryName'];
			}else{
				/* Fetch User country code, country name and IP */
				$ip=getenv(HTTP_X_FORWARDED_FOR)?getenv(HTTP_X_FORWARDED_FOR):getenv(REMOTE_ADDR);
				if($row=SELECT(" CountryName, CountryCode2 FROM ip_country WHERE inet_aton('".$ip."') >= IPFrom AND inet_aton('".$ip."') <= IPTo")){
					$countryCode=$row['CountryCode2'];
					$countryName=$row['CountryName'];
				}else{
					$countryCode="??";
					$countryName="Unknown";
				}
			}
			/*  UPDATE [publink] number of views*/
			if($row=SELECT(" * FROM publink_info WHERE pubUID=".$id." AND YTID LIKE '".$link."'")){
				UPDATE(" publink_info SET totalView=totalView+1 WHERE pubUID=".$id." AND YTID LIKE '".$link."'");
				/*  UPDATE [adlink] number of views*/
				UPDATE(" adlink_info SET viewed=viewed+1 WHERE adUID=".$adUID);
				/* If the country for this adLink exist just update number of views */
				if($row=SELECT(" * FROM publink_stat WHERE adUID=".$adUID." AND countryName LIKE '".$countryName."'")){
					UPDATE(" publink_stat SET views=views+1 WHERE adUID=".$adUID." AND countryName LIKE '".$countryName."'");
				}else{/* If the country for this adLink does not exist add a row and set it to 1 (number of views */
					INSERT(" INTO publink_stat VALUES('".$adUID."','".$countryCode."','".$countryName."',1)");
				}
			}
		}
	}

	/*
	*	Database Interfaces
	*/
	function SELECT($s){	query("SELECT ".$s);	return fetch();	}
	function UPDATE($s){	return query("UPDATE ".$s);	}
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