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
DrawBC("GradeBook > ".ProgramTitle());

include_once 'functions/_makeLetterGrade.fnc.php';
include_once 'functions/_makePercentGrade.fnc.php';
$max_allowed = Preferences('ANOMALOUS_MAX','Gradebook')/100;

// if running as a teacher program then centre[allow_edit] will already be set according to admin permissions
if(!isset($_CENTRE['allow_edit']))
	$_CENTRE['allow_edit'] = true;
	
$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='".User('STAFF_ID')."' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
if(count($config_RET))
	foreach($config_RET as $title=>$value)
		$programconfig[User('STAFF_ID')][$title] = $value[1]['VALUE'];
else
	$programconfig[User('STAFF_ID')] = true;
	
$course_period_id = UserCoursePeriod();
$course_id = DBGet(DBQuery("SELECT COURSE_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID='$course_period_id'"));
$course_id = $course_id[1]['COURSE_ID'];

$assignments_RET = DBGet(DBQuery("SELECT ga.ASSIGNMENT_ID,ga.TITLE,ga.POINTS,gt.TITLE AS TYPE_TITLE,CASE WHEN (ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) THEN 'Y' ELSE NULL END AS DUE FROM GRADEBOOK_ASSIGNMENTS ga,GRADEBOOK_ASSIGNMENT_TYPES gt WHERE ga.STAFF_ID='".User('STAFF_ID')."' AND ((ga.COURSE_ID='$course_id' AND ga.STAFF_ID='".User('STAFF_ID')."') OR ga.COURSE_PERIOD_ID='$course_period_id') AND ga.MARKING_PERIOD_ID='".UserMP()."' AND gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID ORDER BY ga.".Preferences('ASSIGNMENT_SORTING','Gradebook')." DESC"),array(),array('ASSIGNMENT_ID'));
// when changing course periods the assignment_id will be wrong except for '' (totals) and 'all'
if($_REQUEST['assignment_id'] && $_REQUEST['assignment_id']!='all')
{
	foreach($assignments_RET as $id=>$assignment)
		if($_REQUEST['assignment_id']==$id)
		{
			$found = true;
			break;
		}
	if(!$found)
		unset($_REQUEST['assignment_id']);
}

