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
$menu['School_Setup']['admin'] = array(
						'School_Setup/PortalNotes.php'=>'Portal Notes',
						'School_Setup/Schools.php'=>'School Information',
						'School_Setup/Schools.php?new_school=true'=>'Add a School',
						'School_Setup/CopySchool.php'=>'Copy School',
						'School_Setup/MarkingPeriods.php'=>'Marking Periods',
						'School_Setup/Calendar.php'=>'Calendars',
						'School_Setup/Periods.php'=>'Periods',
						'School_Setup/GradeLevels.php'=>'Grade Levels',
						'School_Setup/Rollover.php'=>'Rollover'
					);

$menu['School_Setup']['teacher'] = array(
						'School_Setup/Schools.php'=>'School Information',
						'School_Setup/MarkingPeriods.php'=>'Marking Periods',
						'School_Setup/Calendar.php'=>'Calendar'
					);

$menu['School_Setup']['parent'] = array(
						'School_Setup/Schools.php'=>'School Information',
						'School_Setup/Calendar.php'=>'Calendar'
					);

$exceptions['School_Setup'] = array(
						'School_Setup/PortalNotes.php'=>true,
						'School_Setup/Schools.php?new_school=true'=>true,
						'School_Setup/Rollover.php'=>true
					);
?>