<?php
	session_start();
	if (isset($_SESSION['username'])){
		session_destroy();
		//$depid = substr($_SESSION['username'], 4, 3);
		//$view = "dep_".$depid;
		//$query = "drop view ".$view;
				//echo $query;
		//$result = pg_query($query);
		header( "refresh:1;url=index.php" );
		echo "You will be redirected to login page in 1 seconds";
	}
	else{
		session_destroy();
		header("Location: index.php");
	}
?>