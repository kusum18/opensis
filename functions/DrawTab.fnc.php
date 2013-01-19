<?php

function DrawTab($title,$link='',$tabcolor='tab_header_bg_active',$textcolor='',$type='',$rollover='')
{
	if(substr($title,0,1)!='<')
		$title = ereg_replace(" ","&nbsp;",$title);

	$block_table .= "<table border=0 class=tab_header_bg_active cellspacing=0 cellpadding=0 align=left>";
	
	$block_table .= "  <tr class='$tabcolor' id=tab[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]>";
	$block_table .= "    <td class=tab_header_left_active></td><td valign=middle class=drawtab_header >";
	if($link)
	{
		if(is_array($rollover))
			$rollover = " onmouseover=\"document.getElementById('tab[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.backgroundColor='".$rollover['tabcolor']."';document.getElementById('tab_link[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.color='".$rollover['textcolor']."';\" onmouseout=\"document.getElementById('tab[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.backgroundColor='';document.getElementById('tab_link[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.color='".$textcolor."';\" ";
		if(!isset($_REQUEST['_CENTRE_PDF']))
			$block_table .= "<A HREF='$link' $rollover id=tab_link[".ereg_replace('[^a-zA-Z0-9]','_',$link)."] onclick='grabA(this); return false;'>$title</A>";
		else
			$block_table .= "<b>$title</b>";
	}
	else
	{
		if(!isset($_REQUEST['_CENTRE_PDF']))
			$block_table .= $title;
		else
			$block_table .= $title;
	}
	$block_table .= "</td><td class=tab_header_right_active></td>";
	$block_table .= "  </tr>";
	$block_table .= "</table>\n";
	return $block_table;
}


function DrawinactiveTab($title,$link='',$tabactivecolor='tab_header_bg',$textcolor='',$type='',$rollover='')
{
	if(substr($title,0,1)!='<')
		$title = ereg_replace(" ","&nbsp;",$title);

	$block_table .= "<table cellspacing=0 cellpadding=0 align=left >";
	
	$block_table .= "  <tr class=tab_header_bg id=tab[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]>";
	$block_table .= "    <td class=tab_header_left ></td><td valign=middle class=drawinactivetab_header >";
	if($link)
	{
		if(is_array($rollover))
			$rollover = " onmouseover=\"document.getElementById('tab[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.backgroundColor='".$rollover['tabcolor']."';document.getElementById('tab_link[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.color='".$rollover['textcolor']."';\" onmouseout=\"document.getElementById('tab[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.backgroundColor='';document.getElementById('tab_link[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.color='".$textcolor."';\" ";
		if(!isset($_REQUEST['_CENTRE_PDF']))
			$block_table .= "<A HREF='$link' $rollover id=tab_link[".ereg_replace('[^a-zA-Z0-9]','_',$link)."] onclick='grabA(this); return false;'>$title</A>";
		else
			$block_table .= "$title";
	}
	else
	{
		if(!isset($_REQUEST['_CENTRE_PDF']))
			$block_table .= $title;
		else
			$block_table .= $title;
	}
	$block_table .= "</td><td class=tab_header_right></td>";
	$block_table .= "  </tr>";
	$block_table .= "</table>\n";
	return $block_table;
}



function DrawRoundedRect($title,$link='',$tabcolor='#333366',$textcolor='#FFFFFF',$type='',$rollover='')
{
	if(substr($title,0,1)!='<')
		$title = ereg_replace(" ","&nbsp;",$title);

	$block_table .= "<table border=0 cellspacing=0 cellpadding=0>";
	$block_table .= "  <tr style='background-color:$tabcolor' id=tab[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]>";
	$block_table .= "    <td height=5 width=5><IMG SRC=assets/left_upper_corner.gif border=0></td><td rowspan=3 width=100% class=\"BoxHeading\" valign=middle>";
	if($link)
	{
		if(is_array($rollover))
			$rollover = " onmouseover=\"document.getElementById('tab[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.backgroundColor='".$rollover['tabcolor']."';document.getElementById('tab_link[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.color='".$rollover['textcolor']."';\" onmouseout=\"document.getElementById('tab[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.backgroundColor='$tabcolor';document.getElementById('tab_link[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]').style.color='".$textcolor."';\" ";
		if(!isset($_REQUEST['_CENTRE_PDF']))
			$block_table .= "<A HREF='$link' class=BoxHeading style='color:$textcolor' $rollover id=tab_link[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]>$title</A>";
		else
			$block_table .= "<font color=$textcolor face=Verdana,Arial,sans-serif size=-2><b>$title</b></font>";
	}
	else
	{
		if(!isset($_REQUEST['_CENTRE_PDF']))
			$block_table .= "<font color=$textcolor>" . $title . "</font>";
		else
			$block_table .= "<font color=$textcolor><b>" . $title . "</b></font>";
	}
	$block_table .= "</td><td height=5 width=5><IMG SRC=assets/right_upper_corner.gif border=0></td>";
	$block_table .= "  </tr>";

	// MIDDLE ROW
	$block_table .= "  <tr style='background-color:$tabcolor' id=tab[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]>";
	$block_table .= "    <td width=5>&nbsp;</td>";
	$block_table .= "<td width=5>&nbsp;</td>";
	$block_table .= "  </tr>";


	// BOTTOM ROW
	$block_table .= "  <tr style='background-color:$tabcolor' id=tab[".ereg_replace('[^a-zA-Z0-9]','_',$link)."]>";
	$block_table .= "    <td height=5 width=5 valign=bottom><IMG SRC=assets/left_lower_corner.gif border=0></td>";
	$block_table .= "<td height=5 width=5 valign=bottom><IMG SRC=assets/right_lower_corner.gif border=0></td>";
	$block_table .= "  </tr>";



	$block_table .= "</table>\n";
	return $block_table;
}
?>