<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	6/10/2011
	Ver:	1.0
	----------------------
	Author:	Reza Moussavi
	Date:	6/02/2011
	Ver:	0.5

*/
require_once 'biz/videobar/videobar.php';
require_once 'biz/ipviewer/ipviewer.php';
require_once 'biz/user/user.php';
require_once 'biz/userviewer/userviewer.php';

class linkviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $data;
	var $extra;

	//Nodes (bizvars)

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
			$_SESSION['osMsg']['frame_btnLink'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_btnUser'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_btnIP'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_btnPublisher'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		if(!isset($_SESSION['osNodes'][$fullname]['data']))
			$_SESSION['osNodes'][$fullname]['data']='';
		$this->data=&$_SESSION['osNodes'][$fullname]['data'];

		if(!isset($_SESSION['osNodes'][$fullname]['extra']))
			$_SESSION['osNodes'][$fullname]['extra']='';
		$this->extra=&$_SESSION['osNodes'][$fullname]['extra'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='linkviewer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_btnLink':
				$this->onBtnLink($info);
				break;
			case 'frame_btnUser':
				$this->onBtnUser($info);
				break;
			case 'frame_btnIP':
				$this->onBtnIP($info);
				break;
			case 'frame_btnPublisher':
				$this->onBtnPublisher($info);
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
			case 'frm':
				$_style=' style="float:left;width:750px;margin:5px;padding:5px;border:1px solid #A0FFA0;background-color:#F0FFF0;" ';
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


	/********************************
	*	Functionalities
	*********************************/
	/*
	*	$Data keys include
			all from adlink.biz database + userName
	*/
	function bookData($Data){
		$this->data=$Data;
	}
	/********************************
	*	Frames
	*********************************/
	function frm(){
		$remaining=$this->data['maxViews'] - $this->data['viewed'];
		$bgcolor=$this->data['running']==0?"gray":"transparent";
		$divStyle="padding:1px;margin:1px;height: 22px; overflow: hidden; float: left; display: inline; background-color:".$bgcolor.";";
		$btnStyle="height:100%;width:100%;border:0px;cursor:pointer;background-color:transparent;";
		$btnAction="";
		$divbtnStyle=$divStyle."border:1px solid #ccc;";
		$videoCel=$this->backLinkCel($divbtnStyle,$btnStyle,$btnAction);
		$userCel=$this->backUserCel($divbtnStyle,$btnStyle,$btnAction);
		$IPCel=$this->backIpCel($divbtnStyle,$btnStyle,$btnAction);
		$publisherCel=$this->backPublisherCel($divbtnStyle,$btnStyle,$btnAction);
		$extra=$this->backExtra();
		return <<<PHTMLCODE

			<div style="float:left">
				{$videoCel}
				{$userCel}
				<div style="width:50px; $divStyle "><a title="Paid: {$this->data['paid']}">
					{$this->data['paid']}	</a></div>
				<div style="width:50px; $divStyle "><a title="Viewd {$this->data['viewed']} times">
					{$this->data['viewed']}	</a></div>
				<div style="width:50px; $divStyle "><a title="{$remaining} times remained to be watched">
					{$remaining}</a></div>
				<div style="width:30px; $divStyle "><a title="Running for xx days">
					time</a></div>
				<div style="width:75px; $divStyle "><a title="Advertisor Pay Per View(AOPV):{$this->data['AOPV']} / Rate to be paid to publisher(APRate): {$this->data['APRate']}">
					{$this->data['AOPV']}/{$this->data['APRate']}</a></div>
				{$IPCel}
				{$publisherCel}
				<div style="width:50px; $divStyle "><a title="Interest till now for us(views website)">
					Int$	</a></div>
			</div>
			$extra
		
PHTMLCODE;

	}
	/********************************
	*	Frame Cells
	*********************************/
	function backLinkCel($divstyle,$btnstyle,$btnAction){
		$frm=$this->_fullname."Link";
		$Title=htmlspecialchars($this->data['title'], ENT_QUOTES);
		return <<<PHTMLCODE

			<div style="width:200px; $divstyle "><a title="[CLICK] Video Title: {$Title}">
				<form name="$frm" method="post">
					<input type="hidden" name="_message" value="frame_btnLink" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="{$Title}" $btnAction onclick='javascript:sndmsg("{$frm}")' style="$btnstyle" />
				</form>
			</a></div>
		
PHTMLCODE;

	}
	function backUserCel($divstyle,$btnstyle,$btnAction){
		$frm=$this->_fullname."User";
		return <<<PHTMLCODE

			<div style="width:75px; $divstyle "><a title="[CLICK] Advertisor Name: {$this->data['userName']}">
				<form name="$frm" method="post">
					<input type="hidden" name="_message" value="frame_btnUser" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="{$this->data['userName']}" $btnAction onclick='javascript:sndmsg("{$frm}")' style="$btnstyle" />
				</form>
			</a></div>
		
PHTMLCODE;

	}
	function backIPCel($divstyle,$btnstyle,$btnAction){
		$frm=$this->_fullname."IP";
		return <<<PHTMLCODE

			<div style="width:30px; $divstyle "><a title="[CLICK] visit statistics">
				<form name="$frm" method="post">
					<input type="hidden" name="_message" value="frame_btnIP" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="Stat" $btnAction onclick='javascript:sndmsg("{$frm}")' style="$btnstyle" />
				</form>
			</a></div>
		
PHTMLCODE;

	}
	function backPublisherCel($divstyle,$btnstyle,$btnAction){
		$frm=$this->_fullname."Publisher";
		return <<<PHTMLCODE

			<div style="width:50px; $divstyle "><a title="[CLICK] List of ublishers whom published this video">
				<form name="$frm" method="post">
					<input type="hidden" name="_message" value="frame_btnPublisher" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="Pubs" $btnAction onclick='javascript:sndmsg("{$frm}")' style="$btnstyle" />
				</form>
			</a></div>
		
PHTMLCODE;

	}
	/********************************
	*	Frame Extras
	*********************************/
	function backExtra(){
		switch($this->extra){
			case "link":
				return $this->backExtraLink();
				break;
			case "user":
				return $this->backExtraUser();
				break;
			case "ip":
				return $this->backExtraIP();
				break;
			case "publisher":
				return $this->backExtraPublisher();
				break;
			default:
				return " ";
				break;
		}
	}
	function backExtraLink(){
		$x=new videobar("");
		$x->bookInfo($this->data);
		$x->bookMode("short");
		$frm=$x->_backframe();
		return <<<PHTMLCODE

			<div style="float:left;margin-left:25px;margin-bottom:10px;margin-top:5px;padding:5px;border: 1px dotted gray;">
				{$frm}
			</div>
		
PHTMLCODE;

	}
	function backExtraUser(){
		$u=new user("");
		$uv=new userviewer("");
		$uv->bookModeUser("short",$u->backUserData($this->data['advertisor']));
		$frm=$uv->_backframe();
		return <<<PHTMLCODE

			<div style="float:left;margin-left:25px;margin-bottom:10px;margin-top:5px;padding:5px;border: 1px dotted gray;"> 
				{$frm}
			</div>
		
PHTMLCODE;

	}
	function backExtraIP(){
		$ipv=new ipviewer("");
		$ipv->bookInfo(isset($this->data['adUID'])?$this->data['adUID']:-1,htmlspecialchars($this->data['title'],ENT_QUOTES));
		return $ipv->_backframe();
	}
	function backExtraPublisher(){
		query("SELECT * FROM publink_info as pl, user_info as u WHERE u.userUID=pl.publisher AND pl.totalView>0 AND pl.adLinkUID=".$this->data['adUID']);
		$html="";
		while($row=fetch()){
			$u=new userviewer("");
			$u->bookModeUser("short",$row);
			$html.=$u->_backframe();
		}
		if($html=="")
			$html="EMPTY!";
		return $html;
	}
	/********************************
	*	Message Handler
	*********************************/
	function onBtnLink($info){
		$this->extra=$this->extra=="link"?" ":"link";
	}
	function onBtnUser($info){
		$this->extra=$this->extra=="user"?" ":"user";
	}
	function onBtnIP($info){
		$this->extra=$this->extra=="ip"?" ":"ip";
	}
	function onBtnPublisher($info){
		$this->extra=$this->extra=="publisher"?" ":"publisher";
	}

}

?>