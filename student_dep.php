<?php
	header('Content-Type: text/html; charset=utf-8');
	include('header.php');
	pg_query("delete from student where studentid like '%CH5%'");
	pg_query("delete from student where studentid like '%CH6%'");
	pg_query("delete from student where studentid like '2013%'");
	pg_query("delete from student where studentid='2000021'");
	pg_query("delete from student where studentid='2001308'");
	pg_query("delete from student where studentid='2002187'");
	pg_query("delete from student where studentid='2002422'");
	pg_query("delete from student where studentid='2002439'");
	$initial = microtime(true);
	$arr = pg_query('select studentid from student order by studentid');
	while ($row = pg_fetch_assoc($arr)){
		$studentid = $row['studentid'];
		$dep = substr($studentid,4,3);
		echo $studentid."<br>";
		pg_query("insert into studentdep values('".$studentid."','".$dep."');");
	}
	$final = microtime(true);
  //echo $final."<br>";
  $duration = $final - $initial;
  echo $duration*1000;
	
?>
