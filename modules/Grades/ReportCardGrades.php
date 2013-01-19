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
include 'modules/Grades/DeletePromptX.fnc.php';
//echo '<pre>'; var_dump($_REQUEST); echo '</pre>';
DrawBC("Gradebook > ".ProgramTitle());

if($_REQUEST['modfunc']=='update')
{
	if($_REQUEST['values'] && ($_POST['values'] || $_REQUEST['ajax']))
	{
		if($_REQUEST['tab_id'])
		{
			foreach($_REQUEST['values'] as $id=>$columns)
			{
				if($id!='new')
				{
					if($_REQUEST['tab_id']!='new')
						$sql = "UPDATE REPORT_CARD_GRADES SET ";
					else
						$sql = "UPDATE REPORT_CARD_GRADE_SCALES SET ";

					foreach($columns as $column=>$value)
						$sql .= $column."='".str_replace("\'","''",$value)."',";

					if($_REQUEST['tab_id']!='new')
						$sql = substr($sql,0,-1) . " WHERE ID='$id'";
					else
						$sql = substr($sql,0,-1) . " WHERE ID='$id'";
					DBQuery($sql);
				}
				else
				{
					if($_REQUEST['tab_id']!='new')
					{
						$sql = 'INSERT INTO REPORT_CARD_GRADES ';
						$fields = 'ID,SCHOOL_ID,SYEAR,GRADE_SCALE_ID,';
						$values = db_seq_nextval('REPORT_CARD_GRADES_SEQ').',\''.UserSchool().'\',\''.UserSyear().'\',\''.$_REQUEST['tab_id'].'\',';
					}
					else
					{
						$sql = 'INSERT INTO REPORT_CARD_GRADE_SCALES ';
						$fields = 'ID,SCHOOL_ID,SYEAR,';
						$values = db_seq_nextval('REPORT_CARD_GRADE_SCALES_SEQ').',\''.UserSchool().'\',\''.UserSyear().'\',';
					}

					$go = false;
					foreach($columns as $column=>$value)
						if($value)
						{
							$fields .= $column.',';
							$values .= '\''.str_replace("\'","''",$value).'\',';
							$go = true;
						}
					$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
					if($go)
						DBQuery($sql);
				}
			}
		}
	}
	unset($_REQUEST['modfunc']);
}

if($_REQUEST['modfunc']=='remove')
{
	if($_REQUEST['tab_id']!='new')
	{
		if(DeletePromptX('Report Card Grade'))
		{
			DBQuery("DELETE FROM REPORT_CARD_GRADES WHERE ID='$_REQUEST[id]'");
		}
	}
	else
		if(DeletePromptX('Report Card Grading Scale'))
		{
			DBQuery("DELETE FROM REPORT_CARD_GRADES WHERE GRADE_SCALE_ID='$_REQUEST[id]'");
			DBQuery("DELETE FROM REPORT_CARD_GRADE_SCALES WHERE ID='$_REQUEST[id]'");
			unset($_SESSION['GR_scale_id']);
		}
}

