<?PHP


class pq {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables
	var $test;

	//Nodes (bizvars)

	function __construct($fullname) {
		$this->_frmChanged=false;
		$this->_tmpNode=false;
		if($fullname==null){
			$fullname='_tmpNode_'.count($_SESSION['osNodes']);
			$this->_tmpNode=true;
		}
		$this->_fullname=$fullname;
		if(!isset($_SESSION['osNodes'][$fullname])){
			$_SESSION['osNodes'][$fullname]=array();
			//If any message need to be registered will placed here
			$_SESSION['osMsg']['user_login'][$this->_fullname]=true;
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='happy';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		if(!isset($_SESSION['osNodes'][$fullname]['test']))
			$_SESSION['osNodes'][$fullname]['test']="nothing";
		$this->test=&$_SESSION['osNodes'][$fullname]['test'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='pq';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'user_login':
				$this->onL($info);
				break;
			default:
				break;
		}
	}

	function _bookframe($frame){
		$this->_frmChanged=true;
		$this->_curFrame=$frame;
		//$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$_style='';
		switch($this->_curFrame){
			case 'happy':
				$_style='';
				break;
			case 'sad':
				$_style='';
				break;
		}
		$html='<div '.$_style.' id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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


	function onL($info){
		$this->test=" - LogedIn";
	}
	
	function happy(){
		$this->_bookframe("sad");
		return "I am happy!".$this->test;
	}
	function sad(){
		$this->_bookframe("happy");
		return "I am NOT happy!".$this->test;
	}

}

?>