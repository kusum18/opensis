<?php

if($_REQUEST['modfunc']=='update')
{
	if(UserStudentID() && AllowEdit())
	{
		if(count($_REQUEST['food_service']))
		{
			$sql = "UPDATE FOOD_SERVICE_STUDENT_ACCOUNTS SET ";
			foreach($_REQUEST['food_service'] as $column_name=>$value)
				$sql .= $column_name.'=\''.str_replace("\'","''",str_replace("`","''",trim($value))).'\',';
			$sql = substr($sql,0,-1).' WHERE STUDENT_ID='.$_REQUEST['student_id'];
			DBQuery($sql);
		}
	}
	unset($_REQUEST['modfunc']);
}

Widgets('fsa_discount');
Widgets('fsa_status');
Widgets('fsa_barcode');
Widgets('fsa_account_id');

$extra['SELECT'] .= ',coalesce(fssa.STATUS,\'Active\') AS STATUS';
$extra['SELECT'] .= ',(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fssa.ACCOUNT_ID) AS BALANCE';
if(!strpos($extra['FROM'],'fssa'))
{
	$extra['FROM'] .= ',FOOD_SERVICE_STUDENT_ACCOUNTS fssa';
	$extra['WHERE'] .= ' AND fssa.STUDENT_ID=s.STUDENT_ID';
}
$extra['functions'] += array('BALANCE'=>'red');
$extra['columns_after'] = array('BALANCE'=>'Balance','STATUS'=>'Status');

Search('student_id',$extra);

if(!$_REQUEST['modfunc'] && UserStudentID())
{
	$student = DBGet(DBQuery('SELECT s.STUDENT_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,s.NAME_SUFFIX,fsa.ACCOUNT_ID,fsa.STATUS,fsa.DISCOUNT,fsa.BARCODE,(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fsa.ACCOUNT_ID) AS BALANCE FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fsa WHERE s.STUDENT_ID='.UserStudentID().' AND fsa.STUDENT_ID=s.STUDENT_ID'));
	$student = $student[1];

	// find other students associated with the same account
	$xstudents = DBGet(DBQuery("SELECT s.STUDENT_ID,CONCAT(s.FIRST_NAME,' ',s.LAST_NAME) AS FULL_NAME FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fssa WHERE fssa.ACCOUNT_ID=".$student['ACCOUNT_ID']." AND s.STUDENT_ID=fssa.STUDENT_ID AND s.STUDENT_ID!=".UserStudentID()));

	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&student_id=".UserStudentID()."&modfunc=update method=POST>";

	DrawHeader('',SubmitButton('Save'));

	echo '<BR>';
	PopTable('header','Account Information','width=100%');
	echo '<TABLE width=100%>';
	echo '<TR>';
	echo '<TD valign=top>';
	echo '<TABLE width=100%><TR>';

	echo '<TD valign=top>';
	echo $student['FIRST_NAME'].' '.$student['MIDDLE_NAME'].' '.$student['LAST_NAME'].' '.$student['NAME_SUFFIX'].'<BR>';
	echo '<b><small>'.$student['STUDENT_ID'].'</small></b>';
	echo '</TD>';

	echo '<TD valign=top>'.red($student['BALANCE']).'<BR><small>Balance</small></TD>';

	echo '</TR></TABLE>';
	echo '</TD></TR></TABLE>';
	echo '<HR>';

	echo '<TABLE width=100% border=0 cellpadding=0 cellspacing=0>';
	echo '<TR><TD valign=top>';

	echo '<TABLE border=0 cellpadding=6 width=100%>';
	echo '<TR>';
	echo '<TD>';
	echo TextInput($student['ACCOUNT_ID'],'food_service[ACCOUNT_ID]','Account ID','size=12 maxlength=10');
	// warn if other students associated with the same account
	if(count($xstudents))
	{
		$warning = 'Other students associated with same account:<BR>';
		foreach($xstudents as $xstudent)
			$warning .= str_replace('\'','&#39;',$xstudent['FULL_NAME']).'<BR>';
		echo button('warning','','# onMouseOver=\'stm(["Warning","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
	}
	// warn if account non-existent (balance query failed)
	if(!$student['BALANCE'])
	{
		$warning = 'Non-existent account!';
		echo button('warning','','# onMouseOver=\'stm(["Warning","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
	}
	echo '</TD>';
	echo '<TD>';
	$options = array('Inactive'=>'Inactive','Disabled'=>'Disabled','Closed'=>'Closed');
	echo SelectInput($student['STATUS'],'food_service[STATUS]','Status',$options,'Active');
	echo '</TD>';
	echo '</TR>';
	echo '<TR>';
	echo '<TD>';
	$options = array('Reduced'=>'Reduced','Free'=>'Free');
	echo SelectInput($student['DISCOUNT'],'food_service[DISCOUNT]','Discount',$options,'Full');
	echo '</TD>';
	echo '<TD>';
	echo TextInput($student['BARCODE'],'food_service[BARCODE]','Barcode','size=12 maxlength=25');
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
