<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. It is  web-based, 
#  open source, and comes packed with features that include student 
#  demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  Copyright (C) 2007-2008, Open Solutions for Education, Inc.
#
#*************************************************************************
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, version 2 of the License. See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#**************************************************************************
// Establish DB connection.
function db_start()
{	global $DatabaseServer,$DatabaseUsername,$DatabasePassword,$DatabaseName,$DatabasePort,$DatabaseType;

	switch($DatabaseType)
	{
		case 'oracle':
			$connection = @ocilogon($DatabaseUsername,$DatabasePassword,$DatabaseServer);
		break;
		case 'postgres':
			//if($DatabaseServer!='localhost') //use for windows
			if($DatabaseServer!='host') //updated for linux 
				$connectstring = "host=$DatabaseServer ";
			if($DatabasePort!='5432')
				$connectstring .= "port=$DatabasePort ";
			$connectstring .= "dbname=$DatabaseName user=$DatabaseUsername";
			if(!empty($DatabasePassword))
				$connectstring.=" password=$DatabasePassword";
			$connection = pg_connect($connectstring);
		break;
		case 'mysql':
			$connection = mysql_connect($DatabaseServer,$DatabaseUsername,$DatabasePassword);
			mysql_select_db($DatabaseName);
		break;
	}

	// Error code for both.
	if($connection === false)
	{
		switch($DatabaseType)
		{
			case 'oracle':
				$errors=OciError();
				$errormessage = $errors['message'];
			break;
			case 'postgres':
				$errormessage = pg_last_error($connection);
			break;
			case 'mysql':
				$errormessage = mysql_error($connection);
			break;
		}
		db_show_error("","Could not Connect to Database: $DatabaseServer",$errstring);
	}
	return $connection;
}

// This function connects, and does the passed query, then returns a connection identifier.
// Not receiving the return == unusable search.
//		ie, $processable_results = DBQuery("select * from students");
function DBQuery($sql)
{	global $DatabaseType,$_CENTRE;

	$connection = db_start();

	switch($DatabaseType)
	{
		case 'oracle':
			$result = @ociparse($connection, $sql);
			if($result === false)
			{
				$errors = OCIError($connection);
				db_show_error($sql,"DB Parse Failed.", $errors['message']);
			}
			if(!@OciExecute($result))
			{
				$errors = OCIError($result);
				db_show_error($sql,"DB Execute Failed.", $errors['message']);
			}
			OciCommit($connection);
			OciLogoff($connection);
		break;
		case 'postgres':
			$sql = ereg_replace("([,\(=])[\r\n\t ]*''",'\\1NULL',$sql);
			$result = @pg_exec($connection,$sql);
			if($result===false)
			{
			 	echo $sql;
				$errstring = pg_last_error($connection);
				db_show_error($sql,"DB Execute Failed.", $errstring);
			}
		break;
		case 'mysql':
			$sql = ereg_replace("([,\(=])[\r\n\t ]*''",'\\1NULL',$sql);
			if(preg_match_all("/'(\d\d-[A-Za-z]{3}-\d{2,4})'/",$sql,$matches))
				{
					foreach($matches[1] as $match)
					{
						$dt = date('Y-m-d',strtotime($match));
						$sql = preg_replace("/'$match'/","'$dt'",$sql);
					}
				}
			if(substr($sql,0,6)=="BEGIN;")
			{
				$array = explode( ";", $sql );
				foreach( $array as $value )
				{
					if($value!="")
					{
						$result = mysql_query($value);
						if(!$result)
						{
							mysql_query("ROLLBACK");
							die(db_show_error($sql,"DB Execute Failed.",mysql_error()));
						}
					}
				}
			}
			else
			{
				$result = mysql_query($sql) or die(db_show_error($sql,"DB Execute Failed.",mysql_error()));
			}
		break;
	}
	return $result;
}

