<?PHP

/*
	Compiled by bizLang compiler version 1.3 (Jan 2 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}

	Author:	Reza Moussavi
	Date:	1/26/2010
	Version:1.2

*/

class user {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $userUID;
	var $email;
	var $loggedIn;

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

		if(!isset($_SESSION['osNodes'][$fullname]['userUID']))
			$_SESSION['osNodes'][$fullname]['userUID']='';
		$this->userUID=&$_SESSION['osNodes'][$fullname]['userUID'];

		if(!isset($_SESSION['osNodes'][$fullname]['email']))
			$_SESSION['osNodes'][$fullname]['email']='';
		$this->email=&$_SESSION['osNodes'][$fullname]['email'];

		if(!isset($_SESSION['osNodes'][$fullname]['loggedIn']))
			$_SESSION['osNodes'][$fullname]['loggedIn']='';
		$this->loggedIn=&$_SESSION['osNodes'][$fullname]['loggedIn'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']=user;
	}

	function sleep(){
		$_SESSION['osNodes'][$this->_fullname]['slept']=true;
	}

	function __destruct() {
		if($this->_tmpNode or !isset($_SESSION['osNodes'][$this->_fullname]['slept']))
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['slept']);
	}


	function message($message, $info) {
		switch($message){
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


    function logout() {
        $this->loggedIn = 0;
        osBroadcast("use_logout", array());
		osBookUser(array());
    }
    function isLoggedin() {
        return $this->loggedIn;
    }
    function backUID($email) {
        return $this->userUID;
    }
    function sendVerificationCode() {
        $this->sendEmail($this->email, "Verify", $this->createVerificationCode());
    }
    function validate($verificationCode) {        
        query("SELECT * FROM user_info WHERE userUID='" . $this->userUID . "' ;");
        if ($row = fetch()) {
            //correct code
            if ($verificationCode == $row["verificationCode"]) {
                query("UPDATE user_info SET verificationCode='0' WHERE userUID = '" . $this->userUID . "';");
                $this->loggedIn = 1;
                return TRUE;
            }
            //wrong code
            else
            {
                $this->loggedIn = 3;
                return FALSE;
            } 
        }
        //shouldn't be possible
        else
        {
            echo 'this should really not be possible...';
        }
    }
    /*     * **************************General Biz functionality*************************** */
    function login($email, $password) {        
        //First check if the email even exists in the database...
        query("SELECT * FROM user_info WHERE email='" . $email . "' AND biznessUID= '" . osBackBizness() . "';");
        $row = fetch();
        
        //Didn't exist...
        if (count($row) == 1) {
            $this->loggedIn = -2;
            return -2;
        }
        //Email did exist
        else
        {
            $hashPassword = $this->sha1Hash($email,$password);
            $s = "SELECT * FROM user_info WHERE email='" . $email . "' AND password='" . $hashPassword . "'  AND biznessUID= '" . osBackBizness() . "';";
            query($s);
            
            if ($row = fetch()) {
                $this->email = $email;
                $this->userUID = $row["userUID"];
                
                if ($row["verificationCode"] == '0') {
                    // login succefull
                    $this->loggedIn = 1;
                    
                    // let bizes know we're logged in!
                    osBroadcast("login", array("email" => $this->email, "userUID" => $this->userUID));
					osBookUser(array("email" => $this->email, "UID" => $this->userUID, "name"=>$this->email));
                    return 1;
                } else {
                    //login succefull but need to validation
                    $this->loggedIn = 2;
                    return 2;
                }
            }
            else
            {
                //login failed
                $this->loggedIn = -1;
                return -1; 
            }
        }
        
    }
    function add($email, $password, $passwordagain) {        
        //check if the email is already registered 
        query("SELECT * FROM user_info WHERE email='" . $email . "' ;");
        if ($row = fetch())
        {
            if ($email == $row["email"]) {
                return -1; //user already exist
            }
        }
        //if not, proceed! 
        else
        {
            if($password == $passwordagain)
            {
                //save the new user in the database
                $vcode = $this->createVerificationCode();
                $hashPassword = $this->sha1Hash($email,$password);
                query("INSERT INTO user_info (email,password,verificationCode,biznessUID) VALUES ('" . $email . "', '" . $hashPassword . "','" . $vcode . "','".osBackBizness()."');");
                
                // A welcome message to the user...
                $msg = "Welcome! Please verify your account using this code: ".$vcode;
                
                //send an email to the user. FIX MAILING FUNCTION!
                $this->sendEmail($email, "Welcome to buziness!", $msg);
                
                
                //to get the fresh userUID...
                query("SELECT * FROM user_info WHERE email='" . $email . "' ;");
                if ($row = fetch())
                    {
                        $this->userUID = $row['userUID'];
                    }
                $this->loggedIn = 2;
                
                //All set - user added and logged in!
                return 1; 
            }
            //passwords didn't match...
            else
            {
                return -2;
            }
        }
    }
    function backUIDByEmail($email) {
        query("SELECT * FROM user_info WHERE email='" . $email . "' ;");
        if ($row = fetch()) {
            return $row["userUID"];
        } else {
            return -1;
        }
    }
    function sendNewPassword($email) {
        query("SELECT * FROM user_info WHERE email='" . $email . "';");
        if ($row = fetch()) {
                $password = $this->generateRandomPassword();
                $hashPassword = $this->sha1Hash($email,$password);
                query("UPDATE user_info SET password = '" . $hashPassword . "' WHERE email = '" . $email . "';");
                $this->sendEmail($email, "New Password", $password);
                return TRUE; //email succefully sent
        }
        return FALSE; //email wasn't found in db
    }
    /*     * **************************Internal Biz functionality*************************** */
    private function sha1Hash($email,$password) {
        $hashPassword = sha1($email.$password);
        return $hashPassword;
    }
    private function sendEmail($to, $title, $msgToSend) {
        /*
         * Needs to be reworked...
         */
        mail($to, $title, $msgToSend, "From: Bizness");
    }
    private function createRandomChars($toSelectFrom, $length) {
        $i = 0;
        $pass = '';
        while ($i < $length) {
            $num = rand(0, strlen($toSelectFrom) - 1);
            $tmp = substr($toSelectFrom, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }
        return $pass;
    }
    private function generateRandomPassword() {
        $toSelectFrom = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789";
        return $this->createRandomChars($toSelectFrom, 8);
    }
    private function createVerificationCode() {
        $toSelectFrom = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return $this->createRandomChars($toSelectFrom, 10);
    }

}

?>