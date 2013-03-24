<?php
/**
*
* Repraesentiert und verwaltet ein Modul
* 
* @version 	1.0
* @since 	2.0
* @author	Stefan Schoeb <opendix@gmail.com>
*
*/
class Module
{
	/**
	 * ID des Moduls 
	 * 
	 * Wird beim instanzieren übergeben und dann vom Konstruktor gesetzt.
	 *
	 * @var Int
	 */
	private $moduleId = NULL;
	
	/**
	 * Name des Moduls
	 * 
	 * Wird beim instanzieren nach festlegen der Modul-ID ermittelt
	 *
	 * @var String
	 */
	private $moduleName = '';
	
	/**
	 * Instanz der Modul-Klasse
	 *
	 * @var object
	 */
	private $moduleInstance = NULL;

	/**
	 * Konfiguration für dieses Modul
	 *
	 * @var Configuration
	 */
	private $config = null;
	
	private $dbConfig = null;
	
	/**
	 * Klasse die zum aufrufen des Moduls instanziert wird
	 *
	 * @var String
	 */
	private $instanceClass = '';
	
	/**
	 * Konstruktor
	 * 
	 * prüft ob die ID numerisch ist.
	 * inistialisiert den Namen des Moduls
	 * 
	 * @exception CMSException wenn die id nicht numerisch ist
	 * @param unknown_type $id
	 */
	public function __construct($id)
	{
		
		if(!is_numeric($id))
		{
			throw new CMSException('Keine g&uuml;ltige ID &uuml;bergeben!', CMSException::T_SYSTEMERROR );
		}
		
		$this -> moduleId = $id;
		$this -> InitModulName();
		$this -> InitConfiguration();
	}

	/**
	 * Liesst den Namen des Moduls aus der Datenbank aus
	 * 
	 * Die Modul-ID muss gesetzt sein damit der Namen ausgelesen werden kann
	 * 
	 * @exception CMSException wenn irgendwas mit der Datenbankabfrage nicht funktioniert hat
	 *
	 */
	private function InitModulName()
	{
		try 
		{
			$this -> moduleName = SqlManager::GetInstance() -> SelectItem('sysmodul', 'name', 'id=' . $this -> moduleId);
			
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Der Name des gew&uuml;nschten Moduls konnte nicht abgefragt werden!',CMSException::T_SYSTEMERROR , $ex);
		}
	}
	
	/**
	 * Initialisiert die Modul-Konfiguration 
	 * 
	 * Eine Konfiguration muss nicht vorhanden sein, darum wird erst geprüft ob die INI-Datei vorhanden ist, wenn 
	 * ja wird diese geparst und eingelesen. Sollte dies nicht der Fall sein wird für die Config-Eigenschaft der Klasse
	 * null gesetzt damit der Getter beim zugriff auf die Eigenschaft eine Exception wirft
	 *
	 */
	private function InitConfiguration()
	{
		//Zusammenstellen des Pfades der zur Konfiguratiosndatei führt
		$configFolderPath 	= Cms::GetInstance() -> GetConfiguration() -> Get('Path/modulconfigfolder');
		$configDbIniPath 	= Cms::GetInstance() -> GetConfiguration() -> Get(('Path/moduldbconfigfile'));
		$configIniPath 		= Cms::GetInstance() -> GetConfiguration() -> Get(('Path/modulconfigfile'));
		
		$modPath = $this -> GetModulePath();
			
		//Einlesen der gesamten Konfiguration
		$this -> config 	= new Configuration($modPath . $configFolderPath . $configIniPath);
		$this -> dbConfig 	= new Configuration($modPath . $configFolderPath . $configDbIniPath);
		
	}
	
	/**
	 * Gibt den Pfad zu diesem Modul zurück
	 *
	 * @return String
	 */
	public function GetModulePath()
	{
		$rootPath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/moduleFolder');
		return $rootPath .= $this -> moduleId . '_' . strtolower($this -> moduleName) . '/';
	}

