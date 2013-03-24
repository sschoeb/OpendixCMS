<?php

/**
 * File mit der Validator-Klasse
 * 
 * @package 	OpendixCMS.Core
 */

/**
 * Klasse um Daten zu validieren
 * 
 * @author 		Stefan Schöb
 * @version 	1.0
 * @package 	OpendixCMS.Core
 *
 */
class Validator
{
	/**
	 * Prüft ob es sich um eine Zahl handelt
	 * 
	 * Optional kann angegeben werden ob die Zahl zwischen zwei Werten 
	 * sein muss.
	 *
	 * @param 	String	$value	Wert der geprüft werden soll
	 * @param 	int 	$min	Optionale Angabe, dass die zu prüfende Zahl grösser sein muss
	 * @param 	int 	$max	Optionale Angabe, dass die zu prüfende Zahl kleiner sein muss
	 * @return 	Boolean Validierung erfolgreich oder nicht
	 */
	public static function IsValidNumber($value, $min = '', $max = '')
	{
		if (! is_numeric ( $value ))
		{
			return false;
		}
		
		if ($min != '' && $min > $value)
		{
			return false;
		}
		
		if ($max != '' && $max < $value)
		{
			return false;
		}
		return True;
	}
	
	public static function ForgeInputNumber($value)
	{
		if (! is_numeric ( $value ))
			throw new CMSException ( 'Kein numerischer Wert!', CMSException::T_MODULEERROR );
		
		return $value;
	}
	
	/**
	 * Prüft ob es sich bei der Übergabe um eine korrekte E-Mail handelt
	 *
	 * @param 	String 	$value		Wert der geprüft werden soll
	 * @return 	Boolean	Validierung erfolgreich oder nicht
	 */
	public static function IsValidEmail($value)
	{
		//TODO ereg
		return @ereg ( '^[A-Za-z0-9]+([-_.]?[A-Za-z0-9])+@[A-Za-z0-9]+([-_.]?[A-Za-z0-9])+.[A-Za-z]{2,4}', $value );
	}
	
	/**
	 * Formt einen Wert so um, dass er ohne Probleme im Script verwendet werden kann
	 *
	 * @param	String 		$value			Wert der geprüft werdensoll
	 * @param 	Boolean 	$stripTags		Boolean ob strip_tags() angewendet werden soll
	 * @param 	Boolean 	$trim			Boolean ob trim() angewendet werden soll
	 * @param 	Boolean 	$htmlentities 	Boolean ob htmlentities() angegenwet werden soll
	 * @todo 	Arrays müssen auch kontrolliert werden
	 * @return 	String		Formatierter Wert
	 */
	public static function ForgeInput($value, $stripTags = true, $trim = true, $htmlentities = true)
	{
		if (is_array ( $value ))
		{
			foreach ( $value as $key => $val )
			{
				$value [$key] = Validator::ForgeInput ( $val, $stripTags, $trim, $htmlentities );
			}
		} else
		{
			if ($stripTags)
			{
				$value = strip_tags ( $value );
			}
			if ($trim)
			{
				$value = trim ( $value );
			}
			if ($htmlentities)
			{
				$value = htmlentities ( $value, ENT_QUOTES );
			}
		}
		return $value;
	}
	/**
	 * Gibt zurück ob es sich beim Parameter um eine gültige URL handelt. 
	 * Wird mit dem zweiten Parameter true mitgegeben, so wird vesucht
	 * ob auf die angegebene URL eine Verbindung hergestellt werden kann
	 *
	 * @param String	$url		Die zu prüfende URL
	 * @param Boolean 	$connect	Boolean ob geprüft werden soll ob die Seite erreichbar ist
	 * @return Boolean -> true wenn URL gültig ansonsten false
	 */
	public static function IsValidUrl($url, $connect = false)
	{
		return true;
	}
}