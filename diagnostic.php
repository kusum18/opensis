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
error_reporting(1);
session_start();
if(!$_SESSION['STAFF_ID'])
{
	$unset_username = true;
	$_SESSION['USERNAME'] = 'diagnostic';
	$_SESSION['STAFF_ID'] = '-1';
}
if(!file_exists('./Warehouse.php'))
	$error[] = 'The diagnostic.php file needs to be in the Centre directory to be able to run.  Please move it there, and run it again.';
else
{
	include './Warehouse.php';
	if(!@opendir("$CentrePath/functions"))
		$error[] = 'The value for $CentrePath in config.inc.php is not correct or else the functions directory does not have the correct permissions to be read by the webserver.  Make sure $CentrePath points to the openSIS installation directory and that it is readable by all users.';

	if(!function_exists('pg_connect'))
		$error[] = 'PHP was not compiled with PostgreSQL support.  You need to recompile PHP using the --with-pgsql option for openSIS to work.';
	else
	{
			$connectstring="host=$DatabaseServer port=$DatabasePort dbname=$DatabaseName user=$DatabaseUsername";
			if(!empty($DatabasePassword))
				$connectstring.=" password=$DatabasePassword";
			$connection = @pg_connect($connectstring);

		if(!$connection)
			$error[] = 'openSIS cannot connect to the Postgres database.  Either Postgres is not running, it was not started with the -i option, or connections from this host are not allowed in the pg_hba.conf file. Last Postgres Error: '.pg_last_error();
		else
		{
			$result = @pg_exec($connection,'SELECT * FROM CONFIG');
			if($result===false)
				$errstring = pg_last_error($connection);

			if(strpos($errstring,'config: permission denied')!==false)
				$error[] = 'The database was created with the wrong permissions.  The user specified in the config.inc.php file does not have permission to access the centre database.  Use the super-user (postgres) or recreate the database adding \connect - YOUR_USERNAME to the top of the centre.sql file.';
			elseif(strpos($errstring,'elation "config" does not exist')!==false)
				$error[] = 'At least one of the tables does not exist.  Make sure you ran the centre.sql file as described in the INSTALL file.';
			elseif($errstring)
				$error[] = $errstring;
		}
	}
}

echo _ErrorMessage($error,'error');
if(!count($error))
	echo '<h3>Your openSIS installation is properly configured.</h3>';
phpinfo();

if($unset_username)
{
	unset($_SESSION['USERNAME']);
	unset($_SESSION['STAFF_ID']);
}

function _ErrorMessage($errors,$code='error')
{
	if($errors)
	{
		$return .= "<TABLE border=0><TR><TD align=left>";
		if(count($errors)==1)
		{
			if($code=='error' || $code=='fatal')
				$return .= '<b><font color=#CC0000>Error:</font></b> ';
			else
				$return .= '<b><font color=#00CC00>Note:</font></b> ';
			$return .= (($errors[0])?$errors[0]:$errors[1]);
		}
		else
		{
			if($code=='error' || $code=='fatal')
				$return .= "<b><font color=#CC0000>Errors:</font></b>";
			else
				$return .= '<b><font color=#00CC00>Note:</font></b>';
			$return .= '<ul>';
			foreach($errors as $value)
				$return .= "<LI><font size=-1>$value</font></LI>\n";
			$return .= '</ul>';
		}
			$return .= "</TD></TR></TABLE><br>";

		if($code=='fatal')
		{
			echo $return;
			if(!$_REQUEST['_CENTRE_PDF'])
				Warehouse('footer');
			exit;
		}

		return $return;
	}
}

?>
