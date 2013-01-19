<?php

/*
	Call in an SQL statement to select students based on custom fields

	Use in the where section of the query by CustomFIelds('where')
*/

function CustomFields($location,$table_arr='')
{	global $_CENTRE;
	if(count($_REQUEST['month_cust_begin']))
	{
		foreach($_REQUEST['month_cust_begin'] as $field_name=>$month)
		{
			$_REQUEST['cust_begin'][$field_name] = $_REQUEST['day_cust_begin'][$field_name].'-'.$_REQUEST['month_cust_begin'][$field_name].'-'.$_REQUEST['year_cust_begin'][$field_name];
			$_REQUEST['cust_end'][$field_name] = $_REQUEST['day_cust_end'][$field_name].'-'.$_REQUEST['month_cust_end'][$field_name].'-'.$_REQUEST['year_cust_end'][$field_name];
			if(!VerifyDate($_REQUEST['cust_begin'][$field_name]) || !VerifyDate($_REQUEST['cust_end'][$field_name]))
			{
				unset($_REQUEST['cust_begin'][$field_name]);
				unset($_REQUEST['cust_end'][$field_name]);
			}
		}
		unset($_REQUEST['month_cust_begin']);unset($_REQUEST['year_cust_begin']);unset($_REQUEST['day_cust_begin']);
		unset($_REQUEST['month_cust_end']);unset($_REQUEST['year_cust_end']);unset($_REQUEST['day_cust_end']);
	}
	if(count($_REQUEST['cust']))
	{
		foreach($_REQUEST['cust'] as $key=>$value)
		{
			if($value=='')
				unset($_REQUEST['cust'][$key]);
		}
	}
	switch($location)
	{
		case 'from':
		break;

		case 'where':
		if(count($_REQUEST['cust']) || count($_REQUEST['cust_begin']))
			$fields = DBGet(DBQuery("SELECT TITLE,ID,TYPE FROM CUSTOM_FIELDS"),array(),array('ID'));

		if(count($_REQUEST['cust']))
		{
			foreach($_REQUEST['cust'] as $field_name => $value)
			{
				if($value!='')
				{
					switch($fields[substr($field_name,7)][1]['TYPE'])
					{
						case 'radio':
							$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.$fields[substr($field_name,7)][1]['TITLE'].': </b></font>';
							if($value=='Y')
							{
								$string .= " and s.$field_name='$value' ";
								$_CENTRE['SearchTerms'] .= 'Yes';
							}
							elseif($value=='N')
							{
								$string .= " and (s.$field_name!='Y' OR s.$field_name IS NULL) ";
								$_CENTRE['SearchTerms'] .= 'No';
							}
							$_CENTRE['SearchTerms'] .= '<BR>';
						break;

						case 'codeds':
							$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.$fields[substr($field_name,7)][1]['TITLE'].': </b></font>';
							if($value=='!')
							{
								$string .= " and (s.$field_name='' OR s.$field_name IS NULL) ";
								$_CENTRE['SearchTerms'] .= 'No Value';
							}
							else
							{
								$string .= " and s.$field_name='$value' ";
								$_CENTRE['SearchTerms'] .= $value;
							}
							$_CENTRE['SearchTerms'] .= '<BR>';
							break;

						case 'select':
							$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.$fields[substr($field_name,7)][1]['TITLE'].': </b></font>';
							if($value=='!')
							{
								$string .= " and (s.$field_name='' OR s.$field_name IS NULL) ";
								$_CENTRE['SearchTerms'] .= 'No Value';
							}
							else
							{
								$string .= " and s.$field_name='$value' ";
								$_CENTRE['SearchTerms'] .= $value;
							}
							$_CENTRE['SearchTerms'] .= '<BR>';
							break;

						case 'autos':
							$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.$fields[substr($field_name,7)][1]['TITLE'].': </b></font>';
							if($value=='!')
							{
								$string .= " and (s.$field_name='' OR s.$field_name IS NULL) ";
								$_CENTRE['SearchTerms'] .= 'No Value';
							}
							else
							{
								$string .= " and s.$field_name='$value' ";
								$_CENTRE['SearchTerms'] .= $value;
							}
							$_CENTRE['SearchTerms'] .= '<BR>';
							break;

						case 'edits':
							$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.$fields[substr($field_name,7)][1]['TITLE'].': </b></font>';
							if($value=='!')
							{
								$string .= " and (s.$field_name='' OR s.$field_name IS NULL) ";
								$_CENTRE['SearchTerms'] .= 'No Value';
							}
							elseif($value=='~')
							{
								$string .= " and position('\n'||s.$field_name||'\r' IN '\n'||(SELECT SELECT_OPTIONS FROM CUSTOM_FIELDS WHERE ID='".substr($field_name,7)."')||'\r')=0 ";
								$_CENTRE['SearchTerms'] .= 'Other';
							}
							else
							{
								$string .= " and s.$field_name='$value' ";
								$_CENTRE['SearchTerms'] .= $value;
							}
							$_CENTRE['SearchTerms'] .= '<BR>';
							break;

						case 'text':
							if(substr($value,0,2)=='\"' && substr($value,-2)=='\"')
							{
								$string .= " and s.$field_name='".substr($value,2,-2)."' ";
								$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.$fields[substr($field_name,7)][1]['TITLE'].': </b></font>'.substr($value,2,-2).'<BR>';
							}
							else
							{
								$string .= " and LOWER(s.$field_name) LIKE '".strtolower($value)."%' ";
								$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.$fields[substr($field_name,7)][1]['TITLE'].' starts with: </b></font>'.$value.'<BR>';
							}
						break;
					}
				}
			}
		}
		if(count($_REQUEST['cust_begin']))
		{
			foreach($_REQUEST['cust_begin'] as $field_name => $value)
			{
				if($fields[substr($field_name,7)][1]['TYPE']=='numeric')
				{
					$_REQUEST['cust_end'][$field_name] = ereg_replace('[^0-9.-]+','',$_REQUEST['cust_end'][$field_name]);
					$value = ereg_replace('[^0-9.-]+','',$value);
				}

				if($_REQUEST['cust_begin'][$field_name]!='' && $_REQUEST['cust_end'][$field_name]!='')
				{
					if($fields[substr($field_name,7)][1]['TYPE']=='numeric' && $_REQUEST['cust_begin'][$field_name]>$_REQUEST['cust_end'][$field_name])
					{
						$temp = $_REQUEST['cust_end'][$field_name];
						$_REQUEST['cust_end'][$field_name] = $value;
						$value = $temp;
					}
					$string .= " and s.$field_name BETWEEN '$value' AND '".$_REQUEST['cust_end'][$field_name]."' ";
					if($fields[substr($field_name,7)][1]['TYPE']=='date')
						$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.$fields[substr($field_name,7)][1]['TITLE'].' between: </b></font>'.ProperDate($value).' &amp; '.ProperDate($_REQUEST['cust_end'][$field_name]).'<BR>';
					else
						$_CENTRE['SearchTerms'] .= '<font color=gray><b>'.$fields[substr($field_name,7)][1]['TITLE'].' between: </b></font>'.$value.' &amp; '.$_REQUEST['cust_end'][$field_name].'<BR>';
				}
			}
		}

		break;
	}
		return $string;
}
?>
