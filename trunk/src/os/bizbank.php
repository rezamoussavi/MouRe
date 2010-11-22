<?PHP
	$_SESSION['bizbank_name']="eBoardPortal";
	require_once "../bizbank/eBoardPortal.php";
	$bizbank=new eBoardPortal(&$_SESSION['bizbank']);
?>