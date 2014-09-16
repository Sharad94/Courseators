<?php
	$conn = pg_connect("host=localhost dbname=courseators user=psql password=psql") or die('Could not connect: ' . pg_last_error());
	$term=$_GET["term"];
	$term = strtoupper($term);
 	$query=pg_query("SELECT * FROM course where upper(courseid) like '%".$term."%' or upper(name) like '%".$term."%' order by courseid");
 	$json=array();
 	
    while($student=pg_fetch_array($query)){
		$json[]=array(
					'value'=> $student["courseid"],
                    'label'=>$student["courseid"]." - ".$student["name"]
                    );
	}
echo json_encode($json); 
?>