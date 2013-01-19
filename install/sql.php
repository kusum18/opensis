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
$text = "
--
-- Dumping data for table `APP`
--

INSERT INTO `APP` (`name`, `value`) VALUES
('version', '4.5.0'),
('date', '2009-07-27'),
('build', '07272009000'),
('update', '0'),
('last_updated', 'Jul 27, 2009');

--
-- Dumping data for table `ATTENDANCE_CALENDAR`
--

INSERT INTO `ATTENDANCE_CALENDAR` (`syear`, `school_id`, `school_date`, `minutes`, `block`, `calendar_id`) VALUES
(2009, 1, '2009-07-01', 999, NULL, 1),
(2009, 1, '2009-07-02', 999, NULL, 1),
(2009, 1, '2009-07-03', 999, NULL, 1),
(2009, 1, '2009-07-06', 999, NULL, 1),
(2009, 1, '2009-07-07', 999, NULL, 1),
(2009, 1, '2009-07-08', 999, NULL, 1),
(2009, 1, '2009-07-09', 999, NULL, 1),
(2009, 1, '2009-07-10', 999, NULL, 1),
(2009, 1, '2009-07-13', 999, NULL, 1),
(2009, 1, '2009-07-14', 999, NULL, 1),
(2009, 1, '2009-07-15', 999, NULL, 1),
(2009, 1, '2009-07-16', 999, NULL, 1),
(2009, 1, '2009-07-17', 999, NULL, 1),
(2009, 1, '2009-07-20', 999, NULL, 1),
(2009, 1, '2009-07-21', 999, NULL, 1),
(2009, 1, '2009-07-22', 999, NULL, 1),
(2009, 1, '2009-07-23', 999, NULL, 1),
(2009, 1, '2009-07-24', 999, NULL, 1),
(2009, 1, '2009-07-27', 999, NULL, 1),
(2009, 1, '2009-07-28', 999, NULL, 1),
(2009, 1, '2009-07-29', 999, NULL, 1),
(2009, 1, '2009-07-30', 999, NULL, 1),
(2009, 1, '2009-07-31', 999, NULL, 1),
(2009, 1, '2009-08-03', 999, NULL, 1),
(2009, 1, '2009-08-04', 999, NULL, 1),
(2009, 1, '2009-08-05', 999, NULL, 1),
(2009, 1, '2009-08-06', 999, NULL, 1),
(2009, 1, '2009-08-07', 999, NULL, 1),
(2009, 1, '2009-08-10', 999, NULL, 1),
(2009, 1, '2009-08-11', 999, NULL, 1),
(2009, 1, '2009-08-12', 999, NULL, 1),
(2009, 1, '2009-08-13', 999, NULL, 1),
(2009, 1, '2009-08-14', 999, NULL, 1),
(2009, 1, '2009-08-17', 999, NULL, 1),
(2009, 1, '2009-08-18', 999, NULL, 1),
(2009, 1, '2009-08-19', 999, NULL, 1),
(2009, 1, '2009-08-20', 999, NULL, 1),
(2009, 1, '2009-08-21', 999, NULL, 1),
(2009, 1, '2009-08-24', 999, NULL, 1),
(2009, 1, '2009-08-25', 999, NULL, 1),
(2009, 1, '2009-08-26', 999, NULL, 1),
(2009, 1, '2009-08-27', 999, NULL, 1),
(2009, 1, '2009-08-28', 999, NULL, 1),
(2009, 1, '2009-08-31', 999, NULL, 1),
(2009, 1, '2009-09-01', 999, NULL, 1),
(2009, 1, '2009-09-02', 999, NULL, 1),
(2009, 1, '2009-09-03', 999, NULL, 1),
(2009, 1, '2009-09-04', 999, NULL, 1),
(2009, 1, '2009-09-07', 999, NULL, 1),
(2009, 1, '2009-09-08', 999, NULL, 1),
(2009, 1, '2009-09-09', 999, NULL, 1),
(2009, 1, '2009-09-10', 999, NULL, 1),
(2009, 1, '2009-09-11', 999, NULL, 1),
(2009, 1, '2009-09-14', 999, NULL, 1),
(2009, 1, '2009-09-15', 999, NULL, 1),
(2009, 1, '2009-09-16', 999, NULL, 1),
(2009, 1, '2009-09-17', 999, NULL, 1),
(2009, 1, '2009-09-18', 999, NULL, 1),
(2009, 1, '2009-09-21', 999, NULL, 1),
(2009, 1, '2009-09-22', 999, NULL, 1),
(2009, 1, '2009-09-23', 999, NULL, 1),
(2009, 1, '2009-09-24', 999, NULL, 1),
(2009, 1, '2009-09-25', 999, NULL, 1),
(2009, 1, '2009-09-28', 999, NULL, 1),
(2009, 1, '2009-09-29', 999, NULL, 1),
(2009, 1, '2009-09-30', 999, NULL, 1),
(2009, 1, '2009-10-01', 999, NULL, 1),
(2009, 1, '2009-10-02', 999, NULL, 1),
(2009, 1, '2009-10-05', 999, NULL, 1),
(2009, 1, '2009-10-06', 999, NULL, 1),
(2009, 1, '2009-10-07', 999, NULL, 1),
(2009, 1, '2009-10-08', 999, NULL, 1),
(2009, 1, '2009-10-09', 999, NULL, 1),
(2009, 1, '2009-10-12', 999, NULL, 1),
(2009, 1, '2009-10-13', 999, NULL, 1),
(2009, 1, '2009-10-14', 999, NULL, 1),
(2009, 1, '2009-10-15', 999, NULL, 1),
(2009, 1, '2009-10-16', 999, NULL, 1),
(2009, 1, '2009-10-19', 999, NULL, 1),
(2009, 1, '2009-10-20', 999, NULL, 1),
(2009, 1, '2009-10-21', 999, NULL, 1),
(2009, 1, '2009-10-22', 999, NULL, 1),
(2009, 1, '2009-10-23', 999, NULL, 1),
(2009, 1, '2009-10-26', 999, NULL, 1),
(2009, 1, '2009-10-27', 999, NULL, 1),
(2009, 1, '2009-10-28', 999, NULL, 1),
(2009, 1, '2009-10-29', 999, NULL, 1),
(2009, 1, '2009-10-30', 999, NULL, 1),
(2009, 1, '2009-11-02', 999, NULL, 1),
(2009, 1, '2009-11-03', 999, NULL, 1),
(2009, 1, '2009-11-04', 999, NULL, 1),
(2009, 1, '2009-11-05', 999, NULL, 1),
(2009, 1, '2009-11-06', 999, NULL, 1),
(2009, 1, '2009-11-09', 999, NULL, 1),
(2009, 1, '2009-11-10', 999, NULL, 1),
(2009, 1, '2009-11-11', 999, NULL, 1),
(2009, 1, '2009-11-12', 999, NULL, 1),
(2009, 1, '2009-11-13', 999, NULL, 1),
(2009, 1, '2009-11-16', 999, NULL, 1),
(2009, 1, '2009-11-17', 999, NULL, 1),
(2009, 1, '2009-11-18', 999, NULL, 1),
(2009, 1, '2009-11-19', 999, NULL, 1),
(2009, 1, '2009-11-20', 999, NULL, 1),
(2009, 1, '2009-11-23', 999, NULL, 1),
(2009, 1, '2009-11-24', 999, NULL, 1),
(2009, 1, '2009-11-25', 999, NULL, 1),
(2009, 1, '2009-11-26', 999, NULL, 1),
(2009, 1, '2009-11-27', 999, NULL, 1),
(2009, 1, '2009-11-30', 999, NULL, 1),
(2009, 1, '2009-12-01', 999, NULL, 1),
(2009, 1, '2009-12-02', 999, NULL, 1),
(2009, 1, '2009-12-03', 999, NULL, 1),
(2009, 1, '2009-12-04', 999, NULL, 1),
(2009, 1, '2009-12-07', 999, NULL, 1),
(2009, 1, '2009-12-08', 999, NULL, 1),
(2009, 1, '2009-12-09', 999, NULL, 1),
(2009, 1, '2009-12-10', 999, NULL, 1),
(2009, 1, '2009-12-11', 999, NULL, 1),
(2009, 1, '2009-12-14', 999, NULL, 1),
(2009, 1, '2009-12-15', 999, NULL, 1),
(2009, 1, '2009-12-16', 999, NULL, 1),
(2009, 1, '2009-12-17', 999, NULL, 1),
(2009, 1, '2009-12-18', 999, NULL, 1),
(2009, 1, '2009-12-21', 999, NULL, 1),
(2009, 1, '2009-12-22', 999, NULL, 1),
(2009, 1, '2009-12-23', 999, NULL, 1),
(2009, 1, '2009-12-24', 999, NULL, 1),
(2009, 1, '2009-12-25', 999, NULL, 1),
(2009, 1, '2009-12-28', 999, NULL, 1),
(2009, 1, '2009-12-29', 999, NULL, 1),
(2009, 1, '2009-12-30', 999, NULL, 1),
(2009, 1, '2009-12-31', 999, NULL, 1),
(2009, 1, '2010-01-01', 999, NULL, 1),
(2009, 1, '2010-01-04', 999, NULL, 1),
(2009, 1, '2010-01-05', 999, NULL, 1),
(2009, 1, '2010-01-06', 999, NULL, 1),
(2009, 1, '2010-01-07', 999, NULL, 1),
(2009, 1, '2010-01-08', 999, NULL, 1),
(2009, 1, '2010-01-11', 999, NULL, 1),
(2009, 1, '2010-01-12', 999, NULL, 1),
(2009, 1, '2010-01-13', 999, NULL, 1),
(2009, 1, '2010-01-14', 999, NULL, 1),
(2009, 1, '2010-01-15', 999, NULL, 1),
(2009, 1, '2010-01-18', 999, NULL, 1),
(2009, 1, '2010-01-19', 999, NULL, 1),
(2009, 1, '2010-01-20', 999, NULL, 1),
(2009, 1, '2010-01-21', 999, NULL, 1),
(2009, 1, '2010-01-22', 999, NULL, 1),
(2009, 1, '2010-01-25', 999, NULL, 1),
(2009, 1, '2010-01-26', 999, NULL, 1),
(2009, 1, '2010-01-27', 999, NULL, 1),
(2009, 1, '2010-01-28', 999, NULL, 1),
(2009, 1, '2010-01-29', 999, NULL, 1),
(2009, 1, '2010-02-01', 999, NULL, 1),
(2009, 1, '2010-02-02', 999, NULL, 1),
(2009, 1, '2010-02-03', 999, NULL, 1),
(2009, 1, '2010-02-04', 999, NULL, 1),
(2009, 1, '2010-02-05', 999, NULL, 1),
(2009, 1, '2010-02-08', 999, NULL, 1),
(2009, 1, '2010-02-09', 999, NULL, 1),
(2009, 1, '2010-02-10', 999, NULL, 1),
(2009, 1, '2010-02-11', 999, NULL, 1),
(2009, 1, '2010-02-12', 999, NULL, 1),
(2009, 1, '2010-02-15', 999, NULL, 1),
(2009, 1, '2010-02-16', 999, NULL, 1),
(2009, 1, '2010-02-17', 999, NULL, 1),
(2009, 1, '2010-02-18', 999, NULL, 1),
(2009, 1, '2010-02-19', 999, NULL, 1),
(2009, 1, '2010-02-22', 999, NULL, 1),
(2009, 1, '2010-02-23', 999, NULL, 1),
(2009, 1, '2010-02-24', 999, NULL, 1),
(2009, 1, '2010-02-25', 999, NULL, 1),
(2009, 1, '2010-02-26', 999, NULL, 1),
(2009, 1, '2010-03-01', 999, NULL, 1),
(2009, 1, '2010-03-02', 999, NULL, 1),
(2009, 1, '2010-03-03', 999, NULL, 1),
(2009, 1, '2010-03-04', 999, NULL, 1),
(2009, 1, '2010-03-05', 999, NULL, 1),
(2009, 1, '2010-03-08', 999, NULL, 1),
(2009, 1, '2010-03-09', 999, NULL, 1),
(2009, 1, '2010-03-10', 999, NULL, 1),
(2009, 1, '2010-03-11', 999, NULL, 1),
(2009, 1, '2010-03-12', 999, NULL, 1),
(2009, 1, '2010-03-15', 999, NULL, 1),
(2009, 1, '2010-03-16', 999, NULL, 1),
(2009, 1, '2010-03-17', 999, NULL, 1),
(2009, 1, '2010-03-18', 999, NULL, 1),
(2009, 1, '2010-03-19', 999, NULL, 1),
(2009, 1, '2010-03-22', 999, NULL, 1),
(2009, 1, '2010-03-23', 999, NULL, 1),
(2009, 1, '2010-03-24', 999, NULL, 1),
(2009, 1, '2010-03-25', 999, NULL, 1),
(2009, 1, '2010-03-26', 999, NULL, 1),
(2009, 1, '2010-03-29', 999, NULL, 1),
(2009, 1, '2010-03-30', 999, NULL, 1),
(2009, 1, '2010-03-31', 999, NULL, 1),
(2009, 1, '2010-04-01', 999, NULL, 1),
(2009, 1, '2010-04-02', 999, NULL, 1),
(2009, 1, '2010-04-05', 999, NULL, 1),
(2009, 1, '2010-04-06', 999, NULL, 1),
(2009, 1, '2010-04-07', 999, NULL, 1),
(2009, 1, '2010-04-08', 999, NULL, 1),
(2009, 1, '2010-04-09', 999, NULL, 1),
(2009, 1, '2010-04-12', 999, NULL, 1),
(2009, 1, '2010-04-13', 999, NULL, 1),
(2009, 1, '2010-04-14', 999, NULL, 1),
(2009, 1, '2010-04-15', 999, NULL, 1),
(2009, 1, '2010-04-16', 999, NULL, 1),
(2009, 1, '2010-04-19', 999, NULL, 1),
(2009, 1, '2010-04-20', 999, NULL, 1),
(2009, 1, '2010-04-21', 999, NULL, 1),
(2009, 1, '2010-04-22', 999, NULL, 1),
(2009, 1, '2010-04-23', 999, NULL, 1),
(2009, 1, '2010-04-26', 999, NULL, 1),
(2009, 1, '2010-04-27', 999, NULL, 1),
(2009, 1, '2010-04-28', 999, NULL, 1),
(2009, 1, '2010-04-29', 999, NULL, 1),
(2009, 1, '2010-04-30', 999, NULL, 1),
(2009, 1, '2010-05-03', 999, NULL, 1),
(2009, 1, '2010-05-04', 999, NULL, 1),
(2009, 1, '2010-05-05', 999, NULL, 1),
(2009, 1, '2010-05-06', 999, NULL, 1),
(2009, 1, '2010-05-07', 999, NULL, 1),
(2009, 1, '2010-05-10', 999, NULL, 1),
(2009, 1, '2010-05-11', 999, NULL, 1),
(2009, 1, '2010-05-12', 999, NULL, 1),
(2009, 1, '2010-05-13', 999, NULL, 1),
(2009, 1, '2010-05-14', 999, NULL, 1),
(2009, 1, '2010-05-17', 999, NULL, 1),
(2009, 1, '2010-05-18', 999, NULL, 1),
(2009, 1, '2010-05-19', 999, NULL, 1),
(2009, 1, '2010-05-20', 999, NULL, 1),
(2009, 1, '2010-05-21', 999, NULL, 1),
(2009, 1, '2010-05-24', 999, NULL, 1),
(2009, 1, '2010-05-25', 999, NULL, 1),
(2009, 1, '2010-05-26', 999, NULL, 1),
(2009, 1, '2010-05-27', 999, NULL, 1),
(2009, 1, '2010-05-28', 999, NULL, 1),
(2009, 1, '2010-05-31', 999, NULL, 1),
(2009, 1, '2010-06-01', 999, NULL, 1),
(2009, 1, '2010-06-02', 999, NULL, 1),
(2009, 1, '2010-06-03', 999, NULL, 1),
(2009, 1, '2010-06-04', 999, NULL, 1),
(2009, 1, '2010-06-07', 999, NULL, 1),
(2009, 1, '2010-06-08', 999, NULL, 1),
(2009, 1, '2010-06-09', 999, NULL, 1),
(2009, 1, '2010-06-10', 999, NULL, 1),
(2009, 1, '2010-06-11', 999, NULL, 1),
(2009, 1, '2010-06-14', 999, NULL, 1),
(2009, 1, '2010-06-15', 999, NULL, 1),
(2009, 1, '2010-06-16', 999, NULL, 1),
(2009, 1, '2010-06-17', 999, NULL, 1),
(2009, 1, '2010-06-18', 999, NULL, 1),
(2009, 1, '2010-06-21', 999, NULL, 1),
(2009, 1, '2010-06-22', 999, NULL, 1),
(2009, 1, '2010-06-23', 999, NULL, 1),
(2009, 1, '2010-06-24', 999, NULL, 1),
(2009, 1, '2010-06-25', 999, NULL, 1),
(2009, 1, '2010-06-28', 999, NULL, 1),
(2009, 1, '2010-06-29', 999, NULL, 1),
(2009, 1, '2010-06-30', 999, NULL, 1),
(2009, 1, '2010-07-01', 999, NULL, 1);

