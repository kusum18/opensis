<?php

StaffWidgets('fsa_barcode');
StaffWidgets('fsa_exists_Y');

Search('staff_id',$extra);

if ($_REQUEST['modfunc']=='modify') {
	if (UserStaffID() && AllowEdit()) {
		if ($_REQUEST['submit']['cancel']) {
			if (DeletePromptX('Sale','Cancel'))
				unset($_SESSION['SALE']);
			unset($_REQUEST['submit']);
		}
		elseif ($_REQUEST['submit']['complete']) {
			if (count($_SESSION['SALE'])) {
				// get next transaction id
				$id = DBGet(DBQuery('SELECT '.db_seq_nextval('FOOD_SERVICE_STAFF_TRANSACTIONS_SEQ').' AS SEQ_ID '.FROM_DUAL));
				$id = $id[1]['SEQ_ID'];

				foreach($_SESSION['SALE'] as $key=>$item) {
					$sql = 'INSERT INTO FOOD_SERVICE_STAFF_TRANSACTION_ITEMS';
					$fields = 'ITEM_ID,TRANSACTION_ID,AMOUNT,SHORT_NAME,DESCRIPTION';
					$values = $key.','.$id.',-'.$item['PRICE'].',\''.$item['SHORT_NAME'].'\',\''.$item['DESCRIPTION'].'\'';
					$sql = 'INSERT INTO FOOD_SERVICE_STAFF_TRANSACTION_ITEMS ('.$fields.') values ('.$values.')';
					DBQuery($sql);
				}

				$sql1 = 'UPDATE FOOD_SERVICE_STAFF_ACCOUNTS SET TRANSACTION_ID='.$id.',BALANCE=BALANCE+(SELECT sum(AMOUNT) FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID='.$id.') WHERE STAFF_ID='.UserStaffID();
				$fields = 'TRANSACTION_ID,STAFF_ID,SYEAR,BALANCE,TIMESTAMP,SHORT_NAME,DESCRIPTION,SELLER_ID';
				$values = $id.','.UserStaffID().','.UserSyear().',(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID='.UserStaffID().'),CURRENT_TIMESTAMP,\'LUNCH\',\'Lunch\','.User('STAFF_ID');
				$sql2 = 'INSERT INTO FOOD_SERVICE_STAFF_TRANSACTIONS ('.$fields.') values ('.$values.')';
				DBQuery('BEGIN; '.$sql1.'; '.$sql2.'; COMMIT');

				unset($_SESSION['SALE']);
			}
			unset($_REQUEST['modfunc']);
		}
		else
			unset($_REQUEST['modfunc']);
	}
	else
		unset($_REQUEST['modfunc']);
}

if ($_REQUEST['modfunc']=='add') {
	if (UserStaffID()  && AllowEdit() && $_REQUEST['new_item'] && $_POST['new_item']) {
		// get info for transcation item
		$item = DBGET(DBQuery('SELECT * FROM FOOD_SERVICE_MENU_ITEMS WHERE SHORT_NAME=\''.$_REQUEST['new_item'].'\''));
		$item = $item[1];

		$price = $item['PRICE_STAFF'];

		$_SESSION['SALE'][]=array('SHORT_NAME'=>$item['SHORT_NAME'],'DESCRIPTION'=>$item['DESCRIPTION'],'PRICE'=>$price);
	}
	unset($_REQUEST['modfunc']);
}

if($_REQUEST['modfunc']=='remove') {
	if (AllowEdit())
		unset($_SESSION['SALE'][$_REQUEST['id']]);
	unset($_REQUEST['modfunc']);
}

