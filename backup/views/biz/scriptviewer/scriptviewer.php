<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	5/1/2011
	Ver:	0.1

*/
require_once 'biz/publink/publink.php';

class scriptviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $script;
	var $id;
	var $link;

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

		if(!isset($_SESSION['osNodes'][$fullname]['id']))
			$_SESSION['osNodes'][$fullname]['id']=0;
		$this->id=&$_SESSION['osNodes'][$fullname]['id'];

		if(!isset($_SESSION['osNodes'][$fullname]['link']))
			$_SESSION['osNodes'][$fullname]['link']="";
		$this->link=&$_SESSION['osNodes'][$fullname]['link'];

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


	function generateScript($adLinkData){
		$pl=new publink("");
		$this->id=$pl->generateScript($adLinkData);
		$this->link=$adLinkData['videoCode'];
		if($this->id!=0){
			$this->script="<EMBED SRC='http://www.sam-rad.com/YouTubePlayer.swf' FlashVars='id=".$this->id."&link=".$this->link."?version=3' WIDTH='960' HEIGHT='540' allowfullscreen='true' scale='noscale'/>";
		}else{
			$this->script="LOGIN first!";
		}
	}
	function frm(){
		if($this->id!=0){
			return <<<PHTMLCODE

				<input style="display:none;" id="{$this->_fullname}id" value="{$this->id}" />
				<input style="display:none;" id="{$this->_fullname}link" value="{$this->link}" />
				<textarea id="{$this->_fullname}scriptarea" rows="5" cols="75">{$this->script}</textarea><br />
				Width : <input size="10" id="{$this->_fullname}W" value="960" onkeypress='JavaSript:onWChange("{$this->_fullname}")' onchange='JavaSript:onWChange("{$this->_fullname}")'/>px 
				Height : <input size="10" id="{$this->_fullname}H" value="540" onkeypress='JavaSript:onHChange("{$this->_fullname}")' onchange='JavaSript:onHChange("{$this->_fullname}")'/>px
				<br><b>Instruction:</b><br>
				Copy content in the text area and paste it in your weblog/website
			
PHTMLCODE;

		}else{
			return <<<PHTMLCODE

				<textarea id="{$this->_fullname}script" rows="5" cols="75">{$this->script}</textarea>
			
PHTMLCODE;

		}
	}

}

?>