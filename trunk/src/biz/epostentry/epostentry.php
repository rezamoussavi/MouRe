<?PHP

/*
	Compiled by bizLang compiler version 1.01

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/4/2011
	TestApproval: none

*/
require_once '../biz/epost/epost.php';

class epostentry {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $ownerName;
	var $ownerUID;

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
		if(! isset ($data['curFrame']))
			$data['curFrame']='frm';
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->ownerName=&$data['ownerName'];
		$this->ownerUID=&$data['ownerUID'];

	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			return;
		}
		switch($message){
			case 'stickit':
				$this->onStickIt($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		switch($message){
			case 'stickit':
				$this->onStickIt($info);
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


	function bookPostOwner($ownerName,$ownerUID){
		$this->ownerName=$ownerName;
		$this->ownerUID=$ownerUID;
	}
	function frm(){
		$html=<<<HTML
		<FORM name="$this->_fullname" method="POST">
			<input type="hidden" name="_message" value="stickit" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
			Content:<br />
			<textarea name="content" rows="2" cols="25"></textarea><br />
			Title <input type="input" name="title" />
			<input value ="Stick It!" type = "button" onclick = 'JavaScript:sndmsg("$this->_fullname")' class="press" style="margin-top: 10px; margin-right: 0px;" />
		</FORM>
HTML;
		return $html;
	}
	function onStickIt($info){
		$emptyarray=array();
		$ep=new epost($emptyarray);
		$ep->addpost(array("title"=>$info['title'],"content"=>$info['content'],"ownerbiz"=>$this->ownerName,"ownerbizUID"=>$this->ownerUID));
		osBroadcast("newPostAdded",array());
	}

}

?>