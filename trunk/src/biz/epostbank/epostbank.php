<?PHP

/*
	Compiled by bizLang compiler version 1.1

	{Family included}

	Author:		Reza Moussavi
	Version:	1.1
	Date:		1/26/2011
	TestApproval: none
	-------------------
	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/10/2011
	TestApproval: none

*/
require_once '../biz/fullepostviewer/fullepostviewer.php';
require_once '../biz/category/category.php';

class epostbank {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;

	//Variables
	var $curUID;

	//Nodes (bizvars)
	var $posts_array_data; 	var $posts;

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->_curFrame='frm';
		$this->posts=array();
		$this->curUID=-1;
	}

	function __sleep(){
		return array('_fullname', '_curFrame','curUID','posts');
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
		$posts='';
		foreach($this->posts as $p){
			$posts.=$p->_backframe();
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
		$this->_bookframe("frm");
	}
	function reload(){
		$cat=new category("temp");
		$content=$cat->backContentOf($this->curUID);
		$this->posts=array();
		$id=0;
		foreach($content as $c){
			if($c['bizname']=='epost'){
				$this->posts[]=new fullepostviewer($this->_fullname.$id++);
				end($this->posts)->bookUID($c['bizUID']);
			}
		}
	}

}

?>