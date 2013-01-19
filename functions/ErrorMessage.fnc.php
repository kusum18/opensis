<?php

// If there are missing vals or similar, show them a msg.
//
// Pass in an array with error messages and this will display them
// in a standard fashion.
//
// in a program you may have:
/*
if(!$sch)
	$error[]="School not provided.";
if($count == 0)
	$error[]="Number of students is zero.";
ErrorMessage($error);
*/
// (note that array[], the brackets with nothing in them makes
// PHP automatically use the next index.

// Why use this?  It will tell the user if they have multiple errors
// without them having to re-run the program each time finding new
// problems.  Also, the error display will be standardized.

// If a 2ND is sent, the list will not be treated as errors, but shown anyway

function ErrorMessage($errors,$code='error')
{
	
	if($errors)
	{
		$return = "<div style=text-align:left><table cellpadding=5 cellspacing=5 class=alert_box ><tr>";
		if(count($errors)==1)
		{
			if($code=='error' || $code=='fatal')
				$return .= '<td class=note></td><td class=note_msg >';
			else
				$return .= '<td class=alert></td><td class=alert_msg >';
			$return .= ($errors[0]?$errors[0]:$errors[1]);
		}
		else
		{
			if($code=='error' || $code=='fatal')
				$return .= "<td class=note></td><td class=note_msg >";
			else
				$return .= '<td class=alert></td><td class=alert_msg >';
			$return .= '<ul>';
			foreach($errors as $value)
					$return .= "<LI>$value</LI>\n";
			$return .= '</ul>';
		}
		$return .= "</td></tr></table></div>";

		if($code=='fatal')
		{
			$css = getCSS();
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table></div>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr>";
				if(User('PROFILE')!='teacher')
				{
					$return .= "<tr>
								<td class=\"footer\">
								<table width=\"100%\" border=\"0\">
								<tr>
								<td valign=middle class=\"copyright\">Copyright &copy; 2007-2008 Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).</td>
								<td valign=bottom class=\"credits\"><a href='http://www.os4ed.com' target='_blank'><img src=\"themes/".$css. "/os4ed_logo.png\" /></a></td>
								</tr>
								</table>
								</td>
								</tr>
								</table>";
				}
				$return .= "</td></tr></table></td></tr></table>";
			if($isajax=="")
			echo $return;
			if(!$_REQUEST['_CENTRE_PDF'])
				Warehouse('footer');
			exit;
		}
		

		return $return;
	}
}

function ErrorMessage1($errors,$code='error')
{
	
	if($errors)
	{
		$return = "<div style=text-align:left><table cellpadding=5 cellspacing=5 class=alert_box ><tr>";
		if(count($errors)==1)
		{
			if($code=='error' || $code=='fatal')
				$return .= '<td class=note></td><td class=note_msg >';
			else
				$return .= '<td class=alert></td><td class=alert_msg >';
			$return .= ($errors[0]?$errors[0]:$errors[1]);
		}
		else
		{
			if($code=='error' || $code=='fatal')
				$return .= "<td class=note></td><td class=note_msg >";
			else
				$return .= '<td class=alert></td><td class=alert_msg >';
			$return .= '<ul>';
			foreach($errors as $value)
					$return .= "<LI>$value</LI>\n";
			$return .= '</ul>';
		}
		$return .= "</td></tr></table></div>";

		if($code=='fatal')
		{
			$css = getCSS();
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table></div>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr></table>";
				$return .= "</td></tr>";
				$return .= "<tr>
							<td class=\"footer\">
							<table width=\"100%\" border=\"0\">
							<tr>
							<td valign=middle class=\"copyright\">Copyright &copy; 2007-2008 Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).</td>
							<td valign=bottom class=\"credits\"><a href='http://www.os4ed.com' target='_blank'><img src=\"themes/".$css. "/os4ed_logo.png\" /></a></td>
							</tr>
							</table>
							</td>
							</tr>
							</table>";
				$return .= "</td></tr></table></td></tr></table>";
			if($isajax=="")
		//	echo $return;
			if(!$_REQUEST['_CENTRE_PDF'])
				Warehouse('footer');
			exit;
		}

		return $return;
	}
}

?>
