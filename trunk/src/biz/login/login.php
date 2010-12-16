<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//import bizes
$bizes = array('user');

$bizpath = '../biz/';
foreach($bizes as $bizname)
{
    $biz = $bizpath.$bizname."/".$bizname.".php";
    require_once($biz);
}


//require_once '../biz/user/user.php';

class login
{

    //all of our biz classes should define these three variable
    var $_fullname;
    var $_bizname;
    var $_parent;

    /*     * **************************FIELDS*************************** */
    //CALSS FIELDS
    //FIELDS WHICH HAVE TYPE OF OTHER BIZES
    //var $myBizes['userShow'];
    
    //bizes
    var $bizes = array('userShow' => 'user');
    var $myBizes = array();
    
    var $signupSuccess;
    var $newPasswordSuccess;
    var $html;


    /*     * **************************CONSTRUCTOR*************************** */

    function __construct($data) {
        $this->_bizname = &$data["bizname"];
        $this->_fullname = &$data["fullname"];
		$this->_parent = &$data["parent"];
        
        foreach($this->bizes as $bizname=>$biz)
        {
            if (!(isset($data[$bizname]))) {
                
                $data[$bizname]['fullname'] = ($this->_bizname . "_" . $bizname); //$this->user
                $data[$bizname]['bizname'] = $bizname;
                $data[$bizname]['parent'] = $this;
                
            }
            $this->myBizes[$bizname] = new $biz(&$data[$bizname]);
        }
        
    }

    /*     * **************************MESSAGE HANDELING*************************** */

    function message($to, $message, $info) {

        if ($to != $this->_fullname) {
            //pass msg to childs
            return;
        }
        
        switch ($message) {

            case "login":
                //If the user is already logged in, just break. Otherwise, log in.
                $this->myBizes['userShow']->login($info['email'], $info['password']);
                break;
                    
            case "logout":
                $this->myBizes['userShow']->logout();
                break;
            
            case "displaySignupForm":
                $this->myBizes['userShow']->loggedIn = -3;
                break;
            
            case "requestNewPassword":
                $req = $this->myBizes['userShow']->sendNewPassword($info['email']);
                    
                if($req)
                {
                    $this->newPasswordSuccess = 1;
                }
                else
                {
                    $this->newPasswordSuccess = -1;
                }
                
                break;
            
            case "displayForgotForm":
                $this->myBizes['userShow']->loggedIn = -4;
                break;
            
            case "signup":
                if($info['email'] != "" && $info['password'] != "" && $info['passwordagain'] != "")
                {
                    $signup = $this->myBizes['userShow']->add($info['email'], $info['password'], $info['passwordagain']);
                    
                    switch($signup)
                    {
                        case 1:
                            $this->signupSuccess = 1;
                            $this->myBizes['userShow']->loggedIn = 2;
                            break;
                        
                        case -1:
                            $this->signupSuccess = -1;
                            $this->myBizes['userShow']->loggedIn = -3;
                            break;
                        
                        case -2:
                            $this->signupSuccess = -2;
                            $this->myBizes['userShow']->loggedIn = -3;
                            break;
                        
                        default:
                            echo '##### weird...';
                            break;
                    }
                }
                break;

            case "validate":
                $this->myBizes['userShow']->validate($info["validationCode"]);    
                break;
            
            case "home":
                $this->myBizes['userShow']->loggedIn = 0;
                break;
            
            default:
                break;
        }
        // handle possible messages for this
        $this->show(1);
    }

    function broadcast($msg, $info) {
        foreach($this->myBizes as $abiz)
        {
            $abiz->broadcast(&$msg, &$info);
        }
    }

    /*     * **************************HTML HANDELING*************************** */

    function show($echo)
    {
        switch($this->myBizes['userShow']->loggedIn)
        {
            // "no"
            case 0:
                $this->html = $this->showLoginForm(0);
                break;
            case -1:
                $this->html = $this->showLoginForm(-1);
                break;
            case -2:
                $this->html = $this->showLoginForm(-2);
                break;
            case -3:
                $this->html = $this->showSignupForm();
                break;
            case -4:
                $this->html = $this->showForgotForm();
                break;
            
            // "yes"
            case 1:
                $this->html = $this->showWelcome();
                break;
            case 2:
                $this->html = $this->showValidation('clean');
                break;
            case 3:
                $this->html = $this->showValidation('error');
                break;
            
            default:
                $this->html = "error in show()";
                break;
        }

        // echo html to Ajax function
        if($echo)
        {
            echo osShow($this);
        }
        //return to parent
        else
        {
            return osShow($this);
        }
    }
    
