<?php
/**
 * Galerie
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.0
 */

/**
 * Klasse mit der die Bilder der Galerien verwaltet werden können
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		1.0
 */
class BilderAdmin
{
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{
		switch ($_GET['action']) {
			case 'images':
				$this -> ShowImages();
				break;
			case 'singleimage':
				$this -> ShowSingleImage();
				break;
			case 'savesingleimage':
				$this -> SaveSingleImage();
				$this -> ShowImages();
				break;
			case 'deleteimage':
				$this -> DeleteImage();
				$this -> ShowImages();
				break;
			case 'addimage':
				break;
			default:
				$this -> ShowGalleries();
				break;
		}
	}
	
	/**
	 * Löscht ein einzelnes Bild
	 *
	 */
	private function DeleteImage()
	{
		//Überprüfen ob eine ID übergeben wurde
		$id = functions::GetVar($_GET, 'id');
		if(!is_numeric($id))
		{
			functions::Output_fehler('Keine g&uuml;ltige ID &uuml;bergeben!');
			return;
		}
		
		//Daten des zu löschenden Bildes abfragen
		$daten = array();
		try 
		{
			$daten = SqlManager::GetInstance() -> SelectRow('modgaleriebilder', $id);
		}
		catch (SqlManagerException $ex)
		{
			functions::Output_fehler($ex);
			return;
		}
		//Erst die Dateien entfernen
		if(!Filebase::RemoveFileById($daten['pfad_k']))
		{
		
			functions::Output_warnung('Das Preview Bild konnte nicht entfernt werden.');
		}
		if(!Filebase::RemoveFileById($daten['pfad_g']))
		{
			functions::Output_warnung('Das Bild in normalgrösse konnte nicht entfernt werden.');
		}
		if($daten['path_sg'] != null)
		{
			if(!Filebase::RemoveFileById($daten['path_sg']))
			{
				functions::Output_warnung('Das Originalbild konnte nicht entfernt werden.');
			}
		}
		
		//Den Datensatz entfernen
		try 
		{
			SqlManager::GetInstance() -> DeleteById('modgaleriebilder', $id);
		}
		catch (SqlManagerException $ex)
		{
			functions::Output_fehler($ex);
			return;
		}
		
		functions::Output_bericht('Das Bild wurde aus der Datenbank gel&ouml;scht!');
	}
	
	/**
	 * Zeigt die verfügbaren Galerien an
	 *
	 */
	private function ShowGalleries()
	{
		//Initialisieren der Datenablagerung
		$daten = array();
		//Abfragen aller Galerien
		try 
		{
			$daten = SqlManager::GetInstance() -> Select('modgalerie', array('name', 'id'));
		}
		catch (SqlManagerException $ex)
		{
			Functions::Output_fehler($ex);
			return;
		}
		
		//Für jede Galerie einen Link generieren
		foreach ($daten as $key =>  $value)
		{
			$daten[$key]['link'] = functions::GetLink(array('action'  => 'images', 'gid' => $value['id']), true);
		}
		
		//Ausgabe der Links
		Functions::Output_var('galerie', $daten);
	}
	
	/**
	 * Zeigt die Bilder an die in einer Galerie verfügbar sind
	 *
	 */
	private function ShowImages()
	{
		//Abfragen und überprüfen der Galerie-ID zu der die Bilder angezeigt werden sollen
		$galerieId = functions::GetVar($_GET, 'gid');
		if(!is_numeric($galerieId))
		{
			functions::Output_fehler('Es wurde keine g&uuml;ltige ID &uuml;bergeben!');
			return;
		}
		
		//Initialisieren der Datenablagerung
		$daten = array();
		
		//Abfragen aller Bilder die der Galerie angehören
		try 
		{
			$daten = SqlManager::GetInstance() -> SelectWithScrollMenue('modgaleriebilder', array('pfad_k', 'id'), 'gallerieId=' . $galerieId);
		}
		catch (SqlManagerException $ex)
		{
			Functions::Output_fehler($ex);
			return;
		}
		
		
		//Verarbeiten der ausgelesenen Daten
		// - Jedem Bild einen Link erstellen über den der User zu den Eigenschaften des Bildes gelangen kann
		// - Für jedes Bild den Pfad zum Preview rausholen
		foreach ($daten['daten'] as $key => $value)
		{
			$daten['daten'][$key]['link']['show'] = functions::GetLink(array('action' => 'singleimage', 'id' => $value['id']), true);
			$daten['daten'][$key]['link']['delete'] = functions::GetLink(array('action' => 'deleteimage', 'id' => $value['id'], 'gid' => $galerieId), true);
			try 
			{
				$daten['daten'][$key]['pfad_preview'] = FILEBASE . Filebase::GetFilePath($daten['daten'][$key]['pfad_k']);
			}
			catch (Exception $ex)
			{
				functions::Output_warnung('Eine Datei konnte nicht in der Datenbank gefunden werden(' . $daten['daten'][$key]['pfad_k'] . ')');
			}
			
		}
		
		$daten['uebersicht'] = functions::GetLink(array('sub' => $_GET['sub']));
		
		//Ausgabe der Daten
		Functions::Output_var('galerie', $daten);
		
	}
	
