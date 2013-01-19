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
echo "<FORM name=scheaddr id=scheaddr action=".PreparePHP_SELF()." method=POST>";
DrawBC("Scheduling > ".ProgramTitle());

if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
{
	while(!VerifyDate($start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['year_start']))
		$_REQUEST['day_start']--;
}
else
	$start_date = '01-'.strtoupper(date('M-y'));

if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
{
	while(!VerifyDate($end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end']))
		$_REQUEST['day_end']--;
}
else
	$end_date = DBDate();
if($_REQUEST['flag']!='list')
DrawHeaderHome(PrepareDateSchedule($start_date,'_start').' - '.PrepareDateSchedule($end_date,'_end'),'<INPUT type=submit class=btn_medium value=Go onclick=\'formload_ajax("scheaddr");\'>');
echo '</FORM>';
echo "<FORM name=addr id=addr action='for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_CENTRE_PDF=true&flag=list' method=POST target=_blank>";
$enrollment_RET = DBGet(DBQuery("SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,se.START_DATE AS START_DATE,NULL AS END_DATE,se.START_DATE AS DATE,se.STUDENT_ID,s.CONCAT(LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME FROM SCHEDULE se,STUDENTS s,COURSES c,COURSE_PERIODS cp WHERE c.COURSE_ID=se.COURSE_ID AND cp.COURSE_PERIOD_ID=se.COURSE_PERIOD_ID AND cp.COURSE_ID=c.COURSE_ID AND s.STUDENT_ID=se.STUDENT_ID AND se.SCHOOL_ID='".UserSchool()."' AND se.START_DATE BETWEEN '$start_date' AND '$end_date' 
							UNION SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,NULL AS START_DATE,se.END_DATE AS END_DATE,se.END_DATE AS DATE,se.STUDENT_ID,s.LAST_NAME||', '||s.FIRST_NAME AS FULL_NAME FROM SCHEDULE se,STUDENTS s,COURSES c,COURSE_PERIODS cp WHERE c.COURSE_ID=se.COURSE_ID AND cp.COURSE_PERIOD_ID=se.COURSE_PERIOD_ID AND cp.COURSE_ID=c.COURSE_ID AND s.STUDENT_ID=se.STUDENT_ID AND se.SCHOOL_ID='".UserSchool()."' AND se.END_DATE BETWEEN '$start_date' AND '$end_date'
								ORDER BY DATE DESC"),array('START_DATE'=>'ProperDate','END_DATE'=>'ProperDate'));
$columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','COURSE_TITLE'=>'Course','TITLE'=>'Course Period','START_DATE'=>'Enrolled','END_DATE'=>'Dropped');
ListOutput($enrollment_RET,$columns,'Schedule Record','Schedule Records');
if($_REQUEST['flag']!='list')
echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Add/Drop Report for Selected Students\'></CENTER>';

echo '</FORM>';
?>