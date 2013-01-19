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
if(!$_REQUEST['modfunc'] && $_REQUEST['search_modfunc']!='list')
	unset($_SESSION['MassSchedule.php']);

if($_REQUEST['modfunc']=='save')
{
	if($_SESSION['MassSchedule.php'])
	{
		$start_date = $_REQUEST['day'].'-'.$_REQUEST['month'].'-'.$_REQUEST['year'];
		if(!VerifyDate($start_date))
			BackPrompt('The date you entered is not valid');
		$course_mp = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
		$course_mp = $course_mp[1]['MARKING_PERIOD_ID'];
		$course_mp_table = GetMPTable(GetMP($course_mp,'TABLE'));

		if($course_mp_table!='FY' && $course_mp!=$_REQUEST['marking_period_id'] && strpos(GetChildrenMP($course_mp_table,$course_mp),"'".$_REQUEST['marking_period_id']."'")===false)
		{
		//	BackPrompt("You cannot schedule a student into that course during the marking period that you chose.  This course meets on ".GetMP($course_mp).'.');
		ShowErr("You cannot schedule a student into that course during the marking period that you chose.  This course meets on ".GetMP($course_mp).'.');
		for_error();
		}
		$mp_table = GetMPTable(GetMP($_REQUEST['marking_period_id'],'TABLE'));

		$current_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM SCHEDULE WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."' AND SYEAR='".UserSyear()."' AND (('".$start_date."' BETWEEN START_DATE AND END_DATE OR END_DATE IS NULL) AND '".$start_date."'>=START_DATE)"),array(),array('STUDENT_ID'));
		$request_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM SCHEDULE_REQUESTS WHERE WITH_PERIOD_ID=(SELECT PERIOD_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."') AND SYEAR='".UserSyear()."' AND COURSE_ID='".$_SESSION['MassSchedule.php']['course_id']."'"),array(),array('STUDENT_ID'));
		$check_seats = DBGet(DBQuery("SELECT  (TOTAL_SEATS - FILLED_SEATS) AS AVAILABLE_SEATS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
		$check_seats = $check_seats[1]['AVAILABLE_SEATS'];
		$no_seat = 'There is no available seats in this period.<br>Please increase the "Total Seats" from Scheduling>>Setup>Courses';

		foreach($_REQUEST['student'] as $student_id=>$yes)
		{
			if(!$current_RET[$student_id])
			{
				if(!$request_RET[$student_id])
				{
					$sql = "INSERT INTO SCHEDULE (SYEAR,SCHOOL_ID,STUDENT_ID,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID,START_DATE)
								values('".UserSyear()."','".UserSchool()."','".$student_id."','".$_SESSION['MassSchedule.php']['course_id']."','".$_SESSION['MassSchedule.php']['course_period_id']."','".$mp_table."','".$_REQUEST['marking_period_id']."','".$start_date."')";
					DBQuery($sql);
					DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'");
					$request_exists = false;
					$note = "That course has been added to the selected students' schedules.";
				}
				else
				{
					$select_stu = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STUDENTS WHERE STUDENT_ID='".$student_id."'"));
					$select_stu = $select_stu[1]['FIRST_NAME']."&nbsp;".$select_stu[1]['LAST_NAME'];
					$request_clash .= $select_stu."<br>";
					$request_exists = true;
				}
			}
			else
			{
			$select_stu = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STUDENTS WHERE STUDENT_ID='".$student_id."'"));
			$select_stu = $select_stu[1]['FIRST_NAME']."&nbsp;".$select_stu[1]['LAST_NAME'];
			$clash .= $select_stu."<br>";
			}
		}
		unset($_REQUEST['modfunc']);
		unset($_SESSION['MassSchedule.php']);
	}
	else
	{
		//BackPrompt('You must choose a Course');
		ShowErr('You must choose a Course');
		for_error();
		
	}
		
}


