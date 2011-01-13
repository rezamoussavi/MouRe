<?PHP

/*
	Compiled by bizLang compiler version 1.01

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/10/2011
	TestApproval: none

*/
require_once '../biz/fullepostviewer/fullepostviewer.php';
require_once '../biz/category/category.php';

class epostbank {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $curUID;

	//Nodes (bizvars)
	var $posts_array_data; 	var $posts;

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
		if(! isset ($data['curFrame']))
			$data['curFrame']='frm';
		if(! isset ($data['curUID']))
			$data['curUID']=-1;
		if(! isset ($data['posts_array_data']))
			$data['posts_array_data']=array();
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->curUID=&$data['curUID'];


		$this->posts=array();
		$this->posts_array_data=&$data['posts_array_data'];
		foreach($data['posts_array_data'] as $na=>&$da){
			if(! isset($da['bizname'])){
				$da['bizname']=$na;
				$da['fullname']=$this->_fullname."_".$na;
				$da['parent']=$this;
			}
			$this->posts[]=new fullepostviewer($da);
		}
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			foreach($this->posts as $i=>&$_element)
				$_element->message($to, $message, $info);
			return;
		}
		switch($message){
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		foreach($this->posts as $i=>&$_element)
			$_element->broadcast($message, $info);
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
		if($echo)
			echo $html;
		else
			return $html;
	}


//########################################
//         YOUR FUNCTIONS GOES HERE
//########################################


	function frm(){
		$posts='';
		foreach($this->posts as $p){
			$posts.=$p->_backframe()."<br>";
		}
		$html=<<<PHTML
			$posts
PHTML;
		return $html;
	}
	function showBy($ownerName,$ownerUID){
		if($this->curUID==$ownerUID){
			return;
		}
		$this->curUID=$ownerUID;
		$this->reload();
		_setframe("frm");
	}
	function reload(){
		$cat=new category(array());
		$content=$cat->backContentOf($this->curUID);
		
		// Empty the array
		$this->posts_array_data=array();
		$this->posts=array();
		foreach($content as $c){
			if($c['bizname']=='epost'){
				
				// Add new Node to the array
				$_index=count($this->posts_array_data);
				$_data=array();
				$_data['parent']=$this;
				$_data['bizname']=$_index;
				$_data['fullname']=$this->_fullname.'_'.$_index;
				$this->posts_array_data[]=$_data;
				$this->posts[]=new  fullepostviewer($this->posts_array_data[$_index]);
				end($this->posts[])->bookUID($c['bizUID']);
			}
		}
	}

}

?>