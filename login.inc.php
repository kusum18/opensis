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
Warehouse('header');
	echo '<link rel="stylesheet" type="text/css" href="styles/login.css">';
	echo '<script type="text/javascript" src="js/tabmenu.js"></script>';
	echo "
	<script type='text/javascript'>
	function delete_cookie (cookie_name)
		{
  			var cookie_date = new Date ( );  // current date & time
  			cookie_date.setTime ( cookie_date.getTime() - 1 );
			  document.cookie = cookie_name += \"=; expires=\" + cookie_date.toGMTString();
		}

</script>";
	echo "<BODY onLoad='document.loginform.USERNAME.focus();  delete_cookie(\"dhtmlgoodies_tab_menu_tabIndex\");'>";
	echo "";
	
	echo "
	<form name=loginform method='post' action='index.php'>
	<table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td valign='middle' height='100%'><table class='wrapper' border='0' cellspacing='0' cellpadding='0' align='center'>
        
        <tr>
          <td class='header'><table width='100%' border='0' cellspacing='0' cellpadding='0' class='logo_padding'>
              <tr>
                <td><img src='assets/osis_logo.png' height='63' width='152' border='0' /></td>
                <td align='right'><a href='http://www.os4ed.com' target=_blank ><img src='assets/os4ed_logo.png' height='62' width='66' border='0'/></a></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td class='content'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                      <td class='header_padding'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                          <tr>
                            <td class='header_txt'>Student Information System</td>
                          </tr>
                        </table></td>
                    </tr>
                    <tr>
                      <td class='padding'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                          <tr>
                            <td>
                
				<table border='0' cellspacing='2' cellpadding='2' align=center>
                 
                  <tr>
                    <td>User Name :</td>
                    <td><input name='USERNAME' type='text' class='login_txt'></td>
                  </tr>
                  <tr>
                    <td>Password :</td>
                    <td><input name='PASSWORD' class='login_txt' type='password'></td>
                  </tr>
				  <tr><td colspan=2>
				  " ;
				  
				  if($_REQUEST['reason'])
				$note[] = 'You must have javascript enabled to use openSIS.';
				echo ErrorMessage($error,'Error');
	
				  echo "
				  </td></tr><tr>
                    <td></td>
                    <td><input name='' type='submit' class='login' value='' onMouseDown=Set_Cookie('dhtmlgoodies_tab_menu_tabIndex','',-1) />
                    </td>
                  </tr>
				  </table>
				  </td>
                          </tr>
                          <tr>
                            <td align='center'><p style='padding:6px;'>This is a restricted network. Use of this network, its equipment, and resources is monitored at all times and requires explicit permission from the network administrator. 
                              If you do not have this permission in writing, you are violating the regulations of this network and can and will be prosecuted to the fullest extent of law. By continuing into this system, you are acknowledging that you are aware of and agree to these terms.</p></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table>
              </tr>
            </table>
        <tr>
          <td class='footer' valign='top'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td class='margin'></td>
              </tr>
              <tr>
                <td align='center' class='copyright'>
                Copyright &copy; 2007-2008 Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).
                Opensis is licensed under the <a href='http://www.gnu.org/licenses/gpl.html'>GPL License</a>.
                </td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
</td>
</tr>
</table></form>
";

	
	
	
	Warehouse("footer");
	
?>
