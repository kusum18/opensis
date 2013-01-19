<?php

function GetStuList(& $extra)
{	global $contacts_RET,$view_other_RET,$_CENTRE;

	if((!$extra['SELECT_ONLY'] || strpos($extra['SELECT_ONLY'],'GRADE_ID')!==false) && !$extra['functions']['GRADE_ID'])
		$functions = array('GRADE_ID'=>'GetGrade');
	else
		$functions = array();

	if($extra['functions'])
		$functions += $extra['functions'];

	if(!$extra['DATE'])
	{
		$queryMP = UserMP();
		$extra['DATE'] = DBDate();
	}
	else
		$queryMP = GetCurrentMP('QTR',$extra['DATE'],false);

	if($_REQUEST['expanded_view']=='true')
	{
		if(!$extra['columns_after'])
			$extra['columns_after'] = array();
#############################################################################################
//Commented as it crashing for Linux due to  Blank Database tables
		//$view_fields_RET = DBGet(DBQuery("SELECT cf.ID,cf.TYPE,cf.TITLE FROM PROGRAM_USER_CONFIG puc,CUSTOM_FIELDS cf WHERE puc.TITLE=cf.ID AND puc.PROGRAM='StudentFieldsView' AND puc.USER_ID='".User('STAFF_ID')."' AND puc.VALUE='Y'"));
#############################################################################################
		$view_address_RET = DBGet(DBQuery("SELECT VALUE FROM PROGRAM_USER_CONFIG WHERE PROGRAM='StudentFieldsView' AND TITLE='ADDRESS' AND USER_ID='".User('STAFF_ID')."'"));
		$view_address_RET = $view_address_RET[1]['VALUE'];
		$view_other_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE PROGRAM='StudentFieldsView' AND TITLE IN ('CONTACT_INFO','HOME_PHONE','GUARDIANS','ALL_CONTACTS') AND USER_ID='".User('STAFF_ID')."'"),array(),array('TITLE'));

		if(!count($view_fields_RET) && !isset($view_address_RET) && !isset($view_other_RET['CONTACT_INFO']))
		{
			$extra['columns_after'] = array('CONTACT_INFO'=>'<IMG SRC=assets/down_phone_button.gif border=0>','CUSTOM_200000000'=>'Gender','CUSTOM_200000001'=>'Ethnicity','ADDRESS'=>'Mailing Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode') + $extra['columns_after'];
			$select = ',s.STUDENT_ID AS CONTACT_INFO,s.CUSTOM_200000000,s.CUSTOM_200000001,COALESCE(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,COALESCE(a.MAIL_CITY,a.CITY) AS CITY,COALESCE(a.MAIL_STATE,a.STATE) AS STATE,COALESCE(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE ';
			$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID AND sam.MAILING='Y') LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
			$functions['CONTACT_INFO'] = 'makeContactInfo';
			// if gender is converted to codeds type
			//$functions['CUSTOM_200000000'] = 'DeCodeds';
			$extra['singular'] = 'Student Address';
			$extra['plural'] = 'Student Addresses';

			$extra2['NoSearchTerms'] = true;
			$extra2['SELECT_ONLY'] = 'ssm.STUDENT_ID,p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,pjc.TITLE,pjc.VALUE,a.PHONE,sjp.ADDRESS_ID ';
			$extra2['FROM'] .= ',ADDRESS a,STUDENTS_JOIN_ADDRESS sja LEFT OUTER JOIN STUDENTS_JOIN_PEOPLE sjp ON (sja.STUDENT_ID=sjp.STUDENT_ID AND sja.ADDRESS_ID=sjp.ADDRESS_ID AND (sjp.CUSTODY=\'Y\' OR sjp.EMERGENCY=\'Y\')) LEFT OUTER JOIN PEOPLE p ON (p.PERSON_ID=sjp.PERSON_ID) LEFT OUTER JOIN PEOPLE_JOIN_CONTACTS pjc ON (pjc.PERSON_ID=p.PERSON_ID) ';
			$extra2['WHERE'] .= ' AND a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=ssm.STUDENT_ID ';
			$extra2['ORDER_BY'] .= 'COALESCE(sjp.CUSTODY,\'N\') DESC';
			$extra2['group'] = array('STUDENT_ID','PERSON_ID');

			// EXPANDED VIEW AND ADDR BREAKS THIS QUERY ... SO, TURN 'EM OFF
			if(!$_REQUEST['_CENTRE_PDF'])
			{
				$expanded_view = $_REQUEST['expanded_view'];
				$_REQUEST['expanded_view'] = false;
				$addr = $_REQUEST['addr'];
				unset($_REQUEST['addr']);
				$contacts_RET = GetStuList($extra2);
				$_REQUEST['expanded_view'] = $expanded_view;
				$_REQUEST['addr'] = $addr;
			}
			else
				unset($extra2['columns_after']['CONTACT_INFO']);
		}
		else
		{
			if($view_other_RET['CONTACT_INFO'][1]['VALUE']=='Y' && !$_REQUEST['_CENTRE_PDF'])
			{
				$select .= ',NULL AS CONTACT_INFO ';
				$extra['columns_after']['CONTACT_INFO'] = '<IMG SRC=assets/down_phone_button.gif border=0>';
				$functions['CONTACT_INFO'] = 'makeContactInfo';

				$extra2 = $extra;
				$extra2['NoSearchTerms'] = true;
				$extra2['SELECT'] = '';
				$extra2['SELECT_ONLY'] = 'ssm.STUDENT_ID,p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,pjc.TITLE,pjc.VALUE,a.PHONE,sjp.ADDRESS_ID,COALESCE(sjp.CUSTODY,\'N\') ';
				$extra2['FROM'] .= ',ADDRESS a,STUDENTS_JOIN_ADDRESS sja LEFT OUTER JOIN STUDENTS_JOIN_PEOPLE sjp ON (sja.STUDENT_ID=sjp.STUDENT_ID AND sja.ADDRESS_ID=sjp.ADDRESS_ID AND (sjp.CUSTODY=\'Y\' OR sjp.EMERGENCY=\'Y\')) LEFT OUTER JOIN PEOPLE p ON (p.PERSON_ID=sjp.PERSON_ID) LEFT OUTER JOIN PEOPLE_JOIN_CONTACTS pjc ON (pjc.PERSON_ID=p.PERSON_ID) ';
				$extra2['WHERE'] .= ' AND a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=ssm.STUDENT_ID ';
				$extra2['ORDER_BY'] .= 'COALESCE(sjp.CUSTODY,\'N\') DESC';
				$extra2['group'] = array('STUDENT_ID','PERSON_ID');
				$extra2['functions'] = array();
				$extra2['link'] = array();

				// EXPANDED VIEW AND ADDR BREAKS THIS QUERY ... SO, TURN 'EM OFF
				$expanded_view = $_REQUEST['expanded_view'];
				$_REQUEST['expanded_view'] = false;
				$addr = $_REQUEST['addr'];
				unset($_REQUEST['addr']);
				$contacts_RET = GetStuList($extra2);
				$_REQUEST['expanded_view'] = $expanded_view;
				$_REQUEST['addr'] = $addr;
			}
			foreach($view_fields_RET as $field)
			{
				$extra['columns_after']['CUSTOM_'.$field['ID']] = $field['TITLE'];
				if($field['TYPE']=='date')
					$functions['CUSTOM_'.$field['ID']] = 'ProperDate';
				elseif($field['TYPE']=='numeric')
					$functions['CUSTOM_'.$field['ID']] = 'removeDot00';
				elseif($field['TYPE']=='codeds')
					$functions['CUSTOM_'.$field['ID']] = 'DeCodeds';
				$select .= ',s.CUSTOM_'.$field['ID'];
			}
			if($view_address_RET)
			{
				$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID AND sam.".$view_address_RET."='Y') LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
				$extra['columns_after'] += array('ADDRESS'=>ucwords(strtolower(str_replace('_',' ',$view_address_RET))).' Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode');
				if($view_address_RET!='MAILING')
					$select .= ",a.ADDRESS_ID,a.ADDRESS,a.CITY,a.STATE,a.ZIPCODE,a.PHONE,ssm.STUDENT_ID AS PARENTS";
				else
					$select .= ",a.ADDRESS_ID,COALESCE(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,COALESCE(a.MAIL_CITY,a.CITY) AS CITY,COALESCE(a.MAIL_STATE,a.STATE) AS STATE,COALESCE(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE,a.PHONE,ssm.STUDENT_ID AS PARENTS ";
				$extra['singular'] = 'Student Address';
				$extra['plural'] = 'Student Addresses';

				if($view_other_RET['HOME_PHONE'][1]['VALUE']=='Y')
				{
					$functions['PHONE'] = 'makePhone';
					$extra['columns_after']['PHONE'] = 'Home Phone';
				}
				if($view_other_RET['GUARDIANS'][1]['VALUE']=='Y' || $view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
				{
					$functions['PARENTS'] = 'makeParents';
					if($view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
						$extra['columns_after']['PARENTS'] = 'Contacts';
					else
						$extra['columns_after']['PARENTS'] = 'Guardians';
				}
			}
			elseif($_REQUEST['addr'] || $extra['addr'])
			{
				$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ".$extra['STUDENTS_JOIN_ADDRESS'].") LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
				$distinct = 'DISTINCT ';
			}
		}
		$extra['SELECT'] .= $select;
	}
	elseif($_REQUEST['addr'] || $extra['addr'])
	{
		$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ".$extra['STUDENTS_JOIN_ADDRESS'].") LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
		$distinct = 'DISTINCT ';
	}

	switch(User('PROFILE'))
	{
		case 'admin':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.CUSTOM_200000002,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,ssm.SCHOOL_ID AS LIST_SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
				if($_REQUEST['include_inactive']=='Y')
					$sql .= ','.db_case(array("(ssm.SYEAR='".UserSyear()."' AND ('".date('Y-m-d',strtotime($extra['DATE']))."'>ssm.START_DATE AND ('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE ';
			}

			$sql .= " FROM STUDENTS s,STUDENT_ENROLLMENT ssm ".$extra['FROM']." WHERE ssm.STUDENT_ID=s.STUDENT_ID ";
			if($_REQUEST['include_inactive']=='Y')
				$sql .= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR<='".UserSyear()."' ORDER BY START_DATE DESC LIMIT 1)";
			else
				$sql .= " AND ssm.SYEAR='".UserSyear()."' AND ('".date('Y-m-d',strtotime($extra['DATE']))."'>=ssm.START_DATE AND ('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) ";

			if(UserSchool() && $_REQUEST['_search_all_schools']!='Y')
				$sql .= " AND ssm.SCHOOL_ID='".UserSchool()."'";
			else
			{
				if(User('SCHOOLS'))
					$sql .= " AND ssm.SCHOOL_ID IN (".substr(str_replace(',',"','",User('SCHOOLS')),2,-2).") ";
				$extra['columns_after']['LIST_SCHOOL_ID'] = 'School';
				$functions['LIST_SCHOOL_ID'] = 'GetSchool';
			}

			if(!$extra['SELECT_ONLY'] && $_REQUEST['include_inactive']=='Y')
				$extra['columns_after']['ACTIVE'] = 'Status';
		break;

		case 'teacher':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.CUSTOM_200000002,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
				if($_REQUEST['include_inactive']=='Y')
				{
					$sql .= ','.db_case(array("('".$extra['DATE']."'>=ssm.START_DATE AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE';
					$sql .= ','.db_case(array("('".$extra['DATE']."'>=ss.START_DATE AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE_SCHEDULE';
				}
			}

			$sql .= " FROM STUDENTS s,COURSE_PERIODS cp,SCHEDULE ss,STUDENT_ENROLLMENT ssm ".$extra['FROM']." WHERE ssm.STUDENT_ID=s.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID
					AND ssm.SCHOOL_ID='".UserSchool()."' AND ssm.SYEAR='".UserSyear()."' AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR
					AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
					AND cp.TEACHER_ID='".User('STAFF_ID')."' AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."'
					AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID";

			if($_REQUEST['include_inactive']=='Y')
			{
				$sql .= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR ORDER BY START_DATE DESC LIMIT 1)";
				$sql .= " AND ss.START_DATE=(SELECT START_DATE FROM SCHEDULE WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR AND MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).") AND COURSE_ID=cp.COURSE_ID AND COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID ORDER BY START_DATE DESC LIMIT 1)";
			}
			else
			{
				$sql .= " AND ('".$extra['DATE']."'>=ssm.START_DATE AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))";
				$sql .= " AND ('".$extra['DATE']."'>=ss.START_DATE AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))";
			}

			if(!$extra['SELECT_ONLY'] && $_REQUEST['include_inactive']=='Y')
			{
				$extra['columns_after']['ACTIVE'] = 'School Status';
				$extra['columns_after']['ACTIVE_SCHEDULE'] = 'Course Status';
			}
		break;

		case 'parent':
		case 'student':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.CUSTOM_200000002,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
			}
			$sql .= " FROM STUDENTS s,STUDENT_ENROLLMENT ssm ".$extra['FROM']."
					WHERE ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ssm.SCHOOL_ID='".UserSchool()."' AND ('".DBDate()."' BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND '".DBDate()."'>ssm.START_DATE)) AND ssm.STUDENT_ID".($extra['ASSOCIATED']?" IN (SELECT STUDENT_ID FROM STUDENTS_JOIN_USERS WHERE STAFF_ID='".$extra['ASSOCIATED']."')":"='".UserStudentID()."'");
		break;
		default:
			exit('Error');
	}

	$sql = appendSQL($sql,$extra);

	$sql .= $extra['WHERE'].' ';
	$sql .= CustomFields('where');

	if($extra['GROUP'])
		$sql .= ' GROUP BY '.$extra['GROUP'];

	if(!$extra['ORDER_BY'] && !$extra['SELECT_ONLY'])
	{
		if(Preferences('SORT')=='Grade')
			$sql .= " ORDER BY (SELECT SORT_ORDER FROM SCHOOL_GRADELEVELS WHERE ID=ssm.GRADE_ID),FULL_NAME";
		else
			$sql .= " ORDER BY FULL_NAME";
		$sql .= $extra['ORDER'];
	}
	elseif($extra['ORDER_BY'])
		$sql .= ' ORDER BY '.$extra['ORDER_BY'];

	if($extra['DEBUG']===true)
		echo '<!--'.$sql.'-->';

	return DBGet(DBQuery($sql),$functions,$extra['group']);
}