if($_REQUEST['student_id'])
{
	if($_REQUEST['student_id']!=$_SESSION['student_id'])
	{
		$_SESSION['student_id'] = $_REQUEST['student_id'];
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	}
	$_REQUEST['stuid'] = $_REQUEST['student_id'];

	$LO_columns = array('TYPE_TITLE'=>'Category','TITLE'=>'Assignment','POINTS'=>'Points','LETTER_GRADE'=>'Grade','COMMENT'=>'Comment');
	$item = 'Assignment';
	$items = 'Assignments';
	$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]";
	$link['TITLE']['variables'] = array('assignment_id'=>'ASSIGNMENT_ID');

	$current_RET[$_REQUEST['student_id']] = DBGet(DBQuery("SELECT g.ASSIGNMENT_ID FROM GRADEBOOK_GRADES g,GRADEBOOK_ASSIGNMENTS a WHERE a.ASSIGNMENT_ID=g.ASSIGNMENT_ID AND a.MARKING_PERIOD_ID='".UserMP()."' AND g.STUDENT_ID='$_REQUEST[student_id]' AND g.COURSE_PERIOD_ID='$course_period_id'".($_REQUEST['assignment_id']=='all'?'':" AND g.ASSIGNMENT_ID='$_REQUEST[assignment_id]'")),array(),array('ASSIGNMENT_ID'));
	if(count($assignments_RET))
	{
		foreach($assignments_RET as $id=>$assignment)
			$total_points[$id] = $assignment[1]['POINTS'];
	}
	$count_assignments = count($assignments_RET);

	$extra['SELECT'] = ",ga.ASSIGNMENT_ID,gt.TITLE AS TYPE_TITLE,ga.TITLE,ga.POINTS AS TOTAL_POINTS,'' AS LETTER_GRADE,CASE WHEN (ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) THEN 'Y' ELSE NULL END AS DUE";
	$extra['SELECT'] .= ',(SELECT POINTS FROM GRADEBOOK_GRADES WHERE STUDENT_ID=s.STUDENT_ID AND ASSIGNMENT_ID=ga.ASSIGNMENT_ID) AS POINTS';
	$extra['SELECT'] .= ',(SELECT COMMENT FROM GRADEBOOK_GRADES WHERE STUDENT_ID=s.STUDENT_ID AND ASSIGNMENT_ID=ga.ASSIGNMENT_ID) AS COMMENT';
	$extra['FROM'] = ",GRADEBOOK_ASSIGNMENTS ga,GRADEBOOK_ASSIGNMENT_TYPES gt";
	$extra['WHERE'] = "AND ga.STAFF_ID='".User('STAFF_ID')."' AND ((ga.COURSE_ID='$course_id' AND ga.STAFF_ID='".User('STAFF_ID')."') OR ga.COURSE_PERIOD_ID='$course_period_id') AND ga.MARKING_PERIOD_ID='".UserMP()."' AND gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID".($_REQUEST['assignment_id']=='all'?'':" AND ga.ASSIGNMENT_ID='$_REQUEST[assignment_id]'");
	$extra['ORDER_BY'] = Preferences('ASSIGNMENT_SORTING','Gradebook')." DESC";
	$extra['functions'] = array('POINTS'=>'_makeExtraStuCols','LETTER_GRADE'=>'_makeExtraStuCols','COMMENT'=>'_makeExtraStuCols');
}
else
{
	$LO_columns = array('FULL_NAME'=>'Student');
	if($_REQUEST['assignment_id']!='all')
		$LO_columns += array('STUDENT_ID'=>'Student ID');
	if($_REQUEST['include_inactive']=='Y')
		$LO_columns += array('ACTIVE'=>'School Status','ACTIVE_SCHEDULE'=>'Course Status');
	$item = 'Student';
	$items = 'Students';
	$link['FULL_NAME']['link'] = "Modules.php?modname=$_REQUEST[modname]&include_inactive=$_REQUEST[include_inactive]&assignment_id=all";
	$link['FULL_NAME']['variables'] = array('student_id'=>'STUDENT_ID');

		if($_SESSION['student_id'])
		{
			unset($_SESSION['student_id']);
			echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
		}

	if($_REQUEST['assignment_id']=='all')
	{
		$current_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.ASSIGNMENT_ID,g.POINTS FROM GRADEBOOK_GRADES g,GRADEBOOK_ASSIGNMENTS a WHERE a.ASSIGNMENT_ID=g.ASSIGNMENT_ID AND a.MARKING_PERIOD_ID='".UserMP()."' AND g.COURSE_PERIOD_ID='$course_period_id'"),array(),array('STUDENT_ID','ASSIGNMENT_ID'));
		$count_extra = array('SELECT_ONLY'=>'ssm.STUDENT_ID');
		$count_students = GetStuList($count_extra);
		$count_students = count($count_students);

		$extra['functions'] = array();
		if(count($assignments_RET))
		{
			foreach($assignments_RET as $id=>$assignment)
			{
				$assignment = $assignment[1];
				$extra['SELECT'] .= ",'$id' AS G$id,'$assignment[DUE]' AS D$id";
				$extra['functions'] += array('G'.$id=>'_makeExtraCols');
				$LO_columns += array('G'.$id=>$assignment['TYPE_TITLE'].'<BR>'.$assignment['TITLE']);
				$total_points[$id] = $assignment['POINTS'];
			}
		}
	}
	elseif($_REQUEST['assignment_id'])
	{
		$id = $_REQUEST['assignment_id'];
		$extra['SELECT'] .= ",'$id' AS POINTS,'$id' AS LETTER_GRADE,'$id' AS COMMENT,'".$assignments_RET[$id][1]['DUE']."' AS DUE";
		$extra['functions'] = array('POINTS'=>'_makeExtraAssnCols','LETTER_GRADE'=>'_makeExtraAssnCols','COMMENT'=>'_makeExtraAssnCols');
		$LO_columns += array('POINTS'=>'Points','LETTER_GRADE'=>'Grade','COMMENT'=>'Comment');
		$total_points = DBGet(DBQuery("SELECT POINTS FROM GRADEBOOK_ASSIGNMENTS WHERE ASSIGNMENT_ID='$id'"));
		$total_points[$id] = $total_points[1]['POINTS'];
		$current_RET = DBGet(DBQuery("SELECT STUDENT_ID,POINTS,COMMENT,ASSIGNMENT_ID FROM GRADEBOOK_GRADES WHERE ASSIGNMENT_ID='$id' AND COURSE_PERIOD_ID='$course_period_id'"),array(),array('STUDENT_ID','ASSIGNMENT_ID'));
	}
	else
	{
		if(count($assignments_RET))
		{
			$extra['SELECT'] .= ",'' AS POINTS,'' AS LETTER_GRADE,'' AS COMMENT";
			$extra['functions'] = array('POINTS'=>'_makeExtraAssnCols','LETTER_GRADE'=>'_makeExtraAssnCols');
			$LO_columns += array('POINTS'=>'Points','LETTER_GRADE'=>'Grade');
			// this will get the grades for all students ever enrolled in the class
			// the "group by start_date" and "distinct on" are needed in case a student is enrolled more than once (re-enrolled)
			if($programconfig[User('STAFF_ID')]['WEIGHT']=='Y')
				$points_RET = DBGet(DBQuery("SELECT DISTINCT s.STUDENT_ID, gt.ASSIGNMENT_TYPE_ID, sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL, gt.FINAL_GRADE_PERCENT FROM STUDENTS s JOIN SCHEDULE ss ON (ss.STUDENT_ID=s.STUDENT_ID AND ss.COURSE_PERIOD_ID='$course_period_id') JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='".User('STAFF_ID')."') AND ga.MARKING_PERIOD_ID='".UserMP()."') LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID),GRADEBOOK_ASSIGNMENT_TYPES gt WHERE gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND gt.COURSE_ID='$course_id' AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) OR gg.POINTS IS NOT NULL) GROUP BY s.STUDENT_ID,ss.START_DATE,gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT"),array(),array('STUDENT_ID'));
			else
				$points_RET = DBGet(DBQuery("SELECT DISTINCT s.STUDENT_ID,'-1' AS ASSIGNMENT_TYPE_ID, sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,'1' AS FINAL_GRADE_PERCENT FROM STUDENTS s JOIN SCHEDULE ss ON (ss.STUDENT_ID=s.STUDENT_ID AND ss.COURSE_PERIOD_ID='$course_period_id') JOIN GRADEBOOK_ASSIGNMENTS ga ON ((ga.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='".User('STAFF_ID')."') AND ga.MARKING_PERIOD_ID='".UserMP()."') LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.STUDENT_ID=s.STUDENT_ID AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID) WHERE ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE) OR gg.POINTS IS NOT NULL) GROUP BY s.STUDENT_ID,ss.START_DATE"),array(),array('STUDENT_ID'));
			foreach($assignments_RET as $id=>$assignment)
				$total_points[$id] = $assignment[1]['POINTS'];
		}
	}
}

