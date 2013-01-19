<?php

if($_REQUEST['values'] && $_POST['values'] && $_REQUEST['save'])
{
	if(UserStaffID() && AllowEdit())
	{
		//$existing_account = DBGet(DBQuery('SELECT \'exists\' FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID='.UserStaffID()));
		//if(!count($existing_account))
		//	BackPrompt('That user does not have a Meal Account. Choose a different username and try again.');
		if(($_REQUEST['values']['TYPE']=='Deposit' || $_REQUEST['values']['TYPE']=='Credit' || $_REQUEST['values']['TYPE']=='Debit') && ($amount = is_money($_REQUEST['values']['AMOUNT'])))
		{
			// get next transaction id
			$id = DBGet(DBQuery("SELECT ".db_seq_nextval('FOOD_SERVICE_STAFF_TRANSACTIONS_SEQ')." AS SEQ_ID ".FROM_DUAL));
			$id = $id[1]['SEQ_ID'];

			$fields = 'ITEM_ID,TRANSACTION_ID,AMOUNT,SHORT_NAME,DESCRIPTION';
			$values = "'0','".$id."','".($_REQUEST['values']['TYPE']=='Debit' ? -$amount : $amount)."','".strtoupper($_REQUEST['values']['OPTION'])."','".$_REQUEST['values']['OPTION'].' '.$_REQUEST['values']['DESCRIPTION']."'";
			$sql = "INSERT INTO FOOD_SERVICE_STAFF_TRANSACTION_ITEMS (".$fields.") values (".$values.")";
			DBQuery($sql);

			$sql1 = "UPDATE FOOD_SERVICE_STAFF_ACCOUNTS SET TRANSACTION_ID='".$id."',BALANCE=BALANCE+(SELECT sum(AMOUNT) FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID='".$id."') WHERE STAFF_ID='".UserStaffID()."'";
			$fields = 'TRANSACTION_ID,SYEAR,SCHOOL_ID,STAFF_ID,BALANCE,TIMESTAMP,SHORT_NAME,DESCRIPTION,SELLER_ID';
			$values = "'".$id."','".UserSyear()."','".UserSchool()."','".UserStaffID()."',(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID='".UserStaffID()."'),CURRENT_TIMESTAMP,'".strtoupper($_REQUEST['values']['TYPE'])."','".$_REQUEST['values']['TYPE']."','".User('STAFF_ID')."'";
			$sql2 = "INSERT INTO FOOD_SERVICE_STAFF_TRANSACTIONS (".$fields.") values (".$values.")";
			DBQuery('BEGIN; '.$sql1.'; '.$sql2.'; COMMIT');
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

StaffWidgets('fsa_status');
StaffWidgets('fsa_barcode');
StaffWidgets('fsa_exists_Y');

$extra['SELECT'] .= ",(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE";
$extra['SELECT'] .= ",(SELECT STATUS FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS STATUS";
$extra['functions'] += array('BALANCE'=>'red');
$extra['columns_after'] = array('BALANCE'=>'Balance','STATUS'=>'Status');

Search('staff_id',$extra);

if(UserStaffID() && !$_REQUEST['modfunc'])
{
	$staff = DBGet(DBQuery("SELECT s.STAFF_ID,s.FIRST_NAME||' '||s.LAST_NAME AS FULL_NAME,(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE FROM STAFF s WHERE s.STAFF_ID='".UserStaffID()."'"));
	$staff = $staff[1];

	//$PHP_tmp_SELF = PreparePHP_SELF();
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc= method=POST>";

	DrawHeader('',SubmitButton('Cancel','cancel').SubmitButton('Save','save'));

	echo '<TABLE width=100%><TR>';

	echo '<TD valign=top>'.NoInput($staff['FULL_NAME'],$staff['STAFF_ID']).'</TD>';
	echo '<TD valign=top>'.NoInput(red($staff['BALANCE']),'Balance').'</TD>';

	echo '</TR></TABLE>';
	echo '<HR>';

	if($error) echo $error;

	if($staff['BALANCE']!='')
	{
		$RET = DBGet(DBQuery("SELECT fst.TRANSACTION_ID,fst.DESCRIPTION AS TYPE,fsti.DESCRIPTION,fsti.AMOUNT FROM FOOD_SERVICE_STAFF_TRANSACTIONS fst,FOOD_SERVICE_STAFF_TRANSACTION_ITEMS fsti WHERE fst.SYEAR='".UserSyear()."' AND fst.STAFF_ID='".UserStaffID()."' AND fst.TIMESTAMP BETWEEN CURRENT_DATE AND CURRENT_DATE+1 AND fsti.TRANSACTION_ID=fst.TRANSACTION_ID"));

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
		echo ErrorMessage(array('<IMG SRC=assets/x.gif align=absmiddle> This user does not have a Meal Account.'));
	echo '</FORM>';
}
?>
