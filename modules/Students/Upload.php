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
DrawBC("Students >> ".ProgramTitle());
PopTable ('header','Upload Student\'s Photo');
if($_REQUEST['modfunc']=='edit')
{
	if($StudentPicturesPath && (($file = @fopen($picture_path=$StudentPicturesPath.UserSyear().'/'.UserStudentID().'.JPG','r')) || ($file = @fopen($picture_path=$StudentPicturesPath.(UserSyear()-1).'/'.UserStudentID().'.JPG','r'))))
	{
	echo '<div align=center><IMG SRC="'.$picture_path.'?id='.rand(6,100000).'" width=150 class=pic></div><div class=break></div>';
	}
	unset($_REQUEST['modfunc']);
}
if(UserStudentID())
{
if($_REQUEST['action']=='upload' && $_FILES['file']['name'])
{
	$target_path=$StudentPicturesPath.UserSyear().'/'.UserStudentID().'.JPG';
	if(file_exists($target_path))
	unlink($target_path);
	$target_path=$StudentPicturesPath.UserSyear().'/'.UserStudentID().'.JPG';
	$destination_path = $StudentPicturesPath.UserSyear();
	if ($_FILES["file"]["error"] > 0)
    {
    $msg = "<font color=red><b>Unable to upload. Return Code: " . $_FILES["file"]["error"] . "</b></font>";
    echo '
	'.$msg.'
	<form enctype="multipart/form-data" action="Modules.php?modname=Students/Upload&action=upload" method="POST">';
	echo '<div align=center>Select Student\'s Picture: <input name="file" type="file" /><br /><br>
	<input type="submit" value="Upload Picture" class=btn_large />
	</div></form>';
    }
  	else
    {
      move_uploaded_file($_FILES["file"]["tmp_name"], $target_path);
	  @fopen($target_path,'r');
	  echo '<div align=center><IMG SRC="'.$target_path.'?id='.rand(6,100000).'" width=150 class=pic></div><div class=break></div>';
	  fclose($target_path);
      echo "<b>Copied file to " .$destination_path."</b><p>";
      $filename =  $target_path;
	  PopTable ('footer');
    }    
}
else
{
echo '
'.$msg.'
<form enctype="multipart/form-data" action="Modules.php?modname=Students/Upload.php&action=upload" method="POST">';
echo '<div align=center>Select image file: <input name="file" type="file" /><br /><br>
<input type="submit" value="Upload" class=btn_medium />&nbsp;<input type=button class=btn_medium value=Cancel onclick=\'load_link("Modules.php?modname=Students/Student.php");\'></div>
</form>';
PopTable ('footer');
}
}
else
{
	echo 'Please select a student first! from the <b>"Students"</b> Tab';
	PopTable ('footer');
}
?>