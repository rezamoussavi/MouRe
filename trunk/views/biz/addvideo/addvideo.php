<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	4/27/2011
	Ver:	1.0
	-----------------------
	Author:	Reza Moussavi
	Date:	4/21/2011
	Ver:	0.1

*/
require_once 'biz/transaction/transaction.php';
require_once 'biz/adlink/adlink.php';
require_once 'biz/offer/offer.php';

class addvideo {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $errMessage;

	//Nodes (bizvars)
	var $offer;

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
			$_SESSION['osMsg']['frame_addVideo'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_login'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->offer=new offer($this->_fullname.'_offer');

		if(!isset($_SESSION['osNodes'][$fullname]['errMessage']))
			$_SESSION['osNodes'][$fullname]['errMessage']="";
		$this->errMessage=&$_SESSION['osNodes'][$fullname]['errMessage'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='addvideo';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'frame_addVideo':
				$this->onAddVideo($info);
				break;
			case 'user_login':
				$this->onLoginStatusChange($info);
				break;
			case 'user_logout':
				$this->onLoginStatusChange($info);
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
				$_style=' class="addvideo_area_div"  ';
				break;
			case 'frmSuccess':
				$_style=' class="addvideo_success_div"  ';
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


	/************************************************
	*		FRAMES
	************************************************/
	function frm(){
		if(!osUserLogedin()){
			return <<<PHTMLCODE

				<div class="addvideo_login_msg">Log in first</div>
			
PHTMLCODE;

		}
		//else
		$formname=$this->_fullname;
		$of=$this->offer->backInfo();
		$t=new transaction("");
		$balance=$t->backBalance(osBackUserID());
		$countries="<option value='any'>any</option>";
		query("SELECT DISTINCT CountryName FROM ip_country ORDER BY CountryName");
		$row=fetch();
		while($row=fetch()){
			$countries.="<option value='".$row['CountryName']."'>".$row['CountryName']."</option>";
		}
		$html=<<<PHTMLCODE

			<input id="APRatio" value="{$of['APRatio']}" type="hidden"/>
			<center><font color=red>{$this->errMessage}</font><br></center>
			<span class="addvideo_title">Rocket views video submission</span>
			<br /><br />
			<span class="addvideo_description">
			Here you can submit the youtube video link to be promoted and go viral.<br/>
			Remember that higher offer on each view means encouraging more publishers, then higher number of viewers and views.
			</span>
			<br /><br />
			<form name="$formname" method="post">
				<input type="hidden" name="_message" value="frame_addVideo" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input type="hidden" class="total_price" value="0" name="total" id="theTotal" />
				<div class="addvideo_label">
					Title<font color="red">*</font>
					<span class="ToolText" onMouseOver="javascript:this.className='ToolTextHover'" onMouseOut="javascript:this.className='ToolText'">&nbsp;&nbsp;&nbsp;?
						<span>
							Choose a title to give a fast idea to publishers about your video
						</span>
					</span>
				</div>
				<div class="addvideo_field">
					<input id="theTitle" name="title" style="width:400px;" onkeypress='setTimeout("checkTitle()",100)' onchange='JavaScript:checkTitle();'><span id="msgTitle"></span>
					<br />
				</div>
				<div class="addvideo_label">
					YouTube link<font color="red">*</font>
					<span class="ToolText" onMouseOver="javascript:this.className='ToolTextHover'" onMouseOut="javascript:this.className='ToolText'">&nbsp;&nbsp;&nbsp;?
						<span>
							Copy and paste the youtube video link from browser's address bar or shortened link provided by youtube share.
						</span>
					</span>
				</div>
				<div class="addvideo_field">
					<input id="theYLink" name="link" style="width:400px;" onkeypress='setTimeout("checkYLink()",100)' onchange='JavaScript:checkYLink()'><span id="msgYLink"></span><br />
				</div>
				<div class="addvideo_label">
					Your Offer on Price/View<font color="red">*</font>
					<span class="ToolText" onMouseOver="javascript:this.className='ToolTextHover'" onMouseOut="javascript:this.className='ToolText'">&nbsp;&nbsp;&nbsp;?
						<span>
							This is the price you offer to publishers for each view counted via their publishing.
							<br> It is obvious that the higher price you offer to publishers to publish your video,
							 the more publishers will be encouraged and you will get faster and more number of views.
							 <br> It is noticeable that publishers will earn money for real views, simulated to be the closest to youtube number of views.
						</span>
					</span>
				</div>
				<div class="addvideo_field">
					<input id="theAOPV" name="AOPV" style="width:50px;" onkeypress='setTimeout("checkAOPV({$of['minAOPV']})",100)' onchange='JavaScript:checkAOPV({$of['minAOPV']})'>
					(min: {$of['minAOPV']}$) <span id="msgAOPV"></span>
					 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Publishers will earn <span id="publisher_will_earn">0</span>$ for each view
					<br />
				</div>
				<div class="addvideo_label">
					Number of Viewes<font color="red">*</font>
					<span class="ToolText" onMouseOver="javascript:this.className='ToolTextHover'" onMouseOut="javascript:this.className='ToolText'">&nbsp;&nbsp;&nbsp;?
						<span>
							This is the number of views you wish to get at least for the video submitted.
							<br> When this number is finished, publishers will not earn more money by your link,
							 so they may remove your video from where they have shared it or let it be and let you get more and more views.
						</span>
					</span>
				</div>
				<div class="addvideo_field">
					<input id="theNOV" name="NOV" style="width:50px;" onkeypress='setTimeout("checkNOV({$of['minNOV']})",100)' onchange='JavaScript:checkNOV({$of['minNOV']})'>
					(min: {$of['minNOV']})
					<span id="msgNOV"></span>
					<br />
				</div>
				<div class="addvideo_label">
					Target Country
					<span class="ToolText" onMouseOver="javascript:this.className='ToolTextHover'" onMouseOut="javascript:this.className='ToolText'">&nbsp;&nbsp;&nbsp;?
						<span>
							If you have a target country for viewers of your video, you can choose it here. 
							If a country is selected, publishers will earn for views sent only from that country. 
							<br> If you have multiple countries targeted, please submit the video for each of those countries separately, with number of views you wish to get in each.
						</span>
					</span>
				</div>
				<div class="addvideo_field">
					<SELECT name="country" style="width:150px;">$countries</SELECT>
				</div>
				<hr>
				<div class="addvideo_label">
					Total:
				</div>
				<div class="addvideo_field">
					<span id="theTotalShow" >0.0</span>$<span id="msgTotal"></span><br />
				</div>
				<div style="float:left;">
					<div class="addvideo_label">
						Your Balance:
					</div>
					<div class="addvideo_field">
						<span id="theBalance">$balance</span>$<br />
					</div>
				</div>
				<input style="float:right;" id="theButton" disabled type="button" value="Submit" onclick = "_eSetHTML('addvideowait','<img src=\'/img/loading.gif\'>');JavaScript:sndmsg('$formname')">
				<span id="addvideowait" style="float:right;"></span>
			</form>
		
PHTMLCODE;

		$this->errMessage="";
		return $html;
	}
	function frmSuccess(){
		$msg=$this->errMessage;
		$this->errMessage="";
		$this->_bookframe("frm");
		return <<<PHTMLCODE

			<center><font color=green>{$msg}</font><hr></center>
		
PHTMLCODE;

	}
	/*************************************************
	*		MESSAGES
	*************************************************/
	function onLoginStatusChange($info){
		//REFRESH AUTOMATICALLY
	}
	function onAddVideo($info){
		$of=$this->offer->backInfo();
		$t=new transaction("");
		$balance=$t->backBalance(osBackUserID());
		$e="";
		if(strpos($info['link'],"www.youtube.com/watch?v=")===FALSE && strpos($info['link'],"youtu.be/")===FALSE){
			$e.="Enter a valid link<br>";
		}
		if($info['AOPV']<$of['minAOPV']){
			$e.="Minimum Offer should be ".$of['minAOPV']."<br>";
		}
		if($info['NOV']<$of['minNOV']){
			$e.="Minimum Number of Views should be ".$of['minNOV']."<br>";
		}
		if($info['NOV']*$info['AOPV']!=$info['total']){
			$e.="Invalid total value<br>";
		}
		if(strlen($info['title'])<2){
			$e.="Invalid Title<br>";
		}
		if($info['total']>$balance){
			$e.="Insuffient balance<br>";
		}
		if(strlen($e)<2){// NO ERROR
			$al=new adlink("");
			$data=array();
			$data['advertisor']=osBackUserID();
			$data['running']=1;
			$data['lastDate']="";
			$data['startDate']=date("Y/m/d");
			$data['link']=$info['link'];
			$data['title']=$info['title'];
			$data['maxViews']=$info['NOV'];
			$data['AOPV']=$info['AOPV'];
			$data['paid']=0;
			$data['APRate']=$of['APRatio'];
			$data['minLifeTime']=$of['minLifeTime'];
			$data['minCancelTime']=$of['minCancelTime'];
			$data['country']=$info['country'];
			$data['paid']=$info['total'];
			$al->bookLink($data);
			$emb=$al->backYEmbed($data['link']);
			$e="Added Successfully<br>$emb";
			$t=new transaction("");
			$t->bookAdPay($info['AOPV']*$info['NOV'],"Ad video - title: ".$info['title']);
			$this->_bookframe("frmSuccess");
		}else{// HAS ERROR
			$e="ERROR: <br>".$e;
			$this->_bookframe("frm");
		}
		$this->errMessage=$e;
	}

}

?>