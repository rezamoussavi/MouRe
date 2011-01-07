<?PHP

/*
	Compiled by bizLang compiler version 1.01

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/7/2011
	TestApproval: none

*/
require_once '../biz/tab/tab.php';

class tabbank {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $curTabUID;

	//Nodes (bizvars)
	var $tab_array_data; 	var $tab;

	function __construct(&$data) {
		if (!isset($data['sleep'])) {
			$data['sleep'] = true;
			$this->_initialize($data);
			$this->_wakeup($data);
		}else{
			$this->_wakeup($data);
		}
	}

	function _initialize(&$data){
		if(! isset ($data['tab_array_data']))
			$data['tab_array_data']=array();
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->curTabUID=&$data['curTabUID'];


		$this->tab=array();
		$this->tab_array_data=&$data['tab_array_data'];
		foreach($data['tab_array_data'] as $na=>&$da){
			if(! isset($da['bizname'])){
				$da['bizname']=$na;
				$da['fullname']=$this->_fullname."_".$na;
				$da['parent']=$this;
			}
			$this->tab[]=new tab($da);
		}
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			foreach($this->tab as $i=>&$_element)
				$_element->message($to, $message, $info);
			return;
		}
		switch($message){
			case 'tabselected':
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
			case 'tabselected':
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
		if($echo)
			echo $html;
		else
			return $html;
	}


//########################################
//         YOUR FUNCTIONS GOES HERE
//########################################


	// reset the contents
	// $list=array(array("name"=>xxxx , "UID"=>xxxx),...)
	function booklist($list){
		
		// Empty the array
		$this->tab_array_data=array();
		$this->tab=array();
		$this->curTabUID=-1;
		$first=true;
		foreach($list as $t){
			
			// Add new Node to the array
			$_index=count($this->tab_array_data);
			$_data=array();
			$_data['parent']=$this;
			$_data['bizname']=$_index;
			$_data['fullname']=$this->_fullname.'_'.$_index;
			$this->tab_array_data[]=$_data;
			$this->tab[]=new  tab($this->tab_array_data[$_index]);
			end($this->tab)->title=$t['name'];
			end($this->tab)->UID=$t['UID'];
			if($first){
				$this->tab[0]->bookSelected(true);
				$first=false;
			}
		}
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