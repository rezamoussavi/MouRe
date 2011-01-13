<?PHP

/*
	Compiled by bizLang compiler version 1.01

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/6/2011
	TestApproval: none

*/

class tab {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $selected;
	var $title;
	var $UID;

	//Nodes (bizvars)

	function __construct(&$data) {
		if (!isset($data['sleep'])) {
			$data['sleep'] = true;
			$this->_initialize($data);
			$this->_wakeup($data);
		}else{
			$this->_wakeup($data);
		}
	}

	function _initialize(&$data){
		if(! isset ($data['curFrame']))
			$data['curFrame']='notselectedfrm';
		if(! isset ($data['selected']))
			$data['selected']=false;
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->selected=&$data['selected'];
		$this->title=&$data['title'];
		$this->UID=&$data['UID'];

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
		$html='<div id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
		if($echo)
			echo $html;
		else
			return $html;
	}


//########################################
//         YOUR FUNCTIONS GOES HERE
//########################################


	function bookSelected($selected){
echo "[D03] - ";
		if($this->selected==$selected){
			return;
		}
echo "[D04] - ";
		$this->selected=$selected;
		if($selected){
echo "[D05] - ";
			osBroadcast("tabselected",array("UID"=>$this->UID));
			$this->_bookframe("selectedfrm");
		}else{
echo "[D06] - ";
			$this->_bookframe("notselectedfrm");
		}	
	}
	function onClicktab($info){
echo "[D01] - ";
		if(!$this->selected){
echo "[D02] - ";
			$this->bookSelected(true);
		}
	}
	function notselectedfrm(){
		$mark=$this->selected?"{T}":"{F}";
		$caption=$this->title.$mark.$this->UID;
		$html=<<<PHTML
			<form name="{$this->_fullname}" action="post">
				<input type="hidden" name="_message" value="clicktab" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input type="button" value="$caption" onclick='JavaScript:sndmsg("{$this->_fullname}")' class="press" />
			</form>
PHTML;
		return $html;
	}
	function selectedfrm(){
		$mark=$this->selected?"{T}":"{F}";
		$caption=$this->title.$mark.$this->UID;
		$html=<<<PHTML
			<input type="button" value="[[ $caption ]]" class="press" />
PHTML;
		return $html;
	}

}

?>