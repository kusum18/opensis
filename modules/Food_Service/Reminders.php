<?php
if($_REQUEST['type'])
	$_SESSION['FSA_type'] = $_REQUEST['type'];
else
	$_SESSION['_REQUEST_vars']['type'] = $_REQUEST['type'] = $_SESSION['FSA_type'];

if($_REQUEST['modfunc']!='save')
{
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
//$header .= '<TD width=10></TD><TD>'.DrawTab('Students',"Modules.php?modname=$_REQUEST[modname]&type=student",$tabcolor_s,$textcolor_s,'_circle',array('tabcolor'=>Preferences('HEADER'),'textcolor'=>'FFFFFF')).'</TD>';
//$header .= '<TD width=10></TD><TD>'.DrawTab('Users',   "Modules.php?modname=$_REQUEST[modname]&type=staff",  $tabcolor_u,$textcolor_u,'_circle',array('tabcolor'=>Preferences('HEADER'),'textcolor'=>'FFFFFF')).'</TD>';
//$header .= '<TD width=10></TD></TR></TABLE>';
//$header = "";
//DrawHeader(($_REQUEST['type']=='staff' ? 'User ' : 'Student ').ProgramTitle(),'<TABLE bgcolor=#ffffff><TR><TD>'.$header.'</TD></TR></TABLE>');
DrawBC("Food Service >> ".ProgramTitle());

}
include('modules/Food_Service/'.($_REQUEST['type']=='staff' ? 'Users' : 'Students').'/Reminders.php');

function _makeChooseCheckbox($value,$title)
{
	global $THIS_RET;

	return '<INPUT type=checkbox name=st_arr[] value='.$value.($THIS_RET['WARNING']||$THIS_RET['NEGATIVE']||$THIS_RET['MINIMUM']?' checked>':'');
}

function x($value)
{
	if($value)
		return isset($_REQUEST['_CENTRE_PDF']) ? '<B>X</B>' : '<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>';
	else
		return '&nbsp;';
}

function red($value)
{
	if($value<0)
		return '<FONT color=red>'.$value.'</FONT>';
	else
		return $value;
}
?>
