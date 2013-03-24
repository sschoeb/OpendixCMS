<?php

/**
 * Z�hlt die Codezeilen und gibt diese aus
 * 
 * @version 	1.0
 * @author 		Stefan Schöb <opendix@gmail.com>
 * @package 	Opendix.Addin
 * 
 */

$folders = array('lib/', 'config/', 'templates/');
$files = array('index.php');
$not = array('lib/javascript/prototype.js', 
				'lib/system/wysiwyg/', 
				'lib/system/core/smarty/',
				'lib/system/pear/', 
				'templates/template1/c/',
				'templates/template2/c/',
				'templates/template3/c/', 
				'lib/modul/11_newsfeed/classes/newsfeed/');

$errors = array();
$lines = 0;

$c=count($folders);
for($i=0;$i<$c;$i++)
{
	$files = array_merge($files,DoFolder($folders[$i], $not) );
}

$emptyLines = 0;
$brackLines = 0;

$c=count($files);
for($i=0;$i<$c;$i++)
{
	$data = file($files[$i]);
	$fileLines = count($data);

	for($j=0;$j<$fileLines;$j++)
	{
		$line = trim($data[$j]);
		if(strlen($line) == 0)
			$emptyLines++;
		
		if($line == "}" || $line == '{')
			$brackLines++;
	}
	$lines += $fileLines;
	echo $fileLines . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$files[$i] .  '<br />';
}
echo '---------------------------------------------<br />';
echo 'Total: ' . $lines . ' Lines <br />';
echo 'Davon leere Zeilen: ' . $emptyLines . '<br/>';
echo 'Davon Klammer-Zeilen: ' . $brackLines;

function DoFolder($folderName, $not)
{
	
	$files = array();
	$items = scandir($folderName);
	$c=count($items);
	for($i=0;$i<$c;$i++)
	{
		if($items[$i] == '.' ||$items[$i] == '..' || $items[$i] == '.svn')
		{
			continue;
		}
	
		
		if(in_array($folderName .$items[$i] . '/', $not) || in_array($folderName .$items[$i], $not))
		{
			continue;
		}
		
		if(is_dir($folderName .$items[$i]))
		{
			
			$files = array_merge($files,DoFolder($folderName . $items[$i] .'/' , $not));
			continue;
		}
		$files[] = $folderName . $items[$i];
	}
	return $files;
}