	/**
	 * Zeigt die Informationen von einem einzelnen Bild an
	 *
	 */
	private function ShowSingleImage()
	{
		//Abfrage und überprüfung der Bild-ID 
		$imageId = functions::GetVar($_GET, 'id');
		if(!is_numeric($imageId))
		{
			functions::Output_fehler('Es wurde keine g&uuml;ltige ID &uuml;bergeben!');
			return;
		}
		
		//Javascripts für den Filebrowser importieren
		JsImport::ImportSystemJS('filebrowser.js');
		JsImport::ImportSystemJS('ajax_queue.js');
		JsImport::ImportSystemJS('ajax_file_browser_client.js');
		JsImport::ImportModuleJS('galerie', 'galerieFileBrowser.js');
		JsImport::ImportModuleJS('galerie', 'galerie.js');
		Functions::RunJs('radioBoxChange();');
		
		//Initialisieren der Datenablagerung
		$daten = array();
		
		//Abfragen der Informationen zu dem Bild
		try 
		{
			$daten = SqlManager::GetInstance() -> SelectRow('modgaleriebilder', $imageId);
		}
		catch (SqlManagerException $ex)
		{
			Functions::Output_fehler($ex);
			return;
		}
		
		//Verarbeiten der Daten
		try 
		{
			$daten['preview'] = FILEBASE . Filebase::GetFilePath($daten['pfad_k']);
		}
		catch (Exception $ex)
		{
			Functions::Output_warnung('Der Pfad zum Preview-Bild konnte nicht ermittelt werden!');
		}
		try 
		{
			$daten['pfad_g'] = Filebase::GetFilePath($daten['pfad_g']);
		}
		catch (Exception $ex)
		{
			Functions::Output_warnung('Der Pfad zum Bild in Normalgr&ouml;sse konnte nicht ermittelt werden!');
		}

		if($daten['path_sg'] !== null)
		{
			try 
			{
				$daten['original']['path'] = Filebase::GetFilePath($daten['path_sg']);
			}
			catch (Exception $ex)
			{
				$daten['original']['path'] = 'unbekannt';
				Functions::Output_warnung('Der Pfad zum Bild in sehr grossen Bild konnte nicht ermittelt werden!');
			}
			$daten['original']['filesystem'] = ' checked="checked"';
		}
		else 
		{
			$daten['original']['delete'] = ' checked="checked"';
		}
	
		//Link zum speichern der Bild-Informationen
		$gid = 0;
		try 
		{
			$gid = SqlManager::GetInstance() -> SelectItem('modgaleriebilder', 'gallerieId', 'id=\''. $imageId .'\'');
		}
		catch (SqlManagerException $ex)
		{
			functions::Output_fehler($ex);
		}

		
		$daten['galerie'] = functions::Selector('modgalerie', 'name', $daten['gallerieId']);
		$daten['savelink'] = functions::GetLink(array('action' => 'savesingleimage', 'id' => $imageId, 'gid' => $gid), true);
		
		functions::Output_var('galerie', $daten);
	}
	
