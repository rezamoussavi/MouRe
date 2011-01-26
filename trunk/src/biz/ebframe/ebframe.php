<?PHP

/*
	Compiled by bizLang compiler version 1.1

	{Family included}

	Author:		Reza Moussavi
	Version:	1.1
	Date:		1/26/2011
	TestApproval: none
	-------------------
	Author:		Reza Moussavi
	Date:		1/12/2011
	Version:	1.0
	TestApproval: none

*/
require_once '../biz/tabbank/tabbank.php';
require_once '../biz/epostentry/epostentry.php';
require_once '../biz/epostbank/epostbank.php';
require_once '../biz/category/category.php';

class ebframe {

	//Mandatory Variables for a biz
	var $_fullname;
	var $_curFrame;

	//Variables
	var $cureBUID;

	//Nodes (bizvars)
	var $tabbar;
	var $entrybar;
	var $epostbar;

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->_curFrame='frm';
		$this->tabbar=new tabbank($this->_fullname.'_tabbar');
		$this->entrybar=new epostentry($this->_fullname.'_entrybar');
		$this->epostbar=new epostbank($this->_fullname.'_epostbar');
	}

	function __sleep(){
		return array('_fullname', '_curFrame','cureBUID','tabbar','entrybar','epostbar');
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			$this->tabbar->message($to, $message, $info);
			$this->entrybar->message($to, $message, $info);
			$this->epostbar->message($to, $message, $info);
			return;
		}
		switch($message){
			case 'eboard_eBoardSelected':
				$this->onEBoardSelected($info);
				break;
			case 'tab_tabselected':
				$this->onTabSelected($info);
				break;
			case 'epost_newPostAdded':
				$this->onNewPostAdded($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		$this->tabbar->broadcast($message, $info);
		$this->entrybar->broadcast($message, $info);
		$this->epostbar->broadcast($message, $info);
		switch($message){
			case 'eboard_eBoardSelected':
				$this->onEBoardSelected($info);
				break;
			case 'tab_tabselected':
				$this->onTabSelected($info);
				break;
			case 'epost_newPostAdded':
				$this->onNewPostAdded($info);
				break;
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


	function onEBoardSelected($info){
		$this->cureBUID=$info['UID'];
		$cat=new category("temp");
		$content=$cat->backContentOf($this->cureBUID);
		$ar=array();
		foreach($content as $c){
			$lable=$cat->backLable($c['bizUID']);
			$ar[]=array("name"=>$lable,"UID"=>$c['bizUID']);
		}
		$this->tabbar->booklist($ar);
		$this->_bookframe("frm");
	}
	function onTabSelected($info){
		$this->entrybar->bookPostOwner("category",$info['UID']);
		$this->epostbar->showBy("category",$info['UID']);
	}
	function onNewPostAdded($info){
		$this->epostbar->reload();
	}
	function frm(){
		$tabbar=$this->tabbar->_backframe();
		$entrybar=$this->entrybar->_backframe();
		$epostbar=$this->epostbar->_backframe();
		$html=<<<PHTML
			$tabbar<br>
			$entrybar<br>
			$epostbar<br>
PHTML;
		return $html;
	}

}

?>