<?php
DrawHeader('Staff Billing Administration');

if($_REQUEST[modfunc]=='')
{
	PopTable('header','Find a Staff Member');
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=list method=POST>";
	echo '<TABLE>';
	echo '<TR><TD>Last Name</TD><TD><INPUT type=text name=last></TD></TR>';
	echo '<TR><TD>First Name</TD><TD><INPUT type=text name=first></TD></TR>';
	echo '<TR><TD>ID</TD><TD><INPUT type=text name=stuid></TD></TR>';
	PrepareSchool(SessionSchool(),'',SessionCurSchool());
	Warehouse('searchyear');
	echo '<TR><TD align=center colspan=2>';
	Buttons('Submit','Reset');
	echo '</TD></TR>';
	echo '</TABLE>';
	echo '</FORM>';
	PopTable('footer');
}


if($_REQUEST[modfunc]=='list')
{
	$sql = "SELECT LAST_NAME||', '||FIRST_NAME as FULL_NAME,TEACHER_ID AS STAFF_ID,SCHOOL,SCHOOL AS DISP_SCHOOL,'T' AS TYPE
			FROM TEACHERS WHERE SYEAR='$_REQUEST[year]' ";
	if($_REQUEST[sch])
		$sql .= "AND SCHOOL='$_REQUEST[sch]' ";
	if($_REQUEST[last])
		$sql .= "AND LAST_NAME LIKE '".strtoupper($_REQUEST[last])."%' ";
	if($_REQUEST[first])
		$sql .= "AND FIRST_NAME LIKE '".strtoupper($_REQUEST[first])."%' ";
	if($_REQUEST[stuid])
		$sql .= "AND TEACHER_ID='".strtoupper($_REQUEST[stuid])."' ";

	$sql .= "UNION ";

	$sql .= "SELECT LAST_NAME||', '||FIRST_NAME as FULL_NAME,STAFF_ID AS STAFF_ID,SCHOOL,SCHOOL AS DISP_SCHOOL,'S' AS TYPE
			FROM STAFF WHERE SYEAR='$_REQUEST[year]' ";
	if($_REQUEST[sch])
		$sql .= "AND SCHOOL='$_REQUEST[sch]' ";
	if($_REQUEST[last])
		$sql .= "AND LAST_NAME LIKE '".strtoupper($_REQUEST[last])."%' ";
	if($_REQUEST[first])
		$sql .= "AND FIRST_NAME LIKE '".strtoupper($_REQUEST[first])."%' ";
	if($_REQUEST[stuid])
		$sql .= "AND STAFF_ID='".strtoupper($_REQUEST[stuid])."' ";
	$sql .= "ORDER BY 1 ";
	
	$QI = DBQuery($sql);
	$RET = DBGet($QI,array('FULL_NAME'=>'GetCapWords','DISP_SCHOOL'=>'GetSchool'));
	
	$columns = array('FULL_NAME'=>'Staff Member','STAFF_ID'=>'Staff ID','DISP_SCHOOL'=>'School');
	$link['FULL_NAME']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=detail&editor=stubilling&f_year=$_REQUEST[year]";
	$link['FULL_NAME']['variables'] = array('f_stuid'=>'STAFF_ID','f_school'=>'SCHOOL','type'=>'TYPE');
	ListOutput($RET,$columns,'Staff Member','Staff Members',$link);
}


if($_REQUEST[modfunc]=='detail')
{
	$header = "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&f_school=$_REQUEST[f_school]&f_year=$_REQUEST[f_year]&f_stuid=$_REQUEST[f_stuid]&editor=$_REQUEST[editor]&type=$_REQUEST[type] method=POST>";
	$header .= "<SELECT name=editor onChange='document.forms[0].submit();'>";
	$header .= '<OPTION value=stubilling>Billing</OPTION>';
	$header .= "<OPTION value=lunch ".(($_REQUEST[editor]=='lunch')?' SELECTED':'').'>Lunch Billing</OPTION>';
	$header .= "</SELECT>";
	$header .= '</FORM>';

	if($_REQUEST[type]=='S')
	{
		define('STU_SCHOOL_MEETS','STAFF');
		define('SSMSTUDENT_ID','ssm.STAFF_ID');
	}
	elseif($_REQUEST[type]=='T')
	{
		define('STU_SCHOOL_MEETS','TEACHERS');
		define('SSMSTUDENT_ID','ssm.TEACHER_ID');
	}
	
	$QI = DBQuery('SELECT FIRST_NAME||\' \'||LAST_NAME AS FULL_NAME FROM '.STU_SCHOOL_MEETS.' ssm WHERE '.SSMSTUDENT_ID."='".$_REQUEST[f_stuid]."'");
	$name = DBGet($QI,array('FULL_NAME'=>'GetCapWords'));
	
	DrawHeader($name[1][FULL_NAME],$header);
	
	include('modules/StudentInfo/StuBilling.inc.php');
}
?>