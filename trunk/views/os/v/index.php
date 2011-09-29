<?php
	session_start();
	/*
	*	This file provide for VIEWS project
	*	to get publink id and increment views
	*/
	$log="View Counter<br/>";
//	$GapTime=3600;
	$GapTime=3;
	$Now=date("YmdHis");
	require_once "../db.php";
	$osdbcon = mysql_connect ($ServerAddress, $UN, $Pass);
	$result=false;
	if ($osdbcon && isset($_GET['id']) && isset($_GET['link'])){
		mysql_select_db($DataBase,$osdbcon);
		$log.="db_connect GET[id] GET[link] : ok<br/>";
		$id=$_GET['id'];
//		$link=substr($_GET['link'],0,strlen($_GET['link'])-10);
		$link=$_GET['link'];
		/* Timer stuff */
		if(!isset($_SESSION['video'])){
			$_SESSION['video']=array();
		}
		if(!isset($_SESSION['video']['$link'])){
			/*
			*	This video has not viewed here
			*	Set the time and do view
			*/
			$_SESSION['video']['$link']=$Now-$GapTime-100;
			$log.="This video has not viewed here<br/>";
		}
		if($Now-$_SESSION['video']['$link']>$GapTime){
			$log.="Has been viewed more than $GapTime seconds ago<br/>";
			$_SESSION['video']['$link']=$Now;
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
				$log.="<b>countryCode</b>: $countryCode<br/>";
				$log.="<b>countryName</b>: $countryName<br/>";
				/*
					Check if video has finished and no view will apply
					It will check -1 and date and set it to 0 if time has passed
				*/
				$today=date("Y/m/d");
				UPDATE(" adlink_info SET running=0 WHERE running=-1 AND lastDate<'".$today."'");
				/* fetch video country */
				$adInfo=SELECT(" country FROM adlink_info WHERE adUID=".$adUID." AND running != 0");
				if(isset($adInfo['country']))
					$adCountry=$adInfo['country'];
				else
					$adCountry="__NONE";
				/*
				*	if viewer country match video target country
				*	will count it otherwise leave it
				*/
				$log.="<b>Country target</b>: $adCountry<br/>";
				if($adCountry==$countryName || $adCountry=="any"){
					$log.="Country: OK<br/>";
					/*  UPDATE [publink] number of views */
					if($row=SELECT(" * FROM publink_info WHERE pubUID=".$id." AND YTID LIKE '".$link."'")){
						$log.="YTID + publishID : FOUND<br/>";
						UPDATE(" publink_info SET totalView=totalView+1 WHERE pubUID=".$id." AND YTID LIKE '".$link."'");
						/*  UPDATE [adlink] number of views*/
						UPDATE(" adlink_info SET viewed=viewed+1 WHERE adUID=".$adUID);
						/* If the country for this adLink exist just update number of views */
						if($row=SELECT(" * FROM publink_stat WHERE adUID=".$adUID." AND countryName LIKE '".$countryName."'")){
							UPDATE(" publink_stat SET views=views+1 WHERE adUID=".$adUID." AND countryName LIKE '".$countryName."'");
						}else{/* If the country for this adLink does not exist add a row and set it to 1 (number of views */
							INSERT(" INTO publink_stat VALUES('".$adUID."','".$countryCode."','".$countryName."',1)");
						}
						$log.="<b>--- UPDATE DONE ---</b><br/>";
					}else
						$log.="YTID($link) + publishID($id) : NOT Found on DB<br/>";
				}//if country is correct
				else
					$log.="Country: Missmatch<br/>";
			}
		}// not viewed else
		else{//Has been viewd during last GapView
			$timeago=$Now-$_SESSION['video']['$link'];
			$log.="Has been viewed LESS than $GapTime seconds ago; $timeago seconds ago<br/>";
		}
	}//main if (id and link has been set)
	else
		$log.="db_connect GET[id] GET[link] : ERROR!<br/>";

//	osLog($log);


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

	/*
	*	OS functions
	*/
	function osLog($Message){
		$Biz="Viewer";
		$NodeID="Counter";
		$t=$_SERVER['REQUEST_TIME'];
		query("INSERT INTO os_log(TimeStamp,Biz,NodeID,Message) VALUES('$t','$Biz','$NodeID','$Message')");
	}

// osLog($_X);
// echo $log;
?>
