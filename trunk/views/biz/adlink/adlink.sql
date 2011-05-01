<?PHP
	function bizsql(){
		query("CREATE TABLE adlink_info (`adUID` INT(11) NOT NULL AUTO_INCREMENT, `advertisor` INT(11) NOT NULL, `running` TINYINT(4) NOT NULL, `lastDate` VARCHAR(8) NULL, `startDate` VARCHAR(8) NOT NULL, `link` VARCHAR(255) NOT NULL,`img` VARCHAR(255) NOT NULL, `embed` VARCHAR(255) NOT NULL, `maxViews` INT(11) NOT NULL, `AOPV` FLOAT NOT NULL, `paid` FLOAT NOT NULL, `APRate` FLOAT NOT NULL, `minLifeTime` INT(11) NOT NULL, `minCancelTime` INT(11) NOT NULL, PRIMARY KEY (`adUID`),UNIQUE (`adUID`))");
	}

?>