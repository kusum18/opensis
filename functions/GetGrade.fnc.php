<?php

function GetGrade($grade,$column='TITLE')
{	global $_CENTRE;
	
	if($column!='TITLE' && $column!='SHORT_NAME' && $column!='SORT_ORDER')
		$column = 'TITLE';

	if(!$_CENTRE['GetGrade'])
	{
		$QI=DBQuery("SELECT ID,TITLE,SORT_ORDER,SHORT_NAME FROM SCHOOL_GRADELEVELS");
		$_CENTRE['GetGrade'] = DBGet($QI,array(),array('ID'));
	}
	if($column=='TITLE')
		$extra = '<!-- '.$_CENTRE['GetGrade'][$grade][1]['SORT_ORDER'].' -->';

	return $extra.$_CENTRE['GetGrade'][$grade][1][$column];
}
?>