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
	Date:	02/10/2011
	Version: 0.2
	---------------------
	Author: Reza Moussavi
	Date:	02/07/2011
	Version: 0.1

*/
require_once '../biz/product/product.php';

class productviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables
	var $large;
	var $link;

	//Nodes (bizvars)
	var $product;

	function __construct($fullname) {
		$this->_frmChanged=false;
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
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmLarge';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->product=new product($this->_fullname.'_product');

		if(!isset($_SESSION['osNodes'][$fullname]['large']))
			$_SESSION['osNodes'][$fullname]['large']=true;
		$this->large=&$_SESSION['osNodes'][$fullname]['large'];

		if(!isset($_SESSION['osNodes'][$fullname]['link']))
			$_SESSION['osNodes'][$fullname]['link']='';
		$this->link=&$_SESSION['osNodes'][$fullname]['link'];

		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='productviewer';
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
		$this->_frmChanged=true;
		$this->_curFrame=$frame;
		//$this->show(true);
	}
	function _backframe(){
		return $this->show(false);
	}

	function show($echo){
		$_style='';
		switch($this->_curFrame){
			case 'frmLarge':
				$_style=' style="width:595; float:left; border: 1px solid #cccccc;" ';
				break;
			case 'frmSmall':
				$_style=' style="width:295; float:left; height:250; border: 1px dotted #f0f0f0;" ';
				break;
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


	function backUID(){
		return $this->product->backUID();
	}
	function bookSmall(){
		$this->large=false;
		$this->_bookframe("frmSmall");
	}
	function bookLarge(){
		$this->large=true;
		$this->_bookframe("frmLarge");
	}
	function bookProductUID($UID){
		$this->product->bookProductUID($UID);
		if($this->large){
			$this->_bookframe("frmLarge");
		}else{
			$this->_bookframe("frmSmall");
		}
	}
	function frmLarge(){
		$title=$this->product->backTitle();
		$description=$this->product->backDescription();
		$price=$this->product->backPrice();
		$image=$this->product->backImage();
		$t=$this->product->backRemainingTime();
		$discount=$this->product->backDiscount();
		if($t<1){
			$time="";
			$buy="BUY (time out)";
		}else{
			$time=$t;
			$buy='<a href="">BUY</a>';
		}
		$html=<<<PHTMLCODE

			<b><font color=blue>Today's deal:</font> </b>$title
			<center>price:$price you get %$discount Discount <br />
			<b>$buy</b><br>
			<font color=red><h1>$time</h1></font></center><br>
			<center><img src="$image"/></center>$description
		
PHTMLCODE;

		return $html;
	}
	function frmSmall(){
		$title=$this->product->backTitle();
		$price=$this->product->backPrice();
		$image=$this->link!=''?'<a href="'.$this->link.'"><img src="'.$this->product->backIcon().'"/></a>"':'<img src="'.$this->product->backIcon().'"/>';
		$discount=$this->product->backDiscount();
		$html=<<<PHTMLCODE

			<center>$title<br />
			price:$price you get %$discount Discount</center>
			<center>$image</center>
		
PHTMLCODE;

		return $html;
	}

}

?>