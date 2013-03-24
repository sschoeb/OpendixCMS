<?php
/**
 * Personenverwaltung
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.0
 */

/**
 * Klasse zum verwalten der Personen
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.1
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		1.0
 */
class Person
{
	private $imagesPath = 'personen/';

	function __construct()
	{

		switch ($_GET['action'])
		{
			case "edit":
				$this -> alone();
				break;
			case 'getvcf':
				$this -> getVcf();
				$this -> showAll();
				break;
			case "upload":
				$this -> upload();
				$this -> showAll();
				break;
			case "save":
				
				if(isset($_POST['addgroup']))
				{
					$this -> AddGroup();
					$this -> alone();
				}
				else 
				{
					$this -> save();
					$this -> showAll();
				}
			
				break;

			case 'delete':
				$this -> delete();
				$this -> showAll();
				break;
			case "add":
				$this -> add();
				$this -> alone();
				break;
			case 'removeGroup':
				$this -> RemoveGroup();
				$this -> alone();
				break;
			default:
				$this -> showAll();
				break;
		}
	}

	/**
	 * F�gt einem Benutzer eine Gruppe hinzu
	 *
	 */
	private function AddGroup()
	{
		//User-ID und Gruppen-ID ermitteln
		$userId = Functions::GetVar($_GET, 'id');
		$groupId = Functions::GetVar($_POST, 'groupid');
		
		//Pr�fen der G�ltigkeit der IDs
		if(!is_numeric($userId) || !is_numeric($groupId))
		{
			functions::Output_fehler('User-ID oder Gruppe-ID wurde nicht korrekt &uuml;bergeben!');
			return;
		}
		
		//Abfragen des maximus der folge der Gruppe. Der neue Benutzer wird zuletzt angeh�ngt
		try 
		{
			//Abfragen des maximus der folge der Gruppe. Der neue Benutzer wird zuletzt angeh�ngt
			$insert = SqlManager::GetInstance() -> Query('SELECT MAX(folge) FROM modpersoningroup WHERE groupid=\''.  $groupId .'\'');
			$max = mysql_result($insert, 0);
			
			//Neuen Datensatz einf�gen
			SqlManager::GetInstance() -> Insert('modpersoningroup', array('groupid' => $groupId, 'userid' => $userId, 'folge' => $max + 1));
			
		}
		catch (Exception $ex)
		{
			functions::Output_fehler('Mysql-Error: nljnljawsdboasudzflajshdgfalsjhdfb');
			return;		
		}
		
		functions::Output_bericht('Neue Gruppe wurde erfolgreich hinzugef&uuml;gt!');
	}
	
	/**
	 * Entfernt einem Benutzer eine Gruppe
	 *
	 */
	private Function RemoveGroup()
	{

		//User-ID und Gruppen-ID ermitteln
		$userId = Functions::GetVar($_GET, 'id');
		$groupId = Functions::GetVar($_GET, 'groupid');
		
		//Pr�fen der G�ltigkeit der IDs
		if(!is_numeric($userId) || !is_numeric($groupId))
		{
			functions::Output_fehler('User-ID oder Gruppe-ID wurde nicht korrekt &uuml;bergeben!');
			return;
		}
		
		try 
		{
			SqlManager::GetInstance() -> Delete('modpersoningroup', 'userid=\''. $userId .'\' AND groupid = \'' . $groupId . '\'');
		}
		catch (Exception $ex)
		{
			functions::Output_fehler('Mysql-Error: aanljnljawsdboasudzflajshdgfalsjhdfb');
			return;		
		}
		
		Functions::Output_bericht('Gruppe wurde erfolgreich von der Person entfernt!');
	}
	
	/**
	 * Sendet ein VCF-File eines Benutzers
	 *
	 */
	protected function GetVcf()
	{
		$id = functions::GetVar($_GET, 'id');
		if(!is_numeric($id))
		{
			functions::Output_fehler('Es wurde keine gültige ID angegeben!');;
			return;
		}
		
		$daten = array();
		
		try 
		{
			$daten = SqlManager::GetInstance() -> SelectRow('modperson', $id);
		}
		catch (Exception $ex)
		{
			functions::Output_fehler('Mysql_error: jnnnnuu');
			return;
		}
		
		$name = split(' ', $daten['name']);
		$wohnort = split(' ', $daten['wohnort']);
		if(!is_numeric($wohnort[0]))
		{
			$wohnort[0] = '';
		}
		$vcf = new VCardCreator($name[0], $name[1], $daten['email'], $daten['tel'], '', '', $daten['funktion'], $wohnort[0], $daten['adresse'], $wohnort[1]);
		$vcf -> create('temp/persontemp/' . $id . '.vcf');
		Functions::Download('temp/persontemp/' . $id . '.vcf');
	}

