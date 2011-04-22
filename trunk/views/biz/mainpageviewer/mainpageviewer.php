<?PHP

/*
	Compiled by bizLang compiler version 3.2 [DB added] (March 26 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	4/21/2011
	Ver:		0.1

*/

class mainpageviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables
	var $userpage;

	//Nodes (bizvars)

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
			$_SESSION['osMsg']['tab_tabChanged'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_showMyAccount'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmHome';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		if(!isset($_SESSION['osNodes'][$fullname]['userpage']))
			$_SESSION['osNodes'][$fullname]['userpage']=0;
		$this->userpage=&$_SESSION['osNodes'][$fullname]['userpage'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='mainpageviewer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'tab_tabChanged':
				$this->onTabChanged($info);
				break;
			case 'user_showMyAccount':
				$this->onMyAcc($info);
				break;
			case 'user_logout':
				$this->onLogOut($info);
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
			case 'frmHome':
				$_style=' style="float:left; width:700;" ';
				break;
			case 'frmAdVideo':
				$_style=' style="float:left; width:700;" ';
				break;
			case 'frmPubVideo':
				$_style=' style="float:left; width:700;" ';
				break;
			case 'frmUser':
				$_style=' style="float:left; width:700;" ';
				break;
			case 'frmAdmin':
				$_style=' style="float:left; width:700;" ';
				break;
			case 'frmHow':
				$_style=' style="float:left; width:700;" ';
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


	//////////////////////////////////////////////////////////////////////
	//			MESSAGE
	//////////////////////////////////////////////////////////////////////
	function onLogOut(){
		if($this->userpage==1){
			$this->userpage=0;
			$this->_bookframe("frmHome");
		}
	}
	function onTabChanged($info){
		switch($info['tabName']){
			case "Home":
				$this->userpage=0;
				$this->_bookframe("frmHome");
				break;
			case "How":
				$this->userpage=0;
				$this->_bookframe("frmHow");
				break;
		}
	}
	function onMyAcc($info){
		if(osBackUserRole()=="admin"){
			$this->userpage=1;
			$this->_bookframe("frmAdmin");
		}else{
			$this->userpage=1;
			$this->_bookframe("frmUser");
		}
	}
	function on2($info){
		$this->userpage=0;
	}
	function on3($info){
		$this->userpage=0;
	}
	//////////////////////////////////////////////////////////////////////
	//			VIEW
	//////////////////////////////////////////////////////////////////////
	function frmHow(){
		return <<<PHTMLCODE

			How / About
		
PHTMLCODE;

	}
	function frmHome(){
		return <<<PHTMLCODE

			Home Page
		
PHTMLCODE;

	}
	function frmAdmin(){
		return <<<PHTMLCODE

			Admin Page
		
PHTMLCODE;

	}
	function frmUser(){
		return <<<PHTMLCODE

			User Page
		
PHTMLCODE;

	}
	function frmPubVideo(){
		return <<<PHTMLCODE

			Publish a Video
		
PHTMLCODE;

	}
	function frmAdVideo(){
		return <<<PHTMLCODE

			Advertise a Video
		
PHTMLCODE;

	}

}

?>