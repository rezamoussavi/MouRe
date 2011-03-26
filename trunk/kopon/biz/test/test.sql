<?PHP
	function bizsql(){
		query("CREATE TABLE test_info (`UID` int(11) NOT NULL auto_increment,`name` varchar(50) NOT NULL, PRIMARY KEY  (`UID`))");
		query("CREATE TABLE test_list (`listID` int(11), `listname` varchar(50))");
		query("CREATE TABLE test_other (`ID` int(11), `family` varchar(100))");
	}

?>