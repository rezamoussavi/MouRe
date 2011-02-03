<?PHP

/*
	Compiled by bizLang compiler version 1.3.5 (Feb 3 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}

	Author:		Reza Moussavi
	Version:	1.1
	Date:		1/26/2011
	TestApproval: none
	-------------------
	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/6/2011
	TestApproval: none
	-------------------
	NOTE: untill we dont support css in biz
		please add replace following line in compiled php in show function
		$html='<div id="' . $this->_fullname . '" style="display:inline;">'.call_user_func(array($this, $this->_curFrame)).'</div>';

*/

class tab {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $selected;
	var $title;
	var $UID;

	//Nodes (bizvars)

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
			$_SESSION['osMsg']['frame_clicktab'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='notselectedfrm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		if(!isset($_SESSION['osNodes'][$fullname]['selected']))
			$_SESSION['osNodes'][$fullname]['selected']=false;
		$this->selected=&$_SESSION['osNodes'][$fullname]['selected'];

		if(!isset($_SESSION['osNodes'][$fullname]['title']))
			$_SESSION['osNodes'][$fullname]['title']='';
		$this->title=&$_SESSION['osNodes'][$fullname]['title'];

		if(!isset($_SESSION['osNodes'][$fullname]['UID']))
			$_SESSION['osNodes'][$fullname]['UID']='';
		$this->UID=&$_SESSION['osNodes'][$fullname]['UID'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='tab';
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
			case 'frame_clicktab':
				$this->onClicktab($info);
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


	function bookSelected($selected){
		if($this->selected==$selected){
			return;
		}
		$this->selected=$selected;
		if($selected){
			osBroadcast("tab_tabselected",array("UID"=>$this->UID));
			$this->_bookframe("selectedfrm");
		}else{
			$this->_bookframe("notselectedfrm");
		}	
	}
	function onClicktab($info){
		if(!$this->selected){
			$this->bookSelected(true);
		}
	}
	function notselectedfrm(){
		$html=<<<PHTML
			<form name="{$this->_fullname}" action="post" style="display:inline;">
				<input type="hidden" name="_message" value="frame_clicktab" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input type="button" value="{$this->title}" onclick='JavaScript:sndmsg("{$this->_fullname}")' class="press" />
			</form>
PHTML;
		return $html;
	}
	function selectedfrm(){
		$html=<<<PHTML
			<input type="button" value="[{$this->title}]" class="press" />
PHTML;
		return $html;
	}

}

?>