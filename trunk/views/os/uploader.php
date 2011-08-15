<?php
	if ($_FILES["file"]["error"] > 0){
		//##################################//File has been uploaded with errors
		echo "Error: " . $_FILES["file"]["error"] . "<br />";
	}else{
		//##################################// Files has been uploaded correctly
		if(!is_dir("biz/".$_POST['biz']."/")){
			//##################################// Creat biz directory if not exists
			mkdir("biz/".$_POST['biz']);
		}
		$source=$_FILES["file"]["tmp_name"];
		$dest="biz/".$_POST['biz']."/" . $_FILES["file"]["name"];
		if(move_uploaded_file($source,$dest)){
			//##################################// uploaded/copied successfully
			echo "ok";
		}else{
			echo "error:\n $source \n $dest \n";
		}
	}
?>
