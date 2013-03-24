<?php

class Cms
{
	/**
	 * Statische Variabel fuer den Singleton
	 *
	 * @var Cms
	 */
	private static $myInstance;
	
	/**
	 * Instanz der Berechtigungsklasse
	 *
	 * @var Law
	 */
	private $law = NULL;
	
	/**
	 * Instanz der User-Klasse
	 *
	 * @var User
	 */
	private $user = NULL;
	
	/**
	 * Instanz der Konfigurationsklasse
	 *
	 * @var Configuration
	 */
	private $configuration = NULL;
	
	/**
	 * Instanz der Menü-Klasse
	 *
	 * @var Menue
	 */
	private $menue = NULL;
	
	/**
	 * Instanz des Moduls
	 *
	 * @var unknown_type
	 */
	private $module = NULL;
	
	/**
	 * Array in dem abgefragte User zwischengespeichert werden
	 *
	 * @var User-Array
	 */
	private $users = array ();
	
	private $template = NULL;
	
	/**
	 * Gibt die Instanz eines CMS zurueck
	 * 
	 * @access public
	 * @return Cms
	 */
	public static function GetInstance()
	{
		if (! self::$myInstance instanceof Cms)
		{
			self::$myInstance = new Cms ();
		}
		return self::$myInstance;
	}
	
	/**
	 * Getter der CMS-Eigenschaften
	 *
	 * @param String $name Name der Eigenschaft die abgefragt wird (configuration, menue, user, law)
	 * @exception CMSException wenn auf eine nicht vorhandene Eigenschaft zugegriffen werden soll
	 * @return mixed
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'configuration' :
				return $this->configuration;
				break;
			case 'menue' :
				return $this->menue;
				break;
			case 'user' :
				return $this->user;
				break;
			case 'law' :
				return $this->law;
				break;
			default :
				throw new CMSException ( 'Unbekannte System-Eigenschaft abgefragt!', CMSException::T_SYSTEMERROR );
				break;
		}
	}
	
	/**
	 * Initialisiert alle wichtigen Klassen des CMS
	 * 
	 * Reihenfolge:
	 * 1. Law
	 * 2. Session
	 * 3. User
	 * 4. Configuration
	 *
	 */
	public function Init()
	{
		// Klasse stellt die allgemeine Konfiguration des CMS bereit
		//$this->configuration = new Configuration ( '../../config/config.ini' );
		//$this->configuration = new Configuration ( 'config/config.ini' );
		$this->configuration = new Configuration(INIFILE);
		
		// MySql-Sprache setzen gleichzeitig wird die erste Instanz
		// der SqlManager-Klasse erstellt welche gleich auch die 
		// Verbindung aufbaut
		SqlManager::GetInstance ()->SetMySqlLanguage ( $this->configuration->Get ( 'language/mysql' ) );
		
		// PHP-Sprache setzen
		setlocale ( LC_TIME, $this->configuration->Get ( 'language/php' ) );
		
		// Objekt mit der Abfragen auf die Berechtigung einer Seite gehandelt werden
		$this->Law = new Law ();
		
		// Einen Step in der Session machen
		Session::Step ();
		
		// Klasse handelt die Daten des Besuchers/Users
		$this->User = new User ();
		
		$this->template = new Template ();
		
		// System-Javascript initialisieren
		JsImport::ImportSystemJS ( 'config.js' );
		JsImport::ImportSystemJS ( 'system.js' );
		JsImport::ImportSystemJS ( 'prototype.js' );
		
		$this->menue = new Menue ();
	}
	
	/**
	 * Lädt das Modul
	 * 
	 * Optional kann als Parameter eine Modul-ID mitgegeben werden. Wird der Wert NULL übergeben 
	 *
	 * @param unknown_type $id
	 */
	public function LoadModule($id = NULL)
	{
		if (is_null ( $id ))
		{
			$id = $this->GetMenueId ();
		}
		
		$daten = NULL;
		try
		{
			$daten = SqlManager::GetInstance ()->Select ( 'sysmenue', array ('class', 'template', 'moduleId' ), 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Das Modul konnte nicht geladen werden!', $ex );
		}
		
		//Neues Modul instanzieren
		$this->module = new Module ( $daten [0] ['moduleId'] );
		
		$template = 0;
		try
		{
			$template = SqlManager::GetInstance ()->SelectItem ( 'systemplate', 'template', 'id=\'' . $daten [0] ['template'] . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage des Moduls fehlgeschlagen', CMSException::T_SYSTEMERROR, $ex );
		}
		
		//Dem index-Template das Modul-Template angeben
		MySmarty::GetInstance ()->OutputSystemVar ( 'content', $template );
		
		//Generieren des Moduls 
		$this->module->Generate ( $daten [0] ['class'] );
	
	}
	
	/**
	 * Gibt die Seite aus
	 *
	 */
	public function Output()
	{
		$this->menue = new Menue ();
		
		$smarty = MySmarty::GetInstance ();
		$smarty->SetTemplate ( $this->template );
		
		CssImport::ImportCss ( 'style.css' );
		
		$properties ['add'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'add' ) );
		if (isset ( $_GET ['id'] ))
		{
			$properties ['save'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'save', 'id' => $_GET ['id'] ) );
		} else
		{
			$properties ['save'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'save' ) );
		}
		
		$properties ['upload'] = Functions::GetLink ( array ('action' => 'upload' ), true );
		$properties ['main'] = Functions::GetLink ( array ('sub' => $_GET ['sub'] ) );
		if ($this->GetUser ()->IsDeclared ())
			$properties ['login'] = Functions::GetLink ( array ('sub' => Cms::GetLoginSub (), 'action' => 'logout' ) );
		else
			$properties ['login'] = Functions::GetLink ( array ('sub' => Cms::GetLoginSub () ) );
		
