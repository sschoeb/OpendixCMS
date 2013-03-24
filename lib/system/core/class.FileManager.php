<?php

/**
 * Beinhaltet die Klasse zum veränderungen im Dateisystem vorzunehmen
 * 
 * @package 	OpendixCMS.Core
 */

/**
 * Klasse mit der man das Filesystem manipulieren kann
 * 
 * Grundsätzlich sollte nicht direkt am Filesystem rummanipuliert werden. Dazu sollte
 * die Filebase-Klasse verwendet werden. Ansonsten kann es zu einem Durcheinander
 * mit der Datenbank kommen. 
 * Durch das verwenden der Filebase-Klasse wird auch gewährleistet, dass Dateien nicht
 * gelöscht werden können wenn noch irgendwo daraufhin verlinkt wird. Dies ist nicht möglich
 * sollte direkt über die FileManager-Klasse auf das Dateisystem zugegriffen werden.
 * 
 * Ausgenommen davon sind Funktionen die Informationen über das Dateisystem liefern!
 * 
 * @author 		Stefan Schöb
 * @version 	1.0
 * @package 	OpendixCMS.Core
 *
 */
class FileManager
{
	
	/**
	 * Uploadet ein File in das angegebene Verzeichnis
	 *
	 * @param	String 		$source			Quelle von dem das File gelesen wird
	 * @param 	String 		$destination	Pfad in dem das File abgespeichert wird
	 */
	public static function UploadFile($dst, $name = 'file', $type = 'all')
	{
		//Der Typ-Parameter definiert was für ein Typ die raufzuladende Datei haben muss
		switch ($type)
		{
			case 'images' :
				//Bilder
				break;
			case 'all' :
				break;
		}
		
		$size = $_FILES [$name] ['size'];
		if ($size > "20000000")
		{
			throw new CMSException ( 'Die Datei ist zu gross!', CMSException::T_WARNING );
		}
		
		//VIelleicht soll der upload gelogt werden?
		$loggswitch = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Logg/upload' );
		if ($loggswitch == 'on')
		{
			$loggfolder = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/loggUploadFolder' );
			$logbase = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/loggFolder' );
			Logfile::log ( $logbase, $loggfolder, 'File:' . $wohin . $_FILES [$name] ['name'] . '/Goesse: ' . $_FILES [$name] ['size'] . ' /User: ' . $_SESSION ['nick'] . '(' . $_SERVER ['REMOTE_ADDR'] . ')' );
		}
		//Sollte das File bereits existieren wird einfach noch eine Zahl vorne dran gesetzt.
		$i = 0;
		$tempname = $_FILES [$name] ['name'];
		
		while ( file_exists ( $dst . $tempname ) )
		{
			$tempname = $i . '_' . $_FILES [$name] ['name'];
			$i ++;
		}
		
		$_FILES [$name] ['name'] = $tempname;
		
		move_uploaded_file ( $_FILES [$name] ['tmp_name'], $dst . $_FILES [$name] ['name'] );
		
		if (! file_exists ( $dst . $_FILES [$name] ['name'] ))
		{
			throw new CMSException ( 'Dateiupload fehlgeschlagen!', CMSException::T_WARNING );
		}
		return $dst . $_FILES [$name] ['name'];
	
	}
	
