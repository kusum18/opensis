<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. It is  web-based, 
#  open source, and comes packed with features that include student 
#  demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  Copyright (C) 2007-2008, Open Solutions for Education, Inc.
#
#*************************************************************************
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, version 2 of the License. See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#**************************************************************************
//if($_REQUEST['modfunc']!='XMLHttpRequest')
	DrawBC("Scheduling -> ".ProgramTitle());

Widgets('request');
Search('student_id',$extra);


if($_REQUEST['modfunc']=='remove')
{
	if(DeletePrompt('request'))
	{
		DBQuery("DELETE FROM SCHEDULE_REQUESTS WHERE REQUEST_ID='".$_REQUEST['id']."'");
		unset($_REQUEST['modfunc']);
		unset($_SESSION['_REQUEST_vars']['modfunc']);
		unset($_SESSION['_REQUEST_vars']['id']);
	}
}

if($_REQUEST['modfunc']=='update')
{
	foreach($_REQUEST['values'] as $request_id=>$columns)
	{
		$sql = "UPDATE SCHEDULE_REQUESTS SET ";

		foreach($columns as $column=>$value)
		{
			$sql .= $column."='".str_replace("\'","''",$value)."',";
		}
		$sql = substr($sql,0,-1) . " WHERE STUDENT_ID='".UserStudentID()."' AND REQUEST_ID='".$request_id."'";
		//echo $sql;
		DBQuery($sql);		
	}
	unset($_REQUEST['modfunc']);
}

if($_REQUEST['modfunc']=='add')
{
 
	$course_id = $_REQUEST['course_id'];
	$course_weight = substr($_REQUEST['course'],strpos($_REQUEST['course'],'-')+1);
	//$subject_id = DBGet(DBQuery("SELECT SUBJECT_ID FROM COURSES WHERE COURSE_ID='".$course_id."'"));
	$subject_id =$_REQUEST['subject_id'];
	$mp_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$mp_id = $mp_id[1]['MARKING_PERIOD_ID'];
	DBQuery("INSERT INTO SCHEDULE_REQUESTS (REQUEST_ID,SYEAR,SCHOOL_ID,STUDENT_ID,SUBJECT_ID,COURSE_ID,MARKING_PERIOD_ID) values(".db_seq_nextval('SCHEDULE_REQUESTS_SEQ').",'".UserSyear()."','".UserSchool()."','".UserStudentID()."','".$subject_id."','".$course_id."','".$mp_id."')");
	unset($_REQUEST['modfunc']);
}

