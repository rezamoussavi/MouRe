<?PHP
	function bizsql(){
		query("CREATE TABLE IF NOT EXISTS user_info (`userUID` int(11) NOT NULL auto_increment, `email` varchar(50) NOT NULL, `paypalemail` varchar(50), `userName` varchar(50) NOT NULL, `BDate` varchar(10) NOT NULL, `Address` varchar(255) NOT NULL, `Country` varchar(100) NOT NULL, `PostalCode` varchar(25) NOT NULL, `password` varchar(50) NOT NULL, `verificationCode` varchar(50) NOT NULL, `biznessUID` int(11) NOT NULL, `role` varchar(50) NOT NULL COMMENT `user / admin`, PRIMARY KEY  (`userUID`), UNIQUE KEY `email` (`email`))");
	}

?>