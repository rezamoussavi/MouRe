<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	6/03/2011
	Version:1.5
	---------------------
	Author:	Reza Moussavi
	Date:	2/16/2011
	Version:1.3
	---------------------
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
	var $userName;
	var $email;
	var $loggedIn;
	var $BDate;
	var $Address;
	var $role;

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

		if(!isset($_SESSION['osNodes'][$fullname]['userName']))
			$_SESSION['osNodes'][$fullname]['userName']='';
		$this->userName=&$_SESSION['osNodes'][$fullname]['userName'];

		if(!isset($_SESSION['osNodes'][$fullname]['email']))
			$_SESSION['osNodes'][$fullname]['email']='';
		$this->email=&$_SESSION['osNodes'][$fullname]['email'];

		if(!isset($_SESSION['osNodes'][$fullname]['loggedIn']))
			$_SESSION['osNodes'][$fullname]['loggedIn']='';
		$this->loggedIn=&$_SESSION['osNodes'][$fullname]['loggedIn'];

		if(!isset($_SESSION['osNodes'][$fullname]['BDate']))
			$_SESSION['osNodes'][$fullname]['BDate']='';
		$this->BDate=&$_SESSION['osNodes'][$fullname]['BDate'];

		if(!isset($_SESSION['osNodes'][$fullname]['Address']))
			$_SESSION['osNodes'][$fullname]['Address']='';
		$this->Address=&$_SESSION['osNodes'][$fullname]['Address'];

		if(!isset($_SESSION['osNodes'][$fullname]['role']))
			$_SESSION['osNodes'][$fullname]['role']='';
		$this->role=&$_SESSION['osNodes'][$fullname]['role'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='user';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
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
	}


