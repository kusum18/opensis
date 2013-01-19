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
#error_reporting(1);
require_once('Warehouse.php');
if($_REQUEST['modfunc']=='logout')
{
	if($_SESSION)
		header("Location: $_SERVER[PHP_SELF]?modfunc=logout".(($_REQUEST['reason'])?'&reason='.$_REQUEST['reason']:''));
	session_destroy();
}

	if($_REQUEST['register'])
	{
		if($_REQUEST['R1']=='register')
		header("Location:register.php");
	}


if($_REQUEST['USERNAME'] && $_REQUEST['PASSWORD'])
{
	$username = str_replace("\'","",$_REQUEST['USERNAME']);
	$username = str_replace("&","",$_REQUEST['USERNAME']);
	$username = str_replace("\\","",$_REQUEST['USERNAME']);
	$password = str_replace("\'","",md5($_REQUEST['PASSWORD']));
	$password = str_replace("&","",md5($_REQUEST['PASSWORD']));
	$password = str_replace("\\","",md5($_REQUEST['PASSWORD']));
	$login_RET = DBGet(DBQuery("SELECT USERNAME,PROFILE,STAFF_ID,LAST_LOGIN,FAILED_LOGIN FROM STAFF WHERE SYEAR='$DefaultSyear' AND UPPER(USERNAME)=UPPER('$username') AND UPPER(PASSWORD)=UPPER('$password')"));
	$student_RET = DBGet(DBQuery("SELECT s.USERNAME,s.STUDENT_ID,s.LAST_LOGIN,s.FAILED_LOGIN FROM STUDENTS s,STUDENT_ENROLLMENT se WHERE UPPER(s.USERNAME)=UPPER('$username') AND UPPER(s.PASSWORD)=UPPER('$password') AND se.STUDENT_ID=s.STUDENT_ID AND se.SYEAR=$DefaultSyear AND CURRENT_DATE>=se.START_DATE AND (CURRENT_DATE<=se.END_DATE OR se.END_DATE IS NULL)"));
//	if(!$login_RET && !$student_RET && $CentreAdmins)
	if(!$login_RET && !$student_RET)
	{
	//	$admin_RET = DBGet(DBQuery("SELECT STAFF_ID FROM STAFF WHERE PROFILE='admin' AND SYEAR='$DefaultSyear' AND STAFF_ID IN ($CentreAdmins) AND UPPER(PASSWORD)=UPPER('$_REQUEST[PASSWORD]')"));
		$admin_RET = DBGet(DBQuery("SELECT STAFF_ID FROM STAFF WHERE PROFILE='$username' AND SYEAR='$DefaultSyear' AND UPPER(PASSWORD)=UPPER('$password')"));  // Uid and Password Checking,  Ritwik
		if($admin_RET)
		{
			$login_RET = DBGet(DBQuery("SELECT USERNAME,PROFILE,STAFF_ID,LAST_LOGIN,FAILED_LOGIN FROM STAFF WHERE SYEAR='$DefaultSyear' AND UPPER(USERNAME)=UPPER('$username')"));
			$student_RET = DBGet(DBQuery("SELECT s.USERNAME,s.STUDENT_ID,s.LAST_LOGIN,s.FAILED_LOGIN FROM STUDENTS s,STUDENT_ENROLLMENT se WHERE UPPER(s.USERNAME)=UPPER('$username') AND se.STUDENT_ID=s.STUDENT_ID AND se.SYEAR=$DefaultSyear AND CURRENT_DATE>=se.START_DATE AND (CURRENT_DATE<=se.END_DATE OR se.END_DATE IS NULL)"));
		}
	}
	if($login_RET && $login_RET[1]['PROFILE']!='none')
	{
		//$_SESSION['USERNAME'] = $login_RET[1]['USERNAME']; // hopefully we are finally done with this
		$_SESSION['STAFF_ID'] = $login_RET[1]['STAFF_ID'];
		$_SESSION['LAST_LOGIN'] = $login_RET[1]['LAST_LOGIN'];
		$failed_login = $login_RET[1]['FAILED_LOGIN'];
		if($admin_RET)
			DBQuery("UPDATE STAFF SET LAST_LOGIN=CURRENT_TIMESTAMP WHERE STAFF_ID='".$admin_RET[1]['STAFF_ID']."'");
		else
			DBQuery("UPDATE STAFF SET LAST_LOGIN=CURRENT_TIMESTAMP,FAILED_LOGIN=NULL WHERE STAFF_ID='".$login_RET[1]['STAFF_ID']."'");

		if(Config('LOGIN')=='No')
		{
		
			//require "register.inc.php";
		
		
				require('soaplib/nusoap.php');
				$parameters = array($_SERVER['SERVER_NAME'], $_SERVER['SERVER_ADDR'], $CentreVersion, $_SERVER['PHP_SELF'], $_SERVER['DOCUMENT_ROOT'], $_SERVER['SCRIPT_NAME']);
				$s = new nusoap_client('http://register.os4ed.com/register.php');
				$result = $s->call('installlog',$parameters);
				
				DBQuery("UPDATE config SET LOGIN='Y'");
		}
	}
	elseif($login_RET && $login_RET[1]['PROFILE']=='none')
		$error[] = "Your account has not yet been activated.  When your account has been verified by school administration, you will be notified by email.";
	elseif($student_RET)
	{
		$_SESSION['STUDENT_ID'] = $student_RET[1]['STUDENT_ID'];
		$_SESSION['LAST_LOGIN'] = $student_RET[1]['LAST_LOGIN'];
		$failed_login = $student_RET[1]['FAILED_LOGIN'];
		if($admin_RET)
			DBQuery("UPDATE STAFF SET LAST_LOGIN=CURRENT_TIMESTAMP WHERE STAFF_ID='".$admin_RET[1]['STAFF_ID']."'");
		else
			DBQuery("UPDATE STUDENTS SET LAST_LOGIN=CURRENT_TIMESTAMP,FAILED_LOGIN=NULL WHERE STUDENT_ID='".$student_RET[1]['STUDENT_ID']."'");
	}
	else
	{
		DBQuery("UPDATE STAFF SET FAILED_LOGIN=".db_case(array('FAILED_LOGIN',"''",'1','FAILED_LOGIN+1'))." WHERE UPPER(USERNAME)=UPPER('$_REQUEST[USERNAME]') AND SYEAR='$DefaultSyear'");
		DBQuery("UPDATE STUDENTS SET FAILED_LOGIN=".db_case(array('FAILED_LOGIN',"''",'1','FAILED_LOGIN+1'))." WHERE UPPER(USERNAME)=UPPER('$_REQUEST[USERNAME]')");
		$error[] = "Incorrect username or password. Please try again.";
	}
}

