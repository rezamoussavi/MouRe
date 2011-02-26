<?PHP

/*
	Compiled by bizLang compiler version 1.5 (Feb 21 2011) By Reza Moussavi
	1.1: {Family included}
	1.2: {flatten sleep session}
	1.3: {direct message sending}
	1.3.5: {sleep and decunstructed merged + _tmpNode_ added to fix a bug}
	1.4: {multi parameter in link message}
	1.5: {multi secName support: frm/frame, msg/messages,fun/function/phpfunction}

	Author: Max Mirkia
	Date:	2/7/2010
	Version: 0.1

*/

class product {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

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
			"biz/product" . "b" . $this->UID . ".jpg");
	}
	
	function backImage(){
		return "biz/product" . "b" . $this->UID . ".jpg";	
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
	
	function bookProductUID($UID){
		//query()
		if($row = fetch()){
			$this->UID = $row[''];
			$this->title = $row[''];
			$this->discount = $row[''];
			$this->description = $row[''];
			$this->startTime = $row[''];
			$this->endTime = $row[''];
			$this->price = $row[''];
		}
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
		return "biz/product" . "i" . $this->UID . ".jpg";
	}
	
	function bookIcon($tempPath){
		move_uploaded_file($tempPath,
			"biz/product" . "i" . $this->UID . ".jpg");		
	}
	
	function backAllUID(){
		return array();
	}

}

?>