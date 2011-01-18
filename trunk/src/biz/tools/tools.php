<?PHP

/*
	Compiled by bizLang compiler version 1.02

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/ /2011
	TestApproval: none

*/

class tools {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;

	//Variables

	//Nodes (bizvars)

	function __construct($fullname) {
		$this->_fullname=$fullname;
	}

	function __sleep(){
		return array('_fullname', '_curFrame');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			return;
		}
		switch($message){
			default:
				break;
		}
	}

	function broadcast($message, $info) {
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
		if($echo)
			echo $html;
		else
			return $html;
	}

}

?>