<?php
function install_module($dir, $filename)
{
 	$app_ver =DBGet(DBQuery("select * from app"));
	$ver = $app_ver[1]['VALUE'];
	$bld = $app_ver[4]['VALUE'];

if (file_exists($dir."/".$filename)) {
    require($dir."/".$filename);
    
    if($version && $version==$ver)
	{
		//if($build > $bld)
		//{
		 //Starting our code here
		 	//Installing the SQL
			if($sql && file_exists($dir."/".$sql))
			{
				$myFile = $dir."/".$sql;
				$rsql = file_get_contents($myFile );
				DBQuery($rsql);
				unlink($myFile);
			}
						
			//Coping the files
			if($files)
			$cnt = count($files);
			
			if($cnt > 0)
			{
				for($i=0; $i<$cnt; $i++)
				{
				 	if(file_exists($dir."/".$files[$i]))
				 	{
					rename($files[$i], $files[$i].".".$ver);
					copy($dir."/".$files[$i], $files);
					unlink($dir."/".$files[$i]);					
					echo "Successfully changed ".$files[$i]." <br>";
					}
					else
					echo "The requested file ".$files[$i]." is not available with the update. <br>";
				}
			}
			
			//For Coping Dirs
			if($dirs)
			$cnt = count($dirs);
			
			if($cnt > 0)
			{
				for($i=0; $i<$cnt; $i++)
				{
						$install = @ dir($dir."/".$dirs[$i]);
				 while (($file = $install->read()) !== false)
					{	
					 	if(file_exists($dirs[$i]."/".$file))
					 	{
							rename($dirs[$i]."/".$file, $dirs[$i]."/".$file.".".$ver);
							copy($dir."/".$dirs[$i]."/".$file, $dirs[$i]."/".$file);
							unlink($dir."/".$dirs[$i]."/".$file);					
							echo "Successfully changed ".$dirs[$i]."/".$file." <br>";
						}
						else
						{
							copy($dir."/".$dirs[$i]."/".$file, $dirs[$i]."/".$file);
							unlink($dir."/".$dirs[$i]."/".$file);					
							echo "Successfully copied ".$dirs[$i]."/".$file." <br>";
						}
					}
					$install->close();
					rmdir($dir."/".$dirs[$i]);
				}
			}
			
			$sql = "update app set value='".date('l F d, Y')."' where name='last_updated'";
			DBQuery($sql);
			unlink($dir."/".$filename);
			
			echo "<br><br><b>All the updates are installed successfully. Please restart the application to take effect</b>";
		//}	
		//else
		//echo "The application has higher version than the update";
	}
	else
	echo "Version mismatch";

} else {
    echo "<b>The file <font color=red>$filename</font> does not exist or corruped update</b>";
}
}
?>