if($_REQUEST['modfunc']=='create_account')
{
	Warehouse('header');
	$_CENTRE['allow_edit'] = true;
	if($_REQUEST['staff']['USERNAME'])
		$_REQUEST['modfunc'] = 'update';
	else
		$_REQUEST['staff_id'] = 'new';
	include('modules/Users/User.php');

	if(!$_REQUEST['staff']['USERNAME'])
		Warehouse('footer_plain');
	else
	{
		$note[] = 'Your account has been created.  You will be notified by email when it is verified by school administration and you can log in.';
		session_destroy();
	}
}

if(!$_SESSION['STAFF_ID'] && !$_SESSION['STUDENT_ID'] && $_REQUEST['modfunc']!='create_account')
{
	//Login
	require "login.inc.php";	
}


elseif($_REQUEST['modfunc']!='create_account')
{
	echo "
		<HTML>
			<HEAD><TITLE>".Config('TITLE')."</TITLE><link rel=\"shortcut icon\" href=\"favicon.ico\"></HEAD>";
	echo "<noscript><META http-equiv=REFRESH content='0;url=index.php?modfunc=logout&reason=javascript' /></noscript>";
	echo "<frameset id=mainframeset rows='*,0' border=0 framespacing=0>
				<frameset cols='0,*' border=0>
					<frame name='side' src='' frameborder='0' />
					<frame name='body' src='Modules.php?modname=".($_REQUEST['modname']='misc/Portal.php')."&failed_login=$failed_login' frameborder='0' style='border: inset #C9C9C9 2px' />
				</frameset>
				<frame name='help' src='' />
			</frameset>
		</HTML>";
}
?>
