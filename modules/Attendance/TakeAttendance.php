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
include 'modules/Attendance/config.inc.php';

if($_REQUEST['month_date'] && $_REQUEST['day_date'] && $_REQUEST['year_date'])
{
	if(strlen($_REQUEST['year_date'])==4)
	while(!VerifyDate($date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.substr($_REQUEST['year_date'],2,4)))
		$_REQUEST['day_date']--;
	else
	while(!VerifyDate($date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date']))
		$_REQUEST['day_date']--;
}else
{
	$_REQUEST['day_date'] = date('d');
	$_REQUEST['month_date'] = strtoupper(date('M'));
	$_REQUEST['year_date'] = date('y');
	$date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date'];
}

DrawBC("Attendance > ".ProgramTitle());

if(!isset($_REQUEST['table']))
	$_REQUEST['table'] = '0';

if($_REQUEST['table']=='0')
	$table = 'ATTENDANCE_PERIOD';
else
	$table = 'LUNCH_PERIOD';

$course_RET = DBGET(DBQuery("SELECT cp.HALF_DAY FROM ATTENDANCE_CALENDAR acc,COURSE_PERIODS cp,SCHOOL_PERIODS sp WHERE acc.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND acc.SCHOOL_DATE='$date' AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."'
AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE)
AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0
	OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)
".($_REQUEST['table']=='0'?"AND cp.DOES_ATTENDANCE='Y'":'')));

$qtr_id = GetCurrentMP('QTR',$date,false);

// if running as a teacher program then centre[allow_edit] will already be set according to admin permissions
if(!isset($_CENTRE['allow_edit']))
{
	// allow teacher edit if selected date is in the current quarter or in the corresponding grade posting period
	$current_qtr_id = GetCurrentMP('QTR',DBDate(),false);
	$time = strtotime(DBDate('postgres'));
	if(($current_qtr_id && $qtr_id==$current_qtr_id || GetMP($qtr_id,'POST_START_DATE') && ($time<=strtotime(GetMP($qtr_id,'POST_END_DATE')))) && ($edit_days_before=='' || strtotime($date)<=$time+$edit_days_before*86400) && ($edit_days_after=='' || strtotime($date)>=$time-$edit_days_after*86400))
		$_CENTRE['allow_edit'] = true;
}

$current_Q = "SELECT ATTENDANCE_TEACHER_CODE,STUDENT_ID,ADMIN,COMMENT FROM $table WHERE SCHOOL_DATE='$date' AND COURSE_PERIOD_ID='".UserCoursePeriod()."'".($table=='LUNCH_PERIOD'?" AND TABLE_NAME='$_REQUEST[table]'":'');
$current_RET = DBGet(DBQuery($current_Q),array(),array('STUDENT_ID'));
if($_REQUEST['attendance'] && ($_POST['attendance'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['attendance'] as $student_id=>$value)
	{
		if($current_RET[$student_id])
		{
			$sql = "UPDATE ".$table." SET ATTENDANCE_TEACHER_CODE='".substr($value,5)."' ";
			if($current_RET[$student_id][1]['ADMIN']!='Y')
				$sql .= ",ATTENDANCE_CODE='".substr($value,5)."'";
			if(isset($_REQUEST['comment'][$student_id]))
				$sql .= ",COMMENT='".trim($_REQUEST['comment'][$student_id])."'";
			$sql .= " WHERE SCHOOL_DATE='$date' AND COURSE_PERIOD_ID='".UserCoursePeriod()."' AND STUDENT_ID='$student_id'";
		}
		else
			$sql = "INSERT INTO ".$table." (STUDENT_ID,SCHOOL_DATE,MARKING_PERIOD_ID,PERIOD_ID,COURSE_PERIOD_ID,ATTENDANCE_CODE,ATTENDANCE_TEACHER_CODE,COMMENT".($table=='LUNCH_PERIOD'?',TABLE_NAME':'').") values('$student_id','$date','$qtr_id','".UserPeriod()."','".UserCoursePeriod()."','".substr($value,5)."','".substr($value,5)."','".$_REQUEST['comment'][$student_id]."'".($table=='LUNCH_PERIOD'?",'$_REQUEST[table]'":'').")";
		DBQuery($sql);
		if($_REQUEST['table']=='0')
			UpdateAttendanceDaily($student_id,$date);
	}
	if($_REQUEST['table']=='0')
	{
		$RET = DBGet(DBQuery("SELECT 'completed' AS COMPLETED FROM ATTENDANCE_COMPLETED WHERE STAFF_ID='".User('STAFF_ID')."' AND SCHOOL_DATE='$date' AND PERIOD_ID='".UserPeriod()."'"));
		if(!count($RET))
			DBQuery("INSERT INTO ATTENDANCE_COMPLETED (STAFF_ID,SCHOOL_DATE,PERIOD_ID) values('".User('STAFF_ID')."','$date','".UserPeriod()."')");
	}

	$current_RET = DBGet(DBQuery($current_Q),array(),array('STUDENT_ID'));
	unset($_SESSION['_REQUEST_vars']['attendance']);
}

$codes_RET = DBGet(DBQuery("SELECT ID,TITLE,DEFAULT_CODE,STATE_CODE FROM ATTENDANCE_CODES WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND TYPE = 'teacher' AND TABLE_NAME='".$_REQUEST['table']."'".($course_RET[1]['HALF_DAY'] ? " AND STATE_CODE!='H'" : '')." ORDER BY SORT_ORDER"));
if(count($codes_RET))
{
	foreach($codes_RET as $code)
	{
		$extra['SELECT'] .= ",'$code[STATE_CODE]' AS CODE_".$code['ID'];
		if($code['DEFAULT_CODE']=='Y')
			$extra['functions']['CODE_'.$code['ID']] = '_makeRadioSelected';
		else
			$extra['functions']['CODE_'.$code['ID']] = '_makeRadio';
		$columns['CODE_'.$code['ID']] = $code['TITLE'];
	}
}
else
	$columns = array();
$extra['SELECT'] .= ',s.STUDENT_ID AS COMMENT';
$columns += array('COMMENT'=>'Comment');
if(!is_array($extra['functions']))
	$extra['functions'] = array();
$extra['functions'] += array('FULL_NAME'=>'_makeTipMessage','COMMENT'=>'makeCommentInput');
$extra['DATE'] = $date;
$stu_RET = GetStuList($extra);

$date_note = $date!=DBDate() ? ' <span class=red>The selected date is not today</span>' : '';

#$date_note .= AllowEdit() ? ' <FONT COLOR=green>You can edit this attendance</FONT>':' <FONT COLOR=red>You can not edit this attendance</FONT>';
# commented as requested


if($_REQUEST['table']=='0')
{
	$completed_RET = DBGet(DBQuery("SELECT 'Y' as COMPLETED FROM ATTENDANCE_COMPLETED WHERE STAFF_ID='".User('STAFF_ID')."' AND SCHOOL_DATE='$date' AND PERIOD_ID='".UserPeriod()."'"));
	if($completed_RET[1]['COMPLETED']=='Y')
		$note = ErrorMessage(array('<IMG SRC=assets/check.gif>You have taken attendance today for this period.'),'note');
}

echo "<FORM ACTION=Modules.php?modname=$_REQUEST[modname]&table=$_REQUEST[table] method=POST>";
if(count($course_RET)!=0)
DrawHeader(PrepareDate($date,'_date',false,array('submit'=>true)).$date_note,SubmitButton('Save','','class=btn_medium'));
else
DrawHeader(PrepareDate($date,'_date',false,array('submit'=>true)).$date_note);

DrawHeader($note);

$LO_columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','GRADE_ID'=>'Grade') + $columns;

$tabs[] = array('title'=>'Attendance','link'=>"Modules.php?modname=$_REQUEST[modname]&table=0&month_date=$_REQUEST[month_date]&day_date=$_REQUEST[day_date]&year_date=$_REQUEST[year_date]");
$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM ATTENDANCE_CODE_CATEGORIES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
foreach($categories_RET as $category)
	$tabs[] = array('title'=>$category['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&table=$category[ID]&month_date=$_REQUEST[month_date]&day_date=$_REQUEST[day_date]&year_date=$_REQUEST[year_date]");

echo '<BR>';
if(count($categories_RET))
{
	echo '<CENTER>'.WrapTabs($tabs,"Modules.php?modname=$_REQUEST[modname]&table=$_REQUEST[table]&month_date=$_REQUEST[month_date]&day_date=$_REQUEST[day_date]&year_date=$_REQUEST[year_date]").'</CENTER>';
	$extra = array('download'=>false,'search'=>false);
}
else
{
	$extra = array();
	$singular = 'Student';
	$plural = 'Students';
}
if(!$qtr_id)
	echo "<table align=center><tr><td class=note></td><td class=note_msg>The selected date is not in a school quarter.</td></tr></table>";
else
{
	if(count($course_RET)!=0)
	{
		ListOutput($stu_RET,$LO_columns,$singular,$plural,array(),array(),$extra);
		echo '<CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER>';
	}
	else
		echo "<table align=center><tr><td class=note></td><td class=note_msg>You cannot take attendance for this period on this day</td></tr></table>";
}
	
echo '</FORM>';

function _makeRadio($value,$title)
{	global $THIS_RET,$current_RET;

	$colors = array('P'=>'#00FF00','A'=>'#FF0000','H'=>'#FFCC00','T'=>'#0000FF');
	if($current_RET[$THIS_RET['STUDENT_ID']][1]['ATTENDANCE_TEACHER_CODE']==substr($title,5))
		return "<TABLE align=center".($colors[$value]?' bgcolor='.$colors[$value]:'')."><TR><TD><INPUT type=radio name=attendance[$THIS_RET[STUDENT_ID]] value='$title' CHECKED></TD></TR></TABLE>";
	else
		return "<TABLE align=center><TR><TD><INPUT type=radio name=attendance[$THIS_RET[STUDENT_ID]] value='$title'".(AllowEdit()?'':' disabled')."></TD></TR></TABLE>";
}

function _makeRadioSelected($value,$title)
{	global $THIS_RET,$current_RET;

	$colors = array('P'=>'#00FF00','A'=>'#FF0000','H'=>'#FFCC00','T'=>'#0000FF');
	$colors1 = array('P'=>'#DDFFDD','A'=>'#FFDDDD','H'=>'#FFEEDD','T'=>'#DDDDFF');
	if($current_RET[$THIS_RET['STUDENT_ID']][1]['ATTENDANCE_TEACHER_CODE']!='')
		if($current_RET[$THIS_RET['STUDENT_ID']][1]['ATTENDANCE_TEACHER_CODE']!=substr($title,5))
			return "<TABLE align=center><TR><TD><INPUT type=radio name=attendance[$THIS_RET[STUDENT_ID]] value='$title'".(AllowEdit()?'':' disabled')."></TD></TR></TABLE>";
		else
			return "<TABLE align=center".($colors[$value]?' bgcolor='.$colors[$value]:'')."><TR><TD><INPUT type=radio name=attendance[$THIS_RET[STUDENT_ID]] value='$title' CHECKED></TD></TR></TABLE>";
	else
		return "<TABLE align=center".($colors1[$value]?' bgcolor='.$colors1[$value]:'')."><TR><TD><INPUT type=radio name=attendance[$THIS_RET[STUDENT_ID]] value='$title' CHECKED></TD></TR></TABLE>";
}

function _makeTipMessage($value,$title)
{	global $THIS_RET,$StudentPicturesPath;

	if($StudentPicturesPath && ($file = @fopen($picture_path=$StudentPicturesPath.UserSyear().'/'.$THIS_RET['STUDENT_ID'].'.JPG','r') || $file = @fopen($picture_path=$StudentPicturesPath.(UserSyear()-1).'/'.$THIS_RET['STUDENT_ID'].'.JPG','r')))
		return '<DIV onMouseOver=\'stm(["'.str_replace("'",'&#39;',$THIS_RET['FULL_NAME']).'","<IMG SRC='.str_replace('\\','\\\\',$picture_path).'>"],["white","#333366","","","",,"black","#e8e8ff","","","",,,,2,"#333366",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'>'.$value.'</DIV>';
	else
		return $value;
}

function makeCommentInput($student_id,$column)
{	global $current_RET;

	return TextInput($current_RET[$student_id][1]['COMMENT'],'comment['.$student_id.']','','',true,true);
}
?>