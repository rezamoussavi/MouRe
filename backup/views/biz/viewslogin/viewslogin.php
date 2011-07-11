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
				$_style=' style="float:left; width:200; height:150px;border:1px dotted gray;" ';
				break;
			case 'frmLogin':
				$_style=' style="float:left; width:200; height:150px;border:1px dotted gray;" ';
				break;
			case 'frmWelcome':
				$_style=' style="float:left; width:200; height:150px;border:1px dotted gray;" ';
				break;
			case 'frmSignup':
				$_style=' style="float:left; width:200; height:150px;border:1px dotted gray;" ';
				break;
			case 'frmValidation':
				$_style=' style="float:left; width:200; height:150px;border:1px dotted gray;" ';
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
					$this->msg="Check Your email an login to set Verification code";
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

			$msg
			<div style="width:200;height:50px;margin-top:120;margin-left:50px;">
				<form id="$suFrm" style="display:inline;">
					<input type="hidden" name="_message" value="frame_signupBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="Signup" onclick="JavaScript:sndmsg('$suFrm')"/>
				</form>
				<form id="$lgFrm" style="display:inline;">
					<input type="hidden" name="_message" value="frame_loginBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="Login" onclick="JavaScript:sndmsg('$lgFrm')"/>
				</form>
			</div>
		
PHTMLCODE;

	}
	function frmLogin(){
		$suFrm=$this->_fullname."signup";
		$lgFrm=$this->_fullname."dologin";
		$xFrm=$this->_fullname."x";
		return <<<PHTMLCODE

			<div style="width:200;height:50px;margin-top:120;margin-left:50px;">
				<form id="$suFrm" style="display:inline;">
					<input type="hidden" name="_message" value="frame_signupBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="Signup" onclick="JavaScript:sndmsg('$suFrm')"/>
				</form>
				<form style="display:inline;">
					<input type="hidden" name="_message" value="frame_loginBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input disabled type="button" value="Login" />
				</form>
			</div>
			<div style="margin-left:50px;display:block;position:absolute;z-index:1;border:1px solid black;background-color:gray;width:160px;height:100px;">
				<form id="$lgFrm" method="post">
					<input type="hidden" name="_message" value="frame_doLoginBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					email: <input name="email" size=10><br />
					password: <input name="password" type="password" size=10><br />
					<input type="button" value="Login" onclick="JavaScript:sndmsg('$lgFrm')"/>
				</form>
				<form id="$xFrm" method="post">
					<input type="hidden" name="_message" value="frame_xBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="X" onclick="JavaScript:sndmsg('$xFrm')"/>
				</form>
			</div>
		
PHTMLCODE;

	}
	function frmWelcome(){
		$u=osBackUser();
		$name=$u['userName'];
		$balance=$u['balance'];
		$lgoutFrm=$this->_fullname."lgout";
		$d=array();
		$link=osBackPageLink("Myacc");
		return <<<PHTMLCODE

			Welcome $name<br />
			Balance $balance<br />
			<form id="$lgoutFrm" method="post" style="display:inline;">
				<input type="hidden" name="_message" value="frame_logoutBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input type="button" value="Logout" onclick="JavaScript:sndmsg('$lgoutFrm')"/>
			</form>			
			<a href="$link" > My Acc </a>
		
PHTMLCODE;

	}
	function frmSignup(){
		$lgFrm=$this->_fullname."login";
		$dosuFrm=$this->_fullname."dodosignup";
		$xFrm=$this->_fullname."x";
		return <<<PHTMLCODE

			<div style="width:200;height:50px;margin-top:120;margin-left:50px;">
				<form id="" style="display:inline;">
					<input disabled type="button" value="Signup" />
				</form>
				<form id="$lgFrm" style="display:inline;">
					<input type="hidden" name="_message" value="frame_loginBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="Login" onclick="JavaScript:sndmsg('$lgFrm')"/>
				</form>
			</div>
			<div style="margin-left:-150px;display:block;position:absolute;z-index:1;border:1px solid black;background-color:gray;width:200px;">
				<form id="$dosuFrm" method="post">
					<input type="hidden" name="_message" value="frame_doSignupBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<div style="width: 100px; margin-top: 10px;">Email </div> <input type="input" name="email" /><br />
					<div style="width: 100px; margin-top: 10px;">Real Name </div> <input type="input" name="userName" /><br />
					<div style="width: 100px; margin-top: 10px;">Country </div> <input type="input" name="Country" /><br />
					<div style="width: 100px; margin-top: 10px;">Address </div> <input type="input" name="Address" /><br />
					<div style="width: 100px; margin-top: 10px;">Postal Code </div> <input type="input" name="PostalCode" /><br />
					<div style="width: 100px; margin-top: 10px;">Password </div> <input type="password" name="password" /><br />
					<div style="width: 100px; margin-top: 10px;">Password Again </div> <input type="password" name="passwordagain" /><br />
					<input type="button" value="Sign Up" onclick="JavaScript:sndmsg('$dosuFrm')"/>
				</form>
				<form id="$xFrm" method="post">
					<input type="hidden" name="_message" value="frame_xBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="X" onclick="JavaScript:sndmsg('$xFrm')"/>
				</form>
			</div>
		
PHTMLCODE;

	}
	function frmValidation(){
		$suFrm=$this->_fullname."signup";
		$vFrm=$this->_fullname."ver";
		$xFrm=$this->_fullname."x";
		return <<<PHTMLCODE

			<div style="width:200;height:50px;margin-top:120;margin-left:50px;">
				<form id="$suFrm" style="display:inline;">
					<input type="hidden" name="_message" value="frame_signupBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="Signup" onclick="JavaScript:sndmsg('$suFrm')"/>
				</form>
				<form style="display:inline;">
					<input type="hidden" name="_message" value="frame_loginBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input disabled type="button" value="Login" />
				</form>
			</div>
			<div style="margin-left:50px;display:block;position:absolute;z-index:1;border:1px solid black;background-color:gray;width:160px;height:100px;">
				<form id="$vFrm" method="post">
					<input type="hidden" name="_message" value="frame_validateBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					Verification Code: <input name="vcode" size=10><br />
					<input type="button" value="Login" onclick="JavaScript:sndmsg('$vFrm')"/>
				</form>
				<form id="$xFrm" method="post">
					<input type="hidden" name="_message" value="frame_xBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="X" onclick="JavaScript:sndmsg('$xFrm')"/>
				</form>
			</div>
		
PHTMLCODE;

	}

}

?>