function makeContactInfo($student_id,$column)
{	global $THIS_RET,$contacts_RET;

	if(count($contacts_RET[$THIS_RET['STUDENT_ID']]))
	{
		foreach($contacts_RET[$THIS_RET['STUDENT_ID']] as $person)
		{
			if($person[1]['FIRST_NAME'] || $person[1]['LAST_NAME'])
				$tipmessage .= ''.$person[1]['STUDENT_RELATION'].': '.$person[1]['FIRST_NAME'].' '.$person[1]['LAST_NAME'].' | ';
			$tipmessage .= '';
			if($person[1]['PHONE'])
				$tipmessage .= ' '.$person[1]['PHONE'].'';
			foreach($person as $info)
			{
				if($info['TITLE'] || $info['VALUE'])
					$tipmessage .= ''.$info['TITLE'].''.$info['VALUE'].'';
			}
			$tipmessage .= '';
		}
	}
	else
		$tipmessage = 'This student has no contact information.';
	return button('phone','','# alt="'.$tipmessage.'" title="'.$tipmessage.'"');
}

function removeDot00($value,$column)
{
	return str_replace('.00','',$value);
}

function makePhone($phone,$column='')
{	global $THIS_RET;

	if(strlen($phone)==10)
		$return .= '('.substr($phone,0,3).')'.substr($phone,3,7).'-'.substr($phone,7);
	if(strlen($phone)=='7')
		$return .= substr($phone,0,3).'-'.substr($phone,3);
	else
		$return .= $phone;

	return $return;
}

