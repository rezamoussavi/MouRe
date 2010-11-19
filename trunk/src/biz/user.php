<?php

//DB Info : Specify what informaton this BIZ is going to use from DB




class user {

    //all of our biz classes should define these two variables
    var $_fullname;
    var $_bizname;
    var $_parent;
    /*     * **************************FIELDS*************************** */
    //CALSS FIELDS
    var $userUID;
    var $email;
    var $loggedin;

    //FIELDS WHICH HAVE TYPE OF OTHER BIZES



    /*     * **************************CONSTRUCTOR*************************** */

    //function __construct($data){
    //SET CLASS FIELDS WITH $data
    //IF REQUIRED BIZES HAVE NOT BEEN USED,INITIALIZE
    /* if(!(isset($data["bizVN"]))){
      $data[$bizVN][$_fullname] = (this->$_fullname."_".$bizVN);
      $data[$bizVN][$_bizname] = $_bizname;
      }
      this->$bizVN = new "$bizVN"(&$data[$bizVN]);
      } */



    //}
    function __construct($data) {
        $this->_bizname = &$data["bizname"];
        $this->_fullname = &$data["fullname"];
        $this->email = &$data["email"];
        $this->loggedin = &$data["loggedin"];
        $this->userUID = &$data["userUID"];
    }

    /*     * **************************MESSAGE HANDELING*************************** */

    /* function message($to,$message,$info) {
      this->$bizVN->message(&$to,&$message,&$info);
      if($to != this->$-fullname){
      return;
      }
      show_content();
      } */

    function message($to, $message, $info) {
        if ($to != $this->_fullname) {
            //pass msg to childs


            return;
        }
//                switch ($message) {
//
//                case "login":
//                    $this->login($info['email'],$info['password'],$info['biznessUID']);
//                    break;
//                case "logout":
//                    $this->logout();
//                    break;
//                case "forgetPassword":
//                    $this->sendNewPassword();
//                    break;
//                case "signup":
//                    $this->add($info['email'],$info['password']);
//                    break;;
//                case "validate":
//                    if ($this->validate($info["validationCode"])) {
//                        $this->login($info['email'],$info['password'],$info['biznessUID']);
//                    }
//                    break;
//                default:
//                    break;
//            }
    }

    function broadcast($msg, $info) {
        //pass to child bizes
    }

    /*     * **************************Especial Biz functionality*************************** */

    function logout() {
        $this->loggedin = false;
    }

    function isLoggedin() {
        return $this->loggedin;
    }

    function backUID($email) {
        return $this->userUID;
    }

    function sendVerificationCode() {
        $this->sendEmail($this->email, "Verify", $this->createVerificationCode());
    }

    function validate($verificationCode) {
        query("SELECT * FROM user_info WHERE userUID LIKE '" . $this->userUID . "' ;");
        if ($row = fetch()) {
            if ($verificationCode == $row["verificationCode"]) {
                query("UPDATE user_info SET validationCode = '0' WHERE userUID = '" . $this->userUID . "';");
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    /*     * **************************General Biz functionality*************************** */

    function login($email, $password) {
        query("SELECT * FROM user_info WHERE email LIKE '" . $email . "' AND password LIKE '" . $password . "' AND biznessUID= '" . osBackBiznessUID() . "' ;");
        //query("SELECT * FROM user_info;");
        if ($row = fetch()) {
            $this->email = $email;
            $this->loggedin = true;
            $this->userUID = $row["userUID"];
            if ($row["validationCode"] == '0') {
                return 1; // login succefull
            }
            return -1; //login succefull but need to validate
        }
        query("SELECT * FROM user_info WHERE email LIKE '" . $email . "' AND biznessUID= '" . osBackBiznessUID() . "';");
        if ($row = fetch()) {
            $this->email = $email;
            return -2; //email exists but password was wrong
        }
        return 0; //login failed
    }

    function add($email, $password) {
        query("SELECT * FROM user_info WHERE email LIKE '" . $email . "' ;");
        if ($row = fetch()) {
            if ($email == $row["email"]) {
                return FALSE; //user already exist
            }
        } else {
            $vcode = $this->createVerificationCode();
            query("INSERT INTO user_info (email,password,verificationCode,biznessUID) VALUES ('" . $email . "', '" . $password . "','" . $vcode . "','".osBackBiznessUID()."');");
            $this->sendEmail($email, "Welcome,Please Verify", $vcode);
            return TRUE; //user added succefully
        }
    }

    function backUID($email) {
        query("SELECT * FROM user_info WHERE email LIKE '" . $email . "' ;");
        if ($row = fetch()) {
            return $row["userUID"];
        } else {
            return -1;
        }
    }

    function sendNewPassword($email) {
        query("SELECT * FROM user_info WHERE email LIKE '" . $email . "';");
        if ($row = fetch()) {
            if ($email == $row["email"]) {
                $password = $this->generateRandomPassword();
                query("UPDATE user_info SET password = '" . $password . "' WHERE email = '" . $email . "';");
                $this->sendEmail($email, "New Password", $password);
                return TRUE; //email succefully sent
            }
        }
        return FALSE; //email wasn't found in db
    }

    /*     * **************************Internal Biz functionality*************************** */

    private function sendEmail($to, $title, $msgToSend) {
        mail($to, $title, $msgToSend, "From: Our Server");
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

    /*     * **************************HTML HANDELING*************************** */

    function show() {

    }

//	function show_notLoggedin() {
//            echo <<<EOF
//            <FORM name = "$this->_fullname" method = "post">
//                email : <INPUT type="input" name="email"><br>
//                Password : <INPUT type="input" name="password"><br>
//                <INPUT type="hidden" name="_message" value="login">
//                <INPUT type = "hidden" name="_target" value="$this->_fullname">
//                <INPUT value ="Login" type = "button" onclick = 'JavaScript:sndmsg("$this->_fullname")'>
//            </FORM>
//
//
//
//EOF;
//        }
//
//	function show_loggedin() {
//            echo <<<EOF
//            Welcome dear $this->firstName $this->lastName <br>
//            <FORM name = "$this->_fullname" method = "post">
//                <INPUT type ="hidden" name="_message" value="logout">
//                <INPUT type = "hidden" name="_target" value="$this->_fullname">
//                <INPUT value ="Logout" type = "button" onclick = 'JavaScript:sndmsg("$this->_fullname")'>
//            </FORM>
//
//
//EOF;
//
//        }
}

?>