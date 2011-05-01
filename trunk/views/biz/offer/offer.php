<?PHP

/*
	Compiled by bizLang compiler version 3.2 [DB added] (March 26 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	4/28/2011
	Ver:		1.0
	-----------------------------------
	Author:	Reza Moussavi
	Date:	4/27/2011
	Ver:		0.2
	-----------------------------------
	Author:	Reza Moussavi
	Date:	4/21/2011
	Ver:		0.1

*/

class offer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables
	var $offerUID;
	var $available;
	var $minAOPV;
	var $APRatio;
	var $minLifeTime;
	var $minCancelTime;
	var $minNOV;

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
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		if(!isset($_SESSION['osNodes'][$fullname]['offerUID']))
			$_SESSION['osNodes'][$fullname]['offerUID']=-1;
		$this->offerUID=&$_SESSION['osNodes'][$fullname]['offerUID'];

		if(!isset($_SESSION['osNodes'][$fullname]['available']))
			$_SESSION['osNodes'][$fullname]['available']=false;
		$this->available=&$_SESSION['osNodes'][$fullname]['available'];

		if(!isset($_SESSION['osNodes'][$fullname]['minAOPV']))
			$_SESSION['osNodes'][$fullname]['minAOPV']=0.0;
		$this->minAOPV=&$_SESSION['osNodes'][$fullname]['minAOPV'];

		if(!isset($_SESSION['osNodes'][$fullname]['APRatio']))
			$_SESSION['osNodes'][$fullname]['APRatio']=0;
		$this->APRatio=&$_SESSION['osNodes'][$fullname]['APRatio'];

		if(!isset($_SESSION['osNodes'][$fullname]['minLifeTime']))
			$_SESSION['osNodes'][$fullname]['minLifeTime']=0;
		$this->minLifeTime=&$_SESSION['osNodes'][$fullname]['minLifeTime'];

		if(!isset($_SESSION['osNodes'][$fullname]['minCancelTime']))
			$_SESSION['osNodes'][$fullname]['minCancelTime']=0;
		$this->minCancelTime=&$_SESSION['osNodes'][$fullname]['minCancelTime'];

		if(!isset($_SESSION['osNodes'][$fullname]['minNOV']))
			$_SESSION['osNodes'][$fullname]['minNOV']=0;
		$this->minNOV=&$_SESSION['osNodes'][$fullname]['minNOV'];

		if(!isset($_SESSION['osNodes'][$fullname]['biz']))
			$this->init(); //Customized Initializing
		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='offer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
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
	}


//########################################
//         YOUR FUNCTIONS GOES HERE
//########################################


	function init(){
		query("SELECT * FROM offer_info WHERE available=1;");
		$row=fetch();
		if($row){
			$this->offerUID=$row['offerUID'];
			$this->available=$row['available'];
			$this->minAOPV=$row['minAOPV'];
			$this->APRatio=$row['APRatio'];
			$this->minLifeTime=$row['minLifeTime'];
			$this->minCancelTime=$row['minCancelTime'];
			$this->minNOV=$row['minNOV'];
		}
	}
	function bookInfo($info){
		if(isset($info['offerUID']))
			$this->offerUID=$info['offerUID'];
		if(isset($info['available']))
			$this->available=$info['available'];
		if(isset($info['minAOPV']))
			$this->minAOPV=$info['minAOPV'];
		if(isset($info['APRatio']))
			$this->APRatio=$info['APRatio'];
		if(isset($info['minLifeTime']))
			$this->minLifeTime=$info['minLifeTime'];
		if(isset($info['minCancelTime']))
			$this->minCancelTime=$info['minCancelTime'];
		if(isset($info['minNOV']))
			$this->minNOV=$info['minNOV'];
	}
	function backInfo(){
		return array('offerUID'=>$this->offerUID,'available'=>$this->available,'minAOPV'=>$this->minAOPV,'APRatio'=>$this->APRatio,'minLifeTime'=>$this->minLifeTime,	'minCancelTime'=>$this->minCancelTime,	'minNOV'=>$this->minNOV);
	}

}

?>