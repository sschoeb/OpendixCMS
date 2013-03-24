<?php
/**
 * Rechtverwaltung
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse zum verwalten von Berechtigungen
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 1.0     
 */
class Recht
{
	function __construct()
	{

		
		$this -> spalte = array();
		$this -> table 	= functions::cleaninput($_REQUEST['table']);				//mit dem parameter table wird mitgeliefert welche Tabelle bearbeitet werden soll
														//Referenz zu Smarty
		switch($_REQUEST['action'])												//auswahl der gewollten aktion
		{
			case save:															//Änderungen speichern
				$this -> rechtsave();
				$this -> showrecht();
				break;
			case add:															//Neuen Datensatz speichern (nachher wird wieder show() ausgeführt)
				$this -> add();
				$this -> showrecht();
				break;
			case del:															//Datensatz löschen
				$this -> del(functions::cleaninput($_REQUEST['id']));
				break;
			default:															//Sollte keine aktion angegeben sein (was beim ersten aufruf der fall ist) wird einfach die Tabelle angezeigt
				$this -> showrecht();
				break;
		}	


	}
	
	function showrecht()
	{
		$active_page = !empty($_GET['page']) ? $_GET['page'] : 0;		//Hier kommt die Blätterklasse ins Spiel
		
		$query 	= 'SELECT COUNT(id) FROM sysuserrecht';					//Abfragen wieviele Einträge die tabelle hat
		$result	= 	@mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 153 ');
		list($entries) = mysql_fetch_row($result);

		$blaettern=new Blaettern($active_page, $entries);				//Neue Instanz von blaettern erstellen
		$blaettern->set_Link_Href(SEITE . '&amp;page=');
		$blaettern->set_Entries_Per_Page(ANZ_SEITEN);					//Anzahl seiten pro seiten festlegen (Konstante wird hier im script gesetzt und steh tin der config.ini)
		
		$query = "SELECT sysrecht.seite, sysuserrecht.id, rechtId, benutzerId, gruppeId FROM sysuserrecht, sysrecht WHERE sysrecht.id = sysuserrecht.rechtId ORDER BY sysrecht.seite ASC LIMIT ".($blaettern->get_Epp() * $blaettern->get_Active_Page()).', '.$blaettern->get_Epp();
		$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 152 ');	

		$i = 0;
		if (mysql_num_rows($result)>0) 
		{
			while($daten = @mysql_fetch_assoc($insert))
			{
				$site_q = "SELECT seite FROM sysrecht WHERE id = ". $daten['rechtId'];
				$site_i	= @mysql_query($site_q) OR functions::output_fehler('MySQL-Error: Nr. 85 ');
				$seite = @mysql_result($site_i, 0);
			
				if(substr($seite, 0, 8) == "content_")		//Da Contents einen zufallsnamen haben im Template, wirdhier versucht den template-name durch den Namen des Contents zu ersetzen
				{
					$subquery 	= "SELECT syscontent.name FRSysContentent, sysmenue WHERE sysmenue.template='$seiteSysContentontent.id = sysmenue.contentId";
					$subinsert	= @mysql_query($subquery) OR functions::output_fehler('MySQL-Error: Nr. 154 ');	
					$name 	= @mysql_result($subinsert, 0);
					if($name != "")
					{
						$seite 	= "Content: " . $name;
					}
				}
			
				$show[$i]['i'] 			= $i;
				$show[$i]['id'] 		= $daten['id'];
				$show[$i]['r_select'] 	= $seite;
				$show[$i]['b_select'] 	= $this -> select($daten['benutzerId'], "b");
				$show[$i]['g_select'] 	= $this -> select($daten['gruppeId'], "g");
				if($daten['benutzerId'] == NULL || $daten['benutzerId'] == 0)
				{
					if($daten['benutzerId'] == "0")
					{
						$show[$i]['checked3'] = " checked";
					}
					else
					{
						$show[$i]['checked2'] = " checked";
					}
					
				}
				elseif ($daten['gruppeId'] != "")
				{
					$show[$i]['checked1'] = " checked";
				}
				$i++;
			}
			
		}
		else 
		{
			functions::output_fehler('Keine Datens&auml;tze gefunden! ');
		}
		
		$blaetter =    $blaettern->create();	//Erstellen der Blätter-Links 
		
		functions::output_var("blaetter", $blaetter);
		//functions::output_var("seite", $this -> seite_aktuell());
		functions::output_var("select_b", $this -> select('', "b"));
		functions::output_var("select_g", $this -> select('', "g"));
		functions::output_var("select_r", $this -> select('', "r"));
		functions::output_var("show", $show);
	}	
	
