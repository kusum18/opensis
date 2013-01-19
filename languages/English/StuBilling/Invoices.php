<?php

if($_REQUEST[modfunc]=='')
{
	if(defined('PRINT_LETTER'))
		$title = 'Student Billing Notifications';
	else
		$title = 'Student Billing Invoices';

	DrawHeader($title);
	echo '<BR>';
	PopTable('header','Search');
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=list method=POST>";
	echo '<TABLE>';
	Warehouse('searchstu');
	echo '<TR><TD>Balance Between</TD><TD><INPUT type=text name=balance_low> &amp; <INPUT type=text name=balance_high></TD></TR>';
	echo '<TR><TD>Balance Not Zero</TD><TD><INPUT type=checkbox name=not_zero value=Y></TD></TR>';
	echo '<TR><TD>Invoice</TD><TD>';
	echo '<SELECT name=editor>
			<OPTION value=both>Student Billing & Lunch</OPTION>
			<OPTION value=stubilling SELECTED>Student Billing</OPTION>
			<OPTION value=lunch>Lunch</OPTION>
		</SELECT>';
	echo '</TD></TR>';
	PrepareSchool(SessionSchool(),'',SessionCurSchool());
	Warehouse('searchgrade');
	Warehouse('searchyear');

	if(defined('PRINT_LETTER'))
	{
		echo '<TR><TD>Letter Text</TD><TD>';
		echo '<TEXTAREA name=letter rows=10 cols=50>

Dear __PARENTS__,
	Your child, __STUDENT__ (__STUDENT_ID__) now has a balance of __BALANCE__. It should be refilled as soon as possible to avoid running out.
		
--Administration
		</TEXTAREA>';
		echo '</TD></TR>';
	}
	echo '<TR><TD colspan=2 align=center>';
	Buttons('Find','Reset');
	echo '</TD></TR>';
	echo '</TABLE>';
	echo '</FORM>';
	PopTable('footer');
}

if($_REQUEST['modfunc']=='list')
{
	$_REQUEST['balance_low'] = ereg_replace('[^0-9]','',$_REQUEST['balance_low']);
	$_REQUEST['balance_high'] = ereg_replace('[^0-9]','',$_REQUEST['balance_high']);

	if($_REQUEST['balance_low']>$_REQUEST['balance_high'])
	{
		$tmp = $_REQUEST['balance_high'];
		$_REQUEST['balance_high'] = $_REQUEST['balance_low'];
		$_REQUEST['balance_low'] = $tmp;
	}
}

