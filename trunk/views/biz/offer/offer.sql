<?PHP
	function bizsql(){
		query("CREATE TABLE IF NOT EXISTS offer_info (`offerUID` int(11) NOT NULL auto_increment, `available` tinyint(4), `minAOPV` float NOT NULL, `APRatio` int(11) NOT NULL, `minLifeTime` int(11) NOT NULL, `minCancelTime` int(11) NOT NULL, `minNOV` int(11) NOT NULL, PRIMARY KEY  (`offerUID`))");
	}

?>