if($_REQUEST['values'] && ($_POST['values'] || $_REQUEST['ajax']) && $_SESSION['assignment_id']==$_REQUEST['assignment_id'])
{
	foreach($_REQUEST['values'] as $student_id=>$assignments)
	{
		foreach($assignments as $assignment_id=>$columns)
		{
			if($columns['POINTS'])
			{
				if($columns['POINTS']=='*')
					$columns['POINTS'] = '-1';
				else
				{
					if(substr($columns['POINTS'],-1)=='%')
						$columns['POINTS'] = substr($columns['POINTS'],0,-1) * $total_points[$assignment_id] / 100;
					elseif(!is_numeric($columns['POINTS']))
						$columns['POINTS'] = _makePercentGrade($columns['POINTS'],$course_period_id) * $total_points[$assignment_id] / 100;
					if($columns['POINTS']<0)
						$columns['POINTS'] = '0';
					elseif($columns['POINTS']>9999.99)
						$columns['POINTS'] = '9999.99';
				}
			}
			$sql = '';
			if($current_RET[$student_id][$assignment_id])
			{
				$sql = "UPDATE GRADEBOOK_GRADES SET ";
				foreach($columns as $column=>$value)
				{
					$sql .= $column."='".str_replace("\'","''",$value)."',";
				}
				$sql = substr($sql,0,-1) . " WHERE STUDENT_ID='$student_id' AND ASSIGNMENT_ID='$assignment_id' AND COURSE_PERIOD_ID='$course_period_id'";
			}
			elseif($columns['POINTS']!='' || $columns['COMMENT'])
				$sql = "INSERT INTO GRADEBOOK_GRADES (STUDENT_ID,PERIOD_ID,COURSE_PERIOD_ID,ASSIGNMENT_ID,POINTS,COMMENT) values('$student_id','".UserPeriod()."','".$course_period_id."','".$assignment_id."','".$columns['POINTS']."','".$columns['COMMENT']."')";

			if($sql)
				DBQuery($sql);
		}
	}
	if($_REQUEST['student_id'])
		$current_RET[$_REQUEST['student_id']] = DBGet(DBQuery("SELECT g.ASSIGNMENT_ID FROM GRADEBOOK_GRADES g,GRADEBOOK_ASSIGNMENTS a WHERE a.ASSIGNMENT_ID=g.ASSIGNMENT_ID AND a.MARKING_PERIOD_ID='".UserMP()."' AND g.STUDENT_ID='$_REQUEST[student_id]' AND g.COURSE_PERIOD_ID='$course_period_id'".($_REQUEST['assignment_id']=='all'?'':" AND g.ASSIGNMENT_ID='$_REQUEST[assignment_id]'")),array(),array('ASSIGNMENT_ID'));
	elseif($_REQUEST['assignment_id']=='all')
		$current_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.ASSIGNMENT_ID,g.POINTS FROM GRADEBOOK_GRADES g,GRADEBOOK_ASSIGNMENTS a WHERE a.ASSIGNMENT_ID=g.ASSIGNMENT_ID AND a.MARKING_PERIOD_ID='".UserMP()."' AND g.COURSE_PERIOD_ID='$course_period_id'"),array(),array('STUDENT_ID','ASSIGNMENT_ID'));
	else
		$current_RET = DBGet(DBQuery("SELECT STUDENT_ID,POINTS,COMMENT,ASSIGNMENT_ID FROM GRADEBOOK_GRADES WHERE ASSIGNMENT_ID='$_REQUEST[assignment_id]' AND COURSE_PERIOD_ID='$course_period_id'"),array(),array('STUDENT_ID','ASSIGNMENT_ID'));

	unset($_REQUEST['values']);
	unset($_SESSION['_REQUEST_vars']['values']);
}

