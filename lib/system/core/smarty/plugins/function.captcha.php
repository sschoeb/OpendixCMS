<?php
/*
	Published by Pavlos Stamboulides under GPL2
	pavlos@psychology.deletethis.gr
*/

require_once('captcha.class.php');


function smarty_function_captcha($params, &$smarty){
	$length = ((int)$params['length'])? (int)$params['length']: 4;
	$name = ($params['name'])? $params['name'] : 'captcha';
	$font = GetPath();
	
	$tempfolder = "temp/captcha";
	
	$c = new Captcha($length);
	$code = $c->GenStr();
	$c->font = $font ;
	$salted = md5($code . CAPTCHA_SALT . 'some salt' . $code .CAPTCHA_SALT . 'extra salt');
	
	$code = $c->Generate("$tempfolder/$salted.png");
	return "<img src=\"lib/system/core/smarty/plugins/captcha.php?cap=$salted\" /><input type=hidden name=\"$name\" value=\"$salted\" />";

}

function GetPath()
{
	$fontspath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/fontsfolder');
	$fonts = FileManager::GetFileList($fontspath);
	return $fontspath . $fonts[rand(0, count($fonts) - 1)];
}


?>