if(UserStaffID() && !$_REQUEST['modfunc']) {
	$staff = DBGet(DBQuery('SELECT STAFF_ID,FIRST_NAME,LAST_NAME,MIDDLE_NAME FROM STAFF WHERE STAFF_ID='.UserStaffID()));
	$staff = DBGet(DBQuery('SELECT s.STAFF_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,(SELECT BALANCE FROM FOOD_SERVICE_STAFF_ACCOUNTS WHERE STAFF_ID=s.STAFF_ID) AS BALANCE FROM STAFF s WHERE s.STAFF_ID='.UserStaffID()));
	$staff = $staff[1];

	$PHP_tmp_SELF = PreparePHP_SELF();
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=modify METHOD=POST>";
	DrawHeader('',SubmitButton('Cancel Sale','submit[cancel]').SubmitButton('Complete Sale','submit[complete]'));
	echo '</FORM>';

	echo '<TABLE width=100%><TR>';

	echo '<TD valign=top>';
	echo $staff['FIRST_NAME'].' '.$staff['MIDDLE_NAME'].' '.$staff['LAST_NAME'].'<BR>';
	echo '<b><small>'.$staff['STAFF_ID'].'</small></b>';
	echo '</TD>';

	echo '<TD valign=top>'.red($staff['BALANCE']).'<BR><small>Balance</small></TD>';

	echo '</TR></TABLE>';
	echo '<HR>';

	if ($staff['BALANCE']) {

		echo '<TABLE border=0 width=100%>';
		echo '<TR><TD width=100% valign=top>';

		$RET = DBGet(DBQuery('SELECT fsti.DESCRIPTION,fsti.AMOUNT FROM FOOD_SERVICE_STAFF_TRANSACTIONS fst,FOOD_SERVICE_STAFF_TRANSACTION_ITEMS fsti WHERE fst.STAFF_ID='.UserStaffID().' AND fst.SYEAR='.UserSyear().' AND fst.DESCRIPTION=\'Lunch\' AND fst.TIMESTAMP BETWEEN CURRENT_DATE AND \'tomorrow\' AND fsti.TRANSACTION_ID=fst.TRANSACTION_ID'));

		$columns = array('DESCRIPTION'=>'Item','AMOUNT'=>'Amount');
		ListOutput($RET,$columns,'Earlier Sale','Earlier Sales',$link,false,array('save'=>false,'search'=>false));

		// IMAGE
		//if ($file = @fopen($StaffPicturesPath.'/'.UserStaffID().'.jpg','r')) {
		//fclose($file);
		//echo '<TD rowspan=2 width=150 align=left valign=top><IMG SRC="'.$StaffPicturesPath.'/'.UserStaffID().'.jpg" width=150></TD>';
		//}

		echo '</TD></TR>';
		echo '<TR><TD width=100% valign=top>';

		$RET = array(array());
		foreach($_SESSION['SALE'] as $key=>$value)
			$RET[]=array('AMOUNT'=>$value['PRICE'],'DESCRIPTION'=>$value['DESCRIPTION'],'SALE_ID'=>$key);
		unset($RET[0]);

		$items_RET = DBGet(DBQuery('SELECT * FROM FOOD_SERVICE_MENU_ITEMS WHERE SCHOOL_ID=\''.UserSchool().'\' AND PRICE_STAFF IS NOT NULL ORDER BY SORT_ORDER'));
		if(count($items_RET)) {
			foreach($items_RET as $value)
				$items[$value['SHORT_NAME']] = $value['DESCRIPTION'];
		}

		$link['remove'] = array('link'=>"Modules.php?modname=$_REQUEST[modname]&modfunc=remove",
					'variables'=>array('id'=>'SALE_ID'));
		$link['add']['html'] = array('DESCRIPTION'=>'<TABLE border=0 cellpadding=0 cellspacing=0><TR><TD>'.SelectInput('','new_item','',$items).'</TD></TR></TABLE>',
					'AMOUNT'=>'<TABLE border=0 cellpadding=0 cellspacing=0><TR><TD><INPUT type=submit value=Add></TD></TR></TABLE>',
					'remove'=>button('add'));
		$columns = array('DESCRIPTION'=>'Item','AMOUNT'=>'Amount');

		echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=add method=POST>";
		ListOutput($RET,$columns,'Item','Items',$link,false,array('save'=>false,'search'=>false));
		echo '</FORM>';

		echo '</TD></TR></TABLE>';
	}
	else
		echo ErrorMessage(array('<IMG SRC=assets/x.gif align=absmiddle> This user does not have a Meal Account.'));
}
?>
