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
if($_REQUEST['values'] && ($_POST['values'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['values'] as $id=>$columns)
	{
		if($id!='new')
		{
			$sql = "UPDATE STUDENT_ENROLLMENT_CODES SET ";
							
			foreach($columns as $column=>$value)
			{
				$sql .= $column."='".str_replace("\'","''",$value)."',";
			}
			$sql = substr($sql,0,-1) . " WHERE ID='$id'";
			DBQuery($sql);
		}
		else
		{
			$sql = "INSERT INTO STUDENT_ENROLLMENT_CODES ";

			$fields = 'ID,SYEAR,';
			$values = db_seq_nextval('STUDENT_ENROLLMENT_CODES_SEQ').",'".UserSyear()."',";

			$go = 0;
			foreach($columns as $column=>$value)
			{
				if($value)
				{
					$fields .= $column.',';
					$values .= "'".str_replace("\'","''",$value)."',";
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			
			if($go)
				DBQuery($sql);
		}
	}
}

DrawBC("Students > ".ProgramTitle());

if($_REQUEST['modfunc']=='remove')
{
	if(DeletePrompt('enrollment code'))
	{
		DBQuery("DELETE FROM STUDENT_ENROLLMENT_CODES WHERE ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
}

if($_REQUEST['modfunc']!='remove')
{
	$sql = "SELECT ID,TITLE,SHORT_NAME,TYPE FROM STUDENT_ENROLLMENT_CODES WHERE SYEAR='".UserSyear()."' ORDER BY TITLE";
	$QI = DBQuery($sql);
	$codes_RET = DBGet($QI,array('TITLE'=>'makeTextInput','SHORT_NAME'=>'makeTextInput','TYPE'=>'makeSelectInput'));
	
	$columns = array('TITLE'=>'Title','SHORT_NAME'=>'Short Name','TYPE'=>'Type');
	$link['add']['html'] = array('TITLE'=>makeTextInput('','TITLE'),'SHORT_NAME'=>makeTextInput('','SHORT_NAME'),'TYPE'=>makeSelectInput('','TYPE'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
//	$link['remove']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=remove\");'";
	$link['remove']['variables'] = array('id'=>'ID');
	
	echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
	#DrawHeader('',SubmitButton('Save'));
	ListOutput($codes_RET,$columns,'Enrollment Code','Enrollment Codes',$link);
	echo '<br /><CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_student_enrollment_code_F1();"').'</CENTER>';
	echo '</FORM>';
}

function makeTextInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	if($name=='SHORT_NAME')
		$extra = 'size=5 maxlength=10 class=cell_floating';
		else 
		$extra = 'class=cell_floating';
	
	return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function makeSelectInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
	
	if($name=='TYPE')
		$options = array('Add'=>'Add','Drop'=>'Drop');
	
	return SelectInput($value,'values['.$id.']['.$name.']','',$options);
}

function makeCheckBoxInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	return CheckBoxInput($value,'values['.$id.']['.$name.']');
}



?>