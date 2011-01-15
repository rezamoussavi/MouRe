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

class eblistviewer {

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
	var $userUID;
	var $UID;//Will set by parent to let this biz know which category/eBList it is
	var $eBLists;//array of eBListViewer.biz
	var $eBListsData;
	var $eBoards;//array of eBLineViewer.biz
	var $eBoardsData;

    /*     * **************************CONSTRUCTOR*************************** */

    function __construct(&$data) {

		  if (!isset($data['sleep']))
		  {
				$data['sleep'] = true;
            $this->initialize($data);
        }
		  $this->wakeup($data);  
	}

	function initialize(&$data){
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
		if(! isset ($data["userUID"]))
			$data['userUID']=-1;
		if(! isset ($data["UID"]))
			$data['UID']=0;
		if(! isset ($data['expanded']))
			switch ($data['UID']){
				case  0:	$data['expanded']=true; break;
				default:	$data['expanded']=false; break;
			}
		/* *** Initializing myCat *** */
		if(! isset ($data['myCat'])){
			$data['myCat']['fullname']=$this->_bizname."_myCat";
			$data['myCat']['bizname']="myCat";
			$data['myCat']['parent']=$this;
			$data['myCat']['catUID']=$data['UID'];
		}
		$data['eBLists']=array();
		$data['eBoards']=array();
	}

	function wakeup(&$data){
        $this->_bizname = &$data["bizname"];
        $this->_fullname = &$data["fullname"];
		$this->_parent = &$data["parent"];
		/* *** In this biz we have arrays of bizes so we skip the array way of initializing bizes
        foreach($this->bizes as $bizname=>$biz)
        {
            $this->myBizes[$bizname] = new $biz(&$data[$bizname]);
        }
		*/
		$this->userUID=&$data["userUID"];
		$this->UID=&$data["UID"];
		$this->expanded=&$data["expanded"];
		$this->myCat=new category(&$data['myCat']);
		$this->eBListsData=&$data['eBLists'];
		$this->eBoardsData=&$data['eBoards'];
		$this->eBLists=array();
		$this->eBoards=array();
		
		foreach($data['eBLists'] as $listName=>&$listdata)
		{
			if(! isset($listdata["bizname"]))
			{
				$listdata["bizname"]=$listName;
				$listdata["fullname"]=$this->_fullname."_".$listName;
				$listdata["parent"]=$this;
			}
			$this->eBLists[]=new eblistviewer(&$listdata);
		}
		
		foreach($data['eBoards'] as $eBName=>&$eBdata)
		{
			if(! isset($eBdata["bizname"]))
			{
				$eBdata["bizname"]=$eBName;
				$eBdata["fullname"]=$this->_fullname."_".$eBName;
				$eBdata["parent"]=$this;
			}
			$this->eBoards[]=new eblineviewer(&$eBdata);
		}
		if($this->expanded)
		{
			$this->loadContent($data);
		}
	}

	function loadContent(&$maindata){
		if((sizeof($maindata['eBLists'])+sizeof($maindata['eBoards']))!=0)
		{
			return;// It has loaded once
		}
		$con=$this->myCat->backContent();
		foreach($con as $cat)
		{
			switch($cat['extra'])
			{
				case 'eBList':
					$index=count($this->eBListsData);
					$data=array();
					$data['UID']=$cat['bizUID'];
					$data['parent']=$this;
					$data['bizname']=$index;
					$data['fullname']=$this->_fullname."_".$data['bizname'];
					$this->eBListsData[]=$data;
					$this->eBLists[]=new eblistviewer($this->eBListsData[$index]);
					break;
				case 'eBoard':
					$index=count($this->eBoardsData);
					$data=array();
					$data['UID']=$cat['bizUID'];
					$data['parent']=$this;
					$data['bizname']=$index;
					$data['fullname']=$this->_fullname."_".$data['bizname'];
					$this->eBoardsData[]=$data;
					$this->eBoards[]=new eblineviewer($this->eBoardsData[$index]);
					break;
				default:
					break;
			}//switch
		}//foreach
	}

    /*     * **************************MESSAGE HANDELING*************************** */

    function message($to, $message, $info) {
		  echo "\n##".$this->_fullname." : (".$to.", ".$message.", ".$info.") ##\n";
        if ($to != $this->_fullname)
		  {
				//pass msg to childs
				$this->myCat->message($to, $message, $info);
				foreach($this->eBoards as $i=>&$eB)
				{
					 $eB->message($to, $message, $info);
				}
				foreach($this->eBLists as $i=>&$eL)
				{
					 $eL->message($to, $message, $info);
				}
            return;
        }
        // handle possible messages for this
		  if($message=="click")
		  {
			  $this->expanded= ! $this->expanded;
			  $this->show(true);
		  }
    }

    function broadcast($msg, $info) {
		/* *** not in this biz
        foreach($this->myBizes as &$abiz)
        {
            $abiz->broadcast(&$msg, &$info);
        }
		*/
		$this->myCat->broadcast(&$msg, &$info);
		foreach($this->eBoards as $i=>&$eB)
			$eB->broadcast(&$msg, &$info);
		foreach($this->eBLists as $i=>&$eL)
			$eL->broadcast(&$msg, &$info);
      switch($msg)
      {
         case 'login':
            $this->userUID = $info["userUID"];
            $this->show(true);
            break;
         
         case 'logout':
            $this->userUID = -1;
            $this->show(true);
            break;
      }
    }

    /*     * **************************HTML HANDELING*************************** */

    function show($echo)
    {
		$content=$this->showContent(false);
		$formName=$this->_fullname;
		$msgTarget=$this->_fullname;
		$lable=$this->myCat->lable;

        $this->html= '
			<form name="' . $formName . '" method="post">
	         <input type="hidden" name="_message" value="click" />
				<input type = "hidden" name="_target" value="' . $msgTarget . '" />
				<input value ="' . $lable . '" type = "button" onclick = \'JavaScript:sndmsg("' . $formName . '")\' class="press" style="margin-top: 10px; margin-right: 0px;" />
			</form>
			' . $content;

		if($echo)
		{
			echo osShow($this);
		}
		else
		{
			return osShow($this);
		}
    }
	
	function showContent($echo){
		$html='';
		if($this->expanded){
			foreach($this->eBLists as $i=>&$L)
			{
				$html = $html.$L->show(false);
			}
			foreach($this->eBoards as $i=>&$B)
			{
				$html = $html.$B->show(false);
			}
		}
		if($echo)
		{
			echo $html;
		}
		else
		{
			return $html;
		}
	}
}

?>