		$path = $this->GetOutputPath ();
		
		$user = array ();
		$user ['isdeclared'] = $this->GetUser ()->IsDeclared ();
		
		$smarty->OutputSystemVar ( 'path', $path );
		$smarty->OutputSystemVar ( 'link', $properties );
		$smarty->OutputSystemVar ( 'menue', $this->menue->Output () );
		$smarty->OutputSystemVar ( 'user', $user );
		
		$smarty->Display ();
		
	}
	/**
	 * 
	 */
	private function GetOutputPath()
	{
		$path = array ();
		$templatePath = array ();
		$templatePath ['base'] = $this->template->GetFullTemplatePath ();
		$templatePath ['image'] = $templatePath ['base'] . 'images/';
		$templatePath ['css'] = $templatePath ['base'] . 'css/';
		$path ['template'] = $templatePath;
		return $path;
	}
	
	/**
	 * Gibt die Menue-ID welche auf der Seite als sub-Parameter mitgegeben wird zurück
	 * 
	 * Die Methode überprüft auch die korrektheit der ID
	 * - Handelt es sich um einen numerischen Wert?
	 * - Ist die ID in der Datenbank verfügbar?
	 * Sollte keine ID übergeben worden sein wird aus der Datenbank die ID abgefragt die als 
	 * Standard-Seite geladen werden soll.
	 * 
	 * @exception CMSException wenn keine gültige ID �bergeben wurde
	 * @return int modul-ID die aufgerufen wird
	 */
	public function GetMenueId()
	{
		//ID aus der URL abfragen
		$id = '';
		if (! isset ( $_GET ['sub'] ))
		{
			//If no ID is set, load the standard-Page
			$id = $this->GetStandardModule ();
			$_GET ['sub'] = $id;
			return $id;
		}
		
		$id = Validator::ForgeInput ( $_GET ['sub'] );
		
		$row = null;
		try
		{
			$row = SqlManager::GetInstance ()->SelectRow ( 'sysmenue', $id, array ('type', 'href' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-fehler', CMSException::T_SYSTEMERROR, $ex );
		}
		
		// Der Typ 2 im Men�punkt sagt, dass der Men�punkt direkt auf einen Untermen�punkt 
		// weitergeleitet wird
		$row ['type'];
		if ($row ['type'] == 2)
		{
			$id = $row ['href'];
			$_GET ['sub'] = $id;
		}
		
		//Es wurde ein Wert für sub übergeben:
		//prüfen ob es ein numerischer Wert ist
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Bei der &uuml;bergebenen Modul-ID handelt es sich nicht um einen g&uuml;ltigen Wert!', CMSException::T_SYSTEMERROR );
		}
		
		//Es ist ein numerischer Wert -> Abfrage der Daten aus der Datenbank
		$count = NULL;
		try
		{
			$count = SqlManager::GetInstance ()->Count ( 'sysmenue', 'id=' . $id );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Das Modul konnte nicht aus der Datenbank abgefragt werden!', CMSException::T_SYSTEMERROR, $ex );
		}
		
		if ($count == 0)
		{
			throw new CMSException ( 'Zu der in der SUB &uuml;bergebenen ID existiert kein Men&uuml;eintrag!', CMSException::T_SYSTEMERROR );
		}
		
		return $id;
	
	}
	
	/**
	 * Gibt die ID des als Standard definierten Moduls zur�ck
	 * 
	 * @return int
	 * @exception CMSException 
	 *
	 */
	private function GetStandardModule()
	{
		$id = NULL;
		try
		{
			$id = SqlManager::GetInstance ()->SelectItem ( 'sysmenue', 'id', 'standard=1' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Das Standard-Modul konnte nicht abgefragt werden!', CMSException::T_SYSTEMERROR, $ex );
		}
		return $id;
	}
	
	/**
	 * Gibt die Konfiguration zurück
	 *
	 * @return Configuration
	 */
	public function GetConfiguration()
	{
		return $this->configuration;
	}
	
	/**
	 * Gibt die Instanz der User-Klasse zurück
	 *
	 * @param int $id	Wird kein Wert mitgegeben, gibt die Funktion die User
	 * Instanz des aktuellen Besuchers zurück. Ansonsten die User
	 * Instanz des Users mit der entsprechenden ID.
	 * @return User
	 */
	public function GetUser($id = null)
	{
		if (is_null ( $id ))
		{
			return $this->User;
		}
		
		//Abgefragte User werden zwischengespeichert, darum hier
		//prüfen ob der User nicht bereits abgefragt wurde
		if (! isset ( $this->users [$id] ))
		{
			//... wenn nicht dann eine User-Instanz erstellen
			$this->users [$id] = new User ( false );
			$this->users [$id]->load ( $id );
		}
		
		return $this->users [$id];
	}
	
	/**
	 * Gibt die Law-Instanz zurück
	 *
	 * @return unknown
	 */
	public function GetLaw()
	{
		return $this->Law;
	}
	
	public function GetMenue()
	{
		return $this->menue;
	}
	
	/**
	 * Gibt das geladene Modul zurück
	 *
	 * @return unknown
	 */
	public function GetModule()
	{
		return $this->module;
	}
	
	public function GetTemplate()
	{
		return $this->template;
	}
	
	public function reboot()
	{
		$this->Init ();
	}
	
	public static function GetLoginSub()
	{
		try
		{
			return SqlManager::GetInstance ()->SelectItem ( 'sysmenue', 'id', 'type=\'4\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Sql-Fehler', CMSException::T_SYSTEMERROR, $ex );
		}
	}

}