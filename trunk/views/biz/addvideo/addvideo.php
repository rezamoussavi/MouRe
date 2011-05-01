<?PHP

/*
	Compiled by bizLang compiler version 3.2 [DB added] (March 26 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	4/27/2011
	Ver:		1.0
	------------------------------------
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
	var $_frmChanged;

	//Variables
	var $errMessage;

	//Nodes (bizvars)
	var $offer;

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
			$_SESSION['osMsg']['frame_addVideo'][$this->_fullname]=true;
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
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
			case 'frm':
				$_style='';
				break;
			case 'frmSuccess':
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


	function frm(){
		if(!osUserLogedin()){
			return <<<PHTMLCODE

				<div style="text-align:center; width=100%;">Log in first</div>
			
PHTMLCODE;

		}
		//else
		$formname=$this->_fullname;
		$minAOPV=$this->offer->minAOPV;
		$minNOV=$this->offer->minNOV;
		return <<<PHTMLCODE

			<center><font color=red>{$this->errMessage}</font><hr></center>
			<form name="$formname" method="post">
				<input type="hidden" name="_message" value="frame_addVideo" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				Youtube link: <input name="link" size=50><br>
				Your Offer on Price/View: <input name="AOPV" size=5> (min: $minAOPV)<br>
				Number of Viewes: <input name="NOV" size=5> (min: $minNOV)<br>
				Commision: (auto calculate)
				<hr>
				Total: (auto calculate) <input type="button" value="Pay"><br>
				<input type="button" value="Apply" onclick = 'JavaScript:sndmsg("$formname")'>
			</form>
		
PHTMLCODE;

	}
	function frmSuccess(){
		$msg=$this->errMessage;
		$this->errMessage="";
		$this->_bookframe("frm");
		return <<<PHTMLCODE

			<center><font color=green>{$msg}</font><hr></center>
		
PHTMLCODE;

	}
	function onAddVideo($info){
		$e="";
		if(strlen($info['link'])<5){
			$e.="Enter a valid link<br>";
		}
		if($info['AOPV']<$this->offer->minAOPV){
			$e.="Minimum Offer should be ".$this->offer->minAOPV."<br>";
		}
		if($info['NOV']<$this->offer->minNOV){
			$e.="Minimum Number of Views should be ".$this->offer->minNOV."<br>";
		}
		if(strlen($e)<2){// NO ERROR
			$al=new adlink("");
			$data=array();
			$user=osBackUser();
			$data['advertisor']=$user['UID'];
			$data['running']=1;
			$data['lastDate']="";
			$data['startDate']="";
			$data['link']=$info['link'];
			$data['maxViews']=$info['NOV'];
			$data['AOPV']=$info['AOPV'];
			$data['paid']=0;
			$data['APRate']=$this->offer->APRatio;
			$data['minLifeTime']=$this->offer->minLifeTime;
			$data['minCancelTime']=$this->offer->minCancelTime;
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