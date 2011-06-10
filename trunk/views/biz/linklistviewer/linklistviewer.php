<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	6/02/2011
	Ver:	1.0

*/
require_once 'biz/adlink/adlink.php';
require_once 'biz/linkviewer/linkviewer.php';

class linklistviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables

	//Nodes (bizvars)
	var $links; // array of biz

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
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmList';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		//handle arrays
		$this->links=array();
		if(!isset($_SESSION['osNodes'][$fullname]['links']))
			$_SESSION['osNodes'][$fullname]['links']=array();
		foreach($_SESSION['osNodes'][$fullname]['links'] as $arrfn)
			$this->links[]=new linkviewer($arrfn);

		if(!isset($_SESSION['osNodes'][$fullname]['biz']))
			$this->init(); //Customized Initializing
		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='linklistviewer';
	}

	function gotoSleep() {
		$_SESSION['osNodes'][$this->_fullname]['links']=array();
		foreach($this->links as $node){
			$_SESSION['osNodes'][$this->_fullname]['links'][]=$node->_fullname;
		}
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'user_logout':
				$this->onLogout($info);
				break;
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
			case 'frmList':
				$_style=' style="" ';
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


	/********************************
	*	Functionalities
	*********************************/
	function init(){
		$this->links=array();
		$al=new adlink("");
		$all=$al->backAllLink();
		$user=new user("");
		$i=0;
		foreach($all as $LData){
			$u=$user->backUserData($LData['advertisor']);
			$LData['userName']=$u['userName'];
			$l=new linkviewer($this->_fullname.$i++);
			$l->bookData($LData);
			$this->links[]=$l;
		}
	}
	/********************************
	*	Frames
	*********************************/
	function frmList(){
		if(!osIsAdmin()){
			return '<font style="background-color:#000000;color:#00FF00;">. Admin Access Only! .</font>';
		}
		$html="";
		foreach($this->links as $l){
			$html.=$l->_backframe();
		}
		return $html;
	}
	/********************************
	*	Message Handlers
	*********************************/
	function onLogout(){
		$this->_bookframe("frmList");
	}

}

?>