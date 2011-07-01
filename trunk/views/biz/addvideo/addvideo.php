<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	4/27/2011
	Ver:		1.0
	-----------------------
	Author:	Reza Moussavi
	Date:	4/21/2011
	Ver:		0.1

*/
require_once 'biz/adlink/adlink.php';
require_once 'biz/offer/offer.php';

class addvideo {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $errMessage;

	//Nodes (bizvars)
	var $offer;

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
			$_SESSION['osMsg']['frame_addVideo'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_login'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->offer=new offer($this->_fullname.'_offer');

		if(!isset($_SESSION['osNodes'][$fullname]['errMessage']))
			$_SESSION['osNodes'][$fullname]['errMessage']="";
		$this->errMessage=&$_SESSION['osNodes'][$fullname]['errMessage'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='addvideo';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_addVideo':
				$this->onAddVideo($info);
				break;
			case 'user_login':
				$this->onLoginStatusChange($info);
				break;
			case 'user_logout':
				$this->onLoginStatusChange($info);
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
			case 'frmSuccess':
				$_style='';
				break;
		}
		$html='<script type="text/javascript" language="Javascript">';
		$html.=<<<JAVASCRIPT
	var okTitle=false;
	function checkTitle(){
		if(document.getElementById('theTitle').value.length<1){
			document.getElementById('msgTitle').innerHTML = "<font color=red>Invalid Name</font>";
			okTitle=false;
		}else{
			document.getElementById('msgTitle').innerHTML = "<font color=green>OK</font>";
			okTitle=false;
		}
		checkAll();
	}
	function checkAll(){
		if(okTitle)
			document.getElementById('theButton').disabled=0;
		else
			document.getElementById('theButton').disabled=0;
	}

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


	/************************************************
	*		FRAMES
	************************************************/
	function frm(){
		if(!osUserLogedin()){
			return <<<PHTMLCODE

				<div style="text-align:center; width=100%;">Log in first</div>
			
PHTMLCODE;

		}
		//else
		$formname=$this->_fullname;
		$of=$this->offer->backInfo();
		$html=<<<PHTMLCODE

			<center><font color=red>{$this->errMessage}</font><hr></center>
			<form name="$formname" method="post">
				<input type="hidden" name="_message" value="frame_addVideo" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				Title: <input id="theTitle" name="title" size=50 onkeypress='JavaScript:checkTitle()' onchange="JavaScript:checkTitle();"><span id="msgTitle"></span><br>
				Youtube link: <input name="link" size=50><br>
				Your Offer on Price/View: <input name="AOPV" size=5> (min: {$of['minAOPV']})<br>
				Number of Viewes: <input name="NOV" size=5> (min: {$of['minNOV']})<br>
				Commision: (auto calculate)
				<hr>
				Total: (auto calculate) <input type="button" value="Pay"><br>
				<input id="theButton" disabled type="button" value="Apply" onclick = 'JavaScript:sndmsg("$formname")'>
			</form>
		
PHTMLCODE;

		$this->errMessage="";
		return $html;
	}
	function frmSuccess(){
		$msg=$this->errMessage;
		$this->errMessage="";
		$this->_bookframe("frm");
		return <<<PHTMLCODE

			<center><font color=green>{$msg}</font><hr></center>
		
PHTMLCODE;

	}
	/*************************************************
	*		MESSAGES
	*************************************************/
	function onLoginStatusChange($info){
		//REFRESH AUTOMATICALLY
	}
	function onAddVideo($info){
		$of=$this->offer->backInfo();
		$e="";
		if(strlen($info['link'])<5){
			$e.="Enter a valid link<br>";
		}
		if($info['AOPV']<$of['minAOPV']){
			$e.="Minimum Offer should be ".$of['minAOPV']."<br>";
		}
		if($info['NOV']<$of['minNOV']){
			$e.="Minimum Number of Views should be ".$of['minNOV']."<br>";
		}
		if(strlen($e)<2){// NO ERROR
			$al=new adlink("");
			$data=array();
			$data['advertisor']=osBackUserID();
			$data['running']=1;
			$data['lastDate']="";
			$data['startDate']="";
			$data['link']=$info['link'];
			$data['title']=$info['title'];
			$data['maxViews']=$info['NOV'];
			$data['AOPV']=$info['AOPV'];
			$data['paid']=0;
			$data['APRate']=$of['APRatio'];
			$data['minLifeTime']=$of['minLifeTime'];
			$data['minCancelTime']=$of['minCancelTime'];
			$al->bookLink($data);
			$emb=$al->backYEmbed($data['link']);
			$e="Added Successfully<br>$emb";
			$this->_bookframe("frmSuccess");
		}else{// HAS ERROR
			$e="ERROR: <br>".$e;
			$this->_bookframe("frm");
		}
		$this->errMessage=$e;
	}

}

?>