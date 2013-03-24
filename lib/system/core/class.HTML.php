<?php

/**
 * Vereinfachert die Ausgabe von Html-Sachen 
 * 
 *
 * @package    	OpendixCMS.Core
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	1.0
 */

/**
 * Klasse welche die Ausgabe von HTML-Elementen (wie CheckBoxen, Selects, ..) vereinfachert
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		2.0     
 */
class HTML
{
	/**
	 * Gibt checked="checked" zurück falls der Parameter 1 ist
	 *
	 * @param int $checked	1 oder 0
	 * @return String
	 */
	public static function Checkbox($checked)
	{
		if($checked == 1)
		{
			return ' checked="checked" ';
		}
		return '';
	}
	
	/**
	 * Gibt ein Array zurück welches zu einem Select gewandelt werden kan
	 *
	 * @param 	String 	$externalTable	Tabelle die abgefragt werden soll
	 * @param 	String 	$externalCol	Spalte in der Fremden Tabelle
	 * @param 	int 	$selected		ID des selektierten Eintrages
	 * @param 	String 	$order			Ordnung nach dem die Daten abgefragt werden
	 * @return 	Array
	 */
	public static function Select($externalTable, $externalCol, $selected = 0, $order = '', $where='')
	{
		$daten = array();

		$cols = array('id');
		if(is_array($externalCol))
		{
			$cols = array_merge($cols, $externalCol);
		}
		else 
		{
			$cols[] = $externalCol;
		}
		try 
		{
			$daten = SqlManager::GetInstance() -> Select($externalTable, $cols, $where, $order);
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Erstellen der Select-Daten fehlgeschlagen!', CMSException::T_WARNING, $ex);
		}
		$r = array();
		$c = count($daten);
		
		if($c == 0)
		{
			return '';
		}
		
		for($i=0;$i<$c; $i++)
		{
			$ri = array('value' =>null, 'name' => null);
			
			$ri['value'] = $daten[$i]['id'];
			if(is_array($externalCol))
			{
				foreach ($externalCol as $key => $value)
				{
					$ri['name'] .= ' ' . $daten[$i][$value];
				}
			}
			else 
			{
				$ri['name'] = $daten[$i][$externalCol];
			}
			
			if($ri['value'] == $selected)
			{
				$ri['selected'] = true;
			}
			$r[] = $ri;
		}
		return $r;
	}
}