	function select($id = "", $case = "")
	{
		$select = '';
		switch($case)
		{
			case r:
				$query = "SELECT id, seite FROM sysrecht";
				$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 75 ');
				while($daten = @mysql_fetch_assoc($insert))
				{
					if($daten['id'] == $id)
					{
						$select = $select . "<option selected>". $daten['seite'] ."</option>";
					}
					else
					{
						$select = $select . "<option>". $daten['seite'] ."</option>";
					}
				}
				break;
			case b:
				$query = "SELECT id, nick FROM sysuser";
				$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 76 ');
				while($daten = @mysql_fetch_assoc($insert))
				{
					if($daten['id'] == $id)
					{
						$select = $select . "<option selected>". $daten['nick'] ."</option>";
					}
					else
					{
						$select = $select . "<option>". $daten['nick'] ."</option>";
					}
				}
				break;
			case g:
				$query = "SELECT id, name FROM sysrechtgruppe";
				$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 77 ');
				while($daten = @mysql_fetch_assoc($insert))
				{
					if($daten['id'] == $id)
					{
						$select = $select . "<option selected>". $daten['name'] ."</option>";
					}
					else
					{
						$select = $select . "<option>". $daten['name'] ."</option>";
					}
				}
				break;
		}
		return $select;
	}
	
	function add()
	{
		$benutzer 	= functions::cleaninput($_REQUEST['benutzer']);
		$gruppe 	= functions::cleaninput($_REQUEST['gruppe']);
		$recht 		= functions::cleaninput($_REQUEST['recht']);
		
		$query 		= "SELECT b.id, g.id, r.id FROM sysuser b, sysrechtgruppe g, sysrecht r WHERE b.nick = '$benutzer' AND g.name = '$gruppe' AND r.seite = '$recht'";
		$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 78 ');
		$daten 		= @mysql_fetch_array($insert);
		
		if($_REQUEST['vs'] == "bId")
		{
			$daten[1] = "NULL";
		}
		else
		{
			$daten[0] = "NULL";
		}
		
		$query 		= "INSERT INTO sysuserrecht VALUES('', $daten[0],$daten[2],$daten[1])";
		$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 79 ');
		
		$this -> showrecht();
	}
	
	function del()
	{
		$id = functions::cleaninput($_REQUEST['id']);
		functions::switchDatensatz($id, "sysuserrecht" , 1);
		//$query 	= "DELETE FROM sysuserrecht WHERE id = '$id' LIMIT 1";
		//$insert = @mysql_query($query) OR functions::output_var('MySQL-Error: Nr. 80 ');
		
		$this -> showrecht();
	}
	
	function rechtsave()
	{
		$i = 0;
		$query = "SELECT * FROM sysuserrecht";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 81 ');
		while($daten = @mysql_fetch_assoc($insert))
		{
			$benutzer 	= functions::cleaninput($_REQUEST['benutzer_' . $i]);
			$gruppe 	= functions::cleaninput($_REQUEST['gruppen_' . $i]);
			$recht 		= functions::cleaninput($_REQUEST['recht_' . $i]);			
			$vs			= functions::cleaninput($_REQUEST['radio_' . $i]); 
			
			if($benutzer != "" && $gruppe != "" && $recht != "" && $vs != "")
			{
			
				$in_query	= "SELECT b.id, g.id, r.id FROM sysuser b, sysrechtgruppe g, sysrecht r WHERE b.nick = '$benutzer' AND g.name = '$gruppe' AND r.seite = '$recht'";
				$in_insert 	= @mysql_query($in_query) OR functions::output_fehler('MySQL-Error: Nr. 82 ');
				$mitte 		= @mysql_fetch_array($in_insert);			
				
				$benutzer 	= $mitte[0];
				$gruppe 	= $mitte[1];
				$recht		= $mitte[2];
				
				switch ($vs)
				{
					case "uId":
						$daten['gruppeId'] = "NULL";
						if($daten['benutzerId'] != $benutzer)
						{
							$daten['benutzerId'] = $benutzer;
						}
						break;
					case "all":
						$daten['benutzerId'] = 0;
						$daten['gruppeId'] = "NULL";
						break;
					case "gId":
						$daten['benutzerId'] = "NULL";
						if($daten['gruppeId'] != $gruppe)
						{
							$daten['gruppeId'] = $gruppe;
						}
						break;
				}		
				
				if($daten['rechtId'] != $recht)
				{
					$daten['rechtId'] = $recht;
				}
				$up_query = "UPDATE sysuserrecht SET benutzerId = ". $daten['benutzerId'] ." , rechtId = ". $daten['rechtId'] ." , gruppeId = ". $daten['gruppeId'] . " WHERE id = ". functions::cleaninput($_REQUEST['id_' . $i]);
				$up_insert = @mysql_query($up_query) OR functions::output_fehler('MySQL-Error: Nr. 83 ');		
			}
			$i++;
		}
		
		
	}
}
?>