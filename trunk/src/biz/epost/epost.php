<?PHP

/*
	Compiled by bizLang compiler version 1.01

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/10/2011
	TestApproval: none

*/

class epost {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $UID;
	var $author;
	var $title;
	var $content;
	var $edition;
	var $lastedition;
	var $noOfComments;
	var $timeStamp;

	//Nodes (bizvars)

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
		if(! isset ($data['UID']))
			$data['UID']=-1;
		if(! isset ($data['edition']))
			$data['edition']=0;
		if(! isset ($data['lastedition']))
			$data['lastedition']=0;
		if(! isset ($data['noOfComments']))
			$data['noOfComments']=0;
		if(! isset ($data['timeStamp']))
			$data['timeStamp']="20101231235959";
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->UID=&$data['UID'];
		$this->author=&$data['author'];
		$this->title=&$data['title'];
		$this->content=&$data['content'];
		$this->edition=&$data['edition'];
		$this->lastedition=&$data['lastedition'];
		$this->noOfComments=&$data['noOfComments'];
		$this->timeStamp=&$data['timeStamp'];

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
		if($echo)
			echo $html;
		else
			return $html;
	}


//########################################
//         YOUR FUNCTIONS GOES HERE
//########################################


	function backCommentsUID(){
		if($this->UID==-1){
			return;
		}
		$ret=array();
		//
		return $ret;
	}
	function bookUID($UID){
	}
	function addpost($data){
		//$data=> content, title, ownerbiz, ownerbizUID
	}
	function next(){
		//goto next edition if there is any
		return false;
	}
	function prev(){
		//goto previous edition if there is any
		return false;
	}

}

?>