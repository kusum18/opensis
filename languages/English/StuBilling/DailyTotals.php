<?php
DrawHeader('Daily Totals');

if($_REQUEST[modfunc]=='')
{
	echo '<BR>';
	PopTable('header','Search');
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=list method=POST>";
	echo '<TABLE>';
	echo '<TR><TD>Beginning Date</TD><TD>'.PrepareDate('_begin',DBDate(),false).'</TD></TR>';
	echo '<TR><TD>Ending Date</TD><TD>'.PrepareDate('_end',DBDate(),false).'</TD></TR>';
	$accounts_RET = DBGet(DBQuery("SELECT ID,TITLE FROM STU_BILLING_ACCOUNTS WHERE SYEAR='".GetSysYear()."'"));
	PrepareSchool(SessionSchool(),'',SessionCurSchool());
	if(count($accounts_RET))
	{
		echo '<TR><TD>Account</TD><TD>';
		echo '<SELECT name=account_id>
			<OPTION value="">Not Specified</OPTION>';
		foreach($accounts_RET as $value)
			echo "<OPTION value=$value[ID]>$value[TITLE]</OPTION>";
		echo '</SELECT>';
		echo '</TD></TR>';
	}
	echo '<TR><TD colspan=2 align=center>';
	Buttons('Find','Reset');
	echo '</TD></TR>';
	echo '</TABLE>';
	PopTable('footer');
}

if($_REQUEST[modfunc]=='list')
{
	$begin_date = $_REQUEST[day_begin].'-'.$_REQUEST[month_begin].'-'.$_REQUEST[year_begin];
	$end_date = $_REQUEST[day_end].'-'.$_REQUEST[month_end].'-'.$_REQUEST[year_end];
	
	$account_title = DBGet(DBQuery("SELECT TITLE FROM STU_BILLING_ACCOUNTS WHERE ID='$_REQUEST[account_id]'"));
	if($account_title[1])
		$account_title = ' : '.$account_title[1]['TITLE'];

	DrawHeader(ProperDate($begin_date).' - '.ProperDate($end_date).' : '.GetSchool($_REQUEST[sch]).$account_title);
	echo '<BR>';
	echo '<TABLE>';
	
	// STUBILLING CREDITS
	echo '<TR><TH align=left>Student Billing Credits</TH>';
	$sql = "SELECT sum(AMOUNT) as TOTAL FROM STU_BILLING_ACT sba ";
	if($_REQUEST[sch])
		$sql .= ",STU_SCHOOL_MEETS ssm WHERE ssm.SYEAR=sba.SYEAR AND ssm.STUDENT_ID=sba.STUDENT_ID AND ssm.SCHOOL='$_REQUEST[sch]' AND ";
	else
		$sql .= "WHERE ";
	$sql .= "sba.PAYMENT_DATE BETWEEN '$begin_date' AND '$end_date' AND (sba.LUNCH_PAYMENT!='Y' OR sba.LUNCH_PAYMENT IS NULL) ".(($_REQUEST[account_id])?"AND ACCOUNT_ID='$_REQUEST[account_id]'":'');
	$QI = DBQuery($sql);
	$RET = DBGet($QI);
	echo '<TD>'.Currency($RET[1][TOTAL]).'</TD>';
	echo '</TR>';

	// STUBILLING DEBITS
	echo '<TR><TH align=left>Student Billing Debits</TH>';
	$sql = "SELECT sum(AMOUNT) as TOTAL FROM STU_BILLING_FEES sba ";
	if($_REQUEST[sch])
		$sql .= ",STU_SCHOOL_MEETS ssm WHERE ssm.SYEAR=sba.SYEAR AND ssm.STUDENT_ID=sba.STUDENT_ID AND ssm.SCHOOL='$_REQUEST[sch]' AND ";
	else
		$sql .= "WHERE ";
	$sql .= "sba.EFFECTIVE_DATE BETWEEN '$begin_date' AND '$end_date' ".(($_REQUEST[account_id])?"AND ACCOUNT_ID='$_REQUEST[account_id]'":'');
	$QI = DBQuery($sql);
	$RET = DBGet($QI);
	echo '<TD>'.Currency($RET[1][TOTAL]).'</TD>';
	echo '</TR>';

	// WAIVED DEBITS
	echo '<TR><TH align=left>Student Billing Waived Debits</TH>';
	$sql = "SELECT sum('0'-AMOUNT) as TOTAL FROM STU_BILLING_FEES sba ";
	if($_REQUEST[sch])
		$sql .= ",STU_SCHOOL_MEETS ssm WHERE ssm.SYEAR=sba.SYEAR AND ssm.STUDENT_ID=sba.STUDENT_ID AND ssm.SCHOOL='$_REQUEST[sch]' AND ";
	else
		$sql .= "WHERE ";
	$sql .= "sba.EFFECTIVE_DATE BETWEEN '$begin_date' AND '$end_date' AND sba.FORGIVEN_FEE_ID IS NOT NULL ".(($_REQUEST[account_id])?"AND ACCOUNT_ID='$_REQUEST[account_id]'":'');
	$QI = DBQuery($sql);
	$RET = DBGet($QI);
	echo '<TD>'.Currency($RET[1][TOTAL]).'</TD>';
	echo '</TR>';

	
	// LUNCH CREDITS
	echo '<TR><TH align=left>Lunch Credits</TH>';
	$sql = "SELECT sum(AMOUNT) as TOTAL FROM STU_BILLING_ACT sba ";
	if($_REQUEST[sch])
		$sql .= ",STU_SCHOOL_MEETS ssm WHERE ssm.SYEAR=sba.SYEAR AND ssm.STUDENT_ID=sba.STUDENT_ID AND ssm.SCHOOL='$_REQUEST[sch]' AND ";
	else
		$sql .= "WHERE ";
	$sql .= "sba.PAYMENT_DATE BETWEEN '$begin_date' AND '$end_date' AND sba.LUNCH_PAYMENT='Y' ";
	$QI = DBQuery($sql);
	$RET = DBGet($QI);
	echo '<TD>'.Currency($RET[1][TOTAL]).'</TD>';
	echo '</TR>';
	
	// LUNCH DEBITS
	echo '<TR><TH align=left>Lunch Debits</TH>';
	$sql = "SELECT sum(AMOUNT) as TOTAL FROM STU_BILLING_ACT_LUNCH sba ";
	if($_REQUEST[sch])
		$sql .= ",STU_SCHOOL_MEETS ssm WHERE ssm.SYEAR=sba.SYEAR AND ssm.STUDENT_ID=sba.STUDENT_ID AND ssm.SCHOOL='$_REQUEST[sch]' AND ";
	else
		$sql .= "WHERE ";
	$sql .= "sba.PAYMENT_DATE BETWEEN '$begin_date' AND '$end_date' ";
	$QI = DBQuery($sql);
	$RET = DBGet($QI);
	echo '<TD>'.Currency($RET[1][TOTAL]).'</TD>';
	echo '</TR>';
	
	echo '</TABLE>';
}



?>