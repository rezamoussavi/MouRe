<?PHP

/*
	Compiled by bizLang compiler version 2.0 (March 4 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}
	1.4: {multi parameter in link message}
	1.5: {multi secName support: frm/frame, msg/messages,fun/function/phpfunction}
	2.0: {upload bothe biz and php directly to server (ready to use)}

	Author: Reza Moussavi
	Date:	3/10/2010
	Version: 1.5
    ------------------
	Author: Max Mirkia
	Date:	2/14/2010
	Version: 1.0
    ------------------
    Author: Max Mirkia
	Date:	2/7/2010
	Version: 0.1

*/

class tabbank {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables
	var $tabs;
	var $curTabName;

	//Nodes (bizvars)

	function __construct($fullname) {
		$this->_frmChanged=false;
		$this->_tmpNode=false;
		if($fullname==null){
			$fullname='_tmpNode_'.count($_SESSION['osNodes']);
			$this->_tmpNode=true;
		}
		$this->_fullname=$fullname;
		if(!isset($_SESSION['osNodes'][$fullname])){
			$_SESSION['osNodes'][$fullname]=array();
			//If any message need to be registered will placed here
			$_SESSION['osMsg']['client_tab'][$this->_fullname]=true;
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		if(!isset($_SESSION['osNodes'][$fullname]['tabs']))
			$_SESSION['osNodes'][$fullname]['tabs']='';
		$this->tabs=&$_SESSION['osNodes'][$fullname]['tabs'];

		if(!isset($_SESSION['osNodes'][$fullname]['curTabName']))
			$_SESSION['osNodes'][$fullname]['curTabName']='';
		$this->curTabName=&$_SESSION['osNodes'][$fullname]['curTabName'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='tabbank';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'client_tab':
				$this->onTabSelected($info);
				break;
			default:
				break;
		}
	}

	function _bookframe($frame){
		if($frame!=$this->_curFrame){
			$this->_frmChanged=true;
			$this->_curFrame=$frame;
		}
		//$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$html='<div id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
		if($_SESSION['silentmode'])
			return;
		if($echo)
			echo $html;
		else
			return $html;
	}


//########################################
//         YOUR FUNCTIONS GOES HERE
//########################################


    function bookContent($content){//String[]
        $this->tabs=array();
        foreach($content as $c){
            $this->tabs[]=$c;
        }
		$this->curTabName=$this->tabs[0];
    }
    function frm(){
		$html='';
		foreach($this->tabs as $t){
			if($t==$this->curTabName){
				$html.=<<<PHTMLCODE
 <b>[[{$t}]]</b> 
PHTMLCODE;

			}else{
				$link=osBackLinkInfo($this->_fullname,"tab",array("name"=>$this->curTabName),"tab",array("name"=>$t));
				$html.=<<<PHTMLCODE

					<a href="{$link}">{$t}</a>
				
PHTMLCODE;

			}
		}
		return $html;
	}
    function onTabSelected($info){
		$this->curTabName=$info["name"];
		osBroadcast("tab_tabChanged",array("tabName"=>$info["name"]));
    }

}

?>