<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Max Mirkia
	Date:	6/02/2011
	Ver:	1.0

*/
require_once 'biz/settingviewer/settingviewer.php';
require_once 'biz/userlistviewer/userlistviewer.php';
require_once 'biz/linklistviewer/linklistviewer.php';

class adminviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables

	//Nodes (bizvars)
	var $setting;
	var $users;
	var $links;

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
			$_SESSION['osMsg']['frame_settingListViewer'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_userListViewer'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_linkListViewer'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmButtons';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->setting=new settingviewer($this->_fullname.'_setting');

		$this->users=new userlistviewer($this->_fullname.'_users');

		$this->links=new linklistviewer($this->_fullname.'_links');

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='adminviewer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_settingListViewer':
				$this->onSettingListViewr($info);
				break;
			case 'frame_userListViewer':
				$this->onUserListViewer($info);
				break;
			case 'frame_linkListViewer':
				$this->onLinkListViewer($info);
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
			case 'frmButtons':
				$_style='  style="" ';
				break;
			case 'frmSetting':
				$_style='  style="" ';
				break;
			case 'frmUsers':
				$_style='  style="" ';
				break;
			case 'frmLinks':
				$_style='  style="" ';
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


	/*-----------------------------------------
	-		MESSAGES
	-----------------------------------------*/
	function onLinkListViewer($info){
		$this->_bookframe("frmLinks");
	}
	
	
	function onSettingListViewr($info){
		$this->_bookframe("frmSetting");
	}
	
	function onUserListViewer($info){
		$this->_bookframe("frmUsers");
	}
	
	/*************************************
	*		FRAMES
	*************************************/
	
	function frmLinks(){
		$button = $this->frmButtons();
		$links = $this->links->_backframe();
		return <<<PHTMLCODE

			$button <br> $links
		
PHTMLCODE;

	
	}
	
	function frmSetting(){
		$button = $this->frmButtons();
		$setting = $this->setting->_backframe();
		return <<<PHTMLCODE

			$button <br> $setting
		
PHTMLCODE;

	
	}
	
	function frmUsers(){
		$button = $this->frmButtons();
		$users = $this->users->_backframe();
		return <<<PHTMLCODE

			$button <br> $users		
		
PHTMLCODE;

	
	}
	
	function frmButtons(){
		$frmSetting=$this->_fullname."setting";
		$frmLinks=$this->_fullname."links";
		$frmUsers=$this->_fullname."users";		
		return <<<PHTMLCODE

			<div style="width:100%; float:left; text-align:center;">
				<form name="$frmSetting" method="post" style="display:inline;">
					<input type="hidden" name="_message" value="frame_settingListViewer" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input value ="Setting" type = "button" onclick = 'JavaScript:sndmsg("$frmSetting")' class="press" style="margin-top: 10px; margin-right: 50px;" />
				</form>				
				<form name="$frmUsers" method="post" style="display:inline;">
					<input type="hidden" name="_message" value="frame_userListViewer" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input value ="Users" type = "button" onclick = 'JavaScript:sndmsg("$frmUsers")' class="press" style="margin-top: 10px; margin-right: 50px;" />
				</form>
				<form name="$frmLinks" method="post" style="display:inline;">
					<input type="hidden" name="_message" value="frame_linkListViewer" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input value ="Links" type = "button" onclick = 'JavaScript:sndmsg("$frmLinks")' class="press" style="margin-top: 10px; margin-right: 50px;" />
				</form>
			</div><hr>
		
PHTMLCODE;

	
	}

}

?>