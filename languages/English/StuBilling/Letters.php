<?php
define('PRINT_LETTER',true);

if($_REQUEST[modfunc]=='list')
{
	$_REQUEST[letter] = str_replace("\n",'<BR>',$_REQUEST[letter]);
	//$_REQUEST[letter] = str_replace("\r",'<BR>',$_REQUEST[letter]);
	$_REQUEST[letter] = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$_REQUEST[letter]);
	
	if(strpos($_REQUEST[letter],'__STUDENT__')!==false)
		$LETTER[student] = true;
	if(strpos($_REQUEST[letter],'__STUDENT_ID__')!==false)
		$LETTER[student_id] = true;
	if(strpos($_REQUEST[letter],'__PARENTS__')!==false)
		$LETTER[parents] = true;
	if(strpos($_REQUEST[letter],'__BALANCE__')!==false)
		$LETTER[balance] = true;
}

include('modules/StuBilling/Invoices.php');

?>