if(!$_REQUEST['modfunc'])
{
	if(User('PROFILE')=='admin')
	{
		$grade_scales_RET = DBGet(DBQuery('SELECT ID,TITLE FROM REPORT_CARD_GRADE_SCALES WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' ORDER BY SORT_ORDER'),array(),array('ID'));
		if(!$_REQUEST['tab_id'])
			if(!$_SESSION['GR_scale_id'])
				if(count($grade_scales_RET))
					$_REQUEST['tab_id'] = $_SESSION['GR_scale_id'] = key($grade_scales_RET);
				else
					$_REQUEST['tab_id'] = 'new';
			else
				$_REQUEST['tab_id'] = $_SESSION['GR_scale_id'];
		else
			if($_REQUEST['tab_id']!='new')
				$_SESSION['GR_scale_id'] = $_REQUEST['tab_id'];
	}
	else
	{
		$course_period_RET = DBGet(DBQuery('SELECT GRADE_SCALE_ID,DOES_BREAKOFF,TEACHER_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID=\''.UserCoursePeriod().'\''));
		if(!$course_period_RET[1]['GRADE_SCALE_ID'])
			ErrorMessage(array('This course is not graded.'),'fatal');
		$grade_scales_RET = DBGet(DBQuery('SELECT ID,TITLE FROM REPORT_CARD_GRADE_SCALES WHERE ID=\''.$course_period_RET[1]['GRADE_SCALE_ID'].'\''),array(),array('ID'));
		if($course_period_RET[1]['DOES_BREAKOFF']=='Y')
		{
			$teacher_id = $course_period_RET[1]['TEACHER_ID'];
			$config_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE USER_ID='$teacher_id' AND PROGRAM='Gradebook'"),array(),array('TITLE'));
		}
		$_REQUEST['tab_id'] = key($grade_scales_RET);
	}

	$tabs = array();
	$grade_scale_select = array();
	foreach($grade_scales_RET as $id=>$grade_scale)
	{
		$tabs[] = array('title'=>$grade_scale[1]['TITLE'],'link'=>"Modules.php?modname=$_REQUEST[modname]&tab_id=$id");
		$grade_scale_select += array($id=>$grade_scale[1]['TITLE']);
	}

	if($_REQUEST['tab_id']!='new')
	{
		$sql = 'SELECT * FROM REPORT_CARD_GRADES WHERE GRADE_SCALE_ID=\''.$_REQUEST['tab_id'].'\' AND SYEAR=\''.UserSyear().'\' ORDER BY BREAK_OFF IS NOT NULL DESC,BREAK_OFF DESC, SORT_ORDER';
		$functions = array('TITLE'=>'makeGradesInput',
                            'BREAK_OFF'=>'makeGradesInput',
                            'SORT_ORDER'=>'makeGradesInput',
                            'GPA_VALUE'=>'makeGradesInput',
                            //'UNWEIGHTED_GP'=>'makeGradesInput',
                            'COMMENT'=>'makeGradesInput');
		$LO_columns = array('TITLE'=>'Title',
                            'BREAK_OFF'=>'Breakoff',
                            'GPA_VALUE'=>'GPA Value',
                            //'UNWEIGHTED_GP'=>'Unweighted GP Value',
                            'SORT_ORDER'=>'Order',
                            'COMMENT'=>'Comment');

		if(User('PROFILE')=='admin' && AllowEdit())
		{
			$functions += array('GRADE_SCALE_ID'=>'makeGradesInput');
			$LO_columns += array('GRADE_SCALE_ID'=>'Grade Scale');
		}

		$link['add']['html'] = array('TITLE'=>makeGradesInput('','TITLE'),'BREAK_OFF'=>makeGradesInput('','BREAK_OFF'),'GPA_VALUE'=>makeGradesInput('','GPA_VALUE'),'SORT_ORDER'=>makeGradesInput('','SORT_ORDER'),'COMMENT'=>makeGradesInput('','COMMENT'));
		$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&tab_id=$_REQUEST[tab_id]";
		$link['remove']['variables'] = array('id'=>'ID');
		$link['add']['html']['remove'] = button('add');

		if(User('PROFILE')=='admin')
			$tabs[] = array('title'=>button('add'),'link'=>"Modules.php?modname=$_REQUEST[modname]&tab_id=new");
	}
	else
	{
		//BJJ modifications to $functions array and $LO_columns array to handle scale value GP_SCALE
        $sql = 'SELECT * FROM REPORT_CARD_GRADE_SCALES WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' ORDER BY SORT_ORDER,ID';
		$functions = array('TITLE'=>'makeTextInput','GP_SCALE'=>'makeTextInput', 'COMMENT'=>'makeTextInput','SORT_ORDER'=>'makeTextInput');
		$LO_columns = array('TITLE'=>'Gradescale','GP_SCALE'=>'Scale Value', 'COMMENT'=>'Comment','SORT_ORDER'=>'Sort Order');

		$link['add']['html'] = array('TITLE'=>makeTextInput('','TITLE'),'GP_SCALE'=>makeTextInput('', 'GP_SCALE'),'COMMENT'=>makeTextInput('','COMMENT'),'SORT_ORDER'=>makeTextInput('','SORT_ORDER'));
		$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&tab_id=new";
		$link['remove']['variables'] = array('id'=>'ID');
		$link['add']['html']['remove'] = button('add');

		$tabs[] = array('title'=>button('white_add'),'link'=>"Modules.php?modname=$_REQUEST[modname]&tab_id=new");
	}
	$LO_ret = DBGet(DBQuery($sql),$functions);

	echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&tab_id=$_REQUEST[tab_id] method=POST>";
	#DrawHeader('',SubmitButton('Save'));
	echo '<BR>';

	echo '<CENTER>'.WrapTabs($tabs,"Modules.php?modname=$_REQUEST[modname]&tab_id=$_REQUEST[tab_id]").'</CENTER>';
	PopTable_wo_header ('header');
	ListOutputMod($LO_ret,$LO_columns,'','',$link,array(),array('count'=>false,'download'=>false,'search'=>false));
	echo '<BR>';
	echo '<CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_grade_grade();"').'</CENTER>';
	PopTable ('footer');
	echo '</FORM>';
}

function makeGradesInput($value,$name)
{	global $THIS_RET,$grade_scale_select,$teacher_id,$config_RET;
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';

	if($name=='GRADE_SCALE_ID')
		return SelectInput($value,"values[$id][$name]",'',$grade_scale_select,false);
	elseif($name=='COMMENT')
		$extra = 'size=15 maxlength=100';
	elseif($name=='GPA_VALUE')
		$extra = 'size=5 maxlength=5';
	elseif($name=='SORT_ORDER')
		$extra = 'size=5 maxlength=5';
	elseif($name=='BREAK_OFF' && $teacher_id && $config_RET[UserCoursePeriod().'-'.$THIS_RET['ID']][1]['VALUE']!='')
		return '<FONT color=blue>'.$config_RET[UserCoursePeriod().'-'.$THIS_RET['ID']][1]['VALUE'].'</FONT>';
	else
		$extra = 'size=5 maxlength=5';

	return TextInput($value,"values[$id][$name]",'',$extra);
}

function makeTextInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
    //bjj adding 'GP_SCALE'
	if($name=='TITLE')
		$extra = 'size=15 maxlength=25';
    elseif($name=='GP_SCALE')
        $extra = 'size=5 maxlength=5';
	elseif($name=='COMMENT')
		$extra = 'size=15 maxlength=100';
	else
		$extra = 'size=5 maxlength=5';

	return TextInput($value,"values[$id][$name]",'',$extra);
}


