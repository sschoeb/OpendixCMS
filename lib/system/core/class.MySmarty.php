<?php
/**
 * Smarty
 *
 * @package    	OpendixCMS.Core
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	2.0
 */

/**
 * Diese Klasse Überschreibt die Variabeln der Ordner für Smarty
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS.Core
 * @version 	2.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		2.0
 */
class MySmarty extends Smarty
{
	/**
	 * Statische Variabel fuer den Singleton
	 *
	 * @var MySmarty
	 */
	private static $myInstance;
	
	/**
	 * Instanz der Template-Klasse
	 *
	 * @var Template
	 */
	private $template = NULL;
	
	private $outputData = array('cms' => array(), 'module' => array(), 'warning' => array(), 'error' => array(), 'confirmation' => array());
	
	/**
	* Gibt die Instanz eines MySmarty zurueck
	* 
	* @access public
	* @return MySmarty
	*/
	public static function GetInstance()
	{
		if(!self::$myInstance instanceof MySmarty)
		{
			self::$myInstance = new MySmarty();
		}
		return self::$myInstance;
	}

	
	
	/**
	 * Ersetzt eine Variabel im Template durch einen entsprechenden Wert
	 *
	 * @param String 	$var	Name der Variabel die ersetzt werden soll
	 * @param mixed 	$by		Wert durch den die Variabel ersetzt wird
	 */
	public function OutputSystemVar($var, $by)
	{
		$this -> outputData['cms'][$var] = $by;
	}

	/**
	 * Ersetzt eine Variabel im Template durch einen entsprechenden Wert
	 *
	 * @param String 	$var	Name der Variabel die ersetzt werden soll
	 * @param mixed 	$by		Wert durch den die Variabel ersetzt wird
	 */
	public function OutputModuleVar($var, $by)
	{
		$this -> outputData['module'][$var] = $by;
	}
	
	
	/**
	 * Gibt eine Warnung im Template aus
	 *
	 * @param String 	$message	Nachricht die als Warnung angezeigt wird
	 * @exception  CMSException wenn $message empty ist
	 */
	public function OutputWarning($message)
	{
		if(empty($message))
		{
			throw new CMSException('Warnung enth&auml;lt keine Nachricht!', CMSException::T_WARNING );
		}
		$this -> outputData['warning'][] = $message;
	}
	
	/**
	 * Gibt eine Fehlermeldung im Template aus
	 *
	 * @param String 	$message	Nachricht die als Fehler angezeigt wird
	 * @exception  CMSException wenn $message empty ist
	 */
	public function OutputError($message)
	{
		if(empty($message))
		{
			throw new CMSException('Error enth&auml;lt keine Nachricht!', CMSException::T_WARNING);
		}	
		$this -> outputData['error'][] = $message;
	}
	
	/**
	 * Gibt eine Bestätigung/positiver Bericht im Template aus
	 *
	 * @param String 	$message 	Nachricht die als Bestätigung angezeigt wird
	 * @exception  CMSException wenn $message empty ist
	 */
	public function OutputConfirmation($message)
	{
		if(empty($message))
		{
			throw new CMSException('Confirmation enth&auml;lt keine Nachricht!', CMSException::T_WARNING);
		}
		$this -> outputData['confirmation'][] = $message;
	}
	
	/**
	 * Setzt das anzuzeigende Template
	 *
	 * @param Template 	$template 	Instanz der Template-Klasse die gesetzt werden soll
	 * @exception 	CMSException 	Wenn es sich beim übergebenen Parameter nicht um eine Instanz
	 * 								der Template-Klasse handelt
	 */
	public function SetTemplate($template)
	{
		if(!$template instanceof Template)
		{
			throw new CMSException('Smarty wurde keine Template-Instanz &uuml;bergeben!', CMSException::T_SYSTEMERROR );
		}
		
		$this -> template = $template;
		$templatePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/templateFolder');
		
		$this -> template_dir  	= $templatePath . $template -> GetTemplatePath();
		$this -> compile_dir	= $templatePath . $template -> GetTemplate_cPath();
		$this -> config_dir		= 'config/';
	  	$this -> config_dir 	= 'config/';
	  	$this -> cache_dir 		= 'smarty/cache/';
	  	$this -> caching 		= false;
	}
	
	private function Output()
	{
		$this -> assign('cms', $this -> outputData['cms']);
		$this -> assign('module', $this -> outputData['module']);
		$this -> assign('warning', $this -> outputData['warning']);
		$this -> assign('error', $this -> outputData['error']);
		$this -> assign('confirmation', $this -> outputData['confirmation']);
	}
	
	/**
	 * Gibt das Template aus
	 * 
	 * Bevor diese Funktion aufgerufen wird, muss über die Funktion SetTemplate
	 * ein Template gesetzt werden sonst wird eine CMSException geworfen
	 * Für die Smarty-Funktion wird dabei die Eigenschaft indexTemplate welche
	 * per Template-Instanz übergeben wurde verwendet! 
	 * 
	 * @exception CMSException wenn kein Template gesetzt ist
	 *
	 */
	public function Display()
	{
		if(is_null($this -> template))
		{
			throw new CMSException('Es wurde kein Template ausgew&auml;hlt!', CMSException::T_SYSTEMERROR);
		}
		
		//Sämtliche Zwischengespeicherten Daten ausgeben
		$this -> Output();
		
		

		//Abfragen des gesamten Pfads um zu überprüfen ob das Template existiert
		$path = $this -> template_dir . $this -> template -> GetIndexTemplate();
		
		//Prüfen ob das Template vorhanden ist
		if(!file_exists($path))
		{
			throw new CMSException('Das Index-Template ist nicht vorhanden!', CMSException::T_SYSTEMERROR );
		}
		
		
		//Anzeigen des Index-Templates
		//Smarty geht hier vom Template-Ordner aus, darum nur den Namen
		//des Index-templates angeben
		parent::display($this -> template -> GetIndexTemplate());
		
	}
	
}
