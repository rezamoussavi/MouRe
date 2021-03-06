<?PHP

/*
	Compiled by bizLang compiler version 3.2 [DB added] (March 26 2011) By Reza Moussavi

	Author: Reza Moussavi
	Date:	03/11/2011
	Version: 1.1
	------------------
	Author: Max Mirkia
	Date:	2/14/2011
	Version: 1.0
	------------------
	Author: Max Mirkia
	Date:	2/7/2011
	Version: 0.1

*/

class usertabbank {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables
	var $tabs;
	var $curTab;

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

		if(!isset($_SESSION['osNodes'][$fullname]['curTab']))
			$_SESSION['osNodes'][$fullname]['curTab']='';
		$this->curTab=&$_SESSION['osNodes'][$fullname]['curTab'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='usertabbank';
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
				$this->onUserTabChanged($info);
				break;
			default:
				break;
		}
	}

	function _bookframe($frame){
		$this->_frmChanged=true;
		$this->_curFrame=$frame;
		//$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$_style='';
		switch($this->_curFrame){
			case 'frm':
				$_style=' style="width:595; float:left; background-color:#fafafa; text-align:center;" ';
				break;
		}
		$html='<div '.$_style.' id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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
        $this->_bookframe("frm");
    }
	function bookSelected($tab){
		$this->curTab=$tab;
		osBroadcast("usertab_usertabChanged",array("tabName"=>$tab));
	}
    function frm(){
		$html='';
		foreach($this->tabs as $t){
			if($this->curTab==$t){
				$html.= <<<PHTMLCODE
 [[ $t ]] 
PHTMLCODE;

			}else{
				$link=osBackLinkInfo($this->_fullname,"tab",array("name"=>$this->curTab),"tab",array("name"=>$t));
				$html.= <<<PHTMLCODE
 &nbsp; <a href={$link}>$t</a> &nbsp; 
PHTMLCODE;

			}
		}
		return $html;
    }
    function onUserTabChanged($info){
		$this->curTab=$info['name'];
		osBroadcast("usertab_usertabChanged",array("tabName"=>$this->curTab));
		$this->_bookframe("frm");
    }

}

?>