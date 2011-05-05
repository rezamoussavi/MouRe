<?PHP

/*
	Compiled by bizLang compiler version 3.2 [DB added] (March 26 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	5/1/2011
	Ver:		0.1

*/

class scriptviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $script;

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

		if(!isset($_SESSION['osNodes'][$fullname]['script']))
			$_SESSION['osNodes'][$fullname]['script']="[!]";
		$this->script=&$_SESSION['osNodes'][$fullname]['script'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='scriptviewer';
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
				$_style='';
				break;
		}
		$html='<script type="text/javascript" language="Javascript">';
		$html.=<<<JAVASCRIPT
	function Do_Copy(txt){
		if (window.clipboardData) {
			window.clipboardData.setData("Text",txt);
			alert('The text is copied to your clipboard...');
		}else{
			alert('The text is NOT copied to your clipboard...');
		}
	}

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


	function generateScript($adLinkID){
		$user=osBackUser();
		$userID=$user['UID'];
		if($userID<1){
			$this->script="Generating data (".$adLinkID.") failed! - LOGIN first ";
		}else{
			$this->script="Generating data (".$adLinkID.") not accomplished! for user (".$userID." : ".$user['email'].") ";
		}
	}
	function frm(){
		return <<<PHTMLCODE

			<textarea id="codetopublish{$this->_fullname}" rows="5" cols="45">{$this->script}</textarea>
			<br><input type="button" value="Copy" onclick='JavaScript:Do_Copy("{$this->script}")'>
			<br><b>Instruction:</b><br>
			Press Copy button or copy content in the text area and paste it in your weblog/website
		
PHTMLCODE;

	}

}

?>