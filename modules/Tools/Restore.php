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
if($_REQUEST['action']=='restore' && $_FILES['file']['name'])
{
global $DatabaseServer, $DatabaseUsername, $DatabasePassword, $DatabaseName, $DatabasePort;

if($_REQUEST['pass']==$DatabasePassword)
{
$target_path="";

	if ($_FILES["file"]["error"] > 0)
    {
    $msg = "<font color=red><b>Unable to upload. Return Code: " . $_FILES["file"]["error"] . "</b></font>";
	
	echo "<br>".$msg."<br>";
PopTable('header', 'Do you want to restore the database');
echo '<form id="dataForm" name="dataForm" method="post" enctype="multipart/form-data" action="Modules.php?modname=Tools/Restore.php&action=restore"> 
	Enter the Database password: <input type=password id=pass name=pass><br><br>
    Upload the backup: <input type="file" name="path" id="path" style="width:200px"/> <br><br>
	<center>
    <input type="submit" value="Restore Database" name="actionButton" id="actionButton" class="btn_large"> 
	</center>
    </form> ';

PopTable('footer');
	
	}
	else
	{
	  move_uploaded_file($_FILES["file"]["tmp_name"], $target_path ."/".$_FILES["file"]["name"]);
      echo "<b>Copied file " . $_FILES["file"]["name"]." to temporary  dir</b><br>";
   $file =  $target_path ."/".$_FILES["file"]["name"];
	  
	
	//$file=$_FILES["path"]["name"]; 

	
	
      $dbname = $DatabaseName; //database name 
      $dbconn = mysql_pconnect($DatabaseServer,$DatabaseUsername,$DatabasePassword); //connectionstring 
      if (!$dbconn) { 
        echo "Can't connect.\n"; 
        exit; 
      } 
	  $sql="drop database ".$DatabaseName." ;";
	  mysql_query($sql);
	  $sql="create database ".$dbname." ;";
	  mysql_query($sql);
	  mysql_select_db($DatabaseName);
	  executeSQL($file);
//      $startcount=0;
//		$endcount=$max_count_supported;
      
//      $back = fopen($file,"r"); 
      /*
		$file_handle = fopen($file, "r");
			while (!feof($file_handle)) {
   				$line = fgets($file_handle);
   				$restore .= utf8_encode($line);
			}
		fclose($file_handle);
//      $contents = fread($back, filesize($file)); 
//	  $restore = utf8_encode($contents);
      $res = pgsql_query($restore); 
          */
	  
	  echo "<br><br><b>Restore Successful</b>"; 
	  echo "<br>You must restart the application to take effect";
	  }
}
else
{
	$msg = "<font color=red><b>Invalid Password</b></font><br>";
	
	echo "<br>".$msg."<br>";
	PopTable('header', 'Do you want to restore the database');
	echo '<form id="dataForm" name="dataForm" method="post" enctype="multipart/form-data" action="Modules.php?modname=Tools/Restore.php&action=restore"> 
	Enter the Database password: <input type=password id=pass name=pass><br><br>
    Upload the backup: <input type="file" name="path" id="path" style="width:200px"/> <br><br>
	<center>
    <input type="submit" value="Restore Database" name="actionButton" id="actionButton" class="btn_large"> 
	</center>
    </form> ';
    PopTable('footer');
}
}
else
{
echo "<br><br>";
PopTable('header', 'Restore Database');
echo '<b>Do you want to restore a backed up database? </b>';
echo '<br>';
echo '<form id="dataForm" name="dataForm" method="post" enctype="multipart/form-data" action="Modules.php?modname=Tools/Restore.php&action=restore"> 
<div class=clear></div>
	<table><tr><td>Enter database password</td><td>:</td><td><input type=password id=pass style=width:234px name=pass></td></tr>
    <tr><td>Upload backup file</td><td>:</td><input type="file" name="file" size=30 /></td></tr></table>
	<div class=clear></div>
	<center>
    <input type="submit" value="Restore Database" name="actionButton" id="actionButton" class="btn_large"> 
	</center>
    </form> ';

PopTable('footer');
}


function executeSQL($myFile)
{	
	$sql = file_get_contents($myFile);
	$sqllines = split("\n",$sql);
	$cmd = '';
	$delim = false;
	foreach($sqllines as $l)
	{
		if(preg_match('/^\s*--/',$l) == 0)
		{
			if(preg_match('/DELIMITER \$\$/',$l) != 0)
			{	
				$delim = true;
			}
			else
			{
				if(preg_match('/DELIMITER ;/',$l) != 0)
				{
					$delim = false;
				}
				else
				{
					if(preg_match('/END\$\$/',$l) != 0)
					{
						$cmd .= ' END';
					}
					else
					{
						$cmd .= ' ' . $l . "\n";
					}
				}
				if(preg_match('/.+;/',$l) != 0 && !$delim)
				{
					$result = mysql_query($cmd) or die(mysql_error());
					$cmd = '';
				}
			}
		}
	}
}

?>