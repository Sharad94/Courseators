<?php
include ('functions.php');
$error = "";
	
if (!isset($_SESSION['username'])){
  header("Location: index.php");
  session_destroy();  
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

		 $(".addysearch").click(function(){
                      var id = $(this).attr('id');
                      window.open(
			'currentcourse.php?name='+id+'&submit=',
			'_blank'
		      );
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
  			<h1>Find courses !</h1>
		</div>


	<form action="suggest.php" class="form-course-add"  method="post">
		<select class="form-control" id = "dropdown" name ="dropdown">
		<?php
			//echo "hehe";
			$arr = array();

			array_push($arr,'All students (current semester)');
			array_push($arr,'Same department students (current semester)');
			array_push($arr,'Same department students (all semesters)');
			array_push($arr,'Friends (current semester)');
			array_push($arr,'Friends (all semesters)');
			for($i=1; $i<6; $i++){
				if (isset($_POST['submit'])){
				if($_POST['dropdown']==$i)
					echo "<option value = '".$i."' selected ='selected'>".$arr[$i-1]."</option>";
				else
					echo "<option value = '".$i."'>".$arr[$i-1]."</option>";
			}
			else
				echo "<option value = '".$i."'>".$arr[$i-1]."</option>";
			}

		?>
		</select>
		<button id="submit" type="submit" class="btn btn-default" name="submit">Update Courses</button>
	</form>
<?php

if (isset($_POST['submit']))
	$ret = suggest($_POST['dropdown']);
else
	$ret = suggest(1);

//var_dump($ret);
if (sizeof($ret)){
	for ($i=0;$i<sizeof($ret);$i++){
		$courseinfo = getcourseinfo($ret[$i]['courseid']);
		echo '<div class="panel panel-default addysearch" id="'.printarr($courseinfo,'courseid').'">
  					<div class="panel-heading" id="panel-heading-info">
    						<div class="info">
      							<h3 class="panel-title">';

							  echo printarr($courseinfo,'courseid'); 
							  echo " ";
							  echo printarr($courseinfo,'name');

      							echo '</h3>
					</div>
				</div>
			</div>';						
	}
}


?>
	
		
	</div>
</body>
</html>
