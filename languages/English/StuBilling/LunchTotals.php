<?php
DrawHeader(ProgramTitle());

$begin_date = $_REQUEST['day_begin'].'-'.$_REQUEST[month_begin].'-'.$_REQUEST[year_begin];
$end_date = $_REQUEST[day_end].'-'.$_REQUEST[month_end].'-'.$_REQUEST[year_end];
DrawHeader(ProperDate($begin_date).' - '.ProperDate($end_date).' : '.GetSchool($_REQUEST[sch]));

// LUNCH DEBITS
$sql = "SELECT MENU_ID,".db_case(array('FSC',"''","'REG'","'1'","'FREE'","'2'","'REDUCED'"))." as FSC,
			count(*) as COUNT 
		FROM STU_BILLING_ACT_LUNCH sba,STUDENT_ENROLLMENT se 
		WHERE se.SYEAR=sba.SYEAR AND se.STUDENT_ID=sba.STUDENT_ID AND se.SCHOOL_ID='".UserSchool()."' AND ";
$sql .= "PAYMENT_DATE BETWEEN '$begin_date' AND '$end_date'
		GROUP BY MENU_ID,FSC";
$QI = DBQuery($sql);
$counts_RET = DBGet($QI,array(),array('MENU_ID','FSC'));

$sql = "SELECT ID,SCHOOL_ID,TITLE FROM LUNCH_MENU WHERE SCHOOL_ID='".UserSchool()."' ";
$QI = DBQuery($sql);
$menu_RET = DBGet($QI,array(),array('SCHOOL_ID'));

$columns = array('TITLE'=>'Lunch Item','REG_COUNT'=>'Regular Purchases','FREE_COUNT'=>'Free Purchases','REDUCED_COUNT'=>'Reduced Purchases','TOTAL'=>'Total');
foreach($menu_RET as $school=>$items)
{
	unset($RET);
	$RET[] = '';
	foreach($items as $item)
	{
		$total = $counts_RET[$item[ID]]['REG'][1]['COUNT'] + $counts_RET[$item[ID]]['FREE'][1]['COUNT'] + $counts_RET[$item[ID]]['REDUCED'][1]['COUNT'];
		$RET[] = array('TITLE'=>$item[TITLE],'REG_COUNT'=>$counts_RET[$item[ID]]['REG'][1]['COUNT'],'FREE_COUNT'=>$counts_RET[$item[ID]]['FREE'][1]['COUNT'],'REDUCED_COUNT'=>$counts_RET[$item[ID]]['REDUCED'][1]['COUNT'],'TOTAL'=>$total);
	}
	unset($RET[0]);
	ListOutput($RET,$columns,'','','',array(),array('save'=>false));
}
?>