<?php
/**
* @file $Id: MassAssignPayments.php 422 2007-02-10 22:08:22Z focus-sis $
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
	foreach($_REQUEST['student'] as $student_id=>$yes)
	{
		$sql = "INSERT INTO BILLING_PAYMENTS (ID,SYEAR,SCHOOL_ID,STUDENT_ID,PAYMENT_DATE,AMOUNT,COMMENTS)
					values(".db_seq_nextval('BILLING_PAYMENTS_SEQ').",'".UserSyear()."','".UserSchool()."','".$student_id."','".DBDate()."','".ereg_replace('[^0-9,.]+','',$_REQUEST['amount'])."','".str_replace("\'","''",$_REQUEST['comments'])."')";
		DBQuery($sql);
	}
	unset($_REQUEST['modfunc']);
	$note = _("That payment has been added to the selected students.");
}

if(!$_REQUEST['modfunc'])
{
	DrawHeader(ProgramTitle());
	if($note)
		DrawHeader('<IMG SRC=assets/check.gif>'.$note);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
		DrawHeader('',SubmitButton(_('Add Payment to Selected Students')));
		
		echo '<BR><CENTER><TABLE bgcolor=#'.Preferences('COLOR').'><TR><TD align='.ALIGN_RIGHT.'>'._('Payment Amount').'</TD><TD><INPUT type=text name=amount size=5 maxlength=10></TD></TR>';
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
		echo '<BR><CENTER>'.SubmitButton(_('Add Payment to Selected Students')).'</CENTER>';
		echo "</FORM>";
	}

}

function _makeChooseCheckbox($value,$title)
{	global $THIS_RET;

	return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}

?>