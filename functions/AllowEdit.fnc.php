<?php

function AllowEdit($modname=false)
{	global $_CENTRE;

	if(!$modname)
		$modname = $_REQUEST['modname'];

	if($modname=='Students/Student.php' && $_REQUEST['category_id'])
		$modname = $modname.'&category_id='.$_REQUEST['category_id'];

	if(User('PROFILE')=='admin')
	{
		if(!$_CENTRE['AllowEdit'])
		{
			if(User('PROFILE_ID'))
				$_CENTRE['AllowEdit'] = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_EDIT='Y'"),array(),array('MODNAME'));
			else
				$_CENTRE['AllowEdit'] = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_EDIT='Y'"),array(),array('MODNAME'));
		}

		if(!$_CENTRE['AllowEdit'])
			$_CENTRE['AllowEdit'] = array(true);

		if(count($_CENTRE['AllowEdit'][$modname]))
			return true;
		else
			return false;
	}
	else
		return $_CENTRE['allow_edit'];
}

function AllowUse($modname=false)
{	global $_CENTRE;

	if(!$modname)
		$modname = $_REQUEST['modname'];

	if($modname=='Students/Student.php' && $_REQUEST['category_id'])
		$modname = $modname.'&category_id='.$_REQUEST['category_id'];

	if(!$_CENTRE['AllowUse'])
	{
		if(User('PROFILE_ID')!='')
			$_CENTRE['AllowUse'] = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
		else
			$_CENTRE['AllowUse'] = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
	}

	if(!$_CENTRE['AllowUse'])
		$_CENTRE['AllowUse'] = array(true);

	if(count($_CENTRE['AllowUse'][$modname]))
		return true;
	else
		return false;
}

function ProgramLink($modname,$title='',$options='')
{
	if(AllowUse($modname))
		$link = '<A HREF=Modules.php?modname='.$modname.$options.'>';
	if($title)
		$link .= $title;
	if(AllowUse($modname))
		$link .= '</A>';

	return $link;
}

function ProgramLinkforExport($modname,$title='',$options='')
{
	if(AllowUse($modname))
		$link = '<A HREF=for_export.php?modname='.$modname.$options.'>';
	if($title)
		$link .= $title;
	if(AllowUse($modname))
		$link .= '</A>';

	return $link;
}

?>