    function showLoginForm($mode)
    {
        //some data...
        $formName = $this->_fullname."login";
        $formNameSignup = $this->_fullname."displaySignupForm";
        $formNameForgot = $this->_fullname."displayForgotForm";
        
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
        '<h2>Login</h2>
        
        <h5>' . $modehtml . '</h5>
        <form name="' . $formName . '" method="post" >
        
            <div style="width: 50px; margin-top: 10px;">Email </div> <input type="input" name="email" /><br />
            <div style="width: 50px; margin-top: 10px;">Password </div> <input type="password" name="password" /> <br />
            
            <input type="hidden" name="_message" value="login" />
            <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
            
            <div style="width: 180px;">
            <input value ="Login" type = "button" onclick = \'JavaScript:sndmsg("' . $formName . '")\' class="press" style="margin-top: 10px; margin-right: 50px;" />            
            </div>
        </form>

        <form name="' . $formNameSignup . '" method="post" >
            <input type="hidden" name="_message" value="displaySignupForm" />
            <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
            <span>No account yet?</span>
            <input value ="sign up!" type = "button" onclick = \'JavaScript:sndmsg("' . $formNameSignup . '")\' class="press" style="margin-top: 10px; margin-right: 0px;" />            
        </form>
        
        <form name="' . $formNameForgot . '" method="post" >
            <input type="hidden" name="_message" value="displayForgotForm" />
            <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
            <span>Password lost?</span>
            <input value ="get a new!" type = "button" onclick = \'JavaScript:sndmsg("' . $formNameForgot . '")\' class="press" style="margin-top: 10px; margin-right: 0px;" />            
        </form>
        ';
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
        
        $html = '
        <h2>Signup!</h2> 
        <h5>' .  $msg . '</h5>
        <form name="' . $formName . '" method="post" >
                <div style="width: 100px; margin-top: 10px;">Email </div> <input type="input" name="email" /><br />
                <div style="width: 100px; margin-top: 10px;">Password </div> <input type="password" name="password" /><br />
                <div style="width: 100px; margin-top: 10px;">Password Again </div> <input type="password" name="passwordagain" /><br />
                
                <input type="hidden" name="_message" value="signup" />
                <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
                <input value ="sign up!" type = "button" onclick = \'JavaScript:sndmsg("' . $formName . '")\' class="press"  style="margin-top: 10px;"><br />
        </form>
        
        <form name="' . $formNameHome . '" method="post" >
            <input type="hidden" name="_message" value="home" />
            <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
            
            <span>Already a user?</span>
            <input value ="login!" type = "button" onclick = \'JavaScript:sndmsg("' . $formNameHome . '")\' class="press" style="margin-top: 10px; margin-right: 0px;" />            
        </form>
        ';
        return $html;
    }
    
    function showForgotForm()
    {
        $formName = $this->_fullname."requestNewPassword";
        $formNameHome = $this->_fullname."home";
        $msg = "";
        
        switch($this->newPasswordSuccess)
        {
            case -1:
                $msg = "Account does not exist.";
                break;
            
            case 1:
                $msg = "Check your email for the new password!";
                break;
            
            default:
                break;
        }
        
        $html = '
        
        <h2>Get a new password</h2> 
        <h5>' .  $msg . '</h5>';
        
        if($this->newPasswordSuccess < 1)
        {
            $html .= '
            <form name="' . $formName . '" method="post" >
                    <div style="width: 100px; margin-top: 10px;">Email </div> <input type="input" name="email" /><br />
                    
                    <input type="hidden" name="_message" value="requestNewPassword" />
                    <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
                    <input value ="send" type = "button" onclick = \'JavaScript:sndmsg("' . $formName . '")\' class="press"  style="margin-top: 10px;"><br />
            </form>';
        }
        
        $html .= '
        <form name="' . $formNameHome . '" method="post" >
            <input type="hidden" name="_message" value="home" />
            <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
            
            <span>Go back </span>
            <input value ="home" type = "button" onclick = \'JavaScript:sndmsg("' . $formNameHome . '")\' class="press" style="margin-top: 10px; margin-right: 0px;" />            
        </form>
        ';
        return $html;
    }
    
    function showValidation($mode)
    {
        //important to add the div thingy here so that the ajax knows what to update :)
        $formNameLogout = $this->_fullname."logout";
        $formNameValidate = $this->_fullname."validate";
        
        if($mode == 'clean')
        {
            $failmsg = "";
        }
        else if($mode == 'error')
        {
            $failmsg = "incorrect code.";
        }

        $html = '<h5>You need to validate your account. Check your email!</h5>';
        $html .= '<h3 style="margin-bottom: 2px;">Logout</h3>

                <form name="' . $formNameLogout . '" method="post" >
                <input type="hidden" name="_message" value="logout" />
                <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
                <input value ="Logout" type = "button" onclick = \'JavaScript:sndmsg("' . $formNameLogout . '")\'  style="margin-top: 0px;"><br />
                </form>
                
                <h5>' . $failmsg . '</h5>
                <form name="' . $formNameValidate . '" method="post" >
                <div style="width: 100px; margin-top: 10px;">Validation code </div> <input type="input" name="validationCode" /><br />
                <input type="hidden" name="_message" value="validate" />
                <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
                <input value ="Validate!" type = "button" onclick = \'JavaScript:sndmsg("' . $formNameValidate . '")\'  style="margin-top: 10px;"><br />
                </form>
                
                ';

        return $html;
    }
    
    function showWelcome()
    {
        //important to add the div thingy here so that the ajax knows what to update :)
        $formName = $this->_fullname."logout";

        $html = "<h2>Welcome sir!</h2>";
        $html .= '<form name="' . $formName . '" method="post" >
                <input type="hidden" name="_message" value="logout" />
                <input type = "hidden" name="_target" value="' . $this->_fullname . '" />
    
                <input value ="Logout" type = "button" onclick = \'JavaScript:sndmsg("' . $formName . '")\'  class="press" style="margin-top: 0px;"><br />
                </form>';
        return $html;
    }

}

?>
