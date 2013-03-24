<?php

/**
 * File mit der FileBase-KLasse
 *
 * @package  	OpendixCMS.Core
 */

/**
 * Klasse mit der man Operationen auf der Filebase ausführen kann
 *
 * @author 		Stefan Schoeb <opendix@gmail.com>
 * @version 	1.0
 * @package 	OpendixCMS.Core
 */
class Filebase
{

	/**
	 * Upload einer Datei in die Filebase
	 * 
	 * @param unknown_type $dst
	 * @param unknown_type $frmName
	 * @param unknown_type $type
	 * @return int ID der neu hinzugefügten Datei
	 */
	public static function UploadFile($dst, $frmName = 'file', $type='all')
	{
		$filebasePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
		$newPath = FileManager::UploadFile($filebasePath . $dst, $frmName, $type);
		return Filebase::AddFile($newPath);
	}
	
	/**
	 * Gibt die Grösse einer Datei in Bytes zurück
	 *
	 * @param String $path	Pfad zur Datei
	 * @return int
	 */
	public static function GetFileSize($path)
	{
		$filebasePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
		if(!file_exists($filebasePath . $path))
		{
			throw new CMSException('Datei existiert nicht!', CMSException::T_WARNING );
		}
		return filesize($filebasePath . $path);
	}
	
	/**
	 * Gibt die Grösser eine Datei in Bytes zurück
	 *
	 * @param int $id	ID der Datei
	 * @return int
	 */
	public static function GetFileSizeById($id)
	{
		return Filebase::GetFileSize(Filebase::GetFilePath($id));
	}

	
	/**
	 * Sendet eine Datei aus der Filebase an den Besucher
	 *
	 * @param int $id	ID der Datei in der Filebase welche an den User gesendet werden soll
	 */
	public static function DownloadFileById($id)
	{
		
		//Pfad ermitteln und dann Download starten
		$path = Filebase::GetFilePath($id);
		
		$filebasePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
		
		
		FileManager::DowloadFile($filebasePath . $path);
			
	}

	/**
	 * Fügt das im Pfad angegebene File zur Datenbank hinzu
	 *
	 * @param 	String 	$path	Pfad der Datei die hinzugefügt werden soll
	 * @return 	int	ID des neuen Eintrages in der Filebase
	 */
	public static function AddFile($path)
	{
		$info = Filebase::getPathInfo($path);
		$id = null;
		try 
		{
			$id = SqlManager::GetInstance() ->Insert('sysfilebase', array('folder' => $info['folder'], 'file' => $info['file']));
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Datei konnte nicht hinzugef&uuml;gt werden!',CMSException::T_SYSTEMERROR ,$ex);
		}
		return $id;
	}

	/**
	 * Entfernt eine Datei aus der File-Datenbank
	 *
	 * @param 	String 		$path	Pfad zu der Datei die entfernt werden soll
	 */
	public function RemoveFile($path)
	{
		$trashItem = new TrashItem('Datei aus der Filebase: ' . $path);
		$trashItem -> AddFile(new TrashFile($path));
		Trash::Delete($trashItem);
		return true;
	}

	/**
	 * Entfernt eine Datei nach der in der Datenbank abgelegten ID
	 *
	 * @param 	int 	$id		Die ID zu dem das File gehört welches gelöscht wird
	 */
	public static function RemoveFileById($id)
	{
		$info = array();
		try 
		{
			$info = SqlManager::GetInstance() -> SelectRow('sysfilebase', $id, array('file', 'folder'));
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Pfad zur Datei konnte nicht abgefragt werden!',CMSException::T_WARNING ,$ex);
		}

		$filebasePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
		
	
		
		Filebase::removeFile($filebasePath . trim($info['folder']) . '/' .  $info['file']);
	}

	/**
	 * Entfernt einen Ordner in der Filebase mitsamt allen darunter gespeicherten Files
	 *
	 * @param 	String 	$path	Der Pfad zu dem Ordner der gel�scht werden soll
	 */
	public function RemoveFolder($path)
	{
		
		$files = array();
		try 
		{
			$notAble = SqlManager::GetInstance() -> Count('sysfilebase', 'folder LIKE \'' . $path . '%\' AND usedcount != 0');
			if($notAble > 0)
			{
				throw new CMSException('Ordner kann nicht gel&ouml:scht werden da noch Dateien verwendet werden!', CMSException::T_WARNING );
			}
			$files = SqlManager::GetInstance() -> Select('sysfilebase', array('folder', 'file'), 'folder LIKE \'' . $path . '%\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Ordner konnten nicht abgefragt werden!',CMSException::T_WARNING ,$ex);
		}
		
		$trashItem = new TrashItem('Gelöschter Ordner: ' . $path);
		
		$c=count($files);	
		for($i=0;$i<$c;$i++)
		{
			$trashItem -> AddFile(new TrashFile($files['folder'] . $files['file']));
		}
		
		Trash::Delete($trashItem);
	}

