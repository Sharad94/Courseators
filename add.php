<?php

	include ('functions.php');
	$id = $_POST["data"][0];
	if (strcmp($id,"tentative")==0){
		add_tentative($_SESSION['username'],$_POST["data"][1]);
	}
	else if (strcmp($id,"bookmark")==0){
		add_bookmark($_SESSION['username'],$_POST["data"][1]);
	}
	
?>