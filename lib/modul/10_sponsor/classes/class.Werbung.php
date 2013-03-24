<?php
/**
 * Sponsorenverwaltung
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Admin Bereich für den Sponsoren Teil
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 1.0     
 */
class Werbung extends Sponsor
{
	/**
	 * Funktion zum hinzufügen eines Sponsors
	 *
	 */
	function Add()
	{
		$name 			= functions::cleaninput($_POST['name']);
		$url 			= functions::cleaninput($_POST['url']);
		$bild 			= functions::cleaninput($_POST['bild']);
		$beschreibung 	= functions::cleaninput($_POST['beschreibung']);
		$gId 			= functions::cleaninput($_POST['gId']);
		
		if($name == "" || $gId == '')
		{
			functions::output_warnung('Sie müssen einen Namen angeben für diesen Sponsor');
			return false;
		}
		$query = "SELECT MAX(folge) FROM modsponsor WHERE gId = '$gId'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 32ll9 ');
		$max = mysql_result($insert, 0) +1;
		
		$query = "INSERT INTO modsponsor(gId, name, url, bild, beschreibung, anzuzeigen, angezeigt, folge, werbung) VALUES('$gId', '$name','$url','$bild','$beschreibung','0','0', '$max', '1')";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 329 ');
		if(!$insert)
		{
			functions::output_fehler('Sponsor konnte nicht hinzugefügt werden!');
		}
		functions::output_bericht('Sponsor erfoglreich hinzugefügt');
	}
}