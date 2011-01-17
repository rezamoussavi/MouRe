<?PHP

/*
	Compiled by bizLang compiler version 1.01

	Author:		Reza Moussavi
	Version:	0.1
	Date:		1/10/2011
	TestApproval: none

*/
require_once '../biz/category/category.php';

class epost {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

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

	function __construct(&$data) {
		if (!isset($data['sleep'])) {
			$data['sleep'] = true;
			$this->_initialize($data);
			$this->_wakeup($data);
		}else{
			$this->_wakeup($data);
		}
	}

	function _initialize(&$data){
		if(! isset ($data['UID']))
			$data['UID']=-1;
		if(! isset ($data['authorUID']))
			$data['authorUID']=-1;
		if(! isset ($data['edition']))
			$data['edition']=0;
		if(! isset ($data['lastedition']))
			$data['lastedition']=0;
		if(! isset ($data['noOfComments']))
			$data['noOfComments']=0;
		if(! isset ($data['timeStamp']))
			$data['timeStamp']="20101231235959";
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->UID=&$data['UID'];
		$this->authorUID=&$data['authorUID'];
		$this->author=&$data['author'];
		$this->title=&$data['title'];
		$this->content=&$data['content'];
		$this->edition=&$data['edition'];
		$this->lastedition=&$data['lastedition'];
		$this->noOfComments=&$data['noOfComments'];
		$this->timeStamp=&$data['timeStamp'];

	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			return;
		}
		switch($message){
			default:
				break;
		}
	}

	function broadcast($message, $info) {
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
			$ea=array();
			$cat=new category($ea);
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