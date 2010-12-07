<?php
/*
*	Author: Reza Moussavi
*	Version 1.0
*	12/3/2010
*	In this biz we have arrays of bizes and only one single bizvar, myCat which is category.biz
*	Thus in this biz I dont follow the method which initialize containing bizes in an array
*/

//import bizes
$bizes = array('category','eblineviewer');

$bizpath = '../biz/';
foreach($bizes as $bizname)
{
    $biz = $bizpath.$bizname."/".$bizname.".php";
    require_once($biz);
}

class eblistviewer
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
	var $expanded;
	var $UID;//Will set by parent to let this biz know which category/eBList it is
	var $eBLists;//array of eBListViewer.biz
	var $eBoards;//array of eBLineViewer.biz

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
		if(! isset ($data['expanded']))
			switch ($data['UID']){
				case  0:	$data['expanded']=true; break;
				default:	$data['expanded']=false; break;
			}
		$this->expanded=&$data["expanded"];
		$this->myCat=new category(&$data['myCat']);
		if(isset ($data['eBLists'])){
			$this->eBLists=array();
			foreach($data['eBLists'] as $listName=>$listdata){
				$this->eBLists[]=new eblistviewer(&$listdata);
			}
		}
		if(isset ($data['eBoards'])){
			$this->eBoards=array();
			foreach($data['eBoards'] as $eBName=>$eBdata){
				$this->eBoards[]=new eblineviewer(&$eBdata);
			}
		}
		if($this->expanded)
			$this->loadContent();
	}

	function old__construct($data){
        $this->_bizname = &$data["bizname"];
        $this->_fullname = &$data["fullname"];
		$this->_parent = &$data["parent"];

		/* *** In this biz we have arrays of bizes so we skip the array way of initializing bizes
        foreach($this->bizes as $bizname=>$biz)
        {
            if (!(isset($data[$bizname]))) {
                $data[$bizname]['fullname'] = ($this->_bizname . "_" . $bizname);
                $data[$bizname]['bizname'] = $bizname;
                $data[$bizname]['parent'] = $this;
            }
            $this->myBizes[$bizname] = new $biz(&$data[$bizname]);
        }
		*/
		if(! isset ($data["UID"]))
			$data['UID']=0;
		$this->UID=&$data["UID"];
		if(! isset ($data['expanded']))
			switch ($data['UID']){
				case  0:	$data['expanded']=true; break;
				default:	$data['expanded']=false; break;
			}
		$this->expanded=&$data["expanded"];
		/* *** Initializing myCat *** */
		if(! isset ($data['myCat'])){
			$data['myCat']['fullname']=$this->_bizname."_myCat";
			$data['myCat']['bizname']="myCat";
			$data['myCat']['parent']=$this;
			$data['myCat']['catUID']=$data['UID'];
		}
		$this->myCat=new category(&$data['myCat']);
		if(isset ($data['eBLists'])){
			$this->eBLists=array();
			foreach($data['eBLists'] as $listName=>$listdata){
				$this->eBLists[]=new eblistviewer(&$listdata);
			}
		}
		if(isset ($data['eBoards'])){
			$this->eBoards=array();
			foreach($data['eBoards'] as $eBName=>$eBdata){
				$this->eBoards[]=new eblineviewer(&$eBdata);
			}
		}
		if($this->expanded)
			$this->loadContent();
    }

	function loadContent(){
		if(isset ($data['eBLists']) || isset ($data['eBoards']))
			return;
		$con=$this->myCat->backContent();
		foreach($con as $cat){
			switch($cat['extra']){
				case 'eBList':
					$data=array();
					$this->eBLists[0][]=$data;
					$data['UID']=$cat['bizUID'];
					$data['parent']=$this;
					$data['bizname']=count($this->eBLists)-1;
					$data['fullname']=$this->_fullname."_".$data['bizname'];
					$this->eBLists[]=new eblistviewer($data);
					break;
				case 'eBoard':
					$data=array();
					$this->eBoards[0][]=$data;
					$data['UID']=$cat['bizUID'];
					$data['parent']=$this;
					$data['bizname']=count($this->eBoards)-1;
					$data['fullname']=$this->_fullname."_".$data['bizname'];
					$this->eBoards[]=new eblineviewer($data);
					break;
				default:
					break;
			}
		}
	}

    /*     * **************************MESSAGE HANDELING*************************** */

    function message($to, $message, $info) {
        if ($to != $this->_fullname) {
            //pass msg to childs
			$this->myCat->message($to, $message, $info);
			foreach($this->eBoards as $eB)
				$eB->message($to, $message, $info);
			foreach($this->eBLists as $eL)
				$eL->message($to, $message, $info);
            return;
        }
        // handle possible messages for this
		if($message=="click"){
			$this->expanded= ! $this->expanded;
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
		foreach($this->eBoards as $eB)
			$eB->broadcast(&$msg, &$info);
		foreach($this->eBLists as $eL)
			$eL->broadcast(&$msg, &$info);
    }

    /*     * **************************HTML HANDELING*************************** */

    function show($echo)
    {
		$Content=$this->showContent(false);
		$formName=$this->_fulname;
		$msgTarget=$this->_fullname;
		$Lable=$this->myCat->lable;

        $this->html= '
			<form name="' . $formName . '" method="post">
	            <input type="hidden" name="_message" value="click" />
				<input type = "hidden" name="_target" value="' . $msgTarget . '" />
				<input value ="' . $Lable . '" type = "button" onclick = \'JavaScript:sndmsg("' . $formName . '")\' class="press" style="margin-top: 10px; margin-right: 0px;" />
			</form>
			' . $Content;
		if($echo)
			echo osShow($this);
		else
			return osShow($this);
    }
	
	function showContent($echo){
		$html='';
		if($this->expanded){
			foreach($this->eBLists as $L)
				$html = $html.$L->show(false);
			foreach($this->eBoards as $B)
				$html = $html.$B->show(false);
		}
		if($echo)
			echo $html;
		else
			return $html;
	}
}

?>
