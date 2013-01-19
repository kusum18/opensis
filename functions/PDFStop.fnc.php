<?php

function PDFStop($handle)
{	global $OutputType,$htmldocAssetsPath;

	if($OutputType=="PDF")
	{
		$html = ob_get_contents();
		ob_end_clean();
		$html =  '<HTML><BODY>'.$html.'</BODY></HTML>';
		require_once("dompdf/dompdf_config.inc.php");
		//require_once("convertcharset/ConvertCharset.class.php");
		
		//$html = $convertcharset->Convert($html, 'utf-8', 'iso-8859-1');
		/*
		$temphtml = "tmp/ani.htm";
		$fp = @fopen($temphtml,"w");
		if (!$fp)
			die("Can't open $temphtml");
		fputs($fp, $html);
		@fclose($fp);
		*/
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->render();
		$dompdf->stream(ProgramTitle().".pdf", array("Attachment" => 0));
		
		
		//header("Location:dompdf/dompdf.php?input_file=tmp/ani.htm&output_file=sample.pdf");
		
	}
	else
	{
	 	
		$html = ob_get_contents();
		ob_end_clean();
		$html =  '<HTML><BODY>'.$html.'</BODY></HTML>';
		echo $html;
	}
}
?>