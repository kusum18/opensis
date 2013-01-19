<?php

Widgets('fsa_status');
Widgets('fsa_barcode');

Search('student_id',$extra);

if ($_REQUEST['modfunc']=='modify') {
	if (UserStudentID() && AllowEdit()) {
		if ($_REQUEST['submit']['cancel']) {
			if (DeletePromptX('Sale','Cancel'))
				unset($_SESSION['SALE']);
			unset($_REQUEST['submit']);
		}
		elseif ($_REQUEST['submit']['complete']) {
			if (count($_SESSION['SALE'])) {
				//$discount = '(SELECT DISCOUNT FROM FOOD_SERVICE_STUDENT_ACCOUNTS WHERE STUDENT_ID='.UserStudentID().')';
				//$account_id = '(SELECT ACCOUNT_ID FROM FOOD_SERVICE_STUDENT_ACCOUNTS WHERE STUDENT_ID='.UserStudentID().')';
				$student = DBGet(DBQuery('SELECT DISCOUNT,ACCOUNT_ID FROM FOOD_SERVICE_STUDENT_ACCOUNTS WHERE STUDENT_ID='.UserStudentID()));
				$discount = $student[1]['DISCOUNT'];
				$account_id = $student[1]['ACCOUNT_ID'];

				// get next transaction id
				$id = DBGet(DBQuery('SELECT '.db_seq_nextval('FOOD_SERVICE_TRANSACTIONS_SEQ').' AS SEQ_ID '.FROM_DUAL));
				$id = $id[1]['SEQ_ID'];

				foreach($_SESSION['SALE'] as $key=>$item) {
					$sql = 'INSERT INTO FOOD_SERVICE_TRANSACTION_ITEMS';
					$fields = 'ITEM_ID,TRANSACTION_ID,AMOUNT,DISCOUNT,SHORT_NAME,DESCRIPTION';
					$values = $key.','.$id.',-'.$item['PRICE'].',\''.$item['DISCOUNT'].'\',\''.$item['SHORT_NAME'].'\',\''.$item['DESCRIPTION'].'\'';
					$sql = 'INSERT INTO FOOD_SERVICE_TRANSACTION_ITEMS ('.$fields.') values ('.$values.')';
					DBQuery($sql);
				}

				$sql1 = 'UPDATE FOOD_SERVICE_ACCOUNTS SET TRANSACTION_ID='.$id.',BALANCE=BALANCE+(SELECT sum(AMOUNT) FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID='.$id.') WHERE ACCOUNT_ID='.$account_id;
				$fields = 'TRANSACTION_ID,ACCOUNT_ID,STUDENT_ID,SYEAR,DISCOUNT,BALANCE,TIMESTAMP,SHORT_NAME,DESCRIPTION,SELLER_ID';
				$values = $id.','.$account_id.','.UserStudentID().','.UserSyear().',\''.$discount.'\',(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID='.$account_id.'),CURRENT_TIMESTAMP,\'LUNCH\',\'Lunch\','.User('STAFF_ID');
				$sql2 = 'INSERT INTO FOOD_SERVICE_TRANSACTIONS ('.$fields.') values ('.$values.')';
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
	if (UserStudentID()  && AllowEdit() && $_REQUEST['new_item'] && $_POST['new_item']) {
		// get student lunch discount information
		$student = DBGet(DBQuery('SELECT DISCOUNT FROM FOOD_SERVICE_STUDENT_ACCOUNTS WHERE STUDENT_ID='.UserStudentID()));
		$discount = $student[1]['DISCOUNT'];

		// get info for transcation item
		$item = DBGET(DBQuery('SELECT * FROM FOOD_SERVICE_MENU_ITEMS WHERE SHORT_NAME=\''.$_REQUEST['new_item'].'\''));
		$item = $item[1];

		// determine price based on discount
		switch ($discount) {
			case 'Reduced':
				if (is_null($price = $item['PRICE_REDUCED'])) {
					$price = $item['PRICE'];
					$discount = '';
				}
				break;
			case 'Free':
				if (is_null($price = $item['PRICE_FREE'])) {
					$price = $item['PRICE'];
					$discount = '';
				}
				break;
			default:
				$price = $item['PRICE'];
				$discount = '';
		}
		$_SESSION['SALE'][]=array('SHORT_NAME'=>$item['SHORT_NAME'],'DESCRIPTION'=>$item['DESCRIPTION'],'PRICE'=>$price,'DISCOUNT'=>$discount);
	}
	unset($_REQUEST['modfunc']);
}

if($_REQUEST['modfunc']=='remove') {
	if (AllowEdit())
		unset($_SESSION['SALE'][$_REQUEST['id']]);
	unset($_REQUEST['modfunc']);
}

if(UserStudentID() && !$_REQUEST['modfunc']) {
	$student = DBGet(DBQuery('SELECT s.STUDENT_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,s.NAME_SUFFIX,fsa.ACCOUNT_ID,fsa.STATUS,fsa.DISCOUNT,fsa.BARCODE,(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fsa.ACCOUNT_ID) AS BALANCE FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fsa WHERE s.STUDENT_ID='.UserStudentID().' AND fsa.STUDENT_ID=s.STUDENT_ID'));
	$student = $student[1];

	$PHP_tmp_SELF = PreparePHP_SELF();
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=modify METHOD=POST>";
	DrawHeader('',SubmitButton('Cancel Sale','submit[cancel]').SubmitButton('Complete Sale','submit[complete]'));
	echo '</FORM>';

	echo '<TABLE width=100%><TR>';

	echo '<TD valign=top>';
	echo $student['FIRST_NAME'].' '.$student['MIDDLE_NAME'].' '.$student['LAST_NAME'].' '.$student['NAME_SUFFIX'].'<BR>';
	echo '<b><small>'.$student['STUDENT_ID'].'</small></b>';
	echo '</TD>';

	echo '<TD valign=top>'.red($student['BALANCE']).'<BR><small>Balance</small></TD>';

	echo '</TR></TABLE>';
	echo '<HR>';

	if ($student['BALANCE']) {

		echo '<TABLE border=0 width=100%>';
		echo '<TR><TD width=100% valign=top>';

		$RET = DBGet(DBQuery('SELECT fsti.DESCRIPTION,fsti.AMOUNT FROM FOOD_SERVICE_TRANSACTIONS fst,FOOD_SERVICE_TRANSACTION_ITEMS fsti WHERE fst.ACCOUNT_ID='.$student['ACCOUNT_ID'].' AND fst.STUDENT_ID='.UserStudentID().' AND fst.SYEAR='.UserSyear().' AND fst.DESCRIPTION=\'Lunch\' AND fst.TIMESTAMP BETWEEN CURRENT_DATE AND \'tomorrow\' AND fsti.TRANSACTION_ID=fst.TRANSACTION_ID'));

		$columns = array('DESCRIPTION'=>'Item','AMOUNT'=>'Amount');
		ListOutput($RET,$columns,'Earlier Sale','Earlier Sales',$link,false,array('save'=>false,'search'=>false));

		// IMAGE
		if ($file = @fopen($StudentPicturesPath.($syear = UserSyear()).'/'.UserStudentID().'.jpg','r') || $file = @fopen($StudentPicturesPath.($syear = UserSyear() - 1).'/'.UserStudentID().'.jpg','r')) {
			fclose($file);
			echo '<TD rowspan=2 width=150 align=left valign=top><IMG SRC="'.$StudentPicturesPath.$syear.'/'.UserStudentID().'.jpg" width=150></TD>';
		}

		echo '</TD></TR>';
		echo '<TR><TD width=100% valign=top>';

		$RET = array(array());
		foreach($_SESSION['SALE'] as $key=>$value)
			$RET[]=array('AMOUNT'=>$value['PRICE'],'DESCRIPTION'=>$value['DESCRIPTION'],'SALE_ID'=>$key);
		unset($RET[0]);

		$items_RET = DBGet(DBQuery('SELECT SHORT_NAME,DESCRIPTION FROM FOOD_SERVICE_MENU_ITEMS WHERE SCHOOL_ID=\''.UserSchool().'\' AND PRICE IS NOT NULL ORDER BY SORT_ORDER'));
		$items = array();
		if(count($items_RET)) {
			foreach($items_RET as $value)
				$items += array($value['SHORT_NAME']=>$value['DESCRIPTION']);
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
		echo ErrorMessage(array('<IMG SRC=assets/x.gif align=absmiddle> This student does not have a valid Meal Account.'));
}
?>
