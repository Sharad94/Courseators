<?php
include('header.php');

function authenticate($username,$password){
	$arr = getuser($username);

	if ($arr){
		$row=pg_fetch_assoc($arr);
		//var_dump($row);
		if ($row['password'] === $password){
			return true;
		}
		return false;
	}
	return false;	
}

// create table questions (questionid serial primary key, question text not null, questiontime text not null);

// create table asks (questionid serial primary key references questions(questionid), courseid varchar(6) references course(courseid), studentid text references student(studentid));

//create table answers (upvotes int not null, downvotes int not null, questionid int references questions(questionid), answer text not null, studentid text references student(studentid), primary key (questionid, studentid));

function ask_question($username,$ques,$time,$course)
{	
	
	if(checkcourse($course)){
		$arr=pg_query("insert into questions (question, questiontime) values ('".pg_escape_string($ques)."','".$time."')");
 		//echo "insert into questions (question, questiontime) values ('".$ques."','".$time."')";
 
		$arr1=pg_query("insert into asks (courseid, studentid) values ('".strtoupper($course)."','".strtoupper($username)."')");
 		//echo "insert into asks (courseid, studentid) values ('".strtoupper($course)."','".strtoupper($username)."')";
 
		//echo "select questionid from questions where question = '".$ques."' and questiontime = '".$time."'";
		 //$qid = pg_query("select questionid from questions where question = '".$ques."' and questiontime = '".$time."'");
 ////echo "select questionid from questions where question = '".$ques."' and questiontime = '".$time."'";
 		
		//$qid = pg_fetch_assoc($qid);
		//echo "insert into answers (upvotes,downvotes,questionid) values (0,0,".$qid['questionid'].")";
		 //$arr2=pg_query("insert into answers (upvotes,downvotes,questionid) values (0,0,".$qid['questionid'].")");
 ////echo "insert into answers (upvotes,downvotes,questionid) values (0,0,".$qid['questionid'].")";
 
//check whether insertion was successful or not
	
		if ( (!$arr) || (!$arr1))
		{
			return false;
		}
		else 
			return true;
	}
	else {
		return false;
	}
}



function suggest($dropdown)
{
	$username = $_SESSION['username'];
	$depid = get_depid();
	if($depid)
		$depid = $depid[0]['depid'];
	//$depid = substr($username, 4, 3);

	if($dropdown == 1){
		$db = "(select * from tentativecourses where courseid not in (select courseid from tentativecourses where studentid='".$username."')) as db";
		$query = "select courseid from ".$db." group by courseid order by count(studentid) desc";
	}
	
	if($dropdown == 2){
		
		$db = "(select * from tentativecourses where courseid not in (select courseid from tentativecourses where studentid='".$username."')) as db";
		$query = "select temp.courseid from (select * from ".$db." where depid = '".$depid."') as temp group by temp.courseid order by count(temp.studentid) desc";
	}

	if($dropdown == 3){
		$view = "dep_".$depid;
		$db = "(select donecourses.studentid, donecourses.courseid from ".$view." as donecourses where courseid not in (select courseid from tentativecourses where studentid='".$username."')) as db";
		$query = "select courseid from ".$db." group by courseid order by count(studentid) desc";

	}
	
	if($dropdown == 4){
		$db = "(select * from tentativecourses where courseid not in (select courseid from tentativecourses where studentid='".$username."') and studentid in ((select studentid1 as student from friends where studentid2='".$username."') union (select studentid2 as studentid from friends where studentid1='".$username."'))) as db";
		$query = "select courseid from ".$db." group by courseid order by count(studentid) desc";
	}
	
	if($dropdown == 5){
		$db = "(select donecourses.courseid, donecourses.studentid from donecourses where donecourses.courseid not in (select courseid from tentativecourses where studentid='".$username."') and donecourses.studentid in ((select studentid1 as student from friends where studentid2='".$username."') union (select studentid2 as studentid from friends where studentid1='".$username."'))) as db";
		$query = "select courseid from ".$db." group by courseid order by count(studentid) desc";	
	}
	
	$arr = pg_query($query) or die('Error: ' . pg_last_error());
 
 

	if (pg_num_rows($arr)==0){
		return false;
	}
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}
	return $ret;
}






function checkcourse($course){
	$arr = pg_query("select * from coursesem where courseid = '".strtoupper($course)."' and year = ".$_SESSION['year']." and semnumber = ".$_SESSION['sem']);
	//echo "select * from coursesem where courseid = '".strtoupper($course)."' and year = ".$_SESSION['year']." and semnumber = ".$_SESSION['sem'];
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	return true;
}

function checkcourseall($course){
	$arr = pg_query("select * from course where courseid = '".strtoupper($course)."'");
 	//echo "select * from coursesem where courseid = '".strtoupper($course)."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	return true;
}

