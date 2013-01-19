<?php

function User($item)
{	global $_CENTRE,$DefaultSyear;

	if(!$_SESSION['UserSyear'])
		$_SESSION['UserSyear'] = $DefaultSyear;

	if(!$_CENTRE['User'] || $_SESSION['UserSyear']!=$_CENTRE['User'][1]['SYEAR'])
	{
		if($_SESSION['STAFF_ID'])
		{
			$sql = "SELECT STAFF_ID,USERNAME,CONCAT(FIRST_NAME,' ',LAST_NAME) AS NAME,PROFILE,PROFILE_ID,SCHOOLS,CURRENT_SCHOOL_ID,EMAIL,SYEAR FROM STAFF WHERE SYEAR='$_SESSION[UserSyear]' AND USERNAME=(SELECT USERNAME FROM STAFF WHERE SYEAR='$DefaultSyear' AND STAFF_ID='$_SESSION[STAFF_ID]')";
			$_CENTRE['User'] = DBGet(DBQuery($sql));
		}
		elseif($_SESSION['STUDENT_ID'])
		{
			$sql = "SELECT s.USERNAME,CONCAT(s.FIRST_NAME,' ',s.LAST_NAME) AS NAME,'student' AS PROFILE,'0' AS PROFILE_ID,CONCAT(',',se.SCHOOL_ID,',') AS SCHOOLS,se.SYEAR,se.SCHOOL_ID FROM STUDENTS s,STUDENT_ENROLLMENT se WHERE s.STUDENT_ID='$_SESSION[STUDENT_ID]' AND se.SYEAR='$_SESSION[UserSyear]' AND se.STUDENT_ID=s.STUDENT_ID ORDER BY se.END_DATE DESC LIMIT 1";
			$_CENTRE['User'] = DBGet(DBQuery($sql));
			$_SESSION['UserSchool'] = $_CENTRE['User'][1]['SCHOOL_ID'];
		}
		else
			exit('Error');
	}

	return $_CENTRE['User'][1][$item];
}

function Preferences($item,$program='Preferences')
{	global $_CENTRE;

	if($_SESSION['STAFF_ID'] && !$_CENTRE['Preferences'][$program])
	{
		$QI=DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='$_SESSION[STAFF_ID]' AND PROGRAM='$program'");
		$_CENTRE['Preferences'][$program] = DBGet($QI,array(),array('TITLE'));
	}

	$defaults = array('NAME'=>'Common',
				'SORT'=>'Name',
				'SEARCH'=>'Y',
				'DELIMITER'=>'Tab',
				'COLOR'=>'#FFFFCC',
				'HIGHLIGHT'=>'#85E1FF',
				'TITLES'=>'gray',
				'THEME'=>'Brushed-Steel',
				'HIDDEN'=>'Y',
				'MONTH'=>'M',
				'DAY'=>'j',
				'YEAR'=>'Y',
				'DEFAULT_ALL_SCHOOLS'=>'N',
				'ASSIGNMENT_SORTING'=>'ASSIGNMENT_ID',
				'ANOMALOUS_MAX'=>'100'
				);

	if(!isset($_CENTRE['Preferences'][$program][$item][1]['VALUE']))
		$_CENTRE['Preferences'][$program][$item][1]['VALUE'] = $defaults[$item];

	if($_SESSION['STAFF_ID'] && User('PROFILE')=='parent' || $_SESSION['STUDENT_ID'])
		$_CENTRE['Preferences'][$program]['SEARCH'][1]['VALUE'] = 'N';

	return $_CENTRE['Preferences'][$program][$item][1]['VALUE'];
}
?>