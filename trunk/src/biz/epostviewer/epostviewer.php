<?PHP

/*
	Compiled by bizLang compiler version 1.3 (Jan 2 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}

	Author:		Reza Moussavi
	Version:	1.1
	Date:		1/26/2011
	TestApproval: none
	-------------------
	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/6/2011
	TestApproval: none

*/
require_once '../biz/epost/epost.php';
require_once '../biz/epostentry/epostentry.php';

class epostviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $showCommentBox;
	var $UID;

	//Nodes (bizvars)
	var $post;
	var $comment;

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
			$_SESSION['osMsg']['frame_next'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_prev'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_comment'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->post=new epost($this->_fullname.'_post');

		$this->comment=new epostentry($this->_fullname.'_comment');

		if(!isset($_SESSION['osNodes'][$fullname]['showCommentBox']))
			$_SESSION['osNodes'][$fullname]['showCommentBox']=false;
		$this->showCommentBox=&$_SESSION['osNodes'][$fullname]['showCommentBox'];

		if(!isset($_SESSION['osNodes'][$fullname]['UID']))
			$_SESSION['osNodes'][$fullname]['UID']=-1;
		$this->UID=&$_SESSION['osNodes'][$fullname]['UID'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='epostviewer';
	}

	function sleep(){
		$_SESSION['osNodes'][$this->_fullname]['slept']=true;
	}

	function __destruct() {
		if($this->_tmpNode or !isset($_SESSION['osNodes'][$this->_fullname]['slept']))
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['slept']);
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_next':
				$this->onNext($info);
				break;
			case 'frame_prev':
				$this->onPrev($info);
				break;
			case 'frame_comment':
				$this->onComment($info);
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


	function backCommentsUID(){
		return $this->post->backCommentsUID();
	}
	function bookUID($UID){
		$this->UID=$UID;
		$this->post->bookUID($UID);
		$this->comment->bookPostOwner("epost",$UID);
	}
	function onNext($info){
		if($this->post->next())
			$this->_bookframe("frm");
	}
	function onPrev($info){
		if($this->post->prev())
			$this->_bookframe("frm");
	}
	function onComment($info){
		$this->showCommentBox=!$this->showCommentBox;
		$this->_bookframe("frm");
	}
	function frm(){
		$next=$this->_fullname."next";
		$prev=$this->_fullname."prev";
		$comment=$this->_fullname."comment";
		$header=<<<PHTML
			<div style="float:left;display:inline;">
				{$this->post->title}
			</div>
			<div style="float:right;display:inline;">
				By: {$this->post->author}
			</div>
			<br />
PHTML;
		$body=<<<PHTML
			<div style="border-style:dashed;border-width:1px;">
				{$this->post->content}
			</div><br />
PHTML;
		$footer=<<<PHTML
			({$this->post->timeStamp})
			<form name="$prev" method="post" style="display:inline">
				<input type="hidden" name="_message" value="frame_prev" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="<" type = "button" onclick = 'JavaScript:sndmsg("$prev")'  class="press"/>
			</form>
			{$this->post->edition} / {$this->post->lastedition}
			<form name="$next" method="post" style="display:inline">
				<input type="hidden" name="_message" value="frame_next" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value =">" type = "button" onclick = 'JavaScript:sndmsg("$next")'  class="press"/>
			</form>
			({$this->post->noOfComments})
			<form name="$comment" method="post" style="display:inline">
				<input type="hidden" name="_message" value="frame_comment" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="Comment" type = "button" onclick = 'JavaScript:sndmsg("$comment")'  class="press"/>
			</form>
PHTML;
		$comments='';
		if($this->showCommentBox)
			$comments='<div style="margin-left:50px;"><br/>'.$this->comment->_backframe().'</div>';
		$html=$header.$body.$footer.$comments;
		return $html;
	}

}

?>