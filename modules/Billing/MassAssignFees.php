<?php
/**
* @file $Id: MassAssignFees.php 422 2007-02-10 22:08:22Z focus-sis $
* @package Focus/SIS
* @copyright Copyright (C) 2006 Andrew Schmadeke. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
* Focus/SIS is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.txt for copyright notices and details.
*/

if($_REQUEST['modfunc']=='save')
{
	$due_date = $_REQUEST['day'].'-'.$_REQUEST['month'].'-'.$_REQUEST['year'];
	if(!VerifyDate($due_date))
		BackPrompt(_('The date you entered is not valid'));

	foreach($_REQUEST['student'] as $student_id=>$yes)
	{
			$sql = "INSERT INTO BILLING_FEES (STUDENT_ID,ID,TITLE,AMOUNT,SYEAR,SCHOOL_ID,ASSIGNED_DATE,DUE_DATE,COMMENTS)
						values('".$student_id."',".db_seq_nextval('BILLING_FEES_SEQ').",'".str_replace("\'","''",$_REQUEST['title'])."','".ereg_replace('[^0-9,.]+','',$_REQUEST['amount'])."','".UserSyear()."','".UserSchool()."','".DBDate()."','".$due_date."','".str_replace("\'","''",$_REQUEST['comments'])."')";
			DBQuery($sql);
	}
	unset($_REQUEST['modfunc']);
	$note = _("That fee has been added to the selected students.");
}


if(!$_REQUEST['modfunc'])
{
	DrawHeader(ProgramTitle());
	if($note)
		DrawHeader('<IMG SRC=assets/check.gif>'.$note);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
		DrawHeader('',SubmitButton(_('Add Fee to Selected Students')));
		
		echo '<BR><CENTER><TABLE bgcolor=#'.Preferences('COLOR').'>';
		echo '<TR><TD align='.ALIGN_RIGHT.'>'._('Title').'</TD><TD><INPUT type=text name=title></TD></TR>';
		echo '<TR><TD align='.ALIGN_RIGHT.'>'._('Amount').'</TD><TD><INPUT type=text name=amount size=5 maxlength=10></TD></TR>';
		echo '<TR><TD align='.ALIGN_RIGHT.'>'._('Due Date').'</TD><TD>'.PrepareDate(DBDate(),'').'</TD></TR>';
		echo '<TR><TD align='.ALIGN_RIGHT.'>'._('Comment').'</TD><TD><INPUT type=text name=comments></TD></TR>';
		echo '</TABLE></CENTER><BR>';
	}
}

if(!$_REQUEST['modfunc'])
{
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",NULL AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['new'] = true;

	Widgets('all');
	
	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER>'.SubmitButton(_('Add Fee to Selected Students')).'</CENTER>';
		echo "</FORM>";
	}

}

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

	return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}

?>