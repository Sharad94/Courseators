<?php
	include('functions.php');
	$studentid = $_GET['username'];
	$studentid = trim(strtoupper($studentid));
	$arr = getuser($studentid);
	$diff = false;
	$student="";
	if ($arr){
		$student = pg_fetch_assoc($arr);
		$prof = pg_fetch_assoc(getprof($student['advisor']));

	}
	if (strcmp($_SESSION['username'],trim(strtoupper($_GET['username'])))!=0) {
		$diff = true;
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
				source:'getstudentcomplete.php',
				minLength:2
			});
			$('.add').click(function(){
				var obj = <?php echo json_encode($studentid); ?>;
				console.log(obj);
                var id_of_item_to_approve = $(this).attr("id");
                var data=[];
                data.push(id_of_item_to_approve);
                data.push(obj);
                $.ajax({
                    url: "send_request.php", //This is the page where you will handle your SQL insert
                    type: "POST",
                    data: {data:data}, //The data your sending to some-page.php
                    success: function(data){
                        console.log(data);
                        window.location.reload();
                    },
                    error:function(){
                        console.log("AJAX request was a failure");
                    }   
                  });

                  
                });

			$('.noti-friend').click(function(){
				//var obj = <?php echo json_encode($studentid); ?>;
                var id_of_item_to_approve = $(this).attr("id");
                var obj = $(this).attr("name");
                var data=[];
                data.push(id_of_item_to_approve);
                data.push(obj);
                $.ajax({
                    url: "add_friend.php", //This is the page where you will handle your SQL insert
                    type: "POST",
                    data: {data:data}, //The data your sending to some-page.php
                    success: function(data){
                        console.log(data);
                        window.location.reload();
                    },
                    error:function(){
                        console.log("AJAX request was a failure");
                    }   
                  });
                  
                });
				$("#notifications").click(function(){
					$(".notifications").toggle();
				});
				$(".panel-heading").click(function(){
                      var id = $(this).attr('id');
                      console.log(id+'-body');
                      $('#'+id+'-body').toggle();
                });
			});
	</script>
	<style type="text/css">
	.notifications{
		display: none;
	}
	.panel-heading{
		cursor: pointer;
	}
	.panel-body{
		display: none;
	}
	</style>
	
