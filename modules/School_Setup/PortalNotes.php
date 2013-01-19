<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. It is  web-based, 
#  open source, and comes packed with features that include student 
#  demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  Copyright (C) 2007-2008, Open Solutions for Education, Inc.
#
#*************************************************************************
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, version 2 of the License. See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#**************************************************************************
if($_REQUEST['day_values'] && ($_POST['day_values'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['day_values'] as $id=>$values)
	{
		if($_REQUEST['day_values'][$id]['START_DATE'] && $_REQUEST['month_values'][$id]['START_DATE'] && $_REQUEST['year_values'][$id]['START_DATE'])
			$_REQUEST['values'][$id]['START_DATE'] = $_REQUEST['day_values'][$id]['START_DATE'].'-'.$_REQUEST['month_values'][$id]['START_DATE'].'-'.$_REQUEST['year_values'][$id]['START_DATE'];
		elseif(isset($_REQUEST['day_values'][$id]['START_DATE']) && isset($_REQUEST['month_values'][$id]['START_DATE']) && isset($_REQUEST['year_values'][$id]['START_DATE']))
			$_REQUEST['values'][$id]['START_DATE'] = '';

		if($_REQUEST['day_values'][$id]['END_DATE'] && $_REQUEST['month_values'][$id]['END_DATE'] && $_REQUEST['year_values'][$id]['END_DATE'])
			$_REQUEST['values'][$id]['END_DATE'] = $_REQUEST['day_values'][$id]['END_DATE'].'-'.$_REQUEST['month_values'][$id]['END_DATE'].'-'.$_REQUEST['year_values'][$id]['END_DATE'];
		elseif(isset($_REQUEST['day_values'][$id]['END_DATE']) && isset($_REQUEST['month_values'][$id]['END_DATE']) && isset($_REQUEST['year_values'][$id]['END_DATE']))
			$_REQUEST['values'][$id]['END_DATE'] = '';
	}
	if(!$_POST['values'])
		$_POST['values'] = $_REQUEST['values'];
}

$profiles_RET = DBGet(DBQuery("SELECT ID,TITLE FROM USER_PROFILES ORDER BY ID"));
if((($_REQUEST['profiles'] && ($_POST['profiles']  || $_REQUEST['ajax'])) || ($_REQUEST['values'] && ($_POST['values'] || $_REQUEST['ajax']))) && AllowEdit())
{
	$notes_RET = DBGet(DBQuery("SELECT ID FROM PORTAL_NOTES WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));

	foreach($notes_RET as $note_id)
	{
		$note_id = $note_id['ID'];
		$_REQUEST['values'][$note_id]['PUBLISHED_PROFILES'] = '';
		foreach(array('admin','teacher','parent') as $profile_id)
			if($_REQUEST['profiles'][$note_id][$profile_id])
				$_REQUEST['values'][$note_id]['PUBLISHED_PROFILES'] .= ','.$profile_id;
		if(count($_REQUEST['profiles'][$note_id]))
		{
			foreach($profiles_RET as $profile)
			{
				$profile_id = $profile['ID'];

				if($_REQUEST['profiles'][$note_id][$profile_id])
					$_REQUEST['values'][$note_id]['PUBLISHED_PROFILES'] .= ','.$profile_id;
			}
		}
		if($_REQUEST['values'][$note_id]['PUBLISHED_PROFILES'])
			$_REQUEST['values'][$note_id]['PUBLISHED_PROFILES'] .= ',';
	}
}

if($_REQUEST['values'] && ($_POST['values'] || $_REQUEST['ajax']) && AllowEdit())
{
	foreach($_REQUEST['values'] as $id=>$columns)
	{
		if($id!='new')
		{
			$sql = "UPDATE PORTAL_NOTES SET ";

			foreach($columns as $column=>$value)
			{
				$sql .= $column."='".str_replace("\'","''",$value)."',";
			}
			$sql = substr($sql,0,-1) . " WHERE ID='$id'";
			DBQuery($sql);
		}
		else
		{
			if(count($_REQUEST['profiles']['new']))
			{
				foreach(array('admin','teacher','parent') as $profile_id)
				{
					if($_REQUEST['profiles']['new'][$profile_id])
						$_REQUEST['values']['new']['PUBLISHED_PROFILES'] .= $profile_id.',';
					$columns['PUBLISHED_PROFILES'] = ','.$_REQUEST['values']['new']['PUBLISHED_PROFILES'];
				}
				foreach($profiles_RET as $profile)
				{
					$profile_id = $profile['ID'];

					if($_REQUEST['profiles']['new'][$profile_id])
						$_REQUEST['values']['new']['PUBLISHED_PROFILES'] .= $profile_id.',';
					$columns['PUBLISHED_PROFILES'] = ','.$_REQUEST['values']['new']['PUBLISHED_PROFILES'];
				}
			}
			else
				$_REQUEST['values']['new']['PUBLISHED_PROFILES'] = '';

			$sql = "INSERT INTO PORTAL_NOTES ";

			$fields = 'ID,SCHOOL_ID,SYEAR,PUBLISHED_DATE,PUBLISHED_USER,';
			$values = db_seq_nextval('PORTAL_NOTES_SEQ').",'".UserSchool()."','".UserSyear()."',CURRENT_TIMESTAMP,'".User('STAFF_ID')."',";

			$go = 0;
			foreach($columns as $column=>$value)
			{
				if($value)
				{
					$fields .= $column.',';
					$values .= "'".str_replace("\'","''",$value)."',";
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

			if($go)
				DBQuery($sql);
		}
	}
	unset($_REQUEST['values']);
	unset($_SESSION['_REQUEST_vars']['values']);
	unset($_REQUEST['profiles']);
	unset($_SESSION['_REQUEST_vars']['profiles']);
}

DrawBC("School Setup > ".ProgramTitle());

if($_REQUEST['modfunc']=='remove' && AllowEdit())
{
	if(DeletePrompt('message'))
	{
		DBQuery("DELETE FROM PORTAL_NOTES WHERE ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
}

if($_REQUEST['modfunc']!='remove')
{
	$sql = "SELECT ID,SORT_ORDER,TITLE,CONTENT,START_DATE,END_DATE,PUBLISHED_PROFILES,CASE WHEN END_DATE IS NOT NULL AND END_DATE<CURRENT_DATE THEN 'Y' ELSE NULL END AS EXPIRED FROM PORTAL_NOTES WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY EXPIRED DESC,SORT_ORDER,PUBLISHED_DATE DESC";
	$QI = DBQuery($sql);
	$notes_RET = DBGet($QI,array('TITLE'=>'_makeTextInput','CONTENT'=>'_makeContentInput','SORT_ORDER'=>'_makeTextInput_rc1','START_DATE'=>'_makePublishing'));

	$columns = array('TITLE'=>'Title','CONTENT'=>'Note','SORT_ORDER'=>'Sort Order','START_DATE'=>'Publishing Options');
	//,'START_TIME'=>'Start Time','END_TIME'=>'End Time'
	$link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'CONTENT'=>_makeContentInput('','CONTENT'),'SHORT_NAME'=>_makeTextInput('','SHORT_NAME'),'SORT_ORDER'=>_makeTextInput_rc('','SORT_ORDER'),'START_DATE'=>_makePublishing('','START_DATE'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
	$link['remove']['variables'] = array('id'=>'ID');

	echo "<FORM name=F2 id=F2 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
	#DrawHeader('',SubmitButton('Save'));
	ListOutput($notes_RET,$columns,'Note','Notes',$link);
	echo '<br><CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_school_setup_portalnotes();"').'</CENTER>';
	echo '</FORM>';
}

function _makeTextInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	if($name!='TITLE')
		$extra = 'size=5 maxlength=10 class=cell_floating';
		else # added else for the first textbox merlinvicki
		$extra = 'class=cell_floating';

	return TextInput($name=='TITLE' && $THIS_RET['EXPIRED']?array($value,'<FONT class=red>'.$value.'</FONT>'):$value,"values[$id][$name]",'',$extra);
}

function _makeTextInput_rc($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	if($name!='TITLE')
		$extra = 'size=5 maxlength=10 class=cell_floating';
		else # added else for the first textbox merlinvicki
		$extra = 'class=cell_floating';

	return TextInput($name=='TITLE' && $THIS_RET['EXPIRED']?array($value,'<FONT class=red>'.$value.'</FONT>'):$value,"values[$id][$name]",'',$extra);
}

function _makeTextInput_rc1($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	if($name!='TITLE')
		$extra = 'size=5 maxlength=10 class=cell_floating';
		else # added else for the first textbox merlinvicki
		$extra = 'class=cell_floating';

	return TextInput($name=='TITLE' && $THIS_RET['EXPIRED']?array($value,'<FONT class=red>'.$value.'</FONT>'):$value,"values[$id][$name]",'',$extra);
}



function _makeContentInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	return TextareaInput($value,"values[$id][$name]",'','rows=16 cols=55');
}

function _makePublishing($value,$name)
{	global $THIS_RET,$profiles_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	$return = '<TABLE width=216><TR><TD class=LO_field align="center"><b>Visible Between:</b></TD></tr><tr><TD align="center">';
	$return .= DateInput($value,"values[$id][$name]").'</TD></tr><tr><TD class=LO_field align="center"><b>&</b></TD></tr><tr><TD align="center">';
	$return .= DateInput($THIS_RET['END_DATE'],"values[$id][END_DATE]").'</TD></TR>';
	$return .= '<TR><TD colspan=4 class=break></TD></TR><TR><TD colspan=4>';

	if(!$profiles_RET)
		$profiles_RET = DBGet(DBQuery("SELECT ID,TITLE FROM USER_PROFILES ORDER BY ID"));

	$return .= '<TABLE border=0 cellspacing=0 cellpadding=0 width=96% class=LO_field><TR><TD colspan=4><b>Visible To: </b></TD></TR>';
	foreach(array('admin'=>'Administrator w/Custom','teacher'=>'Teacher w/Custom','parent'=>'Parent w/Custom') as $profile_id=>$profile)
		$return .= "<tr><TD colspan=4><INPUT type=checkbox name=profiles[$id][$profile_id] value=Y".(strpos($THIS_RET['PUBLISHED_PROFILES'],",$profile_id,")!==false?' CHECKED':'')."> $profile</TD></tr>";
	$i = 3;
	foreach($profiles_RET as $profile)
	{
		$i++;
		$return .= '<tr><TD colspan=4><INPUT type=checkbox name=profiles['.$id.']['.$profile['ID'].'] value=Y'.(strpos($THIS_RET['PUBLISHED_PROFILES'],",$profile[ID],")!==false?' CHECKED':'')."> $profile[TITLE]</TD></tr>";
		if($i%4==0 && $i!=count($profile))
			$return .= '<TR>';
	}
	for(;$i%4!=0;$i++)
		$return .= '<TD></TD>';
	$return .= '</TR>';
	//<TR><TD colspan=4><B><A HREF=#>Schools: ...</A></B></TD></TR></TABLE>';
	$return .= '</TABLE>';
	$return .= '</TD></TR></TABLE>';
	return $return;
}



?>