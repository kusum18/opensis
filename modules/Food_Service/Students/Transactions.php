<?php

if($_REQUEST['values'] && $_POST['values'] && $_REQUEST['save'])
{
	if(UserStudentID() && AllowEdit())
	{
		if(($_REQUEST['values']['TYPE']=='Deposit' || $_REQUEST['values']['TYPE']=='Credit' || $_REQUEST['values']['TYPE']=='Debit') && ($amount = is_money($_REQUEST['values']['AMOUNT'])))
		{
			$account_id = DBGet(DBQuery("SELECT ACCOUNT_ID FROM FOOD_SERVICE_STUDENT_ACCOUNTS WHERE STUDENT_ID='".UserStudentID()."'"));
			$account_id = $account_id[1]['ACCOUNT_ID'];

			// get next transaction id
			$id = DBGet(DBQuery("SELECT ".db_seq_nextval('FOOD_SERVICE_TRANSACTIONS_SEQ')." AS SEQ_ID ".FROM_DUAL));
			$id = $id[1]['SEQ_ID'];

			$fields = 'ITEM_ID,TRANSACTION_ID,AMOUNT,DISCOUNT,SHORT_NAME,DESCRIPTION';
			$values = "'0','".$id."','".($_REQUEST['values']['TYPE']=='Debit' ? -$amount : $amount)."',NULL,'".strtoupper($_REQUEST['values']['OPTION'])."','".$_REQUEST['values']['OPTION'].' '.$_REQUEST['values']['DESCRIPTION']."'";
			$sql = "INSERT INTO FOOD_SERVICE_TRANSACTION_ITEMS (".$fields.") values (".$values.")";
			DBQuery($sql);

			$sql1 = "SELECT @amt:=SUM(amount) FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID='".$id."'";
			$sql2 = "UPDATE FOOD_SERVICE_ACCOUNTS SET TRANSACTION_ID='".$id."',BALANCE=BALANCE+@amt WHERE ACCOUNT_ID='".$account_id."'";
			$fields = 'TRANSACTION_ID,SYEAR,SCHOOL_ID,ACCOUNT_ID,BALANCE,TIMESTAMP,SHORT_NAME,DESCRIPTION,SELLER_ID';
			$values = "'".$id."','".UserSyear()."','".UserSchool()."','".$account_id."',(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID='".$account_id."'),CURRENT_TIMESTAMP,'".strtoupper($_REQUEST['values']['TYPE'])."','".$_REQUEST['values']['TYPE']."','".User('STAFF_ID')."'";
			$sql3 = "INSERT INTO FOOD_SERVICE_TRANSACTIONS (".$fields.") values (".$values.")";
			DBQuery('BEGIN; '.$sql1.'; '.$sql2.'; '.$sql3.'; COMMIT');
		}
		else
			$error = ErrorMessage(array('Please enter valid Type and Amount.'));
	}
	unset($_REQUEST['modfunc']);
}

if($_REQUEST['cancel'])
{
	unset($_REQUEST['modfunc']);
}

Widgets('fsa_discount');
Widgets('fsa_status');
Widgets('fsa_barcode');
Widgets('fsa_account_id');

$extra['SELECT'] .= ",coalesce(fssa.STATUS,'Active') AS STATUS";
$extra['SELECT'] .= ",(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fssa.ACCOUNT_ID) AS BALANCE";
if(!strpos($extra['FROM'],'fssa'))
{
	$extra['FROM'] .= ",FOOD_SERVICE_STUDENT_ACCOUNTS fssa";
	$extra['WHERE'] .= " AND fssa.STUDENT_ID=s.STUDENT_ID";
}
$extra['functions'] += array('BALANCE'=>'red');
$extra['columns_after'] = array('BALANCE'=>'Balance','STATUS'=>'Status');

Search('student_id',$extra);

if(UserStudentID() && !$_REQUEST['modfunc'])
{
	$student = DBGet(DBQuery("SELECT s.STUDENT_ID,CONCAT(".(Preferences('NAME')=='Common'?'coalesce(s.CUSTOM_200000002,s.FIRST_NAME)':'s.FIRST_NAME').",' ',s.LAST_NAME) AS FULL_NAME,fsa.ACCOUNT_ID,fsa.STATUS,(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fsa.ACCOUNT_ID) AS BALANCE FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fsa WHERE s.STUDENT_ID='".UserStudentID()."' AND fsa.STUDENT_ID=s.STUDENT_ID"));
	$student = $student[1];

	//$PHP_tmp_SELF = PreparePHP_SELF();
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc= method=POST>";

	DrawHeader('',SubmitButton('Cancel','cancel').SubmitButton('Save','save'));

	echo '<TABLE width=100%><TR>';

	echo '<TD valign=top>'.NoInput($student['FULL_NAME'],$student['STUDENT_ID']).'</TD>';
	echo '<TD valign=top>'.NoInput(red($student['BALANCE']),'Balance').'</TD>';

	echo '</TR></TABLE>';
	echo '<HR>';

	if($error) echo $error;

	if($student['BALANCE'])
	{
		$RET = DBGet(DBQuery("SELECT fst.TRANSACTION_ID,fst.DESCRIPTION AS TYPE,fsti.DESCRIPTION,fsti.AMOUNT FROM FOOD_SERVICE_TRANSACTIONS fst,FOOD_SERVICE_TRANSACTION_ITEMS fsti WHERE fst.SYEAR='".UserSyear()."' AND fst.ACCOUNT_ID='".$student['ACCOUNT_ID']."' AND DATEDIFF(fst.TIMESTAMP,CURRENT_DATE)=0 AND fsti.TRANSACTION_ID=fst.TRANSACTION_ID"));

		echo '<TABLE border=0 width=100%><TR><TD width=100% valign=top>';

		if(AllowEdit())
		{
			$types = array('Deposit'=>'Deposit','Credit'=>'Credit','Debit'=>'Debit');
			$link['add']['html']['TYPE'] = SelectInput('','values[TYPE]','',$types,false);
			$options = array('Cash'=>'Cash','Check'=>'Check','Credit Card'=>'Credit Card','Debit Card'=>'Debit Card','Transfer'=>'Transfer');
			$link['add']['html']['DESCRIPTION'] = SelectInput('','values[OPTION]','',$options).' '.TextInput('','values[DESCRIPTION]','','size=20 maxlength=50');
			$link['add']['html']['AMOUNT'] = TextInput('','values[AMOUNT]','','size=5 maxlength=10');
			$link['add']['html']['remove'] = button('add');
			$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=delete";
			$link['remove']['variables'] = array('id'=>'TRANSACTION_ID');
		}

		$columns = array('TYPE'=>'Type','DESCRIPTION'=>'Description','AMOUNT'=>'Amount');

		ListOutput($RET,$columns,'Earlier Transaction','Earlier Transactions',$link,false,array('save'=>false,'search'=>false));
		echo '<CENTER>'.SubmitButton('Save','save').'</CENTER>';

		echo '</TD></TR></TABLE>';
	}
	else
		echo ErrorMessage(array('<IMG SRC=assets/x.gif align=absmiddle> This student does not have a valid Meal Account.'));
	echo '</FORM>';
}
?>
