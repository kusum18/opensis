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
require_once("data.php");
if($_REQUEST['action']=='backup' )
{
	$cmd = "mysqldump ".$DatabaseName." --result-file=/tmp/".$DatabaseName.".sql -u ".$DatabaseUsername;
	if($DatabasePassword != '')
	{
		$cmd .= " -p ".$DatabasePassword;
	}
	$output = exec($cmd);
	echo "$cmd: $output";
	force_download($DatabaseName.".sql");
}
else
{
PopTable('header', 'Backup');
echo '<b>Do you want to backup the database?</b>';
echo "<br><br>";


echo '<form id="dataForm" name="dataForm" method="post" action="for_export.php?modname=Tools/Backup.php&action=backup&_CENTRE_PDF=true" target=_blank> 
    <br>
	    <center><input type="submit" value="Yes" name="actionButton" id="actionButton" class="btn_medium">&nbsp;&nbsp;
		<input type="button" value="Cancel" class="btn_medium" onclick=\'load_link("Modules.php?modname=misc/Portal.php");\'></center>
		 
</form> ';
PopTable('footer');
}
function force_download($file) 
{ 
 	   if ((isset($file))&&(file_exists($file))) { 
       header("Content-type: application/force-download"); 
       header('Content-Disposition: inline; filename="' . $file . '"'); 
       header("Content-Transfer-Encoding: Binary"); 
       header("Content-length: ".filesize($file)); 
       header('Content-Type: application/octet-stream'); 
       header('Content-Disposition: attachment; filename="' . $file . '"'); 
       readfile("$file"); 
    } else { 
       echo "No file selected"; 
    } //end if 

}//end function

?>
