<?PHP

/*
	Compiled by bizLang compiler version 1.02

	Author: Reza Moussavi
	Date:	12/29/2010
	Version:	1.0

*/
require_once '../biz/category/category.php';

class eblineviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;

	//Variables
	var $UID;

	//Nodes (bizvars)
	var $myCat;

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->_curFrame='frm';
		$this->myCat=new category($this->_fullname.'_myCat');
		$this->init(); //Customized Initializing
	}

	function __sleep(){
		return array('_fullname', '_curFrame','UID','myCat');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			$this->myCat->message($to, $message, $info);
			return;
		}
		switch($message){
			case 'clickBtn':
				$this->onClickBtn($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		$this->myCat->broadcast($message, $info);
		switch($message){
			case 'clickBtn':
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
		osBroadcast("eBoardSelected",array("UID"=>$this->UID));
		//_bookrame("frm");
	}
	function frm(){
		$Lable=$this->myCat->lable;
		$html=<<<PHTML
			<form name="{$this->_fullname}" method="post">
				<input type="hidden" name="_message" value="clickBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="$Lable" type = "button" onclick = 'JavaScript:sndmsg("{$this->_fullname}")' class="press" style="margin-top: 10px; margin-right: 0px;" />
			</form>
PHTML;
		return $html;
	}

}

?>