<?php

if(!$_REQUEST['month'])
	$_REQUEST['month'] = date("n");
else
	$_REQUEST['month'] = MonthNWSwitch($_REQUEST['month'],'tonum')+0;
if(!$_REQUEST['year'])
	$_REQUEST['year'] = date("Y");

$last = 31;
while(!checkdate($_REQUEST['month'],$last,$_REQUEST['year']))
	$last--;

$time = mktime(0,0,0,$_REQUEST['month'],1,$_REQUEST['year']);
$time_last = mktime(0,0,0,$_REQUEST['month'],$last,$_REQUEST['year']);

// use the dafault calendar
$default_RET = DBGet(DBQuery("SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND DEFAULT_CALENDAR='Y'"));
if(count($default_RET))
	$calendar_id = $default_RET[1]['CALENDAR_ID'];
else
{
	$calendars_RET = DBGet(DBQuery("SELECT CALENDAR_ID FROM ATTENDANCE_CALENDARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	if(count($calendars_RET))
		$calendar_id = $calendars_RET[1]['CALENDAR_ID'];
	else
		ErrorMessage(array('There are no calendars yet setup.'),'fatal');
}

$menus_RET = DBGet(DBQuery('SELECT MENU_ID,TITLE FROM FOOD_SERVICE_MENUS WHERE SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'),array(),array('MENU_ID'));
if(!$_REQUEST['menu_id'])
	if(!$_SESSION['FSA_menu_id'])
		if(count($menus_RET))
			$_REQUEST['menu_id'] = $_SESSION['FSA_menu_id'] = key($menus_RET);
		else
			ErrorMessage(array('There are no menus yet setup.'),'fatal');
	else
		$_REQUEST['menu_id'] = $_SESSION['FSA_menu_id'];
else
		$_SESSION['FSA_menu_id'] = $_REQUEST['menu_id'];

if($_REQUEST['submit']['save'] && $_REQUEST['food_service'] && $_POST['food_service'])
{
	$events_RET = DBGet(DBQuery("SELECT ID,DATE_FORMAT(SCHOOL_DATE,'%d-%b-%y') AS SCHOOL_DATE FROM CALENDAR_EVENTS WHERE SCHOOL_DATE BETWEEN '".date('d-M-y',$time)."' AND '".date('d-M-y',$time_last)."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TITLE='".$menus_RET[$_REQUEST['menu_id']][1]['TITLE']."'"),array(),array('SCHOOL_DATE'));
	//echo '<pre>'; var_dump($events_RET); echo '</pre>';

	foreach($_REQUEST['food_service'] as $school_date=>$description)
	{
		if($events_RET[$school_date])
			if($description['text'] || $description['select'])
				DBQuery("UPDATE CALENDAR_EVENTS SET DESCRIPTION='".$description['text'].$description['select']."' WHERE ID='".$events_RET[$school_date][1]['ID']."'");
			else
				DBQuery("DELETE FROM CALENDAR_EVENTS WHERE ID='".$events_RET[$school_date][1]['ID']."'");
		else
			if($description['text'] || $description['select'])
				DBQuery("INSERT INTO CALENDAR_EVENTS (ID,SYEAR,SCHOOL_ID,SCHOOL_DATE,TITLE,DESCRIPTION) values(".db_seq_nextval('CALENDAR_EVENTS_SEQ').",'".UserSyear()."','".UserSchool()."','".$school_date."','".$menus_RET[$_REQUEST['menu_id']][1]['TITLE']."','".$description['text'].$description['select']."')");
	}
	unset($_REQUEST['food_service']);
	unset($_SESSION['_REQUEST_vars']['food_service']);
}

if($_REQUEST['submit']['print'])
{
	$events_RET = DBGet(DBQuery("SELECT TITLE,DESCRIPTION,DATE_FORMAT(SCHOOL_DATE,'%d-%b-%y') AS SCHOOL_DATE FROM CALENDAR_EVENTS WHERE SCHOOL_DATE BETWEEN '".date('d-M-y',$time)."' AND '".date('d-M-y',$time_last)."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND (TITLE='".$menus_RET[$_REQUEST['menu_id']][1]['TITLE']."' OR TITLE='No School')"),array(),array('SCHOOL_DATE'));

	$skip = date("w",$time);

	echo "<!-- MEDIA TOP 1in -->\n<P><CENTER>\n";
	echo "<TABLE border=2 cellpadding=3 bgcolor=#FFFFFF>\n";
	if($_REQUEST['_CENTRE_PDF'])
		if(($file = @fopen($CentrePath.'/assets/dailymenu'.UserSchool().'.jpg','r')))
		{
			echo "<TR align=center><TD colspan=7><img src='assets/dailymenu".UserSchool().".jpg'></TD></TR>\n";
			fclose($file);
		}
		else
			echo "<TR align=center><TD colspan=7><font color=black size=+3><b>".GetSchool(UserSchool())."</b></font></TD></TR>\n";
	echo "<TR align=center><TD colspan=2>".$menus_RET[$_REQUEST['menu_id']][1]['TITLE']."</TD><TD colspan=3><font color=black size=+2><b>".date('F Y',mktime(0,0,0,$_REQUEST['month'],1,$_REQUEST['year']))."</b></font></TD><TD colspan=2>".$menus_RET[$_REQUEST['menu_id']][1]['TITLE']."</TD></TR>\n";
	echo "<TR bgcolor=#808080 align=center>\n";
	echo "<TD width=100><font color=white><b>Sunday</b></font></TD>\n<TD width=100><font color=white><b>Monday</b></font></TD>\n<TD width=100><font color=white><b>Tuesday</b></font></TD>\n<TD width=100><font color=white><b>Wednesday</b></font></TD>\n<TD width=100><font color=white><b>Thursday</b></font></TD>\n<TD width=100><font color=white><b>Friday</b></font></TD>\n<TD width=100><font color=white><b>Saturday</b></font></TD>\n";
	echo "</TR>\n";

	if($skip)
		echo "<TR height=100><TD bgcolor=#C0C0C0 colspan=".$skip.">&nbsp;</TD>\n";

	for($i = 1; $i <= $last; $i++)
	{
		if($skip%7==0)
			echo "<TR height=100>";
		$day_time = mktime(0,0,0,$_REQUEST['month'],$i,$_REQUEST['year']);
		$date = strtoupper(date('d-M-y',$day_time));

		echo "<TD width=100 valign=top><b>$i</b>";

		if(count($events_RET[$date]))
		{
			foreach($events_RET[$date] as $event)
			{
				if($event['TITLE']!=$menus_RET[$_REQUEST['menu_id']][1]['TITLE'])
					echo "<br><i>".$event['TITLE']."</i>";
				echo "<br>".htmlspecialchars($event['DESCRIPTION'],ENT_QUOTES);
			}
		}
		echo "</TD>\n";

		$skip++;

		if($skip%7==0)
			echo "</TR>\n";
	}
	if($skip%7!=0)
		echo "<TD bgcolor=#C0C0C0 colspan=".(7-$skip%7).">&nbsp;</TD>\n</TR>";

	echo "</TABLE>\n";
	echo "</CENTER></P>\n";
}
else
{
	DrawHeader(ProgramTitle());

	if(AllowEdit())
	{
		$description_RET = DBGet(DBQuery("SELECT DISTINCT DESCRIPTION FROM CALENDAR_EVENTS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TITLE='".$menus_RET[$_REQUEST['menu_id']][1]['TITLE']."' AND DESCRIPTION IS NOT NULL ORDER BY DESCRIPTION"));
		if(count($description_RET))
		{
			$description_select = '<OPTION value="">or select previous meal';
			foreach($description_RET as $description)
				$description_select .= '<OPTION value="'.$description['DESCRIPTION'].'">'.$description['DESCRIPTION'];
			$description_select .= '</SELECT';
		}
	}

	$calendar_RET = DBGet(DBQuery("SELECT DATE_FORMAT(SCHOOL_DATE,'%d-%b-%y') as SCHOOL_DATE FROM ATTENDANCE_CALENDAR WHERE SCHOOL_DATE BETWEEN '".date('d-M-y',$time)."' AND '".date('d-M-y',$time_last)."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND CALENDAR_ID='$calendar_id' AND MINUTES>0 ORDER BY SCHOOL_DATE"),array(),array('SCHOOL_DATE'));

	$events_RET = DBGet(DBQuery("SELECT ID,TITLE,DESCRIPTION,DATE_FORMAT(SCHOOL_DATE,'%d-%b-%y') AS SCHOOL_DATE FROM CALENDAR_EVENTS WHERE SCHOOL_DATE BETWEEN '".date('d-M-y',$time)."' AND '".date('d-M-y',$time_last)."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TITLE='".$menus_RET[$_REQUEST['menu_id']][1]['TITLE']."' ORDER BY SCHOOL_DATE"),array('DESCRIPTION'=>'makeDescriptionInput','SCHOOL_DATE'=>'ProperDate'));

	$events_RET[0] = array(); // make sure indexing from 1
	foreach($calendar_RET as $school_date=>$value)
		$events_RET[] = array('ID'=>'new','SCHOOL_DATE'=>ProperDate($school_date),'DESCRIPTION'=>TextInput('','food_service['.$school_date.'][text]','','size=35').($description_select ? '<SELECT name=food_service['.$school_date.'][select]>'.$description_select : ''));
	unset($events_RET[0]);
	$LO_columns = array('ID'=>'#','SCHOOL_DATE'=>'Date','DESCRIPTION'=>'Description');

	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&menu_id=$_REQUEST[menu_id]&month=$_REQUEST[month]&year=$_REQUEST[year] METHOD=POST>";
	DrawHeader(PrepareDate(strtoupper(date("d-M-y",$time)),'',false,array('M'=>1,'Y'=>1,'submit'=>true)),SubmitButton('Save','submit[save]').'<INPUT type=submit value=\'Generate Menu\' name=submit[print]>');
	echo '<BR>';

	$tabs = array();
	foreach($menus_RET as $id=>$meal)
		$tabs[] = array('title'=>$meal[1]['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&menu_id=$id&month=$_REQUEST[month]&year=$_REQUEST[year]");

	$extra = array('save'=>false,'search'=>false,
		'header'=>WrapTabs($tabs,"Modules.php?modname=$_REQUEST[modname]&menu_id=$_REQUEST[menu_id]&month=$_REQUEST[month]&year=$_REQUEST[year]"));
	$singular = $menus_RET[$_REQUEST['menu_id']][1]['TITLE'].' Day';
	$plural = $singular.'s';

	ListOutput($events_RET,$LO_columns,$singular,$plural,array(),array(),$extra);

	echo '<CENTER>'.SubmitButton('Save','submit[save]').'</CENTER>';
	echo "</FORM>";
}

function makeDescriptionInput($value,$name)
{	global $THIS_RET,$calendar_RET;

	if($calendar_RET[$THIS_RET['SCHOOL_DATE']])
		unset($calendar_RET[$THIS_RET{'SCHOOL_DATE'}]);

	return TextInput($value,'food_service['.$THIS_RET['SCHOOL_DATE'].'][text]','','size=35');
}
?>
