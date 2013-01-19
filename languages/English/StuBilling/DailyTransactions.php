<?php
DrawHeader('Daily Transactions');

if($_REQUEST[modfunc]=='')
{
	echo '<BR>';
	PopTable('header','Search');
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=list method=POST>";
	echo '<TABLE>';
	echo '<TR><TD>Beginning Date</TD><TD>'.PrepareDate('_begin',DBDate(),false).'</TD></TR>';
	echo '<TR><TD>Ending Date</TD><TD>'.PrepareDate('_end',DBDate(),false).'</TD></TR>';
	PrepareSchool(SessionSchool(),'',SessionCurSchool());
	echo '<TR><TD>Report</TD><TD>';
	echo '<SELECT name=type>
			<OPTION value=stubillingcredits>Student Billing Credits</OPTION>
			<OPTION value=stubillingdebits>Student Billing Debits</OPTION>
			<OPTION value="stubillingwaived debits">Student Billing Waived Debits</OPTION>
			<OPTION value=lunchcredits>Lunch Credits</OPTION>
			<OPTION value=lunchdebits>Lunch Debits</OPTION>
		</SELECT>';
	echo '</TD></TR>';
	$accounts_RET = DBGet(DBQuery("SELECT ID,TITLE FROM STU_BILLING_ACCOUNTS WHERE SYEAR='".GetSysYear()."'"));
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

	DrawHeader(ucwords(str_replace('stubilling','student billing ',str_replace('lunch','lunch ',$_REQUEST[type]))).' : '.ProperDate($begin_date).' - '.ProperDate($end_date).' : '.GetSchool($_REQUEST[sch]).$account_title);
	echo '<BR>';
	echo '<TABLE>';
	
	switch($_REQUEST[type])
	{
		case 'stubillingcredits':
		// STUBILLING CREDITS
		$sql = "SELECT s.LAST_NAME||', '||s.FIRST_NAME as FULL_NAME,sba.STUDENT_ID,sum(AMOUNT) as AMOUNT FROM STU_BILLING_ACT sba,STUDENTS s ";
		if($_REQUEST[sch])
			$sql .= ",STU_SCHOOL_MEETS ssm WHERE ssm.SYEAR=sba.SYEAR AND ssm.STUDENT_ID=sba.STUDENT_ID AND ssm.SCHOOL='$_REQUEST[sch]' AND s.STUDENT_ID=ssm.STUDENT_ID AND ";
		else
			$sql .= "WHERE ";
		$sql .= "s.STUDENT_ID=sba.STUDENT_ID AND sba.PAYMENT_DATE BETWEEN '$begin_date' AND '$end_date' AND (sba.LUNCH_PAYMENT!='Y' OR sba.LUNCH_PAYMENT IS NULL) ".(($_REQUEST[account_id])?"AND ACCOUNT_ID='$_REQUEST[account_id]'":'')." GROUP BY sba.STUDENT_ID,FULL_NAME";
		break;
		
		case 'stubillingdebits':
		// STUBILLING CREDITS
		$sql = "SELECT s.LAST_NAME||', '||s.FIRST_NAME as FULL_NAME,sba.STUDENT_ID,sum(AMOUNT) as AMOUNT FROM STU_BILLING_FEES sba,STUDENTS s ";
		if($_REQUEST[sch])
			$sql .= ",STU_SCHOOL_MEETS ssm WHERE ssm.SYEAR=sba.SYEAR AND ssm.STUDENT_ID=sba.STUDENT_ID AND ssm.SCHOOL='$_REQUEST[sch]' AND s.STUDENT_ID=ssm.STUDENT_ID AND ";
		else
			$sql .= "WHERE ";
		$sql .= "s.STUDENT_ID=sba.STUDENT_ID AND sba.EFFECTIVE_DATE BETWEEN '$begin_date' AND '$end_date' ".(($_REQUEST[account_id])?"AND ACCOUNT_ID='$_REQUEST[account_id]'":'')." GROUP BY sba.STUDENT_ID,FULL_NAME";
		break;

		case 'stubillingwaived debits':
		// STUBILLING CREDITS
		$sql = "SELECT s.LAST_NAME||', '||s.FIRST_NAME as FULL_NAME,sba.STUDENT_ID,sum('0'-AMOUNT) as AMOUNT FROM STU_BILLING_FEES sba,STUDENTS s ";
		if($_REQUEST[sch])
			$sql .= ",STU_SCHOOL_MEETS ssm WHERE ssm.SYEAR=sba.SYEAR AND ssm.STUDENT_ID=sba.STUDENT_ID AND ssm.SCHOOL='$_REQUEST[sch]' AND s.STUDENT_ID=ssm.STUDENT_ID AND ";
		else
			$sql .= "WHERE ";
		$sql .= "s.STUDENT_ID=sba.STUDENT_ID AND sba.EFFECTIVE_DATE BETWEEN '$begin_date' AND '$end_date' AND FORGIVEN_FEE_ID IS NOT NULL ".(($_REQUEST[account_id])?"AND ACCOUNT_ID='$_REQUEST[account_id]'":'')." GROUP BY sba.STUDENT_ID,FULL_NAME";
		break;
		
		case 'lunchcredits':
		// STUBILLING CREDITS
		$sql = "SELECT s.LAST_NAME||', '||s.FIRST_NAME as FULL_NAME,sba.STUDENT_ID,sum(AMOUNT) as AMOUNT FROM STU_BILLING_ACT sba,STUDENTS s ";
		if($_REQUEST[sch])
			$sql .= ",STU_SCHOOL_MEETS ssm WHERE ssm.SYEAR=sba.SYEAR AND ssm.STUDENT_ID=sba.STUDENT_ID AND ssm.SCHOOL='$_REQUEST[sch]' AND s.STUDENT_ID=ssm.STUDENT_ID AND ";
		else
			$sql .= "WHERE ";
		$sql .= "s.STUDENT_ID=sba.STUDENT_ID AND sba.PAYMENT_DATE BETWEEN '$begin_date' AND '$end_date' AND sba.LUNCH_PAYMENT='Y' GROUP BY sba.STUDENT_ID,FULL_NAME";
		break;

		case 'lunchdebits':
		// STUBILLING CREDITS
		$sql = "SELECT s.LAST_NAME||', '||s.FIRST_NAME as FULL_NAME,sba.STUDENT_ID,sum(AMOUNT) as AMOUNT FROM STU_BILLING_ACT_LUNCH sba,STUDENTS s ";
		if($_REQUEST[sch])
			$sql .= ",STU_SCHOOL_MEETS ssm WHERE ssm.SYEAR=sba.SYEAR AND ssm.STUDENT_ID=sba.STUDENT_ID AND ssm.SCHOOL='$_REQUEST[sch]' AND s.STUDENT_ID=ssm.STUDENT_ID AND ";
		else
			$sql .= "WHERE ";
		$sql .= "s.STUDENT_ID=sba.STUDENT_ID AND sba.PAYMENT_DATE BETWEEN '$begin_date' AND '$end_date' GROUP BY sba.STUDENT_ID,FULL_NAME";
		break;
	}

	$QI = DBQuery($sql);
	$RET = DBGet($QI,array('FULL_NAME'=>'GetCapWords','AMOUNT'=>'sumCurrency'));

	$link[add][html] = array('FULL_NAME'=>'<B>Total</B>','AMOUNT'=>Currency($sumCurrency));
	$columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','AMOUNT'=>'Amount');
	ListOutput($RET,$columns,'Student','Students',$link);

}


function sumCurrency($amount)
{	global $sumCurrency;

	$sumCurrency += $amount;
	return Currency($amount);
}

?>