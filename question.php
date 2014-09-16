<?php
include ('functions.php');
$error = "";
	
if (!isset($_SESSION['username'])){
  header("Location: index.php");
  session_destroy();  
}
$topques="";
$courseid="";
function temp($str){
	$nam=array();
	$courseid = $_POST[$str];
	$unansweredques = get_unanswered_questions($courseid);
	$topques = get_bestans($courseid);
	//var_dump($unansweredques);
	//var_dump($topques);
	if ($topques){
		if ($unansweredques)
			$topques = array_merge($topques,$unansweredques);		
	}
	else if ($unansweredques){
		$topques = $unansweredques;
	}
	array_push($nam, $courseid);
	array_push($nam, $topques);
	return $nam;
}
$courseid="";

if(isset($_POST['submit-course'])){
	$topques = temp('search-course')[1];
	$courseid = $_POST['search-course'];
}
//var_dump($topques);

if (isset($_POST['submit'])){
	$topques = temp('hidden-input-course')[1];
	//var_dump($topques);
	$qid = $_POST['hidden-input'];
	$courseid = $_POST['hidden-input-course'];
	$ans = $_POST['myans'];
	//echo $ans;
	$success = add_ans($_SESSION['username'],$qid,$ans);
	$error = "";
	if (!$success)
	{
		$error = "Your answer was not submitted. Try again later.";
	}
	else{
		$error = "Your answer was submitted :)";
	}
}

if (isset($_POST['upvote']))
{
	$ans_stdt_id = $_POST['ans_student_id'];
	$qid = $_POST['hidden-input'];
	upvote($ans_stdt_id,$qid);
}
if (isset($_POST['downvote']))
{
	//echo "downvoted!";
	$ans_stdt_id = $_POST['ans_student_id'];
	$qid = $_POST['hidden-input'];
	downvote($ans_stdt_id,$qid);
}

//####################################################################################################
//####################################################################################################
//Check for multiple upvotes and downovtes
//####################################################################################################
//####################################################################################################

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
        $(".anslist").click(function(){
                      var id = $(this).attr('id');
                      window.open(
							'answers.php?qid='+id,
							'_blank'
		      			);
                  });
	});
	
	
</script>
<style type="text/css">
	.container{
		width:80%;
		margin: 0 auto;
	}
	.anslist{
		cursor: pointer;
	}
	
	</style>
</head>

<body>
	<div class="container">
		<div class="page-header">
  			<h1>Questions and Answers</h1>
		</div>
		<div id = "search-course">
			<form class="navbar-form navbar-left" role="search" action="question.php"  method="post">
				<div id = "search" style = "float:left;">
					<h5>Course:</h5>
				</div>
				<div class="form-group" style= "margin-left:10px;">
			    	<input type="text" class="form-control" placeholder="Search" name="search-course">
				</div>
			  	<button type="submit" name= "submit-course" class="btn btn-default">Submit</button>
			</form>
		</div>
		<div id ="error">
			<?php echo '<h3>'.$error.'</h3>'; ?>
		</div>
		<?php
		
		if ($topques){

			for ($i =0 ;$i < sizeof($topques); $i++)
			{
				
				$ans = get_topans($topques[$i]['questionid']);
				
		?>
			<form class="review-form" action="question.php" method="post" role="search">
			<div style = "margin-top:80px;" class="panel panel-default">
				<div class="panel-heading">
					<?php echo '<b><h3 class="panel-title">'.get_ques($topques[$i]['questionid']).' - '.get_course_from_ques($topques[$i]['questionid']).'</b></h3>'; ?>
					<br>
					<p><i>Question asked by: <?php echo get_ques_info($topques[$i]['questionid'])['studentid']; ?></i></p>
				</div>
					
					<div class="panel-body">
					
					<div id = "answer">
						<?php
							if ($ans){
						?>
						<div class = "label-answer" style = "float:left;"><h5><b>Top Answer</b></h5></div>
						<div class = "buttons" style = "float:right;">
							<?php echo '<button type="submit" name ="upvote" class="btn btn-default">Upvote '.get_upvote($ans['studentid'],$ans['questionid']).'</button>'; ?>
							<?php echo '<button type="submit" name = "downvote" class="btn btn-default">Downvote '.get_downvote($ans['studentid'],$ans['questionid']).'</button>'; ?>
						</div>
						<div style = "clear:both;"></div>
						<div>	
							<p>
								<?php
									//echo $i;
									//echo "<br>";
									//echo $topques[$i]['questionid'];
									//var_dump($topques[$i]);
									//var_dump($ans);
									echo $ans['answer'];
									//var_dump($ans);
									echo "<br>";
									echo "<br>";
									echo "<i>";
									echo "Question Answered by: ";
									echo $ans['studentid'];
									echo '<input name ="ans_student_id" value="'.$ans['studentid'].'" style="display:none;">';
									echo "</i>";
								?>
							</p>
						</div>
					</div>
					<?php echo '<div class="panel-footer anslist" id="'.$topques[$i]['questionid'].'">View All Answers</div>' ?>
					<?php
						}
					?>
					<?php echo '<div class = "input-group" >'; ?>
						<span class="input-group-addon">Submit Your answer:</span>
	          			<input style="float:left;" placeholder ="...." type="text" name="myans" class="form-control">
	          			<?php
	          			echo '<input name ="hidden-input" value="'.$topques[$i]['questionid'].'" style="display:none;">';
	          			echo '<input name ="hidden-input-course" value="'.$courseid.'" style="display:none;">';
	          			?>
						<div style="clear:both;"></div>					
          			</div>
          			<br>
          			<button type="submit" name = "submit" style="float:left;" class="btn btn-default">Submit</button>

				</div>
			</div>
			</form>
		<?php
			}
		}
		?>	
	</div>
</body>
</html>
