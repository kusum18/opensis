<?php
include_once('modules/StudentInfo/StuBillingFunctions.php');
if(!defined('SSMSTUDENT_ID'))
	define('SSMSTUDENT_ID','ssm.STUDENT_ID');
if(!defined('STU_SCHOOL_MEETS'))
	define('STU_SCHOOL_MEETS','STU_SCHOOL_MEETS');

$student_id = $_REQUEST[f_stuid];
$editor = $_REQUEST[editor];

if($_REQUEST[stumodfunc]=='add_fee')
{
	$new_date = $_REQUEST[day_new_fee].'-'.$_REQUEST[month_new_fee].'-'.$_REQUEST[year_new_fee];
	if($new_date=='--')
		$new_date = '';
	if($_REQUEST['defined'])
	{
		$sql = "SELECT SYEAR,TITLE,AMOUNT,DUE_DATE,ACCOUNT_ID FROM STU_BILLING_DEFINED_FEES WHERE ID='$_REQUEST[defined]'";
		$defined = DBGet(DBQuery($sql));
		$defined = $defined[1];
		DBQuery("INSERT INTO STU_BILLING_FEES (ID,SYEAR,STUDENT_ID,TITLE,AMOUNT,DUE_DATE,EFFECTIVE_DATE,DEFINED_ID,ACCOUNT_ID) values(".db_seq_nextval('STU_BILLING_FEES_SEQ').",'$defined[SYEAR]','$student_id','$defined[TITLE]','$defined[AMOUNT]','$defined[DUE_DATE]','".DBDate()."','$_REQUEST[defined]','$defined[ACCOUNT_ID]')");
	}
	else
		DBQuery("INSERT INTO STU_BILLING_FEES (ID,SYEAR,STUDENT_ID,TITLE,AMOUNT,DUE_DATE,EFFECTIVE_DATE,ACCOUNT_ID) values(".db_seq_nextval('STU_BILLING_FEES_SEQ').",'$_REQUEST[f_year]','$student_id','$_REQUEST[new_fee_title]','".str_replace('$','',str_replace(',','',$_REQUEST[new_fee_amount]))."','$new_date','".DBDate()."','$_REQUEST[account_id]')");
	$note[] = 'That Fee has been Added';
	$_REQUEST[stumodfunc] = '';
}

if($_REQUEST[stumodfunc]=='add_payment')
{
	$new_date = $_REQUEST[day_new_payment].'-'.$_REQUEST[month_new_payment].'-'.$_REQUEST[year_new_payment];
	if($new_date=='--')
		$new_date = '';
	$_REQUEST[payment]['new'] = str_replace('$','',str_replace(',','',$_REQUEST[payment]['new']));
	if($_REQUEST['payment']['new'])
	{
		if($editor=='lunch')
			$lunch = 'Y';
		DBQuery("INSERT INTO STU_BILLING_ACT (ID,SYEAR,STUDENT_ID,AMOUNT,PAYMENT_DATE,LUNCH_PAYMENT,ACT_COMMENT,ACCOUNT_ID) values(".db_seq_nextval('STU_BILLING_ACT_SEQ').",'$_REQUEST[f_year]','$student_id','{$_REQUEST[payment]['new']}','".$new_date."','$lunch','$_REQUEST[act_comment]','$_REQUEST[account_id]')");
		$note[] = 'That Payment has been Added';
	}
	$_REQUEST[stumodfunc] = '';
}

if($_REQUEST[stumodfunc]=='delete_payment')
{
	if(DeletePrompt('payment'))
	{
		if($editor=='lunch')
		{
			$QI = DBQuery("SELECT AMOUNT FROM STU_BILLING_ACT WHERE ID='$_REQUEST[payment_id]' AND STUDENT_ID='$student_id'");
			$amount = DBGet($QI);
			$amount = $amount[1]['AMOUNT'];
		}	
		DBQuery("DELETE FROM STU_BILLING_ACT WHERE ID='$_REQUEST[payment_id]'");
		if($_REQUEST[payment_id]!=0)
			$note[] = 'That Payment has been Deleted';
		else
			$note[] = 'That payment could not be deleted since it is a previous amount.  Try deleting the previous transactions in the school year in which they were made.';
		$_REQUEST[stumodfunc] = '';
	}
}

