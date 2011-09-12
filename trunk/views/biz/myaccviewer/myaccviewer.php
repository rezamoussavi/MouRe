<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	5/5/2011
	Ver:	1.0

*/
require_once 'biz/profileviewer/profileviewer.php';
require_once 'biz/videolistviewer/videolistviewer.php';
require_once 'biz/transaction/transaction.php';

class myaccviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $balance;
	var $withdrawn;
	var $paid;
	var $earned;
	var $reimbursed;
	var $adpay;
	var $cur_menue;
	var $withdraw_msg;

	//Nodes (bizvars)
	var $profile;
	var $pubLinks;
	var $adLinks;

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
			$_SESSION['osMsg']['page_Myacc_profile'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_Myacc_pubLink'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_Myacc_adLink'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_Myacc_balance'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_reCalc'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_withdraw'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_login'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
			$_SESSION['osMsg']['page_paypal'][$this->_fullname]=true;
		}

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmProfile';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->profile=new profileviewer($this->_fullname.'_profile');

		$this->pubLinks=new videolistviewer($this->_fullname.'_pubLinks');

		$this->adLinks=new videolistviewer($this->_fullname.'_adLinks');

		if(!isset($_SESSION['osNodes'][$fullname]['balance']))
			$_SESSION['osNodes'][$fullname]['balance']=0;
		$this->balance=&$_SESSION['osNodes'][$fullname]['balance'];

		if(!isset($_SESSION['osNodes'][$fullname]['withdrawn']))
			$_SESSION['osNodes'][$fullname]['withdrawn']=0;
		$this->withdrawn=&$_SESSION['osNodes'][$fullname]['withdrawn'];

		if(!isset($_SESSION['osNodes'][$fullname]['paid']))
			$_SESSION['osNodes'][$fullname]['paid']=0;
		$this->paid=&$_SESSION['osNodes'][$fullname]['paid'];

		if(!isset($_SESSION['osNodes'][$fullname]['earned']))
			$_SESSION['osNodes'][$fullname]['earned']=0;
		$this->earned=&$_SESSION['osNodes'][$fullname]['earned'];

		if(!isset($_SESSION['osNodes'][$fullname]['reimbursed']))
			$_SESSION['osNodes'][$fullname]['reimbursed']=0;
		$this->reimbursed=&$_SESSION['osNodes'][$fullname]['reimbursed'];

		if(!isset($_SESSION['osNodes'][$fullname]['adpay']))
			$_SESSION['osNodes'][$fullname]['adpay']=0;
		$this->adpay=&$_SESSION['osNodes'][$fullname]['adpay'];

		if(!isset($_SESSION['osNodes'][$fullname]['cur_menue']))
			$_SESSION['osNodes'][$fullname]['cur_menue']="profile";
		$this->cur_menue=&$_SESSION['osNodes'][$fullname]['cur_menue'];

		if(!isset($_SESSION['osNodes'][$fullname]['withdraw_msg']))
			$_SESSION['osNodes'][$fullname]['withdraw_msg']="";
		$this->withdraw_msg=&$_SESSION['osNodes'][$fullname]['withdraw_msg'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='myaccviewer';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'page_Myacc_profile':
				$this->onProfileBtn($info);
				break;
			case 'page_Myacc_pubLink':
				$this->onPubLinkBtn($info);
				break;
			case 'page_Myacc_adLink':
				$this->onAdLinkBtn($info);
				break;
			case 'page_Myacc_balance':
				$this->onBalanceBtn($info);
				break;
			case 'frame_reCalc':
				$this->onReCalc($info);
				break;
			case 'frame_withdraw':
				$this->onWithdraw($info);
				break;
			case 'user_login':
				$this->onReCalc($info);
				break;
			case 'user_logout':
				$this->onLogout($info);
				break;
			case 'page_paypal':
				$this->onPayPal($info);
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
			case 'frmProfile':
				$_style=' ';
				break;
			case 'frmPubLinks':
				$_style=' ';
				break;
			case 'frmAdLinks':
				$_style=' ';
				break;
			case 'frmBalance':
				$_style=' ';
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


	/******************************
	*	Functionalities
	******************************/
    private function sendPaymentEmail($amount) {
		$mailheader='MIME-Version: 1.0' . "\r\n" .
					'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
					'From: paypa!@RocketViews.com' . "\r\n" .
					'Reply-To: kian.gb@gmail.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
		$paypalemail=osBackUserPaypalEmail();
		$userID=osBackUserID();
		$t=new transaction("");
		$trans=$t->backAll();
		$u=osBackUser();
		$name=strlen($u['userName'])>0?$u['userName']:"&lt;EMPTY&gt;";
		$email=$u['email'];
		$msg=<<<PHTMLCODE

			Hi,<br />
			UserName: <b>$name</b> <br />
			Email: <b>$email</b> <br />
			Paypal Email: <b>$paypalemail</b> <br /><br />
			Amount: $amount $ <br />
			<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business={$paypalemail}&item_name=Withdraw_RocketViews&item_number={$userID}&amount={$amount}&currency_code=USD">
				Click Here to Pay
			</a> <br /><br />
			$trans<br /><br />
			Regards<br />
			RocketViews<br />
			Withdraw System<br />
		
PHTMLCODE;

        mail("reza2mussavi@hotmail.com", "Paypal Withdraw RocketViews", $msg, $mailheader);
    }
	/******************************
	*	Message Handlers
	******************************/
	function onWithdraw($info){
		////////////////////////////////////
		//	checkPass
		////////////////////////////////////
		$u=new user("");
		if($u->checkPass($info['password'])==FALSE){
			$this->withdraw_msg="<font color='red'>Wrong paypal email or Password</font>";
		}else{
			////////////////////////////////////
			//	check paypalemail
			////////////////////////////////////
			$pmail=osBackUserPaypalEmail();
			if(strlen($pmail."")<4){//1st time, add pmail
				if($u->bookPaypalEmail($info['paypalemail'],$info['password'])==TRUE){
					$pmail=$info['paypalemal'];
				}
			}
			if($info['paypalemail']!=$pmail){
				$this->withdraw_msg="<font color='red'>Wrong paypal email or Password</font>";
			}else{
				////////////////////////////////////
				//	check amount
				////////////////////////////////////
				$this->reCalc();
				if($this->balance<$info['amount']){
					$this->withdraw_msg="<font color='red'>Not enough credit in balance</font>";
				}else{
					////////////////////////////////////
					// redeuce transaction
					////////////////////////////////////
					$t=new transaction("");
					$t->bookWithdraw(-$info['amount'],"Withdraw via paypal");
					////////////////////////////////////
					// send email to us
					////////////////////////////////////
					$this->sendPaymentEmail($info['amount']);
					////////////////////////////////////
					//	Show Result
					////////////////////////////////////
					$this->withdraw_msg="<font color='green'>It will be in your paypal account in 48h</font>";
				}//Amount check
			}//paypalemail
		}//Password check
		$this->_bookframe("frmBalance");
	}
	function onReCalc($info){
		$this->reCalc();
	}
	function onPayPal($info){
		if(isset($_SESSION['paypal_paid'])){
			if($_SESSION['paypal_paid']>0){
				$txnid="UNKNOWN";
				if(isset($_SESSION['paypal_txnid'])) $txnid=$_SESSION['paypal_txnid'];
				$t=new transaction("");
				$t->bookCharge($_SESSION['paypal_paid'],"Paid via PayPal, TransactionID: ".$txnid);
				$this->reCalc();
			}
		}
		unset($_SESSION['paypal_paid']);
		unset($_SESSION['paypal_txnid']);
	}
	function onLogout($info){
		$this->_bookframe("frmProfile");
	}
	function onProfileBtn($info){
		$this->cur_menue="profile";
		$this->_bookframe("frmProfile");
	}
	function onPubLinkBtn($info){
		$this->cur_menue="mypub";
		$this->pubLinks->bookModeUser("mypub",osBackUserID());
		$this->_bookframe("frmPubLinks");
	}
	function onAdLinkBtn($info){
		$this->cur_menue="myad";
		$this->adLinks->bookModeUser("myad",osBackUserID());
		$this->_bookframe("frmAdLinks");
	}
	function onBalanceBtn($info){
		$this->cur_menue="credit";
		$this->_bookframe("frmBalance");
	}
	/******************************
	*	Frames
	******************************/
	function frmProfile(){
		return $this->buttons().$this->profile->_backframe();
	}
	function frmPubLinks(){
		return $this->buttons().$this->pubLinks->_backframe();
	}
	function frmAdLinks(){
		return $this->buttons().$this->adLinks->_backframe();
	}
	function frmBalance(){
		if(!osUserLogedin()){
			return "";
		}
		$paypal=$this->frmPaypal();
		$ReCalcFrmName = $this->_fullname."reCalc";
		$testFrame=$this->_fullname."test";
		$buttons=$this->buttons();
		$transaction_history=$this->transactionHistory();
		return<<<PHTMLCODE

			$buttons
			<div class="balance_area_div">
				<div class="balance_info">
					<div class="balance_title_area">
						<span class="balance_title_span">Credit information summary</span>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input value="ReCalculate" type="button" class="btn_flat" id="btn_recalc" onclick="_eSetHTML('recalcwait','<img src=\'/img/loading.gif\'>');JavaScript:sndevent('{$this->_fullname}','frame_reCalc')" />
						<span id="recalcwait"></span>
					</div>
					<div class="balance_label">+ Earned amount by publishing:</div>
						<div class="balance_data">{$this->earned} $</div>
					<div class="balance_label">- Withdrawn amount:</div>
						<div class="balance_data">{$this->withdrawn} $</div>
					<hr width=400 heught=1 class="balance_hr" color="#DBD7D8" /><br/>
					<div class="balance_label">+ Deposited amount:</div>
						<div class="balance_data">{$this->paid} $</div>
					<div class="balance_label">- Paid amount for your ads:</div>
						<div class="balance_data">{$this->adpay} $</div>
					<div class="balance_label">+ Reimbursed amount:<br/><font size=1>&nbsp;&nbsp;&nbsp;after stopping a video's publish</font></div>
						<div class="balance_data">{$this->reimbursed} $</div>
					<hr width=400 heught=1 class="balance_hr" color=black /><br/>
					<div class="balance_label" style="font-size:150%;">&nbsp;&nbsp;&nbsp;&nbsp;Balance:</div>
						 <div class="balance_data">{$this->balance} $</div>
				</div>
				<div class="balance_section">
					$paypal
					$transaction_history
				</div>
			</div>
		
PHTMLCODE;

	}
	function frmPaypal(){
		$userID=osBackUserID();
		$frmWithdraw=$this->_fullname."withdraw";
		$wmsg=$this->withdraw_msg;
		$this->withdraw_msg="";
		$pmail=osBackUserPaypalEmail();
		$withdrawmsg=strlen($pmail."")<4?"<span id='paypal_warning' ><b>Warning:</b> Please consider that once your paypal email account is set,<br />you cannot use other paypal accounts for future withdrawals for your own security.<br/>Please recheck the email address to be correct!</span>":"";
		return <<<PHTMLCODE

			<div id="paypal_buttons_area">
				<div id="payment_box">
					<span class="paypal_title_span">Deposit funds to your account</span><br/>
					Amount to be deposited to your account via credit card or Paypal: &#36;<input id="paypal_user_amount" size="5" value="0" onchange="JavaScript:checkPaypal();" onkeypress="JavaScript:checkPaypal();" /><span id="paypal_pay_msg"></span>
					<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" name="paypal_form">
						<input type="hidden" name="amount" value="00.00">
						<input type="hidden" name="cmd" value="_xclick">
						<input type="hidden" name="image_url" value="http://rocketviews.com/img/paypallogo.png">
						<input type="hidden" name="business" value="FCE49XXTRTKV4">
						<input type="hidden" name="lc" value="US">
						<input type="hidden" name="item_name" value="RocketViews Balance">
						<input type="hidden" name="item_number" value="7">
						<input type="hidden" name="button_subtype" value="services">
						<input type="hidden" name="no_note" value="0">
						<input type="hidden" name="custom" value="{$userID}">
						<input type="hidden" name="no_shipping" value="1">
						<input type="hidden" name="rm" value="1">
						<input type="hidden" name="return" value="http://RocketViews.com/?p=Myacc_balance">
						<input type="hidden" name="cancel_return" value="http://RocketViews.com/?p=Myacc_balance">
						<input type="hidden" name="currency_code" value="USD">
						<input type="hidden" name="handling" value="00.00">
						<input type="hidden" name="bn" value="PP-BuyNowBF:btn_paynowCC_LG.gif:NonHosted">
						<input type="image" id="paypal_button" disabled=1 src="https://www.sandbox.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
				<div id="widthraw_box">
					<span class="paypal_title_span">Withdraw funds from your account</span><br/>
					<form id="$frmWithdraw" action="" method="POST">
						<input type="hidden" name="_message" value="frame_withdraw" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
						$withdrawmsg <br />
						Paypal email: <input name="paypalemail" size="5" />
						Amount: &#36; <input name="amount" id="withdraw_amount" value="0" size="5" onchange="JavaScript:checkValidWithdraw();" onkeypress="JavaScript:checkValidWithdraw();" /><span id="paypal_withdraw_msg"></span><br />
						password: <input type="password" name="password" size="5" />
						<input type="button" disabled=1 id="withdraw_button" value="Withdraw" onclick="_eSetHTML('withdrawmessage','<img src=\'/img/loading.gif\'> Processing...');JavaScript:sndmsg('$frmWithdraw')">
					</form>
					<br /><span id="withdrawmessage">$wmsg</span>
				</div>
			</div>
		
PHTMLCODE;

	}
	function frmPaypal_OLD(){
		if(isset($_SESSION['paypal_confirm'])){
			if($_SESSION['paypal_confirm']=="true"){
				$pay=$_SESSION['paypal_amount'];
				$charge=$pay*$_SESSION['paypal_charge'];
				$total=$pay+$charge;
				$html=<<<PHTMLCODE

					<div class="balance_test">
						<form action="paypal/DoExpressCheckoutPayment.php" method="POST">
							Confirm payment to your balance at RocketViews.com
							<br/>
							<b>Add to Balance:</b> {$pay}
							<br/>
							<b>Paypal charge:</b> {$charge}
							<br />
							<hr><b>Order Total:</b>{$total}
							<br />
							<input type="submit" value="Pay" />
						</form>
					</div>
				
PHTMLCODE;

			}
		}else{
			$msg="";
			if(isset($_SESSION['paypal_done'])){
				if($_SESSION['paypal_done']="true"){
					$msg="<b><font color=green>Transaction colpleted Succesfully!</font></b>";
				}
			}
			unset($_SESSION['paypal_done']);
			$html=<<<PHTMLCODE

				<div class="balance_test">
					$msg
					<form action='./paypal/ReviewOrder.php' METHOD='POST'>
						<input name="L_AMT0" />
						<input type='image' name='submit' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif' border='0' align='top' alt='Check out with PayPal'/>
					</form>
				</div>
			
PHTMLCODE;

		}
		unset($_SESSION['paypal_confirm']);
		return $html;
	}
	function transactionHistory(){
		$tran="";
		query("SELECT * FROM transaction_history WHERE UID=".osBackUserID()." ORDER BY date DESC, TID DESC");
		while($row=fetch()){
			$tran.=<<<PHTMLCODE

				<div class="balance_row">
					<div style="float:left;width:50px;padding:3px;">{$row['TID']}</div>
					<div style="float:left;width:100px;padding:3px;">{$row['date']}</div>
					<div style="float:left;width:100px;padding:3px;">{$row['type']}</div>
					<div style="float:left;width:100px;padding:3px;">{$row['amount']}</div>
					<div style="float:left;width:400px;padding:3px;">{$row['comments']}</div>
				</div>
			
PHTMLCODE;

		}
		return <<<PHTMLCODE

			<div style="float:left;width:800px;">
				<div class="balance_row_head">
					<div style="float:left;width:50px;padding:3px;">ID</div>
					<div style="float:left;width:100px;padding:3px;">Date</div>
					<div style="float:left;width:100px;padding:3px;">Type</div>
					<div style="float:left;width:100px;padding:3px;">$</div>
					<div style="float:left;width:400px;padding:3px;">Comments</div>
				</div>
				$tran
			</div>
		
PHTMLCODE;

	}
	function buttons(){
		$ProfileFormName=$this->_fullname."profileBtn";
		$BalanceFormName=$this->_fullname."balanceBtn";
		$pubLinkFormName=$this->_fullname."pubLinkBtn";
		$adLinkFormName=$this->_fullname."adLinkBtn";
		$mnu_bg_profile=($this->cur_menue=="profile")?"#E3E1E1":"white";
		$mnu_bg_myad=($this->cur_menue=="myad")?"#E3E1E1":"white";
		$mnu_bg_mypub=($this->cur_menue=="mypub")?"#E3E1E1":"white";
		$mnu_bg_credit=($this->cur_menue=="credit")?"#E3E1E1":"white";
		$link_profile=osBackPageLink("Myacc_profile");
		$link_pubLink=osBackPageLink("Myacc_pubLink");
		$link_adLink=osBackPageLink("Myacc_adLink");
		$link_balance=osBackPageLink("Myacc_balance");
		$html=<<<PHTMLCODE

		    <div id="content_menu"> 
		        <div class="content_menu_lst" style="background-Color:$mnu_bg_profile;" id="cml_1" onmouseover="menu_hover('cml_1')" onmouseout="menu_out('cml_1','$mnu_bg_profile')" onclick="window.location.href='{$link_profile}';">Profile</div> 
		        <div class="content_menu_lst" style="background-Color:$mnu_bg_mypub;" id="cml_2" onmouseover="menu_hover('cml_2')" onmouseout="menu_out('cml_2','$mnu_bg_mypub')" onclick="window.location.href='{$link_pubLink}';">My Published Videos</div> 
		        <div class="content_menu_lst" style="background-Color:$mnu_bg_myad;" id="cml_3" onmouseover="menu_hover('cml_3')" onmouseout="menu_out('cml_3','$mnu_bg_myad')" onclick="window.location.href='{$link_adLink}';">My Ads & Virals</div> 
		        <div class="content_menu_lst" style="background-Color:$mnu_bg_credit;" id="cml_4" onmouseover="menu_hover('cml_4')" onmouseout="menu_out('cml_4','$mnu_bg_credit')" onclick="window.location.href='{$link_balance}';">Credit</div> 
		    </div> 
		
PHTMLCODE;

		return $html;
	}
	/******************************
	*	Functionalities
	******************************/
	function reCalc(){
		$t=new transaction("");
		$all=$t->backUserSummary(osBackUserID());
		$this->balance=$all['Balance'];
		$this->paid=$all['Charge'];
		$this->adpay=$all['adPay'];
		$this->earned=$all['Earn'];
		$this->withdrawn=$all['Withdrawn'];
		$this->reimbursed=$all['Reimburse'];
	}

}

?>