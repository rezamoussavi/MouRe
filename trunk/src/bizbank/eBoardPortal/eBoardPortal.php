<?php

//DB Info : Specify what informaton this BIZ is going to use from DB

/*
 * might be possible to solve multiple biz-spec with a config file...
 */

//import bizes
$bizes = array('login', 'dummie');

$bizpath = '../biz/';
foreach($bizes as $bizname)
{
    $biz = $bizpath.$bizname."/".$bizname.".php";
    require_once($biz);
}

class eBoardPortal {

    //all of our biz classes should define these two variable
    var $_bizbankname;

    /*     * **************************FIELDS*************************** */
    //CALSS FIELDS
    var $bizness_id;
    
    //FIELDS WHICH HAVE TYPE OF OTHER BIZES
    //var $login;
    //var $dummie;
    var $bizes = array('login' => 'login',
                       'd1' => 'dummie',
                       'd2' => 'dummie');
    var $myBizes = array();

    /*     * **************************CONSTRUCTOR*************************** */

    function __construct($data) {
        $this->_bizbankname = "eBoardPortal";
        
        foreach($this->bizes as $bizname=>$biz)
        {
            if (!(isset($data[$bizname]))) {
                
                $data[$bizname]['fullname'] = ($this->_bizbankname . "_" . $bizname); //$this->user
                $data[$bizname]['bizname'] = $bizname;
                $data[$bizname]['parent'] = $this;
                
            }
            $this->myBizes[$bizname] = new $biz(&$data[$bizname]);
        }
        
        $this->bizness_id = 1;
    }

    /*     * **************************MESSAGE HANDELING*************************** */


    function message($to, $message, $info) {
        $this->myBizes['login']->message(&$to, &$message, &$info);
        if ($to != $this->_bizbankname) {
            return;
        }
        show();
        // handle possible messages for this
    }

    function broadcast($msg, $info)
    {
        foreach($this->myBizes as $abiz)
        {
            $abiz->broadcast(&$msg, &$info);
        }
    }

    /*     * **************************HTML HANDELING*************************** */

    function show() {
    
    //print out the html head
    echo '<?xml version="1.0" encoding="UTF-8"?>
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
            <head>
   
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <link href="../os/css/layout.css" rel="stylesheet" type="text/css" />
            <link href="../os/css/eBoardPortal.css" rel="stylesheet" type="text/css" />

            <script type="text/javascript" src="../os/js/bloop.js"></script>
            </head>
            <body>';

    //include the actual page content
    include 'eBoardPortal_view.php';
   
    //close html document
    echo '</body></html>';

    }

}

?>