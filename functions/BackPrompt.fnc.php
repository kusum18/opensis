<?php

function BackPrompt($message)
{
	echo "<SCRIPT language=javascript>window.history.back();alert(\"$message\");</SCRIPT>";
	exit();
}
?>