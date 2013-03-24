<?php
/**
 * Verinfachert das Verarbeiten von Formularen
 * 
 *
 * @package    	OpendixCMS.Core
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	1.0
 */

/**
 * Verinfachert das Verarbeiten von Formularen
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		2.0     
 */
class FormParser
{
	/**
	 * Verarbeitet eine Datums-Eingabe welche über das Smarty Date-Plugin
	 * erstellt wurde.
	 *
	 * @param array $array
	 */
	public static function ProcessDateInput($array)
	{

		if(!checkdate($array['Month'], $array['Day'], $array['Year']))
		{
			$array['Month'] = date('m');
			$array['Day'] = date('d');
			$array['Year'] = date('Y');
			MySmarty::GetInstance() -> OutputWarning('Ung&uuml;ltiges Datum angegeben!');
		}
		
		$mysqlDate = $array['Year'] . '-' . $array['Month'] . '-' . $array['Day'];
		
		if(isset($array['Hour']) && $array['Minute'])
		{
			$mysqlDate .= ' ' . $array['Hour'] . ':' . $array['Minute'];
			if(isset($array['Second']))
			{
				$mysqlDate .= ':' . $array['Second'];
			}
		}
		return $mysqlDate;
	}
	
	public static function ParseCheckBox($active)
	{
		if($active == 'on')
			return 1;
		return 0;
	}
}