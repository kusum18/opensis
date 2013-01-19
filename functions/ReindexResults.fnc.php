<?php
function ReindexResults($array)
{
 	$i=1;
	foreach($array as $value)
	{
		$new[$i]=$value;
		$i++;
	}
	return $new;
}
?>