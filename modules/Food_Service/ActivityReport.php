<?php

if($_REQUEST['month_date'] && $_REQUEST['day_date'] && $_REQUEST['year_date'])
	while(!VerifyDate($date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date']))
		$_REQUEST['day_date']--;
else
{
	$_REQUEST['day_date'] = date('d');
	$_REQUEST['month_date'] = strtoupper(date('M'));
	$_REQUEST['year_date'] = date('y');
	$date = $_REQUEST['day_date'].'-'.$_REQUEST['month_date'].'-'.$_REQUEST['year_date'];
}

if($_REQUEST['type'])
	$_SESSION['FSA_type'] = $_REQUEST['type'];
else
	$_SESSION['_REQUEST_vars']['type'] = $_REQUEST['type'] = $_SESSION['FSA_type'];

if($_REQUEST['type']=='staff')
{
	$tabcolor_s = '#DFDFDF'; $textcolor_s = '#999999';
	$tabcolor_u = Preferences('HEADER'); $textcolor_u = '#FFFFFF';
}
else
{
	$tabcolor_s = Preferences('HEADER'); $textcolor_s = '#FFFFFF';
	$tabcolor_u = '#DFDFDF'; $textcolor_u = '#999999';
}
DrawBC("Food Service >> ".ProgramTitle());

//$header = '<TABLE border=0 cellpadding=0 cellspacing=0 height=14><TR>';
//$header .= '<TD width=10></TD><TD>'.DrawTab('Students',"Modules.php?modname=$_REQUEST[modname]&day_date=$_REQUEST[day_date]&month_date=$_REQUEST[month_date]&year_date=$_REQUEST[year_date]&type=student",$tabcolor_s,$textcolor_s,'_circle',array('tabcolor'=>Preferences('HEADER'),'textcolor'=>'#FFFFFF')).'</TD>';
//$header .= '<TD width=10></TD><TD>'.DrawTab('Users',   "Modules.php?modname=$_REQUEST[modname]&day_date=$_REQUEST[day_date]&month_date=$_REQUEST[month_date]&year_date=$_REQUEST[year_date]&type=staff",  $tabcolor_u,$textcolor_u,'_circle',array('tabcolor'=>Preferences('HEADER'),'textcolor'=>'#FFFFFF')).'</TD>';
//$header .= '<TD width=10></TD></TR></TABLE>';

//DrawHeader(($_SESSION['FSA_type']=='staff' ? 'User ' : 'Student ').ProgramTitle(),'<TABLE bgcolor=#ffffff><TR><TD>'.$header.'</TD></TR></TABLE>');

if($_REQUEST['modfunc']=='delete')
{
	require_once('modules/Food_Service/includes/DeletePromptX.fnc.php');
	if($_REQUEST['item_id']!='')
	{
		if(DeletePromptX('transaction item'))
		{
			require_once('modules/Food_Service/includes/DeleteTransactionItem.fnc.php');
			DeleteTransactionItem($_REQUEST['transaction_id'],$_REQUEST['item_id'],$_REQUEST['type']);
			DBQuery('BEGIN; '.$sql1.'; '.$sql2.'; '.$sql3.'; COMMIT');
			unset($_REQUEST['modfunc']);
			unset($_REQUEST['delete_ok']);
			unset($_SESSION['_REQUEST_vars']['modfunc']);
			unset($_SESSION['_REQUEST_vars']['delete_ok']);
		}
	}
	else
	{
		if(DeletePromptX('transaction'))
		{
			require_once('modules/Food_Service/includes/DeleteTransaction.fnc.php');
			DeleteTransaction($_REQUEST['transaction_id'],$_REQUEST['type']);
			unset($_REQUEST['modfunc']);
			unset($_REQUEST['delete_ok']);
			unset($_SESSION['_REQUEST_vars']['modfunc']);
			unset($_SESSION['_REQUEST_vars']['delete_ok']);
		}
	}
}

if(!$_REQUEST['modfunc'])
{
$transaction_items = array('CASH'=>array(1=>array('DESCRIPTION'=>'Cash','COUNT'=>0,'AMOUNT'=>0)),
			   'CHECK'=>array(1=>array('DESCRIPTION'=>'Check','COUNT'=>0,'AMOUNT'=>0)),
			   'CREDIT CARD'=>array(1=>array('DESCRIPTION'=>'Credit Card','COUNT'=>0,'AMOUNT'=>0)),
			   'DEBIT CARD'=>array(1=>array('DESCRIPTION'=>'Debit Card','COUNT'=>0,'AMOUNT'=>0)),
			   'TRANSFER'=>array(1=>array('DESCRIPTION'=>'Transfer','COUNT'=>0,'AMOUNT'=>0)),
			   ''=>array(1=>array('DESCRIPTION'=>'n/s','COUNT'=>0,'AMOUNT'=>0))
			   );

$menus_RET = DBGet(DBQuery('SELECT TITLE FROM FOOD_SERVICE_MENUS WHERE SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'));
//echo '<pre>'; var_dump($menus_RET); echo '</pre>';
$items = DBGet(DBQuery('SELECT SHORT_NAME,DESCRIPTION,0 AS COUNT FROM FOOD_SERVICE_ITEMS WHERE SCHOOL_ID=\''.UserSchool().'\' ORDER BY SORT_ORDER'),array(),array('SHORT_NAME'));
//echo '<pre>'; var_dump($items); echo '</pre>';

$types = array('DEPOSIT'=>array('DESCRIPTION'=>'Deposit','COUNT'=>0,'AMOUNT'=>0,'ITEMS'=>$transaction_items),
	       'CREDIT'=>array('DESCRIPTION'=>'Credit','COUNT'=>0,'AMOUNT'=>0,'ITEMS'=>$transaction_items),
	       'DEBIT'=>array('DESCRIPTION'=>'Debit','COUNT'=>0,'AMOUNT'=>0,'ITEMS'=>$transaction_items)
	       );

foreach($menus_RET as $menu)
	$types += array($menu['TITLE']=>array('DESCRIPTION'=>$menu['TITLE'],'COUNT'=>0,'AMOUNT'=>0,'ITEMS'=>$items));

$type_select = 'Type<SELECT name=type_select><OPTION value=\'\'>Not Specified</OPTION>';
foreach($types as $short_name=>$type)
	$type_select .= '<OPTION value='.$short_name.($_REQUEST['type_select']==$short_name ? ' SELECTED' : '').'>'.$type['DESCRIPTION'].'</OPTION>';
$type_select .= '</SELECT>';

$staff_RET = DBGet(DBquery("SELECT STAFF_ID,CONCAT(FIRST_NAME,' ',LAST_NAME) AS FULL_NAME FROM STAFF WHERE SYEAR='".UserSyear()."' AND SCHOOLS LIKE '%,".UserSchool().",%' AND PROFILE='admin' ORDER BY LAST_NAME"));

$staff_select = 'User<SELECT name=staff_select><OPTION value=\'\'>Not Specified</OPTION>';
foreach($staff_RET as $staff)
	$staff_select .= '<OPTION value='.$staff['STAFF_ID'].($_REQUEST['staff_select']==$staff['STAFF_ID'] ? ' SELECTED' : '').'>'.$staff['FULL_NAME'].'</OPTION>';
$staff_select .= '</SELECT>';

$PHP_tmp_SELF = PreparePHP_SELF();
echo "<FORM action=$PHP_tmp_SELF method=POST>";
DrawHeaderHome(PrepareDate($date,'_date').' : '.$type_select.' : '.$staff_select.' : <INPUT type=submit value=Go>');
echo '</FORM>';

if($_REQUEST['type_select'])
	$where = "AND fst.SHORT_NAME='".$_REQUEST['type_select']."' ";

if($_REQUEST['staff_select'])
	$where = "AND fst.SELLER_ID='".$_REQUEST['staff_select']."' ";

if($_REQUEST['detailed_view']!='true')
	DrawHeader("<A HREF=".PreparePHP_SELF($_REQUEST,array(),array('detailed_view'=>'true')).'>Detailed View</A>');
else
	DrawHeader("<A HREF=".PreparePHP_SELF($_REQUEST,array(),array('detailed_view'=>'false')).'>Original View</A>');

include('modules/Food_Service/'.($_REQUEST['type']=='staff' ? 'Users' : 'Students').'/ActivityReport.php');
//echo '<pre>'; var_dump($RET); echo '</pre>';

//echo '<pre>'; var_dump($types); echo '</pre>';
if($_REQUEST['detailed_view']=='true')
{
	$LO_types = array(array(array()));
	foreach($types as $type)
		if($type['COUNT'])
		{
			$LO_types[] = array(array('DESCRIPTION'=>$type['DESCRIPTION'],'DETAIL'=>'','COUNT'=>$type['COUNT'],'AMOUNT'=>number_format($type['AMOUNT'],2)));
			foreach($type['ITEMS'] as $item)
				if($item[1]['COUNT'])
					$LO_types[last($LO_types)][] = array('DESCRIPTION'=>$type['DESCRIPTION'],'DETAIL'=>$item[1]['DESCRIPTION'],'COUNT'=>$item[1]['COUNT'],'AMOUNT'=>number_format($item[1]['AMOUNT'],2));
		}
	$types_columns = array('DESCRIPTION'=>'Description','DETAIL'=>'Detail','COUNT'=>'Count','AMOUNT'=>'Amount');
	$types_group = array('DESCRIPTION');
}
else
{
	$LO_types = array(array());
	foreach($types as $type)
		if($type['COUNT'])
			$LO_types[] = array('DESCRIPTION'=>$type['DESCRIPTION'],'COUNT'=>$type['COUNT'],'AMOUNT'=>number_format($type['AMOUNT'],2));
	$types_columns = array('DESCRIPTION'=>'Description','COUNT'=>'Count','AMOUNT'=>'Amount');
}
unset($LO_types[0]);
//echo '<pre>'; var_dump($LO_types); echo '</pre>';

ListOutput($LO_types,$types_columns,'Transaction Type','Transaction Types',false,$types_group,array('save'=>false,'search'=>false,'print'=>false));

ListOutput($RET,$columns,'Transaction','Transactions',$link,$group,array('save'=>false,'search'=>false,'print'=>false));
}

function last(&$array)
{
	end($array);
	return key($array);
}

function bump_count($value)
{	global $THIS_RET,$types;

	if($types[$value])
	{
		$types[$value]['COUNT']++;
		$types[$value]['AMOUNT'] += $THIS_RET['AMOUNT'];
	} else
		$types += array($value=>array('DESCRIPTION'=>"<FONT color=red>$value</FONT>",'COUNT'=>1,'ITEMS'=>array(),'AMOUNT'=>$THIS_RET['AMOUNT']));
	return $value;
}

function bump_items_count($value)
{	global $THIS_RET,$types;

	if($types[$THIS_RET['TRANSACTION_SHORT_NAME']]['ITEMS'][$value])
	{
		$types[$THIS_RET['TRANSACTION_SHORT_NAME']]['ITEMS'][$value][1]['COUNT']++;
		$types[$THIS_RET['TRANSACTION_SHORT_NAME']]['ITEMS'][$value][1]['AMOUNT'] += $THIS_RET['AMOUNT'];;
	}
	else
		$types[$THIS_RET['TRANSACTION_SHORT_NAME']]['ITEMS'] += array($value=>array(1=>array('DESCRIPTION'=>"<FONT color=red>$value</FONT>",'COUNT'=>1,'AMOUNT'=>$THIS_RET['AMOUNT'])));
	return $value;
}
?>
