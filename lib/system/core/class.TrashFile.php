<?php

class TrashFile
{
	/**
	 * Ursprünglicher Pfad zur Datei
	 *
	 * @var String
	 */
	private $path = '';
	
	/**
	 * Name der Datei in dem ZIP-File in welchem die Dateien für den Papierkorb abgelegt werden
	 * Der Name wird in der Trash-Klasse festgelegt wenn der Lösch-Vorgang ausgeführt wird
	 *
	 * @var String
	 */
	private $nameInZip = '';
	
	/**
	 * Boolean ob die Datei in der Filebase liegt
	 *
	 * @var Boolean
	 */
	private $isFilebaseFile = false;
	
	/**
	 * Wird die Datei komplett gelöscht, dann wird auch der Datensatz in der 
	 * sysfilebase-Tabelle gelöscht. Damit dieser wiederhergestellt werden kann
	 * wird ein TrashRecord dafür erstellt der in dieser Variabel gespeichert wird
	 *
	 * @var TrashRecord
	 */
	private $filebaserecord = null;
	
	/**
	 * Boolean ob die Datei gelöscht wurde, oder ob nur der UseCount runtergezählt wurde
	 *
	 * @var Boolean
	 */
	private $filedeleted = false;
	
	/**
	 * Konstruktor
	 *
	 * @param String $path	Pfad zur löschenden Datei
	 */
	public function __construct($path)
	{
		if(!file_exists($path))
		{
			throw new CMSException('Datei existiert nicht!', CMSException::T_WARNING );
		}
		$this -> SetPath($path);
		
	}
	
	/**
	 * Initialisiert die Dateilöschung
	 *
	 * @param String $zipname
	 * @return Boolean	true wenn die Datei wirklich gelöscht wurde, false wenn nur der UseCount abgezählt wurde
	 */
	public function Init($zipname)
	{
		if($this -> isFilebaseFile)
		{
			//Abfrage der ID der Datei in der Filebase
			$id = Filebase::GetFileId($this -> path);
			//Den UseCount der Datei um eines vermindern
			Filebase::UnUseFileById($id);
			//Abfrage des neuen UseCounts
			$usedCount = Filebase::GetUsedCount($id);
			
			//Wenn der UsedCount gleich 0 ist und die Datei nicht gesperrt ist
			//wird die Datei nicht mehr gebraucht -> Die Datei wird im
			//Dateisystem gelöscht
			if($usedCount == 0 && !Filebase::IsLocked($id))
			{
				//.. ansonsten wird die Datei + der entsprechende Datensatz 
				//in der sysfilebase-Tabelle gelöscht
				$this -> filedeleted = true;
				$this -> filebaserecord = new TrashRecord('sysfilebase', $id);
			}
			else 
			{
				//... sollte die Condition nicht zutreffen, wird die Datei im 
				//System beibehalten
				return false;
			}
		}
		
		//Den namen in Zip-File zwischengespeichern, da dieser für das wiederherstellen wieder
		//benötigt wird -> Ich muss wissen, welche Datei im ZIP zu welchem Pfad gehört
		$this -> nameInZip = $zipname;
		$tempDir = Cms::GetInstance() -> GetConfiguration() -> Get('Trash/tempfolder');
		
		//Datei in den temporären Ordner verschieben, aus diesem raus wird dann das ZIP-File erstellt
		FileManager::RenameFile($this -> path, $tempDir . $this -> nameInZip);
		return true;
	}
	
	/**
	 * Wiederherstellen dieser Datei
	 *
	 */
	public function Restore()
	{
		if($this -> isFilebaseFile)
		{
			if(!$this -> filedeleted)
			{
				$id = Filebase::GetFileId($this -> path);
				try 
				{
					SqlManager::GetInstance() -> Query('UPDATE sysfilebase SET usedcount=usedcount+1 WHERE id=\''. $id .'\'');
				}
				catch (SqlManagerException $ex)
				{
					throw new CMSException('Update der Filebasedatenbank fehlgeschlagen!',CMSException::T_WARNING ,$ex);
				}
				return ;				
			}
		}
		
		$tempDir = Cms::GetInstance() -> GetConfiguration() -> Get('Trash/tempfolder');
		FileManager::RenameFile($tempDir . $this -> nameInZip, $this -> path);		
		
	}
	
	public function SetPath($value)
	{
		if(Filebase::IsInFileBase($value))
		{
			//Dateien in der Filebase müssen anders behandelt werden da diese evtl noch in der
			//Datenbank verknüpft sind
			$this -> isFilebaseFile = true;
		}
		$this -> path = $value;
	}
	
	public function GetFilebaseRecord()
	{
		return $this -> filebaserecord;
	}
	
}