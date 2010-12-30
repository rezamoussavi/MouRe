<?PHP

/*
	Compiled by bizLang compiler version 1.0

	Author: Reza Moussavi
	Date:	12/30/2010
	Version: 1.0

*/

class dummie {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $userEmail;

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
			$data['curFrame']=sad;
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->userEmail=&$data['userEmail'];

	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			return;
		}
		switch($message){
			case 'login':
				$this->onLogin($info);
				break;
			case 'logout':
				$this->onLogout($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		switch($message){
			case 'login':
				$this->onLogin($info);
				break;
			case 'logout':
				$this->onLogout($info);
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


	function onLogin($info){
		$this->userEmail=$info['email'];
		$this->_bookframe("happy");
	}
	function onLogout($info){
		$this->_bookframe("sad");
	}
	function happy(){
		return "<h2>:) </h2>". $this->userEmail;
	}
	function sad(){
		return '<h2>:( </h2>';
	}

}

?>