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
$max_cols = 3;
$max_rows = 10;
$to_family = 'To the parents of:';

if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['st_arr']))
	{
		$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
		$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";

		$_REQUEST['mailing_labels']='Y';
		Widgets('mailing_labels');
		$extra['SELECT'] .= ",coalesce(s.CUSTOM_200000002,s.FIRST_NAME) AS NICK_NAME";
		$extra['group'] = array('ADDRESS_ID');
		$RET = GetStuList($extra);

		if(count($RET))
		{
			$skipRET = array();
						for($i=(($_REQUEST['start_row']-1)*$max_cols+$_REQUEST['start_col']); $i>1; $i--)
			$skipRET[-$i] = array(1=>array('MAILING_LABEL'=>' '));

			$handle = PDFstart();
			echo '<!-- MEDIA SIZE 8.5x11in -->';
			echo '<!-- MEDIA TOP 0.5in -->';
			echo '<!-- MEDIA BOTTOM 0.25in -->';
			echo '<!-- MEDIA LEFT 0.25in -->';
			echo '<!-- MEDIA RIGHT 0.25in -->';
			echo '<!-- FOOTER RIGHT "" -->';
			echo '<!-- FOOTER LEFT "" -->';
			echo '<!-- FOOTER CENTER "" -->';
			echo '<!-- HEADER RIGHT "" -->';
			echo '<!-- HEADER LEFT "" -->';
			echo '<!-- HEADER CENTER "" -->';
			
			echo '<table width="100%" height="860" border="0" cellspacing="0" cellpadding="0" style=font-family:Arial; font-size:12px;>';

			$cols = 0;
			$rows = 0;
			foreach($skipRET+$RET as $i=>$addresses)
			{
			
					if($i<1)
				{
			
					if($_REQUEST['to_address']=='student')
					{
					
						foreach($addresses as $key=>$address)
						{
						echo $address['NICK_NAME'];
						           $addresses[$key]['MAILING_LABEL'] = $address['NICK_NAME'].' '.$address['LAST_NAME'].'<BR>'.substr($address['MAILING_LABEL'],strpos($address['MAILING_LABEL'],'<!-- -->')+8);
					}
					}
					elseif($_REQUEST['to_address']=='family')
					{
					
						// if grouping by address, replace people list in mailing labels with students list
						$lasts = array();
						foreach($addresses as $address)
						{
							$lasts[$address['LAST_NAME']][] = $address['NICK_NAME'];
														}
						$students = '';
						foreach($lasts as $last=>$firsts)
						{
						
							$student = '';
							$previous = '';
							foreach($firsts as $first)
							{
								if($student && $previous)
									$student .= ','.$previous;
								elseif($previous)
									$student = $previous;
								$previous = $first;
							}
							if($student)
								$student .= '&'.$previous.' '.$last;
							else
								$student = $previous.' '.$last;
							$students .= $student.', ';
						}
						
						 $addresses = array(1=>array('MAILING_LABEL'=>'<SMALL>'.$to_family.'<BR></SMALL>'.substr($students,0,-2).'<BR>'.substr($addresses[1]['MAILING_LABEL'],strpos($addresses[1]['MAILING_LABEL'],'<!-- -->')+8)));
					//echo $addresses['NICK_NAME'];
					}
				}

				foreach($addresses as $address)
				{
				 $address['MAILING_LABEL'];
					if(!$address['MAILING_LABEL'])
						continue;

					if($cols < 1)
						echo '<tr>';
					echo '<td width="33.3%" height="86" align="center" valign="middle">';
					if($_REQUEST['to_address']=='student')
					{
						echo $address['NICK_NAME'].' &nbsp; '.$address['LAST_NAME'].'<BR>';
						echo "C/o &nbsp;".$address['MAILING_LABEL'];
					}
					else
						echo $address['MAILING_LABEL'];
					echo '</td>';

					$cols++;

					if($cols == $max_cols)
					{
						echo '</tr>';
						$rows++;
						$cols=0;
					}

					if($rows == $max_rows)
					{
						echo '</table><!--NEW PAGE -->';
						echo '<table width="100%" height="860" border="0" cellspacing="0" cellpadding="0" style=font-family:Arial; font-size:12px;>';
						$rows=0;
					}
				}
			}

			if ($cols == 0 && $rows == 0)
			{}
			else
			{
				while ($cols !=0 && $cols < $max_cols)
				{
					echo '<td width="33.3%" height="86" align="center" valign="middle">&nbsp;</td>';
					$cols++;
				}
				if ($cols == $max_cols)
					echo '</tr>';
				echo '</table>';
			}
			//echo '</body></html>';

			PDFstop($handle);
		}
		else
			BackPrompt('No Students were found.');
	}
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Students -> ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_search_all_schools=$_REQUEST[_search_all_schools]&_CENTRE_PDF=true method=POST target=_blank>";
		//$extra['header_right'] = '<INPUT type=submit class=btn_xxlarge value=\'Create Labels for Selected Students\'>';

		$extra['extra_header_left'] = '<TABLE>';

		$extra['extra_header_left'] .= '<TR><TD><INPUT type=radio name=to_address value="" checked>To Contacts</TD></TR>';
		$extra['extra_header_left'] .= '<TR><TD><INPUT type=radio name=to_address value="student">To Student</TD></TR>';
		$extra['extra_header_left'] .= '<TR><TD><INPUT type=radio name=to_address value="family">To Family</TD></TR>';

		$extra['extra_header_left'] .= '</TABLE>';
		$extra['extra_header_right'] = '<TABLE>';

		$extra['extra_header_right'] .= '<TR><TD align=right>Starting row</TD><TD><SELECT name=start_row>';
		for($row=1; $row<=$max_rows; $row++)
			$extra['extra_header_right'] .=  '<OPTION value="'.$row.'">'.$row;
		$extra['extra_header_right'] .=  '</SELECT></TD></TR>';
		$extra['extra_header_right'] .= '<TR><TD align=right>Starting column</TD><TD><SELECT name=start_col>';
		for($col=1; $col<=$max_cols; $col++)
			$extra['extra_header_right'] .=  '<OPTION value="'.$col.'">'.$col;
		$extra['extra_header_right'] .= '</SELECT></TD></TR>';

		$extra['extra_header_right'] .= '</TABLE>';
	}

	Widgets('course');
	Widgets('request');
	Widgets('activity');
	Widgets('absences');
	Widgets('gpa');
	Widgets('class_rank');
	Widgets('letter_grade');
	Widgets('eligibility');
	//$extra['force_search'] = true;

	$extra['SELECT'] .= ",s.STUDENT_ID AS CHECKBOX";
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;

	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
		echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Labels for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}
?>
