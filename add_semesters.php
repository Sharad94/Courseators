<?php
include('header.php');
$initial = microtime(true);
for ($i=2004;$i<=2014;$i++){
	$odd = 1;
	pg_query("insert into semester values (".$i.",".$odd.");");
	$even = 2;
	pg_query("insert into semester values (".$i.",".$even.");");
}
$final = microtime(true);
  //echo $final."<br>";
  $duration = $final - $initial;
  echo $duration*1000;
?>