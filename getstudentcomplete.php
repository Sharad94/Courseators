<?php
	$conn = pg_connect("host=localhost dbname=courseators user=psql password=psql") or die('Could not connect: ' . pg_last_error());
	$term=$_GET["term"];
	$term = strtoupper($term);
 	$query=pg_query("SELECT studentid as id,name FROM student where upper(student.studentid) like '%".$term."%' or upper(student.name) like '%".$term."%' union select profid as id,name from prof where upper(prof.profid) like '%".$term."%' or upper(prof.name) like '%".$term."' order by id");
 	$json=array();
 	
    while($student=pg_fetch_array($query)){
		$json[]=array(
					'value'=> $student["id"],
                    'label'=>$student["id"]." - ".$student["name"]
                    );
	}
echo json_encode($json); 
?>