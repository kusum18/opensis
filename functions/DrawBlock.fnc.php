<?php

function DrawBlock($title,$content,$tabcolor='#333366',$textcolor='#FFFFFF')
{	global $user_id,$wstation,$DatabaseType,$block_table,$global_block_id;


	$block_table = "";
	$block_table .= "<center><TABLE border=\"0\" width=\"98%\" cellspacing=\"0\" cellpadding=\"0\"><TR><TD>";

	$block_table .= DrawTab($title);
	$block_table .= "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"1\" class=\"Box\">";
	$block_table .= "  <tr>";
	$block_table .= "    <td><table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"10\" class=\"BoxContents\">";
	$block_table .= "  <tr>";
	$block_table .= "    <td><img src=\"assets/pixel_trans.gif\" border=\"0\" alt=\"\" width=\"100%\" height=\"1\"></td>";
	$block_table .= "  </tr>";
	$block_table .= "  <tr>";
	$block_table .= "    <td class=\"boxText\" align=left>";

	$block_table .= $content;

	$block_table .= "</td>";
	$block_table .= "  </tr>";
	$block_table .= "  <tr>";
	$block_table .= "    <td><img src=\"assets/pixel_trans.gif\" border=\"0\" alt=\"\" width=\"100%\" height=\"1\"></td>";
	$block_table .= "  </tr>";
	$block_table .= "</table>";
	$block_table .= "</td>";
	$block_table .= "  </tr>";
	$block_table .= "</table>\n\n";
	$block_table .= "</TD></TR></TABLE></centers>";
	echo $block_table;
}

?>