$_SESSION['assignment_id'] = $_REQUEST['assignment_id'];

$stu_RET = GetStuList($extra);

$assignment_select = '<SELECT name=assignment_id onchange="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include_inactive='.$_REQUEST['include_inactive'].'&assignment_id=\'+this.options[selectedIndex].value"><OPTION value="">Totals</OPTION><OPTION value="all"'.(($_REQUEST['assignment_id']=='all' && !$_REQUEST['student_id'])?' SELECTED':'').'>All</OPTION>';
if($_REQUEST['student_id'])
	$assignment_select .= '<OPTION value='.$_REQUEST['assignment_id'].' SELECTED>'.$stu_RET[1]['FULL_NAME'].'</OPTION>';
foreach($assignments_RET as $id=>$assignment)
	$assignment_select .= '<OPTION value='.$id.(($_REQUEST['assignment_id']==$id && !$_REQUEST['student_id'])?' SELECTED':'').'>'.$assignment[1]['TYPE_TITLE'].' - '.$assignment[1]['TITLE'].'</OPTION>';
$assignment_select .= '</SELECT>';

echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&student_id=$_REQUEST[student_id] method=POST>";

$tmp_REQUEST = $_REQUEST;
unset($tmp_REQUEST['include_inactive']);

DrawHeaderHome($assignment_select,$_REQUEST['assignment_id']?SubmitButton('Save','','class=btn_medium'):'','<INPUT type=checkbox name=include_inactive value=Y'.($_REQUEST['include_inactive']=='Y'?" CHECKED onclick='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST)."&include_inactive=\";'":" onclick='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST)."&include_inactive=Y\";'").'>Include Inactive Students');

if(!$_REQUEST['student_id'] && $_REQUEST['assignment_id']=='all')
	$options = array('yscroll'=>true);

ListOutput($stu_RET,$LO_columns,$item,$items,$link,array(),$options);
echo $_REQUEST['assignment_id']?'<CENTER>'.SubmitButton('Save','','class=btn_medium').'</CENTER>':'';
echo '</FORM>';