function makeParents($student_id,$column='')
{	global $THIS_RET,$view_other_RET,$_CENTRE;

	if($THIS_RET['PARENTS']==$student_id)
	{
		if(!$THIS_RET['ADDRESS_ID'])
			$THIS_RET['ADDRESS_ID'] = 0;

		$THIS_RET['PARENTS'] = '';

		if($_CENTRE['makeParents'])
			$constraint = 'AND (LOWER(sjp.STUDENT_RELATION) LIKE \''.strtolower($_CENTRE['makeParents']).'%\')';
		elseif($view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
			$constraint = "AND (sjp.CUSTODY='Y' OR sjp.EMERGENCY='Y')";
		else
			$constraint = "AND sjp.CUSTODY='Y'";

		$people_RET = DBGet(DBQuery("SELECT p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.ADDRESS_ID,sjp.CUSTODY,sjp.EMERGENCY FROM STUDENTS_JOIN_PEOPLE sjp,PEOPLE p WHERE sjp.PERSON_ID=p.PERSON_ID AND sjp.STUDENT_ID='$student_id' ".$constraint." ORDER BY p.LAST_NAME,p.FIRST_NAME"));
		if(count($people_RET))
		{
			foreach($people_RET as $person)
			{
				if($person['ADDRESS_ID']==$THIS_RET['ADDRESS_ID'])
				{
					if($person['CUSTODY']=='Y')
						$color = '0000FF';
					elseif($person['EMERGENCY']=='Y')
						$color = 'FFFF00';

					if($_REQUEST['_CENTRE_PDF'])
						$THIS_RET['PARENTS'] .= '<TR><TD>'.button('dot',$color,'',6).'</TD><TD>'.$person['FIRST_NAME'].' '.$person['LAST_NAME'].'</TD></TR>, ';
					else
						$THIS_RET['PARENTS'] .= '<TR><TD>'.button('dot',$color,'',6).'</TD><TD><A HREF=# onclick=\'window.open("Modules.php?modname=misc/ViewContact.php?person_id='.$person['PERSON_ID'].'","","scrollbars=yes,resizable=yes,width=400,height=200");\'>'.$person['FIRST_NAME'].' '.$person['LAST_NAME'].'</A></TD></TR>';
				}
			}
			if($_REQUEST['_CENTRE_PDF'])
				$THIS_RET['PARENTS'] = substr($THIS_RET['PARENTS'],0,-2);
		}
	}

	if($THIS_RET['PARENTS'])
		return '<TABLE border=0 cellpadding=0 cellspacing=0 class=LO_field>'.$THIS_RET['PARENTS'].'</TABLE>';
}

function appendSQL($sql,& $extra)
{	global $_CENTRE;

	if($_REQUEST['stuid'])
	{
		$sql .= " AND ssm.STUDENT_ID = '$_REQUEST[stuid]' ";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>Student ID: </b></font>'.$_REQUEST['stuid'].'<BR>';
	}
	if($_REQUEST['last'])
	{
		$sql .= " AND LOWER(s.LAST_NAME) LIKE '".strtolower($_REQUEST['last'])."%' ";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>Last Name starts with: </b></font>'.$_REQUEST['last'].'<BR>';
	}
	if($_REQUEST['first'])
	{
		$sql .= " AND LOWER(s.FIRST_NAME) LIKE '".strtolower($_REQUEST['first'])."%' ";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>First Name starts with: </b></font>'.$_REQUEST['first'].'<BR>';
	}
	if($_REQUEST['grade'])
	{
		$sql .= " AND ssm.GRADE_ID = '$_REQUEST[grade]' ";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>Grade: </b></font>'.GetGrade($_REQUEST['grade']).'<BR>';
	}
	if($_REQUEST['addr'])
	{
		$sql .= " AND (LOWER(a.ADDRESS) LIKE '%".strtolower($_REQUEST['addr'])."%' OR LOWER(a.CITY) LIKE '".strtolower($_REQUEST['addr'])."%' OR LOWER(a.STATE)='".strtolower($_REQUEST['addr'])."' OR ZIPCODE LIKE '".$_REQUEST['addr']."%')";
		if(!$extra['NoSearchTerms'])
			$_CENTRE['SearchTerms'] .= '<font color=gray><b>Address contains: </b></font>'.$_REQUEST['addr'].'<BR>';
	}

	return $sql;
}
?>