--
-- Dumping data for table `ATTENDANCE_CALENDARS`
--

INSERT INTO `ATTENDANCE_CALENDARS` (`school_id`, `title`, `syear`, `calendar_id`, `default_calendar`, `rollover_id`) VALUES
(1, 'Master', 2009, 1, 'Y', NULL);

--
-- Dumping data for table `ATTENDANCE_CODES`
--

INSERT INTO `ATTENDANCE_CODES` (`id`, `syear`, `school_id`, `title`, `short_name`, `type`, `state_code`, `default_code`, `table_name`, `sort_order`) VALUES
(1, 2009, 1, 'Absent', 'A', 'teacher', 'A', NULL, 0, NULL),
(2, 2009, 1, 'Vacation', 'V', 'official', 'A', NULL, 0, NULL),
(3, 2009, 1, 'Present', 'P', 'teacher', 'P', 'Y', 0, NULL),
(4, 2009, 1, 'Tardy', 'T', 'teacher', 'P', NULL, 0, NULL),
(5, 2009, 1, 'Less than 5 minutes late', 'L', 'teacher', 'P', NULL, 0, NULL),
(6, 2009, 1, 'Excused Absence', 'E', 'official', 'A', NULL, 0, NULL),
(7, 2009, 1, 'Sick', 'S', 'official', 'A', NULL, 0, NULL);

--
-- Dumping data for table `ATTENDANCE_COMPLETED`
--

INSERT INTO `ATTENDANCE_COMPLETED` (`staff_id`, `school_date`, `period_id`) VALUES
(2, '2009-07-01', 1),
(2, '2009-07-01', 2),
(2, '2009-07-01', 4),
(2, '2009-07-01', 5),
(2, '2009-07-01', 6),
(2, '2009-07-01', 7);

--
-- Dumping data for table `ATTENDANCE_DAY`
--

INSERT INTO `ATTENDANCE_DAY` (`student_id`, `school_date`, `minutes_present`, `state_value`, `syear`, `marking_period_id`, `comment`) VALUES
(1, '2009-07-01', 300, 1.0, 2009, 3, NULL);

--
-- Dumping data for table `ATTENDANCE_PERIOD`
--

INSERT INTO `ATTENDANCE_PERIOD` (`student_id`, `school_date`, `period_id`, `attendance_code`, `attendance_teacher_code`, `attendance_reason`, `admin`, `course_period_id`, `marking_period_id`, `comment`) VALUES
(1, '2009-07-01', 1, 3, 3, NULL, NULL, 8, 3, NULL),
(1, '2009-07-01', 2, 3, 3, NULL, NULL, 7, 3, NULL),
(1, '2009-07-01', 7, 3, 3, NULL, NULL, 1, 3, NULL),
(1, '2009-07-01', 6, 3, 3, NULL, NULL, 2, 3, NULL),
(1, '2009-07-01', 5, 3, 3, NULL, NULL, 4, 3, NULL),
(1, '2009-07-01', 4, 3, 3, NULL, NULL, 5, 3, NULL);

--
-- Dumping data for table `CONFIG`
--

INSERT INTO `CONFIG` (`title`, `syear`, `login`) VALUES
('Opensis Student Information System', 2008, 'Y'),
('Opensis Student Information System', 2009, 'No');

--
-- Dumping data for table `COURSES`
--

INSERT INTO `COURSES` (`syear`, `course_id`, `subject_id`, `school_id`, `grade_level`, `title`, `short_name`, `rollover_id`) VALUES
(2009, 1, 5, 1, NULL, 'Music', 'Mu', NULL),
(2009, 2, 5, 1, NULL, 'Painting', 'Pa', NULL),
(2009, 3, 5, 1, NULL, 'Sculpture', 'Scul', NULL),
(2009, 4, 1, 1, NULL, 'English', 'Eng', NULL),
(2009, 5, 1, 1, NULL, 'French', 'Fr', NULL),
(2009, 6, 6, 1, NULL, 'Lunch 1', 'Lu1', NULL),
(2009, 7, 6, 1, NULL, 'Lunch 2', 'Lu2', NULL),
(2009, 8, 2, 1, NULL, 'EveryDayMath', 'EDM', NULL),
(2009, 9, 4, 1, NULL, 'GeoScience', 'GSCI', NULL),
(2009, 10, 4, 1, NULL, 'Physics', 'Phy', NULL),
(2009, 11, 3, 1, NULL, 'US History', 'USH', NULL),
(2009, 12, 3, 1, NULL, 'World History', 'WH', NULL),
(2009, 13, 7, 1, NULL, 'Study Hall Tactics', 'SHT', NULL),
(2009, 14, 7, 1, NULL, 'Library Skills', 'LS', NULL),
(2009, 15, 8, 3, NULL, 'English', 'eng', NULL);

--
-- Dumping data for table `COURSE_PERIODS`
--

