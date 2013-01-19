<?php

DrawHeader('Pre-Defined Fee Report');

if($_REQUEST[modfunc]=='')
{
	echo '<BR>';
	PopTable('header','Search');
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=list method=POST>";
	echo '<TABLE>';
	Warehouse('searchstu');
	echo '<TR><TD>Pre-Defined Fee</TD><TD>';
	echo '<SELECT name=fee_id>';
	$RET = DBGet(DBQuery("SELECT TITLE,ID FROM STU_BILLING_DEFINED_FEES WHERE SYEAR='".GetSysYear()."'"));
	if(count($RET))
	{
		foreach($RET as $value)
			echo "<OPTION value='$value[ID]'>$value[TITLE]</OPTION>";
	}
	echo '</SELECT>';
	echo '</TD></TR>';
	echo '<TR><TD>Show Students</TD><TD>';
	echo '<SELECT name=editor>
			<OPTION value=with>With this Fee</OPTION>
			<OPTION value=without>Without this Fee</OPTION>
			<OPTION value=waived>With this Fee Waived</OPTION>
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
	DrawHeader('School Year: '.DispYear($_REQUEST[year]).' as of '.ProperDate(DBDate()));

	switch($_REQUEST[editor])
	{
		case 'without':
		$not = 'NOT';
		break;
		
		case 'waived':
		$waived = 'AND FORGIVEN_FEE_ID IS NOT NULL';
		break;
		
		default:
		break;
	}
		
	$sql = "SELECT ssm.STUDENT_ID,ssm.SCHOOL,ssm.GRADE,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME 
			FROM STU_SCHOOL_MEETS ssm,STUDENTS s
			WHERE ssm.SYEAR='$_REQUEST[year]' AND s.STUDENT_ID=ssm.STUDENT_ID 
				AND $not EXISTS (SELECT '' FROM STU_BILLING_FEES WHERE DEFINED_ID='$_REQUEST[fee_id]' $waived)"; 
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

	$QI = DBQuery($sql);
	$RET = DBGet($QI,array('LAST_NAME'=>'GetCapWords','FIRST_NAME'=>'GetCapWords','SCHOOL'=>'GetSchool','GRADE'=>'GetGrade'));
	
	$columns = array('LAST_NAME'=>'Last Name','FIRST_NAME'=>'First Name','STUDENT_ID'=>'Student ID','SCHOOL'=>'School','GRADE'=>'Grade');
	ListOutput($RET,$columns,'Student','Students');

}
?>