	/**
	 * Instanziert das Modul
	 * 
	 * @exception CmsException wenn die Klasse für das Modul nicht existiert
	 *
	 */
	public function Generate($class)
	{
		$this -> instanceClass = $class;
		if(!class_exists($this ->instanceClass))
		{
			throw new CMSException('Die f&uuml;r das Modul ben&ouml;tigte Klasse ist nicht vorhanden!(\''. $this ->instanceClass .'\')', CMSException::T_SYSTEMERROR );
		}
		
		$this -> RunBootFunctions();
		
		$this -> moduleInstance = new $this -> instanceClass;
		
		$this -> RunBootFunctions(false);
		
	}	
	
	/**
	 * Funktion mit der man die Boot-Funktionen ausführen kann
	 * 
	 * Der Parameter bestimmt ob die Funktionen vor dem instanzieren des Moduls oder erst danach ausgeführt werden
	 *
	 * @param Boolean $bevoreModuleInstance		Bei true werden die Funktionen aufgerufen die vor dem instanzieren 
	 * 											aufgerufen werden müssen. Bei false diese nach dem instanzieren
	 * @exception Wenn die Klasse einer aufzurufenden Funktion nicht existiert
	 */
	public function RunBootFunctions($bevoreModuleInstance=true)
	{
		//Abfragen der Daten der Bootfunktionen
		$daten = array();
		try 
		{
			if(!$bevoreModuleInstance)
			{
				$bevoreModuleInstance = '0';
			}
			$daten = SqlManager::GetInstance() -> Select('sysboot', array('actionId'), 'bevore=\'' . $bevoreModuleInstance . '\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Die Boot-Funktionen konnten nicht ermittelt werden!', CMSException::T_SYSTEMERROR , $ex);
		}
		
		$c=count($daten);
		for($i=0;$i<$c;$i++)
		{
			$action = new Action($daten[$i]['actionId']);
			$action -> Run();
		}
	}
	
	/**
	 * Gibt die ModulID zurück
	 *
	 * @return unknown
	 */
	public function GetModuleid()
	{
		return $this -> moduleId;
	}
	
	/**
	 * Gibt die Konfiguration für diese Modul zurück 
	 * 
	 * Ist keine Konfigurationsdatei vorhanden wird hier eine Exception geworfen!
	 *
	 * @return Configuration
	 */
	public function GetDbConfiguration($key, $menueId=null)
	{
		//Prüfen ob eine Konfiguration geladen wurde
		if(is_null($this -> dbConfig))
		{
			throw new CMSException('Es ist keien Konfiguration definiert!', CMSException::T_WARNING );
		}
		
		//Überprüfen ob die Konfigurationd ie abgefragt wird existiert
		if(!$this -> dbConfig -> SectionExists($key))
		{
			throw new CMSException('Unbekannte Eigenschaft aus der Modul-Konfigurationa bgefragt!', CMSException::T_WARNING );
		}
		
		//Abfrage des Wertes aus der Datenbank
		if(is_null($menueId))
		{
			$menueId = Cms::GetInstance() -> GetMenueId();	
		}
		$value = null;
		try 
		{
			$where =  'menueId=\''. $menueId .'\' AND propertyName=\'' . $key . '\'';
			$c = SqlManager::GetInstance() -> Count('sysmodulproperties', $where);
			if($c > 1)
			{
				$value = SqlManager::GetInstance() -> Select('sysmodulproperties', array('propertyValue'), $where);
			}
			elseif($c == 1)
			{
				$value = SqlManager::GetInstance() -> SelectItem('sysmodulproperties','propertyValue', $where);
			}
			else 
			{
				throw new CMSException('Diese Konfiguration ist nicht in der Datenbank! ('. $key .')', CMSException::T_WARNING );
			}
			
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Modul-Konfigurationf ehlgeschlagen!',CMSException::T_WARNING ,$ex);
		}
		
		return $value;
	}
	