INSERT INTO `COURSE_PERIODS` (`syear`, `school_id`, `course_period_id`, `course_id`, `course_weight`, `title`, `short_name`, `period_id`, `mp`, `marking_period_id`, `teacher_id`, `room`, `total_seats`, `filled_seats`, `does_attendance`, `does_honor_roll`, `does_class_rank`, `gender_restriction`, `house_restriction`, `availability`, `parent_id`, `days`, `calendar_id`, `half_day`, `does_breakoff`, `rollover_id`, `grade_scale_id`, `credits`) VALUES
(2009, 1, 1, 14, NULL, 'Period 7 - Q1 - Lib101 - Teach T Teacher', 'Lib101', 7, 'QTR', 3, 2, 'Library', 24, 1, 'Y', NULL, NULL, 'N', NULL, NULL, 1, 'MTWHF', 1, NULL, 'Y', NULL, 1, 0.250),
(2009, 1, 2, 11, NULL, 'Period 6 - Q1 - USH1 - Teach T Teacher', 'USH1', 6, 'QTR', 3, 2, 'SS101', 24, 1, 'Y', 'Y', 'Y', 'N', NULL, NULL, 2, 'MTWHF', 1, NULL, 'Y', NULL, 1, 0.250),
(2009, 1, 4, 9, NULL, 'Period 5 - Q1 - GeoSci1 - Teach T Teacher', 'GeoSci1', 5, 'QTR', 3, 2, 'SCI101', 24, 1, 'Y', 'Y', 'Y', 'N', NULL, NULL, 4, 'MTWHF', 1, NULL, 'Y', NULL, 1, 0.250),
(2009, 1, 5, 8, NULL, 'Period 4 - Q1 - EDM1 - Teach T Teacher', 'EDM1', 4, 'QTR', 3, 2, 'M101', 24, 1, 'Y', 'Y', 'Y', 'N', NULL, NULL, 5, 'MTWHF', 1, NULL, 'Y', NULL, 1, 0.250),
(2009, 1, 6, 6, NULL, 'Period 3 - L1 - Teach T Teacher', 'L1', 3, 'FY', 11, 2, 'Cafe', 24, 1, NULL, NULL, NULL, 'N', NULL, NULL, 6, 'MTWHF', 1, NULL, NULL, NULL, NULL, NULL),
(2009, 1, 7, 4, NULL, 'Period 2 - Writing - Teach T Teacher', 'Writing', 2, 'FY', 11, 2, 'lang 101', 24, 1, 'Y', NULL, NULL, 'N', NULL, NULL, 7, 'MTWHF', 1, NULL, NULL, NULL, 1, 0.250),
(2009, 1, 8, 1, NULL, 'Period 1 - Q1 - Mu1 - Teach T Teacher', 'Mu1', 1, 'QTR', 3, 2, 'Mu101', 24, 1, 'Y', NULL, 'Y', 'N', NULL, NULL, 8, 'MTWHF', 1, NULL, 'Y', NULL, 1, 0.250);

--
-- Dumping data for table `COURSE_SUBJECTS`
--

INSERT INTO `COURSE_SUBJECTS` (`syear`, `school_id`, `subject_id`, `title`, `short_name`, `rollover_id`) VALUES
(2009, 1, 1, 'Languages', NULL, NULL),
(2009, 1, 2, 'Mathematics', NULL, NULL),
(2009, 1, 3, 'Social Studies', NULL, NULL),
(2009, 1, 4, 'Sciences', NULL, NULL),
(2009, 1, 5, 'Arts', NULL, NULL),
(2009, 1, 6, 'Lunch', NULL, NULL),
(2009, 1, 7, 'Study Skills', NULL, NULL),
(2009, 3, 8, 'Language', NULL, NULL);

--
-- Dumping data for table `CUSTOM_FIELDS`
--
INSERT INTO `CUSTOM_FIELDS` (`id`, `type`, `search`, `title`, `sort_order`, `select_options`, `category_id`, `system_field`, `required`, `default_selection`, `hide`) VALUES
('200000000', 'select', NULL, 'Gender', '0', 'Male\n  Female', '1', 'Y', 'Y', NULL, NULL),
('200000001', 'select', NULL, 'Ethnicity', '1', 'White, Non-Hispanic\n  Black, Non-Hispanic\n  Amer. Indian or Alaskan Native\n  Asian or Pacific Islander\n  Hispanic\n  Other', '1', 'Y', 'Y', NULL, NULL),
('200000002', 'text', NULL, 'Common Name', '2', NULL, '1', 'Y', NULL, NULL, NULL),
('200000003', 'text', NULL, 'Social Security', '3', NULL, '1', 'Y', NULL, NULL, 'Y'),
('200000004', 'date', NULL, 'Birthdate', '4', NULL, '1', 'Y', 'Y', NULL, NULL),
('200000005', 'select', NULL, 'Language', '5', 'English\n  Spanish', '1', 'Y', NULL, NULL, NULL),
('200000006', 'text', NULL, 'Physician', '6', NULL, '2', 'Y', NULL, NULL, NULL),
('200000007', 'text', NULL, 'Physician Phone', '7', NULL, '2', 'Y', NULL, NULL, NULL),
('200000008', 'text', NULL, 'Preferred Hospital', '8', NULL, '2', 'Y', NULL, NULL, NULL),
('200000009', 'textarea', NULL, 'Comments', '9', NULL, '2', 'Y', NULL, NULL, NULL),
('200000011', 'textarea', NULL, 'Doctor''s Note Comments', '11', NULL, '2', 'Y', NULL, NULL, NULL),
('200000012', 'date', NULL, 'Estimated Grad. Date', NULL, NULL, '1', 'Y', NULL, NULL, NULL);

--
-- Dumping data for table `ELIGIBILITY`
--

INSERT INTO `ELIGIBILITY` (`student_id`, `syear`, `school_date`, `period_id`, `eligibility_code`, `course_period_id`) VALUES
(1, 2009, '2009-07-01', 2, 'FAILING', 7),
(1, 2009, '2009-07-01', 1, 'PASSING', 8);

--
-- Dumping data for table `ELIGIBILITY_ACTIVITIES`
--

INSERT INTO `ELIGIBILITY_ACTIVITIES` (`id`, `syear`, `school_id`, `title`, `start_date`, `end_date`) VALUES
(1, 2008, 1, 'Boy''s Basketball', '2008-01-01', '2008-04-14'),
(2, 2008, 1, 'Chess Team', '2008-09-01', '2008-06-04'),
(3, 2008, 1, 'Girl''s Basketball', '2008-01-01', '2008-04-15'),
(4, 2009, 1, 'test', '2009-07-01', '2009-07-12'),
(5, 2009, 1, 'new ', '2009-07-01', '2009-07-31');

--
-- Dumping data for table `ELIGIBILITY_COMPLETED`
--

INSERT INTO `ELIGIBILITY_COMPLETED` (`staff_id`, `school_date`, `period_id`) VALUES
(2, '2009-07-01', 1);

--
-- Dumping data for table `FOOD_SERVICE_ITEMS`
--

INSERT INTO `FOOD_SERVICE_ITEMS` (`item_id`, `school_id`, `short_name`, `sort_order`, `description`, `icon`, `price`, `price_reduced`, `price_free`, `price_staff`) VALUES
(15, 1, 'En', 1, 'Entre', NULL, 0.25, 0.15, NULL, 0.75);

--
-- Dumping data for table `FOOD_SERVICE_MENUS`
--

INSERT INTO `FOOD_SERVICE_MENUS` (`menu_id`, `school_id`, `title`, `sort_order`) VALUES
(6, 1, 'Lunch', 1);

--
-- Dumping data for table `GRADEBOOK_ASSIGNMENTS`
--

