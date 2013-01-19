<?php

function DeCodeds($value,$column)
{	global $_CENTRE;

	$field = substr($column,7);

	if(!$_CENTRE['DeCodeds'][$field])
	{
		$select_options = DBGet(DBQuery("SELECT SELECT_OPTIONS FROM CUSTOM_FIELDS WHERE ID='$field'"));
		$select_options = str_replace("\n","\r",str_replace("\r\n","\r",$select_options[1]['SELECT_OPTIONS']));
		$select_options = explode("\r",$select_options);
		foreach($select_options as $option)
		{
			$option = explode('|',$option);
			if($option[0]!='' && $option[1]!='')
				$options[$option[0]] = $option[1];
		}
		if(count($options))
			$_CENTRE['DeCodeds'][$field] = $options;
		else
			$_CENTRE['DeCodeds'][$field] = true;
	}

	if($value!='')
		if($_CENTRE['DeCodeds'][$field][$value]!='')
			return $_CENTRE['DeCodeds'][$field][$value];
		else
			return "<FONT color=red>$value</FONT>";
	else
		return '';
}
?>