//Return all courses done by a student ordered by year and sem number
function all_courses($username){
	////echo 'select * from donecourse where studentid = "'.$username.'" order by year,semnumber ASC';
	$arr = pg_query("select * from donecourses where studentid = '".$username."' order by year,semnumber ASC");
 	//echo "select * from donecourses where studentid = '".$username."' order by year,semnumber ASC";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}
	return $ret;
}
function get_unanswered_questions($courseid){
	$arr = pg_query("select asks.questionid from asks where courseid='".$courseid."' except (select answers.questionid from answers)");
	if (pg_num_rows($arr)==0){
		return false;
	}
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}
	return $ret;
}
//create table answers (upvotes int not null, downvotes int not null, questionid int references questions(questionid), answer text not null, studentid text references student(studentid), primary key (questionid, studentid));
//create table asks (questionid serial primary key references questions(questionid), courseid varchar(6) references course(courseid), studentid text references student(studentid));
//Get the top 10 answers of the given course
function get_bestans($courseid)
{
	////echo "select answers.questionid from answers inner join asks on answers.questionid = asks.questionid where courseid = '".$courseid."' order by answers.upvotes limit 10";
	// $arr = pg_query("select questions.questionid from questions inner join asks on questions.questionid = asks.questionid where courseid = '".$courseid."' order by answers.upvotes limit 10");
 ////echo "select questions.questionid from questions inner join asks on questions.questionid = asks.questionid where courseid = '".$courseid."' order by answers.upvotes limit 10";
 	
	//$arr = pg_query("select questionid from questions");
	//$temp = pg_query("select asks.questionid from asks where asks.courseid=".$courseid."");
	//echo "create view  as select * from (select questionid,studentid,max(upvotes - downvotes) as difference from answers group by questionid,studentid) as temp";
	pg_query("create view helper as select * from (select asks.questionid,asks.courseid,temp.studentid,temp.difference from asks,(select questionid,studentid,max(upvotes - downvotes) as difference from answers group by questionid,studentid) as temp where temp.questionid=asks.questionid) as temp1 where temp1.courseid='".$courseid."'");

	$arr = pg_query("select distinct answers.questionid from answers,(select helper.questionid, helper.studentid from helper,(select questionid, max(difference) from helper group by questionid) as temp where helper.difference = temp.max order by temp.max) as temp2 where temp2.questionid=answers.questionid and temp2.studentid=answers.studentid");
	//$arr = pg_query("select distinct answers.questionid from answers,(select helper.questionid, helper.studentid,temp.max as maxer from helper,(select questionid, max(difference) from helper group by questionid) as temp where helper.difference = temp.max) as temp2 where temp2.questionid=answers.questionid and temp2.studentid=answers.studentid order by temp2.maxer");
	pg_query("drop view helper");
	//$arr = pg_query("select answers.questionid from answers,(select helper.questionid, helper.studentid from (select questionid, max(difference) from helper group by questionid) as temp,(select * from (select questionid,studentid,max(upvotes - downvotes) as difference from answers group by questionid,studentid) as motu) as helper where helper.difference = temp.max) as temp2 where temp2.questionid=answers.questionid and temp2.studentid=answers.studentid");
	//echo "select baaga.questionid,upvotes-downvotes as votes from asks,(select * from (with temp as (select upvotes,downvotes,questionid,answer,studentid,upvotes - downvotes as difference from answers) select * from temp,(select max(temp.difference) as max from temp) as t where temp.difference = t.max) as baaga1) as baaga where asks.questionid= baaga.questionid and asks.courseid='".$courseid."' order by votes";
 	//echo "select questionid from questions";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}
	return $ret;
}


function add_ans($studentid,$qid,$ans)
{

	////echo "insert into answers(upvotes,downvotes,questionid,answer,studentid) values (0,0,".$qid.",'".pg_escape_string($ans)."','".$studentid."' )";
	$arr = pg_query("insert into answers(upvotes,downvotes,questionid,answer,studentid) values (0,0,".$qid.",'".pg_escape_string($ans)."','".$studentid."' )");
 	//echo "insert into answers(upvotes,downvotes,questionid,answer,studentid) values (0,0,".$qid.",'".pg_escape_string($ans)."','".$studentid."' )";
 
	return $arr;
}

//Returns all the answers of a question ordered by upvotes
function get_allans($questionid)
{
	$arr = pg_query("select * from answers where questionid = ".pg_escape_string($questionid)." order by (upvotes - downvotes) DESC");
 	//echo "select * from answers where questionid = ".pg_escape_string($questionid)." order by upvotes DESC";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}
	return $ret;	
}

function get_course_from_ques($qid)
{
	$arr = pg_query("select courseid from asks where questionid = ".$qid."");
 	//echo "select studentid from asks where questionid = '".$qid."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	else
	$q =  pg_fetch_assoc($arr);
	return $q['courseid'];	
}

function get_student($qid)
{
	$arr = pg_query("select studentid from asks where questionid = ".$qid."");
 	//echo "select studentid from asks where questionid = '".$qid."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	else
	$q =  pg_fetch_assoc($arr);
	return $q['studentid'];
}

//Return the no of upvotes to an answers of the given student and the given question
function get_upvote($studentid,$questionid)
{
	//echo "select upvotes from answers where studentid = '".$studentid."' and questionid = ".$questionid."";
	$arr = pg_query("select upvotes from answers where studentid = '".$studentid."' and questionid = ".$questionid."");
 //echo "select upvotes from answers where studentid = '".$studentid."' and questionid = ".$questionid."";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	else 
		$ans = pg_fetch_assoc($arr);
		return $ans['upvotes'];
}

//Upvotes the count of answer
function upvote($studentid,$questionid)
{
	////echo "update answers set upvotes = upvotes +1 where studentid = '".$studentid."' and questionid = ".$questionid."";
	$already_downvoted = pg_query("select * from checkdownvotes where studentid1 ='".$studentid."' and studentid2 = '".$_SESSION['username']."' and questionid = ".$questionid."");
	if (pg_num_rows($already_downvoted) != 0)
	{
		//echo "aaya";
		$arr = pg_query("update answers set downvotes = downvotes - 1 where studentid = '".$studentid."' and questionid = ".$questionid."");
		$delete = pg_query("delete from checkdownvotes where studentid1 ='".$studentid."' and studentid2 = '".$_SESSION['username']."' and questionid = ".$questionid."");
	}
	else
	{
		//echo "aaya11111111";
		$already_upvoted = pg_query("select * from checkupvotes where studentid1 ='".$studentid."' and studentid2 = '".$_SESSION['username']."' and questionid = ".$questionid." ");
		if (pg_num_rows($already_upvoted) != 0)
		{
			return false;
		}
		$arr = pg_query("update answers set upvotes = upvotes +1 where studentid = '".$studentid."' and questionid = ".$questionid."");
		$arr1 = pg_query(" insert into checkupvotes values('".$studentid."','".$_SESSION['username']."', $questionid) ");
	}
 //echo "update answers set upvotes = upvotes +1 where studentid = '".$studentid."' and questionid = ".$questionid."";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	else return true;
}
//Get no of downvotes on a given answer
function get_downvote($studentid,$questionid)
{
	//echo "select downvotes from answers where studentid = '".$studentid."' and questionid = ".$questionid."";
	$arr = pg_query("select downvotes from answers where studentid = '".$studentid."' and questionid = ".$questionid."");
 //echo "select downvotes from answers where studentid = '".$studentid."' and questionid = ".$questionid."";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	else 
		$ans = pg_fetch_assoc($arr);
		return $ans['downvotes'];
}

