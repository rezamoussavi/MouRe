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
	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/10/2011
	TestApproval: none

*/
require_once '../biz/fullepostviewer/fullepostviewer.php';
require_once '../biz/category/category.php';

class epostbank {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $curUID;

	//Nodes (bizvars)
	var $posts; // array of biz

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
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		//handle arrays
		$this->posts=array();
		if(!isset($_SESSION['osNodes'][$fullname]['posts']))
			$_SESSION['osNodes'][$fullname]['posts']=array();
		foreach($_SESSION['osNodes'][$fullname]['posts'] as $arrfn)
			$this->posts[]=new fullepostviewer($arrfn);

		if(!isset($_SESSION['osNodes'][$fullname]['curUID']))
			$_SESSION['osNodes'][$fullname]['curUID']=-1;
		$this->curUID=&$_SESSION['osNodes'][$fullname]['curUID'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']=epostbank;
	}

	function sleep(){
		$_SESSION['osNodes'][$this->_fullname]['slept']=true;
		$_SESSION['osNodes'][$this->_fullname]['posts']=array();
		foreach($this->posts as $node){
			$_SESSION['osNodes'][$this->_fullname]['posts'][]=$node->_fullname;
		}
	}

	function __destruct() {
		if($this->_tmpNode or !isset($_SESSION['osNodes'][$this->_fullname]['slept']))
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['slept']);
	}


	function message($message, $info) {
		switch($message){
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
		$posts='';
		foreach($this->posts as $p){
			$posts.=$p->_backframe();
		}
		$html=<<<PHTML
			$posts
PHTML;
		return $html;
	}
	function showBy($ownerName,$ownerUID){
		if($this->curUID==$ownerUID){
			return;
		}
		$this->curUID=$ownerUID;
		$this->reload();
		$this->_bookframe("frm");
	}
	function reload(){
		$cat=new category("temp");
		$content=$cat->backContentOf($this->curUID);
		$this->posts=array();
		$id=0;
		foreach($content as $c){
			if($c['bizname']=='epost'){
				$this->posts[]=new fullepostviewer($this->_fullname.$id++);
				end($this->posts)->bookUID($c['bizUID']);
			}
		}
	}

}

?>