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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link rel="stylesheet" href="../styles/installer.css" type="text/css" />
<script type="text/javascript" src="js/validator.js"></script>
</head>
<body>
<div class="heading">Congratulations your database is updated
<div style="background-image:url(images/step4.gif); background-repeat:no-repeat; background-position:50% 20px; height:270px;">
  <form name='step4' id='step4' method="post" action="ins4.php">
    <table border="0" cellspacing="6" cellpadding="3" align="center">
      <tr>
        <td  align="center" style="padding-top:36px; padding-bottom:16px">Step 4 of 5</td>
      </tr>
      <tr>
        <td align="center"><strong>System is ready for use<br />
          Please Enter School Year. <br  />Example:<?php echo date("Y"); ?></strong></td>
      </tr>
      <tr>
        <td align="center" valign="top"><table width="245" border="0" cellpadding="4" cellspacing="0" id="table1">
            <tr>
              <td align="center"><input type="text" name="syear" size="20" value="<?php echo date("Y"); ?>" /></td>
            </tr>
            <tr>
              <td  align="center"><input type="submit" value="Save & Next" class=btn_wide name="btnsyear" /></td>
            </tr>
          </table>
          <script language="JavaScript" type="text/javascript">
				
				function CheckYear()
				{
					  var frm = document.forms["step4"];
					  if(frm.syear.value <2000)
						{
							alert('The year should start from 2000');
							frm.syear.focus();
							return false;
						  }
						  else
						  {
							return true;
						  }
				}
				
					var frmvalidator  = new Validator("step4");
					frmvalidator.addValidation("syear","req","Please enter the System Year");
					  frmvalidator.addValidation("syear","maxlen=4", "Maximum length of year is 4");
					  frmvalidator.addValidation("syear","numeric");
					  frmvalidator.setAddnlValidationFunction("CheckYear");
				</script>        </td>
      </tr>
    </table>
  </form>
</div>
</div>
</body>
</html>