// return next row.
function db_fetch_row($result)
{	global $DatabaseType;

	switch($DatabaseType)
	{
		case 'oracle':
			OCIFetchInto($result,$row,OCI_ASSOC+OCI_RETURN_NULLS);
			$return = $row;
		break;
		case 'postgres':
			$return=@pg_fetch_array($result);
			if(is_array($return))
			{
				foreach($return as $key => $value)
				{
					if(is_int($key))
						unset($return[$key]);
				}
			}
		break;
		case 'mysql':
			$return = mysql_fetch_array($result);
			if(is_array($return))
			{
				foreach($return as $key => $value)
				{
					if(is_int($key))
						unset($return[$key]);
				}
			}
		break;
	}
	return @array_change_key_case($return,CASE_UPPER);
}

// returns code to go into SQL statement for accessing the next value of a sequenc	function db_seq_nextval($seqname)
function db_seq_nextval($seqname)
{	global $DatabaseType;

	if($DatabaseType=='oracle')
		$seq=$seqname.".nextval";
	elseif($DatabaseType=='postgres')
		$seq="nextval('".$seqname."')";
	elseif($DatabaseType=='mysql')
		$seq="fn_".strtolower($seqname)."()";
	return $seq;
}

// start transaction
function db_trans_start($connection)
{	global $DatabaseType;

	if($DatabaseType=='postres')
		db_trans_query($connection,"BEGIN WORK");
}

// run query on transaction -- if failure, runs rollback.
function db_trans_query($connection,$sql)
{	global $DatabaseType;

	if($DatabaseType=='oracle')
	{
		$parse = ociparse($connection,$sql);
		if($parse===false)
		{
			db_trans_rollback($connection);
			db_show_error($sql,"DB Transaction Parse Failed.");
		}
		$result=OciExecute($parse,OCI_DEFAULT);
		if ($result===false)
		{
			db_trans_rollback($connection);
			db_show_error($sql,"DB Transaction Execute Failed.");
		}
		$result=$parse;
	}
	elseif($DatabaseType=='postgres')
	{
		$sql = ereg_replace("([,\(=])[\r\n\t ]*''",'\\1NULL',$sql);
		$result = pg_query($connection,$sql);
		if($result===false)
		{
			db_trans_rollback($connection);
			db_show_error($sql,"DB Transaction Execute Failed.");
		}
	}

	return $result;
}

// rollback commands.
function db_trans_rollback($connection)
{	global $DatabaseType;

	if($DatabaseType=='oracle')
		OCIRollback($connection);
	elseif($DatabaseType=='postgres')
		pg_query($connection,"ROLLBACK");
}

// commit changes.
function db_trans_commit($connection)
{	global $DatabaseType;

	if($DatabaseType=='oracle')
		OCICommit($connection);
	elseif($DatabaseType=='postgres')
		pg_query($connection,"COMMIT");
}

// keyword mapping.
if($DatabaseType=='oracle')
	define("FROM_DUAL"," FROM DUAL ");
else
	define("FROM_DUAL"," ");

// DECODE and CASE-WHEN support
function db_case($array)
{	global $DatabaseType;

	$counter=0;
	if($DatabaseType=='postgres' || $DatabaseType=='mysql')
	{
		$array_count=count($array);
		$string = " CASE WHEN $array[0] =";
		$counter++;
		$arr_count = count($array);
		for($i=1;$i<$arr_count;$i++)
		{
			$value = $array[$i];

			if($value=="''" && substr($string,-1)=='=')
			{
				$value = ' IS NULL';
				$string = substr($string,0,-1);
			}

			$string.="$value";
			if($counter==($array_count-2) && $array_count%2==0)
				$string.=" ELSE ";
			elseif($counter==($array_count-1))
				$string.=" END ";
			elseif($counter%2==0)
				$string.=" WHEN $array[0]=";
			elseif($counter%2==1)
				$string.=" THEN ";

			$counter++;
		}
	}
	else
	{
		$string=" decode( ";
		foreach($array as $value)
			$string.="$value,";
		$string[strlen($string)-1]=")";
		$string.=" ";
	}
	return $string;
}

// String position.
function db_strpos($args)
{	global $DatabaseType;

	if($DatabaseType=='postgres')
		$ret = 'strpos(';
	else
		$ret = 'instr(';

	foreach($args as $value)
		$ret .= $value . ',';
	$ret = substr($ret,0,-1) . ')';

	return $ret;
}

