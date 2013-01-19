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

if($_REQUEST['modfunc']=='delete' && User('PROFILE')=='admin')
{
	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
		echo '</FORM>';
	if(DeletePrompt($_REQUEST['title']))
	{
		DBQuery("DELETE FROM $_REQUEST[table] WHERE ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
}

if($_REQUEST['modfunc']=='update')
	unset($_REQUEST['modfunc']);

if(!$_REQUEST['modfunc'])
{
	echo '<div style="position: absolute; z-index:1000; width: 495px; height: 300px; visibility:hidden; background-image:url(\'assets/comment_background.gif\');" id="dc"></div>';
	
	echo '<TABLE width=100% border=0 cellpadding=0 cellspacing=0>';
	echo '<TR><TD valign=top>';
	$_REQUEST['category_id'] = 2;
	echo '<div class=hseparator><b>Medical Information</b></div><div class=clear></div>';
	include('modules/Students/includes/Other_Info.inc.php');
	echo '</TD></TR><TR><TD valign=top>';
	echo '<TABLE width=100%><TR><TD valign=top>';
	$table = 'STUDENT_MEDICAL';
	$functions = array('TYPE'=>'_makeType','MEDICAL_DATE'=>'_makeDate','COMMENTS'=>'_makeComments');
	$med_RET = DBGet(DBQuery("SELECT ID,TYPE,MEDICAL_DATE,COMMENTS FROM STUDENT_MEDICAL WHERE STUDENT_ID='".UserStudentID()."' ORDER BY MEDICAL_DATE,TYPE"),$functions);
	$columns = array('TYPE'=>'','MEDICAL_DATE'=>'Date','COMMENTS'=>'Comments');
	$link['add']['html'] = array('TYPE'=>_makeType('',''),'MEDICAL_DATE'=>_makeDate('','MEDICAL_DATE'),'COMMENTS'=>_makeComments('','COMMENTS'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&table=STUDENT_MEDICAL&title=".urlencode('immunization or physical');
	$link['remove']['variables'] = array('id'=>'ID');

	if(count($med_RET)==0)
		$plural = 'Immunizations or Physicals';
	else
		$plural = 'Immunizations and Physicals';

	echo '<div class=hseparator><b>Immunization/Physical Record</b></div>';
	ListOutput($med_RET,$columns,'Immunization or Physical',$plural,$link,array(),array('search'=>false));
	echo '</TD></tr><TD valign=top>';
	$table = 'STUDENT_MEDICAL_ALERTS';
	$functions = array('TITLE'=>'_makeComments');
	$med_RET = DBGet(DBQuery("SELECT ID,TITLE FROM STUDENT_MEDICAL_ALERTS WHERE STUDENT_ID='".UserStudentID()."' ORDER BY ID"),$functions);
	$columns = array('TITLE'=>'Medical Alert');
	$link['add']['html'] = array('TITLE'=>_makeComments('','TITLE'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&table=STUDENT_MEDICAL_ALERTS&title=".urlencode('medical alert');
	$link['remove']['variables'] = array('id'=>'ID');
	echo '<div class=clear></div><div class=hseparator><b>Medical Alert</b></div>';
	ListOutput($med_RET,$columns,'Medical Alert','Medical Alerts',$link,array(),array('search'=>false));
	
	echo '</TD></TR></TABLE>';
	echo '</TD>';
	// IMAGE
	echo '</TR>';

	echo '<TR><TD valign=top>';
	$table = 'STUDENT_MEDICAL_VISITS';
	$functions = array('SCHOOL_DATE'=>'_makeDate','TIME_IN'=>'_makeComments','TIME_OUT'=>'_makeComments','REASON'=>'_makeComments','RESULT'=>'_makeComments','COMMENTS'=>'_makeLongComments');
	$med_RET = DBGet(DBQuery("SELECT ID,SCHOOL_DATE,TIME_IN,TIME_OUT,REASON,RESULT,COMMENTS FROM STUDENT_MEDICAL_VISITS WHERE STUDENT_ID='".UserStudentID()."' ORDER BY SCHOOL_DATE"),$functions);
	$columns = array('SCHOOL_DATE'=>'Date','TIME_IN'=>'Time In','TIME_OUT'=>'Time Out','REASON'=>'Reason','RESULT'=>'Result','COMMENTS'=>'Comments');
	$link['add']['html'] = array('SCHOOL_DATE'=>_makeDate('','SCHOOL_DATE'),'TIME_IN'=>_makeComments('','TIME_IN'),'TIME_OUT'=>_makeComments('','TIME_OUT'),'REASON'=>_makeComments('','REASON'),'RESULT'=>_makeComments('','RESULT'),'COMMENTS'=>_makeLongComments('','COMMENTS'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&table=STUDENT_MEDICAL_VISITS&title=".urlencode('visit');
	$link['remove']['variables'] = array('id'=>'ID');
	echo '<div class=clear></div><div class=hseparator><b>Nurse Visit Record</b></div>';
	ListOutput($med_RET,$columns,'Nurse Visit','Nurse Visits',$link,array(),array('search'=>false));
	echo '</TD></TR></TABLE>';
}

?>