	/**
	 * Speichert die Informationen von einem einzelnen Bild
	 *
	 */
	private function SaveSingleImage()
	{
		//Abfragen und überprüfen der ID
		$id = functions::GetVar($_GET, 'id');
		if(!is_numeric($id))
		{
			functions::Output_fehler('Es wurde keine g&uuml;ltige ID &uuml;bergeben!');
			return;
		}
		
		//Initialisieren des Datenspeichers
		$daten = array();
		
		//ISO-Wert auslesen und kontrollieren
		$daten['iso'] = functions::GetVar($_POST, 'iso');
		if(!is_numeric($daten['iso']))
		{
			functions::Output_warnung('Es wurde kein g&uuml;ltiger ISO-Wert angegeben!');
			$daten['iso'] = 0;
		}
		
		//Auslesen des Kommentars
		$daten['comment'] = Functions::GetVar($_POST, 'comment');
		//Auslesen der verwendeten Kamera
		$daten['kamera'] = Functions::GetVar($_POST, 'kamera');
		
		
		switch ($_POST['originalChoice'])
		{
			case 'delete':
				//Es soll kein Bild vorhanden sein, das bestehende Bild wird gelöscht
				$daten['pfad_sg'] = $this  -> GetPathSg($id);
				Filebase::RemoveFileById($daten['pfad_sg']);
				$daten['pfad_sg'] = null;
				break;
			case 'filesystem':
				//Es soll ein Bild aus der Filebase angezeigt werden
				$filebasePath = FILEBASE . Functions::GetVar($_POST, 'originalPath');
				$daten['pfad_sg'] = '';
				try 
				{
					$daten['pfad_sg'] = Filebase::GetFileId($filebasePath);
				}
				catch (Exception $ex)
				{
					functions::Output_fehler('Die angegebene Datei konnte nicht in der Filebase gefunden werden!');
					return;
				}
				
				break;
			case 'upload':
				//Es soll ein Bild über den Browser geuploaded werden
				$up = Functions::Upload(FILEBASE . 'galerie/originalimages/', 'newOriginalImage');
				if(!$up)
				{
					functions::Output_fehler('Die Datei konnte nicht auf dem Server gespeichert werden!');
				}
				$daten['pfad_sg'] = Filebase::AddFile($up);
				break;
		}
		
		//Auslesen der Datumsinformationen
		$date = array();
		$date['year'] = functions::GetVar($_POST, 'datumYear');
		$date['month'] = functions::GetVar($_POST, 'datumMonth');
		$date['day'] = functions::GetVar($_POST, 'datumDay');
		$date['hour'] = functions::GetVar($_POST, 'datumHour');
		$date['minute'] = functions::GetVar($_POST, 'datumMinute');
		$date['second'] = functions::GetVar($_POST, 'datumSecond');

		//Prüfen ob das Datum korrekt ist
		if(!checkdate($date['month'], $date['day'], $date['year']))
		{
			Functions::Output_fehler('Es wurde ein ung&uuml;ltiges Datum eingegeben!');
			return;
		}
		
		//Zusammensetzend es Datumstrings
		$dateString = $date['year'] . '-' . $date['month'] . '-' . $date['day'] . ' ' . $date['hour'] . ':' . $date['minute'] . ':' . $date['second'];
		
	
		//Galerie-ID auslesen und prüfen
		$gid = functions::GetVar($_POST, 'galerie');
		if(!is_numeric($gid))
		{
			functions::Output_fehler('Es wurde keine g&uuml;ltige Galerie-ID ¨&uuml;bergeben!');
			return;
		}
		
		//Speichern der Informationen
		try 
		{
			SqlManager::GetInstance() -> Update('modgaleriebilder', array('gallerieId' => $gid , 'datum' => $dateString, 'iso' => $daten['iso'], 'comment' => $daten['comment'], 'kamera' => $daten['kamera'], 'path_sg' => $daten['pfad_sg']), 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			Functions::Output_fehler('Die Eigenschaften des Bildes konnten nicht gespeichert werden: ' . $ex);
			return;
		}
		functions::Output_bericht('Eigenschaften wurden erfolgreich gespeichert!');
	}
	
	/**
	 * Gibt den Pfad zur der als Parameter übergebenen Datei zurück
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	private function GetPathSg($id)
	{
		try 
		{
			return SqlManager::GetInstance() -> SelectItem('modgaleriebilder', 'path_sg', 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			Functions::Output_fehler($ex);
			return;
		}
	}
	
	private function AddImages()
	{
		
	}
}