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
	
	include 'Warehouse.php';
	include 'data.php';
	$v_year = $_SESSION['UserSyear'];

	$flag = $_GET['u'];
	$usr = substr($flag, -4);
	
	// ------------------------ For Unique Checking ---------------------------------- //
	$un = substr($flag, 0, -4);
	$un = strtoupper($un);
	// ------------------------ For Unique Checking ---------------------------------- //
		
	if($usr == 'user')
	{
		$result = DBGet(DBQuery("SELECT username FROM STAFF WHERE syear= $v_year "));
		$rit = 0;

		foreach ($result as $row) 
		{
		  $unames[$rit] = strtoupper($row[0]); // For Unique Checking.
		  $rit++;
		}
	
		if ($un != '') 
		{
			if (in_array ($un, $unames)) 
			{
				echo '0';
			} 
			else 
			{
				echo '1';
			}
			exit;
		}
	}
	else
	{
		$result = DBGet(DBQuery("select s.username from STUDENTS s, STUDENT_ENROLLMENT se where s.student_id = se.student_id and se.syear = $v_year"));
		
		$rit = 0;
		foreach ($result as $row) 
		{
		  $unames[$rit] = strtoupper($row[0]); // For Unique Checking.
		  $rit++;
		}
	
		if ($un != '') 
		{
			if (in_array ($un, $unames)) 
			{
				echo '0';
			} 
			else 
			{
				echo '1';
			}
			exit;
		}
	}

?>