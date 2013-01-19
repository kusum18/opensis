<?php

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
//$header .= '<TD width=10></TD><TD>'.DrawTab('Students',"Modules.php?modname=$_REQUEST[modname]&type=student",$tabcolor_s,$textcolor_s,'_circle',array('tabcolor'=>Preferences('HEADER'),'textcolor'=>'#FFFFFF')).'</TD>';
//$header .= '<TD width=10></TD><TD>'.DrawTab('Users',   "Modules.php?modname=$_REQUEST[modname]&type=staff",  $tabcolor_u,$textcolor_u,'_circle',array('tabcolor'=>Preferences('HEADER'),'textcolor'=>'#FFFFFF')).'</TD>';
//$header .= '<TD width=10></TD></TR></TABLE>';
//$header = "";
//DrawHeader(($_REQUEST['type']=='staff'?'User ':'Student ').ProgramTitle(),'<TABLE bgcolor=#ffffff><TR><TD>'.$header.'</TD></TR></TABLE>');

DrawBC("Food Service >> ".ProgramTitle());

if($_REQUEST['modfunc']=='delete')
{
	require_once('modules/Food_Service/includes/DeletePromptX.fnc.php');
	if(DeletePromptX('transaction'))
	{
		require_once('modules/Food_Service/includes/DeleteTransaction.fnc.php');
		DeleteTransaction($_REQUEST['id'],$_REQUEST['type']);
		unset($_REQUEST['modfunc']);
		unset($_REQUEST['delete_ok']);
		unset($_SESSION['_REQUEST_vars']['modfunc']);
		unset($_SESSION['_REQUEST_vars']['delete_ok']);
	}
}

if(!$_REQUEST['modfunc'])
{
include('modules/Food_Service/'.($_REQUEST['type']=='staff'?'Users':'Students').'/Transactions.php');
}

function red($value)
{
	if($value<0)
		return '<FONT color=red>'.$value.'</FONT>';
	else
		return $value;
}

function is_money($value)
{
	if($value > 0) {
		if (strpos($value,'.')) return $value;
		elseif ($value >= 100) return $value/100;
		else return $value;
	}
	else return false;
}
?>
