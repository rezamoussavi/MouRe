<?PHP

/*
	Compiled by bizLang compiler version 1.1

	{Family included}

	Author: Reza Moussavi
	Date:	1/25/2011
	Version:	1.2
	----------------------
	Author: Reza Moussavi
	Date:	1/15/2011
	Version:	1.1
	----------------------
	Author: Reza Moussavi
	Date:	12/29/2010
	Version:	1.0

*/
require_once '../biz/category/category.php';
require_once '../biz/eblineviewer/eblineviewer.php';

class eblistviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;

	//Variables
	var $expanded;
	var $userUID;
	var $UID;
	var $contentLoaded;
	var $curLink;
	var $linkto;

	//Nodes (bizvars)
	var $myCat;
	var $eBLists_array_data; 	var $eBLists;
	var $eBoards_array_data; 	var $eBoards;

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->_curFrame='frm';
		$this->myCat=new category($this->_fullname.'_myCat');
		$this->eBLists=array();
		$this->eBoards=array();
		$this->expanded=false;
		$this->userUID=-1;
		$this->UID=0;
		$this->contentLoaded=false;
		$this->curLink="close";
		$this->linkto="open";
		$this->init(); //Customized Initializing
	}

	function __sleep(){
		return array('_fullname', '_curFrame','expanded','userUID','UID','contentLoaded','curLink','linkto','myCat','eBLists','eBoards');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			$this->myCat->message($to, $message, $info);
			foreach($this->eBLists as $i=>&$_element)
				$_element->message($to, $message, $info);
			foreach($this->eBoards as $i=>&$_element)
				$_element->message($to, $message, $info);
			return;
		}
		switch($message){
			case 'frame_click':
				$this->onClick($info);
				break;
			case 'client_open':
				$this->onOpen($info);
				break;
			case 'client_close':
				$this->onClose($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		$this->myCat->broadcast($message, $info);
		foreach($this->eBLists as $i=>&$_element)
			$_element->broadcast($message, $info);
		foreach($this->eBoards as $i=>&$_element)
			$_element->broadcast($message, $info);
		switch($message){
			case 'frame_click':
				$this->onClick($info);
				break;
			case 'client_open':
				$this->onOpen($info);
				break;
			case 'client_close':
				$this->onClose($info);
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


	function onOpen($info){
		$this->expanded=false;
		$this->linkto="close";
		$this->curLink="open";
		$this->onClick(array());
	}
	function onClose($info){
		$this->expanded=true;
		$this->linkto="open";
		$this->curLink="close";
		$this->onClick(array());
	}
	function init(){
		$this->myCat->bookUID($this->UID);
	}
	function frm(){
		if($this->expanded)
			$this->loadContent();
		$Lable=$this->myCat->lable;
		$link=osBackLink($this->_fullname,$this->curLink,$this->linkto);
        $html_old=<<<PHTML
			<form name="{$this->_fullname}" method="post">
	            <input type="hidden" name="_message" value="frame_click" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="$Lable" type = "button" onclick = 'JavaScript:sndmsg("{$this->_fullname}")' class="press" style="margin-top: 10px; margin-right: 0px;" />
			</form>
PHTML;
        $html=<<<PHTML
				<input value="$Lable" type = "button" onClick="window.location='$link'" class="press">
PHTML;
		$html.= $this->backContent();
		return $html;
	}
	function backContent(){
		$html='<div style="margin-left:10px;">';
		if($this->expanded){
			foreach($this->eBLists as $i=>&$L)
				$html .= $L->_backframe();
			foreach($this->eBoards as $j=>&$B)
				$html .= $B->_backframe();
		}
		return $html.'</div>';
	}
	function bookUID($UID){
		$this->contentLoaded=false;
		$this->eBoards=array();
		$this->eBLists=array();
		$this->UID=$UID;
		$this->init();
	}
	function onClick($info){
		$this->expanded=! $this->expanded;
		$this->_bookframe("frm");
	}
	function loadContent(){
		if($this->contentLoaded)
			return;
		$this->contentLoaded=true;
		$con=$this->myCat->backContent();
		$id=0;
		foreach($con as $cat){
			switch($cat['extra']){
				case 'eBList':
					$this->eBLists[]=new eblistviewer($this->_fullname.$id++);
					end($this->eBLists)->bookUID($cat['bizUID']);
					break;
				case 'eBoard':
					$this->eBoards[]=new eblineviewer($this->_fullname.$id++);;
					end($this->eBoards)->bookUID($cat['bizUID']);
					break;
				default:
					break;
			}//switch
		}//foreach
	}

}

?>