<?php
    header('Content-Type: text/html; charset=utf-8');
    include('header.php');
    $t = "slot_2013_sem2.csv";
    $initial = microtime(true);
    $handle = fopen($t, "r");
    $count=0;
	$year = substr($t,5,4);
	$semnumber = substr($t,13,1);
    
        if ($handle) {
            $line = fgets($handle);
            $line = fgets($handle);
            while (!feof($handle)) {
                $temp = $line;
                $arr1 = split(',', $temp);
                $temp = strtolower(trim($arr1[8]));
                $profid = pg_query("select profid from prof where lower(name) = '".$temp."';") or die('Could not connect: ' . pg_last_error());
                while($row = pg_fetch_assoc($profid)){
                    
                    if (strlen($row['profid'])!=0){
                        echo "insert into coursesem values('".trim($arr1[2])."','".$row['profid']."','".trim($arr1[1])."',".intval(trim($arr1[9])).",".$year.",".$semnumber.")";
                        pg_query("insert into coursesem values('".trim($arr1[2])."','".$row['profid']."','".trim($arr1[1])."',".intval(trim($arr1[9])).",".$year.",".$semnumber.")");
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

