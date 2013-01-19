<?php
// example:
//
//	if(DeletePrompt('Title'))
//	{
//		DBQuery("DELETE FROM BOK WHERE id='$_REQUEST[benchmark_id]'");
//	}


function DeletePromptX($title,$action='Delete')
{
	$PHP_tmp_SELF = PreparePHP_SELF($_REQUEST,array('delete_ok','delete_cancel'));

	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header','Confirm'.(strpos($action,' ')===false?' '.ucwords($action):''));
//		echo "<CENTER><h4>Are You Sure You Want to $action that $title?</h4><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST><INPUT type=submit value=OK></FORM><FORM action=$PHP_tmp_SELF&delete_cancel=1 METHOD=POST><INPUT type=submit value=Cancel></FORM></CENTER>";
		echo "<CENTER><h4>Are You Sure You Want to $action that ".ucwords($title)."?</h4><FORM action=$PHP_tmp_SELF METHOD=POST><INPUT type=submit name=delete_ok value=OK><INPUT type=submit name=delete_cancel value=Cancel></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	if($_REQUEST['delete_ok']) { unset($_REQUEST['delete_ok']); unset($_REQUEST['modfunc']); return true; }
	if($_REQUEST['delete_cancel']) { unset($_REQUEST['delete_cancel']); unset($_REQUEST['modfunc']); return false; }
	return false;
}

function PromptX($title='Confirm',$question='',$message='',$pdf='')
{
	$PHP_tmp_SELF = PreparePHP_SELF($_REQUEST,array('delete_ok'),$pdf==true?array('_CENTRE_PDF'=>true):array());

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
		echo "<CENTER><h4>$question</h4><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit value=OK><INPUT type=button name=delete_cancel value=Cancel onClick='javascript:history.back()'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;
}
?>