	public function GetConfiguration($key)
	{
		return $this -> config -> Get($key);
	}
	
	/**
	 * Gibt sämtliche möglichen Verbindungen auf dieses Modul zurück
	 *
	 * @return unknown
	 */
	public function GetConnections()
	{
		
		if($this -> config -> Get('moduleconnections/enabled') == 0)
		{
			throw new CMSException('Es sind keine Verbindungen auf dieses Modul erlaubt!', CMSException::T_MODULEERROR );
		}
		$modconn = 'moduleconnection';
		$connections = array();
		$c = $this -> config -> Get('moduleconnections/count');
		for($i=1; $i<=$c;$i++)
		{
			$connections[$i]['id'] = $i;
			$connections[$i]['name'] = $this -> config -> Get($modconn . $i . '/name');
			$connections[$i]['elements'] = $this -> config -> Get($modconn . $i . '/elements');
		}
		return $connections;
	}
	
	/**
	 * Gibt die Parameter die für die Link-erstellung benötigt werden zurück
	 *
	 * @param int $connectionId
	 * @param array $params
	 */
	public function GetLinkParams($connectionId, $values)
	{
		
		$params = array();
		$modconnection = 'moduleconnection' .  $connectionId . '/';
		$c = $this -> config -> Get($modconnection . 'paramcount');
		
		for($i=0;$i<$c;$i++)
		{

			$name = $this -> config -> Get($modconnection . 'param' . ($i+1) . 'name');
			$params[$name] = $this -> config -> Get($modconnection . 'param' . ($i+1) . 'value');
			foreach ($values as $key => $value)
			{
				if(preg_match('/\?'. $key .'\?/', $params[$name]))
				{
					$params[$name] = $value;
				}
			}
		}
		
		return $params;
		
	}
	
	public function GetConnectionElements($menueId, $connectionId)
	{
		$modconnection 	= 'moduleconnection' .  $connectionId . '/';
		if($this -> config -> Get($modconnection . 'elements') == 0)
		{
			return;
		}
		$table 			= $this -> config -> Get($modconnection . 'sqltable');
		$col 			= $this -> config -> Get($modconnection . 'sqlcol');
		$restrictionFu	= $this -> config -> Get($modconnection . 'sqlrestrictionfunction');
		
		try 
		{
			$restrictionCl = SqlManager::GetInstance() -> SelectItem('sysmenue', 'class', 'id=\''. $menueId .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('SQL-Abfrage der Connection-Klasse fehlgeschlagen', CMSException::T_MODULEERROR , $ex);
		}
		
		$restriction 	= call_user_func(array($restrictionCl, $restrictionFu), $this, $menueId);
		
		try 
		{
			$elements = SqlManager::GetInstance() -> Select($table, array('id', $col), $restriction);
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('SQL-Abfrage der Connection-Elemente fehlgeschlagen!', CMSException::T_MODULEERROR , $ex);
		}
		return $elements;
	}
	
	public function GetModuleIdOutOfMenueId($menueId)
	{
		try 
		{
			return SqlManager::GetInstance() -> SelectItem('sysmenue', 'moduleId', 'id=\''. $menueId .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage des Moduls fehlgeschlagen!', CMSException::T_MODULEERROR );
		}
	}
	
	public function GetName()
	{
		return $this -> moduleName;
	}
	
	public static function GetModuleIdByName($moduleName)
	{
		try 
		{
			$data = SqlManager::GetInstance() -> Select('sysmodul', array('id'), 'name=\''. $moduleName .'\'');
			return $data[0]['id'];
		}catch(SqlManagerException $ex)
		{
			throw new CMSException('SQL-Fehler', $ex);
		}
		
		throw new CMSException('Modul mit diesm Namen nicht gefunden "'.$moduleName.'"');
	}
}