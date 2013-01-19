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
			$sql = substr($sql,0,-1)." WHERE STUDENT_ID='".$_REQUEST['student_id']."'";
			DBQuery($sql);
		}
	}
	//unset($_REQUEST['modfunc']);
	unset($_REQUEST['food_service']);
	unset($_SESSION['_REQUEST_vars']['food_service']);
}

if(!$_REQUEST['modfunc'] && UserStudentID())
{
	$student = DBGet(DBQuery("SELECT s.STUDENT_ID,".(Preferences('NAME')=='Common'?'coalesce(s.CUSTOM_200000002,s.FIRST_NAME)':'s.FIRST_NAME')."||' '||s.LAST_NAME AS FULL_NAME,fsa.ACCOUNT_ID,fsa.STATUS,fsa.DISCOUNT,fsa.BARCODE,(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fsa.ACCOUNT_ID) AS BALANCE FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fsa WHERE s.STUDENT_ID='".UserStudentID()."' AND fsa.STUDENT_ID=s.STUDENT_ID"));
	$student = $student[1];

	// find other students associated with the same account
	$xstudents = DBGet(DBQuery("SELECT s.STUDENT_ID,".(Preferences('NAME')=='Common'?'coalesce(s.CUSTOM_200000002,s.FIRST_NAME)':'s.FIRST_NAME')."||' '||s.LAST_NAME AS FULL_NAME FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fssa WHERE fssa.ACCOUNT_ID='".$student['ACCOUNT_ID']."' AND s.STUDENT_ID=fssa.STUDENT_ID AND s.STUDENT_ID!='".UserStudentID()."'"));

	echo '<TABLE width=100%>';
	echo '<TR>';
	echo '<TD valign=top>';
	echo '<TABLE width=100%><TR>';

	//echo '<TD valign=top>'.NoInput($student['FULL_NAME'],$student['STUDENT_ID']).'</TD>';
	echo '<TD valign=top>'.NoInput(($student['BALANCE']<0?'<FONT color=red>':'').$student['BALANCE'].($student['BALANCE']<0?'</FONT>':''),'Balance').'</TD>';

	echo '</TR></TABLE>';
	echo '</TD></TR></TABLE>';
	echo '<HR>';

	echo '<TABLE width=100% border=0 cellpadding=0 cellspacing=0>';
	echo '<TR><TD valign=top>';

	echo '<TABLE border=0 cellpadding=6 width=100%>';
	echo '<TR>';
	echo '<TD>'.TextInput($student['ACCOUNT_ID'],'food_service[ACCOUNT_ID]','Account ID','size=12 maxlength=10');
	// warn if other students associated with the same account
	if(count($xstudents))
	{
		$warning = 'Other students associated with same account:<BR>';
		foreach($xstudents as $xstudent)
			$warning .= str_replace('\'','&#39;',$xstudent['FULL_NAME']).'<BR>';
		echo button('warning','','# onMouseOver=\'stm(["Warning","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
	}
	// warn if account non-existent (balance query failed)
	if($student['BALANCE']=='')
	{
		$warning = 'Non-existent account!';
		echo button('warning','','# onMouseOver=\'stm(["Warning","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
	}
	echo '</TD>';
	$options = array('Inactive'=>'Inactive','Disabled'=>'Disabled','Closed'=>'Closed');
	echo '<TD>'.SelectInput($student['STATUS'],'food_service[STATUS]','Status',$options,'Active').'</TD>';
	echo '</TR><TR>';
	$options = array('Reduced'=>'Reduced','Free'=>'Free');
	echo '<TD>'.SelectInput($student['DISCOUNT'],'food_service[DISCOUNT]','Discount',$options,'Full').'</TD>';
	echo '<TD>'.TextInput($student['BARCODE'],'food_service[BARCODE]','Barcode','size=12 maxlength=25').'</TD>';
	echo '</TR>';
	echo '</TABLE>';

	echo '</TD></TR>';
	echo '</TABLE>';
}
?>
