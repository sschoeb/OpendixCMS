<?php

class CssImport
{
	public static $css = array();
	
	public static function ImportCss($css)
	{
		$templatePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/templateFolder');
		
		$path = $templatePath . Cms::GetInstance() -> GetTemplate() -> GetTemplatePath();
		$datei = $path . '/css/' . $css;
		
		if(!file_exists($datei))
		{
			throw new CMSException('CSS-Daten nicht gefunden "'. $datei .'"', CMSException::T_WARNING);
		}
		
		CssImport::$css[] = $datei;	
		MySmarty::GetInstance() -> OutputSystemVar('css', CssImport::$css);
	}
}
