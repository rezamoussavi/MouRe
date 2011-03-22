<?php
	if ($_FILES["file"]["error"] > 0){
		//##################################//File has been uploaded with errors
		echo "Error: " . $_FILES["file"]["error"] . "<br />";
	}else{
		//##################################// Files has been uploaded correctly
		if(!is_dir("biz/".$_POST['biz']."/")){
			//##################################// Creat biz directory if not exists
			mkdi("biz/".$_POST['biz']);
		}
		if(move_uploaded_file($_FILES["file"]["tmp_name"],"biz/".$_POST['biz']."/" . $_FILES["file"]["name"])){
			//##################################// uploaded/copied successfully
			echo "ok";
		}else{
			echo "error";
		}
	}
?>