<?PHP

/*
	Compiled by bizLang compiler version 3.0 (March 22 2011) By Reza Moussavi

	Author: Reza Moussavi
	Date:	02/10/2011
	Version: 0.2
	---------------------
	Author: Reza Moussavi
	Date:	02/07/2011
	Version: 0.1

*/
require_once 'biz/product/product.php';

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
		$t=$this->timeFormat($this->product->backRemainingTime());
		$discount=$this->product->backDiscount();
		if($this->product->backRemainingTime()<1){
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
		$image=$this->link!=''?'<a href="'.$this->link.'"><img src="'.$this->product->backIcon().'"/></a>':'<img src="'.$this->product->backIcon().'"/>';
		$discount=$this->product->backDiscount();
		$day=$this->dayFormat($this->product->backStartTime());
		$html=<<<PHTMLCODE

			<center>$title<br />
			price:$price you get %$discount Discount</center>
			<center>
			$image<br />
			Announcment Day: $day
			</center>
		
PHTMLCODE;

		return $html;
	}
	function timeFormat($t){
		switch(strlen($t)){
			case 1:
				$t="000".$t;
				break;
			case 2:
				$t="00".$t;
				break;
			case 3:
				$t="0".$t;
				break;
		}
		return substr($t,0,2)." : ".substr($t,2);
	}
	function dayFormat($t){
		return substr($t,0,4)."/".substr($t,4,2)."/".substr($t,6,2);
	}

}

?>