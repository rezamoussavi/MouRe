<?PHP

/*
	Compiled by bizLang compiler version 1.01

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/6/2011

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
			$data['curFrame']=notselectedfrm;
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
			case 'click':
				$this->onClick($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		switch($message){
			case 'click':
				$this->onClick($info);
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
		$var.selected=$selected;
		osBroadcast("tabselected",array("UID"=>"UID"));
	}
	function onClick($info){
		if(!selected){
			$this->bookSelected(true);
			_setframe("selectedframe");
		}
	}
	function notselectedfrm(){
		$html=<<<HTML
			[ { $this->title } ]
HTML;
		return $html;
	}
	function selectedfrm(){
		$html=<<<HTML
			<b> [[ {$this->title} ]] </b>
HTML;
		return $html;
	}

}

?>