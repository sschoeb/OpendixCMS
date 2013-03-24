<?php

/**
 * Trash / Papierkorb
 *
 * @package    	OpendixCMS.Core
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	1.0
 */

/**
 * Klasse bietet zum verwalten des Papierkorbs
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	2008, Stefan Schöb
 * @since 		2.0
 */
class Trash
{
	
	/**
	 * Leert den Papierkorb
	 *
	 */
	public static function Flush()
	{
		
	}	
	
	/**
	 * Löscht ein TrashItem
	 *
	 * @param TrashItem $item
	 */
	public function Delete(TrashItem $item)
	{
		$files 			= $item -> GetFiles();
		$trashFolder 	= Cms::GetInstance() -> GetConfiguration() -> Get('Trash/trashfolder');
		$tempDir 		= Cms::GetInstance() -> GetConfiguration() -> Get('Trash/tempfolder');
		$pack 			= array();
		
		$c = count($files);
		for($i=0;$i<$c;$i++)
		{
			if(file_exists($tempDir . 'file' . $i))
			{
				unlink($tempDir . 'file' . $i);
			}
			
			if(!$files[$i] -> Init('file' . $i))
			{
				continue;
			}
			$pack[] = $tempDir . 'file' . $i;
		}
		
		$did = NULL;
		$data = serialize($item);

		try 
		{
			$did = SqlManager::GetInstance() -> Insert('systrash', array('summary' => $item -> GetName(), 'trashitem' => $data));
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Fehler beim einf&uuml;gen in den Papierkorb!',CMSException::T_WARNING ,$ex);
		}
		if($c > 0)
		{
			if(!file_exists($trashFolder . $did . '/'))
			{
				FileManager::CreateFolder($trashFolder . $did . '/');
			}
			
			$zip = new Zip($trashFolder . $did . '/files.zip');
			$zip -> create($pack, array('remove_all_path' => true));
			
			FileManager::RemoveFiles($pack);
		}
		
	
	}

	
	/**
	 * Stellt einen Eintrag wiederher
	 *
	 * @param int $id	ID des Eintrags, der wiederhergestellt werden soll
	 */
	public static function Restore($id)
	{
		//Prüfen ob die ID numerisch ist
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		//Abfragen des serialisierten TrashItem-Objekts aus der Datenbank
		$data = '';
		try 
		{
			$data = SqlManager::GetInstance() -> SelectItem('systrash', 'trashitem', 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Fehler beim Abrufen der Daten aus der Datenbank!',CMSException::T_WARNING ,$ex);
		}
		//Objekt wiederherstellen und Datensätze wiederherstellen
		$obj = unserialize($data);
		$obj -> RestoreRecords();
		
		//Dateien Abfragen die mit diesem TrashItem gelöscht wurden
		$files = $obj -> GetFiles();
		$c = count($files);
		//Falls keine Dateien betroffen sind ist die Wiederherstelltung hier abgeschlossen
		if($c == 0)
		{
			return;
		}
		//Ansonsten die Dateien wiederherstellen -> Dazu die benötigten Pfade aus der Konfiguratione rmitteln
		$trashFolder = Cms::GetInstance() -> GetConfiguration() -> Get('Trash/trashfolder');
		$tempDir = Cms::GetInstance() -> GetConfiguration() -> Get('Trash/tempfolder');
		
		//Entzippen der Dateien in den Tempordner
		$zip = new Zip($trashFolder . $id . '/files.zip');
		$zip -> extract(array('add_path' => $tempDir));

		//Jedes Datei-Objekt anweisen, seine Datei selbst wieder an den entsprechenden Platz im 
		//Dateisystem zu verschieben
		for($i=0;$i<$c;$i++)
		{
			$files[$i] -> Restore();
		}
		
		//Ordner im Papierkorb entfernen
		FileManager::RemoveFolder($trashFolder . $id . '/');
		
		//Eintrag in der Datenbank entfernen
		try 
		{
			SqlManager::GetInstance() -> Delete('systrash', $id);
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Eintrag konnte aus der Papierkorb-Datenbank nicht entfernt werden',CMSException::T_WARNING ,$ex);
		}
		
	}
	
	
}