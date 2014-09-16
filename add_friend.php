<?php
	include ('functions.php');
	$id = $_POST["data"][0];
	$friend = $_POST["data"][1];
	deletefromtentative($friend,$_SESSION['username']);
	if (strcmp($id,"accept")==0){
		addfriend($friend,$_SESSION['username']);
	}
?>