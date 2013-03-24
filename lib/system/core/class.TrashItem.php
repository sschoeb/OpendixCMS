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
 * Repräsentiert ein Objekt das gelöscht werden soll
 * 
 * Wird in einem Modul ein Element gelöscht, kann dieses mehrere Datensätze und Dateien
 * betraffen. Diese Klasse speichert sämtliche betroffenen Elemente. Das erstelle 
 * TrashItem-Objekt kann dann mit der Trash-Klasse in den Papierkorb verschoeben werden.
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	2008, Stefan Schöb
 * @since 		2.0
 */
class TrashItem
{
	/**
	 * Namen des zu löschenden Objekts
	 *
	 * @var String
	 */
	private $name = '';
	
	/**
	 * Array mit allen zu löschenden Dateien
	 *
	 * @var StringArray
	 */
	private $files = array();
	
	/**
	 * Array mit allen zu löschenden Datensätzen
	 *
	 * @var TabRecordArray
	 */
	private $records = array();
	
	/**
	 * Konstruktor
	 *
	 * @param String $name	Name des zu löschenden Elements
	 */
	public function __construct($name)
	{
		$this -> name = $name;
	}
	
	/**
	 * Fügt eine zu löschende Datei hinzu
	 *
	 * @param TrashFile $file
	 */
	public function AddFile(TrashFile $file)
	{
		$rec = $file -> GetFilebaseRecord();
		if($rec != null)
		{
			$this -> records[] = $file -> GetFilebaseRecord();
		}
		$this -> files[] = $file;
	}
	
	/**
	 * Fügt die Ressourcen eines gelöschten Datensatzes hinzu
	 *
	 * @param TrashRecord $record
	 */
	public function AddRecord(TrashRecord $record)
	{
		$this -> records[] = $record;
	}
	
	/**
	 * Funktion zum wiederherstellen der Datensätze
	 *
	 */
	public function RestoreRecords()
	{
		//Wiederherstellen der Datensätze
		$c = count($this -> records);
		for($i=0;$i<$c;$i++)
		{
			$this -> records[$i] -> Restore();
		}
	}
	
	/**
	 * Gibt den Namen des zu löschenden Objekts zurück
	 *
	 * @return String
	 */
	public function GetName()
	{
		return $this -> name;
	}
	
	public function GetFiles()
	{
		return $this -> files;
	}
	
}