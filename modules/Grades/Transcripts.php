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
$schoolinfo = DBGET(DBQUERY('SELECT * FROM SCHOOLS WHERE ID = '.UserSchool()));
$schoolinfo = $schoolinfo[1];
$tsyear = UserSyear();
$tlogo = "assets/logo.png";
$tpicturepath = $CentrePath.$StudentPicturesPath;
$studataquery = "select 
s.first_name
, s.last_name
, s.middle_name
, s.custom_200000000 as gender
, s.custom_200000004 as birthdate
, a.address
, a.city
, a.state
, a.zipcode
, a.phone
, a.mail_address
, a.mail_city
, a.mail_state
, a.mail_zipcode
, sg.title as grade_title
, sg.short_name as grade_short
, (select start_date from STUDENT_ENROLLMENT where student_id = s.student_id order by syear, start_date limit 1) as init_enroll
, CASE 
    WHEN sg.short_name = '12' THEN e.syear + 1
    WHEN sg.short_name = '11' THEN e.syear + 2
    WHEN sg.short_name = '10' THEN e.syear + 3
    WHEN sg.short_name = '09' THEN e.syear + 4 END AS gradyear
from 
STUDENTS s,
STUDENTS_JOIN_ADDRESS sja,
ADDRESS a,
STUDENT_ENROLLMENT e,
SCHOOL_GRADELEVELS sg,
SCHOOLS sch
where 
sja.student_id = s.student_id
and a.address_id = sja.address_id
and s.student_id = e.student_id
and (e.start_date <= e.end_date or e.end_date is null) ".
// and (e.end_date >= now() or e.end_date is null) 
"and e.syear = $tsyear
and e.grade_id = sg.id
and e.school_id = sch.id ";


