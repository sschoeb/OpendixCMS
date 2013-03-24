<?php

define('LIBPATH', '../../../../lib/');
define('FILEBASE', '../../../../filebase/');
define('INIFILE', '../../../../../scgams/config.ini.php');

include_once('../../../../lib/loader.php');

SqlManager::GetInstance() -> Connect();

$id = functions::GetVar($_GET, 'gId');
if(!is_numeric($id))
{
	die('FEHLERHAFTE ID ANGEGEBEN!');
}

$dir 		= '../../../../temp/bildertemp/';
$importer 	= new BildInput();
$files 		= scandir($dir);
$count = count($files);
for($i=0; $i<$count;$i++)
{
	if($files[$i] == '.' || $files[$i] == '..' || substr(trim($files[$i]), 0, 1) == '.')
	{
		unset($files[$i]);
	}
}

if(count($files) == 0)
{
	die('3');
}

sort($files);

$importer -> import($files[0], $id);