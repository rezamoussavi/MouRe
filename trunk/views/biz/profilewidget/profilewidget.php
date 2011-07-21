<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	7/30/2011
	Ver:	1.0
	---------------------
	Author:	Reza Moussavi
	Date:	6/03/2011
	Ver:	0.1

*/
require_once 'biz/user/user.php';
require_once 'biz/adlink/adlink.php';
require_once 'biz/publink/publink.php';

class profilewidget {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $balance;

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
			$_SESSION['osMsg']['frame_showMyAccount'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_login'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		if(!isset($_SESSION['osNodes'][$fullname]['balance']))
			$_SESSION['osNodes'][$fullname]['balance']=0;
		$this->balance=&$_SESSION['osNodes'][$fullname]['balance'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='profilewidget';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_showMyAccount':
				$this->onShowMyAccount($info);
				break;
			case 'user_logout':
				$this->onLogout($info);
				break;
			case 'user_login':
				$this->onLogin($info);
				break;
			default:
				break;
		}
	}

	function _bookframe($frame){
		$this->_curFrame=$frame;
		//$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$_style='';
		switch($this->_curFrame){
			case 'frm':
				$_style='  style="float:left; width:200;" ';
				break;
		}
		$html.='<div '.$_style.' id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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


	/******************************
	*	Message Handlers
	******************************/
	function onLogin($info){
		$u=new user("");
		if($ud=$u->backUserData(osBackUserID())){
			$this->balance=$ud['balance'];
		}
	}
	function onLogout($info){
		/*
		*	Since the biz is interested in a message
		*	It needs a callback function
		*	But by this specific message we need to refresh the biz frame
		*	by default each biz which is alive will be refreshed
		*	So there is no need to put any code on this function
		*/
	}
	
	function onShowMyAccount($info){
		$data=array();
		osBroadcast("user_showMyAccount",$data);
	}
	/******************************
	*	Frames
	******************************/
	function frm(){
		if(!osUserLogedin()){
			return "";
		}
		$myAccFormName = $this->_fullname."myacc";
		$html=<<<PHTMLCODE

			Balance: {$this->balance} $
			<form name="$myAccFormName" method="post">
				<input type="hidden" name="_message" value="frame_showMyAccount" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="My Page" type = "button" onclick = 'JavaScript:sndmsg("$myAccFormName")'  class="press" style="margin-top: 0px;" />
			</form>
		
PHTMLCODE;

		return $html;
	}

}

?>