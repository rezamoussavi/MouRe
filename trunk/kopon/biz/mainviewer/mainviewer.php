<?PHP

/*
	Compiled by bizLang compiler version 2.0 (March 4 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}
	1.4: {multi parameter in link message}
	1.5: {multi secName support: frm/frame, msg/messages,fun/function/phpfunction}
	2.0: {upload bothe biz and php directly to server (ready to use)}

	Author: Reza Moussavi
	Date:	02/10/2011
	Version: 0.2
	---------------------
	Author: Reza Moussavi
	Date:	02/07/2011
	Version: 0.1

*/
require_once '../biz/tabbank/tabbank.php';
require_once '../biz/multipageviewer/multipageviewer.php';

class mainviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables

	//Nodes (bizvars)
	var $tabbar;
	var $pages;

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
			$_SESSION['osMsg']['user_login'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->tabbar=new tabbank($this->_fullname.'_tabbar');

		$this->pages=new multipageviewer($this->_fullname.'_pages');

		if(!isset($_SESSION['osNodes'][$fullname]['biz']))
			$this->init(); //Customized Initializing
		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='mainviewer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'user_login':
				$this->onLogedin($info);
				break;
			case 'user_logout':
				$this->onLogedout($info);
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
			case 'frm':
				$_style=' style="float:left; width:600;" ';
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


	function init(){
		$this->initTabbar("Main");
	}
	function initTabbar($def){
		$tab=array("Main","Previous");
		if(osIsAdmin()){
			$tab[]="CPanel";
		}
		$user=osBackUser();
		if($user['UID']!=-1){
			$tab[]="MyAcc";
		}
		$tab[]="How";
		$this->tabbar->bookContent($tab);
		$this->tabbar->bookSelected($def);
	}
	function onLogedin($info){
		$this->initTabbar($this->tabbar->curTabName);
		$this->_bookframe("frm");
	}
	function onLogedout($info){
		if($this->tabbar->curTabName=="MyAcc"){
			$this->initTabbar("Main");
		}else{
			$this->initTabbar($this->tabbar->curTabName);
		}
		$this->_bookframe("frm");
	}
	function frm(){
		$tab=$this->tabbar->_backframe();
		$pages=$this->pages->_backframe();
		$html=<<<PHTMLCODE

			$tab <br> $pages
		
PHTMLCODE;

		return $html;
	}

}

?>