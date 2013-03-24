<?php

/**
 * Bericht Admin-Bereich
 * 
 *
 * @package    	OpendixCMS
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	2.0
 */

/**
 * Bericht-Klasse
 * 	
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	2.0
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		1.0     
 */
class Bericht extends ModulBase 
{
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		
		CssImport::ImportCss('content.css');
		
		if(!isset($_GET['action']))
		{
			return;
		}
		
		switch ($_GET['action'])
		{
			case 'switchactive':
				$this -> SwitchActive();
				$this -> Overview();
				break;
			case 'getanhang':
				$this -> GetAttachement();
				$this -> Item();
				break;
			case 'delanhang':
				$this -> DeleteAttachement();
				$this -> Item();
				break;
			case 'removegalerie':
				$this -> DeleteGalerieLink();
				$this -> Item();
				break;
			case 'ajaxfbb':
				$this -> AJAX_fbbrowser();
				break;
		}
		
	}
	
	/**
	 * Speichert die Eigenschaften eines Berichtes ab
	 *
	 */
	protected function Save()
	{
		//Prüfen ob eine gültige ID für einen Bericht übergeben wurde
		$id = Validator::ForgeInput($_GET['id']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		//Allgemeine Daten aus dem formular auslesen
		$html 	= Validator::ForgeInput($_POST['opendixckeditor'], false);
		$group 	= Validator::ForgeInput($_POST['group']);
		$title 	= Validator::ForgeInput($_POST['title']);
		$active = 0;
		if(isset($_POST['active']))
		{
			$active = 1;
		}

		try 
		{
			SqlManager::GetInstance() -> Update('modbericht', array('gId' => $group, 
																	'html' => $html, 
																	'title' => $title,
										'active' => $active
																	), 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Speichern der allgemienen Eigenschaften fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		
		//Alle verlinkten Anhänge speichern
		if(isset($_POST['anhang']))
		{
			Functions::AddAttachement('modberichtanhang', 'berichtId', $id);
		}
		
		//Alle verlinkten Gallerien einfügen
		if(isset($_POST['glink']))
		{
			$gallerien = Validator::ForgeInput($_POST['glink']);
			foreach ($gallerien as $value)
			{
				try 
				{
					SqlManager::GetInstance() -> Insert('modberichtgallery', array('berichtId' => $id, 'gallerieId' => $value));
				}
				catch (SqlManagerException $ex)
				{
					throw new CMSException('Eine Gallerie konnte nicht verkn&uuml;pft werden!',CMSException::T_MODULEERROR ,$ex);
				}
			}
		}
		
		MySmarty::GetInstance()->OutputConfirmation('Bericht erfolgreich gespeichert');
		
	}
	
	/**
	 * Entfernt eine Verlinkung auf eine Gallerie
	 *
	 */
	private function DeleteGalerieLink()
	{
		$id = Validator::ForgeInput($_GET['gid']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$trash = new TrashItem('Gel&ouml;schte verlinkung auf eine Gallerie!');
		$trash -> AddRecord(new TrashRecord('modberichtgallery', $id));
		Trash::Delete($trash);
	}
	
	/**
	 * Zeigt einen einzelnen Bericht an
	 *
	 */
	protected  function Item()
	{
		$id = Validator::ForgeInput($_GET['id']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		JsImport::ImportEditorJs ();
		
		$daten = null;
		try 
		{
			$daten = SqlManager::GetInstance() -> SelectRow('modbericht', $id);
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Eigenschaften des Berichts konnten nicht abgefragt werden!',CMSException::T_MODULEERROR ,$ex);
		}
		
		$daten['avaibleGallerie'] 	= HTML::Select('modgalerie', 'name');
		$daten['gId'] 				= HTML::Select('modberichtgruppe', 'name', $daten['gId']);
		$daten['active'] 			= HTML::Checkbox($daten['active']);
		
		//Abfrage aller verlinkten Anhänge/Galerien
		try 
		{
			$daten['attachements'] 	= SqlManager::GetInstance() -> Select('modberichtanhang', array('id', 'fileId'), 'berichtId=\''. $id .'\'');
			$daten['gallery'] 		= SqlManager::GetInstance() -> QueryAndFetch('SELECT mbg.id AS id, mg.name AS name FROM modberichtgallery mbg, modgalerie mg WHERE mg.id=mbg.gallerieId AND mbg.berichtId=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der verlinkten Anh&auml;nge/gallerien fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		
		$c=count($daten['attachements']);
		for($i=0;$i<$c;$i++)
		{
			$daten['attachements'][$i]['links'] 			= array();
			$daten['attachements'][$i]['links']['get']	 	= Functions::GetLink(array('action' => 'getanhang', 'aid' => $daten['attachements'][$i]['fileId']), true);
			$daten['attachements'][$i]['links']['delete'] 	= Functions::GetLink(array('action' => 'delanhang', 'aid' => $daten['attachements'][$i]['id']), true);
			$daten['attachements'][$i]['name'] 				= Filebase::GetFilePath($daten['attachements'][$i]['fileId']);
		}

		if($c == 0)
		{
			
			$daten['attachements'] = '';
		}
		
		$c=count($daten['gallery']);
		for($i=0;$i<$c;$i++)
		{
			$daten['gallery'][$i]['links'] = array();
			$daten['gallery'][$i]['links']['delete'] = Functions::GetLink(array('action' => 'removegalerie', 'gid' => $daten['gallery'][$i]['id']), true);

		}
		if($c ==0)
		{
			$daten['gallery'] = '';
		}
		
		$daten['timer']['start'] 	= Timer::GetInformation($daten['timerId1']);
		$daten['timer']['stop'] 	= Timer::GetInformation($daten['timerId1']);
		
		JsImport::ImportSystemJS('fbbrowser.js');
		JsImport::ImportModuleJS('agenda', 'agenda.js');
		JsImport::ImportModuleJS('bericht', 'bericht.js');
		
		$daten['link']['save'] = Functions::GetLink(array('action' => 'save'), true);
		
		//Functions::InitEditor($daten['html']);
		MySmarty::GetInstance() -> OutputModuleVar('daten', $daten);

	}
	
	public static function AJAX_fbbrowser()
	{
		//Prüfen ob der User berechtigt ist
		$hasLaw = Cms::GetInstance() -> GetLaw() -> HasLaw(Law::T_VIEW , $_GET['sub']);
		if(!$hasLaw)
		{
			AJAX::ThrowError('Sie sind nicht berechtigt sich den Filebasebrowser anzuzeigen!');
		}
		//Ausgabe des Filebrowsers
		AJAX::Fbbrowser();
		//Da dies ein AJAX-Aufruf ist wird hier das Script abgebrochen ansonsten würde noch die ganze Seite ausgegebenw erden
		die();
	}
	
	/**
	 * Zeigt alle Berichte in der Übersicht an
	 *
	 */
	protected  function Overview()
	{
		//Erst alle Gruppen abfragen
		$groups = null;
		try 
		{
			$groups = SqlManager::GetInstance() -> Select('modberichtgruppe', array('id', 'name'));
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Bericht-Gruppen konnten nicht abgefragt werden!',CMSException::T_MODULEERROR ,$ex);
		}
		
		//Dann alle Berichte zu den einzelnen Gruppen holen
		$c=count($groups);
		for($i=0;$i<$c;$i++)
		{
			$groups[$i]['berichte'] = array();
			try 
			{
				$groups[$i]['berichte'] = SqlManager::GetInstance() -> Select('modbericht', array('id', 'title', 'active'), 'gId=\''. $groups[$i]['id'] .'\'');
			}
			catch (SqlManagerException $ex)
			{
				throw new CMSException('Abfrage der Berichte einer Gruppe fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
			}
			$jc=count($groups[$i]['berichte']);
			for($j=0;$j<$jc;$j++)
			{
				$groups[$i]['berichte'][$j]['links'] = array();
				$groups[$i]['berichte'][$j]['links']['item'] = Functions::GetLink(array('action' => 'item', 'id' => $groups[$i]['berichte'][$j]['id']), true);
				$groups[$i]['berichte'][$j]['links']['delete'] = Functions::GetLink(array('action' => 'delete', 'id' => $groups[$i]['berichte'][$j]['id']), true);
			}
		}
		
		MySmarty::GetInstance() -> OutputModuleVar('berichte', $groups);
		MySmarty::GetInstance() -> OutputModuleVar('groups', HTML::Select('modberichtgruppe', 'name'));
		
	}
	
	/**
	 * Fügt einen Bericht hinzu
	 *
	 */
	protected  function Add()
	{
		$name = Validator::ForgeInput($_POST['addname']);
		$group = Validator::ForgeInput($_POST['addgroup']);
		//Prüfen ob der Name mindestens 3 Zeichen lang ist
		if(strlen($name) < 3)
		{
			throw new CMSException('Der angegebene Name ist zu kurz!', CMSException::T_MODULEERROR );
		}
		
		try 
		{
			$_GET['id'] = SqlManager::GetInstance() -> Insert('modbericht', array('title' => $name, 'gId' => $group, 'created' => array('date' => 'NOW')));
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Einf&uuml;gen in die Datenbank fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
	}
	
	/**
	 * Wechselt den Status eines Berichts von Aktiv auf inaktiv oder umgekehrt
	 *
	 */
	private function SwitchActive()
	{
		$id = Validator::ForgeInput($_GET['id']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		Functions::SwitchActiv('modbercht', 'id');
		
	}
	
	/**
	 * Entfernt einen Anhang
	 *
	 */
	private function DeleteAttachement()
	{
		$id = Validator::ForgeInput($_GET['aid']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$trashitem = new TrashItem('Anhang eines Berichts');
		$berichtPath = $this -> GetAttachementPath($id);
		$trashitem -> AddFile(new TrashFile($berichtPath));
		$trashitem -> AddRecord(new TrashRecord('modberichtanhang', $id));
	}
	
	/**
	 * Sendet einen Anhang an den Benutzer
	 *
	 */
	private function GetAttachement()
	{
		$id = Validator::ForgeInput($_GET['aid']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		//$path = $this -> GetAttachementPath($id);
		//Filebase::DownloadFileById($id);
		Filebase::DownloadFileById($id);
	}
	
	
	/**
	 * Gibt den Pfad zu einem Anhang zurück dessen ID man kennt
	 *
	 * @param int $id
	 * @return unknown
	 */
	private function GetAttachementPath($id)
	{
		$berichtPath = null;
		try 
		{
			$fileId = SqlManager::GetInstance() -> SelectItem('modberichtanhang', 'fileId', 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage des Anhang-Pfades fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		$fileBasePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
		return $fileBasePath . Filebase::GetFilePath($fileId);
	}

	
	/**
	 * Verschiebt einen Bericht in den Papierkorb
	 *
	 */
	protected  function Delete()
	{
		$id = Validator::ForgeInput($_GET['id']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$trash = new TrashItem('Gel&ouml;schter Bericht!');
		
		//Alle Anhänge abfragen
		$berichte = null;
		try 
		{
			$berichte = SqlManager::GetInstance() -> Select('modberichtanhang', array('id', 'fileId'), 'berichtId=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Berichte fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		$c=count($berichte);
		for($i=0;$i<$c;$i++)
		{
			$filePath = Filebase::GetFilePath($berichte[$i]['fileId']);
			$trash -> AddFile(new TrashFile($filePath));
			$trash -> AddRecord(new TrashRecord('modberichtanhang', $berichte[$i]['id']));
		}
		
		//Alle verlinkungen auf Gallerien entfernen
		$gallerien = null;
		try 
		{
			$gallerien = SqlManager::GetInstance() -> Select('modberichtgallery', array('id'), 'berichtId=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Gallerien fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		$c=count($gallerien);
		for($i=0;$i<$c;$i++)
		{
			$trash -> AddRecord(new TrashRecord('modberichtgallery', $gallerien[$i]['id']));
		}
		
		$trash -> AddRecord(new TrashRecord('modbericht', $id));
		
		Trash::Delete($trash);
	}
	
	public static function GetMenuePointRestriction($module, $menueId)
	{
		return '';
	}
}