	/**
	 * Funktion die eifnach ein Bild raufl�dt und in der Gr�sse anpasst!
	 *
	 */
	function Upload()
	{
		if(!$bild = functions::upload(FILEBASE . $this -> imagesPath, "bildUpload", 'images'))
		{
			functions::output_warnung('Bild konnte nicht raufgeladen werden!');
			return ;
		}
		
		$thumber = new mythumbnail();
		$thumber ->setmaxHigh(200);
		$thumber->setMaxWidth(140);
		$thumber->setFileName($bild);
		$thumber->setpfadIn(FILEBASE . $this -> imagesPath);
		$thumber->setpfadOut(FILEBASE . $this -> imagesPath);
		$thumber->setdelOldFile(false);
		$thumber->setWzeichenlogo(false);
		$thumber->setcopyOldToOut(false);
		$thumber->setRename(false);
		$thumber->setdelOldFile(true);
		$thumber->setPrefix("_");
		$tempbild = $bild;
		$i=0;
		while(file_exists(FILEBASE . $this -> imagesPath . basename($tempbild)))
		{
			$tempbild = $i . basename($bild);
			$i++;
		}
		try {
		$neubild = $thumber->doBildFile(basename($bild));
		}catch(Exception $ex)
		{
			functions::Output_fehler($ex -> getMessage());
			return;
		}
		Filebase::addFile(FILEBASE . $this -> imagesPath . $neubild);
		functions::output_bericht("Das Bild wurde geuploaded und unter folgendem Namen abgelegt: " . $neubild);
		return Filebase::getFileId(FILEBASE . $this -> imagesPath . $neubild);
	}

	/**
	 * Löscht eine Person
	 *
	 * Bilder bleiben bestehen!!
	 *
	 */
	private function Delete()
	{
		//Abfrage und �berpr�fen der ID der Person die gel�scht werden sol
		$id = functions::getVar($_GET, 'id');
		if(!is_numeric($id))
		{
			functions::output_fehler('Keine ID angegeben!');
			return false;
		}

		//L�schen der Person
		try 
		{
			SqlManager::GetInstance() -> DeleteById('modperson', $id);
		}
		catch (Exception $ex)
		{
			Functions::Output_fehler('Mysql-Error: askdkjasjqtqzuiortzuitrzui');
			return;
		}
		
		functions::Output_bericht('Person erfolgreich gelöscht!');
	}