//////////////////////////////////////////// Validation Start //////////////////////////////////////////////////
	
	if($_REQUEST['modfunc']!='remove')
	{
	
		echo '
		<script language="JavaScript" type="text/javascript">
	
		var frmvalidator  = new Validator("F1");
		
		//frmvalidator.addValidation("values[new][TITLE]","req","Please enter the Title");
		frmvalidator.addValidation("values[new][TITLE]","maxlen=100", "Max length for Title is 100");
		frmvalidator.addValidation("values[new][TITLE]","grade_title", "Title allows only alphanumeric value");
		
		//frmvalidator.addValidation("values[new][SORT_ORDER]","req","Please enter Sort Order");
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");

		</script>
		';
	
	}
	
	
		if($_REQUEST['tab_id']=='new' && $_REQUEST['modfunc']!='remove')
		{
	
			echo '
			<script language="JavaScript" type="text/javascript">
		
			var frmvalidator  = new Validator("F1");
			
			//frmvalidator.addValidation("values[new][TITLE]","req","Please enter the Gradescale");
			frmvalidator.addValidation("values[new][TITLE]","maxlen=10", "Max length for Gradescale is 10");
			frmvalidator.addValidation("values[new][TITLE]","alphabetic", "Sort Order allows only alphabetic value");
			
			//frmvalidator.addValidation("values[new][SORT_ORDER]","req","Please enter Sort Order");
			frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");
	
			</script>
			';
	
		}

//////////////////////////////////// Validation Enb ///////////////////////////////////////////////	

?>