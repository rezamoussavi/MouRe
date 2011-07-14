<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	7/05/2010
	Version:1.0

*/
require_once 'biz/user/user.php';

class viewslogin {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $msg;
	var $email;
	var $password;

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
			$_SESSION['osMsg']['frame_loginBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_signupBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_doLoginBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_xBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_logoutBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_validateBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_doSignupBtn'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmMain';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		if(!isset($_SESSION['osNodes'][$fullname]['msg']))
			$_SESSION['osNodes'][$fullname]['msg']="";
		$this->msg=&$_SESSION['osNodes'][$fullname]['msg'];

		if(!isset($_SESSION['osNodes'][$fullname]['email']))
			$_SESSION['osNodes'][$fullname]['email']='';
		$this->email=&$_SESSION['osNodes'][$fullname]['email'];

		if(!isset($_SESSION['osNodes'][$fullname]['password']))
			$_SESSION['osNodes'][$fullname]['password']='';
		$this->password=&$_SESSION['osNodes'][$fullname]['password'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='viewslogin';
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
			case 'frame_signupBtn':
				$this->onSignupBtn($info);
				break;
			case 'frame_doLoginBtn':
				$this->onDoLoginBtn($info);
				break;
			case 'frame_xBtn':
				$this->onXBtn($info);
				break;
			case 'frame_logoutBtn':
				$this->onLogoutBtn($info);
				break;
			case 'frame_validateBtn':
				$this->onValidateBtn($info);
				break;
			case 'frame_doSignupBtn':
				$this->onDoSignupBtn($info);
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
			case 'frmMain':
				$_style=' class="log_sign"  ';
				break;
			case 'frmLogin':
				$_style=' class="log_sign"  ';
				break;
			case 'frmWelcome':
				$_style=' class="log_sign"  ';
				break;
			case 'frmSignup':
				$_style=' class="log_sign"  ';
				break;
			case 'frmValidation':
				$_style=' class="log_sign"  ';
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


	/**********************************
	*			Message Handler
	**********************************/
	function onLoginBtn($info){
		$this->_bookframe("frmLogin");
	}
	function onSignupBtn($info){
		$this->_bookframe("frmSignup");
	}
	function onDoLoginBtn($info){
		$u=new user("");
		$u->login($info['email'],$info['password']);
		switch($u->loggedIn){
			case -1:
				$this->msg="Incorrect Password";
				$this->_bookframe("frmMain");
				break;
			case -2:
				$this->msg="Account does not exist";
				$this->_bookframe("frmMain");
				break;
			case 1:
				$this->msg="";
				$this->_bookframe("frmWelcome");
				break;
			case 2:
				$this->msg="";
				$this->email=$info['email'];
				$this->password=$info['password'];
				$this->_bookframe("frmValidation");
				break;
		}
	}
	function onXBtn($info){
		$this->_bookframe("frmMain");
	}
	function onLogoutBtn($info){
		$u=new user("");
		$u->logout();
		$this->_bookframe("frmMain");
	}
	function onMyaccBtn($info){
		if($info['Page']=="show"){
			$data=array();
			osBroadcast("user_showMyAccount",$data);
		}
	}
	function onValidateBtn($info){
		$u=new user("");
		$u->login($this->email,$this->password);
		$u->validate($info['vcode']);
		$this->onDoLoginBtn(array("email"=>$this->email,"password"=>$this->password));
	}
	function onDoSignupBtn($info){
		if($info['email'] != "" && $info['password'] != "" && $info['passwordagain'] != "")
		{
			$u=new user("");
			$signup = $u->add($info['email'], $info['password'], $info['passwordagain'], $info['userName'], $info['Address'], $info['Country'], $info['PostalCode'],'user');
			switch($signup){
				case 1://All set - user added and logged in!
					$this->msg="Check Your email!";
					$this->_bookframe("frmMain");
					break;
				case -1://user already exist
					$this->msg="user already exist";
					$this->_bookframe("frmMain");
					break;
				case -2://passwords didn't match...
					$this->msg="passwords didn't match...";
					$this->_bookframe("frmMain");
					break;
				default:
					osLog('viewslogin',$this->_fullname,"##### weird...");
					$this->_bookframe("frmMain");
					break;
			}
		}
	}
	/**********************************
	*			FRAMES
	**********************************/
	function frmMain(){
		$suFrm=$this->_fullname."signup";
		$lgFrm=$this->_fullname."login";
		$msg=$this->msg;
		$this->msg="";
		return <<<PHTMLCODE

			<div style="height:40px;">$msg</div>
	        <ul>
	            <li>
					<form id="$suFrm" style="display:inline;">
						<input type="hidden" name="_message" value="frame_signupBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
	                	<a id="signup-link" href="#signup" onclick="JavaScript:sndmsg('$suFrm')">Sign up</a>
					</form>
	            </li>
	            <li>
					<form id="$lgFrm" style="display:inline;">
						<input type="hidden" name="_message" value="frame_loginBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
	                	<a id="login-link" href="#login" onclick="JavaScript:sndmsg('$lgFrm')">Log in</a>
					</form>
	            </li>
	        </ul>
		
PHTMLCODE;

	}
	function frmLogin(){
		$suFrm=$this->_fullname."signup";
		$lgFrm=$this->_fullname."dologin";
		$xFrm=$this->_fullname."x";
		return <<<PHTMLCODE

			<div style="height:40px;"></div>
	        <ul>
	            <li>
					<form id="$suFrm" style="display:inline;">
						<input type="hidden" name="_message" value="frame_signupBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
	                	<a id="signup-link" href="#signup" onclick="JavaScript:sndmsg('$suFrm')">Sign up</a>
					</form>
	            </li>
	            <li>
					<form id="" style="display:inline;">
	                	<a disabled id="login-link" >Log in</a>
					</form>
	            </li>
	        </ul>
            <div id="login_div" class="bloop">
			    <form name="$xFrm" style="float:right;">
			    	<input type="hidden" name="_message" value="frame_xBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
			    	<font size="1px" color="red"><a style="cursor:pointer;" onclick="JavaScript:sndmsg('$xFrm')">X</a></font>
			    </form>
                <form id="$lgFrm" method="post">
					<input type="hidden" name="_message" value="frame_doLoginBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
                    <div class="username_div">
                        <label class="user_lbl">Username: </label>
                        <input class="user_tf" name="email" type="text" />
                    </div>
                    <div class="password_div">
                        <label class="pass_lbl">Password: </label>
                        <input class="pass_tf" name="password" type="password" />
                    </div>
				     <div id="forgot_div">
                    	<a id="forgot" class="login_anc" href="javascript:my_function()">Forgot Password?</a>
                    </div>
                    <div id="login_btn_div">
                        <input class="form_btn" type="button" value="Login" onclick="JavaScript:sndmsg('$lgFrm')" />
                    </div>
                </form>
            </div>
		
PHTMLCODE;

	}
	function frmWelcome(){
		$u=osBackUser();
		$name=strlen($u['userName'])>1?$u['userName']:$u['email'];
		$balance=$u['balance'];
		$lgoutFrm=$this->_fullname."lgout";
		$d=array();
		$link=osBackPageLink("Myacc");
		return <<<PHTMLCODE

            <div id="pers_info">
                Welcome <span id="user_name">$name</span>!
                <div id="balance_div">Balance: <span id="balance_span">$balance $</span></div>
            </div>
			<ul>
				<li>
					<a id="signup-link" href="$link" >My Account</a>
				</li>
				<li>
					<form id="$lgoutFrm" style="display:inline;">
						<input type="hidden" name="_message" value="frame_logoutBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
						<a id="login-link" href="#logout" onclick="JavaScript:sndmsg('$lgoutFrm')">Log out</a>
					</form>
				</li>
			</ul>
		
PHTMLCODE;

	}
	function frmSignup(){
		$lgFrm=$this->_fullname."login";
		$dosuFrm=$this->_fullname."dodosignup";
		$xFrm=$this->_fullname."x";
		return <<<PHTMLCODE

			<div style="height:40px;"></div>
	        <ul>
	            <li>
					<form id="" style="display:inline;">
	                	<a id="signup-link" >Sign up</a>
					</form>
	            </li>
	            <li>
					<form id="$lgFrm" style="display:inline;">
						<input type="hidden" name="_message" value="frame_loginBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
	                	<a id="login-link" href="#login" onclick="JavaScript:sndmsg('$lgFrm')">Log in</a>
					</form>
	            </li>
	        </ul>
            <div id="signup_div" class="bloop">
			    <form name="$xFrm" style="float:right;">
			    	<input type="hidden" name="_message" value="frame_xBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
			    	<font size="1px" color="red"><a style="cursor:pointer;" onclick="JavaScript:sndmsg('$xFrm')">X</a></font>
			    </form>
            	<form id="$dosuFrm" method="post">
            		<input type="hidden" name="_message" value="frame_doSignupBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
            		<div class="username_div">
                        <label class="user_lbl">Email: </label>
                        <input class="user_tf" name="email" type="text" />
                    </div>
                    <div class="password_div">
                        <label class="pass_lbl">Password: </label>
                        <input class="pass_tf" name="password" type="password" />
                    </div>
                    <div class="password_div">Confirm: </label>
                        <input class="confirm_tf" name="passwordagain" type="password" />
                    </div>
                    <div id="signup_btn_div">
                        <input class="form_btn" type="button" value="Sign up" onclick="JavaScript:sndmsg('$dosuFrm')" />
                    </div>
            	</form>
            </div>
		
PHTMLCODE;

	}
	function frmValidation(){
		$suFrm=$this->_fullname."signup";
		$vFrm=$this->_fullname."ver";
		$xFrm=$this->_fullname."x";
		return <<<PHTMLCODE

			<div style="height:40px;"></div>
	        <ul>
	            <li>
					<form id="$suFrm" style="display:inline;">
						<input type="hidden" name="_message" value="frame_signupBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
	                	<a id="signup-link" href="#signup" onclick="JavaScript:sndmsg('$suFrm')">Sign up</a>
					</form>
	            </li>
	            <li>
					<form id="" style="display:inline;">
	                	<a disabled id="login-link" >Log in</a>
					</form>
	            </li>
	        </ul>
            <div id="login_div" class="bloop">
                <form id="$vFrm" method="post">
					<input type="hidden" name="_message" value="frame_validateBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
                    <div class="username_div">
                        <label class="user_lbl">Code: </label>
                        <input class="user_tf" name="vcode" type="text" />
                    </div>
					<input class="form_btn" type="button" value="Login" onclick="JavaScript:sndmsg('$vFrm')" />
                </form>
            </div>
		
PHTMLCODE;

	}

}

?>