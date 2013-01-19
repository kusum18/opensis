<?php

function PDFStart($options="--webpage --quiet -t pdf12 --jpeg --no-links --portrait --footer t --header . --left 0.5in --top 0.5in")
{
	$_REQUEST['_CENTRE_PDF'] = 1;
	$pdfitems['options']=$options;
	ob_start();
	echo "<link rel='stylesheet' type='text/css' href='styles/export.css'><body style=\" font-family:Arial; font-size:12px;\">"; // font style hack merlinvicki
	return $pdfitems;
}
?>
