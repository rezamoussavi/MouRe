<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../biz/user/user.php';

class login {

    //all of our biz classes should define these two variable
    var $_fullname;
    var $_bizname;
    var $_parent;

    /*     * **************************FIELDS*************************** */
    //CALSS FIELDS
    //FIELDS WHICH HAVE TYPE OF OTHER BIZES
    var $user_Show;


    /*     * **************************CONSTRUCTOR*************************** */

    //function __construct($data){
    //SET CLASS FIELDS WITH $data
    //IF REQUIRED BIZES HAVE NOT BEEN USED,INITIALISE
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
        if (!(isset($data['user_show']))) {
            $data['user_show']['fullname'] = ($this->_bizname . "_" . "user_show"); //$this->user
            $data['user_show']['bizname'] = 'user_show';
            $data['user_show']['parent'] = $this;
        } else {
            $this->user_show = new user(&$data['user_show']);
        }
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
        switch ($message) {

            case "login":
                $this->user_show->login($info['email'], $info['password']);
                break;
            case "logout":
                $this->logout();
                break;
            case "forgetPassword":
                $this->sendNewPassword();
                break;
            case "signup":
                $this->add($info['email'], $info['password']);
                break;
                ;
            case "validate":
                if ($this->validate($info["validationCode"])) {
                    $this->login($info['email'], $info['password']);
                }
                break;
            default:
                break;
        }
        // handle possible messages for this
        $this->show();
    }

    function broadcast($msg, $info) {
        $this->user_show->broadcast(&$msg, &$info);
    }

    /*     * **************************HTML HANDELING*************************** */

    function showLoginForm() {

        echo <<<EOF
            <FORM name = "$this->_fullname" method = "post">
                email : <INPUT type="input" name="email"><br>
                Password : <INPUT type="password" name="password"><br>
                Confirm Password : <INPUT type="password" name="password"><br>
                <INPUT type="hidden" name="_message" value="login">
                <INPUT type = "hidden" name="_target" value="$this->_fullname">
                <INPUT value ="Login" type = "button" onclick = 'JavaScript:sndmsg("$this->_fullname")'>
            </FORM>



EOF;
    }

    function show() {
        if($this->user_show->loggedIn == true)
        {
            echo "Welcome!";
        }
        else
        {
            echo "NOOO!";
            $this->showLoginForm();
        }
    }

}

?>
