<?PHP

/*
	Compiled by bizLang compiler version 1.4 (Feb 4 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}
	1.4: {multi parameter in link message}

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
	var $_tmpNode;

	//Variables
	var $expanded;
	var $userUID;
	var $UID;
	var $contentLoaded;
	var $curLink;
	var $linkto;

	//Nodes (bizvars)
	var $myCat;
	var $eBLists; // array of biz
	var $eBoards; // array of biz

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
			$_SESSION['osMsg']['frame_click'][$this->_fullname]=true;
			$_SESSION['osMsg']['client_page'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->myCat=new category($this->_fullname.'_myCat');

		//handle arrays
		$this->eBLists=array();
		if(!isset($_SESSION['osNodes'][$fullname]['eBLists']))
			$_SESSION['osNodes'][$fullname]['eBLists']=array();
		foreach($_SESSION['osNodes'][$fullname]['eBLists'] as $arrfn)
			$this->eBLists[]=new eblistviewer($arrfn);

		//handle arrays
		$this->eBoards=array();
		if(!isset($_SESSION['osNodes'][$fullname]['eBoards']))
			$_SESSION['osNodes'][$fullname]['eBoards']=array();
		foreach($_SESSION['osNodes'][$fullname]['eBoards'] as $arrfn)
			$this->eBoards[]=new eblineviewer($arrfn);

		if(!isset($_SESSION['osNodes'][$fullname]['expanded']))
			$_SESSION['osNodes'][$fullname]['expanded']=false;
		$this->expanded=&$_SESSION['osNodes'][$fullname]['expanded'];

		if(!isset($_SESSION['osNodes'][$fullname]['userUID']))
			$_SESSION['osNodes'][$fullname]['userUID']=-1;
		$this->userUID=&$_SESSION['osNodes'][$fullname]['userUID'];

		if(!isset($_SESSION['osNodes'][$fullname]['UID']))
			$_SESSION['osNodes'][$fullname]['UID']=0;
		$this->UID=&$_SESSION['osNodes'][$fullname]['UID'];

		if(!isset($_SESSION['osNodes'][$fullname]['contentLoaded']))
			$_SESSION['osNodes'][$fullname]['contentLoaded']=false;
		$this->contentLoaded=&$_SESSION['osNodes'][$fullname]['contentLoaded'];

		if(!isset($_SESSION['osNodes'][$fullname]['curLink']))
			$_SESSION['osNodes'][$fullname]['curLink']="close";
		$this->curLink=&$_SESSION['osNodes'][$fullname]['curLink'];

		if(!isset($_SESSION['osNodes'][$fullname]['linkto']))
			$_SESSION['osNodes'][$fullname]['linkto']="open";
		$this->linkto=&$_SESSION['osNodes'][$fullname]['linkto'];

		$this->init(); //Customized Initializing
		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='eblistviewer';
	}

	function sleep(){
		$_SESSION['osNodes'][$this->_fullname]['slept']=true;
		$_SESSION['osNodes'][$this->_fullname]['eBLists']=array();
		foreach($this->eBLists as $node){
			$_SESSION['osNodes'][$this->_fullname]['eBLists'][]=$node->_fullname;
		}
		$_SESSION['osNodes'][$this->_fullname]['eBoards']=array();
		foreach($this->eBoards as $node){
			$_SESSION['osNodes'][$this->_fullname]['eBoards'][]=$node->_fullname;
		}
	}

	function __destruct() {
		$_SESSION['osNodes'][$this->_fullname]['eBLists']=array();
		foreach($this->eBLists as $node){
			$_SESSION['osNodes'][$this->_fullname]['eBLists'][]=$node->_fullname;
		}
		$_SESSION['osNodes'][$this->_fullname]['eBoards']=array();
		foreach($this->eBoards as $node){
			$_SESSION['osNodes'][$this->_fullname]['eBoards'][]=$node->_fullname;
		}
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_click':
				$this->onClick($info);
				break;
			case 'client_page':
				$this->onPage($info);
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


	function onPage($info){
		if($info['state']=="open"){
			$this->expanded=false;
			$this->linkto="close";
			$this->curLink="open";
		}
		else if($info['state']=="close"){
			$this->expanded=true;
			$this->linkto="open";
			$this->curLink="close";
		}
		$this->onClick(array());
	}
	function init(){
		$this->myCat->bookUID($this->UID);
	}
	function frm(){
		if($this->expanded)
			$this->loadContent();
		$Lable=$this->myCat->lable;
		$link=osBackLinkInfo($this->_fullname,"page",array("state"=>$this->curLink),"page",array("state"=>$this->linkto));
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