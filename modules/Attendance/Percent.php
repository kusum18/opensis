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
DrawBC("Attendance >> ".ProgramTitle());
if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
	$start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.substr($_REQUEST['year_start'],2,4);
else
	$start_date = '01-'.strtoupper(date('M-y'));

if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
	$end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.substr($_REQUEST['year_end'],2,4);
else
	$end_date = DBDate();

if($_REQUEST['modfunc']=='search')
{
	echo '<BR>';
	PopTable('header','Advanced');
	echo "<FORM name=percentform action=Modules.php?modname=$_REQUEST[modname]&list_by_day=$_REQUEST[list_by_day]&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end] method=POST>";
	echo '<TABLE>';
	Search('general_info');
	Search('student_fields');
	if(User('PROFILE')=='admin')
		echo '<CENTER><INPUT type=checkbox name=_search_all_schools value=Y'.(Preferences('DEFAULT_ALL_SCHOOLS')=='Y'?' CHECKED':'').'><font color=black>Search All Schools</font></CENTER><BR>';
	echo '<CENTER>'.Buttons('Submit').'</CENTER>';
	echo '</FORM>';
	PopTable('footer');
}

if(!$_REQUEST['modfunc'])
{
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&list_by_day=$_REQUEST[list_by_day] method=POST>";
	$advanced_link = " <A HREF=Modules.php?modname=$_REQUEST[modname]&modfunc=search&list_by_day=$_REQUEST[list_by_day]&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end]>Advanced</A>";
	DrawHeaderHome(PrepareDate($start_date,'_start').' - '.PrepareDate($end_date,'_end').$advanced_link,'<INPUT type=submit value=Go class=btn_medium>');
	echo '</FORM>';

	if($_REQUEST['list_by_day']=='true')
	{
		$cal_days = 1;

		$extra = '';
		if($_REQUEST['GENDER'])
			$extra .= " AND s.GENDER='$_REQUEST[GENDER]'";
		if($_REQUEST['stuid'])
			$extra .= " AND s.STUDENT_ID = '$_REQUEST[stuid]' ";
		if($_REQUEST['last'])
			$extra .= " AND UPPER(s.LAST_NAME) LIKE '".strtoupper($_REQUEST['last'])."%' ";
		if($_REQUEST['first'])
			$extra .= " AND UPPER(s.FIRST_NAME) LIKE '".strtoupper($_REQUEST['first'])."%' ";
		if($_REQUEST['ethnic'])
			$extra .= " AND s.ETHNICITY = '$_REQUEST[ethnic]' ";

		$student_days_absent = DBGet(DBQuery("SELECT ad.SCHOOL_DATE,ssm.GRADE_ID,COALESCE(sum(ad.STATE_VALUE-1)*-1,0) AS STATE_VALUE FROM ATTENDANCE_DAY ad,STUDENT_ENROLLMENT ssm,STUDENTS s WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ad.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ad.SYEAR=ssm.SYEAR AND ad.SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND (ad.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ad.SCHOOL_DATE)) ".$extra.CustomFields('where')." GROUP BY ad.SCHOOL_DATE,ssm.GRADE_ID"),array(''),array('SCHOOL_DATE','GRADE_ID'));
		$student_days_possible = DBGet(DBQuery("SELECT ac.SCHOOL_DATE,ssm.GRADE_ID,'' AS DAYS_POSSIBLE,count(*) AS ATTENDANCE_POSSIBLE,count(*) AS STUDENTS,'' AS PRESENT,'' AS ABSENT,'' AS ADA,'' AS AVERAGE_ATTENDANCE,'' AS AVERAGE_ABSENT FROM STUDENT_ENROLLMENT ssm,ATTENDANCE_CALENDAR ac,STUDENTS s WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ac.SYEAR=ssm.SYEAR AND ssm.SCHOOL_ID='".UserSchool()."' AND ssm.SCHOOL_ID=ac.SCHOOL_ID AND (ac.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ac.SCHOOL_DATE)) AND ac.SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' ".$extra.CustomFields('where')." GROUP BY ac.SCHOOL_DATE,ssm.GRADE_ID"),array('SCHOOL_DATE'=>'ProperDate','GRADE_ID'=>'GetGrade','STUDENTS'=>'_makeByDay','PRESENT'=>'_makeByDay','ABSENT'=>'_makeByDay','ADA'=>'_makeByDay','AVERAGE_ATTENDANCE'=>'_makeByDay','AVERAGE_ABSENT'=>'_makeByDay','DAYS_POSSIBLE'=>'_makeByDay'));

		$columns = array('SCHOOL_DATE'=>'Date','GRADE_ID'=>'Grade','STUDENTS'=>'Students','DAYS_POSSIBLE'=>'Days Possible','PRESENT'=>'Present','ABSENT'=>'Absent','ADA'=>'ADA','AVERAGE_ATTENDANCE'=>'Average Attendance','AVERAGE_ABSENT'=>'Average Absent');

		ListOutput($student_days_possible,$columns,'','',$link);
	}
	else
	{
		$cal_days = DBGet(DBQuery("SELECT count(*) AS COUNT,CALENDAR_ID FROM ATTENDANCE_CALENDAR WHERE ".($_REQUEST['_search_all_schools']!='Y'?"SCHOOL_ID='".UserSchool()."' AND ":'')." SYEAR='".UserSyear()."' AND SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' GROUP BY CALENDAR_ID"),array(),array('CALENDAR_ID'));
		$calendars_RET = DBGet(DBQuery("SELECT CALENDAR_ID,TITLE FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' ".($_REQUEST['_search_all_schools']!='Y'?" AND SCHOOL_ID='".UserSchool()."'":'')),array(),array('CALENDAR_ID'));

		$extra = '';
		if($_REQUEST['GENDER'])
			$extra .= " AND s.GENDER='$_REQUEST[GENDER]'";
		if($_REQUEST['stuid'])
			$extra .= " AND s.STUDENT_ID = '$_REQUEST[stuid]' ";
		if($_REQUEST['last'])
			$extra .= " AND UPPER(s.LAST_NAME) LIKE '".strtoupper($_REQUEST['last'])."%' ";
		if($_REQUEST['first'])
			$extra .= " AND UPPER(s.FIRST_NAME) LIKE '".strtoupper($_REQUEST['first'])."%' ";
		if($_REQUEST['ethnic'])
			$extra .= " AND s.ETHNICITY = '$_REQUEST[ethnic]' ";

		$extra .= CustomFields('where')." GROUP BY ssm.GRADE_ID,ssm.CALENDAR_ID";

		$student_days_absent = DBGet(DBQuery("SELECT ssm.GRADE_ID,ssm.CALENDAR_ID,COALESCE(sum(ad.STATE_VALUE-1)*-1,0) AS STATE_VALUE FROM ATTENDANCE_DAY ad,STUDENT_ENROLLMENT ssm,STUDENTS s WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ad.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ad.SYEAR=ssm.SYEAR AND ad.SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND (ad.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ad.SCHOOL_DATE)) ".$extra),array(''),array('GRADE_ID','CALENDAR_ID'));
		$student_days_possible = DBGet(DBQuery("SELECT ssm.GRADE_ID,ssm.CALENDAR_ID,'' AS DAYS_POSSIBLE,count(*) AS ATTENDANCE_POSSIBLE,count(*) AS STUDENTS,'' AS PRESENT,'' AS ABSENT,'' AS ADA,'' AS AVERAGE_ATTENDANCE,'' AS AVERAGE_ABSENT FROM STUDENT_ENROLLMENT ssm,ATTENDANCE_CALENDAR ac,STUDENTS s WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ac.SYEAR=ssm.SYEAR AND ac.CALENDAR_ID=ssm.CALENDAR_ID AND ".($_REQUEST['_search_all_schools']!='Y'?"ssm.SCHOOL_ID='".UserSchool()."' AND ":'')." ssm.SCHOOL_ID=ac.SCHOOL_ID AND (ac.SCHOOL_DATE BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND ssm.START_DATE <= ac.SCHOOL_DATE)) AND ac.SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' ".$extra),array('GRADE_ID'=>'_make','STUDENTS'=>'_make','PRESENT'=>'_make','ABSENT'=>'_make','ADA'=>'_make','AVERAGE_ATTENDANCE'=>'_make','AVERAGE_ABSENT'=>'_make','DAYS_POSSIBLE'=>'_make'));

		$columns = array('GRADE_ID'=>'Grade','STUDENTS'=>'Total Attendance','DAYS_POSSIBLE'=>'Days Possible','PRESENT'=>'Present','ABSENT'=>'Absent','ADA'=>'ADA','AVERAGE_ATTENDANCE'=>'Average Attendance','AVERAGE_ABSENT'=>'Average Absent');
		$link['add']['html'] = array('GRADE_ID'=>'<b>Total</b>','STUDENTS'=>round($sum['STUDENTS'],1),'DAYS_POSSIBLE'=>$cal_days[key($cal_days)][1]['COUNT'],'PRESENT'=>$sum['PRESENT'],'ADA'=>Percent(($sum['PRESENT'])/($sum['PRESENT']+$sum['ABSENT'])),'ABSENT'=>$sum['ABSENT'],'AVERAGE_ATTENDANCE'=>round($sum['AVERAGE_ATTENDANCE'],1),'AVERAGE_ABSENT'=>round($sum['AVERAGE_ABSENT'],1));

		ListOutput($student_days_possible,$columns,'','',$link);
	}
}

