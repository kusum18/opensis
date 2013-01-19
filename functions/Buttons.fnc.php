<?php
function Buttons($value1,$value2='')
{
	$buttons = '<INPUT type=SUBMIT class=btn_medium value="'.$value1.'">';
	if($value2!='') 
		$buttons .= ' <INPUT type=RESET class=btn_medium value="'.$value2.'">';
	
	return $buttons;
}
?>