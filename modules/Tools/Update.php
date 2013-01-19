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
if($_REQUEST['action']=='install' && $_FILES['file']['name'])
{
 	$target_path="tmp";
	if ($_FILES["file"]["error"] > 0)
    {
    $msg = "<font color=red><b>Unable to upload. Return Code: " . $_FILES["file"]["error"] . "</b></font>";
    echo '
	<fieldset style="width:400px"><legend><b>Upload your file</b></legend>
	'.$msg.'
	<form enctype="multipart/form-data" action="Modules.php?modname=Tools/Update.php&action=install" method="POST">';

	//echo '<input type="hidden" name="MAX_FILE_SIZE" value="100000" /> ';

	echo 'Select the update zip file to upload: <input name="file" type="file" /><br /><br>
	<input type="submit" value="Upgrade Application" class=btn_large />
	</form>
	</fieldset>
';
    }
  	else
    {
      move_uploaded_file($_FILES["file"]["tmp_name"], $target_path ."/".$_FILES["file"]["name"]);
      echo "<b>Copied file " . $_FILES["file"]["name"]." to temporary  dir</b><br>";
      $filename =  $target_path ."/".$_FILES["file"]["name"];
      unzip_install($filename, $target_path);
      unlink($filename);
      install_module($target_path, 'update.php');
      
    }
    
}
else
{
echo '
<fieldset style="width:400px"><legend><b>Upload your file</b></legend>
'.$msg.'
<form enctype="multipart/form-data" action="Modules.php?modname=Tools/Update.php&action=install" method="POST">';

//echo '<input type="hidden" name="MAX_FILE_SIZE" value="100000" /> ';

echo 'Select the update zip file to upload: <input name="file" type="file" /><br /><br>
<input type="submit" value="Upgrade Application" class=btn_large />
</form>
</fieldset>
';
}
?>