<?php
include ('functions.php');
$error = "";
	
if (!isset($_SESSION['username'])){
  header("Location: index.php");
  session_destroy();
}


if (isset($_GET['qid'])){
	//echo $_GET['qid'];
	$qid = $_GET['qid'];
}
//Display all answers

if (isset($_POST['submit'])){
	
	$qid = $_POST['hidden-input'];
	$ans = $_POST['myans'];
	//echo $ans;
	$success = add_ans($_SESSION['username'],$qid,$ans);
	$error = "";
	if (!$success)
	{
		$error = "Your answer was not submitted. Either you have already submitted an answer or Try again later.";
	}
	else
	{
		$error = "Congratulations! Your answer was submitted :)";
	}

}

if (isset($_POST['upvote']))
{	
	$qid = $_POST['qid'];
	$ans_stdt_id = $_POST['ans_student_id'];
	upvote($ans_stdt_id,$qid);
}
if (isset($_POST['downvote']))
{
	//echo "downvoted!";
	$qid = $_POST['qid'];
	$ans_stdt_id = $_POST['ans_student_id'];
	downvote($ans_stdt_id,$qid);
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
	});
	
	
</script>
<style type="text/css">
	.container{
		width:80%;
		margin: 0 auto;
	}
	
	</style>
</head>

<body>
	<div class="container">
		<div class="page-header">
  			<h1>Question</h1>
		</div>
		<button type ="text" class="btn btn-default" onClick="window.location.href='http://localhost/courseators/question.php';" >Go back to all question</button>
		<div id = "error" style = "padding:10px;">
			<?php echo $error; ?>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo '<h3 class="panel-title">'.get_ques($qid).'</h3>'; ?>
			</div>
			<div class="panel-body">
			<?php
				
				$answers = get_allans($qid);
				//echo sizeof($answers);
				for ($i = 0; $i < sizeof($answers); $i++)
				{
			?>

			
					<ul class="list-group">
						<li class="list-group-item">
							<div class ="alist">
								<form class="review-form" action="answers.php" method="post">
									<div class = "label-answer" style = "float:left;"><h5><b>Answer</b></h5></div>
									<div class = "buttons" style = "float:right;">
										<?php echo '<button type="submit" name ="upvote" class="btn btn-default">Upvote '.get_upvote($answers[$i]['studentid'],$qid).'</button>'; ?>
										<?php echo '<button type="submit" name = "downvote" class="btn btn-default">Downvote '.get_downvote($answers[$i]['studentid'],$qid).'</button>'; ?>
									</div>
									<div style = "clear:both;"></div>
									<div>	
										<p>
											<?php
												//var_dump($answers[$i]);
												echo $answers[$i]['answer'];
												echo '<input name ="ans_student_id" value="'.$answers[$i]['studentid'].'" style="display:none;">';
												echo '<input name ="qid" value="'.$answers[$i]['questionid'].'" style="display:none;">';
											?>
										</p>
									</div>
								</form>
							</div>
						</li>
					</ul>
			<?php
				}
			?>
			<form class="review-form" action="answers.php" method="post">
				<div class = "input-group">
					<span class="input-group-addon">Submit Your answer:</span>
	      			<input style="float:left;" placeholder ="...." type="text" name="myans" class="form-control">
	      			<?php
	      			echo '<input name ="hidden-input" value="'.$qid.'" style="display:none;">';
	      			?>
					<div style="clear:both;"></div>					
	  			</div><br>
	  			<button type="submit" name = "submit" style="float:left;" class="btn btn-default">Submit</button>
				</div>
			</form>
		</div>
	</div>


</body>
</html>


