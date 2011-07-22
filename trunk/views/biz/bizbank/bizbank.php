<?PHP

/*
	Compiled by bizLang compiler version 4.0 [JQuery] (May 5 2011) By Reza Moussavi

	Author:	Reza Moussavi
	Date:	4/21/2011
	Ver:		0.1

*/
require_once 'biz/viewslogin/viewslogin.php';
require_once 'biz/tabbank/tabbank.php';
require_once 'biz/mainpageviewer/mainpageviewer.php';

class bizbank {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;
	var $_tmpNode;

	//Variables

	//Nodes (bizvars)
	var $login;
	var $tabbar;
	var $pages;

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

		//default frame if exists
		if(!isset($_SESSION['osNodes'][$fullname]['_curFrame']))
			$_SESSION['osNodes'][$fullname]['_curFrame']='frm';
		$this->_curFrame=&$_SESSION['osNodes'][$fullname]['_curFrame'];

		$this->login=new viewslogin($this->_fullname.'_login');

		$this->tabbar=new tabbank($this->_fullname.'_tabbar');

		$this->pages=new mainpageviewer($this->_fullname.'_pages');

		if(!isset($_SESSION['osNodes'][$fullname]['biz']))
			$this->init(); //Customized Initializing
		$_SESSION['osNodes'][$fullname]['node']=$this;
		$_SESSION['osNodes'][$fullname]['biz']='bizbank';
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
		$_style='';
		switch($this->_curFrame){
			case 'frm':
				$_style=' ';
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
		$this->tabbar->bookContent(array("Pricing","Contact us"));
	}
	function frm(){
		$login=$this->login->_backframe();
		$tab=$this->tabbar->_backframe();
		$pages=$this->pages->_backframe();
		$home=osBackPageLink("PubVideo");
		return <<<PHTMLCODE

			<div id="header_bg">
				<div id="logo_div">
					<a href="$home">
						<img id="logo_img" alt="" src="./img/logo.png" />
					</a>
				</div>
				<div id="menu_div">
					$tab
				    $login
				</div>
			</div>
			$pages
		
PHTMLCODE;

	}

}

?>