// CONVERT VARCHAR TO NUMERIC
function db_to_number($text)
{	global $DatabaseType;

	if($DatabaseType=='postgres')
		return '('.$text.')::text::float::numeric';
	else
		return 'to_number('.$text.')';
}

// returns an array with the field names for the specified table as key with subkeys
// of SIZE, TYPE, SCALE and NULL.  TYPE: varchar, numeric, etc.
function db_properties($table)
{	global $DatabaseType,$DatabaseUsername;

	switch($DatabaseType)
	{
		case 'oracle':
			$sql="SELECT COLUMN_NAME, DATA_TYPE, DATA_LENGTH, DATA_PRECISION,
				DATA_SCALE, NULLABLE, DATA_DEFAULT
				FROM ALL_TAB_COLUMNS WHERE TABLE_NAME='".strtoupper($table)."'
				AND OWNER='".strtoupper($DatabaseUsername)."' ORDER BY COLUMN_ID";
			$result = DBQuery($sql);
			while($row=db_fetch_row($result))
			{
				if($row['DATA_TYPE']=='VARCHAR2')
				{
					$properties[$row['COLUMN_NAME']]['TYPE'] = "VARCHAR";
					$properties[$row['COLUMN_NAME']]['SIZE'] = $row['DATA_LENGTH'];
				}
				elseif($row['DATA_TYPE']=='NUMBER')
				{
					$properties[$row['COLUMN_NAME']]['TYPE'] = "NUMERIC";
					$properties[$row['COLUMN_NAME']]['SIZE'] = $row['DATA_PRECISION'];
					$properties[$row['COLUMN_NAME']]['SCALE'] = $row['DATA_SCALE'];
				}
				else
				{
					$properties[$row['COLUMN_NAME']]['TYPE'] = $row['DATA_TYPE'];
					$properties[$row['COLUMN_NAME']]['SIZE'] = $row['DATA_LENGTH'];
					$properties[$row['COLUMN_NAME']]['SCALE'] = $row['DATA_SCALE'];
				}
				$properties[$row['COLUMN_NAME']]['NULL'] = $row['NULLABLE'];
			}
		break;
		case 'postgres':
			$sql = "SELECT a.attnum,a.attname AS field,t.typname AS type,
					a.attlen AS length,a.atttypmod AS lengthvar,
					a.attnotnull AS notnull
				FROM pg_class c, pg_attribute a, pg_type t
				WHERE c.relname = '".strtolower($table)."'
					and a.attnum > 0 and a.attrelid = c.oid
					and a.atttypid = t.oid ORDER BY a.attnum";
			$result = DBQuery($sql);
			while($row = db_fetch_row($result))
			{
				$properties[strtoupper($row['FIELD'])]['TYPE'] = strtoupper($row['TYPE']);
				if(strtoupper($row['TYPE'])=="NUMERIC")
				{
					$properties[strtoupper($row['FIELD'])]['SIZE'] = ($row['LENGTHVAR'] >> 16) & 0xffff;
					$properties[strtoupper($row['FIELD'])]['SCALE'] = ($row['LENGTHVAR'] -4) & 0xffff;
				}
				else
				{
					if($row['LENGTH']>0)
						$properties[strtoupper($row['FIELD'])]['SIZE'] = $row['LENGTH'];
					elseif($row['LENGTHVAR']>0)
						$properties[strtoupper($row['FIELD'])]['SIZE'] = $row['LENGTHVAR']-4;
				}
				if ($row['NOTNULL']=='t')
					$properties[strtoupper($row['FIELD'])]['NULL'] = "N";
				else
					$properties[strtoupper($row['FIELD'])]['NULL'] = "Y";
			}
		break;
		case 'mysql':
			$result = DBQuery("SHOW COLUMNS FROM $table");
			while($row = db_fetch_row($result))
			{
				$properties[strtoupper($row['FIELD'])]['TYPE'] = strtoupper($row['TYPE'],strpos($row['TYPE'],'('));
				if(!$pos = strpos($row['TYPE'],','))
					$pos = strpos($row['TYPE'],')');
				else
					$properties[strtoupper($row['FIELD'])]['SCALE'] = substr($row['TYPE'],$pos+1);

				$properties[strtoupper($row['FIELD'])]['SIZE'] = substr($row['TYPE'],strpos($row['TYPE'],'(')+1,$pos);

				if($row['NULL']!='')
					$properties[strtoupper($row['FIELD'])]['NULL'] = "Y";
				else
					$properties[strtoupper($row['FIELD'])]['NULL'] = "N";
			}
		break;
	}
	return $properties;
}

