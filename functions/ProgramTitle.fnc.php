<?php

function ProgramTitle()
{	global $_CENTRE;

	if(!$_CENTRE['Menu'])
		include 'Menu.php';
	foreach($_CENTRE['Menu'] as $modcat=>$programs)
	{
		if(count($programs))
		{
			foreach($programs as $program=>$title)
			{
				if($_REQUEST['modname']==$program)
					return $title;
			}
		}
	}
	return 'Centre';
}
?>