INSERT INTO `GRADEBOOK_ASSIGNMENTS` (`assignment_id`, `staff_id`, `marking_period_id`, `course_period_id`, `course_id`, `assignment_type_id`, `title`, `assigned_date`, `due_date`, `points`, `description`) VALUES
(1, 2, 3, 8, NULL, 1, 'H1', '2009-06-27', NULL, 10, NULL),
(2, 2, 3, 8, NULL, 1, 'H2', '2009-06-27', NULL, 10, NULL),
(3, 2, 3, 8, NULL, 1, 'H3', '2009-06-27', NULL, 10, NULL),
(4, 2, 3, 8, NULL, 1, 'H4', '2009-06-27', NULL, 10, NULL),
(5, 2, 3, 8, NULL, 1, 'H5', '2009-06-27', NULL, 10, NULL),
(6, 2, 3, 8, NULL, 1, 'H6', '2009-06-27', NULL, 10, NULL),
(7, 2, 3, 8, NULL, 1, 'H7', '2009-06-27', NULL, 10, NULL),
(8, 2, 3, 8, NULL, 1, 'H8', '2009-06-27', NULL, 10, NULL),
(9, 2, 3, 8, NULL, 1, 'H9', '2009-06-27', NULL, 10, NULL),
(10, 2, 3, 8, NULL, 1, 'H10', '2009-06-27', NULL, 10, NULL),
(11, 2, 3, 8, NULL, 2, 'Q1', '2009-06-27', NULL, 10, NULL),
(12, 2, 3, 8, NULL, 2, 'Q2', '2009-06-27', NULL, 10, NULL),
(13, 2, 3, 8, NULL, 2, 'Q3', '2009-06-27', NULL, 10, NULL),
(14, 2, 3, 8, NULL, 2, 'Q4', '2009-06-27', NULL, 10, NULL),
(15, 2, 3, 8, NULL, 2, 'Q5', '2009-06-27', NULL, 10, ''),
(16, 2, 3, 8, NULL, 2, 'Q6', '2009-06-27', NULL, 10, NULL),
(17, 2, 3, 8, NULL, 2, 'Q7', '2009-06-27', NULL, 10, NULL),
(18, 2, 3, 8, NULL, 2, 'Q8', '2009-06-27', NULL, 10, NULL),
(19, 2, 3, 8, NULL, 2, 'Q9', '2009-06-27', NULL, 10, NULL),
(20, 2, 3, 8, NULL, 2, 'Q10', '2009-06-27', NULL, 10, NULL),
(21, 2, 3, 8, NULL, 3, 'T1', '2009-06-27', NULL, 25, NULL),
(22, 2, 3, 8, NULL, 3, 'T2', '2009-06-27', NULL, 25, NULL),
(23, 2, 3, 8, NULL, 3, 'T3', '2009-06-27', NULL, 25, NULL),
(24, 2, 3, 8, NULL, 3, 'T4', '2009-06-27', NULL, 25, NULL),
(25, 2, 3, 7, NULL, 6, 'T1', '2009-06-27', NULL, 25, ''),
(26, 2, 3, 7, NULL, 6, 'T2', '2009-06-27', NULL, 25, NULL),
(27, 2, 3, 7, NULL, 6, 'T3', '2009-06-27', NULL, 25, NULL),
(28, 2, 3, 7, NULL, 6, 'T4', '2009-06-27', NULL, 25, NULL),
(29, 2, 3, 7, NULL, 5, 'Q1', '2009-06-27', NULL, 25, NULL),
(30, 2, 3, 7, NULL, 5, 'Q2', '2009-06-27', NULL, 25, NULL),
(31, 2, 3, 7, NULL, 5, 'Q3', '2009-06-27', NULL, 25, NULL),
(32, 2, 3, 7, NULL, 5, 'Q4', '2009-06-27', NULL, 25, NULL),
(33, 2, 3, 7, NULL, 4, 'H1', '2009-06-27', NULL, 10, NULL),
(34, 2, 3, 7, NULL, 4, 'H2', '2009-06-27', NULL, 10, NULL),
(35, 2, 3, 7, NULL, 4, 'H3', '2009-06-27', NULL, 10, NULL),
(36, 2, 3, 7, NULL, 4, 'H4', '2009-06-27', NULL, 10, NULL),
(37, 2, 3, 7, NULL, 4, 'H5', '2009-06-27', NULL, 10, NULL),
(38, 2, 3, 7, NULL, 4, 'H6', '2009-06-27', NULL, 10, NULL),
(39, 2, 3, 7, NULL, 4, 'H7', '2009-06-27', NULL, 10, NULL),
(40, 2, 3, 7, NULL, 4, 'H8', '2009-06-27', NULL, 10, NULL),
(41, 2, 3, 7, NULL, 4, 'H9', '2009-06-27', NULL, 10, NULL),
(42, 2, 3, 7, NULL, 4, 'H10', '2009-06-27', NULL, 10, NULL),
(43, 2, 3, 5, NULL, 9, 'T1', '2009-06-27', NULL, 25, NULL),
(44, 2, 3, 5, NULL, 9, 'T2', '2009-06-27', NULL, 25, NULL),
(45, 2, 3, 5, NULL, 9, 'T3', '2009-06-27', NULL, 25, NULL),
(46, 2, 3, 5, NULL, 9, 'T4', '2009-06-27', NULL, 25, NULL),
(47, 2, 3, 5, NULL, 8, 'Q1', '2009-06-27', NULL, 25, NULL),
(48, 2, 3, 5, NULL, 8, 'Q2', '2009-06-27', NULL, 25, NULL),
(49, 2, 3, 5, NULL, 8, 'Q3', '2009-06-27', NULL, 25, NULL),
(50, 2, 3, 5, NULL, 8, 'Q4', '2009-06-27', NULL, 25, NULL),
(51, 2, 3, 5, NULL, 7, 'H1', '2009-06-27', NULL, 20, NULL),
(52, 2, 3, 5, NULL, 7, 'H2', '2009-06-27', NULL, 20, NULL),
(53, 2, 3, 5, NULL, 7, 'H3', '2009-06-27', NULL, 20, NULL),
(54, 2, 3, 5, NULL, 7, 'H4', '2009-06-27', NULL, 20, NULL),
(55, 2, 3, 5, NULL, 7, 'H5', '2009-06-27', NULL, 20, NULL),
(56, 2, 3, 4, NULL, 12, 'H1', '2009-06-27', NULL, 20, NULL),
(57, 2, 3, 4, NULL, 12, 'H2', '2009-06-27', NULL, 20, NULL),
(58, 2, 3, 4, NULL, 12, 'H3', '2009-06-27', NULL, 20, NULL),
(59, 2, 3, 4, NULL, 12, 'H4', '2009-06-27', NULL, 20, NULL),
(60, 2, 3, 4, NULL, 12, 'H5', '2009-06-27', NULL, 20, NULL),
(61, 2, 3, 4, NULL, 11, 'Q1', '2009-06-27', NULL, 25, NULL),
(62, 2, 3, 4, NULL, 11, 'Q2', '2009-06-27', NULL, 25, NULL),
(63, 2, 3, 4, NULL, 11, 'Q3', '2009-06-27', NULL, 25, NULL),
(64, 2, 3, 4, NULL, 11, 'Q4', '2009-06-27', NULL, 25, NULL),
(65, 2, 3, 4, NULL, 10, 'T1', '2009-06-27', NULL, 25, NULL),
(66, 2, 3, 4, NULL, 10, 'T2', '2009-06-27', NULL, 25, NULL),
(67, 2, 3, 4, NULL, 10, 'T3', '2009-06-27', NULL, 25, NULL),
(68, 2, 3, 4, NULL, 10, 'T4', '2009-06-27', NULL, 25, NULL),
(69, 2, 3, 2, NULL, 13, 'H1', '2009-06-27', NULL, 20, NULL),
(70, 2, 3, 2, NULL, 13, 'H2', '2009-06-27', NULL, 20, NULL),
(71, 2, 3, 2, NULL, 13, 'H3', '2009-06-27', NULL, 20, NULL),
(72, 2, 3, 2, NULL, 13, 'H4', '2009-06-27', NULL, 20, NULL),
(73, 2, 3, 2, NULL, 13, 'H5', '2009-06-27', NULL, 20, NULL),
(74, 2, 3, 2, NULL, 14, 'Q1', '2009-06-27', NULL, 25, NULL),
(75, 2, 3, 2, NULL, 14, 'Q2', '2009-06-27', NULL, 25, NULL),
(76, 2, 3, 2, NULL, 14, 'Q3', '2009-06-27', NULL, 25, NULL),
(77, 2, 3, 2, NULL, 14, 'Q4', '2009-06-27', NULL, 25, NULL),
(78, 2, 3, 2, NULL, 15, 'T1', '2009-06-27', NULL, 25, NULL),
(79, 2, 3, 2, NULL, 15, 'T2', '2009-06-27', NULL, 25, NULL),
(80, 2, 3, 2, NULL, 15, 'T3', '2009-06-27', NULL, 25, NULL),
(81, 2, 3, 2, NULL, 15, 'T4', '2009-06-27', NULL, 25, NULL),
(82, 2, 3, 1, NULL, 16, 'H1', '2009-06-27', NULL, 20, NULL),
(83, 2, 3, 1, NULL, 16, 'H2', '2009-06-27', NULL, 20, NULL),
(84, 2, 3, 1, NULL, 16, 'H3', '2009-06-27', NULL, 20, NULL),
(85, 2, 3, 1, NULL, 16, 'H4', '2009-06-27', NULL, 20, NULL),
(86, 2, 3, 1, NULL, 16, 'H5', '2009-06-27', NULL, 20, NULL),
(87, 2, 3, 1, NULL, 17, 'Q1', '2009-06-27', NULL, 25, NULL),
(88, 2, 3, 1, NULL, 17, 'Q2', '2009-06-27', NULL, 25, NULL),
(89, 2, 3, 1, NULL, 17, 'Q3', '2009-06-27', NULL, 25, NULL),
(90, 2, 3, 1, NULL, 17, 'Q4', '2009-06-27', NULL, 25, NULL),
(91, 2, 3, 1, NULL, 18, 'T1', '2009-06-27', NULL, 25, NULL),
(92, 2, 3, 1, NULL, 18, 'T2', '2009-06-27', NULL, 25, NULL),
(93, 2, 3, 1, NULL, 18, 'T3', '2009-06-27', NULL, 25, NULL),
(94, 2, 3, 1, NULL, 18, 'T4', '2009-06-27', NULL, 25, '');

--
-- Dumping data for table `GRADEBOOK_ASSIGNMENT_TYPES`
--

INSERT INTO `GRADEBOOK_ASSIGNMENT_TYPES` (`assignment_type_id`, `staff_id`, `course_id`, `title`, `final_grade_percent`) VALUES
(1, 2, 1, 'Homework', 0.20000),
(2, 2, 1, 'Quiz', 0.40000),
(3, 2, 1, 'Tests', 0.40000),
(4, 2, 4, 'Homework', 0.20000),
(5, 2, 4, 'Quiz', 0.40000),
(6, 2, 4, 'Test', 0.40000),
(7, 2, 8, 'Homework', 0.20000),
(8, 2, 8, 'Quiz', 0.40000),
(9, 2, 8, 'Test', 0.40000),
(10, 2, 9, 'Test', 0.40000),
(11, 2, 9, 'Quiz', 0.40000),
(12, 2, 9, 'Homework', 0.20000),
(13, 2, 11, 'Homework', 0.20000),
(14, 2, 11, 'Quiz', 0.40000),
(15, 2, 11, 'Test', 0.40000),
(16, 2, 14, 'Homework', 0.20000),
(17, 2, 14, 'Quiz', 0.40000),
(18, 2, 14, 'Test', 0.40000);

--
-- Dumping data for table `GRADEBOOK_GRADES`
--

INSERT INTO `GRADEBOOK_GRADES` (`student_id`, `period_id`, `course_period_id`, `assignment_id`, `points`, `comment`) VALUES
(1, 1, 8, 24, 23.00, NULL),
(1, 1, 8, 23, 24.00, NULL),
(1, 1, 8, 22, 21.00, NULL),
(1, 1, 8, 21, 25.00, NULL),
(1, 1, 8, 20, 8.00, NULL),
(1, 1, 8, 19, 8.00, NULL),
(1, 1, 8, 18, 5.00, NULL),
(1, 1, 8, 17, 7.00, NULL),
(1, 1, 8, 16, 8.00, NULL),
(1, 1, 8, 15, 5.00, NULL),
(1, 1, 8, 14, 7.00, NULL),
(1, 1, 8, 13, 7.00, NULL),
(1, 1, 8, 12, 10.00, NULL),
(1, 1, 8, 11, 9.00, NULL),
(1, 1, 8, 10, 8.00, NULL),
(1, 1, 8, 9, 7.00, NULL),
(1, 1, 8, 8, 8.00, NULL),
(1, 1, 8, 7, 6.00, NULL),
(1, 1, 8, 6, 8.00, NULL),
(1, 1, 8, 5, 9.00, NULL),
(1, 1, 8, 4, 6.00, NULL),
(1, 1, 8, 3, 7.00, NULL),
(1, 1, 8, 2, 7.00, NULL),
(1, 1, 8, 1, 8.00, NULL),
(1, 2, 7, 42, 9.00, NULL),
(1, 2, 7, 41, 8.00, NULL),
(1, 2, 7, 40, 7.00, NULL),
(1, 2, 7, 39, 6.00, NULL),
(1, 2, 7, 38, 8.00, NULL),
(1, 2, 7, 37, 9.00, NULL),
(1, 2, 7, 36, 6.00, NULL),
(1, 2, 7, 35, 8.00, NULL),
(1, 2, 7, 34, 7.00, NULL),
(1, 2, 7, 33, 6.00, NULL),
(1, 2, 7, 32, 23.00, NULL),
(1, 2, 7, 31, 19.00, NULL),
(1, 2, 7, 30, 22.00, NULL),
(1, 2, 7, 29, 21.00, NULL),
(1, 2, 7, 28, 23.00, NULL),
(1, 2, 7, 27, 25.00, NULL),
(1, 2, 7, 26, 22.00, NULL),
(1, 2, 7, 25, 24.00, NULL),
(1, 4, 5, 55, 19.00, '2'),
(1, 4, 5, 54, 19.00, NULL),
(1, 4, 5, 53, 18.00, NULL),
(1, 4, 5, 52, 16.00, NULL),
(1, 4, 5, 51, 17.00, NULL),
(1, 4, 5, 50, 23.00, NULL),
(1, 4, 5, 49, 21.00, NULL),
(1, 4, 5, 48, 24.00, NULL),
(1, 4, 5, 47, 23.00, NULL),
(1, 4, 5, 46, 22.00, NULL),
(1, 4, 5, 45, 17.00, NULL),
(1, 4, 5, 44, 19.00, NULL),
(1, 4, 5, 43, 21.00, NULL),
(1, 5, 4, 68, 23.00, '1'),
(1, 5, 4, 67, 24.00, NULL),
(1, 5, 4, 66, 25.00, NULL),
(1, 5, 4, 65, 21.00, NULL),
(1, 5, 4, 64, 24.00, NULL),
(1, 5, 4, 63, 25.00, NULL),
(1, 5, 4, 62, 22.00, NULL),
(1, 5, 4, 61, 21.00, NULL),
(1, 5, 4, 60, 19.00, NULL),
(1, 5, 4, 59, 20.00, NULL),
(1, 5, 4, 58, 18.00, NULL),
(1, 5, 4, 57, 17.00, NULL),
(1, 5, 4, 56, 19.00, NULL),
(1, 6, 2, 81, 24.00, NULL),
(1, 6, 2, 80, 24.00, NULL),
(1, 6, 2, 79, 21.00, NULL),
(1, 6, 2, 78, 23.00, NULL),
(1, 6, 2, 77, 25.00, NULL),
(1, 6, 2, 76, 24.00, NULL),
(1, 6, 2, 75, 23.00, NULL),
(1, 6, 2, 74, 22.00, NULL),
(1, 6, 2, 73, 19.00, NULL),
(1, 6, 2, 72, 18.00, NULL),
(1, 6, 2, 71, 19.00, NULL),
(1, 6, 2, 70, 18.00, NULL),
(1, 6, 2, 69, 20.00, NULL),
(1, 7, 1, 94, 24.00, '1'),
(1, 7, 1, 93, 23.00, NULL),
(1, 7, 1, 92, 22.00, NULL),
(1, 7, 1, 91, 25.00, NULL),
(1, 7, 1, 90, 25.00, NULL),
(1, 7, 1, 89, 24.00, NULL),
(1, 7, 1, 88, 25.00, NULL),
(1, 7, 1, 87, 22.00, NULL),
(1, 7, 1, 86, 19.00, NULL),
(1, 7, 1, 85, 20.00, NULL),
(1, 7, 1, 84, 19.00, NULL),
(1, 7, 1, 83, 18.00, NULL),
(1, 7, 1, 82, 19.00, NULL);

