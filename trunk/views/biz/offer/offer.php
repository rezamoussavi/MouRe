<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

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

	//Variables
	var $data;

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

		if(!isset($_SESSION['osNodes'][$fullname]['data']))
			$_SESSION['osNodes'][$fullname]['data']='';
		$this->data=&$_SESSION['osNodes'][$fullname]['data'];

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


	function bookInfo($info){
		query("UPDATE offer_info SET available=0 WHERE available=1");
		query("INSERT INTO offer_info(available,minAOPV,APRatio,minLifeTime,minCancelTime,minNOV) VALUES(1,$info[minAOPV],$info[APRatio],$info[minLifeTime],$info[minCancelTime],$info[minNOV])");
		$this->data=$info;
	}
	function backInfo(){
		query("SELECT * FROM offer_info WHERE available=1");
		if($this->data=fetch())
			return $this->data;
		return array("minNOV"=>"","minAOPV"=>"APRatio",""=>"","minLifeTime"=>"","minCancelTime"=>"","APRatio"=>"");
	}
	function init(){
		$this->data=array();
		query("SELECT * FROM offer_info WHERE available=1;");
		$row=fetch();
		if($row){
			$this->data=$row;
		}
	}

}

?>