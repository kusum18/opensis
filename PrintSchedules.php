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
if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['st_arr']))
	{
	$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
	$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";

	if($_REQUEST['day_include_active_date'] && $_REQUEST['month_include_active_date'] && $_REQUEST['year_include_active_date'])
	{
		$date = $_REQUEST['day_include_active_date'].'-'.$_REQUEST['month_include_active_date'].'-'.$_REQUEST['year_include_active_date'];
		$date_extra = 'OR (\''.$date.'\' >= sr.START_DATE AND sr.END_DATE IS NULL)';
	}
	else
	{
		$date = DBDate();
		$date_extra = 'OR sr.END_DATE IS NULL';
	}
	$columns = array('PERIOD_TITLE'=>'Period - Teacher','MARKING_PERIOD_ID'=>'Term','DAYS'=>'Days','ROOM'=>'Room','COURSE_TITLE'=>'Course','COURSE_WEIGHT'=>'Weight');

	$extra['SELECT'] .= ',c.TITLE AS COURSE_TITLE,p_cp.TITLE AS PERIOD_TITLE,sr.MARKING_PERIOD_ID,p_cp.DAYS,p_cp.ROOM';
	$extra['FROM'] .= ' LEFT OUTER JOIN SCHEDULE sr ON (sr.STUDENT_ID=ssm.STUDENT_ID),COURSES c,COURSE_PERIODS p_cp,SCHOOL_PERIODS sp ';
	$extra['WHERE'] .= " AND p_cp.PERIOD_ID=sp.PERIOD_ID AND ssm.SYEAR=sr.SYEAR AND sr.COURSE_ID=c.COURSE_ID AND sr.COURSE_PERIOD_ID=p_cp.COURSE_PERIOD_ID  AND ('$date' BETWEEN sr.START_DATE AND sr.END_DATE $date_extra)";
	if($_REQUEST['mp_id'])
		$extra['WHERE'] .= ' AND sr.MARKING_PERIOD_ID IN ('.GetAllMP(GetMPTable(GetMP($_REQUEST['mp_id'],'TABLE')),$_REQUEST['mp_id']).')';

	$extra['functions'] = array('MARKING_PERIOD_ID'=>'GetMP','DAYS'=>'_makeDays');
	$extra['group'] = array('STUDENT_ID');
	$extra['ORDER'] = ',sp.SORT_ORDER';
	if($_REQUEST['mailing_labels']=='Y')
		$extra['group'][] = 'ADDRESS_ID';
	Widgets('mailing_labels');

	$RET = GetStuList($extra);

	if(count($RET))
	{
		$handle = PDFStart();
		foreach($RET as $student_id=>$courses)
		{
			if($_REQUEST['mailing_labels']=='Y')
			{
				foreach($courses as $address)
				{
					echo '<BR><BR><BR>';
					unset($_CENTRE['DrawHeader']);
					DrawHeader(Config('TITLE').' Student Schedule');
					DrawHeader($address[1]['FULL_NAME'],$address[1]['STUDENT_ID']);
					DrawHeader($address[1]['GRADE_ID']);
					DrawHeader(GetSchool(UserSchool()));
					DrawHeader(ProperDate($date),$_REQUEST['mp_id']?GetMP($_REQUEST['mp_id']):'');

					echo '<BR><BR><TABLE width=100%><TR><TD width=50> &nbsp; </TD><TD>'.$address[1]['MAILING_LABEL'].'</TD></TR></TABLE><BR>';

					ListOutput($address,$columns,'Course','Courses',array(),array(),array('center'=>false,'print'=>false));
					echo '<!-- NEW PAGE -->';
				}
			}
			else
			{
				unset($_CENTRE['DrawHeader']);
				DrawHeader(Config('TITLE').' Student Schedule');
				DrawHeader($courses[1]['FULL_NAME'],$courses[1]['STUDENT_ID']);
				DrawHeader($courses[1]['GRADE_ID']);
				DrawHeader(GetSchool(UserSchool()));
				DrawHeader(ProperDate($date),$_REQUEST['mp_id']?GetMP($_REQUEST['mp_id']):'');

				ListOutput($courses,$columns,'Course','Courses',array(),array(),array('center'=>false,'print'=>false));
				echo '<!-- NEW PAGE -->';
			}
		}
		PDFStop($handle);
	}
	else
		BackPrompt('No Students were found.');
	}
	else
		BackPrompt('You must choose at least one student.');
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Scheduling > ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		$mp_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,1 AS TBL FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,2 AS TBL FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' UNION SELECT MARKING_PERIOD_ID,TITLE,SORT_ORDER,3 AS TBL FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY TBL,SORT_ORDER"));
		$mp_select = '<SELECT name=mp_id><OPTION value="">N/A';
		foreach($mp_RET as $mp)
			$mp_select .= '<OPTION value='.$mp['MARKING_PERIOD_ID'].'>'.$mp['TITLE'];
		$mp_select .= '</SELECT>';

	//	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_CENTRE_PDF=true method=POST>";
		echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_CENTRE_PDF=true method=POST target=_blank>";
		#$extra['header_right'] = '<INPUT type=submit value=\'Create Schedules for Selected Students\'>';
		PopTable_wo_header ('header');
		$extra['extra_header_left'] = '<TABLE>';
		$extra['extra_header_left'] .= '<TR><TD align=right width=120>Marking Period</TD><TD>'.$mp_select.'</TD></TR>';
		$extra['extra_header_left'] .= '<TR><TD align=right width=120>Include only courses active as of</TD><TD>'.PrepareDate('','_include_active_date').'</TD></TR>';
		Widgets('mailing_labels',true);
		$extra['extra_header_left'] .= $extra['search'];
		$extra['search'] = '';
		$extra['extra_header_left'] .= '</TABLE>';
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;
	//$extra['force_search'] = true;

	Widgets('request');
	Widgets('course');

	Search('student_id',$extra);

	if($_REQUEST['search_modfunc']=='list')
	{
		PopTable ('footer');
		echo '<BR><CENTER><INPUT type=submit class=btn_xlarge value=\'Create Schedules for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

function _makeDays($value,$column)
{
	foreach(array('U','M','T','W','H','F','S') as $day)
		if(strpos($value,$day)!==false)
			$return .= $day;
		else
			$return .= '-';
	return $return;
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}
?>
