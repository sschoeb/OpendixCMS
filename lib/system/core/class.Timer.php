<?php

/**
 * Klasse für den Umgang mit Timern
 * 
 *
 * @package    	OpendixCMS
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	1.0
 */

/**
 * Erlaubt es einfach Timer zu setzen und zu entfernen
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.0
 * @copyright 	2006-2009, Stefan Schöb
 * @since	 	2.0     
 */
class Timer
{

	/**
	 * Speichert einen Timer
	 *
	 * @param int 		$id			ID des Timers der gespeichert werden soll. Wird NULL mitgegeben wird ein neuer
	 * 								Timer erstellt
	 * @param String 	$date		Datum des Timers im MySQL-Format
	 * @param int 		$active		1 oder 0 über den aktiv-status des Timers
	 * @param int 		$actionId	ID der Action die vom Timer ausgeführt werden soll
	 * @return int	ID des Timers
	 */
	public static function Set($id, $date, $actionId, $moduleId, $datensatzId)
	{
		
		if(is_null($id))
		{
			try 
			{
				return SqlManager::GetInstance() -> Insert('systimer', array(	'date' 		=> $date, 
																				'modulId' 	=> $moduleId, 
																				'actionId' 	=> $actionId, 
																				'datensatzId' => $datensatzId));
			}
			catch (SqlManagerException $ex)
			{
				throw new CMSException('Einf&uuml;gen des Timers fehlgeschlagen!',CMSException::T_WARNING ,$ex);
			}
		}
		
		try 
		{
			SqlManager::GetInstance() -> Update('systimer', array('date' => $date, 'active' => $active, 'actionId' => $actionId), 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Update des Timers fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
	
		return $id;
	}
	
	/**
	 * Entfernt einen gesetzen Timer aus der systimer-Tabelle
	 *
	 * @param unknown_type $id
	 */
	public static function Remove($id)
	{
		try 
		{
			SqlManager::GetInstance() -> Delete('systimer', 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Entfernen des Timers fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
	}
	
	/**
	 * Gibt die Informationen über den Timer zurück
	 *
	 * @param itn $id
	 */
	public function GetInformation($id)
	{
		$daten = array();
		try 
		{
			$daten = SqlManager::GetInstance() -> SelectRow('systimer', $id, array('date', 'actionId'));
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Timer-informationen fehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
		return $daten;
	}
		
	/**
	 * Durchl�uft die systimer-Tabelle und f�hrt alle n�tigen Timer aus
	 *
	 */
	public static function ProceedTimer()
	{
	
		$daten = null;
		try{
			$daten = SqlManager::GetInstance() -> Select('systimer', array('actionId'), 'date<\'' . strftime('', time()). '\'');
		}catch(SqlManagerException $ex)
		{
			throw new CMSException('Sql-fehler', CMSException::T_SYSTEMERROR, $ex);
		}
		
		//echo count($daten);
		for($i=0; $i<count($daten); $i++)
		{
			$action = new Action($daten[$i]['actionId']);
			echo "JO";
			$action -> Run();
		}
	}
	
	public static function GetTimerForEntity($moduleId, $entityId)
	{
		$timer = SqlManager::GetInstance() -> Select('systimer', array('id', 'actionId', 'date'), 'modulId=\''. $moduleId . '\' AND datensatzId=\''. $entityId .'\'');
		for ($i=0; $i<count($timer); $i++)
		{
			$timer[$i]['name'] = Action::GetNameById($timer[$i]['actionId']);
			//$timer[$i]['date'] = ;
		}
		return $timer;
	
	
	}
}










