<?php
	session_start();
	echo <<<PHTML

	finalPaymentAmount =  {$_SESSION["Payment_Amount"]} <br />
	

PHTML;
	
?>
