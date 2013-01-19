<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. It is  web-based, 
#  open source, and comes packed with features that include student 
#  demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  Copyright (C) 2007-2008, Open Solutions for Education, Inc.
#
#*************************************************************************
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, version 2 of the License. See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#**************************************************************************
$isajax="";
$btn = $_REQUEST['btn'];
if($btn == 'Update' || $btn == '')
{
	$btn = 'old';
}
echo $nsc = $_REQUEST['nsc'];

if($_REQUEST['new_school']!='true')
{
	//echo "NT";
	$ns = "NT";
}
else
{
	//echo "TT";
	$ns = "TT";
}

$handle=opendir("js");
while ($file = readdir($handle)) {
$filelst = "$filelst,$file";
}
closedir($handle);
$filelist = explode(",",$filelst);

if(count($filelist)>3)
{
for ($count=1;$count<count($filelist);$count++) {
$filename=$filelist[$count];
if(($filename != ".") && ($filename != "..") && ($filename!=""))
echo "<script src='js/".$filename."'></script>";
}
}
	
echo "<script type='text/javascript'>
	function changeColors(){ 
       
        //change all link colors 
        var aTags = document.getElementsByTagName(\"a\"); 
		
		
        for(i=0;i<aTags.length;i++){ 
        	if(document.getElementsByTagName('a')[i].id=='hm')
                document.getElementsByTagName('a')[i].className = 'submenuitem'; 
				//document.getElementsByTagName('a')[i].style.fontWeight = 'normal'; 
        } 
	} 
	
		
</script>";	


error_reporting(1);
//error_reporting(E_ALL); // uncomment this to view error on page
$start_time = time();
include 'Warehouse.php';

///Newly added
if(!$_SESSION['UserSchool'])
{
	if(User('PROFILE')=='admin' && (!User('SCHOOLS') || strpos(User('SCHOOLS'),','.User('CURRENT_SCHOOL_ID').',')!==false))
		$_SESSION['UserSchool'] = User('CURRENT_SCHOOL_ID');
	elseif(User('PROFILE')=='student')
		$_SESSION['UserSchool'] = trim(User('SCHOOLS'),',');
	elseif(User('PROFILE')=='teacher')
	{
		$QI = DBQuery("SELECT cp.SCHOOL_ID FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."'".(UserMP()?' AND cp.MARKING_PERIOD_ID IN ('.GetAllMP('QTR',UserMP()).')':'')." ORDER BY sp.SORT_ORDER LIMIT 1");
		$RET = DBGet($QI);
		$_SESSION['UserSchool'] = $RET[1]['SCHOOL_ID'];
	}
}



if((!$_SESSION['UserMP'] || ($_REQUEST['school'] && $_REQUEST['school'] != $old_school) || ($_REQUEST['syear'] && $_REQUEST['syear'] != $old_syear) || ($_REQUEST['period'] && $_REQUEST['period'] != $old_period)) && User('PROFILE')!='parent')
	$_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
//// Newly added




array_rwalk($_REQUEST,'strip_tags');

if(!isset($_REQUEST['_CENTRE_PDF']))
{
	Warehouse('header');


$css = trim(getCSS());


echo "<link rel='stylesheet' type='text/css' href='themes/".trim($css)."/".trim($css).".css'>";
echo "<link rel='stylesheet' type='text/css' href='styles/help.css'>";

if(strpos($_REQUEST['modname'],'misc/')===false)
		echo '<script language="JavaScript">if(window == top  && (!window.opener || window.opener.location.href.substring(0,(window.opener.location.href.indexOf("&")!=-1?window.opener.location.href.indexOf("&"):window.opener.location.href.replace("#","").length))!=window.location.href.substring(0,(window.location.href.indexOf("&")!=-1?window.location.href.indexOf("&"):window.location.href.replace("#","").length)))) window.location.href = "index.php";</script>';
	echo "<BODY onload='newLoad()'>";
}		
		
echo "
<center>
  <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"wrapper\">
    <tr>
      <td ><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
          <tr>
            <td class=\"banner\" valign=\"top\"><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                <tr>
                  <td align=\"left\">&nbsp;</td>
                  <td align=\"right\"><div class=\"user_info\">".User('NAME')." &nbsp;&nbsp;|&nbsp;&nbsp; <a href='index.php?modfunc=logout' class='logout'>Log Out</a>
					<FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns method=POST>
						<INPUT type=hidden name=modcat value='' id=modcat_input>
						<br>".date('l F j, Y')." <br><br>";
	
							if(User('PROFILE')=='admin')
							{
								$schools = substr(str_replace(",","','",User('SCHOOLS')),2,-2);
								#$QI = DBQuery("SELECT ID,TITLE FROM SCHOOLS ORDER BY ID DESC".($schools?" WHERE ID IN ($schools)":''));
								$QI = DBQuery("SELECT ID,TITLE FROM SCHOOLS ORDER BY ID DESC");
								$RET = DBGet($QI);
								
							/*$_REQUEST['school']
								if(!UserSchool())
								{
									$_SESSION['UserSchool'] = $RET[1]['ID'];
									DBQuery("UPDATE STAFF SET CURRENT_SCHOOL_ID='".UserSchool()."' WHERE STAFF_ID='".User('STAFF_ID')."'");
								}
							*/
								
								if(!UserSchool())
								{
									$_SESSION['UserSchool'] = $RET[1]['ID'];
									DBQuery("UPDATE STAFF SET CURRENT_SCHOOL_ID='".UserSchool()."' WHERE STAFF_ID='".User('STAFF_ID')."'");
								}
								
								echo "<SELECT name=school onChange='document.forms[0].submit();' style='width:150;'>";
								foreach($RET as $school)
									echo "<OPTION value=$school[ID]".((UserSchool()==$school['ID'])?' SELECTED':'').">".$school['TITLE']."</OPTION>";
								#	echo "<OPTION value=$school[ID]".((UserSchool()==$school['ID'])?' SELECTED':'').">".$school['TITLE']."</OPTION>";
							
								echo "</SELECT>&nbsp;";
							}

								if(1)
								{
								if(User('PROFILE')!='student')
									$sql = "SELECT DISTINCT sy.SYEAR FROM SCHOOL_YEARS sy,STAFF s WHERE s.SYEAR=sy.SYEAR AND s.USERNAME=(SELECT USERNAME FROM STAFF WHERE SYEAR='$DefaultSyear' AND STAFF_ID='$_SESSION[STAFF_ID]')";
								else
									$sql = "SELECT DISTINCT sy.SYEAR FROM SCHOOL_YEARS sy,STUDENT_ENROLLMENT se WHERE se.SYEAR=sy.SYEAR AND se.STUDENT_ID='$_SESSION[STUDENT_ID]'";
								$years_RET = DBGet(DBQuery($sql));
								}
								else
								$years_RET = array(1=>array('SYEAR'=>"$DefaultSyear"));
								
								echo "<SELECT name=syear onChange='document.forms[0].submit();'>";
								foreach($years_RET as $year)
									echo "<OPTION value=$year[SYEAR]".((UserSyear()==$year['SYEAR'])?' SELECTED':'').">$year[SYEAR]-".($year['SYEAR']+1)."</OPTION>";
								echo '</SELECT>&nbsp;';

								if(User('PROFILE')=='parent')
								{
									$RET = DBGet(DBQuery("SELECT sju.STUDENT_ID,s.LAST_NAME||', '||s.FIRST_NAME AS FULL_NAME,se.SCHOOL_ID FROM STUDENTS s,STUDENTS_JOIN_USERS sju, STUDENT_ENROLLMENT se WHERE s.STUDENT_ID=sju.STUDENT_ID AND sju.STAFF_ID='".User('STAFF_ID')."' AND se.SYEAR=".UserSyear()." AND se.STUDENT_ID=sju.STUDENT_ID AND (('".DBDate()."' BETWEEN se.START_DATE AND se.END_DATE OR se.END_DATE IS NULL) AND '".DBDate()."'>=se.START_DATE)"));
								
									if(!UserStudentID())
										$_SESSION['student_id'] = $RET[1]['STUDENT_ID'];
								
									echo "<SELECT name=student_id onChange='document.forms[0].submit();'>";
									if(count($RET))
									{
										foreach($RET as $student)
										{
											echo "<OPTION value=$student[STUDENT_ID]".((UserStudentID()==$student['STUDENT_ID'])?' SELECTED':'').">".$student['FULL_NAME']."</OPTION>";
											if(UserStudentID()==$student['STUDENT_ID'])
												$_SESSION['UserSchool'] = $student['SCHOOL_ID'];
										}
									}
									echo "</SELECT>&nbsp;";

									if(!UserMP())
										$_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
								}
								
								if(User('PROFILE')=='teacher')
								{
									//if(UserMP())
									//	$QI = DBQuery("SELECT DISTINCT cp.PERIOD_ID,cp.COURSE_PERIOD_ID,sp.TITLE,sp.SHORT_NAME,cp.MARKING_PERIOD_ID,cp.DAYS,cp.SCHOOL_ID,sp.SORT_ORDER,c.TITLE AS COURSE_TITLE FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."' AND cp.MARKING_PERIOD_ID IN (".GetAllMP('QTR',UserMP()).") ORDER BY sp.SORT_ORDER ");
									//else
									$QI = DBQuery("SELECT DISTINCT cp.PERIOD_ID,cp.COURSE_PERIOD_ID,sp.TITLE,sp.SHORT_NAME,cp.MARKING_PERIOD_ID,cp.DAYS,cp.SCHOOL_ID,sp.SORT_ORDER,c.TITLE AS COURSE_TITLE FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."' ORDER BY sp.SORT_ORDER ");
									$RET = DBGet($QI);
									// get the fy marking period id, there should be exactly one fy marking period
									$fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
									$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];
								
									if(!UserPeriod())
										$_SESSION['UserPeriod'] = $RET[1]['PERIOD_ID'];
									if(!UserCoursePeriod())
										$_SESSION['UserCoursePeriod'] = $RET[1]['COURSE_PERIOD_ID'];
								
									echo "<SELECT name=period onChange='document.forms[0].submit();' style='width:150;'>";
									foreach($RET as $period)
									{
										echo "<OPTION value=$period[COURSE_PERIOD_ID]".((UserCoursePeriod()==$period['COURSE_PERIOD_ID'])?' SELECTED':'').">".$period['SHORT_NAME'].($period['MARKING_PERIOD_ID']!=$fy_id?' '.GetMP($period['MARKING_PERIOD_ID'],'SHORT_NAME'):'').(strlen($period['DAYS'])<5?' '.$period['DAYS']:'').' - '.$period['COURSE_TITLE']."</OPTION>";
										if(UserCoursePeriod()==$period['COURSE_PERIOD_ID'])
										{
											if($period['SCHOOL_ID']!=UserSchool())
												unset($_SESSION['UserMP']);
											$_SESSION['UserSchool'] = $period['SCHOOL_ID'];
											$_SESSION['UserPeriod'] = $period['PERIOD_ID'];
										}
									}
									echo "</SELECT>&nbsp;";
								}

//For Marking Period
$RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
echo "<SELECT name=mp onChange='document.forms[0].submit();'>";
if(count($RET))
{
	if(!UserMP())
		$_SESSION['UserMP'] = $RET[1]['MARKING_PERIOD_ID'];

	foreach($RET as $quarter)
			echo "<OPTION value=$quarter[MARKING_PERIOD_ID]".(UserMP()==$quarter['MARKING_PERIOD_ID']?' SELECTED':'').">".$quarter['TITLE']."</OPTION>";
}
echo "</SELECT>";
//Marking Period

echo '</FORM></div>';
echo "</td></tr></table></td></tr>";

if(UserStudentID() && User('PROFILE')!='parent' && User('PROFILE')!='student')
{
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
}
if(UserStaffID() && User('PROFILE')=='admin')
{
	if(UserStudentID())
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
}

echo "

<tr>
            <td class=\"content\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                <tr>
                  <td align=\"center\" ><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"content_wrapper\">
                      <tr>
                        <td class=\"menubar\" style='padding-left:20px; padding-right:20px;'>
      <div id='cdnavheader'>
      <ul>
";

require('Menu.php');
echo "<li><a style='cursor:hand;' href='#' onmouseup='check_content(\"ajax.php?modname=misc/Portal.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"Home\"'><span>" ."Home". "</span></a></li>";
foreach($_CENTRE['Menu'] as $modcat=>$programs)
{
	if(count($_CENTRE['Menu'][$modcat]))
	{
		$keys = array_keys($_CENTRE['Menu'][$modcat]);
		$menu = false;
		foreach($keys as $key_index=>$file)
		{
			if(!is_numeric($file))
				$menu = true;
		}
		if(!$menu)
			continue;

echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)."\"'><span>".str_replace('_',' ',$modcat)."</span></a></li>";

}
}

	echo"</ul></div></td></tr>";


echo "<tr>
                        <td class=\"submenubar_bg\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                            <tr>
                              <td class=\"submenubar\" style='padding-left:10px; padding-right:10px;'>";
							  
echo "<div id='submenu_1' style='display:none;'>

<table cellspacing=0 celpadding=0 border=0 width=100%><tr><td width=60% valign=middle class='welcome'><b>Welcome to openSIS Student Information System</b></td><td width=40% class='version'>Version : ".$CentreVersion." | Build Date : ".$builddate."</td></tr></table> 

</div>"; 
$i = 2;
foreach($_CENTRE['Menu'] as $modcat=>$programs)
{

	if(count($_CENTRE['Menu'][$modcat]))
	{
		$keys = array_keys($_CENTRE['Menu'][$modcat]);
		$menu = false;
		foreach($keys as $key_index=>$file)
		{
			if(!is_numeric($file))
				$menu = true;
		}
		if(!$menu)
			continue;



echo "<div id='submenu_".$i."' style='display:none'>"; 


//echo "<br />";
//echo "<DIV id=menu_visible".$modcat."></DIV>";
//echo "<DIV id=menu_hidden".$modcat." style=\"visibility:hidden;position:absolute;\">";
		//foreach($_CENTRE['Menu'][$modcat] as $file=>$title)
		
		//added this td instead of <tr> in for-each loop for sub menu
		
		//$intarray=0;
		
		$int=0;
		$mm = 0;
		foreach($keys as $key_index=>$file)
		{
			
				
			$int = $int+1; 
			//echo $int;
			
		 				
			//This is for Selected submenu
			
			if($_GET["student_id"]=="new")
			{
				if($modcat=="Students")
		 	{	
		 	 	if($int==2)
		 	 	{
				$style="class='submenu_link'";
				}
				else
				{
				$style="class='submenuitem'";
				}
			}	
			
			}
			elseif($_GET["staff_id"]=="new")
			{
			 
			 if($modcat=="Users")
		 	{	
		 	 	if($int==2)
		 	 	{
				$style="class='submenu_link'";
				}
				else
				{
				$style="class='submenuitem'";
				}
			}	
			
			 
			 
			 
			
			}else
			{
				if($_REQUEST['modname']==$file)
			{	
				$style="class='submenu_link'";

			}else
			{
			$style="class='submenuitem'";
			}
		
		}
		
		 	// Selected submenu ends
		 	
			$title = $_CENTRE['Menu'][$modcat][$file];

		
			
					
			if($mm==0)
			{
			if(substr($file,0,7)=='http://')
				echo "<A ".$style." HREF=$file target=body >$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";
			elseif(substr($file,0,7)=='HTTP://')
				echo "<A ".$style." HREF=$file target=_blank>$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";
			elseif(!is_numeric($file))
				echo "<A ".$style." id=hm HREF=# onClick='check_content(\"ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)." >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";	
			elseif($keys[$key_index+1] && !is_numeric($keys[$key_index+1]))
			{
			 $mm=$mm+1;
			echo '<label class="dd_menuitem" id="mm_'.$modcat.'_'.$mm.'"><b>'.$title.'</b>&nbsp;<img src="themes/'.trim($css).'/mnu_drpdwn.gif" />&nbsp;&nbsp;|&nbsp;&nbsp;</label>&nbsp;'.'<div id="menu_child_'.$modcat.'_'.$mm.'" style="position: absolute; visibility: hidden; width:200px;">';
			}

			}elseif($mm>0)
			{
			$menumm = $mm; 
			if(substr($file,0,7)=='http://')
				echo "<A id=dd class='dd_submenuitem' HREF=$file target=body >$title</A>";
			elseif(substr($file,0,7)=='HTTP://')
				echo "<A id=dd class='dd_submenuitem' HREF=$file target=_blank>$title</A>";
			elseif(!is_numeric($file))
				echo "<A id=dd class='dd_submenuitem' HREF=# onClick='check_content(\"ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)." >> "."$title\"' onmouseup=\"document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">$title</A>";
			elseif($keys[$key_index+1] && !is_numeric($keys[$key_index+1]))
			{
			 $mm=$mm+1;
			echo '</div><label class="dd_menuitem" id="mm_'.$modcat.'_'.$mm.'"><b>'.$title.'</b>&nbsp;<img src="themes/'.trim($css).'/mnu_drpdwn.gif" />&nbsp;&nbsp;|&nbsp;&nbsp;</label>'.'<div id="menu_child_'.$modcat.'_'.$mm.'" style="position: absolute; visibility: hidden; width=200px;">';
			}
				
				echo '<script type="text/javascript">
createmenu("mm_'.$modcat.'_'.$menumm.'", "menu_child_'.$modcat.'_'.$menumm.'", "hover", "y", "pointer");
</script>';
			}
		
					
						
			

			
			
			echo "</b>";
	
		}
		
		

		
		echo "</div></div></DIV>";
		$i=$i+1;
	}
	
	
}	
echo "	</div> ";
echo '</tr></table></td></tr>';	
	


	
	
echo "
<tr >
                        <td class=\"pageheading_bg\"><div class=heading>";


					
echo "				<div class=\"page_heading_breadcrumb\"><label id='header' name='header'></label>&nbsp;";
## for opera echo '<div id="showhelp"><a href="javascript:void(0);" onclick="inter=setInterval(\'ShowBox(helpdiv, 380, 503, 630, 188, showhelp)\',1);return false;"><b>Help</b></a></div>';
echo '<div id="showhelp"><a href="javascript:void(0);" onclick="inter=setInterval(\'ShowBox(helpdiv, 380, 499, 499, 211, showhelp)\',1);return false;"><b>Help</b></a></div>';

echo '
	<div style="height:0px; width=0px; position: absolute; overflow:hidden; visibility: hidden; text-align:left; " id="helpdiv">

<table width=100% border=0 cellspacing=0 cellpadding=0><tr><td align=right style=" height:25px; padding-top:6px;  padding-right:5px; _padding-top:2px;"><a href="javascript:void(0);" onclick="inter=setInterval(\'HideBox(helpdiv, showhelp)\',1);return false;"><b>Hide Help</b></a></td></tr></table>
<div style="background-image:url(themes/Black/help_top.gif); width:495px; height:17px;"></div>
<iframe id="cframe" src="Bottom.php?modname='.$_REQUEST["modname"].'" width="493" height=194px frameborder="0" scrolling="no" style="background-image:url(themes/Black/help_bg.gif); width:495px; background-repeat:repeat-y; background-color:transparent; text-align:left " >
</iframe>
<div style="background-image:url(themes/Black/help_bottom.gif); background-repeat:no-repeat; width:495px; height:10px;"></div>

</div>
';


echo "</div></div>";


echo "</td>
                      </tr>
<tr>
                        <td  valign=\"top\" class=\"txt_container_bg\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                            <tr>
                              <td class=\"txt_bg\">
							  
							  <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                              <tr>
                              <td class=\"txt_container\" valign=\"top\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                              <tr>
                              <td class=\"txt_padding\">
"	;

echo "<div id='content' name='content'>";

//For Student or User Information
if(UserStudentID() && User('PROFILE')!='parent' && User('PROFILE')!='student')
{
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
	DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>)  | <A HREF='.$_SESSION['List_PHP_SELF'].'&bottom_back=true target=body>Back to Student List</A>');
}
if(UserStaffID() && User('PROFILE')=='admin')
{
	//if(UserStudentID())
	//	echo '<IMG SRC=assets/pixel_trans.gif height=2>';
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
	DrawHeaderHome( 'Selected User: '.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].' (<A HREF=Side.php?staff_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>)');
}	

