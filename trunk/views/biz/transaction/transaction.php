<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	14/07/2011
	Ver:	0.1

*/

class transaction {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables

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
		}

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='transaction';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
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
	}


//########################################
//         YOUR FUNCTIONS GOES HERE
//########################################


	/*************************************
	*	APIs
	*************************************/
	function bookCharge($amount,$comments){
		$this->INSERT(osBackUserID(),date("Y/m/d"),"Charge",$amount,$comments);
	}
	function bookAdPay($amount,$comments){
		$this->INSERT(osBackUserID(),date("Y/m/d"),"adPay",-$amount,$comments);
	}
	function bookWithdraw($amount,$comments){
		$this->INSERT(osBackUserID(),date("Y/m/d"),"Withdra",-$amount,$comments);
	}
	function bookReimburse($amount,$comments){
		$this->INSERT(osBackUserID(),date("Y/m/d"),"Reimburse",$amount,$comments);
	}
	function backBalance($UID){
		$earned=0;
		query("SELECT SUM(PPV * totalView) as earned FROM publink_info WHERE publisher=".$UID);
		if($row=fetch()){$earned=sprintf("%.2f",$row['earned']);}
		$trans=0;
		query("SELECT SUM(amount) as trans FROM transaction_history WHERE UID=".$UID);
		if($row=fetch()){
			$trans=$row['trans'];
		}
		return $trans+$earned;
	}
	function backUserSummary($UID){
		$ret=array("Balance"=>0,"Charge"=>0,"Earn"=>0,"Reimburse"=>0,"Withdraw"=>0,"adPay"=>0);
		query("SELECT SUM(PPV * totalView) as earned FROM publink_info WHERE publisher=".$UID);
		if($row=fetch()){$ret['Earn']=sprintf("%.2f",$row['earned']);}
		query("SELECT SUM(amount) as total FROM transaction_history WHERE UID=".$UID." AND type='Charge'");
		if($row=fetch()){$ret['Charge']=sprintf("%.2f",$row['total']);}
		query("SELECT SUM(amount) as total FROM transaction_history WHERE UID=".$UID." AND type='adPay'");
		if($row=fetch()){$ret['adPay']=sprintf("%.2f",$row['total']);}
		query("SELECT SUM(amount) as total FROM transaction_history WHERE UID=".$UID." AND type='Withdraw'");
		if($row=fetch()){$ret['Withdraw']=sprintf("%.2f",$row['total']);}
		query("SELECT SUM(amount) as total FROM transaction_history WHERE UID=".$UID." AND type='Reimburse'");
		if($row=fetch()){$ret['Reimburse']=sprintf("%.2f",$row['total']);}
		$ret['Balance']=$ret['Earn']+$ret['Charge']+$ret['adPay']+$ret['Withdraw']+$ret['Reimburse'];
		osBroadcast("transaction_update",array("balance"=>$ret['Balance']));
		return $ret;
	}
	/*************************************
	*	INTERNAL FUNCTIONS
	*************************************/
	function INSERT($UID,$date,$type,$amount,$comments){
		$s="INSERT INTO transaction_history (UID,date,type,amount,comments) ";
		$s.=" VALUE ('$UID','$date','$type','$amount','$comments');";
		query($s);
	}

}

?>