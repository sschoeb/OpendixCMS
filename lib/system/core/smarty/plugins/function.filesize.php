<?php

function smarty_function_filesize($params, &$smarty)
{
	$k = 0;
	$groessen = array(' Bytes', ' KB', ' MB', ' GB', ' TB');
	while($params['bytes'] > 1023)
	{
		$params['bytes'] /= 1024;
		$k++;
	}

	$params['bytes'] = round($params['bytes'], 2);			//Runden damit es nicht heisst: 23.98691726397  MB
	return $params['bytes'] .= $groessen[$k];
}