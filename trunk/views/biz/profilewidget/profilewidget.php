<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	6/03/2011
	Ver:	0.1

*/

class profilewidget {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables

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
			$_SESSION['osMsg']['user_login'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

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
			case 'user_login':
				$this->onLoginChange($info);
				break;
			case 'user_logout':
				$this->onLoginChange($info);
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
				$_style=' style="float:left; width:200;" ';
				break;
		}
		$html='<script type="text/javascript" language="Javascript">';
		$html.=<<<JAVASCRIPT

JAVASCRIPT;
		$html.=<<<JSONDOCREADY
function {$this->_fullname}(){}
JSONDOCREADY;
		$html.='</script>
<div '.$_style.' id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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
	function onLoginChange($info){
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
		$myAccFormName = $this->_fullname."myacc";
		if(osUserLogedin()){
		$html=<<<PHTMLCODE

			<form name="$myAccFormName" method="post" style="display:inline;">
				<input type="hidden" name="_message" value="frame_showMyAccount" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="My Page" type = "button" onclick = 'JavaScript:sndmsg("$myAccFormName")'  class="press" style="margin-top: 0px;">
			</form>
		
PHTMLCODE;

		}else{
			$html="";
		}
		return $html;
	}

}

?>