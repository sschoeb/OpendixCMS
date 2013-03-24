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
 * Klasse repräsentiert einen Datensatz aus einer Tabelle der in den Papierkorb verschoben werden soll
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	2008, Stefan Schöb
 * @since 		2.0
 */
class TrashRecord
{
	/**
	 * Tabelle aus der einDatensatz entfernt werden soll
	 *
	 * @var unknown_type
	 */
	private $table = '';
	
	/**
	 * ID des zu löschenden Datensatzes
	 *
	 * @var int
	 */
	private $id= 0;
	
	/**
	 * Feldnamen der Tabelle
	 *
	 * @var StringArray
	 */
	private $fields = array();
	
	/**
	 * Alle Werte des Datensatzes
	 *
	 * @var StringArray
	 */
	private $values = array();
	
	/**
	 * Konstruktor	
	 *
	 * @param String 	$table	Tabelle aus der einDatensatz entfernt werden soll
	 * @param int 		$id		ID des zu löschenden Datensatzes
	 */
	public function __construct($table, $id)
	{
		$this -> table = $table;
		$this -> id = $id;
		$this -> Init();
	}
	
	/**
	 * Initialisiert den Datensatz
	 * 
	 * Es werden alle Felder mit den Daten ausgelesen und anschliessend
	 * wird der Datensatz gelöscht
	 *
	 */
	private function Init()
	{
		try 
		{
			//Abfragen aller Werte des Datensatzes
			$this -> values = SqlManager::GetInstance() -> SelectRow($this -> table, $this -> id, NULL, MYSQL_NUM);
			//Abfragen alle Feldnamen der Tabelle
			$this -> fields = SqlManager::GetInstance() -> GetFieldNames($this -> table);
			//Löschen des Datensatzes
			SqlManager::GetInstance() -> DeleteById($this -> table, $this -> id);
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Fehler beim Abfragen der Daten',CMSException::T_MODULEERROR ,$ex);
		}
	}
	
	/**
	 * Stellt diesen Datensatz wiederher
	 *
	 */
	public function Restore()
	{
		$fields = array();
		//Zusammenfügen von Feldnamen und Werten
		$c = count($this -> fields);
		for($i=0;$i<$c;$i++)
		{
			$fields[$this -> fields[$i]] = $this -> values[$i];
		}
		try 
		{
			//Datensatz wieder in die Datenbank einfügen
			SqlManager::GetInstance() -> Insert($this -> table, $fields);
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Fehler beim wiederherstellen eine Datensatzes!',CMSException::T_WARNING ,$ex);
		}
	}
}