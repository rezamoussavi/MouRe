<?PHP

/*
	Compiled by bizLang compiler version 1.01

	Author:	Reza Moussavi
	Date:	12/30/2010
	Version:1.0

*/

class user {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $userUID;
	var $email;
	var $loggedIn;

	//Nodes (bizvars)

	function __construct(&$data) {
		if (!isset($data['sleep'])) {
			$data['sleep'] = true;
			$this->_initialize($data);
			$this->_wakeup($data);
		}else{
			$this->_wakeup($data);
		}
	}

	function _initialize(&$data){
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->userUID=&$data['userUID'];
		$this->email=&$data['email'];
		$this->loggedIn=&$data['loggedIn'];

	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			return;
		}
		switch($message){
			default:
				break;
		}
	}

	function broadcast($message, $info) {
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
        osBroadcast("logout", array());
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