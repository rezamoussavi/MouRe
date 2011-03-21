<?php

//########################################################
//########################################################
//   DATA BASE START
//########################################################
//########################################################

	$osdbcon = mysql_connect ($ServerAddress, $UN, $Pass);
	if ($osdbcon){mysql_select_db($DataBase,$osdbcon);}
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
		$_result= $osdbcon? mysql_query($s,$osdbcon):false;
		return $_result;
	}
	function fetch(){
		global $_result;
		if(!$_result) return;
		return mysql_fetch_assoc($_result);
	}
//########################################################
//########################################################
//   DATA BASE END
//########################################################
//########################################################

	function _D($a){
		echo "<hr />DEBUG: ".$a."<hr />";
	}

	function osBackLink($node,$curLink,$linkto){
		$ar1=array();
		$ar2=array();
		$ret=osBackLinkInfo($node,$curLink,$ar1,$linkto,$ar2);
		return $ret;
	}

	function osBackLinkInfo($node,$curLink,$curInfo,$linkto,$toInfo){
		$ret="?";
		if(! isset($_SESSION['osLink']))
			$_SESSION['osLink']=array();
		$curLink=osAttachInfo($curLink,$curInfo);
		$linkto=osAttachInfo($linkto,$toInfo);
		$_SESSION['osLink'][$node]=$curLink;//Save Current State for others
		foreach($_SESSION['osLink'] as $n=>$v){ //Create others link info for this one
			if($ret!="?"){
				$ret.="&";
			}
			if($n==$node)// put linkto info for this one requested with linkto info
				$ret.=$n."=".$linkto;
			else
				$ret.=$n."=".$v;
		}
		if($curLink==null){
			unset($_SESSION['osLink'][$node]);
		}
		return $ret;
	}

	function osAttachInfo($msg,$info){
		if(count($info)>0){
			$msg.=":";
			foreach($info as $ck=>$cv)
				$msg.=$ck."=".$cv.",";
			$msg=substr($msg,0,strlen($msg)-1);
		}
		return $msg;
	}

	function osIsAdmin(){
		return false;
	}

	function osBookUser($user){
		$_SESSION['user']=array("UID"=>-1);
		$_SESSION['user']=$user;
	}

	function osBackUser(){
		return $_SESSION['user'];
	}

	function osBroadcast($msg,$info){
		if(isset($_SESSION['osMsg'][$msg])){
			if(count($_SESSION['osMsg'][$msg])>0){
				foreach($_SESSION['osMsg'][$msg] as $node=>$v){
					osMessage($node,$msg,$info);
				}
			}
		}
	}
	function osMessage($to,$msg,$info){
		//global $node;
		$node=NULL;
		if(!isset($_SESSION['osNodes'][$to])){
			return;
		}
		if(!isset($_SESSION['osNodes'][$to]['node'])){
			$biz=$_SESSION['osNodes'][$to]['biz'];
			if($biz){
				$node=new $biz($to);
			}
		}else{
			$node=$_SESSION['osNodes'][$to]['node'];
		}
		if($node){
			$node->message($msg,$info);
		}
	}

	function osBackBizness(){
		return 1;
		global $bizbank;
		return $bizbank->bizness_id;
	}

	function osBackBizbank(){
		return 1;
		global $bizbank;
		return $bizbank->bizbank_id;
	}
	
	function osShow($callingBiz)
	{
		return '<div id="' . $callingBiz->_fullname . '">' . $callingBiz->html . '</div>';
	}

	function osParse($s){
		$msg="";
		$ar=array();
		$i=strpos($s,":");
		if($i===false){
			$msg=$s;
		}
		else{
			$msg=substr($s,0,$i);
			$params=osParseParams(substr($s,$i+1));
			$ar=osParseParamVal($params);
		}
		return array($msg,$ar);
	}

	function osParseParams($s){
		$ar=array();
		while(strlen($s)>0){
			$i=strpos($s,",");
			if($i===false)
				$i=strlen($s);
			$ar[]=substr($s,0,$i);
			$s=substr($s,$i+1);
		}
		return $ar;
	}

	function osParseParamVal($a){
		$ret=array();
		foreach($a as $s){
			$i=strpos($s,"=");
			if($i===false)
				$ret[$s]=0;
			else
				$ret[substr($s,0,$i)]=substr($s,$i+1);
		}
		return $ret;
}
?>