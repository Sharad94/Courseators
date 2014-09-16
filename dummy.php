<?php
include('functions.php');
$arr = pg_query('select studentid from student');
while($row = pg_fetch_assoc($arr)){
	$ret[] = $row;
}
for ($j=0;$j<sizeof($ret);$j++){
	
		$stu = $ret[$j]['studentid'];
		$i=intval(substr($stu,9,2));
		add_dummydata($stu,$i);
	
	
}
?>