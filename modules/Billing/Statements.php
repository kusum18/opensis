<?php
/**
* @file $Id: Statements.php 422 2007-02-10 22:08:22Z focus-sis $
* @package Focus/SIS
* @copyright Copyright (C) 2006 Andrew Schmadeke. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
* Focus/SIS is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.txt for copyright notices and details.
*/

Widgets('all');
Widgets('mailing_labels');
Widgets('document_template');
$extra['force_search'] = true;

if(!$_REQUEST['search_modfunc'] || $_REQUEST['search_modfunc']=='search' || $_FOCUS['modules_search'])
{
	DrawHeader(ProgramTitle());

	$extra['new'] = true;
	$extra['action'] .= "&_FOCUS_PDF=true";
	Search('student_id',$extra);
}
else
{
	// For the Student Fees / Student Payments programs
	$_REQUEST['print_statements'] = true;
	if($_REQUEST['mailing_labels']=='Y')
		$extra['group'][] = 'ADDRESS_ID';	
	
	$RET = GetStuList($extra);

	if(count($RET))
	{
		$handle = PDFStart();
		foreach($RET as $student)
		{
			if($_REQUEST['mailing_labels']=='Y')
			{
				foreach($student as $address)
				{
					echo '<BR><BR><BR>';
					unset($_FOCUS['DrawHeader']);
					DrawHeader(Config('TITLE').' '._('Statement'));
					DrawHeader($address['FULL_NAME'],$address['STUDENT_ID']);
					DrawHeader($address['GRADE_ID']);
					DrawHeader(GetSchool(UserSchool()));
					DrawHeader(ProperDate(DBDate()));
		
					echo '<BR><BR><TABLE width=100%><TR><TD width=50> &nbsp; </TD><TD>'.$address[1]['MAILING_LABEL'].'</TD></TR></TABLE><BR>';
					
					$_SESSION['student_id'] = $address['STUDENT_ID'];
					include($staticpath.'modules/Billing/StudentFees.php');
					include($staticpath.'modules/Billing/StudentPayments.php');
					echo '<!-- NEW PAGE -->';				
				}
			}
			else
			{
				$_SESSION['student_id'] = $student['STUDENT_ID'];
				unset($_FOCUS['DrawHeader']);
				DrawHeader(Config('TITLE').' '._('Statement'));
				DrawHeader($student['FULL_NAME'],$student['STUDENT_ID']);
				DrawHeader($student['GRADE_ID']);
				DrawHeader(GetSchool(UserSchool()));
				DrawHeader(ProperDate(DBDate()));

				include($staticpath.'modules/Billing/StudentFees.php');
				include($staticpath.'modules/Billing/StudentPayments.php');
				echo '<!-- NEW PAGE -->';
			}
		}
		unset($_SESSION['student_id']);
		PDFStop($handle);
	}
	else
		BackPrompt(_('No Students were found.'));
}
?>