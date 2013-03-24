<?php

/**
 * Authentifizerungsklassen
 * 
 *
 * @package    	OpendixCMS.Core
 * @author     	Stefan Sch�b <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Sch�b
 * @version    	1.0
 */

/**
 * Klasse mit der Javascripts importiert werden k�nnen
 *
 * 
 * @access   	public
 * @package 	OpendixCMS.Core
 * @version  	1.0
 */
class  JsImport
{
	/**
	 * Array in dem die bereits importierten gespeichert werden
	 *
	 * @var Array
	 */
	public static $scripts = array();
	
	/**
	 * Array in dem die bereits importierten gespeichert werden (Hier diese, die nicht verlinkt sondern komplett importiert werden)
	 *
	 * @var Array
	 */
	public static $insideScripts = array();
	
	/**
	 * Importiert ein Javascript von einem Modul
	 *
	 * @param unknown_type $datei
	 * @todo 	das mit der module-Id ist keine gute Lösung
	 */
	public static Function  ImportModuleJS($module, $datei)
	{
		//Abfrage der Module-id
		$id = JsImport::GetModuleId($module);
		$datei = 'lib/modul/' . $id . '_' . strtolower($module) . '/js/' . $datei;
		JsImport::$scripts[] = $datei;
		MySmarty::GetInstance() -> OutputSystemVar('javascript', JsImport::$scripts);
	}
	
	/**
	 * Importiert ein Javascript aus dem System
	 *
	 * @param 	String	$datei
	 */
	public static Function  ImportSystemJS($datei)
	{
		$datei = 'lib/javascript/' . $datei;
		JsImport::$scripts[] = $datei;
		MySmarty::GetInstance() -> OutputSystemVar('javascript', JsImport::$scripts);
	}
	
	public static function ImportExternalJs($datei)
	{
		JsImport::$scripts[] = $datei;
		MySmarty::GetInstance() -> OutputSystemVar('javascript', JsImport::$scripts);
	
	}
	
	public static function ImportEditorJs()
	{
		$datei = 'lib/system/wysiwyg/ckeditor.js';
		JsImport::$scripts[] = $datei;
		MySmarty::GetInstance() -> OutputSystemVar('javascript', JsImport::$scripts);
	}
	
	/**
	 * Importiert ein System-Javascript direkt -> Es wird nicht verlinkt sondern direkt eingebunden
	 *
	 * @param String $datei
	 * @static 
	 */
	public static Function ImportSystemJSInside($datei)
	{
		$datei = 'lib/javascript/' . $datei;
		JsImport::$insideScripts[] = $datei;
		MySmarty::GetInstance() -> OutputSystemVar('javascriptInside', JsImport::$insideScripts);
	}
	
	/**
	 * Importiert ein System-Javascript direkt -> Es wird nicht verlinkt sondern direkt eingebunden
	 *
	 * @param String $module
	 * @param String $datei
	 * @static 
	 */
	public static Function ImportModuleJSInside($module, $datei)
	{
		$id = JsImport::GetModuleId($module);
		$datei = 'lib/modul/' . $id . '_' . $module . '/js/' . $datei;
		JsImport::$insideScripts[] = $datei;
		MySmarty::GetInstance() -> OutputSystemVar('javascriptInside', JsImport::$insideScripts);
	}
	
	/**
	 * Gibt die ID des Moduls zur�ck
	 *
	 * @param String $module
	 * @return int
	 */
	private static  function GetModuleId($module)
	{
		$query = 'SELECT id FROM sysmodul WHERE name=\''. $module .'\'';
		$insert = mysql_query($query) OR Functions::Output_fehler('Mysql-Error:nj');
		return @mysql_result($insert, 0);
	}
	
	/**
	 * Führt eine Javascript Funktion nach laden der Seite aus
	 *
	 * @param String $function Name der Funktion inkl. Klammern und ;
	 */
	public static function RunJs($function)
	{
		static $runjs;
		$runjs[] = $function;
		MySmarty::GetInstance() -> OutputSystemVar('runatstart', $runjs);		
	}
}