--
-- Dumping data for table `PORTAL_NOTES`
--

INSERT INTO `PORTAL_NOTES` (`id`, `school_id`, `syear`, `title`, `content`, `sort_order`, `published_user`, `published_date`, `start_date`, `end_date`, `published_profiles`) VALUES
(1, 1, 2009, 'Welcome to openSIS', 'Welcome to openSIS, the premier open source student information system!', 1, 1, '2009-06-30 15:04:59', '2009-01-01', '2010-01-01', ',admin,teacher,parent,0,1,2,3,');

--
-- Dumping data for table `PROFILE_EXCEPTIONS`
--

INSERT INTO `PROFILE_EXCEPTIONS` (`profile_id`, `modname`, `can_use`, `can_edit`) VALUES
(0, 'School_Setup/Schools.php', 'Y', NULL),
(0, 'School_Setup/Calendar.php', 'Y', NULL),
(0, 'Students/Student.php', 'Y', NULL),
(0, 'Students/Student.php&category_id=1', 'Y', NULL),
(0, 'Students/Student.php&category_id=3', 'Y', NULL),
(0, 'Scheduling/Schedule.php', 'Y', NULL),
(0, 'Scheduling/Requests.php', 'Y', NULL),
(0, 'Grades/StudentGrades.php', 'Y', NULL),
(0, 'Grades/FinalGrades.php', 'Y', NULL),
(0, 'Grades/ReportCards.php', 'Y', NULL),
(0, 'Grades/Transcripts.php', 'Y', NULL),
(0, 'Grades/GPARankList.php', 'Y', NULL),
(0, 'Attendance/StudentSummary.php', 'Y', NULL),
(0, 'Attendance/DailySummary.php', 'Y', NULL),
(0, 'Eligibility/Student.php', 'Y', NULL),
(0, 'Eligibility/StudentList.php', 'Y', NULL),
(0, 'Food_Service/Accounts.php', 'Y', NULL),
(0, 'Food_Service/Statements.php', 'Y', NULL),
(0, 'Food_Service/DailyMenus.php', 'Y', NULL),
(0, 'Food_Service/MenuItems.php', 'Y', NULL),
(1, 'School_Setup/PortalNotes.php', 'Y', 'Y'),
(1, 'School_Setup/Schools.php', 'Y', 'Y'),
(1, 'School_Setup/Schools.php?new_school=true', 'Y', 'Y'),
(1, 'School_Setup/CopySchool.php', 'Y', 'Y'),
(1, 'School_Setup/MarkingPeriods.php', 'Y', 'Y'),
(1, 'School_Setup/Calendar.php', 'Y', 'Y'),
(1, 'School_Setup/Periods.php', 'Y', 'Y'),
(1, 'School_Setup/GradeLevels.php', 'Y', 'Y'),
(1, 'School_Setup/Rollover.php', 'Y', 'Y'),
(1, 'Students/Student.php', 'Y', 'Y'),
(1, 'Students/Student.php&include=General_Info&student_id=new', 'Y', 'Y'),
(1, 'Students/AssignOtherInfo.php', 'Y', 'Y'),
(1, 'Students/AddUsers.php', 'Y', 'Y'),
(1, 'Students/AdvancedReport.php', 'Y', 'Y'),
(1, 'Students/AddDrop.php', 'Y', 'Y'),
(1, 'Students/Letters.php', 'Y', 'Y'),
(1, 'Students/MailingLabels.php', 'Y', 'Y'),
(1, 'Students/StudentLabels.php', 'Y', 'Y'),
(1, 'Students/PrintStudentInfo.php', 'Y', 'Y'),
(1, 'Students/StudentFields.php', 'Y', 'Y'),
(1, 'Students/AddressFields.php', 'Y', 'Y'),
(1, 'Students/PeopleFields.php', 'Y', 'Y'),
(1, 'Students/EnrollmentCodes.php', 'Y', 'Y'),
(1, 'Students/Upload.php?modfunc=edit', 'Y', 'Y'),
(1, 'Students/Upload.php', 'Y', 'Y'),
(1, 'Students/Student.php&category_id=1', 'Y', 'Y'),
(1, 'Students/Student.php&category_id=3', 'Y', 'Y'),
(1, 'Students/Student.php&category_id=2', 'Y', 'Y'),
(1, 'Users/User.php', 'Y', 'Y'),
(1, 'Users/User.php&category_id=1', 'Y', NULL),
(1, 'Users/User.php&staff_id=new', 'Y', 'Y'),
(1, 'Users/AddStudents.php', 'Y', 'Y'),
(1, 'Users/Preferences.php', 'Y', 'Y'),
(1, 'Users/Profiles.php', 'Y', 'Y'),
(1, 'Users/Exceptions.php', 'Y', 'Y'),
(1, 'Users/UserFields.php', 'Y', 'Y'),
(1, 'Users/TeacherPrograms.php?include=Grades/InputFinalGrades.php', 'Y', 'Y'),
(1, 'Users/TeacherPrograms.php?include=Grades/Grades.php', 'Y', 'Y'),
(1, 'Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php', 'Y', 'Y'),
(1, 'Users/TeacherPrograms.php?include=Eligibility/EnterEligibility.php', 'Y', 'Y'),
(1, 'Scheduling/Schedule.php', 'Y', 'Y'),
(1, 'Scheduling/Requests.php', 'Y', 'Y'),
(1, 'Scheduling/MassSchedule.php', 'Y', 'Y'),
(1, 'Scheduling/MassRequests.php', 'Y', 'Y'),
(1, 'Scheduling/MassDrops.php', 'Y', 'Y'),
(1, 'Scheduling/ScheduleReport.php', 'Y', 'Y'),
(1, 'Scheduling/RequestsReport.php', 'Y', 'Y'),
(1, 'Scheduling/UnfilledRequests.php', 'Y', 'Y'),
(1, 'Scheduling/IncompleteSchedules.php', 'Y', 'Y'),
(1, 'Scheduling/AddDrop.php', 'Y', 'Y'),
(1, 'Scheduling/PrintSchedules.php', 'Y', 'Y'),
(1, 'Scheduling/PrintRequests.php', 'Y', 'Y'),
(1, 'Scheduling/PrintClassLists.php', 'Y', 'Y'),
(1, 'Scheduling/PrintClassPictures.php', 'Y', 'Y'),
(1, 'Scheduling/Courses.php', 'Y', 'Y'),
(1, 'Scheduling/Scheduler.php', 'Y', 'Y'),
(1, 'Grades/ReportCards.php', 'Y', 'Y'),
(1, 'Grades/CalcGPA.php', 'Y', 'Y'),
(1, 'Grades/Transcripts.php', 'Y', 'Y'),
(1, 'Grades/TeacherCompletion.php', 'Y', 'Y'),
(1, 'Grades/GradeBreakdown.php', 'Y', 'Y'),
(1, 'Grades/FinalGrades.php', 'Y', 'Y'),
(1, 'Grades/GPARankList.php', 'Y', 'Y'),
(1, 'Grades/ReportCardGrades.php', 'Y', 'Y'),
(1, 'Grades/ReportCardComments.php', 'Y', 'Y'),
(1, 'Grades/FixGPA.php', 'Y', 'Y'),
(1, 'Grades/EditReportCardGrades.php', 'Y', 'Y'),
(1, 'Grades/EditHistoryMarkingPeriods.php', 'Y', 'Y'),
(1, 'Attendance/Administration.php', 'Y', 'Y'),
(1, 'Attendance/AddAbsences.php', 'Y', 'Y'),
(1, 'Attendance/Percent.php', 'Y', 'Y'),
(1, 'Attendance/Percent.php?list_by_day=true', 'Y', 'Y'),
(1, 'Attendance/DailySummary.php', 'Y', 'Y'),
(1, 'Attendance/StudentSummary.php', 'Y', 'Y'),
(1, 'Attendance/TeacherCompletion.php', 'Y', 'Y'),
(1, 'Attendance/DuplicateAttendance.php', 'Y', 'Y'),
(1, 'Attendance/AttendanceCodes.php', 'Y', 'Y'),
(1, 'Attendance/FixDailyAttendance.php', 'Y', 'Y'),
(1, 'Eligibility/Student.php', 'Y', 'Y'),
(1, 'Eligibility/AddActivity.php', 'Y', 'Y'),
(1, 'Eligibility/StudentList.php', 'Y', 'Y'),
(1, 'Eligibility/TeacherCompletion.php', 'Y', 'Y'),
(1, 'Eligibility/Activities.php', 'Y', 'Y'),
(1, 'Eligibility/EntryTimes.php', 'Y', 'Y'),
(1, 'Tools/Backup.php', 'Y', 'Y'),
(1, 'Tools/Restore.php', 'Y', 'Y'),
(1, 'Students/Upload.php', 'Y', 'Y'),
(1, 'Students/Upload.php?modfunc=edit', 'Y', 'Y'),
(1, 'Food_Service/Accounts.php', 'Y', 'Y'),
(1, 'Food_Service/Statements.php', 'Y', 'Y'),
(1, 'Food_Service/Transactions.php', 'Y', 'Y'),
(1, 'Food_Service/ServeMenus.php', 'Y', 'Y'),
(1, 'Food_Service/ActivityReport.php', 'Y', 'Y'),
(1, 'Food_Service/TransactionsReport.php', 'Y', 'Y'),
(1, 'Food_Service/MenuReports.php', 'Y', 'Y'),
(1, 'Food_Service/Reminders.php', 'Y', 'Y'),
(1, 'Food_Service/DailyMenus.php', 'Y', 'Y'),
(1, 'Food_Service/MenuItems.php', 'Y', 'Y'),
(1, 'Food_Service/Menus.php', 'Y', 'Y'),
(1, 'Food_Service/Kiosk.php', 'Y', 'Y'),
(2, 'School_Setup/Schools.php', 'Y', NULL),
(2, 'School_Setup/MarkingPeriods.php', 'Y', NULL),
(2, 'School_Setup/Calendar.php', 'Y', NULL),
(2, 'Students/Student.php', 'Y', NULL),
(2, 'Students/AddUsers.php', 'Y', NULL),
(2, 'Students/AdvancedReport.php', 'Y', NULL),
(2, 'Students/Student.php&category_id=1', 'Y', NULL),
(2, 'Students/Student.php&category_id=3', 'Y', NULL),
(2, 'Students/Student.php&category_id=4', 'Y', 'Y'),
(2, 'Users/User.php', 'Y', NULL),
(2, 'Users/User.php&category_id=1', 'Y', NULL),
(2, 'Users/User.php&category_id=2', 'Y', NULL),
(2, 'Users/Preferences.php', 'Y', NULL),
(2, 'Scheduling/Schedule.php', 'Y', NULL),
(2, 'Scheduling/PrintSchedules.php', 'Y', NULL),
(2, 'Scheduling/PrintClassLists.php', 'Y', NULL),
(2, 'Scheduling/PrintClassPictures.php', 'Y', NULL),
(2, 'Grades/InputFinalGrades.php', 'Y', NULL),
(2, 'Grades/ReportCards.php', 'Y', NULL),
(2, 'Grades/Grades.php', 'Y', NULL),
(2, 'Grades/Assignments.php', 'Y', NULL),
(2, 'Grades/AnomalousGrades.php', 'Y', NULL),
(2, 'Grades/Configuration.php', 'Y', NULL),
(2, 'Grades/ProgressReports.php', 'Y', NULL),
(2, 'Grades/StudentGrades.php', 'Y', NULL),
(2, 'Grades/FinalGrades.php', 'Y', NULL),
(2, 'Grades/ReportCardGrades.php', 'Y', NULL),
(2, 'Grades/ReportCardComments.php', 'Y', NULL),
(2, 'Attendance/TakeAttendance.php', 'Y', NULL),
(2, 'Attendance/DailySummary.php', 'Y', NULL),
(2, 'Attendance/StudentSummary.php', 'Y', NULL),
(2, 'Eligibility/EnterEligibility.php', 'Y', NULL),
(2, 'Food_Service/Accounts.php', 'Y', NULL),
(2, 'Food_Service/Statements.php', 'Y', NULL),
(2, 'Food_Service/DailyMenus.php', 'Y', NULL),
(2, 'Food_Service/MenuItems.php', 'Y', NULL),
(3, 'Attendance/StudentSummary.php', 'Y', NULL),
(3, 'Attendance/DailySummary.php', 'Y', NULL),
(3, 'Eligibility/Student.php', 'Y', NULL),
(3, 'Eligibility/StudentList.php', 'Y', NULL),
(3, 'School_Setup/Schools.php', 'Y', NULL),
(3, 'School_Setup/Calendar.php', 'Y', NULL),
(3, 'Students/Student.php', 'Y', NULL),
(3, 'Students/Student.php&category_id=1', 'Y', 'Y'),
(3, 'Students/Student.php&category_id=3', 'Y', 'Y'),
(3, 'Users/User.php', 'Y', NULL),
(3, 'Users/User.php&category_id=1', 'Y', 'Y'),
(3, 'Users/Preferences.php', 'Y', NULL),
(3, 'Scheduling/Schedule.php', 'Y', NULL),
(3, 'Scheduling/Requests.php', 'Y', NULL),
(3, 'Grades/StudentGrades.php', 'Y', NULL),
(3, 'Grades/FinalGrades.php', 'Y', NULL),
(3, 'Grades/ReportCards.php', 'Y', NULL),
(3, 'Grades/Transcripts.php', 'Y', NULL),
(3, 'Grades/GPARankList.php', 'Y', NULL),
(3, 'Food_Service/Accounts.php', 'Y', NULL),
(3, 'Food_Service/Statements.php', 'Y', NULL),
(3, 'Food_Service/DailyMenus.php', 'Y', NULL),
(3, 'Food_Service/MenuItems.php', 'Y', NULL),
(3, 'Students/Student.php&category_id=2', 'Y', NULL),
(3, 'Students/Student.php&category_id=4', 'Y', NULL),
(3, 'Students/Student.php&category_id=5', 'Y', NULL),
(3, 'Users/User.php&category_id=2', 'Y', NULL),
(3, 'Users/User.php&category_id=3', 'Y', NULL),
(3, 'Scheduling/PrintClassPictures.php', 'Y', NULL);

