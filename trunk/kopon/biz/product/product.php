<?PHP

/*
	Compiled by bizLang compiler version 2.0 (March 4 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}
	1.4: {multi parameter in link message}
	1.5: {multi secName support: frm/frame, msg/messages,fun/function/phpfunction}
	2.0: {upload bothe biz and php directly to server (ready to use)}

	Author: Reza Moussavi
	Date:	3/3/2010
	Version: 0.3
	------------------
	Author: Max Mirkia
	Date:	2/7/2010
	Version: 0.1

*/

class product {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables
	var $UID;
	var $title;
	var $description;
	var $price;
	var $image;
	var $discount;
	var $startTime;
	var $endTime;
	var $icon;

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

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		if(!isset($_SESSION['osNodes'][$fullname]['UID']))
			$_SESSION['osNodes'][$fullname]['UID']='';
		$this->UID=&$_SESSION['osNodes'][$fullname]['UID'];

		if(!isset($_SESSION['osNodes'][$fullname]['title']))
			$_SESSION['osNodes'][$fullname]['title']='';
		$this->title=&$_SESSION['osNodes'][$fullname]['title'];

		if(!isset($_SESSION['osNodes'][$fullname]['description']))
			$_SESSION['osNodes'][$fullname]['description']='';
		$this->description=&$_SESSION['osNodes'][$fullname]['description'];

		if(!isset($_SESSION['osNodes'][$fullname]['price']))
			$_SESSION['osNodes'][$fullname]['price']='';
		$this->price=&$_SESSION['osNodes'][$fullname]['price'];

		if(!isset($_SESSION['osNodes'][$fullname]['image']))
			$_SESSION['osNodes'][$fullname]['image']='';
		$this->image=&$_SESSION['osNodes'][$fullname]['image'];

		if(!isset($_SESSION['osNodes'][$fullname]['discount']))
			$_SESSION['osNodes'][$fullname]['discount']='';
		$this->discount=&$_SESSION['osNodes'][$fullname]['discount'];

		if(!isset($_SESSION['osNodes'][$fullname]['startTime']))
			$_SESSION['osNodes'][$fullname]['startTime']='';
		$this->startTime=&$_SESSION['osNodes'][$fullname]['startTime'];

		if(!isset($_SESSION['osNodes'][$fullname]['endTime']))
			$_SESSION['osNodes'][$fullname]['endTime']='';
		$this->endTime=&$_SESSION['osNodes'][$fullname]['endTime'];

		if(!isset($_SESSION['osNodes'][$fullname]['icon']))
			$_SESSION['osNodes'][$fullname]['icon']='';
		$this->icon=&$_SESSION['osNodes'][$fullname]['icon'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='product';
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
		if($frame!=$this->_curFrame){
			$this->_curFrame=$frame;
		}
		//$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$_style='';
		switch($this->_curFrame){
		}
		$html='<div '.$_style.' id="' . $this->_fullname . '">'.call_user_func(array($this, $this->_curFrame)).'</div>';
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


	function bookProductUID($UID){
		$date=date("Ymd");
		$to=$date."2400";
		$from=$date."0000";
		if($UID>0){
			$q="select * from product_product where UID=$UID";
		}
		else{
			$q="select * from product_product where endtime<'$to' and starttime>'$from'";
		}
		xquery($this,$q);
		if($row = fetch()){
			$this->UID = $row['UID'];
			$this->title = $row['Title'];
			$this->discount = $row['Discount'];
			$this->description = $row['Description'];
			$this->startTime = $row['StartTime'];
			$this->endTime = $row['EndTime'];
			$this->price = $row['Price'];
		}
	}
	function backTitle(){
		return $this->title;
	}
	function bookTitle($title){
		$this->title = $title;
	}
	
	function backDescription(){
		return $this->description;
	}
	
	function bookDescription($description){
		$this->description = $description;
	}
	
	function bookPrice($price){
		$this->price = $price;
	}
	
	function backPrice(){
		return $this->price;
	}
	
	function bookImage($tempPath){
		move_uploaded_file($tempPath,
			"biz/product/images/" . "b" . $this->UID . ".jpg");
	}
	
	function backImage(){
		return "http://".$_SERVER['HTTP_HOST']."/biz/product/images/" . "b" . $this->UID . ".jpg";	
	}
	
	function backRemainingTime(){
		return $this->endTime - $this->startTime;
	}
	
	function bookDiscount($discount){
		$this->discount = $discount;
	}
	
	function backDiscount(){
		return $this->discount;
	}
	
	function backProductUID(){
		$this->UID;
	}
	
	function bookStartTime($startTime){
		$this->startTime = $startTime;
	}
	
	function backStartTime(){
		return $this->startTime;
	}
	
	function backEndTime(){
		return $this->endTime;
	}
	
	function bookEndTime($endTime){
		$this->endTime = $endTime;
	}
	
	function backIcon(){
		return "http://".$_SERVER['HTTP_HOST']."/biz/product/images/" . "i" . $this->UID . ".jpg";
	}
	
	function bookIcon($tempPath){
		move_uploaded_file($tempPath,
			"biz/product/images" . "i" . $this->UID . ".jpg");		
	}
	
	function backAllUID(){
		$date=date("Ymd");
		$to=$date."2400";
		xquery($this,"select UID from product_product where endtime<'$to'");
		$ret=array();
		while($d=fetch()){
			$ret[]=$d['UID'];
		}
		return $ret;
	}

}

?>