<?php
function TextAreaInputOrg($value,$name,$title='',$options='',$div=true, $divwidth='500px')
{
	if(Preferences('HIDDEN')!='Y')
		$div = false;

	if(AllowEdit() && !$_REQUEST['_CENTRE_PDF'])
	{
		$value = str_replace("'",'&#39;',str_replace('"','&rdquo;',$value));

		if(strpos($options,'cols')===false)
			$options .= ' cols=30';
		if(strpos($options,'rows')===false)
			$options .= ' rows=4';
		$rows = substr($options,strpos($options,'rows')+5,2)*1;
		$cols = substr($options,strpos($options,'cols')+5,2)*1;

		if($value=='' || $div==false)
			return "<TEXTAREA name=$name $options>$value</TEXTAREA>".($title!=''?'<BR><small>'.(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':'').'</small>':'');
		else
			return "<DIV id='div$name'><div style='width:500px;' onclick='javascript:addHTML(\"<TEXTAREA id=textarea$name name=$name $options>".ereg_replace("[\n\r]",'\u000D\u000A',str_replace("\r\n",'\u000D\u000A',str_replace("'","&#39;",$value)))."</TEXTAREA>".($title!=''?"<BR><small>".str_replace("'",'&#39;',(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':''))."</small>":'')."\",\"div$name\",true); document.getElementById(\"textarea$name\").value=unescape(document.getElementById(\"textarea$name\").value);'><TABLE class=LO_field height=100%><TR><TD>".((substr_count($value,"\r\n")>$rows)?'<DIV style="overflow:auto; height:'.(15*$rows).'px; width:'.($cols*10).'; padding-right: 16px;">'.nl2br($value).'</DIV>':'<DIV style="overflow:auto; width:'.$divwidth.'; padding-right: 16px;">'.nl2br($value).'</DIV>').'</TD></TR></TABLE>'.($title!=''?'<BR><small>'.str_replace("'",'&#39;',(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':'')).'</small>':'').'</div></DIV>';
	}
	else
		return (($value!='')?nl2br($value):'-').($title!=''?'<BR><small>'.(strpos(strtolower($title),'<font ')===false?'<FONT color='.Preferences('TITLES').'>':'').$title.(strpos(strtolower($title),'<font ')===false?'</FONT>':'').'</small>':'');
}

function ShowErr($msg)
{
	echo "<script type='text/javascript'>
	document.getElementById('divErr').innerHTML='<font color=red>".$msg."</font>';</script>";
}

function for_error()
{
 		$css=getCSS(); 		
		echo "<br><br><form action=Modules.php?modname=$_REQUEST[modname] method=post>";
		echo '<BR><CENTER>'.SubmitButton('Try Again','','class=btn_medium').'</CENTER>';
		echo "</form>";	
		echo "</div>";

	echo "</td>
                                        </tr>
                                      </table></td>
                                  </tr>
                                </table></td>
                            </tr>
                          </table></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>

			<tr>
            <td class=\"footer\">
			<table width=\"100%\" border=\"0\">
  <tr>
    <td valign=middle class=\"copyright\">Copyright &copy; 2007-2008 Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).</td>
	<td valign=bottom class=\"credits\"><a href='http://www.os4ed.com' target='_blank'><img src=\"themes/".$css. "/os4ed_logo.png\" /></a></td>
  </tr>
</table>
			</td>
          	</tr>
        </table></td>
    </tr>
  </table>
</center>
</body>
</html>";

		exit();
}



function ExportLink($modname,$title='',$options='')
{
	if(AllowUse($modname))
		$link = '<A HREF=for_export.php?modname='.$modname.$options.'>';
	if($title)
		$link .= $title;
	if(AllowUse($modname))
		$link .= '</A>';

	return $link;
}

function getCSS()
{
		$css='Blue';
		if(User('STAFF_ID'))
		{
		$sql = "select value from PROGRAM_USER_CONFIG where title='THEME' and user_id=".User('STAFF_ID');
		$data = DBGet(DBQuery($sql));
		if(count($data[1]))
		$css=$data[1]['VALUE']; 
		}
		return $css;		
}


function Prompt_Calender($title='Confirm',$question='',$message='',$pdf='')
{	
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_CENTRE_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
		echo "<CENTER><h4>$question</h4><FORM name=prompt_form id=prompt_form action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK onclick='formcheck_school_setup_calender();'>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='load_link(\"Modules.php?modname=$_REQUEST[modname]\");'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}


function Prompt_Copy_School($title='Confirm',$question='',$message='',$pdf='')
{	
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_CENTRE_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
		echo "<CENTER><h4>$question</h4><FORM name=prompt_form id=prompt_form action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK onclick='formcheck_school_setup_copyschool();'>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='load_link(\"Modules.php?modname=School_Setup/Calendar.php\");'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}


function Prompt_rollover($title='Confirm',$question='',$message='',$pdf='')
{	
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_CENTRE_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
	//	echo "<CENTER><h4>$question</h4><FORM name=roll_over id=roll_over action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK onclick=\"document.roll_over.submit();\">&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='javascript:history.go(-1);'></FORM></CENTER>";
		echo "<CENTER><h4>$question</h4><FORM name=roll_over id=roll_over action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK onclick=\"document.roll_over.submit();\">&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='load_link(\"Modules.php?modname=School_Setup/Calendar.php\");'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}





function Prompt_Runschedule($title='Confirm',$question='',$message='',$pdf='')
{	
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_CENTRE_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
		echo "<CENTER><h4>$question</h4><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='load_link(\"Modules.php?modname=Scheduling/Schedule.php\");'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}



#############################################################################################
# This function is written for the date reset problem, so if any date  resets to Jan 1 20 use this 

// SEND PrepareDateSchedule a name prefix, and a date in oracle format 'd-M-y' as the selected date to have returned a date selection series
// of pull-down menus
// For the default to be Not Specified, send a date of 00-000-00 -- For today's date, send nothing
// The date pull-downs will create three variables, monthtitle, daytitle, yeartitle
// The third parameter (booleen) specifies whether Not Specified should be allowed as an option

function PrepareDateSchedule($date='',$title='',$allow_na=true,$options='')
{	global $_CENTRE;

	if($options=='')
		$options = array();
	if(!$options['Y'] && !$options['M'] && !$options['D'] && !$options['C'])
		$options += array('Y'=>true,'M'=>true,'D'=>true,'C'=>true);
		
	if($options['short']==true)
		$extraM = "style='width:60;' ";
	if($options['submit']==true)
	{
		$tmp_REQUEST['M'] = $tmp_REQUEST['D'] = $tmp_REQUEST['Y'] = $_REQUEST;
		unset($tmp_REQUEST['M']['month'.$title]);
		unset($tmp_REQUEST['D']['day'.$title]);
		unset($tmp_REQUEST['Y']['year'.$title]);
		$extraM .= "onchange='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST['M'])."&amp;month$title=\"+this.form.month$title.value;'";
		$extraD .= "onchange='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST['D'])."&amp;day$title=\"+this.form.day$title.value;'";
		$extraY .= "onchange='document.location.href=\"".PreparePHP_SELF($tmp_REQUEST['Y'])."&amp;year$title=\"+this.form.year$title.value;'";
	}
	
	if($options['C'])
		$_CENTRE['PrepareDate']++;

	if(strlen($date)==9) // ORACLE
	{
		$day = substr($date,0,2);
		$month = substr($date,3,3);
		$year = substr($date,7,2);

		$return .= '<!-- '.$year.MonthNWSwitch($month,'tonum').$day.' -->';
	}
	else // POSTGRES
	{
		$temp = split('-',$date);
		if(strlen($temp[0])==4)
		{
			$day = $temp[2];
			$year = substr($temp[0],2,2);
		}
		else
		{
			$day = $temp[0];
			$year = substr($temp[2],2,2);
		}
		$month = MonthNWSwitch($temp[1],'tochar');

		$return .= '<!-- '.$year.MonthNWSwitch($month,'tonum').$day.' -->';
	}

	// MONTH  ---------------
	if($options['M'])
	{
		$return .= "<SELECT NAME=month".$title." id=monthSelect".$_CENTRE['PrepareDate']." SIZE=1 $extraM>";
		//  -------------------------------------------------------------------------- //
		
		if($month == 'JAN')
			$month = 1;
		elseif($month == 'FEB')
			$month = 2;
		elseif($month == 'MAR')
			$month = 3;
		elseif($month == 'APR')
			$month = 4;
		elseif($month == 'MAY')
			$month = 5;
		elseif($month == 'JUN')
			$month = 6;
		elseif($month == 'JUL')
			$month = 7;
		elseif($month == 'AUG')
			$month = 8;
		elseif($month == 'SEP')
			$month = 9;
		elseif($month == 'OCT')
			$month = 10;
		elseif($month == 'NOV')
			$month = 11;
		elseif($month == 'DEC')
			$month = 12;
		
		//  -------------------------------------------------------------------------- //
		if($allow_na)
		{
			if($month=='000')
				$return .= "<OPTION value=\"\" SELECTED>N/A";else $return .= "<OPTION value=\"\">N/A";
		}
		
		if($month=='1'){$return .= "<OPTION VALUE=JAN SELECTED>January";}else{$return .= "<OPTION VALUE=JAN>January";}
		if($month=='2'){$return .= "<OPTION VALUE=FEB SELECTED>February";}else{$return .= "<OPTION VALUE=FEB>February";}
		if($month=='3'){$return .= "<OPTION VALUE=MAR SELECTED>March";}else{$return .= "<OPTION VALUE=MAR>March";}
		if($month=='4'){$return .= "<OPTION VALUE=APR SELECTED>April";}else{$return .= "<OPTION VALUE=APR>April";}
		if($month=='5'){$return .= "<OPTION VALUE=MAY SELECTED>May";}else{$return .= "<OPTION VALUE=MAY>May";}
		if($month=='6'){$return .= "<OPTION VALUE=JUN SELECTED>June";}else{$return .= "<OPTION VALUE=JUN>June";}
		if($month=='7'){$return .= "<OPTION VALUE=JUL SELECTED>July";}else{$return .= "<OPTION VALUE=JUL>July";}
		if($month=='8'){$return .= "<OPTION VALUE=AUG SELECTED>August";}else{$return .= "<OPTION VALUE=AUG>August";}
		if($month=='9'){$return .= "<OPTION VALUE=SEP SELECTED>September";}else{$return .= "<OPTION VALUE=SEP>September";}
		if($month=='10'){$return .= "<OPTION VALUE=OCT SELECTED>October";}else{$return .= "<OPTION VALUE=OCT>October";}
		if($month=='11'){$return .= "<OPTION VALUE=NOV SELECTED>November";}else{$return .= "<OPTION VALUE=NOV>November";}
		if($month=='12'){$return .= "<OPTION VALUE=DEC SELECTED>December";}else{$return .= "<OPTION VALUE=DEC>December";}
		
		$return .= "</SELECT> ";
	}

	// DAY  ---------------
	if($options['D'])
	{
		$return .="<SELECT NAME=day".$title." id=daySelect".$_CENTRE['PrepareDate']." SIZE=1 $extraD>";
		if($allow_na)
		{
			if($day=='00'){$return .= "<OPTION value=\"\" SELECTED>N/A";}else{$return .= "<OPTION value=\"\">N/A";}
		}
		
		for($i=1;$i<=31;$i++)
		{
			if(strlen($i)==1)
				$print='0'.$i;
			else
				$print = $i;
			
			$return .="<OPTION VALUE=".$print;
			if($day==$print)
				$return .=" SELECTED";
			$return .=">$i ";
		}
		$return .="</SELECT> ";
	}
	
	// YEAR	 ---------------
	if($options['Y'])
	{
		if(!$year)
		{
			$begin = date('Y') - 20;
			$end = date('Y') + 5;
		}
		else
		{
			if($year<50)
				$year = '20'.$year;
			else
				$year = '19'.$year;
			$begin = $year - 5;
			$end = $year + 5;
		}
	
		$return .="<SELECT NAME=year".$title." id=yearSelect".$_CENTRE['PrepareDate']." SIZE=1 $extraY>";
		if($allow_na)
		{
			if($year=='00'){$return .= "<OPTION value=\"\" SELECTED>N/A";}else{$return .= "<OPTION value=\"\">N/A";}
		}
			
		for($i=$begin;$i<=$end;$i++)
		{
			$return .="<OPTION VALUE=".substr($i,0);
			if($year==$i){$return .=" SELECTED";}
			$return .=">".$i;
		}
		$return .="</SELECT> ";
	}
	
	if($options['C'])
		$return .= '<img src="assets/calendar.gif" id="trigger'.$_CENTRE['PrepareDate'].'" style="cursor: hand;" onmouseover=this.style.background=""; onmouseout=this.style.background=""; onClick='."MakeDate('".$_CENTRE['PrepareDate']."',this);".' />';
	
	if($_REQUEST['_CENTRE_PDF'])
		$return = ProperDate($date);
	return $return;
}
#############################################################################################
function PromptCourseWarning($title='Confirm',$question='',$message='',$pdf='')
{	
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_CENTRE_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
		echo "<CENTER><h4>$question</h4><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='javascript:history.go(-1);'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}

?>
