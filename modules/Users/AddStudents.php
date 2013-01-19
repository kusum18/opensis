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
if($_REQUEST['modfunc']=='save' && AllowEdit())
{
	$current_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM STUDENTS_JOIN_USERS WHERE STAFF_ID='".UserStaffID()."'"),array(),array('STUDENT_ID'));
	foreach($_REQUEST['student'] as $student_id=>$yes)
	{
		if(!$current_RET[$student_id]&& UserStaffID()!='')
		{
			$sql = "INSERT INTO STUDENTS_JOIN_USERS (STUDENT_ID,STAFF_ID) values('".$student_id."','".UserStaffID()."')";
			DBQuery($sql);
		}
	}
	unset($_REQUEST['modfunc']);
	$note = "The selected user's profile now includes access to the selected students.";
}
DrawBC("Users > ".ProgramTitle());

if($_REQUEST['modfunc']=='delete' && AllowEdit())
{
	if(DeletePrompt('student from that user','remove access to'))
	{
		DBQuery("DELETE FROM STUDENTS_JOIN_USERS WHERE STUDENT_ID='$_REQUEST[student_id]' AND STAFF_ID='".UserStaffID()."'");
		unset($_REQUEST['modfunc']);
	}
}

if($_REQUEST['modfunc']!='delete')
{	
	if(!UserStaffID())
		Search('staff_id','parent');
	else
	{
		$profile = DBGet(DBQuery("SELECT PROFILE FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
		if($profile[1]['PROFILE']!='parent')
		{
			unset($_SESSION['staff_id']);
			echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';			
			Search('staff_id','parent');
		}
	}
	
	if(UserStaffID())
	{
		if($_REQUEST['search_modfunc']=='list')
		{
			echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
			DrawHeader('',SubmitButton('Add Selected Students','','class=btn_large'));
		}
	}
	
	if($note)
		DrawHeader('<IMG SRC=assets/check.gif>'.$note);
	if(UserStaffID())
	{
		echo '<CENTER><TABLE><TR><TD valign=top>';
		DrawHeader("<div><A class=big_font><img src=\"themes/Blue/expanded_view.png\" />Associate</A></div><div class=break ></div>",$extra['header_right']);
		$current_RET = DBGet(DBQuery("SELECT u.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME FROM STUDENTS_JOIN_USERS u,STUDENTS s WHERE s.STUDENT_ID=u.STUDENT_ID AND u.STAFF_ID='".UserStaffID()."'"));
		$link['remove'] = array('link'=>"Modules.php?modname=$_REQUEST[modname]&modfunc=delete",'variables'=>array('student_id'=>'STUDENT_ID'));
		#$link['remove'] = array('link'=>"#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=delete\");'",'variables'=>array('student_id'=>'STUDENT_ID'));
	//	$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&mp_term=FY\");'";		
		ListOutput($current_RET,array('FULL_NAME'=>'Students'),'','',$link,array(),array('search'=>false));
		echo '</TD><TD valign=top>';
		
		$extra['link'] = array('FULL_NAME'=>false);
		$extra['SELECT'] = ",NULL AS CHECKBOX";
		$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
		$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
		$extra['new'] = true;
		$extra['options']['search'] = false;
	
		if(AllowEdit())
			Search('student_id',$extra);
		
		echo '</TD></TR></TABLE></CENTER>';
		
		if($_REQUEST['search_modfunc']=='list')
			echo "<BR><CENTER>".SubmitButton('Add Selected Students','','class=btn_large')."</CENTER></FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

	return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}

?>