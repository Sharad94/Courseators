<?php
  header('Content-Type: text/html; charset=utf-8');
        $dbconn = pg_connect("host=localhost dbname=courseators user=psql password=psql") or die('Could not connect: ' . pg_last_error());
        $handle = fopen("courses-code-mapping.csv", "r");
        $initial = microtime(true);
        $count=0;
	echo "baaga";
        if ($handle) {
                $line = fgets($handle);
                while (!feof($handle)) {
                	$temp = $line;
                        $arr1 = split(',', $temp);
                        echo count($arr1);
                        echo "<br>";

                //for($i=0; $i<count($arr1); $i++){
                echo "insert into department values('".trim($arr1[1])."','".trim($arr1[0])."','".intval(trim($arr1[2]))."','".intval(trim($arr1[3]))."    ')" ;
                echo "<br>";
                $result = pg_query("insert into department values('".trim($arr1[1])."','".trim($arr1[0])."','".intval(trim($arr1[2]))."','".intval(trim($arr1[3]))."')") or die('Could not connect: ' . pg_last_error());
                //}

        

        $line = fgets($handle);
        }
  }
  $final = microtime(true);
  //echo $final."<br>";
  $duration = $final - $initial;
  echo $duration*1000;
?>

