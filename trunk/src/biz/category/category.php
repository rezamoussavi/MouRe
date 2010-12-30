<?PHP

/*
	Compiled by bizLang compiler version 1.0

	Author: Reza Moussavi
	Date:	12/29/2010
	Version:	1.0

*/

class category {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $catUID;
	var $lable;
	var $type_name;

	//Nodes (bizvars)

	function __construct(&$data) {
		if (!isset($data['sleep'])) {
			$data['sleep'] = true;
			$this->_initialize($data);
			$this->_wakeup($data);
			$this->init(); //Customized Initializing
		}else{
			$this->_wakeup($data);
		}
	}

	function _initialize(&$data){
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->catUID=&$data['catUID'];
		$this->lable=&$data['lable'];
		$this->type_name=&$data['type_name'];

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

	function _setframe($frame){
		$this->_curFrame=$frame;
		$this->show(true);
	}

	function show($echo){
		$html='<div id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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
	function bookUID($UID){
		$this->catUID=$UID;
		$this->init();
	}
	function backContent(){
		$ret=array();
		if($this->catUID!=0){
			query("SELECT co.bizname AS bizname,co.bizUID AS bizUID, co.extra AS extra FROM category_cat AS c, category_content AS co WHERE co.catUID=c.catUID AND c.catUID=".$this->catUID);
			while($row=fetch()){
				$ret[]=array("bizname"=>$row['bizname'],"bizUID"=>$row['bizUID'],"extra"=>$row['extra']);
			}
		}
		return $ret;
	}

}

?>