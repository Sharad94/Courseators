<?php
  header('Content-Type: text/html; charset=utf-8');
  
	$dbconn = pg_connect("host=localhost dbname=courseators user=psql password=psql") or die('Could not connect: ' . pg_last_error());
	$handle = fopen("hukka.txt", "r");
	$count=0;

  $initial = microtime(true);
	if ($handle) {
		$line = fgets($handle);
		while (!feof($handle)) {
        $description = "";
        $prereqarr="";
        $overlaparr="";
        $ltp = "";
    		if (preg_match('/[A-Z][A-Z][A-Z][0-9][0-9][0-9]/', substr($line,0,6))) {
  				$course = $line;
          $courseid = substr($line,0,6);
          $name = substr($line,7);
          
  				
          $line = fgets($handle);
  				$credits = $line;
          $nocredits = strstr($credits,'(',true);
          preg_match_all('/\d+[.\d]*/',$nocredits,$matches);
          $ltp = strstr($credits,'(');
          $credits = $matches[0][0];
          
          if ($credits=='')
            $credits = 0;
  				$line = fgets($handle);
  				
  				if (preg_match('/Pre-requisites/',substr($line,0,14))) {
  					
            $prereq = substr($line,15);
            
            $prereqarr = multiexplode(array("&","and","/",","),$prereq);
            //$prereqarr = preg_split('/([&and\\/,])/', $prereq);
            //var_dump($prereqarr);
  					//echo $prereq."<br>";
  					$line = fgets($handle);
  					if (preg_match('/Overlaps with/',substr($line,0,13))){
              $overlap = substr($line, 13);
              $overlap = str_replace(':','',$overlap);
              $overlaparr = multiexplode(array("&","and","/",","),$overlap);
              //var_dump($overlaparr);
  						$line = fgets($handle);
  					}
  				}
          if (preg_match('/Overlaps with/',substr($line,0,14))){
              $overlap = substr($line, 15);
              $overlaparr = multiexplode(array("&","and","/",","), $overlap);
              //var_dump($overlaparr);
              //echo $overlap."<br>";
              $line = fgets($handle);
            }
  				while (!preg_match('/[A-Z][A-Z][A-Z][0-9][0-9][0-9]/', substr($line,0,6))) {
  							$description = $description.$line;

  							if(feof($handle)){
  								break;
  							}
  							$line = fgets($handle);
  				}
          $courseid = str_replace(' ','',$courseid);
          $ltp = str_replace(' ','',$ltp);



          //echo "insert into course values('".$courseid."','".$name."','".$description."','".$ltp."',".$credits.");";
  				//echo "insert into course values('".$courseid."','".$name."','".$description."','".$ltp."',".$credits.");"."<br>";
          //$result = pg_query($dbconn,"insert into course values('".$courseid."','".$name."','".pg_escape_string($description)."','".$ltp."',".$credits.");");
          
          
          
          

          for ($i=0 ; $i < sizeof($prereqarr) ; $i++){
            if (!empty($prereqarr[$i])){
              //echo "insert into prereq values('".$courseid."','".$prereqarr[$i]."')";
              $temp = str_replace(' ','',$prereqarr[$i]);
              $temp = trim($temp);
              //$result = pg_query("insert into prereq values('".$courseid."','".$temp."')");
            }
          }
          for ($i=0 ; $i < sizeof($overlaparr) ; $i++){
            if (!empty($overlaparr[$i])){
              //echo "insert into overlap values('".$courseid."','".$overlaparr[$i]."')";
              $temp = str_replace(' ','',$overlaparr[$i]);
              $temp = trim($temp);
              //$result = pg_query("insert into overlap values('".$courseid."','".$temp."')");
            }
          }
    		}
 	   }
	} 
	fclose($handle);
  $final = microtime(true);
  function multiexplode ($delimeters, $string){
    $ready = str_replace($delimeters, $delimeters[0], $string);
    $launch = explode($delimeters[0], $ready);
    return $launch;
  }
?>
