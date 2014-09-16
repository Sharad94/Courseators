<!doctype html>
<?php
	include('functions.php');
	if(isset($_SESSION['username'])){
		$studentid = $_SESSION['username'];

		$arr = gettentative($studentid);
		
	}
	else {
		session_destroy();
	}
?>

<html>
<head>
	<style type="text/css">
		.container{
			width: 100%;
		}
		table, th, td{
			border : 1px solid black;
		}
		.timetable{
			width : 88%;
		}
		.time{
			font-weight: bold;
			background-color: #EFFBFF;
			text-align: center;
			border : 1px solid black;
			float: left;
			width:8%;
			padding-top: 20px;
			padding-bottom: 20px;
		}

		.monday-row{

		}
		.monday{
			float: left;
			text-align: center;
			height: 20%;
			padding-top: 20px;
			padding-bottom: 20px;
			border:1px solid black;
		}
		.mon{
			font-weight: bold;
			background-color: #EFFBFF;
			width:8%;
		}
		.a{
			width: 12%;
		}
		.b{
			width: 12%;
			padding-left: 0.2%;
		}
		.h{
			width: 8%;
		}
		.j{
			width: 8%;
		}
		.tut{
			width:32%;
			padding-left: 0.55%;
		}
		.m{
			width: 12.5%;
		}
		.tuesday{
			float: left;
			text-align: center;
			height: 20%;
			border:1px solid black;
			padding-top: 20px;
			padding-bottom: 20px;
		}
		.tue{
			font-weight: bold;
			background-color: #EFFBFF;
			width: 8%;
		}
		.c{
			width:8%;
		}
		.d{
			width: 8%;
		}
		.e{
			width: 8%;
		}
		.f{
			width: 8%;
		}
		.k{
			width: 8%;
		}
		.l{
			width: 8%;
		}
		.empty{
			width: 8%;
			padding-top: 20px;
			padding-bottom: 20px;

		}
	</style>
	<script type="text/javascript" src="jquery.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		var obj = <?php echo json_encode($arr); ?>;
		var courses=[];
		var slots = [];
		for (var i=0;i<obj.length;i++){
			courses.push(obj[i]['courseid']);
			slots.push(obj[i]['slotid']);
		}
		$(".course").each(function(){
			$(this).html("....");
		});
		var colors = ["#A2BFF4","#99FF66","#CCFFFF","#FFC299","#6699FF","#3399FF","#00BFFF","#7CFC00","#F5FFFA","#5C85AD","#DEE7EF"];
		//var slots=['A','C','D'];
		//var courses = ['CSL102','CSL103','AML120'];

		for (var i=0; i<slots.length; i++){
			var slot = slots[i].toLowerCase();
			$("."+slot).each(function(){
				$(this).html(courses[i]);
				$(this).css('background-color',colors[i]);
			});
		}
	});
	</script>
</head>
<body>
	<div class="container">
	<h2>
		Make your Timetable
	</h2>
	<ul class="pager">
  			<li class="previous"><a href="course_done.php">&larr; Back</a></li>
	</ul>
		<div class="timetable">
			<div class="time-row">
				<div class="time heading">Time</div>
				<div class="time">08:00 - 09:00</div>
				<div class="time">09:00 - 10:00</div>
				<div class="time">10:00 - 11:00</div>
				<div class="time">11:00 - 12:00</div>
				<div class="time">12:00 - 13:00</div>
				<div class="time">13:00 - 14:00</div>
				<div class="time">14:00 - 15:00</div>
				<div class="time">15:00 - 16:00</div>
				<div class="time">16:00 - 17:00</div>
				<div class="time">17:00 - 18:00</div>
				<div class="time last">18:00 - 19:00</div>
			</div>
			<div style="clear:both;"></div>
			<div class="monday-row">
				<div class="mon monday">Mon</div>
				<div class="a monday course"></div>
				<div class="b monday course"></div>
				<div class="h monday course"></div>
				<div class="j monday course"></div>
				<div class="tut monday course"></div>
				<div class="m monday course"></div>
			</div>
			<div style="clear:both;"></div>
			<div class="tuesday-row">
				<div class="tue tuesday">Tue</div>
				<div class="c tuesday course"></div>
				<div class="d tuesday course"></div>
				<div class="e tuesday course"></div>
				<div class="f tuesday course"></div>
				<div class="j tuesday course"></div>
				<div class="tut tuesday course"></div>
				<div class="k tuesday course"></div>
				<div class="l tuesday course"></div>
			</div>
			<div style="clear:both;"></div>
			<div class="tuesday-row">
				<div class="tue tuesday">Wed</div>
				<div class="c tuesday course"></div>
				<div class="d tuesday course"></div>
				<div class="e tuesday course"></div>
				<div class="h tuesday course"></div>
				<div class="j tuesday course"></div>
				<div class="tut tuesday course"></div>
				<div class="k tuesday course"></div>
				<div class="l tuesday course"></div>
			</div>
			<div style="clear:both;"></div>
			<div class="monday-row">
				<div class="mon monday">Thu</div>
				<div class="a monday course"></div>
				<div class="b monday course"></div>
				<div class="f monday course"></div>
				<div class="h monday course"></div>
				<div class="tut monday course"></div>
				<div class="m monday course"></div>
			</div>
			<div style="clear:both;"></div>
			<div class="tuesday-row">
				<div class="tue tuesday">Fri</div>
				<div class="c tuesday course"></div>
				<div class="d tuesday course"></div>
				<div class="e tuesday course"></div>
				<div class="f tuesday course"></div>
				<div class="empty tuesday course"></div>
				<div class="tut tuesday course"></div>
				<div class="k tuesday course"></div>
				<div class="l tuesday course"></div>
			</div>
			<div style="clear:both;"></div>
		</div>
		<br>
		<div class="clashes">
		<?php
				for ($i=0;$i<sizeof($arr);$i++)
					for ($j=$i+1; $j<sizeof($arr); $j++)
						if($arr[$i]['slotid']==$arr[$j]['slotid'])
							echo "<h4>Course ".$arr[$i]['courseid']." clashes with ".$arr[$j]['courseid']." Slot ".$arr[$i]['slotid']."</h4>";
	
		?>
		</div>
	</div>
</body>
</html>