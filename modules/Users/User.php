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
if(User('PROFILE')!='admin' && User('PROFILE')!='teacher' && $_REQUEST['staff_id'] && $_REQUEST['staff_id']!='new')
{
	if(User('USERNAME'))
	{
		echo "You're not allowed to do this! This attempted violation has been logged and your IP address was captured.";
		DBQuery("INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$_SERVER[REMOTE_ADDR]','".date('Y-m-d')."','$CentreVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
		Warehouse('footer');
		if($CentreNotifyAddress)
			mail($CentreNotifyAddress,'HACKING ATTEMPT',"INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$_SERVER[REMOTE_ADDR]','".date('Y-m-d')."','$CentreVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname] - tried to access user','".User('USERNAME')."')");
	}
	exit;
}

if(!$_REQUEST['include'])
{
	$_REQUEST['include'] = 'General_Info';
	$_REQUEST['category_id'] = '1';
}
elseif(!$_REQUEST['category_id'])
	if($_REQUEST['include']=='General_Info')
		$_REQUEST['category_id'] = '1';
	elseif($_REQUEST['include']=='Schedule')
		$_REQUEST['category_id'] = '2';
	elseif($_REQUEST['include']!='Other_Info')
	{
		$include = DBGet(DBQuery("SELECT ID FROM STAFF_FIELD_CATEGORIES WHERE INCLUDE='$_REQUEST[include]'"));
		$_REQUEST['category_id'] = $include[1]['ID'];
	}

if(User('PROFILE')!='admin')
{
	if(User('PROFILE_ID'))
		$can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND MODNAME='Users/User.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"));
	else
		$can_edit_RET = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND MODNAME='Users/User.php&category_id=$_REQUEST[category_id]' AND CAN_EDIT='Y'"),array(),array('MODNAME'));
	if($can_edit_RET)
		$_CENTRE['allow_edit'] = true;
}

unset($schools);
if($_REQUEST['modfunc']=='update')
{
	if($_REQUEST['staff']['SCHOOLS'])
	{
		foreach($_REQUEST['staff']['SCHOOLS'] as $school_id=>$yes)
			$schools .= ','.$school_id;
		$_REQUEST['staff']['SCHOOLS'] = $schools.',';
	}
	else
		$_REQUEST['staff']['SCHOOLS'] = $_POST['staff'] = '';

	if(count($_POST['staff']) && (User('PROFILE')=='admin' || basename($_SERVER['PHP_SELF'])=='index.php') || $_REQUEST['ajax'])
	{
		if($_REQUEST['staff_id'] && $_REQUEST['staff_id']!='new')
		{
			$profile_RET = DBGet(DBQuery("SELECT PROFILE,PROFILE_ID,USERNAME FROM STAFF WHERE STAFF_ID='$_REQUEST[staff_id]'"));

			if(isset($_REQUEST['staff']['PROFILE']) && $_REQUEST['staff']['PROFILE']!=$profile_RET[1]['PROFILE_ID'])
			{
				if($_REQUEST['staff']['PROFILE']=='admin')
					$_REQUEST['staff']['PROFILE_ID'] = '1';
				elseif($_REQUEST['staff']['PROFILE']=='teacher')
					$_REQUEST['staff']['PROFILE_ID'] = '2';
				elseif($_REQUEST['staff']['PROFILE']=='parent')
					$_REQUEST['staff']['PROFILE_ID'] = '3';
			}

			if($_REQUEST['staff']['PROFILE_ID'])
				DBQuery("DELETE FROM STAFF_EXCEPTIONS WHERE USER_ID='$_REQUEST[staff_id]'");
			elseif(isset($_REQUEST['staff']['PROFILE_ID']) && $profile_RET[1]['PROFILE_ID'])
			{
				DBQuery("DELETE FROM STAFF_EXCEPTIONS WHERE USER_ID='$_REQUEST[staff_id]'");
				DBQuery("INSERT INTO STAFF_EXCEPTIONS (USER_ID,MODNAME,CAN_USE,CAN_EDIT) SELECT s.STAFF_ID,e.MODNAME,e.CAN_USE,e.CAN_EDIT FROM STAFF s,PROFILE_EXCEPTIONS e WHERE s.STAFF_ID='$_REQUEST[staff_id]' AND s.PROFILE_ID=e.PROFILE_ID");
			}

			// CHANGE THE USERNAME
			if($_REQUEST['staff']['USERNAME'] && $_REQUEST['staff']['USERNAME']!=$profile_RET[1]['USERNAME'])
			{
				$existing_staff = DBGet(DBQuery("SELECT SYEAR FROM STAFF WHERE USERNAME='".$_REQUEST['staff']['USERNAME']."' AND SYEAR=(SELECT SYEAR FROM STAFF WHERE STAFF_ID=$_REQUEST[staff_id])"));
				if(count($existing_staff))
					BackPrompt('A user with that username already exists for the '.$existing_staff[1]['SYEAR'].' school year. Choose a different username and try again.');
			}

			$sql = "UPDATE STAFF SET ";
			foreach($_REQUEST['staff'] as $column_name=>$value)
					{
				if($column_name=='PASSWORD')
					$sql .= "$column_name='".str_replace("\'","''",str_replace("`","''",md5($value)))."',";	
				else
 				$sql .= "$column_name='".str_replace("\'","''",str_replace("`","''",$value))."',";
								
			}
				
			$sql = substr($sql,0,-1) . " WHERE STAFF_ID='$_REQUEST[staff_id]'";
			if(User('PROFILE')=='admin')
				DBQuery($sql);
		}
		else
		{
			if($_REQUEST['staff']['PROFILE']=='admin')
				$_REQUEST['staff']['PROFILE_ID'] = '1';
			elseif($_REQUEST['staff']['PROFILE']=='teacher')
				$_REQUEST['staff']['PROFILE_ID'] = '2';
			elseif($_REQUEST['staff']['PROFILE']=='parent')
				$_REQUEST['staff']['PROFILE_ID'] = '3';

			$existing_staff = DBGet(DBQuery("SELECT 'exists' FROM STAFF WHERE USERNAME='".$_REQUEST['staff']['USERNAME']."' AND SYEAR='".UserSyear()."'"));
			//if(count($existing_staff))
				//BackPrompt('A user with that username already exists for the current school year. Choose a different username and try again.');
			
			$staff_id = DBGet(DBQuery('SELECT '.db_seq_nextval('STAFF_SEQ').' AS STAFF_ID'.FROM_DUAL));
			$staff_id = $staff_id[1]['STAFF_ID'];

			$sql = "INSERT INTO STAFF ";
			$fields = 'SYEAR,STAFF_ID,';
			$values = "'".UserSyear()."','$staff_id',";

			if(basename($_SERVER['PHP_SELF'])=='index.php')
			{
				$fields .= 'PROFILE,';
							$values = "'".Config('SYEAR')."'".substr($values,strpos($values,','))."'none',";
			}

			foreach($_REQUEST['staff'] as $column=>$value)
			{
				if($value)
				{
					$fields .= $column.',';
					if(strtoupper($column)=='PASSWORD')
						$values .= "'".str_replace("\'","''",md5($value))."',";
					else
					$values .= "'".str_replace("\'","''",$value)."',";
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			DBQuery($sql);

			$_REQUEST['staff_id'] = $staff_id;
		}
	}
	unset($_REQUEST['staff']);
	unset($_REQUEST['modfunc']);
	unset($_SESSION['_REQUEST_vars']['staff']);
	unset($_SESSION['_REQUEST_vars']['modfunc']);

	if(User('STAFF_ID')==$_REQUEST['staff_id'])
	{
		unset($_CENTRE['User']);
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
	}
}

$extra['SELECT'] = ',LAST_LOGIN';
$extra['columns_after'] = array('LAST_LOGIN'=>'Last Login');
$extra['functions'] = array('LAST_LOGIN'=>'makeLogin');

if(basename($_SERVER['PHP_SELF'])!='index.php')
{
	if($_REQUEST['staff_id']=='new')
		DrawBC("Users > Add a User");
	else
		DrawBC("Users > ".ProgramTitle());
	Search('staff_id',$extra);
}
else
	DrawHeader('Create Account');

if($_REQUEST['modfunc']=='delete' && basename($_SERVER['PHP_SELF'])!='index.php' && AllowEdit())
{
	if(DeletePrompt('user'))
	{
		DBQuery("DELETE FROM PROGRAM_USER_CONFIG WHERE USER_ID='".UserStaffID()."'");
		DBQuery("DELETE FROM STAFF_EXCEPTIONS WHERE USER_ID='".UserStaffID()."'");
		DBQuery("DELETE FROM STUDENTS_JOIN_USERS WHERE STAFF_ID='".UserStaffID()."'");
		DBQuery("DELETE FROM STAFF WHERE STAFF_ID='".UserStaffID()."'");
		unset($_SESSION['staff_id']);
		unset($_REQUEST['staff_id']);
		unset($_REQUEST['modfunc']);
		echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
		Search('staff_id',$extra);
	}
}

if((UserStaffID() || $_REQUEST['staff_id']=='new') && ((basename($_SERVER['PHP_SELF'])!='index.php') || !$_REQUEST['staff']['USERNAME']) && $_REQUEST['modfunc']!='delete')
{
	if($_REQUEST['staff_id']!='new')
	{
		$sql = "SELECT s.TITLE,s.STAFF_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,
						s.USERNAME,s.PASSWORD,s.SCHOOLS,s.PROFILE,s.PROFILE_ID,s.PHONE,s.EMAIL,s.LAST_LOGIN,s.ROLLOVER_ID
				FROM STAFF s WHERE s.STAFF_ID='".UserStaffID()."'";
		$QI = DBQuery($sql);
		$staff = DBGet($QI);
		$staff = $staff[1];
		echo "<FORM name=staff id=staff action=Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&category_id=$_REQUEST[category_id]&staff_id=".UserStaffID()."&modfunc=update method=POST >";
	}
	elseif(basename($_SERVER['PHP_SELF'])!='index.php')
		echo "<FORM name=staff id=staff action=Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&category_id=$_REQUEST[category_id]&modfunc=update method=POST>";
	else
		echo "<FORM name=F2 id=F2 action=index.php?modfunc=create_account METHOD=POST>";

	if(basename($_SERVER['PHP_SELF'])!='index.php')
	{
		if(UserStaffID() && UserStaffID()!=User('STAFF_ID') && UserStaffID()!=$_SESSION['STAFF_ID'] && User('PROFILE')=='admin')
			$delete_button = '<INPUT type=button class=btn_medium value=Delete onclick="window.location=\'Modules.php?modname='.$_REQUEST['modname'].'&modfunc=delete\'">';
	}
	#DrawHeader('',$delete_button.SubmitButton('Save','','class=btn_medium'));

	if(User('PROFILE_ID'))
		$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
	else
		$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
	$profile = DBGet(DBQuery("SELECT PROFILE FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
	$profile = $profile[1]['PROFILE'];
	$categories_RET = DBGet(DBQuery("SELECT ID,TITLE,INCLUDE FROM STAFF_FIELD_CATEGORIES WHERE ".($profile?strtoupper($profile).'=\'Y\'':'ID=\'1\'')." ORDER BY SORT_ORDER,TITLE"));

	foreach($categories_RET as $category)
	{
		if($can_use_RET['Users/User.php&category_id='.$category['ID']])
		{
				if($category['ID']=='1')
					$include = 'General_Info';
				elseif($category['ID']=='2')
					$include = 'Schedule';
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
	PopTable('header',$tabs,'width=96%');

	if(!strpos($_REQUEST['include'],'/'))
		include('modules/Users/includes/'.$_REQUEST['include'].'.inc.php');
	else
	{
		include('modules/'.$_REQUEST['include'].'.inc.php');
		$separator = '<HR>';
		include('modules/Users/includes/Other_Info.inc.php');
	}
	PopTable('footer');
	echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_user_user();"').'</CENTER>';
	echo '</FORM>';
}
?>