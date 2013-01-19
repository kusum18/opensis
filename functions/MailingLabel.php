<?php

function MailingLabel($address_id)
{	global $THIS_RET,$_CENTRE;

	$student_id = $THIS_RET['STUDENT_ID'];
	if($address_id && !$_CENTRE['MailingLabel'][$address_id][$student_id])
	{
		$people_RET = DBGet(DBQuery("SELECT a.ADDRESS_ID,p.PERSON_ID,
			coalesce(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,coalesce(a.MAIL_CITY,a.CITY) AS CITY,coalesce(a.MAIL_STATE,a.STATE) AS STATE,coalesce(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE,a.PHONE,
				p.LAST_NAME,p.FIRST_NAME,p.MIDDLE_NAME
			FROM ADDRESS a,PEOPLE p,STUDENTS_JOIN_PEOPLE sjp
			WHERE a.ADDRESS_ID='$address_id' AND a.ADDRESS_ID=sjp.ADDRESS_ID AND p.PERSON_ID=sjp.PERSON_ID
				AND sjp.CUSTODY='Y' AND sjp.STUDENT_ID='".$student_id."'"),array(),array('LAST_NAME'));

		if(count($people_RET))
		{
			foreach($people_RET as $last_name=>$people)
			{
				for($i=1;$i<count($people);$i++)
					$return .= $people[$i]['FIRST_NAME'].' &amp; ';
				$return .= $people[$i]['FIRST_NAME'].' '.$people[$i]['LAST_NAME'].'<BR>';
			}
			// mab - this is a bit of a kludge but insert an html comment so people and address can be split later
			$return .= '<!-- -->'.$people[$i]['ADDRESS'].'<BR>'.$people[$i]['CITY'].', '.$people[$i]['STATE'].' '.$people[$i]['ZIPCODE'];
		}

		$_CENTRE['MailingLabel'][$address_id][$student_id] = $return;
	}

	return $_CENTRE['MailingLabel'][$address_id][$student_id];
}
?>