	/**
	 * Sendet die mit dem Paramete $path übergebene Datei an den Besucher
	 *
	 * @param String $path	Pfad zur Datei die versendet werden soll
	 */
	public static function DowloadFile($path)
	{
		
		//Prüfen ob der Download geloggt werden soll
		$loggswitch = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Logg/download' );
		if ($loggswitch == 'on')
		{
			$loggfolder = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/loggDownloadFolder' );
			$logbase = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/loggFolder' );
			Logfile::log ( $logbase . $loggfolder, 'File:' . $pfad . ' /IP: ' . $_SERVER ['REMOTE_ADDR'] );
		}
		
		//Prüfen ob die zu versendene Datei existiert
		if (! file_exists ( $path ))
		{
			throw new CMSException ( 'Die Datei die zum Download angeboten wurde existiert nicht auf dem Server!', CMSException::T_WARNING );
		}
		
		//Prüfen ob die PEAR-Klasse für den Download vorhanden ist
		if (! class_exists ( 'HTTP_Download' ))
		{
			throw new CMSException ( 'Klasse HTTP_Download von Pear ist nicht verf&uuml;gbar!', CMSException::T_WARNING );
		}
		
		//Datei an den Besucher senden
		$e = HTTP_Download::staticSend ( array ('file' => $path ), true );
		
		//Prüfen ob beim Senden Fehler aufgetreten sind
		if ($e != '')
		{
			throw new CMSException ( $e->getMessage (), CMSException::T_WARNING );
		}
	}
	
	/**
	 * Entfernt eine Datei aus der File-Datenbank
	 *
	 * @param 	String 		$path	Pfad zu der Datei die entfernt werden soll
	 * @exception CMSException
	 * @return 	Boolean		
	 */
	public static function RemoveFile($path)
	{
		if (! file_exists ( $path ))
		{
			
			throw new CMSException ( 'Die zu entfernende Datei existiert nicht!', CMSException::T_WARNING );
		}
		return unlink ( $path );
	}
	
	/**
	 * Scannt ein Verzeichnis auf Dateien (rekursiv)
	 *
	 * @param unknown_type $dir
	 * @return unknown
	 */
	public static function RecursiveScandir($dir)
	{
		static $files = array ();
		
		$temp = array ();
		$temp = scandir ( $dir );
		$count = count ( $temp );
		for($i = 0; $i < $count; $i ++)
		{
			if ($temp [$i] == '.' || $temp [$i] == '..')
			{
				
				continue;
			}
			if (is_dir ( $dir . $temp [$i] ))
			{
				FileManager::RecursiveScandir ( $dir . $temp [$i] . '/' );
				continue;
			}
			$files [] = $dir . $temp [$i];
		}
		
		return $files;
	}
	
	/**
	 * Entfernt mehrere Dateien
	 *
	 * @param array $files	Array mit den zu entfernenden Dateien
	 */
	public static function RemoveFiles($files)
	{
		$c = count ( $files );
		for($i = 0; $i < $c; $i ++)
		{
			FileManager::RemoveFile ( $files [$i] );
		}
	}
	
	/**
	 * Erstellt eine Datei mit dem angegebenen Inhalt
	 *
	 * @param 	String 	$path		Pfad mit namen der Datei
	 * @param 	String	$content	Inhalt der in die Datei geschrieben werden soll
	 * @param 	Boolean $overwrite 	Boolean ob eine bestehende Datei �berschrieben werden soll
	 * 
	 * @example 	Exception - Wenn die Datei bereits existiert oder nicht angelegt werden kann
	 */
	public static function CreateFile($path, $content, $overwrite = false)
	{
		
		if (file_exists ( $path ) && ! $overwrite)
		{
			throw new CMSException ( 'Datei existiert bereits', CMSException::T_WARNING );
		}
		
		if (file_exists ( $path ) && $overwrite)
		{
			FileManager::RemoveFile ( $path );
		}
		
		$ressource = fopen ( $path, 'w' );
		fwrite ( $ressource, $content );
		fclose ( $ressource );
		
		if (! file_exists ( $path ))
		{
			throw new CMSException ( 'Datei konnte nicht angelegt werden', CMSException::T_WARNING );
		}
	}
	
