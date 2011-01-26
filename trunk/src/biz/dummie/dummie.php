<?PHP

/*
	Compiled by bizLang compiler version 1.1

	{Family included}

	Author:		Reza Moussavi
	Version:	1.1
	Date:		1/26/2011
	TestApproval: none
	-------------------
	Author: Reza Moussavi
	Date:	12/30/2010
	Version: 1.0

*/

class dummie {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;

	//Variables
	var $userEmail;
	var $UID;
	var $debug;

	//Nodes (bizvars)

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->_curFrame='sad';
		$this->debug="<hr>DEBUG:";
	}

	function __sleep(){
		return array('_fullname', '_curFrame','userEmail','UID','debug');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			return;
		}
		switch($message){
			case 'global_debug':
				$this->onDebug($info);
				break;
			case 'eboard_eBoardSelected':
				$this->onEBoardSelected($info);
				break;
			case 'user_login':
				$this->onLogin($info);
				break;
			case 'user_logout':
				$this->onLogout($info);
				break;
			case 'tab_tabselected':
				$this->onTab($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		switch($message){
			case 'global_debug':
				$this->onDebug($info);
				break;
			case 'eboard_eBoardSelected':
				$this->onEBoardSelected($info);
				break;
			case 'user_login':
				$this->onLogin($info);
				break;
			case 'user_logout':
				$this->onLogout($info);
				break;
			case 'tab_tabselected':
				$this->onTab($info);
				break;
			default:
				break;
		}
	}

	function _bookframe($frame){
		$this->_curFrame=$frame;
		$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$html='<div id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
		if($_SESSION['silentmode'])
			return;
		if($echo)
			echo $html;
		else
			return $html;
	}


//########################################
//         YOUR FUNCTIONS GOES HERE
//########################################


	function onDebug($info){
		if($info['debug']=="kill"){
			$this->debug="<hr>DEBUG:";
		}else{
			$this->debug.=$info['debug'].'<br>';
		}
		$this->_bookframe("debug");
	}
	function onTab($info){
		$this->userEmail="tab:".$info['UID'];
		$this->_bookframe("happy");
	}
	function onEBoardSelected($info){
		$this->userEmail="eboard: ".$info['UID'];
		$this->_bookframe("happy");
	}
	function onLogin($info){
		$this->userEmail=$info['email'];
		$this->_bookframe("happy");
	}
	function onLogout($info){
		$this->_bookframe("sad");
	}
	function happy(){
		return "<h2>:) </h2>". $this->userEmail;
	}
	function sad(){
		return '<h2>:( </h2>';
	}
	function debug(){
		return "<font color=red>".$this->debug."</font>";
	}

}

?>