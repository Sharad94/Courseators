<?php

$courselist=false;
include('functions.php');
if (!isset($_SESSION['username'])){
  header("Location: index.php");
  session_destroy();  
}

if (isset($_GET['submit'])){
  	$courseid = trim($_GET['name']);
    $courseid = strtoupper($courseid);
    if (checkcourseall($courseid)){
      $courselist = getcourseinfo($courseid);
      $prereqlist = getprereq($courseid);
      $overlaplist = getoverlap($courseid);
      if (!$courselist){
        //print error course not valid.
      }
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
                $(document).ready(function(){
                    $("#name").autocomplete({
                        source:'getautocomplete.php',
                        minLength:2
                    });
                    $(".advanced").click(function(){
                      $(".advanced-div").toggle();
                    });

                    /*$(".panel-heading").click(function(){
                      var id = $(this).attr('id');
                      console.log(id+'-body');
                      $('#'+id+'-body').toggle("slow");
                    });*/
                    $("#prof").change(function(){
                      var profid=getSelectedText('prof');
                      if (profid!=null){
                        var obj = <?php echo json_encode($courselist); ?>;
                        var id_of_item_to_approve = profid;
                        var data=[];
                        data.push(id_of_item_to_approve);
                        data.push(obj["courseid"]);
                        $.ajax({
                            url: "getprofreview.php", //This is the page where you will handle your SQL insert
                            type: "POST",
                            data: {data:data}, //The data your sending to some-page.php
                            success: function(data){
                              console.log(data);
                                var data = JSON.parse(data);
                                var attendance = 0;
                                var rating = 0;
                                var grading = 0;
                                //$("#comments").innerHTML="";
                                for (var i=0 ; i < data.length; i++){
                                  var comment = data[i]["comments"];
                                  attendance += parseInt(data[i]["attpolicy"]);
                                  rating += parseInt(data[i]["rating"]);
                                  grading += parseInt(data[i]["grading"]);

                                  $("#comments").append("<span>"+comment+"</span><br>");
                                }
                                attendance = attendance/data.length;
                                rating = rating/data.length;
                                grading = grading/data.length;

                                $("#attendance").text(attendance+"/10");
                                $("#grading").text(grading+"/10");
                                $("#rating").text(rating+"/10");
                            },
                            error:function(){
                                console.log("AJAX request was a failure");
                            }     
                        });
                      }
                    });
                });

                function getSelectedText(elementId) {
                  var elt = document.getElementById(elementId);
                  if (elt.selectedIndex == -1)
                      return null;
                  return elt.options[elt.selectedIndex].value;
                }

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
          /*.panel-body{
            display: none;

          }*/
          .panel-default{

            cursor: pointer;
          }
          #comments{
            height:20%;
            overflow-y:scroll;
          }

        </style>
	</head>
	<body>
  <div class = "container">
  	<div class="form" id="course_form">
        <form method="get" action="allcourses.php">
              <div class="row">
                <div class="col-lg-6">
                  <div class="input-group">
                    <input type="text" id="name" class="form-control" name="name" placeholder="Select Course" required="required">
                    <span class="input-group-btn">
                      <button id="submit" class="btn btn-default" name="submit" type="submit">Go!</button>

                    </span>
                  </div>
                </div>
              </div>
        </form>
  	</div>
    
    <br>
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

    <div class="panel panel-default" >
      <div class="panel-heading" id="panel-slide-2">
        <h3 class="panel-title">Review</h3>
      </div>
      <div class="panel-body" id="panel-slide-2-body">

        <form class="review-form" role="search">
          <div class="left-form">
            <div class="input-group">
              <span class="input-group-addon">Professor:</span>
              <select name="prof" class="form-control" id="prof">
                <?php
                  $proflist = get_profs_course($courseid);
                  if ($proflist){
                    for ($i=0;$i < sizeof($proflist);$i++){
                      echo "<option value='".$proflist[$i]['profid']."'>".$proflist[$i]['name']."</option>";
                    }
                  }
                ?>
              </select>
            </div>
            <br>
            <div class="input-group">
              <span class="input-group-addon">Attendance Policy:</span>
              <!--input type="range" style="margin-top:1.5%;" min="0" max="10" value="0" step="1" id="attendance"/-->
              <span class="input-group-addon" id="attendance"></span>
            </div>
            <!--span id="range-attendance"></span-->
            <br>
            <br>
            <div class="input-group">
              <span class="input-group-addon">Rating:</span>
              <!--input type="range" style="margin-top:1.5%;" min="0" max="5" value="0" step="1" id="rating"/-->
              <span class="input-group-addon" id="rating"></span>
            </div>
            <span id="range-rating"></span>
            <br>
            <br>
          </div>
          <div class="right-form">
            <div class="input-group">
              <span class="input-group-addon">Grading</span>
              <!--input type="range" style="margin-top:1.5%;" min="0" max="5" value="0" step="1" id="grading"/-->
              <span class="input-group-addon" id="grading"></span>
            </div>
            <br>
            <p> Comments:</p>
            <div id="comments">
              
            </div>

          </div>
        </form>
      </div>
    </div>
        <?php
      }
    ?>
  </div>
  </body>

<html>
