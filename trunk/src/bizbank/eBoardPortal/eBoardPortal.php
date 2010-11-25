<?php

//DB Info : Specify what informaton this BIZ is going to use from DB

require_once '../biz/login/login.php';

class eBoardPortal {

    //all of our biz classes should define these two variable
    var $_bizbankname;

    /*     * **************************FIELDS*************************** */
    //CALSS FIELDS
    var $bizness_id;
    
    //FIELDS WHICH HAVE TYPE OF OTHER BIZES
    var $login;
    


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
        $this->_bizbankname = "eBoardPortal";
        if (!(isset($data['login']))) {
            $data['login']['fullname'] = ($this->_bizbankname . "_" . "login"); //$this->user
            $data['login']['bizname'] = 'login';
            $data['login']['parent'] = $this;
        }
        $this->login = new login(&$data['login']);
        $this->bizness_id = 1;
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
        $this->login->message(&$to, &$message, &$info);
        if ($to != $this->_bizbankname) {
            return;
        }
        show();
        // handle possible messages for this
    }

    function broadcast($msg, $info) {
        $this->login->broadcast(&$msg, &$info);
    }

    /*     * **************************HTML HANDELING*************************** */

    function show() {
    echo '<?xml version="1.0" encoding="UTF-8"?>';     
    ?>

	
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
   
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../bizbank/eBoardPortal/layout.css" rel="stylesheet" type="text/css" />
	</head>
	<body>

   
    
  
    <div id="contentholder">
        <div class="content">
        <div class="toprow">
            <div class="login">
            
            <?php $this->login->show() ?>
            
            </div>
    
            <div class="banner">
                <h1>A Board Name</h1>
            </div>
    
            <div class="helpinfo"> hej 3
            </div>
        </div>
        </div>
    </div>        
    </body></html>

    
<?php    
    }

}

?>