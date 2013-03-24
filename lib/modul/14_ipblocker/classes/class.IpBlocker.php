<?php
/**
 * File für die Klasse IPBlocker
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse zum administrieren der geblockten IPs
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 1.0     
 */
class IpBlocker
{
	/**
	 * Konstruktor
	 *
	 */
	public function __Construct()
	{
		if($_GET['action'] == 'save')
		{
			$this -> Save();
		}
		$this -> Show();
	}
	
	/**
	 * Zeigt die gesperrten IPs an
	 *
	 */
	private function Show()
	{
		$file = functions::GetIniParam('gesperrteIpsFile');
		if(!file_exists($file))
		{
			functions::output_fehler('File mit geblockten IP\'s existiert nicht!');
			return;
		}
		functions::output_var('IpBlocker', file_get_contents($file));
	}
	
	/**
	 * Speichert die gesperrten IPs
	 *
	 */
	private function Save()
	{
		$file = functions::GetIniParam('gesperrteIpsFile');
		if(!file_exists($file))
		{
			functions::output_fehler('File mit geblockten IP\'s existiert nicht!');
			return;
		}

		if(file_put_contents($file, functions::GetVar($_POST, 'ips')))
		{
			functions::Output_bericht('Liste der geblockten IP\'s erfolgreich gespeichert!');
			return ;
		}
		functions::Output_warnung('Liste der geblockten IP\'s konnte nicht gespeichert werden!');
	}
	
}

?>