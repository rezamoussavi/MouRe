<?PHP

/*
	Compiled by bizLang compiler version 1.5 (Feb 21 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}
	1.4: {multi parameter in link message}
	1.5: {multi secName support: frm/frame, msg/messages,fun/function/phpfunction}

	Author: Reza Moussavi
	Date:	02/21/2011
	Version: 1
	---------------------
	Author: Reza Moussavi
	Date:	02/07/2011
	Version: 0.1

*/
require_once '../biz/user/user.php';

class profile {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables

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
			$_SESSION['osMsg']['client_applyChanges'][$this->_fullname]=true;
			$_SESSION['osMsg']['client_cancelChanges'][$this->_fullname]=true;
			$_SESSION['osMsg']['?????_editInfo'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmView';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='profile';
	}

	function sleep(){
		$_SESSION['osNodes'][$this->_fullname]['slept']=true;
	}

	function __destruct() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'client_applyChanges':
				$this->onApplyChanges($info);
				break;
			case 'client_cancelChanges':
				$this->onCancelChanges($info);
				break;
			case '?????_editInfo':
				$this->onEditInfo($info);
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


	function frmView(){
		$html='';
		$uUID=osBackUser();
		if($uUID==-1){
			$html=<<<PHTMLCODE

				User not loggedin.
			
PHTMLCODE;

		}else{
			$u=new user();
			$u->bookUID($uUID);
			$html=<<<PHTMLCODE

				Name: {$u->backName()}<br>
				email: {$u->backEmail()}<br>
				Address: {$u->backAddress()}<br>
				Birth Date: {$u->backBDate()}<br>
				<FORM name="{$this->_fullname}" method="post">
					<input type="hidden" name="_message" value="frame_editInfo" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input value ="Edit" type = "button" onclick = 'JavaScript:sndmsg("{$this->_fullname}")' class="press" style="margin-top: 10px; margin-right: 50px;" />
				</FORM>
			
PHTMLCODE;

		}
		return $html;
	}
	function frmEdit(){
		$html=<<<PHTMLCODE

			<FORM name="{$this->_fullname}" method="post">
				<input type="hidden" name="_message" value="frame_applyChanges" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				Name: <input type="input" name="Name"><br />
				Address: <input type="input" name="Address"><br />
				Birth Date: <input type="input" name="BDate"><br />
				New Password: <input type="password" name="NewPassword"><br />
				Confirm Password: <input type="password" name="ConfirmPassword"><br />
				Old Password: <input type="password" name="OldPassword"><br />
				<input value ="Apply" type = "button" onclick = 'JavaScript:sndmsg("{$this->_fullname}")' class="press" style="margin-top: 10px; margin-right: 50px;" />
			</FORM>
			<FORM name="{$this->_fullname}cancel" method="post">
				<input type="hidden" name="_message" value="frame_cancelChanges" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="Cancel" type = "button" onclick = 'JavaScript:sndmsg("{$this->_fullname}cancel")' class="press" style="margin-top: 10px; margin-right: 50px;" />
			</FORM>
		
PHTMLCODE;

		return $html;
	}
	function onApplyChanges($info){
		// $info indexes: Name, Address, BDate, NewPassword, ConfirmPassword, OldPassword
		$uUID=osBackUser();
		if($uUID>0){
			$u=new user();
			$u->bookUID($uUID);
			$u->bookName($info['Name']);
			$u->bookAddress($info['Address']);
			$u->bookBDate($info['BDate']);
			$u->bookPassword($info['NewPassword']);
		}
		_bookFrame("frmView");
	}
	function onCancelChanges($info){
		_bookFrame("frmView");
	}
	function onEditInfo($info){
		_bookFrame("frmEdit");
	}

}

?>