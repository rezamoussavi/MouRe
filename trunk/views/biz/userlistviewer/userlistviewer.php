<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	6/02/2011
	Ver:	1.0
	---------------------
	Author:	Reza Moussavi
	Date:	5/31/2011
	Ver:	0.1

*/
require_once 'biz/user/user.php';
require_once 'biz/userviewer/userviewer.php';

class userlistviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables

	//Nodes (bizvars)
	var $users; // array of biz

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
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmUsers';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		//handle arrays
		$this->users=array();
		if(!isset($_SESSION['osNodes'][$fullname]['users']))
			$_SESSION['osNodes'][$fullname]['users']=array();
		foreach($_SESSION['osNodes'][$fullname]['users'] as $arrfn)
			$this->users[]=new userviewer($arrfn);

		if(!isset($_SESSION['osNodes'][$fullname]['biz']))
			$this->init(); //Customized Initializing
		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='userlistviewer';
	}

	function gotoSleep() {
		$_SESSION['osNodes'][$this->_fullname]['users']=array();
		foreach($this->users as $node){
			$_SESSION['osNodes'][$this->_fullname]['users'][]=$node->_fullname;
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
			case 'frmUsers':
				$_style=' ';
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


	function init(){
		$this->users=array();
		$u=new user("");
		$us=$u->backAll();
		$i=0;
		foreach($us as $userData){
			$uv=new userviewer($this->_fullname.$i++);
			$uv->bookModeUser("bar",$userData);
			$this->users[]=$uv;
		}
	}
	/****************************
	*	Frames
	****************************/
	function frmUsers(){
		if(!osIsAdmin()){
			return '<font style="background-color:#000000;color:#00FF00;">. Admin Access Only! .</font>';
		}
		if(count($this->users)<1){
			$this->init();
		}
		$html="<table>";
		foreach($this->users as $uv){
			$html.="<tr>";
			$html.=$uv->_backframe();
			$html.="</tr>";
		}
		$html.="</table>";
		return $html;
	}
	/****************************
	*	Functionalities
	****************************/
	function onLogout($info){
		$this->_bookframe("frmUsers");
	}

}

?>