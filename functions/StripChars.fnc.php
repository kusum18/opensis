<?php

function StripChars($value,$chars)
{
	foreach($chars as $char)
		$value = str_replace($char,'',$value);
		
	return $value;
}
?>