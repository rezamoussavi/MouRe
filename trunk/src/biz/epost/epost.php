<?PHP

/*
	Compiled by bizLang compiler version 1.3.5 (Feb 3 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/10/2011
	TestApproval: none

*/
require_once '../biz/category/category.php';

class epost {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables
	var $UID;
	var $authorUID;
	var $author;
	var $title;
	var $content;
	var $edition;
	var $lastedition;
	var $noOfComments;
	var $timeStamp;

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

		if(!isset($_SESSION['osNodes'][$fullname]['UID']))
			$_SESSION['osNodes'][$fullname]['UID']=-1;
		$this->UID=&$_SESSION['osNodes'][$fullname]['UID'];

		if(!isset($_SESSION['osNodes'][$fullname]['authorUID']))
			$_SESSION['osNodes'][$fullname]['authorUID']=-1;
		$this->authorUID=&$_SESSION['osNodes'][$fullname]['authorUID'];

		if(!isset($_SESSION['osNodes'][$fullname]['author']))
			$_SESSION['osNodes'][$fullname]['author']='';
		$this->author=&$_SESSION['osNodes'][$fullname]['author'];

		if(!isset($_SESSION['osNodes'][$fullname]['title']))
			$_SESSION['osNodes'][$fullname]['title']='';
		$this->title=&$_SESSION['osNodes'][$fullname]['title'];

		if(!isset($_SESSION['osNodes'][$fullname]['content']))
			$_SESSION['osNodes'][$fullname]['content']='';
		$this->content=&$_SESSION['osNodes'][$fullname]['content'];

		if(!isset($_SESSION['osNodes'][$fullname]['edition']))
			$_SESSION['osNodes'][$fullname]['edition']=0;
		$this->edition=&$_SESSION['osNodes'][$fullname]['edition'];

		if(!isset($_SESSION['osNodes'][$fullname]['lastedition']))
			$_SESSION['osNodes'][$fullname]['lastedition']=0;
		$this->lastedition=&$_SESSION['osNodes'][$fullname]['lastedition'];

		if(!isset($_SESSION['osNodes'][$fullname]['noOfComments']))
			$_SESSION['osNodes'][$fullname]['noOfComments']=0;
		$this->noOfComments=&$_SESSION['osNodes'][$fullname]['noOfComments'];

		if(!isset($_SESSION['osNodes'][$fullname]['timeStamp']))
			$_SESSION['osNodes'][$fullname]['timeStamp']="20101231235959";
		$this->timeStamp=&$_SESSION['osNodes'][$fullname]['timeStamp'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='epost';
	}

	function sleep(){
		$_SESSION['osNodes'][$this->_fullname]['slept']=true;
	}

	function __destruct() {
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
		$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$html='<div id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
		if($_SESSION['silentmode'])
			return;
		if($echo)
			echo $html;
		else
			return $html;
	}


//########################################
//         YOUR FUNCTIONS GOES HERE
//########################################


	function bookUID($UID){
		if($this->UID==$UID){
			return;
		}
		$q= 'select ';
		$q.='p.title as title, ';
		$q.='p.author_UID as authorUID, ';
		$q.='p.author_name as author, ';
		$q.='p.latestEditionNo as lastedition, ';
		$q.='p.numberOfComments as noOfComments, ';
		$q.='c.content as content, ';
		$q.='c.editionNo as edition, ';
		$q.='c.timestamp as timeStamp ';
		$q.='from epost_epost as p join epost_content as c ';
		$q.='ON p.epostUID=c.epostUID ';
		$q.='where p.latestEditionNo=c.editionNo and p.epostUID='.$UID;
		query($q);
		$row=fetch();
		if(! $row)
			return false;
		$this->UID=$UID;
		$this->authorUID=$row['authorUID'];
		$this->author=$row['author'];
		$this->title=$row['title'];
		$this->content=$row['content'];
		$this->edition=$row['edition'];
		$this->lastedition=$row['lastedition'];
		$this->noOfComments=$row['noOfComments'];
		$this->timeStamp=$row['timeStamp'];
		return true;
	}
	function backCommentsUID(){
		if($this->UID==-1){
			return;
		}
		//array of UIDs
		query("select commentUID as UID from epost_comments where epostUID=" . $this->UID);
		$ret=array();
		while($a=fetch()){
			$ret[]=$a['UID'];
		}
		return $ret;
	}
	function addpost($data){//return boolean as successfullness
		// NOTE: $data=> (title,content,ownerbiz,ownerbizUID)
		// backUser (from os)
		$user=osBackUser();
		if(!$user)
			return false;
		// insert post in epost
		$q ='insert into epost_epost';
		$q.='(title,author_UID,author_name,owner_biz,owner_bizUID,latestEditionNo,numberOfComments) ';
		$q.='values("'. $data['title'] .'","'. $user['UID'] .'","'. $user['name'] .'","'. $data['ownerbiz'] .'",'. $data['ownerbizUID'] .',1,0)';
		query($q);
		$UID=mysql_insert_id();
		// insert a row in category (if needed)
		if($data['ownerbiz']=="category"){
			$cat=new category("temp");
			$cat->addcontent(array("catUID"=>$data['ownerbizUID'],"bizname"=>"epost","bizUID"=>$UID,"extra"=>""));
		}
		// insert a row in comments (if needed)
		if($data['ownerbiz']=="epost"){
			$q ='insert into epost_comments';
			$q.='(epostUID,commentUID) ';
			$q.='values("'. $data['ownerbizUID'] .'","'. $UID .'")';
			query($q);
		}
		// insert content
		$q ='insert into epost_content';
		$q.='(epostUID,content,editionNo,timestamp) ';
		$q.='values("' .$UID. '","' .$data['content']. '",1,"'. date(YmdHis) .'")';
		$q.='';
		query($q);
		// update numberOfComments by sending this UID
		$this->increaseNumberOfComments($UID);
		return true;
	}
	function increaseNumberOfComments($UID){//increase number of comments of parent of this UID
		$w=$this->fetchOwner($UID);
		if($w){
			if($w['owner']=="epost"){
				//update $w['ownerUID'].numberOfComments
				query('update epost_epost set numberOfComments=numberOfComments+1 where epostUID='.$w['ownerUID']);
				//its parent as well
				$this->increaseNumberOfComments($w['ownerUID']);
			}
		}
	}
	function fetchOwner($UID){
		query('select owner_biz as owner, owner_bizUID as ownerUID from epost_epost where epostUID='.$UID);
		return fetch();
	}
	function next(){
		//goto next edition if there is any
		if($this->lastedition==$this->edition)
			return false;
		qurey('select * from epost_content where epostUID='.$this->UID.' and editionNo='.$this->edition+1);
		$e=fetch();
		if(!$e)
			return false;
		$this->content=$e['content'];
		$this->timeStamp=$e['timestamp'];
		$this->edition++;
		return true;
	}
	function prev(){
		//goto previous edition if there is any
		if($this->edition>1)
			return false;
		qurey('select * from epost_content where epostUID='.$this->UID.' and editionNo='.$this->edition-1);
		$e=fetch();
		if(!$e)
			return false;
		$this->content=$e['content'];
		$this->timeStamp=$e['timestamp'];
		$this->edition--;
		return true;
	}

}

?>