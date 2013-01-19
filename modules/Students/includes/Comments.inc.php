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
//$_CENTRE['allow_edit'] = true;
if($_REQUEST['modfunc']=='update')
{
	$existing_RET = DBGet(DBQuery("SELECT STUDENT_ID FROM STUDENT_MP_COMMENTS WHERE STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."' AND MARKING_PERIOD_ID='".GetParentMP('SEM',UserMP())."'"));
	if(!$existing_RET)
		DBQuery("INSERT INTO STUDENT_MP_COMMENTS (SYEAR,STUDENT_ID,MARKING_PERIOD_ID) values('".UserSyear()."','".UserStudentID()."','".GetParentMP('SEM',UserMP())."')");
	SaveData(array('STUDENT_MP_COMMENTS'=>"STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."' AND MARKING_PERIOD_ID='".GetParentMP('SEM',UserMP())."'"),'',array('COMMENT'=>'Comment'));
	//unset($_SESSION['_REQUEST_vars']['modfunc']);
	//unset($_SESSION['_REQUEST_vars']['values']);
}
if(!$_REQUEST['modfunc'])
{
	$comments_RET = DBGet(DBQuery("SELECT COMMENT FROM STUDENT_MP_COMMENTS WHERE STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."' AND MARKING_PERIOD_ID='".GetParentMP('SEM',UserMP())."'"));
	echo '<TABLE>';
	echo '<TR>';
	echo '<TD valign=bottom>';
	echo '<b>'.$mp['TITLE'].' Comments</b><BR>';
	echo '<TEXTAREA id=textarea name=values[STUDENT_MP_COMMENTS]['.UserStudentID().'][COMMENT] cols=66 rows=22'.(AllowEdit()?'':' readonly').' onkeypress="document.getElementById(\'chars_left\').innerHTML=(1121-this.value.length); if(this.value.length>1121) {document.getElementById(\'chars_left\').innerHTML=\'Fewer than 0\'}">';
	echo $comments_RET[1]['COMMENT'];
	echo '</TEXTAREA>';
	echo '<table><tr><td><IMG SRC=assets/comment_button.gif onload="document.getElementById(\'chars_left\').innerHTML=1121-document.getElementById(\'textarea\').value.length";></td><td><small><div id=chars_left>1121</div></small></td><td><small>characters remaining.<small></td></tr></table>';
	echo '</TD>';
	//echo '<TR><TD align=center><INPUT type=submit value=Save></TD></TR>';
	echo '</TR></TABLE>';
	echo "<br><b>*If more than one teacher will be adding comments for this student:</b><br>";
	echo "<ul><li>Type your name above the comments you enter.</li>";
	echo "<li>Leave space for other teachers to enter their comments.</li></ul>";

	$_REQUEST['category_id'] = '4';
	$separator = '<hr>';
	include('modules/Students/includes/Other_Info.inc.php');
}
?>