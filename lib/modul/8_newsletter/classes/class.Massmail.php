<?php

/**
 * Massenmail-Klassen
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Sch�b <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.0
 */

/**
 * Klasse zum versenden von Berichten
 * 
 * @author Stefan Sch�b
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Sch�b
 * @since 1.0     
 */
class Massmail
{

	/**
	 * Dieser Text wird an einen Newsletter angeh�ngt um den Link zum abschalten des Newsletters zu erl�utern!
	 *
	 * @var string
	 */
	private $addtext = "\n \n \n\n----------------------------------------------------------\nDiese E-Mail wurde durch die automatische Newsletterversendung von der Homepage des Skiclub Gams (http://www.scGams.ch) verschickt! \n Sollten sie diesen Service nicht mehr in Anspruch nehmen wollen klicken sie auf den folgenden Link:\n";

	/**
	 * Variable in der der Betreff gespeichert wird.
	 *
	 * @var string
	 */
	private $betreff = '';

	/**
	 * Konstruktor
	 *
	 */
	function massMail()
	{

		if($_REQUEST['action'] == '' && $_GET['subAction'] != '')
		{
			$_REQUEST['action'] = $_GET['subAction'];
		}
		switch($_REQUEST['action'])
		{
			case "send":
				$this -> absenden();
				$this -> show();
				break;
			case "edit":
				$this -> edit();
				break;
			case "clean":
				$this -> cleanEmail();
				$this -> edit();
			case "save":
				$this -> save();
				$this -> edit();
				break;
			case 'add':
				$this -> addNewsletter();
				$this -> edit();
				break;
			case 'del':
				$this -> del();
				$this -> edit();
				break;
			default:
				$this -> show();
				break;
		}
	}


	/**
	 * L�scht eine E-Mail
	 *
	 * @return unknown
	 */
	function del()
	{
		$id = functions::cleaninput($_REQUEST['id']);
		if($id == '')
		{
			functions::output_warnung('E-Mail konnte nicht gel&ouml;scht werden. Keine ID angegeben!');
			return false;
		}
		functions::switchDatensatz($id, 'modnewsletter', 1);
	}

