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
error_reporting(0);
session_start();
$_SESSION['username'] = $_POST["addusername"];
$_SESSION['password'] = $_POST["addpassword"];
$_SESSION['server'] = $_POST["server"];
$_SESSION['port'] = $_POST["port"];
$_SESSION['host'] = $_POST['server'] . ':' . $_POST['port'];
$err .= "
<html>
<head>
<link rel='stylesheet' type='text/css' href='../styles/installer.css' />
</head>
<body>
<table>
<tr><td valign='middle' align='centre'>
<font color=red><b>Couldn't connect to database server: " . $_SESSION['host'] . "</b></font>
</td></tr>
<tr><td>
<br /><b>Possible causes are:</b>
</td></tr>
<tr><td>
<ol>
<li>MySQL is not installed. Try downloading from <a href='http://dev.mysql.com/downloads/' target=_blank>MySQL Website</a></li>
<li>Username or Password or MySQL Configuration is incorrect</li>
<li>Php.ini is not properly configured. Search for MySQL in php.ini</li>
</ol>
</td></tr>
<tr><td>
<a href='step1.php'><b>Retry</b><a>
</td></tr>
</table>
</td></tr>
</table>
</div>
</div>
<div class=tab_footer></div>
</div>
</div>
</div>
</div></center>
</body>
</html>
";


$dbconn = mysql_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password'])
or 
exit($err);


if($_SESSION['mod']=='upgrade')
{
header('Location: selectdb.php');
}
else
{
header('Location: step2.php');
}
?>
                    