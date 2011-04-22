<?PHP
	function bizsql(){
		query("CREATE TABLE user_info (`userUID` int(11) NOT NULL auto_increment, `email` varchar(50) NOT NULL, `password` varchar(50) NOT NULL, `verificationCode` varchar(50) NOT NULL, `biznessUID` int(11) NOT NULL, `role` varchar(50) NOT NULL COMMENT `"user)");
		query("CREATE TABLE user_"admin"'; PRIMARY KEY (`userUID`), UNIQUE KEY `email` (`email`))");
	}

?>