	/**
	 * Verschiebt eine Datei von einem zum anderen Ort
	 *
	 * @param	String 	$src		Quellen von da die Datei gelesen werden soll
	 * @param	String 	$dst		Ziel wo das File hingeschrieben werden soll
	 * @param 	Boolean	$overwrite	Boolean ob die Zieldatei überschrieben wird
	 */
	public static function MoveFile($src, $dst, $overwrite = true)
	{
		if (! copy ( $src, $dst ))
		{
			throw new CMSException ( 'Datei konnte nicht kopiert werden!', CMSException::T_WARNING );
		}
		if (! unlink ( $src ))
		{
			throw new CMSException ( 'Quelldatei konnte nicht gel&ouml;scht werden!' . CMSException::T_WARNING );
		}
	}
	
	/**
	 * Erstellt einen Ordner
	 *
	 * @param 	String 	$path	Pfad zu dem zu erstellenden Ordner
	 * @exception CMSException wenn der Ordner nicht entfernt werden kann
	 */
	public static function CreateFolder($path)
	{
		if (! mkdir ( $path ))
		{
			throw new CMSException ( 'Ordner konnte nicht erstellt werden', CMSException::T_WARNING );
		}
	}
	
	/**
	 * Entfernt einen Ordner
	 * 
	 * Sollten sich im Ordner noch Dateien oder Unterordner befinden werden diese ebenfalls gelöscht
	 *
	 * @param String $path	Pfad zum Ordner der gelöscht werden soll
	 */
	public static function RemoveFolder($path)
	{
		//Prüfen ob der Ordner leer ist
		$files = FileManager::GetFileList ( $path );
		
		//Falls nicht dann alles darin löschen
		if (count ( $files ) != 0)
		{
			foreach ( $files as $item )
			{
				if (is_dir ( $item ))
				{
					FileManager::RemoveFolder ( $item );
					continue;
				}
				FileManager::RemoveFile ( $item );
			}
		}
		if (! rmdir ( $path ))
		{
			throw new CMSException ( 'Ordner konnte nicht gel&ouml;scht werden!', CMSException::T_WARNING );
		}
	}
	
	/**
	 * Verschiebt einen Ordner
	 *
	 * @param String 	$src		Herkunft des Ordners
	 * @param String 	$dst		Neuer Ort an dem der Ordner gespeichert werden soll
	 * @param Boolean 	$overwrite	Boolean ob der Ordner am Zielort überschrieben werden soll
	 * falls bereits ein entsprechender Ordner existiert
	 * Ist dieser auf false und der Ordner existiert, wird eine
	 * CMSException ausgelöst
	 * 
	 * @exception 	CMSException wenn der Zielordner bereits existiert und $overwirte=false
	 */
	public static function MoveFolder($src, $dst, $overwrite = true)
	{
		//Prüfen ob der Zielordner bereits existiert (je nach overwrite-Einstellung darf er nicht überschrieben werden)
		if (! $overwrite && file_exists ( $dst ))
		{
			throw new CMSException ( 'Ordner konnte nicht verschoben werden da der Zielordner bereits existiert!', CMSException::T_WARNING );
		}
		
		//Kopieren des Ordners
		if (! copy ( $src, $dst ))
		{
			throw new CMSException ( 'Kopieren des Ordners fehlgeschlagen!', CMSException::T_WARNING );
		}
		
		//Löschen des Herkunftordners
		FileManager::RemoveFolder ( $src );
	}
	
	/**
	 * Bennent eine Datei um
	 *
	 * Bsp:
	 * $path = /filebase/files/name.txt
	 * $newName = /filebase/files/neuername.txt
	 * ==> filebase/files/name.txt wird zu filebase/files/neuername.txt
	 * 
	 * @param String 	$path		Pfad zu Datei			
	 * @param String 	$newName	Neuer Name der Datei  	
	 * 
	 */
	public static function RenameFile($path, $newName)
	{
		if (! rename ( $path, $newName ))
		{
			throw new CMSException ( 'Datei(' . $path . ') konnte nicht umbenannt werden!', CMSException::T_WARNING );
		}
	}
	
