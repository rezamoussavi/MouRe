<?php
///////////////////////////////////     Connection.START
	$UN="salamroo_os";
	$Pass="salamroo_os_pass";
	$DataBase="salamroo_os";
	$ServerAddress="localhost";
	$osdbcon = mysql_connect ($ServerAddress, $UN, $Pass);
	if ($osdbcon) 
	{
		if(!mysql_select_db($DataBase,$osdbcon))
			echo "Not DB selected<br>";
	}
	else
	{
		echo("can not open DB");
	}
	$_result=null;
	function query($s){
		global $osdbcon,$_result;
		if(!$osdbcon){ $_result=null; return;}
		$_result= mysql_query($s,$osdbcon);
	}
	function fetch(){
		global $_result;
		if(!$_result) return;
		return mysql_fetch_assoc($_result);
	}
///////////////////////////////////     Connection.END
///////////////////////////////////
?>
