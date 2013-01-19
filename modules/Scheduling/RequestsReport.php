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
	$count_RET = DBGet(DBQuery("SELECT cs.TITLE as SUBJECT_TITLE,c.TITLE as COURSE_TITLE,sr.COURSE_ID,COUNT(*) AS COUNT,(SELECT sum(TOTAL_SEATS) FROM COURSE_PERIODS cp WHERE cp.COURSE_ID=sr.COURSE_ID) AS SEATS FROM SCHEDULE_REQUESTS sr,COURSES c,COURSE_SUBJECTS cs WHERE cs.SUBJECT_ID=c.SUBJECT_ID AND sr.COURSE_ID=c.COURSE_ID AND sr.SYEAR='".UserSyear()."' AND sr.SCHOOL_ID='".UserSchool()."' GROUP BY sr.COURSE_ID,cs.TITLE,c.TITLE"),array(),array('SUBJECT_TITLE'));
	$columns = array('SUBJECT_TITLE'=>'Subject','COURSE_TITLE'=>'Course','COUNT'=>'Number of Requests','SEATS'=>'Seats');
	
	DrawBC("Scheduling > ".ProgramTitle());
	ListOutput($count_RET,$columns,'','',array(),array(array('SUBJECT_TITLE')));
?>