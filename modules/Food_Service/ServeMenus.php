<?php
require_once('modules/Food_Service/includes/DeletePromptX.fnc.php');

if($_REQUEST['modfunc']=='select')
{
	$_SESSION['FSA_type'] = $_REQUEST['fsa_type'];
	unset($_REQUEST['modfunc']);
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
$header = '<TABLE border=0 cellpadding=0 cellspacing=0 height=14><TR>';
$header .= '<TD width=10></TD><TD>'.DrawTab('Students',"Modules.php?modname=$_REQUEST[modname]&modfunc=select&menu_id=$_REQUEST[menu_id]&fsa_type=student",$tabcolor_s,$textcolor_s,'_circle',array('tabcolor'=>Preferences('HEADER'),'textcolor'=>'#FFFFFF')).'</TD>';
$header .= '<TD width=10></TD><TD>'.DrawTab('Users',   "Modules.php?modname=$_REQUEST[modname]&modfunc=select&menu_id=$_REQUEST[menu_id]&fsa_type=staff",  $tabcolor_u,$textcolor_u,'_circle',array('tabcolor'=>Preferences('HEADER'),'textcolor'=>'#FFFFFF')).'</TD>';
$header .= '<TD width=10></TD></TR></TABLE>';

DrawHeader(($_SESSION['FSA_type']=='staff' ? 'User ' : 'Student ').ProgramTitle(),'<TABLE bgcolor=#ffffff><TR><TD>'.$header.'</TD></TR></TABLE>');

$menus_RET = DBGet(DBQuery('SELECT MENU_ID,TITLE FROM FOOD_SERVICE_MENUS WHERE SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'),array(),array('MENU_ID'));
if(!$_REQUEST['menu_id'])
{
	if(!$_SESSION['FSA_menu_id'])
	if(count($menus_RET))
			$_REQUEST['menu_id'] = $_SESSION['FSA_menu_id'] = key($menus_RET);
		else
			ErrorMessage(array('There are no menus yet setup.'),'fatal');
	else
		$_REQUEST['menu_id'] = $_SESSION['FSA_menu_id'];
	unset($_SESSION['FSA_sale']);

	# Prefill the sale with all menu items and let them remove the ones they don't want
	$items_RET = DBGet(DBQuery("SELECT fsi.ITEM_ID,fsi.SHORT_NAME,fsi.DESCRIPTION,fsi.PRICE,fsi.PRICE_REDUCED,fsi.PRICE_FREE,fsi.ICON FROM FOOD_SERVICE_ITEMS fsi,FOOD_SERVICE_MENU_ITEMS fsmi WHERE fsmi.MENU_ID='".$_REQUEST['menu_id']."' AND fsi.ITEM_ID=fsmi.ITEM_ID AND fsmi.CATEGORY_ID IS NOT NULL AND fsi.SCHOOL_ID='".UserSchool()."' ORDER BY fsi.SORT_ORDER"));
	foreach($items_RET as $row)
	{
		$_SESSION['FSA_sale'][$row['ITEM_ID']] = $row['SHORT_NAME'];
	}
	
}
else
	$_SESSION['FSA_menu_id'] = $_REQUEST['menu_id'];

if ($_REQUEST['modfunc']=='add')
{
	if($_REQUEST['item_sn'])
		$_SESSION['FSA_sale'][] = $_REQUEST['item_sn'];
	unset($_REQUEST['modfunc']);
}

if($_REQUEST['modfunc']=='remove')
{
	if($_REQUEST['id']!='')
		unset($_SESSION['FSA_sale'][$_REQUEST['id']]);
	unset($_REQUEST['modfunc']);
}

include('modules/Food_Service/'.($_SESSION['FSA_type']=='staff'?'Users/':'Students/').'/ServeMenus.php');

function red($value)
{
        if($value<0)
                return '<FONT color=red>'.$value.'</FONT>';
        else
                return $value;
}

function makeIcon($value,$name)
{	global $FS_IconsPath;

	if($value)
		return '<IMG src='.$FS_IconsPath.'/'.$value.' height=30>';
	else
		return '&nbsp;';
}
?>
