<?php
	if(isset($_GET['kill']))
		unset($_SESSION['bizbank']);


	if(!isset($_SESSION['bizbank'])){
		$_SESSION['bizbank']=array();
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