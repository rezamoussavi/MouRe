<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	4/21/2011
	Ver:		0.1

*/
require_once 'biz/addvideo/addvideo.php';
require_once 'biz/videolistviewer/videolistviewer.php';
require_once 'biz/myaccviewer/myaccviewer.php';
require_once 'biz/adminviewer/adminviewer.php';

class mainpageviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $userpage;

	//Nodes (bizvars)
	var $AddVideo;
	var $VideoList;
	var $UserPanel;
	var $AdminPanel;

	function __construct($fullname) {
		$this->_tmpNode=false;
		if($fullname==null){
			$fullname='_tmpNode_'.count($_SESSION['osNodes']);
			$this->_tmpNode=true;
		}
		$this->_fullname=$fullname;
		if(!isset($_SESSION['osNodes'][$fullname])){
			$_SESSION['osNodes'][$fullname]=array();
			//If any message need to be registered will placed here
			$_SESSION['osMsg']['tab_tabChanged'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_showMyAccount'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_AdVideo'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_PubVideo'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmHome';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->AddVideo=new addvideo($this->_fullname.'_AddVideo');

		$this->VideoList=new videolistviewer($this->_fullname.'_VideoList');

		$this->UserPanel=new myaccviewer($this->_fullname.'_UserPanel');

		$this->AdminPanel=new adminviewer($this->_fullname.'_AdminPanel');

		if(!isset($_SESSION['osNodes'][$fullname]['userpage']))
			$_SESSION['osNodes'][$fullname]['userpage']=0;
		$this->userpage=&$_SESSION['osNodes'][$fullname]['userpage'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='mainpageviewer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'tab_tabChanged':
				$this->onTabChanged($info);
				break;
			case 'user_showMyAccount':
				$this->onMyAcc($info);
				break;
			case 'user_logout':
				$this->onLogOut($info);
				break;
			case 'frame_AdVideo':
				$this->onAdVideo($info);
				break;
			case 'frame_PubVideo':
				$this->onPubVideo($info);
				break;
			default:
				break;
		}
	}

	function _bookframe($frame){
		$this->_curFrame=$frame;
		//$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$_style='';
		switch($this->_curFrame){
			case 'frmHome':
				$_style=' style="float:left; width:700;" ';
				break;
			case 'frmAdVideo':
				$_style=' style="float:left; width:700;" ';
				break;
			case 'frmPubVideo':
				$_style=' style="float:left; width:700;" ';
				break;
			case 'frmUser':
				$_style=' style="float:left; width:700;" ';
				break;
			case 'frmAdmin':
				$_style=' style="float:left; width:700;" ';
				break;
			case 'frmHow':
				$_style=' style="float:left; width:700;" ';
				break;
		}
		$html='<script type="text/javascript" language="Javascript">';
		$html.=<<<JAVASCRIPT

JAVASCRIPT;
		$html.=<<<JSONDOCREADY
function {$this->_fullname}(){}
JSONDOCREADY;
		$html.='</script>
<div '.$_style.' id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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


	//////////////////////////////////////////////////////////////////////
	//			MESSAGE
	//////////////////////////////////////////////////////////////////////
	function onAdVideo($info){
		$this->userpage=0;
		$this->_bookframe("frmAdVideo");
	}
	function onPubVideo($info){
		$this->userpage=0;
		$this->_bookframe("frmPubVideo");
	}
	function onLogOut(){
		if($this->userpage==1){
			$this->userpage=0;
			$this->_bookframe("frmHome");
		}
	}
	function onTabChanged($info){
		switch($info['tabName']){
			case "Home":
				$this->userpage=0;
				$this->_bookframe("frmHome");
				break;
			case "How":
				$this->userpage=0;
				$this->_bookframe("frmHow");
				break;
		}
	}
	function onMyAcc($info){
		if(osBackUserRole()=="admin"){
			$this->userpage=1;
			$this->_bookframe("frmAdmin");
		}else{
			$this->userpage=1;
			$this->_bookframe("frmUser");
		}
	}
	function on2($info){
		$this->userpage=0;
	}
	function on3($info){
		$this->userpage=0;
	}
	//////////////////////////////////////////////////////////////////////
	//			VIEW
	//////////////////////////////////////////////////////////////////////
	function frmButtons(){
		$frmAdName=$this->_fullname."advideo";
		$frmPubName=$this->_fullname."pubvideo";
		return <<<PHTMLCODE

			<div style="width:100%; float:left; text-align:center;">
				<form name="$frmAdName" method="post" style="display:inline;">
					<input type="hidden" name="_message" value="frame_AdVideo" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input value ="Ad a Video" type = "button" onclick = 'JavaScript:sndmsg("$frmAdName")' class="press" style="margin-top: 10px; margin-right: 50px;" />
				</form>
				<form name="$frmPubName" method="post" style="display:inline;">
					<input type="hidden" name="_message" value="frame_PubVideo" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input value ="Publish a Video" type = "button" onclick = 'JavaScript:sndmsg("$frmPubName")' class="press" style="margin-top: 10px; margin-right: 50px;" />
				</form>
			</div><hr>
		
PHTMLCODE;

	}
	function frmHow(){
		$buttons=$this->frmButtons();
		return <<<PHTMLCODE

			$buttons
			How Page
		
PHTMLCODE;

	}
	function frmHome(){
		$buttons=$this->frmButtons();
		return <<<PHTMLCODE

			$buttons
			Home Page
		
PHTMLCODE;

	}
	function frmAdmin(){
		$Admin=$this->AdminPanel->_backframe();
		return <<<PHTMLCODE

			$Admin
		
PHTMLCODE;

	}
	function frmUser(){
		$U=$this->UserPanel->_backframe();
		return <<<PHTMLCODE

			$U
		
PHTMLCODE;

	}
	function frmPubVideo(){
		$btn=$this->frmButtons();
		$this->VideoList->bookModeUser("topublish",-1);
		$VList=$this->VideoList->_backframe();
		return <<<PHTMLCODE

			$btn <br> $VList
		
PHTMLCODE;

	}
	function frmAdVideo(){
		$btn=$this->frmButtons();
		$adV=$this->AddVideo->_backframe();
		return <<<PHTMLCODE

			$btn <br> $adV
		
PHTMLCODE;

	}

}

?>