	/**
	 * Bennent einen Ordner um
	 *
	 * @param String 	$path		Pfad zum Ordner
	 * @param String 	$newName	Neuer Name des Ordners
	 */
	public static function RenameFolder($path, $newName)
	{
		if (! rename ( $path, $newName ))
		{
			throw new CMSException ( 'Ordner(' . $path . ') konnte nicht umbenannt werden!', CMSException::T_WARNING );
		}
	}
	
	/**
	 * Findet eine Datei
	 *
	 * Die suche beginnt in dem angegebenen Ordner und geht
	 * rekursiv durch sämtliche Unterordner
	 * 
	 * @param String 	$file		Name der Datei
	 * @param String 	$startDir	Ordner in dem gesucht wird
	 * @return String	Den Pfad zur Datei wenn diese gefunden wurde
	 * @exception CMSException wenn die Datei nicht gefunden wurde
	 */
	public static Function FindFile($file, $startDir)
	{
		
		$subDirectories = array ();
		$dirIt = new DirectoryIterator ( $startDir );
		
		foreach ( $dirIt as $dirItem )
		{
			if ($dirItem->isDot ())
			{
				continue;
			}
			if ($dirItem->isDir ())
			{
				$subDirectories [] = $dirItem->getPathname ();
				continue;
			}
			if ($dirItem->getFilename () == $file)
			{
				return $dirItem->getPathname ();
			}
		}
		
		foreach ( $subDirectories as $directory )
		{
			$result = Functions::findfile ( $file, $directory );
			
			if ($result)
			{
				return $result;
			}
		}
		
		throw new CMSException ( 'Die gesuchte Datei wurde nicht gefunden', CMSException::T_WARNING );
	}
	
	/**
	 * Gibt die Anzahl Dateien in dem Ordner zurück
	 *
	 * Die Datei Thumbs.db wird nicht mitgezählt!
	 * 
	 * @param 	String 	$ordner	Ordner in dem die Dateien gezählt werden sollen
	 * @return 	int	Anzahl der Dateien
	 * @exception CMSException wenn der Ordner nicht geöffnet werden kann um die Dateien zu zählen
	 */
	public static Function CountFiles($ordner)
	{
		return count ( FileManager::GetFileList ( $ordner ) );
	}
	
	/**
	 * Gibt eine Liste der Dateien in einem Ordner zurück
	 *
	 * Die Datei Thumbs.db wird ignoriert
	 * 
	 * @param 	String 	$ordner	Pfad aus dem die Liste ausge
	 * @return 	Array 
	 */
	public static function GetFileList($folder)
	{
		$handle = @opendir ( $folder );
		if (! $handle)
		{
			throw new CMSException ( 'Ordner konnte nicht ge&ouml;ffnet werden! Z&auml;hlen der Dateien nicht m&ouml;glich!', CMSException::T_WARNING );
		}
		$filearray = array ();
		while ( false !== ($file = @readdir ( $handle )) )
		{
			if ($file != "." && $file != ".." && $file != 'Thumbs.db')
			{
				$filearray [] = $file;
			}
		}
		
		@closedir ( $handle );
		return $filearray;
	}
	
	/**
	 * Formatiert einen String zu einem Wert welcher 
	 * problemlos als Dateinamen verwendet werden kann
	 * 
	 * @param String $name 	Dateiname
	 */
	public static function GetNameAsFileName($name)
	{
		$name = str_replace ( ':', '', $name );
		$name = str_replace ( '�', 'ae', $name );
		$name = str_replace ( '�', 'oe', $name );
		$name = str_replace ( '�', 'ue', $name );
		$name = str_replace ( '\\', '', $name );
		$name = str_replace ( '/', '', $name );
		$name = str_replace ( ' ', '', $name );
		$name = str_replace ( '&', 'und', $name );
		$name = str_replace ( '"', '', $name );
		$name = str_replace ( "'", '', $name );
		
		return $name;
	}
}