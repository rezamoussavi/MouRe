<?PHP
	function bizsql(){
		query("CREATE TABLE adlink_info (`adUID` INT(11) NOT NULL AUTO_INCREMENT, `title` VARCHAR(255) NULL, `advertisor` INT(11) NOT NULL, `running` TINYINT(4) NOT NULL, `lastDate` VARCHAR(10) NULL, `startDate` VARCHAR(10) NOT NULL, `link` VARCHAR(255) NOT NULL,`img` VARCHAR(255) NOT NULL, `embed` VARCHAR(255) NOT NULL, `maxViews` INT(11) NOT NULL, `viewed` INT(11) NOT NULL, `AOPV` FLOAT NOT NULL, `paid` FLOAT NOT NULL, `reimbursed` INT(11) NOT NULL, `APRate` FLOAT NOT NULL, `minLifeTime` INT(11) NOT NULL, `minCancelTime` INT(11) NOT NULL, PRIMARY KEY (`adUID`))");
	}

?>