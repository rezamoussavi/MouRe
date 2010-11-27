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
    var $userShow;
    var $signupSuccess;


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
            $this->userShow = new user(&$data['user_show']);
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
                //If the user is already logged in, just break. Otherwise, log in.
                $this->userShow->login($info['email'], $info['password']);
                break;
                    
            case "logout":
                $this->userShow->logout();
                break;
            
            case "forgotPassword":
                $this->show("forgotPassword");
                break;
            
            case "doForgetPassword":
                $this->show("login");
                $this->sendNewPassword();
                break;
            
            case "displaySignupForm":
                $this->userShow->loggedIn = -3;
                break;
            
            case "signup":
                
                $signup = $this->userShow->add($info['email'], $info['password'], $info['passwordagain']);
                
                switch($signup)
                {
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
                
                break;
            
            case "signupPage":
                $this->show("signupPage");
                break;

            case "validate":
                $this->userShow->validate($info["validationCode"]);    
                break;
            
            case "home":
                $this->userShow->loggedIn = 0;
                break;
            
            default:
                break;
        }
        // handle possible messages for this
        $this->show();
    }

    function broadcast($msg, $info) {
        $this->userShow->broadcast(&$msg, &$info);
    }

    /*     * **************************HTML HANDELING*************************** */

    function show()
    {
        $html;
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
                $html = $this->showSignupForm(-3);
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
        // "return"
        echo $html;
    }
    
    function showLoginForm($mode)
    {
        //some data...
        $formName = $this->_fullname."login";
        $formNameSignup = $this->_fullname."displaySignupForm";
        
        $modehtml = "";
        switch($mode)
        {
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
        $html =
        '<div id = "' . $this->_fullname . '">
        <h2>Login</h2>
        
        <h5>' . $modehtml . '</h5>
        <form name="' . $formName . '" method="post" >
        
            <div style="width: 50px; margin-top: 10px;">Email </div> <input type="input" name="email" style="border: 1px solid #666; background-color: #fff;"><br />
            <div style="width: 50px; margin-top: 10px;">Password </div> <input type="password" name="password" style="border: 1px solid #666; background-color: #fff;"> <br />
            
            <input type="hidden" name="_message" value="login" />
            <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
            
            <div style="width: 180px;">
            <input value ="Login" type = "button" onclick = \'JavaScript:sndmsg("' . $formName . '")\' class="press" style="margin-top: 10px; margin-right: 50px; border: 1px solid #ccc; background-color: #fff;" />            
            </div>
        </form>

        <form name="' . $formNameSignup . '" method="post" >
            <input type="hidden" name="_message" value="displaySignupForm" />
            <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
            
            <input value ="signup!" type = "button" onclick = \'JavaScript:sndmsg("' . $formNameSignup . '")\' class="press" style="margin-top: 10px; margin-right: 0px; border: 1px solid #ccc; background-color: #fff;" />            
        </form>
        
        </div>';
        return $html;
    }
    
    function showSignupForm()
    {
        $formName = $this->_fullname."signup";
        $formNameHome = $this->_fullname."home";
        $msg = "";
        
        switch($this->signupSuccess)
        {
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
        
        $html = '<div id = "' . $this->_fullname . '">
        
        <h2>Signup!</h2> 
        <h5>' .  $msg . '</h5>
        <form name="' . $formName . '" method="post" >
                <div style="width: 100px; margin-top: 10px;">Email </div> <input type="input" name="email" style="border: 1px solid #666; background-color: #fff;"><br />
                <div style="width: 100px; margin-top: 10px;">Password </div> <input type="password" name="password" style="border: 1px solid #666; background-color: #fff;"><br />
                <div style="width: 100px; margin-top: 10px;">Password Again </div> <input type="password" name="passwordagain" style="border: 1px solid #666; background-color: #fff;"><br />
                
                <input type="hidden" name="_message" value="signup" />
                <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
                <input value ="sign up!" type = "button" onclick = \'JavaScript:sndmsg("' . $formName . '")\' class="press"  style="margin-top: 10px; border: 1px solid #666; background-color: #fff;"><br />
        </form>
        
        <form name="' . $formNameHome . '" method="post" >
            <input type="hidden" name="_message" value="home" />
            <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
            
            <input value ="Back to login" type = "button" onclick = \'JavaScript:sndmsg("' . $formNameHome . '")\' class="press" style="margin-top: 10px; margin-right: 0px; border: 1px solid #ccc; background-color: #fff;" />            
        </form>
        
        </div>
        ';
        return $html;
    }
    
    function showValidation($mode)
    {
        //important to add the div thingy here so that the ajax knows what to update :)
        $formNameLogout = $this->_fullname."logout";
        $formNameValidate = $this->_fullname."validate";
        
        $html = '<div id = "' . $this->_fullname . '">';
        if($mode == 'clean')
        {
            $failmsg = "";
        }
        else if($mode == 'error')
        {
            $failmsg = "incorrect code.";
        }

        $html .= "You need to validate your account. Check your email!";
        $html .= '<h3 style="margin-bottom: 2px;">Logout</h3>

                <form name="' . $formNameLogout . '" method="post" >
                <input type="hidden" name="_message" value="logout" />
                <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
                <input value ="Logout" type = "button" onclick = \'JavaScript:sndmsg("' . $formNameLogout . '")\'  style="margin-top: 0px; border: 1px solid #666; background-color: #fff;"><br />
                </form>
                
                <h5>' . $failmsg . '</h5>
                <form name="' . $formNameValidate . '" method="post" >
                <div style="width: 100px; margin-top: 10px;">Validation code </div> <input type="input" name="validationCode" style="border: 1px solid #666; background-color: #fff;"><br />
                <input type="hidden" name="_message" value="validate" />
                <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
                <input value ="Validate!" type = "button" onclick = \'JavaScript:sndmsg("' . $formNameValidate . '")\'  style="margin-top: 10px; border: 1px solid #666; background-color: #fff;"><br />
                </form>
                
                ';

        $html .= "</div>";
        return $html;
    }
    
    function showWelcome()
    {
        //important to add the div thingy here so that the ajax knows what to update :)
        $formName = $this->_fullname."logout";
        $html = '<div id = "' . $this->_fullname . '">';
        
      
        $html .= "<h2>Welcome sir!</h2>";
        $html .= '<h3 style="margin-bottom: 2px;">Logout</h3>
                <form name="' . $formName . '" method="post" >

                <input type="hidden" name="_message" value="logout" />
                <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
    
                <input value ="Logout" type = "button" onclick = \'JavaScript:sndmsg("' . $formName . '")\'  style="margin-top: 0px; border: 1px solid #666; background-color: #fff;"><br />
                </form>';

        $html .= "</div>";
        return $html;
    }

























/****  Deprecated stuff... ****/

    function show2($showMsg) {

        switch ($showMsg) {
            case "login":
                
            //some data...
            $formName = $this->_fullname."Login";
            
            //pack html variable
            $html =
            
            '<div id = "' . $this->_fullname . '">
            <h2>Login</h2>
            <form name="' . $formName . '" method="post" >
            
                
                <div style="width: 50px; margin-top: 10px;">email </div> <input type="input" name="email" style="border: 1px solid #666; background-color: #fff;"><br />
                <div style="width: 50px; margin-top: 10px;">Password </div> <input type="password" name="password" style="border: 1px solid #666; background-color: #fff;"> <br />
                
                <input type="hidden" name="_message" value="login" />
                <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
                
                <input value ="Login" type = "button" onclick = \'JavaScript:sndmsg("' . $formName . '")\'  style="margin-top: 10px; border: 1px solid #666; background-color: #fff;"><br />
            </form>
            </div>';

/*
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
*/
                echo $html;
                break;
            
            case "welcome":
                $html = "welcome!";
                break;
            
            case "signupPage":
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
            
            case "forgotPassword":
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
            
            case "userExists":
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
            
            case "validation":
                $html = '
                <div id = "' . $this->_fullname . '">
                <form name = "' . $this->_fullname . '" method = "post" >
                do validation!<br />
                <input type="hidden" name="_message" value="signup" />
                <input type = "hidden" name="_target" value="' . $this->_fullname . '">
                <input value ="choose new email" type = "button" onclick = \'JavaScript:sndmsg("' . $this->_fullname . '")\'>
                </form>
                </div>';
                
                echo $html;
            break;
        
            default:
            echo "Suppose something went wrong...";
                break;
        }
        
        
        //return $html;
    }
    

    /*
    function showLoginForm() {
        return $this->show("login");
    }
    */

}

?>