//Upvotes the count of answer
function downvote($studentid,$questionid)
{
	$already_upvoted = pg_query("select * from checkupvotes where studentid1 ='".$studentid."' and studentid2 = '".$_SESSION['username']."' and questionid = ".$questionid."");
	if (pg_num_rows($already_upvoted) != 0)
	{
		$arr = pg_query("update answers set upvotes = upvotes - 1 where studentid = '".$studentid."' and questionid = ".$questionid."");
		$delete = pg_query("delete from checkupvotes where studentid1 ='".$studentid."' and studentid2 = '".$_SESSION['username']."' and questionid = ".$questionid."");
	}
	else{

	$already_downvoted = pg_query("select * from checkdownvotes where studentid1 ='".$studentid."' and studentid2 = '".$_SESSION['username']."' and questionid = ".$questionid." ");
	if (pg_num_rows($already_downvoted) != 0)
	{
		return false;	
	}
	////echo "update answers set downvotes = downvotes +1 where studentid = '".$studentid."' and questionid = ".$questionid."";
	$arr = pg_query("update answers set downvotes = downvotes +1 where studentid = '".$studentid."' and questionid = ".$questionid."");
	$add = pg_query(" insert into checkdownvotes values('".$studentid."','".$_SESSION['username']."', $questionid) ");
 //echo "update answers set downvotes = downvotes +1 where studentid = '".$studentid."' and questionid = ".$questionid."";
 	}
	if (pg_num_rows($arr)==0){
		return false;
	}
	else return true;
}


//Return the entire info of the topmost ans in terms of no of upvotes given a question
function get_topans($qid)
{
	//create table answers (upvotes int not null, downvotes int not null, questionid int references questions(questionid) on delete cascade on update cascade, answer text not null, studentid text references student(studentid) on delete cascade on update cascade, primary key (questionid, studentid));
	//echo ("with temp as (select upvotes,downvotes,questionid,answer,studentid,upvotes - downvotes as difference from answers where questionid=".$qid.") select * from temp,(select max(temp.difference) as max from temp) as t where temp.difference = t.max");
	// //echo "select * from answers,(select max(upvotes) as match from answers where questionid = '".$qid."') as t where questionid = '".$qid."' and  answers.upvotes = t.match";
	//$arr = pg_query("select * from answers,(select max(upvotes) as match from answers where questionid = '".$qid."') as t where questionid = '".$qid."' and  answers.upvotes = t.match");
	$arr = pg_query("with temp as (select upvotes,downvotes,questionid,answer,studentid,upvotes - downvotes as difference from answers where questionid=".$qid.") select * from temp,(select max(temp.difference) as max from temp) as t where temp.difference = t.max");
	//echo "with temp as (select upvotes,downvotes,questionid,answer,studentid,upvotes - downvotes as difference from answers where questionid='".$qid."') select * from temp,(select max(temp.difference) as max from temp) as t where temp.difference = t.max";
 //echo "select * from answers,(select max(upvotes) as match from answers where questionid = '".$qid."') as t where questionid = '".$qid."' and  answers.upvotes = t.match";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	else
	$a = pg_fetch_assoc($arr);
	return $a;
}

function get_ques($qid)
{
	////echo "select * from questions where questionid = '".$qid."'";
	$arr = pg_query("select * from questions where questionid = '".$qid."'");
 //echo "select * from questions where questionid = '".$qid."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	else
	$q =  pg_fetch_assoc($arr);
	return $q['question'];
}

function get_ques_info($qid)
{
	////echo "select * from questions where questionid = '".$qid."'";
	$arr = pg_query("select * from asks where questionid = '".$qid."'");
 //echo "select * from questions where questionid = '".$qid."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	else
	$q =  pg_fetch_assoc($arr);
	return $q;
}


function get_all_donecourses_name($username){

	$arr = pg_query("select donecourses.courseid,course.name,donecourses.semnumber,donecourses.year from donecourses inner join course on donecourses.courseid=course.courseid where donecourses.studentid = '".$username."' order by donecourses.year,donecourses.semnumber ASC");
 //echo "select donecourses.courseid,course.name,donecourses.semnumber,donecourses.year from donecourses inner join course on donecourses.courseid=course.courseid where donecourses.studentid = '".$username."' order by donecourses.year,donecourses.semnumber ASC";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}
	return $ret;
}

function get_all_tentativecourses_name($username){

	$arr = pg_query("select tentativecourses.courseid,course.name from tentativecourses inner join course on tentativecourses.courseid=course.courseid where tentativecourses.studentid = '".$username."'");
 //echo "select donecourses.courseid,course.name,donecourses.semnumber,donecourses.year from donecourses inner join course on donecourses.courseid=course.courseid where donecourses.studentid = '".$username."' order by donecourses.year,donecourses.semnumber ASC";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}
	return $ret;
}