function _makeExtraAssnCols($assignment_id,$column)
{	global $THIS_RET,$total_points,$current_RET,$points_RET,$tabindex,$max_allowed;

	switch($column)
	{
		case 'POINTS':
			$tabindex++;
			if($assignment_id=='' && !$_REQUEST['student_id'])
			{
				if(count($points_RET[$THIS_RET['STUDENT_ID']]))
				{
					$total = $total_points = 0;
					foreach($points_RET[$THIS_RET['STUDENT_ID']] as $partial_points)
						if($partial_points['PARTIAL_TOTAL']!=0)
						{
							$total += $partial_points['PARTIAL_POINTS'];
							$total_points += $partial_points['PARTIAL_TOTAL'];
						}
				}
				else
					$total = $total_points = 0;

				return '<TABLE border=0 cellspacing=0 cellpadding=0 class=LO_field><TR><TD>'.$total.'</TD><TD>&nbsp;/&nbsp;</TD><TD>'.$total_points.'</TD></TR></TABLE>';
			}
			else
			{
				if($current_RET[$THIS_RET['STUDENT_ID']][$assignment_id][1]['POINTS']=='-1')
					$points = '*';
				elseif(strpos($current_RET[$THIS_RET['STUDENT_ID']][$assignment_id][1]['POINTS'],'.'))
					$points = rtrim(rtrim($current_RET[$THIS_RET['STUDENT_ID']][$assignment_id][1]['POINTS'],'0'),'.');
				else
					$points = $current_RET[$THIS_RET['STUDENT_ID']][$assignment_id][1]['POINTS'];

				return '<TABLE border=0 cellspacing=0 cellpadding=0 class=LO_field><TR><TD>'.TextInput($points,'values['.$THIS_RET['STUDENT_ID'].']['.$assignment_id.'][POINTS]','',' size=2 maxlength=7 tabindex='.$tabindex).'</TD><TD>&nbsp;/&nbsp;</TD><TD>'.$total_points[$assignment_id].'</TD></TR></TABLE>';
			}
		break;

		case 'LETTER_GRADE':
			if($assignment_id=='' && !$_REQUEST['student_id'])
			{
				if(count($points_RET[$THIS_RET['STUDENT_ID']]))
				{
					$total = $total_percent = 0;
					foreach($points_RET[$THIS_RET['STUDENT_ID']] as $partial_points)
						if($partial_points['PARTIAL_TOTAL']!=0)
						{
							$total += $partial_points['PARTIAL_POINTS'] * $partial_points['FINAL_GRADE_PERCENT'] / $partial_points['PARTIAL_TOTAL'];
							$total_percent += $partial_points['FINAL_GRADE_PERCENT'];
						}
					if($total_percent!=0)
						$total /= $total_percent;
				}
				else
					$total = 0;

				return ($total>$max_allowed?'<FONT color=red>':'').Percent($total,0).($total>$max_allowed?'</FONT>':'').'&nbsp;<B>'._makeLetterGrade($total).'</B>';
			}
			else
			{
				$points = $current_RET[$THIS_RET['STUDENT_ID']][$assignment_id][1]['POINTS'];
				if($total_points[$assignment_id]!=0)
					if($points!='-1')
						return ($THIS_RET['DUE']||$points!=''?($points>$total_points[$assignment_id]*$max_allowed?'<FONT color=red>':''):'<FONT color=gray>').Percent($points/$total_points[$assignment_id],0).($THIS_RET['DUE']||$points!=''?($points>$total_points[$assignment_id]*$max_allowed?'</FONT>':''):'').'&nbsp;<B>'. _makeLetterGrade($points/$total_points[$assignment_id]).'</B>'.($THIS_RET['DUE']||$points!=''?'':'</FONT>');
					else
						return 'N/A&nbsp;N/A';
				else
					return 'E/C';
			}
		break;

		case 'COMMENT':
			return TextInput($current_RET[$THIS_RET['STUDENT_ID']][$assignment_id][1]['COMMENT'],'values['.$THIS_RET['STUDENT_ID'].']['.$assignment_id.'][COMMENT]','',' maxlength=100 tabindex='.(500+$tabindex));
		break;
	}

}

