<?php

function DrawHeaderHome($left='',$right='',$center='')
{	global $_CENTRE;

	if(!isset($_CENTRE['DrawHeader']))
		$_CENTRE['DrawHeader'] = '';

	if($_CENTRE['DrawHeader'] == '')
	{
		$attribute = 'B';
		$font_color = '436477';
	}
	else
	{
		$attribute = 'FONT size=-1';
		$font_color = '000000';
	}

	echo '<TABLE width=100%  border=0 cellpadding=5 cellspacing=5 align=center><TR>';
	if($left)
		echo '<TD '.$_CENTRE['DrawHeader'].' align=left>&nbsp;<font color=#'.$font_color.'><'.$attribute.'>'.$left.'</'.substr($attribute,0,4).'></font></TD>';
	if($center)
		echo '<TD '.$_CENTRE['DrawHeader'].' align=center><font color=#'.$font_color.'><'.$attribute.'>'.$center.'</'.$attribute.'></font></TD>';
	if($right)
		echo '<TD align=right '.$_CENTRE['DrawHeader'].'><font color=#'.$font_color.'><'.$attribute.'>'.$right.'</'.substr($attribute,0,4).'></font></TD>';
	echo '</TR><tr><td class=break colspan=3></td></tr></TABLE>';

	if($_CENTRE['DrawHeaderHome'] == '' && !$_REQUEST['_CENTRE_PDF'])
		$_CENTRE['DrawHeaderHome'] = ' style="border:0;border-style: none none none none;"';
	//$_CENTRE['DrawHeader'] = '';
	else
	//	$_CENTRE['DrawHeader'] = ' style="border:1;border-style: none none solid none;"';
		$_CENTRE['DrawHeaderHome'] = '';
}
?>