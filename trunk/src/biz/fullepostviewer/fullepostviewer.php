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
	Date:		1/10/2011
	TestApproval: none

*/
require_once '../biz/epostviewer/epostviewer.php';

class fullepostviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $expanded;
	var $UID;
	var $isLoaded;

	//Nodes (bizvars)
	var $post;
	var $comments; // array of biz

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
			$_SESSION['osMsg']['frame_expand'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->post=new epostviewer($this->_fullname.'_post');

		//handle arrays
		$this->comments=array();
		if(!isset($_SESSION['osNodes'][$fullname]['comments']))
			$_SESSION['osNodes'][$fullname]['comments']=array();
		foreach($_SESSION['osNodes'][$fullname]['comments'] as $arrfn)
			$this->comments[]=new fullepostviewer($arrfn);

		if(!isset($_SESSION['osNodes'][$fullname]['expanded']))
			$_SESSION['osNodes'][$fullname]['expanded']=false;
		$this->expanded=&$_SESSION['osNodes'][$fullname]['expanded'];

		if(!isset($_SESSION['osNodes'][$fullname]['UID']))
			$_SESSION['osNodes'][$fullname]['UID']=-1;
		$this->UID=&$_SESSION['osNodes'][$fullname]['UID'];

		if(!isset($_SESSION['osNodes'][$fullname]['isLoaded']))
			$_SESSION['osNodes'][$fullname]['isLoaded']=false;;
		$this->isLoaded=&$_SESSION['osNodes'][$fullname]['isLoaded'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']=fullepostviewer;
	}

	function sleep(){
		$_SESSION['osNodes'][$this->_fullname]['slept']=true;
		$_SESSION['osNodes'][$this->_fullname]['comments']=array();
		foreach($this->comments as $node){
			$_SESSION['osNodes'][$this->_fullname]['comments'][]=$node->_fullname;
		}
	}

	function __destruct() {
		if($this->_tmpNode or !isset($_SESSION['osNodes'][$this->_fullname]['slept']))
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['slept']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_expand':
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
					<input type="hidden" name="_message" value="frame_expand" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
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