if($_REQUEST[stumodfunc]=='delete_fee')
{
	if(DeletePrompt('fee'))
	{
		if($editor=='lunch')
		{
			$QI = DBQuery("SELECT AMOUNT FROM STU_BILLING_ACT_LUNCH WHERE ID='$_REQUEST[fee_id]' AND STUDENT_ID='$student_id'");
			$amount = DBGet($QI);
			$amount = $amount[1]['AMOUNT'];
			DBQuery("DELETE FROM STU_BILLING_ACT_LUNCH WHERE ID='$_REQUEST[fee_id]' AND STUDENT_ID='$student_id'");
		}
		else
		{
			DBQuery("DELETE FROM STU_BILLING_FEES WHERE ID='$_REQUEST[fee_id]' AND STUDENT_ID='$student_id'");
			DBQuery("DELETE FROM STU_BILLING_FEES WHERE FORGIVEN_FEE_ID='$_REQUEST[fee_id]' AND STUDENT_ID='$student_id'");
		}
		if($_REQUEST[fee_id]!=0)
			$note[] = 'That Fee has been Deleted';
		else
			$note[] = 'That fee could not be deleted since it is a previous amount.  Try deleting the previous transactions in the school year in which they were made.';
		$_REQUEST[stumodfunc] = '';
	}
}

if($_REQUEST[stumodfunc]=='forgive_fee')
{
	$sql = "SELECT FORGIVEN_FEE_ID FROM STU_BILLING_FEES WHERE STUDENT_ID='$student_id' AND FORGIVEN_FEE_ID='$_REQUEST[fee_id]'";
	$QI = DBQuery($sql);
	$forgiven_RET = DBGet($QI);
	if(!$forgiven_RET[1])
	{
		$sql = "SELECT TITLE,AMOUNT,ACCOUNT_ID FROM STU_BILLING_FEES WHERE ID='$_REQUEST[fee_id]'";
		$QI = DBQuery($sql);
		$RET = DBGet($QI);
	
		DBQuery("INSERT INTO STU_BILLING_FEES (ID,SYEAR,STUDENT_ID,EFFECTIVE_DATE,TITLE,AMOUNT,FORGIVEN_FEE_ID,ACCOUNT_ID) values(".db_seq_nextval('STU_BILLING_FEES_SEQ').",'$_REQUEST[f_year]','$student_id','".DBDate()."','Waived Fee: ".$RET[1][TITLE]." (".SessionUserID().")','".(0-$RET[1][AMOUNT])."','$_REQUEST[fee_id]','".$RET[1][ACCOUNT_ID]."')");
		$note[] = 'That Fee has been Waived';
	}
	else
		$note[] = 'That Fee has Already been Waived';
	$_REQUEST[stumodfunc] = '';
}

