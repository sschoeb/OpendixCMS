<?php

/**
 * Beinhaltet die Klasse um den Filebrowser zu steuern
 *
 * @package 	OpendixCMS.Core
 */

/**
 * Bietet funktionen um mit dem Filebrowser zu arbeiten
 *
 * @package 	OpendixCMS.Core
 * @author 		Stefan Schöb
 * @version 	1.0
 *
 */
class Filebrowser
{

	/**
	 * Konstruktor
	 * 
	 * @todo deleteFolder
	 *
	 */
	public function __construct()
	{
		$dirs = unserialize($_SESSION['filebase_dirs']);
		$files = unserialize($_SESSION['filebase_files']);

		//Filebase::checkFileBase();

		//$_SESSION['filebase_temp']='';

		if(!isset($_GET['nextaction']) || isset($_GET['action']) && $_SESSION['nextaction'] == $_GET['nextaction']  )
		{
			switch ($_GET['action'])
			{
				case 'root':
					$_SESSION['filebase_temp'] = '';

					break;
				case 'delete':
					$this -> deleteFile();
					break;
				case 'joinFolder':
					$arr = unserialize($_SESSION['filebase_navPath']);
					$_SESSION['filebase_temp'] = $arr[$_GET['path']];
					break;
				case 'upload':
					$this -> uploadFile();
					break;
				case 'join':
					if($_GET['path'] > -1)
					{
						$_SESSION['filebase_temp'] .= $dirs[$_GET['path']['name']] . '/';
					}
					break;
				case 'up':
					$temp = substr(strrev($_SESSION['filebase_temp']), 1);
					$_SESSION['filebase_temp'] = strrev(strchr($temp, '/'));
					break;
				case 'createFolder':
					$this -> createFolder();
				case 'deleteFolder':
					break;
					break;
			}
		}

		$this -> outputNavString();
		$_SESSION['nextaction'] = md5(time());
		MySmarty::GetInstance() -> OutputVar('createFolderLink', Functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'createFolder', 'nextaction' => md5(time()))));
		MySmarty::GetInstance() -> OutputVar('uploadLink', Functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'upload', 'nextaction' => md5(time()))));
		$this -> showDir();
	}

	/**
	 * Erstellt einen Ordner
	 *
	 */
	private function CreateFolder()
	{
		$folderName = $_POST['newFolder'];
		if(!preg_match('/[A-Za-z0-9]+/', $folderName))
		{
			throw new CMSException('Der Ordnername enth&auml;lt unterlaubte Zeichen!', CMSException::T_WARNING );
		}
		if(is_dir(FILEBASE . $_SESSION['filebase_temp'] . $folderName))
		{
			throw new CMSException('Ordner existiert bereits - Nicht angelegt!', CMSException::T_WARNING );
		}
		if(!@mkdir(FILEBASE . $_SESSION['filebase_temp'] . $folderName, 0777))
		{
			throw new CMSException('Verzeichnis konnte nicht erstellt werden!', CMSException::T_WARNING );
		}
		MySmarty::GetInstance() -> OutputConfirmation('Verzeichnis wurde erfolgreich erstellt!');

	}

	/**
	 * Gibt die Leiste mit den Navigator oben aus
	 *
	 */
	private function outputNavString()
	{
		//Link zum Root verzeichnis
		MySmarty::GetInstance() -> OutputVar('rootLink', functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'root')));

		//Links
		$arr = split('/', substr($_SESSION['filebase_temp'], 0, strlen($_SESSION['filebase_temp'])-1));

		if(count($arr) == 1 && $arr[0] == '')
		{
			return;
		}

		$nav = array();
		for($i=0; $i<count($arr); $i++)
		{
			$k = $i;
			$temp = array();
			while($k >= 0)
			{
				$temp[] = $arr[$k];
				$k--;
			}
			$nav[$i] = join('/', array_reverse($temp)) . '/';
		}

		$_SESSION['filebase_navPath'] = serialize($nav);

		//Hier die Ausgabe
		$out = array();
		$arr = split('/', substr($_SESSION['filebase_temp'], 0, strlen($_SESSION['filebase_temp'])-1));
		for($i=0; $i<count($arr); $i++)
		{
			$out[]['name'] = $arr[$i] .' /';
			$arr[$i] = FILEBASE . $arr[$i];
			$out[count($out) -1]['link'] = functions::getlink(array('sub' => $_GET['sub'], 'action' => 'joinFolder', 'path' => "$i"));
		}

		MySmarty::GetInstance() -> OutputVar('filebase_navLink', $out);
	}

	/**
	 * Löscht eine Datei
	 *
	 */
	private function DeleteFile()
	{
		$files = unserialize($_SESSION['filebase_files']);

		if(!Filebase::removeFile($_SESSION['filebase_temp'] . $files[$_GET['path']]))
		{
			throw new CMSException('Datei konnte nicht gel&ouml;scht werden!', CMSException::T_WARNING );
			return;
		}
		MySmarty::GetInstance() -> OutputConfirmation('Datei wurde erfolgreich entfernt!');
	}

	/**
	 * Zeigt den Inhalt eines Ordners an
	 *
	 */
	private function ShowDir()
	{

		$fileBaseFolder = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
		$items= array();
		$items = scandir($fileBaseFolder . $_SESSION['filebase_temp']);
		$count = count($items);
		$dirs = array();
		$files = array();
		for($i=0; $i<$count; $i++)
		{
			if($items[$i] == '..' ||$items[$i] == '.')
			{
				continue;
			}
			if(is_dir($fileBaseFolder . $_SESSION['filebase_temp'] . $items[$i]))
			{
				$dirs[] = $items[$i];
				continue;
			}
			$files[] = $items[$i];
		}

		if($_SESSION['filebase_temp'] != '')
		{
			MySmarty::GetInstance() -> OutputVar('filesBackLink', functions::GetLink(array('sub' => $_GET['sub'],'action' => 'up', 'nextaction' => md5(time()))));
		}
		$_SESSION['filebase_dirs'] = serialize($dirs);
		$count = count($dirs);
		for($i=0; $i<$count; $i++)
		{
			$name = $dirs[$i];
			$dirs[$i] = array('name' => $name);
			$dirs[$i]['link'] = functions::GetLink(array('sub' => $_GET['sub'],'action' => 'join', 'path' => "$i", 'nextaction' => md5(time())));
		}

		MySmarty::GetInstance() -> OutputVar('directories', $dirs);

		$_SESSION['filebase_files'] = serialize($files);
		$count = count($files);
		for($i=0; $i<$count; $i++)
		{
			$name = $files[$i];
			$files[$i] = array('name' => $name);
			//$files[$i]['link'] = functions::GetLink(array('sub' => $_GET['sub'],'action' => 'join', 'path' => $i));
			$files[$i]['link']['del'] = functions::GetLink(array('sub' => $_GET['sub'],'action' => 'delete', 'path' => "$i", 'nextaction' => md5(time())));
		}

		MySmarty::GetInstance() -> OutputVar('files', $files);

	}

	/**
	 * Führt einen Upload im Filebrowser aus
	 * 
	 * @exception CMSException wenn die Datei nicht geuploaded werden kann
	 *
	 */
	private function UploadFile()
	{
		$fileBaseFolder = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
		if(!$path = Functions::Upload($fileBaseFolder . $_SESSION['filebase_temp'], 'fileUpload'))
		{
			throw new CMSException('Datei konnte nicht geuploaded werden!', CMSException::T_WARNING);
		}
		
		Filebase::addFile($path);
		MySmarty::GetInstance() -> OutputConfirmation('Datei erfolgreich hinzugef&uuml;gt!');
	}

	/**
	 * Fügt das Array der übergebenen Files in die gewünschte Tabelle ein
	 *
	 * @param 	String 	$array	Array mit allen Files die in der DB gesucht und verlinkt werden
	 * @param 	String 	$table	Tabelle in die geschrieben werden soll (z.B: modAgenda)
	 * @param  	int		$key	Fremdschlüssel der Tabelle zu der verknüpft werden soll (z.B. zu einem Termin)
	 */
	/*public static function AddFiles($array, $table, $foreignkey)
	{
		$count = count($array);
		foreach($array as $key => $value)
		{
			
			$value = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder') . $value;
			$value = str_replace('//', '/', $value);


			$id = 0;
			try {
				$id = Filebase::getFileId($value);
			}catch (FileNotFoundInDbException $ex)
			{
				MySmarty::GetInstance() -> OutputWarning('Datei ' . $value . ' wurde in der Filebase nicht gefunden!');;
				continue;
			}

			if($id == 0)
			{
				MySmarty::GetInstance() -> OutputWarning('Datei ' . $value . ' wurde in der Filebase nicht gefunden!');;
				continue;
			}

			try 
			{
				SqlManager::GetInstance() -> Insert('sysfilebasejoin', array('filebaseId' => $id, 'foreignId' => $foreignkey, 'foreignTable' => $table));
			}
			catch (SqlManagerException $ex)
			{
				MySmarty::GetInstance() -> OutputWarning($value . ' nicht hinzugef&uuml;gt!');
			}
			
			MySmarty::GetInstance() -> OutputConfirmation($value . ' erfolgreich hinzugef&uuml;gt!');
		}
	}*/

	/**
	 * Gibt die Files für einen Eintrag zurück
	 *
	 * @param unknown_type $table
	 * @param unknown_type $foreignKey
	 * @exception CMSException wenn bei der SQL-Abfrage ein Fehler auftritt
	 * @return unknown
	 */
	/*public static function GetFiles($table, $foreignKey)
	{
		$files = array();
		$insert = NULL;
		try 
		{
			$insert = SqlManager::GetInstance() -> Query('SELECT sysfilebase.id, sysfilebase.file, sysfilebase.folder FROM sysfilebase, sysfilebasejoin WHERE foreignTable=\''. $table .'\' AND foreignId=\''. $foreignKey .'\' AND sysfilebase.id = sysfilebasejoin.filebaseId');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Fehler beim Abfragen der Dateien aus der Filebasetabelle', CMSException::T_WARNING , $ex);
		}
		while($daten = mysql_fetch_assoc($insert))
		{
			$files[] = array();
			$c = count($files) -1;
			$files[$c]['id'] = $daten['id'];
			$files[$c]['file'] = $daten['file'];
			$files[$c]['folder'] = $daten['folder'];
			$files[$c]['path'] = $daten['folder'] . '/' . $daten['file'];
		}
		return $files;
	}*/

	
	/**
	 * Sendet eine Datei aus der Filebase
	 *
	 * @param 	int 	$id		ID des Files das gesendet werden soll
	 * @exception CmsException 	wenn die zu sendene Datei nicht existiert. Die Datei nicht gesendet werden konnte oder
	 * 							ider Klasse HTTP_Download (PEAR-Klasse) nicht verfügbar ist
	 */
	/*public static function SendFile($id)
	{
		$file = Filebase::getFilePath($id);
		$fileBaseFolder = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
		
		if(!file_exists($fileBaseFolder . $file) || !class_exists('HTTP_Download'))
		{
			throw new CMSException('Datei nicht vorhanden!', CMSException::T_WARNING );
		}
		//Datei senden
		$e = HTTP_Download::staticSend(array('file' => $fileBaseFolder . $file), false);
		if(is_object($e))
		{
			throw new CMSException('Datei konnte nicht gesendet werden: ' . $e -> getmessage(), CMSException::T_WARNING );
		}
	}*/
}
