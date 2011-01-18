<?php
	date_default_timezone_set('GMT');
	require_once "../bizbank/eBoardPortal/eBoardPortal.php";
	$bizbank=NULL;
	if(isset($_GET['kill'])){
		$_SESSION['bizbank']=NULL;
		$bizbank=NULL;
	}

	if($_SESSION['bizbank']!=NULL)
		$bizbank=unserialize($_SESSION['bizbank']);
	else
		$bizbank=new eBoardPortal("eBoardPortal");

	if(!isset($_SESSION['user'])){
		$_SESSION['user']['UID']=-1;
		$_SESSION['user']['Email']='';
		$_SESSION['user']['Name']='';
	}

	function osRegMsg($fullname,$message){
		if(!isset($_SESSION['broadcast'][$message])){
			$_SESSION['broadcast'][$message]=array();
		}
		$_SESSION['broadcast'][$message][]=$fullname;
	}

	function osBookUser($user){
		$_SESSION['user']=$user;
	}

	function osBackUser(){
		return $_SESSION['user'];
	}

	function osBroadcast($msg,$info){
		global $bizbank;
		$bizbank->broadcast($msg,$info);
	}
	function osMessage($to,$msg,$info){
		global $bizbank;
		$bizbank->message($to,$msg,$info);
	}
	function osBackBizness(){
		return 1;
		global $bizbank;
		return $bizbank->bizness_id;
	}
	function osBackBizbank(){
		return 1;
		global $bizbank;
		return $bizbank->bizbank_id;
	}
	
	function osShow($callingBiz)
	{
		return '<div id="' . $callingBiz->_fullname . '">' . $callingBiz->html . '</div>';
	}

?>