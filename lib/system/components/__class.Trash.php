<?php

/**
 * Papierkorb
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Sch�b <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.0
 */

/**
 * Klasse zum verwalten des Papierkorbs
 * 
 * @author Stefan Sch�b
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Sch�b
 * @since 1.0     
 */
class OTrash
{
	function __construct()
	{
		if(isset($_REQUEST['id']) && $_REQUEST['action'] == "del")		//Ein einziger Dattensatz soll gel�scht werden
		{
			$this -> delitem(functions::cleaninput($_REQUEST['id']));
		}
		if($_REQUEST['action'] == "trun")								//Papierkorb soll geleert werden
		{
			$this -> cleanTrash();
			$this -> showTrash();
		}
		else
		{
			if($_REQUEST['action'] == "restore" && $_REQUEST['id'] != "")	//Datensatz soll wiederhergestelt werden
			{
				$this -> restore(functions::cleaninput($_REQUEST['id']));
			}
			$this -> showTrash();		//Anzeigen von Trash-Inhalt
		}

	}

	/**
	 * Funktion zum anzeigen des Inhalts des Papierkorbes
	 * 
	 * @todo Anzeige des ganzen Eintrags in einem Popup
	 *
	 */
	function showTrash()
	{


		$i = 0;			//Abfragen aller Daten im Papierkorb
		$query = "SELECT id, tableId, zeit, value FROM syspapierkorb ORDER BY tableId ASC,  zeit DESC";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 114 ');

		while($daten = mysql_fetch_assoc($insert))
		{	//Abfragen des Tabellennamens aus der ID
			//echo $t_query = "SELECT name FROM SysTabelle WHERE id = '" . $daten['tableId'] . "' ";
			//$t_insert = @mysql_query($t_query) OR functions::output_fehler('MySQL-Error: Nr. 115 ');
			//Zuweisung f�r Smarty
			//$trash[$i]['tabelle']	= @mysql_result($t_insert, 0);
			$trash[$i]['tabelle']	= $this -> GetTableTyp($daten['tableId']);
			$trash[$i]['zeit']		= date("d.m.Y - H:i:s" ,$daten['zeit']);
			$trash[$i]['muell'] 	= substr(join(" ", explode("~", $daten['value'])), 0, 25) . '...';		//Values sidn mit _ getrennt, diese durch " " ersetzen
			$trash[$i]['id'] 		= $daten['id'];		//Id �bergeben falls man Operationen mit Datens�tzen im Trahs machen m�chte
			$trash[$i]['link']['restore'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'restore', 'id' => $daten['id']));
			$trash[$i]['link']['del'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'del', 'id' => $daten['id']));
			$i++;
		}
		functions::output_var("trash", $trash);
		
		functions::Output_var('trashTrunLink', functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'trun')));
		//functions::output_var("seite", $this -> seite_aktuell("&action"));

	}

	/**
	 * Gibt den Typ zur�ck der f�r eine Tabelle Steht
	 *
	 */
	private function GetTableTyp($table)
	{
		$typ = array('modnews' => 'News', 'modnewslettergruppe' => 'Newsletter-Gruppe', 'modgbook' => 'Gaestebuch', 'newsletter'=>'Newsletter-EMail');
		if(array_key_exists($table, $typ))
		{
			return $typ[$table];
		}
		else {
			return "Unbekannt";
		}
	}

	//
	// Funktion mit der man den ganzen Papierkorb auf einmal leeren kann
	//
	function cleanTrash()
	{
		if($_REQUEST['ok'] == 1)	//Erst abfragen ob die Best�tigung schon gesetzt wurde!
		{
			$query = "TRUNCATE TABLE syspapierkorb ";		//Dann Tabelle leeren
			$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 113 ');
			if($insert == true)
			{
				functions::output_bericht('Der papierkorb wurde erfolgreich geleert!');
			}
		}
		else
		{	//Ansonsten Parameter �bergeben das Er Nachfragen soll
			//functions::output_var("seite", $this -> seite_aktuell("&action"));
			$trashRequest['ja'] = functions::GetLink(array('sub' => $_GET['sub'], 'ok' => 1, 'action' => 'trun'));
			$trashRequest['nein'] = functions::GetLink(array('sub' => $_GET['sub']));
			Functions::Output_var('trashRequest', $trashRequest);
			functions::output_var("giveOk", 1);
		}
	}

	//
	// Funktion zum entfernen eines einzigen Datensatzes aus dem papierkorb
	//
	function delitem($id, $meldung = "Der Datensatz wurde erfolgreich gel�scht!")
	{
		$query = "DELETE FROM syspapierkorb WHERE id = '$id' LIMIT 1";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 116 ');
		functions::output_bericht($meldung);
	}

	//
	// Funktion zum wiederherstellen von Datens�tzen.
	//
	function restore($id)
	{
		if($id == '')
		{
			functions::Output_warnung('Keine ID angegeben');
			return false;
		}
		$query = "SELECT tableId, value FROM syspapierkorb WHERE id = '$id'";		//Abfragen des Datensatzes der zur�ck geholt werden soll
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 117 ');
		$daten = @mysql_fetch_assoc($insert);
		$muell = explode("~", $daten['value']);						//Die enthaltenen Daten auf den INSERT vorbereiten, um platz zu sparen wurden die Daten des ehem. Datensatzes mit _ voneinander getrennt in eine spalte geschriben

		for($i = 0; $i< count($muell); $i++)						//Zusammenf�gen der VALUE anweisung im INSERT
		{
			if($values == "")
			{
				$values = "'" . $muell[$i] . "'";					//Das erste mal kommt noch kein Komma vor den Value
			}
			else
			{
				$values = $values .  ",'" . $muell[$i] . "'";
			}
		}

		$name = $daten['tableId'];

		$query = "INSERT INTO $name VALUES($values)";			//Einf�gen des Datensatzes
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 119 ');
		if($insert == true)										//Sollte das gelungen sein wird der Datensatz aus dem Papierkorb entfernt!
		{
			$this -> delitem($id, "Erfolgreich wiederhergestellt!");
		}


	}
}


?>
