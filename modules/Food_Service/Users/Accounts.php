<?php

if($_REQUEST['modfunc']=='update')
{
	if(UserStaffID() && AllowEdit())
	{
		if($_REQUEST['submit']['delete'])
		{
			if(DeletePromptX('User Account'))
				DBQuery('DELETE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID='.UserStaffID());
			//unset($_REQUEST['submit']);
		}
		else
		{
			if(count($_REQUEST['food_service']))
			{
				$sql = 'UPDATE FOOD_SERVICE_STAFF_ACCOUNTS SET ';
				foreach($_REQUEST['food_service'] as $column_name=>$value)
					$sql .= $column_name.'=\''.str_replace("\'","''",str_replace("`","''",trim($value))).'\',';
				$sql = substr($sql,0,-1).' WHERE STAFF_ID='.$_REQUEST['staff_id'];
				DBQuery($sql);
			}
			unset($_REQUEST['modfunc']);
		}
	}
	else
		unset($_REQUEST['modfunc']);
}

if($_REQUEST['modfunc']=='create')
{
	if(UserStaffID() && AllowEdit())
	{
		if(count($_REQUEST['food_service']))
		{
			$fields = 'STAFF_ID,BALANCE,TRANSACTION_ID,';
			$values = UserStaffID().',0.00,0,';
			foreach($_REQUEST['food_service'] as $column_name=>$value)
			{
				$fields .= $column_name.',';
				$values .= '\''.str_replace("\'","''",str_replace("`","''",$value)).'\',';
			}
			$sql = 'INSERT INTO FOOD_SERVICE_STAFF_ACCOUNTS ('.substr($fields,0,-1).') values ('.substr($values,0,-1).')';
			DBQuery($sql);
		}
	}
	unset($_REQUEST['modfunc']);
}

StaffWidgets('fsa_status');
StaffWidgets('fsa_barcode');
StaffWidgets('fsa_exists_Y');

$extra['SELECT'] .= ',(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE';
$extra['SELECT'] .= ',(SELECT coalesce(STATUS,\'Active\') FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS STATUS';
$extra['functions'] += array('BALANCE'=>'red');
$extra['columns_after'] = array('BALANCE'=>'Balance','STATUS'=>'Status');

Search('staff_id',$extra);

if(!$_REQUEST['modfunc'] && UserStaffID())
{
	$staff = DBGet(DBQuery('SELECT s.STAFF_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,(SELECT STATUS FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS STATUS,(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE,(SELECT BARCODE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BARCODE FROM STAFF s WHERE s.STAFF_ID='.UserStaffID()));
	$staff = $staff[1];

	if($staff['BALANCE'])
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&staff_id=".UserStaffID()."&modfunc=update method=POST>";
		DrawHeader('',SubmitButton('Save','submit[save]').($staff['BALANCE'] == 0 ? SubmitButton('Delete Account','submit[delete]') : ''));
	}
	else
	{
		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&staff_id=".UserStaffID()."&modfunc=create method=POST>";
		DrawHeader('',SubmitButton('Create Account'));
	}

	echo '<BR>';
	PopTable('header','Account Information','width=100%');
        echo '<TABLE width=100%>';
        echo '<TR>';
        echo '<TD valign=top>';
        echo '<TABLE width=100%><TR>';

	echo '<TD valign=top>';
        echo $staff['FIRST_NAME'].' '.$staff['MIDDLE_NAME'].' '.$staff['LAST_NAME'].'<BR>';
        echo '<b><small>'.$staff['STAFF_ID'].'</small></b>';
	if(!$staff['BALANCE'])
	{
		$warning = 'This user does not have a Meal Account.';
		echo '<BR>'.button('warning','','# onMouseOver=\'stm(["Warning","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
	}
	echo '</TD>';

        echo '<TD valign=top>'.red($staff['BALANCE']).'<BR><small>Balance</small></TD>';

        echo '</TR></TABLE>';
        echo '</TD></TR></TABLE>';
        echo '<HR>';

	echo '<TABLE width=100% border=0 cellpadding=0 cellspacing=0>';
	echo '<TR><TD valign=top>';

	echo '<TABLE border=0 cellpadding=6 width=100%>';
	echo '<TR>';
	echo '<TD>';
	$options = array('Inactive'=>'Inactive','Disabled'=>'Disabled','Closed'=>'Closed');
	echo SelectInput($staff['STATUS'],'food_service[STATUS]','Status',$options,'Active');
	echo '</TD>';
	echo '<TD>';
	echo TextInput($staff['BARCODE'],'food_service[BARCODE]','Barcode','size=12 maxlength=25');
	echo '</TD>';
	echo '</TR>';
	echo '</TABLE>';

	echo '</TD></TR>';
	echo '</TABLE>';
	PopTable('footer');
	echo '<CENTER>'.SubmitButton('Save').'</CENTER>';
	echo '</FORM>';
}
?>