	/**
	 * Extrahiert Dateiname und Pfadname aus einer Pfadangabe
	 *
	 * @param	array 	$path	Assoziatives Array mit zwei Einträge, file und folder
	 */
	private function GetPathInfo($path)
	{
		
		$info = array();
		$info['file'] = trim(basename($path));
		$info['folder'] = trim(dirname($path));
		
		$filebasePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
		//$filebasePath = 'cmsv2/filebase/';
		
		$info['folder'] = $info['folder'] . '/';
		
		//Beim Ordner muss noch der Pfad zur Filebase vorne entferntw werden
		if(substr($info['folder'] . '/', 0, strlen($filebasePath)) == $filebasePath)
		{
			$count = 1;
			$info['folder'] =  str_replace($filebasePath, '', $info['folder'], $count);
		}
		
		$info['file'] = trim($info['file'], '\\/');
		$info['folder'] = trim($info['folder'], '\\/');
		
		$info['folder'] = str_replace('//', '/', $info['folder']);
		
		return $info;
	}

	/**
	 * Gibt den Pfad eines Files der übergebenen ID zurück
	 *
	 * @param 	int 	$id		ID zu der das File gesucht wird
	 */
	public static function GetFilePath($id, $addFilebasePath=false)
	{
		if(!is_numeric($id))
		{
			throw new CMSException('Ung&uuml;ltige File-Id', CMSException::T_WARNING );
		}
		$daten = array();
		try 
		{
			$daten = SqlManager::GetInstance() -> SelectRow('sysfilebase', $id, array('file', 'folder'));
			
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage des Pfades fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
		if(!$daten)
		{
			throw new CMSException('Ung&uuml;ltuge file-ID. Nicht in Datenbank vorhanden!', CMSException::T_WARNING);
		}
		
		if($addFilebasePath)
		{
			$filebasePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
			$daten['folder'] = $filebasePath . $daten['folder'];
		}
		return trim($daten['folder']) . '/'. trim($daten['file']);
	}

	/**
	 * Gibt die ID einer Datei zurück
	 *
	 * @param 	String 	$path	Pfad zu dem File
	 */
	public static function GetFileId($path)
	{
		//Daten über den Pfad abrufen
		$info = Filebase::getPathInfo($path);

		//Prüfen ob diese Datei in der Datenbank vorhanden ist
		if(!Filebase::isInFileBase($path))
		{
			throw new CMSException('Datei nicht in Datenbank gefunden!', CMSException::T_WARNING );
		}

		//Id abfragen
		$id = '';
		try 
		{
			$id = SqlManager::GetInstance() -> SelectItem('sysfilebase', 'id', 'folder=\''. $info['folder'] .'\' AND file=\''. $info['file'] .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der ID fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
		
		return $id;
		
	}

	/**
	 * Prüft ob eine Datei in der Filedatenbank vorhanden ist
	 *
	 * @param	String 	$path	Datei die gesucht werden soll
	 */
	public static function IsInFileBase($path)
	{
		$info = Filebase::getPathInfo($path);
			
		$c=0;
		try 
		{
			$c = SqlManager::GetInstance() -> Count('sysfilebase', 'folder=\''. $info['folder'] .'\' AND file=\''. $info['file'] .'\'');
		}
		catch (SqlManagerException $ex)
		{

			throw new CMSException('Abfrage der Anzahl der Datei in der Datenbank fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
		
		if($c == 0)
		{
			return  false;
		}
		return true;
	}

	/**
	 * Überprüft die Datenbank der Filebase mit den tatsächlich vorhandenen Files
	 *
	 * Nicht vorhandene Files werden aus der Datenbank gelöscht wobei nicht in der
	 * Datenbank erfasste Files zur Datenbank hinzugefügt werden
	 *
	 */
	public static function CheckFileBase()
	{
		$filebasePath = 'filebase/';
		
		//Erst alle Files die vorhanden sind auslesen
		$files = array();
		$files = FileManager::RecursiveScandir($filebasePath);


		//Alle Files in der Datenbank abfragen
		$query = 'SELECT id, folder, file FROM sysfilebase';
		$insert = mysql_query($query) OR Functions::Output_fehler('Mysql-Error: aolnnnuuuuuuu');
		while($daten = mysql_fetch_assoc($insert))
		{
			//Prüfen ob die Datei existiert in der Filebase
			if(!file_exists($filebasePath . trim($daten['folder']) . '/' . trim($daten['file'])))
			{
				//Wenn nicht dann aus der Filebase löschen
				$delquery = 'DELETE FROM sysfilebase WHERE id = \'' . $daten['id'] . '\'';
				$delinsert = mysql_query($delquery) OR Functions::Output_warnung('Eintrag konnte nicht aus der Datenbank entfernt werden');
				echo "<br>Datensatz entfernt: " .  trim($daten['folder']) . '/' . trim($daten['file']);
			}

			//Hier nun immer das File das gerade dran ist aus der Tabelle der gefunden Files löschen
			//So kann nach dieser While jedes File das noch in dem $files-Array ist zur Filebase hinzugefügt werden
			if(in_array($filebasePath . $daten['folder'] . '/' . $daten['file'], $files))
			{

				$key = array_search($filebasePath . $daten['folder'] . '/' .  $daten['file'], $files);
				if($key === false)
				{
					continue;
				}

				unset($files[$key]);
			}
		}

		//Nun alle Files die im Ordner gefunden wurden aber nicht in der DB einfügen
		if(count($files) == 0)
		{
			return;
		}

		$query = 'INSERT INTO `sysfilebase` (`folder` , `file` ) VALUES '; //(NULL , 'folder1', 'file1'), (NULL , 'folder1', 'file1');';
		$i=0;
		foreach($files as $key => $value)
		{
			$info = Filebase::getPathInfo($value);
			if($i != 0)
			{
				$query .= ',';
			}
			echo "<br>Datensatz hinzugef&uuml;gt: " .  $info['folder'] . $info['file'];
			$query .= '(\''. $info['folder'] .'\',\''. $info['file'] .'\')';
			$i++;
		}

		mysql_query($query) OR Functions::Output_fehler('Mysql-Error: ikkklahslkdbasldhjfg adf a1' . mysql_error());
	}

	/**
	 * Gibt alle Dateien aus einem Ordner zurück
	 *
	 * @param 	String 	$path		Pfad der geprüft werden soll
	 * @param	String 	$format		Format, welches die Dateien aufweisen müssen
	 *
	 * @return 	Array mit allen Files des übergebenen Formats
	 */
	public static function GetFiles($path, $format='*.*')
	{
		$files = array();
		
		try 
		{
			$files = SqlManager::GetInstance() -> Select('sysfilebase', array('file', 'folder'), 'folder LIKE \''. $path . '%\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage aller Dateien fehlgeschlagen',CMSException::T_WARNING ,$ex);
		}
		
		$c=count($files);
		for($i=0;$i<$c;$i++)
		{
			if(!preg_match($format, $files[$i]['file']))
			{
				continue;
			}
			if($files[$i]['folder'] != $path)
			{
				$count = 1;
				$files[$i]['file'] = str_replace($path, '', $files[$i]['folder'], $count) . $files[$i]['file'];
			}		
		}

		return $files;
	}

	/**
	 * Zällt die Dateien in dem angegebenen Pfad
	 *
	 * @param 	String 		$path		Pfad in dem die Dateien gezählt werden sollen
	 * @param	Boolean 	$subDir		Boolean ob Unterordner miteinbezogen werden
	 */
	public static function CountFiles($path,$subDir = true)
	{
		$sub = '%';
		if(!$subDir)
		{
			$sub = '';
		}
		$c=0;
		try 
		{
			$c = SqlManager::GetInstance() -> Count('sysfilebase', 'folder LIKE \''. $path . $sub .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Anzahl fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
		return $c;
	}

	
	/**
	 * Vermerkt eine Datei als benutzt in der Datenbank durch eine Verknüpfung
	 * mit einem anderen Datensatz. Somit kann die Datei erst entfernt werden
	 * wenn vorher auch diese Verknüpfung entfernt wird
	 *
	 * @param String 	$path		Pfad zur Datei auf die eine Verknüpfung erstellt werden soll
	 */
	public static function UseFile($path)
	{
		$id = Filebase::GetFileId($path);
		Filebase::UseFileById($id);
	}
	
	/**
	 * Vermerkt mehrere Datenen als benutzt in der Datenbank
	 * -> siehe Filebase::UseFile
	 *
	 * @param String[] 	$files		Array mit allen Dateien die mit dem Datensatz verknüpft werden sollen
	 */
	public static function UseFiles($files)
	{
		$c=count($files);
		for($i=0;$i<$c;$i++)
		{
			Filebase::UseFile($files[$i]);
		}
	}
	
	/**
	 * Vermerkt eine Datei in der Filebase nach deren ID als benutzt
	 * -> siehe Filebase::useFile
	 *
	 * @param int 		$id			ID der Datei auf die eine Verknüpfung erstellt werden soll
	 */
	public static function UseFileById($id)
	{
		try 
		{
			SqlManager::GetInstance() -> Query('UPDATE sysfilebase SET usedcount = usedcount + 1 WHERE id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			MySmarty::GetInstance() -> OutputWarning($value . ' nicht hinzugef&uuml;gt!');
		}
	}
	
	/**
	 * Gibt den aktuellen UseCount einer Datei zurück	
	 *
	 * @param 	int 	$id		ID der Datei, deren UseCount abgefragt werden soll
	 * @return 	int 			aktuellen UseCount der Datei
	 */
	public static function GetUsedCount($id)
	{
		$count = 0;
		try 
		{
			$count = SqlManager::GetInstance() -> SelectItem('sysfilebase','usedcount', 'id=\''. $id .'\'');
			
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Anzahl der Verkn&uuml;pfungen fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
		return $count;
	}
	
	/**
	 * Zählt den Used-Count einer Datei um eins runter
	 *
	 * @param int $id	ID der Datei, welche unused werden soll
	 */
	public static  function UnUseFileById($id)
	{
		//Prüfen ob der UsedCount nicht bereits 0 ist
		$usedCount = Filebase::GetUsedCount($id);
		if($usedCount == 0)
		{
			return;
		}
		//.. wenn nicht dann hier eines vom UsedCount abziehen
		try 
		{
			SqlManager::GetInstance() -> Query('UPDATE sysfilebase SET usedcount = usedcount - 1 WHERE id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			MySmarty::GetInstance() -> OutputWarning($value . ' nicht hinzugef&uuml;gt!');
		}		
	}
	
	/**
	 * Gibt zurück, ob die Datei in der Filebase gesperrt ist
	 *
	 * @param int $id	ID der zu prüfenden Datei
	 */
	public static function IsLocked($id)
	{
		$state = '';
		try 
		{
			$state = SqlManager::GetInstance() -> SelectItem('sysfilebase', 'locked', 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage des Locked-Status fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
		if($state == 0)
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Sperrt eine Datei
	 *
	 * @param unknown_type $id
	 */
	public static function LockFile($id)
	{
		$user = Cms::GetInstance() -> GetUser() -> GetId();
		try 
		{
			SqlManager::GetInstance() -> Update('sysfilebase', array('locked' => 1, 'lockedBy' => $user), 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Sperren der Datei fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
	}
	
	/**
	 * Unlocked eine Datei
	 * 
	 * Dateien können nur von dem User entsperrt werden, der dieses gesperrt hat
	 *
	 * @param INT $id	ID der Datei die entsperrt werden soll
	 */
	public static function UnLockFile($id)
	{
		//Abfrage der User-ID des aktuellen Users
		$user = Cms::GetInstance() -> GetUser() -> GetId();
		
		$state = null;
		//Prüfen ob die Datei von diesem User gesperrt wurde
		try 
		{
			$state = SqlManager::GetInstance() -> SelectRow('sysfilebase', $id, array('locked', 'lockedBy'));
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage des Lock-State fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
		
		//Prüfen ob die Datei überhaupt gesperrt ist...
		if($state['locked'] == 0)
		{
			//... wenn nicht dann hier die Funktion verlassen, da dass gewünschte Ergebniss bereits existiert
			return;
		}
		
		//Prüfen ob der angemeldete User = dem User der die Datei gesperrt hat
		if($user == $state['lockedBy'])
		{
			//.. wenn nicht, dann Fehlermeldung generieren
			throw new CMSException('Die Datei konnte nicht entsperrt werden! Falscher Benutzer!', CMSException::T_WARNING );
		}
		
		try 
		{
			SqlManager::GetInstance() -> Update('sysfilebase', array('locked' => 0, 'lockedBy' => 'NULL'), 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Entsperren der Datei fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
	}
	
	public function GetFileName($fileId)
	{
		$name = null;
		try{
			$name = SqlManager::GetInstance() -> SelectItem('sysfilebase', 'file', 'id=\'' . $fileId . '\'');
		}catch(SqlManager $ex)
		{
			throw new CMSException('SQL-Fehler',CMSException::T_WARNING ,$ex);
		}
		
		return $name;
		
	}
}