//
	

echo "<div id='update_panel'>";
echo "<center><div id='divErr'></div></center>";

	//if(strpos($_REQUEST['modname'],'misc/')===false && $_REQUEST['modname']!='Students/Student.php' && $_REQUEST['modname']!='School_Setup/Calendar.php' && $_REQUEST['modname']!='Scheduling/Schedule.php' && $_REQUEST['modname']!='Attendance/Percent.php' && $_REQUEST['modname']!='Attendance/Percent.php?list_by_day=true' && $_REQUEST['modname']!='Scheduling/MassRequests.php' && $_REQUEST['modname']!='Scheduling/MassSchedule.php' && $_REQUEST['modname']!='Student_Billing/Fees.php')
if(!isset($_REQUEST['_CENTRE_PDF']))
{
	
//	if(strpos($_REQUEST['modname'],'misc/')===false)
/*		echo '<script language="JavaScript">if(window == top  && (!window.opener || window.opener.location.href.substring(0,(window.opener.location.href.indexOf("&")!=-1?window.opener.location.href.indexOf("&"):window.opener.location.href.replace("#","").length))!=window.location.href.substring(0,(window.location.href.indexOf("&")!=-1?window.location.href.indexOf("&"):window.location.href.replace("#","").length)))) window.location.href = "index.php";</script>'; */
	
	echo '<DIV id="Migoicons" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>';
	echo "<TABLE width=100% border=0 cellpadding=0><TR><TD valign=top align=center>";
}


