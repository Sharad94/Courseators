<?php

	session_start();
	error_reporting(E_ERROR);
	$month = intval(date('n'));
	$year = intval(date('Y'));
	$odd = 0;
	if ($month > 6)
	{
		$odd = 1;
	}
	if ($odd==1){
		$_SESSION['year'] = $year;
		$_SESSION['sem']=1;	
	}
	else {
		$_SESSION['year'] = ($year-1);
		$_SESSION['sem'] = 2;
	}
	$_SESSION['year'] = 2014;
	$_SESSION['sem'] = 1;

	header('Content-Type: text/html; charset=utf-8');
	
	$conn = pg_connect("host=localhost dbname=courseators user=psql password=psql") or die('Could not connect: ' . pg_last_error());
?>
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="style.css">
<script src="jquery.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">

$(".nav-tabs li a").click(function(){
	$('.active').removeClass('active');
	$(this).parent().addClass('active');
});
	
</script>

<?php 
	if (isset($_SESSION['username'])){
		echo'<ul class="nav nav-tabs">
  				
  				<li><a href="allcourses.php">All Courses</a></li>
  				<li><a href="add_courses.php">Add courses</a></li>
  				<li><a href="currentcourse.php">Current Courses</a></li>
  				<li><a href="course_done.php">Make Timetable</a></li>
  				<li><a href="question.php">Questions</a></li>
  				<li><a href="ask_question.php">Ask Question</a></li>
  				<li><a href="review.php">Review</a></li>
  				<li><a href="suggest.php">Suggestions</a></li>

			</ul>';
		echo "<div class='message'>";
    		echo "<a href='student.php?username=".$_SESSION['username']."' class='welcome'>Welcome ".$_SESSION['username']."</a>";
    		echo "<a class='logout' href='logout.php'>logout</a>";
    	echo "</div>";
    }
?>
