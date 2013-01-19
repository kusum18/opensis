<?php

// DRAWS A TABLE WITH A BLUE TAB, SURROUNDING SHADOW
// REQUIRES A TITLE

function PopTableCustom($action,$title='Search',$table_att='', $cell_padding='5')
{	global $_CENTRE;

	if($action=='header')
	{
		echo "<CENTER>
			<TABLE cellpadding=0 cellspacing=0 $table_att>";

			echo "<TR><TD align=center colspan=3>";
			if(is_array($title))
				echo WrapTabs($title,$_CENTRE['selected_tab']);
			else
				echo DrawTab($title);
			echo "</TD></TR>
			<TR><TD background=assets/left_shadow.gif width=4  rowspan=2>&nbsp;</TD><TD background=assets/bottom.gif height=7></TD><TD background=assets/right_shadow.gif width=4  rowspan=2></TD></TR><TR><TD bgcolor=white>";

		// Start content table.
		echo "<TABLE cellpadding=".$cell_padding." cellspacing=0 width=100%><tr><td bgcolor=white>";
	}
	elseif($action=='footer')
	{
		// Close embeded table.
		echo "</td></tr></TABLE>";

		// 2nd cell is for shadow.....
		echo "</TD>
		</TR>
		<TR>
			<TD background=assets/left_corner_shadow.gif height=6 width=4></TD>
			<TD background=assets/bottom_shadow.gif height=6></TD>
			<TD height=6 width=4 background=assets/right_corner_shadow.gif></TD>
		</TR></TABLE></CENTER>";
	}
}
?>