--
-- Dumping data for table `PROGRAM_CONFIG`
--

INSERT INTO `PROGRAM_CONFIG` (`syear`, `school_id`, `program`, `title`, `value`) VALUES
(2008, 1, 'eligibility', 'START_DAY', '1'),
(2008, 1, 'eligibility', 'START_HOUR', '23'),
(2008, 1, 'eligibility', 'START_MINUTE', '30'),
(2008, 1, 'eligibility', 'START_M', 'PM'),
(2008, 1, 'eligibility', 'END_DAY', '5'),
(2008, 1, 'eligibility', 'END_HOUR', '23'),
(2008, 1, 'eligibility', 'END_MINUTE', '30'),
(2008, 1, 'eligibility', 'END_M', 'PM'),
(2009, 1, 'eligibility', 'START_DAY', '1'),
(2009, 1, 'eligibility', 'START_HOUR', '23'),
(2009, 1, 'eligibility', 'START_MINUTE', '30'),
(2009, 1, 'eligibility', 'START_M', 'PM'),
(2009, 1, 'eligibility', 'END_DAY', '5'),
(2009, 1, 'eligibility', 'END_HOUR', '23'),
(2009, 1, 'eligibility', 'END_MINUTE', '30'),
(2009, 1, 'eligibility', 'END_M', 'PM');

--
-- Dumping data for table `PROGRAM_USER_CONFIG`
--

INSERT INTO `PROGRAM_USER_CONFIG` (`user_id`, `program`, `title`, `value`) VALUES
(1, 'Preferences', 'THEME', 'Green'),
(1, 'Preferences', 'MONTH', 'M'),
(1, 'Preferences', 'DAY', 'j'),
(1, 'Preferences', 'YEAR', 'Y'),
(1, 'Preferences', 'HIDDEN', 'Y'),
(2, 'Gradebook', '3-6', ''),
(2, 'Gradebook', '3-5', ''),
(2, 'Gradebook', '3-4', ''),
(2, 'Gradebook', '3-3', ''),
(2, 'Gradebook', '3-2', ''),
(2, 'Gradebook', '3-1', ''),
(2, 'Gradebook', 'COMMENT_A', ''),
(2, 'Gradebook', 'LATENCY', '0'),
(2, 'Gradebook', 'ANOMALOUS_MAX', '100'),
(2, 'Gradebook', 'ELIGIBILITY_CUMULITIVE', 'Y'),
(2, 'Gradebook', 'DEFAULT_ASSIGNED', 'Y'),
(2, 'Gradebook', 'ASSIGNMENT_SORTING', 'ASSIGNMENT_ID'),
(2, 'Gradebook', 'ROUNDING', ''),
(2, 'Gradebook', '3-7', ''),
(2, 'Gradebook', '3-8', ''),
(2, 'Gradebook', '3-9', ''),
(2, 'Gradebook', '3-10', ''),
(2, 'Gradebook', '3-11', ''),
(2, 'Gradebook', '3-12', ''),
(2, 'Gradebook', '3-14', ''),
(2, 'Gradebook', '3-13', ''),
(2, 'Gradebook', '3-15', ''),
(2, 'Preferences', 'HIGHLIGHT', '#f3bb96'),
(2, 'Preferences', 'MONTH', 'M'),
(2, 'Preferences', 'DAY', 'j'),
(2, 'Preferences', 'YEAR', 'Y'),
(2, 'Preferences', 'HIDDEN', 'Y'),
(2, 'Preferences', 'THEME', 'Green'),
(2, 'Gradebook', 'WEIGHT', 'Y');

--
-- Dumping data for table `REPORT_CARD_COMMENTS`
--

INSERT INTO `REPORT_CARD_COMMENTS` (`id`, `syear`, `school_id`, `course_id`, `sort_order`, `title`) VALUES
(1, 2009, 1, NULL, 1, 'Fails to Meet Course Requirements'),
(2, 2009, 1, NULL, 2, 'Comes to Class Unprepared'),
(3, 2009, 1, NULL, 3, 'Exerts Positive Influence in Class');

--
-- Dumping data for table `REPORT_CARD_GRADES`
--

