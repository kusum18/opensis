<?php

function GetStaffList(& $extra)
{	global $profiles_RET;
	$functions = array('PROFILE'=>'makeProfile');
	switch(User('PROFILE'))
	{
		case 'admin':
		$profiles_RET = DBGet(DBQuery("SELECT * FROM USER_PROFILES"),array(),array('ID'));
		$sql = "SELECT CONCAT(
					COALESCE(s.LAST_NAME,' '),', ',COALESCE(s.FIRST_NAME,' '),' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,
					s.PROFILE,s.PROFILE_ID,s.STAFF_ID,s.SCHOOLS ".$extra['SELECT']."
				FROM
					STAFF s ".$extra['FROM']."
				WHERE
					s.SYEAR='".UserSyear()."'";
		if($_REQUEST['_search_all_schools']!='Y')
			$sql .= " AND (s.SCHOOLS LIKE '%,".UserSchool().",%' OR s.SCHOOLS IS NULL OR s.SCHOOLS='') ";
		if($_REQUEST['username'])
			$sql .= "AND UPPER(s.USERNAME) LIKE '".strtoupper($_REQUEST['username'])."%' ";
		if($_REQUEST['last'])
			$sql .= "AND UPPER(s.LAST_NAME) LIKE '".strtoupper($_REQUEST['last'])."%' ";
		if($_REQUEST['first'])
			$sql .= "AND UPPER(s.FIRST_NAME) LIKE '".strtoupper($_REQUEST['first'])."%' ";
		if($_REQUEST['profile'])
			$sql .= "AND s.PROFILE='".$_REQUEST['profile']."' ";

		$sql .= $extra['WHERE'].' ';
		$sql .= "ORDER BY FULL_NAME";

		if ($extra['functions'])
			$functions += $extra['functions'];

		return DBGet(DBQuery($sql),$functions);
		break;
	}
}

function makeProfile($value)
{	global $THIS_RET,$profiles_RET;

	if($THIS_RET['PROFILE_ID'])
		$return = $profiles_RET[$THIS_RET['PROFILE_ID']][1]['TITLE'];
	elseif($value=='admin')
		$return = 'Administrator w/Custom';
	elseif($value=='teacher')
		$return = 'Teacher w/Custom';
	elseif($value=='parent')
		$return = 'Parent w/Custom';
	elseif($value=='none')
		$return = 'No Access';
	else $return = $value;

	return $return;
}
?>
