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
$QI = DBQuery("SELECT PERIOD_ID,TITLE FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER ");
$RET = DBGet($QI);

$SCALE_RET = DBGet(DBQuery("SELECT * from SCHOOLS where ID = '".UserSchool()."'"));

DrawBC("Gradebook > ".ProgramTitle());

$mps = GetAllMP('PRO',UserMP());
$mps = explode(',',str_replace("'",'',$mps));
$table = '<TABLE><TR><TD valign=top><TABLE>
	</TR>
		<TD align=right valign=top><font color=gray>Calculate GPA for</font></TD>
		<TD>';

foreach($mps as $mp)
{
	if($mp!='0')
		$table .= '<INPUT type=radio name=marking_period_id value='.$mp.($mp==UserMP()?' CHECKED':'').'>'.GetMP($mp).'<BR>';
}

$table .= '</TD>
	</TR>
	<TR>
		<TD colspan = 2 align=center><font color=gray>GPA based on a scale of '.$SCALE_RET[1]['REPORTING_GP_SCALE'].'</TD>
	</TR>'.
//	<TR>
//		<TD align=right valign=top><font color=gray>Base class rank on</font></TD>
//		<TD><INPUT type=radio name=rank value=WEIGHTED_GPA CHECKED>Weighted GPA<BR><INPUT type=radio name=rank value=GPA>Unweighted GPA</TD>
'</TABLE></TD><TD width=350><small>GPA calculation modifies existing records.<BR><BR>Weighted and unweighted GPA is calculated by dividing the weighted and unweighted grade points configured for each letter grade (assigned in the Report Card Codes setup program) by the base grading scale specified in the school setup.  </small></TD></TR></TABLE>';

$go = Prompt('GPA Calculation','Calculate GPA and Class Rank',$table);
if($go)
{
	DBQuery("SELECT CALC_CUM_GPA_MP('".$_REQUEST['marking_period_id']."')");
    DBQuery("SELECT SET_CLASS_RANK_MP('".$_REQUEST['marking_period_id']."')");
    //DBQuery("DELETE FROM STUDENT_GPA_CALCULATED WHERE MARKING_PERIOD_ID='".$_REQUEST['marking_period_id']."'");
//	DBQuery("INSERT INTO STUDENT_GPA_CALCULATED 
//				(STUDENT_ID,MARKING_PERIOD_ID,WEIGHTED_GPA,GPA) 
//				SELECT sgr.STUDENT_ID,sgr.MARKING_PERIOD_ID,sgr.GPA_POINTS_WEIGHTED/sgr.DIVISOR/'$_REQUEST[weight]',
//					sgr.GPA_POINTS/sgr.DIVISOR FROM STUDENT_GPA_RUNNING sgr,STUDENT_ENROLLMENT ssm
//				WHERE 
//					sgr.STUDENT_ID=ssm.STUDENT_ID AND (('".DBDate()."' BETWEEN ssm.START_DATE AND ssm.END_DATE OR ssm.END_DATE IS NULL) AND '".DBDate()."'>=ssm.START_DATE)
//					AND ssm.SYEAR='".UserSyear()."' AND sgr.MARKING_PERIOD_ID='".$_REQUEST['marking_period_id']."' AND ssm.SCHOOL_ID='".UserSchool()."'
//			");

//	$gpa_RET = DBGet(DBQuery("SELECT sgc.STUDENT_ID,ssm.GRADE_ID,sgc.$_REQUEST[rank] AS CUM_GPA FROM STUDENT_GPA_CALCULATED sgc,STUDENT_ENROLLMENT ssm WHERE sgc.STUDENT_ID=ssm.STUDENT_ID AND ssm.SCHOOL_ID='".UserSchool()."' AND ssm.SYEAR='".UserSyear()."' AND (('".DBDate()."' BETWEEN ssm.START_DATE AND ssm.END_DATE OR ssm.END_DATE IS NULL) AND '".DBDate()."'>=ssm.START_DATE) ORDER BY sgc.$_REQUEST[rank] DESC"),array(),array('GRADE_ID'));
//	if(count($gpa_RET))
//	{
//		foreach($gpa_RET as $grade)
//		{
//			$i = 0;
//			foreach($grade as $student)
//			{
//				$i++;
//				if($prev_gpa!=$student['CUM_GPA'])
//					$rank = $i;
//	
//				DBQuery("UPDATE STUDENT_GPA_CALCULATED SET CLASS_RANK='$rank' WHERE STUDENT_ID='$student[STUDENT_ID]' AND MARKING_PERIOD_ID='".$_REQUEST['marking_period_id']."'");
//				$prev_gpa = $student['CUM_GPA'];
//			}
//		}
//	}
	
	unset($_REQUEST['delete_ok']);
	DrawHeader('<table><tr><td><IMG SRC=assets/check.gif></td><td>GPA and class rank for '.GetMP($_REQUEST['marking_period_id']).' has been calculated.</td></tr></table>');
	Prompt('GPA Calculation','Calculate GPA and Class Rank',$table);
}
?>