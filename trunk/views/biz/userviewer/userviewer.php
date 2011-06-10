<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	5/31/2011
	Ver:	1.0

*/
require_once 'biz/videolistviewer/videolistviewer.php';

class userviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $userData;
	var $showAdLinks;
	var $showPubLinks;

	//Nodes (bizvars)
	var $vlv;

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
			$_SESSION['osMsg']['frame_showAdLinks'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_showPubLinks'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->vlv=new videolistviewer($this->_fullname.'_vlv');

		if(!isset($_SESSION['osNodes'][$fullname]['userData']))
			$_SESSION['osNodes'][$fullname]['userData']='';
		$this->userData=&$_SESSION['osNodes'][$fullname]['userData'];

		if(!isset($_SESSION['osNodes'][$fullname]['showAdLinks']))
			$_SESSION['osNodes'][$fullname]['showAdLinks']=false;
		$this->showAdLinks=&$_SESSION['osNodes'][$fullname]['showAdLinks'];

		if(!isset($_SESSION['osNodes'][$fullname]['showPubLinks']))
			$_SESSION['osNodes'][$fullname]['showPubLinks']=false;
		$this->showPubLinks=&$_SESSION['osNodes'][$fullname]['showPubLinks'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='userviewer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_showAdLinks':
				$this->onShowAdLinks($info);
				break;
			case 'frame_showPubLinks':
				$this->onShowPubLinks($info);
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
				$_style='';
				break;
			case 'frmBar':
				$_style=' style="" ';
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


	/*****************************
	*	Messages
	*****************************/
	function onShowAdLinks($info){
		if($this->showAdLinks){
			$this->showAdLinks=false;
		}else{
			$this->vlv->bookModeUser("myad",$this->userData['userUID']);
			$this->showAdLinks=true;
			$this->showPubLinks=false;
		}
	}
	function onShowPubLinks($info){
		if($this->showPubLinks){
			$this->showPubLinks=false;
		}else{
			$this->vlv->bookModeUser("mypub",$this->userData['userUID']);
			$this->showPubLinks=true;
			$this->showAdLinks=false;
		}
	}
	/*****************************
	*	Frames
	*****************************/
	function frm(){
		return "[userviewer.biz]!";
	}
	function frmBar(){
		$frmAdLink=$this->_fullname."adLink";
		$frmPubLink=$this->_fullname."pubLink";
		$html=<<<PHTMLCODE

			<br /><div style="height:20px;float:left;overflow: hidden;width:100px;display:inline;">{$this->userData['email']}</div>
			<div style="height:20px;background-color:#eebbee;float:left;overflow: hidden;width:100px;display:inline;">{$this->userData['userName']}</div>
			<div style="height:20px;float:left;overflow: hidden;width:200px;display:inline;">{$this->userData['Address']}</div>
			<form name="$frmAdLink" style="float:left;overflow: hidden;width:50px;display:inline;"><input type="hidden" name="_message" value="frame_showAdLinks" /><input type = "hidden" name="_target" value="{$this->_fullname}" /><input type="button" value="adL." onclick='Javascript:sndmsg("$frmAdLink")' /></form>
			<form name="$frmPubLink" style="float:left;overflow: hidden;width:50px;display:inline;"><input type="hidden" name="_message" value="frame_showPubLinks" /><input type = "hidden" name="_target" value="{$this->_fullname}" /><input type="button" value="pubL." onclick='Javascript:sndmsg("$frmPubLink")' /></form>
			<div style="float:left;overflow: hidden;width:50px;display:inline;">{$this->userData['balance']}</div>
			<div style="float:left;overflow: hidden;width:50px;display:inline;">Intr</div><br />
		
PHTMLCODE;

		if(($this->showAdLinks) OR ($this->showPubLinks)){
			$html.="<br />".$this->vlv->_backframe();
		}
		return $html."<br />";
	}
	/*****************************
	*	Functionalities
	*****************************/
	function bookModeUser($mode,$userData){
		$this->userData=$userData;
		switch($mode){
			case "bar":
				$this->_bookframe("frmBar");
				break;
			default:
				$this->_bookframe("frm");
				break;
		}
	}

}

?>