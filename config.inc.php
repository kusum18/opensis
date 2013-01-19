<?php
if(CONFIG_INC==0)
{
	define('CONFIG_INC',1);
	// IgnoreFiles should contain any names of files or folders
	// which should be ignored by the function inclusion system.
	$IgnoreFiles = Array('.DS_Store','CVS','.svn');
    if (file_exists("data.php"))
    {
        include("data.php");
    }
	include("database.inc.php");
    include("upgrade.inc.php");

	$CentrePath = dirname(__FILE__).'/';
	$CentreVersion = Version();
	$builddate = BuildDate();//"November 3, 2008";
	$htmldocPath = "";
	$OutputType = "HTML"; //options are HTML or PDF
	$htmldocPath = '';
	$htmldocAssetsPath = '';		// way htmldoc accesses the assets/ directory, possibly different than user - empty string means no translation
	$StudentPicturesPath = 'assets/StudentPhotos/';
	$UserPicturesPath = 'assets/UserPhotos/';
	$CentreTitle = 'openSIS Student Information System';
	$CentreAdmins = '1';			// can be list such as '1,23,50' - note, these should be id's in the DefaultSyear, otherwise they can't login anyway
	$CentreNotifyAddress = '';

	$CentreModules = array(
		'School_Setup'=>true,
		'Students'=>true,
		'Users'=>true,
		'Scheduling'=>true,
		'Grades'=>true,
		'Attendance'=>true,
		'Eligibility'=>true,
		'Food_Service'=>false,
		'Discipline'=>false,
		'Billing' =>false,
		'EasyCom' =>false,
		'Tools'=>true,
		'Student_Billing'=>false,
		'State_Reports'=>false,
		'Custom'=>false
	);

	// If session isn't started, start it.
	if(!isset($SessionStart))
		$SessionStart = 1;
}
?>