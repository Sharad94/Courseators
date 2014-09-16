<?php
  header('Content-Type: text/html; charset=utf-8');
	$dbconn = pg_connect("host=localhost dbname=courseators user=psql password=psql") or die('Could not connect: ' . pg_last_error());
	$handle = fopen("core_elective_data.txt", "r");
	$count=0;
	$initial = microtime(true);
	if ($handle) {
		$line = fgets($handle);
		while (!feof($handle)) {
		$temp = $line;
		if(substr($temp, 0, 1) == "@"){

			$dep = substr($temp, 1, 3);
			echo $dep;
			$line = fgets($handle);
			$line = fgets($handle);
			$temp2 = $line;
			$arr1 = split(',', $temp2);
			echo count($arr1);
			echo "<br>";
			$line = fgets($handle);
			$line = fgets($handle);
			$temp3 = $line;
			$arr2 = split(',', $temp3);

		for($i=0; $i<count($arr1); $i++){
			echo "insert into coreof values('".trim($arr1[$i])."','".$dep."')" ;
			echo "<br>";


			$result = pg_query("insert into coreof values('".trim($arr1[$i])."','".$dep."')") ;


		}
	

                for($i=0; $i<count($arr2); $i++){
                        echo "insert into elecof values('".trim($arr2[$i])."','".$dep."')" ;
                        echo "<br>";
          $result = pg_query("insert into elecof values('".trim($arr2[$i])."','".$dep."')");

                }
        }

	
        $line = fgets($handle);
	} 
  }
  $final = microtime(true);
  //echo $final."<br>";
  $duration = $final - $initial;
  echo $duration*1000;
?>