if($_REQUEST['modfunc']=='save')
{
   
    $handle = PDFStart();
    //loop through each student
    foreach($_REQUEST['st_arr'] as $arrkey=>$student_id)
    {
        $stu_ret = DBGet(DBQuery($studataquery." and s.student_id = $student_id"));
        $sinfo = $stu_ret[1];
        $school_html = '<table border="0" align=right style="padding-right:40px"><tr><td align=right><table border="0" cellpadding="4" cellspacing="0">
		<tr><td><img height="45px" src="'.$tlogo.'"></td></tr>
                          <tr>
                            <td valign="top" ><div style="font-family:Arial; font-size:13px;">
                              <div style="font-size:18px; font-weight:bold; ">'.$schoolinfo['TITLE'].'</div>
                              <div>'.$schoolinfo['ADDRESS'].'</div>
                              <div>'.$schoolinfo['CITY'].', '.$schoolinfo['STATE'].'&nbsp;&nbsp;'.$schoolinfo['ZIPCODE'].'</div>
                              
                          ';
			if($schoolinfo['PHONE'])				  
	     	$school_html .=  '<div>Phone: '.$schoolinfo['PHONE'].'</div>
                          ';

			#if($schoolinfo['E_MAIL'])				  
	     	#$school_html .=  '<div><b>Email:</b> '.$schoolinfo['E_MAIL'].'</div>';
			#if($schoolinfo['CEEB'])				  
	     	#$school_html .=  '<div><b>CEEB:</b> '.$schoolinfo['CEEB'].'</div>';

			#if($schoolinfo['WWW_ADDRESS'])				  
	     	#$school_html .=  '<div>'.$schoolinfo['WWW_ADDRESS'].'</div>';

						  
		 	$school_html .= '<div style="font-size:15px; ">'.$schoolinfo['PRINCIPAL'].', Principal</div></div> </td>
                            </tr>
                          </table></td></tr></table>';
                                    
        $tquery = "select * from TRANSCRIPT_GRADES where student_id = $student_id and school_id = ".UserSchool()." order by posted";
        
        $TRET = DBGet(DBQuery($tquery));
        $course_html = array(0=>'', 1=>'', 2=>'');
        $colnum = 0;
        $grand_gp_total = 0;
        $grand_scale_total = 0;
        $grand_cred_attempted = 0;
        $grand_cred_earned = 0;
        $last_posted = null;
        $section_html = '';
        
        $section = 0;
         
        $tsecs = array();
        $trecs = array();
        $tsection = 0;
        //loop through each transcript record
        foreach($TRET as $rec){
            if ($rec['POSTED'] != $last_posted){
                if (count($trecs) > 0){
                    array_push($tsecs,$trecs);
                }
                $trecs = array();
            }
            array_push($trecs, $rec);
            $last_posted = $rec['POSTED'];
        }
        array_push($tsecs, $trecs);
        
        $totallines = 38;
        $linesleft = $totallines;
        $tcolumns = array(0=>array(), 1=>array(), 2=>array());
        $colnum = 0;
        foreach($tsecs as $tsec){
            if (count($tsec)+3 > $linesleft){
                $colnum += 1;
                $linesleft = $totallines;
            }
            array_push($tcolumns[$colnum],$tsec);
            $linesleft -= count($tsec)+3;
            
        }
        $colnum = 0;
        foreach ($tcolumns as $tcolumn){
            
            foreach ($tcolumn as $tsection){
                //$gp_total =0;
                //$scale_total =0;
                $cred_attempted =0;
                $cred_earned =0;
                $firstrec = $tsection[0];
                $posted_arr = explode('-',$firstrec['POSTED']);
                $course_html[$colnum] .= "<tr>
                                        <td style='font-size:16px; border-bottom:1px solid #000;'><b>Grade</b> ".$firstrec['GRADELEVEL']."</td>
										<td style='font-size:16px; border-bottom:1px solid #000;'>&nbsp;<b>".$firstrec['MP_NAME']."<b></td>
                                        <td style='font-size:16px; border-bottom:1px solid #000;'>".$posted_arr[1].'/'.$posted_arr[0]."</td></tr>";
               
                foreach($tsection as $trec){
                    //BJJ not using weighting any more
   //                 if ($trec['WEIGHTING']==1.25)
//                        $gradeletter = $trec['GRADE_LETTER'].'(H)';
//                    else
                    $gradeletter = $trec['GRADE_LETTER'];
                    $course_html[$colnum] .= "<tr><td height=\"8\">&nbsp;&nbsp;&nbsp;".$trec['COURSE_NAME']."</td>
                                            <td>".$gradeletter."</td>
                                            <td style='font-family:Arial; font-size:12px;'>".sprintf("%01.3f",$trec['CREDIT_EARNED'])."</td></tr>";
                    if ($trec['CREDIT_ATTEMPTED']!=0.25){
                        //$gp_total += $trec['GP_VALUE']*$trec['WEIGHTING'];
                        //$scale_total += $trec['GP_SCALE'];
                    }
                    $cred_attempted += $trec['CREDIT_ATTEMPTED'];
                    $cred_earned += $trec['CREDIT_EARNED'];
                                            
                }
                //$grand_gp_total += $gp_total;
                //$grand_scale_total += $scale_total;
                $grand_cred_attempted += $cred_attempted;
                $grand_cred_earned += $cred_earned;
                //$gpa = ($gp_total/$scale_total)*4;
                $course_html[$colnum] .= "<tr><td colspan=3 style='font-size:16px; border-top:1px solid #000;'>
                                            <TABLE width='100%' style='font-family:Arial; font-size:12px;'><TR><TD>Credit Attempted: ".sprintf("%01.3f",$cred_attempted)." / Credit Earned: ".sprintf("%01.3f",$cred_earned)."</TD>
                                            </TR></TABLE></td></tr>";
                                            //<tr><td height = 0 colspan=3 align=\"center\">___________</td></tr>";
            }
            $colnum += 1;
        }
        //$grand_gpa = ($grand_gp_total/$grand_scale_total)*4;
        $picturehtml = '';
        if (file_exists($StudentPicturesPath.$tsyear.'/'.$student_id.'.JPG')){

				$picturehtml = '<td valign="top" align="left" width=30%><img style="padding:4px; border:1px solid #333333; background-color:#fff;" src="'.$StudentPicturesPath.$tsyear.'/'.$student_id.'.JPG"></td>';
        }     else {
		$picturehtml = '<td valign="top" align="left" width=30%><img style="padding:4px; border:1px solid #333333; background-color:#fff;" src="assets/noimage.jpg"></td>';
		}    
        $student_html = '
                <table border="0" style="font-family:Arial; font-size:12px;" cellpadding="0" cellspacing="0"><tr>'.$picturehtml.
                    '<td width=70% valign=bottom>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial; font-size:12px;">
                        <tr><td valign=bottom><div style="font-family:Arial; font-size:13px; padding:0px 12px 0px 12px;"><div style="font-size:18px;">'.$sinfo['LAST_NAME'].', '.$sinfo['FIRST_NAME'].' '.$sinfo['MIDDLE_NAME'].'</div>
                            <div>'.$sinfo['ADDRESS'].'</div>
                            <div>'.$sinfo['CITY'].', '.$sinfo['STATE'].'  '.$sinfo['ZIPCODE'].'</div>
                            <div><b>Phone:</b>  '.$sinfo['PHONE'].'</div>
							<div><table cellspacing="0" cellpadding="3" border="1"  style="font-family:Arial; font-size:13px; border-collapse: collapse; text-align:center"><tr><td><b>Date of Birth</b></td><td><b>Gender</b></td><td><b>Grade</b></td></tr><tr><td>'.str_replace('-','/',$sinfo['BIRTHDATE']).'</td><td>'.$sinfo['GENDER'].'</td><td>'.$sinfo['GRADE_SHORT'].'</td></tr></table>'.'</div>
							</td>
                        </tr></table></td></tr><tr><td colspan="2" style="padding:6px 0px 6px 0px;"><table width="100%" cellspacing="0" cellpadding="3" border="1" align=center  style="font-family:Arial; font-size:13px; border-collapse: collapse; text-align:center"><tr><td><b>GPA:</b> '.sprintf("%01.3f",$trec['CUM_GPA']).'&nbsp;&nbsp;&nbsp;&nbsp;<b>Class Rank:</b> '.$trec['CUM_RANK'].'</td></tr><tr><td><b>Credit Attempted:</b> '.sprintf("%01.2f",$grand_cred_attempted).'&nbsp;&nbsp;&nbsp;&nbsp;<b>Credit Earned:</b> '.sprintf("%01.2f",$grand_cred_earned). '</td></tr></table></td></tr></table>';
        
           
            
        echo '  <!-- HEADER CENTER "'.$schoolinfo['TITLE'].' Transcript" -->
                <!-- FOOTER CENTER "Transcript is unofficial unless signed by a school official" -->
              <!-- MEDIA LEFT .25in -->
              <!-- MEDIA TOP .25in -->
              <!-- MEDIA RIGHT .25in -->
              <!-- MEDIA BOTTOM .25in -->
            <table width="860px" border="0" cellpadding="2" cellspacing="0">
                
              <tr>  <!-- this is the header row -->
                <td height="100" valign="top">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        
                        <td width="50%" valign="top" align="center">'.$student_html.'</td>
                        
                        <td width="50%" valign="top" align="right">'.$school_html.'</td>
                      </tr>
                    </table>
                </td>
              </tr>  <!-- end of header row -->
              <tr>   <!-- this is the main body row -->
                <td width="100%" valign="top" >
                  <table width="100%" height="400px" border="1" cellpadding="0" cellspacing="0">
                    <tr>
                        <td valign="top">
                            <table width="100%" border="0" cellpadding="0" cellspacing="6" style="font-family:Arial; font-size:12px;">
                                  <tr>
                                    <td valign="top" align="left" valign="top">     <!-- -->
                                        <table border="0" cellpadding="3" cellspacing="0" style="font-family:Arial; font-size:12px;">
                                            '.$course_html[0].'
                                        </table>
                                      </td>
                                      <td valign="top"align="center"><table width="100%">'.$course_html[1].'</table></td>
                                      <td valign="top"align="center"><table width="100%">'.$course_html[2].'</table></td>
                                    </tr>
                            </table>
                        </td>
                    </tr>
                  </table>
                </td>
              
              </tr>  <!-- end of main body row -->
              <tr>   <!-- this is the footer row -->
                <td align=left>
                    <table align=left>
                        <tr>

                           
                            <td valign="Top" align="left">
                                <table width="100%" >
                                    
                                    <tr><td colspan="3" height="10">&nbsp;</td></tr> 
                                    <tr valign="bottom">
                                        <td align="center" valign="bottom"><br>_______________________________</td>
										<td colspan="2" >&nbsp;</td>
                                    </tr> 
									<tr>
                                        <td align="left" valign="top" style="font-family:Arial; font-size:13px; font-weight:bold">Signature</td>
                                        <td colspan="2">&nbsp;</td>
                                        
                                    </tr>
                                    <tr><td colspan="3" height="10">&nbsp;</td></tr> 
                                    <tr valign="bottom">
                                        <td align="center" valign="bottom"><br>_______________________________</td>
										<td colspan="2" >&nbsp;</td>
                                    </tr> 
									<tr>
                                        <td align="left" valign="top" style="font-family:Arial; font-size:13px; font-weight:bold">Title</td>
                                        <td colspan="2">&nbsp;</td>
                                        
                                    </tr> 
                                </table>
                            </td>
                       </tr>     
                   </table> 
                </td>
              </tr>   <!-- end of footer row -->
            </table><div style="page-break-before: always;">&nbsp;</div>';   
		    echo '<!-- NEW PAGE -->';
		 }
		PDFStop($handle);

}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Gradebook > ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&_CENTRE_PDF=true method=POST target=_blank>";
		#$extra['header_right'] = '<INPUT type=submit value=\'Create Transcripts for Selected Students\'>';
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['new'] = true;
	$extra['options']['search'] = false;
	$extra['force_search'] = true;

	Widgets('course');
	Widgets('gpa');
	Widgets('class_rank');
	Widgets('letter_grade');

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER><INPUT type=submit class=btn_xlarge value=\'Create Transcripts for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}
function _convertlinefeed($string){
    return str_replace("\n", "&nbsp;&nbsp;&nbsp;", $string);
}
?>
