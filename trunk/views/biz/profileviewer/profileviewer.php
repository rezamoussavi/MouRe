<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	5/6/2011
	Ver:		1.0

*/
require_once 'biz/user/user.php';

class profileviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $changepassmsg;
	var $personalinfomsg;

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
			$_SESSION['osMsg']['frame_updateInfo'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_changePassword'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		if(!isset($_SESSION['osNodes'][$fullname]['changepassmsg']))
			$_SESSION['osNodes'][$fullname]['changepassmsg']="";
		$this->changepassmsg=&$_SESSION['osNodes'][$fullname]['changepassmsg'];

		if(!isset($_SESSION['osNodes'][$fullname]['personalinfomsg']))
			$_SESSION['osNodes'][$fullname]['personalinfomsg']="";
		$this->personalinfomsg=&$_SESSION['osNodes'][$fullname]['personalinfomsg'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='profileviewer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_updateInfo':
				$this->onUpdateInfo($info);
				break;
			case 'frame_changePassword':
				$this->onChangePassword($info);
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
			case 'frm':
				$_style=' class="profile_area_div"  ';
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


	/********************************************
	*		FRAME
	********************************************/
	function frm(){
		$user=osBackUser();
		$formName=$this->_fullname."ApplyBtn";
		$formPass=$this->_fullname."Pass";
		$userBox='<input class="persinf_inp" id="profile_name_inp" type="text" size="30" name="userName"/>';
		$bdBox='<input class="persinf_inp" id="profile_birth_inp" type="text" size="30" name="BDate"/>';
		if(isset($user['userName'])){
			if(strlen($user['userName'])>2){
				$userBox=<<<PHTMLCODE
<input class="persinf_inp" id="profile_name_inp" type="text" size="30" name="user-Name" value="{$user['userName']}" disabled/>
PHTMLCODE;

			}
		}
		if(isset($user['BDate'])){
			if(strlen($user['BDate'])>2){
				$bdBox=<<<PHTMLCODE
<input class="persinf_inp" id="profile_birth_inp" type="text" size="30" name="B-Date" value="{$user['BDate']}" disabled/>
PHTMLCODE;

			}
		}
		if(!osUserLogedin()){
			$html="Please Login First";
		}else{
			$html=<<<PHTMLCODE

				{$this->personalinfomsg}
				<span class="profile_title_span">Personal Information</span> 
				<form id="$formName" class="persinf_frm" method="post">
					<input type="hidden" name="_message" value="frame_updateInfo" /><input type = "hidden" name="_target" value="{$this->_fullname}" /><br />
					<label class="bold" id="profile_email_lbl">email </label> 
					<input class="persinf_inp" id="profile_email_inp" type="text" size="30" value="{$user['email']}" disabled/> 
					<br /><br /> 					
					<label class="bold" id="profile_name_lbl">Full name <span class="red_star">*</span></label> 
					{$userBox}
					<br /><br /> 
					<label class="bold" id="profile_birth_lbl">Birth date <span class="red_star">*</span></label> 
					{$bdBox}
					<br /><br /> 					
					<label class="notes" id="profile_note_1">* These information must be real for future security checkings (can be filled once)</label> 
					<br /><br /> 
					<label class="bold" id="profile_address_lbl">Full Address</label> 
					<input class="persinf_inp" id="profile_address_inp" type="text" size="30" name="Address" value="{$user['Address']}"/> 
					<br /><br /> 
					<label class="bold" id="profile_country_lbl">Country</label> 
					<input class="persinf_inp" id="profile_country_inp" type="text" size="30" name="Country" value="{$user['Country']}"/> 
					<br /><br /> 
					<label class="bold" id="profile_zipcode_lbl">Postal Code</label> 
					<input class="persinf_inp" id="profile_zipcode_inp" type="text" size="30" name="PostalCode" value="{$user['PostalCode']}" /> 
					<br /><br /> 
					<input class="persinf_inp" id="profile_save_btn" type="button" value="Save" onclick='document.getElementById("{$this->_fullname}getPassword").style.display="block";document.getElementById("profile_save_btn").style.display="none";'/> 
					<div class="persinf_confirm_div" id="{$this->_fullname}getPassword"> 
						<label class="bold">Password: </label> 
						<input type="password" size="30" name="Password"/> 
						<input class="persinf_inp" type="button" value="Confirm" onclick='Javascript:sndmsg("$formName")'/> 
					</div> 
					<br /><br /> 
				</form> 
				{$this->changepassmsg}
				<span class="profile_title_span">Change Password</span> 
				<form id="$formPass" class="change_pass_frm" method="post"> 
					<input type="hidden" name="_message" value="frame_changePassword" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<label class="bold" id="newpass_lbl">New Password</label> 
					<input class="persinf_inp" id="newpass_inp" name="NewPass1" type="password" size="30" /> 
					<br /><br /> 
					<label class="bold" id="conf_newpass_lbl">Confirm New Pass</label> 
					<input class="persinf_inp" id="conf_newpass_inp" name="NewPass2" type="password" size="30" /> 
					<br /><br /> 
					<label class="bold" id="oldpass_lbl">Current Pass</label> 
					<input class="persinf_inp" id="oldpass_inp" name="Password" type="password" size="30" /> 
					<br /><br /> 
					<input class="persinf_inp" class="bold" id="confpass_btn" type="button" value="Save" onclick='Javascript:sndmsg("$formPass")'/> 
				</form> 
			
PHTMLCODE;

		}
		$this->personalinfomsg="";
		$this->changepassmsg="";
		return $html;
	}
	/********************************************
	*		Message Handlers
	********************************************/
	function onChangePassword($info){
		//check password match
		if($info['NewPass1']!=$info['NewPass2']){
			$this->changepassmsg="<center><b><font color=red>Password and Confirm password dismatch! try again</font></b></center>";
			return;
		}
		//applypass
		$u=new user("");
		$this->changepassmsg=($u->changePass($info['Password'],$info['NewPass1']))?"<b><center><font color=green>Password has been Changed!</font></center></b>":"<b><center><font color=red>ERROR! Password NOT Changed!</font></center></b>";
	}
	function onUpdateInfo($info){
		$curU=osBackUser();
		$info['email']=$curU['email'];
		if(isset($curU['userName'])){
			if(strlen($curU['userName'])>1){
				$info['userName']=$curU['userName'];
			}
		}
		if(isset($curU['BDate'])){
			if(strlen($curU['BDate'])>1){
				$info['BDate']=$curU['BDate'];
			}
		}
		$u=new user("");
		$ret=$u->bookInfo($info);
		if($ret==1)
			$this->personalinfomsg="<center><b><font color=green>Update Successfully!</font></b></center>";
		else
			$this->personalinfomsg="<center><b><font color=red>$ret</font></b></center>";
		$this->_bookframe("frm");
	}

}

?>