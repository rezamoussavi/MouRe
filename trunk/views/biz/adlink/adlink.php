<?PHP

/*
	Compiled by bizLang compiler version 3.2 [DB added] (March 26 2011) By Reza Moussavi

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


	function bookLink($info){
		$img=$this->backYImg($info['link']);
		$embed=$this->backYEmbed($info['link']);
		$q="INSERT INTO adlink_info (advertisor,running,lastDate,startDate,link,img,embed,maxViews,AOPV,paid,APRate,minLifeTime,minCancelTime)";
		$q.=" VALUES({$info['advertisor']},{$info['running']},'{$info['lastDate']}','{$info['startDate']}','{$info['link']}','{$img}','{$embed}',{$info['maxViews']},{$info['AOPV']},{$info['paid']},{$info['APRate']},{$info['minLifeTime']},{$info['minCancelTime']})";
		query($q);
	}
	/************************************************
	*
	*************************************************/
	function backVideoList($mode){
		$vl=array();
		switch($mode){
			case "topublish":
				query("SELECT * FROM adlink_info WHERE running=1");
				break;
		}
		while($row=fetch())	{$vl[]=$row;}
		return $vl;
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
		return substr($y,$a,strpos($y."&","&")-$a);
	}

}

?>