</head>
<body>
	<div class="container">
		<div class="form">
        <form method="get" action="student.php">
              <div class="row">
                <div class="col-lg-8">
                  <div class="input-group">
                    <input type="text" class="form-control autocomplete" name="username" placeholder="Search Professor/Student" required="required">
                    <span class="input-group-btn">
                      <button id="submit" class="btn btn-default" name="submit" type="submit">Search</button>
                    </span>
                    <?php
                   
						//echo var_dump(pg_fetch_assoc($checkprof));
                    	//checkiffriend($_SESSION['username'],$_GET['username']);
                    ?>
                    <span class="input-group-btn" style="padding-left:5%;">
					<?php
					
                      	if ($diff) {
                      		if (checkiffriend($_SESSION['username'],$_GET['username'])){
								echo '<button class="btn btn-default" name="friend" type="button" disabled>Friends</button>';
                      		} 
                  			else {
	                 			if (tentativerequestexists($_SESSION['username'],$_GET['username'])){

	                 				echo '<button id="accept" class="btn btn-default noti-friend" name = "'.$_GET['username'].'" type="button">Accept</button>';
	                 			}
	                 			else{
	                 				if (requestexists($_SESSION['username'],$_GET['username'])) {
	                 					echo '<button id="friend" class="btn btn-default add" name="friend" type="button" disabled>Friend Request Sent</button>';
	                 				}
	                 				else {
	                 					echo '<button id="friend" class="btn btn-default add" name="friend" type="button">Send Friend Request</button>';
	                 				}
	                 			}	
                      		}
                    	}
                    
                    ?>
                    	
                    	<?php
                    		if (!$diff){
                    			$tentativefriends = gettentativefriends($_SESSION['username']);
		  						if ($tentativefriends){
		  							echo '<button id="notifications" class="btn btn-default" name="submit" type="button">'.
		  								sizeof($tentativefriends)
		  							.'</button>';
		  						}
		  						else {
		  							echo '<button id="notifications" class="btn btn-default" name="submit" type="button">0</button>';
		  						}

		  					}
                    	?>
                    	
                    </span>
                  </div>
                </div>
              </div>
        	</form>
  		</div>
  		<div class="notifications">
	  		<ul class="list-group">
	  		<?php
		  		if ($_SESSION['username']==$studentid){
		  			$tentativefriends = gettentativefriends($_SESSION['username']);
		  			if ($tentativefriends){
		  				for ($i=0;$i < sizeof($tentativefriends);$i++){
		  					
		  					echo '<li class="list-group-item">
		  						<a href=?username="'.$tentativefriends[$i]['studentid1'].'">'.$tentativefriends[$i]['name'].'</a>
		  						<span class="input-group-btn">
		  							<button id="accept" class="btn btn-default noti-friend" name = "'.$tentativefriends[$i]['studentid1'].'" type="button">Accept</button>
		  							<button id="decline" class="btn btn-default noti-friend" name = "'.$tentativefriends[$i]['studentid1'].'" type="button">Decline</button>
		  						</span>
		  					</li>';
		  				}
		  			}
		  		}
	  		?>
			</ul>
  		</div>
		<div class="page-header">
  			<div style="float:right;">
  			<?php
				echo $student['img'];
			?>
			</div>
  			<h1>Welcome <?php  echo $student['name']; ?><small><?php  echo $student['studentid']; ?></small></h1>
		</div>
		<div style="clear:both;"></div>
		<?php
			if (strlen($prof['name'])){
		?>
		<dl class="dl-horizontal">
  			<dt>Advisor :</dt>
  			<dd>
  			<?php
  				echo $prof['name'];
  			?>
  			</dd>
		</dl>
		<?php 
			}
			if (strlen($student['email'])){ 
		?>
		<dl class="dl-horizontal">
  			<dt>Email :</dt>
  			<dd>
  			<?php
  				echo $student['email'];
  			?>
  			</dd>
		</dl>
		<?php 
			}
			if (strlen($student['postaladdr'])){ 
		?>
		<dl class="dl-horizontal">
  			<dt>Postal Address :</dt>
  			<dd>
  			<?php
  				echo $student['postaladdr'];
  			?>
  			</dd>
		</dl>
		<?php 
			}
			if (strlen($student['telno'])){ 
		?>
		<dl class="dl-horizontal">
  			<dt>Tel No :</dt>
  			<dd>
  			<?php
  				echo $student['telno'];
  			?>
  			</dd>
		</dl>
		<?php 
			}
			if (strlen($student['mobile'])){ 
		?>
		<dl class="dl-horizontal">
  			<dt>Mobile :</dt>
  			<dd>
  			<?php
  				echo $student['mobile'];
  			?>
  			</dd>
		</dl>
		<?php 
			}
			if (strlen($student['address'])){ 
		?>
		<dl class="dl-horizontal">
  			<dt>Address :</dt>
  			<dd>
  			<?php
  				echo $student['address'];
  			?>
  			</dd>
		</dl>
		<?php 
			}
			if (strlen($student['homephone'])){ 
		?>
		<dl class="dl-horizontal">
  			<dt>Home Phone :</dt>
  			<dd>
  			<?php
  				echo $student['homephone'];
  			?>
  			</dd>
		</dl>
		<?php 
			}
			if (strlen($student['roomaddr'])){ 
		?>
		<dl class="dl-horizontal">
  			<dt>Room Address :</dt>
  			<dd>
  			<?php
  				echo $student['roomaddr'];
  			?>
  			</dd>
		</dl>
		<?php 
			}
			if (strlen($student['url'])){ 
		?>
		<dl class="dl-horizontal">
  			<dt>URL :</dt>
  			<dd>
  			<?php
  				echo $student['url'];
  			?>
  			</dd>
		</dl>
		<?php 
			}
			$fr = getfriends($_GET['username']);
			if ((!$diff || checkiffriend($_SESSION['username'],$_GET['username'])) && $fr){
				
				echo '<div class="panel panel-default">
  					<div class="panel-heading" id="panel-slide-1">
    					<h3 class="panel-title">'.sizeof($fr).' Friends</h3>
  					</div>
  					<div class="panel-body" id="panel-slide-1-body">
  					<ul class="list-group">';
    					
					
					for ($i=0; $i<sizeof($fr); $i++){
						
						echo '<li class="list-group-item"><a target="blank" href="student.php?username='.$fr[$i]['id'].'">'.$fr[$i]['name'].'</a></li>';
					}
				echo ' </ul>
					</div>
				</div>';
			}
		?>
		<?php
			//get_all_donecourses_name($_GET['username']);
		?>
		<?php
			$arr = get_all_donecourses_name($_GET['username']);
			if ((!$diff || checkiffriend($_SESSION['username'],$_GET['username'])) && $arr){
				
				echo '<div class="panel panel-default">
						<div class="panel-heading" id="panel-slide-2">
							<h3 class="panel-title">'.sizeof($arr).' Courses Done</h3>
						</div>
		  				<div class="panel-body" id="panel-slide-2-body">
		  					<ul class="list-group">';
			    			
			    			
			    			for ($i=0; $i<sizeof($arr); $i++){
								echo '<li class="list-group-item"><a target="blank" href="allcourses.php?name='.$arr[$i]['courseid'].'&submit=">'.$arr[$i]['name'].'</a> Year '.$arr[$i]['year'].' Sem '.$arr[$i]['semnumber'].'</li>';
							}
			    		}
			    	
			    echo '</ul>
		  	</div>
		</div>';
		?>
		<?php
			$arr = get_all_tentativecourses_name($_GET['username']);
			if ((!$diff || checkiffriend($_SESSION['username'],$_GET['username'])) && $arr){
				
				echo '<div class="panel panel-default">
						<div class="panel-heading" id="panel-slide-3">
							<h3 class="panel-title">'.sizeof($arr).' Tentative Courses</h3>
						</div>
		  				<div class="panel-body" id="panel-slide-3-body">
		  					<ul class="list-group">';
			    			
			    			
			    			for ($i=0; $i<sizeof($arr); $i++){
								echo '<li class="list-group-item"><a target="blank" href="currentcourse.php?name='.$arr[$i]['courseid'].'&submit=">'.$arr[$i]['name'].'</li>';
							}
			    		}
			    	
			    echo '</ul>
		  	</div>
		</div>';
		?>
	</div>
</body>
</html>