function _makeExtraStuCols($value,$column)
{	global $THIS_RET,$assignment_count,$count_assignments,$max_allowed;

	switch($column)
	{
		case 'POINTS':
			$assignment_count++;
			$tabindex = $assignment_count;

			if($value=='-1')
				$value = '*';
			elseif(strpos($value,'.'))
				$value = rtrim(rtrim($value,'0'),'.');

			return '<TABLE border=0 cellspacing=0 cellpadding=0 class=LO_field><TR><TD>'.TextInput($value,'values['.$THIS_RET['STUDENT_ID'].']['.$THIS_RET['ASSIGNMENT_ID'].'][POINTS]','',' size=2 maxlength=7 tabindex='.$tabindex).'</TD><TD>&nbsp;/&nbsp;</TD><TD>'.$THIS_RET['TOTAL_POINTS'].'</TD></TR></TABLE>';
		break;

		case 'LETTER_GRADE':
			if($THIS_RET['TOTAL_POINTS']!=0)
				if($THIS_RET['POINTS']!='-1')
					return ($THIS_RET['DUE']||$THIS_RET['POINTS']!=''?($THIS_RET['POINTS']>$THIS_RET['TOTAL_POINTS']*$max_allowed?'<FONT color=red>':''):'<FONT color=gray>').Percent($THIS_RET['POINTS']/$THIS_RET['TOTAL_POINTS'],0).($THIS_RET['DUE']||$THIS_RET['POINTS']!=''?($THIS_RET['POINTS']>$THIS_RET['TOTAL_POINTS']*$max_allowed?'</FONT>':''):'').'&nbsp;<B>'. _makeLetterGrade($THIS_RET['POINTS']/$THIS_RET['TOTAL_POINTS']).'</B>'.($THIS_RET['DUE']||$THIS_RET['POINTS']!=''?'':'</FONT>');
				else
					return 'N/A&nbsp;N/A';
			else
				return 'E/C';
		break;

		case 'COMMENT':
			$tabindex += $count_assignments;

			return TextInput($value,'values['.$THIS_RET['STUDENT_ID'].']['.$THIS_RET['ASSIGNMENT_ID'].'][COMMENT]','',' maxlength=100 tabindex='.$tabindex);
		break;
	}
}

function _makeExtraCols($assignment_id,$column)
{	global $THIS_RET,$total_points,$current_RET,$old_student_id,$student_count,$tabindex,$count_students,$max_allowed;

	if($THIS_RET['STUDENT_ID']!=$old_student_id)
	{
		$student_count++;
		$tabindex=$student_count;
		$old_student_id = $THIS_RET['STUDENT_ID'];
	}
	else
		$tabindex += $count_students;

	if($current_RET[$THIS_RET['STUDENT_ID']][$assignment_id][1]['POINTS']=='-1')
		$points = '*';
	elseif(strpos($current_RET[$THIS_RET['STUDENT_ID']][$assignment_id][1]['POINTS'],'.'))
		$points = rtrim(rtrim($current_RET[$THIS_RET['STUDENT_ID']][$assignment_id][1]['POINTS'],'0'),'.');
	else
		$points = $current_RET[$THIS_RET['STUDENT_ID']][$assignment_id][1]['POINTS'];

	if($total_points[$assignment_id]!=0)
		if($points!='*')
			return '<TABLE border=0 cellspacing=0 cellpadding=0 class=LO_field><TR align=center><TD>'.TextInput($points,'values['.$THIS_RET['STUDENT_ID'].']['.$assignment_id.'][POINTS]','',' size=2 maxlength=7 tabindex='.$tabindex).'<HR>'.$total_points[$assignment_id].'</TD><TD>&nbsp;'.($THIS_RET['D'.$assignment_id]||$points!=''?($points>$total_points[$assignment_id]*$max_allowed?'<FONT color=red>':''):'<FONT color=gray>').Percent($points/$total_points[$assignment_id],0).($THIS_RET['D'.$assignment_id]||$points!=''?($points>$total_points[$assignment_id]*$max_allowed?'</FONT>':''):'').'<BR>&nbsp;<B>'. _makeLetterGrade($points/$total_points[$assignment_id]).'</B>'.($THIS_RET['D'.$assignment_id]||$points!=''?'':'</FONT>').'</TD></TR></TABLE>';
		else
			return '<TABLE border=0 cellspacing=0 cellpadding=0 class=LO_field><TR align=center><TD>'.TextInput($points,'values['.$THIS_RET['STUDENT_ID'].']['.$assignment_id.'][POINTS]','',' size=2 maxlength=7 tabindex='.$tabindex).'<HR>'.$total_points[$assignment_id].'</TD><TD>&nbsp;N/A<BR>&nbsp;N/A</TD></TR></TABLE>';
	else
		return '<TABLE border=0 cellspacing=0 cellpadding=0 class=LO_field><TR align=center><TD>'.TextInput($points,'values['.$THIS_RET['STUDENT_ID'].']['.$assignment_id.'][POINTS]','',' size=2 maxlength=7 tabindex='.$tabindex).'<HR>'.$total_points[$assignment_id].'</TD><TD>&nbsp;E/C</TD></TR></TABLE>';
}
?>