<?php

function Currency($num,$sign='before',$red=false)
{
	$original = $num;
	if($sign=='before' && $num<0)
	{
		$negative = true;
		$num *= -1;
	}
	elseif($sign=='CR' && $num<0)
	{
		$cr = true;
		$num *= -1;
	}
	
	$num = "\$".number_format($num,2,'.',',');
	if($negative)
		$num = '-'.$num;
	elseif($cr)
		$num = $num.'CR';
	if($red && $original<0)
		$num = '<font color=red>'.$num.'</font>';

	return '<!-- '.$original.' -->'.$num;
}
?>