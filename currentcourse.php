<?php
$ret = array();
$courselist=false;
include('functions.php');
if (!isset($_SESSION['username'])){
  header("Location: index.php");
  session_destroy();  
}

if (isset($_GET['submit'])){
  	$courseid = trim($_GET['name']);
    $courseid = strtoupper($courseid);

    if (checkcourse($courseid)){
      $courselist = getcourseinfo($courseid);
      $prereqlist = getprereq($courseid);
      $overlaplist = getoverlap($courseid);
      if (!$courselist){
        //print error course not valid.
      }
    }

//#######################################################################################################
//#######################################################################################################
// AML170 pe fail krta hai.. ret is not defined!!!!!
//#######################################################################################################
//#######################################################################################################
    
}

if (isset($_POST['submit-advanced']))
  $ret=advancedsearch($_POST['courseid'], $_POST['coursename'], $_POST['slot'], $_POST['credits'], $_POST['prof']);


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
                  $('.add').click(function(){

                    var obj = <?php echo json_encode($courselist); ?>;
                    var id_of_item_to_approve = $(this).attr("id");
                    var data=[];
                    data.push(id_of_item_to_approve);
                    data.push(obj["courseid"]);
                    $.ajax({
                        url: "add.php", //This is the page where you will handle your SQL insert
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

                    $("#name").autocomplete({
                        source:'getcoursecomplete.php',
                        minLength:2
                    });
                    $(".advanced").click(function(){
                      $(".advanced-div").toggle();
                    });

                    $(".panel-default").click(function(){
                      var id = $(this).attr('id');
                      console.log(id+'-body');
                      $('#'+id+'-body').toggle("slow");
                    });
                    $(".addysearch").click(function(){
                      var id = $(this).attr('id');
                      window.open(
			'?name='+id+'&submit=',
			'_blank'
		      );
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


                
        </script>
        <style type="text/css">
          a{
            text-decoration: underline;
          }
          a:link {
            color:#62B1FF;
            }      /* unvisited link */
          a:visited {
            color:#336699;
            }  /* visited link */
          a:hover {
            color:#5C8AE6;
            }  /* mouse over link */
          a:active {
            color:red;
            }  /* selected link */
          /*.info{
            float: left;
          }
          #panel-heading-info{
            height:17%;
          }
          .bookmark{
            float: right;
          }*/
          .advanced-div{
            display: none;
          }
          .panel-body{
            display: none;

          }
          .panel-default{

            cursor: pointer;
          }

        </style>
	</head>
	<body>
  <div class = "container">
  	<div class="form" id="course_form">
        <form method="get" action="currentcourse.php">
              <div class="row">
                <div class="col-lg-10">
                  <div class="input-group">
                    <input type="text" id="name" class="form-control" name="name" placeholder="Select Course" required="required">
                    <span class="input-group-btn">
                      <button id="submit" class="btn btn-default" name="submit" type="submit">Go!</button>
                      <button type="button" class="btn btn-default advanced">Advanced Search</button> 
                    </span>
                    <span class="input-group-btn" style="padding-left:2%;">
                    <?php
                      if (isset($_SESSION['username']) && isset($_GET['submit']) && $courselist){
                        if (checkcourseexist('tentativecourses',$courselist['courseid'],$_SESSION['username'])) {
                          echo '<button type="button" id="tentative" class="btn btn-default add" disabled>Tentatived</button>';
                        }
                        else {
                         echo '<button type="button" id="tentative" class="btn btn-default add">Add to Tentative</button>'; 
                        }
                        if (checkcourseexist('bookmarks',$courselist['courseid'],$_SESSION['username'])) {
                          echo '<button type="button" id="bookmark" class="btn btn-default add" disabled>Bookmarked</button>';
                        }
                        else {
                         echo '<button type="button" id="bookmark" class="btn btn-default add">Bookmark</button>'; 
                        }
                      }
                    ?>
                    </span>
                      <!--button type="button" id="tentative" class="btn btn-default add">Add To Tentative</button-->
                      
                    
                  </div>
                </div>
              </div>
        </form>
  	</div>
    <br>
    <div class="advanced-div">
      <form action="currentcourse.php" method="post" class="panel-form" style="width:100%;">
        <div class="panel-form-advanced-left" style="width:45%;float:left;">
          <div class="input-group">
                <span class="input-group-addon">ID:</span>
                <input type="text" id="courseid-search" class="form-control" name="courseid" placeholder="Course ID">
          </div><br>
          <div class="input-group">
                <span class="input-group-addon">Name:</span>
                <input type="text" id="coursename-search" class="form-control" name="coursename" placeholder="Course Name">
          </div><br>
          <div class="input-group">
                <span class="input-group-addon">Slot:</span>
                <input type="text" id="slot-search" class="form-control" name="slot" placeholder="Add Slot">
          </div><br>
        </div>
        <div class="panel-form-advanced-right" style="width:45%;float:right;">
          <div class="input-group">
                <span class="input-group-addon">Credits:</span>
                <input type="text" id="credits-search" class="form-control" name="credits" placeholder="Credits">
          </div><br>
          <div class="input-group">
                <span class="input-group-addon">Professor:</span>
                <input type="text" id="prof-search" class="form-control" name="prof" placeholder="Professor">
          </div><br>
          <button type="submit" name="submit-advanced" class="btn btn-default submit-advanced">Search</button>
        </div>
      </form>
    </div>
    <div style="clear:both;"></div>
    <?php 
      if ($courselist) {
    ?>
        <div class="panel panel-default">
          <div class="panel-heading" id="panel-heading-info">
            <div class="info">
              <h3 class="panel-title">
                <?php
                  echo printarr($courselist,'courseid'); 
                  echo " ";
                  echo printarr($courselist,'name');
                ?>
              </h3>
              <br>
              <h4 class="panel-title">
                <?php 
                  echo printarr($courselist,'credits')." credits "; 
                  echo " "; 
                  echo printarr($courselist,'ltp');
                ?>
              </h4>
              <p class="panel-title">
                <?php
                  if ($prereqlist){
                    echo "<strong>Pre-requisites : </strong>";
                    for ($i=0;$i<sizeof($prereqlist);$i++){
                      echo $prereqlist[$i]['prereq'].",";
                    }
                  }
                  else {
                    echo "<strong>Pre-requisites:</strong> None";
                  }
                  
                ?>
              </p>
              <p class="panel-title">
                <?php
                  if ($overlaplist){
                    echo "<strong>Overlaps with : </strong>";
                    for ($i=0;$i<sizeof($overlaplist);$i++){
                      echo "<a href='allcourses.php?name=".$overlaplist[$i]['courseid2']."&submit='>".$overlaplist[$i]['courseid2']."</a> ";
                    }
                  }
                  else {
                    echo "Overlaps with: None";
                  }
                  
                ?>
              </p>
            </div>
            <!--div class="bookmark">
              Bookmark
            </div>
            <div style="clear:both;"></div-->
          </div>
          <div class="panel-body" style="display:block;">
            <?php
              echo printarr($courselist,'description');
            ?>
          </div>
        </div>

    <!--div class="panel panel-default" id="panel-slide-2">
      <div class="panel-heading">
        <h3 class="panel-title">Review</h3>
      </div>
      <div class="panel-body" id="panel-slide-2-body">

        <form class="review-form" role="search">
          <div class="left-form">
            <div class="input-group">
              <span class="input-group-addon">Professor:</span>
              <input type="text" id="prof-search" class="form-control" placeholder="To Review">
            </div>
            <br>
            <div class="input-group">
              <span class="input-group-addon">Attendance Policy:</span>
              <input type="range" style="margin-top:1.5%;" min="0" max="10" value="0" step="1" onchange="showValue(this.value,'range-attendance')"/>
            </div>
            <span id="range-attendance"></span>
            <br>
            <br>
            <div class="input-group">
              <span class="input-group-addon">Rate the Course:</span>
              <input type="range" style="margin-top:1.5%;" min="0" max="5" value="0" step="1" onchange="showValue(this.value)"/>
            </div>
            <span id="range-rating"></span>
            <br>
            <br>
          </div>
          <div class="right-form">
            <p> Comments:</p>
            <textarea class="form-control" rows="3"></textarea>
          </div>
        </form>
      </div>
    </div-->
        <?php
      }
    ?>
	<?php
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

<html>