//Returns the semester in which the student completed that course
function get_sem($username,$courseid)
{
	////echo "select year,semnumber from donecourses where studentid = '".$username."' and courseid = '".$courseid."'";
	$arr = pg_query("select year,semnumber from donecourses where studentid = '".$username."' and courseid = '".$courseid."'");
 //echo "select year,semnumber from donecourses where studentid = '".$username."' and courseid = '".$courseid."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}

	return $arr;
}
//Populate reviews
//create table reviews (reviewid serial primary key, profid text, attpolicy int, rating int, grading int, comments text);
function add_reviews($username,$courseid,$attpolicy,$rating,$grading,$comments){
	////echo "baga";
	$seminfo = pg_fetch_assoc(get_sem($username,$courseid));
	$semno = $seminfo['semnumber'];
	$semyear = $seminfo['year'];
	$profid = pg_fetch_assoc(get_prof_sem($courseid,$semyear,$semno));
	////echo "$profid";

	$arr1 = pg_query("insert into reviews(profid,attpolicy,rating,grading,comments) values ('".$profid['profid']."',".$attpolicy.",".$rating.",".$grading.",'".pg_escape_string($comments)."')");
 //echo "insert into reviews(profid,attpolicy,rating,grading,comments) values ('".$profid['profid']."',".$attpolicy.",".$rating.",".$grading.",'".$comments."')";
 
	$reviewid = pg_query("select reviewid from reviews where profid = '".$profid['profid']."' and attpolicy = ".$attpolicy." and rating = ".$rating." and grading = ".$grading." and comments = '".pg_escape_string($comments)."'");
 //echo "select reviewid from reviews where profid = '".$profid['profid']."' and attpolicy = ".$attpolicy." and rating = ".$rating." and grading = ".$grading." and comments = '".$comments."'";
 
	$reviewid = pg_fetch_assoc($reviewid);
	////echo "update donecourses set reviewid = ".$reviewid['reviewid']." where studentid = '".$username."' and courseid = '".$courseid."'";
	$arr2 = pg_query("update donecourses set reviewid = ".$reviewid['reviewid']." where studentid = '".$username."' and courseid = '".$courseid."'");
 //echo "update donecourses set reviewid = ".$reviewid['reviewid']." where studentid = '".$username."' and courseid = '".$courseid."'";
 

	return $arr1;
	
}

function get_review($username,$courseid){
	////echo $courseid;
	$seminfo = pg_fetch_assoc(get_sem($username,$courseid));
	$semno = $seminfo['semnumber'];
	$semyear = $seminfo['year'];
	$profid = pg_fetch_assoc(get_prof_sem($courseid,$semyear,$semno));

	$reviewid = pg_query("select reviewid from donecourses where studentid = '".$username."' and courseid = '".$courseid."'");
 //echo "select reviewid from donecourses where studentid = '".$username."' and courseid = '".$courseid."'";
 	
	$reviewid = pg_fetch_assoc($reviewid);

	////echo "select * from reviews where reviewid = ".$reviewid['reviewid'];
	$arr = pg_query("select * from reviews where reviewid = ".$reviewid['reviewid']);
 //echo "select * from reviews where reviewid = ".$reviewid['reviewid'];
 
	$arr = pg_fetch_assoc($arr);
	return $arr;
	
}

function isdone_review($username,$courseid){
	$seminfo = get_sem($username,$courseid);
	if (!$seminfo)
	{
		return false;
	}
	$seminfo = pg_fetch_assoc($seminfo);
	$semno = $seminfo['semnumber'];
	$semyear = $seminfo['year'];
	if (get_prof_sem($courseid,$semyear,$semno))
		$profid = pg_fetch_assoc(get_prof_sem($courseid,$semyear,$semno));
	//echo "select reviewid from donecourses where studentid = '".$username."' and courseid = '".$courseid."'";
	$reviewid = pg_query("select reviewid from donecourses where studentid = '".$username."' and courseid = '".$courseid."'");
 //echo "select reviewid from donecourses where studentid = '".$username."' and courseid = '".$courseid."'";
 
	$helper = pg_fetch_assoc($reviewid);
	$helper = $helper['reviewid'];
	$reviewid = $helper;
	if (strlen($reviewid)==0){
		return False;
	}
	else return True;
}

function advancedsearch($courseid, $coursename, $slot, $credits, $prof){
  $query = "select distinct course.courseid from coursesem
		inner join course on coursesem.courseid = course.courseid 
		inner join prof on coursesem.profid=prof.profid 
		inner join slot on slot.slotid=coursesem.slotid where coursesem.year = ".$_SESSION['year']." and coursesem.semnumber = ".$_SESSION['sem']." and ";

  if (strlen($courseid))
	$query = $query."lower(course.courseid) like '%".strtolower($courseid)."%' and ";

  if (strlen($coursename))
	$query = $query."lower(course.name) like '%".strtolower($coursename)."%' and ";
	

  if (strlen($slot))
	$query = $query."lower(coursesem.slotid)='".strtolower($slot)."' and ";

  if (strlen($credits))
	$query = $query."course.credits=".$credits." and ";

  if (strlen($prof))
	$query = $query."lower(prof.name) like '%".strtolower($prof)."%' and ";
	
 

	$query = $query."1=1";
	////echo $query;	
	$arr = pg_query($query);
 //echo $query;
 
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}

  return $ret;
}


function del_courses($username, $courseid)
{
	////echo "delete from donecourses where courseid='".$courseid."'";
	$arr=pg_query("delete from donecourses where courseid='".strtoupper($courseid)."'");
 //echo "delete from donecourses where courseid='".strtoupper($courseid)."'";
 
	return $arr;
}

