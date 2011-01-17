<?PHP

/*
	Compiled by bizLang compiler version 1.01

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/10/2011
	TestApproval: none

*/
require_once '../biz/epostviewer/epostviewer.php';

class fullepostviewer {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $expanded;
	var $UID;
	var $isLoaded;

	//Nodes (bizvars)
	var $post;
	var $comments_array_data; 	var $comments;

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
		if(! isset ($data['expanded']))
			$data['expanded']=false;
		if(! isset ($data['UID']))
			$data['UID']=-1;
		if(! isset ($data['isLoaded']))
			$data['isLoaded']=false;;
		if(! isset ($data['post'])){
			$data['post']['fullname']=$data['fullname'].'_post';
			$data['post']['bizname']='post';
		}
		if(! isset ($data['comments_array_data']))
			$data['comments_array_data']=array();
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->expanded=&$data['expanded'];
		$this->UID=&$data['UID'];
		$this->isLoaded=&$data['isLoaded'];

		$data['post']['parent']=$this;
		$this->post=new epostviewer($data['post']);

		$this->comments=array();
		$this->comments_array_data=&$data['comments_array_data'];
		foreach($data['comments_array_data'] as $na=>&$da){
			if(! isset($da['bizname'])){
				$da['bizname']=$na;
				$da['fullname']=$this->_fullname."_".$na;
				$da['parent']=$this;
			}
			$this->comments[]=new fullepostviewer($da);
		}
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
		
		// Empty the array
		$this->comments_array_data=array();
		$this->comments=array();
		foreach($comments as $c){
			
			// Add new Node to the array
			$_index=count($this->comments_array_data);
			$_data=array();
			$_data['parent']=$this;
			$_data['bizname']=$_index;
			$_data['fullname']=$this->_fullname.'_'.$_index;
			$this->comments_array_data[]=$_data;
			$this->comments[]=new  fullepostviewer($this->comments_array_data[$_index]);
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