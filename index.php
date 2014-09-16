<?php
	include('functions.php');
	$errors="";
	if (isset($_POST['submit'])){
		$username = strtoupper($_POST['username']);
		$password = $_POST['password'];
		if (strlen($username)==0 || strlen($password)==0){
			$errors="Please enter a username or password";
		}
		else {
			
			if (authenticate($username,$password)){
				
				$_SESSION['username'] = strtoupper($username);
				/*$depid = substr($_SESSION['username'], 4, 3);
				$view = "dep_".$depid;
				$query = "create view ".$view." as (select donecourses.studentid, donecourses.courseid from donecourses inner join studentdep on donecourses.studentid = studentdep.studentid where studentdep.depid ='".$depid."')";
				//echo $query;
				$result = pg_query($query);
*/
				header("Location: allcourses.php");
			}
			else {
				$errors = "You have entered a wrong username or password";
			}
		}
	}
?>

<!DOCTYPE html>
<html  lang="en">
	<head>
	
	<style type="text/css">
	/*body{
		display: table;
		height: 100%;
	}
	.nav-tabs{

	}*/
	</style>
	</head>
	<body>
	<div class="container" id="index-container">
		<div class="errors">
			<?php
				echo $errors;
			?>
		</div>
		<div class="login" id="index-login">
				<form action="index.php" method="post">
					<div class="input-group">
						<span class="input-group-addon">@</span>
  						<input type="text" class="form-control" name="username" placeholder="Username" required="required">
  					</div>
				<br>
		    		<div class="input-group">
						<span class="input-group-addon">@</span>
  						<input type="password" class="form-control" name="password" placeholder="Password" required="required">
  					</div>
		    	<br>
		    	<button type="submit" class="btn btn-default" name="submit">Submit</button>
			</form>
		</div>
	</div>
	</body>
</html>