function add_courses($username,$sem_no,$year){
	$sem_no = $sem_no +1;
	$arr=pg_query("select courseid from donecourses where studentid='".$username."' and semnumber=".$sem_no." and year=".$year) or die('Error: ' . pg_last_error());
 //echo "select courseid from donecourses where studentid='".$username."' and semnumber=".$sem_no." and year=".$year;
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}
	return $ret;
}

function add_tentative($username,$course){
	//$depid = substr($username, 4, 3);
	$depid = get_depid();
	if($depid)
		$depid = $depid[0]['depid'];
	$arr=pg_query("insert into tentativecourses values ('".$username."','".strtoupper($course)."','".$depid."')") or die('Error: ' . pg_last_error());
 //echo "insert into tentativecourses values ('".$username."','".strtoupper($course)."','".$depid."')";
	if (!$arr){
		return false;
	}
	return true;
}

function add_bookmark($username,$course){
	$arr=pg_query("insert into bookmarks values ('".$username."','".strtoupper($course)."')") or die('Error: ' . pg_last_error());
 //echo "insert into bookmarks values ('".$username."','".strtoupper($course)."')";
 
	if (!$arr){
		return false;
	}
	return true;
}

function credit_tentative($username)
{
	$arr=pg_query("select courseid from tentativecourses where studentid='".$username."'");
 //echo "select courseid from tentativecourses where studentid='".$username."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	$credits = 0;
	while ($row = pg_fetch_assoc($arr)){
		$temp= pg_query("select credits from course where courseid='".$row['courseid']."'") or die('Error: ' . pg_last_error());
 //echo "select credits from course where courseid='".$row['courseid']."'";
 
		$temp1 = 	pg_fetch_assoc($temp);
		$temp1 = $temp1['credits'];
		$credits = $credits + $temp1;
		////echo $row['courseid'] ." , " . $credits;
		////echo '<br>';
	}
	return $credits;
}

function get_credit($course)
{
	$arr=pg_query("select credits from course where courseid='".strtoupper($course)."'") or die('Error: ' . pg_last_error());
 //echo "select credits from course where courseid='".strtoupper($course)."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	$helper = pg_fetch_assoc($arr);
	$helper = $helper['credits'];
	return $helper;
}

function get_coursename($course)
{
	$arr=pg_query("select name from course where courseid='".strtoupper($course)."'") or die('Error: ' . pg_last_error());
 //echo "select name from course where courseid='".strtoupper($course)."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}

	$helper = pg_fetch_assoc($arr);
	$helper = $helper['name'];
	return $helper;


}
//Gets the name of the professor of a given sem, course and year
function get_prof_name($course,$year,$semno)
{
	$arr=pg_query("select name from prof,(select profid from coursesem where courseid='".strtoupper($course)."' and year = ".$year." and semnumber = ".$semno.") as t where profid = t.profid") or die('Error: ' . pg_last_error());
 //echo "select name from prof,(select profid from coursesem where courseid='".strtoupper($course)."' and year = ".$year." and semnumber = ".$semno.") as t where profid = t.profid";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	return $arr;
}

//Gets the id of the professor of a given sem, course and year
function get_prof_sem($course,$year,$semno)
{
	////echo "select profid from coursesem where courseid='".strtoupper($course)."' and year = ".$year." and semnumber = ".$semno;
	$arr=pg_query("select profid from coursesem where courseid='".strtoupper($course)."' and year = ".$year." and semnumber = ".$semno) or die('Error: ' . pg_last_error());
 //echo "select profid from coursesem where courseid='".strtoupper($course)."' and year = ".$year." and semnumber = ".$semno;
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	return $arr;
}


function get_prof($course)
{
	return "prof_name";
	//return pg_fetch_assoc($arr)['prof'];
}



function delete_tentative($username,$course){
	$arr=pg_query("delete from tentativecourses values where studentid ='".$username."' and courseid = '".strtoupper($course)."'") or die('Error: ' . pg_last_error());
 //echo "delete from tentativecourses values where studentid ='".$username."' and courseid = '".strtoupper($course)."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
}

function check_prereq($username,$course){
	$arr=pg_query("select prereq from prereq where courseid1='".strtoupper($course)."'") or die('Error: ' . pg_last_error());
 //echo "select prereq from prereq where courseid1='".strtoupper($course)."'";
 
	while ($row = pg_fetch_assoc($arr)){
		$is_course = pg_query("select * from course where courseid='".strtoupper($row['prereq'])."'") or die('Error: ' . pg_last_error());
 //echo "select * from course where courseid='".strtoupper($row['prereq'])."'";
 
		if (pg_num_rows($is_course)>0){
			//Prereq is a course
			$done = pg_query("select * from donecourses where courseid='".strtoupper($row['prereq'])."'") or die('Error: ' . pg_last_error());
 //echo "select * from donecourses where courseid='".strtoupper($row['prereq'])."'";
 
			if (pg_num_rows($done)==0)
			{
				return $row['prereq'];
			}
		}
	}
	return "";
}

function check_overlap($username,$course){
	$arr=pg_query("select courseid2 from overlap where courseid1='".strtoupper($course)."'") or die('Error: ' . pg_last_error());
 //echo "select courseid2 from overlap where courseid1='".strtoupper($course)."'";
 
	while ($row = pg_fetch_assoc($arr)){
		$done = pg_query("select * from donecourses where courseid='".strtoupper($row['courseid2'])."'") or die('Error: ' . pg_last_error());
 //echo "select * from donecourses where courseid='".strtoupper($row['courseid2'])."'";
 
		if (pg_num_rows($done)>0)
		{
			return $row['courseid2'];
		}
	}
	return "";
}


