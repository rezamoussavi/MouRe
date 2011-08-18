<?PHP
require "../db.php";
require "../core.php";
// PHP 4.1

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
$oslog="";
foreach ($_POST as $key => $value) {
$oslog.=$key." : ".$value."<br/>";
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}
// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('ssl://sandbox.paypal.com', 443, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];


$POST_payment_gross=$_POST['payment_gross'];
$POST_payment_fee=$_POST['payment_fee'];
if (!$fp) {
	// HTTP ERROR
} else {
	$ok="false";
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment
			$ok="ok";
		}
		else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
		}
	}
	if($ok=="ok"){
		query("SELECT count(txn_id) as cnt FROM transaction_history WHERE txn_id='".$txn_id."';");
		$row=fetch();
		if($row['cnt']==0){
			$amount=$POST_payment_gross-$POST_payment_fee;
			$s="INSERT INTO transaction_history (UID,date,type,amount,comments,txn_id) ";
			$s.=" VALUE ('".$_POST['custom']."','".date("Y/m/d")."','Charge','".$amount."','You paid ".$POST_payment_gross." - ".$POST_payment_fee."(paypal fee)','".$txn_id."');";
			query($s);
			$oslog.="<br><font color=red>VERIFIED</font><hr>".$s;
		}
		else
			$oslog.="<br><font color=red>VERIFIED - Already applied to DB</font><hr>".$s;
	}else{
			$oslog.="<br><font color=red>INVALID</font>";
	}
	fclose ($fp);
}
osLog("PayPal","IPN",$oslog);
?>
