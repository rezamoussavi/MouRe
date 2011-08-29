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
		if(!($_result= $osdbcon? mysql_query($s,$osdbcon):false)){
			osLog("os","query()","calling mysql_query() <b><font color=red>ERROR</font><br />Query:</b><br />".$s);
		}
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

	function osLog($Biz,$NodeID,$Message){
		$t=$_SERVER['REQUEST_TIME'];
		query("INSERT INTO os_log(TimeStamp,Biz,NodeID,Message) VALUES('$t','$Biz','$NodeID','$Message')");
	}

	function osBackPageLink($page){
		$_SESSION['osPage']=$page;
		return "http://".$_SERVER['SERVER_NAME']."?p=".$page;
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

	function osBookUser($user){
		$_SESSION['user']=array("userUID"=>-1);
		$_SESSION['user']=$user;
	}

	function osBackUser(){
		return $_SESSION['user'];
	}

	function osBackUserID(){
		return isset($_SESSION['user']['userUID'])? $_SESSION['user']['userUID'] : -1;
	}

	function osBackUserEmail(){
		return isset($_SESSION['user']['email'])? $_SESSION['user']['email'] : -1;
	}

	function osBackUserPaypalEmail(){
		return isset($_SESSION['user']['paypalemail'])? $_SESSION['user']['paypalemail'] : -1;
	}

	function osUserLogedin(){
		return isset($_SESSION['user']['userUID'])? ($_SESSION['user']['userUID']>-1)?true:false : false;
	}

	function osBackUserRole(){
		return osUserLogedin()? $_SESSION['user']['role']:false;
	}

	function osIsAdmin(){
		return (isset($_SESSION['user']['role']))? ($_SESSION['user']['role']=='admin'):false;
	}

	function osBroadcast($msg,$info){
		if($_SESSION['logMessages']){
			osLog("OS","BroadCast","<b>MSG:</b> $msg<br><b>INFO:</b><br>".arr2str($info));
		}
		if(isset($_SESSION['osMsg'][$msg])){
			if(count($_SESSION['osMsg'][$msg])>0){
				foreach($_SESSION['osMsg'][$msg] as $node=>$v){
					osMessage($node,$msg,$info);
				}
			}
		}
	}
	function osMessage($to,$msg,$info){
		if($node=osBackNode($to)){
			$node->message($msg,$info);
			if($_SESSION['logMessages']){
				$data=arr2str($info);
				$log=<<<MSGLOG
					$msg <b>Sent</b><br>
					<b>TO: </b>$to
					<br><b>DATA:</b><br>
					$data
MSGLOG;
				osLog("OS","osMessage","$log");
			}
		}
	}

	function osBackNode($FName){
		/*
			Evaluate the given FullName
		*/
		if(!isset($_SESSION['osNodes'][$FName])){
			if($_SESSION['logMessages']){osLog("OS","osBackNode","Node not found : $FName");}
			return false;
		}
		/*
			If node is constructed(awake)
		*/
		if(isset($_SESSION['osNodes'][$FName]['node'])){
			if(is_object($_SESSION['osNodes'][$FName]['node']))
			{
				if($_SESSION['logMessages']){osLog("OS","osBackNode","Node is Awake : $FName");}
				return $_SESSION['osNodes'][$FName]['node'];
			}
		}
		/*
			If node not awake, wake it up
		*/
		$biz=$_SESSION['osNodes'][$FName]['biz'];
		if($biz){
			if($_SESSION['logMessages']){osLog("OS","osBackNode","Node Woked Up : $FName");}
			return new $biz($FName);
		}

		if($_SESSION['logMessages']){osLog("OS","osBackNode","Node ERROR : $FName");}		
		return false;
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

	function showLogPage(){
		if(isset($_GET['delete'])){
			query("DELETE FROM os_log WHERE logID=".$_GET['delete']);
		}
		if(isset($_GET['deleteto'])){
			query("DELETE FROM os_log WHERE logID <= ".$_GET['deleteto']);
		}
		if(isset($_GET['deletefrom'])){
			query("DELETE FROM os_log WHERE logID >= ".$_GET['deletefrom']);
		}
		query("SELECT * FROM os_log ORDER BY logID DESC");
		$html="<html>";
		$html.="<head><meta HTTP-EQUIV='refresh' CONTENT='";
		$html.=isset($_GET['auto'])?"1":"60";
		$html.="; ?log";
		if(isset($_GET['auto']))	$html.="&auto";
		$html.="'></head><body>";
		$html.=<<<HTMLSTYLE
			<style type='text/css'>
				table.log{
					border-width:1px;
					border-style:solid;
				}
				table.log td{
					border-width:1px;
					border-style:dotted;
				}
			</style>
HTMLSTYLE;
		$html.="<a href='?log'>ManualRefresh</a><br>";
		if(isset($_GET['auto']))
			$html.="<a href='?log'>Turn OFF AutoRefresh</a>";
		else
			$html.="<a href='?log&auto'>Turn ON AutoRefresh</a><font color=gray size=1>Refresh time On:1sec OFF:2min</font>";
		$html.=" <br>for LogMessages : <font size=1 color=gray>use ?message=on or ?message=off on open page of site</font>";
		$html.="<table class='log'><tr>";
		$html.="<td>ID</td>";
		$html.="<td>Time</td>";
		$html.="<td>Biz</td>";
		$html.="<td>NodeID</td>";
		$html.="<td>Message</td>";
		$html.="<td></td><td></td><td></td></tr>";
		$c=array("#FFFFFF","#FFCCFF","#99FF99","#CCFF33");
		$s=count($c);
		$i=0;
		$t=" ";
		while($row=fetch()){
			if($t!=$row['TimeStamp']){
				$t=$row['TimeStamp'];
				//$i=($i==$s-1)?0:$i+1;
				$html.="<tr bgcolor='#000000'><td colspan=8></td></tr>";
			}
			$bg=$c[$i];
			$html.="<tr bgcolor='$bg'>";
			$html.="<td><font size=1>".$row['logID']."</font></td>";
			$html.="<td><font size=1>".$row['TimeStamp']."</font></td>";
			$html.="<td><font size=1>".$row['Biz']."</font></td>";
			$html.="<td><font size=2>".$row['NodeID']."</font></td>";
			$html.="<td><font size=2>".$row['Message']."</font></td>";
			$html.="<td><a title='Delete this Row' href='?log&delete=".$row['logID']."'><img src='delete.jpg' /></a></td>";
			$html.="<td><a title='Delete All rows from this Down' href='?log&deleteto=".$row['logID']."'>&or;</td>";
			$html.="<td><a title='Delete All rows from this Up' href='?log&deletefrom=".$row['logID']."'>&and;</a></td>";
			$html.="</tr>";
		}
		$html.="</table></body></html>";
		echo $html;
	}

	function arr2str($a){
		if(is_array($a)){
			$ret=" [ ";
			foreach($a as $k=>$v){
				$ret.=$k."=>".arr2str($v)." , ";
			}
			$ret=substr($ret,0,strlen($ret)-2);
			$ret.=" ]  ";
			return $ret;
		}else{
			return $a;
		}
	}

	function arr2strOpt($a){
		return arr2strOptimized($a,1,"Ar");
	}

	function arr2strOptimized($a,$tabs,$id){
		if(is_array($a)){
			$tab=TabToSpace($tabs);
			$ret=<<<PHTML
				<b style="cursor:pointer;" onclick="getElementById('$id').style.display='none'"> [ </b> <div id="$id" style="display:none;">
PHTML;
			$idCounter=0;
			foreach($a as $k=>$v){
				$idCounter++;
				$ret.=$tab.$k."=>".arr2strOptimized($v,$tabs+1,$id.$idCounter)."<br />";
			}
			//$ret=substr($ret,0,strlen($ret)-2);
			$ret.=<<<PHTML
				</div> $tab <b style="cursor:pointer;" onclick="getElementById('$id').style.display='block'"> ] </b>
PHTML;
			return $ret;
		}else if (is_string($a)){
			return htmlspecialchars($a, ENT_QUOTES);
		}else{
			return "[?]";
		}
	}

	function TabToSpace($tabs){
		$ret="";
		if ($tabs>0) for ($i=0;$i<$tabs;$i++) $ret.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		return $ret;
	}
?>
