<?PHP

/*
	Compiled by bizLang compiler version 3.2 [DB added] (March 26 2011) By Reza Moussavi

	Author: Reza Moussavi
	Date:	02/21/2011
	Version: 1
	---------------------
	Author: Reza Moussavi
	Date:	02/07/2011
	Version: 0.1

*/
require_once 'biz/user/user.php';

class profile {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables
	var $message;

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
			$_SESSION['osMsg']['frame_applyChanges'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_cancelChanges'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_editInfo'][$this->_fullname]=true;
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmView';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		if(!isset($_SESSION['osNodes'][$fullname]['message']))
			$_SESSION['osNodes'][$fullname]['message']='';
		$this->message=&$_SESSION['osNodes'][$fullname]['message'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='profile';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_applyChanges':
				$this->onApplyChanges($info);
				break;
			case 'frame_cancelChanges':
				$this->onCancelChanges($info);
				break;
			case 'frame_editInfo':
				$this->onEditInfo($info);
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
			case 'frmView':
				$_style='';
				break;
			case 'frmEdit':
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


	function frmView(){
		$html='';
		$uUID=osBackUser();
		if($uUID==-1){
			$html=<<<PHTMLCODE

				User not loggedin.
			
PHTMLCODE;

		}else{
			$u=new user("");
			$uName=$u->backName();
			$uMail=$u->backEmail();
			$uAddress=$u->backAddress();
			$uBDate=$u->backBDate();
			$html=<<<PHTMLCODE

				Name: {$uName}<br>
				email: {$uMail}<br>
				Address: {$uAddress}<br>
				Birth Date: {$uBDate}<br>
				<FORM name="{$this->_fullname}" method="post">
					<input type="hidden" name="_message" value="frame_editInfo" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input value ="Edit" type = "button" onclick = 'JavaScript:sndmsg("{$this->_fullname}")' class="press" style="margin-top: 10px; margin-right: 50px;" />
				</FORM>
			
PHTMLCODE;

		}
		return $html;
	}
	function frmEdit(){
		$u=new user("");
		$uName=$u->backName();
		$uMail=$u->backEmail();
		$uAddress=$u->backAddress();
		$uBDate=$u->backBDate();
		$msg=$this->message;
		$this->message='';
		$html=<<<PHTMLCODE

			<center><b><font color=red>{$msg}</font></b></center>
			<FORM name="{$this->_fullname}" method="post">
				Name: <input type="input" name="Name" value="$uName" /><br />
				Address: <input type="input" name="Address" value="$uAddress" /><br />
				Birth Date: <input type="input" name="BDate" value="$uBDate" /><FONT color=gray>yyy/mm/dd e.g. 1987/07/27</FONT><br />
				Current Password:<FONT color=red>*</FONT> <input type="password" name="OldPassword">
				<hr width=300px align=left />
				New Password: <input type="password" name="NewPassword"><FONT color=gray>leave blank if you dont want to change password</FONT><br />
				Confirm Password: <input type="password" name="ConfirmPassword">
				<hr width=300px align=left />
				<input type="hidden" name="_message" value="frame_applyChanges" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="Apply" type = "button" onclick = 'JavaScript:sndmsg("{$this->_fullname}")' class="press" style="margin-top: 10px; margin-right: 50px;" />
				<input value ="Cancel" type = "button" onclick = 'JavaScript:sndmsg("{$this->_fullname}cancel")' class="press" style="margin-top: 10px; margin-right: 50px;" />
			</FORM>
			<FORM name="{$this->_fullname}cancel" method="post">
				<input type="hidden" name="_message" value="frame_cancelChanges" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
			</FORM>
		
PHTMLCODE;

		return $html;
	}
	function onApplyChanges($info){
		// $info indexes: Name, Address, BDate, NewPassword, ConfirmPassword, OldPassword
		if($info['NewPassword']==$info['ConfirmPassword']){
			$u=new user("");
			$info=array("email"=>$u->backEmail(),  "Pass"=>$info['OldPassword'],  "NewPass"=>$info['NewPassword'],  "Address"=>$info['Address'],  "BDate"=>$info['BDate'],  "Name"=>$info['Name']);
			$this->message=$u->updateUserInfo($info);
		}else{
			$this->message='new password does not match with confirm password';
		}
		if($this->message==='ok')	{$this->_bookframe("frmView");$this->message='';}	else{$this->_bookframe("frmEdit");}
	}
	function onCancelChanges($info){
		$this->_bookframe("frmView");
	}
	function onEditInfo($info){
		$this->_bookframe("frmEdit");
	}

}

?>