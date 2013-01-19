<?php
require_once "zip/dUnzip2.inc.php";
require_once "zip/dZip.inc.php";

function unzip_install($filename, $tmpdir)
{
	$zip = new dUnzip2($filename);

	// Activate debug
	//$zip->debug = true;

	// Unzip all the contents of the zipped file to a new folder called "uncompressed"
	$zip->getList();
	$zip->unzipAll($tmpdir);
	echo "<b>Unpacked the zipped file</b><br>";
}

?>