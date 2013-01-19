<?php
error_reporting(0);
session_start();
$_SESSION['db'] = $_POST['sdb'];
$dbconn = mysql_connect($_SESSION['server'],$_SESSION['username'],$_SESSION['password']) or die() ;

mysql_select_db($_SESSION['db']);

include('opensis-4.5-upgrade.php');
include('reset_auto_increment.php');

header('Location: step5.php');
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
