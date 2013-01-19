<?php
/**
* @file $Id: ReferralLog.php 405 2007-01-22 21:10:19Z focus-sis $
* @package Focus/SIS
* @copyright Copyright (C) 2006 Andrew Schmadeke. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
* Focus/SIS is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.txt for copyright notices and details.
*/

$categories_RET = DBGet(DBQuery("SELECT f.ID,u.TITLE,u.SELECT_OPTIONS,f.DATA_TYPE,u.SORT_ORDER FROM DISCIPLINE_FIELDS f,DISCIPLINE_FIELD_USAGE u WHERE f.DATA_TYPE!='multiple_checkbox' AND u.DISCIPLINE_FIELD_ID=f.ID ORDER BY ".db_case(array('DATA_TYPE',"'textarea'","'1'","'0'")).",SORT_ORDER"),array(),array('ID'));

$extra['new'] = true;
$extra['second_col'] .= '<fieldset><legend>'._('Include in Referral Log').':</legend><TABLE>';

$extra['second_col'] .= '<TR><TD><INPUT type=checkbox name=elements[ENTRY_DATE] value=Y CHECKED>'._('Entry Date').'</TD>';
$extra['second_col'] .= '<TD><INPUT type=checkbox name=elements[STAFF_ID] value=Y CHECKED>'._('Reporter').'</TD></TR>';
foreach($categories_RET as $id=>$category)
{
	if($i%2==0)
		$extra['second_col'] .= '</TR><TR>';
	$extra['second_col'] .= '<TD><INPUT type=checkbox name=elements[CATEGORY_'.$id.'] value=Y'.($category[1]['DATA_TYPE']=='textarea'?' CHECKED':'').'>'.$category[1]['TITLE'].'</TD>';
	$i++;
}
$extra['second_col'] .= '</TABLE></fieldset>';

$templates_RET = DBGet(DBQuery("SELECT ID,TITLE FROM HEADER_TEMPLATES ORDER BY TITLE"));
foreach($templates_RET as $template)
	$options .= "<OPTION value=$template[ID]>".$template['TITLE'].'</OPTION>';
$extra['second_col'] .= '<TABLE><TR><TD width=100> &nbsp; </TD><TD align='.ALIGN_RIGHT.'>'._('Document Template').' </TD><TD><SELECT name=_template_id><OPTION value="">'._('None').'</OPTION>'.$options.'</SELECT></TD></TR></TABLE>';

Widgets('all');
$extra['force_search'] = true;


if(!$_REQUEST['search_modfunc'] || $_REQUEST['search_modfunc']=='search' || $_FOCUS['modules_search'])
{
	DrawHeader(ProgramTitle());
	
	Search('student_id',$extra);
}
else
{
	if($_REQUEST['month_discipline_entry_begin'] && $_REQUEST['day_discipline_entry_begin'] && $_REQUEST['year_discipline_entry_begin'])
	{
		$start_date = $_REQUEST['day_discipline_entry_begin'].'-'.$_REQUEST['month_discipline_entry_begin'].'-'.$_REQUEST['year_discipline_entry_begin'];
		if(!VerifyDate($start_date))
			unset($start_date);
		$end_date = $_REQUEST['day_discipline_entry_end'].'-'.$_REQUEST['month_discipline_entry_end'].'-'.$_REQUEST['year_discipline_entry_end'];
		if(!VerifyDate($end_date))
			unset($end_date);
	}

	if(!$_REQUEST['_FOCUS_PDF'])
	{
		DrawHeader(ProgramTitle());
		echo '<BR><BR>';
	}
	
	foreach($_REQUEST['elements'] as $column=>$Y)
	{
		$extra['SELECT'] .= ',r.'.$column;
	}

	$extra['FROM'] .= ',DISCIPLINE_REFERRALS r ';
	$extra['WHERE'] .= " AND r.STUDENT_ID=ssm.STUDENT_ID AND r.SYEAR=ssm.SYEAR ";
	if(strpos($extra['FROM'],'DISCIPLINE_REFERRALS dr')!==false)
		$extra['WHERE'] .= ' AND r.ID=dr.ID';
	
	$extra['group'] = array('STUDENT_ID');
	$extra['ORDER'] = ',r.ENTRY_DATE';
	
	$RET = GetStuList($extra);

	if(count($RET))
	{
		foreach($RET as $student_id=>$referrals)
		{
			unset($_FOCUS['DrawHeader']);
			DrawHeader(Config('TITLE').' '._('Discipline Log'));

			DrawHeader($referrals[1]['FULL_NAME'],$referrals[1]['STUDENT_ID']);
			DrawHeader(GetSchool(UserSchool()),$courses[1]['GRADE_ID']);
			if($start_date && $end_date)
				DrawHeader(ProperDate($start_date).' - '.ProperDate($end_date));
			else
				DrawHeader(_('School Year').': '.UserSyear().' - '.(UserSyear()+1));
			echo '<BR>';

			foreach($referrals as $referral)
			{
				echo '<TABLE cellpadding=5><TR>';
				if($_REQUEST['elements']['ENTRY_DATE'])
					echo '<TD><small><font color=gray>'._('Date').': </font></small><b>'.ProperDate($referral['ENTRY_DATE']).'</b></TD>';
				if($_REQUEST['elements']['STAFF_ID'])
					echo '<TD><small><font color=gray>'._('Reporter').': </font></small><b>'.GetTeacher($referral['STAFF_ID']).'</b></TD>';

				$end_tr = false;
				foreach($_REQUEST['elements'] as $column=>$Y)
				{
					if($column=='ENTRY_DATE' || $column=='STAFF_ID')
						continue;

					if($categories_RET[substr($column,9)][1]['DATA_TYPE']=='textarea' && !$end_tr)
					{
						$end_tr = true;
						echo '</TR></TABLE>';
					}
					elseif($categories_RET[substr($column,9)][1]['DATA_TYPE']=='textarea')
						echo '<BR>';
					
					if($categories_RET[substr($column,9)][1]['DATA_TYPE']!='textarea')
						echo '<TD><small><font color=gray>'.$categories_RET[substr($column,9)][1]['TITLE'].': </font></small><b> '.$referral[$column].'</b></TD>';
					else
						echo $referral[$column].'<BR>';
				}
				echo '<HR>';
			}
			echo '<BR>';
			echo '<!-- NEW PAGE -->';
		}
	}
	else
		BackPrompt(_('No Students were found.'));
}
?>