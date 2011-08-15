<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	5/31/2011
	Ver:	1.0

*/
require_once 'biz/offer/offer.php';

class settingviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $message;

	//Nodes (bizvars)
	var $offer;

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
			$_SESSION['osMsg']['frame_apply'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmSetting';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->offer=new offer($this->_fullname.'_offer');

		if(!isset($_SESSION['osNodes'][$fullname]['message']))
			$_SESSION['osNodes'][$fullname]['message']="";
		$this->message=&$_SESSION['osNodes'][$fullname]['message'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='settingviewer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_apply':
				$this->onApply($info);
				break;
			default:
				break;
		}
	}

	function _bookframe($frame){
		$this->_curFrame=$frame;
		//$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$_style='';
		switch($this->_curFrame){
			case 'frmSetting':
				$_style='  style="" ';
				break;
		}
		$html='<div '.$_style.' id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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


	/************************
	*	Frames
	*************************/
	function frmSetting(){
		$frmName=$this->_fullname."Apply";
		$offer=$this->offer->backInfo();
		$minAOPV=$offer['minAOPV'];
		$minNOV=$offer['minNOV'];
		$APRatio=$offer['APRatio'];
		$minLifeTime=$offer['minLifeTime'];
		$minCancelTime=$offer['minCancelTime'];
		$msg=$this->message;
		$this->message="";
		return <<<PHTMLCODE

			$msg
			<form id="$frmName" method="POST">
				<input type="hidden" name="_message" value="frame_apply" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				minAOPV: <input name="minAOPV" value="$minAOPV" /><br/>
				minNOV: <input name="minNOV" value="$minNOV" /><br/>
				APRatio: <input name="APRatio" value="$APRatio" /><br/>
				minLifeTime: <input name="minLifeTime" value="$minLifeTime" /> days<br/>
				minCancelTime: <input name="minCancelTime" value="$minCancelTime" /> days<br/>
				<input type="button" value="Apply" onclick='Javascript:sndmsg("$frmName")' />
			</form>
		
PHTMLCODE;

	}
	/************************
	*	Message Handler
	*************************/
	function onApply($info){
		$this->offer->bookInfo($info);
		$this->message="<font color=green>Changes has been saved!</font>";
	}

}

?>