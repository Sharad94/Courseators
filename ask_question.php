<?php
include ('functions.php');
$error = "";
	
if (!isset($_SESSION['username'])){
  header("Location: index.php");
  session_destroy();  
}

if (isset($_POST['submit'])){

 	$date = date('Y/m/d H:i:s');
 	//function ask_question($username,$ques,$time,$course)
 	if(ask_question($_SESSION['username'], pg_escape_string($_POST['question']), $date, $_POST['course'] ))
		$error = "Your Question was submitted.";
	else
		$error = "Your Question wasn't submitted";
	//$result = pg_query("select count(*) from student")  or die('Error: ' . pg_last_error());

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
		width:50%;
		margin: 0 auto;
	}
	
	</style>
</head>

<body>
	<div class="container">
		<div class="page-header">
  			<h1>Ask your question!</h1>
		</div>
		 
		<form action="ask_question.php" class="form-course-add"  method="post">
			<?php
			
				echo $error;	
				echo "<h3>Enter your question here*</h3>";
				echo '<textarea rows = "4" class= "form-control" required = "required" name="question" placeholder ="I would like to know.. "></textarea>';
				echo '<br>';
				echo '<h3 style = "float:left;margin-right:15px;">Course ID*: </h3><input required="required" id = "course_no" style = "width:50%;margin-top:16px;" class= "form-control  autocomplete" name="course" placeholder ="AML120"></input>';
				echo '<button id="submit" type="submit" class="btn btn-default" name="submit">Post Question</button>';
					
			
			
			?>
		<br>
		
		</form>
		
	</div>
</body>
</html>
