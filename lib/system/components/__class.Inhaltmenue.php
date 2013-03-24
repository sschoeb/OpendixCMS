<?php

/**
 * Klasse für Unterpunkte
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Zeigt alle unterpunkte im Menü an
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 1.0     
 */
 
class Inhaltmenue
{
	function __construct()
	{
		$sub = functions::cleaninput($_REQUEST['sub']);
		
		
		if($sub == '')
		{
			$query = "SELECT id FROM sysmenue WHERE standard = '1'";
			$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 347');
			$sub = mysql_result($insert, 0);
		}
		
		$i=0;
		$query = "SELECT id, eintrag, template, href FROM sysmenue WHERE vater = '$sub'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 348');
		while($daten = mysql_fetch_assoc($insert))
		{
			//Überprüfen ob man diese Unterkategorie sehen darf!
			//Sollte es sich bei der Unterkategorie um eine Auflistung der Unterpunkte handeln muss 
			//dies mit der checkinhaltmenue-funktion getestet werden 
			if($daten['template'] == 'open_menueinhalt.tpl')
			{
				if(!RechtCheck::checkinhaltMenue($daten['id']))
				{
					continue;
				}			
			}
			//ansonsten normal mit der check-funktion!
			if(!RechtCheck::check($daten['template']))
			{
				continue;
			}
			
			//Sollte berechtigung da sein die einzelnen Daten für die Ausgabe im Array speichern!
		 	//$menue[$i]['id'] = $daten['id'];
		 	$menue[$i]['name'] = $daten['eintrag'];
		 	//$menue[$i]['param'] = $daten['href'];
		 	$menue[$i]['link'] = functions::GetLink(array_merge(array('sub' => $daten['id']), functions::SplitHref($daten['href'])));
		 	$i++;
		}
		functions::output_var('subs', $menue);
	}
}
?>