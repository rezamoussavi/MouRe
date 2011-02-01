<?PHP

/*
	Compiled by bizLang compiler version 1.3 (Jan 2 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}

	Author:		Reza Moussavi
	Version:	1.1
	Date:		1/26/2011
	TestApproval: none
	-------------------
	Author: Reza Moussavi
	Date:	12/29/2010
	Version:	1.0

*/
require_once '../biz/category/category.php';

class eblineviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $UID;

	//Nodes (bizvars)
	var $myCat;

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
			$_SESSION['osMsg']['frame_clickBtn'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->myCat=new category($this->_fullname.'_myCat');

		if(!isset($_SESSION['osNodes'][$fullname]['UID']))
			$_SESSION['osNodes'][$fullname]['UID']='';
		$this->UID=&$_SESSION['osNodes'][$fullname]['UID'];

		$this->init(); //Customized Initializing
		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']=eblineviewer;
	}

	function sleep(){
		$_SESSION['osNodes'][$this->_fullname]['slept']=true;
	}

	function __destruct() {
		if($this->_tmpNode or !isset($_SESSION['osNodes'][$this->_fullname]['slept']))
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['slept']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_clickBtn':
				$this->onClickBtn($info);
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


	function init(){
		$this->myCat->bookUID($this->UID);
	}
	function bookUID($UID){
		$this->UID=$UID;
		$this->init();
	}
	function onClickBtn(){
		osBroadcast("eboard_eBoardSelected",array("UID"=>$this->UID));
		//_bookrame("frm");
	}
	function frm(){
		$Lable=$this->myCat->lable;
		$html=<<<PHTML
			<form name="{$this->_fullname}" method="post">
				<input type="hidden" name="_message" value="frame_clickBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="$Lable" type = "button" onclick = 'JavaScript:sndmsg("{$this->_fullname}")' class="press" style="margin-top: 10px; margin-right: 0px;" />
			</form>
PHTML;
		return $html;
	}

}

?>