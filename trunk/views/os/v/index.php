<?php
	session_start();
	/*
	*	This file provide for VIEWS project
	*	to get publink id and increment views
	*/
	$GapTime=3600;
	$Now=date("YmdHis");
	include_once "../db.php";
	$osdbcon = mysql_connect ($ServerAddress, $UN, $Pass);
	$result=false;
													//$_X.=="A .Welcom<br>";
	if ($osdbcon && isset($_GET['id']) && isset($_GET['link'])){
													//$_X.=.="B. id=".$_GET['id']." - link=".$_GET['link']."<br>";
		mysql_select_db($DataBase,$osdbcon);
													//$_X.=.="C.1 <br>";
		$id=$_GET['id'];
		$link=substr($_GET['link'],0,strlen($_GET['link'])-10);
		/* Timer stuff */
													//$_X.=.="C.2 <br>";
		if(!isset($_SESSION['video'])){
			$_SESSION['video']=array();
													//$_X.=.="C.3 <br>";
		}
		if(!isset($_SESSION['video']['$link'])){
			/*
			*	This video has not viewed here
			*	Set the time and do view
			*/
			$_SESSION['video']['$link']=$Now-$GapTime-100;
													//$_X.=.="C.4 <br>";
		}
		if($Now-$_SESSION['video']['$link']>$GapTime){
													//$_X.=.="C.5 ".$Now-$_SESSION['video']['$link']." > ".$GapTime."(gaptime)<br>";
			$_SESSION['video']['$link']=$Now;
			/*
			*	Fetch Publisher and adUID
			*	If the id exists
			*/
			if($row=SELECT(" * FROM publink_info WHERE pubUID=".$id)){
													//$_X.=.="D.<br>";
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
				/* fetch video country */
				$adInfo=SELECT(" country FROM adlink_info WHERE adUID=".$adUID);
				$adCountry=$adInfo['country'];
				/*
				*	if viewer country match video target country
				*	will count it otherwise leave it
				*/
													//$_X.=.="E.<br>";
				if($adCountry==$countryName || $adCountry=="any"){
													//$_X.=.="F.<br>";
					/*  UPDATE [publink] number of views */
					if($row=SELECT(" * FROM publink_info WHERE pubUID=".$id." AND YTID LIKE '".$link."'")){
													//$_X.=.="G.<br>";
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
				}//if country is correct
			}
		}// not viewed else
		else{//Has been viewd during last GapView
		}
	}//main if

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

//oslog($_X);
//echo $_X;
?>
