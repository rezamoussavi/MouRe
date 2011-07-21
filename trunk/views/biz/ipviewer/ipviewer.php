<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	7/05/2011
	Ver:	1.0
	---------------------
	Author:	Reza Moussavi
	Date:	4/21/2011
	Ver:	0.1

*/
require_once 'biz/publink/publink.php';

class ipviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $linkID;
	var $title;

	//Nodes (bizvars)

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
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		if(!isset($_SESSION['osNodes'][$fullname]['linkID']))
			$_SESSION['osNodes'][$fullname]['linkID']='';
		$this->linkID=&$_SESSION['osNodes'][$fullname]['linkID'];

		if(!isset($_SESSION['osNodes'][$fullname]['title']))
			$_SESSION['osNodes'][$fullname]['title']='';
		$this->title=&$_SESSION['osNodes'][$fullname]['title'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='ipviewer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			default:
				break;
		}
	}

	function _bookframe($frame){
		$this->_curFrame=$frame;
		//$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$_style='';
		switch($this->_curFrame){
			case 'frm':
				$_style='  style="float:left;border:1px solid #000000;margin:5px;padding:2px;width:200px;" ';
				break;
		}
		$html.='<div '.$_style.' id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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


	/*******************************
	*	Frame
	*******************************/
	function frm(){
		$pl=new publink("");
		$data=$pl->backStat($this->linkID);
		if(!is_array($data)){
			return "No Record for Statistics";
		}
		$total=0;
		$html='<div style="width:200px;float:left;">';
		$html.='<div style="float:left;width:100;display:inline;margin:2px;padding:2px;border:1px dotted black;">Country</div>';
		$html.='<div style="float:left;width:50;display:inline;margin:2px;padding:2px;border:1px dotted black;">Views</div></div>';
		foreach($data as $d){
			$html.='<div style="width:200px;float:left;">';
			$html.='<div style="float:left;width:100;display:inline;margin:2px;padding:2px;border:1px dotted black;">'.$d['countryName'].'('.$d['countryCode'].')</div>';
			$html.='<div style="float:left;width:50;display:inline;margin:2px;padding:2px;border:1px dotted black;">'.$d['views'].'</div></div>';
			$total+=$d['views'];
		}
		$html.='<div style="width:200px;float:left;border:1px dotted black;">';
		$html.='<div style="float:left;width:100;display:inline;margin:2px;padding:2px;text-align:right;">Total:</div>';
		$html.='<div style="float:left;width:50;display:inline;margin:2px;padding:2px;">'.$total.'</div></div>';
		$html.='<div style="float:left;width:150;display:inline;margin:2px;padding:2px;"><a target="_blank" href="/worldmap?video='.$this->linkID.'&title='.$this->title.'" title="Open in new window">Show on Map</a></div>';
		return $html;
	}
	/*******************************
	*	Book/Back
	*******************************/
	function bookInfo($id,$title){
		$this->linkID=$id;
		$this->title=$title;
	}

}

?>