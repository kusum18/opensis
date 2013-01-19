<?php

function DeleteTransaction($transaction_id,$type='student')
{
	if($type=='staff')
	{
		$sql1 = "SELECT @amt:=coalesce(sum(AMOUNT),0) FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id'";
		$sql2 = "SELECT @staff_id:=STAFF_ID FROM FOOD_SERVICE_STAFF_TRANSACTIONS WHERE TRANSACTION_ID='$transaction_id'";
		$sql3 = "UPDATE FOOD_SERVICE_STAFF_ACCOUNTS SET BALANCE=BALANCE-@amt WHERE STAFF_ID='@staff_id'";
		$sql4 = "DELETE FROM FOOD_SERVICE_STAFF_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id' AND ITEM_ID='$item_id'";
		$sql5 = "DELETE FROM FOOD_SERVICE_STAFF_TRANSACTIONS WHERE TRANSACTION_ID='$transaction_id'";
	}
	else
	{
		$sql1 = "SELECT @amt:=coalesce(sum(AMOUNT),0) FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id'";
		$sql2 = "SELECT @account_id:=ACCOUNT_ID FROM FOOD_SERVICE_TRANSACTIONS WHERE TRANSACTION_ID='$transaction_id'";
		$sql3 = "UPDATE FOOD_SERVICE_ACCOUNTS SET BALANCE=BALANCE-@amt WHERE ACCOUNT_ID=@account_id";
		$sql4 = "DELETE FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID='$transaction_id'";
		$sql5 = "DELETE FROM FOOD_SERVICE_TRANSACTIONS WHERE TRANSACTION_ID='$transaction_id'";
	}
	DBQuery('BEGIN; '.$sql1.'; '.$sql2.'; '.$sql3.'; '.$sql4.'; '.$sql5.'; COMMIT;');
}
?>
