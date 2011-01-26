<?PHP

/*
	Compiled by bizLang compiler version 1.1

	{Family included}

	Author:		Reza Moussavi
	Version:	1.1
	Date:		1/26/2011
	TestApproval: none
	-------------------------
	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/7/2011
	TestApproval: none

*/
require_once '../biz/tab/tab.php';

class tabbank {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;

	//Variables
	var $curTabUID;

	//Nodes (bizvars)
	var $tab_array_data; 	var $tab;

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->_curFrame='frm';
		$this->tab=array();
		$this->curTabUID=-1;
	}

	function __sleep(){
		return array('_fullname', '_curFrame','curTabUID','tab');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			foreach($this->tab as $i=>&$_element)
				$_element->message($to, $message, $info);
			return;
		}
		switch($message){
			case 'tab_tabselected':
				$this->onTabSelected($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		foreach($this->tab as $i=>&$_element)
			$_element->broadcast($message, $info);
		switch($message){
			case 'tab_tabselected':
				$this->onTabSelected($info);
				break;
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


	function frm(){
		$html='';
		foreach($this->tab as $t){
			$html.=$t->_backframe();
		}
		return $html;
	}
	// reset the contents
	// $list=array(array("name"=>xxxx , "UID"=>xxxx),...)
	function booklist($list){
		$this->tab=array();
		$this->curTabUID=-1;
		$first=true;
		$id=0;
		foreach($list as $t){
			$this->tab[]=new tab($this->_fullname.$id++);
			end($this->tab)->title=$t['name'];
			end($this->tab)->UID=$t['UID'];
			if($first){
				$this->tab[0]->bookSelected(true);
				$first=false;
			}
		}
		$this->_bookframe("frm");
	}
	function onTabSelected($info){
		if($this->curTabUID==$info['UID']){
			return;
		}
		if($this->curTabUID!=-1){
			foreach($this->tab as $t){
				if($t->UID==$this->curTabUID){
					$t->bookSelected(false);
					break;
				}
			}
		}
		$this->curTabUID=$info['UID'];
	}

}

?>