<?PHP
require_once '../biz/mainviewer/mainviewer.php';
require_once '../biz/login/login.php';
require_once '../biz/referal/referal.php';

class kopon {

	//Mandatory Variables for a biz
	var $_fullname;

	//Nodes (bizvars)
	var $main;
	var $login;
	var $referal;

	function __construct($fullname) {
		$this->_fullname=$fullname;
		$this->main=new mainviewer($this->_fullname.'_main');

		$this->login=new login($this->_fullname.'_login');

		$this->referal=new referal($this->_fullname.'_referal');

	}

    function message($to, $msg, $info) {
    }

    function broadcast($msg, $info)
    {
    }

	function sleep(){
	}

	function show($echo){
		if($_SESSION['silentmode'])
			return;
		$main=$this->main->_backframe();
		$referal=$this->referal->_backframe();
		$login=$this->login->_backframe();
		$html= <<<PHTMLCODE
			<div id="{$this->_fullname}">
			$referal $login<br />
			$main
			</div>
PHTMLCODE;
		if($echo)
			echo $html;
		else
			return $html;
	}

}

?>