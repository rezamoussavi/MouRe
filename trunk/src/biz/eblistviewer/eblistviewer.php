<?PHP

/*
	Compiled by bizLang compiler version 1.0

	Author: Reza Moussavi
	Date:	12/29/2010
	Version:	1.0

*/
require_once '../biz/category/category.php';
require_once '../biz/eblineviewer/eblineviewer.php';

class eblistviewer {

	//Mandatory Variables for a biz
	var $_bizname;
	var $_fullname;
	var $_parent;
	var $_curFrame;

	//Variables
	var $expanded;
	var $userUID;
	var $UID;
	var $contentLoaded;

	//Nodes (bizvars)
	var $myCat;
	var $eBLists_array_data; 	var $eBLists;
	var $eBoards_array_data; 	var $eBoards;

	function __construct(&$data) {
		if (!isset($data['sleep'])) {
			$data['sleep'] = true;
			$this->_initialize($data);
			$this->_wakeup($data);
			$this->init(); //Customized Initializing
		}else{
			$this->_wakeup($data);
		}
	}

	function _initialize(&$data){
		if(! isset ($data['curFrame']))
			$data['curFrame']=frm;
		if(! isset ($data['expanded']))
			$data['expanded']=false;
		if(! isset ($data['userUID']))
			$data['userUID']=-1;
		if(! isset ($data['UID']))
			$data['UID']=0;
		if(! isset ($data['contentLoaded']))
			$data['contentLoaded']=false;
		if(! isset ($data['myCat'])){
			$data['myCat']['fullname']=$this->_fullname.'_myCat';
			$data['myCat']['bizname']='myCat';
		}
		if(! isset ($data['eBLists_array_data']))
			$data['eBLists_array_data']=array();
		if(! isset ($data['eBoards_array_data']))
			$data['eBoards_array_data']=array();
	}

	function _wakeup(&$data){
		$this->_bizname = &$data['bizname'];
		$this->_fullname = &$data['fullname'];
		$this->_parent = &$data['parent'];
		$this->_curFrame = &$data['curFrame'];

		$this->expanded=&$data['expanded'];
		$this->userUID=&$data['userUID'];
		$this->UID=&$data['UID'];
		$this->contentLoaded=&$data['contentLoaded'];

		$data['myCat']['parent']=$this;
		$this->myCat=new category($data['myCat']);

		$this->eBLists=array();
		$this->eBLists_array_data=&$data['eBLists_array_data'];
		foreach($data['eBLists_array_data'] as $na=>&$da){
			if(! isset($da['bizname'])){
				$da['bizname']=$na;
				$da['fullname']=$this->_fullname."_".$na;
				$da['parent']=$this;
			}
			$this->eBLists[]=new eblistviewer($da);
		}

		$this->eBoards=array();
		$this->eBoards_array_data=&$data['eBoards_array_data'];
		foreach($data['eBoards_array_data'] as $na=>&$da){
			if(! isset($da['bizname'])){
				$da['bizname']=$na;
				$da['fullname']=$this->_fullname."_".$na;
				$da['parent']=$this;
			}
			$this->eBoards[]=new eblineviewer($da);
		}
	}

	function message($to, $message, $info) {
		if ($to != $this->_fullname) {
			$this->myCat->message($to, $message, $info);
			foreach($this->eBLists as $i=>&$_element)
				$_element->message($to, $message, $info);
			foreach($this->eBoards as $i=>&$_element)
				$_element->message($to, $message, $info);
			return;
		}
		switch($message){
			case 'click':
				$this->onClick($info);
				break;
			case 'login':
				$this->onLogin($info);
				break;
			case 'logout':
				$this->onLogout($info);
				break;
			default:
				break;
		}
	}

	function broadcast($message, $info) {
		$this->myCat->broadcast($message, $info);
		foreach($this->eBLists as $i=>&$_element)
			$_element->broadcast($message, $info);
		foreach($this->eBoards as $i=>&$_element)
			$_element->broadcast($message, $info);
		switch($message){
			case 'click':
				$this->onClick($info);
				break;
			case 'login':
				$this->onLogin($info);
				break;
			case 'logout':
				$this->onLogout($info);
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


	function init(){
		$this->myCat->bookUID($this->UID);
	}
	function frm(){
		if($this->expanded)
			$this->loadContent();
		$Lable=$this->myCat->lable;
        $html= '
			<form name="' . $this->_fullname . '" method="post">
	            <input type="hidden" name="_message" value="click" /><input type = "hidden" name="_target" value="' . $this->_fullname . '" />
				<input value ="' . $Lable . '" type = "button" onclick = \'JavaScript:sndmsg("' . $this->_fullname . '")\' class="press" style="margin-top: 10px; margin-right: 0px;" />
			</form>
			' . $this->backContent();
		return $html;
	}
	function backContent(){
		$html='';
		if($this->expanded){
			foreach($this->eBLists as $i=>&$L)
				$html = $html.$L->_backframe();
			foreach($this->eBoards as $j=>&$B)
				$html = $html.$B->_backframe();
		}
		return $html;
	}
	function bookUID($UID){
		$this->contentLoaded=false;
		
		// Empty the array
		$this->eBoards_array_data=array();
		$this->eBoards=array();
		
		// Empty the array
		$this->eBLists_array_data=array();
		$this->eBLists=array();
		$this->UID=$UID;
		$this->init();
	}
	function onClick($info){
		$this->expanded=! $this->expanded;
		$this->_bookframe("frm");
	}
	function onLogin($info){
		$this->userUID=$info['userUID'];
		$this->_bookframe("frm");		
	}
	function onLogout($info){
		$this->userUID=-1;
		$this->_bookframe("frm");
	}
	function loadContent(){
		if($this->contentLoaded)
			return;
		$this->contentLoaded=true;
		$con=$this->myCat->backContent();
		foreach($con as $cat){
			switch($cat['extra']){
				case 'eBList':
					
					// Add new Node to the array
					$_index=count($this->eBLists_array_data);
					$_data=array();
					$_data['parent']=$this;
					$_data['bizname']=$_index;
					$_data['fullname']=$this->_fullname.'_'.$_index;
					$this->eBLists_array_data[]=$_data;
					$this->eBLists[]=new  eblistviewer($this->eBLists_array_data[$_index]);
					end($this->eBLists)->bookUID($cat['bizUID']);
					break;
				case 'eBoard':
					
					// Add new Node to the array
					$_index=count($this->eBoards_array_data);
					$_data=array();
					$_data['parent']=$this;
					$_data['bizname']=$_index;
					$_data['fullname']=$this->_fullname.'_'.$_index;
					$this->eBoards_array_data[]=$_data;
					$this->eBoards[]=new  eblineviewer($this->eBoards_array_data[$_index]);
					end($this->eBoards)->bookUID($cat['bizUID']);
					break;
				default:
					break;
			}//switch
		}//foreach
	}

}

?>