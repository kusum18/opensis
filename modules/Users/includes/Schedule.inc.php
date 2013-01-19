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
if(GetTeacher(UserStaffID(),'','PROFILE',false)=='teacher')
{
	$schedule_RET = DBGet(DBQuery("SELECT cp.PERIOD_ID,cp.ROOM,c.TITLE,cp.COURSE_WEIGHT,cp.MARKING_PERIOD_ID FROM COURSE_PERIODS cp,COURSES c WHERE cp.COURSE_ID=c.COURSE_ID AND cp.TEACHER_ID='".UserStaffID()."' AND cp.SYEAR='".UserSyear()."'"),array('PERIOD_ID'=>'GetPeriod','MARKING_PERIOD_ID'=>'GetMP'));
	ListOutput($schedule_RET,array('TITLE'=>'Course','PERIOD_ID'=>'Period','ROOM'=>'Room','MARKING_PERIOD_ID'=>'Marking Period'),'Course','Courses');
	echo '<HR>';
}

$_REQUEST['category_id'] = 2;
include('modules/Users/includes/Other_Info.inc.php');
?>