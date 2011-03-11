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

	Author: Max Mirkia
	Date:	2/14/2011
	Version: 1.0
	------------------
	Author: Max Mirkia
	Date:	2/7/2011
	Version: 0.1

*/
require_once '../biz/usertab/usertab.php';

class usertabbank {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables

	//Nodes (bizvars)
	var $usertab; // array of biz

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
			$_SESSION['osMsg']['usertab_usertabChanged'][$this->_fullname]=true;
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		//handle arrays
		$this->usertab=array();
		if(!isset($_SESSION['osNodes'][$fullname]['usertab']))
			$_SESSION['osNodes'][$fullname]['usertab']=array();
		foreach($_SESSION['osNodes'][$fullname]['usertab'] as $arrfn)
			$this->usertab[]=new usertab($arrfn);

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='usertabbank';
	}

	function gotoSleep() {
		$_SESSION['osNodes'][$this->_fullname]['usertab']=array();
		$_SESSION['osNodes'][$this->_fullname]['sleep']=true;
		foreach($this->usertab as $node){
			$_SESSION['osNodes'][$this->_fullname]['usertab'][]=$node->_fullname;
		}
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'usertab_usertabChanged':
				$this->onUserTabChanged($info);
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
		$_style='';
		switch($this->_curFrame){
			case 'frm':
				$_style='';
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
        $this->usertab=array();
        $id=0;
        foreach($content as $c){
            $this->usertab[]=new usertab($this->_fullname.$c);
            end($this->usertab)->bookLabel($c);
        }
        //$this->_bookframe("frm");
    }
    function frm(){
		$html='';
		foreach($this->usertab as $t){
			$html.=$t->_backframe();
		}
		return $html;
    }
    function onUserTabChanged($info){
        foreach($this->usertab as $t){
            if($t->backLabel()==$info['usertabName']){
                $t->bookSelected(true);
            }else{
                $t->bookSelected(false);
            }
        }
    }

}

?>