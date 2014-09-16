<?php
	include('header.php');
	$initial = microtime(true);
	pg_query("insert into slot VALUES('A','0800','0920');");
	pg_query("insert into slot VALUES('B','0930','1050');");
	pg_query("insert into slot VALUES('H','1100','1150');");
	pg_query("insert into slot VALUES('J','1200','1250');");
	pg_query("insert into slot VALUES('M','1700','1820');");
	pg_query("insert into slot VALUES('C','0800','0850');");
	pg_query("insert into slot VALUES('D','0900','0950');");
	pg_query("insert into slot VALUES('E','1000','1050');");
	pg_query("insert into slot VALUES('F','1100','1150');");
	pg_query("insert into slot VALUES('K','1700','1750');");
	pg_query("insert into slot VALUES('L','1800','1850');");
	for ($i=0; $i<26; $i++){
		pg_query("insert into slot VALUES('".chr($i+65)."','----','----');");
	}
	$final = microtime(true);
  //echo $final."<br>";
  $duration = $final - $initial;
  echo $duration*1000;
?>
