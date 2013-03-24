<?php
/**
 * Sponsoren
 * 
 *
 * @package    	OpendixCMS
 * @author     	Stefan Sch�b <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Sch�b
 * @version    	1.1
 */

/**
 * Klasse zum anezigen von Sponsoren
 * 
 * @author 		Stefan Sch�b
 * @package 	OpendixCMS
 * @version 	2.0
 * @copyright 	Copyright &copy; 2006, Stefan Sch�b
 * @since 		1.0     
 */

class OpenSponsor extends OpenModulBase
{
	
	public function __construct()
	{
		parent::__construct ();
		
		CssImport::ImportCss('content.css');
	}
	
	/**
	 * Zeigt die Liste s�mtlicher Verf�gbaren Sponsoren an. Diese werden jeweils in
	 * Gruppen zusammengefasst
	 */
	protected function Overview()
	{
		$data = null;
		$sqlMan = SqlManager::GetInstance ();
		try
		{
			$data = $sqlMan->Select ( 'modsponsorgruppe', array ('id', 'name' ), '', 'folge' );
			for($i = 0; $i < count ( $data ); $i ++)
			{
				$data [$i] ['items'] = $sqlMan->Select ( 'modsponsor', array ('name', 'url', 'bild', 'beschreibung' ), 'gId=\'' . $data [$i] ['id'] . '\'' );
				for($k = 0; $k < count ( $data [$i] ['items'] ); $k ++)
				{
					if ($data [$i] ['items'] [$k] ['bild'] != '')
						$data [$i] ['items'] [$k] ['bild'] = Filebase::GetFilePath ( $data [$i] ['items'] [$k] ['bild'], true );
				}
				$data[$i]['items'][count($data[$i]['items'])-1]['last'] = true;
			}
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', $ex );
		}
		MySmarty::GetInstance ()->OutputModuleVar ( 'data', $data );
	}
	
	/**
	 * Gibt einen einzelnen zuf�lligen Sponsor zur�ck
	 */
	public static function GetRandomSponsor()
	{
		$data = null;
		$whereStmt = "anzuzeigen > '0' OR anzuzeigen = '-1'";
		$sqlMan = SqlManager::GetInstance ();
		try
		{
			// Abfragen aller Sponsoren die angezeigt werden k�nnen
			$data = $sqlMan->Select ( 'modsponsor', array ('id' ), $whereStmt );
			if (count ( $data ) == 0)
				return;
			
			shuffle ( $data );
			$data = $sqlMan->SelectRow ( 'modsponsor', $data [0] ['id'] );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', $ex );
		}
		
		return $data;
	}
	
	/**
	 * Funktion �bergibt einen zuf�lligen Sponsor dem Template und erh�ht
	 * den angezeigt-Count des Sponsors entsprechend
	 */
	public static function ShowRandomSponsor()
	{
		$sponsor = OpenSponsor::GetRandomSponsor ();
		
		$sqlMan = SqlManager::GetInstance ();
		
		// Falls der Sponsor nicht ewig angezeigt werden soll sowie der
		// anzuzeigen-Count nicht bereits 0 ist wird der Count um eins runtergez�hlt
		if ($sponsor ['anzuzeigen'] != 0 && $sponsor ['anzuzeigen'] != - 1)
		{
			$sponsor ['anzuzeigen'] --;
		}
		
		try
		{
			$sqlMan->Update ( 'modsponsor', array ('angezeigt' => $sponsor ['angezeigt'] + 1, 'anzuzeigen' => $sponsor ['anzuzeigen'] ), 'id=\'' . $sponsor ['id'] . '\'' );
		} catch ( SqlManagerException $e )
		{
			throw new CMSException ( 'SQL-Fehler', $e );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'randomsponsor', $sponsor );
	}
}