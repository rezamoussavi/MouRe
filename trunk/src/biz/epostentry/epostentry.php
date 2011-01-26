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
	Date:		1/4/2011
	TestApproval: none

*/
require_once '../biz/epost/epost.php';

class epostentry {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;

	//Variables
	var $ownerName;
	var $ownerUID;

	//Nodes (bizvars)

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->_curFrame='frm';
	}

	function __sleep(){
		return array('_fullname', '_curFrame','ownerName','ownerUID');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			return;
		}
		switch($message){
			case 'frame_stickit':
				$this->onStickIt($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		switch($message){
			case 'frame_stickit':
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


	function bookPostOwner($ownerName,$ownerUID){
		$this->ownerName=$ownerName;
		$this->ownerUID=$ownerUID;
	}
	function frm(){
		$html=<<<HTML
		<FORM name="$this->_fullname" method="POST">
			<input type="hidden" name="_message" value="frame_stickit" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
			Content:<br />
			<textarea name="content" rows="2" cols="25"></textarea><br />
			Title <input type="input" name="title" />
			<input value ="Stick It!" type = "button" onclick = 'JavaScript:sndmsg("$this->_fullname")' class="press" style="margin-top: 10px; margin-right: 0px;" />
		</FORM>
HTML;
		return $html;
	}
	function onStickIt($info){
		$ep=new epost("temp");
		$ep->addpost(array("title"=>$info['title'],"content"=>$info['content'],"ownerbiz"=>$this->ownerName,"ownerbizUID"=>$this->ownerUID));
		osBroadcast("epost_newPostAdded",array());
	}

}

?>