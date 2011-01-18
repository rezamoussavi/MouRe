<?PHP

/*
	Compiled by bizLang compiler version 1.02

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

	//Variables
	var $selected;
	var $title;
	var $UID;

	//Nodes (bizvars)

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->_curFrame='notselectedfrm';
		$this->selected=false;
	}

	function __sleep(){
		return array('_fullname', '_curFrame','selected','title','UID');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			return;
		}
		switch($message){
			case 'clicktab':
				$this->onClicktab($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		switch($message){
			case 'clicktab':
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
		$html='<div id="' . $this->_fullname . '" style="display:inline;">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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
			osBroadcast("tabselected",array("UID"=>$this->UID));
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
				<input type="hidden" name="_message" value="clicktab" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
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