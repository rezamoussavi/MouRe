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
            case "forgotPassword":
                $this->show("forgotPassword");
                break;
            case "doForgetPassword":
                $this->show("login");
                $this->sendNewPassword();
                break;
            case "doSignup":
                if($this->user_show->add($info['email'], $info['password'])){
                    $this->show("validation");
                }else{
                    $this->show("userExists");
                }
            case "signupPage":
                $this->show("signupPage");
                break;
            case "validation":
                $this->show("validation");
            case "validate":
                if ($this->validate($info["validationCode"])) {
                    $this->login($info['email'], $info['password']);
                }
                break;
            default:
                break;
        }
        // handle possible messages for this
        //$this->show("login");
    }

    function broadcast($msg, $info) {
        $this->user_show->broadcast(&$msg, &$info);
    }

    /*     * **************************HTML HANDELING*************************** */

    function show($showMsg) {
//        var $formName="a";
        switch ($showMsg) {
            case "login":

//                echo <<<EOF
                    echo "<div id = \"".$this->_fullname."\">";
//EOF;
             $formName = $this->_fullname."Login";
                echo <<<EOF
            <form name = "$formName" method = "post" >
                email : <input type="input" name="email"><br/>
                Password : <input type="password" name="password"><br/>
                Confirm Password : <input type="password" name="password"><br />
                <input type="hidden" name="_message" value="login" />
                <input type = "hidden" name="_target" value="$this->_fullname">
                <input value ="Login" type = "button" onclick = 'JavaScript:sndmsg("$formName")'><br />
            </form>
EOF;
             $formName = $this->_fullname."signup";
                echo <<<EOF
            <form name = "$formName" method = "post" >
                new user? <br />
                <input type="hidden" name="_message" value="signupPage" />
                <input type = "hidden" name="_target" value="$this->_fullname">
                <input value ="signup" type = "button" onclick = 'JavaScript:sndmsg("$formName")'><br />
            </form>
EOF;
                $formName = $this->_fullname."forgotPassword";
                echo <<<EOF
            <form name = "$formName" method = "post" >
                forgot password ? <br />
                <input type="hidden" name="_message" value="forgotPassword" />
                <input type = "hidden" name="_target" value="$this->_fullname">
                <input value ="send me new password" type = "button" onclick = 'JavaScript:sndmsg("$formName")'>
            </form>
EOF;
            echo "</div>";





                break;
            case ("signupPage"):
                echo <<<EOF
            <div id = "$this->_fullname">
            <form name = "$this->_fullname" method = "post" >
                  sign up page!<br />
                email : <input type="input" name="email"><br/>
                Password : <input type="password" name="password"><br/>
                Confirm Password : <input type="password" name="password"><br />
                <input type="hidden" name="_message" value="doSignup" />
                <input type = "hidden" name="_target" value="$this->_fullname">
                <input value ="sign me up" type = "button" onclick = 'JavaScript:sndmsg("$this->_fullname")'>
            </form>
            </div>



EOF;
                break;
            case ("forgotPassword"):
                echo <<<EOF
            <div id = "$this->_fullname">
            <form name = "$this->_fullname" method = "post" >
                email : <input type="input" name="email"><br/>
                <input type="hidden" name="_message" value="doForgotPassword" />
                <input type = "hidden" name="_target" value="$this->_fullname">
                <input value ="send me my new password" type = "button" onclick = 'JavaScript:sndmsg("$this->_fullname")'>
            </form>
            </div>



EOF;
                break;
            case ("userExists"):
                echo <<<EOF
            <div id = "$this->_fullname">
            <form name = "$this->_fullname" method = "post" >
                User already exists<br />
                Please choose another email<br />
                <input type="hidden" name="_message" value="signup" />
                <input type = "hidden" name="_target" value="$this->_fullname">
                <input value ="choose new email" type = "button" onclick = 'JavaScript:sndmsg("$this->_fullname")'>
            </form>
            </div>
EOF;
                break;
            case ("validation"):
                echo <<<EOF
            <div id = "$this->_fullname">
            <form name = "$this->_fullname" method = "post" >
                do validation!<br />
                <input type="hidden" name="_message" value="signup" />
                <input type = "hidden" name="_target" value="$this->_fullname">
                <input value ="choose new email" type = "button" onclick = 'JavaScript:sndmsg("$this->_fullname")'>
            </form>
            </div>
EOF;


            break;
            default:
                break;
        }
    }

    function showLoginForm() {
        $this->show("login");
//        echo <<<EOF
//            <form name = "$this->_fullname" method = "post" />
//                email : <input type="input" name="email"><br/>
//                Password : <input type="password" name="password"><br/>
//                Confirm Password : <input type="password" name="password"><br />
//                <input type="hidden" name="_message" value="login" />
//                <input type = "hidden" name="_target" value="$this->_fullname">
//                <input value ="Login" type = "button" onclick = 'JavaScript:sndmsg("$this->_fullname")'>
//            </form>
//
//
//
//
//EOF;
    }

}

?>
