<?php
///////////////////////////////////     Connection.START
	$UN="root";
	$Pass="zxc";
	$DataBase="MouRe2";
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
	function parseQuery($caller,$s){
		//simple replaces
		$cmd=array("#db->","#dbfilter->","#by_bizness");
		$sql=array(get_class($caller)."_","(SELECT * FROM ","WHERE biznessUID=".osBackBizness().")");
		$s=str_replace($cmd,$sql,$s);
		//functional replaces
		
		//finally
		return $s;
	}
	function xquery($caller,$s){
		global $osdbcon,$_result;
		if(!$osdbcon){ $_result=null; return;}
		$_result= mysql_query(parseQuery($caller,$s),$osdbcon);
	}
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
