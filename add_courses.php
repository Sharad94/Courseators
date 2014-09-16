<?php
include ('functions.php');
	
if (!isset($_SESSION['username'])){
  header("Location: index.php");
  session_destroy();  
}
$error = "";
$set = 0;
if (isset($_POST['submit'])){
 
 	if (isset($_POST['course'])){
 		$keysyear = array_keys($_POST['course']);
 		//var_dump($keysyear);
 		//echo "<br>";
 		//echo sizeof($_POST['course']);
		for ($i = 0 ; $i < sizeof($_POST['course']); $i++)
		{
			$semkey = array_keys($_POST['course'][$keysyear[$i]]);
			//echo sizeof($_POST['course'][$i]);
			for ($j =  0; $j < sizeof($_POST['course'][$keysyear[$i]]) ; $j++)
			{
				//echo $keysyear[$i];
				for ($k = 0; $k < sizeof($_POST['course'][$keysyear[$i]][$semkey[$j]]) ; $k++)
				{
					//echo "<br>";
					//echo $_POST['course'][$keysyear[$i]][$j][$k];
					//echo "<br>";
					if (strlen($_POST['course'][$keysyear[$i]][$semkey[$j]][$k])!=0)
						$added= check_courses($_SESSION['username'], $keysyear[$i], $semkey[$j], $_POST['course'][$keysyear[$i]][$semkey[$j]][$k]);
						if ($added)
						{
							//$error = "Your course was added";
						}
						else
						{
							$set  =1;
							//$error = "This Course was not floated that sem.";
						}
					
				}	
			}
		}
	}
	if ($set == 0)
	{
		$error = "Your course was added";
	}
	else
	{
		$error = "This Course was not floated that sem.";
	}

	$course_deleted =  $_POST['deleted'];
	$i= 0;
	while ( $i < strlen($course_deleted))
	{
		//echo substr($course_deleted, $i, $i+6);
		$deleted  = del_courses($_SESSION['username'], substr($course_deleted, $i, 6));
		if ($deleted)
		{
			$error = "Your course was deleted";
		}
		else
		{
			$error = "Your course was not deleted";
		}
		$i += 6;
	} 	
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
            	source:'getautocomplete.php',
            	minLength:2
        	});
	}
	$(document).ready(function(){
		
		
		$(".plus").click(function(){
			$id = this.id;
			$appendid = $id.substring(4);
			$year = $appendid.substring(1,5);
			$sem_no = $appendid.substring(6,7);
			var html='<div class="input-group"><span class="input-group-addon">Course : </span><input type="text" id="courses" class="form-control autocomplete" name="course['+$year+']['+$sem_no+'][]"></div>';
	  		//console.log(html);
	  		$(".form-course-add .courses"+$appendid).append(html);
			//$(".form-course-add .courses"+$appendid+" #course"+plusclick+"").focus();
			auto();
		});


	});
	
	

	$(document).ready(function(){
		$(".minus").click(function(){
			$id = this.id;
			$appendid = $id.substring(5);
			$courseid = $appendid.substring(1,7);
			var html=$courseid;
			document.getElementById($courseid).remove();
	  		//console.log(html);
	  		//$(".container .deleted").append(html);
	  		var temp = $(".container .deleted .input-deleted").val();
	  		$(".container .deleted .input-deleted").val(temp+html);
		});


	});

	/*var plus_load = false;
	function addForm(){
		if (plus_load){

		}
		else {
			var addplus = '<button id="plus" class="btn btn-default" style="float:left;">+</button>';
			$(".container .plus-button").append(addplus);
			plus_load = true;

		}
	}*/	
	
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
  			<h1>Add your previous courses<small> Semester Wise</small></h1>
		</div>
		<?php
		$ret_core = core_credits_done();
		if($ret_core){
			//var_dump($ret_core);
			echo "Core credits done : ".$ret_core[0]['sum'];
		}
		$core_req = get_core_req();
		if($core_req)
			echo "/ ".$core_req[0]['corecred']."<br>";

		$ret_elec = elec_credits_done();
		if($ret_elec){
			//var_dump($ret_elec);

			echo "Elec credits done : ".$ret_elec[0]['sum'];
		}
				$elec_req = get_elec_req();
		if($elec_req)
			echo "/ ".$elec_req[0]['eleccred']."<br>";
		?>
		<div class="plus-button">
		</div>
		<div id = "error"><?php echo $error; ?></div>
		<form action="add_courses.php" class="form-course-add"  method="post">
			<!-- <select class="semester form-control" style="width:40%;margin-left:45px;" onchange="addForm();">
				<option value="cs">Choose Semester</option>
				<option value="1">Semester 1</option>
				<option value="2">Semester 2</option>
			</select>
			<br>
			<div style="clear:both;"></div> -->
		<div class = "deleted" name="deleted">
			<!-- Div for course deleted -->
			<input name="deleted" class="input-deleted" style="display:none;">
		</div>
		

			<?php
				$month = intval(date('n'));
							
				$year = intval(date('Y'));
				$entry_year = intval(substr($_SESSION['username'],0,4));
				$odd = 0;
				
				if ($month > 6)
				{
					//It is an even sem
					$odd = 1;
				}
				$year = $year -1;

				for ($i = $entry_year; $i <= $year; $i++)
				{
					$courselist = add_courses($_SESSION['username'],0,$i);
					echo '<h4 style="float:left;">Year '.$i.' Semester 1</h4>';

					echo '<button type="button"  id="plus-'.$i.'-0" class="btn btn-default plus" style="float:left;">+</button>';
					echo '<div style="clear:both;"></div>';		
					echo '<div class="courses-'.$i.'-0">';
					for ($j=0;$j<sizeof($courselist);$j++){
						if ($courselist)
						{
							echo '<div class = "input-group" id = "'.$courselist[$j]['courseid'].'">';
							echo '<span class="input-group-addon">Courses</span>';
                  			echo '<input readonly style="float:left; width:90%;" type="text" value ="' .$courselist[$j]['courseid']. '" name="course['.$i.'][0][]" class="form-control" id="courses'.($i-$entry_year).'">';
                  			echo '<button type="button"  id="minus-'.$courselist[$j]['courseid'].'" class="btn btn-default minus" style="float:right;">-</button>';
							echo '<div style="clear:both;"></div>';		
                  			echo '</div>';
                  		}
                    }


					echo '</div><hr>';
					$courselist = add_courses($_SESSION['username'],1,$i);
					echo '<h4 style="float:left;" >Year '.$i.' Semester 2</h4>';
					echo '<button type="button" id="plus-'.$i.'-1" class="btn btn-default plus" style="float:left;">+</button>';
					echo '<div style="clear:both;"></div>';
					echo '<div class="courses-'.$i.'-1">';
					for ($j=0;$j<sizeof($courselist);$j++){
						if ($courselist)
						{
							echo '<div class = "input-group" id = "'.$courselist[$j]['courseid'].'">';
							echo '<span class="input-group-addon">Courses</span>';
                  			echo '<input readonly style="float:left; width:90%;" type="text" value ="' .$courselist[$j]['courseid']. '" name="course['.$i.'][1][]" class="form-control" id="courses'.($i-$entry_year).'">';
                  			echo '<button type="button"  id="minus-'.$courselist[$j]['courseid'].'" class="btn btn-default minus" style="float:right;">-</button>';
							echo '<div style="clear:both;"></div>';		
                  			echo '</div>';
                  		}
                    }
					echo '</div><hr>';
				}
				if ($odd == 1)
				{
					$courselist = add_courses($_SESSION['username'],0,($year +1));
					echo '<h4 style="float:left;">Year '.($year +1).' Semester 1</h4>';
					echo '<button type="button"  id="plus-'.($year +1).'-0" class="btn btn-default plus" style="float:left;">+</button>';
					echo '<div style="clear:both;"></div><hr>';		
					echo '<div class="courses-'.($year +1).'-0">';
					for ($j=0;$j<sizeof($courselist);$j++){
						if ($courselist)
						{
							echo '<div class = "input-group" id = "'.$courselist[$j]['courseid'].'">';
							echo '<span class="input-group-addon">Courses</span>';
                  			echo '<input readonly style="float:left; width:70%;" type="text" value ="' .$courselist[$j]['courseid']. '" name="course['.($year +1).'][0][]" class="form-control" id="courses'.(($year +1)-$entry_year).'">';
                  			echo '<button type="button" style="float:left;" id="minus-'.$courselist[$j]['courseid'].'" class="btn btn-default minus" style="float:right;">-</button>';
							echo '<div style="clear:both;"></div>';		
                  			echo '</div>';
                  		}
                    }

					echo '</div>';

				}
			?>

			<div class="courses-1">

			</div>

			<br>
			<button id="submit" type="submit" class="btn btn-default" name="submit">Update Courses</button>
		</form>
		
	</div>
</body>
</html>
