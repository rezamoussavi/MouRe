<?PHP
	if(isset($_GET['test'])){
		$h="Location: http://www.".$_SERVER['SERVER_NAME']."?kill";
		$h="Location: index.php?kill";
		header($h);
		echo "<br>".$h;
	}
	echo "testing..";
	echo "<br> try test.php?test";
?>