<?php
DrawHeader('Mass Add Student Billing Fees');

if($_REQUEST[modfunc]=='update')
{
	if(DeletePrompt('fee to all students '.(($_REQUEST[grade])?'in '.GetGrade($_REQUEST[grade]):'').' '.(($_REQUEST[school])?'at '.GetSchool($_REQUEST[school]):''),'apply'))
	{
		$_REQUEST['date'] = $_REQUEST['day'] . '-' . $_REQUEST['month'] . '-' . $_REQUEST['year'];
		if($_REQUEST['date']=='--')
			$_REQUEST['date']='';
		$effective_date = DBDate();
		$global_id = DBGet(DBQuery("SELECT ".db_seq_nextval('STU_BILLING_GLOABL_SEQ').' AS GLOBAL_ID'.FROM_DUAL));
		if($_REQUEST['defined'])
		{
			$sql = "SELECT SYEAR,TITLE,AMOUNT,DUE_DATE,ACCOUNT_ID FROM STU_BILLING_DEFINED_FEES WHERE ID='$_REQUEST[defined]'";
			$defined = DBGet(DBQuery($sql));
			$defined = $defined[1];
			$_REQUEST['syear'] = $defined[SYEAR];
			$_REQUEST['title'] = $defined[TITLE];
			$_REQUEST['account_id'] = $defined[ACCOUNT_ID];
			$_REQUEST['amount'] = $defined[AMOUNT];
			$_REQUEST['date'] = $defined[DUE_DATE];
		}

		$sql = "INSERT INTO STU_BILLING_FEES 
					(ID,GLOBAL_ID,ACCOUNT_ID,STUDENT_ID,TITLE,AMOUNT,EFFECTIVE_DATE,DUE_DATE,SYEAR,SCHOOL,GRADE) 
					(SELECT ".db_seq_nextval('STU_BILLING_FEES_SEQ').",".$global_id[1][GLOBAL_ID].",'".$_REQUEST['account_id']."',ssm.STUDENT_ID,
						'".$_REQUEST['title']."','".$_REQUEST['amount']."','$effective_date',
						'".$_REQUEST['date']."','".$_REQUEST['syear']."',
						'".$_REQUEST['school']."','".$_REQUEST['grade']."'
					FROM STU_SCHOOL_MEETS ssm 
					WHERE
						ssm.SYEAR='$_REQUEST[syear]' AND ssm.ACTIVE='A' ";
		if($_REQUEST[grade])
			$sql .= "AND ssm.GRADE='$_REQUEST[grade]' ";
		if($_REQUEST[school])
			$sql .= "AND ssm.SCHOOL='$_REQUEST[school]' ";
		$sql .= ')';
							
		DBQuery($sql);
	
		$note[] = 'The Student Billing Fee '.$title.' has been Applied to all students '.(($_REQUEST[grade])?'in '.GetGrade($_REQUEST[grade]):'').' '.(($_REQUEST[school])?'at '.GetSchool($_REQUEST[school]):'');
		unset($_REQUEST[modfunc]);
	}
}

if($_REQUEST[modfunc]=='delete')
{
	if(DeletePrompt('fee for all applicable students'))
	{
		DBQuery("DELETE FROM STU_BILLING_FEES WHERE GLOBAL_ID=$_REQUEST[id]");
		$note[] = "That Fee has been Deleted for All Applicable Students";
		unset($_REQUEST[modfunc]);
	}
}

if(!$_REQUEST[modfunc])
{
	// LIST
	if($note)
	{
		echo '<TABLE width=100%><TR><TD bgcolor=#E8E8E9><font size=-1>';
		ErrorMessage($note,'note');
		echo '</font></TD></TR></TABLE>';
	}
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=update METHOD=POST>";
	
	$functions = array('ACCOUNT_ID'=>'getAccount','DUE_DATE'=>'ProperDate','SYEAR'=>'DispYear','SCHOOL'=>'GetSchool','GRADE'=>'GetGrade');
	$RET = DBGet(DBQuery('SELECT DISTINCT sb.GLOBAL_ID,sb.TITLE,sb.AMOUNT,sb.ACCOUNT_ID,
							to_char(sb.DUE_DATE,\'dd-MON-yy\') as DUE_DATE,sb.SYEAR,sb.SCHOOL,sb.GRADE 
						FROM STU_BILLING_FEES sb WHERE GLOBAL_ID IS NOT NULL ORDER BY sb.GLOBAL_ID'),$functions);
		
	$columns = array('TITLE'=>'Title','AMOUNT'=>'Amount','ACCOUNT_ID'=>'Account','DUE_DATE'=>'Due Date','SYEAR'=>'School Year','SCHOOL'=>'School','GRADE'=>'Grade');
	$link[add][html] = array('TITLE'=>makeTitleInput(),'AMOUNT'=>makeAmountInput(),'ACCOUNT_ID'=>makeAccountInput(),'DUE_DATE'=>makeDateInput(),'SYEAR'=>makeSyearInput(),'SCHOOL'=>makeSchoolInput(),'GRADE'=>makeGradeInput(),'remove'=>button('add'));
	$link[remove] = array('link'=>"Modules.php?modname=$_REQUEST[modname]&modfunc=delete",
					 'variables'=>array('id'=>'GLOBAL_ID'));
	$_REQUEST[modfunc] = 'list';
	unset($_REQUEST[modfunc]);
	ListOutput($RET,$columns,'Student Billing Fee','Student Billing Fees',$link);
	echo '<center><input type=submit class=btn_medium value=Save></center>';
}
// -- END LIST
	
function makeTitleInput($value='')
{
	$sql = "SELECT ID,TITLE,AMOUNT,SCHOOL,GRADE FROM STU_BILLING_DEFINED_FEES WHERE SYEAR='".GetSysYear()."'";
	$QI = DBQuery($sql);
	$defined_RET = DBGet($QI,array('SCHOOL'=>'GetSchool','Grade'=>'GetGrade'));
	if(count($defined_RET))
	{
		$return .= "<BR><SELECT name=defined><OPTION value=''>Not Specified</OPTION>";
		foreach($defined_RET as $defined)
			$return .= "<OPTION value=$defined[ID]>$defined[TITLE] - $defined[AMOUNT] - $defined[SCHOOL] $defined[GRADE]</OPTION>";
		$return .= "</SELECT>";
	}

	return "<INPUT type=text name=title maxlength=30 size=10 value='$value'>$return";
}	

function makeAmountInput($value='')
{
	return "<INPUT type=text name=amount maxlength=10 size=6 ".(($value)?"value=".Currency($value):'').'>';
}

function makeDateInput($value='00-000-00')
{

	return PrepareDate("",$value);
}

function makeYesInput($value='')
{
	if($value=='Y')
		return 'Yes';
	else
		return 'No';
}

function makeSchoolInput($value='')
{
		
	$QI = DBQuery("SELECT NAME,SCHOOL,SCHOOL_TYPE FROM SCHOOLS WHERE SCHOOL > '00' AND DISPLAY_FORMS='Y' ORDER BY ".db_case(array("SCHOOL","'ALL'","'2'","'1'")).",".db_case(array("SCHOOL_TYPE","'ELM'","'1'","'MID'","'2'","'HIGH'","'3'","'4'")).",NAME ");
	$RET = DBReturn($QI,$count);
	$return .= "<SELECT name=school>";
	$return .= "<OPTION value=''>Not Specified</OPTION>";
	foreach($RET as $key=>$school)
	{
		$return .= "<OPTION value=$school[SCHOOL]";
		if($school[SCHOOL]==$value)
			$return .= " SELECTED";
		$return .= ">$school[NAME]</OPTION>";
	}
	$return .= "</SELECT>";
	return $return;
}

function makeSyearInput($value='')
{
		
	$syear = '20'.GetSysYear();

	if($value<50)
		$default = '20'.$value;
	else
		$default = '19'.$value;

	if(!$value)
		$default = $syear;
	$return .= "<SELECT name=syear>";
	for($i=$syear-3;$i<$syear+3;$i++)
	{
		$year = substr($i,2);
		$return .= "<OPTION value=$year".(($default==$i)?' SELECTED':'').">".ProperYear($year)."</OPTION>";
	}
	$return .= "</SELECT>";
	return $return;
}

function makeGradeInput($value='')
{		
	$QI = DBQuery("SELECT DISTINCT GRADE FROM SCHOOL_GRADELEVELS WHERE DISPLAY = '1' ORDER BY GRADE");
	$RET = DBReturnNoWord($QI,$count);
	$return .= "<SELECT name=grade>";
	$return .= "<OPTION value=''>N/A</OPTION>";
	foreach($RET as $grade)
	{
		$return .= "<OPTION value=$grade[GRADE]";
		if($grade[GRADE]==$value)
			$return .= " SELECTED";
		$return .= ">$grade[GRADE]</OPTION>";
	}
	$return .= "</SELECT>";
	return $return;
}

function makeAccountInput($value='')
{		
	$QI = DBQuery("SELECT TITLE,ID FROM STU_BILLING_ACCOUNTS WHERE SYEAR='".GetSysYear()."' ORDER BY TITLE");
	$RET = DBReturnNoWord($QI,$count);
	$return .= "<SELECT name=account_id>";
	foreach($RET as $account)
	{
		$return .= "<OPTION value=$account[ID]";
		if($account[ID]==$value)
			$return .= " SELECTED";
		$return .= ">$account[TITLE]</OPTION>";
	}
	$return .= "</SELECT>";
	return $return;
}

function getAccount($account_id)
{	global $_CENTRE;
	
	if(!$_CENTRE['GetAccount'])
	{
		$QI=DBQuery("SELECT ID,TITLE FROM STU_BILLING_ACCOUNTS");
		$_CENTRE['GetAccount'] = DBGet($QI,array(),array('ID'));
	}

	return $_CENTRE['GetAccount'][$account_id][1]['TITLE'];
}
?>