<?php
// if $homeroom is null then teacher and subject for period used for attendance are used for homeroom teacher and subject
// if $homeroom is set then teacher for $homeroom subject and $homeroom are used for teacher and subject
//$homeroom = 'Homeroom';
$target = '0.0';
$warning = '5.00';
$warning_note = '%N\'s lunch account is getting low.  Please send in additional lunch money with %H reminder slip.  THANK YOU!';
$negative_note = '%N now has a <B>negative balance</B> in %H lunch account. Please stop by the office and speak with Mrs. Wanda or Mrs. Donna before placing additional lunch orders. Thanks!';
$minimum = '0.00';
$minimum_note = '%N now has a <B>negative balance</B> in %H lunch account. Please stop by the office and speak with Mrs. Wanda or Mrs. Donna before placing additional lunch orders. Thanks!';
$year_end_note = 'Note: Requested payemnt amount';

if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['st_arr']))
	{
	$st_list = "'".implode("','",$_REQUEST['st_arr'])."'";

	$students = DBGet(DBQuery("SELECT s.STUDENT_ID,s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,s.NAME_SUFFIX,s.CUSTOM_200000002 AS NICKNAME,s.CUSTOM_200000000 AS GENDER,fsa.ACCOUNT_ID,fsa.STATUS,(SELECT BALANCE FROM FOOD_SERVICE_ACCOUNTS WHERE ACCOUNT_ID=fsa.ACCOUNT_ID) AS BALANCE,(SELECT TITLE FROM SCHOOLS WHERE ID=ssm.SCHOOL_ID AND SYEAR=ssm.SYEAR) AS SCHOOL,(SELECT TITLE FROM SCHOOL_GRADELEVELS WHERE ID=ssm.GRADE_ID) AS GRADE".($_REQUEST['year_end']=='Y'?",(SELECT count(1) FROM ATTENDANCE_CALENDAR WHERE CALENDAR_ID=ssm.CALENDAR_ID AND SCHOOL_DATE>CURRENT_DATE) AS DAYS,(SELECT -sum(fsti.AMOUNT) FROM FOOD_SERVICE_TRANSACTIONS fst,FOOD_SERVICE_TRANSACTION_ITEMS fsti WHERE fst.SYEAR=ssm.SYEAR AND fsti.TRANSACTION_ID=fst.TRANSACTION_ID AND fst.ACCOUNT_ID=fsa.ACCOUNT_ID AND fsti.AMOUNT<0 AND fst.TIMESTAMP BETWEEN CURRENT_DATE-14 AND CURRENT_DATE-1) AS T_AMOUNT,(SELECT count(1) FROM ATTENDANCE_CALENDAR WHERE CALENDAR_ID=ssm.CALENDAR_ID AND SCHOOL_DATE BETWEEN CURRENT_DATE-14 AND CURRENT_DATE-1) AS T_DAYS":'')." FROM STUDENTS s,STUDENT_ENROLLMENT ssm,FOOD_SERVICE_STUDENT_ACCOUNTS fsa WHERE s.STUDENT_ID IN (".$st_list.") AND fsa.STUDENT_ID=s.STUDENT_ID AND ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='".UserSyear()."'"));
	$handle = PDFStart();
	$cnt = 0; 	
	foreach($students as $student)
	{
		if($homeroom)
			$teacher = DBGet(DBQuery("SELECT CONCAT(s.FIRST_NAME,' ',s.LAST_NAME) AS FULL_NAME,cs.TITLE
			FROM STAFF s,SCHEDULE sch,COURSE_PERIODS cp,COURSES c,COURSE_SUBJECTS cs
			WHERE s.STAFF_ID=cp.TEACHER_ID AND sch.STUDENT_ID='".$student['STUDENT_ID']."' AND cp.COURSE_ID=sch.COURSE_ID AND c.COURSE_ID=cp.COURSE_ID AND c.SUBJECT_ID=cs.SUBJECT_ID AND cs.TITLE='".$homeroom."' AND sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND sch.SYEAR='".UserSyear()."'"));
		else
			$teacher = DBGet(DBQuery("SELECT CONCAT(s.FIRST_NAME,' ',s.LAST_NAME) AS FULL_NAME,cs.TITLE
			FROM STAFF s,SCHEDULE sch,COURSE_PERIODS cp,COURSES c,COURSE_SUBJECTS cs,SCHOOL_PERIODS sp
			WHERE s.STAFF_ID=cp.TEACHER_ID AND sch.STUDENT_ID='".$student['STUDENT_ID']."' AND cp.COURSE_ID=sch.COURSE_ID AND c.COURSE_ID=cp.COURSE_ID AND c.SUBJECT_ID=cs.SUBJECT_ID AND sp.ATTENDANCE='Y' AND sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND sch.SYEAR='".UserSyear()."'"));
		$teacher = $teacher[1];
		$xstudents = DBGet(DBQuery("SELECT s.STUDENT_ID,s.FIRST_NAME,s.LAST_NAME,s.CUSTOM_200000002 AS NICKNAME FROM STUDENTS s,STUDENT_ENROLLMENT ssm,FOOD_SERVICE_STUDENT_ACCOUNTS fssa WHERE ssm.SYEAR='".UserSyear()."' AND ssm.START_DATE>=CURRENT_DATE AND (ssm.END_DATE<=CURRENT_DATE OR ssm.END_DATE IS NULL) AND fssa.ACCOUNT_ID='".$student['ACCOUNT_ID']."' AND s.STUDENT_ID=fssa.STUDENT_ID AND s.STUDENT_ID!='".$student['STUDENT_ID']."'"));
		$xstudents = DBGet(DBQuery("SELECT s.STUDENT_ID,s.FIRST_NAME,s.LAST_NAME FROM STUDENTS s,FOOD_SERVICE_STUDENT_ACCOUNTS fssa WHERE fssa.ACCOUNT_ID='".$student['ACCOUNT_ID']."' AND s.STUDENT_ID=fssa.STUDENT_ID AND s.STUDENT_ID!='".$student['STUDENT_ID']."'"));

		if($_REQUEST['year_end']=='Y')
			$xtarget = number_format($student['DAYS']*$student['T_AMOUNT']/$student['T_DAYS'],2);
		else
			$xtarget = number_format($target*(count($xstudents)+1),2);

		$last_deposit = DBGet(DBQuery("SELECT (SELECT sum(AMOUNT) FROM FOOD_SERVICE_TRANSACTION_ITEMS WHERE TRANSACTION_ID=fst.TRANSACTION_ID) AS AMOUNT,DATE_FORMAT(fst.TIMESTAMP,'%y-%b-%d') AS DATE FROM FOOD_SERVICE_TRANSACTIONS fst WHERE fst.SHORT_NAME='DEPOSIT' AND fst.ACCOUNT_ID='".$student['ACCOUNT_ID']."' AND SYEAR='".UserSyear()."' ORDER BY fst.TRANSACTION_ID DESC LIMIT 1"),array('DATE'=>'ProperDate'));
		$last_deposit = $last_deposit[1];

		if($student['BALANCE'] < $minimum)
			reminder($student,$teacher,$xstudents,$xtarget,$last_deposit,$minimum_note);
		elseif($student['BALANCE'] < 0)
			reminder($student,$teacher,$xstudents,$xtarget,$last_deposit,$negative_note);
		elseif($student['BALANCE'] < $warning)
			reminder($student,$teacher,$xstudents,$xtarget,$last_deposit,$warning_note);

		echo '<!-- NEED 3in -->';
	}
	PDFStop($handle);
	}
	else
	BackPrompt('You must choose at least one student');
}

if(!$_REQUEST['modfunc'])
{
	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&_CENTRE_PDF=true method=POST target=_blank>";
		//DrawHeader('',SubmitButton('Create Reminders for Selected Students'));
		$extra['header_right'] = SubmitButton('Create Reminders for Selected Students');

		$extra['extra_header_left'] = '<TABLE><TR>';
		$extra['extra_header_left'] .= '<TD align=right>Estimate for year end</TD><TD align=left><INPUT type=checkbox name=year_end value=Y></TD>';
		$extra['extra_header_left'] .= '</TR></TABLE>';
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y checked name=controller onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['new'] = true;
	$extra['options']['search'] = false;

	Widgets('fsa_balance_warning');
	Widgets('fsa_status');

	$extra['SELECT'] .= ',coalesce(fssa.STATUS,\'Active\') AS STATUS,fsa.BALANCE';
	$extra['SELECT'] .= ",CASE WHEN fsa.BALANCE < '$warning' AND fsa.BALANCE >= 0 THEN 'Y' ELSE NULL END AS WARNING";
	$extra['SELECT'] .= ",CASE WHEN fsa.BALANCE < 0 AND fsa.BALANCE >= '$minimum' THEN 'Y' ELSE NULL END AS NEGATIVE";
	$extra['SELECT'] .= ",CASE WHEN fsa.BALANCE < '$minimum' THEN 'Y' ELSE NULL END AS MINIMUM";
	if(!strpos($extra['FROM'],'fssa'))
	{
		$extra['FROM'] .= ',FOOD_SERVICE_STUDENT_ACCOUNTS fssa';
		$extra['WHERE'] .= ' AND fssa.STUDENT_ID=s.STUDENT_ID';
	}
	if(!strpos($extra['FROM'],'fsa'))
	{
		$extra['FROM'] .= ',FOOD_SERVICE_ACCOUNTS fsa';
		$extra['WHERE'] .= ' AND fsa.ACCOUNT_ID=fssa.ACCOUNT_ID';
	}
	$extra['functions'] += array('BALANCE'=>'red','WARNING'=>'x','NEGATIVE'=>'x','MINIMUM'=>'x');
	$extra['columns_after'] = array('BALANCE'=>'Balance','STATUS'=>'Status','WARNING'=>'Warning<br>'.$warning,'NEGATIVE'=>'Negative','MINIMUM'=>'Minimum<br>'.$minimum);

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER>'.SubmitButton('Create Reminders for Selected Students').'</CENTER>';
		echo "</FORM>";
	}
}

function reminder($student,$teacher,$xstudents,$target,$last_deposit,$note)
{
	global $cnt;
	
	$minimum_payment = number_format($target - $student['BALANCE'],2);
	if($minimum_payment < 0)
		$minimum_payment = '0.00';

	$cnt++;
	if($cnt>3) {
		echo '<TABLE style="page-break-before:always;" width="100%">';
		$cnt = 1;
	}
	else
		echo '<TABLE width=100%>';
	echo '<TR><TD colspan=3 align=center><FONT size=+1><I><B>Payment Reminder</B></I></FONT></TD></TR>';
	echo '<TR><TD colspan=3 align=center><B>'.$student['SCHOOL'].'</B></TD></TR>';

	echo '<TR><TD width=33%>';
	echo ($student['NICKNAME']?$student['NICKNAME']:$student['FIRST_NAME']).' '.$student['LAST_NAME'].'<BR>';
	echo '<small>'.$student['STUDENT_ID'].'</small>';
	if(count($xstudents))
	{
		echo '<small><BR>Other students om theis account:';
		foreach($xstudents as $xstudent)
			echo '<BR>&nbsp;&nbsp;'.($xstudent['NICKNAME']?$xstudent['NICKNAME']:$xstudent['FIRST_NAME']).' '.$xstudent['LAST_NAME'];
		echo '</small>';
	}
	echo '</TD><TD width=34%>';
	echo $student['GRADE'].'<BR>';
	echo '<small>Grade</small>';
	echo '</TD><TD width=33%>';
	echo $teacher['FULL_NAME'].'<BR>';
	echo '<small>'.$teacher['TITLE'].' Teacher</small>';
	echo '</TD></TR>';

	echo '<TR><TD width=33%>';
	echo ProperDate(DBDate()).'<BR>';
	echo '<small>Today\'s Date</small>';
	echo '</TD><TD width=34%>';
	echo ($last_deposit ? $last_deposit['DATE'] : 'None').'<BR>';
	echo '<small>Date of Last Deposit</small>';
	echo '</TD><TD width=33%>';
	echo ($last_deposit ? $last_deposit['AMOUNT'] : 'None').'<BR>';
	echo '<small>Amount of Last Deposit</small>';
	echo '</TD></TR>';

	echo '<TR><TD width=33%>';
	echo ($student['BALANCE']<0 ? '<B>'.$student['BALANCE'].'</B>' : $student['BALANCE']).'<BR>';
	echo '<small>Balance</small>';
	echo '</TD><TD width=34%>';
	echo '<B>'.$minimum_payment.'</B><BR>';
	echo '<small><B>Mimimum Payment</B></small>';
	echo '</TD><TD width=33%>';
	echo $student['ACCOUNT_ID'].'<BR>';
	echo '<small>Account ID</small>';
	echo '</TD></TR>';

	$note = str_replace('%N',($student['NICKNAME'] ? $student['NICKNAME'] : $student['FIRST_NAME']),$note);
	$note = str_replace('%F',$student['FIRST_NAME'],$note);
	$note = str_replace('%H',($student['GENDER'] ? (substr($student['GENDER'],0,1)=='F' ? 'her' : 'his') : 'his/her'),$note);
	$note = str_replace('%P',$minimum_payment,$note);
	$note = str_replace('%T',$target,$note);

	echo '<TR><TD colspan=3>';
	echo '<BR>'.$note.'<BR>';
	echo '</TD></TR>';
	echo "<TR><TD colspan=3><BR><BR><HR><BR><BR></TD></TR></TABLE>\n";
}
?>
