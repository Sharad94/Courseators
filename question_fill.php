<?php
include('functions.php');
$arr = pg_query('select studentid from student order by name limit 100');
while($row = pg_fetch_assoc($arr)){
	$ret[] = $row;
}
for ($j=0;$j<sizeof($ret);$j++){
	
		$stu = $ret[$j]['studentid'];
		$i=intval(substr($stu,9,2));
		//add_dummydata($stu,$i);
		add_questions($stu,$i);
		
}
?>