if($_REQUEST['modfunc']!='choose_course')
{
	DrawBC("Scheduling > ".ProgramTitle());
	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM name=sav id=sav action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
		#DrawHeader('',SubmitButton('Add Course to Selected Students'));
		PopTable_wo_header ('header');
		echo '<TABLE><TR><TD>Course to Add</TD><TD><DIV id=course_div>';
		if($_SESSION['MassSchedule.php'])
		{
			$course_title = DBGet(DBQuery("SELECT TITLE FROM COURSES WHERE COURSE_ID='".$_SESSION['MassSchedule.php']['course_id']."'"));
			$course_title = $course_title[1]['TITLE'];
			$period_title = DBGet(DBQuery("SELECT TITLE FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
			$period_title = $period_title[1]['TITLE'];

			echo "$course_title - $_REQUEST[course_weight]<BR>$period_title";
		}
		echo '</DIV>'."<A HREF=# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'>Choose a Course</A></TD></TR>";
		echo '<TR><TD>Start Date</TD><TD>'.PrepareDate(DBDate(),'').'</TD></TR>';

		echo '<TR><TD>Marking Period</TD><TD>';
		//echo '<SELECT name=marking_period_id><OPTION value=0>Full Year</OPTION>';
		$years_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
		$semesters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
		$quarters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
		echo '<SELECT name=marking_period_id><OPTION value='.$years_RET[1]['MARKING_PERIOD_ID'].'>'.$years_RET[1]['TITLE'].'</OPTION>';
		foreach($semesters_RET as $mp)
			echo "<OPTION value=$mp[MARKING_PERIOD_ID]>".$mp['TITLE'].'</OPTION>';
		foreach($quarters_RET as $mp)
			echo "<OPTION value=$mp[MARKING_PERIOD_ID]>".$mp['TITLE'].'</OPTION>';
		echo '</SELECT>';
		echo '</TD></TR>';
		echo '</TABLE>';
		PopTable ('footer');
	}

	if($note)
		DrawHeader('<IMG SRC=assets/check.gif>'.$note);
	if($check_seats<=0 && $no_seat)
		DrawHeaderHome('<IMG SRC=assets/warning_button.gif>'.$no_seat);
	if($clash)
		DrawHeaderHome('<IMG SRC=assets/warning_button.gif>'.$clash." is already in the schedule");
	if($request_exists)
		DrawHeaderHome('<IMG SRC=assets/warning_button.gif>'.$request_clash.' already have unscheduled requests');

}

if(!$_REQUEST['modfunc'])
{
	if($_REQUEST['search_modfunc']!='list')
		unset($_SESSION['MassSchedule.php']);
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",CAST(NULL AS CHAR(1)) AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['new'] = true;

	Widgets('course');
	Widgets('request');
	Widgets('activity');

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER>'.SubmitButton('Add Course to Selected Students','','class=btn_xlarge onclick=\'formload_ajax("sav");\'').'</CENTER>';
		echo "</FORM>";
	}

}

if($_REQUEST['modfunc']=='choose_course')
{
//	if($_REQUEST['course_id'])
//	{
//		$weights_RET = DBGet(DBQuery("SELECT COURSE_WEIGHT,GPA_MULTIPLIER FROM COURSE_WEIGHTS WHERE COURSE_ID='$_REQUEST[course_id]'"));
//		if(count($weights_RET)==1)
//			$_REQUEST['course_weight'] = $weights_RET[1]['COURSE_WEIGHT'];
//	}

	if(!$_REQUEST['course_period_id'])
		include 'modules/Scheduling/CoursesforWindow.php';
	else
	{
		$_SESSION['MassSchedule.php']['subject_id'] = $_REQUEST['subject_id'];
		$_SESSION['MassSchedule.php']['course_id'] = $_REQUEST['course_id'];
		//$_SESSION['MassSchedule.php']['course_weight'] = $_REQUEST['course_weight'];
		$_SESSION['MassSchedule.php']['course_period_id'] = $_REQUEST['course_period_id'];

		$course_title = DBGet(DBQuery("SELECT TITLE FROM COURSES WHERE COURSE_ID='".$_SESSION['MassSchedule.php']['course_id']."'"));
		$course_title = $course_title[1]['TITLE'];
		$period_title = DBGet(DBQuery("SELECT TITLE FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_SESSION['MassSchedule.php']['course_period_id']."'"));
		$period_title = $period_title[1]['TITLE'];

		echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$course_title<BR>$period_title\"; window.close();</script>";
	}
}

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

		return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}



?>