	/**
	 * Funktion die eine einzelne Person anzeigt
	 *
	 * @return mixed false wenn fehlgeschlagen ansonsten true
	 */
	public function Alone()
	{
		//Abfrage und �berpr�fen der ID der Person die abgefragt werden soll
		$id = functions::getVar($_GET, 'id');
		if(!is_numeric($id))
		{
			functions::output_fehler('Keine ID angegeben!');
			return false;
		}
		
		//Array in welchem die Daten dieser Person gespeichert werden
		$daten = array();
		
		try 
		{
			//Abfragen der Daten zu dieser Person
			$daten = SqlManager::GetInstance() -> SelectRow('modperson', $id);
			
			//Array in das alle Gruppen dieser Persongespeichert werden
			$daten['group'] = array();
			
			//Abfragen aller Gruppen in denen dieser Person ist
			$insert =SqlManager::GetInstance() -> Query('SELECT g.name AS groupname, g.id AS groupid FROM modpersongruppe g, modpersoningroup pig WHERE pig.userid=\''. $id .'\' AND g.id = pig.groupid');
			while ($groupdata = mysql_fetch_assoc($insert))
			{
					$pos = count($daten['group']);
					$daten['group'][$pos]['name'] = $groupdata['groupname'];
					$daten['group'][$pos]['dellink'] = functions::GetLink(array('sub' => $_GET['sub'], 'groupid' => $groupdata['groupid'], 'id' => $id, 'action' => 'removeGroup'));
			}
		}
		catch (Exception $ex)
		{
			functions::Output_fehler('Mysql-Error:ljasdasldnlasziwekr8s');
			return;
		}
		
		$daten['addgrouplink'] = Functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'addGroup', 'id' => $id));
		$daten['addgroupselect'] = Functions::Selector('modpersongruppe', 'name');
		
		$daten['imageSelect'] = $this -> bild_select($daten['bildId']);
		$daten['imagePath'] = $this -> getImageName($daten['bildId']);
		$daten['gruppenId'] = functions::selector("modpersongruppe", "name", $daten['gruppenId']);
		$daten['saveLink'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'save', 'id' => $_GET['id']));
		functions::output_var('person_info', $daten);

	}

	/**
	 * Gibt den Namen des Bidles zurück
	 *
	 * @param unknown_type $id
	 */
	private function GetImageName($id)
	{
		$query = 'SELECT file FROM sysfilebase WHERE id =\''. $id .'\'';
		$insert = mysql_query($query) OR Functions::Output_fehler('Mysql-Error: lasdjhjhhkweiruzq');
		if(mysql_num_rows($insert) == 0)
		{
			return '';
		}
		return mysql_result($insert, 0);
	}

	/**
	 * Gibt die Übersicht aus
	 *
	 */
	private function ShowAll()
	{
		
		try 
		{
			$daten = SqlManager::GetInstance() -> Select('modperson', array('id', 'name'), '', 'name', 'ASC');
			for($i=0; $i< count($daten); $i++)
			{
				$daten[$i]['detail'] = functions::GetLink(array('action' => 'edit', 'id' => $daten[$i]['id']), true);
				$daten[$i]['delete'] = functions::GetLink(array('action' => 'delete', 'id' => $daten[$i]['id']), true);
			}
			
			Functions::Output_var('person_data', $daten);
		}
		catch (Exception $ex)
		{
			throw new CMSException('Abfrage der Benutzer fehlgeschlagen!', CMSException::T_MODULEERROR , $ex);
		}
	}


	/**
	 * Gibt eine Select für die Bildauswahl aus
	 *
	 * @param unknown_type $selected
	 * @return unknown
	 */
	function Bild_select($selected = '')
	{
		$files = Filebase::getFiles(str_replace('/', '', $this -> imagesPath));

		for($i=0; $i<count($files); $i++)
		{
			if($files[$i]['id'] == $selected)
			{

				$return = $return . "<option value=\"$selected\" selected>". $files[$i]['file'] ."</option>";
			}
			else
			{
				$return = $return . "<option value=\"". $files[$i]['id'] ."\">". $files[$i]['file'] ."</option>";
			}
		}

		return $return;
	}


	/**
	 * Speichert die Daten einer Person	
	 *
	 * @return unknown
	 */
	function Save()
	{
		$id = functions::getVar($_GET, 'id');
		if($id == '')
		{
			functions::output_warnung("Person nicht gespeichert! Keine ID angegeben!");
			return false;
		}
		$funktion = functions::getVar($_POST, 'funktion');
		$name = functions::getVar($_POST, 'person_name');
		$wohnort = functions::getVar($_POST, 'wohnort');
		$tel = functions::getVar($_POST, 'tel');
		$email = functions::getVar($_POST, 'email');
		$adresse = functions::getVar($_POST, 'adresse');

		//Prüfen ob ein Bild aus der DB verwendet werden soll oder ob ein neues Bild geploaded werden soll
		if($_POST['imageType'] == 'up')
		{
			//Dann ein Bild uploaden, zur filebase hinzufügen und verwenden
			$bild = $this -> upload();
		}
		else
		{
			//Bild aus dem select verwenden
			$bild = functions::getVar($_POST, 'imageSelect');
		}

		try 
		{
			SqlManager::GetInstance() -> Update('modperson', array('adresse' => $adresse, 'funktion' => $funktion, 'name' => $name, 'wohnort' => $wohnort, 'tel' => $tel, 'email' => $email, 'bildId' => $bild), 'id=\''. $id .'\'');
		}
		catch (Exception $ex)
		{
			functions::Output_fehler('Mysql-Error:n ksadfhikjjjjjjjjjjjjjj');
			return;
		}
		
		functions::Output_bericht($name . ' wurde erfolgreich gespeichert!');

	}


	/**
	 * F�gt eine Person zur Datenbank hinzu
	 *
	 */
	function Add()
	{
		
		$name = functions::GetVar($_POST, 'name');
		if(strlen($name) < 3)
		{
			functions::Output_warnung('Der angegebene Name ist zu kurz! (min. 3 Zeichen)');
			return;
		}
		
		try
		{
			SqlManager::GetInstance() -> Insert('modperson', array('name' => $name));
		}
		catch (Exception $ex)
		{
			functions::Output_fehler('Mysql-Error: askijdkjngwuzegr8345345b5  b345345');
			return;
		}
		
		functions::Output_bericht('Person erfolgreich hinzugefügt!');
		
		//Id in den Get-Parameter setzen damit beim aufruf von alone() eine ID vorhanden ist
		$_GET['id'] = mysql_insert_id();
	}


}