INSERT INTO `REPORT_CARD_GRADES` (`id`, `syear`, `school_id`, `title`, `sort_order`, `gpa_value`, `break_off`, `comment`, `grade_scale_id`, `unweighted_gp`) VALUES
(1, 2009, 1, 'A+', 1, 12.00, 97, 'Consistently superior', 2, 12.00),
(2, 2009, 1, 'A', 2, 11.00, 93, 'Superior', 2, 11.00),
(3, 2009, 1, 'A-', 3, 10.00, 90, NULL, 2, 10.00),
(4, 2009, 1, 'B+', 4, 9.00, 87, NULL, 2, 9.00),
(5, 2009, 1, 'B', 5, 8.00, 83, 'Above average', 2, 8.00),
(6, 2009, 1, 'B-', 6, 7.00, 80, NULL, 2, 7.00),
(7, 2009, 1, 'C+', 7, 6.00, 77, NULL, 2, 6.00),
(8, 2009, 1, 'C', 8, 5.00, 73, 'Average', 2, 5.00),
(9, 2009, 1, 'C-', 9, 4.00, 70, NULL, 2, 4.00),
(10, 2009, 1, 'D+', 10, 3.00, 67, NULL, 2, 3.00),
(11, 2009, 1, 'D', 11, 2.00, 63, 'Below average', 2, 2.00),
(12, 2009, 1, 'D-', 12, 1.00, 60, NULL, 2, 1.00),
(13, 2009, 1, 'F', 13, 0.00, 0, 'Failing', 2, 0.00),
(14, 2009, 1, 'I', 14, 0.00, 0, 'Incomplete', 2, 0.00),
(15, 2009, 1, 'N/A', 15, 0.00, NULL, NULL, 2, 0.00),
(16, 2009, 1, 'A+', 1, 4.00, 97, '', 1, NULL),
(17, 2009, 1, 'A', 2, 3.67, 93, '', 1, NULL),
(18, 2009, 1, 'A-', 3, 3.33, 90, '', 1, NULL),
(19, 2009, 1, 'B+', 4, 3.00, 87, '', 1, NULL),
(20, 2009, 1, 'B', 5, 2.67, 83, '', 1, NULL),
(21, 2009, 1, 'B-', 6, 2.33, 80, '', 1, NULL),
(22, 2009, 1, 'C+', 7, 2.00, 77, '', 1, NULL),
(23, 2009, 1, 'C', 8, 1.67, 73, '', 1, NULL),
(24, 2009, 1, 'C-', 9, 1.33, 70, '', 1, NULL),
(25, 2009, 1, 'D+', 10, 1.00, 67, '', 1, NULL),
(26, 2009, 1, 'D', 11, 0.67, 63, '', 1, NULL),
(27, 2009, 1, 'D-', 12, 0.33, 60, '', 1, NULL),
(28, 2009, 1, 'F', 13, 0.00, 59, '', 1, NULL),
(29, 2009, 1, 'Inc', 14, 0.00, 0, NULL, 1, NULL);

--
-- Dumping data for table `REPORT_CARD_GRADE_SCALES`
--

INSERT INTO `REPORT_CARD_GRADE_SCALES` (`id`, `syear`, `school_id`, `title`, `comment`, `sort_order`, `rollover_id`, `gp_scale`) VALUES
(1, 2009, 1, 'Main', NULL, 1, NULL, 4.000);

--
-- Dumping data for table `SCHEDULE`
--

INSERT INTO `SCHEDULE` (`syear`, `school_id`, `student_id`, `start_date`, `end_date`, `modified_date`, `modified_by`, `course_id`, `course_weight`, `course_period_id`, `mp`, `marking_period_id`, `scheduler_lock`, `id`) VALUES
(2009, 1, 1, '2009-07-01', NULL, NULL, NULL, 1, NULL, 8, 'QTR', 3, NULL, NULL),
(2009, 1, 1, '2009-07-01', NULL, NULL, NULL, 4, NULL, 7, 'FY', 11, NULL, NULL),
(2009, 1, 1, '2009-07-01', NULL, NULL, NULL, 6, NULL, 6, 'FY', 11, NULL, NULL),
(2009, 1, 1, '2009-07-01', NULL, NULL, NULL, 8, NULL, 5, 'QTR', 3, NULL, NULL),
(2009, 1, 1, '2009-07-01', NULL, NULL, NULL, 9, NULL, 4, 'QTR', 3, NULL, NULL),
(2009, 1, 1, '2009-07-01', NULL, NULL, NULL, 11, NULL, 2, 'QTR', 3, NULL, NULL),
(2009, 1, 1, '2009-07-01', NULL, NULL, NULL, 14, NULL, 1, 'QTR', 3, NULL, NULL);

--
-- Dumping data for table `SCHOOLS`
--

INSERT INTO `SCHOOLS` (`syear`, `id`, `title`, `address`, `city`, `state`, `zipcode`, `area_code`, `phone`, `principal`, `www_address`, `e_mail`, `ceeb`, `reporting_gp_scale`) VALUES
(2009, 1, 'Demo School', '922 Pathview Court', 'Dacula', 'Georgia', '30019', NULL, NULL, 'Mr. Principal', 'www.os4ed.com', NULL, NULL, 4.000);

--
-- Dumping data for table `SCHOOL_GRADELEVELS`
--

INSERT INTO `SCHOOL_GRADELEVELS` (`id`, `school_id`, `short_name`, `title`, `next_grade_id`, `sort_order`) VALUES
(1, 1, 'Fr', 'Freshman', 2, 1),
(2, 1, 'So', 'Sophomore', 3, 2),
(3, 1, 'Jr', 'Junior', 4, 3),
(4, 1, 'Sr', 'Senior', NULL, 4);

--
-- Dumping data for table `SCHOOL_PERIODS`
--

INSERT INTO `SCHOOL_PERIODS` (`period_id`, `syear`, `school_id`, `sort_order`, `title`, `short_name`, `length`, `block`, `attendance`, `rollover_id`, `start_time`, `end_time`) VALUES
(1, 2009, 1, 1, 'Period 1', 'P1', 50, NULL, 'Y', NULL, NULL, NULL),
(2, 2009, 1, 2, 'Period 2', 'P2', 50, NULL, 'Y', NULL, NULL, NULL),
(3, 2009, 1, 3, 'Period 3', 'P3', 50, NULL, 'Y', NULL, NULL, NULL),
(4, 2009, 1, 4, 'Period 4', 'P4', 50, NULL, 'Y', NULL, NULL, NULL),
(5, 2009, 1, 5, 'Period 5', 'P5', 50, NULL, 'Y', NULL, NULL, NULL),
(6, 2009, 1, 6, 'Period 6', 'P6', 50, NULL, 'Y', NULL, NULL, NULL),
(7, 2009, 1, 7, 'Period 7', 'P7', 50, NULL, 'Y', NULL, NULL, NULL);

--
-- Dumping data for table `SCHOOL_PROGRESS_PERIODS`
--

INSERT INTO `SCHOOL_PROGRESS_PERIODS` (`marking_period_id`, `syear`, `school_id`, `quarter_id`, `title`, `short_name`, `sort_order`, `start_date`, `end_date`, `post_start_date`, `post_end_date`, `does_grades`, `does_exam`, `does_comments`, `rollover_id`) VALUES
(7, 2009, 1, 3, 'Midterm 1', 'M1', 1, '2009-08-22', '2009-09-22', '2009-09-21', '2009-09-22', 'Y', NULL, NULL, NULL),
(8, 2009, 1, 4, 'Midterm 2', 'M2', 2, '2009-10-12', '2009-11-12', '2009-11-11', '2009-11-12', 'Y', NULL, NULL, NULL),
(9, 2009, 1, 5, 'Midterm 3', 'M3', 3, '2010-01-08', '2010-02-08', '2010-02-07', '2010-02-08', 'Y', NULL, NULL, NULL),
(10, 2009, 1, 6, 'Midterm 4', 'M4', 4, '2010-03-11', '2010-04-11', '2010-04-10', '2010-04-11', 'Y', NULL, NULL, NULL);

--
-- Dumping data for table `SCHOOL_QUARTERS`
--

INSERT INTO `SCHOOL_QUARTERS` (`marking_period_id`, `syear`, `school_id`, `semester_id`, `title`, `short_name`, `sort_order`, `start_date`, `end_date`, `post_start_date`, `post_end_date`, `does_grades`, `does_exam`, `does_comments`, `rollover_id`) VALUES
(3, 2009, 1, 1, 'Quarter 1', 'Q1', 1, '2009-07-01', '2009-10-11', '2009-10-10', '2009-10-11', 'Y', NULL, 'Y', NULL),
(4, 2009, 1, 1, 'Quarter 2', 'Q2', 2, '2009-10-12', '2010-01-07', '2010-01-06', '2010-01-07', 'Y', NULL, 'Y', NULL),
(5, 2009, 1, 2, 'Quarter 3', 'Q3', 3, '2010-01-08', '2010-03-10', '2010-03-09', '2010-03-10', 'Y', NULL, 'Y', NULL),
(6, 2009, 1, 2, 'Quarter 4', 'Q4', 4, '2010-03-11', '2010-06-30', '2010-06-24', '2010-06-30', 'Y', NULL, 'Y', NULL);

--
-- Dumping data for table `SCHOOL_SEMESTERS`
--

INSERT INTO `SCHOOL_SEMESTERS` (`marking_period_id`, `syear`, `school_id`, `year_id`, `title`, `short_name`, `sort_order`, `start_date`, `end_date`, `post_start_date`, `post_end_date`, `does_grades`, `does_exam`, `does_comments`, `rollover_id`) VALUES
(1, 2009, 1, 11, 'Semester 1', 'S1', 1, '2009-07-01', '2010-01-07', '2010-01-06', '2010-01-07', NULL, NULL, NULL, NULL),
(2, 2009, 1, 11, 'Semester 2', 'S2', 2, '2010-01-08', '2010-06-30', '2010-06-24', '2010-06-30', NULL, NULL, NULL, NULL);

--
-- Dumping data for table `SCHOOL_YEARS`
--

INSERT INTO `SCHOOL_YEARS` (`marking_period_id`, `syear`, `school_id`, `title`, `short_name`, `sort_order`, `start_date`, `end_date`, `post_start_date`, `post_end_date`, `does_grades`, `does_exam`, `does_comments`, `rollover_id`) VALUES
(11, 2009, 1, 'Full Year', 'FY', 1, '2009-07-01', '2010-06-30', '0000-00-00', '0000-00-00', NULL, NULL, NULL, NULL);

--
-- Dumping data for table `STAFF`
--

INSERT INTO `STAFF` (`syear`, `staff_id`, `current_school_id`, `title`, `first_name`, `last_name`, `middle_name`, `username`, `password`, `phone`, `email`, `profile`, `homeroom`, `schools`, `last_login`, `failed_login`, `profile_id`, `rollover_id`) VALUES
(2009, 1, 1, NULL, 'Admin', 'Administrator', 'A', 'admin', md5('admin'), NULL, NULL, 'admin', NULL, NULL, '2009-07-01 23:09:22', NULL, 1, NULL),
(2009, 2, NULL, NULL, 'Teach', 'Teacher', 'T', 'teacher', md5('teacher'), NULL, NULL, 'teacher', NULL, ',1,', '2009-07-01 23:20:15', NULL, 2, NULL),
(2009, 3, NULL, NULL, 'Parent', 'Parent', 'P', 'parent', md5('parent'), NULL, NULL, 'parent', NULL, NULL, '2009-07-01 18:47:07', NULL, 3, NULL);

--
-- Dumping data for table `STAFF_EXCEPTIONS`
--

