<?PHP

/*
	Compiled by bizLang compiler version 1.4 (Feb 4 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}
	1.4: {multi parameter in link message}

	Author:		Reza Moussavi
	Version:	1.1
	Date:		1/26/2011
	TestApproval: none
	-------------------
	Author:		Reza Moussavi
	Date:		1/12/2011
	Version:	1.0
	TestApproval: none

*/
require_once '../biz/tabbank/tabbank.php';
require_once '../biz/epostentry/epostentry.php';
require_once '../biz/epostbank/epostbank.php';
require_once '../biz/category/category.php';

class ebframe {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $cureBUID;

	//Nodes (bizvars)
	var $tabbar;
	var $entrybar;
	var $epostbar;

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
			$_SESSION['osMsg']['eboard_eBoardSelected'][$this->_fullname]=true;
			$_SESSION['osMsg']['tab_tabselected'][$this->_fullname]=true;
			$_SESSION['osMsg']['epost_newPostAdded'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->tabbar=new tabbank($this->_fullname.'_tabbar');

		$this->entrybar=new epostentry($this->_fullname.'_entrybar');

		$this->epostbar=new epostbank($this->_fullname.'_epostbar');

		if(!isset($_SESSION['osNodes'][$fullname]['cureBUID']))
			$_SESSION['osNodes'][$fullname]['cureBUID']='';
		$this->cureBUID=&$_SESSION['osNodes'][$fullname]['cureBUID'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='ebframe';
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
			case 'eboard_eBoardSelected':
				$this->onEBoardSelected($info);
				break;
			case 'tab_tabselected':
				$this->onTabSelected($info);
				break;
			case 'epost_newPostAdded':
				$this->onNewPostAdded($info);
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


	function onEBoardSelected($info){
		$this->cureBUID=$info['UID'];
		$cat=new category("");
		$content=$cat->backContentOf($this->cureBUID);
		$ar=array();
		foreach($content as $c){
			$lable=$cat->backLable($c['bizUID']);
			$ar[]=array("name"=>$lable,"UID"=>$c['bizUID']);
		}
		$this->tabbar->booklist($ar);
		$this->_bookframe("frm");
	}
	function onTabSelected($info){
		$this->entrybar->bookPostOwner("category",$info['UID']);
		$this->epostbar->showBy("category",$info['UID']);
	}
	function onNewPostAdded($info){
		$this->epostbar->reload();
	}
	function frm(){
		$tabbar=$this->tabbar->_backframe();
		$entrybar=$this->entrybar->_backframe();
		$epostbar=$this->epostbar->_backframe();
		$html=<<<PHTML
			$tabbar<br>
			$entrybar<br>
			$epostbar<br>
PHTML;
		return $html;
	}

}

?>