//########################################
//         YOUR FUNCTIONS GOES HERE
//########################################


	function changePass($old,$new){
		$u=osBackUser();
		$hashPass = $this->sha1Hash($u['email'],$old);
		$newPass = $this->sha1Hash($u['email'],$new);
		query("SELECT * FROM user_info WHERE password='$hashPass'");
		if(fetch()){
			query("UPDATE user_info SET password='$newPass' WHERE password='$hashPass'");
			return true;
		}else
			return false;
	}
	function bookInfo($info){
		if(is_array($info)){
			if(isset($info['email'])&&isset($info['Password'])){
				$hashPass = $this->sha1Hash($info['email'],$info['Password']);
				query("SELECT * FROM user_info WHERE email='".$info['email']."' AND password='".$hashPass."'");
				$passcheck=fetch();
				if(!is_array($passcheck)) return "Incorrect Password!";
				$q="UPDATE user_info SET email='$info[email]'";
				if(isset($info['RealName']))
					$q.=" , userName='$info[RealName]' ";
				if(isset($info['PostalCode']))
					$q.=" , PostalCode='$info[PostalCode]' ";
				if(isset($info['Country']))
					$q.=" , Country='$info[Country]' ";
				if(isset($info['BDate']))
					$q.=", BDate='$info[BDate]'";
				if(isset($info['Address']))
					$q.=" , Address='$info[Address]'";
				$q.="  WHERE password='$hashPass'";
				query($q);
				$u=osBackUser();
				$u['email']=$info['email'];
				if(isset($info['RealName']))
					$u['userName']=$info['RealName'];
				if(isset($info['BDate']))
					$u['BDate']=$info['BDate'];
				if(isset($info['Address']))
					$u['Address']=$info['Address'];
				if(isset($info['PostalCode']))
					$u['PostalCode']=$info['PostalCode'];
				if(isset($info['Country']))
					$u['Country']=$info['Country'];
				osBookUser($u);
				return 1;
			}else return "Password required!";
		}else return "Internal error, try again!";
	}
	// $info = array("email"=>xxx,  "Pass"=>xxx,  "NewPass"=>xxx,  "Address"=>xxx,  "BDate"=>xxx,  "Name"=>xxx)
	function updateUserInfo($info){
		$email='';	$pass='';	$message='ok';
		//#######################
		// check if email and pass included
		// and have values also if there is a bdate if it is in right format
		// else RETURN propper error message
		if(isset($info['email']))	{$email=$info['email'];}	else{return 'Enter email!';}
		if(isset($info['Pass']))	{$pass=$info['Pass'];}		else{return 'Enter Password';}
		if(strlen(trim($pass))<1)	{return 'Enter Password';}
		$bdate=isset($info['BDate'])?$this->convertBDate($info['BDate']):'';
		if($bdate=='error')	{return 'Incorrect BDate Format';}
		//#######################
		// Extract other info from $info
		// put empty string if is not set
		$newpass=isset($info['NewPass'])?$info['NewPass']:'';
		$address=isset($info['Address'])?$info['Address']:'';
		$name=isset($info['Name'])?$info['Name']:'';
		//#######################
		// update user if pass is correct
		//
		$hashPassword = $this->sha1Hash($email,$pass);
		query("SELECT * FROM user_info WHERE email='" . $email . "' AND password='" . $hashPassword . "'  AND biznessUID= '" . osBackBizness() . "';");
		if($row=fetch()){// Password correct
			$UID=$row['userUID'];
			$newpass = (strlen(trim($newpass))<1)?$hashPassword:$this->sha1Hash($email,$newpass);
			$q="UPDATE user_info SET password='".$newpass."', Address='".$address."', BDate='".$bdate."', userName='".$name."' WHERE userUID='".$UID."'";
			if(!query($q)){
				$message='DB ERROR:<br />'.$q;
			}
		}else{//Incorrect password
			$message='Incorrect Password';
		}
		if($message=='ok'){
			osBookUser(array("email" => $email, "userUID" => $UID, "Address"=>$address, "userName"=>$name, "BDate"=>$info['BDate']));
		}
		return $message;
	}
	function convertBDate($b){
		$b=trim($b);
		if(strlen($b)!=10)
			return 'error';
		if(substr($b,4,1)!="/" || substr($b,7,1)!="/")
			return 'error';
		return substr($b,0,4).substr($b,5,2).substr($b,8,2);
	}
	function backName(){
		$n='{no name}';
		$u=osBackUser();
		if(isset($u['userName'])){
			$n=$u['userName'];
		}
		return $n;
	}
	function backEmail(){
		$n='{no email}';
		$u=osBackUser();
		if(isset($u['email'])){
			$n=$u['email'];
		}
		return $n;
	}
	function backAddress(){
		$n='{no address}';
		$u=osBackUser();
		if(isset($u['Address'])){
			$n=$u['Address'];
		}
		return $n;
	}
	function backBDate(){
		$n='{no b-date}';
		$u=osBackUser();
		if(isset($u['BDate'])){
			$n=$u['BDate'];
		}
		return $n;
	}
	function addSlash2BDate($b){
		return substr($b,0,4)."/".substr($b,4,2)."/".substr($b,6,2);
	}
    function logout() {
        $this->loggedIn = 0;
		osBookUser(array("email" => "", "userUID" => -1, "name"=>""));
        osBroadcast("user_logout", array());
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
                $this->userName = $row["userName"];
                if ($row["verificationCode"] == '0') {
                    // login succefull
                    $this->loggedIn = 1;
                    
                    // let bizes know we're logged in!
					osBookUser(array("email" => $this->email, "userUID" => $this->userUID, "Address"=>$row["Address"], "balance"=>$row["balance"], "Country"=>$row["Country"], "PostalCode"=>$row["PostalCode"], "userName"=>$row["userName"], "role"=>$row["role"], "BDate"=>$row["BDate"]));
                    osBroadcast("user_login", array());
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
    function add($email, $password, $passwordagain, $userName, $Address, $Country, $PostalCode, $role) {
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
                query("INSERT INTO user_info (email,password,verificationCode,biznessUID,userName,Address,Country,PostalCode,role) VALUES ('" . $email . "', '" . $hashPassword . "','" . $vcode . "','".osBackBizness()."','".$userName."','".$Address."','".$Country."','".$PostalCode."','".$role."');");
                
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
	function backUserData($userUID){
		query("SELECT * FROM user_info WHERE userUID='".$userUID."'");
		return fetch();
	}
	function backAll(){
		$users=array();
		if(!osIsAdmin()){	return $users;	}
		query("SELECT * FROM user_info WHERE role='user'");
		while($row=fetch()){
			unset($row['password']);
			unset($row['verificationCode']);
			$users[]=$row;
		}
		return $users;
	}

}

?>