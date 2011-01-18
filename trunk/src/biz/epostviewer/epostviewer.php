<?PHP

/*
	Compiled by bizLang compiler version 1.02

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

	//Variables
	var $showCommentBox;
	var $UID;

	//Nodes (bizvars)
	var $post;
	var $comment;

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->_curFrame='frm';
		$this->post=new epost($this->_fullname.'_post');
		$this->comment=new epostentry($this->_fullname.'_comment');
		$this->showCommentBox=false;
		$this->UID=-1;
	}

	function __sleep(){
		return array('_fullname', '_curFrame','showCommentBox','UID','post','comment');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			$this->post->message($to, $message, $info);
			$this->comment->message($to, $message, $info);
			return;
		}
		switch($message){
			case 'next':
				$this->onNext($info);
				break;
			case 'prev':
				$this->onPrev($info);
				break;
			case 'comment':
				$this->onComment($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		$this->post->broadcast($message, $info);
		$this->comment->broadcast($message, $info);
		switch($message){
			case 'next':
				$this->onNext($info);
				break;
			case 'prev':
				$this->onPrev($info);
				break;
			case 'comment':
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
				<input type="hidden" name="_message" value="prev" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="<" type = "button" onclick = 'JavaScript:sndmsg("$prev")'  class="press"/>
			</form>
			{$this->post->edition} / {$this->post->lastedition}
			<form name="$next" method="post" style="display:inline">
				<input type="hidden" name="_message" value="next" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value =">" type = "button" onclick = 'JavaScript:sndmsg("$next")'  class="press"/>
			</form>
			({$this->post->noOfComments})
			<form name="$comment" method="post" style="display:inline">
				<input type="hidden" name="_message" value="comment" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
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