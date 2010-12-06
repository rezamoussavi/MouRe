<?php

//DB Info : Specify what informaton this BIZ is going to use from DB


/*database attributes
 * userUID
 * email
 * hashPassword
 * verificationCode
 * biznessUID
 */

require_once 'biz.php';

class user extends Biz {
    
    //the bizes you want to use: {variable name : biz type}
    private $bizes = array();

    /*     * **************************FIELDS*************************** */
    //CALSS FIELDS
    var $userUID;
    var $email;
    var $loggedIn;  /*
                     * -4: not logged in, forgot password
                     * -3: not logged in, display signup form
                     * -2: failed login, email doesn't exist in db
                     * -1: failed login, wrong password
                     * 0: basically not logged in... default mode...
                     * 1: basically logged in
                     * 2: logged in but needs validation
                     * 3: logged in but entered wrong validation code
                     */





    /*     * **************************CONSTRUCTOR*************************** */
    function __construct($data) {
        //generic
        parent::__construct($data, $this->bizes);
        
        //biz specific
        $this->email = &$data["email"];
        $this->loggedIn = &$data["loggedin"];
        $this->userUID = &$data["userUID"];
    }

    /*     * **************************MESSAGE HANDELING*************************** */

    function message($to, $message, $info) {
        if ($to != $this->_fullname) {
            //pass msg to childs
            return;
        }

    }

    function broadcast($msg, $info) {
        //pass to child bizes
        //no children
    }

    /*     * **************************Especial Biz functionality*************************** */

    function logout() {
        $this->loggedIn = 0;
        osBroadcast("logout", array());
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

    /*     * **************************HTML HANDELING*************************** */

    function show($echo) {
        echo "user show lol";
    }

}

?>