if($_REQUEST[modfunc]=='list' && ($_REQUEST[editor]=='stubilling' || $_REQUEST[editor]=='lunch'))
{
	if($_REQUEST[editor]=='stubilling')
	{
		$debits_table = 'STU_BILLING_FEES';
		$credits_cond = " (LUNCH_PAYMENT!='Y' OR LUNCH_PAYMENT IS NULL) ";
		$letter_title = 'Student Billing Notification';
	}
	else
	{
		$debits_table = 'STU_BILLING_ACT_LUNCH';
		$credits_cond = " LUNCH_PAYMENT='Y' ";
		$letter_title = 'Lunch Billing Notification';
	}
	$sql = "select school,ADDRESS1,zipcode,area_code,telephone,city,state from schools";
	$address_RET = DBGet(DBQuery($sql),array(),array('SCHOOL'));
	foreach($address_RET as $school=>$address)
	{
		$address = $address[1];
		$school_address[$school] = '<font size=+1>'.GetTitle() . '</font><BR>' . $address['ADDRESS1'] . '<BR>' . $address['CITY'].', '.$address['STATE'].' '.$address['ZIPCODE'].'<BR>('.$address['AREA_CODE'].') '.substr($address['TELEPHONE'],0,3).'-'.substr($address['TELEPHONE'],3);
	}
	
	
	$sql = "SELECT ssm.STUDENT_ID,ssm.SCHOOL,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,rm.PERSON_ID ";
	if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
		$sql .= ",(SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.") as BALANCE ";
	$sql .= "FROM STU_SCHOOL_MEETS ssm,STUDENTS s,RELATIONS_MEETS rm
			WHERE
				ssm.SYEAR='$_REQUEST[year]' AND s.STUDENT_ID=ssm.STUDENT_ID 
				AND rm.STUDENT_ID = ssm.STUDENT_ID AND rm.CUSTODY = 'Y'
				"; 
	if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
		$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
				 BETWEEN '$_REQUEST[balance_low]' AND '$_REQUEST[balance_high]' ";
	if($_REQUEST[not_zero]=='Y')
		$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
				 != '0' ";
	if($_REQUEST[sch])
		$sql .= "AND ssm.SCHOOL='$_REQUEST[sch]' ";
	if($_REQUEST[grade])
		$sql .= "AND ssm.GRADE='$_REQUEST[grade]' ";
	if($_REQUEST[stuid])
		$sql .= "AND ssm.STUDENT_ID='$_REQUEST[stuid]' ";
	if($_REQUEST[first])
		$sql .= "AND s.FIRST_NAME LIKE '".strtoupper($_REQUEST[first])."%' ";
	if($_REQUEST[last])
		$sql .= "AND s.LAST_NAME LIKE '".strtoupper($_REQUEST[last])."%' ";
	$sql .= "ORDER BY s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME ";

	$QI = DBQuery($sql);
	$RET = DBGet($QI,array('LAST_NAME'=>'GetCapWords','FIRST_NAME'=>'GetCapWords'),array('STUDENT_ID'));
	
	if(count($RET))
	{
		$handle=PDFStart("Invoices.pdf", "Click Here to Download the Invoices","--webpage --quiet -t pdf12 --jpeg --no-links --portrait --footer t --header . --left 0.5in ");
		$_REQUEST['PDF'] = true;
	
		$f_year = $_REQUEST[f_year] = $_REQUEST['year'];
		$modfunc = 'detail';
		
		foreach($RET as $student_id=>$student_records)
		{
			unset($addresses);
			foreach($student_records as $person)
			{
				$addr = GetAddrPer($person['PERSON_ID']);
				$addr = $addr[1];
				$parents = $addr['FIRST_NAME'].' '.$addr['LAST_NAME'];
				if($addresses[$addr['ADDRESS_ID']]['PARENTS'])
					$parents = $addresses[$addr['ADDRESS_ID']]['PARENTS'] . ' &amp; ' . $parents;
				if(is_array($addr))
					$addresses[$addr['ADDRESS_ID']] =  $addr + array('PARENTS'=>str_replace(' ','&nbsp;',$parents));					
			}
			
			if(count($addresses))
			{
				foreach($addresses as $addr)
				{
					if(defined('PRINT_LETTER'))
						printLetter($student_records[1],$letter_title);

					echo '<CENTER>';
					echo '<TABLE border=1 width=100%><TR><TD width=100% align=center><font size=+1><b>Student Financial Account<b></font></TD></TR></TABLE>';
					echo '<B>'.$school_address[$student_records[1]['SCHOOL']].'</B>';
					echo '</CENTER>';

					echo '<TABLE><TR><TD width=35> &nbsp; </TD><TD nowrap><BR><BR><BR>';
					echo $addr['PARENTS'].'<BR>';
					echo $addr['HOUSE_NO']." ".$addr['LETTER']." ".$addr['DIRECTION']." ".$addr['STREET']." ";
					if($addr['APT']){ echo "Apt. ".$addr['APT'];}
					echo '<BR>'.$addr['CITY'].", ".$addr['STATE']." ".$addr['ZIPCODE'];
					if($addr['PLUS4'])
						echo '-'.$addr['PLUS4'];
					echo '</TD></TR></TABLE><BR>';
					
					echo '<TABLE width=100%><TR><TD align=left width=50%><b>'.$student_records[1][FIRST_NAME].' '.$student_records[1][LAST_NAME].' - '.$student_records[1][STUDENT_ID].'</b></TD>';
					echo '<TD align=right>'.ProperDate(DBDate()).'</TD></TR></TABLE>';
					$f_stuid = $_REQUEST[f_stuid] = $student_records[1][STUDENT_ID];
					$f_school = $_REQUEST[f_school] = $student_records[1][SCHOOL];
					include('modules/StudentInfo/StuBilling.inc.php');
					echo '<!-- NEW PAGE -->';
				}
			}
		}
	
		PDFStop($handle);
	}
	else
		echo '<CENTER><H4>No Students were Found.</H4></CENTER>';
}
elseif($_REQUEST[modfunc]=='list' && $_REQUEST[editor]=='both')
{
	$debits_table = 'STU_BILLING_FEES';
	$credits_cond = " (LUNCH_PAYMENT!='Y' OR LUNCH_PAYMENT IS NULL) ";
	$stubilling_title = '<B>Student Fees and Payments</B><BR>School Year: '.DispYear($_REQUEST[year]).'<BR>as of '.ProperDate(DBDate());
	
	$sql = "SELECT ssm.STUDENT_ID,ssm.SCHOOL,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME ";
	if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
		$sql .= ",(SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.") as BALANCE ";
	$sql .= "FROM STU_SCHOOL_MEETS ssm,STUDENTS s
			WHERE ssm.SYEAR='$_REQUEST[year]' AND s.STUDENT_ID=ssm.STUDENT_ID "; 
	if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
		$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
				 BETWEEN '$_REQUEST[balance_low]' AND '$_REQUEST[balance_high]' ";
	if($_REQUEST[not_zero]=='Y')
		$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
				 != '0' ";
	if($_REQUEST[sch])
		$sql .= "AND ssm.SCHOOL='$_REQUEST[sch]' ";
	if($_REQUEST[grade])
		$sql .= "AND ssm.GRADE='$_REQUEST[grade]' ";
	if($_REQUEST[stuid])
		$sql .= "AND ssm.STUDENT_ID='$_REQUEST[stuid]' ";
	if($_REQUEST[first])
		$sql .= "AND s.FIRST_NAME LIKE '".strtoupper($_REQUEST[first])."%' ";
	if($_REQUEST[last])
		$sql .= "AND s.LAST_NAME LIKE '".strtoupper($_REQUEST[last])."%' ";
	$sql .= "ORDER BY s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME ";

	$QI = DBQuery($sql);
	$stubilling_RET = DBGet($QI,array('LAST_NAME'=>'GetCapWords','FIRST_NAME'=>'GetCapWords'));
	
	$debits_table = 'STU_BILLING_ACT_LUNCH';
	$credits_cond = " LUNCH_PAYMENT='Y' ";
	$lunch_title = '<B>Lunch Purchases and Payments</B><BR>School Year: '.DispYear($_REQUEST[year]).'<BR>as of '.ProperDate(DBDate());

	$sql = "SELECT ssm.STUDENT_ID,ssm.SCHOOL,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME ";
	if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
		$sql .= ",(SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.") as BALANCE ";
	$sql .= "FROM STU_SCHOOL_MEETS ssm,STUDENTS s
			WHERE ssm.SYEAR='$_REQUEST[year]' AND s.STUDENT_ID=ssm.STUDENT_ID "; 
	if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
		$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
				 BETWEEN '$_REQUEST[balance_low]' AND '$_REQUEST[balance_high]' ";
	if($_REQUEST[not_zero]=='Y')
		$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
				 != '0' ";
	if($_REQUEST[sch])
		$sql .= "AND ssm.SCHOOL='$_REQUEST[sch]' ";
	if($_REQUEST[grade])
		$sql .= "AND ssm.GRADE='$_REQUEST[grade]' ";
	if($_REQUEST[stuid])
		$sql .= "AND ssm.STUDENT_ID='$_REQUEST[stuid]' ";
	if($_REQUEST[first])
		$sql .= "AND s.FIRST_NAME LIKE '".strtoupper($_REQUEST[first])."%' ";
	if($_REQUEST[last])
		$sql .= "AND s.LAST_NAME LIKE '".strtoupper($_REQUEST[last])."%' ";
	$sql .= "ORDER BY s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME ";

	$QI = DBQuery($sql);
	$lunch_RET = DBGet($QI,array('LAST_NAME'=>'GetCapWords','FIRST_NAME'=>'GetCapWords'),array('STUDENT_ID'));
	
	if(count($stubilling_RET) || count($lunch_RET))
	{
		$handle=PDFStart("Invoices.pdf", "Click Here to Download the Invoices","--webpage --quiet -t pdf12 --jpeg --no-links --portrait --footer t --header . --left 0.5in ");
	
		$f_year = $_REQUEST[f_year] = $_REQUEST['year'];
		$modfunc = 'detail';
		
		if(count($stubilling_RET))
		{
			foreach($stubilling_RET as $student)
			{
				$_REQUEST[editor] = 'stubilling';
				if(defined('PRINT_LETTER'))
					printLetter($student,'Student Billing Notification');
				echo '<H4>'.$student[FIRST_NAME].' '.$student[LAST_NAME].' - '.$student[STUDENT_ID].'</H4>';
				echo $stubilling_title;
				$f_stuid = $_REQUEST[f_stuid] = $student[STUDENT_ID];
				$f_school = $_REQUEST[f_school] = $student[SCHOOL];
				include('modules/StudentInfo/StuBilling.inc.php');
				echo '<!-- NEW PAGE -->';			
				
				if($lunch_RET[$student[STUDENT_ID]])
				{
					if(defined('PRINT_LETTER'))
						printLetter($lunch_RET[$student[STUDENT_ID]][1],'Lunch Billing Notification');
					
					$_REQUEST[editor] = 'lunch';
					echo '<H4>'.$lunch_RET[$student[STUDENT_ID]][1][FIRST_NAME].' '.$lunch_RET[$student[STUDENT_ID]][1][LAST_NAME].' - '.$lunch_RET[$student[STUDENT_ID]][1][STUDENT_ID].'</H4>';
					echo $lunch_title;
					$f_stuid = $_REQUEST[f_stuid] = $lunch_RET[$student[STUDENT_ID]][1][STUDENT_ID];
					$f_school = $_REQUEST[f_school] = $lunch_RET[$student[STUDENT_ID]][1][SCHOOL];
					include('modules/StudentInfo/StuBilling.inc.php');
					echo '<!-- NEW PAGE -->';			
					unset($lunch_RET[$student[STUDENT_ID]]);
				}
			}
		}
		
		if(count($lunch_RET))
		{
			foreach($lunch_RET as $student_id=>$student)
			{
				if(defined('PRINT_LETTER'))
					printLetter($student[1],'Lunch Billing Notification');
				
				$student = $student[1];
				$_REQUEST[editor] = 'lunch';
				echo '<H4>'.$student[FIRST_NAME].' '.$student[LAST_NAME].' - '.$student[STUDENT_ID].'</H4>';
				echo $lunch_title;
				$f_stuid = $_REQUEST[f_stuid] = $student[STUDENT_ID];
				$f_school = $_REQUEST[f_school] = $student[SCHOOL];
				include('modules/StudentInfo/StuBilling.inc.php');
				echo '<!-- NEW PAGE -->';			
			}
		}	
		PDFStop($handle);
	}
	else
		echo '<CENTER><H4>No Students were Found.</H4></CENTER>';
}
elseif($_REQUEST[modfunc]=='list' && $_REQUEST[editor]=='none')
{
	$debits_table = 'STU_BILLING_FEES';
	$credits_cond = " (LUNCH_PAYMENT!='Y' OR LUNCH_PAYMENT IS NULL) ";
	$stubilling_title = '<B>Student Fees and Payments</B><BR>School Year: '.DispYear($_REQUEST[year]).'<BR>as of '.ProperDate(DBDate());
	
	$sql = "SELECT ssm.STUDENT_ID,ssm.SCHOOL,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME ";
	if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
		$sql .= ",(SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.") as BALANCE ";
	$sql .= "FROM STU_SCHOOL_MEETS ssm,STUDENTS s
			WHERE ssm.SYEAR='$_REQUEST[year]' AND s.STUDENT_ID=ssm.STUDENT_ID "; 
	if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
		$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
				 BETWEEN '$_REQUEST[balance_low]' AND '$_REQUEST[balance_high]' ";
	if($_REQUEST[not_zero]=='Y')
		$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
				!= '0' ";
	if($_REQUEST[sch])
		$sql .= "AND ssm.SCHOOL='$_REQUEST[sch]' ";
	if($_REQUEST[grade])
		$sql .= "AND ssm.GRADE='$_REQUEST[grade]' ";
	if($_REQUEST[stuid])
		$sql .= "AND ssm.STUDENT_ID='$_REQUEST[stuid]' ";
	if($_REQUEST[first])
		$sql .= "AND s.FIRST_NAME LIKE '".strtoupper($_REQUEST[first])."%' ";
	if($_REQUEST[last])
		$sql .= "AND s.LAST_NAME LIKE '".strtoupper($_REQUEST[last])."%' ";
	$sql .= "ORDER BY s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME ";

	$QI = DBQuery($sql);
	$stubilling_RET = DBGet($QI,array('FIRST_NAME'=>'GetCapWords','LAST_NAME'=>'GetCapWords'));
	
	$debits_table = 'STU_BILLING_ACT_LUNCH';
	$credits_cond = " LUNCH_PAYMENT='Y' ";
	$lunch_title = '<B>Lunch Purchases and Payments</B><BR>School Year: '.DispYear($_REQUEST[year]).'<BR>as of '.ProperDate(DBDate());

	$sql = "SELECT ssm.STUDENT_ID,ssm.SCHOOL,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME ";
	if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
		$sql .= ",(SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.") as BALANCE ";
	$sql .= "FROM STU_SCHOOL_MEETS ssm,STUDENTS s
			WHERE ssm.SYEAR='$_REQUEST[year]' AND s.STUDENT_ID=ssm.STUDENT_ID "; 
	if(($_REQUEST[balance_low] || $_REQUEST[balance_low]==='0') && ($_REQUEST[balance_high] || $_REQUEST[balance_high]==='0'))
		$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
				 BETWEEN '$_REQUEST[balance_low]' AND '$_REQUEST[balance_high]' ";
	if($_REQUEST[not_zero]=='Y')
		$sql .= "AND (SELECT COALESCE((SELECT sum(AMOUNT) FROM STU_BILLING_ACT WHERE STUDENT_ID=ssm.STUDENT_ID AND $credits_cond),0) -
				COALESCE((SELECT sum(AMOUNT) FROM $debits_table WHERE STUDENT_ID=ssm.STUDENT_ID),0) ".FROM_DUAL.")
				 != '0' ";
	if($_REQUEST[sch])
		$sql .= "AND ssm.SCHOOL='$_REQUEST[sch]' ";
	if($_REQUEST[grade])
		$sql .= "AND ssm.GRADE='$_REQUEST[grade]' ";
	if($_REQUEST[stuid])
		$sql .= "AND ssm.STUDENT_ID='$_REQUEST[stuid]' ";
	if($_REQUEST[first])
		$sql .= "AND s.FIRST_NAME LIKE '".strtoupper($_REQUEST[first])."%' ";
	if($_REQUEST[last])
		$sql .= "AND s.LAST_NAME LIKE '".strtoupper($_REQUEST[last])."%' ";
	$sql .= "ORDER BY s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME ";

	$QI = DBQuery($sql);
	$lunch_RET = DBGet($QI,array('FIRST_NAME'=>'GetCapWords','LAST_NAME'=>'GetCapWords'),array('STUDENT_ID'));
	
	if(count($stubilling_RET) || count($lunch_RET))
	{
		$handle=PDFStart("Letters.pdf", "Click Here to Download the Letters","--webpage --quiet -t pdf12 --jpeg --no-links --portrait --footer t --header . --left 0.5in ");
	
		if(count($stubilling_RET))
		{
			foreach($stubilling_RET as $student)
			{
				if(defined('PRINT_LETTER'))
					printLetter($student,'Student Billing Notification');
				
				if($lunch_RET[$student[STUDENT_ID]])
				{
					if(defined('PRINT_LETTER'))
						printLetter($lunch_RET[$student[STUDENT_ID]][1],'Lunch Billing Notification');
				}
			}
		}
		
		if(count($lunch_RET))
		{
			foreach($lunch_RET as $student_id=>$student)
			{
				if(defined('PRINT_LETTER'))
				{
					$student[1][BALANCE] = $student[1][LUNCH_BALANCE];
					printLetter($student[1],'Lunch Billing Notification');
				}
			}
		}	
		PDFStop($handle);
	}
	else
		echo '<CENTER><H4>No Students were Found.</H4></CENTER>';
}

function printLetter($student,$title)
{	global $LETTER;

	echo '<B>'.$title.'</B><BR>';
	$letter = $_REQUEST[letter];
	if($LETTER[student])
		$letter = str_replace('__STUDENT__',$student[FIRST_NAME].' '.$student[LAST_NAME],$letter);
	if($LETTER[student_id])
		$letter = str_replace('__STUDENT_ID__',$student[STUDENT_ID],$letter);
	if($LETTER[parents])
		$letter = str_replace('__PARENTS__',GetRelNames($student[STUDENT_ID]),$letter);
	if($LETTER[balance])
		$letter = str_replace('__BALANCE__',Currency($student[BALANCE]),$letter);

	echo $letter;
	echo '<!-- NEW PAGE -->';
}

?>