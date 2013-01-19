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
include 'modules/Grades/config.inc.php';

require_once('functions/_makeLetterGrade.fnc.php');
$_CENTRE['allow_edit'] = false;
if($_REQUEST['_CENTRE_PDF'])
	$do_stats = false;

#DrawHeader(ProgramTitle());
Search('student_id');

if(UserStudentID() && !$_REQUEST['modfunc'])
{
if(!$_REQUEST['id'])
{
	DrawHeader('Totals',"<A HREF=Modules.php?modname=$_REQUEST[modname]&id=all>Expand All</A>");
	$courses_RET = DBGet(DBQuery("SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.TEACHER_ID AS STAFF_ID FROM SCHEDULE s,COURSE_PERIODS cp,COURSES c WHERE s.SYEAR='".UserSyear()."' AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.MARKING_PERIOD_ID IN (".GetAllMP('QTR',UserMP()).") AND ('".DBDate()."' BETWEEN s.START_DATE AND s.END_DATE OR '".DBDate()."'>=s.START_DATE AND s.END_DATE IS NULL) AND s.STUDENT_ID='".UserStudentID()."' AND cp.GRADE_SCALE_ID IS NOT NULL".(User('PROFILE')=='teacher'?' AND cp.TEACHER_ID=\''.User('STAFF_ID').'\'':'')." AND c.COURSE_ID=cp.COURSE_ID ORDER BY (SELECT SORT_ORDER FROM SCHOOL_PERIODS WHERE PERIOD_ID=cp.PERIOD_ID)"),array(),array('COURSE_PERIOD_ID'));
	$LO_columns = array('TITLE'=>'Course Title','TEACHER'=>'Teacher','PERCENT'=>'Percent','GRADE'=>'Letter','UNGRADED'=>'Ungraded')+($do_stats?array('BAR1'=>'Grade Range','BAR2'=>'Class Rank'):array());

	if(count($courses_RET))
	{
		$LO_ret = array(0=>array());

		foreach($courses_RET as $course)
		{
			$course = $course[1];
			$staff_id = $course['STAFF_ID'];
			$course_id = $course['COURSE_ID'];
			$course_period_id = $course['COURSE_PERIOD_ID'];
			$course_title = $course['TITLE'];
			//echo $staff_id.'+'.$course_id.'+'.$course_period_id.'+'.$course_title.'|';
			$assignments_RET = DBGet(DBQuery("SELECT ASSIGNMENT_ID,TITLE,POINTS FROM GRADEBOOK_ASSIGNMENTS WHERE STAFF_ID='$staff_id' AND (COURSE_ID='$course_id' OR COURSE_PERIOD_ID='$course_period_id') AND MARKING_PERIOD_ID='".UserMP()."' ORDER BY DUE_DATE DESC,ASSIGNMENT_ID"));
			//echo '<pre>'; var_dump($assignments_RET); echo '</pre>';

			if(!$programconfig[$staff_id])
			{
				$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='$staff_id' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
				if(count($config_RET))
					foreach($config_RET as $title=>$value)
						$programconfig[$staff_id][$title] = $value[1]['VALUE'];
				else
					$programconfig[$staff_id] = true;
			}

			if($programconfig[$staff_id]['WEIGHT']=='Y')
			{
				$points_RET = DBGet(DBQuery("SELECT      gt.ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,    gt.FINAL_GRADE_PERCENT,sum(".db_case(array('gg.POINTS',"''","1","0")).") AS UNGRADED FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.COURSE_PERIOD_ID='$course_period_id' AND gg.STUDENT_ID='".UserStudentID()."' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID),GRADEBOOK_ASSIGNMENT_TYPES gt WHERE (ga.COURSE_PERIOD_ID='$course_period_id' OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."' AND gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND gt.COURSE_ID='$course_id' AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE+".round($programconfig[$staff_id]['LATENCY']).") OR gg.POINTS IS NOT NULL) GROUP BY gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT"));
				if($do_stats)
					$all_RET = DBGet(DBQuery("SELECT gg.STUDENT_ID,     gt.ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,    gt.FINAL_GRADE_PERCENT FROM GRADEBOOK_GRADES gg,GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES g ON (g.COURSE_PERIOD_ID='$course_period_id' AND g.STUDENT_ID='".UserStudentID()."' AND g.ASSIGNMENT_ID=ga.ASSIGNMENT_ID),GRADEBOOK_ASSIGNMENT_TYPES gt WHERE gt.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND ga.ASSIGNMENT_ID=gg.ASSIGNMENT_ID AND ga.MARKING_PERIOD_ID='".UserMP()."' AND gg.COURSE_PERIOD_ID='$course_period_id' AND (ga.COURSE_PERIOD_ID='$course_period_id' OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='$staff_id') AND gt.COURSE_ID='$course_id' AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE+".round($programconfig[$staff_id]['LATENCY']).") OR gg.POINTS IS NOT NULL) GROUP BY gg.STUDENT_ID,gt.ASSIGNMENT_TYPE_ID,gt.FINAL_GRADE_PERCENT"),array(),array('STUDENT_ID'));
			}
			else
			{
				$points_RET = DBGet(DBQuery("SELECT '-1' AS ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,'1' AS FINAL_GRADE_PERCENT,sum(".db_case(array('gg.POINTS',"''","1","0")).") AS UNGRADED FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.COURSE_PERIOD_ID='$course_period_id' AND gg.STUDENT_ID='".UserStudentID()."' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID)                               WHERE (ga.COURSE_PERIOD_ID='$course_period_id' OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."'                                                                               AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE+".round($programconfig[$staff_id]['LATENCY']).") OR gg.POINTS IS NOT NULL) GROUP BY                          FINAL_GRADE_PERCENT"));
				if($do_stats)
					$all_RET = DBGet(DBQuery("SELECT gg.STUDENT_ID,'-1' AS ASSIGNMENT_TYPE_ID,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'gg.POINTS')).") AS PARTIAL_POINTS,sum(".db_case(array('gg.POINTS',"'-1'","'0'",'ga.POINTS')).") AS PARTIAL_TOTAL,'1' AS FINAL_GRADE_PERCENT FROM GRADEBOOK_GRADES gg,GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES g ON (g.COURSE_PERIOD_ID='$course_period_id' AND g.STUDENT_ID='".UserStudentID()."' AND g.ASSIGNMENT_ID=ga.ASSIGNMENT_ID)                               WHERE                                                 ga.ASSIGNMENT_ID=gg.ASSIGNMENT_ID AND ga.MARKING_PERIOD_ID='".UserMP()."' AND gg.COURSE_PERIOD_ID='$course_period_id' AND (ga.COURSE_PERIOD_ID='$course_period_id' OR ga.COURSE_ID='$course_id' AND ga.STAFF_ID='$staff_id')                               AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE+".round($programconfig[$staff_id]['LATENCY']).") OR gg.POINTS IS NOT NULL) GROUP BY gg.STUDENT_ID,                         FINAL_GRADE_PERCENT"),array(),array('STUDENT_ID'));
			}
			//echo '<pre>'; var_dump($points_RET); echo '</pre>';
			//echo '<pre>'; var_dump($all_RET); echo '</pre>';

			if(count($points_RET))
			{
				$total = $total_percent = 0;
				$ungraded = 0;
				foreach($points_RET as $partial_points)
				{
					if($partial_points['PARTIAL_TOTAL']!=0)
					{
						$total += $partial_points['PARTIAL_POINTS'] * $partial_points['FINAL_GRADE_PERCENT'] / $partial_points['PARTIAL_TOTAL'];
						$total_percent += $partial_points['FINAL_GRADE_PERCENT'];
					}
					$ungraded += $partial_points['UNGRADED'];
				}
				if($total_percent!=0)
					$total /= $total_percent;
				$percent = $total;

				if($do_stats)
				{
					$min_percent = $max_percent = $percent;
					$avg_percent = 0;
					$lower = $higher = 0;
					foreach($all_RET as $xstudent_id=>$student)
					{
						$total = $total_percent = 0;
						foreach($student as $partial_points)
							if($partial_points['PARTIAL_TOTAL']!=0)
							{
								$total += $partial_points['PARTIAL_POINTS'] * $partial_points['FINAL_GRADE_PERCENT'] / $partial_points['PARTIAL_TOTAL'];
								$total_percent += $partial_points['FINAL_GRADE_PERCENT'];
							}
						if($total_percent!=0)
							$total /= $total_percent;

						if($total<$min_percent)
							$min_percent = $total;
						if($total>$max_percent)
							$max_percent = $total;
						$avg_percent += $total;
						if($xstudent_id!==UserStudentID())
							if($total>$percent)
								$higher++;
							else
								$lower++;
					}
					$avg_percent /= count($all_RET);

					$scale = $max_percent>1?$max_percent:1;
					$w1 = round(100*$min_percent/$scale);
					if($percent<$avg_percent)
					{
						$w2 = round(100*($percent-$min_percent)/$scale); $c2 = '#ff0000';
						$w4 = round(100*($max_percent-$avg_percent)/$scale); $c4 = '#00ff00';
					}
					else
					{
						$w2 = round(100*($avg_percent-$min_percent)/$scale); $c2 = '#00ff00';
						$w4 = round(100*($max_percent-$percent)/$scale); $c4 = '#ff0000';
					}
					$w5 = round(100*(1.0-$max_percent/$scale));
					$w3 = 100-$w1-$w2-$w4-$w5;
					$bargraph1 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0?"<TD width=$w1%></TD>":'').($w2>0?"<TD width=$w2% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=$c2>&nbsp;</TD>".($w3>0?"<TD width=$w3% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=$c4>&nbsp;</TD>".($w4>0?"<TD width=$w4% bgcolor=#00a000></TD>":'').($w5>0?"<TD width=$w5%></TD>":'').'</TR></TABLE>';

					$scale = $lower+$higher+1;
					$w1 = round(100*$lower/$scale);
					$w3 = round(100*$higher/$scale);
					$w2 = 100-$w1-$w3;
					$bargraph2 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0||$lower>0?"<TD width=$w1%></TD>":'')."<TD width=$w2% bgcolor=#ff0000>&nbsp;</TD>".($w3>0||$higher>0?"<TD width=$w3%></TD>":'').'</TR></TABLE>';
				}

				$LO_ret[] = array('ID'=>$course_period_id,'TITLE'=>$course['COURSE_TITLE'],'TEACHER'=>substr($course_title,strrpos(str_replace(' - ',' ^ ',$course_title),'^')+2),'PERCENT'=>number_format(100*$percent,1).'%','GRADE' =>'<b>'._makeLetterGrade($percent,$course_period_id,$staff_id).'</b>','UNGRADED'=>$ungraded)+($do_stats?array('BAR1'=>$bargraph1,'BAR2'=>$bargraph2):array());
			}
			//else
				//$LO_ret[] = array('ID'=>$course_period_id,'TITLE'=>$course['COURSE_TITLE'],'TEACHER'=>substr($course_title,strrpos(str_replace(' - ',' ^ ',$course_title),'^')+2));
		}
		unset($LO_ret[0]);
		$link = array('TITLE'=>array('link'=>"Modules.php?modname=$_REQUEST[modname]",'variables'=>array('id'=>'ID')));
		ListOutput($LO_ret,$LO_columns,'Course','Courses',$link,array(),array('center'=>false,'save'=>false,'search'=>false));
	}
	else
		DrawHeader('There are no grades available for this student.');
}
else
{
	if($_REQUEST['id']=='all')
	{
		$courses_RET = DBGet(DBQuery("SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.TEACHER_ID AS STAFF_ID FROM SCHEDULE s,COURSE_PERIODS cp,COURSES c WHERE s.SYEAR='".UserSyear()."' AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.MARKING_PERIOD_ID IN (".GetAllMP('QTR',UserMP()).") AND ('".DBDate()."' BETWEEN s.START_DATE AND s.END_DATE OR '".DBDate()."'>=s.START_DATE AND s.END_DATE IS NULL) AND s.STUDENT_ID='".UserStudentID()."' AND cp.GRADE_SCALE_ID IS NOT NULL".(User('PROFILE')=='teacher'?' AND cp.TEACHER_ID=\''.User('STAFF_ID').'\'':'')." AND c.COURSE_ID=cp.COURSE_ID ORDER BY cp.COURSE_ID"));
		DrawHeader('All Courses','');
	}
	else
	{
		$courses_RET = DBGet(DBQuery("SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.COURSE_ID,cp.TEACHER_ID AS STAFF_ID FROM COURSE_PERIODS cp,COURSES c WHERE cp.COURSE_PERIOD_ID='$_REQUEST[id]' AND c.COURSE_ID=cp.COURSE_ID"));
		DrawHeader('<B>'.$courses_RET[1]['COURSE_TITLE'].'</B> - '.substr($courses_RET[1]['TITLE'],strrpos(str_replace(' - ',' ^ ',$courses_RET[1]['TITLE']),'^')+2),"<A HREF=Modules.php?modname=$_REQUEST[modname]>Back to Totals</A>");
	}
	//echo '<pre>'; var_dump($courses_RET); echo '</pre>';

	foreach($courses_RET as $course)
	{
		$staff_id = $course['STAFF_ID'];
		if(!$programconfig[$staff_id])
		{
			$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='$staff_id' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
			if(count($config_RET))
				foreach($config_RET as $title=>$value)
					$programconfig[$staff_id][$title] = $value[1]['VALUE'];
			else
				$programconfig[$staff_id] = true;
		}

		$assignments_RET = DBGet(DBQuery("SELECT ga.ASSIGNMENT_ID,gg.POINTS,gg.COMMENT,ga.TITLE,ga.DESCRIPTION,ga.ASSIGNED_DATE,ga.DUE_DATE,ga.POINTS AS POINTS_POSSIBLE,at.TITLE AS CATEGORY FROM GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES gg ON (gg.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND gg.STUDENT_ID='".UserStudentID()."'),GRADEBOOK_ASSIGNMENT_TYPES at WHERE (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."' AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE+".round($programconfig[$staff_id]['LATENCY']).") OR gg.POINTS IS NOT NULL) AND (ga.POINTS!='0' OR gg.POINTS IS NOT NULL AND gg.POINTS!='-1') ORDER BY ga.ASSIGNMENT_ID DESC"),array('TITLE'=>'_makeTipTitle'));
		//echo '<pre>'; var_dump($assignments_RET); echo '</pre>';
		if(count($assignments_RET))
		{
			if($do_stats)
				$all_RET = DBGet(DBQuery("SELECT ga.ASSIGNMENT_ID,min(".db_case(array('gg.POINTS',"'-1'",'ga.POINTS','gg.POINTS')).") AS MIN,max(".db_case(array('gg.POINTS',"'-1'",'0','gg.POINTS')).") AS MAX,".db_case(array("sum(".db_case(array('gg.POINTS',"'-1'",'0','1')).")","'0'","'0'","sum(".db_case(array('gg.POINTS',"'-1'",'0','gg.POINTS')).") / sum(".db_case(array('gg.POINTS',"'-1'",'0','1')).")"))." AS AVG,sum(CASE WHEN gg.POINTS<=g.POINTS AND gg.STUDENT_ID!=g.STUDENT_ID THEN 1 ELSE 0 END) AS LOWER,sum(CASE WHEN gg.POINTS>g.POINTS THEN 1 ELSE 0 END) AS HIGHER FROM GRADEBOOK_GRADES gg,GRADEBOOK_ASSIGNMENTS ga LEFT OUTER JOIN GRADEBOOK_GRADES g ON (g.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' AND g.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND g.STUDENT_ID='".UserStudentID()."'),GRADEBOOK_ASSIGNMENT_TYPES at WHERE (ga.COURSE_PERIOD_ID='$course[COURSE_PERIOD_ID]' OR ga.COURSE_ID='$course[COURSE_ID]' AND ga.STAFF_ID='$staff_id') AND ga.MARKING_PERIOD_ID='".UserMP()."' AND gg.ASSIGNMENT_ID=ga.ASSIGNMENT_ID AND at.ASSIGNMENT_TYPE_ID=ga.ASSIGNMENT_TYPE_ID AND ((ga.ASSIGNED_DATE IS NULL OR CURRENT_DATE>=ga.ASSIGNED_DATE) AND (ga.DUE_DATE IS NULL OR CURRENT_DATE>=ga.DUE_DATE+".round($programconfig[$staff_id]['LATENCY']).") OR g.POINTS IS NOT NULL) AND ga.POINTS!='0' GROUP BY ga.ASSIGNMENT_ID"),array(),array('ASSIGNMENT_ID'));
			//echo '<pre>'; var_dump($all_RET); echo '</pre>';

			$LO_columns = array('TITLE'=>'Title','CATEGORY'=>'Category','POINTS'=>'Points / Possible','PERCENT'=>'Percent','LETTER'=>'Letter','COMMENT'=>'Comment')+($do_stats?array('BAR1'=>'Grade Range','BAR2'=>'Class Rank'):array());

			$LO_ret = array(0=>array());

			foreach($assignments_RET as $assignment)
			{
				if($do_stats)
				{
					if($all_RET[$assignment['ASSIGNMENT_ID']])
					{
						$all = $all_RET[$assignment['ASSIGNMENT_ID']][1];

						$scale = $all['MAX']>$assignment['POINTS_POSSIBLE']?$all['MAX']:$assignment['POINTS_POSSIBLE'];
						if($assignment['POINTS']!='-1' && $assignment['POINTS']!='')
						{
							$w1 = round(100*$all['MIN']/$scale);
							if($assignment['POINTS']<$all['AVG'])
							{
								$w2 = round(100*($assignment['POINTS']-$all['MIN'])/$scale); $c2 = '#ff0000';
								$w4 = round(100*($all['MAX']-$all['AVG'])/$scale); $c4 = '#00ff00';
							}
							else
							{
								$w2 = round(100*($all['AVG']-$all['MIN'])/$scale); $c2 = '#00ff00';
								$w4 = round(100*($all['MAX']-$assignment['POINTS'])/$scale); $c4 = '#ff0000';
							}
							$w5 = round(100*(1.0-$all['MAX']/$scale));
							$w3 = 100-$w1-$w2-$w4-$w5;
							$bargraph1 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0?"<TD width=$w1%></TD>":'').($w2>0?"<TD width=$w2% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=$c2>&nbsp;</TD>".($w3>0?"<TD width=$w3% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=$c4>&nbsp;</TD>".($w4>0?"<TD width=$w4% bgcolor=#00a000></TD>":'').($w5>0?"<TD width=$w5%></TD>":'').'</TR></TABLE>';

							$scale = $all['LOWER']+$all['HIGHER']+1;
							$w1 = round(100*$all['LOWER']/$scale);
							$w3 = round(100*$all['HIGHER']/$scale);
							$w2 = 100-$w1-$w3;
							$bargraph2 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0||$lower>0?"<TD width=$w1%></TD>":'')."<TD width=$w2% bgcolor=#ff0000>&nbsp;</TD>".($w3>0||$higher>0?"<TD width=$w3%></TD>":'').'</TR></TABLE>';
						}
						else
						{
							$w1 = round(100*$all['MIN']/$scale);
							$w2 = round(100*($all['AVG']-$all['MIN'])/$scale);
							$w4 = round(100*($all['MAX']-$all['AVG'])/$scale);
							$w5 = round(100*(1.0-$all['MAX']/$scale));
							$bargraph1 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0>'.($w1>0?"<TD width=$w1%></TD>":'').($w2>0?"<TD width=$w2% bgcolor=#00a000></TD>":'')."<TD width=0% bgcolor=#00ff00>&nbsp;</TD>".($w4>0?"<TD width=$w4% bgcolor=#00a000></TD>":'').($w5>0?"<TD width=$w5%></TD>":'').'</TR></TABLE>';
							$bargraph2 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0><TD width=100%>&nbsp;</TD></TR></TABLE>';
						}
					}
					else
						$bargraph1 = $bargraph2 = '<TABLE border=0 width=100% cellspacing=0 cellpadding=0><TR bgcolor=#c0c0c0><TD width=100%>&nbsp;</TD></TR></TABLE>';
				}

				$LO_ret[] = array('TITLE'=>$assignment['TITLE'],'CATEGORY'=>$assignment['CATEGORY'],'POINTS'=>($assignment['POINTS']=='-1'?'*':($assignment['POINTS']==''?'<FONT color=red>0</FONT>':rtrim(rtrim(number_format($assignment['POINTS'],1),'0'),'.'))).' / '.$assignment['POINTS_POSSIBLE'],'PERCENT'=>($assignment['POINTS_POSSIBLE']=='0'?'':($assignment['POINTS']=='-1'?'*':number_format(100*$assignment['POINTS']/$assignment['POINTS_POSSIBLE'],1).'%')),'LETTER'=>($assignment['POINTS_POSSIBLE']=='0'?'e/c':($assignment['POINTS']=='-1'?'n/a':'<b>'._makeLetterGrade($assignment['POINTS']/$assignment['POINTS_POSSIBLE'],$course['COURSE_PERIOD_ID'],$staff_id))).'</b>','COMMENT'=>$assignment['COMMENT'].($assignment['POINTS']==''?($assignment['COMMENT']?'<BR>':'').'<FONT color=red>no grade</FONT>':''))+($do_stats?array('BAR1'=>$bargraph1,'BAR2'=>$bargraph2):array());
			}
			if($_REQUEST['id']=='all')
			{
				echo '<BR>';
				DrawHeader('<B>'.substr($course['TITLE'],0,strpos(str_replace(' - ',' ^ ',$course['TITLE']),'^')).'</B> - '.substr($course['TITLE'],strrpos(str_replace(' - ',' ^ ',$course['TITLE']),'^')+2),"<A HREF=Modules.php?modname=$_REQUEST[modname]>Back to Totals</A>");
			}
			unset($LO_ret[0]);
			ListOutput($LO_ret,$LO_columns,'Assignment','Assignments',array(),array(),array('center'=>false,'save'=>$_REQUEST['id']!='all','search'=>false));
		}
		else
			if($_REQUEST['id']!='all')
				DrawHeader('There are no grades available for this student.');
	}
}
}

function _makeTipTitle($value,$column)
{	global $THIS_RET;

	if(($THIS_RET['DESCRIPTION'] || $THIS_RET['ASSIGNED_DATE'] || $THIS_RET['DUE_DATE']) && !$_REQUEST['_CENTRE_PDF'])
	{
		if($THIS_RET['DESCRIPTION'])
		{
			$tip_title = str_replace(array("'",'"'),array('&#39;','&rdquo;'),$THIS_RET['DESCRIPTION']);
			$tip_title = 'Description: '.str_replace("\r\n",'<BR>',$tip_title);
		}
		if($THIS_RET['ASSIGNED_DATE'])
			$tip_title .= ($tip_title?'<BR>':'').'Assigned: '.ProperDate($THIS_RET['ASSIGNED_DATE']);
		if($THIS_RET['DUE_DATE'])
			$tip_title .= ($tip_title?'<BR>':'').'Due: '.ProperDate($THIS_RET['DUE_DATE']);
		$tip_title = '<A HREF=# onMouseOver=\'stm(["Details","'.$tip_title.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'>'.$value.'</A>';
	}
	else
		$tip_title = $value;

	return $tip_title;
}
?>
