<?PHP
	function bizsql(){
		query("CREATE TABLE IF NOT EXISTS transaction_history (`TID` INT NOT NULL AUTO_INCREMENT, `UID` INT NOT NULL, `date` VARCHAR( 10 ) NOT NULL, `type` VARCHAR( 50 ) NOT NULL, `amount` FLOAT NOT NULL, `comments` TEXT NOT NULL ,PRIMARY KEY (  `TID` ))");
	}

?>