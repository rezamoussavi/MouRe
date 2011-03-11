<?PHP

/*
	Compiled by bizLang compiler version 2.0 (March 4 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}
	1.4: {multi parameter in link message}
	1.5: {multi secName support: frm/frame, msg/messages,fun/function/phpfunction}
	2.0: {upload bothe biz and php directly to server (ready to use)}

	Author:		Reza Moussavi
	Version:	1.1
	Date:		1/26/2011
	TestApproval: none
	-------------------
	Author:	Reza Moussavi
	Date:	12/30/2010
	Version:1.0

*/
require_once '../biz/user/user.php';

class login {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables
	var $signupSuccess;
	var $newPasswordSuccess;

	//Nodes (bizvars)
	var $userShow;

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
			$_SESSION['osMsg']['frame_loginBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_logoutBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_displaySignupForm'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_requestNewPassword'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_displayForgotForm'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_signup'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_validate'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_home'][$this->_fullname]=true;
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->userShow=new user($this->_fullname.'_userShow');

		if(!isset($_SESSION['osNodes'][$fullname]['signupSuccess']))
			$_SESSION['osNodes'][$fullname]['signupSuccess']='';
		$this->signupSuccess=&$_SESSION['osNodes'][$fullname]['signupSuccess'];

		if(!isset($_SESSION['osNodes'][$fullname]['newPasswordSuccess']))
			$_SESSION['osNodes'][$fullname]['newPasswordSuccess']='';
		$this->newPasswordSuccess=&$_SESSION['osNodes'][$fullname]['newPasswordSuccess'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='login';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_loginBtn':
				$this->onLoginBtn($info);
				break;
			case 'frame_logoutBtn':
				$this->onLogoutBtn($info);
				break;
			case 'frame_displaySignupForm':
				$this->onDisplaySignupForm($info);
				break;
			case 'frame_requestNewPassword':
				$this->onRequestNewPassword($info);
				break;
			case 'frame_displayForgotForm':
				$this->onDisplayForgotForm($info);
				break;
			case 'frame_signup':
				$this->onSignup($info);
				break;
			case 'frame_validate':
				$this->onValidate($info);
				break;
			case 'frame_home':
				$this->onHome($info);
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
				$_style=' style="float:left; width:200;" ';
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


	function onLoginBtn($info){
		$this->userShow->login($info['email'], $info['password']);
		$this->_bookframe("frm");
	}
	function onLogoutBtn($info){
		$this->userShow->logout();
		$this->_bookframe("frm");
	}
	function onDisplaySignupForm($info){
		$this->userShow->loggedIn = -3;
		$this->_bookframe("frm");
	}
	function onRequestNewPassword($info){
		$req = $this->userShow->sendNewPassword($info['email']);
		if($req){
			$this->newPasswordSuccess = 1;
		}else{
			$this->newPasswordSuccess = -1;
		}
		$this->_bookframe("frm");
	}
	function onDisplayForgotForm($info){
		$this->userShow->loggedIn = -4;
		$this->_bookframe("frm");
	}
	function onSignup($info){
		if($info['email'] != "" && $info['password'] != "" && $info['passwordagain'] != "")
		{
			$signup = $this->userShow->add($info['email'], $info['password'], $info['passwordagain']);
			switch($signup){
				case 1:
					$this->signupSuccess = 1;
					$this->userShow->loggedIn = 2;
					break;
				case -1:
					$this->signupSuccess = -1;
					$this->userShow->loggedIn = -3;
					break;
				case -2:
					$this->signupSuccess = -2;
					$this->userShow->loggedIn = -3;
					break;
				default:
					echo '##### weird...';
					break;
			}
		}
		$this->_bookframe("frm");
	}
	function onValidate($info){
		$this->userShow->validate($info["validationCode"]);
		$this->_bookframe("frm");
	}
	function onHome($info){
		$this->userShow->loggedIn = 0;
		$this->_bookframe("frm");
	}
	function frm(){
		$html='';
		switch($this->userShow->loggedIn)
		{
			// "no"
			case 0:
				$html = $this->showLoginForm(0);
				break;
			case -1:
				$html = $this->showLoginForm(-1);
				break;
			case -2:
				$html = $this->showLoginForm(-2);
				break;
			case -3:
				$html = $this->showSignupForm();
				break;
			case -4:
				$html = $this->showForgotForm();
				break;
			// "yes"
			case 1:
				$html = $this->showWelcome();
				break;
			case 2:
				$html = $this->showValidation('clean');
				break;
			case 3:
				$html = $this->showValidation('error');
				break;
			default:
				$html = "error in show()";
				break;
		}
		return $html;
	}
	function showLoginForm($mode){
		//some data...
		$formName = $this->_fullname."login";
		$formNameSignup = $this->_fullname."displaySignupForm";
		$formNameForgot = $this->_fullname."displayForgotForm";
		$modehtml = "";
		switch($mode){
			case 0:
				$modehtml = "";
				break;
			case -1:
				$modehtml = "Incorrect password!";
				break;
			case -2:
				$modehtml = "Account does not exist";
				break;
			default:
				$modehtml = "";
				break;
		}
		//pack html variable
		$html =<<<PHTML
			<h2>Login</h2>
			<h5>$modehtml</h5>
			<form name="$formName" method="post" >
				<div style="width: 50px; margin-top: 10px;">Email </div> <input type="input" name="email" /><br />
				<div style="width: 50px; margin-top: 10px;">Password </div> <input type="password" name="password" /> <br />
				<input type="hidden" name="_message" value="frame_loginBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<div style="width: 180px;">
				<input value ="Login" type = "button" onclick = 'JavaScript:sndmsg("$formName")' class="press" style="margin-top: 10px; margin-right: 50px;" />
				</div>
			</form>
			<form name="$formNameSignup" method="post" >
				<input type="hidden" name="_message" value="frame_displaySignupForm" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<span>No account yet?</span>
				<input value ="sign up!" type = "button" onclick = 'JavaScript:sndmsg("$formNameSignup")' class="press" style="margin-top: 10px; margin-right: 0px;" />
			</form>
			<form name="$formNameForgot" method="post" >
				<input type="hidden" name="_message" value="frame_displayForgotForm" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<span>Password lost?</span>
				<input value ="get a new!" type = "button" onclick = 'JavaScript:sndmsg("$formNameForgot")' class="press" style="margin-top: 10px; margin-right: 0px;" />
			</form>
PHTML;
		return $html;
    }
    function showSignupForm(){
		$formName = $this->_fullname."signup";
		$formNameHome = $this->_fullname."home";
		$msg = "";
		switch($this->signupSuccess){
			case 1:
				$msg = "";
				break;
			case -1:
				$msg = "There is already an account registered using this email.";
				break;
			case -2:
				$msg = "The passwords you entered didn't match. Try again!";
				break;
		}
		$html =<<<PHTML
			<h2>Signup!</h2> 
			<h5>$msg</h5>
			<form name="$formName" method="post" >
				<div style="width: 100px; margin-top: 10px;">Email </div> <input type="input" name="email" /><br />
				<div style="width: 100px; margin-top: 10px;">Password </div> <input type="password" name="password" /><br />
				<div style="width: 100px; margin-top: 10px;">Password Again </div> <input type="password" name="passwordagain" /><br />
				<input type="hidden" name="_message" value="frame_signup" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="sign up!" type = "button" onclick = 'JavaScript:sndmsg("$formName")' class="press"  style="margin-top: 10px;"><br />
			</form>
			<form name="$formNameHome" method="post" >
				<input type="hidden" name="_message" value="frame_home" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<span>Already a user?</span>
				<input value ="login!" type = "button" onclick = 'JavaScript:sndmsg("$formNameHome")' class="press" style="margin-top: 10px; margin-right: 0px;" />
			</form>
PHTML;
		return $html;
	}
	function showForgotForm(){
		$formName = $this->_fullname."requestNewPassword";
		$formNameHome = $this->_fullname."home";
		$msg = "";
        switch($this->newPasswordSuccess){
			case -1:
				$msg = "Account does not exist.";
				break;
			case 1:
				$msg = "Check your email for the new password!";
				break;
			default:
				break;
		}
		$html = <<<PHTML
			<h2>Get a new password</h2> 
			<h5>$msg</h5>
PHTML;
			if($this->newPasswordSuccess < 1){
				$html .= <<<PHTML
				<form name="$formName" method="post" >
					<div style="width: 100px; margin-top: 10px;">Email </div> <input type="input" name="email" /><br />
					<input type="hidden" name="_message" value="frame_requestNewPassword" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input value ="send" type = "button" onclick = 'JavaScript:sndmsg("$formName")' class="press"  style="margin-top: 10px;"><br />
				</form>
PHTML;
			}
			$html .= <<<PHTML
			<form name="$formNameHome" method="post" >
				<input type="hidden" name="_message" value="frame_home" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<span>Go back </span>
				<input value ="home" type = "button" onclick = 'JavaScript:sndmsg("$formNameHome")' class="press" style="margin-top: 10px; margin-right: 0px;" />
			</form>
PHTML;
		return $html;
	}
    function showValidation($mode){
		//important to add the div thingy here so that the ajax knows what to update :)
		$formNameLogout = $this->_fullname."logout";
		$formNameValidate = $this->_fullname."validate";
		if($mode == 'clean'){
			$failmsg = "";
		}else if($mode == 'error'){
			$failmsg = "incorrect code.";
		}
		$html = '<h5>You need to validate your account. Check your email!</h5>';
		$html .= <<<PHTML
			<h3 style="margin-bottom: 2px;">Logout</h3>
			<form name="$formNameLogout" method="post" >
				<input type="hidden" name="_message" value="frame_logoutBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="Logout" type = "button" onclick = 'JavaScript:sndmsg("$formNameLogout")'  style="margin-top: 0px;"><br />
			</form>
			<h5>$failmsg</h5>
			<form name="$formNameValidate" method="post" >
				<div style="width: 100px; margin-top: 10px;">Validation code </div> <input type="input" name="validationCode" /><br />
				<input type="hidden" name="_message" value="frame_validate" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value ="Validate!" type = "button" onclick = 'JavaScript:sndmsg("$formNameValidate")'  style="margin-top: 10px;"><br />
			</form>
PHTML;
		return $html;
	}
	function showWelcome(){
		//important to add the div thingy here so that the ajax knows what to update :)
		$formName = $this->_fullname."logout";
		$html = <<<PHTML
				<h2>Welcome sir!</h2>
				<form name="$formName" method="post" >
					<input type="hidden" name="_message" value="frame_logoutBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input value ="Logout" type = "button" onclick = 'JavaScript:sndmsg("$formName")'  class="press" style="margin-top: 0px;"><br />
				</form>
PHTML;
		return $html;
	}

}

?>