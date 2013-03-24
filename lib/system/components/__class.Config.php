<?php
/**
 * Konfiguration
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/** 
 * Klasse mit der man eine beliebige Konfiguration anzeigen/speichern kann
 *
 * 
 * @access   	public
 * @package 	OpendixCMS
 * @version  	1.0
 */
class Config
{
	/**
	 * Zeigt die übergebenen INI-Einträge an
	 *
	 * @param 	array $item		schlüssel ist der name in der INI-datei
	 * 							wert ist der typ wie es ausgegeben wird, sollte
	 * 	 						es ein array sein, so handelt es sich um einie select-box 							
	 * @return  mixed 			false wenn fehlgeschlagen sont nichts
	 */
	function show($item)
	{
		//Erst prüfen ob auch ein array übergeben wurde
		if(!is_array($item))
		{
			return false;
		}
		
		foreach ($item as $key => $value)
		{
			switch($value)
			{
				//Bei select-Boxen können die werte als array da mitgegeben werden
				case is_array($value):
					$selectbox = '';
					foreach($value as $select_key => $select_value)
					{
						if($select_value == functions::getINIparam($key))
						{
							$selectbox .= "<option selected=\"selected\">$select_value</option>";
							continue;
						}
						$selectbox .= "<option>$select_value</option>";
					}
					functions::output_var($key, $selectbox);
					break;
					
				case 'checkbox':
					if(functions::getINIparam($key) == '1')
					{
						functions::output_var($key, ' selected=\"selected\" ');
					}
					break;
				
				default:
					if(functions::getINIparam($key))
					{	
						functions::output_var($key, functions::getINIParam($key));
					}
					break;
			}
		}
	}
	
	/**
	 * Speichert eine Konfiguration
	 *
	 * @param 	array $item		schlüssel ist der name in der INI-datei
	 * 							wert ist der typ wie es ausgegeben wird, sollte
	 * 	 						es ein array sein, so handelt es sich um einie select-box 
	 * @return 	boolean			true 	erfolgreich
	 * 							false	fehlgeschalgen
	 */
	function save($item)
	{
		if(!is_array($item))
		{
			return false;
		}	
		$ini = parse_ini_file(INIFILE);
		
		foreach($item as $key => $value)
		{	
			//Value kann ein array sein, so müssen noch verschiedene sachen der eingabe geprüft werden.
			if(is_array($value))
			{
				switch ($value['filter'])
				{
					//Muss numerisch sein
					case NUM:
						if(!is_numeric($_REQUEST[$key]))
						{
							functions::output_warnung($key . " konnte nicht gespeichert werden da ihre Eingabe keine Zahl ist");
							continue;
						}
						break;
					//Eine gewisse länge darf nicht überschritten werden
					case MAXLEN:
						if($value['maxlen'] != '')
						{
							if(strlen($_REQUEST[$key]) > $value['maxlen'])
							{
								functions::output_warnung($key . " konnte nicht gespeichert werden da ihre Eingabe zu lang ist!");
								continue;
							}
						}
						break;
					case MINLEN:
						if($value['maxlen'] != '')
						{
							if(strlen($_REQUEST[$key]) < $value['maxlen'])
							{
								functions::output_warnung($key . " konnte nicht gespeichert werden da ihre Eingabe zu kurz ist!");
								continue;
							}
						}					
						break;
					case BETWEEN:
						if($value['min'] != '' && $value['max'] != '')
						{
							if(strlen($_REQUEST[$key]) < $value['min'] && strlen($_REQUEST[$key]) > $value['max'])
							{
								functions::output_warnung($key . ' konnte nicht gespeichert werden da ihre Eingabe nicht zwischen ' . $value['min'] . ' und ' . $value['max'] . ' liegt!');
								continue;
							}
						}
						break;
					//Es muss eine IP sein
					case IP:
						if(!ereg("^([01]?[0-9][0-9]?|2[0-4][0-9]|25[0-5])\.([01]?[0-9][0-9]?|2[0-4][0-9]|25[0-5])\.([01]?[0-9][0-9]?|2[0-4][0-9]|25[0-5])\.([01]?[0-9][0-9]?|2[0-4][0-9]|25[0-5])$", $_REQUEST[$key]))
						{
							functions::output_warnung($key . ' konnte nicht gespeichert werden da ihre Eingabe keine IP ist.');
							continue;
						}
						break;
				}
			}
			$ini[$key] = functions::cleaninput($_REQUEST[$key]);	
		}
		
		if(!functions::write_ini_file($ini))
		{
			functions::output_warnung('Konfiguration konnte nicht gespeichert werden!');
			return false;
		}
		functions::output_bericht('Konfiguration erfolgreich gespeichert!');
		return true;
	}
}

?>