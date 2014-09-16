<?php
	$conn = pg_connect("host=localhost dbname=courseators user=psql password=psql") or die('Could not connect: ' . pg_last_error());
	$profid = trim($_POST["data"][0]);
	$courseid = trim($_POST["data"][1]);
	$arr = get_reviews_course($courseid,$profid);
	echo json_encode($arr);
	function get_reviews_course($courseid,$profid){
		$arr = pg_query("select reviews.attpolicy,reviews.rating,reviews.grading,reviews.comments from reviews inner join (select distinct reviewid from donecourses where courseid='".$courseid."') as temp on temp.reviewid = reviews.reviewid and reviews.profid='".$profid."'");
		if (pg_num_rows($arr)==0){
			return false;
		}

		while($row = pg_fetch_assoc($arr)){
			$ret[] = $row;
		}
		return $ret;
	}
	
?>