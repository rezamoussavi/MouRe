<?PHP

/*
	Compiled by bizLang compiler version 3.2 [DB added] (March 26 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	5/1/2011
	Ver:		1.0
	----------------------------------
	Author:	Reza Moussavi
	Date:	4/21/2011
	Ver:		0.1

*/
require_once 'biz/adlink/adlink.php';
require_once 'biz/videobar/videobar.php';

class videolistviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables

	//Nodes (bizvars)
	var $VBars; // array of biz

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

		//handle arrays
		$this->VBars=array();
		if(!isset($_SESSION['osNodes'][$fullname]['VBars']))
			$_SESSION['osNodes'][$fullname]['VBars']=array();
		foreach($_SESSION['osNodes'][$fullname]['VBars'] as $arrfn)
			$this->VBars[]=new videobar($arrfn);

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='videolistviewer';
	}

	function gotoSleep() {
		$_SESSION['osNodes'][$this->_fullname]['VBars']=array();
		foreach($this->VBars as $node){
			$_SESSION['osNodes'][$this->_fullname]['VBars'][]=$node->_fullname;
		}
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
				$_style='';
				break;
		}
		$html='<script type="text/javascript" language="Javascript">';
		$html.=<<<JAVASCRIPT

JAVASCRIPT;
		$html.=<<<JSONDOCREADY
function {$this->_fullname}(){}
JSONDOCREADY;
		$html.='</script>
<div '.$_style.' id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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


	function bookMode($mode){
		$al=new adlink("");
		$Vs=array();
		$Vs=$al->backVideoList($mode);
		$this->VBars=array();
		foreach($Vs as $v){
			$vb=new videobar("VBar".$this->_fullname.count($this->VBars));
			$vb->bookInfo($v);
			$vb->bookMode($mode);
			$this->VBars[]=$vb;
		}
	}
	function frm(){
		$VBars="";
		foreach($this->VBars as $vb){
			$VBars.=$vb->_backframe()."<hr>";
		}
		return <<<PHTMLCODE

			<center>Choose a video to Publish</center>
			<br>$VBars
		
PHTMLCODE;

	}

}

?>