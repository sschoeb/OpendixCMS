<?php

/**
 * Badwordliste für das Gästebuch
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse mit der die Badwordliste für das Gästebuch verwaltet werden kann
 * 
 * @author 	Stefan Schöb
 * @package OpendixCMS
 * @version 1.1
 * @copyright 2006-2009, Stefan Schöb
 * @since 1.0     
 */
class Badword
{
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{
		if(!isset($_GET['action']))
		{
			$this -> Show();
			return;
		}
		
		switch ($_GET['action'])
		{
			case 'save':
				$this -> Save();
				break;
			default:
				throw new CMSException('Unbekannte Aktion!', CMSException::T_MODULEERROR );
				break;
		}
	}
	
	/**
	 * Speichert die aktuelle Liste ab
	 *
	 */
	private function Save()
	{
		$badwordlist = Cms::GetInstance() -> GetConfiguration() -> Get('Gbook/badwordlist');
		file_put_contents($badwordlist, Validator::ForgeInput($_POST['badwordlist']));
	}
	
	/**
	 * Zeigt die aktuelle Liste an
	 *
	 */
	private function Show()
	{
		$badwordlist = Cms::GetInstance() -> GetConfiguration() -> Get('Gbook/badwordlist');
		MySmarty::GetInstance() -> OutputModuleVar('badwordlist', file_get_contents($badwordlist));
	}
}