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
if(!$_CENTRE['Menu'])
{
	foreach($CentreModules as $module=>$include)
		if($include)
			include "modules/$module/Menu.php";

	$profile = User('PROFILE');

	if($profile!='student')
		if(User('PROFILE_ID'))
			$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
		else
			$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
	else
	{
		$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='0' AND CAN_USE='Y'"),array(),array('MODNAME'));
		$profile = 'parent';
	}

	foreach($menu as $modcat=>$profiles)
	{
	 	$menuprof = $menu;
		$programs = $profiles[$profile];
		foreach($programs as $program=>$title)
		{
			if(!is_numeric($program))
			{
				if($can_use_RET[$program] && ($profile!='admin' || !$exceptions[$modcat][$program] || AllowEdit($program)))
					$_CENTRE['Menu'][$modcat][$program] = $title;
			}
			else
				$_CENTRE['Menu'][$modcat][$program] = $title;
		}
	}

	if(User('PROFILE')=='student')
		unset($_CENTRE['Menu']['Users']);
}
?>