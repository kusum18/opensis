<?php

function GetSchool($sch)
{	global $_CENTRE;
	
	if(!$_CENTRE['GetSchool'])
	{
		$QI=DBQuery("SELECT ID,TITLE FROM SCHOOLS");
		$_CENTRE['GetSchool'] = DBGet($QI,array(),array('ID'));
	}

	if($_CENTRE['GetSchool'][$sch])
		return $_CENTRE['GetSchool'][$sch][1]['TITLE'];
	else
		return $sch;
}
?>
