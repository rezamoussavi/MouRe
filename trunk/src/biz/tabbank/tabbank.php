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
	-------------------------
	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/7/2011
	TestApproval: none

*/
require_once '../biz/tab/tab.php';

class tabbank {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $curTabUID;

	//Nodes (bizvars)
	var $tab; // array of biz

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
			$_SESSION['osMsg']['tab_tabselected'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		//handle arrays
		$this->tab=array();
		if(!isset($_SESSION['osNodes'][$fullname]['tab']))
			$_SESSION['osNodes'][$fullname]['tab']=array();
		foreach($_SESSION['osNodes'][$fullname]['tab'] as $arrfn)
			$this->tab[]=new tab($arrfn);

		if(!isset($_SESSION['osNodes'][$fullname]['curTabUID']))
			$_SESSION['osNodes'][$fullname]['curTabUID']=-1;
		$this->curTabUID=&$_SESSION['osNodes'][$fullname]['curTabUID'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='tabbank';
	}

	function sleep(){
		$_SESSION['osNodes'][$this->_fullname]['slept']=true;
		$_SESSION['osNodes'][$this->_fullname]['tab']=array();
		foreach($this->tab as $node){
			$_SESSION['osNodes'][$this->_fullname]['tab'][]=$node->_fullname;
		}
	}

	function __destruct() {
		$_SESSION['osNodes'][$this->_fullname]['tab']=array();
		foreach($this->tab as $node){
			$_SESSION['osNodes'][$this->_fullname]['tab'][]=$node->_fullname;
		}
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'tab_tabselected':
				$this->onTabSelected($info);
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


	function frm(){
		$html='';
		foreach($this->tab as $t){
			$html.=$t->_backframe();
		}
		return $html;
	}
	// reset the contents
	// $list=array(array("name"=>xxxx , "UID"=>xxxx),...)
	function booklist($list){
		$this->tab=array();
		$this->curTabUID=-1;
		$id=0;
		foreach($list as $t){
			$this->tab[]=new tab($this->_fullname.$id++);
			end($this->tab)->title=$t['name'];
			end($this->tab)->UID=$t['UID'];
		}
		$this->_bookframe("frm");
	}
	function onTabSelected($info){
		if($this->curTabUID==$info['UID']){
			return;
		}
		foreach($this->tab as $t){
			if($t->UID!=$info['UID']){
				$t->bookSelected(false);
			}
		}
		$this->curTabUID=$info['UID'];
	}

}

?>