<?php

function DeleteTransactionItem($transaction_id,$item_id,$type='student')
{
	if($_REQUEST['type']=='staff')
	{
		$sql1 = "SELECT @amt:=AMOUNT FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id'";
		$sql2 = "SELECT @staff_id:=STAFF_ID FROM FOOD_SERVICE_STAFF_TRANSACTIONS WHERE TRANSACTION_ID='$transaction_id'";
		$sql3 = "UPDATE FOOD_SERVICE_STAFF_TRANSACTIONS SET BALANCE=BALANCE-@amt WHERE TRANSACTION_ID>='$transaction_id' AND STAFF_ID=@staff_id";
		$sql4 = "UPDATE FOOD_SERVICE_STAFF_ACCOUNTS SET BALANCE=BALANCE-@amt WHERE STAFF_ID=@staff_id";
		$sql5 = "DELETE FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id'";
	}
	else
	{
		$sql1 = "SELECT @amt:=AMOUNT FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id'";
		$sql2 = "SELECT @account_id:=ACCOUNT_ID FROM FOOD_SERVICE_TRANSACTIONS WHERE TRANSACTION_ID='$transaction_id'";
		$sql3 = "UPDATE FOOD_SERVICE_TRANSACTIONS SET BALANCE=BALANCE-@amt WHERE TRANSACTION_ID>='$transaction_id' AND ACCOUNT_ID=@account_id";
		$sql4 = "UPDATE FOOD_SERVICE_ACCOUNTS SET BALANCE=BALANCE-@amt WHERE ACCOUNT_ID=@account_id";
		$sql5 = "DELETE FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id'";
	}
	DBQuery('BEGIN; '.$sql1.'; '.$sql2.'; '.$sql3.'; '.$sql4.'; '.$sql5.'; COMMIT');
}
?>
