<?php

if($_REQUEST['detailed_view']=='true')
{
	$RET = DBGet(DBQuery("SELECT fst.TRANSACTION_ID AS TRANS_ID,fst.TRANSACTION_ID,fst.ACCOUNT_ID,fst.SHORT_NAME,fst.STUDENT_ID,fst.DISCOUNT,(SELECT sum(AMOUNT) FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID=fst.TRANSACTION_ID) AS AMOUNT,fst.BALANCE,DATE_FORMAT(fst.TIMESTAMP,'%y-%b-%d') AS DATE,DATE_FORMAT(fst.TIMESTAMP,'%r') AS TIME,fst.DESCRIPTION,".db_case(array('fst.STUDENT_ID',"''",'NULL',"(SELECT CONCAT(".(Preferences('NAME')=='Common'?'coalesce(CUSTOM_200000002,FIRST_NAME)':'FIRST_NAME').",' ',LAST_NAME) FROM STUDENTS WHERE STUDENT_ID=fst.STUDENT_ID)"))." AS FULL_NAME,".db_case(array('fst.SELLER_ID',"''",'NULL',"(SELECT FIRST_NAME||' '||LAST_NAME FROM STAFF WHERE STAFF_ID=fst.SELLER_ID)"))." AS SELLER FROM FOOD_SERVICE_TRANSACTIONS fst WHERE SYEAR='".UserSyear()."' AND fst.TIMESTAMP BETWEEN '".date($date,'Y-m-d')."' AND '".date($date,'Y-m-d')."' + INTERVAL 1 DAY AND SCHOOL_ID='".UserSchool()."'".$where." ORDER BY fst.TRANSACTION_ID DESC"),array('DATE'=>'ProperDate','SHORT_NAME'=>'bump_count'));
	foreach($RET as $key=>$value)
	{
		// get details of each transaction
		$tmpRET = DBGet(DBQuery("SELECT TRANSACTION_ID AS TRANS_ID,*,'".$value['SHORT_NAME']."' AS TRANSACTION_SHORT_NAME FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID='".$value['TRANSACTION_ID']."'"),array('SHORT_NAME'=>'bump_items_count'));

		// merge transaction and detail records
		$RET[$key] = array($value) + $tmpRET;
	}
	//echo '<pre>'; var_dump($RET); echo '</pre>';
	$columns = array('TRANSACTION_ID'=>'#','ACCOUNT_ID'=>'Account ID','FULL_NAME'=>'Student','DATE'=>'Date','TIME'=>'Time','BALANCE'=>'Balance','DISCOUNT'=>'Discount','DESCRIPTION'=>'Description','DISCOUNT'=>'Discount','AMOUNT'=>'Amount','SELLER'=>'User');
	$group = array(array('TRANSACTION_ID'));
	$link['remove']['link'] = PreparePHP_SELF($_REQUEST,array(),array('modfunc'=>'delete'));
	$link['remove']['variables'] = array('transaction_id'=>'TRANS_ID','item_id'=>'ITEM_ID');
}
else
{
	$RET = DBGet(DBQuery("SELECT fst.TRANSACTION_ID,fst.ACCOUNT_ID,fst.SHORT_NAME,fst.STUDENT_ID,fst.DISCOUNT,(SELECT sum(AMOUNT) FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID=fst.TRANSACTION_ID) AS AMOUNT,fst.BALANCE,DATE_FORMAT(fst.TIMESTAMP,'%y-%b-%d') AS DATE,DATE_FORMAT(fst.TIMESTAMP,'%r') AS TIME,fst.DESCRIPTION,".db_case(array('fst.STUDENT_ID',"''",'NULL',"(SELECT CONCAT(".(Preferences('NAME')=='Common'?'coalesce(CUSTOM_200000002,FIRST_NAME)':'FIRST_NAME').",' ',LAST_NAME) FROM STUDENTS WHERE STUDENT_ID=fst.STUDENT_ID)"))." AS FULL_NAME FROM FOOD_SERVICE_TRANSACTIONS fst WHERE SYEAR='".UserSyear()."' AND fst.TIMESTAMP BETWEEN '".date($date,'Y-m-d')."' AND '".date($date,'Y-m-d')."' + INTERVAL 1 DAY AND SCHOOL_ID='".UserSchool()."'".$where."ORDER BY fst.TRANSACTION_ID DESC"),array('DATE'=>'ProperDate','SHORT_NAME'=>'bump_count'));
	$columns = array('TRANSACTION_ID'=>'#','ACCOUNT_ID'=>'Account ID','FULL_NAME'=>'Student','DATE'=>'Date','TIME'=>'Time','BALANCE'=>'Balance','DISCOUNT'=>'Discount','DESCRIPTION'=>'Description','AMOUNT'=>'Amount');
}
?>
