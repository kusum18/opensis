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

//if(User('PROFILE')=='teacher')
//	$_REQUEST['period_id'] = UserPeriod();

if($_REQUEST['search_modfunc'] || $_REQUEST['student_id'] || User('PROFILE')=='parent' || User('PROFILE')=='student')
{
	if(!UserStudentID() && !$_REQUEST['student_id'])
	{
		//$periods_RET = DBGet(DBQuery("SELECT PERIOD_ID,TITLE FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
		$periods_RET = DBGet(DBQuery("SELECT sp.PERIOD_ID,sp.TITLE FROM SCHOOL_PERIODS sp WHERE sp.SYEAR='".UserSyear()."' AND sp.SCHOOL_ID='".UserSchool()."' AND EXISTS(SELECT '' FROM COURSE_PERIODS cp WHERE cp.PERIOD_ID=sp.PERIOD_ID and cp.DOES_ATTENDANCE='Y'".(User('PROFILE')=='teacher'?" AND cp.PERIOD_ID='".UserPeriod()."'":'').") ORDER BY sp.SORT_ORDER"));
		$period_select = "<SELECT name=period_id onchange='this.form.submit();'><OPTION value=\"\">Daily</OPTION>";
		if(count($periods_RET))
		{
			foreach($periods_RET as $period)
				$period_select .= "<OPTION value=$period[PERIOD_ID]".(($_REQUEST['period_id']==$period['PERIOD_ID'])?' SELECTED':'').">$period[TITLE]</OPTION>";
		}
		$period_select .= '</SELECT>';
	}

	$PHP_tmp_SELF = PreparePHP_SELF();
	echo "<FORM action=$PHP_tmp_SELF method=POST>";
	DrawHeaderHome(PrepareDate($start_date,'_start').' - '.PrepareDate($end_date,'_end').' : <INPUT type=submit class=btn_medium value=Go>',$period_select);
	echo '</FORM>';
}

if($_REQUEST['period_id'])
{
	$extra['SELECT'] .= ",(SELECT count(*) FROM ATTENDANCE_PERIOD ap,ATTENDANCE_CODES ac
						WHERE ac.ID=ap.ATTENDANCE_CODE AND (ac.STATE_CODE='A' OR ac.STATE_CODE='H') AND ap.STUDENT_ID=ssm.STUDENT_ID
						AND ap.PERIOD_ID='$_REQUEST[period_id]'
						AND ap.SCHOOL_DATE BETWEEN '".date('Y-m-d',strtotime($start_date))."' AND '".date('Y-m-d',strtotime($end_date))."') AS STATE_ABS";

	#$extra['columns_after']['STATE_ABS'] = 'State Abs';
	$codes_RET = DBGet(DBQuery("SELECT ID,TITLE FROM ATTENDANCE_CODES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TABLE_NAME='0' AND (DEFAULT_CODE!='Y' OR DEFAULT_CODE IS NULL)"));
	if(count($codes_RET)>1)
	{
		foreach($codes_RET as $code)
		{
			$extra['SELECT'] .= ",(SELECT count(*) FROM ATTENDANCE_PERIOD ap,ATTENDANCE_CODES ac
						WHERE ac.ID=ap.ATTENDANCE_CODE AND ac.ID='$code[ID]' AND ap.PERIOD_ID='$_REQUEST[period_id]' AND ap.STUDENT_ID=ssm.STUDENT_ID
						AND ap.SCHOOL_DATE BETWEEN '".date('Y-m-d',strtotime($start_date))."' AND '".date('Y-m-d',strtotime($end_date))."') AS ABS_$code[ID]";
			$extra['columns_after']["ABS_$code[ID]"] = $code['TITLE'];
		}
	}
}
else
{
	/*$extra['SELECT'] .= ",(SELECT COALESCE((SUM(STATE_VALUE-1)*-1),0.0) FROM ATTENDANCE_DAY ad
						WHERE ad.STUDENT_ID=ssm.STUDENT_ID
						AND ad.SCHOOL_DATE BETWEEN '$start_date' AND '$end_date') AS STATE_ABS";*/
	$extra['SELECT'] .= ",(SELECT count(*) FROM ATTENDANCE_PERIOD ap,ATTENDANCE_CODES ac
						WHERE ac.ID=ap.ATTENDANCE_CODE AND (ac.STATE_CODE='A' OR ac.STATE_CODE='H') AND ap.STUDENT_ID=ssm.STUDENT_ID
						AND ap.PERIOD_ID='$_REQUEST[period_id]'
						AND ap.SCHOOL_DATE BETWEEN '".date('Y-m-d',strtotime($start_date))."' AND '".date('Y-m-d',strtotime($end_date))."') AS STATE_ABS";
						
	#$extra['columns_after']['STATE_ABS'] = 'Days Abs';
	$codes_RET = DBGet(DBQuery("SELECT ID,TITLE FROM ATTENDANCE_CODES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TABLE_NAME='0' AND (DEFAULT_CODE!='Y' OR DEFAULT_CODE IS NULL)"));
	if(count($codes_RET)>1)
	{
		foreach($codes_RET as $code)
		{
			$extra['SELECT'] .= ",(SELECT count(*) FROM ATTENDANCE_PERIOD ap,ATTENDANCE_CODES ac
						WHERE ac.ID=ap.ATTENDANCE_CODE AND ac.ID='$code[ID]' AND ap.STUDENT_ID=ssm.STUDENT_ID
						AND ap.SCHOOL_DATE BETWEEN '".date('Y-m-d',strtotime($start_date))."' AND '".date('Y-m-d',strtotime($end_date))."') AS ABS_$code[ID]";
			$extra['columns_after']["ABS_$code[ID]"] = $code['TITLE'];
		}
	}
}
$extra['link']['FULL_NAME']['link'] = "Modules.php?modname=$_REQUEST[modname]&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end]&period_id=$_REQUEST[period_id]";
$extra['link']['FULL_NAME']['variables'] = array('student_id'=>'STUDENT_ID');
if((!$_REQUEST['search_modfunc'] || $_CENTRE['modules_search']) && !$_REQUEST['student_id'])
	$extra['new'] = true;