INSERT INTO `STAFF_EXCEPTIONS` (`user_id`, `modname`, `can_use`, `can_edit`) VALUES
(1, 'School_Setup/PortalNotes.php', 'Y', 'Y'),
(1, 'School_Setup/Schools.php', 'Y', 'Y'),
(1, 'School_Setup/Schools.php?new_school=true', 'Y', 'Y'),
(1, 'School_Setup/CopySchool.php', 'Y', 'Y'),
(1, 'School_Setup/MarkingPeriods.php', 'Y', 'Y'),
(1, 'School_Setup/Calendar.php', 'Y', 'Y'),
(1, 'School_Setup/Periods.php', 'Y', 'Y'),
(1, 'School_Setup/GradeLevels.php', 'Y', 'Y'),
(1, 'School_Setup/Rollover.php', 'Y', 'Y'),
(1, 'Students/Student.php', 'Y', 'Y'),
(1, 'Students/Student.php&include=General_Info&student_id=new', 'Y', 'Y'),
(1, 'Students/AssignOtherInfo.php', 'Y', 'Y'),
(1, 'Students/AddUsers.php', 'Y', 'Y'),
(1, 'Students/AdvancedReport.php', 'Y', 'Y'),
(1, 'Students/AddDrop.php', 'Y', 'Y'),
(1, 'Students/Letters.php', 'Y', 'Y'),
(1, 'Students/MailingLabels.php', 'Y', 'Y'),
(1, 'Students/StudentLabels.php', 'Y', 'Y'),
(1, 'Students/PrintStudentInfo.php', 'Y', 'Y'),
(1, 'Students/StudentFields.php', 'Y', 'Y'),
(1, 'Students/AddressFields.php', 'Y', 'Y'),
(1, 'Students/PeopleFields.php', 'Y', 'Y'),
(1, 'Students/EnrollmentCodes.php', 'Y', 'Y'),
(1, 'Students/Student.php&category_id=1', 'Y', 'Y'),
(1, 'Students/Student.php&category_id=3', 'Y', 'Y'),
(1, 'Students/Student.php&category_id=2', 'Y', 'Y'),
(1, 'Users/User.php', 'Y', 'Y'),
(1, 'Users/User.php&category_id=1', 'Y', 'Y'),
(1, 'Users/User.php&staff_id=new', 'Y', 'Y'),
(1, 'Users/AddStudents.php', 'Y', 'Y'),
(1, 'Users/Preferences.php', 'Y', 'Y'),
(1, 'Users/Profiles.php', 'Y', 'Y'),
(1, 'Users/Exceptions.php', 'Y', 'Y'),
(1, 'Users/UserFields.php', 'Y', 'Y'),
(1, 'Users/TeacherPrograms.php?include=Grades/InputFinalGrades.php', 'Y', 'Y'),
(1, 'Users/TeacherPrograms.php?include=Grades/Grades.php', 'Y', 'Y'),
(1, 'Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php', 'Y', 'Y'),
(1, 'Users/TeacherPrograms.php?include=Eligibility/EnterEligibility.php', 'Y', 'Y'),
(1, 'Scheduling/Schedule.php', 'Y', 'Y'),
(1, 'Scheduling/Requests.php', 'Y', 'Y'),
(1, 'Scheduling/MassSchedule.php', 'Y', 'Y'),
(1, 'Scheduling/MassRequests.php', 'Y', 'Y'),
(1, 'Scheduling/MassDrops.php', 'Y', 'Y'),
(1, 'Scheduling/ScheduleReport.php', 'Y', 'Y'),
(1, 'Scheduling/RequestsReport.php', 'Y', 'Y'),
(1, 'Scheduling/UnfilledRequests.php', 'Y', 'Y'),
(1, 'Scheduling/IncompleteSchedules.php', 'Y', 'Y'),
(1, 'Scheduling/AddDrop.php', 'Y', 'Y'),
(1, 'Scheduling/PrintSchedules.php', 'Y', 'Y'),
(1, 'Scheduling/PrintRequests.php', 'Y', 'Y'),
(1, 'Scheduling/PrintClassLists.php', 'Y', 'Y'),
(1, 'Scheduling/PrintClassPictures.php', 'Y', 'Y'),
(1, 'Scheduling/Courses.php', 'Y', 'Y'),
(1, 'Scheduling/Scheduler.php', 'Y', 'Y'),
(1, 'Grades/ReportCards.php', 'Y', 'Y'),
(1, 'Grades/CalcGPA.php', 'Y', 'Y'),
(1, 'Grades/Transcripts.php', 'Y', 'Y'),
(1, 'Grades/TeacherCompletion.php', 'Y', 'Y'),
(1, 'Grades/GradeBreakdown.php', 'Y', 'Y'),
(1, 'Grades/FinalGrades.php', 'Y', 'Y'),
(1, 'Grades/GPARankList.php', 'Y', 'Y'),
(1, 'Grades/FixGPA.php', 'Y', 'Y'),
(1, 'Attendance/Administration.php', 'Y', 'Y'),
(1, 'Attendance/AddAbsences.php', 'Y', 'Y'),
(1, 'Attendance/Percent.php', 'Y', 'Y'),
(1, 'Attendance/Percent.php?list_by_day=true', 'Y', 'Y'),
(1, 'Attendance/DailySummary.php', 'Y', 'Y'),
(1, 'Attendance/StudentSummary.php', 'Y', 'Y'),
(1, 'Attendance/TeacherCompletion.php', 'Y', 'Y'),
(1, 'Attendance/DuplicateAttendance.php', 'Y', 'Y'),
(1, 'Attendance/AttendanceCodes.php', 'Y', 'Y'),
(1, 'Attendance/FixDailyAttendance.php', 'Y', 'Y'),
(1, 'Eligibility/Student.php', 'Y', 'Y'),
(1, 'Eligibility/AddActivity.php', 'Y', 'Y'),
(1, 'Eligibility/StudentList.php', 'Y', 'Y'),
(1, 'Eligibility/TeacherCompletion.php', 'Y', 'Y'),
(1, 'Eligibility/Activities.php', 'Y', 'Y'),
(1, 'Eligibility/EntryTimes.php', 'Y', 'Y'),
(1, 'Grades/ReportCardComments.php', 'Y', 'Y'),
(1, 'Grades/ReportCardGrades.php', 'Y', 'Y'),
(1, 'Grades/EditReportCardGrades.php', 'Y', 'Y'),
(1, 'Grades/EditHistoryMarkingPeriods.php', 'Y', 'Y'),
(1, 'Grades/EditReportCardGrades.php', 'Y', 'Y'),
(1, 'Grades/EditHistoryMarkingPeriods.php', 'Y', 'Y'),
(1, 'Tools/Update.php', 'Y', 'Y'),
(1, 'Tools/InstallModule.php', 'Y', 'Y'),
(1, 'Tools/Backup.php', 'Y', 'Y'),
(1, 'Tools/Restore.php', 'Y', 'Y'),
(1, 'Food_Service/TeacherCompletion.php', 'Y', 'Y'),
(1, 'Food_Service/Accounts.php', 'Y', 'Y'),
(1, 'Food_Service/Statements.php', 'Y', 'Y'),
(1, 'Food_Service/Transactions.php', 'Y', 'Y'),
(1, 'Food_Service/ServeMenus.php', 'Y', 'Y'),
(1, 'Food_Service/ActivityReport.php', 'Y', 'Y'),
(1, 'Food_Service/TransactionsReport.php', 'Y', 'Y'),
(1, 'Food_Service/MenuReports.php', 'Y', 'Y'),
(1, 'Food_Service/Reminders.php', 'Y', 'Y'),
(1, 'Food_Service/DailyMenus.php', 'Y', 'Y'),
(1, 'Food_Service/MenuItems.php', 'Y', 'Y'),
(1, 'Food_Service/Menus.php', 'Y', 'Y'),
(1, 'Food_Service/Kiosk.php', 'Y', 'Y');

--
-- Dumping data for table `STAFF_FIELD_CATEGORIES`
--

INSERT INTO `STAFF_FIELD_CATEGORIES` (`id`, `title`, `sort_order`, `include`, `admin`, `teacher`, `parent`, `none`) VALUES
(1, 'General Info', 1, NULL, 'Y', 'Y', 'Y', 'Y'),
(2, 'Schedule', 2, NULL, NULL, 'Y', NULL, NULL),
(3, 'Food Service', 3, 'Food_Service/User', 'Y', 'Y', NULL, NULL);

--
-- Dumping data for table `STUDENTS`
--

INSERT INTO `STUDENTS` (`student_id`, `last_name`, `first_name`, `middle_name`, `name_suffix`, `username`, `password`, `last_login`, `failed_login`, `custom_200000000`, `custom_200000001`, `custom_200000002`, `custom_200000003`, `custom_200000004`, `custom_200000005`, `custom_200000006`, `custom_200000007`, `custom_200000008`, `custom_200000009`, `custom_200000010`, `custom_200000011`, `custom_200000012`, `alt_id`) VALUES
(1, 'Student', 'Student', 'S', NULL, 'student', md5('student'), '2009-07-01 18:52:33', NULL, 'Male', 'White, Non-Hispanic', 'Bug', NULL, '1994-12-04', 'English', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Dumping data for table `STUDENTS_JOIN_USERS`
--

INSERT INTO `STUDENTS_JOIN_USERS` (`student_id`, `staff_id`) VALUES
(1, 3);

--
-- Dumping data for table `STUDENT_ELIGIBILITY_ACTIVITIES`
--

INSERT INTO `STUDENT_ELIGIBILITY_ACTIVITIES` (`syear`, `student_id`, `activity_id`) VALUES
(2009, 1, 5),
(2009, 1, 4);

--
-- Dumping data for table `STUDENT_ENROLLMENT`
--

INSERT INTO `STUDENT_ENROLLMENT` (`id`, `syear`, `school_id`, `student_id`, `grade_id`, `start_date`, `end_date`, `enrollment_code`, `drop_code`, `next_school`, `calendar_id`, `last_school`) VALUES
(1, 2009, 1, 1, 1, '2009-04-09', NULL, 3, NULL, NULL, 1, NULL);


--
-- Dumping data for table `STUDENT_ENROLLMENT_CODES`
--

INSERT INTO `STUDENT_ENROLLMENT_CODES` (`id`, `syear`, `title`, `short_name`, `type`) VALUES
(1, 2009, 'Moved from District', 'MOVE', 'Drop'),
(2, 2009, 'Expelled', 'EXP', 'Drop'),
(3, 2009, 'Beginning of Year', 'EBY', 'Add'),
(4, 2009, 'From Other District', 'OTHER', 'Add'),
(5, 2009, 'Transferred in District', 'TRAN', 'Drop'),
(6, 2009, 'Transferred in District', 'EMY', 'Add');

--
-- Dumping data for table `STUDENT_FIELD_CATEGORIES`
--

INSERT INTO `STUDENT_FIELD_CATEGORIES` (`id`, `title`, `sort_order`, `include`) VALUES
(1, 'General Info', 1, NULL),
(2, 'Medical', 3, NULL),
(3, 'Addresses & Contacts', 2, NULL),
(4, 'Comments', 4, NULL),
(5, 'Food Service', 5, 'Food_Service/Student');

--
-- Dumping data for table `USER_PROFILES`
--

INSERT INTO `USER_PROFILES` (`id`, `profile`, `title`) VALUES
(0, 'student', 'Student'),
(1, 'admin', 'Administrator'),
(2, 'teacher', 'Teacher'),
(3, 'parent', 'Parent');
";

	$sqllines = split("\n",$text);
	$cmd = '';
	foreach($sqllines as $l)
	{
		if(preg_match('/^\s*--/',$l) == 0)
		{
			$cmd .= ' ' . $l . "\n";
			if(preg_match('/.+;/',$l) != 0)
			{
				$result = mysql_query($cmd);
				$cmd = '';
			}
		}
	}

?>
