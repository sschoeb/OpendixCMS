<?php
/**
 * Menu-verwaltung
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.0
 */

/**
 * Klasse zum verwalten der Menüstruktur
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Sch�b
 * @since 1.0     
 */
class Menue
{
	public function __Construct()
	{
		//Importieren der nötigen Java-Scripts
		//Functions::ImportJS('admin_menue.js');

		
		switch ($_GET['action'])
		{
			case 'einzel':
				$this -> Einzel();
				//Return damit die Show()-Methode am Ende nicth aufgerufenw ird
				return ;
				break;
			case 'save':
				$this -> Save();
				break;
			case 'standard':
				$this -> SetStandard();
				break;
			case 'add':
				$this -> Add();
				break;
			case 'del':
				$this -> Del();
				break;
		}

		//�bersicht anzeigen
		$this -> Show();

	}

	/**
	 * Funktion die einen einzelnen Men�punkt genau anzeigt
	 *
	 */
	private function Einzel()
	{
		$id = functions::GetVar($_GET, 'id');
		if($id == '')
		{
			functions::output_fehler('ID nicht definiert!');
			return;
		}
		//Abfragen der Daten zu diesem Men�punkt
		$query = "SELECT eintrag, vater, sysrecht.id, href, extern, phpfile, class, activ FROM sysmenue, sysrecht WHERE sysmenue.id = '". functions::cleaninput($_REQUEST['id']) ."' AND sysmenue.template = sysrecht.seite";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 157 ');
		$daten = @mysql_fetch_assoc($insert);

		//�bergabe an smarty...
		functions::output_var("id", functions::cleaninput($_REQUEST['id']));
		if($daten['activ'] == 1)
		{
			$daten['activ']=  ' checked';
		}

		$daten['vater'] =  functions::menuselect($daten['vater']);

		//Einfach der �bersichtlichkeit zu gute...
		$daten['name'] = $daten['eintrag'];
		$daten['gruppe'] = functions::Selector('sysmenuegruppe', 'name', $daten['gId']);
		$daten['template'] = functions::Selector('sysrecht', 'seite', $daten['id'], 'id', 'seite');
		$daten['saveLink'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'save', 'id' => $id));

