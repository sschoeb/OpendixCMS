<?php

/**
 * CMSException
 *
 * @package    	OpendixCMS.Core
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	1.0
 */

/**
 * Repräsentiert die Exception die vom CMS in einem Fehlerfall geworfen wird
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		2.0
 */
class CMSException extends Exception 
{
	/**
	 * Konstante die den Wert eines System-Fehlers hinterlegt
	 * 
	 * Wird eine CMSException mit dem Typ SYSTEMERROR geworfen dann wird
	 * nur eine Fehlermeldung angezeigt und kein Template angezeigt
	 *
	 */
	const T_SYSTEMERROR = 1;
	
	/**
	 * Konstante die den Wert eines Modul-Fehlers hinerlegt
	 * 
	 * Wird eine CMSException mit dem Typ MODULERROR geworfen, dann wird
	 * das indexTemplate ausgegeben, dieses beinhaltet die Fehleranzeige
	 * von Fehlern die nicht durch das System sondern durch ein Modul auftreten.
	 *
	 */
	const T_MODULEERROR = 2;
	
	/**
	 * Konstante die den Wert einer Warnung hinterlegt
	 * 
	 * Wird eine CMSException mit dem Typ WARNING geworfen, dann wird 
	 * das indexTemplate wie auch das Modul-Template ausgegeben. 
	 * Das indexTemplate hat hinterlegt, wie Warnungen im entsprechenden
	 * Template angezeigt werden.
	 *
	 */
	const T_WARNING = 3;
	
	/**
	 * Speichert eine evtl vorhandene innere Exception
	 *
	 * @var Exception
	 */
	private $innerException = NULL;
	
	/**
	 * Typ der Exception
	 *
	 * 1 = Systemfehler
	 * 2 = Modulfehler
	 * 3 = Warnung
	 * 
	 * @var int
	 */
	private $type = NULL;
	
	/**
	 * Konstruktor
	 * 
	 *
	 * @param String 	$message			Nachricht der Exception
	 * @param int 		$type				Typ der Exception (1 = System, 2 = Modul, 3 = Warnung) => Siehe Konstanten
	 * @param Exception $innerException		Innere Exception , z.B. eine SQLManagerException
	 */
	public function __construct($message, $type, $innerException = NULL)
	{
		parent::__construct($message);
		$this -> type = $type;
		$this -> innerException = $innerException;
	}
	
	/**
	 * Gibt den Typ dieser Exception zurück
	 *
	 * @return int
	 */
	public function GetType()
	{
		return $this -> type;
	}
	
	public function GetInnerException()
	{
		return $this -> innerException;
	}
}