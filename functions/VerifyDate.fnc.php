<?php


function VerifyDate($date)
{

/*
	if(strlen($date)==9) // ORACLE
	{
		$day = substr($date,0,2)*1;
		$month = MonthNWSwitch(substr($date,3,3),'tonum')*1;
		$year = substr($date,7,2);
		$year = (($year<50)?20:19) . $year;
	}
	elseif(strlen($date)==10) // POSTGRES
	{
		$day = substr($date,8,2)*1;
		$month = substr($date,5,2)*1;
		$year = substr($date,0,4);
	}
	else
		return false;
*/
	$vdate = explode("-", $date);
	if(count($vdate))
	{
		$day = $vdate[0];
		$month = MonthNWSwitch($vdate[1],'tonum');
		$year = $vdate[2];
	}
	else
	return false;
	
	return checkdate($month,$day,$year);
}

?>