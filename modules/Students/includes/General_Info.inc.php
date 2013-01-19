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


include_once('modules/Students/includes/functions.php');


#########################################################ENROLLMENT##############################################

if(($_REQUEST['month_values'] && ($_POST['month_values'] || $_REQUEST['ajax'])) || ($_REQUEST['values']['STUDENT_ENROLLMENT'] && ($_POST['values']['STUDENT_ENROLLMENT'] || $_REQUEST['ajax'])))
{
	if(!$_REQUEST['values']['STUDENT_ENROLLMENT']['new']['ENROLLMENT_CODE'] && !$_REQUEST['month_values']['STUDENT_ENROLLMENT']['new']['START_DATE'])
	{
		unset($_REQUEST['values']['STUDENT_ENROLLMENT']['new']);
		unset($_REQUEST['day_values']['STUDENT_ENROLLMENT']['new']);
		unset($_REQUEST['month_values']['STUDENT_ENROLLMENT']['new']);
		unset($_REQUEST['year_values']['STUDENT_ENROLLMENT']['new']);
	}
	else
	{
		$date = $_REQUEST['day_values']['STUDENT_ENROLLMENT']['new']['START_DATE'].'-'.$_REQUEST['month_values']['STUDENT_ENROLLMENT']['new']['START_DATE'].'-'.$_REQUEST['year_values']['STUDENT_ENROLLMENT']['new']['START_DATE'];
		$found_RET = DBGet(DBQuery("SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."' AND '" . date("Y-m-d",strtotime($date)). "' BETWEEN START_DATE AND END_DATE"));
		if(count($found_RET))
		{
			unset($_REQUEST['values']['STUDENT_ENROLLMENT']['new']);
			unset($_REQUEST['day_values']['STUDENT_ENROLLMENT']['new']);
			unset($_REQUEST['month_values']['STUDENT_ENROLLMENT']['new']);
			unset($_REQUEST['year_values']['STUDENT_ENROLLMENT']['new']);
			echo ErrorMessage(array('The student is already enrolled on that date, and could not be enrolled a second time on the date you specified.  Please fix, and try enrolling the student again.'));
		}
	}

	$iu_extra['STUDENT_ENROLLMENT'] = "STUDENT_ID='".UserStudentID()."' AND ID='__ID__'";
	$iu_extra['fields']['STUDENT_ENROLLMENT'] = 'ID,SYEAR,STUDENT_ID,';
	$iu_extra['values']['STUDENT_ENROLLMENT'] = "fn_student_enrollment_seq(),'".UserSyear()."','".UserStudentID()."',";
	if(!$new_student)
		SaveData($iu_extra,'',$field_names);
}

$functions = array('START_DATE'=>'_makeStartInput','END_DATE'=>'_makeEndInput','SCHOOL_ID'=>'_makeSchoolInput');
unset($THIS_RET);
$RET = DBGet(DBQuery("SELECT s.FIRST_NAME,s.LAST_NAME,s.custom_200000000, e.ID,e.GRADE_ID,e.ENROLLMENT_CODE,e.START_DATE,e.DROP_CODE,e.END_DATE,e.END_DATE AS END,e.SCHOOL_ID,e.NEXT_SCHOOL,e.CALENDAR_ID FROM STUDENT_ENROLLMENT e,STUDENTS s WHERE e.STUDENT_ID='".UserStudentID()."' AND e.SYEAR='".UserSyear()."' AND e.STUDENT_ID=s.STUDENT_ID ORDER BY e.START_DATE"),$functions);

$add = true;
if(count($RET))
{
	foreach($RET as $value)
	{
		if($value['DROP_CODE']=='' || !$value['DROP_CODE'])
			$add = false;
	}
}
if($add)
	$link['add']['html'] = array('START_DATE'=>_makeStartInput('','START_DATE'),'SCHOOL_ID'=>_makeSchoolInput('','SCHOOL_ID'));

$columns = array('START_DATE'=>'Attendance Start Date this School Year','END_DATE'=>'Dropped','SCHOOL_ID'=>'School');

$schools_RET = DBGet(DBQuery("SELECT ID,TITLE FROM SCHOOLS WHERE ID!='".UserSchool()."'"));
$next_school_options = array(UserSchool()=>'Next grade at current school','0'=>'Retain','-1'=>'Do not enroll after this school year');
if(count($schools_RET))
{
	foreach($schools_RET as $school)
		$next_school_options[$school['ID']] = $school['TITLE'];
}

$calendars_RET = DBGet(DBQuery("SELECT CALENDAR_ID,DEFAULT_CALENDAR,TITLE FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY DEFAULT_CALENDAR ASC"));
if(count($calendars_RET))
{
	foreach($calendars_RET as $calendar)
		$calendar_options[$calendar['CALENDAR_ID']] = $calendar['TITLE'];
}

if($_REQUEST['student_id']!='new')
{
	if(count($RET))
		$id = $RET[count($RET)]['ID'];
	else
		$id = 'new';

	if($id!='new')
		$next_school = $RET[count($RET)]['NEXT_SCHOOL'];
	if($id!='new')
		$calendar = $RET[count($RET)]['CALENDAR_ID'];
	$div = true;
}
else
{
 	$id = 'new';
	$next_school = UserSchool();
	$calendar = $calendars_RET[1]['CALENDAR_ID'];
	$div = false;
}
################################################################################



echo '<TABLE width=100% border=0 cellpadding=3>';
echo '<TR><td height="30px" colspan=2 class=hseparator><b>Demographic Information</b></td></tr>';
echo '<TR><td valign="top">';
echo '<TABLE border=0>';
echo '<tr><td style=width:120px>Name</td><td>:</td><td>';
if($_REQUEST['student_id']=='new')
	echo '<TABLE ><TR><TD >'.TextInput($student['FIRST_NAME'],'students[FIRST_NAME]','<FONT color=red>First</FONT>','size=12 class=cell_floating maxlength=50 style="font-size:14px; font-weight:bold;"').'</TD><TD>'.TextInput($student['MIDDLE_NAME'],'students[MIDDLE_NAME]','Middle','class=cell_floating maxlength=50 style="font-size:14px; font-weight:bold;"').'</TD><TD>'.TextInput($student['LAST_NAME'],'students[LAST_NAME]','<FONT color=red>Last</FONT>','size=12 class=cell_floating maxlength=50 style="font-size:14px; font-weight:bold;"').'</TD><TD>'.SelectInput($student['NAME_SUFFIX'],'students[NAME_SUFFIX]','Suffix',array('Jr.'=>'Jr.','Sr.'=>'Sr.','II'=>'II','III'=>'III','IV'=>'IV','V'=>'V'),'','style="font-size:14px; font-weight:bold;"').'</TD></TR></TABLE>';
else
	echo '<DIV id=student_name><div style="font-size:14px; font-weight:bold;" onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',TextInput($student['FIRST_NAME'],'students[FIRST_NAME]','','maxlength=50 style="font-size:14px; font-weight:bold;"',false)).'</TD><TD>'.str_replace('"','\"',TextInput($student['MIDDLE_NAME'],'students[MIDDLE_NAME]','','size=3 maxlength=50 style="font-size:14px; font-weight:bold;"',false)).'</TD><TD>'.str_replace('"','\"',TextInput($student['LAST_NAME'],'students[LAST_NAME]','','maxlength=50 style="font-size:14px; font-weight:bold;"',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($student['NAME_SUFFIX'],'students[NAME_SUFFIX]','',array('Jr.'=>'Jr.','Sr.'=>'Sr.','II'=>'II','III'=>'III','IV'=>'IV','V'=>'V'),'','style="font-size:14px; font-weight:bold;"',false)).'</TD></TR></TABLE>","student_name",true);\'>'.$student['FIRST_NAME'].' '.$student['MIDDLE_NAME'].' '.$student['LAST_NAME'].' '.$student['NAME_SUFFIX'].'</div></DIV>';
echo'</td></tr>';
#############################################CUSTOM FIELDS###############################

$fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,REQUIRED,HIDE,SORT_ORDER FROM CUSTOM_FIELDS WHERE CATEGORY_ID='$_REQUEST[category_id]' ORDER BY SORT_ORDER,TITLE"));

if(UserStudentID())
{
	$custom_RET = DBGet(DBQuery("SELECT * FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
	$value = $custom_RET[1];
}


if(count($fields_RET))
	echo $separator;
$i=1;
$q=0;
foreach($fields_RET as $field)
{//continue;
	$q++;
	if( $fields_RET[$q]['HIDE']=='Y')
continue;
	switch($field['TYPE'])
	{
		case 'text':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],'','class=cell_medium');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'autos':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'edits':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'numeric':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],'','size=5 maxlength=10 class=cell_medium');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'date':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeDateInput_mod('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;
			

		case 'codeds':
		case 'select':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeSelectInput('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'multiple':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeMultipleInput('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;

		case 'radio':
			if(($i-1)%1==0)
				echo '<TR>';
			echo '<TD>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeCheckboxInput('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			if($i%1==0)
				echo '</TR>';
			$i++;
			break;
	}
}

if(($i-1)%1!=0)
	echo '</TR>';
#echo '</TABLE><BR>';

echo '<TABLE cellpadding=5>';
$i = 1;
foreach($fields_RET as $field)
{
	if($field['TYPE']=='textarea')
	{
		if(($i-1)%1==0)
			echo '<TR>';
		echo '<TD>'.$field['TITLE'].'</td><td>:</td><td>';
		echo _makeTextareaInput('CUSTOM_'.$field['ID'],'class=cell_medium');
		echo '</TD>';
		if($i%2==0)
			echo '</TR>';
		$i++;
	}
}
if(($i-1)%1!=0)
	echo '</TR>';
#echo '</TABLE>';


#############################################CUSTOM FIELDS###############################
echo '</table>';
echo '</td><td valign="top" align="right"><div class=clear></div>';
// IMAGE
if($_REQUEST['student_id']!='new' && $StudentPicturesPath && (($file = @fopen($picture_path=$StudentPicturesPath.UserSyear().'/'.UserStudentID().'.JPG','r')) || ($file = @fopen($picture_path=$StudentPicturesPath.(UserSyear()-1).'/'.UserStudentID().'.JPG','r'))))
{
	fclose($file);
	echo '<div width=150 align="center"><IMG SRC="'.$picture_path.'?id='.rand(6,100000).'" width=150 class=pic>';
	if(User('PROFILE')=='admin' && User('PROFILE')!='student' && User('PROFILE')!='parent')
	echo '<br><a href=Modules.php?modname=Students/Upload.php?modfunc=edit style="text-decoration:none"><b>Update Student\'s Photo</b></a></div>';
	else
	echo '';
}
else
{
	if($_REQUEST['student_id']!='new')
	{
	
	echo '<div align="center"><IMG SRC="assets/noimage.jpg?id='.rand(6,100000).'" width=144 class=pic>';
	if(User('PROFILE')=='admin' && User('PROFILE')!='student' && User('PROFILE')!='parent')
	echo '<br><a href=Modules.php?modname=Students/Upload.php style="text-decoration:none"><b>Upload Student\'s Photo</b></a></div>';
	}
	else
	echo '';
}
	
echo '</td></TR>';
echo '<TR><td height="30px" colspan=2 class=hseparator><b>School Information</b></td></tr><tr><td colspan="2">';
echo '<TABLE border=0>';
echo '<tr><td>Student ID</td><td>:</td><td>';
if($_REQUEST['student_id']=='new')
{
echo NoInput('Will automatically be assigned','');
	//echo TextInput('','assign_student_id','','maxlength=10 size=10 class=cell_medium onkeyup="usercheck_student_id(this)"');
	echo '<span id="ajax_output_stid"></span>';
}
else
	echo NoInput(UserStudentID(),'');

// ----------------------------- Alternate id ---------------------------- //

echo '<tr><td>Alternate ID</td><td>:</td><td>';
echo TextInput($student['ALT_ID'],'students[ALT_ID]','','size=10 class=cell_medium maxlength=10');
echo '</td></tr>';

// ----------------------------- Alternate id ---------------------------- //

echo'</td></tr>';
echo '<tr><td>Grade</td><td>:</td><td>';
if($_REQUEST['student_id']!='new' && $student['SCHOOL_ID'])
	$school_id = $student['SCHOOL_ID'];
else
	$school_id = UserSchool();
$sql = "SELECT ID,TITLE FROM SCHOOL_GRADELEVELS WHERE SCHOOL_ID='".$school_id."' ORDER BY SORT_ORDER";
$QI = DBQuery($sql);
$grades_RET = DBGet($QI);
unset($options);
if(count($grades_RET))
{
	foreach($grades_RET as $value)
		$options[$value['ID']] = $value['TITLE'];
}
if($_REQUEST['student_id']!='new' && $student['SCHOOL_ID']!=UserSchool())
{
	$allow_edit = $_CENTRE['allow_edit'];
	$AllowEdit = $_CENTRE['AllowEdit'][$_REQUEST['modname']];
	$_CENTRE['AllowEdit'][$_REQUEST['modname']] = $_CENTRE['allow_edit'] = false;
}

if($_REQUEST['student_id']=='new')
	$student_id = 'new';
else
	$student_id = UserStudentID();

if($student_id=='new' && !VerifyDate($_REQUEST['day_values']['STUDENT_ENROLLMENT']['new']['START_DATE'].'-'.$_REQUEST['month_values']['STUDENT_ENROLLMENT']['new']['START_DATE'].'-'.$_REQUEST['year_values']['STUDENT_ENROLLMENT']['new']['START_DATE']))
	unset($student['GRADE_ID']);

echo SelectInput($student['GRADE_ID'],'values[STUDENT_ENROLLMENT]['.$student_id.'][GRADE_ID]',(!$student['GRADE_ID']?'<FONT color=red>':'').''.(!$student['GRADE_ID']?'</FONT>':''),$options,'','class=cell_medium');

echo'</td></tr>';
echo '<tr><td>Calendar</td><td>:</td><td>'.SelectInput($calendar,"values[STUDENT_ENROLLMENT][$id][CALENDAR_ID]",(!$calendar||!$div?'':'').''.(!$calendar||!$div?'':''),$calendar_options,false,'class=cell_medium',$div).'</td></tr>';
echo '<tr><td>Rolling/Retention Options</td><td>:</td><td>'.SelectInput($next_school,"values[STUDENT_ENROLLMENT][$id][NEXT_SCHOOL]",(!$next_school||!$div?'':'').''.(!$next_school||!$div?'':''),$next_school_options,false,'class=cell_medium',$div).'</td></tr>';
echo'</table>';
echo '</td></TR>';
echo '<TR><td height="30px" colspan=2 class=hseparator><b>Access Information</b></td></tr><tr><td colspan="2">';
echo '<TABLE border=0>';
echo '<tr><td style=width:120px>Username</td><td>:</td><td>';
echo TextInput($student['USERNAME'],'students[USERNAME]','','class=cell_medium onkeyup="usercheck_init_student(this)"');
echo '<span id="ajax_output_st"></span>';
echo'</td></tr>';
echo '<tr><td>Password</td><td>:</td><td>';
echo TextInput(array($student['PASSWORD'],str_repeat('*',strlen($student['PASSWORD']))),'students[PASSWORD]','','class=cell_medium');
echo '</td></tr>';
echo '<tr><td>Last Login</td><td>:</td><td>';
echo NoInput(ProperDate(substr($student['LAST_LOGIN'],0,10)).substr($student['LAST_LOGIN'],10),'');
echo '</td></tr>';
echo'</table>';
echo '</td></TR>';
echo '<TR><td height="30px" colspan=2 class=hseparator><b>Enrollment Information</b></td></tr><tr><td colspan="2">';
echo '</td></tr></table>';
echo '</TD></TR><tr><td colspan="2">';
if($_REQUEST['student_id']!='new')
{
	if(count($RET))
		$id = $RET[count($RET)]['ID'];
	else
		$id = 'new';

	ListOutput($RET,$columns,'Enrollment Record','Enrollment Records',$link);
	if($id!='new')
		$next_school = $RET[count($RET)]['NEXT_SCHOOL'];
	if($id!='new')
		$calendar = $RET[count($RET)]['CALENDAR_ID'];
	$div = true;
}
else
{
 	$id = 'new';
	ListOutputMod($RET,$columns,'Enrollment Record','Enrollment Records',$link,array(),array('count'=>false));
	$next_school = UserSchool();
	$calendar = $calendars_RET[1]['CALENDAR_ID'];
	$div = false;
}

echo '</TD></TR>';


?>
