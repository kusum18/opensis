<?php
function DrawPNG($src,$extra='')
{
	if(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 6") || strpos($_SERVER['HTTP_USER_AGENT'], "MSIE 5.5")) 
		$img .= "<img src=\"assets/pixel_trans.gif\" $extra style=\"filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='assets/".$src."');\" >";
	else
		$img .= '<img src="assets/'.$src.'" '.$extra.'>';
	
	return $img;
}
?>