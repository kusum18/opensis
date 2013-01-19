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

// TABBED FY,SEM,QTR
// REPLACE DBDate() & date() WITH USER ENTERED VALUES
// ERROR HANDLING

DrawBC("Scheduling > ".ProgramTitle());

Widgets('activity');
Widgets('course');
Widgets('request');

Search('student_id',$extra);

if($_REQUEST['month_date'] && $_REQUEST['day_date'] && $_REQUEST['year_date'])
	while(!VerifyDate($date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date']))
		$_REQUEST['day_date']--;
else
{
	$min_date = DBGet(DBQuery("SELECT min(SCHOOL_DATE) AS MIN_DATE FROM ATTENDANCE_CALENDAR WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	if($min_date[1]['MIN_DATE'] && DBDate('postgres')<$min_date[1]['MIN_DATE'])
	{
		$date = $min_date[1]['MIN_DATE'];
		$_REQUEST['day_date'] = date('d',strtotime($date));
		$_REQUEST['month_date'] = strtoupper(date('M',strtotime($date)));
		$_REQUEST['year_date'] = date('y',strtotime($date));
	}
	else
	{
		$_REQUEST['day_date'] = date('d');
		$_REQUEST['month_date'] = strtoupper(date('M'));
		$_REQUEST['year_date'] = date('y');
		$date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date'];
	}
}

if($_REQUEST['month_schedule'] && ($_POST['month_schedule'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['month_schedule'] as $id=>$start_dates)
	foreach($start_dates as $start_date=>$columns)
	{
		foreach($columns as $column=>$value)
		{
			$_REQUEST['schedule'][$id][$start_date][$column] = $_REQUEST['day_schedule'][$id][$start_date][$column].'-'.$value.'-'.$_REQUEST['year_schedule'][$id][$start_date][$column];
			if($_REQUEST['schedule'][$id][$start_date][$column]=='--')
				$_REQUEST['schedule'][$id][$start_date][$column] = '';
		}
	}
	unset($_REQUEST['month_schedule']);
	unset($_REQUEST['day_schedule']);
	unset($_REQUEST['year_schedule']);
	unset($_SESSION['_REQUEST_vars']['month_schedule']);
	unset($_SESSION['_REQUEST_vars']['day_schedule']);
	unset($_SESSION['_REQUEST_vars']['year_schedule']);
	$_POST['schedule'] = $_REQUEST['schedule'];
}

if($_REQUEST['schedule'] && ($_POST['schedule'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['schedule'] as $course_period_id=>$start_dates)
	foreach($start_dates as $start_date=>$columns)
	{
		$sql = "UPDATE SCHEDULE SET ";

		foreach($columns as $column=>$value)
		{
			$sql .= $column."='".str_replace("\'","''",$value)."',";
		}
		$sql = substr($sql,0,-1) . " WHERE STUDENT_ID='".UserStudentID()."' AND COURSE_PERIOD_ID='".$course_period_id."' AND START_DATE='".$start_date."'";
		DBQuery($sql);
		if($columns['COURSE_PERIOD_ID']!=$course_period_id)
		{
			DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".$columns['COURSE_PERIOD_ID']."'");
			DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS-1 WHERE COURSE_PERIOD_ID='".$course_period_id."'");
		}

		if($columns['START_DATE'] || $columns['END_DATE'])
		{
			$start_end_RET = DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHEDULE WHERE STUDENT_ID='".UserStudentID()."' AND COURSE_PERIOD_ID='".$course_period_id."' AND END_DATE<START_DATE"));
			if(count($start_end_RET))
			{
				DBQuery("DELETE FROM SCHEDULE WHERE STUDENT_ID='".UserStudentID()."' AND END_DATE IS NOT NULL AND END_DATE<START_DATE");
				DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS-1 WHERE COURSE_PERIOD_ID='".$course_period_id."'");
			}
		}
		// User should be asked if he wants absences to be deleted
		if($columns['END_DATE'])
			DBQuery("DELETE FROM ATTENDANCE_PERIOD WHERE STUDENT_ID='".UserStudentID()."' AND COURSE_PERIOD_ID='".$course_period_id."' AND SCHOOL_DATE > '".$columns['END_DATE']."'");
	}
	unset($_SESSION['_REQUEST_vars']['schedule']);
	unset($_REQUEST['schedule']);
}

if(UserStudentID() && $_REQUEST['modfunc']!='choose_course')
{
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=modify METHOD=POST>";

	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['include_inactive']);
	DrawHeaderHome(PrepareDate($date,'_date',false,array('submit'=>true)).' <INPUT type=checkbox name=include_inactive value=Y'.($_REQUEST['include_inactive']=='Y'?" CHECKED onclick='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST)."&include_inactive=\";'":" onclick='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST)."&include_inactive=Y\";'").'>Include Inactive Courses',SubmitButton('Save','','class=btn_medium'));
	DrawHeader(ExportLink('Scheduling/PrintSchedules.php','Print Schedule','&modfunc=save&st_arr[]='.UserStudentID().'&_CENTRE_PDF=true target=_blank'));
	/*
	$schedule_fields_RET = DBGet(DBQuery("SELECT cf.TITLE,s.CUSTOM_71 FROM CUSTOM_FIELDS cf,STUDENTS s WHERE s.STUDENT_ID='".UserStudentID()."' AND cf.ID='71'"));
	if($schedule_fields_RET[1]['TITLE']=='Team')
		DrawHeader('<font color=gray><b>'.$schedule_fields_RET[1]['TITLE'].': </b></font>'.$schedule_fields_RET[1]['CUSTOM_71']);
	*/

	// get the fy marking period id, there should be exactly one fy marking period
	$fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];

	$sql = "SELECT
				s.COURSE_ID,s.COURSE_PERIOD_ID,
				s.MARKING_PERIOD_ID,s.START_DATE,s.END_DATE,
				extract(EPOCH FROM s.START_DATE) AS START_EPOCH,extract(EPOCH FROM s.END_DATE) AS END_EPOCH,sp.PERIOD_ID,
				cp.PERIOD_ID,cp.MARKING_PERIOD_ID as COURSE_MARKING_PERIOD_ID,cp.MP,sp.SORT_ORDER,
				c.TITLE,cp.COURSE_PERIOD_ID AS PERIOD_PULLDOWN,
				s.STUDENT_ID,ROOM,DAYS,SCHEDULER_LOCK
			FROM SCHEDULE s,COURSES c,COURSE_PERIODS cp,SCHOOL_PERIODS sp
			WHERE
				s.COURSE_ID = c.COURSE_ID AND s.COURSE_ID = cp.COURSE_ID
				AND s.COURSE_PERIOD_ID = cp.COURSE_PERIOD_ID
				AND s.SCHOOL_ID = sp.SCHOOL_ID AND s.SYEAR = c.SYEAR AND sp.PERIOD_ID = cp.PERIOD_ID
				AND s.STUDENT_ID='".UserStudentID()."'
				AND s.SYEAR='".UserSyear()."'
				AND s.SCHOOL_ID = '".UserSchool()."'";
	if($_REQUEST['include_inactive']!='Y')
		$sql .= " AND ('".$date."' BETWEEN s.START_DATE AND s.END_DATE OR (s.END_DATE IS NULL AND s.START_DATE<='".$date."')) ";
	$sql .= " ORDER BY sp.SORT_ORDER,s.MARKING_PERIOD_ID";

	$QI = DBQuery($sql);
	$schedule_RET = DBGet($QI,array('TITLE'=>'_makeTitle','PERIOD_PULLDOWN'=>'_makePeriodSelect','COURSE_MARKING_PERIOD_ID'=>'_makeMPSelect','SCHEDULER_LOCK'=>'_makeLock','START_DATE'=>'_makeDate','END_DATE'=>'_makeDate'));

	$link['add']['link'] = "# onclick='window.open(\"for_window.php?modname=$_REQUEST[modname]&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");' ";
	$link['add']['title'] = "Add a Course";

	$columns = array('TITLE'=>'Course','PERIOD_PULLDOWN'=>'Period - Teacher','ROOM'=>'Room','DAYS'=>'Days of Week','COURSE_MARKING_PERIOD_ID'=>'Term','SCHEDULER_LOCK'=>'<IMG SRC=assets/locked.gif border=0>','START_DATE'=>'Enrolled','END_DATE'=>'Dropped');
	$days_RET = DBGet(DBQuery("SELECT DISTINCT DAYS FROM COURSE_PERIODS"));
	if(count($days_RET)==1)
		unset($columns['DAYS']);
	if($_REQUEST['_CENTRE_PDF'])
		unset($columns['SCHEDULER_LOCK']);

	VerifySchedule($schedule_RET);

	ListOutput($schedule_RET,$columns,'Course','Courses',$link);

	echo '<BR><CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER>';
	echo '</FORM>';

	if(AllowEdit())
	{
		unset($_REQUEST);
		$_REQUEST['modname'] = 'Scheduling/Schedule.php';
		$_REQUEST['stuid'] = UserStudentID();
		$_REQUEST['search_modfunc'] = 'list';
		$extra['link']['FULL_NAME']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=choose_course";
		$extra['link']['FULL_NAME']['variables'] = array('subject_id'=>'SUBJECT_ID','course_id'=>'COURSE_ID');
		$extra['link']['FULL_NAME']['js'] = true;
		include('modules/Scheduling/UnfilledRequests.php');
	}
}

if($_REQUEST['modfunc']=='choose_course')
{
	if(!$_REQUEST['course_period_id'])
		include "modules/Scheduling/Courses.php";
	else
	{
		$min_date = DBGet(DBQuery("SELECT min(SCHOOL_DATE) AS MIN_DATE FROM ATTENDANCE_CALENDAR WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
		if($min_date[1]['MIN_DATE'] && DBDate('postgres')<$min_date[1]['MIN_DATE'])
			$date = $min_date[1]['MIN_DATE'];
		else
			$date = DBDate();

		$mp_RET = DBGet(DBQuery("SELECT MP,MARKING_PERIOD_ID,DAYS,PERIOD_ID,MARKING_PERIOD_ID,TOTAL_SEATS,COALESCE(FILLED_SEATS,0) AS FILLED_SEATS FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
		$mps = GetAllMP(GetMPTable(GetMP($mp_RET[1]['MARKING_PERIOD_ID'],'TABLE')),$mp_RET[1]['MARKING_PERIOD_ID']);

		if(is_numeric($mp_RET[1]['TOTAL_SEATS']) && $mp_RET[1]['TOTAL_SEATS']<=$mp_RET[1]['FILLED_SEATS'])
			$warnings[] = 'That section is already full.';

		// the course being scheduled has start date of $date but no end date by default, and scheduled into the course marking period by default
		// if marking periods overlap and dates overlap (already scheduled course does not end or ends after $date) then not okay
		$current_RET = DBGet(DBQuery("SELECT COURSE_PERIOD_ID FROM SCHEDULE WHERE STUDENT_ID='".UserStudentID()."' AND COURSE_ID='".$_REQUEST['course_id']."' AND MARKING_PERIOD_ID IN (".$mps.") AND (END_DATE IS NULL OR '".DBDate()."'<=END_DATE)"));
		if(count($current_RET))
			$warnings[] = 'This student is already scheduled into this course.';

		//if marking periods overlap and same period and same day then not okay
		$period_RET = DBGet(DBQuery("SELECT cp.DAYS FROM SCHEDULE s,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.STUDENT_ID='".UserStudentID()."' AND cp.PERIOD_ID='".$mp_RET[1]['PERIOD_ID']."' AND s.MARKING_PERIOD_ID IN (".$mps.") AND (s.END_DATE IS NULL OR '".DBDate()."'<=s.END_DATE)"));
		$days_conflict = false;
		foreach($period_RET as $existing)
		{
			if(strlen($mp_RET[1]['DAYS'])+strlen($existing['DAYS'])>7)
			{
				$days_conflict = true;
				break;
			}
			else
				foreach(_str_split($mp_RET[1]['DAYS']) as  $i)
					if(strpos($existing['DAYS'],$i)!==false)
					{
						$days_conflict = true;
						break 2;
					}
		}
		if($days_conflict)
			$warnings[] = 'There is already a course scheduled in that period.';

		if(!$warnings)
		{
			DBQuery("INSERT INTO SCHEDULE (SYEAR,SCHOOL_ID,STUDENT_ID,START_DATE,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID) values('".UserSyear()."','".UserSchool()."','".UserStudentID()."','".$date."','".$_REQUEST['course_id']."','".$_REQUEST['course_period_id']."','".$mp_RET[1]['MP']."','".$mp_RET[1]['MARKING_PERIOD_ID']."')");
			DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'");
			echo "<script language=javascript>opener.document.location = 'Modules.php?modname=".$_REQUEST['modname']."&time=".time()."'; window.close();</script>";
		}
		elseif($warnings)
		{
			if(Prompt('Confirm','There is a conflict. Are you sure you want to add this section?',ErrorMessage($warnings,'note')))
			{
				DBQuery("INSERT INTO SCHEDULE (SYEAR,SCHOOL_ID,STUDENT_ID,START_DATE,COURSE_ID,COURSE_PERIOD_ID,MP,MARKING_PERIOD_ID) values('".UserSyear()."','".UserSchool()."','".UserStudentID()."','".$date."','".$_REQUEST['course_id']."','".$_REQUEST['course_period_id']."','".$mp_RET[1]['MP']."','".$mp_RET[1]['MARKING_PERIOD_ID']."')");
				DBQuery("UPDATE COURSE_PERIODS SET FILLED_SEATS=FILLED_SEATS+1 WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'");
				echo "<script language=javascript>opener.document.location = 'Modules.php?modname=".$_REQUEST['modname']."&time=".time()."'; window.close();</script>";
			}
		}
	}
}

function _makeTitle($value,$column='')
{	global $_CENTRE,$THIS_RET;

	return $value;//.' - '.$THIS_RET['COURSE_WEIGHT'];
}

function _makeLock($value,$column)
{	global $THIS_RET;

	if($value=='Y')
		$img = 'locked';
	else
		$img = 'unlocked';

	return '<IMG SRC=assets/'.$img.'.gif '.(AllowEdit()?'onclick="if(this.src.indexOf(\'assets/locked.gif\')!=-1) {this.src=\'assets/unlocked.gif\'; document.getElementById(\'lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].'\').value=\'\';} else {this.src=\'assets/locked.gif\'; document.getElementById(\'lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].'\').value=\'Y\';}"':'').'><INPUT type=hidden name=schedule['.$THIS_RET['COURSE_PERIOD_ID'].']['.$THIS_RET['START_DATE'].'][SCHEDULER_LOCK] id=lock'.$THIS_RET['COURSE_PERIOD_ID'].'-'.$THIS_RET['START_DATE'].' value='.$value.'>';
}

function _makePeriodSelect($course_period_id,$column='')
{	global $_CENTRE,$THIS_RET,$fy_id;

	$sql = "SELECT cp.COURSE_PERIOD_ID,cp.PARENT_ID,cp.TITLE,cp.MARKING_PERIOD_ID,COALESCE(cp.TOTAL_SEATS-cp.FILLED_SEATS,0) AS AVAILABLE_SEATS FROM COURSE_PERIODS cp,SCHOOL_PERIODS sp WHERE sp.PERIOD_ID=cp.PERIOD_ID AND cp.COURSE_ID='$THIS_RET[COURSE_ID]' ORDER BY sp.SORT_ORDER";
	$QI = DBQuery($sql);
	$orders_RET = DBGet($QI);

	foreach($orders_RET as $value)
	{
		if($value['COURSE_PERIOD_ID']!=$value['PARENT_ID'])
		{
			$parent = DBGet(DBQuery("SELECT SHORT_NAME FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='".$value['PARENT_ID']."'"));
			$parent = $parent[1]['SHORT_NAME'];
		}
		$periods[$value['COURSE_PERIOD_ID']] = $value['TITLE'] . (($value['MARKING_PERIOD_ID']!=$fy_id && $value['COURSE_PERIOD_ID']!=$course_period_id)?' ('.GetMP($value['MARKING_PERIOD_ID']).')':'').($value['COURSE_PERIOD_ID']!=$course_period_id?' ('.$value['AVAILABLE_SEATS'].' seats)':'').(($value['COURSE_PERIOD_ID']!=$course_period_id && $parent)?' -> '.$parent:'');
	}

	return SelectInput($course_period_id,"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][COURSE_PERIOD_ID]",'',$periods,false);
}

function _makeMPSelect($mp_id,$name='')
{	global $_CENTRE,$THIS_RET,$fy_id;

	if(!$_CENTRE['_makeMPSelect'])
	{
		$semesters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,NULL AS SEMESTER_ID FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
		$quarters_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));

		$_CENTRE['_makeMPSelect'][$fy_id][1] = array('MARKING_PERIOD_ID'=>"$fy_id",'TITLE'=>'Full Year','SEMESTER_ID'=>'');
		foreach($semesters_RET as $sem)
			$_CENTRE['_makeMPSelect'][$fy_id][] = $sem;
		foreach($quarters_RET as $qtr)
			$_CENTRE['_makeMPSelect'][$fy_id][] = $qtr;

		$quarters_QI = DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SEMESTER_ID FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER");
		$quarters_indexed_RET = DBGet($quarters_QI,array(),array('SEMESTER_ID'));

		foreach($semesters_RET as $sem)
		{
			$_CENTRE['_makeMPSelect'][$sem['MARKING_PERIOD_ID']][1] = $sem;
			foreach($quarters_indexed_RET[$sem['MARKING_PERIOD_ID']] as $qtr)
				$_CENTRE['_makeMPSelect'][$sem['MARKING_PERIOD_ID']][] = $qtr;
		}

		foreach($quarters_RET as $qtr)
			$_CENTRE['_makeMPSelect'][$qtr['MARKING_PERIOD_ID']][] = $qtr;
	}

	foreach($_CENTRE['_makeMPSelect'][$mp_id] as $value)
		$mps[$value['MARKING_PERIOD_ID']] = $value['TITLE'];

	if($THIS_RET['MARKING_PERIOD_ID']!=$mp_id)
		$mps[$THIS_RET['MARKING_PERIOD_ID']] = '* '.$mps[$THIS_RET['MARKING_PERIOD_ID']];

	return SelectInput($THIS_RET['MARKING_PERIOD_ID'],"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][MARKING_PERIOD_ID]",'',$mps,false);
}

function _makeDate($value,$column)
{	global $THIS_RET;

	if($column=='START_DATE')
		$allow_na = false;
	else
		$allow_na = true;

	return DateInput($value,"schedule[$THIS_RET[COURSE_PERIOD_ID]][$THIS_RET[START_DATE]][$column]",'',true,$allow_na);
}

function VerifySchedule(&$schedule)
{
	$conflicts = array();

	$ij = count($schedule);
	for($i=1; $i<$ij; $i++)
		for($j=$i+1; $j<=$ij; $j++)
			if(!$conflicts[$i] || !$conflicts[$j])
				// the following two if's are equivalent, the second matches the 'Add a Course' logic, the first is the demorgan equivalent and easier to follow
				// if -not- marking periods don't overlap -or- dates don't overlap (i ends and j starts after i -or- j ends and i starts after j) then check further
				//if(! (strpos(GetAllMP(GetMPTable(GetMP($schedule[$i]['MARKING_PERIOD_ID'],'TABLE')),$schedule[$i]['MARKING_PERIOD_ID']),"'".$schedule[$j]['MARKING_PERIOD_ID']."'")===false
				//|| $schedule[$i]['END_EPOCH'] && $schedule[$j]['START_EPOCH']>$schedule[$i]['END_EPOCH'] || $schedule[$j]['END_EPOCH'] && $schedule[$i]['START_EPOCH']>$schedule[$j]['END_EPOCH']))
				// if marking periods overlap -and- dates overlap (i doesn't end or j starts before i ends -and- j doesn't end or i starts before j ends) check further
				if(strpos(GetAllMP(GetMPTable(GetMP($schedule[$i]['MARKING_PERIOD_ID'],'TABLE')),$schedule[$i]['MARKING_PERIOD_ID']),"'".$schedule[$j]['MARKING_PERIOD_ID']."'")!==false
				&& (!$schedule[$i]['END_EPOCH'] || $schedule[$j]['START_EPOCH']<=$schedule[$i]['END_EPOCH']) && (!$schedule[$j]['END_EPOCH'] || $schedule[$i]['START_EPOCH']<=$schedule[$j]['END_EPOCH']))
					// should not be enrolled in the same course with overlapping marking periods and dates
					if($schedule[$i]['COURSE_ID']==$schedule[$j]['COURSE_ID']) //&& $schedule[$i]['COURSE_WEIGHT']==$schedule[$j]['COURSE_WEIGHT'])
						$conflicts[$i] = $conflicts[$j] = true;
					else
						// if different periods then okay
						if($schedule[$i]['PERIOD_ID']==$schedule[$j]['PERIOD_ID'])
							// should not be enrolled in the same period on the same day
							if(strlen($schedule[$i]['DAYS'])+strlen($schedule[$j]['DAYS'])>7)
								$conflicts[$i] = $conflicts[$j] = true;
							else
								foreach(_str_split($schedule[$i]['DAYS']) as $k)
									if(strpos($schedule[$j]['DAYS'],$k)!==false)
									{
										$conflicts[$i] = $conflicts[$j] = true;
										break;
									}

	foreach($conflicts as $i=>$true)
		$schedule[$i]['TITLE'] = '<FONT color=red>'.$schedule[$i]['TITLE'].'</FONT>';
}

function _str_split($str)
{
	$ret = array();
	$len = strlen($str);
	for($i=0;$i<$len;$i++)
		$ret [] = substr($str,$i,1);
	return $ret;
}
?>