function tentative_courses($username){
	$arr=pg_query("select temp.courseid,prof.name from prof,(select tentativecourses.courseid,coursesem.profid from tentativecourses inner join coursesem on tentativecourses.courseid = coursesem.courseid where tentativecourses.studentid='".$username."' and coursesem.year = ".$_SESSION['year']." and coursesem.semnumber=".$_SESSION['sem'].") as temp where temp.profid = prof.profid");
 	//echo "select courseid from tentativecourses where studentid='".$username."'";
	if (pg_num_rows($arr)==0){
		return false;
	}
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}
	return $ret;
}


function check_courses($username,$year, $sem_no, $course_id){
	  
	$sem_no = $sem_no +1;
	//echo "select * from coursesem where courseid='".trim($course_id)."' and year = ".$year." and semnumber = ".$sem_no." ";
	$is_floated = pg_query("select * from coursesem where courseid='".trim($course_id)."' and year = ".$year." and semnumber = ".$sem_no." ");
	if (pg_num_rows($is_floated) == 0)
	{
		return false;
	}
	$arr=pg_query("insert into donecourses (studentid, year, semnumber, courseid) 
	select '".$username."', ".$year.",".$sem_no.", '".$course_id."' where not exists (select * from donecourses where courseid = '".$course_id."' and studentid='".$username."')") or die('Error: ' . pg_last_error());
	//echo "insert into donecourses (studentid, year, semnumber, courseid) 
	//select '".$username."', ".$year.",".$sem_no.", '".$course_id."' where not exists (select * from donecourses where courseid = '".$course_id."' and studentid='".$username."')";
	if (!$arr){
		return false;
	}
	return true;
}


function getuser($username){
	$username = strtolower(trim($username));
	$arr=pg_query("(select student.studentid,student.password,student.name,student.img,student.email,student.advisor,student.telno,student.mobile,student.fax,student.address,student.homephone,student.roomaddr,student.postaladdr,student.url from student where lower(studentid)='".$username."') union (select prof.profid,prof.password,prof.name,prof.img,prof.email,prof.advisor,prof.telno,prof.mobile,prof.fax,prof.address,prof.homephone,prof.roomaddr,prof.postaladdr,prof.url from prof where lower(profid)='".$username."')") or die('Error: ' . pg_last_error());
 //echo "(select student.studentid,student.password,student.name,student.img,student.email,student.advisor,student.telno,student.mobile,student.fax,student.address,student.homephone,student.roomaddr,student.postaladdr,student.url from student where lower(studentid)='".$username."') union (select prof.profid,prof.password,prof.name,prof.img,prof.email,prof.advisor,prof.telno,prof.mobile,prof.fax,prof.address,prof.homephone,prof.roomaddr,prof.postaladdr,prof.url from prof where lower(profid)='".$username."')";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	return $arr;
}

function getprof($profid){
	$arr = pg_query("select name from prof where profid='".trim($profid)."'");
 	//echo "select name from prof where profid='".trim($profid)."'";
 
	return $arr;
}
function courses_done(){
	
}





function getcourseinfo($courseid){
	$arr = pg_query("select * from course where courseid='".$courseid."'");
 	//echo "select * from course where courseid='".$courseid."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	return pg_fetch_assoc($arr);
}

function getprereq($courseid){
	$arr = pg_query("select * from prereq where courseid1='".$courseid."'");
 	//echo "select * from prereq where courseid1='".$courseid."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}
	return $ret;
}

function getoverlap($courseid){
	$arr = pg_query("select * from overlap where courseid1='".$courseid."'");
 	//echo "select * from overlap where courseid1='".$courseid."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	while ($row = pg_fetch_assoc($arr)){
		$ret[]=$row;
	}
	return $ret;
}

function printarr($arr,$field){
	if ($arr){
		return $arr[$field];
	}
}
function gettentative($username){
	$arr = pg_query("select coursesem.courseid,coursesem.slotid from coursesem,(select * from tentativecourses where studentid='".trim($username)."') as temp where temp.courseid=coursesem.courseid and coursesem.year=".$_SESSION['year']." and coursesem.semnumber=".$_SESSION['sem']."");
 	//echo "select coursesem.courseid,coursesem.slotid from coursesem,(select * from tentativecourses where studentid='".trim($username)."') as temp where temp.courseid=coursesem.courseid and coursesem.year=".$_SESSION['year']." and coursesem.semnumber=".$_SESSION['sem']."";
 
	while($row = pg_fetch_assoc($arr)){
		$ret[] = $row;
	}
	return $ret;
}

function checkcourseexist($table,$course,$username){
	$arr = pg_query("select courseid from ".$table." where courseid = '".$course."' and studentid='".$username."'");
 	//echo "select courseid from ".$table." where courseid = '".$course."' and studentid='".$username."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	return true;
}
function send_request($username,$friend){
	$arr = pg_query("insert into tentativefriends values ('".$username."','".$friend."')");
 //echo "insert into tentativefriends values ('".$username."','".$friend."')";
 
	if (!$arr)
		return false;
	return true;
}
function requestexists($username,$friend){
	$arr = pg_query("select * from tentativefriends where studentid1='".$username."' and studentid2='".$friend."'");
 //echo "select * from tentativefriends where studentid1='".$username."' and studentid2='".$friend."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	return true;
}
function gettentativefriends($username){
	$arr = pg_query("select tentativefriends.studentid1,tentativefriends.studentid2,student.name from tentativefriends inner join student on tentativefriends.studentid1=student.studentid where tentativefriends.studentid2='".$username."'");
 //echo "select tentativefriends.studentid1,tentativefriends.studentid2,student.name from tentativefriends inner join student on tentativefriends.studentid1=student.studentid where tentativefriends.studentid2='".$username."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	while($row = pg_fetch_assoc($arr)){
		$ret[] = $row;
	}

	return $ret;
}

