<?php
DrawHeader('Pre-Defined Student Billing Fees');

if($_REQUEST[modfunc]=='update')
{
	$_REQUEST['date'] = $_REQUEST['day'] . '-' . $_REQUEST['month'] . '-' . $_REQUEST['year'];
	if($_REQUEST['date']=='--')
		$_REQUEST['date']='';
	$effective_date = DBDate();
	$sql = "INSERT INTO STU_BILLING_DEFINED_FEES (ID,ACCOUNT_ID,TITLE,AMOUNT,DUE_DATE,SYEAR,SCHOOL,GRADE) 
			values(".db_seq_nextval('STU_BILLING_DEFINED_FEES_SEQ').",'$_REQUEST[account_id]','$_REQUEST[title]','$_REQUEST[amount]','$_REQUEST[date]','$_REQUEST[syear]','$_REQUEST[school]','$_REQUEST[grade]')";
	DBQuery($sql);

	$note[] = 'That Pre-Defined Fee has been added';
	unset($_REQUEST[modfunc]);
}

if($_REQUEST[modfunc]=='delete')
{
	if(DeletePrompt('pre-defined fee'))
	{
		DBQuery("DELETE FROM STU_BILLING_DEFINED_FEES WHERE ID='$_REQUEST[id]'");
		$note[] = "That Pre-Defined Fee has been Deleted";
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
	
	$functions = array('AMOUNT'=>'Currency','ACCOUNT_ID'=>'getAccount','DUE_DATE'=>'ProperDate','SYEAR'=>'DispYear','SCHOOL'=>'GetSchool','GRADE'=>'GetGrade');
	$RET = DBGet(DBQuery('SELECT DISTINCT sb.ID,sb.TITLE,sb.AMOUNT,
							to_char(sb.DUE_DATE,\'dd-MON-yy\') as DUE_DATE,sb.SYEAR,sb.SCHOOL,sb.GRADE,sb.ACCOUNT_ID 
						FROM STU_BILLING_DEFINED_FEES sb ORDER BY sb.TITLE'),$functions);
		
	$columns = array('TITLE'=>'Title','AMOUNT'=>'Amount','ACCOUNT_ID'=>'Account','DUE_DATE'=>'Due Date','SYEAR'=>'School Year','SCHOOL'=>'School','GRADE'=>'Grade');
	$link[add][html] = array('TITLE'=>makeTitleInput(),'AMOUNT'=>makeAmountInput(),'ACCOUNT_ID'=>makeAccountInput(),'DUE_DATE'=>makeDateInput(),'SYEAR'=>makeSyearInput(),'SCHOOL'=>makeSchoolInput(),'GRADE'=>makeGradeInput(),'remove'=>button('add'));
	$link[remove] = array('link'=>"Modules.php?modname=$_REQUEST[modname]&modfunc=delete",
					 'variables'=>array('id'=>'ID'));
	$_REQUEST[modfunc] = 'list';
	unset($_REQUEST[modfunc]);
	ListOutput($RET,$columns,'Pre-Defined Fee','Pre-Defined Fees',$link);
	echo '<center><input type=submit class=btn_medium value=Save></center>';
}
// -- END LIST
	
function makeTitleInput($value='')
{
	return "<INPUT type=text name=title maxlength=30 size=10 value='$value'>";
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
		$return .= "<OPTION value=$year".(($default==$i)?' SELECTED':'').">".DispYear($year)."</OPTION>";
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