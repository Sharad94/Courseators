<?php
	session_start();
	$conn = pg_connect("host=localhost dbname=courseators user=psql password=psql") or die('Could not connect: ' . pg_last_error());
	
	$term=$_GET["term"];
	$term = strtoupper($term);
 	$year = $_SESSION['year'];
 	$sem = $_SESSION['sem'];
 	//echo ("SELECT * FROM (select coursesem.courseid,slotid,course.name,coursesem.year,coursesem.semnumber from course inner join coursesem on coursesem.courseid = course.courseid) as temp where upper(temp.courseid) like '%".$term."%' or upper(name) like '%".$term."%' and temp.year = ".$year." and temp.semnumber = ".$sem." order by courseid"); 
 	$query=pg_query("SELECT * FROM (select coursesem.courseid,slotid,course.name,coursesem.year,coursesem.semnumber from course inner join coursesem on coursesem.courseid = course.courseid) as temp where (upper(temp.courseid) like '%".$term."%' or upper(name) like '%".$term."%') and temp.year = ".$year." and temp.semnumber = ".$sem." and temp.courseid not in (select courseid from tentativecourses where studentid='".$_SESSION['username']."') order by courseid");
 	//echo($query);
 	
 	$json=array();
 	while($student=pg_fetch_array($query)){
		$json[]=array(
					'value'=> $student["courseid"],
                    'label'=>$student["courseid"]." - ".$student["name"]." - Slot ".$student['slotid']
                    );
	}
echo json_encode($json);
?>