<?php

/**
 * Datei für die Action-Klasse
 * 
 *
 * @package    	OpendixCMS.Core
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	1.0
 */

/**
 * Bietet den Zugriff auf eine Action
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		2.0     
 */
class Action
{
	/**
	 * Funktion die ausgeführt werden soll
	 *
	 * @var String
	 */
	private $fu = null;
	
	/**
	 * Klasse die ausgeführt werden soll
	 *
	 * @var String
	 */
	private $cl = null;
	
	/**
	 * ID der Action in der Datenbank
	 *
	 * @var int
	 */
	private $id = null;
	
	/**
	 * Boolean ob die Funktion statisch aufgerufen werden kann oder
	 * ob zu erst eine Instanz der Klasse($this -> cl) erstellt werden
	 * muss um die Funktion aufzurufen
	 *
	 * @var Boolean
	 */
	private $isStatic = false;
	
	/**
	 * Konstruktor
	 * 
	 * Wird eine ID mitgegeben, lädt die Klasse die entsprechende Action aus der Datenbank
	 *
	 * @param int $id
	 */
	public function __construct($id = null)
	{
		if(is_null($id))
		{
			return ;
		}
		
		$this -> id = $id;
		$this -> LoadAction();
	}
	
	/**
	 * Führt die Action aus
	 *
	 */
	public function Run()
	{
		if(!class_exists($this-> cl))
		{	
			throw new CMSException('Klasse nicht gefunden: (\'' .$this -> cl . '\')', CMSException::T_SYSTEMERROR );
		}
		//Ist die Funktion als "public" deklariert muss keine instanz der Klasse erstellt werden
		if($this -> isStatic)			
		{
			call_user_func(array($this -> cl, $this -> fu));
		}
		else
		{
			//Objekt erstellen -> Funktion aufrufen
			$object = new $this -> cl;
			$object-> $this -> fu;
		}
	}
	
	/**
	 * Setzt den Wert für die Funktion
	 *
	 * 
	 * @param String $value
	 */
	public function SetFunction($value)
	{
		$this -> fu = $value;
	}
	
	/**
	 * Setzt den Wert für die Klasse
	 *
	 * @param String $value
	 */
	public function SetClass($value)
	{
		$this -> cl = $value;
	}
	
	/**
	 * Setzt den Wert für den Switch zwischen einer statisch 
	 * aufrufbaren Funktion und einer von einem Objekt
	 * aufrufbaren Funktion
	 *
	 * @param Boolean $value
	 */
	public function SetStatic($value)
	{
		if(!is_bool($value))
		{
			throw new CMSException('Es handelt sich nicht um einen Boolean', CMSException::T_SYSTEMERROR );
		}
		$this -> isStatic = $value;
	}
	
	/**
	 * Speichert die Eigenschaften der Action in der Datenbank
	 * 
	 * Ist noch kein Datensatz für diese Action vorhanden, wird einer erstellt
	 * Ansonsten wird der alte geupdatet.
	 *
	 */
	public function Save()
	{
		if(is_null($this -> id))
		{
			try 
			{
				SqlManager::GetInstance() -> Insert('sysaction', array('function' => $this -> fu, 'class' => $this -> cl));
			}
			catch (SqlManagerException $ex)
			{
				throw new CMSException('Erstellen einer neuen Action fehlgeschlagen!',CMSException::T_WARNING ,$ex);
			}
			return ;
		}
		try 
		{
			SqlManager::GetInstance() -> Update('sysaction', array('function' => $this -> fu, 'class' => $this -> cl), 'id=\''. $this -> id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Update der Action fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
	}
	
	/**
	 * Lädt die Eigenschaften einer Action aus einer Datenbank
	 *
	 */
	private function LoadAction()
	{
		$daten = null;
		try 
		{
			$daten = SqlManager::GetInstance() -> SelectRow('sysaction', $this -> id, array('function', 'class', 'static'));
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Timer-Eigenschaften fehlgeschlagen!',CMSException::T_SYSTEMERROR ,$ex);
		}
		
		$this -> fu = $daten['function'];
		$this -> cl = $daten['class'];
		$this -> isStatic = $daten['static'];
	}
	
	public static function GetNameById($actionId)
	{
		return SqlManager::GetInstance() ->  SelectItem('sysaction', 'name', 'id=\''. $actionId .'\'');	
	}
}