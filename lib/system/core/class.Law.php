<?php

/**
*
* Verwaltet Berechtigungsanfragen auf die Datenbank
* 
* @version 	1.0
* @since 	2.0
* @author	Stefan Sch�b <opendix@gmail.com>
*
*/
class Law
{
	/**
	 * Bereits abgefragte Berechtigungen werden in diesem Array zwischengespeichert
	 *
	 * @var array
	 */
	private $laws = array();

	/**
	 * Berechtigung eine Seite anzusehen
	 *
	 */
	const T_VIEW 	= 1;
	
	/**
	 * Berechtigung eine Seite zu editieren
	 *
	 */
	const T_EDIT 	= 2;
	
	/**
	 * Berechtigung ein Element auf einer Seite hinzuzufügen
	 *
	 */
	const T_ADD 	= 3;
	
	/**
	 * Berechtigung ein Element auf einer Seite zu löschen
	 *
	 */
	const T_DELETE 	= 4;
	

	/**
	 * Abfrage ob der Besucher auf einen Men�punkt die abgefragte Berechtigung hat
	 * 
	 * Als $type kann ein Wert zwischen 1-4 angegeben werden:
	 * 1 = Einsehen
	 * 2 = Editieren
	 * 3 = Hinzufügen
	 * 4 = Löschen
	 * 
	 * Es wird gepr�ft ob der User selbst berechtig ist die Operation auf den Men�punkt anzuwenden
	 * oder ob er sich in einer Gruppe befindet welche die benötigten Berechtigungen aufweist
	 *
	 * @param int $type		Typ der Berechtigun die gefragt ist
	 * @param int $menueId	Men�punkt der gepr�ft werden soll
	 * @exception CmsException wenn ein Fehler in der Mysql-Abfrage vorfällt
	 * @return Boolean true wenn der User berechtigt ist ansonsten false
	 */
	public function HasLaw($type, $menueId)
	{
		$count = NULL;
		//Prüfen ob diese Berechtigung bereits abgefragt wurde
		if(isset($this -> laws[$type][$menueId]))
		{
			//Wenn bereits abgefragt und auf true dann ist der User berechtigt
			if($this -> laws[$type][$menueId])
			{
				return true;
			}
		}
		
		//Wenn ein Menüpunkt als öffentlich markiert ist, dann hat jeder Besucher
		//der Webseite die View-Berechtigung, hier wird geprüft ob es sich beim 
		//Menüpunkt um einen öffentlichen handelt, falls die View-Berechtigung
		//abgefragt wird 
		if($type == Law::T_VIEW)
		{
			$common = null;
			try 
			{
				$common = SqlManager::GetInstance() -> Count('syscommon', 'menueid=\''. $menueId .'\'');
			}
			catch (SqlManagerException $ex)
			{
				throw new CMSException('Abfrage ob die Seite &ouml;ffentlich ist fehlgeschlagen!',CMSException::T_SYSTEMERROR ,$ex);
			}
			if($common > 0)
			{
				//Der Menüpunkt ist öffentlich -> Der Besucher ist berechtigt
				$this -> laws[Law::T_VIEW][$menueId] = true;
				return true;
			}
		}
		
		//Die gewünschte Berechtigung wurde noch nie abgefragt
		//Abfrage der Daten
		try 
		{

			$userSql = 'SELECT COUNT(sl.id) AS anz FROM 
								syslaw sl, 
								sysuser su
							WHERE
								sl.type=\'' . $type . '\'
								AND
									sl.menueId = \''. $menueId .'\'
								AND 
									sl.userId=su.id 
								AND 
									sl.userId=\'' . Cms::GetInstance() -> GetUser() -> id .'\'';
			
			$groupSql = 'SELECT COUNT(sl.id) AS anz FROM 
								syslaw sl, 
								sysuser su, 
								syslawgroup slg, 
								sysuserlawgroup sulg
							WHERE
								sl.type=\'' . $type . '\'
								AND
									sl.menueId = \''. $menueId .'\'
								AND 
									sl.groupId=slg.id
								AND
									sulg.groupId=slg.id
								AND 
									sulg.userId=\'' . Cms::GetInstance() -> GetUser() -> id .'\'';
			
			$userCount = SqlManager::GetInstance() -> Query($userSql);
			$groupCount = SqlManager::GetInstance() -> Query($groupSql);
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Berechtigungen konnten nicht aus der Datenbank abgefragt werden!', $ex);
		}
		
		//Die Daten aus der Abfrage auslesen
		$userCount = mysql_result($userCount, 0);
		$groupCount = mysql_result($groupCount, 0);
		
		//Wenn die Anzahl der von der Abfrage betroffenen User/Gruppen 0 ist, ist der User nicht berechtigt
		if($userCount == 0  && $groupCount == 0)
		{
			//Speichern der Berechtigung im zwischenspeichern um erneutes nachfragen in der Datenbank zu verhindern
			$this -> laws[$type][$menueId] = false;
			return false;
		}
		//Speichern der Berechtigung im zwischenspeichern um erneutes nachfragen in der Datenbank zu verhindern
		$this -> laws[$type][$menueId] = true;
		return true;
	}
	
	
	public static function IsItemOfGroup($itemId, $groups, $table, $rowName='gId')
	{
		$where = '';
		if(is_array($groups))
		{
			$c=count($groups);
			for($i=0;$i<$c;$i++)
			{
				if($where == '')
				{
					$where = '('. $rowName .'=\''. $groups[$i]['propertyValue'] .'\'';
				}
				else 
				{
					$where .= ' OR '. $rowName .'=\''. $groups[$i]['propertyValue'] .'\'';
				}
			}
		}
		else 
		{
			$where = '('. $rowName .'=\''. $groups .'\'';	
		}
		
		$where .= ') AND id=\''. $itemId .'\'';
		$isInGroup = null;
		try 
		{
			$isInGroup = SqlManager::GetInstance() -> Count($table,  $where);
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Berechtigung auf Element fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		if($isInGroup == 0)
		{
			return false;
		}
		return  true;
	}
	
	/**
	 * Prüft ob sich eine Datei in einer File-Verlinkungs-Tabelle befindet
	 * 
	 * Damit wird verhindert, dass z.B. ein Besucher auf sämtliche als Anhänge 
	 * angefügten Dateien zugriff bekommt. Der Besucher darf nur auf die, einem
	 * Ihm zugänglichen Termin/Bericht/... angehängten Anhänge zugreiffen
	 *
	 * @param int 		$itemId		ID des Termins/Berichts/...
	 * @param int 		$recordId	ID des Datensatzes welcher in der Verlinkungs-Tabelle vorhanden sein muss
	 * @param String 	$tableName	Name der Verlinkungs-Tabelle
	 * @param String 	$colName	Spalte für $itemId (z.B. berichtId, agendaId, ...)
	 * @return Boolean
	 */
	public static function IsInFileLinkTable($itemId, $recordId, $tableName, $colName)
	{
		$access = null;
		try 
		{
			$access = SqlManager::GetInstance() -> Count($tableName, '`' . $colName . '`=\'' . $itemId . '\' AND id=\''. $recordId .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Berechtigung fehlgeschlagen!',CMSException::T_SYSTEMERROR ,$ex);
		}
		
		if ($access == 0) 
		{
			return  false;
		}
		return true;
	}
}