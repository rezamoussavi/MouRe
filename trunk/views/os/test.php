<?PHP
	echo ini_get("open_basedir")."<br>";
	echo getcwd()."<br>".$_GET["ad"]."<br>";
	if(is_dir($_GET["ad"]))
		echo "it is";
	else
		echo "Not";
	if(isset($_GET["mk"])){
		if(mkdir($_GET["mk"]))
			echo "<br> made ".$_GET["mk"]."<br>";
		else
			echo "<br> can NOT made ".$_GET["mk"]."<br>";
	}
	/* REMOVE DIR */
	if(isset($_GET["rm"])){
		if(rmdir($_GET["rm"]))
			echo "<br> removee ".$_GET["rm"]."<br>";
		else
			echo "<br> can NOT remove ".$_GET["rm"]."<br>";
	}
	/* FILE */
	
	$ourFileName = "testFile.txt";
	$ourFileHandle = fopen($ourFileName, 'w');
	echo "<hr>can't open file ".$php_errormsg;
	fclose($ourFileHandle);
?>
