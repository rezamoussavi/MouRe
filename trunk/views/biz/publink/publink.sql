<?PHP
	function bizsql(){
		query("CREATE TABLE IF NOT EXISTS publink_info (`pubUID` INT(11) NOT NULL AUTO_INCREMENT, `adLinkUID` INT(11) NOT NULL, `YTID` VARCHAR(50) NOT NULL, `publisher` INT(11) NOT NULL, `totalView` INT(11) NOT NULL, `AOPV` FLOAT NOT NULL, `PPV` FLOAT NOT NULL, PRIMARY KEY (`pubUID`))");
		query("CREATE TABLE IF NOT EXISTS publink_stat (`pubUID` INT(11) NOT NULL, `timeStamp` VARCHAR(14) NOT NULL, `IP` VARCHAR(15) NOT NULL, `countryCode` VARCHAR(5) NOT NULL, `countryName` VARCHAR(255) NOT NULL, INDEX (`pubUID`))");
	}

?>