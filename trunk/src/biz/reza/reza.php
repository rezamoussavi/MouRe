<?PHP


class reza {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;

	//Variables
	var $count;

	//Nodes (bizvars)

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->_curFrame='def_frm';
		$this->count=0;
	}

	function __sleep(){
		return array('_fullname', '_curFrame','count');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			return;
		}
		switch($message){
			case 'eBoardSelected':
				$this->onEBS($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		switch($message){
			case 'eBoardSelected':
				$this->onEBS($info);
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


	function onEBS($info){
		$this->count++;
		if($this->count>5)
			$this->_bookframe("high_frm");
		else
			$this->_bookframe("def_frm");
	}
	function def_frm(){
		return "count=".$this->count;
	}
	function high_frm(){
		return "WOW:".$this->count;
	}

}

?>