if($_REQUEST[stumodfunc]=='')
{
	if(!isset($_REQUEST['PDF']))
		echo '<CENTER>';
	if($note)
		ErrorMessage($note,'note');
	echo '<TABLE border=0 width=100%>';
	echo '<TR>';
	echo '<TD width=50% valign=top>';
	
	if($editor!='lunch' && !isset($_REQUEST['PDF']))
	{		
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&type=$_REQUEST[type]&f_school=$_REQUEST[f_school]&f_year=$_REQUEST[f_year]&f_stuid=$_REQUEST[f_stuid]&editor=$_REQUEST[editor]&stumodfunc=add_fee METHOD=POST>";
	
		$link[add][html][TITLE] =  '<INPUT type=text name=new_fee_title size=12>';
		$grade = DBGet(DBQuery("SELECT GRADE FROM STU_SCHOOL_MEETS WHERE STUDENT_ID='$student_id' AND SYEAR='$_REQUEST[f_year]'"));
		$grade = $grade[1]['GRADE'];
		$sql = "SELECT ID,TITLE,AMOUNT FROM STU_BILLING_DEFINED_FEES WHERE (SCHOOL='$_REQUEST[f_school]' OR SCHOOL IS NULL) AND (GRADE='$grade' OR GRADE IS NULL) AND (SYEAR='$_REQUEST[f_year]')";
		$QI = DBQuery($sql);
		$defined_RET = DBGet($QI);
		if(count($defined_RET))
		{
			$link[add][html][TITLE] .= "<BR><SELECT name=defined><OPTION value=''>Not Specified</OPTION>";
			foreach($defined_RET as $value)
				$link[add][html][TITLE] .= "<OPTION value=$value[ID]>$value[TITLE] - $value[AMOUNT]</OPTION>";
			$link[add][html][TITLE] .= "</SELECT>";
		}
		$link[add][html][AMOUNT] = '<INPUT type=text name=new_fee_amount size=12 value=$>';
		$link[add][html][SB_DATE] =  PrepareDate('_new_fee');
		$sql = "SELECT ID,TITLE FROM STU_BILLING_ACCOUNTS WHERE (SCHOOL='$_REQUEST[f_school]' OR SCHOOL IS NULL) AND (SYEAR='$_REQUEST[f_year]')";
		$QI = DBQuery($sql);
		$accounts_RET = DBGet($QI);
		if(count($accounts_RET))
		{
			$link[add][html][ACCOUNT_ID] .= "<BR><SELECT name=account_id><OPTION value=''>Not Specified</OPTION>";
			foreach($accounts_RET as $value)
				$link[add][html][ACCOUNT_ID] .= "<OPTION value=$value[ID]>$value[TITLE]</OPTION>";
			$link[add][html][ACCOUNT_ID] .= "</SELECT>";
		}

		$link[add][html][REMOVE] = button('add');
	}

	// PREVIOUS BALANCE
	if($editor=='lunch')
	{
		$sql = "SELECT 
				(
					SELECT ".db_case(array('sum(sbf.AMOUNT)',"''","'0'",'sum(sbf.AMOUNT)'))."
					FROM STU_BILLING_ACT_LUNCH sbf
					WHERE 
					(
						(sbf.SYEAR<'$_REQUEST[f_year]' OR sbf.SYEAR>'50') AND sbf.STUDENT_ID = '$student_id'
					)
				)
					-
				(SELECT ".db_case(array('sum(AMOUNT)',"''","'0'",'sum(AMOUNT)'))." FROM STU_BILLING_ACT WHERE LUNCH_PAYMENT='Y' AND (SYEAR<'$_REQUEST[f_year]' OR SYEAR>'50') AND STUDENT_ID='$student_id') AS PREVIOUS_BALANCE
			".FROM_DUAL;

		$QI = DBQuery($sql);
		$previous_balance = DBGet($QI);
		if($previous_balance[1][PREVIOUS_BALANCE]>0)
		{
			$previous_debit = "SELECT 'Previous Amount' as TITLE,0 AS ID,".($previous_balance[1][PREVIOUS_BALANCE])." as AMOUNT,'','' as PAYMENT_DATE ".FROM_DUAL." UNION ";
			$previous_credit = '';
		}
		elseif($previous_balance[1][PREVIOUS_BALANCE]<0)
		{
			$previous_credit = "SELECT 0 AS ID,'' AS PAYMENT_DATE,".(0-$previous_balance[1][PREVIOUS_BALANCE])." as AMOUNT,'Previous Amount' AS ACT_COMMENT ".FROM_DUAL." UNION ";			
			$previous_debit = '';
		}
		else
		{
			$previous_credit = '';
			$previous_debit = '';
		}
	}
	else
	{
		$sql = "SELECT 
				(
					SELECT ".db_case(array('sum(sbf.AMOUNT)',"''","'0'",'sum(sbf.AMOUNT)'))."
					FROM STU_BILLING_FEES sbf,".STU_SCHOOL_MEETS." ssm 
					WHERE 
						ssm.SYEAR<'$_REQUEST[f_year]' AND ".SSMSTUDENT_ID." = '$student_id'
						AND ssm.SYEAR=sbf.SYEAR
						AND ".SSMSTUDENT_ID."=sbf.STUDENT_ID
				)
				-
				(SELECT ".db_case(array('sum(AMOUNT)',"''","'0'",'sum(AMOUNT)'))." FROM STU_BILLING_ACT 
				WHERE (SYEAR<'$_REQUEST[f_year]' OR SYEAR>'50') AND STUDENT_ID='$student_id') AS PREVIOUS_BALANCE
			".FROM_DUAL;
	
		$QI = DBQuery($sql);
		$previous_balance = DBGet($QI);
		if($previous_balance[1][PREVIOUS_BALANCE]>0)
		{
			$previous_debit = "SELECT 0 AS FORGIVEN_FEE_ID,0 AS ACCOUNT_ID,'Previous Amount' as TITLE,0 AS ID,".$previous_balance[1][PREVIOUS_BALANCE]." as AMOUNT,'' AS SB_DATE,'' AS STUDENT_ID,'' AS REMOVE ".FROM_DUAL." UNION ";
			$previous_credit = '';
		}
		elseif($previous_balance[1][PREVIOUS_BALANCE]<0)
		{
			$previous_credit = "SELECT 0 AS ACCOUNT_ID,0 AS ID,'' AS PAYMENT_DATE,".(0-$previous_balance[1][PREVIOUS_BALANCE])." as AMOUNT,'Previous Amount' AS ACT_COMMENT ".FROM_DUAL." UNION ";			
			$previous_debit = '';
		}
		else
		{
			$previous_credit = '';
			$previous_debit = '';
		}
	}
			
	// DEBITS
	if($editor=='lunch')
	{
		$sql = "$previous_debit
				SELECT sbf.TITLE,sbf.ID,sbf.AMOUNT,cast('' as varchar(1)) as REMOVE,to_char(PAYMENT_DATE,'dd-MON-yy') as SB_DATE
				FROM STU_BILLING_ACT_LUNCH sbf
				WHERE sbf.SYEAR='$_REQUEST[f_year]' AND sbf.STUDENT_ID = '$student_id'
				ORDER BY PAYMENT_DATE ASC";
	}
	else
	{
		$sql = "$previous_debit
					SELECT sbf.FORGIVEN_FEE_ID,sbf.ACCOUNT_ID,sbf.TITLE,sbf.ID,sbf.AMOUNT,to_char(sbf.DUE_DATE,'dd-MON-yy') as SB_DATE,sbf.STUDENT_ID,
					cast('' as varchar(1)) as REMOVE
					FROM STU_BILLING_FEES sbf,".STU_SCHOOL_MEETS." ssm 
					WHERE 
						ssm.SYEAR='$_REQUEST[f_year]' AND ".SSMSTUDENT_ID." = '$student_id'
						AND ssm.SYEAR=sbf.SYEAR
						AND ".SSMSTUDENT_ID."=sbf.STUDENT_ID
					ORDER BY SB_DATE DESC
				";
	}

	$QI = DBQuery($sql);
	$functions = array('REMOVE'=>'makeRemoveInput','AMOUNT'=>'countFeesCurrency','SB_DATE'=>'ProperDate');
	if($editor!='lunch')
		$functions['ACCOUNT_ID'] = 'getAccount';
	$fees_RET = DBGet($QI,$functions);

	if($editor=='lunch')
	{
		$singular = 'Lunch Purchase';
		$plural = 'Lunch Purchases';
		$columns = array('REMOVE'=>'','TITLE'=>'Fee','AMOUNT'=>'Amount','SB_DATE'=>'Payment Date');	
		if(isset($_REQUEST['PDF']))
			unset($columns['REMOVE']);
	}
	else
	{
		$singular = 'Fee';
		$plural = 'Fees';
		$columns = array('REMOVE'=>'','TITLE'=>'Description','AMOUNT'=>'Amount','SB_DATE'=>'Due Date','ACCOUNT_ID'=>'Account');
		if(isset($_REQUEST['PDF']))
			unset($columns['REMOVE']);	
	}
	$options = array('print'=>false);
	if(isset($_REQUEST['PDF']))
		$options['center'] = false;

	ListOutput($fees_RET,$columns,$singular,$plural,$link,array(),$options);
	unset($link);
	if($editor!='lunch' && !isset($_REQUEST['PDF']))
		echo '<center><INPUT type=submit value="Add Fee"></center>';
	
	if(!isset($_REQUEST['PDF']))
		echo '</FORM>';
	
	echo '<BR>';
		
	// CREDITS
	$functions = array('PAYMENT_DATE'=>'ProperDate','AMOUNT'=>'countPaymentsCurrency');
	if($editor!='lunch')
		$functions['ACCOUNT_ID'] = 'getAccount';
		
	$sql = "$previous_credit
			SELECT sba.ACCOUNT_ID,sba.ID,to_char(sba.PAYMENT_DATE,'dd-MON-yy') as PAYMENT_DATE,sba.AMOUNT,
						sba.ACT_COMMENT
				FROM STU_BILLING_ACT sba 
				WHERE sba.STUDENT_ID = '$student_id' AND sba.SYEAR='$_REQUEST[f_year]'";
	if($editor=='lunch')
		$sql .= " AND sba.LUNCH_PAYMENT='Y'";
	else
		$sql .= " AND (sba.LUNCH_PAYMENT!='Y' OR sba.LUNCH_PAYMENT IS NULL)";
	$QI = DBQuery($sql);
	$payments_RET = DBGet($QI,$functions);
	
	$columns = array('ACT_COMMENT'=>'Reference','AMOUNT'=>'Amount','PAYMENT_DATE'=>'Date','ACCOUNT_ID'=>'Account');
		
	if(!isset($_REQUEST['PDF']))
	{
		$link[remove] = array('link'=>"Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&type=$_REQUEST[type]&f_stuid=$_REQUEST[f_stuid]&f_school=$_REQUEST[f_school]&f_year=$_REQUEST[f_year]&editor=$_REQUEST[editor]&stumodfunc=delete_payment",'variables'=>array('payment_id'=>'ID'));
		$link[add][html][PAYMENT_DATE] = PrepareDate('_new_payment');
		$link[add][html][AMOUNT] = '<INPUT type=text name=payment[new] value=$ size=12>';
		$link[add][html][ACT_COMMENT] = '<INPUT type=text name=act_comment size=12 maxlength=255>';
		$link[add][html][remove] = button('add');
	
		if($editor!='lunch')
		{	
			$sql = "SELECT ID,TITLE FROM STU_BILLING_ACCOUNTS WHERE (SCHOOL='$_REQUEST[f_school]' OR SCHOOL IS NULL) AND (SYEAR='$_REQUEST[f_year]')";
			$QI = DBQuery($sql);
			$accounts_RET = DBGet($QI);
			if(count($accounts_RET))
			{
				$link[add][html][ACCOUNT_ID] .= "<BR><SELECT name=account_id><OPTION value=''>Not Specified</OPTION>";
				foreach($accounts_RET as $value)
					$link[add][html][ACCOUNT_ID] .= "<OPTION value=$value[ID]>$value[TITLE]</OPTION>";
				$link[add][html][ACCOUNT_ID] .= "</SELECT>";
			}
		}
	
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&type=$_REQUEST[type]&f_stuid=$_REQUEST[f_stuid]&f_school=$_REQUEST[f_school]&f_year=$_REQUEST[f_year]&editor=$_REQUEST[editor]&stumodfunc=add_payment METHOD=POST>";
	}
	if($editor=='lunch')
	{
		$singular = 'Lunch Payment';
		$plural = 'Lunch Payments';
	}
	else
	{
		$singular = 'Payment';
		$plural = 'Payments';
	}

	$options = array('print'=>false);
	if(isset($_REQUEST['PDF']))
		$options['center'] = false;

	ListOutput($payments_RET,$columns,$singular,$plural,$link,array(),$options);
	if(!isset($_REQUEST['PDF']))
	{
		echo '<center><INPUT type=submit value="Add Payment"></center>';
		echo '</FORM>';
	}
	
	echo '</TD>';
	echo '</TR>';
	echo '</TABLE>';
	
	echo '<BR><B>Balance:</B> ';
	if($balance>0)
		echo '<font color=red>';
	echo Currency(0-$balance);
	if($balance>0)
		echo '</font>';
	$balance = 0;

	if(!isset($_REQUEST['PDF']))
		echo '</CENTER>';
}

?>
