<?php
/*
*	Author: Reza Moussavi
*	Version 1.0
*	12/2/2010
*	category.biz improving according to other bizes needs
*	This version suport needs for eBListViewer.biz and eBLineViewer.biz
*/

//import bizes
/*
$bizes = array();

$bizpath = '../biz/';
foreach($bizes as $bizname)
{
    $biz = $bizpath.$bizname."/".$bizname.".php";
    require_once($biz);
}
*/

class category
{

    //all of our biz classes should define these three variable
    var $_fullname;
    var $_bizname;
    var $_parent;

    /*     * **************************FIELDS*************************** */
    //CALSS FIELDS
    //FIELDS WHICH HAVE TYPE OF OTHER BIZES
    
    //bizes
	/*
    var $bizes = array();
    var $myBizes = array();
	*/    
    var $catUID;
    var $lable;
	/*	****No need yet
	var $owner_type;
	var $owner_name;
	var $owner_UID;
	*/
	var $type_name;

    /*     * **************************CONSTRUCTOR*************************** */

    function __construct($data) {
        $this->_bizname = &$data["bizname"];
        $this->_fullname = &$data["fullname"];

        $this->catUID = &$data["catUID"];
        $this->lable = &$data["lable"];
		/* ***No need yet
		$this->owner_type = &$data["owner_type"];
		$this->owner_name = &$data["owner_name"];
		$this->owner_UID = &$data["owner_UID"];
		*/
		$this->type_name = &$data["type_name"];
		/*  
		foreach($this->bizes as $bizname=>$biz)
        {
            if (!(isset($data[$bizname]))) {
                
                $data[$bizname]['fullname'] = ($this->_bizname . "_" . $bizname); //$this->user
                $data[$bizname]['bizname'] = $bizname;
                $data[$bizname]['parent'] = $this;
                
            }
            $this->myBizes[$bizname] = new $biz(&$data[$bizname]);
        }
		*/       
    }

    /*     * **************************MESSAGE HANDELING*************************** */

    function message($to, $message, $info) {
		return;//Ignore all messages
        if ($to != $this->_fullname) {
            //pass msg to childs
            return;
        }
        // handle possible messages for this
        $this->show();
    }

    function broadcast($msg, $info) {
		return;//Ignore all broadcast messages
        foreach($this->myBizes as $abiz)
        {
            $abiz->broadcast(&$msg, &$info);
        }
    }

    /*     * **************************HTML HANDELING*************************** */

    function show()
    {
		//Nothing to show
    }

    /*     * **************************HTML HANDELING*************************** */

	function init(){
		if($this->catUID==0){//----ROOT it is
			query("SELECT c.catUID as catUID, c.Lable AS lable, t.Name AS type_name FROM category_cat AS c,category_type AS t WHERE c.typeUID=t.typeUID AND c.owner_type ='bizness' AND c.owner_UID='".osBackBizbank()."'");
			if($row=fetch()){
				$this->catUID=$row['catUID'].
				$this->lable=$row['lable'].
				$this->type_name=$row['type_name'].
			}
		}else{//---Specific category
			query("SELECT c.Lable AS lable, t.Name AS type_name FROM category_cat AS c,category_type AS t WHERE c.typeUID=t.typeUID AND c.catUID=".$this->catUID);
			if($row=fetch()){
				$this->lable=$row['lable'].
				$this->type_name=$row['type_name'].
			}
		}
	}
	
	function backContent(){
		if($this->catUID==0)
			return;
		query("SELECT co.bizname AS bizname,co.bizUID AS bizUID, co.extra AS extra FROM category_cat AS c, category_content AS co WHERE co.catUID=c.catUID");
		$ret=array();
		while($row=fetch()){
			$ret[]=array("bizname"=>$row['bizname'],"bizUID"=>$row['bizUID'],"extra"=>$row['extra']);
		}
		return $ret;
	}
}

?>
