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
	var $contactus_msg;

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
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_contactusMessage'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_Myacc_profile'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_Myacc_pubLink'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_Myacc_adLink'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_Myacc_balance'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_AdVideo'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_PubVideo'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_Home'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_How'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_contactus'][$this->_fullname]=true;
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

		if(!isset($_SESSION['osNodes'][$fullname]['contactus_msg']))
			$_SESSION['osNodes'][$fullname]['contactus_msg']="";
		$this->contactus_msg=&$_SESSION['osNodes'][$fullname]['contactus_msg'];

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
			case 'user_logout':
				$this->onLogOut($info);
				break;
			case 'frame_contactusMessage':
				$this->onSendContactMessage($info);
				break;
			case 'page_Myacc_profile':
				$this->onMyAcc($info);
				break;
			case 'page_Myacc_pubLink':
				$this->onMyAcc($info);
				break;
			case 'page_Myacc_adLink':
				$this->onMyAcc($info);
				break;
			case 'page_Myacc_balance':
				$this->onMyAcc($info);
				break;
			case 'page_AdVideo':
				$this->onAdVideo($info);
				break;
			case 'page_PubVideo':
				$this->onPubVideo($info);
				break;
			case 'page_Home':
				$this->onHome($info);
				break;
			case 'page_How':
				$this->onHow($info);
				break;
			case 'page_contactus':
				$this->onContactUs($info);
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
				$_style=' ';
				break;
			case 'frmAdVideo':
				$_style=' ';
				break;
			case 'frmPubVideo':
				$_style=' ';
				break;
			case 'frmUser':
				$_style=' class="user_content_container"  ';
				break;
			case 'frmAdmin':
				$_style=' ';
				break;
			case 'frmHow':
				$_style=' ';
				break;
			case 'frmContactUs':
				$_style=' class="contact_us_container"  ';
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


	/**************************************
	*	MESSAGE Handlers
	**************************************/
	function sndmail($name,$email,$subject,$message){
		$mailheader='MIME-Version: 1.0' . "\r\n" .
					'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
					'From: Contactus@RocketViews.com' . "\r\n" .
					'Reply-To: kian.gb@gmail.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
		$msg=<<<PHTMLCODE

			Hi,<br />
			UserName: <b>$name</b> <br />
			Email: <b>$email</b> <br />
			Subject: <b>$subject</b> <br /><br />
			Message: <br />$message<br /><br />
		
PHTMLCODE;

        mail("kian.gb@gmail.com", "Contact Us Form from RocketViews", $msg, $mailheader);
	}
	function onSendContactMessage($info){
		$this->userpage=0;
		$this->sndmail($info['user_name'],$info['user_email'],$info['user_subject'],htmlspecialchars($info['user_message'],ENT_QUOTES));
		$this->contactus_msg="Sent successfully";
		$this->_bookframe("frmContactUs");
	}
	function onAdVideo($info){
		$this->userpage=0;
		$this->_bookframe("frmAdVideo");
	}
	function onPubVideo($info){
		$this->userpage=0;
		$this->_bookframe("frmPubVideo");
	}
	function onLogOut($info){
		if($this->userpage==1){
			$this->userpage=0;
			$this->_bookframe("frmPubVideo");
		}
	}
	function onHow($info){
		$this->userpage=0;
		$this->_bookframe("frmHow");
	}
	function onHome($info){
		$this->userpage=0;
		$this->_bookframe("frmHome");
	}
	function onContactUs($info){
		$this->userpage=0;
		$this->_bookframe("frmContactUs");
	}
	function onMyAcc($info){
		$this->userpage=1;
		if(osBackUserRole()=="admin"){
			$this->_bookframe("frmAdmin");
		}else if(osBackUserRole()=="user"){
			$this->_bookframe("frmUser");
		}else{
			$this->_bookframe("frmPubVideo");
		}
	}
	//////////////////////////////////////////////////////////////////////
	//			VIEW
	//////////////////////////////////////////////////////////////////////
	function frmButtons(){
		return "";
		$adPage=osBackPageLink("AdVideo");
		$pubPage=osBackPageLink("PubVideo");
		return <<<PHTMLCODE

			<div id="publish_btns">
				<a href="$pubPage"><button id="publish_btn" type="button"></button></a>
				<a href="$adPage"><button id="advertise_btn" type="button"></button></a>
			</div>
		
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
		$btn=$this->frmButtons();
		$this->VideoList->bookModeUser("topublish",-1);
		$VList=$this->VideoList->_backframe();
		return <<<PHTMLCODE

			<div id="showbox">
				<div id="showbox_content_home" class="showbox_content"></div>
			</div>
			<div class="content_container" >
				$btn
				<div class="video_list_title">Publishing Videos</div>
				$VList
			</div>
		
PHTMLCODE;

	}
	function frmContactUs(){
		$buttons=$this->frmButtons();
		$frmID=$this->_fullname."contactus";
		$themsg=$this->contactus_msg;
		$this->contactus_msg="";
		return <<<PHTMLCODE

			<div class="contact_us_area_div" >
				<br /><br /><br />
				<span class="profile_title_span">Contact Us</span>
				<br /><br />
				<form id="{$frmID}" class="persinf_frm" method="post">
					<input type="hidden" name="_message" value="frame_contactusMessage" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<div class="contact_row">
						<label class="bold" id="profile_email_lbl">Name </label>
						<input class="persinf_inp" id="profile_email_inp" type="text" size="30" name="user_name"/>
					</div>
					<div class="contact_row">
						<label class="bold" id="profile_email_lbl">Email </label>
						<input class="persinf_inp" id="profile_email_inp" type="text" size="30" name="user_email"/>
					</div>
					<div class="contact_row">
						<label class="bold" id="profile_email_lbl">Subject </label>
						<input class="persinf_inp" id="profile_email_inp" type="text" size="30" name="user_subject"/>
					</div>
					<div class="contact_row_txt">
						<label class="bold" id="profile_email_lbl">Message </label>
						<textarea class="persinf_inp" rows="10" cols="29" name="user_message"></textarea>
					</div>
					<div class="contact_row">
						<input class="persinf_inp" type="button" value="Send" onclick="_eSetHTML('contactwait','<img src=\'/img/loading.gif\'>');JavaScript:sndmsg('{$frmID}');"/>
						<span id="contactwait" style="padding-top: 4px;font-size:13px;color:green;margin-right:10px;float:right;">{$themsg}</span>
					</div>					
				</form>
			</div>
		
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

			<div id="showbox">
				<div id="showbox_content_pubvideo" class="showbox_content"></div>
			</div>
			<div class="content_container" >
				$btn
				<div class="video_list_title">Publishing Videos</div>
				$VList
			</div>
		
PHTMLCODE;

	}
	function frmAdVideo(){
		$btn=$this->frmButtons();
		$adV=$this->AddVideo->_backframe();
		return <<<PHTMLCODE

			<div id="showbox" style="height:432px;">
				<div id="showbox_content_addvideo" class="showbox_content"></div>
			</div>
			<div class="content_container" >
				$btn
				$adV
			</div>
		
PHTMLCODE;

	}

}

?>