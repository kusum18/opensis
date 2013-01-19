<?php


function GetSyear($date)
{	global $_CENTRE;
	
	$RET = DBGet(DBQuery("SELECT SYEAR FROM ATTENDANCE_CALENDAR WHERE SCHOOL_DATE = '$date' AND DEFAULT_CALENDAR='Y'"));

	return $RET[1]['SYEAR'];
}
?>