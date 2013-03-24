<?php
/**
 * Download-Klassen
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse zum verwalten von Downloads
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		1.0
 */
class Download extends ModulBase 
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
			return ;
		}

		switch ($_GET['action'])
		{
			case 'upload':
				$this -> Upload();
				$this -> Overview();
				break;
			case 'get':
				$this -> GetFile();
				$this -> Overview();
				break;
			case 'ajaxfbb':
				$this -> AJAX_fbbrowser();
				break;
			case 'ajaxgetgroup':
				$this -> AJAX_getgroup();
				break;
		}

		
	}

	/**
	 * Sendet eine Datei an den Benutzer
	 *
	 */
	private function GetFile()
	{
		$id = Validator::ForgeInput($_GET['id']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$fileId = null;
		try 
		{
			$fileId = SqlManager::GetInstance() -> SelectItem('moddownload', 'fileId', 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der ID fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		Filebase::DownloadFileById($fileId);
	}
	
	/**
	 * Speichert die Änderungen in der Ansicht
	 *
	 */
	protected function Save()
	{

		if(isset($_POST['anhang']))
		{
			$filebasePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
			$anhaenge = Validator::ForgeInput($_POST['anhang']);
			foreach ($anhaenge as $value) 
			{
				if(!is_numeric($value['group']))
				{
					continue;
				}
								
				Filebase::UseFile($filebasePath . $value['path']);
				$fid = Filebase::GetFileId($filebasePath . $value['path']);
				$this -> AddFile($fid, $value['group']);
			
			}
		}
	
		//Abfragen und filtern aller Daten
		$daten = Validator::ForgeInput($_POST['daten']);

		//Da alle Datensätze in einer Art Übersicht angezeigt werden, müssen hier alle Datensätze 
		//durchlaufen werden um alle evtl. vorgenommenen Änderungen festzuhalten
		foreach ($daten as $id => $values)
		{
			try 
			{
				SqlManager::GetInstance() -> Update('moddownload', array('description' => $values['description'], 
																		'fileAlias' => $values['filealias'], 
																		'`order`' => $values['order'],
																		'gId' => $values['group']), 'id=\''. $id .'\'');
			}
			catch (SqlManagerException $ex)
			{
				throw new CMSException('Update fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
			}
		}

		//Dem User eine Rückmeldung geben, dass der Speichervorgang erfolgreich war
		MySmarty::GetInstance() -> OutputConfirmation('Speichern erfolgreich!');
	}

	/**
	 * Löscht die Verlinkung auf eine Datei
	 *
	 * Dateien die zum Download angeboten werden sind nur verlinkungen in die Filebase
	 * Durch löschen werden nur diese Links aber nicht die Datei gelöscht
	 *
	 */
	protected function Delete()
	{
		$id = Validator::ForgeInput($_GET['id']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}

		$filePath = '';
		$fileId = 0;
		$fileExists = true;
		//Abfragen des Dateinamens
		try 
		{
			$fileId = SqlManager::GetInstance() -> SelectItem('moddownload', 'fileId', 'id=\''. $id .'\'');
			$filePath = Filebase::GetFilePath($fileId, true);
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage des Dateinamens fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		catch(CMSException $ex)
		{
			$fileExists = false;
		}
		
		
		$trash = new TrashItem('Zum Download angebotene Datei: ' . $filePath);

		if($fileExists)
		{
			$trash -> AddFile(new TrashFile($filePath));
		}
		$trash -> AddRecord(new TrashRecord('moddownload', $id));
		
		Trash::Delete($trash);

		MySmarty::GetInstance() -> OutputConfirmation('L&ouml;schen der Dateiverlinkung erfolgreich!');
	}

	/**
	 * Zeigt die momentan freigegebenen Dateien an
	 *
	 */
	protected function Overview()
	{
		
		JsImport::ImportModuleJS('Download', 'download.js');
		JsImport::ImportSystemJS('fbbrowser.js');
		
		$daten = array();
		try 
		{
			$daten = SqlManager::GetInstance() -> Select('moddownload', array('id', 'gId', 'description', 'fileId', 'fileAlias','`order`'));
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der verf&uuml;gbaren Downloads fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		
		$c=count($daten);
		for($i=0;$i<$c;$i++)
		{
			$daten[$i]['group'] = HTML::Select('moddownloadgroup', 'name', $daten[$i]['gId']);
			$linkarr = array();
			$linkarr['delete'] 	= Functions::GetLink(array('action' => 'delete', 'id' => $daten[$i]['id']), true);
			$linkarr['get'] 	= Functions::GetLink(array('action' => 'get', 'id' => $daten[$i]['id']), true);
			$daten[$i]['link'] 	= $linkarr;
			try 
			{
				$daten[$i]['size']	= Filebase::GetFileSizeById($daten[$i]['fileId']);
				$daten[$i]['fileId'] = basename(Filebase::GetFilePath($daten[$i]['fileId']));
			}
			catch (CMSException $ex)
			{
				$daten[$i]['fileError'] = 'Datei existiert nicht!';
			}
			
			
			
		}
		
		MySmarty::GetInstance() -> OutputModuleVar('groups', HTML::Select('moddownloadgroup', 'name'));
		MySmarty::GetInstance() -> OutputModuleVar('downloads', $daten);
	}

	/**
	 * Uploaded eine Datei, fügt sie zur Filebase hinzu und verlinkt sie mit den Downloads
	 *
	 */
	private function Upload()
	{
		//Datei Uploaden:
		$filebasePath = Cms::GetInstance() -> GetConfiguration() -> Get('Download/uploadpath');
		$fId = Filebase::UploadFile($filebasePath);
		Filebase::UseFileById($fId);
		
		//Gruppen-ID rausfinden:
		$gId = Validator::ForgeInput($_POST['uploadgroup']);
		if(!is_numeric($gId))
		{
			throw new CMSException('Die GID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		//Datei in die moddownload-Tabelle einfügen
		$this -> AddFile($fId, $gId);		
		
		MySmarty::GetInstance() -> OutputConfirmation('Datei erfolgreich hinzugef&uuml;gt');
	}
	
	/**
	 * Fügt eine Datei in die moddownload-Tabelle hinzu
	 *
	 * @param int $fid	ID der Datei in der Filebase Tabelle
	 * @param int $gid	ID der Gruppe in der moddownloadgroup-Tabelle
	 */
	private function AddFile($fid, $gid)
	{
		try 
		{
			SqlManager::GetInstance() -> Insert('moddownload', array('fileId' => $fid, 'gId' => $gid));
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Anhang konnte nicht eingetragen werden!',CMSException::T_MODULEERROR ,$ex);
		}
	}
	
	/**
	 * Anzeige des Filebasebrowsers
	 * 
	 * Nach afruf dieser Funktion wird das Script per die() beendet!
	 *
	 */
	private function AJAX_fbbrowser()
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
	 * AJAX-Funktion über die die vorhandenen Download-Gruppen abgefragtwerden können
	 *
	 */
	private function AJAX_getgroup()
	{
		//Prüfen ob der User Zugriff darauf hat
		$hasLaw = Cms::GetInstance() -> GetLaw() -> HasLaw(Law::T_VIEW , $_GET['sub']);
		if(!$hasLaw)
		{
			AJAX::ThrowError('Sie sind nicht berechtigt sich den Filebasebrowser anzuzeigen!');
			return;
		}
		
		//Abfragen aller Gruppen
		$daten = '';
		try 
		{
			$daten = SqlManager::GetInstance() -> Select('moddownloadgroup', array('id', 'name'));
		}
		catch (SqlManagerException $ex)
		{
			AJAX::ThrowError('Abfrage der Gruppen fehlgeschlagen!');
			return;
		}
		
		//Gruppen in XML verpacken
		//
		//<groups>
		//  <item name="derName" id="dieId" />
		//</groups>
		//
		$xmlDoc = new DOMDocument();
		$root = $xmlDoc->createElement('groups');
		$root = $xmlDoc->appendChild($root);
		
		$c = count($daten);
		for($i=0;$i<$c;$i++)
		{
			$item = $xmlDoc -> createElement('item');
			$item -> setAttribute('name', $daten[$i]['name']);
			$item -> setAttribute('id', $daten[$i]['id']);
			$root -> appendChild($item);
		}
		
		$data = $xmlDoc -> saveXML();
		
		//XML-Header ausgeben damit der Browser weis, dass es sich bei der ANtwort um gültiges XML handelt
		AJAX::OutputXmlHeader(strlen($data));

		//XML-Daten ausgeben
		echo $data;
		die();
		
	}
}
