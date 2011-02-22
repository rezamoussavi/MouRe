<?PHP

/*
	Compiled by bizLang compiler version 1.5 (Feb 21 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}
	1.4: {multi parameter in link message}
	1.5: {multi secName support: frm/frame, msg/messages,fun/function/phpfunction}

	Author: Reza Moussavi
	Date:	02/16/2011
	Version: 1
	---------------------
	Author: Reza Moussavi
	Date:	02/07/2011
	Version: 0.1

*/
require_once '../biz/purchaseviewer/purchaseviewer.php';

class history {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables

	//Nodes (bizvars)
	var $purchases;

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

		$this->purchases=new purchaseviewer($this->_fullname.'_purchases');

		$this->init(); //Customized Initializing
		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='history';
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
		$p=new purchase();
		$pUIDs=$p->backAllUID();
		$this->purchases=array();
		$i=1;
		foreach($pUIDs as $pUID){
			$this->purchases[]=new purchaseviewer($this->_fullname.$i++);
			end($this->purchases)->bookUID($pUID);
		}
	}
	function frm(){
		$html='';
		foreach($this->purchases as $p){
			$pframe=$p->_backFrame();
			$html.=<<<PHTMLCODE

				$pframe
			
PHTMLCODE;

		}
		return $html;
	}

}

?>