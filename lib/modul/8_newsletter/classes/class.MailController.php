<?php

class MailController
{
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{
		switch ($_GET['action'])
		{
			case 'saveitem':
				$this -> saveItem();
			case 'item':
				$this -> showItem();
				break;
			case 'save':
				$this -> save();
				$this -> show();
				break;
			case 'del':
				$this-> delete();
				$this -> show();
				break;
			case 'add':
				$this -> add();
			default:
				$this -> show();
				break;
		}
	}

	/**
	 * Zeigt Infos über eine einzelne Person an
	 *
	 */
	private function showItem()
	{
		$query = 'SELECT ';
	}

	/**
	 * Speichert die Informationen über eine einzelne Person
	 *
	 */
	private function saveItem()
	{
		$id = functions::GetVar($_GET, 'id');
		if(!is_numeric($id))
		{
			functions::Output_fehler('Es wurde keine korrekte ID angegeben!');
			return;
		}
	}

	/**
	 * Zeigt alle Gruppen mit den E-Mails an
	 *
	 */
	private function show()
	{

		$emails = array();
		//Abfrage aller E-Mailadressen die in einer Gruppe sind
		$query = 'SELECT id, email, nickname FROM modnewsletter n, modnewsletteremailingroup ng WHERE n.id = ng.mailId ORDER BY nickname';
		$insert = mysql_query($query) OR functions::Output_fehler('Mysql-Error: ajhs9990');
		while($daten = mysql_fetch_assoc($insert))
		{
			
		}
		
		functions::Output_var('emails', $outData);

		//Anzeige der Gruppen für das Hinzufügen-Formular
		functions::Output_var('groups', functions::Selector('modnewslettergruppe', 'name'));
	}

	/**
	 * Entfernt einen Datensatz aus der Newsletter-Tabelle
	 *
	 */
	private function delete()
	{
		$id = functions::GetVar($_GET, 'id');
		if(!is_numeric($id))
		{
			functions::Output_fehler('Ung&uuml;ltige ID');
			return;
		}

		functions::SwitchDatensatz($id, 'modnewsletter', 1);
	}

	/**
	 * Fügt einen Datensatz in die Newsletter-Tabelle ein
	 *
	 */
	private function add()
	{
		$email = functions::GetVar($_POST, 'email');
		$group = functions::GetVar($_POST, 'group');
		$nick = functions::GetVar($_POST, 'nickname');

		if(!functions::Checkmail($email))
		{
			functions::Output_fehler('Es wurde eine ung&uuml;ltige E-Mail angegeben!');
			return;
		}

		if(!is_numeric($group))
		{
			functions::Output_fehler('Die angegebene Gruppe konnte nicht erkannt werden!');
			return;
		}

		$query = 'INSERT INTO modnewsletter(nickname,email,gId) VALUES(\''. $nick .'\',\''. $email .'\',\''. $group .'\')';
		$insert = mysql_query($query) OR functions::Output_fehler('Mysql-Error:  asaskdjasdzzzzaä');
		if($insert)
		{
			functions::Output_bericht($email . ' wurde erfolgreich hinzugefügt!');
			return;
		}

	}

	/**
	 * Speichert die Daten in der Übersicht
	 *
	 */
	private function save()
	{
		foreach($_POST['row'] as $key => $value)
		{
			if(!is_numeric($key))
			{
				functions::Output_warnung('ID ist keine Zahl: ' . $key);
				continue;
			}

			$nick = functions::GetVar($_POST['row'][$key], 'name');
			$group = functions::GetVar($_POST['row'][$key], 'group');

			if(!is_numeric($group))
			{
				functions::Output_warnung('Die Gruppe konnte nicht erkannt werden!');
				continue;
			}

			$query = 'UPDATE modnewsletter SET nickname=\'' . $nick . '\', gId=\'' . $group . '\' WHERE id = \''. $key .'\' LIMIT 1';
			$insert = mysql_query($query) OR Functions::Output_fehler('Mysql-Error: jah');


		}
	}
}

?>