<?php
$text = "
UPDATE `STAFF`    SET password = md5(password) WHERE length(password) < 32;
UPDATE `STUDENTS` SET password = md5(password) WHERE length(password) < 32;

UPDATE `APP` SET value = '4.5.0'        WHERE name = 'version';
UPDATE `APP` SET value = '2009-07-27'   WHERE name = 'date';
UPDATE `APP` SET value = '07272009000'  WHERE name = 'build';
UPDATE `APP` SET value = 'Jul 27, 2009' WHERE name = 'last_updated';
";

	$sqllines = split("\n",$text);
	$cmd = '';
	foreach($sqllines as $l)
	{
		if(preg_match('/^\s*--/',$l) == 0)
		{
			$cmd .= ' ' . $l . "\n";
			if(preg_match('/.+;/',$l) != 0)
			{
				$result = mysql_query($cmd);
				$cmd = '';
			}
		}
	}
?>
