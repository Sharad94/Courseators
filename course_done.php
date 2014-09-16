<?php	
	include('functions.php');


$error = "";

if (!isset($_SESSION['username'])){
  header("Location: index.php");
  session_destroy();  
}

if (isset($_POST['make_tt'])){
  header("Location: timetable.php"); 
}


if (isset($_POST['submit'])){
	$course = trim($_POST['name']);	
	if (checkcourse($course)){
		$prereq = check_prereq($_SESSION['username'],$course);
		if ($prereq == "")
		{
			if (checkcourse($course))
			{
				$overlap = check_overlap($_SESSION['username'],$course);
				if ($overlap == "")
				{
					add_tentative($_SESSION['username'],$course);
				}
				else
				{
					$error = "This course overlapps with already completed course - ".$overlap.".";
				}
			}
		}
		else
		{
			$error = "You have not completed the prereq - ".$prereq." of this course.";
		}
	}
	
	$totalcredit = credit_tentative($_SESSION['username']);
	$credit = 28 - $totalcredit;
	
	if ($credit < 0)
	{
		delete_tentative($_SESSION['username'],$course);
		$error = "Your total credits become ".$totalcredit.".Credit limit Exceeded! Delete some course before adding a new one.";
	}
}
						
if (isset($_POST['minus'])){
	$course = trim($_POST['hidden-input']);	
	delete_tentative($_SESSION['username'],$course);
}



?>



<html>
<head>
	<script type="text/javascript" src="jquery.js"></script>
	<script type="text/javascript" src="jquery-ui.js"></script>
	<link rel="stylesheet" type="text/css" href="jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script type="text/javascript">
	function auto(){
		$(".autocomplete").autocomplete({
            source:'getcoursecomplete.php',
            minLength:2
        });
	}

	$(document).ready(function(){
			auto();
			$(".course-go").click(function(){
				$("#course-done-form").submit();
			});
		
	});
	

</script>

	<style type="text/css">
	
	</style>
</head>
<body>
	<div class="container">
		<h2>
			Make your Timetable
		</h2>
			<div class = "deleted" name="deleted">
			<!-- Div for course deleted -->
			<input name="deleted" class="input-deleted" style="display:none;">
			</div>
			<div class="form" id="course_form">
		        <form class = "courses_done" method="post" action="course_done.php">
		              <div class="row">
		                <div class="col-lg-6">
		                  <div class="input-group">
		                    <input type="text" class="form-control autocomplete" name="name" placeholder="Select Course" required="required">
		                    <span class="input-group-btn">
		                      <button id="submit" class="btn btn-default course-go" name="submit" type="submit">Add!</button>
		                    </span>
		                  </div>
		                </div>
		              </div>
		        </form>
		        <div id = "courses_tentative">
					<!-- Div for already added tentative courses -->
					<div id = "top">
						
						
						<div id ="credits_rem">
							<?php 
							//echo $credit_tentative($_SESSION['username']);
							$credit = 28 - credit_tentative($_SESSION['username']);
							echo "<h4>Credits Remaining(out of 28): ".$credit." </h4>";
							if ($error != "")
							{
								echo $error;
							}
							?>
						</div>
						<h4 style = "style=float:left;">Courses already added</h4>
					</div>
					<div style="clear:both;"></div>
					<div id = "course-list">
						<?php
							$courselist = tentative_courses($_SESSION['username']);
							for ($j=0;$j<sizeof($courselist);$j++)
							{
								if ($courselist)
								{
									echo '<div class = "input-group" id = "'.$courselist[$j]['courseid'].'">';
									echo '<span class="input-group-addon">Credits - '.get_credit($courselist[$j]['courseid']).'</span>';
		                  			echo '<span class="input-group-addon" >'.($courselist[$j]['name']).'</span>';
		                  			echo '<input readonly style=" width:60%;" type="text" value ="' .$courselist[$j]['courseid'].' - '.get_coursename($courselist[$j]['courseid']). '" name="course[]" class="form-control">';
		                  			echo '<form class = "minus" method = post action="course_done.php">';
		                  			echo '<button type="submit"  id="'.$courselist[$j]['courseid'].'" name = "minus" class="btn btn-default" style="float:left;">-</button>';
									echo '<input name ="hidden-input" value="'.$courselist[$j]['courseid'].'" style="display:none;">';
		                  			echo '</form>';
									echo '<div style="clear:both;"></div>';		
		                  			echo '</div>';
		                  		}
		                    }
						?>
					</div>
		        </div>
		        <button onClick="window.location.href='http://localhost/courseators/timetable.php';" name = "make_tt" style ="margin-top:20px;" class="btn btn-default">Make Timetable</button> 
		    </div>
	</div>
</body>



</html>
