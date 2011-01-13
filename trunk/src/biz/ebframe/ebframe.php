<?PHP

/*
	Compiled by bizLang compiler version 1.01

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
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $cureBUID;

	//Nodes (bizvars)
	var $tabbar;
	var $entrybar;
	var $epostbar;

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
		if(! isset ($data['curFrame']))
			$data['curFrame']='frm';
		if(! isset ($data['tabbar'])){
			$data['tabbar']['fullname']=$data['fullname'].'_tabbar';
			$data['tabbar']['bizname']='tabbar';
		}
		if(! isset ($data['entrybar'])){
			$data['entrybar']['fullname']=$data['fullname'].'_entrybar';
			$data['entrybar']['bizname']='entrybar';
		}
		if(! isset ($data['epostbar'])){
			$data['epostbar']['fullname']=$data['fullname'].'_epostbar';
			$data['epostbar']['bizname']='epostbar';
		}
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->cureBUID=&$data['cureBUID'];

		$data['tabbar']['parent']=$this;
		$this->tabbar=new tabbank($data['tabbar']);
		$data['entrybar']['parent']=$this;
		$this->entrybar=new epostentry($data['entrybar']);
		$data['epostbar']['parent']=$this;
		$this->epostbar=new epostbank($data['epostbar']);
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			$this->tabbar->message($to, $message, $info);
			$this->entrybar->message($to, $message, $info);
			$this->epostbar->message($to, $message, $info);
			return;
		}
		switch($message){
			case 'eBoardSelected':
				$this->onEBoardSelected($info);
				break;
			case 'tabSelected':
				$this->onTabSelected($info);
				break;
			case 'newPostAdded':
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
			case 'eBoardSelected':
				$this->onEBoardSelected($info);
				break;
			case 'tabSelected':
				$this->onTabSelected($info);
				break;
			case 'newPostAdded':
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
		$_d=array();
		$cat=new category($_d);
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
			[{$this->cureBUID}]
			$tabbar<br>
			$entrybar<br>
			$epostbar<br>
PHTML;
		return $html;
	}

}

?>