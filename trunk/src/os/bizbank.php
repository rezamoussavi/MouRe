<?PHP
	$_SESSION['bizbank_name']="eBoardPortal";
	require_once "../bizbank/eBoardPortal/eBoardPortal.php";
	$bizbank=new eBoardPortal(&$_SESSION['bizbank']);
?>