	/**
	 * Zeigt alle E-Mails an
	 *
	 */
	function show()
	{
		$i = 0;
		$query = "SELECT id, name FROM modnewslettergruppe ORDER BY id ASC";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 164');
		while($daten = @mysql_fetch_assoc($insert))
		{
			$subquery = "SELECT COUNT(*) FROM modnewsletter WHERE gId = '{$daten['id']}'";
			$subinsert = mysql_query($subquery) OR functions::output_fehler('MySQL-Error: Nr. 164.5g');
			
			$newsletter[$i]['anz'] = mysql_result($subinsert, 0);
			$newsletter[$i]['name'] = $daten['name'];
			$newsletter[$i]['id'] = $daten['id'];
			$i++;
		}
		$i = 0;
		$query = "SELECT id, name FROM sysrechtgruppe";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 166');
		while($daten = @mysql_fetch_assoc($insert))
		{
			$subquery = "SELECT COUNT(*) FROM sysuser b, sysuserrechtgruppe u WHERE u.benutzerId = b.id AND u.gruppenId = '{$daten['id']}'";
			$subinsert = mysql_query($subquery) OR functions::output_fehler('MySQL-Error: Nr. 164.5ga');
			$benutzergruppe[$i]['anz'] = mysql_result($subinsert, 0);		
			$benutzergruppe[$i]['name'] = $daten['name'];
			$benutzergruppe[$i]['id'] 	= $daten['id'];
			$i++;
		}
		functions::Output_var('sendLink', functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'send')));
		functions::output_var("newsletter", $newsletter);
		functions::output_var("benutzergruppe", $benutzergruppe);
	}

	function showx()
	{
		functions::importJs('admin.massmail.js');
		functions::importCSS('admin.massmail.css');

		$i = 0;

		$query = "SELECT id, name FROM modnewslettergruppe ORDER BY id ASC";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 164');
		while($daten = @mysql_fetch_assoc($insert))
		{
			$j=0;
			//Namen der Grupen eintragen
			$newsletter[$i]['name'] = $daten['name'];
			$newsletter[$i]['id'] 	= $daten['id'];

			//Alle E-Mails in der Grupe abfragen und eintragen
			$subquery = "SELECT id, email FROM modnewsletter WHERE gId = '" . $daten['id'] . "'";
			$subinsert = mysql_query($subquery) OR functions::output_fehler('MySQL-Error: Nr. 349');
			while($subdaten = mysql_fetch_assoc($subinsert))
			{
				$newsletter[$i][$j]['email'] 	= $subdaten['email'];
				$newsletter[$i][$j]['id'] 		= $subdaten['id'];
				$j++;
			}
			$i++;
		}
		$i = 0;
		$query = "SELECT id, name FROM sysrechtgruppe";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 166');
		while($daten = @mysql_fetch_assoc($insert))
		{
			$benutzergruppe[$i]['name'] = $daten['name'];
			$benutzergruppe[$i]['id'] 	= $daten['id'];
			$i++;
		}

		functions::output_var("newsletter", $newsletter);
		functions::output_var("benutzergruppe", $benutzergruppe);
	}

	/**
	 * versendet E-Mails
	 *
	 */
	function absenden() //
	{
		$gesendet=0;
		//�berpr�fen ob Betreff und text nicht leer sind
		if($_REQUEST['betreff'] == "" || $_REQUEST['text'] == "")
		{
			functions::output_fehler('Sie müssen einen Betreff und eine Nachricht angeben!');
		}

		//Array in dem alle E-Mails abgelegt werden die gesendet werden m�ssen
		$zusenden = array();

		//�berpr�fen ob ein anhang angegeben wurde, falls ja anhang uploaden
		if($_FILES['file']['name'] != "")
		{
			if(!functions::upload('temp/mailtemp/'))
			{
				functions::output_warnung('Dateianhang konnte nicht versendet werden! - Upload fehlgeschlagen!');
			}
		}

		//�berpr�fen ob die Checkbox Newsletter nicht enabled wurde, wenn ja f�r jedes abfragen ob es enabled ist
		if($_REQUEST['newsletter'] != "on")
		{
			$query = "SELECT id FROM modnewslettergruppe";
			$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 168');
			while($daten = @mysql_fetch_array($insert))
			{
				if($_REQUEST['newsletter'][$daten['id']] == "on")
				{

					$subquery = "SELECT id, email, stopkey FROM modnewsletter WHERE gId = '{$daten['id']}'";
					$subinsert = mysql_query($subquery) OR functions::output_fehler('MySQL-Error: Nr. 168.1');
					while($sub_daten = mysql_fetch_assoc($subinsert))
					{
						if(!$this -> CheckAlreadyExist($sub_daten['email'], $zusenden))
						{
							continue;
						}
						$zusenden[]['email'] = $sub_daten['email'];
						$zusenden[count($zusenden) -1]['stopkey'] = $sub_daten['stopkey'];
						$zusenden[count($zusenden) -1]['newsletter'] = true;
						$zusenden[count($zusenden) -1]['id'] = $sub_daten['id'];

					}

				}
			}
		}

		
		//Nun das gleiche wie oben einfach noch für die Benutzergruppen
		$query = "SELECT id FROM sysrechtgruppe";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 169');
		while($daten = @mysql_fetch_array($insert))
		{

			if($_POST['benutzergruppe'][$daten['id']] == "on")
			{
				$sub_query = "SELECT b.id, email FROM sysuser b, sysuserrechtgruppe ug WHERE b.id = ug.benutzerId AND ug.gruppenId = ". $daten['id'] ." GROUP BY b.id";
				$sub_insert = @mysql_query($sub_query) OR functions::output_fehler('MySQL-Error: Nr. 171');
				while($sub_daten = @mysql_fetch_array($sub_insert))
				{
					if(!$this -> CheckAlreadyExist($sub_daten['email'], $zusenden))
					{
						continue;
					}
					$zusenden[]['email'] = $sub_daten['email'];
					$zusenden[count($zusenden) -1]['stopkey'] ='';
					$zusenden[count($zusenden) -1]['newsletter'] = false;
					$zusenden[count($zusenden) -1]['id'] = 0;
				}
			}
		}

		//Pr�fen ob irgendwelche E-Mails selektiert wurden, ansonsten Warnung und Funktion verlassen
		if(count($zusenden) == 0)
		{
			functions::output_warnung('Sie müssen noch Empfänger auswählen.');
			return;
		}


		//Nun die Mail versenden
		$sent = count($zusenden);
		foreach($zusenden as $item)
		{
			
			//$item ist nun ein Array mit email und stopkey
			$text = $_POST['text'];

			if($item['newsletter'])
			{
				$text .=  $this -> createAddText($item['stopkey'],$item['id']);
			}
			if(!functions::sendmail($item['email'], $_POST['betreff'], $text, $_FILES['file']['name']))
			{
				$sent--;
				functions::output_warnung('Folgender Empfänger konnte nicht erreicht werden: ' . $item['email']);
			}
		}

		
		functions::output_bericht('Es konnten ' . $sent . ' von ' . count($zusenden) . ' Empfänger erreicht werden!');

	}

	/**
	 * Pr�ft ob eine Email bereits in der Liste der Empf�nger vorkommt	
	 *
	 * @param String $email		Die zu pr�fende E-Mail
	 * @param Array $tosend		Array mit allen bereits eingetragenen Mails
	 * @return >Boolean
	 */
	private function CheckAlreadyExist($email, $tosend)
	{
		foreach ($tosend as $email)
		{
			if($tosend['email'] == $email)
			{
				return  false;
			}
		}

		return true;

	}

	/**
	 * Funktion die den Link zur�ckgibt mit dem man sich aus dem Newsletter l�schen kann
	 *
	 * @param string $stopkey
	 * @param int $id
	 * @return string
	 */
	function createAddText($stopkey, $id)
	{
		return $this -> addtext . "http://" . $_SERVER["HTTP_HOST"] .  dirname($_SERVER['REQUEST_URI']). "/stopnewsletter.php?stopkey=".md5($stopkey) ."&id=$id";
	}

	/**
	 * Funktion um E-Mails die sich in den Newsletter eingetragen haben zu bearbeiten
	 *
	 */
	function edit()
	{
		$i = 0;
		$active_page = !empty($_GET['page']) ? $_GET['page'] : 0;		//Hier kommt die Bl�tterklasse ins Spiel

		$query 	= 'SELECT COUNT(id) FROM modnewsletter';					//Abfragen wieviele Eintr�ge die tabelle hat
		$result	= 	@mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 175 ');
		list($entries) = mysql_fetch_row($result);

		$blaettern=new Blaettern($active_page, $entries);		//Neue Instanz von blaettern erstellen
		$blaettern->set_Link_Href("");
		$blaettern->set_Entries_Per_Page(ANZ_SEITEN);					//Anzahl seiten pro seiten festlegen (Konstante wird hier im script gesetzt und steh tin der config.ini)

		$query = "SELECT id, gId, email FROM modnewsletter ORDER BY gId, email ASC  LIMIT ".($blaettern->get_Epp() * $blaettern->get_Active_Page()).', '.$blaettern->get_Epp();
		$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 174 ');
		while($daten = @mysql_fetch_assoc($insert))
		{
			$newsletter[$i]['id'] 		= $daten['id'];
			$newsletter[$i]['i'] 		= $i;
			$newsletter[$i]['gruppe'] 	= functions::selector("modnewslettergruppe", "name", $daten['gId']);
			$newsletter[$i]['email'] 	= $daten['email'];
			$newsletter[$i]['linkDelete'] 	= functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'del', 'id' => $daten['id']));
			$i++;
		}

		functions::output_var('linkClean' , functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'clean')));
		functions::output_var("blaettern", $blaettern -> create());
		functions::output_var("show", $newsletter);


	}

	/**
	 * Funktion mit der man eine E-Mail an einen Newsletter anf�gen kann
	 *
	 * @param string $email
	 */
	function AddNewsletter($email = '')
	{
		if($email == '')
		{
			$email = functions::cleaninput($_REQUEST['email']);
			if($email == '' || !functions::checkmail($email))
			{
				functions::output_fehler('Ung�ltige E-Mail');
				return  false;
			}
		}

		$query = "SELECT COUNT(*) FROM modnewsletter WHERE email='$email'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 34ff6 ');
		if(mysql_result($insert, 0)==1)
		{
			functions::output_warnung('Sie haben sich bereits beim Newsletter angemeldet!');
			return;
		}
		
		$query = "INSERT INTO modnewsletter VALUES('','','$email', '$stopkey')";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 346 ');
		if(!$insert)
		{
			functions::output_fehler('E-Mail konnte nicht hinzugef�t werden');
			return false;
		}
		functions::Output_bericht('E-Mail erfolgreich zum Newsletter hinzugef�gt!');
		return true;
	}

	function CheckNewsletterAdd()
	{
		if(!isset($_GET['newsletter'])){
			return;
		}
		if($_GET['newsletter'] == 1)
		{
			massmail::AddNewsletter();
		}
	}

	/**
	 * FUnktion zum Speichern der E-Mails zu den dazugeh�rigen Newsletter-Gruppen
	 *
	 */
	function save()
	{
		$query	= 'SELECT id, gId FROM modnewsletter ORDER BY email ASC';										//Alle werte aus der tabelle abfragen
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 176 ');
		$j = 0;
		while($daten = @mysql_fetch_assoc($insert))
		{
			if($_REQUEST['id_' . $j] == $daten['id'])
			{
				$subquery = "UPDATE modnewsletter SET gId = '" . $_REQUEST['gId_'.$j] . "' WHERE id = '". $daten['id'] ."' LIMIT 1";
				$subinsert = @mysql_query($subquery) OR functions::output_fehler('MySQL-Error: Nr. 177 ');
			}
			$j++;
		}

	}


	/**
	 * Funktion um ung�ltige E-Mails gleich direkt rauszul�schen
	 *
	 */
	function cleanEmail()
	{
		$query = "SELECT email, id FROM modnewsletter";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 172');
		while($daten = @mysql_fetch_assoc($insert))
		{

			if(!functions::checkmail($daten['email'], '', 1))
			{
				$delquery = "DELETE FROM modnewsletter WHERE id = '". $daten['id'] ."' LIMIT 1";
				$delinsert = @mysql_query($delquery) OR functions::output_fehler('MySQL-Error: Nr. 173');
			}

		}

	}

	/**
	 * FUnktion mit der man einen DNS-lookup unter windows machen kann
	 *
	 * @param 	string 	$hostName
	 * @param 	string 	$recType
	 * @return 	boolean
	 * @todo 	Funktioniert noch nicht und wird auch noch nicht eingesetzt
	 */
	function myCheckDNSRR($hostName, $recType = '')
	{
		if(!empty($hostName))
		{
			if( $recType == '' ) $recType = "MX";
			exec("nslookup -type=$recType $hostName", $result);
			// check each line to find the one that starts with the host
			// name. If it exists then the function succeeded.
			foreach ($result as $line)
			{
				if(eregi("^$hostName",$line))
				{
					return true;
				}
			}
			// otherwise there was no mail handler for the domain
			return false;
		}
		return false;
	}

	/**
	 * Funktion mit der man eine E-Mail validieren kann, allerdings nicht unter Windows! checkdnsrr geht da nicht
	 *
	 * @param 	string 	$email
	 * @return 	boolean
	 * @todo 	kA ob die funktioniert, wird auch noch nicht eingesetzt
	 */
	function validate_email($email)
	{
		$exp = "^[a-z\'0-9]+([._-][a-z\'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$";

		if(eregi($exp,$email))
		{
			if(checkdnsrr(array_pop(explode("@",$email)),"MX"))
			{
				return true;
			}
			else
			{
				return false;
			}

		}
		else
		{
			return false;
		}
	}

	/**
	 * Funktion damit sich ein Benutzer selbstst�ndig �ber den link der zu jedem Newsletter mitgeschickt wird entfernen kann
	 *
	 */
	function takeOffline()
	{
		$key = $_REQUEST['stopkey'];
		$id = functions::cleaninput($_REQUEST['id']);

		if($id == "" || $key == "")
		{
			functions::output_fehler('Falsche Angaben!');
		}
		else
		{
			$query = "SELECT stopkey FROM modnewsletter WHERE id = " . $id;
			$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 178');
			$stopkey = @mysql_result($insert, 0);

			if($key == md5($stopkey))
			{
				$query = "DELETE FROM modnewsletter WHERE id = " . $id . " LIMIT 1";
				$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 179');
				functions::output_bericht('Sie wurden erfolgreich aus der Newsletter-Liste entfernt!');
			}

		}


	}
}//ENd class


?>
