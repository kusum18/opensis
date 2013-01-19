<?php

function GetPeriod($period_id,$title='')
{	global $_CENTRE;

	if(!$_CENTRE['GetPeriod'])
	{
		$sql = "SELECT TITLE, PERIOD_ID FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."'";
		$_CENTRE['GetPeriod'] = DBGet(DBQuery($sql),array(),array('PERIOD_ID'));
	}
	
	return $_CENTRE['GetPeriod'][$period_id][1]['TITLE'];
}
?>
