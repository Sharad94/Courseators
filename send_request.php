<?php
	include ('functions.php');
	$id = $_POST["data"][0];
	if (strcmp($id,"friend")==0){
		$ret = send_request($_SESSION['username'],$_POST["data"][1]);
	}
	

?>