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
$fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,SELECT_OPTIONS,DEFAULT_SELECTION,REQUIRED FROM CUSTOM_FIELDS WHERE CATEGORY_ID='$_REQUEST[category_id]' ORDER BY SORT_ORDER,TITLE"));

if(UserStudentID())
{
	$custom_RET = DBGet(DBQuery("SELECT * FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
	$value = $custom_RET[1];
}

if(count($fields_RET))
echo '<TABLE cellpadding=5>';
$i = 1;
foreach($fields_RET as $field)
{
	switch($field['TYPE'])
	{
		case 'text':
			echo '<TR>';
			echo '<td>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],'','class=cell_medium');
			echo '</TD>';
			echo '</TR>';
			break;

		case 'autos':
			echo '<TR>';
			echo '<td>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],'','class=cell_medium');
			echo '</TD>';
			echo '</TR>';
			break;

		case 'edits':
			echo '<TR>';
			echo '<td>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeAutoSelectInput('CUSTOM_'.$field['ID'],'','class=cell_medium');
			echo '</TD>';
			echo '</TR>';
			break;

		case 'numeric':
			echo '<TR>';
			echo '<td>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeTextInput('CUSTOM_'.$field['ID'],'','size=5 maxlength=10 class=cell_medium');
			echo '</TD>';
			echo '</TR>';
			break;

		case 'date':
			echo '<TR>';
			echo '<td>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeDateInput_mod('CUSTOM_'.$field['ID'],'','class=cell_medium');
			echo '</TD>';
			echo '</TR>';
			break;

		case 'codeds':
		case 'select':
			echo '<TR>';
			echo '<td>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeSelectInput('CUSTOM_'.$field['ID'],'');
			echo '</TD>';
			echo '</TR>';
			break;

		case 'multiple':
			echo '<TR>';
			echo '<td>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeMultipleInput('CUSTOM_'.$field['ID'],'','class=cell_medium');
			echo '</TD>';
			echo '</TR>';
			break;

		case 'radio':
			echo '<TR>';
			echo '<td>'.$field['TITLE'].'</td><td>:</td><td>';
			echo _makeCheckboxInput('CUSTOM_'.$field['ID'],'','class=cell_medium');
			echo '</TD>';
			echo '</TR>';
			break;
	}
}

foreach($fields_RET as $field)
{
	if($field['TYPE']=='textarea')
	{
		echo '<TR>';
		echo '<td valign=top>'.$field['TITLE'].'</td><td valign=top>:</td><td>';
		echo _makeTextareaInput('CUSTOM_'.$field['ID'],'','class=cell_medium');
		echo '</TD>';
		echo '</TR>';
	}
}
echo '</TABLE>';

?>