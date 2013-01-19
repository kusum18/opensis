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
if(UserStaffID() || $_REQUEST['staff_id'])
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname] method=POST>";
	DrawBC("Users > Teacher Programs");


if(!UserStaffID())
	Search('staff_id','teacher');
else
{
	$profile = DBGet(DBQuery("SELECT PROFILE FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
	if($profile[1]['PROFILE']!='teacher')
	{
		unset($_SESSION['staff_id']);
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
		Search('staff_id','teacher');
	}
}

if(UserStaffID())
{
	$QI = DBQuery("SELECT DISTINCT cp.PERIOD_ID,cp.COURSE_PERIOD_ID,sp.TITLE,sp.SHORT_NAME,cp.MARKING_PERIOD_ID,cp.DAYS,sp.SORT_ORDER,c.TITLE AS COURSE_TITLE FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.SCHOOL_ID='".UserSchool()."' AND cp.TEACHER_ID='".UserStaffID()."' ORDER BY sp.SORT_ORDER ");
	$RET = DBGet($QI);
	// get the fy marking period id, there should be exactly one fy marking period
	$fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];

	if($_REQUEST['period'])
		$_SESSION['UserCoursePeriod'] = $_REQUEST['period'];

	if(!UserPeriod())
		$_SESSION['UserPeriod'] = $RET[1]['PERIOD_ID'];
	if(!UserCoursePeriod())
		$_SESSION['UserCoursePeriod'] = $RET[1]['COURSE_PERIOD_ID'];

	$period_select = "<SELECT name=period onChange='document.forms[1].submit();'>";
	foreach($RET as $period)
	{
		$period_select .= "<OPTION value=$period[COURSE_PERIOD_ID]".((UserCoursePeriod()==$period['COURSE_PERIOD_ID'])?' SELECTED':'').">".$period['SHORT_NAME'].($period['MARKING_PERIOD_ID']!=$fy_id?' '.GetMP($period['MARKING_PERIOD_ID'],'SHORT_NAME'):'').(strlen($period['DAYS'])<5?' '.$period['DAYS']:'').' - '.$period['COURSE_TITLE']."</OPTION>";
		if(UserCoursePeriod()==$period['COURSE_PERIOD_ID'])
		{
			$_SESSION['UserPeriod'] = $period['PERIOD_ID'];
		}
	}
	$period_select .= "</SELECT>";

	DrawHeader($period_select);
	echo '</FORM><BR>';
	unset($_CENTRE['DrawHeader']);

	$_CENTRE['allow_edit'] = AllowEdit($_REQUEST['modname']);
	$_CENTRE['User'] = array(1=>array('STAFF_ID'=>UserStaffID(),'NAME'=>GetTeacher(UserStaffID()),'USERNAME'=>GetTeacher(UserStaffID(),'','USERNAME'),'PROFILE'=>'teacher','SCHOOLS'=>','.UserSchool().',','SYEAR'=>UserSyear()));

	echo '<CENTER><TABLE width=100% ><TR><TD>';

	include('modules/'.$_REQUEST['include']);

	echo '</TD></TR></TABLE></CENTER>';
}
?>