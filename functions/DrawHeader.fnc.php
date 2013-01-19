<?php

function DrawHeader($left='',$right='',$center='')
{	global $_CENTRE;

	if(!isset($_CENTRE['DrawHeader']))
		$_CENTRE['DrawHeader'] = '';

	if($_CENTRE['DrawHeader'] == '')
	{
		$attribute = 'b';
		$font_color = '';
		
	}
	else
	{
		$attribute = '';
		$font_color = '';
	}

	echo '<TABLE width=100%  border=0 cellpadding=5 cellspacing=5 align=center><TR>';
	if($left)
		echo '<TD '.$_CENTRE['DrawHeader'].' align=left class=drawheader>&nbsp;<'.$attribute.'>'.$left.'</'.substr($attribute,0,4).'></TD>';
	if($center)
		echo '<TD '.$_CENTRE['DrawHeader'].' align=center class=drawheader ><'.$attribute.'>'.$center.'</'.$attribute.'></TD>';
	if($right)
		echo '<TD align=right class=drawheader'.$_CENTRE['DrawHeader'].' ><'.$attribute.'>'.$right.'</'.substr($attribute,0,4).'></TD>';
	echo '</TR></TABLE>';

	if($_CENTRE['DrawHeaderHome'] == '' && !$_REQUEST['_CENTRE_PDF'])
		$_CENTRE['DrawHeaderHome'] = ' style="border:0;border-style: none none none none;"';
	//$_CENTRE['DrawHeader'] = '';
	else
	//	$_CENTRE['DrawHeader'] = ' style="border:1;border-style: none none solid none;"';
		$_CENTRE['DrawHeaderHome'] = '';
}
?>