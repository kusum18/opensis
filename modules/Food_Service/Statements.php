<?php

if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
	while(!VerifyDate($start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['year_start']))
		$_REQUEST['day_start']--;
else
{
	$_REQUEST['day_start'] = '01';
	$_REQUEST['month_start'] = strtoupper(date('M'));
	$_REQUEST['year_start'] = date('y');
	$start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['year_start'];
}

if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
	while(!VerifyDate($end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end']))
		$_REQUEST['day_end']--;
else
{
	$_REQUEST['day_end'] = date('d');
	$_REQUEST['month_end'] = strtoupper(date('M'));
	$_REQUEST['year_end'] = date('y');
	$end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end'];
}

if($_REQUEST['type'])
	$_SESSION['FSA_type'] = $_REQUEST['type'];
else
	$_SESSION['_REQUEST_vars']['type'] = $_REQUEST['type'] = $_SESSION['FSA_type'];

if($_REQUEST['type']=='staff')
{
	$tabcolor_s = '#DFDFDF'; $textcolor_s = '#999999';
	$tabcolor_u = Preferences('HEADER'); $textcolor_u = '#FFFFFF';
}
else
{
	$tabcolor_s = Preferences('HEADER'); $textcolor_s = '#FFFFFF';
	$tabcolor_u = '#DFDFDF'; $textcolor_u = '#999999';
}
//$header = '<TABLE border=0 cellpadding=0 cellspacing=0 height=14><TR>';
//$header .= '<TD width=10></TD><TD>'.DrawTab('Students',"Modules.php?modname=$_REQUEST[modname]&day_start=$_REQUEST[day_start]&month_start=$_REQUEST[month_start]&year_start=$_REQUEST[year_start]&day_end=$_REQUEST[day_end]&month_end=$_REQUEST[month_end]&year_end=$_REQUEST[year_end]&type=student",$tabcolor_s,$textcolor_s,'_circle',array('tabcolor'=>Preferences('HEADER'),'textcolor'=>'#FFFFFF')).'</TD>';
//$header .= '<TD width=10></TD><TD>'.DrawTab('Users',   "Modules.php?modname=$_REQUEST[modname]&day_start=$_REQUEST[day_start]&month_start=$_REQUEST[month_start]&year_start=$_REQUEST[year_start]&day_end=$_REQUEST[day_end]&month_end=$_REQUEST[month_end]&year_end=$_REQUEST[year_end]&type=staff",  $tabcolor_u,$textcolor_u,'_circle',array('tabcolor'=>Preferences('HEADER'),'textcolor'=>'#FFFFFF')).'</TD>';
//$header .= '<TD width=10></TD></TR></TABLE>';
//$header = "";
//DrawHeader(($_REQUEST['type']=='staff'?'User ':'Student ').ProgramTitle(),'<TABLE bgcolor=#ffffff><TR><TD>'.$header.'</TD></TR></TABLE>');

DrawBC("Food Service >> ".ProgramTitle());

if($_REQUEST['modfunc']=='delete')
{
	require_once('modules/Food_Service/includes/DeletePromptX.fnc.php');
	if($_REQUEST['item_id']!='')
	{
		if(DeletePromptX('transaction item'))
		{
			require_once('modules/Food_Service/includes/DeleteTransactionItem.fnc.php');
			DeleteTransactionItem($_REQUEST['transaction_id'],$_REQUEST['item_id'],$_REQUEST['type']);
			unset($_REQUEST['modfunc']);
			unset($_REQUEST['delete_ok']);
			unset($_SESSION['_REQUEST_vars']['modfunc']);
			unset($_SESSION['_REQUEST_vars']['delete_ok']);
		}
	}
	else
	{
		if(DeletePromptX('transaction'))
		{
			require_once('modules/Food_Service/includes/DeleteTransaction.fnc.php');
			DeleteTransaction($_REQUEST['transaction_id'],$_REQUEST['type']);
			unset($_REQUEST['modfunc']);
			unset($_REQUEST['delete_ok']);
			unset($_SESSION['_REQUEST_vars']['modfunc']);
			unset($_SESSION['_REQUEST_vars']['delete_ok']);
		}
	}
}

if(!$_REQUEST['modfunc'])
{
$types = array('DEPOSIT'=>'Deposit','CREDIT'=>'Credit','DEBIT'=>'Debit');
$menus_RET = DBGet(DBQuery('SELECT TITLE FROM FOOD_SERVICE_MENUS WHERE SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'));

$type_select = 'Type<SELECT name=type_select><OPTION value=\'\'>Not Specified</OPTION>';
foreach($types as $short_name=>$type)
	$type_select .= '<OPTION value='.$short_name.($_REQUEST['type_select']==$short_name ? ' SELECTED' : '').'>'.$type.'</OPTION>';
foreach($menus_RET as $menu)
	$type_select .= '<OPTION value='.$menu['TITLE'].($_REQUEST['type_select']==$menu['TITLE'] ? ' SELECTED' : '').'>'.$menu['TITLE'].'</OPTION>';
$type_select .= '</SELECT>';
include('modules/Food_Service/'.($_REQUEST['type']=='staff'?'Users':'Students').'/Statements.php');
}

function red($value)
{
	if($value<0)
		return '<FONT color=red>'.$value.'</FONT>';
	else
		return $value;
}
?>
