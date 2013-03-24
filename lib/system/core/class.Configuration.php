<?php
/**
 *
 * Repräsentiert eine Konfiguration
 * 
 * @version 	1.0
 * @since 	2.0
 * @author	Stefan Schöb <opendix@gmail.com>
 *
 */
class Configuration
{
	/**
	 * Pfad zur Konfiguration
	 *
	 * @var String
	 */
	private $path = '';
	
	/**
	 * Array in dem alle Konfigurationseinstellungen gespeichert werden.
	 *
	 * @var assocArray
	 */
	private $configurations = array ();
	
	/**
	 * Konstruktor
	 * 
	 * Dem Konstruktor muss ein gültiger Pfad zu einer Konfigurationsdatei übergeben werden
	 * ansonsten wird eine CMSException ausgelöst!
	 *
	 * @exception CMSException wenn die Datei nicht existiert
	 * @param String 	$path
	 * @param Boolean 	$load
	 */
	public function __construct($path, $load = true)
	{
		//Prüfen ob die Konfigurationsdatei existiert
		if (! file_exists ( $path ))
		{
			throw new CMSException ( 'Die angegebene Konfigurationsdatei existiert nicht! (' . $path . ')', CMSException::T_SYSTEMERROR );
		}
		//Speichern des übergebenen Pfades
		$this->path = $path;
		//Wird als zweiter Parameter "true" übergeben, so wird beim instanzieren die Konfigurations ausgelesen
		if ($load)
		{
			$this->Load ();
		}
	}
	
	/**
	 * Gibt eine Einstellung zurück
	 * 
	 * Es kann wie folgt auf die Einstellungen zugegriffen werden:
	 * Es wird die Einstellung "host" aus der Section "Database" gesucht:
	 * $configuration -> Database/host
	 * 
	 * @exception CMSException wenn die gesuchte Konfiguration nich existiert
	 * @param 	String $name	Pfad zur Konfiguration. z.B. Database/host oder Mail/SmtpName
	 * @return 	String
	 */
	public function __get($configPath)
	{
		
		//Es wird immer Section/Configname übergeben -> Teilen von Section und Configname
		$split = preg_split ( '/\//', $configPath );
		$section = $split [0];
		$configName = $split [1];
		
		//Prüfen ob die gesuchte Konfiguration auch existiert
		if (! isset ( $this->configurations [$section] [$configName] ))
		{
			throw new CMSException ( 'Die gesuchte Konfiguration existiert nicht(' . $configPath . ')', CMSException::T_SYSTEMERROR );
		}
		
		return $this->configurations [$section] [$configName];
	}
	
	public function SectionExists($name)
	{
		return isset ( $this->configurations [$name] );
	}
	
	/**
	 * Siehe __get
	 *
	 * @param unknown_type $configPath
	 * @return unknown
	 */
	public function Get($configPath)
	{
		return $this->__get ( $configPath );
	}
	
	/**
	 * Laden der Konfiguration aus der INI-Datei
	 *
	 */
	public function Load()
	{
		$this->configurations = parse_ini_file ( $this->path, true );
	}
	
	/**
	 * Speichern der Konfiguration in der INI-Datei
	 *
	 */
	public function Save()
	{
		$this->WriteIniFile ( $this->configurations );
	}
	
	/**
	 * Gibt den Pfad zur ausgelesenen Konfiguration zurück
	 *
	 * @return String
	 */
	public function GetConfigurationPath()
	{
		return $this->path;
	}
	
	/**
	 * Setzt den Pfad zur Konfiguration
	 * 
	 * Kann benutzt werden um eine Konfiguration auszulesen und diese an einem anderen Ort zu speichern
	 *
	 * @param String $value 	Neuer Pfad zur Konfiguration
	 */
	public function SetConfigurationPath($value)
	{
		$this->path = $value;
	}
	
	/**
	 * Schreib die Konfiguration in die Konfigurationsdatei welche von dieser Instanz repräsentiert wird
	 *
	 * @param array 	$assoc_arr	Array mit den Konfigurationseinstellungen
	 * @return unknown
	 */
	public function WriteIniFile($assoc_arr)
	{
		$content = '';
		
		foreach ( $assoc_arr as $key => $elem )
		{
			if (is_array ( $elem ))
			{
				if ($key != '')
				{
					$content .= "\r\n[" . $key . "]\r\n";
				}
				foreach ( $elem as $key2 => $elem2 )
				{
					$content .= $key2 . "=" . $elem2 . "\r\n";
				}
			} else
			{
				$content .= $key . "=" . $elem . "\r\n";
			}
		}
		
		if (! file_put_contents ( $this->path, trim ( $content ) ))
		{
			throw new CMSException ( 'Die Konfiguration konnte nicht in der INI-Datei gespeichert werden!', CMSException::T_WARNING );
		}
	}
}