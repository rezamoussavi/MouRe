<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	5/5/2011
	Ver:		1.0

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
	var $paid;
	var $earned;
	var $reimbursed;
	var $adpay;

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
			$_SESSION['osMsg']['frame_profileBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_pubLinkBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_adLinkBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_balanceBtn'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_reCalc'][$this->_fullname]=true;
			$_SESSION['osMsg']['frame_test'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_login'][$this->_fullname]=true;
			$_SESSION['osMsg']['user_logout'][$this->_fullname]=true;
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
			case 'frame_profileBtn':
				$this->onProfileBtn($info);
				break;
			case 'frame_pubLinkBtn':
				$this->onPubLinkBtn($info);
				break;
			case 'frame_adLinkBtn':
				$this->onAdLinkBtn($info);
				break;
			case 'frame_balanceBtn':
				$this->onBalanceBtn($info);
				break;
			case 'frame_reCalc':
				$this->onReCalc($info);
				break;
			case 'frame_test':
				$this->onTest($info);
				break;
			case 'user_login':
				$this->onReCalc($info);
				break;
			case 'user_logout':
				$this->onLogout($info);
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


	/******************************
	*	Message Handlers
	******************************/
	function onReCalc($info){
		$this->reCalc();
	}
	function onTest($info){
		$t=new transaction("");
		$t->bookCharge($info['amount'],"For TEST purpose ONLY!");
		$this->reCalc();
	}
	function onLogout($info){
		$this->_bookframe("frmProfile");
	}
	function onProfileBtn($info){
		$this->_bookframe("frmProfile");
	}
	function onPubLinkBtn($info){
		$this->pubLinks->bookModeUser("mypub",osBackUserID());
		$this->_bookframe("frmPubLinks");
	}
	function onAdLinkBtn($info){
		$this->adLinks->bookModeUser("myad",osBackUserID());
		$this->_bookframe("frmAdLinks");
	}
	function onBalanceBtn($info){
		$this->_bookframe("frmBalance");
	}
	/******************************
	*	Frames
	******************************/
	function frmProfile(){
		return $this->buttons()."<hr>".$this->profile->_backframe();
	}
	function frmPubLinks(){
		return $this->buttons()."<hr>".$this->pubLinks->_backframe();
	}
	function frmAdLinks(){
		return $this->buttons()."<hr>".$this->adLinks->_backframe();
	}
	function frmBalance(){
		if(!osUserLogedin()){
			return "";
		}
		$ReCalcFrmName = $this->_fullname."reCalc";
		$testFrame=$this->_fullname."test";
		$html=<<<PHTMLCODE

			Balance: {$this->balance} $
			<a href="http://sam-rad.com/PayPal">Add</a><br />
			Paid: {$this->paid} $<br />
			adPay: {$this->adpay} $<br />
			Re-imbursed: {$this->reimbursed} $<br />
			Earned: {$this->earned} $<br />
			<form name="$ReCalcFrmName" method="post">
				<input type="hidden" name="_message" value="frame_reCalc" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input value="ReCalculate" type="button" onclick='JavaScript:sndmsg("$ReCalcFrmName")' />
			</form>
			<hr />
			<form name="$testFrame" method="post">
				<input type="hidden" name="_message" value="frame_test" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
				<input name="amount" size=5 />
				<input value="test" type="button" onclick='JavaScript:sndmsg("$testFrame")' />
			</form>
		
PHTMLCODE;

		return $this->buttons()."<hr>".$html;
	}
	function buttons(){
		$ProfileFormName=$this->_fullname."profileBtn";
		$BalanceFormName=$this->_fullname."balanceBtn";
		$pubLinkFormName=$this->_fullname."pubLinkBtn";
		$adLinkFormName=$this->_fullname."adLinkBtn";
		$html=<<<PHTMLCODE

			<div style="float:left;width:800px;">
				<form name={$ProfileFormName} method="post" style="float:left;">
					<input type="hidden" name="_message" value="frame_profileBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="Profile" onclick = 'JavaScript:sndmsg("$ProfileFormName")'  />
				</form>
				<form name={$BalanceFormName} method="post" style="float:left;">
					<input type="hidden" name="_message" value="frame_balanceBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="Balance" onclick = 'JavaScript:sndmsg("$BalanceFormName")'  />
				</form>
				<form name={$pubLinkFormName} method="post" style="float:left;">
					<input type="hidden" name="_message" value="frame_pubLinkBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="My Published Links" onclick = 'JavaScript:sndmsg("$pubLinkFormName")'  />
				</form>
				<form name={$adLinkFormName} method="post" style="float:left;">
					<input type="hidden" name="_message" value="frame_adLinkBtn" /><input type = "hidden" name="_target" value="{$this->_fullname}" />
					<input type="button" value="My Advertised Links" onclick = 'JavaScript:sndmsg("$adLinkFormName")'  />
				</form>
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
		$this->reimbursed=$all['Reimburse'];
	}

}

?>