function tentativerequestexists($username,$friend){

	//echo "select * from tentativefriends where studentid2='".$username."' and studentid1='".$friend."'";
	$arr = pg_query("select * from tentativefriends where studentid2='".$username."' and studentid1='".$friend."'");
 //echo "select * from tentativefriends where studentid2='".$username."' and studentid1='".$friend."'";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	return true;
}
function deletefromtentative($studentid1,$studentid2){
	$arr = pg_query("delete from tentativefriends where studentid1='".$studentid1."' and studentid2='".$studentid2."'");
 //echo "delete from tentativefriends where studentid1='".$studentid1."' and studentid2='".$studentid2."'";
 
	if (!$arr){
		return false;
	}
	return true;
}

function addfriend($friend,$username){
	$arr = pg_query("insert into friends values ('".$username."','".$friend."')");
 //echo "insert into friends values ('".$username."','".$friend."')";
 
	if (!$arr){
		return false;
	}
	return true;
}
function checkiffriend($username,$friend){
	////echo "select * from friends where (studentid1='".$username."' and studentid2='".$friend."') or (studentid2='".$username."' and studentid1='".$friend."')";
	$arr = pg_query("select * from friends where (studentid1='".$username."' and studentid2='".$friend."') or (studentid2='".$username."' and studentid1='".$friend."')");
 //echo "select * from friends where (studentid1='".$username."' and studentid2='".$friend."') or (studentid2='".$username."' and studentid1='".$friend."')";
 
	if (pg_num_rows($arr)==0){
		return false;
	}
	return true;
}
function getfriends($username){
	
	$arr = pg_query("select temp.id,student.name from student,(select distinct id from ((select studentid1 as id from friends where studentid2='".$username."') union (select studentid2 as id from friends where studentid1='".$username."')) as temp1) as temp where temp.id=student.studentid");

 
	if (pg_num_rows($arr)==0){
		return false;
	}

	while($row = pg_fetch_assoc($arr)){
		$ret[] = $row;
	}
	return $ret;
}

function get_profs_course($courseid){
	$arr = pg_query("select prof.profid,prof.name from prof inner join (select distinct profid from coursesem where courseid='".$courseid."') as temp on temp.profid=prof.profid");
	if (pg_num_rows($arr)==0){
		return false;
	}

	while($row = pg_fetch_assoc($arr)){
		$ret[] = $row;
	}
	return $ret;
}
function get_reviews_course($courseid,$profid){
	$arr = pg_query("select reviews.attpolicy,reviews.rating,reviews.grading,reviews.comments from reviews inner join (select distinct reviewid from donecourses where courseid='".$courseid."') as temp on temp.reviewid = reviews.reviewid and reviews.profid='".$profid."'");
	if (pg_num_rows($arr)==0){
		return false;
	}

	while($row = pg_fetch_assoc($arr)){
		$ret[] = $row;
	}
	return $ret;
}

function make_views(){
	$query1 = "select depid from department";
	$arr = pg_query($query1);
	while($row = pg_fetch_assoc($arr)){
		$ret[] = $row;
	}
	if (sizeof($ret)){
	for ($i=0;$i<sizeof($ret);$i++){
		$depid = $ret[$i]['depid'];
		$view = "dep_".$depid;
		$query = "create view ".strtoupper($view)." as (select donecourses.studentid, donecourses.courseid from donecourses inner join studentdep on donecourses.studentid = studentdep.studentid where upper(studentdep.depid) ='".strtoupper($depid)."')";
		//$query = "drop view ".$view;
		echo $query;
		$result = pg_query($query);
		}
	}
}


function core_credits_done(){
	$username = $_SESSION['username'];
	//$depid = get_depid();
	//$depid = substr($username, 4, 3);
	$depid = get_depid();
	if($depid)
		$depid = $depid[0]['depid'];
	$courses = "(select courseid from donecourses where studentid = '".$username."')";
	$query = "select sum(credits) from ".$courses." as temp inner join coreof on coreof.courseid=temp.courseid inner join course on course.courseid = temp.courseid where coreof.depid = '".$depid."'";
	//echo $query;
	$arr = pg_query($query);
	if (pg_num_rows($arr)==0){
		return false;
	}

	while($row = pg_fetch_assoc($arr)){
		$ret[] = $row;
	}
	return $ret;
}

function get_depid(){
	$username = $_SESSION['username'];
	$query = "select depid from studentdep where studentid ='".$username."'";
	$arr = pg_query($query);
	if (pg_num_rows($arr)==0){
		return false;
	}

	while($row = pg_fetch_assoc($arr)){
		$ret[] = $row;
	}
	return $ret;
}

function elec_credits_done(){
	$username = $_SESSION['username'];
	$depid = get_depid();
	if($depid)
		$depid = $depid[0]['depid'];
	//$depid = substr($username, 4, 3);
	$courses = "(select courseid from donecourses where studentid = '".$username."')";
	$query = "select sum(credits) from ".$courses." as temp inner join elecof on elecof.courseid=temp.courseid inner join course on course.courseid = temp.courseid where elecof.depid = '".$depid."'";
	//echo $query;
	$arr = pg_query($query);
	if (pg_num_rows($arr)==0){
		return false;
	}

	while($row = pg_fetch_assoc($arr)){
		$ret[] = $row;
	}
	return $ret;
}

function get_core_req(){
	$depid = get_depid();
	if($depid)
		$depid = $depid[0]['depid'];
	$query = "select corecred from department where upper(depid) = '".strtoupper($depid)."'";
	$arr = pg_query($query);
	if (pg_num_rows($arr)==0){
		return false;
	}

	while($row = pg_fetch_assoc($arr)){
		$ret[] = $row;
	}
	return $ret;
}

