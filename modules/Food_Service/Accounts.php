<?php
require_once('modules/Food_Service/includes/DeletePromptX.fnc.php');

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
//DrawHeader(($_REQUEST['type']=='staff'?'User ':'Student ').ProgramTitle(),'<TABLE bgcolor=#ffffff><TR><TD>'.$header.'</TD></TR></TABLE>');
DrawBC("Food Service >> ".ProgramTitle());

include('modules/Food_Service/'.($_REQUEST['type']=='staff'?'Users':'Students').'/Accounts.php');

function red($value)
{
	if($value<0)
		return '<FONT color=red>'.$value.'</FONT>';
	else
		return $value;
}
?>
