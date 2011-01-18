<?PHP

/*
	Compiled by bizLang compiler version 1.02

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/10/2011
	TestApproval: none

*/
require_once '../biz/epostviewer/epostviewer.php';

class fullepostviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;

	//Variables
	var $expanded;
	var $UID;
	var $isLoaded;

	//Nodes (bizvars)
	var $post;
	var $comments_array_data; 	var $comments;

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->_curFrame='frm';
		$this->post=new epostviewer($this->_fullname.'_post');
		$this->comments=array();
		$this->expanded=false;
		$this->UID=-1;
		$this->isLoaded=false;;
	}

	function __sleep(){
		return array('_fullname', '_curFrame','expanded','UID','isLoaded','post','comments');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			$this->post->message($to, $message, $info);
			foreach($this->comments as $i=>&$_element)
				$_element->message($to, $message, $info);
			return;
		}
		switch($message){
			case 'expand':
				$this->onExpand($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		$this->post->broadcast($message, $info);
		foreach($this->comments as $i=>&$_element)
			$_element->broadcast($message, $info);
		switch($message){
			case 'expand':
				$this->onExpand($info);
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


	function bookUID($UID){
		$this->UID=$UID;
		$this->post->bookUID($this->UID);
	}
	function onExpand($info){
		$this->bookExpanded(!$this->expanded);
	}
	function bookExpanded($expanded){
		if(! $this->isLoaded && $expanded){
			$this->loadComments();
		}
		$this->expanded=$expanded;
		$this->_bookframe("frm");
	}
	function loadComments(){
		$comments=$this->post->backCommentsUID();
		$this->comments=array();
		$id=0;
		foreach($comments as $c){
			$this->comments[]=new fullepostviewer($this->_fullname.$id++);
			end($this->comments)->bookUID($c);
		}
		$this->isLoaded=true;
	}
	function frm(){
		$post=$this->post->_backframe();
		$this->expanded? $sign='[-]' : $sign='[+]' ;
		$comments='<div style="margin-left:75px;">';
		if($this->expanded){
			foreach($this->comments as $c){
				$comments.=$c->_backframe();
			}
		}
		$comments.='</div>';
		if($this->post->post->noOfComments>0){
			$sign=<<<PHTML
				<form name="{$this->_fullname}" method="post">
					<input type="hidden" name="_message" value="expand" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input value ="$sign" type = "button" onclick = 'JavaScript:sndmsg("{$this->_fullname}")' class="press"/>
				</form>
PHTML;
		}else{
			$sign='';
		}
		$html=<<<PHTML
			<hr /> $post
			$sign $comments
PHTML;
		return $html;
	}

}

?>