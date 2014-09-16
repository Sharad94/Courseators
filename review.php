<?php
include ('functions.php');
$error = "";
	
if (!isset($_SESSION['username'])){
  header("Location: index.php");
  session_destroy();  
}



if (isset($_POST['submit'])){
$course = trim($_POST['hidden-input']);	
//echo $course;
$ret = add_reviews($_SESSION['username'],$course,$_POST['attendance'],$_POST['rating'],$_POST['grading'],$_POST['comments']);
if ($ret)
{
	echo "Review added";
}
else echo "Review addition failed";
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



	
	$(document).ready(function(){
		
		$(".autocomplete").autocomplete({
            source:'getcoursecomplete.php',
            minLength:2
        });

        $(".panel-heading").click(function(){
        var id = $(this).attr('id');
        //console.log(id);
        $('#panel-body-'+id).toggle("slow");
        });


	});

	function showValue(newValue,id)
                {
                  var show;
                  if (newValue>=0 && newValue<=2){
                    show = "Chill Policy";
                  }
                  else if (newValue>=3 && newValue<=5){
                    show = "Doesn't Care";
                  }
                  else if (newValue>=6 && newValue<=8){
                    show = "75% Attendance Rule";
                  }
                  else if (newValue>=8 && newValue<=10){
                    show = "OMG!";
                  }
                  document.getElementById(id).innerHTML=newValue+" - "+show;
                }

	function showGrade(newValue,id)
                {
                	console.log(id);
                  var show;
                  if (newValue>=0 && newValue<=2){
                    show = "Chill Policy";
                  }
                  else if (newValue>=3 && newValue<=5){
                    show = "Absolute";
                  }
                  else if (newValue>=6 && newValue<=8){
                    show = "Relative";
                  }
                  else if (newValue>=8 && newValue<=10){
                    show = "Fighter";
                  }
                  document.getElementById(id).innerHTML=newValue+" - "+show;
                }

    	function showRating(newValue,id)
                {
                  var show;
                  if (newValue>=0 && newValue<=1){
                    show = "Hard Course";
                  }
                  else if (newValue>=2 && newValue<=3){
                    show = "Moderate Course";
                  }
                  else if (newValue==4 ){
                    show = "Easy Course";
                  }
                  else if (newValue==5){
                    show = "Extremely Easy";
                  }
                  document.getElementById(id).innerHTML=newValue+" - "+show;
                }

	
	
</script>
<style type="text/css">
	.container{
		width:70%;
		margin: 0 auto;
	}
	.panel-heading{
		cursor:pointer;
	}
	.panel-body{
		display: none;
	}
	</style>
</head>

<body>
	<div class="container">
		<div class="page-header">
  			<h1>Review your courses!</h1>
		</div>
		
			<?php
				$courses = all_courses($_SESSION['username']);
				if ($courses)
				{
					for ($i=0;$i<sizeof($courses);$i++)
					{	
						$courseinfo = getcourseinfo($courses[$i]['courseid']);
						$locked = isdone_review($_SESSION['username'],$courses[$i]['courseid']);
						// echo "<br>";
						// echo $locked;
						if ($locked)
						{
							$values_filled = get_review($_SESSION['username'],$courses[$i]['courseid']);
						}
						echo '<div class="panel panel-default addysearch" id="'.printarr($courseinfo,'courseid').'">
		          					<div class="panel-heading" id="'.printarr($courseinfo,'courseid').'">
		        						<div class="info">
		          							<h3 class="panel-title">';

											echo printarr($courseinfo,'courseid'); 
										  	echo " ";
										  	echo printarr($courseinfo,'name');
		          							echo '</h3>
										</div>
									</div>';
									echo '<div class="panel-body" id="panel-body-'.printarr($courseinfo,'courseid').'">';
					?>				
									<form action="review.php" class="review-form" role="search" method="post">
									<div>
										<?php
										echo '<input name ="hidden-input" value="'.printarr($courseinfo,'courseid').'" style="display:none;">';
										?>
							        	<div class="left-form">
								            <div class="input-group">
								              <span class="input-group-addon">Grading Policy:</span>
								              <?php
								              if (!$locked)
									            {
									            	echo '<input type="range" id = "grading-policy-'.printarr($courseinfo,'courseid').'" name = "grading" style="margin-top:1.5%;" min="0" max="10" value="0" step="1" onchange="showGrade(this.value,\'range-grade-'.$i.'\')"/>';
									          	}
									          	else
									          	{
									          		echo '<input type="range" disabled id = "grading-policy-'.printarr($courseinfo,'courseid').'" name = "grading" style="margin-top:1.5%;" min="0" max="10" value="'.$values_filled['grading'].'" step="1" onchange="showGrade(this.value,\'range-grade-'.$i.'\')"/>';	
									          	}
								              ?>
								            </div>
								            <?php echo '<span id="range-grade-'.$i.'"></span>' ?>
								            <br>
								            <br>
								            <div class="input-group">
								              <span class="input-group-addon">Attendance Policy:</span>
								              <?php
								              if (!$locked)
								                {
								              		echo '<input type="range" id = "att-policy-'.printarr($courseinfo,'courseid').'" name = "attendance" style="margin-top:1.5%;" min="0" max="10" value="0" step="1" onchange="showValue(this.value,\'range-attendance-'.$i.'\')"/>';
								              	}
								              	else
								              	{
								              		echo '<input type="range" disabled id = "att-policy-'.printarr($courseinfo,'courseid').'" name = "attendance" style="margin-top:1.5%;" min="0" max="10" value="'.$values_filled['attpolicy'].'" step="1" onchange="showValue(this.value,\'range-attendance-'.$i.'\')"/>';	
								              	}
								              ?>
								            </div>
								            <?php echo '<span id="range-attendance-'.$i.'"></span>' ?>
								            <br>
								            <br>
								            <div class="input-group">
								              	<span class="input-group-addon">Rate the Course:</span>
								              	<?php
								              	if (!$locked)
								              	{
								              		echo '<input type="range" name ="rating" id = "rate-'.printarr($courseinfo,'courseid').'" style="margin-top:1.5%;" min="0" max="5" value="0" step="1" onchange="showRating(this.value,\'range-rating-'.$i.'\')"/>';
								              	}
								              	else
								              	{	
								              		echo '<input type="range" disabled name ="rating" id = "rate-'.printarr($courseinfo,'courseid').'" style="margin-top:1.5%;" min="0" max="5" value="'.$values_filled['rating'].'" step="1" onchange="showRating(this.value,\'range-rating-'.$i.'\')"/>';	
								              	}
								              	?>
								            </div>
								        	<?php echo '<span id="range-rating-'.$i.'"></span>' ?>
								        	<br>
								        	<br>
							          	</div>
							          	<div class="right-form">
								            <p> Comments:</p>
								            <?php
								            if (!$locked)
								            {
								            	echo '<textarea name = "comments" id = "comments-'.printarr($courseinfo,'courseid').'" class="form-control" rows="3"></textarea>';
								        	}
								        	else
								        	{
								        		echo '<textarea name = "comments" disabled  id = "comments-'.printarr($courseinfo,'courseid').'" class="form-control" rows="3">'.$values_filled['comments'].'</textarea>';
								        	}
								            ?>
								            <br>
								            <br>
								            <button type="submit" class="btn btn-default" name="submit">Submit Review</button>
							          	</div>
							      </div>
							          
							        </form>

					<?php

									echo '</div>';
								echo '</div>';

					}
				}
				else
				{
					echo "<h3>You have not added a course yet for review</h3>";
				}

//			echo '</div>';
					
			
			
			?>
	</div>
</body>
</html>
