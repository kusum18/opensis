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
if(User('PROFILE')=='admin')
{
if($_REQUEST['student_id']=='new')
{
if(!$_REQUEST['include'])
{
unset($_SESSION['student_id']);
	unset($_SESSION['_REQUEST_vars']['student_id']);
}
}
}

if(!$_REQUEST['include'])
{
	$_REQUEST['include'] = 'General_Info';
	$_REQUEST['category_id'] = '1';
}
elseif(!$_REQUEST['category_id'])
	if($_REQUEST['include']=='General_Info')
		$_REQUEST['category_id'] = '1';
	elseif($_REQUEST['include']=='Address')
		$_REQUEST['category_id'] = '3';
	elseif($_REQUEST['include']=='Medical')
		$_REQUEST['category_id'] = '2';
	elseif($_REQUEST['include']=='Comments')
		$_REQUEST['category_id'] = '4';
	elseif($_REQUEST['include']!='Other_Info')
	{
		$include = DBGet(DBQuery("SELECT ID FROM STUDENT_FIELD_CATEGORIES WHERE INCLUDE='$_REQUEST[include]'"));
		$_REQUEST['category_id'] = $include[1]['ID'];
	}
	if($_REQUEST['category_id']==3 && !isset($_REQUEST['address_id']))
	{
	$address_id = DBGet(DBQuery("SELECT ADDRESS_ID FROM ADDRESS WHERE STUDENT_ID='".UserStudentID()."'"));
	$address_id = $address_id[1]['ADDRESS_ID'];
	if(count($address_id)>0)
	$_REQUEST['address_id'] = $address_id;
	else
	$_REQUEST['address_id'] = 'new';
	}
//if(strpos($_REQUEST['modname'],'?include='))
//	$_REQUEST['modname'] = substr($_REQUEST['modname'],0,strpos($_REQUEST['modname'],'?include='));

if(User('PROFILE')!='admin')
{
	if(User('PROFILE')!='student')
		if(User('PROFILE_ID'))
			$can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"));
		else
			$can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"),array(),array('MODNAME'));
	else
		$can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='0' AND MODNAME='Students/Student.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"));
	if($can_edit_RET)
		$_CENTRE['allow_edit'] = true;
}

