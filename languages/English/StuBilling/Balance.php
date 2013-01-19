<?php

DrawHeader('Balance Report');

if($_REQUEST[modfunc]=='')
{
	echo '<BR>';
	PopTable('header','Search');
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=list method=POST>";
	echo '<TABLE>';
	Warehouse('searchstu');
	echo '<TR><TD>Balance Between</TD><TD><INPUT type=text name=balance_low> &amp; <INPUT type=text name=balance_high></TD></TR>';
	echo '<TR><TD>Balance Not Zero</TD><TD><INPUT type=checkbox name=not_zero value=Y></TD></TR>';
	echo '<TR><TD>Balance</TD><TD>';
	echo '<SELECT name=editor>
			<OPTION value="Student Billing">Student Billing</OPTION>
			<OPTION value=Lunch>Lunch</OPTION>
		</SELECT>';
	echo '</TD></TR>';
	PrepareSchool(SessionSchool(),'',SessionCurSchool());
	Warehouse('searchgrade');
	Warehouse('searchyear');
	echo '<TR><TD colspan=2 align=center>';
	Buttons('Find','Reset');
	echo '</TD></TR>';
	echo '</TABLE>';
	PopTable('footer');
}

if($_REQUEST[modfunc]=='list')
{
	if($_REQUEST[balance_low]>$_REQUEST[balance_high])
	{
		$tmp = $_REQUEST[balance_high];
		$_REQUEST[balance_high] = $_REQUEST[balance_low];
		$_REQUEST[balance_low] = $tmp;
	}
	
	DrawHeader($_REQUEST[editor].' Balances - School Year: '.DispYear($_REQUEST[year]).' as of '.ProperDate(DBDate()));

	if($_REQUEST[editor]=='Student Billing')
	{
		$sql = "SELECT ssm.STUDENT_ID,ssm.SCHOOL,ssm.GRADE,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME ";
		$sql .= ",(SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_FEES WHERE STUDENT_ID=ssm.STUDENT_ID),0) -
		COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND  (LUNCH_PAYMENT!='Y' OR LUNCH_PAYMENT IS NULL) ),0)
		".FROM_DUAL.") as BALANCE ";
		$sql .= "FROM STU_SCHOOL_MEETS ssm,STUDENTS s
				WHERE ssm.SYEAR='$_REQUEST[year]' AND s.STUDENT_ID=ssm.STUDENT_ID "; 
		if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
			$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID),0) -
					COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_FEES WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
					 BETWEEN '$_REQUEST[balance_low]' AND '$_REQUEST[balance_high]' ";
		if($_REQUEST[not_zero]=='Y')
			$sql .="AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID),0) -
					COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_FEES WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
					!= '0' ";
		if($_REQUEST[sch])
			$sql .= "AND ssm.SCHOOL='$_REQUEST[sch]' ";
		if($_REQUEST[grade])
			$sql .= "AND ssm.GRADE='$_REQUEST[grade]' ";
		if($_REQUEST[stuid])
			$sql .= "AND ssm.STUDENT_ID='$_REQUEST[stuid]' ";
		if($_REQUEST[first])
			$sql .= "AND s.FIRST_NAME LIKE '".strtoupper($_REQUEST[first])."%' ";
		if($_REQUEST[last])
			$sql .= "AND s.LAST_NAME LIKE '".strtoupper($_REQUEST[last])."%' ";
		$sql .= "ORDER BY s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME ";
	}
	else
	{
		$sql = "SELECT ssm.STUDENT_ID,ssm.SCHOOL,ssm.GRADE,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME ";
		$sql .= ",(SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT_LUNCH WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.") - 
		COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND  LUNCH_PAYMENT='Y' ),0) as BALANCE ";
		$sql .= "FROM STU_SCHOOL_MEETS ssm,STUDENTS s
				WHERE ssm.SYEAR='$_REQUEST[year]' AND s.STUDENT_ID=ssm.STUDENT_ID "; 
		if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
			$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND  LUNCH_PAYMENT='Y' ),0) -
					COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT_LUNCH WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
					 BETWEEN '$_REQUEST[balance_low]' AND '$_REQUEST[balance_high]' ";
		if($_REQUEST[not_zero]=='Y')
			$sql .="AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID),0) -
					COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_FEES WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
					!= '0' ";
		if($_REQUEST[sch])
			$sql .= "AND ssm.SCHOOL='$_REQUEST[sch]' ";
		if($_REQUEST[grade])
			$sql .= "AND ssm.GRADE='$_REQUEST[grade]' ";
		if($_REQUEST[stuid])
			$sql .= "AND ssm.STUDENT_ID='$_REQUEST[stuid]' ";
		if($_REQUEST[first])
			$sql .= "AND s.FIRST_NAME LIKE '".strtoupper($_REQUEST[first])."%' ";
		if($_REQUEST[last])
			$sql .= "AND s.LAST_NAME LIKE '".strtoupper($_REQUEST[last])."%' ";
		$sql .= "ORDER BY s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME ";
	}

	$QI = DBQuery($sql);
	$RET = DBGet($QI,array('LAST_NAME'=>'GetCapWords','FIRST_NAME'=>'GetCapWords','SCHOOL'=>'GetSchool','GRADE'=>'GetGrade','BALANCE'=>'Currency'));
	
	$columns = array('LAST_NAME'=>'Last Name','FIRST_NAME'=>'First Name','STUDENT_ID'=>'Student ID','SCHOOL'=>'School','GRADE'=>'Grade','BALANCE'=>'Balance');
	ListOutput($RET,$columns,'Student','Students');

}
?>