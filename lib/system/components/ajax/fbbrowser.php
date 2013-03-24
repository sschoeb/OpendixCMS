<?php

include_once('../lib/system/core/class.FileManager.php');

$folder = $_GET['folder'];

$path = AddSlash('../filebase/' . $folder);
$files = FileManager::GetFileList($path);

if($folder != '')
{
	array_unshift($files, '..');
}

$xmlDoc = new DOMDocument();
$root = $xmlDoc->createElement('filebrowser');
$root = $xmlDoc->appendChild($root);



$c=count($files);

for($i=0;$i<$c;$i++)
{
	$item = null;
	if(is_dir($path . $files[$i]))
	{
		if($files[$i] == '..')
		{
			$item =  $xmlDoc -> createElement('item', $files[$i]);
		}
		else 
		{
			$item =  $xmlDoc -> createElement('item', AddSlash($folder) . $files[$i] . '/');
		}
		
		$item -> setAttribute('type', 'folder');
		
	}
	else 
	{
		$item =  $xmlDoc -> createElement('item', AddSlash($folder) .  $files[$i]);
		$item -> setAttribute('type', 'file');
		
	}
	$root -> appendChild($item);
}
$data = $xmlDoc -> saveXML();


header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate( "D, d M Y H:i:s" ) . 'GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Length: '.strlen($data));
header('Content-Type: text/xml');
 
echo $data;


function AddSlash($folder)
{
	if(strrpos($folder, '/') == strlen($folder) -1)
	{
		return $folder;
	}
	return $folder . '/';
}