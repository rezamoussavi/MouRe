<?php
/*
*	Author: Reza Moussavi
*	Version 1.0
*	12/7/2010
*	In this biz we have arrays of bizes and only one single bizvar, myCat which is category.biz
*	Thus in this biz I dont follow the method which initialize containing bizes in an array
*/

//import bizes
$bizes = array('category');

$bizpath = '../biz/';
foreach($bizes as $bizname)
{
    $biz = $bizpath.$bizname."/".$bizname.".php";
    require_once($biz);
}

class eblineviewer
{
    //all of our biz classes should define these three variable
    var $_fullname;
    var $_bizname;
    var $_parent;

    /*     * **************************FIELDS*************************** */
    //CALSS FIELDS
    //FIELDS WHICH HAVE TYPE OF OTHER BIZES

    //bizes
	/* *** In this biz we have arrays of bizes so we skip the array way of initializing bizes
    var $bizes = array('myCat' => 'category');
    var $myBizes = array();
	*/
	
	var $html;
	var $myCat;
	var $UID;//Will set by parent to let this biz know which category/eBList it is

    /*     * **************************CONSTRUCTOR*************************** */

    function __construct($data) {
		if (!isset($data['sleep'])) {
            $data['sleep'] = true;
            initialize($data);
        }
        wakeup($data);
	}

	function initialize($data){
		/* *** In this biz we have arrays of bizes so we skip the array way of initializing bizes
        foreach($this->bizes as $bizname=>$biz)
        {
            if (!(isset($data[$bizname]))) {
                $data[$bizname]['fullname'] = ($this->_bizname . "_" . $bizname);
                $data[$bizname]['bizname'] = $bizname;
                $data[$bizname]['parent'] = $this;
            }
        }
		*/
		if(! isset ($data["UID"]))
			$data['UID']=0;
		/* *** Initializing myCat *** */
		if(! isset ($data['myCat'])){
			$data['myCat']['fullname']=$this->_bizname."_myCat";
			$data['myCat']['bizname']="myCat";
			$data['myCat']['parent']=$this;
			$data['myCat']['catUID']=$data['UID'];
		}
	}

	function wakeup($data){
        $this->_bizname = &$data["bizname"];
        $this->_fullname = &$data["fullname"];
		$this->_parent = &$data["parent"];
		/* *** In this biz we have arrays of bizes so we skip the array way of initializing bizes
        foreach($this->bizes as $bizname=>$biz)
        {
            $this->myBizes[$bizname] = new $biz(&$data[$bizname]);
        }
		*/
		$this->UID=&$data["UID"];
		$this->myCat=new category(&$data['myCat']);
	}

    /*     * **************************MESSAGE HANDELING*************************** */

    function message($to, $message, $info) {
        if ($to != $this->_fullname) {
            //pass msg to childs
			$this->myCat->message($to, $message, $info);
            return;
        }
        // handle possible messages for this
		if($message=="click"){
			$this->show(true);
		}
    }

    function broadcast($msg, $info) {
		/* *** not in this biz
        foreach($this->myBizes as $abiz)
        {
            $abiz->broadcast(&$msg, &$info);
        }
		*/
		$this->myCat->broadcast(&$msg, &$info);
    }

    /*     * **************************HTML HANDELING*************************** */

    function show($echo)
    {
		$formName=$this->_fulname;
		$msgTarget=$this->_fullname;
		$Lable=$this->myCat->lable;

        $this->html= '
			<form name="' . $formName . '" method="post">
	            <input type="hidden" name="_message" value="click" />
				<input type = "hidden" name="_target" value="' . $msgTarget . '" />
				<input value ="' . $Lable . '" type = "button" onclick = \'JavaScript:sndmsg("' . $formName . '")\' class="press" style="margin-top: 10px; margin-right: 0px;" />
			</form>
			';
		if($echo)
			echo osShow($this);
		else
			return osShow($this);
    }
}

?>