function _make($value,$column)
{	global $THIS_RET,$student_days_absent,$cal_days,$sum,$calendars_RET;

	switch($column)
	{
		case 'STUDENTS':
			//$sum['STUDENTS'] += $value/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'];
			$sum['STUDENTS'] += $value;
			//return round($value/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'],1);
			return $value;
		break;

		case 'DAYS_POSSIBLE':
			return $cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'];
		break;

		case 'PRESENT':
			$sum['PRESENT'] += ($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']);
			return $THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE'];
		break;

		case 'ABSENT':
			$sum['ABSENT'] += ($student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']);
			return $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE'];
		break;

		case 'ADA':
			return Percent((($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']))/$THIS_RET['STUDENTS']);
		break;

		case 'AVERAGE_ATTENDANCE':
			$sum['AVERAGE_ATTENDANCE'] += (($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE'])/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT']);
			return round(($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE'])/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'],1);
		break;

		case 'AVERAGE_ABSENT':
			$sum['AVERAGE_ABSENT'] += ($student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT']);
			return round($student_days_absent[$THIS_RET['GRADE_ID']][$THIS_RET['CALENDAR_ID']][1]['STATE_VALUE']/$cal_days[$THIS_RET['CALENDAR_ID']][1]['COUNT'],1);
		break;

		case 'GRADE_ID':
			return GetGrade($value).(count($cal_days)>1?' - '.$calendars_RET[$THIS_RET['CALENDAR_ID']][1]['TITLE']:'');
	}
}

function _makeByDay($value,$column)
{	global $THIS_RET,$student_days_absent,$cal_days,$sum;

	switch($column)
	{
		case 'STUDENTS':
			$sum['STUDENTS'] += $value/$cal_days;
			return round($value/$cal_days,1);
		break;

		case 'DAYS_POSSIBLE':
			return $cal_days;
		break;

		case 'PRESENT':
			$sum['PRESENT'] += ($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']);
			return $THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'];
		break;

		case 'ABSENT':
			$sum['ABSENT'] += ($student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']);
			return $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'];
		break;

		case 'ADA':
			return Percent((($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']))/$THIS_RET['STUDENTS']);
		break;

		case 'AVERAGE_ATTENDANCE':
			$sum['AVERAGE_ATTENDANCE'] += (($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'])/$cal_days);
			return round(($THIS_RET['ATTENDANCE_POSSIBLE'] - $student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE'])/$cal_days,1);
		break;

		case 'AVERAGE_ABSENT':
			$sum['AVERAGE_ABSENT'] += ($student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']/$cal_days);
			return round($student_days_absent[$THIS_RET['SCHOOL_DATE']][$THIS_RET['GRADE_ID']][1]['STATE_VALUE']/$cal_days,1);
		break;
	}
}
?>