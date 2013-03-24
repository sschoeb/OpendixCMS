<?php

/**
 * Klasse welche nur Termine eines speziellen Typs anzeigt.
 *
 * erbt von agenda und �berschreibt den
 * Konstruktor damit man nicht auf funktionen zugreiffen kann die nur der admin gebrauchen kann
 *
 *
 * @access   public
 * @package OpendixCMS
 * @version  1.0
 */
class Rangliste extends Agenda
{
	/**
	 * PHP 5 Konstruktor
	 *
	 */
	function __Construct()
	{

		$this -> update();
		if($_GET['action'] == 'getanhang')
		{
			$this -> getanhang();
		}
		$this -> setquery();
		//Den Filter setzen
		$this -> filter = " WHERE agendaId = '". functions::getINIparam('agenda_rangliste_gruppe') ."' AND aktiv='1' ";

		//Und dann alle anzeigen
		$this -> showall();

	}

	/**
	 * Setzt das Query
	 *
	 */
	function SetQuery()
	{
		$this -> query = "SELECT id, titel, status, beginn, berichtId, bericht_anzeigen FROM modagendatermin ";
	}

	function Showall()
	{
		$query = $this -> query . $this -> filter;
		$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 367.2 ');
		if(@mysql_num_rows($insert)==0)
		{
			functions::output_fehler('Es sind keine Termine eingetragen die eine Rangliste beinhalten k&ouml;nnten!');
			return false;
		}

		while($daten = @mysql_fetch_assoc($insert))
		{
		 	$rangliste[$i]['titel'] = $daten['titel'];
		 	$rangliste[$i]['status'] = $daten['status'];
		 	$rangliste[$i]['beginn'] = date("d.m.Y",$daten['beginn']);
		 	$rangliste[$i]['berichtId'] = $daten['berichtId'];
		 	$rangliste[$i]['bericht_anzeigen'] = $daten['bericht_anzeigen'];
		 	$subquery = "SELECT id, pfad FROM modagendaanhang WHERE terminId = '{$daten['id']}'";
		 	$subinsert 	= @mysql_query($subquery) OR functions::output_fehler('MySQL-Error: Nr. 367.3 ');
		 	$j=0;
		 	while($subdaten = @mysql_fetch_assoc($subinsert))
		 	{
		 		$rangliste[$i]['rangliste'][$j]['id'] = $subdaten['id'];
		 		$rangliste[$i]['rangliste'][$j]['pfad'] = basename($subdaten['pfad']);
		 		$j++;
		 	}
		 	$i++;
		}
		functions::output_var('rang', $rangliste);
	}

}
