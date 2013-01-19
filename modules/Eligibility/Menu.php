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
$menu['Eligibility']['admin'] = array(
						'Eligibility/Student.php'=>'Student Screen',
						'Eligibility/AddActivity.php'=>'Add Activity',
						1=>'Reports',
						'Eligibility/StudentList.php'=>'Student List',
						'Eligibility/TeacherCompletion.php'=>'Teacher Completion',
						2=>'Setup',
						'Eligibility/Activities.php'=>'Activities',
						'Eligibility/EntryTimes.php'=>'Entry Times'
					);

$menu['Eligibility']['teacher'] = array(
						'Eligibility/EnterEligibility.php'=>'Enter Eligibility'
					);

$menu['Eligibility']['parent'] = array(
						'Eligibility/Student.php'=>'Student Screen',
						'Eligibility/StudentList.php'=>'Student List'
					);

$menu['Users']['admin'] += array(
						'Users/TeacherPrograms.php?include=Eligibility/EnterEligibility.php'=>'Enter Eligibility'
					);

$exceptions['Eligibility'] = array(
						'Eligibility/AddActivity.php'=>true
					);

$exceptions['Users'] += array(
						'Users/TeacherPrograms.php?include=Eligibility/EnterEligibility.php'=>true
					);
?>