<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	5/1/2011
	Ver:		1.0
	-----------------------------------
	Author:	Reza Moussavi
	Date:	4/28/2011
	Ver:		0.1

*/

class adlink {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables

	//Nodes (bizvars)

	function __construct($fullname) {
		$this->_tmpNode=false;
		if($fullname==null){
			$fullname='_tmpNode_'.count($_SESSION['osNodes']);
			$this->_tmpNode=true;
		}
		$this->_fullname=$fullname;
		if(!isset($_SESSION['osNodes'][$fullname])){
			$_SESSION['osNodes'][$fullname]=array();
			//If any message need to be registered will placed here
		}

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='adlink';
	}

	function gotoSleep() {
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			default:
				break;
		}
	}

	function _bookframe($frame){
		$this->_curFrame=$frame;
		//$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
	}


//########################################
//         YOUR FUNCTIONS GOES HERE
//########################################


	function backTotalPaid(){
		return $this->backTotalPaidByUser(osBackUserID());
	}
	function backTotalPaidByUser($UID){
		$paid=0.0;
		query("SELECT SUM(paid) as totalPaid FROM adlink_info WHERE advertisor=$UID");
		if($row=fetch()){
			$paid=$row['totalPaid'];
		}
		return $paid;
	}
	function backTotalReimbursed(){
		return $this->backTotalReimbursedByUser(osBackUserID());
	}
	function backTotalReimbursedByUser($UID){
		$Re=0.0;
		query("SELECT SUM(reimbursed) as totalRe FROM adlink_info WHERE advertisor=$UID");
		if($row=fetch()){
			$Re=$row['totalRe'];
		}
		return $Re;
	}
	function bookLink($info){
		$img=$this->backYImg($info['link']);
		$embed=$this->backYEmbed($info['link']);
		$videoCode=$this->backYCode($info['link']);
		$q="INSERT INTO adlink_info (advertisor,title,running,lastDate,startDate,videoCode,link,img,embed,maxViews,AOPV,paid,APRate,minLifeTime,minCancelTime,country)";
		$q.=" VALUES({$info['advertisor']},'{$info['title']}',{$info['running']},'{$info['lastDate']}','{$info['startDate']}','{$videoCode}','{$info['link']}','{$img}','{$embed}',{$info['maxViews']},{$info['AOPV']},{$info['paid']},{$info['APRate']},{$info['minLifeTime']},{$info['minCancelTime']},'{$info['country']}')";
		query($q);
	}
	function backLinkByID($id){
		query("SELECT * FROM adlink_info WHERE adUID=$id");
		if(!($row=fetch())){
			$row=array();
		}
		return $row;
	}
	function stop($id){
		$d=date("d");
		$m=date("m");
		$y=date("Y");
		/*	fetch the link */
		query(" SELECT * FROM adlink_info WHERE adUID=$id ");
		if($row=fetch()){
			$timeUnlock=date("Y/m/d",mktime(0,0,0,$m,$d - $row['minCancelTime'],$y)) >= $row['startDate'];
			/*
			*	Check IF it is running (running==1)
			*	&& publishe is logedin
			*	&& more than minLifeTime passed
			*/
			if( ($row['advertisor']==osBackUserID()) && ($row['running']==1) && $timeUnlock){
				/*
				*	thus stop it (running=-1)
				*/
				$lastDate=date("Y/m/d",mktime(0,0,0,$m,$d+$row['minLifeTime'],$y));
				$q="UPDATE adlink_info as d SET d.lastDate='".$lastDate."' , d.running=-1 WHERE d.adUID=$id";
				query($q);
				return TRUE;
			}
		}
		return FALSE;
	}
	/************************************************
	*
	*************************************************/
	function backVideoList($mode,$userID){
		$this->removeExpired();
		$vl=array();
		switch($mode){
			case "topublish":
				query("SELECT * FROM adlink_info WHERE running<>0 ORDER BY startDate DESC");
				break;
			case "myad":
				$q="SELECT * FROM adlink_info WHERE advertisor=".$userID." ORDER BY startDate DESC";
				query($q);
				break;
			case "mypub":
				//$q="SELECT * FROM adlink_info WHERE adUID IN(SELECT distinct adlinkUID from publink_info where publisher=".$userID." and totalView>0)";
				$q="SELECT * FROM adlink_info as al,publink_info as pl WHERE al.adUID=pl.adlinkUID AND pl.publisher=".$userID." and pl.totalView>0 ORDER BY startDate DESC";
				query($q);
				break;
		}
		while($row=fetch())	{$vl[]=$row;}
		return $vl;
	}
	function removeExpired(){
		query("UPDATE adlink_info SET running=0 WHERE lastDate>'2000/01/01' AND lastDate<'".date("Y/m/d")."'");
	}
	function backAllUser(){
		$ret=array();
		query("SELECT DISTINCT advertisor FROM adlink_info");
		while($row=fetch()){
			$ret[]=$row['advertisor'];
		}
		return $ret;
	}
	function backAllLink(){
		$ret=array();
		query("SELECT * FROM adlink_info");
		while($row=fetch()){
			$ret[]=$row;
		}
		return $ret;
	}
	/************************************************
	*		YouTube Functions
	*************************************************/
	function backYImg($y){
		$code=$this->backYCode($y);
		return "http://img.youtube.com/vi/".$code."/2.jpg";
	}
	function backYEmbed($y){
		$code=$this->backYCode($y);
		return "<iframe width=640 height=390 src=http://www.youtube.com/embed/".$code." frameborder=0 allowfullscreen></iframe>";
	}
	function backYCode($y){
		$a=strpos($y,"watch?v=")+8;
		if ($a>9){
			return substr($y,$a,strpos($y."&","&")-$a);
		}
		$a=strpos($y,"youtu.be")+9;
		return substr($y,$a,strpos($y."?","?")-$a);
	}

}

?>