/*
if($_REQUEST['modfunc']=='XMLHttpRequest')
{
	header("Content-Type: text/xml\n\n");
	$courses_RET = DBGet(DBQuery("SELECT c.COURSE_ID,c.TITLE FROM COURSES c WHERE ".($_REQUEST['subject_id']?"c.SUBJECT_ID='".$_REQUEST['subject_id']."' AND ":'')."UPPER(c.TITLE) LIKE '".strtoupper($_REQUEST['course_title'])."%' AND c.SYEAR='".UserSyear()."' AND c.SCHOOL_ID='".UserSchool()."'"));
	echo '<?phpxml version="1.0" standalone="yes"?><courses>';
	if(count($courses_RET))
	{
		foreach($courses_RET as $course)
			echo '<course><id>'.$course['COURSE_ID'].'</id><title>'.str_replace('&','&amp;',$course['TITLE']).'</title></course>';
	}
	echo '</courses>';
}
*/
if(!$_REQUEST['modfunc'] && UserStudentID())
{
/*
	echo '<script language=javascript>
function SendXMLRequest(subject_id,course)
{
	if(window.XMLHttpRequest)
		connection = new XMLHttpRequest();
	else if(window.ActiveXObject)
		connection = new ActiveXObject("Microsoft.XMLHTTP");
	connection.onreadystatechange = processRequest;
	connection.open("GET","Modules.php?modname='.$_REQUEST['modname'].'&_CENTRE_PDF=true&modfunc=XMLHttpRequest&subject_id="+subject_id+"&course_title="+course,true);
	connection.send(null);
}

function changeStyle(tag,over)
{
	if(over)
	{
		tag.style.backgroundColor="#'.Preferences('HIGHLIGHT').'";
		tag.style.color="#FFFFFF";
	}
	else
	{
		tag.style.backgroundColor="#FFFFFF";
		tag.style.color="#000000";
	}
}

function doOnClick(course)
{
	document.location.href = "Modules.php?modname='.$_REQUEST['modname'].'&modfunc=add&course="+course;
}

function processRequest()
{
	// LOADED && ACCEPTED
	if(connection.readyState == 4 && connection.status == 200) 
	{
		XMLResponse = connection.responseXML;
		document.getElementById("courses_div").style.visibility = "visible";
		course_list = XMLResponse.getElementsByTagName("courses");
		course_list = course_list[0];
		//alert(course_list[1]);
		courses = course_list.getElementsByTagName("course");
		
		for(i=0;i<courses.length;i++)
		{
			id = courses[i].getElementsByTagName("id")[0].firstChild.data;
			title = courses[i].getElementsByTagName("title")[0].firstChild.data;
			document.getElementById("courses_div").innerHTML = document.getElementById("courses_div").innerHTML + "<A onmousedown=\"doOnClick(\'"+ id +"\')\"><DIV onmouseover=\'changeStyle(this,true)\' onmouseout=\'changeStyle(this,false)\' width=100%>" + title + "</DIV></A>";
		}
	}
}
</script>';
*/	
	$functions = array('COURSE'=>'_makeCourse','WITH_TEACHER_ID'=>'_makeTeacher','WITH_PERIOD_ID'=>'_makePeriod');
	$requests_RET = DBGet(DBQuery("SELECT r.REQUEST_ID,c.TITLE as COURSE,r.COURSE_ID,r.COURSE_WEIGHT,r.MARKING_PERIOD_ID,r.WITH_TEACHER_ID,r.NOT_TEACHER_ID,r.WITH_PERIOD_ID,r.NOT_PERIOD_ID FROM SCHEDULE_REQUESTS r,COURSES c WHERE r.COURSE_ID=c.COURSE_ID AND r.SYEAR='".UserSyear()."' AND r.STUDENT_ID='".UserStudentID()."'"),$functions);
	$columns = array('COURSE'=>'Course','WITH_TEACHER_ID'=>'Teacher','WITH_PERIOD_ID'=>'Period');

	//$link['add']['html'] = array('COURSE_ID'=>_makeCourse('','COURSE_ID'),'WITH_TEACHER_ID'=>_makeTeacher('','WITH_TEACHER_ID'),'WITH_PERIOD_ID'=>_makePeriod('','WITH_PERIOD_ID'),'MARKING_PERIOD_ID'=>_makeMP('','MARKING_PERIOD_ID'));
	$subjects_RET = DBGet(DBQuery("SELECT SUBJECT_ID,TITLE FROM COURSE_SUBJECTS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$subjects= CreateSelect($subjects_RET, 'subject_id', 'Select Subject', 'Modules.php?modname='.$_REQUEST['modname'].'&subject_id=');
	
	if($_REQUEST['subject_id'])
	{

	$courses_RET = DBGet(DBQuery("SELECT c.COURSE_ID,c.TITLE FROM COURSES c WHERE ".($_REQUEST['subject_id']?"c.SUBJECT_ID='".$_REQUEST['subject_id']."' AND ":'')."UPPER(c.TITLE) LIKE '".strtoupper($_REQUEST['course_title'])."%' AND c.SYEAR='".UserSyear()."' AND c.SCHOOL_ID='".UserSchool()."'"));
	$courses = CreateSelect($courses_RET, 'course_id', 'Select Course', 'Modules.php?modname='.$_REQUEST['modname'].'&subject_id='.$_REQUEST['subject_id'].'&course_id=');
			
	}
	if($_REQUEST['course_id'])
	{
	//$periods_RET = DBGet(DBQuery("SELECT c.COURSE_PERIOD_ID,c.TITLE FROM COURSE_PERIODS c, COURSE_SUBJECTS cp WHERE ".($_REQUEST['course_id']?"c.COURSE_ID='".$_REQUEST['course_id']."' AND ":'')."UPPER(c.TITLE) LIKE '".strtoupper($_REQUEST['title'])."%' AND c.SYEAR='".UserSyear()."' AND c.SCHOOL_ID='".UserSchool()."'"));
	//include("modules/Scheduling/RequestsReport.php");
	}
if(User('PROFILE')=='admin')
{
	echo '<br><br><FORM name=ad id=ad action=Modules.php?modname='.$_REQUEST['modname'].'&modfunc=add method=POST>';
	DrawHeaderHome('Add a Request : &nbsp; Subject '.$subjects.' &nbsp; '.$courses,SubmitButton('Add','','class=btn_medium onclick=\'formload_ajax("ad");\''));
	#echo '<small>Add a Request : </small> &nbsp; Subject '.$subjects.' &nbsp; '.$courses;
	#echo '<br><br><CENTER>'.SubmitButton('Add','','class=btn_medium onclick=\'formload_ajax("ad");\'').'</CENTER>';
	echo '</FORM>';
		$link['remove'] = array('link'=>'Modules.php?modname='.$_REQUEST['modname'].'&modfunc=remove','variables'=>array('id'=>'REQUEST_ID'));
	//$link['remove'] = array('link'=>'"#"." onclick='check_content(\"ajax.php?modname='.$_REQUEST['modname'].'&modfunc=remove','variables'=>array('id'=>'REQUEST_ID'));
	echo '<br><br><FORM name=up id=up action=Modules.php?modname='.$_REQUEST['modname'].'&modfunc=update method=POST>';
	//DrawHeaderHome('',SubmitButton('Update'));
	//$link['add']['span'] = '<small>Add a Request : </small> &nbsp; Subject '.$subjects.' &nbsp; '.$courses;
	ListOutput($requests_RET,$columns,'Request','Requests',$link);
	if(!$requests_RET)
	echo '';
	else
	echo '<br><CENTER>'.SubmitButton('Update','','class=btn_medium onclick=\'formload_ajax("up");\'').'</CENTER>';
	echo '</FORM>';

}
else 
{
	$link['remove'] = array('link'=>'Modules.php?modname='.$_REQUEST['modname'].'&modfunc=remove','variables'=>array('id'=>'REQUEST_ID'));
	//$link['remove'] = array('link'=>'"#"." onclick='check_content(\"ajax.php?modname='.$_REQUEST['modname'].'&modfunc=remove','variables'=>array('id'=>'REQUEST_ID'));
	echo '<br><br><FORM name=up id=up action=Modules.php?modname='.$_REQUEST['modname'].'&modfunc=update method=POST>';
	//DrawHeaderHome('',SubmitButton('Update'));
	//$link['add']['span'] = '<small>Add a Request : </small> &nbsp; Subject '.$subjects.' &nbsp; '.$courses;
	ListOutput($requests_RET,$columns,'Request','Requests',$link);
	echo '<br><CENTER>'.SubmitButton('Update','','class=btn_medium onclick=\'formload_ajax("up");\'').'</CENTER>';
	echo '</FORM>';
	}
	
}



function _makeCourse($value,$column)
{	global $THIS_RET;

	return $value.' - '.$THIS_RET['COURSE_WEIGHT'];	

}

function _makeTeacher($value,$column)
{	global $THIS_RET;

	$teachers_RET = DBGet(DBQuery("SELECT s.FIRST_NAME,s.LAST_NAME,s.STAFF_ID AS TEACHER_ID FROM STAFF s,COURSE_PERIODS cp WHERE s.STAFF_ID=cp.TEACHER_ID AND cp.COURSE_ID='".$THIS_RET['COURSE_ID']."'"));
	foreach($teachers_RET as $teacher)
		$options[$teacher['TEACHER_ID']] = $teacher['FIRST_NAME'].' '.$teacher['LAST_NAME'];
	
	return 'With: '.SelectInput($value,'values['.$THIS_RET['REQUEST_ID'].'][WITH_TEACHER_ID]','',$options).' Without: '.SelectInput($THIS_RET['NOT_TEACHER_ID'],'values['.$THIS_RET['REQUEST_ID'].'][NOT_TEACHER_ID]','',$options);
}

function _makePeriod($value,$column)
{	global $THIS_RET;

	$periods_RET = DBGet(DBQuery("SELECT p.TITLE,p.PERIOD_ID FROM SCHOOL_PERIODS p,COURSE_PERIODS cp WHERE p.PERIOD_ID=cp.PERIOD_ID AND cp.COURSE_ID='".$THIS_RET['COURSE_ID']."'"));
	foreach($periods_RET as $period)
		$options[$period['PERIOD_ID']] = $period['TITLE'];
	
	return 'On: '.SelectInput($value,'values['.$THIS_RET['REQUEST_ID'].'][WITH_PERIOD_ID]','',$options).' Not on: '.SelectInput($THIS_RET['NOT_PERIOD_ID'],'values['.$THIS_RET['REQUEST_ID'].'][NOT_PERIOD_ID]','',$options);
}

// DOESN'T SUPPORT MP REQUEST
function _makeMP($value,$column)
{	global $THIS_RET;

	return SelectInput($value,'values['.$THIS_RET['REQUEST_ID'].'][MARKING_PERIOD_ID]','',$options);
}

	function CreateSelect($val, $name, $opt, $link='')
	{
	 	//$html .= "<table width=600px><tr><td align=right width=45%>";
		//$html .= $cap." </td><td width=55%>";
		if($link!='')
		$html .= "<select name=".$name." id=".$name." onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
		else
		$html .= "<select name=".$name." id=".$name." >";
		$html .= "<option value=''>".$opt."</option>";
		
				foreach($val as $key=>$value)
				{
					if($value[strtoupper($name)]==$_REQUEST[$name])
						$html .= "<option selected value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
					else
						$html .= "<option value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";	
				}
		
		
				
		$html .= "</select>";
		return $html;
	}

?>