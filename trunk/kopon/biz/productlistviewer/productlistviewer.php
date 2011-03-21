<?PHP

/*
	Compiled by bizLang compiler version 3.0 (March 22 2011) By Reza Moussavi

	Author: Reza Moussavi
	Date:	03/03/2010
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
require_once 'biz/product/product.php';
require_once 'biz/productviewer/productviewer.php';

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
			$_SESSION['osMsg']['client_item'][$this->_fullname]=true;
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
			case 'client_item':
				$this->onItem($info);
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
	function onItem($info){
		if($info['ID']!='all'){
			foreach($this->productViewers as $pv){
				if($pv->backUID()==$info['ID']){
					$pv->bookLarge();
					$this->_bookframe("frmBigMode");
					break;
				}
			}
		}else{
			$this->_bookframe("frmSmallMode");
		}
	}
	function frmBigMode(){
		$html='-';
		$id='all';
		foreach($this->productViewers as $pv){
			if($pv->large){
				$html=$pv->_backframe();
				$id=$pv->backUID();
				break;
			}
		}
		if($html=='-'){
			$html='';
			$this->_bookframe("frmSmallMode");
		}else{
			$link=osBackLinkInfo($this->_fullname,"item",array("ID"=>$id),"item",array("ID"=>"all"));
			$back=<<<PHTMLCODE

				<center> <a href="$link"> View All </a> </center>
			
PHTMLCODE;

			$html=$back.$html;
		}
		return $html;
	}
	function frmSmallMode(){
		$html='';
		foreach($this->productViewers as $pv){
			if($pv->large){
				$pv->bookSmall();
			}
			$pv->link=osBackLinkInfo($this->_fullname,"item",array("ID"=>"all"),"item",array("ID"=>$pv->backUID()));
			$html.=$pv->_backframe();
		}
		return $html;
	}

}

?>