		functions::Output_var('menueintrag', $daten);

	}

	/**
	 * Funktion um einzelne Men�eintr�ge zu sichern
	 *
	 */
	private function Save()
	{
		$id = functions::GetVar($_GET, 'id');
		if($id == '')
		{
			functions::Output_warnung('Keine ID �bergeben. Daten nicht gespeichert!');
			return ;
		}

		//Daten aus Formilar abfragen
		$vater 		= functions::cleaninput($_POST['vater']);
		$href 		= functions::cleaninput($_POST['href']);
		//name alein geht aus dem Formular nicht => _name
		$name 		= functions::cleaninput($_POST['_name']);
		$template 	= functions::cleaninput($_POST['template']);
		$class 		= functions::cleaninput($_POST['class']);
		$extern 	= functions::cleaninput($_POST['extern']);
		$phpfile 	= functions::cleaninput($_POST['phpfile']);
		$activ 		= functions::cleaninput($_POST['activ']);
		$gId 		= functions::cleaninput($_POST['gruppe']);
		if($activ == "on")
		{
			$activ = 1;
		}
		else
		{
			$activ = 0;
		}

		//Ermitteln des Templates
		$query = "SELECT seite FROM sysrecht WHERE id='$template'";
		$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 163.4555 ');
		$template = @mysql_result($insert,0);
		if($template == '')
		{
			Functions::Output_fehler('Template konnte nicht ermittelt werden!');;
			return;
		}

		//Daten pr�fen
		if(strlen($name) < 3)
		{
			functions::Output_warnung('Name zu kurz. �nderungen nicht gespeichert!');
			return;
		}

		//Daten updaten
		$query = "UPDATE sysmenue SET gId='$gId', vater = '$vater', href = '$href', eintrag = '$name', template= '$template', class = '$class', extern = '$extern', phpfile = '$phpfile', activ = '$activ' WHERE id = '$id'";
		$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 163 ');
		functions::output_bericht('�nderungen erfolgreich gespeichert!');

	}

	/**
	 * Funktion um einen Datensatz hinzuzuf�gen
	 *
	 */
	private function Add()
	{
		//Abfrage der Daten aus dem Formular
		$eintrag 	= functions::cleaninput($_POST['eintrag']);
		$aktiv 		= functions::OnOfDB(functions::cleaninput($_POST['aktiv']));
		$href 		= functions::cleaninput($_POST['href']);
		$template	= functions::cleaninput($_POST['template']);
		$class 		= functions::cleaninput($_POST['klasse']);
		$extern 	= functions::cleaninput($_POST['extern']);
		$vater 		= functions::cleaninput($_POST['vater']);
		$phpfile 	= functions::cleaninput($_POST['phpfile']);
		$gId 		= functions::cleaninput($_POST['gId']);

		if($template == '')
		{
			functions::Output_warnung('Kein Template ausgew�hlt! Nicht hinzugef�gt!');
			return;
		}

		//Pr�fen ob Klasse existiert...
		if(!class_exists($class))
		{
			//... wenn nicht warnung ausgeben!
			functions::Output_warnung('Klasse existiert nicht! Punkt wird hinzugef�gt aber nicht angezeigt!');
		}

		//Template ermitteln aus derm TemplateId
		$query = "SELECT seite FROM sysrecht WHERE id = '$template'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 34we3 ');
		$template = @mysql_result($insert, 0);

		$query 	= "INSERT INTO `sysmenue` ( `id` , gId, `eintrag` , `vater` , `href` , `template` , `class` , `standard` , `folge` , `extern` , `phpfile` , `contentId` , `activ`, `visible` ) VALUES ('','$gId', '$eintrag', '$vater', '$href', '$template', '$class', NULL , '0' , '$extern', '$phpfile', '0', '$aktiv', '1');";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 34 ');
		functions::output_bericht('Men�punkt erfolgreich hinzugef�gt!');
	}

	/**
	 * Funktion zum "l�schen" eines Men�punktes (wird ja in den Papierkobr verschoeben)
	 *
	 */
	private function Del()
	{
		$id = functions::GetVar($_GET, 'id');
		if($id == '')
		{
			functions::output_warnung('Keine ID definiert!');
			return false;
		}

		$query = "SELECT id FROM sysmenue WHERE vater = '$id'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 35 ');
		if(@mysql_num_rows($insert) > 0)
		{
			//echo mysql_result($insert, 0);
			functions::output_fehler('Zu diesem Men�punkt sind noch Untermen�punkte zugewiesen, konnte nicht gel�scht werden!');
		}
		else
		{
			functions::switchDatensatz($id, "sysmenue", 1);
			//$query  = "DELETE FROM sysmenue WHERE id = '$id'";
			//$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 36 ');
		}
		$this -> show();



	}


	/**
	 * Zeigt das Men� in der �bersicht an
	 *
	 */
	private function Show()
	{
		//Allgemein Infos ausgeben
		$allgemein['add']['template'] = functions::Selector('sysrecht', 'seite', 0,'id', 'seite');
		$allgemein['add']['vater'] = functions::menuSelect('');
		$allgemein['add']['gruppe'] = functions::Selector('sysmenuegruppe', 'name');
		functions::Output_var('vorgabe', $allgemein);

		//Erst das Array f�r das Men� erstellen
		$menu 	=  functions::createmenuselect(0,0, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', true);

		//Dann jeden Eintrag durchgehen und den dazugeh�rigen Link generieren
		for($i = 0; $i<count($menu); $i++)
		{
			$menu[$i]['link']['einzel'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'einzel', 'id' => $menu[$i]['id']));
			$menu[$i]['link']['up'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'up', 'id' => $menu[$i]['id']));
			$menu[$i]['link']['down'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'down', 'id' => $menu[$i]['id']));
			$menu[$i]['link']['standard'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'standard', 'id' => $menu[$i]['id']));
			$menu[$i]['link']['del'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'del', 'id' => $menu[$i]['id']));
		}

		//Und nun das ganze ausgeben

		
		$query = "SELECT id FROM sysmenue WHERE standard='1'";
		$insert = @mysql_query($query) OR functions::Output_fehler('Mysql-Error: alnsd9n2lnnnnn');
		$standard = @mysql_result($insert, 0);
		$count = count($menu);
		for($i=0; $i<$count; $i++)
		{
			if($menu[$i]['id'] == $standard)
			{
				$menu[$i]['standard'] = true;
				break;
			}
		}
		
				functions::Output_var('uebersicht', $menu);

	}



	/**
	 * Funktion um die Standard-Seite zu �ndern
	 *
	 */
	private function SetStandard()
	{
		$id = functions::GetVar($_GET, 'id');
		if($id == '')
		{
			functions::Output_fehler('Keine ID angegeben!');
			return;
		}
		//Abfrage wer momentan Standard ist
		$query = "SELECT id FROM sysmenue WHERE standard = 1";
		$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 158 ');
		$idm = @mysql_result($insert, 0);
		if($idm!='')
		{
			//Den alten standard eintrag zur�cksetzen
			$query = "UPDATE sysmenue SET standard = 0 WHERE id = '$idm'";
			$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 159 ');
		}
		else
		{
			functions::Output_warnung('Es war bis jetzt kein Men�punkt als Standard definiert!');
		}

		//Neuen Standardeintrag erstellen
		$query = "UPDATE sysmenue SET standard = 1 WHERE id = '$id'";
		$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 160 ');

		functions::output_bericht('Standard wurde erfolgreich ge�ndert');

	}

	/**
	 * Funktion um Men�-Items auf aktiv zu setzen oder umgekehrt!
	 *
	 */
	private function SetActiv()
	{
		$query = "SELECT activ FROM sysmenue WHERE id = '". functions::cleaninput($_REQUEST['id']) ."'";
		$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 161 ');
		$activ = @mysql_result($insert, 0);

		if($activ == 1)
		{
			$wert = 0;
		}
		else
		{
			$wert = 1;
		}

		$query = "UPDATE sysmenue SET activ = '$wert' WHERE id = '". functions::cleaninput($_REQUEST['id']) ."'";
		$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 162 ');
		functions::output_bericht('Status erfolgreich ge�ndert!');

	}
}
?>