if($_REQUEST['modfunc']=='update' && AllowEdit())
{
	if(count($_REQUEST['month_students']))
	{
		foreach($_REQUEST['month_students'] as $column=>$value)
		{
			$_REQUEST['students'][$column] = $_REQUEST['day_students'][$column].'-'.$_REQUEST['month_students'][$column].'-'.$_REQUEST['year_students'][$column];
			if($_REQUEST['students'][$column]=='--')
				$_REQUEST['students'][$column] = '';
			elseif(!VerifyDate($_REQUEST['students'][$column]))
			{
				unset($_REQUEST['students'][$column]);
				$note = "The invalid date could not be saved.";
			}
		}
	}
	unset($_REQUEST['day_students']); unset($_REQUEST['month_students']); unset($_REQUEST['year_students']);

	if((count($_REQUEST['students']) || count($_REQUEST['values'])) && AllowEdit())
	{
		if($_REQUEST['student_id'] && $_REQUEST['student_id']!='new')
		{
			if(count($_REQUEST['students']))
			{
				$sql = "UPDATE STUDENTS SET ";
				foreach($_REQUEST['students'] as $column_name=>$value)
				{
					if($column_name=='PASSWORD')
					$sql .= "$column_name='".str_replace("\'","''",str_replace("`","''",md5($value)))."',";	
					else
					$sql .= "$column_name='".str_replace("\'","''",str_replace("`","''",$value))."',";	
				}
				$sql = substr($sql,0,-1) . " WHERE STUDENT_ID='$_REQUEST[student_id]'";
				DBQuery($sql);
			}

			if(count($_REQUEST['values']['STUDENT_ENROLLMENT'][UserStudentID()]))
			{
				$sql = "UPDATE STUDENT_ENROLLMENT SET ";
				foreach($_REQUEST['values']['STUDENT_ENROLLMENT'][UserStudentID()] as $column_name=>$value)
					if($column_name=='START_DATE' || $column_name=='END_DATE')
						$sql .= "$column_name='".str_replace("\'","''",date('Y-m-d',strtotime($value)))."',";
					else
						$sql .= "$column_name='".str_replace("\'","''",str_replace('&#39;',"''",$value))."',";
				$sql = substr($sql,0,-1) . " WHERE STUDENT_ID='$_REQUEST[student_id]' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'";
				DBQuery($sql);
			}
		}
		else
		{
			if($_REQUEST['assign_student_id'])
			{
				$student_id = $_REQUEST['assign_student_id'];
				if(count(DBGet(DBQuery("SELECT STUDENT_ID FROM STUDENTS WHERE STUDENT_ID='$student_id'"))))
					BackPrompt('That Student ID is already taken. Please select a different one.');
			}
			else
			{
				do
				{
					$student_id = DBGet(DBQuery('SELECT '.db_seq_nextval('STUDENTS_SEQ').' AS STUDENT_ID '.FROM_DUAL));
					$student_id = $student_id[1]['STUDENT_ID'];
				}
				while(count(DBGet(DBQuery("SELECT STUDENT_ID FROM STUDENTS WHERE STUDENT_ID='$student_id'"))));
			}

			$sql = "INSERT INTO STUDENTS ";
			$fields = 'STUDENT_ID,';
			$values = "'$student_id',";

			foreach($_REQUEST['students'] as $column=>$value)
			{
				if($value)
				{
					$fields .= $column.',';
					if($column=='PASSWORD')
						$values .= "'".str_replace("\'","''",md5($value))."',";
					else
					$values .= "'".str_replace("\'","''",$value)."',";
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			DBQuery($sql);

			$sql = "INSERT INTO STUDENT_ENROLLMENT ";
			$fields = 'ID,STUDENT_ID,SYEAR,SCHOOL_ID,';
			$values = "".db_seq_nextval('STUDENT_ENROLLMENT_SEQ').",'$student_id','".UserSyear()."','".UserSchool()."',";
			
			if($_REQUEST['day_values'])
			$_REQUEST['values']['STUDENT_ENROLLMENT']['new']['START_DATE'] = $_REQUEST['day_values']['STUDENT_ENROLLMENT']['new']['START_DATE'].'-'.$_REQUEST['month_values']['STUDENT_ENROLLMENT']['new']['START_DATE'].'-'.$_REQUEST['year_values']['STUDENT_ENROLLMENT']['new']['START_DATE'];
			else
			$_REQUEST['values']['STUDENT_ENROLLMENT']['new']['START_DATE'] = '';

			foreach($_REQUEST['values']['STUDENT_ENROLLMENT']['new'] as $column=>$value)
			{
				if($value)
				{
					$fields .= $column.',';
					if($column=='START_DATE' || $column=='END_DATE')
						$values .= "'".date('Y-m-d',strtotime($value))."',";
					else
						$values .= "'".str_replace("\'","''",str_replace('&#39;',"''",$value))."',";
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			DBQuery($sql);

			if($CentreModules['Food_Service'])
			{
				// create default food service account for this student
				$sql = "INSERT INTO FOOD_SERVICE_ACCOUNTS (ACCOUNT_ID,BALANCE,TRANSACTION_ID) values('$student_id','0.00','0')";
				DBQuery($sql);

				// associate with default food service account and assign other defaults
				$sql = "INSERT INTO FOOD_SERVICE_STUDENT_ACCOUNTS (STUDENT_ID,DISCOUNT,BARCODE,ACCOUNT_ID) values('$student_id','','','$student_id')";
				DBQuery($sql);
			}

			$_SESSION['student_id'] = $_REQUEST['student_id'] = $student_id;
			$new_student = true;
		}
	}

	if($_REQUEST['values'] && $_REQUEST['include']=='Medical')
		SaveData(array('STUDENT_MEDICAL_ALERTS'=>"ID='__ID__'",'STUDENT_MEDICAL'=>"ID='__ID__'",'STUDENT_MEDICAL_VISITS'=>"ID='__ID__'",'fields'=>array('STUDENT_MEDICAL'=>'ID,STUDENT_ID,','STUDENT_MEDICAL_ALERTS'=>'ID,STUDENT_ID,','STUDENT_MEDICAL_VISITS'=>'ID,STUDENT_ID,'),'values'=>array('STUDENT_MEDICAL'=>db_seq_nextval('STUDENT_MEDICAL_SEQ').",'".UserStudentID()."',",'STUDENT_MEDICAL_ALERTS'=>db_seq_nextval('STUDENT_MEDICAL_ALERTS_SEQ').",'".UserStudentID()."',",'STUDENT_MEDICAL_VISITS'=>db_seq_nextval('STUDENT_MEDICAL_VISITS_SEQ').",'".UserStudentID()."',")));

	if($_REQUEST['include']!='General_Info' && $_REQUEST['include']!='Address' && $_REQUEST['include']!='Medical' && $_REQUEST['include']!='Other_Info')
		if(!strpos($_REQUEST['include'],'/'))
			include('modules/Students/includes/'.$_REQUEST['include'].'.inc.php');
		else
			include('modules/'.$_REQUEST['include'].'.inc.php');

	unset($_REQUEST['modfunc']);
	// SHOULD THIS BE HERE???
	if(!UserStudentID())
		unset($_REQUEST['values']);
	unset($_SESSION['_REQUEST_vars']['modfunc']);
	unset($_SESSION['_REQUEST_vars']['values']);
}

if($_REQUEST['student_id']=='new')
	DrawBC('Students > Add a Student');
else
	DrawBC("Students > ".ProgramTitle());

Search('student_id');

if(UserStudentID() || $_REQUEST['student_id']=='new')
{
	if($_REQUEST['modfunc']!='delete' || $_REQUEST['delete_ok']=='1')
	{
		if($_REQUEST['student_id']!='new')
		{
			$sql = "SELECT s.STUDENT_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,s.NAME_SUFFIX,s.USERNAME,s.PASSWORD,s.LAST_LOGIN,s.ALT_ID,(SELECT SCHOOL_ID FROM STUDENT_ENROLLMENT WHERE SYEAR='".UserSyear()."' AND STUDENT_ID=s.STUDENT_ID ORDER BY START_DATE DESC,END_DATE DESC LIMIT 1) AS SCHOOL_ID,
						(SELECT GRADE_ID FROM STUDENT_ENROLLMENT WHERE SYEAR='".UserSyear()."' AND STUDENT_ID=s.STUDENT_ID ORDER BY START_DATE DESC,END_DATE DESC LIMIT 1) AS GRADE_ID
					FROM STUDENTS s
					WHERE s.STUDENT_ID='".UserStudentID()."'";
			$QI = DBQuery($sql);
			$student = DBGet($QI);
			$student = $student[1];
			$school = DBGet(DBQuery("SELECT SCHOOL_ID,GRADE_ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID='".UserStudentID()."' AND SYEAR='".UserSyear()."' AND ('".DBDate()."' BETWEEN START_DATE AND END_DATE OR END_DATE IS NULL)"));
			$_REQUEST['modname'] = str_replace('?student_id=new','',$_REQUEST['modname']);
			echo "<FORM name=student action=Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&category_id=$_REQUEST[category_id]&student_id=".UserStudentID()."&modfunc=update method=POST>";
		}
		else
			echo "<FORM name=student action=Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=update method=POST>";

		$name = $student['FIRST_NAME'].' '.$student['MIDDLE_NAME'].' '.$student['LAST_NAME'].' '.$student['NAME_SUFFIX'];

		if($_REQUEST['student_id']!='new')
			$name .= ' - '.$student['STUDENT_ID'];
		#DrawHeader($name); // name, id & save button removed as per instructions merlinvicki

		if(User('PROFILE')!='student')
			if(User('PROFILE_ID'))
				$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
			else
				$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
		else
			$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='0' AND CAN_USE='Y'"),array(),array('MODNAME'));
		$categories_RET = DBGet(DBQuery("SELECT ID,TITLE,INCLUDE FROM STUDENT_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE"));

		foreach($categories_RET as $category)
		{
			if($can_use_RET['Students/Student.php&category_id='.$category['ID']])
			{
				if($category['ID']=='1')
					$include = 'General_Info';
				elseif($category['ID']=='3')
					$include = 'Address';
				elseif($category['ID']=='2')
					$include = 'Medical';
				elseif($category['ID']=='4')
					$include = 'Comments';
				elseif($category['INCLUDE'])
					$include = $category['INCLUDE'];
				else
					$include = 'Other_Info';

				$tabs[] = array('title'=>$category['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&include=$include&category_id=".$category['ID']);
			}
		}

		$_CENTRE['selected_tab'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]";
		if($_REQUEST['category_id'])
			$_CENTRE['selected_tab'] .= '&category_id='.$_REQUEST['category_id'];

		echo '<BR>';
		echo PopTable('header',$tabs,'');

		if(!strpos($_REQUEST['include'],'/'))
			include('modules/Students/includes/'.$_REQUEST['include'].'.inc.php');
		else
		{
			include('modules/'.$_REQUEST['include'].'.inc.php');
			$separator = '<HR>';
			include('modules/Students/includes/Other_Info.inc.php');
		}
		echo PopTable('footer');
		echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_student_student();"').'</CENTER>';
		echo '</FORM>';
	}
	else
		if(!strpos($_REQUEST['include'],'/'))
			include('modules/Students/includes/'.$_REQUEST['include'].'.inc.php');
		else
		{
			include('modules/'.$_REQUEST['include'].'.inc.php');
			$separator = '<div class=break></div>';
			include('modules/Students/includes/Other_Info.inc.php');
		}
}
?>