<?php
/**
 * Profil
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse um Profile anzuzeigen/bearbeiten
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 1.0     
 */
class Profil
{
	function __Construct()
	{
		switch($_REQUEST['action'])
		{
			case 'save':
				$this -> save(functions::cleaninput($_REQUEST['id']));
				//$this -> showeinzel(functions::cleaninput($_REQUEST['id']));
				$this -> Showall();
				break;
			case 'item':
				$this -> showeinzel(functions::cleaninput($_REQUEST['id']));
				break;
			case 'add':
				$this -> add();
				$this -> showall();
			default:	
				$this -> showall();
				break;
		}

	}

	/**
	 * Zeigt alle Benutzer an
	 *
	 */
	private function Showall()
	{
		$query = "SELECT id, nick, status FROM sysuser";
		$insert = @mysql_query($query) OR functions::output_fehler('524asd65sad');
		$i=0;
		while($daten = mysql_fetch_assoc($insert))
		{
			$showall[$i]['id'] 		= $daten['id'];
			$showall[$i]['name'] 	= $daten['nick'];
			$showall[$i]['status'] 	= $daten['status'];
			$showall[$i]['link'] 	= functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'item', 'id' => $daten['id']));
			$i++;
		}
		functions::output_var('alleprofile', $showall);
	}

	/**
	 * FUnktion mit der man einen Benutzer hinzufügen kann
	 *
	 */
	private function Add()
	{
		//Erst alle daten holen
		$nick 		= functions::cleaninput($_REQUEST['nick']);
		$vorname 	= functions::cleaninput($_REQUEST['vorname']);
		$nachname 	= functions::cleaninput($_REQUEST['nachname']);
		$passwort 	= md5(functions::cleaninput($_REQUEST['passwort']));
		$email 		= functions::cleaninput($_REQUEST['email']);
		$wohnort 	= functions::cleaninput($_REQUEST['wohnort']);
		$signatur 	= functions::cleaninput($_REQUEST['signatur']);
		//Nick und passwort überprüfen
		if($nick == '')
		{
			functions::output_warnung('Sie müssen einen Namen eingeben!');
			return false;
		}
		
		if(functions::checkPwComplex($passwort) == 2)
		{
			functions::output_warnung('SSie müssen ein gültiges Passwort angeben');
			return false;
		}
		
		$query = "SELECT count(*) FROM sysuser WHERE nick = '$nick'";
		$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 365');
		if(@mysql_result($insert, 0) > 0)
		{
			functions::output_warnung('Es existiert bereits ein Benutzer mit diesem Nicknamen!');
			return false;
		}
		
		//Standard-Design holen
		$query = "SELECT id FROM systemplate WHERE standard = '1'";
		$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 364');
		$template = @mysql_result($insert, 0);
		
		$query = "INSERT INTO `sysuser` ( `id` , `template` , `nick` , `passwort` , `vorname` , `nachname` , `email` , `gesperrt` , `status` , `wohnort` , `avatar` , `signatur` ) VALUES ('', '$template', '$nick', '$passwort', '$vorname', '$nachname', '$email', '', '1', '$wohnort', '', '$signatur');";
		$insert	= @mysql_query($query) OR functions::output_fehler(mysql_error());
		
	}
	
	/**
	 * Zeigt einen einzigen Benutzer an
	 *
	 * @param int $id BenutzerId
	 * @return mixed false wenn fehlgeschlagen ansonsten nichts
	 */
	public function Showeinzel($id)
	{
		if($id == '')
		{
			functions::output_fehler('Ungültige ID Angabe');
			return false;
		}
		$query = "SELECT id, nick, vorname, nachname, email, wohnort, signatur, avatar, template FROM sysuser WHERE id = '$id'";
		$insert = @mysql_query($query) OR functions::output_fehler('Unbekannter mysqllll fehler');
		if(mysql_num_rows($insert) == 0)
		{
			functions::output_fehler('Ungültige ID');
			return false;
		}
		$daten = mysql_fetch_assoc($insert);
		$daten['template'] = functions::selector('systemplate', 'name', $daten['template']);
		$daten['avatar'] = functions::getINIparam('PROFIL_AVATARORDNER') . '/' . $daten['avatar'];
		$daten['saveLink'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'save', 'id' => $daten['id']));
		functions::output_var('benutzer', $daten);
	}

	/**
	 * Speichert eine Benutzer-Konfiguration ab
	 *
	 * @param 	int $id Benutzerid
	 * @return 	boolean
	 * @todo 	Reagieren auf Fehler beim Löschen
	 */
	public function Save($id)
	{
		//Prüfen ob eine ID vorhanden ist
		if($id == '')
		{
			functions::output_fehler('Ungültige ID Angabe');
			return false;
		}	
		
		//Die ganzen Daten aus dem Formular abfragen
		$nick 		= functions::cleaninput($_REQUEST['nick']);
		$vorname 	= functions::cleaninput($_REQUEST['vorname']);
		$nachname 	= functions::cleaninput($_REQUEST['nachname']);
		$passwort 	= functions::cleaninput($_REQUEST['passwort']);
		$email 		= functions::cleaninput($_REQUEST['email']);
		$wohnort 	= functions::cleaninput($_REQUEST['wohnort']);
		$signatur 	= functions::cleaninput($_REQUEST['signatur']);
		$template 	= functions::cleaninput($_REQUEST['template']);
		$bild 		= functions::cleaninput($_FILES['avatar']['name']);
		
		//Sollte ein bild angegeben worden sein muss diese verarbeitet werden
		if($bild != '')
		{
			//Alten Avatar abfragen
			$query = "SELECT avatar FROM sysuser WHERE id = '$id'";
			$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 362');
			$altbild = @mysql_result($insert, 0);
			
			//Sollte dieser existieren diesen löschen
			if(file_exists(MEDIEN . functions::getINIparam('PROFIL_AVATARORDNER') . '/' . $altbild))
			{
				try {
					functions::delfile(MEDIEN . functions::getINIparam('PROFIL_AVATARORDNER') . '/' . $altbild);
				}
				catch(Exception $e){
					//Ignorieren
				}
			}
			
			//Neues Bild uploaden
			if(!$bild = functions::upload(MEDIEN . functions::getINIparam('PROFIL_AVATARORDNER'), 'avatar'))
			{
				functions::output_warnung('Das Bild konnte nicht raufgeladen werden');
			}
			
			//Bild mit der Thumbnail-Klasse zuschneiden
			$thumber = new mythumbnail();		
			$thumber -> setmaxHigh(200);
			$thumber -> setMaxWidth(200);
			$thumber -> setPrefix("avatar_");
			$thumber -> setpfadOut(MEDIEN . functions::getINIparam('PROFIL_AVATARORDNER'));
			$thumber -> setcopyOldToOut(false);
			$bild = $thumber -> doBildFile($bild);
			
			//SQL-Ansatz zusammensetzen für den UPDATE-Befehl
			$avatar = " , avatar = '$bild' ";
		}
		
		//Prüfen ob ein neues Passwort angegeben wurde
		if($passwort != 'xxxxxxx')
		{
			$passwort = ", passwort = '" . md5($passwort) . "' ";
		}
		else 
		{
			$passwort = '';
		}
		
		//Wenn die angegebene Mail korrekt ist wird diese gespeichert
		if(functions::checkmail($email))
		{
			$email = ", email = '$email '" ;
		}
		else 
		{
			$email = '';
		}
		
		//Hier der SQL-String mit dem das ganze gespeichert wird.
		$query = "UPDATE sysuser SET nick = '$nick' $passwort  $email $avatar , vorname = '$vorname', nachname = '$nachname', wohnort = '$wohnort', signatur = '$signatur', template = '$template' WHERE id = $id";
		$insert	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 361'); 

		return true;	
	}
	
	/**
	 * Funktion mit der man einen Benutzer sperren kann
	 *
	 * @return unknown
	 * @access private
	 */
	private function Sperren()
	{
		//Prüfen ob eine ID vorhanden ist
		$id = functions::cleaninput($_REQUEST['id']);
		if($id == '')
		{
			functions::output_fehler('Ungültige ID-Angabe');
			return false;
		}
		
		//SQL-String mit dem der ausgewählte Benutzer gesperrt werden kann
		$query = "UPDATE sysuser SET gesperrt = '1' WHERE id = '$id'";
		$insert = @mysql_query($query) OR functions::output_fehler('360  Fehler');
		if(mysql_affected_rows() == 0)
		{
			//Sollte kein Datensatz betroffen sein Fehler ausgeben da dann zu dieser ID kein Benutzer existiert
			functions::output_fehler('Zu dieser ID existiert kein Benutzer');
			return false;
		}
		
		//Bericht ausgeben das es geklappt hat und true zurückgeben
		functions::output_bericht('Benutzer erfolgreich gesperrt');
		return true;
	}

}



?>