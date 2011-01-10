<?PHP

/*
	Compiled by bizLang compiler version 1.01

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/6/2011
	TestApproval: none

*/
require_once '../biz/epost/epost.php';
require_once '../biz/epostentry/epostentry.php';

class epostviewer {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $showCommentBox;
	var $UID;

	//Nodes (bizvars)
	var $post;
	var $comment;

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
			$data['curFrame']=frm;
		if(! isset ($data['showCommentBox']))
			$data['showCommentBox']=false;
		if(! isset ($data['UID']))
			$data['UID']=-1;
		if(! isset ($data['post'])){
			$data['post']['fullname']=$this->_fullname.'_post';
			$data['post']['bizname']='post';
		}
		if(! isset ($data['comment'])){
			$data['comment']['fullname']=$this->_fullname.'_comment';
			$data['comment']['bizname']='comment';
		}
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->showCommentBox=&$data['showCommentBox'];
		$this->UID=&$data['UID'];

		$data['post']['parent']=$this;
		$this->post=new epost($data['post']);
		$data['comment']['parent']=$this;
		$this->comment=new epostentry($data['comment']);
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
	}
	function onNext($info){
		if($this->post->next())
			_setframe("frm");
	}
	function onPrev($info){
		if($this->post->prev())
			_setframe("frm");
	}
	function onComment($info){
		$this->showCommentBox=!$this->showCommentBox;
		_setframe("frm");
	}
	function frm(){
		$next=$this->_fullname."next";
		$prev=$this->_fullname."prev";
		$comment=$this->_fullname."comment";
		$html=<<<HTML
			<hr />
			By: {$this->post->author} Title: {$this->post->title} <hr />
			{$this->post->contents} <hr/>
			({$this->post->timeStamp})
			<form name="$prev" method="post">
				<input type="hidden" name="_message" value="prev" /><input type = "hidden" name="_target" value="' . $this->_fullname . '" />
				<input value ="<" type = "button" onclick = 'JavaScript:sndmsg("$prev")' />
			</form>
			{$this->post->edition} / {$this->post->lastedition}
			<form name="$next" method="post">
				<input type="hidden" name="_message" value="next" /><input type = "hidden" name="_target" value="' . $this->_fullname . '" />
				<input value =">" type = "button" onclick = 'JavaScript:sndmsg("$next")' />
			</form>
			({$this->post->noOfComments})
			<form name="$comment" method="post">
				<input type="hidden" name="_message" value="comment" /><input type = "hidden" name="_target" value="' . $this->_fullname . '" />
				<input value ="Comment" type = "button" onclick = 'JavaScript:sndmsg("$comments")' />
			</form>
			<hr />
HTML;
		if(showCommentBox)
			$html.="<hr>".$this->comment->_backframe();
		return $html;
	}

}

?>