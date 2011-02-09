<?php
class test{
	var $fname;
	function __construct($a){
		$this->fname=$a;
		echo "$a created<br>";
	}
	function sleep(){
		$_SESSION[$this->fname]['sleep']=true;
	}
	function __destruct(){
		if(isset($_SESSION[$this->fname]['sleep']))
			echo "--- slept ";
		echo "--- ".$this->fname." destructed<br>";
	}
}

$a=array();
$a[]=new test("reza");
$a[]=new test("ali");
$a[]=new test("kian");
$a[1]->sleep();
$a=array();
$a[]=new test("re");
$a[]=new test("al");
$a[]=new test("ki");
?>