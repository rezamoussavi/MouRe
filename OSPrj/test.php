<?php
	class test{
		var $name;
		function __construct($n){
			$this->name=$n;
		}
		function show(){
			echo "show: ".$name;
		}
	}
	
	$name="test";
	$obj=new $$name("Reza");
	$obj->show();
	
?>