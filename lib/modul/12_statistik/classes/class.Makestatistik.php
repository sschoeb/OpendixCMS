<?php
/**
 * Statistik
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse die Statistik führt
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 1.0     
 */
class Makestatistik
{
	function makestatistik()
	{
		$ini = 	functions::parseini();
		$online = time() - $ini['statistik_online'];
		$ip 		= $_SERVER['REMOTE_ADDR'];

		$query = "SELECT id, ip FROM modstatistik WHERE time > '$online'";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 235 ');
		while($daten = @mysql_fetch_assoc($insert))
		{
			if($daten['ip'] == $ip)
			{
				//Da der Besucher noch als Online gitl wird sein letzts erscheinen geupdatet, ansonten wird er nach einer gewissen zeit nochmal gezählt!
				$subquery = "UPDATE modstatistik SET time = ". time() ." WHERE id = ". $daten['id'] . " LIMIT 1";
				$subinsert = mysql_query($subquery) OR functions::output_fehler('MySQL-Error: Nr. 236 ');
				return;		//Statistik-Funktion verlassen wenn die IP schon enthalten ist!
			}
		}

		//Daten vom Benutzer holen
		$time 		= time();

		$herkunft 	= gethostbyaddr($ip);
		$agent 		= $_SERVER['HTTP_USER_AGENT'];
		$referer 	= strip_tags($_SERVER['referer']);
		$aufloesung	= strip_tags($_SERVER['resw']) .' x '.strip_tags($_SERVER['resh']);
		$farbe 		= strip_tags($_SERVER['color']);

		//In die Datenbank einfügen
		$query = "INSERT INTO `modstatistik` ( `id` , `time` , `ip` , `agent`  , `herkunft` , `referer` , `aufloesung` , `farbtiefe` ) VALUES ('', '$time', '$ip', '$agent', '$herkunft', '$referer', '$ausfloesung', '$farbe')";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 233 ');
		if(!insert)
		{
			functions::output_warnung('Statistik konnte nicht nachgeführt werden!');
		}
	}
}

?>