function get_elec_req(){
	$depid = get_depid();
	if($depid)
		$depid = $depid[0]['depid'];
	$query = "select eleccred from department where upper(depid) = '".strtoupper($depid)."'";
	$arr = pg_query($query);
	if (pg_num_rows($arr)==0){
		return false;
	}

	while($row = pg_fetch_assoc($arr)){
		$ret[] = $row;
	}
	return $ret;
}

function add_dummydata($studentid,$offset){
	$start = intval(substr($studentid,0,4));
	$depid = substr($studentid, 4, 3);
	for ($i=2014;$i<=2014;$i++){
		$sem = 1;
		$arr = pg_query("select courseid,year,semnumber from coursesem where year=".$i." and semnumber=".$sem."  order by profid limit 4 offset ".$offset."");
		while($row = pg_fetch_assoc($arr)){
			$ret[] = $row;
		}
		for ($j=0;$j<sizeof($ret);$j++)
			pg_query("insert into tentativecourses values('".$studentid."','".$ret[$j]['courseid']."','".$depid."')");		
		/*$sem = 2;
		$arr = pg_query("select courseid,year,semnumber from coursesem where year=".$i." and semnumber=".$sem."  order by profid limit 4 offset ".$offset."");
		while($row = pg_fetch_assoc($arr)){
			$ret[] = $row;
		}
		for ($j=0;$j<sizeof($ret);$j++)
			pg_query("insert into donecourses values('".$studentid."','".$ret[$j]['year']."','".$ret[$j]['semnumber']."','".$ret[$j]['courseid']."')");
		*/
	}
	
}
function add_questions($studentid,$offset){
	$ques = ["How is the course?","How is the Prof?","Do we have to put significant efforts in the course?","How is class attendance?","Rate auditing criteria","Are the text books sufficient enough?","How is the assignment difficulty level?","Rate professor strictness","Rate difficulty of exams?","Rate preferabilty of the course"];
	
		$time = date('Y/m/d H:i:s');
		$arr = pg_query("select courseid from coursesem where year=2014 and semnumber=1  order by profid limit 4 offset ".$offset."");
		while($row = pg_fetch_assoc($arr)){
			$ret[] = $row;
		}
		for ($j=0;$j<sizeof($ret);$j++)
		{
			ask_question($studentid,$ques[rand(0,9)],$time,$ret[$j]['courseid']);
		}
}

function add_dummy_ans()
{
	$ans=["Pathetic","Poor","Bad","Average","Moderate","Good","Excellent","Best","Amazing","Outstanding"];
	$arr = pg_query("Select questionid from questions");
	while($row = pg_fetch_assoc($arr)){
			$ret[] = $row;
		}
	$arr1 = pg_query('select studentid from student order by name limit 800 offset 1500');
		while($row1 = pg_fetch_assoc($arr1)){
		$ret1[] = $row1;	
	}

	for ($j=0;$j<sizeof($ret);$j++)
	{	
		$qid = $ret[$j]['questionid'];
		$studentid = $ret1[2*$j]['studentid'];
		$ans1 = $ans[rand(0, 9)];
		pg_query("insert into answers(upvotes,downvotes,questionid,answer,studentid) values (".rand(0,25).",".rand(0,20).",".$qid.",'".$ans1."','".$studentid."' )");



		$studentid = $ret1[2*$j+1]['studentid'];
		$ans1 = $ans[rand(0, 9)];
		pg_query("insert into answers(upvotes,downvotes,questionid,answer,studentid) values (".rand(0,25).",".rand(0,20).",".$qid.",'".$ans1."','".$studentid."' )");
	}
}


function add_dummy_reviews(){
	$query = "select studentid, courseid from donecourses";
	$ans=["Pathetic","Awesome course","Bad","Average","Moderate","Excellent course","Best","Dont take it","Conceptual Course", "Absolute Grading"];
	$arr = pg_query($query);
	while($row = pg_fetch_assoc($arr)){
			$ret[] = $row;
		}
	for ($j=0;$j<sizeof($ret);$j++)
	{
		$comments = $ans[rand(0, sizeof($ans)-1)];
		add_reviews($ret[$j]['studentid'],$ret[$j]['courseid'],rand(1,10),rand(1,10),rand(1,10),$comments);
	}
}

function add_friends(){
	$query = "select studentid from student";
	$arr = pg_query($query);
	while($row = pg_fetch_assoc($arr)){
			$ret[] = $row;
		}
	for ($j=0;$j<sizeof($ret);$j++)
	{
		$friend1 = rand(0, sizeof($ret)-1);
		$query = "insert into friends values ('".$ret[$j]['studentid']."', '".$ret[$friend1]['studentid']."')";
		$arr = pg_query($query);

		$friend2 = rand(0, sizeof($ret)-1);
		$query = "insert into friends values ('".$ret[$j]['studentid']."', '".$ret[$friend2]['studentid']."')";
		$arr = pg_query($query);
	}

}

function add_convener(){
	$q = "select depid from department";
	$a = pg_query($q);
		while($r = pg_fetch_assoc($a)){
				$r1[] = $r;
			}
	for($i=0; $i<sizeof($r1); $i++){
		$depid = $r1[$i]['depid'];
		for($j=2012; $j<2013; $j++){
			$query = "select studentid from student where studentid like '".$j.$depid."%' order by name limit 1";
			//echo $query;
			$arr = pg_query($query);
			while($row = pg_fetch_assoc($arr)){
					$ret[] = $row;
				}
			$query = "insert into conveners values ('".$ret[0]['studentid']."','".$depid."')";
			//echo $query;
			$arr = pg_query($query);



		}
	}
}
?>
