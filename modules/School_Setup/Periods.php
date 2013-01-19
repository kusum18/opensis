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
if($_REQUEST['values'] && ($_POST['values'] || $_REQUEST['ajax']) && AllowEdit())
{
	foreach($_REQUEST['values'] as $id=>$columns)
	{
		if($columns['START_HOUR'])
		{	
			$columns['START_TIME'] = $columns['START_HOUR'].':'.$columns['START_MINUTE'].' '.$columns['START_M'];
			unset($columns['START_HOUR']);unset($columns['START_MINUTE']);unset($columns['START_M']);
		}
		if($columns['END_HOUR'])
		{
			$columns['END_TIME'] = $columns['END_HOUR'].':'.$columns['END_MINUTE'].' '.$columns['END_M'];
			unset($columns['END_HOUR']);unset($columns['END_MINUTE']);unset($columns['END_M']);
		}
		
		if($id!='new')
		{
			$sql = "UPDATE SCHOOL_PERIODS SET ";
							
			foreach($columns as $column=>$value)
			{
				$sql .= $column."='".str_replace("\'","''",$value)."',";
			}
			$sql = substr($sql,0,-1) . " WHERE PERIOD_ID='$id'";
			DBQuery($sql);
		}
		else
		{
			$sql = "INSERT INTO SCHOOL_PERIODS ";

			$fields = 'PERIOD_ID,SCHOOL_ID,SYEAR,';
			$values = db_seq_nextval('SCHOOL_PERIODS_SEQ').",'".UserSchool()."','".UserSyear()."',";

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
}

DrawBC("School Setup > ".ProgramTitle());

if($_REQUEST['modfunc']=='remove' && AllowEdit())
{
	if(DeletePrompt('period'))
	{
		DBQuery("DELETE FROM SCHOOL_PERIODS WHERE PERIOD_ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
}

if($_REQUEST['modfunc']!='remove')
{
	$sql = "SELECT PERIOD_ID,TITLE,SHORT_NAME,SORT_ORDER,LENGTH,START_TIME,END_TIME,BLOCK,ATTENDANCE FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER";
	$QI = DBQuery($sql);
	$periods_RET = DBGet($QI,array('TITLE'=>'_makeTextInput','SHORT_NAME'=>'_makeTextInput','SORT_ORDER'=>'_makeTextInputMod','BLOCK'=>'_makeTextInput','LENGTH'=>'_makeTextInputMod','START_TIME'=>'_makeTimeInput','END_TIME'=>'_makeTimeInput','ATTENDANCE'=>'_makeCheckboxInput'));

	$columns = array('TITLE'=>'Title','SHORT_NAME'=>'Short Name','SORT_ORDER'=>'Sort Order','LENGTH'=>'Length (minutes)','BLOCK'=>'Block','ATTENDANCE'=>'Used for Attendance');
	//,'START_TIME'=>'Start Time','END_TIME'=>'End Time'
	$link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'SHORT_NAME'=>_makeTextInput('','SHORT_NAME'),'LENGTH'=>_makeTextInputMod2('','LENGTH'),'SORT_ORDER'=>_makeTextInputMod2('','SORT_ORDER'),'BLOCK'=>_makeTextInput('','BLOCK'),'START_TIME'=>_makeTimeInput('','START_TIME'),'END_TIME'=>_makeTimeInput('','END_TIME'),'ATTENDANCE'=>_makeCheckboxInput('','ATTENDANCE'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
	$link['remove']['variables'] = array('id'=>'PERIOD_ID');
	
	echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
	#DrawHeader('',SubmitButton('Save'));
	ListOutput($periods_RET,$columns,'Period','Periods',$link);
	echo '<br><CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_school_setup_periods();"').'</CENTER>';
	echo '</FORM>';
}

function _makeTextInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
	
	if($name!='TITLE')
		$extra = 'size=5 maxlength=10 class=cell_floating ';
		else # added else for the first textbox merlinvicki
		$extra = 'class=cell_floating';
	
	return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeTextInputMod($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
	
	if($name!='TITLE')
		$extra = 'size=5 maxlength=10 class=cell_floating onkeydown=\"return numberOnly(event);\"';
	
	return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeTextInputMod2($value,$name)
{ global $THIS_RET;

if($THIS_RET['PERIOD_ID'])
$id = $THIS_RET['PERIOD_ID'];
else
$id = 'new';

if($name!='TITLE')
$extra = 'size=5 maxlength=10 class=cell_floating onkeydown="return numberOnly(event);"';

return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeCheckboxInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
	
	return CheckboxInput($value,'values['.$id.']['.$name.']','','',($id=='new'?true:false),'<IMG SRC=assets/check.gif height=15>','<IMG SRC=assets/x.gif height=15>');
}

function _makeTimeInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';

	$hour = substr($value,0,strpos($value,':'));
	$minute = substr($value,strpos($value,':'),strpos($value,' '));
	$m = substr($value,strpos($value,' '));

	for($i=1;$i<=11;$i++)
		$hour_options[$i] = $i;
	$hour_options['0'] = '12';
	
	for($i=0;$i<=9;$i++)
		$minute_options[$i] = '0'.$i;
	for($i=10;$i<=59;$i++)
		$minute_options[$i] = $i;
	
	$m_options = array('AM'=>'AM','PM'=>'PM');

	if($id!='new' && $value)
		return '<DIV id=time'.$id.'><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',SelectInput($hour,'values['.$id.'][START_HOUR]','',$hour_options,false,'',false)).':</TD><TD>'.str_replace('"','\"',SelectInput($minute,'values['.$id.'][START_MINUTE]','',$minute_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($m,'values['.$id.'][START_M]','',$m_options,false,'',false)).'</TD></TR></TABLE>","time'.$id.'",true);\'>'.$value.'</div></DIV>';
	else
		return '<TABLE cellspacing=0 cellpadding=0><TR><TD>'.SelectInput($hour,'values['.$id.'][START_HOUR]','',$hour_options,false,'',false).':</TD><TD>'.SelectInput($minute,'values['.$id.'][START_MINUTE]','',$minute_options,false,'',false).'</TD><TD>'.SelectInput($m,'values['.$id.'][START_M]','',$m_options,false,'',false).'</TD></TR></TABLE>';
}


/* ****************************************** Validation Start ********************************************** */
// include=General_Info && student_id=new
//	if($_REQUEST['category_id']=='new')
//	{
/*	
	if($_REQUEST['modfunc']!='remove')
	{
	
	echo '
	<script language="JavaScript" type="text/javascript">

  	var frmvalidator  = new Validator("F1");
	
  //	frmvalidator.addValidation("values[new][TITLE]","req","Please enter the title");
	//	frmvalidator.addValidation("values[new][TITLE]","alphabetic", "Title allows only alphabetic value");
	frmvalidator.addValidation("values[new][TITLE]","alnum", "Title allows only alphanumeric value");
	frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
	
  //	frmvalidator.addValidation("values[new][SHORT_NAME]","req","Please enter the Short Name");
	//	frmvalidator.addValidation("values[new][SHORT_NAME]","alphabetic", "Short Name allows only alphabetic value");
	frmvalidator.addValidation("values[new][SHORT_NAME]","alnum", "Short Name allows only alphanumeric value");
	frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for Short Name is 50");
	
  //	frmvalidator.addValidation("values[new][SORT_ORDER]","req","Please enter the Sort Order");
	frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");
	frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for Short Order is 5");
	
	frmvalidator.addValidation("values[new][LENGTH]","num", "Length (minutes) allows only numeric value");
	
	
	</script>
	';
	}
*/
//}	

/* ******************************************* Validation End *********************************************** */

?>