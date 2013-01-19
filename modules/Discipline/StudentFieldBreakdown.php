<?php
/**
* @file $Id: StudentFieldBreakdown.php 507 2007-05-11 23:41:24Z focus-sis $
* @package Focus/SIS
* @copyright Copyright (C) 2006 Andrew Schmadeke. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
* Focus/SIS is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.txt for copyright notices and details.
*/

include($staticpath."/assets/SWF/charts.php");

if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
{
	while(!VerifyDate($start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['year_start']))
		$_REQUEST['day_start']--;
}
else
{
	$min_date = DBGet(DBQuery("SELECT min(SCHOOL_DATE) AS MIN_DATE FROM ATTENDANCE_CALENDAR WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
	if($min_date[1]['MIN_DATE'])
		$start_date = $min_date[1]['MIN_DATE'];
	else
		$start_date = '01-'.strtoupper(date('M-y'));
}

if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
{
	while(!VerifyDate($end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end']))
		$_REQUEST['day_end']--;
}
else
	$end_date = DBDate();

if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
	$end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end'];
else
	$end_date = DBDate();

if($_REQUEST['category_id'])
{
	$category_RET = DBGet(DBQuery("SELECT TITLE,SELECT_OPTIONS AS OPTIONS,TYPE FROM CUSTOM_FIELDS WHERE ID='".$_REQUEST['category_id']."'"));
	$category_RET[1]['OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$category_RET[1]['OPTIONS']));
	$category_RET[1]['OPTIONS'] = explode("\r",$category_RET[1]['OPTIONS']);
}

if(!$_REQUEST['chart_type'])
	$_REQUEST['chart_type'] = 'column';

if($_REQUEST['modfunc']=='search')
{
	echo '<BR>';

	Widgets('all');
	$extra['force_search'] = true;
	$extra['search_title'] = _('Advanced');
	$extra['action'] = "&category_id=$_REQUEST[category_id]&chart_type=".str_replace(' ','+',$_REQUEST['chart_type'])."&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end]&modfunc=&search_modfunc= target=body onsubmit='window.close();'";
	Search('student_id',$extra);

/*	PopTable('header',_('Advanced'));
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&category_id=$_REQUEST[category_id]&chart_type=".str_replace(' ','+',$_REQUEST['chart_type'])."&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end] method=POST target=body onsubmit='window.close();'>";
	echo '<TABLE>';
	Search('general_info');
	Search('student_fields');
	echo '<CENTER><INPUT type=submit value='._('Submit').'></CENTER>';
	echo '</FORM>';
	PopTable('footer');
*/
}

if($_REQUEST['modfunc']=='SendChartData' || $_REQUEST['chart_type']=='list')
{
	$chart['chart_type'] = $_REQUEST['chart_type'];
	$chart['series_switch'] = true;
	if($category_RET[1]['TYPE']=='numeric')
		$width = 400;
	else
		$width = 20*count($category_RET[1]['OPTIONS']) < 400 ? 400 : (20*count($category_RET[1]['OPTIONS']));
	if($_REQUEST['chart_type']=='column')
	{
		$chart['legend_rect'] = array('x'=>-1000,'y'=>-1000);
		$chart['chart_value'] = array('position'=>'outside','size'=>15);
		$chart['axis_category'] = array('orientation'=>'diagonal_up','size'=>13);
		$chart['axis_value']['size'] = 13;
		$height = 355;
		$chart['chart_rect'] = array('x'=>100,'y'=>45,'width'=>$width,'height'=>$height);
	}
	elseif($_REQUEST['chart_type']=='3d pie')
	{
		$chart['legend_label']['size'] = 13;
		$height = $width-100;
		$width = 400;
		$chart['chart_rect'] = array('x'=>250,'y'=>45,'width'=>$width,'height'=>$height);
		$chart['legend_bg'] = array ('bg_color'=>Preferences('COLOR'),'border_color'=>'000000','border_alpha'=>0,'border_thickness'=>0);
	}
	$chart['draw_text']  = array(array('x'=>0,'y'=>5,'width'=>$width+200,'height'=>200,'h_align'=>'center','v_align'=>'top','rotation'=>0,'text'=>wordwrap($category_RET[1]['TITLE'].' '._('Breakdown'),30,"\r"),'font'=>'Arial','color'=>'000000','alpha'=>25,'size'=>35));
	$chart['chart_bg'] = array('positive_color'=>Preferences('COLOR'),'negative_color'=>'000000');
	$chart['series_color'] = array("00ff88","ffaa00","44aaff","aa00ff","4e627c","844648","ddaa41","88dd11","4e62dd","ff8811","4d4d4d","4e627c","c89341","4c6b41","5a4b6e","3b5743","303d3d","4c5e6f","564546","784e3a","677b75");
	
	if($category_RET[1]['TYPE']=='select')
	{
		$extra = array();
		$extra['SELECT_ONLY'] = "COALESCE(s.CUSTOM_".$_REQUEST['category_id'].",'*BLANK*') AS TITLE,COUNT(*) AS COUNT ";
		$extra['FROM'] = ',DISCIPLINE_REFERRALS dr ';
		$extra['WHERE'] = "AND dr.STUDENT_ID=ssm.STUDENT_ID AND dr.SCHOOL_ID=ssm.SCHOOL_ID AND dr.ENTRY_DATE BETWEEN '$start_date' AND '$end_date' ";
		$extra['GROUP'] = 'CUSTOM_'.$_REQUEST['category_id'];
		$extra['group'] = array('TITLE');
		Widgets('all');
		$totals_RET = GetStuList($extra);
//		$sql = "SELECT dr.CATEGORY_".$_REQUEST['category_id']." AS TITLE,COUNT(*) AS COUNT FROM DISCIPLINE_REFERRALS dr,STUDENTS s,STUDENT_ENROLLMENT ssm WHERE ssm.STUDENT_ID=s.STUDENT_ID AND dr.ENTRY_DATE BETWEEN '$start_date' AND '$end_date' AND dr.SCHOOL_ID=ssm.SCHOOL_ID AND dr.SCHOOL_ID='".UserSchool()."' ";
//		$sql = appendSQL($sql);
//		$sql .= " GROUP BY CATEGORY_".$_REQUEST['category_id'];

		//$totals_RET = DBGet(DBQuery($sql),array(),array('TITLE'));
		$chart['chart_data'][0][0] = '';
		$chart['chart_data'][1][0] = 'Series';

		$chart['chart_data'][0][] = _('No Value');
		$chart['chart_data'][1][] = $totals_RET['*BLANK*'][1]['COUNT'];			
		foreach($category_RET[1]['OPTIONS'] as $option)
		{
			$chart['chart_data'][0][] = $option;
			$chart['chart_data'][1][] = $totals_RET[$option][1]['COUNT'];			
		}
	}
	elseif($category_RET[1]['TYPE']=='multiple')
	{
		//$referrals_RET = DBGet(DBQuery("SELECT CATEGORY_".$_REQUEST['category_id']." AS TITLE FROM DISCIPLINE_REFERRALS WHERE ENTRY_DATE BETWEEN '$start_date' AND '$end_date' AND SCHOOL_ID='".UserSchool()."'"));
		$extra['SELECT_ONLY'] = "CUSTOM_".$_REQUEST['category_id']." AS TITLE ";
		$extra['FROM'] = ',DISCIPLINE_REFERRALS dr ';
		$extra['WHERE'] = "AND dr.STUDENT_ID=ssm.STUDENT_ID AND dr.SCHOOL_ID=ssm.SCHOOL_ID AND dr.ENTRY_DATE BETWEEN '$start_date' AND '$end_date' ";
		Widgets('all');
		$referrals_RET = GetStuList($extra);

		$chart['chart_data'][0][0] = '';
		$chart['chart_data'][1][0] = 'Series';

		foreach($referrals_RET as $referral)
		{
			$referral['TITLE'] = explode("||",trim($referral['TITLE'],'|'));
			foreach($referral['TITLE'] as $option)
				$options_count[$option]++;
		}

		foreach($category_RET[1]['OPTIONS'] as $option)
		{
			$chart['chart_data'][0][] = $option;
			$chart['chart_data'][1][] = $options_count[$option];
		}		
	}
	elseif($category_RET[1]['TYPE']=='radio')
	{
		$extra = array();
		$extra['SELECT_ONLY'] = db_case(array("s.CUSTOM_".$_REQUEST['category_id'],"'Y'","'"._('Yes')."'","'"._('No')."'"))." AS TITLE,COUNT(*) AS COUNT ";
		$extra['FROM'] = ',DISCIPLINE_REFERRALS dr ';
		$extra['WHERE'] = "AND dr.STUDENT_ID=ssm.STUDENT_ID AND dr.SCHOOL_ID=ssm.SCHOOL_ID AND dr.ENTRY_DATE BETWEEN '$start_date' AND '$end_date' ";
		$extra['GROUP'] = 'CUSTOM_'.$_REQUEST['category_id'];
		$extra['group'] = array('TITLE');
		$extra['DEBUG'] = true;
		Widgets('all');
		$totals_RET = GetStuList($extra);
//		$sql = "SELECT dr.CATEGORY_".$_REQUEST['category_id']." AS TITLE,COUNT(*) AS COUNT FROM DISCIPLINE_REFERRALS dr,STUDENTS s,STUDENT_ENROLLMENT ssm WHERE ssm.STUDENT_ID=s.STUDENT_ID AND dr.ENTRY_DATE BETWEEN '$start_date' AND '$end_date' AND dr.SCHOOL_ID=ssm.SCHOOL_ID AND dr.SCHOOL_ID='".UserSchool()."' ";
//		$sql = appendSQL($sql);
//		$sql .= " GROUP BY CATEGORY_".$_REQUEST['category_id'];

		//$totals_RET = DBGet(DBQuery($sql),array(),array('TITLE'));
		$chart['chart_data'][0][0] = '';
		$chart['chart_data'][1][0] = 'Series';

		$chart['chart_data'][0][0] = _('Yes');
		$chart['chart_data'][1][0] = $totals_RET['Yes'][1]['COUNT'];			
		$chart['chart_data'][0][0] = _('No');
		$chart['chart_data'][1][0] = $totals_RET['No'][1]['COUNT'];			
	}
	elseif($category_RET[1]['TYPE']=='numeric')
	{
		$chart['axis_category']['orientation'] = '';
		$chart['chart_data'][0][0] = '';
		$chart['chart_data'][1][0] = 'Series';

		//$max_min_RET = DBGet(DBQuery("SELECT COALESCE(max(CATEGORY_".$_REQUEST['category_id']."),0) as MAX,COALESCE(min(CATEGORY_".$_REQUEST['category_id']."),0) AS MIN FROM DISCIPLINE_REFERRALS WHERE ENTRY_DATE BETWEEN '$start_date' AND '$end_date' AND SCHOOL_ID='".UserSchool()."'"));
		$extra['SELECT_ONLY'] = "COALESCE(max(CUSTOM_".$_REQUEST['category_id']."),0) as MAX,COALESCE(min(CUSTOM_".$_REQUEST['category_id']."),0) AS MIN ";
		$extra['FROM'] = ',DISCIPLINE_REFERRALS dr';
		$extra['WHERE'] = " AND dr.STUDENT_ID=ssm.STUDENT_ID AND dr.SCHOOL_ID=ssm.SCHOOL_ID AND dr.ENTRY_DATE BETWEEN '$start_date' AND '$end_date' ";
		$max_min_RET = GetStuList($extra);

		$diff = $max_min_RET[1]['MAX'] - $max_min_RET[1]['MIN'];

		if($diff>5)
		{
			for($i=1;$i<=5;$i++)
			{
				$chart['chart_data'][0][$i] = (ceil($diff/5)*($i-1)).' - '.((ceil($diff/5)*$i)-1);
				$mins[$i] = (ceil($diff/5)*($i-1));
				$chart['chart_data'][1][$i] = 0;
			}
			$chart['chart_data'][0][$i-1] = (ceil($diff/5)*($i-2)).'+';
			$mins[$i] = (ceil($diff/5)*($i-1));
		}
		
		//$referrals_RET = DBGet(DBQuery("SELECT COALESCE(CATEGORY_".$_REQUEST['category_id'].",0) AS TITLE FROM DISCIPLINE_REFERRALS WHERE ENTRY_DATE BETWEEN '$start_date' AND '$end_date' AND SCHOOL_ID='".UserSchool()."'"),array('TITLE'=>'_makeNumeric'));
		$extra['SELECT_ONLY'] = "CUSTOM_".$_REQUEST['category_id']." AS TITLE";
		$extra['FROM'] = ",DISCIPLINE_REFERRALS dr";
		$extra['WHERE'] = " AND dr.STUDENT_ID=ssm.STUDENT_ID AND dr.SCHOOL_ID=ssm.SCHOOL_ID AND dr.ENTRY_DATE BETWEEN '$start_date' AND '$end_date' ";
		$extra['functions'] = array('TITLE'=>'_makeNumeric');
		Widgets('all');
		$referrals_RET = GetStuList($extra);
	}
	if($_FOCUS['SearchTerms'])
		$chart['draw_text'][] = array('x'=>0,'y'=>35,'width'=>$width+200,'height'=>100,'h_align'=>'center','v_align'=>'top','rotation'=>0,'text'=>strip_tags(str_replace('<BR>',"\n",$_FOCUS['SearchTerms'])),'font'=>'Arial','color'=>'000000','alpha'=>25,'size'=>20);
	
	if($_REQUEST['chart_type']!='list')
		SendChartData($chart);
}

if(!$_REQUEST['modfunc'])
{
	unset($_REQUEST['PHPSESSID']);
	echo '<FORM action=Modules.php?modname='.$_REQUEST['modname'].'&amp;chart_type='.str_replace(' ','+',$_REQUEST['chart_type']).' method=POST>';
	DrawHeader(ProgramTitle());
	
	$fields_RET = DBGet(DBQuery("SELECT ID,TITLE,SELECT_OPTIONS AS OPTIONS,CATEGORY_ID FROM CUSTOM_FIELDS WHERE TYPE NOT IN ('textarea','text','date','log','holder') ORDER BY SORT_ORDER,TITLE"),array(),array('CATEGORY_ID'));
	$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM STUDENT_FIELD_CATEGORIES"),array(),array('ID'));
	$select = '<SELECT name=category_id onchange="this.form.submit();"><OPTION value="">'._('Please choose a student field').'</OPTION>';
	
	if(count($fields_RET))
	{
		foreach($fields_RET as $category_id=>$fields)
		{
			$select .= '<OPTGROUP label='.$categories_RET[$category_id][1]['TITLE'].'>';
			foreach($fields as $field)
				$select .= '<OPTION value='.$field['ID'].(($_REQUEST['category_id']==$field['ID'])?' SELECTED':'').'>'.$field['TITLE'].'</OPTION>';
			$select .= '</OPTGROUP>';
		}
	}
	$select .= '</SELECT>';
	$advanced_link = " <A HREF=# onclick='remote = window.open(\"Modules.php?modname=$_REQUEST[modname]&modfunc=search&category_id=$_REQUEST[category_id]&chart_type=$_REQUEST[chart_type]&day_start=$_REQUEST[day_start]&day_end=$_REQUEST[day_end]&month_start=$_REQUEST[month_start]&month_end=$_REQUEST[month_end]&year_start=$_REQUEST[year_start]&year_end=$_REQUEST[year_end]&include_top=false\",\"\",\"scrollbars=yes,resizable=yes,width=700,height=600\"); remote.opener = window;'>"._('Advanced')."</A>";

	DrawHeader($select);
	DrawHeader(' &nbsp; &nbsp; <B>'._('Report Timeframe').': </B>'.PrepareDate($start_date,'_start').' - '.PrepareDate($end_date,'_end').$advanced_link,'<INPUT type=submit value='._('Go').'>');

	if($category_RET[1]['TYPE']=='numeric')
		$width = 600;
	else
		$width = 20*count($category_RET[1]['OPTIONS']) < 400 ? 600 : 20*count($category_RET[1]['OPTIONS'])+200;
	if($_REQUEST['chart_type']=='column')
		$height = 650;
	elseif($_REQUEST['chart_type']=='3d pie')
	{
		$height = $width-200;
		$width = 700;
	}
	
	echo '<BR>';
	if($_REQUEST['category_id'])
	{
		$tmp_REQUEST = $_REQUEST;
		unset($tmp_REQUEST['chart_type']);
		$link = PreparePHP_SELF($tmp_REQUEST);
		$tabs = array(array('title'=>_('Column'),'link'=>str_replace($_REQUEST['modname'],$_REQUEST['modname'].'&amp;chart_type=column',$link)),array('title'=>_('Pie'),'link'=>str_replace($_REQUEST['modname'],$_REQUEST['modname'].'&amp;chart_type=3d+pie',$link)),array('title'=>_('List'),'link'=>str_replace($_REQUEST['modname'],$_REQUEST['modname'].'&amp;chart_type=list',$link)));

		$_FOCUS['selected_tab'] = str_replace($_REQUEST['modname'],$_REQUEST['modname'].'&amp;chart_type='.str_replace(' ','+',$_REQUEST['chart_type']),$link);
		PopTable('header',$tabs,'',0);

		if($_REQUEST['chart_type']=='list')
		{
			$chart_data = array('0'=>'');

			// IGNORE THE 'Series' ELEMENT
			unset($chart['chart_data'][1][0]);
			foreach($chart['chart_data'][1] as $key=>$value)
				$chart_data[] = array('TITLE'=>$chart['chart_data'][0][$key],'VALUE'=>$value);
			unset($chart_data[0]);
			ListOutput($chart_data,array('TITLE'=>_('Option'),'VALUE'=>_('Number of Referrals')),_('Option'),_('Options'));
		}
		else
		{
			$_REQUEST['modfunc'] = 'SendChartData';
			$_REQUEST['_FOCUS_PDF'] = 'true';
			echo InsertChart("http://".$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],'/'))."/assets/SWF/charts.swf",str_replace('&amp;','&',PreparePHP_SELF()),$width,$height,'FFFFFF');
			unset($_REQUEST['_FOCUS_PDF']);
		}
		PopTable('footer');
	}
	echo '</FORM>';
}

function _makeNumeric($number,$column)
{	global $max_min_RET,$chart,$diff,$mins;
	
	if(!$number)
		$number=0;
	if($diff==0)
	{
		$chart['chart_data'][0][1] = $number;
		$chart['chart_data'][1][1]++;
	}
	elseif($diff<5)
	{
		$chart['chart_data'][0][((int) $number - (int) $max_min_RET[1]['MIN']+1)] = (int) $number;
		$chart['chart_data'][1][((int) $number - (int) $max_min_RET[1]['MIN']+1)]++;
	}
	else
	{
		for($i=1;$i<=5;$i++)
		{
			if(($number>=$mins[$i] && $number<$mins[$i+1]) || $i==5)
			{
				$chart['chart_data'][1][$i]++;
				break;
			}
		}
	}
	
	return;
}
?>