if($_REQUEST['modname'])
{
	if($_REQUEST['_CENTRE_PDF']=='true')
		ob_start();
	if(strpos($_REQUEST['modname'],'?')!==false)
	{
		$modname = substr($_REQUEST['modname'],0,strpos($_REQUEST['modname'],'?'));
		$vars = substr($_REQUEST['modname'],(strpos($_REQUEST['modname'],'?')+1));

		$vars = explode('?',$vars);
		foreach($vars as $code)
		{
			$code = explode('=',$code);
			$_REQUEST[$code[0]] = $code[1];
		}
	}
	else
		$modname = $_REQUEST['modname'];

	if($_REQUEST['LO_save']!='1' && !isset($_REQUEST['_CENTRE_PDF']) && (strpos($modname,'misc/')===false || $modname=='misc/Registration.php' || $modname=='misc/Export.php' || $modname=='misc/Portal.php'))
		$_SESSION['_REQUEST_vars'] = $_REQUEST;

	$allowed = false;
	include 'Menu.php';
	foreach($_CENTRE['Menu'] as $modcat=>$programs)
	{
		if($_REQUEST['modname']==$modcat.'/Search.php')
		{
			$allowed = true;
			break;
		}
		foreach($programs as $program=>$title)
		{
			if($_REQUEST['modname']==$program)
			{
				$allowed = true;
				break;
			}
		}
	}
	if(substr($_REQUEST['modname'],0,5)=='misc/')
		$allowed = true;

	if($allowed)
	{
		if(Preferences('SEARCH')!='Y')
			$_REQUEST['search_modfunc'] = 'list';
		include('modules/'.$modname);
	}
	else
	{
		if(User('USERNAME'))
		{
			echo "You're not allowed to use this program! This attempted violation has been logged and your IP address was captured.";
			Warehouse('footer');
			if($CentreNotifyAddress)
				mail($CentreNotifyAddress,'HACKING ATTEMPT',"INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$_SERVER[REMOTE_ADDR]','".date('Y-m-d')."','$CentreVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
			if(false && function_exists('mysql_query'))
			{
				$link = @mysql_connect('os4ed.com','centre_log','centre_log');
				@mysql_select_db('centre_log');
				@mysql_query("INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$_SERVER[REMOTE_ADDR]','".date('Y-m-d')."','$CentreVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
				@mysql_close($link);
			}
		}
		exit;
	}

	if($_SESSION['unset_student'])
	{
		unset($_SESSION['unset_student']);
		unset($_SESSION['staff_id']);
	}
}


if(!isset($_REQUEST['_CENTRE_PDF']))
{
	echo '</TD></TR></TABLE>';
	for($i=1;$i<=$_CENTRE['PrepareDate'];$i++)
	{
		echo '<script type="text/javascript">
    Calendar.setup({
        monthField     :    "monthSelect'.$i.'",
        dayField       :    "daySelect'.$i.'",
        yearField      :    "yearSelect'.$i.'",
        ifFormat       :    "%d-%b-%y",
        button         :    "trigger'.$i.'",
        align          :    "Tl",
        singleClick    :    true
    });
</script>';
	}


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
			<table width=\"100%\" border=\"0\" style='visibility:hidden;'>
  <tr>
    <td valign=middle class=\"copyright\">Copyright &copy; 2007-2008 Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).</td>
	<td valign=bottom class=\"credits\"><a href='http://www.os4ed.com' target='_blank'><img src=\"themes/".trim($css). "/os4ed_logo.png\" /></a></td>
  </tr>
</table>
			</td>
          	</tr>
        </table></td>
    </tr>
  </table>
</center>
<div id='cal' style='position:absolute;'> </div>
</body>
</html>
	
	";

}
?>
