<?php
/**
* @file $Id: DailyTransactions.php 507 2007-05-11 23:41:24Z focus-sis $
* @package Focus/SIS
* @copyright Copyright (C) 2006 Andrew Schmadeke. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
* Focus/SIS is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.txt for copyright notices and details.
*/

DrawHeader(ProgramTitle());

if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
	$start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['year_start'];
else
	$start_date = '01-'.strtoupper(date('M-y'));

if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
	$end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end'];
else
	$end_date = DBDate();

echo "<FORM action=Modules.php?modname=$_REQUEST[modname] method=POST>";
DrawHeader(' &nbsp; &nbsp; <B>'._('Report Timeframe').': </B>'.PrepareDate($start_date,'_start').' - '.PrepareDate($end_date,'_end'),'<INPUT type=submit value='._('Go').'>');
echo '</FORM>';

// sort by date since the list is two lists merged and not already properly sorted
if(!$_REQUEST['LO_sort'])
	$_REQUEST['LO_sort'] = 'DATE';

Widgets('all');
$extra['functions'] = array('DEBIT'=>'_makeCurrency','CREDIT'=>'_makeCurrency','DATE'=>'ProperDate');
$fees_extra = $extra;
$fees_extra['SELECT'] .= ",f.AMOUNT AS DEBIT,'' AS CREDIT,CONCAT(f.TITLE,' ',COALESCE(f.COMMENTS,' ')) AS EXPLANATION,f.ASSIGNED_DATE AS DATE,f.ID AS ID";
$fees_extra['FROM'] .= ',BILLING_FEES f';
$fees_extra['WHERE'] .= " AND f.STUDENT_ID=s.STUDENT_ID AND f.SYEAR=ssm.SYEAR AND f.SCHOOL_ID=ssm.SCHOOL_ID AND f.ASSIGNED_DATE BETWEEN '$start_date' AND '$end_date'";

$RET = GetStuList($fees_extra);

$payments_extra = $extra;
$payments_extra['SELECT'] .= ",'' AS DEBIT,p.AMOUNT AS CREDIT,COALESCE(p.COMMENTS,' ') AS EXPLANATION,p.PAYMENT_DATE AS DATE,p.ID AS ID";
$payments_extra['FROM'] .= ',BILLING_PAYMENTS p';
$payments_extra['WHERE'] .= " AND p.STUDENT_ID=s.STUDENT_ID AND p.SYEAR=ssm.SYEAR AND p.SCHOOL_ID=ssm.SCHOOL_ID AND p.PAYMENT_DATE BETWEEN '$start_date' AND '$end_date'";

$payments_RET = GetStuList($payments_extra);

foreach($payments_RET as $payment)
{
	$RET[] = $payment;
}

$columns = array('FULL_NAME'=>_('Student'),'DEBIT'=>_('Fee'),'CREDIT'=>_('Payment'),'DATE'=>_('Date'),'EXPLANATION'=>_('Explanation'));
$link['add']['html'] = array('FULL_NAME'=>'<B>'._('Total').'</B>','DEBIT'=>'<b>'.Currency($totals['DEBIT']).'</b>','CREDIT'=>'<b>'.Currency($totals['CREDIT']).'</b>');
ListOutput($RET,$columns,_('Transaction'),_('Transactions'),$link);

function _makeCurrency($value,$column)
{	global $totals;

	$totals[$column] += $value;
	if($value)
		return Currency($value);
}
?>