function db_show_error($sql,$failnote,$additional='')
{	global $CentreTitle,$CentreVersion,$CentreNotifyAddress;

	PopTable('header','Error');
	$tb = debug_backtrace();
	$error = $tb[1]['file'] . " at " . $tb[1]['line'];
	echo "
		<TABLE CELLSPACING=10 BORDER=0>
			<TD align=right><b>Date:</TD>
			<TD><pre>".date("m/d/Y h:i:s")."</pre></TD>
		</TR><TR>
			<TD align=right><b>Failure Notice:</b></TD>
			<TD><pre> $failnote </pre></TD>
		</TR><TR>
			<TD align=right><b>SQL:</b></TD>
			<TD>$sql</TD>
		</TR>
		</TR><TR>
			<TD align=right><b>Traceback:</b></TD>
			<TD>$error</TD>
		</TR>
		</TR><TR>
			<TD align=right><b>Additional Information:</b></TD>
			<TD>$additional</TD>
		</TR>
		</TABLE>";
	//Something you have asked the system to do has thrown a database error.  A system administrator has been notified, and the problem will be fixed as soon as possible.  It might be that changing the input parameters sent to this program will cause it to run properly.  Thanks for your patience.
	PopTable('footer');
	echo "<!-- SQL STATEMENT: \n\n $sql \n\n -->";

	/*if(false && function_exists('mysql_query'))
	{
		$link = @mysql_connect('os4ed.com','centre_log','centre_log');
		@mysql_select_db('centre_log');
		@mysql_query("INSERT INTO SQL_ERROR_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME,SQL,REQUEST) values('$_SERVER[SERVER_NAME]','$_SERVER[SERVER_ADDR]','".date('Y-m-d')."','$CentreVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."','$sql','".ShowVar($_REQUEST,'Y', 'N')."')");
		@mysql_close($link);
	}*/

	if($CentreNotifyAddress)
	{
		$message = "System: $CentreTitle \n";
		$message .= "Date: ".date("m/d/Y h:i:s")."\n";
		$message .= "Page: ".$_SERVER['PHP_SELF'].' '.ProgramTitle()." \n\n";
		$message .= "Failure Notice:  $failnote \n";
		$message .= "Additional Info: $additional \n";
		$message .= "\n $sql \n";
		$message .= "Request Array: \n".ShowVar($_REQUEST,'Y', 'N');
		$message .= "\n\nSession Array: \n".ShowVar($_SESSION,'Y', 'N');
		mail($CentreNotifyAddress,'openSIS Database Error',$message);

	}

	die();
}
function Version()
{
	$query = DBQuery("select value from APP where name='version'");
	$sql = mysql_fetch_assoc($query);
	return($sql['value']);
}

function BuildDate()
{
	$query = DBQuery("select value from APP where name='build'");
	$build = mysql_fetch_assoc($query);
	$month = substr($build['value'],0,-9);
	$day = substr($build['value'],2,-7);
	$year = substr($build['value'],4,-3);
	switch($month)
	{
		case '01':
		$month = 'January';
		break;
		case '02':
		$month = 'February';
		break;
		case '03':
		$month = 'March';
		break;
		case '04':
		$month = 'April';
		break;
		case '05':
		$month = 'May';
		break;
		case '06':
		$month = 'June';
		break;
		case '07':
		$month = 'July';
		break;
		case '08':
		$month = 'August';
		break;
		case '09':
		$month = 'September';
		break;
		case '10':
		$month = 'October';
		break;
		case '11':
		$month = 'November';
		break;
		case '12':
		$month = 'December';
		break;
	}
	$build_date = $month.',&nbsp;'.$day.'&nbsp;'.$year;
	return($build_date);
}
?>
