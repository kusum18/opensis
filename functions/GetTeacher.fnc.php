<?php

function GetTeacher($teacher_id,$title='',$column='FULL_NAME',$schools=true)
{	global $_CENTRE;
	
	if(!$_CENTRE['GetTeacher'])
	{
		$QI=DBQuery("SELECT STAFF_ID,CONCAT(LAST_NAME,', ',FIRST_NAME) AS FULL_NAME,USERNAME,PROFILE FROM STAFF WHERE SYEAR='".UserSyear()."'".($schools?" AND SCHOOLS LIKE '%,".UserSchool().",%'":''));
		$_CENTRE['GetTeacher'] = DBGet($QI,array(),array('STAFF_ID'));
	}
	
	return $_CENTRE['GetTeacher'][$teacher_id][1][$column];
}
?>