Widgets('activity');
Widgets('course');
Widgets('absences');

Search('student_id',$extra);

if(UserStudentID())
{
	$name_RET = DBGet(DBQuery("SELECT CONCAT(FIRST_NAME,' ',COALESCE(MIDDLE_NAME,' '),' ',LAST_NAME) AS FULL_NAME FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
	DrawHeader($name_RET[1]['FULL_NAME']);
	$PHP_tmp_SELF = PreparePHP_SELF();
$i=0;
	$absences_RET = DBGet(DBQuery("SELECT ap.STUDENT_ID,ap.PERIOD_ID,ap.SCHOOL_DATE,ac.SHORT_NAME,ad.STATE_VALUE,ad.COMMENT AS OFFICE_COMMENT,ap.COMMENT AS TEACHER_COMMENT FROM ATTENDANCE_PERIOD ap,ATTENDANCE_DAY ad,ATTENDANCE_CODES ac WHERE ap.STUDENT_ID=ad.STUDENT_ID AND ap.SCHOOL_DATE=ad.SCHOOL_DATE AND ap.ATTENDANCE_CODE=ac.ID AND (ac.DEFAULT_CODE!='Y' OR ac.DEFAULT_CODE IS NULL) AND ap.STUDENT_ID='".UserStudentID()."' AND ap.SCHOOL_DATE BETWEEN '".date('Y-m-d',strtotime($start_date))."' AND '".date('Y-m-d',strtotime($end_date))."'"),array(),array('SCHOOL_DATE','PERIOD_ID'));
	foreach($absences_RET as $school_date=>$absences)
	{
		$i++;
		$days_RET[$i]['SCHOOL_DATE'] = ProperDate($school_date);
		$days_RET[$i]['DAILY'] = _makeStateValue($absences[key($absences)][1]['STATE_VALUE']);
		$days_RET[$i]['OFFICE_COMMENT'] = $absences[key($absences)][1]['OFFICE_COMMENT'];
		$days_RET[$i]['TEACHER_COMMENT'] = $absences[key($absences)][1]['TEACHER_COMMENT'];
		foreach($absences as $period_id=>$absence)
			$days_RET[$i][$period_id] = $absence[1]['SHORT_NAME'];
	}

	//$periods_RET = DBGet(DBQuery("SELECT PERIOD_ID,SHORT_NAME FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
	$periods_RET = DBGet(DBQuery("SELECT sp.PERIOD_ID,sp.SHORT_NAME FROM SCHOOL_PERIODS sp,SCHEDULE s,COURSE_PERIODS cp WHERE sp.SCHOOL_ID='".UserSchool()."' AND sp.SYEAR='".UserSyear()."' AND s.STUDENT_ID='".UserStudentID()."' AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.DOES_ATTENDANCE='Y' ORDER BY sp.SORT_ORDER"));
	$columns['SCHOOL_DATE'] = 'Date';
	$columns['DAILY'] = 'Present';
	$columns['OFFICE_COMMENT'] = 'Office Comment';
	$columns['TEACHER_COMMENT'] = 'Teacher Comment';
	foreach($periods_RET as $period)
		$columns[$period['PERIOD_ID']] = $period['SHORT_NAME'];
	ListOutput($days_RET,$columns,'Day','Days');
}

function _makeStateValue($value)
{	global $THIS_RET,$date;

	if($value=='0.0')
		return 'None';
	elseif($value=='.5')
		return 'Half-Day';
	else
		return 'Full-Day';
}
?>
