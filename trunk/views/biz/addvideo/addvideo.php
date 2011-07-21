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
require_once 'biz/transaction/transaction.php';
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
				$_style=' ';
				break;
			case 'frmSuccess':
				$_style=' ';
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
		$t=new transaction("");
		$balance=$t->backBalance(osBackUserID());
		$countries="<option value='any'>any</option>";
		query("SELECT DISTINCT CountryName FROM ip_country ORDER BY CountryName");
		$row=fetch();
		while($row=fetch()){
			$countries.="<option value='".$row['CountryName']."'>".$row['CountryName']."</option>";
		}
		$html=<<<PHTMLCODE

			<center><font color=red>{$this->errMessage}</font><hr></center>
			<form name="$formname" method="post">
				<input type="hidden" name="_message" value="frame_addVideo" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				Title: <input id="theTitle" name="title" size=50 onkeypress='setTimeout("checkTitle()",100)' onchange='JavaScript:checkTitle();'><span id="msgTitle"><font color="red">*</font></span><br>
				Youtube link: <input id="theYLink" name="link" size=50 onkeypress='setTimeout("checkYLink()",100)' onchange='JavaScript:checkYLink()'><span id="msgYLink"><font color="red">*</font></span><br>
				Your Offer on Price/View: <input id="theAOPV" name="AOPV" size=5 onkeypress='setTimeout("checkAOPV({$of['minAOPV']})",100)' onchange='JavaScript:checkAOPV({$of['minAOPV']})'> (min: {$of['minAOPV']})<span id="msgAOPV"><font color="red">*</font></span><br />
				Number of Viewes: <input id="theNOV" name="NOV" size=5 onkeypress='setTimeout("checkNOV({$of['minNOV']})",100)' onchange='JavaScript:checkNOV({$of['minNOV']})'> (min: {$of['minNOV']})<span id="msgNOV"><font color="red">*</font></span><br />
				Country: <SELECT name="country" style="width:150px;">$countries</SELECT>
				<hr>
				Total: <span id="theTotal">0</span>$ - balance:<span id="theBalance">$balance</span> $ <span id="msgTotal"></span><br />
				<input id="theButton" disabled type="button" value="Submit" onclick = 'JavaScript:sndmsg("$formname")'>
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
			$data['startDate']=date("Y/m/d");
			$data['link']=$info['link'];
			$data['title']=$info['title'];
			$data['maxViews']=$info['NOV'];
			$data['AOPV']=$info['AOPV'];
			$data['paid']=0;
			$data['APRate']=$of['APRatio'];
			$data['minLifeTime']=$of['minLifeTime'];
			$data['minCancelTime']=$of['minCancelTime'];
			$data['country']=$info['country'];
			$al->bookLink($data);
			$emb=$al->backYEmbed($data['link']);
			$e="Added Successfully<br>$emb";
			$t=new transaction("");
			$t->bookAdPay($info['AOPV']*$info['NOV'],"Ad video - title: ".$info['title']);
			$this->_bookframe("frmSuccess");
		}else{// HAS ERROR
			$e="ERROR: <br>".$e;
			$this->_bookframe("frm");
		}
		$this->errMessage=$e;
	}

}

?>