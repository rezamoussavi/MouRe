<?PHP
	if(isset($_GET['kill']))
		unset($_SESSION['bizbank']);


	if(!isset($_SESSION['bizbank'])){
		$_SESSION['bizbank']=array();
	}

	function os_broadcast($msg,$info){
		global $bizbank;
		$bizbank->broadcast($msg,$info);
	}
	function os_message($to,$msg,$info){
		global $bizbank;
		$bizbank->message($to,$msg,$info);
	}
	function os_back_bizness(){
		global $bizbank;
		return $bizbank->bizness_id;
	}
	function os_back_bizbank(){
		global $bizbank;
		return $bizbank->bizbank_id;
	}

?>