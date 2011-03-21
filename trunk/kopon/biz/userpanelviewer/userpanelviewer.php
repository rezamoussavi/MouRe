<?PHP

/*
	Compiled by bizLang compiler version 3.0 (March 22 2011) By Reza Moussavi

	Author: Max Mirkia
	Date:	2/22/2010
	Version: 1.0
	------------------
	Author: Max Mirkia
	Date:	2/7/2010
	Version: 0.1

*/
require_once 'biz/profile/profile.php';
require_once 'biz/referal/referal.php';
require_once 'biz/history/history.php';

class userpanelviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables

	//Nodes (bizvars)
	var $profile;
	var $referal;
	var $history;

	function __construct($fullname) {
		$this->_frmChanged=false;
		$this->_tmpNode=false;
		if($fullname==null){
			$fullname='_tmpNode_'.count($_SESSION['osNodes']);
			$this->_tmpNode=true;
		}
		$this->_fullname=$fullname;
		if(!isset($_SESSION['osNodes'][$fullname])){
			$_SESSION['osNodes'][$fullname]=array();
			//If any message need to be registered will placed here
			$_SESSION['osMsg']['usertab_usertabChanged'][$this->_fullname]=true;
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmProfile';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->profile=new profile($this->_fullname.'_profile');

		$this->referal=new referal($this->_fullname.'_referal');

		$this->history=new history($this->_fullname.'_history');

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='userpanelviewer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'usertab_usertabChanged':
				$this->onTabChanged($info);
				break;
			default:
				break;
		}
	}

	function _bookframe($frame){
		$this->_frmChanged=true;
		$this->_curFrame=$frame;
		//$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$_style='';
		switch($this->_curFrame){
			case 'frmProfile':
				$_style='';
				break;
			case 'frmReferal':
				$_style='';
				break;
			case 'frmHistory':
				$_style='';
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


	function onTabChanged($info){
		$this->_bookframe("frm".$info['tabName']);
	}
	function frmProfile(){
		$toProfile = $this->profile->_backframe();
		$html=<<<PHTMLCODE

			$toProfile
		
PHTMLCODE;

		return $html;
	}
	function frmReferal(){
		$toShow = $this->referal->_backframe();
		$html=<<<PHTMLCODE

			$toShow
		
PHTMLCODE;

		return $html;
	}
	function frmHistory(){
		$toHistory = $this->history->_backframe();
		$html=<<<PHTMLCODE

			$toHistory
		
PHTMLCODE;

		return $html;
	}

}

?>