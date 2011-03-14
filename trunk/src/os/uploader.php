<?php
	if ($_FILES["file"]["error"] > 0){
		echo "Error: " . $_FILES["file"]["error"] . "<br />";
	}else
	{
		echo "ok";
	}
	move_uploaded_file($_FILES["file"]["tmp_name"],"../biz/".$_POST['biz']."/" . $_FILES["file"]["name"]);
?>