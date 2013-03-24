<?php

/**
*
* Lädt und veraltet die Template-Informationen
* 
* @version 	1.0
* @since 	2.0
* @author	Stefan Schöb <opendix@gmail.com>
*
*/
class Template
{
	/**
	 * Pfad zu den Template-Dateien
	 *
	 * @var String
	 */
	private $templatePath = '';
	
	/**
	 * Pfad zum template_c Ordner
	 * 
	 * In diesem Ordner speichert Smarty die kompilierten Template-Dateien.
	 * Wird ein ungültiger Pfad angegeben wird Smarty einen Error auswerfen
	 *
	 * @var String
	 */
	private $template_cPath = '';
	
	/**
	 * Pfad zum Ordner in dem die Template-spezifischen CSS-Dateien gespeichert sind
	 *
	 * @var String
	 */
	private $template_cssPath = '';
	
	/**
	 * Pfad zum Ordner in dem die Template-spezifischen Javascript-Dateien gespeichert werden
	 *
	 * @var String 
	 */
	private $template_jsPath = '';
	
	/**
	 * Name den das Template trägt
	 *
	 * @var String
	 */
	private $name = '';
	
	private $indexTemplate = '';

	/**
	 * Konstruktor
	 * 
	 * Über den Parameter $noload kann das sofortige initialisieren des standardtemplates
	 * verhindert werden. 
	 *
	 * @param Boolean $noload	true => Kein Template wird geladen
	 * 							false => Standardtemplate wird geladen
	 */
	public function __construct($noload = false)
	{
		if($noload)
		{
			return;
		}
		
		$id = $this -> GetTemplateToLoad();
		$this -> Load($id);
	}
	
	/**
	 * Gibt die ID des zu ladenden Templates zurück
	 *
	 * @return int
	 */
	private function GetTemplateToLoad()
	{
		//Abfragen ob der Besucher angemeldet ist
		$declared = Cms::GetInstance() -> GetUser() -> IsDeclared();
		if($declared)
		{
			//Wenn ja dann die Template-Auswahl des Users zurückgeben
			return Cms::GetInstance() -> GetUser() -> template;
		}
		
		//Ansonsten das vom Admin bestimmte Standard-Template abfragen
		$id = NULL;
		try
		{
			$id = SqlManager::GetInstance() -> SelectItem('sysstyle', 'id', 'standard=1');
		}
		catch(SqlManagerException $ex)
		{
			functions::output_fehler($ex);
			return;
		}	
		return $id;
	}

	/**
	 * Lädt die Template-Einstellungen aus der Datenbank
	 *
	 * @param int $id ID des zu ladenden Templates
	 */
	public function Load($id)
	{
		
		//Abfragen der Template-Daten
		$daten = array();
		try
		{	
			$daten = SqlManager::GetInstance() -> SelectRow('sysstyle', $id);
		}
		catch(SqlManagerException $ex)
		{
			Functions::output_fehler($ex);
		}

		//Speichern der Daten
		$this -> SetTemplatePath($daten['template_path']);
		$this -> SetTemplate_cPath($daten['template_cpath']);
		$this -> SetTemplate_cssPath($daten['template_csspath']);
		$this -> SetTemplate_jsPath($daten['template_jspath']);
		$this -> SetName($daten['name']);	
		$this -> SetIndexTemplate($daten['indexTemplate']);	

		
	}

	/**
	 * Gibt den Pfad zu dem Template-Dateien zurück
	 *
	 * @return String
	 */
	public function GetTemplatePath()
	{
		return $this -> templatePath;
	}

	/**
	 * Gibt den Pfad zum template_c-Ordner des Templates zurück
	 *
	 * @return String
	 */
	public function GetTemplate_cPath()
	{
		return $this -> template_cPath;
	}

	/**
	 * Gibt den Pfad zum CSS-Ordner des Templates zurück
	 *
	 * @return unknown
	 */
	public function GetTemplate_cssPath()
	{
		return $this -> template_cssPath();
	}

	/**
	 * Gibt den Pfad zum Javascript-Ordner des Templates zurück
	 *
	 * @return String
	 */
	public function GetTemplate_jsPath()
	{
		return $this -> template_jsPath();
	}
	
	/**
	 * Gibt den Template-Namen zurück
	 *
	 * @return String 
	 */
	public function GetName()
	{
		return $this -> name;
	}

	/**
	 * Setzt den Pfad zu den Template-Dateien
	 *
	 * @param String $value Neuer Pfad zu den Template-Dateien
	 */
	public function SetTemplatePath($value)
	{
		$this -> templatePath = $value;
	}

	/**
	 * Setzt den Pfad zum template_c-Ordner für dieses Template
	 *
	 * @param String $value Neuer template_c Ordner
	 */
	public function SetTemplate_cPath($value)
	{
		$this -> template_cPath = $value;
	}

	/**
	 * Setzt den Pfad zum CSS-Ordner des Templates
	 *
	 * @param String $value Neuer Pfad zum CSS-Ordner
	 */
	public function SetTemplate_cssPath($value)
	{
		$this -> template_cssPath = $value;
	}

	/**
	 * Setzt den Pfad zum Javascript-Ordner des Templates
	 *
	 * @param String $value Neuer Pfad zum Javascript-Ordner
	 */
	public function SetTemplate_jsPath($value)
	{
		$this -> template_jsPath = $value;
	}
	
	/**
	 * Setzt den Namen des Templates
	 *
	 * @param String $value
	 */
	public function SetName($value)
	{
		$this -> name = $value;
	}
	
	public function GetIndexTemplate()
	{
		return $this -> indexTemplate;
	}
	
	public function SetIndexTemplate($value)
	{
		$this -> indexTemplate = $value;
	}
	
	public function GetFullTemplatePath()
	{
		$templatePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/templateFolder');
		
		return $templatePath . $this -> GetTemplatePath();
	}
}