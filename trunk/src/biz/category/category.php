<?PHP

/*
	Compiled by bizLang compiler version 1.1

	{Family included}

	Author:		Reza Moussavi
	Version:	1.1
	Date:		1/26/2011
	TestApproval: none
	-------------------
	Author: Reza Moussavi
	Date:	12/29/2010
	Version:	1.0

*/

class category {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;

	//Variables
	var $catUID;
	var $lable;
	var $type_name;

	//Nodes (bizvars)

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->init(); //Customized Initializing
	}

	function __sleep(){
		return array('_fullname', '_curFrame','catUID','lable','type_name');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			return;
		}
		switch($message){
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		switch($message){
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


	function init(){
		if($this->catUID==0){//----ROOT it is
			query("SELECT c.catUID as catUID, c.Lable AS lable, t.name AS type_name FROM category_cat AS c,category_type AS t WHERE c.typeUID=t.typeUID AND c.owner_type ='bizness' AND c.owner_UID='".osBackBizness()."'");
			if($row=fetch()){
				$this->catUID=$row['catUID'];
				$this->lable=$row['lable'];
				$this->type_name=$row['type_name'];
			}
		}else{//---Specific category
			query("SELECT c.Lable AS lable, t.Name AS type_name FROM category_cat AS c,category_type AS t WHERE c.typeUID=t.typeUID AND c.catUID=".$this->catUID);
			if($row=fetch()){
				$this->lable=$row['lable'];
				$this->type_name=$row['type_name'];
			}
		}
	}
	// A Global-Special function
	function backLable($UID){
		if($UID==0)
			return $this->lable;
		$ret='';
		query("SELECT c.Lable AS lable, t.Name AS type_name FROM category_cat AS c,category_type AS t WHERE c.typeUID=t.typeUID AND c.catUID=".$UID);
		if($row=fetch())
			$ret=$row['lable'];
		return $ret;
	}
	function bookUID($UID){
		$this->catUID=$UID;
		$this->init();
	}
	function backContentOf($UID){
		$this->bookUID($UID);
		return $this->backContent();
	}
	function backContent(){
		$ret=array();
		if($this->catUID!=0){
			query("SELECT co.bizname AS bizname,co.bizUID AS bizUID, co.extra AS extra FROM category_content AS co WHERE co.catUID=".$this->catUID);
			while($row=fetch()){
				$ret[]=array("bizname"=>$row['bizname'],"bizUID"=>$row['bizUID'],"extra"=>$row['extra']);
			}
		}
		return $ret;
	}
	function addContent($data){
		//$data=(catUID,bizname,bizUID,extra)
		query('insert into category_content values('.$data['catUID'].',"'.$data['bizname'].'",'.$data['bizUID'].',"'.$data['extra'].'")');
	}

}

?>