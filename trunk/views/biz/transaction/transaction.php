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
	function backAll(){
		$UID=osBackUserID();
		$q="SELECT * FROM transaction_history WHERE UID=".$UID." ORDER BY TID DESC";
		query($q);
		$html="<table border='1'>";
		$html.="<tr><th>TransactionID</th><th>date</th><th>type</th><th>amount</th><th>comments</th></tr>";
		while($row=fetch()){
			$html.="<tr>";
			$html.="<td>".$row['TID']."</td><td>".$row['date']."</td><td>".$row['type']."</td><td>".$row['amount']."</td><td>".$row['comments']."</td>";
			$html.="</tr>";
		}
		$html.="</table>";
		return $html;
	}
	function bookWithdraw($amount,$comments){
		$this->INSERT(osBackUserID(),date("Y/m/d"),"Withdraw",$amount,$comments);
	}
	function bookCharge($amount,$comments){
		$this->INSERT(osBackUserID(),date("Y/m/d"),"Charge",$amount,$comments);
	}
	function bookAdPay($amount,$comments){
		$this->INSERT(osBackUserID(),date("Y/m/d"),"adPay",-$amount,$comments);
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
		return sprintf("%.2f",$trans+$earned);
	}
	function backUserSummary($UID){
		$this->calcReimburse($UID);
		$ret=array("Balance"=>0,"Charge"=>0,"Earn"=>0,"Reimburse"=>0,"Withdrawn"=>0,"adPay"=>0);
		/*  Earned  */
		query("SELECT SUM(PPV * totalView) as earned FROM publink_info WHERE publisher=".$UID);
		if($row=fetch()){$ret['Earn']=sprintf("%.2f",$row['earned']);}
		/*  Withdrawn  */
		query("SELECT SUM(amount) as withdrawn FROM transaction_history WHERE type='Withdraw' AND UID=".$UID);
		if($row=fetch()){$ret['Withdrawn']=sprintf("%.2f",$row['withdrawn']);}
		/*  Deposit  */
		query("SELECT SUM(amount) as total FROM transaction_history WHERE UID=".$UID." AND type='Charge'");
		if($row=fetch()){$ret['Charge']=sprintf("%.2f",$row['total']);}
		/*  adPay  */
		query("SELECT SUM(amount) as total FROM transaction_history WHERE UID=".$UID." AND type='adPay'");
		if($row=fetch()){$ret['adPay']=sprintf("%.2f",$row['total']);}
		/*  Reimburse  */
		query("SELECT SUM(amount) as total FROM transaction_history WHERE UID=".$UID." AND type='Reimburse'");
		if($row=fetch()){$ret['Reimburse']=sprintf("%.2f",$row['total']);}
		$ret['Balance']=$ret['Earn']+$ret['Charge']+$ret['adPay']+$ret['Withdrawn']+$ret['Reimburse'];
		osBroadcast("transaction_update",array("balance"=>$ret['Balance']));
		return $ret;
	}
	function calcReimburse($UID){
		while(1){
			query("SELECT * FROM adlink_info WHERE advertisor=$UID AND running=0 AND reimbursed=0 AND maxViews>viewed");
			if(!($row=fetch())) return;
			$re=($row['maxViews']-$row['viewed'])*$row['AOPV'];
			query("UPDATE adlink_info SET reimbursed=".$re." WHERE adUID=".$row['adUID']);
			$this->INSERTonly($UID,$row['lastDate'],"Reimburse",$re,"Paid ".$row['paid']." for ".$row['maxViews']."(each ".$row['AOPV'].") But viewed ".$row['viewed']." times.");
		}
	}
	/*************************************
	*	INTERNAL FUNCTIONS
	*************************************/
	function INSERTonly($UID,$date,$type,$amount,$comments){
		$s="INSERT INTO transaction_history (UID,date,type,amount,comments) ";
		$s.=" VALUE ('$UID','$date','$type','$amount','$comments');";
		query($s);
	}
	function INSERT($UID,$date,$type,$amount,$comments){
		$this->INSERTonly($UID,$date,$type,$amount,$comments);
		$this->backUserSummary(osBackUserID());
	}

}

?>