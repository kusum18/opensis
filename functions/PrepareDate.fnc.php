<?php

// SEND PrepareDate a name prefix, and a date in oracle format 'd-M-y' as the selected date to have returned a date selection series
// of pull-down menus
// For the default to be Not Specified, send a date of 00-000-00 -- For today's date, send nothing
// The date pull-downs will create three variables, monthtitle, daytitle, yeartitle
// The third parameter (booleen) specifies whether Not Specified should be allowed as an option

function PrepareDate($date='',$title='',$allow_na=true,$options='')
{	global $_CENTRE;

	if($options=='')
		$options = array();
	if(!$options['Y'] && !$options['M'] && !$options['D'] && !$options['C'])
		$options += array('Y'=>true,'M'=>true,'D'=>true,'C'=>true);
		
	if($options['short']==true)
		$extraM = "style='width:60;' ";
	if($options['submit']==true)
	{
		$tmp_REQUEST['M'] = $tmp_REQUEST['D'] = $tmp_REQUEST['Y'] = $_REQUEST;
		unset($tmp_REQUEST['M']['month'.$title]);
		unset($tmp_REQUEST['D']['day'.$title]);
		unset($tmp_REQUEST['Y']['year'.$title]);
		$extraM .= "onchange='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST['M'])."&amp;month$title=\"+this.form.month$title.value;'";
		$extraD .= "onchange='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST['D'])."&amp;day$title=\"+this.form.day$title.value;'";
		$extraY .= "onchange='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST['Y'])."&amp;year$title=\"+this.form.year$title.value;'";
	}
	
	if($options['C'])
		$_CENTRE['PrepareDate']++;

	if(strlen($date)==9) // ORACLE
	{
		$day = substr($date,0,2);
		$month = substr($date,3,3);
		$year = substr($date,7,2);

		$return .= '<!-- '.$year.MonthNWSwitch($month,'tonum').$day.' -->';
	}
	else // mysql
	{
		$day = substr($date,8,2);
		$month = MonthNWSwitch(substr($date,5,2),'tochar');
		$year = substr($date,2,2);

		$return .= '<!-- '.$year.MonthNWSwitch($month,'tonum').$day.' -->';
	}

	// MONTH  ---------------
	if($options['M'])
	{
		$return .= "<div style=white-space:nowrap ><SELECT NAME=month".$title." id=monthSelect".$_CENTRE['PrepareDate']." SIZE=1 $extraM>";
		//  -------------------------------------------------------------------------- //
		
		if($month == 'JAN')
			$month = 1;
		elseif($month == 'FEB')
			$month = 2;
		elseif($month == 'MAR')
			$month = 3;
		elseif($month == 'APR')
			$month = 4;
		elseif($month == 'MAY')
			$month = 5;
		elseif($month == 'JUN')
			$month = 6;
		elseif($month == 'JUL')
			$month = 7;
		elseif($month == 'AUG')
			$month = 8;
		elseif($month == 'SEP')
			$month = 9;
		elseif($month == 'OCT')
			$month = 10;
		elseif($month == 'NOV')
			$month = 11;
		elseif($month == 'DEC')
			$month = 12;
		
		//  -------------------------------------------------------------------------- //
		if($allow_na)
		{
			if($month=='000')
				$return .= "<OPTION value=\"\" SELECTED>N/A";else $return .= "<OPTION value=\"\">N/A";
		}
		if($month=='1'){$return .= "<OPTION VALUE=JAN SELECTED>January";}else{$return .= "<OPTION VALUE=JAN>January";}
		if($month=='2'){$return .= "<OPTION VALUE=FEB SELECTED>February";}else{$return .= "<OPTION VALUE=FEB>February";}
		if($month=='3'){$return .= "<OPTION VALUE=MAR SELECTED>March";}else{$return .= "<OPTION VALUE=MAR>March";}
		if($month=='4'){$return .= "<OPTION VALUE=APR SELECTED>April";}else{$return .= "<OPTION VALUE=APR>April";}
		if($month=='5'){$return .= "<OPTION VALUE=MAY SELECTED>May";}else{$return .= "<OPTION VALUE=MAY>May";}
		if($month=='6'){$return .= "<OPTION VALUE=JUN SELECTED>June";}else{$return .= "<OPTION VALUE=JUN>June";}
		if($month=='7'){$return .= "<OPTION VALUE=JUL SELECTED>July";}else{$return .= "<OPTION VALUE=JUL>July";}
		if($month=='8'){$return .= "<OPTION VALUE=AUG SELECTED>August";}else{$return .= "<OPTION VALUE=AUG>August";}
		if($month=='9'){$return .= "<OPTION VALUE=SEP SELECTED>September";}else{$return .= "<OPTION VALUE=SEP>September";}
		if($month=='10'){$return .= "<OPTION VALUE=OCT SELECTED>October";}else{$return .= "<OPTION VALUE=OCT>October";}
		if($month=='11'){$return .= "<OPTION VALUE=NOV SELECTED>November";}else{$return .= "<OPTION VALUE=NOV>November";}
		if($month=='12'){$return .= "<OPTION VALUE=DEC SELECTED>December";}else{$return .= "<OPTION VALUE=DEC>December";}
		
		$return .= "</SELECT> ";
	}

	// DAY  ---------------
	if($options['D'])
	{
		$return .="<SELECT NAME=day".$title." id=daySelect".$_CENTRE['PrepareDate']." SIZE=1 $extraD>";
		if($allow_na)
		{
			if($day=='00'){$return .= "<OPTION value=\"\" SELECTED>N/A";}else{$return .= "<OPTION value=\"\">N/A";}
		}
		
		for($i=1;$i<=31;$i++)
		{
			if(strlen($i)==1)
				$print='0'.$i;
			else
				$print = $i;
			
			$return .="<OPTION VALUE=".$print;
			if($day==$print)
				$return .=" SELECTED";
			$return .=">$i ";
		}
		$return .="</SELECT> ";
	}
	
	// YEAR	 ---------------
	if($options['Y'])
	{
		if(!$year)
		{
			$begin = date('Y') - 20;
			$end = date('Y') + 20;
		}
		else
		{
			if($year<50)
				$year = '20'.$year;
			else
				$year = '19'.$year;
			$begin = $year - 5;
			$end = $year + 20;
		}
	
		$return .="<SELECT NAME=year".$title." id=yearSelect".$_CENTRE['PrepareDate']." SIZE=1 $extraY>";
		if($allow_na)
		{
			if($year=='00'){$return .= "<OPTION value=\"\" SELECTED>N/A";}else{$return .= "<OPTION value=\"\">N/A";}
		}
			
		for($i=$begin;$i<=$end;$i++)
		{
			$return .="<OPTION VALUE=".substr($i,0);
			if($year==$i){$return .=" SELECTED";}
			$return .=">".$i;
		}
		$return .="</SELECT> ";
	}
	
	if($options['C'])
		$return .= '<img src="assets/calendar.gif" id="trigger'.$_CENTRE['PrepareDate'].'" style="cursor: pointer;" onmouseover=this.style.background=""; onmouseout=this.style.background=""; onClick='."MakeDate('".$_CENTRE['PrepareDate']."',this);".' /></div>';
	
	if($_REQUEST['_CENTRE_PDF'])
		$return = ProperDate($date);
	return $return;
}
?>