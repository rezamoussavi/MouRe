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
	Version: 1.1
	------------------
	Author: Max Mirkia
	Date:	2/14/2010
	Version: 1.0
	------------------
	Author: Max Mirkia
	Date:	2/7/2010
	Version: 0.1

*/
require_once '../biz/product/product.php';
require_once '../biz/productviewer/productviewer.php';

class productlistviewer {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;
	var $_frmChanged;

	//Variables

	//Nodes (bizvars)
	var $productViewers; // array of biz

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
			$_SESSION['osMsg']['client_mode'][$this->_fullname]=true;
		}

		$_SESSION['osNodes'][$fullname]['sleep']=false;
		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frmSmallMode';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		//handle arrays
		$this->productViewers=array();
		if(!isset($_SESSION['osNodes'][$fullname]['productViewers']))
			$_SESSION['osNodes'][$fullname]['productViewers']=array();
		foreach($_SESSION['osNodes'][$fullname]['productViewers'] as $arrfn)
			$this->productViewers[]=new productviewer($arrfn);

		if(!isset($_SESSION['osNodes'][$fullname]['biz']))
			$this->init(); //Customized Initializing
		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='productlistviewer';
	}

	function gotoSleep() {
		$_SESSION['osNodes'][$this->_fullname]['productViewers']=array();
		$_SESSION['osNodes'][$this->_fullname]['sleep']=true;
		foreach($this->productViewers as $node){
			$_SESSION['osNodes'][$this->_fullname]['productViewers'][]=$node->_fullname;
		}
		if($this->_tmpNode)
			unset($_SESSION['osNodes'][$this->_fullname]);
		else
			unset($_SESSION['osNodes'][$this->_fullname]['node']);
	}


	function message($message, $info) {
		switch($message){
			case 'client_mode':
				$this->onMode($info);
				break;
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
			case 'frmSmallMode':
				$_style='';
				break;
			case 'frmBigMode':
				$_style='';
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


	function init(){
		$product = new product("");
		$products = $product->backAllUID();
		$i=0;
		foreach($products as $p){
			$this->productViewers[] = new productViewer($this->_fullname.$i++);
			end($this->productViewers)->bookSmall();
			end($this->productViewers)->bookProductUID($p);
		}
	}
	function onMode($info){
		
	}
	
	function frmBigMode(){
		$html=<<<PHTMLCODE

			[[ BIG MODE ]]
		
PHTMLCODE;

		return $html;
	}
	
	function frmSmallMode(){
		$html='';
		foreach($this->productViewers as $pv){
			$html.=$pv->_backframe();
		}
		return <<<PHTMLCODE

			$html
		
PHTMLCODE;

	}
	

}

?>