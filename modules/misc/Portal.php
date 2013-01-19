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
if(!UserSyear())
{
	$_SESSION['UserSyear'] = $DefaultSyear;
}

while(!UserSyear())
{
	session_write_close();
	session_start();
}
#DrawHeader(Config('TITLE'),'Centre v'.$CentreVersion);

$current_hour = date('H');
#DrawHeader('<FONT SIZE=+1>Good '.($current_hour<12?'Morning':($current_hour<18?'Afternoon':'Evening')).', '.User('NAME').'!</FONT>');


if($_SESSION['LAST_LOGIN'])

	$welcome .= 'User: '. User('NAME').' | Last login: '.ProperDate(substr($_SESSION['LAST_LOGIN'],0,10)).' at ' .substr($_SESSION['LAST_LOGIN'],10);
if($_REQUEST['failed_login'])
	$welcome .= ' | <span class=red >'.$_REQUEST['failed_login'].'</b> failed login attempts</span>';

switch (User('PROFILE'))
{
	case 'admin':
		DrawBC ($welcome.' | Role: Administrator');

		$notes_RET = DBGet(DBQuery("SELECT s.TITLE AS SCHOOL,pn.PUBLISHED_DATE,CONCAT('<B>',pn.TITLE,'</B>') AS TITLE,pn.CONTENT FROM PORTAL_NOTES pn,SCHOOLS s,STAFF st WHERE pn.SYEAR='".UserSyear()."' AND pn.START_DATE<=CURRENT_DATE AND (pn.END_DATE>=CURRENT_DATE OR pn.END_DATE IS NULL) AND st.STAFF_ID='".User('STAFF_ID')."' AND (st.SCHOOLS IS NULL OR FIND_IN_SET(pn.SCHOOL_ID,st.SCHOOLS)>0) AND (st.PROFILE_ID IS NULL AND FIND_IN_SET('admin', pn.PUBLISHED_PROFILES)>0 OR st.PROFILE_ID IS NOT NULL AND FIND_IN_SET(st.PROFILE_ID,pn.PUBLISHED_PROFILES)>0) AND s.ID=pn.SCHOOL_ID ORDER BY pn.SORT_ORDER,pn.PUBLISHED_DATE DESC"),array('PUBLISHED_DATE'=>'ProperDate','CONTENT'=>'_nl2br'));

		if(count($notes_RET))
		{
			echo '<div>';
			ListOutput($notes_RET,array('PUBLISHED_DATE'=>'Date Posted','TITLE'=>'Title','CONTENT'=>'Note','SCHOOL'=>'School'),'Note','Notes',array(),array(),array('save'=>false,'search'=>false));
			echo '</div>';
		}

		$events_RET = DBGet(DBQuery("SELECT ce.TITLE,ce.DESCRIPTION,ce.SCHOOL_DATE,s.TITLE AS SCHOOL FROM CALENDAR_EVENTS ce,SCHOOLS s,STAFF st WHERE ce.SCHOOL_DATE BETWEEN CURRENT_DATE AND CURRENT_DATE+30 AND ce.SYEAR='".UserSyear()."' AND st.STAFF_ID='".User('STAFF_ID')."' AND (st.SCHOOLS IS NULL OR FIND_IN_SET(ce.SCHOOL_ID,st.SCHOOLS)>0) AND s.ID=ce.SCHOOL_ID ORDER BY ce.SCHOOL_DATE,s.TITLE"),array('SCHOOL_DATE'=>'ProperDate'));
		

		if(count($events_RET))
		{
			echo '<p>';
			ListOutput($events_RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Event','DESCRIPTION'=>'Description','SCHOOL'=>'School'),'Upcoming Event','Upcoming Events',array(),array(),array('save'=>false,'search'=>false));
			echo '</p>';
		}

		if(Preferences('HIDE_ALERTS')!='Y')
		{
		// warn if missing attendance
		$RET = DBGET(DBQuery("SELECT DISTINCT s.TITLE AS SCHOOL,acc.SCHOOL_DATE,cp.TITLE FROM ATTENDANCE_CALENDAR acc,COURSE_PERIODS cp,SCHOOL_PERIODS sp,SCHOOLS s,STAFF st,SCHEDULE sch WHERE acc.SYEAR='".UserSyear()."' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND st.STAFF_ID='".User('STAFF_ID')."' AND (st.SCHOOLS IS NULL OR FIND_IN_SET(acc.SCHOOL_ID,st.SCHOOLS)>0) AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.FILLED_SEATS<>0 AND acc.SCHOOL_DATE>=sch.START_DATE AND acc.SCHOOL_DATE<'".DBDate()."'
		AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE)
		AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND FIND_IN_SET(DAYOFWEEK( acc.SCHOOL_DATE),cp.DAYS)>0
			OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)
		AND NOT EXISTS(SELECT '' FROM ATTENDANCE_COMPLETED ac WHERE ac.SCHOOL_DATE=acc.SCHOOL_DATE AND ac.STAFF_ID=cp.TEACHER_ID AND ac.PERIOD_ID=cp.PERIOD_ID) AND cp.DOES_ATTENDANCE='Y' AND s.ID=acc.SCHOOL_ID ORDER BY cp.TITLE,acc.SCHOOL_DATE"),array('SCHOOL_DATE'=>'ProperDate'));
		if (count($RET))
		{
			echo '<p><font color=#FF0000><b>Warning!!</b></font> - Teachers have missing attendance data:';
			ListOutput($RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Period - Teacher','SCHOOL'=>'School'),'Period','Periods',array(),array(),array('save'=>false,'search'=>false));
			echo '</p>';
		}
		}

		if($CentreModules['Food_Service'] && Preferences('HIDE_ALERTS')!='Y')
		{
		// warn if negative food service balance
		$staff = DBGet(DBQuery('SELECT (SELECT STATUS FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS STATUS,(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE FROM STAFF s WHERE s.STAFF_ID='.User('STAFF_ID')));
		$staff = $staff[1];
		if($staff['BALANCE'] && $staff['BALANCE']<0)
			echo '<p><font color=#FF0000><b>Warning!!</b></font> - You have a <b>negative</b> food service balance of <font color=#FF0000>'.$staff['BALANCE'].'</font></p>';

		// warn if students with way low food service balances
		$extra['SELECT'] = ',fssa.STATUS,fsa.BALANCE';
		$extra['FROM'] = ',FOOD_SERVICE_ACCOUNTS fsa,FOOD_SERVICE_STUDENT_ACCOUNTS fssa';
		$extra['WHERE'] = ' AND fssa.STUDENT_ID=s.STUDENT_ID AND fsa.ACCOUNT_ID=fssa.ACCOUNT_ID AND fssa.STATUS IS NULL AND fsa.BALANCE<\'-10\'';
		$_REQUEST['_search_all_schools'] = 'Y';
		$RET = GetStuList($extra);
		if (count($RET))
		{
			echo '<p><font color=#FF0000><b>Warning!!</b></font> - Students have food service balances below -$10.00:';
			ListOutput($RET,array('FULL_NAME'=>'Student','GRADE_ID'=>'Grade','BALANCE'=>'Balance'),'Student','Students',array(),array(),array('save'=>false,'search'=>false));
			echo '</p>';
  		}
		}

		#echo '<p>&nbsp;Happy administrating...</p>';
	break;

	case 'teacher':
		DrawBC ($welcome.' | Role: Teacher');

		$notes_RET = DBGet(DBQuery("SELECT s.TITLE AS SCHOOL,pn.PUBLISHED_DATE,CONCAT('<B>',pn.TITLE,'</B>') AS TITLE,pn.CONTENT FROM PORTAL_NOTES pn,SCHOOLS s,STAFF st WHERE pn.SYEAR='".UserSyear()."' AND pn.START_DATE<=CURRENT_DATE AND (pn.END_DATE>=CURRENT_DATE OR pn.END_DATE IS NULL) AND st.STAFF_ID='".User('STAFF_ID')."' AND FIND_IN_SET(pn.SCHOOL_ID,st.SCHOOLS)>0 AND (st.SCHOOLS IS NULL OR FIND_IN_SET(pn.SCHOOL_ID,st.SCHOOLS)>0) AND (st.PROFILE_ID IS NULL AND FIND_IN_SET('teacher',pn.PUBLISHED_PROFILES)>0 OR st.PROFILE_ID IS NOT NULL AND FIND_IN_SET(st.PROFILE_ID,pn.PUBLISHED_PROFILES)>0) AND s.ID=pn.SCHOOL_ID ORDER BY pn.SORT_ORDER,pn.PUBLISHED_DATE DESC"),array('PUBLISHED_DATE'=>'ProperDate','CONTENT'=>'_nl2br'));

		if(count($notes_RET))
		{
			echo '<p>';
			ListOutput($notes_RET,array('PUBLISHED_DATE'=>'Date Posted','TITLE'=>'Title','CONTENT'=>'Note','SCHOOL'=>'School'),'Note','Notes',array(),array(),array('save'=>false,'search'=>false));
			echo '</p>';
		}

		$events_RET = DBGet(DBQuery("SELECT ce.TITLE,ce.DESCRIPTION,ce.SCHOOL_DATE,s.TITLE AS SCHOOL FROM CALENDAR_EVENTS ce,SCHOOLS s WHERE ce.SCHOOL_DATE BETWEEN CURRENT_DATE AND CURRENT_DATE+30 AND ce.SYEAR='".UserSyear()."' AND FIND_IN_SET(ce.SCHOOL_ID,(SELECT SCHOOLS FROM STAFF WHERE STAFF_ID='".User('STAFF_ID')."'))>0 AND s.ID=ce.SCHOOL_ID ORDER BY ce.SCHOOL_DATE,s.TITLE"),array('SCHOOL_DATE'=>'ProperDate'));

		if(count($events_RET))
		{
			echo '<p>';
			ListOutput($events_RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Event','DESCRIPTION'=>'Description','SCHOOL'=>'School'),'Upcoming Event','Upcoming Events',array(),array(),array('save'=>false,'search'=>false));
			echo '</p>';
		}

		if(Preferences('HIDE_ALERTS')!='Y')
		{
		// warn if missing attendance
		$RET = DBGET(DBQuery("SELECT acc.SCHOOL_DATE,cp.TITLE FROM ATTENDANCE_CALENDAR acc,COURSE_PERIODS cp,SCHOOL_PERIODS sp WHERE acc.SYEAR='".UserSyear()."' AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) AND cp.SCHOOL_ID=acc.SCHOOL_ID AND cp.SYEAR=acc.SYEAR AND acc.SCHOOL_DATE<'".DBDate()."' AND cp.CALENDAR_ID=acc.CALENDAR_ID AND cp.TEACHER_ID='".User('STAFF_ID')."'
		AND cp.MARKING_PERIOD_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE UNION SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SCHOOL_ID=acc.SCHOOL_ID AND acc.SCHOOL_DATE BETWEEN START_DATE AND END_DATE)
		AND sp.PERIOD_ID=cp.PERIOD_ID AND (sp.BLOCK IS NULL AND FIND_IN_SET(DAYOFWEEK(acc.SCHOOL_DATE) ,cp.DAYS)>0
			OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK)
		AND NOT EXISTS(SELECT '' FROM ATTENDANCE_COMPLETED ac WHERE ac.SCHOOL_DATE=acc.SCHOOL_DATE AND ac.STAFF_ID=cp.TEACHER_ID AND ac.PERIOD_ID=cp.PERIOD_ID) AND cp.DOES_ATTENDANCE='Y' ORDER BY cp.TITLE,acc.SCHOOL_DATE"),array('SCHOOL_DATE'=>'ProperDate'));
		if (count($RET))
		{
			echo '<p><font color=#FF0000><b>Warning!!</b></font> - You have missing attendance data:';
			ListOutput($RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Period - Teacher'),'Period','Periods',array(),array(),array('save'=>false,'search'=>false));
			echo '</p>';
		}
		}

		if($CentreModules['Food_Service'] && Preferences('HIDE_ALERTS')!='Y')
		{
		// warn if negative food service balance
		$staff = DBGet(DBQuery('SELECT (SELECT STATUS FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS STATUS,(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE FROM STAFF s WHERE s.STAFF_ID='.User('STAFF_ID')));
		$staff = $staff[1];
		if($staff['BALANCE'] && $staff['BALANCE']<0)
			echo '<p><font color=#FF0000><b>Warning!!</b></font> - You have a <b>negative</b> food service balance of <font color=#FF0000>'.$staff['BALANCE'].'</font></p>';
		}

		#echo '<p>&nbsp;Happy teaching...</p>';
	break;

	case 'parent':
		DrawBC ($welcome.' | Role: Parent');

		$notes_RET = DBGet(DBQuery("SELECT s.TITLE AS SCHOOL,pn.PUBLISHED_DATE,pn.TITLE,pn.CONTENT FROM PORTAL_NOTES pn,SCHOOLS s,STAFF st WHERE pn.SYEAR='".UserSyear()."' AND pn.START_DATE<=CURRENT_DATE AND (pn.END_DATE>=CURRENT_DATE OR pn.END_DATE IS NULL) AND st.STAFF_ID='".User('STAFF_ID')."' AND pn.SCHOOL_ID IN (SELECT DISTINCT SCHOOL_ID FROM STUDENTS_JOIN_USERS sju, STUDENT_ENROLLMENT se WHERE sju.STAFF_ID='".User('STAFF_ID')."' AND se.SYEAR=pn.SYEAR AND se.STUDENT_ID=sju.STUDENT_ID AND se.START_DATE<=CURRENT_DATE AND (se.END_DATE>=CURRENT_DATE OR se.END_DATE IS NULL)) AND (st.SCHOOLS IS NULL OR FIND_IN_SET(pn.SCHOOL_ID,st.SCHOOLS)>0) AND (st.PROFILE_ID IS NULL AND FIND_IN_SET('parent',pn.PUBLISHED_PROFILES)>0 OR st.PROFILE_ID IS NOT NULL AND FIND_IN_SET(st.PROFILE_ID,pn.PUBLISHED_PROFILES)>0) AND s.ID=pn.SCHOOL_ID ORDER BY pn.SORT_ORDER,pn.PUBLISHED_DATE DESC"),array('PUBLISHED_DATE'=>'ProperDate','CONTENT'=>'_nl2br'));

		if(count($notes_RET))
		{
			echo '<p>';
			ListOutput($notes_RET,array('PUBLISHED_DATE'=>'Date Posted','TITLE'=>'Title','CONTENT'=>'Note','SCHOOL'=>'School'),'Note','Notes',array(),array(),array('save'=>false,'search'=>false));
			echo '</p>';
		}

		$events_RET = DBGet(DBQuery("SELECT ce.TITLE,ce.SCHOOL_DATE,ce.DESCRIPTION,s.TITLE AS SCHOOL FROM CALENDAR_EVENTS ce,SCHOOLS s WHERE ce.SCHOOL_DATE BETWEEN CURRENT_DATE AND CURRENT_DATE+30 AND ce.SYEAR='".UserSyear()."' AND ce.SCHOOL_ID IN (SELECT DISTINCT SCHOOL_ID FROM STUDENTS_JOIN_USERS sju, STUDENT_ENROLLMENT se WHERE sju.STAFF_ID='".User('STAFF_ID')."' AND se.SYEAR=ce.SYEAR AND se.STUDENT_ID=sju.STUDENT_ID AND se.START_DATE<=CURRENT_DATE AND (se.END_DATE>=CURRENT_DATE OR se.END_DATE IS NULL)) AND s.ID=ce.SCHOOL_ID ORDER BY ce.SCHOOL_DATE,s.TITLE"),array('SCHOOL_DATE'=>'ProperDate'));

		if(count($events_RET))
		{
			echo '<p>';
			ListOutput($events_RET,array('SCHOOL_DATE'=>'Date','TITLE'=>'Event','DESCRIPTION'=>'Description','SCHOOL'=>'School'),'Upcoming Event','Upcoming Events',array(),array(),array('save'=>false,'search'=>false));
			echo '</p>';
		}

		if($CentreModules['Food_Service'] && Preferences('HIDE_ALERTS')!='Y')
		{
		// warn if students with low food service balances
		$extra['SELECT'] = ',fssa.STATUS,fsa.ACCOUNT_ID,CONCAT(\'$\',fsa.BALANCE) AS BALANCE,CONCAT(\'$\',16.5-fsa.BALANCE) AS DEPOSIT';
		$extra['FROM'] = ',FOOD_SERVICE_ACCOUNTS fsa,FOOD_SERVICE_STUDENT_ACCOUNTS fssa';
		$extra['WHERE'] = 'AND fssa.STUDENT_ID=s.STUDENT_ID AND fsa.ACCOUNT_ID=fssa.ACCOUNT_ID AND fssa.STATUS IS NULL AND fsa.BALANCE<\'5\'';
		$extra['ASSOCIATED'] = User('STAFF_ID');
		$RET = GetStuList($extra);
		if (count($RET))
		{
			echo '<p><font color=#FF0000><b>Warning!!</b></font> - You have students with food service balance below $5.00 - please deposit at least the Minimum Deposit into you children\'s accounts.';
			ListOutput($RET,array('FULL_NAME'=>'Student','GRADE_ID'=>'Grade','ACCOUNT_ID'=>'AccountID','BALANCE'=>'Balance','DEPOSIT'=>'Minimum Deposit'),'Student','Students',array(),array(),array('save'=>false,'search'=>false));
			echo '</p>';
		}

		// warn if negative food service balance
		$staff = DBGet(DBQuery('SELECT (SELECT STATUS FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS STATUS,(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE FROM STAFF s WHERE s.STAFF_ID='.User('STAFF_ID')));
		$staff = $staff[1];
		if($staff['BALANCE'] && $staff['BALANCE']<0)
			echo '<p><font color=#FF0000><b>Warning!!</b></font> - You have a <b>negative</b> food service balance of <font color=#FF0000>'.$staff['BALANCE'].'</font></p>';
		}

		#echo '<p>&nbsp;Happy parenting...</p>';
	break;

  case 'student':
		DrawBC ($welcome.' | Role: Student');

		$notes_RET = DBGet(DBQuery("SELECT s.TITLE AS SCHOOL,pn.PUBLISHED_DATE,pn.TITLE,pn.CONTENT FROM PORTAL_NOTES pn,SCHOOLS s WHERE pn.SYEAR='".UserSyear()."' AND pn.START_DATE<=CURRENT_DATE AND (pn.END_DATE>=CURRENT_DATE OR pn.END_DATE IS NULL) AND pn.SCHOOL_ID='".UserSchool()."' AND  position(',0,' IN pn.PUBLISHED_PROFILES)>0 AND s.ID=pn.SCHOOL_ID ORDER BY pn.SORT_ORDER,pn.PUBLISHED_DATE DESC"),array('PUBLISHED_DATE'=>'ProperDate','CONTENT'=>'_nl2br'));

		if(count($notes_RET))
		{
			echo '<p>';
			ListOutput($notes_RET,array('PUBLISHED_DATE'=>'Date Posted','TITLE'=>'Title','CONTENT'=>'Note'),'Note','Notes',array(),array(),array('save'=>false,'search'=>false));
			echo '</p>';
		}

		$events_RET = DBGet(DBQuery("SELECT TITLE,SCHOOL_DATE,DESCRIPTION FROM CALENDAR_EVENTS WHERE SCHOOL_DATE BETWEEN CURRENT_DATE AND CURRENT_DATE+30 AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"),array('SCHOOL_DATE'=>'ProperDate'));

		if(count($events_RET))
		{
			echo '<p>';
			ListOutput($events_RET,array('TITLE'=>'Event','SCHOOL_DATE'=>'Date','DESCRIPTION'=>'Description'),'Upcoming Event','Upcoming Events',array(),array(),array('save'=>false,'search'=>false));
			echo '</p>';
		}

		//echo '<p>&nbsp;Happy learning...</p>';
	break;
}

function _nl2br($value,$column)
{
 	return nl2br($value);
}
?>
