<?PHP

/*
	Compiled by bizLang compiler version 3.2 [DB added] (March 26 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	4/21/2011
	Ver:		0.1

*/

class addvideo {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables

	//Nodes (bizvars)

	function __construct($fullname) {
		$this->_frmChanged=false;
		$this->_tmpNode=false;
		if($fullname==null){
			$fullname='_tmpNode_'.count($_SESSION['osNodes']);
			$this->_tmpNode=true;
		}
		$this->_fullname=$fullname;
		if(!isset($_SESSION['osNodes'][$fullname])){
			$_SESSION['osNodes'][$fullname]=array();
			//If any message need to be registered will placed here
			$_SESSION['osMsg']['frame_addVideo'][$this->_fullname]=true;
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='addvideo';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_addVideo':
				$this->onAddVideo($info);
				break;
			default:
				break;
		}
	}

	function _bookframe($frame){
		$this->_frmChanged=true;
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
		$html='<div '.$_style.' id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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


	function frm(){
		if(!osUserLogedin()){
			return <<<PHTMLCODE

				<div style="text-align:center; width=100%;">Log in first</div>
			
PHTMLCODE;

		}
		//else
		$formname=$this->_fullname;
		$minAOVP=10;////////////change via offer biz
		return <<<PHTMLCODE

			<form name="$fomrname" method="post">
				<input type="hidden" name="_message" value="frame_addVideo" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				Youtube link: <input name="link" size=20><br>
				Your Offer on Price/View: <input name="AOVP" size=5> (min:$minAOVP)<br>
				Number of Viewes: <input name="NOV" size=5><br>
				Commision: (auto calculate)
				<hr>
				Total: (auto calculate) <input type="button" value="Pay"><br>
				<input type="button" value="Apply">
			</form>
		
PHTMLCODE;

	}
	function onAddVideo($info){
	}

}

?>