<?php
/**
 * Modulbase-Klasse für Öffentliche Modul-Klassen
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * OpenModulBase
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.0
 * @copyright	2006-2009, Stefan Schöb
 * @since 		2.0     
 */
class OpenModulBase extends ModulBase 
{
	protected $checkLaw = true;
	
	public function __construct()
	{
		parent::__construct();
		
		
	}
	
	/**
	 * Sämtliche Anfragen auf Save werden mit einer Warnung zur Übersicht weitergeleitet
	 *
	 */
	protected function Save()
	{
		MySmarty::GetInstance() -> OutputWarning('Speicher-Funktion nicht verf&uuml;gbar!');
		$this -> Overview();
	}
	
	/**
	 * Sämtliche Anfragen auf Delete werden mit einer Warnung zur Übersicht weitergeleitet
	 *
	 */
	protected function Delete()
	{
		MySmarty::GetInstance() -> OutputWarning('L&ouml;schfunktion nicht verf&uuml;gbar!');
		$this -> Overview();
	}
	
	/**
	 * Sämtliche Anfragen auf Add werden mit einer Warnung an die Übersicht weitergeleitet
	 *
	 */
	protected function Add()
	{
		MySmarty::GetInstance() -> OutputWarning('Add Funktion nicht verf&uuml;gbar!');
		$this -> Overview();
	}
	
	public function SetCheckLaw($value)
	{
		$this -> checkLaw = $value;
	}
}