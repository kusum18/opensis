<?php

if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['st_arr']))
	{
	$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
	$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";

	//$extra['functions'] = array('GRADE_ID'=>'_grade_id');
	if($_REQUEST['mailing_labels']=='Y')
		Widgets('mailing_labels');

	$RET = GetStuList($extra);

	if(count($RET))
	{
		include('modules/Students/includes/functions.php');
		//------------Comment Heading -----------------------------------------------------
		//$categories_RET = DBGet(DBQuery("SELECT ID,TITLE,INCLUDE FROM STUDENT_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE"),array(),array('ID'));

		// get the address and contacts custom fields, create the select lists and expand select and codeds options
		$address_categories_RET = DBGet(DBQuery("SELECT c.ID AS CATEGORY_ID,c.TITLE AS CATEGORY_TITLE,c.RESIDENCE,c.MAILING,c.BUS,f.ID,f.TITLE,f.TYPE,f.SELECT_OPTIONS,f.DEFAULT_SELECTION,f.REQUIRED FROM ADDRESS_FIELD_CATEGORIES c,ADDRESS_FIELDS f WHERE f.CATEGORY_ID=c.ID ORDER BY c.SORT_ORDER,c.TITLE,f.SORT_ORDER,f.TITLE"),array(),array('CATEGORY_ID'));
		$people_categories_RET = DBGet(DBQuery("SELECT c.ID AS CATEGORY_ID,c.TITLE AS CATEGORY_TITLE,c.CUSTODY,c.EMERGENCY,f.ID,f.TITLE,f.TYPE,f.SELECT_OPTIONS,f.DEFAULT_SELECTION,f.REQUIRED FROM PEOPLE_FIELD_CATEGORIES c,PEOPLE_FIELDS f WHERE f.CATEGORY_ID=c.ID ORDER BY c.SORT_ORDER,c.TITLE,f.SORT_ORDER,f.TITLE"),array(),array('CATEGORY_ID'));
		explodeCustom($address_categories_RET, $address_custom, 'a');
		explodeCustom($people_categories_RET, $people_custom, 'p');

		unset($_REQUEST['modfunc']);
		$handle = PDFStart();
				
		foreach($RET as $student)
		{
			$_SESSION['student_id'] = $student['STUDENT_ID'];
echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Student Information Report</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
	
echo "<table cellspacing=0 style=\"border-collapse:collapse\">";
			echo "<tr><td colspan=3 style=\"height:18px\"></td></tr>";
if($StudentPicturesPath && (($file = @fopen($picture_path=$StudentPicturesPath.UserSyear().'/'.UserStudentID().'.JPG','r')) || ($file = @fopen($picture_path=$StudentPicturesPath.(UserSyear()-1).'/'.UserStudentID().'.JPG','r'))))
{
			echo '<tr><td colspan=3 width=150><IMG SRC="'.$picture_path.'?id='.rand(6,100000).'" width=150  style="padding:4px; background-color:#fff; border:1px solid #333" ></td></tr>';
} else {
echo '<tr><td colspan=3><IMG SRC="assets/noimage.jpg?id='.rand(6,100000).'" width=144  style="padding:4px; background-color:#fff; border:1px solid #333"></td></tr>';
}
	fclose($file);
//echo '</table>';				

	$sql=DBGet(DBQuery("SELECT s.CUSTOM_200000000 AS GENDER, s.CUSTOM_200000001 AS ETHNICITY, s.CUSTOM_200000002 AS COMMON_NAME,  s.CUSTOM_200000003 AS SOCIAL_SEC_NO, s.CUSTOM_200000004 AS BIRTHDAY, s.CUSTOM_200000005 AS LANGUAGE, s.CUSTOM_200000006 AS PHYSICIAN_NAME, s.CUSTOM_200000007 AS PHYSICIAN_PHONO, se.START_DATE AS START_DATE,sec.TITLE AS STATUS, se.NEXT_SCHOOL AS ROLLING  FROM STUDENTS s, STUDENT_ENROLLMENT se,STUDENT_ENROLLMENT_CODES sec WHERE s.STUDENT_ID='".$_SESSION['student_id']."' AND s.STUDENT_ID=se.STUDENT_ID AND se.SYEAR=sec.SYEAR"));

$sql = $sql[1];
unset($_CENTRE['DrawHeader']);

	echo "<tr><td valign=top width=300><table width=100% ><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-weight:bold;\">Personal Information</td></tr>";
			if($_REQUEST['category']['1'])
			{
			//----------------------------------------------

	

			echo "<tr><td width=35%>Student Name:</td>";
			echo "<td width=65%>" .$student['FULL_NAME']. "</td></tr>";
			echo "<tr><td>ID:</td>";
			echo "<td>". $student['STUDENT_ID'] ." </td></tr>";
			echo "<tr><td>Grade:</td>";
			echo "<td>". $student['GRADE_ID'] ." </td></tr>";
			echo "<tr><td>Gender:</td>";
			echo "<td>".$sql['GENDER'] ."</td></tr>";
			echo "<tr><td>Ethnicity:</td>";
			echo "<td>".$sql['ETHNICITY'] ."</td></tr>";
			if($sql['COMMON_NAME'] !='')
			{
			echo "<tr><td>Common Name:</td>";
			echo "<td>".$sql['COMMON_NAME'] ."</td></tr>";
			}
			if($sql['SOCIAL_SEC_NO'] !='')
			{
			echo "<tr><td>Social Security:</td>";
			echo "<td>".$sql['SOCIAL_SEC_NO'] ."</td></tr>";
			}
			echo "<tr><td>BirthDate:</td>";
			echo "<td>".$sql['BIRTHDAY'] ."</td></tr>";
			if($sql['LANGUAGE'] !='')
			{
			echo "<tr><td>Language Spoken:</td>";
			echo "<td>".$sql['LANGUAGE'] ."</td></tr>";
			echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
			}
			if($sql['ROLLING'] !='' && $sql['ROLLING']!=0 && $sql['ROLLING']!=-1)

			{

			$rolling=DBGet(DBQuery("SELECT TITLE FROM SCHOOLS WHERE ID='".$sql['ROLLING']."'"));

			$rolling=$rolling[1]['TITLE'];

			}

			elseif($sql['ROLLING']!=0)

			$rolling = 'Do not enroll after this school year';

			elseif($sql['ROLLING']!=-1)

			$rolling = 'Retain';

			echo "<tr><td>Rolling / Retention Options:</td>";

			echo "<td valign=top>".$rolling ."</td></tr>";

           if($student['MAILING_LABEL'] !='')

			{

			echo "<tr>";

			echo "<td colspan=2>".$student['MAILING_LABEL']."</td></tr>";

			}	
			//----------------------------------------------
			}
			echo '</table></td><td width=12px></td><td valign=top  width=300>';

			if($_REQUEST['category']['3'])
			{
			$addresses_RET = DBGet(DBQuery("SELECT a.ADDRESS_ID, sjp.STUDENT_RELATION,a.ADDRESS,a.CITY,a.STATE,a.ZIPCODE,a.PHONE,a.MAIL_ADDRESS,a.MAIL_CITY,a.MAIL_STATE,a.MAIL_ZIPCODE, sjp.CUSTODY,sja.MAILING,sja.RESIDENCE$address_custom FROM ADDRESS a,STUDENTS_JOIN_ADDRESS sja,STUDENTS_JOIN_PEOPLE sjp WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID='".UserStudentID()."' AND a.ADDRESS_ID=sjp.ADDRESS_ID AND sjp.STUDENT_ID=sja.STUDENT_ID
						  UNION SELECT a.ADDRESS_ID,'No Contacts' AS STUDENT_RELATION,a.ADDRESS,a.CITY,a.STATE,a.ZIPCODE,a.PHONE,a.MAIL_ADDRESS,a.MAIL_CITY,a.MAIL_STATE,a.MAIL_ZIPCODE,NULL AS CUSTODY,sja.MAILING,sja.RESIDENCE$address_custom FROM ADDRESS a,STUDENTS_JOIN_ADDRESS sja WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID='".UserStudentID()."' AND NOT EXISTS (SELECT '' FROM STUDENTS_JOIN_PEOPLE sjp WHERE sjp.STUDENT_ID=sja.STUDENT_ID AND sjp.ADDRESS_ID=a.ADDRESS_ID) ORDER BY ADDRESS ASC,CUSTODY ASC,STUDENT_RELATION"));
			$address_previous = "x";
			foreach($addresses_RET as $address)
			{
				$address_current = $address['ADDRESS'];
				if($address_current != $address_previous)
				{
				echo "<table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-weight:bold;\">Address Information</td></tr>";
				echo "<tr><td width=25%>Address:</td>";
				echo "<td width=75%>".$address['ADDRESS']."</td></tr>";
				echo "<tr><td>City:</td>";
				echo"<td>".($address['CITY']?$address['CITY'].', ':'')."</td></tr>";
				echo "<tr><td>State:</td>";
				echo"<td>".$address['STATE']."</td></tr>";
				echo "<tr><td>Zipcode:</td>";
				echo"<td>".($address['ZIPCODE']?$address['ZIPCODE'].', ':'')."</td></tr>";
				echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
				echo "</table></td></tr>";
					foreach($address_categories_RET as $categories)
					{
						if(!$categories[1]['RESIDENCE']&&!$categories[1]['MAILING']&&!$categories[1]['BUS'] || $categories[1]['RESIDENCE']=='Y'&&$address['RESIDENCE']=='Y' || $categories[1]['MAILING']=='Y'&&$address['MAILING']=='Y' || $categories[1]['BUS']=='Y'&&($address['BUS_PICKUP']=='Y'||$address['BUS_DROPOFF']=='Y'))
							printCustom($categories,$address);

					}
					echo "<tr><td valign=top width=300><table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-weight:bold;\">Contacts Information</td></tr>";
					$contacts_RET = DBGet(DBQuery("SELECT p.PERSON_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,sjp.CUSTODY,sjp.EMERGENCY,sjp.STUDENT_RELATION$people_custom FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE p.PERSON_ID=sjp.PERSON_ID AND sjp.STUDENT_ID='".UserStudentID()."' AND sjp.ADDRESS_ID='".$address['ADDRESS_ID']."'"));
					foreach($contacts_RET as $contact)
					{
                       // echo"<table border=2>";					
						echo "<tr><td colspan=2>".$contact['FIRST_NAME'].' '.($contact['MIDDLE_NAME']?$contact['MIDDLE_NAME'].' ':'').$contact['LAST_NAME'].($contact['STUDENT_RELATION']?': '.$contact['STUDENT_RELATION']:'')."</td></tr>";
						
			$info_RET = DBGet(DBQuery("SELECT ID,TITLE,VALUE FROM PEOPLE_JOIN_CONTACTS WHERE PERSON_ID='".$contact['PERSON_ID']."'"));
			       
				      echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
					  echo "</table></td><td></td><td valign=top>";
						echo '<table width=100%>';
						echo "<tr><td colspan=2 style=\"border-bottom:1px solid #333; font-weight:bold;\">Additional Information</td></tr>";
						foreach($info_RET as $info)
						{
							echo '<tr><td>'.$info['TITLE'].'</td>';
							echo '<td>'.$info['VALUE'].'</td></tr>';
							 echo "<tr><td colspan=2 style=\"height:18px\"></td></tr>";
							
						}
                        echo "</table></td></tr><tr><td valign=top>";
						  
						foreach($people_categories_RET as $categories)
							if(!$categories[1]['CUSTODY']&&!$categories[1]['EMERGENCY'] || $categories[1]['CUSTODY']=='Y'&&$contact['CUSTODY']=='Y' || $categories[1]['EMERGENCY']=='Y'&&$contact['EMERGENCY']=='Y')
								printCustom($categories,$contact);

					}
				}
				$address_previous = $address_current;
			}

			$contacts_RET2 = DBGet(DBQuery("SELECT p.PERSON_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,sjp.CUSTODY,sjp.EMERGENCY,sjp.STUDENT_RELATION$people_custom FROM PEOPLE p,STUDENTS_JOIN_PEOPLE sjp WHERE p.PERSON_ID=sjp.PERSON_ID AND sjp.STUDENT_ID='".UserStudentID()."' AND sjp.ADDRESS_ID='0'"));
			foreach($contacts_RET2 as $contact)
			{
				echo '<B>'.$contact['FIRST_NAME'].' '.($contact['MIDDLE_NAME']?$contact['MIDDLE_NAME'].' ':'').$contact['LAST_NAME'].($contact['STUDENT_RELATION']?': '.$contact['STUDENT_RELATION']:'').' &nbsp;</B>';
				$info_RET = DBGet(DBQuery("SELECT ID,TITLE,VALUE FROM PEOPLE_JOIN_CONTACTS WHERE PERSON_ID='".$contact['PERSON_ID']."'"));
				foreach($info_RET as $info)
				{
					echo '<TR>';
					echo '<TD>'.$info['TITLE'].'</TD>';
					echo '<TD>'.$info['VALUE'].'</TD>';
					echo '</TR>';
				}

				foreach($people_categories_RET as $categories)
					if(!$categories[1]['CUSTODY']&&!$categories[1]['EMERGENCY'] || $categories[1]['CUSTODY']=='Y'&&$contact['CUSTODY']=='Y' || $categories[1]['EMERGENCY']=='Y'&&$contact['EMERGENCY']=='Y')
						printCustom($categories,$contact);
				//echo '</TABLE>';
				
			}
			#echo '<BR>&nbsp;<BR>';
 #echo '</td><td></td><td></td></tr></table></TABLE><div style="page-break-before: always;">&nbsp;</div>';

			}
			if($_REQUEST['category']['2'])
			{
			//------------------------------------------------------------------------------
			#echo "<br>";
			echo "<table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-weight:bold;\">Medical Information</td></tr>";
			if($sql['PHYSICIAN_NAME'] !='')
			{
			echo "<tr><td>Physician Name:</td>";
			echo "<td>".$sql['PHYSICIAN_NAME'] ."</td></tr>";
			}
			if($sql['PHYSICIAN_PHONO'] !='')
			{
			echo "<tr><td>Physicians Phone:</td>";
			echo "<td>".$sql['PHYSICIAN_PHONO'] ."</td></tr>";
			}
			echo '</table>';

				//DrawHeader($categories_RET['2'][1]['TITLE']);
				//include('modules/Students/includes/Medical.inc.php');
				echo '<!-- NEW PAGE -->';
			}
			if($_REQUEST['category']['4'])
			{
				DrawHeader($categories_RET['4'][1]['TITLE']);
				//include('modules/Students/includes/Comments.inc.php');
				echo '<!-- NEW PAGE -->';
			}
			echo '</td><td></td><td></td></tr></table></TABLE><div style="page-break-before: always;">&nbsp;</div>';
			foreach($categories_RET as $id=>$category)
			{
				if($id!='1' && $id!='3' && $id!='2' && $id!='4' && $_REQUEST['category'][$id])
				{
					$_REQUEST['category_id'] = $id;
					//DrawHeader($category[1]['TITLE']);
					$separator = '';
					if(!$category[1]['INCLUDE'])
						include('modules/Students/includes/Other_Info.inc.php');
					elseif(!strpos($category[1]['INCLUDE'],'/'))
						include('modules/Students/includes/'.$category[1]['INCLUDE'].'.inc.php');
					else
					{
						include('modules/'.$category[1]['INCLUDE'].'.inc.php');
						$separator = '<HR>';
						//include('modules/Students/includes/Other_Info.inc.php');
					}

				}
			}
		}
		PDFStop($handle);
	}
	else
		BackPrompt('No Students were found.');
	}
	else
		BackPrompt('You must choose at least one student.');
	unset($_SESSION['student_id']);
	//echo '<pre>'; var_dump($_REQUEST['modfunc']); echo '</pre>';
	$_REQUEST['modfunc']=true;
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Students >> ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_search_all_schools=$_REQUEST[_search_all_schools]&_CENTRE_PDF=true method=POST target=_blank>";
		//$extra['header_right'] = '<INPUT type=submit value=\'Print Info for Selected Students\'>';

		$extra['extra_header_left'] = '<TABLE>';
		//Widgets('mailing_labels',true);
		$extra['extra_header_left'] .= $extra['search'];
		$extra['search'] = '';
		$extra['extra_header_left'] .= '';

		if(User('PROFILE_ID'))
			$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
		else
			$can_use_RET = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
		$categories_RET = DBGet(DBQuery("SELECT ID,TITLE,INCLUDE FROM STUDENT_FIELD_CATEGORIES ORDER BY SORT_ORDER,TITLE"));
		$extra['extra_header_left'] .= '';
		foreach($categories_RET as $category)
			if($can_use_RET['Students/Student.php&category_id='.$category['ID']])
			{
			$extra['extra_header_left'] .= '<TR><TD align="right" style="white-space:nowrap">'.$category['TITLE'].'</td>';
				$extra['extra_header_left'] .= '<td><INPUT type=checkbox name=category['.$category['ID'].'] value=Y checked></TD></TR>';
				
			}
		$extra['extra_header_left'] .= '</TABLE>';
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;

	Widgets('mailing_labels');
	Widgets('course');
	Widgets('request');
	Widgets('activity');
	Widgets('absences');
	Widgets('gpa');
	Widgets('class_rank');
	Widgets('letter_grade');
	Widgets('eligibility');

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Print Info for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

// GetStuList by default translates the grade_id to the grade title which we don't want here.
// One way to avoid this is to provide a translation function for the grade_id so here we
// provide a passthru function just to avoid the translation.
function _grade_id($value)
{
	return $value;
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}

function explodeCustom(&$categories_RET, &$custom, $prefix)
{
	foreach($categories_RET as $id=>$category)
		foreach($category as $i=>$field)
		{
			$custom .= ','.$prefix.'.CUSTOM_'.$field['ID'];
			if($field['TYPE']=='select' || $field['TYPE']=='codeds')
			{
				$select_options = str_replace("\n","\r",str_replace("\r\n","\r",$field['SELECT_OPTIONS']));
				$select_options = explode("\r",$select_options);
				$options = array();
				foreach($select_options as $option)
				{
					if($field['TYPE']=='codeds')
					{
						$option = explode('|',$option);
						if($option[0]!='' && $option[1]!='')
							$options[$option[0]] = $option[1];
					}
					else
						$options[$option] = $option;
				}
				$categories_RET[$id][$i]['SELECT_OPTIONS'] = $options;
			}
		}
}

function printCustom(&$categories, &$values)
{
	echo "<table width=100%><tr><td colspan=2 style=\"border-bottom:1px solid #333;  font-weight:bold;\">".$categories[1]['CATEGORY_TITLE']."</td></tr>";
	foreach($categories as $field)
	{
		echo '<TR>';
		echo '<TD>'.($field['REQUIRED']&&$values['CUSTOM_'.$field['ID']]==''?'<FONT color=red>':'').$field['TITLE'].($field['REQUIRED']&&$values['CUSTOM_'.$field['ID']]==''?'</FONT>':'').'</TD>';
		if($field['TYPE']=='select')
			echo '<TD>'.($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?'':'<FONT color=red>').$values['CUSTOM_'.$field['ID']].($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?'':'</FONT>').'</TD>';
		elseif($field['TYPE']=='codeds')
			echo '<TD>'.($field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]!=''?$field['SELECT_OPTIONS'][$values['CUSTOM_'.$field['ID']]]:'<FONT color=red>'.$values['CUSTOM_'.$field['ID']].'</FONT>').'</TD>';
		else
			echo '<TD>'.$values['CUSTOM_'.$field['ID']].'</TD>';
		echo '</TR>';
	}
	echo '</table>';
}
?>