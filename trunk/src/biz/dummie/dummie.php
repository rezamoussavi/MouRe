<?PHP

/*
	Compiled by bizLang compiler version 1.3.5 (Feb 3 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}

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
	var $_tmpNode;

	//Variables
	var $userEmail;
	var $UID;
	var $debug;

	//Nodes (bizvars)

	function __construct($fullname) {
		$this->_tmpNode=false;
		if($fullname==null){
			$fullname='_tmpNode_'.count($_SESSION['osNodes']);
			$this->_tmpNode=true;
		}
		$this->_fullname=$fullname;
		if(!isset($_SESSION['osNodes'][$fullname])){
			$_SESSION['osNodes'][$fullname]=array();
			//If any message need to be registered will placed here
			$_SESSION['osMsg']['global_debug'][$this->_fullname]=true;
			$_SESSION['osMsg']['eboard_eBoardSelected'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_login'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
			$_SESSION['osMsg']['tab_tabselected'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='sad';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		if(!isset($_SESSION['osNodes'][$fullname]['userEmail']))
			$_SESSION['osNodes'][$fullname]['userEmail']='';
		$this->userEmail=&$_SESSION['osNodes'][$fullname]['userEmail'];

		if(!isset($_SESSION['osNodes'][$fullname]['UID']))
			$_SESSION['osNodes'][$fullname]['UID']='';
		$this->UID=&$_SESSION['osNodes'][$fullname]['UID'];

		if(!isset($_SESSION['osNodes'][$fullname]['debug']))
			$_SESSION['osNodes'][$fullname]['debug']="<hr>DEBUG:";
		$this->debug=&$_SESSION['osNodes'][$fullname]['debug'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='dummie';
	}

	function sleep(){
		$_SESSION['osNodes'][$this->_fullname]['slept']=true;
	}

	function __destruct() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
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