<?php
/**
 * Bilder-Klassen
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Sch�b <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.0
 */

/**
 * Klasse zum importieren von Bildern von Bildern
 *
 * @author 		Stefan Sch�b
 * @package 	OpendixCMS
 * @version 	1.2
 * @copyright 	Copyright &copy; 2006, Stefan Sch�b
 * @since 		1.0
 */
class BildInput
{
	public function __construct()
	{
		$this -> tempdir 	= functions::getINIparam('temp_bilder');
		$this -> enddir		= MEDIEN . str_replace("'", "", functions::getINIparam('galerie_bilder_ordner'));


		switch($_GET['action'])
		{
			case 'uploadForm':
				$this -> showUploadForm();
				break;
			case 'upload':
				$this -> upload();
				$this -> showUploadForm();
				break;
			case 'add':
				$this -> add_select();
				break;
			case 'del':
				$this -> delImage();
			case 'change':
				//$this -> change();
				break;
			case 'save':
				//$this -> save();
				//$this -> change();
				break;
			case 'zip':
				$this -> zip();
				//$this -> change();
				break;
			case 'undownload':
				$this -> undownload();
				//$this -> change();
				break;
			default:
				$this -> add_select();
				break;
		}
	}

	/**
	 * Zeigt das Formular zum uploaden von einzelnen Bildern an
	 *
	 */
	private function showUploadForm()
	{
		functions::output_var('galerieUpload', functions::Selector('modgalerie', 'name'));
		functions::output_var('uploadLink',functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'upload')));
	}

	/**
	 * Uploaded ein einzelnes Bild in eine gewisse Galerie
	 *
	 */
	private  function upload()
	{
		$gId = functions::GetVar($_POST, 'gId');
		if(!is_numeric($gId))
		{
			functions::Output_warnung('Es wurde keine Galerie angegeben!');
			return ;
		}

		//Hier den Upload ausführen
		$path = functions::Upload('temp/UploadTemp/', 'datei');

		if(!$path)
		{
			functions::Output_warnung('Upload fehlgeschlagen!');
			return ;
		}

		$typ = @getimagesize($path);
		//Hier prüfen ob ein korrektes FOrmat mitgegeben wurde
		if(!$typ || ($typ['mime'] != 'image/gif' && $typ['mime'] != 'image/jpeg' && $typ['mime'] != 'image/png'))
		{
			functions::Output_warnung('Die angegebene Datei hat nicht das richtige Format. Nur jpeg, gif und png erlaubt!');
			return;
		}

		$this -> add($gId, $path);
	}

	/**
	 * wertet die Abfrage aus, wohin die Bilder sollen
	 *
	 */
	function add_select()
	{
		switch ($_GET['step'])
		{
			case '2':
				//Fileimport starten
				$this -> startImport();
				break;
			default:
				//Wen noch nichts gemacht wurde hauptauswahl anzeigen
				$this -> showStartScreen();
				break;
		}
	}
	
	/**
	 * Importiert ein Bild in das System:
	 *  1. Thumbnail sowie verkleinerte Bildversion erstellen
	 *  2. Bilder der Datenbank (Filebase) hinzuf�gen
	 *  3. Bilder der Datenbank (Galerie) hinzuf�gen
	 * 
	 * Diese Funktion wird per AJAX aufgerufen und liefert als Resultat ein String
	 * welcher mit 1 (erfolgreich) oder 0 (Fehler) beginnt und anschliessend die
	 * dazu entsprechende Meldung enth�lt
	 * 
	 * @param string 	$imgName	Name des Bildes im tmp Ordner
	 * @param int 		$gallerieId	ID der Galerie welcher das Bild hinzugef�gt werden soll
	 */
	public static function ImportImage($imgName, $gallerieId)
	{
		if(!is_numeric($gallerieId))
		{
			die('0Fehlerhafte Id (nicht numerisch) fuer den Import erhalten.');
		}
		
		$tmpPath = '';
		$outPath = '';
		$thumber = null;
		// Wenn ImgMagick installiert ist dieses verwenden da bessere Qualit�t
		if(ImgMagickThumbnailer::IsInstalled())
		{
			$thumber = new ImgMagickThumbnailer();
		}
		else
		{
			// Ansonsten mit den PHP internen Funktionen die Thumbs erstellen
			$thumber = new PHPthumbnail();	
		}
		
		$thumber -> SetInputPaht($tmpPath);
		$thumber -> SetOutputPath($outPath);
		
		// Kleines Thumbnail erstellen
		$thumber -> SetOutputSize(100, 100);
		$thumber -> setPrefix('thumb_');
		$filename = $thumber -> CreateThumbnail($imgName);
		$thumbId = Filebase::addFile($outPath . $filename);
		
		// Zu importierendes Bild verkleinern da es sowieso auf der Seite
		// nicht gr�sser dargestellt werden kann
		$thumber -> SetOutputSize(300, 300);
		$thumber -> setPrefix('big');
		$filename = $thumber -> CreateThumbnail($imgName);
		$bigId = Filebase::addFile($outPath . $filename);
		
		try 
		{
			SqlManager::GetInstance()->Insert('modgaleriebilder', array('gallerieId' => $gallerieId, 'pfad_k' => $thumbId, 'pfad_g' => $bigId));	
		} 
		catch (SqlManagerException $ex) 
		{
			die( '0SQL-Fehler!');
		}
		
		echo '1Bild erfolgreich importiert';
		
	}

	/**
	 * Importiert ein Bild
	 *
	 * Diese Funktion wird von AJAX-File verwendet um Bilde rzu importieren
	 *
	 * @param unknown_type $path
	 */
	function import($path, $id)
	{
//		if(!is_numeric($id))
//		{
//			die('0Fehlerhafte Id (nicht numerisch) fuer den Import erhalten.');
//		}
//
//		//EXIF-Daten auslesen
//		$exif = read_exif_data('../../../../temp/bildertemp/' . $path);
//		if($exif['FileDateTime'] == '')
//		{
//			$exif['FileDateTime'] = time();
//		}
		
//		//Thumbnail erstellen
//		$thumber = new mythumbnail();
//		$thumber -> setPfadIn('../../../../temp/bildertemp/');
//		$thumber -> setPfadOut(FILEBASE . 'galerie/');
//		try {
//			$file = $thumber -> doBildFile($path);
//		}
//		catch(Exception $ex)
//		{
//			echo '0' . $path . ' Fehler: ' . $ex -> getMessage();
//			return;
//		}
//
//		//Zur Filebase hinzufügen
//		$grossId = @Filebase::addFile(strtolower('galerie/' . $thumber -> getNewFilename()));
//		$kleinId = @Filebase::addFile(strtolower('galerie/' . $file));
//
//		$query = 'INSERT INTO modgaleriebilder(`gallerieId`, `pfad_k`, `pfad_g`, datum, iso, kamera) VALUES(\''. $id .'\',\''. $kleinId .'\',\''. $grossId .'\', FROM_UNIXTIME('. $exif['FileDateTime'] .'), \''. $exif['ISOSpeedRatings'] .'\' , \''. $exif['Make'] . ' ' .$exif['Model'] .'\')';
//		$insert = mysql_query($query) OR die('MYSQL-ERROR: JHSADFOIUHW');
//
//		//echo da hier auf eine AJAX anfrage geantwortet wird
//		if($thumber -> resize)
//		{
//			echo '1' . $path . ' erfolgreich importiert: Breite: ' . $thumber -> change['newHoehe'] . '/' .$thumber -> change['altHoehe'] . ' - '  .$thumber -> change['newBreite'] . '/' . $thumber -> change['altBreite'];
//		}
//		else
//		{
//			echo '1' . $path . ' erfolgreich importiert!';
//		}

	}

	private  function showStartScreen()
	{
		functions::Output_var('step', 1);
		//Prüfen ob Files vorhanden sind
		if(count(Functions::countfiles('temp/bildertemp/')) == 0)
		{
			return ;
		}
		functions::Output_var('filesAvaible', true);
		functions::Output_var('step', 1);
		functions::Output_var('galerien', Functions::Selector('modgalerie', 'name'));
		functions::Output_var('linkStep2', functions::GetLink(array('sub' => $_GET['sub'], 'step' => 2, 'action' => 'add')));
	}

	/**
	 * Startet den Bilder-Import
	 *
	 */
	private function startImport()
	{
		//Pr�fen ob eine neue Galerie erstellt werden soll
		$gId = 0;
		if($_POST['wohin'] == 'neu')
		{
			$name = functions::GetVar($_POST, 'neu');
			if(strlen($name) < 3)
			{
				//Sollte der angegebene Name kürzer als 3 Zeichen sein wieder startscreen aber mit Warnung anzeigen
				functions::Output_warnung('Zu kurzer Name!');
				$this -> showStartScreen();
				return ;
			}
			$query = 'INSERT INTO modgalerie(`name`, `activ`) VALUES(\''. $name .'\',\'1\')';
			$insert = mysql_query($query) OR Functions::Output_fehler('Mysql_erro: akjsdjkh2389234');
			$gId = mysql_insert_id();
		}
		else
		{
			$gId = functions::GetVar($_POST, 'vorhanden');
			if(!is_numeric($gId))
			{
				//Sollte keine Nummer angegeben worden sein wieder startscreen aber mit Warnung anzeigen
				//functions::Output_warnung('Ung&uuml;ltige Galerie-ID!');
				echo $gId;
				//$this -> showStartScreen();
				return ;
			}
		}


		//Galerie-ID in Session ablegen das nach dem AJAX-Import noch weis welehc Galerie
		$_SESSION['ImageImporter_gId'] = $gId;

		//Die ben�tigten Javascripts importieren
		JsImport::ImportModuleJS('galerie', 'imageimport.js');
		JsImport::ImportSystemJS('ajax_queue.js');
		Functions::RunJs('startImport('. $gId .')');
		
		//Step ausgeben damit der richtige Teil des Template sausgegeben wird
		functions::Output_var('step', 2);
		//functions::Output_var('linkStep3', functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'change', 'id' => $_SESSION['ImageImporter_gId'])));
		$sub = null;
		try 
		{
			$sub = SqlManager::GetInstance() -> SelectItem('sysmenue', 'id', 'class=\'BilderAdmin\'');
		}
		catch (SqlManagerException $ex)
		{
			echo "SQL Fehler";
			return;
		}
		$linkstep3 = functions::GetLink(array('sub' => $sub, 'action' => 'images', 'gid' => $_SESSION['ImageImporter_gId']));
		functions::Output_var('linkStep3', $linkstep3);
	}

	/**
	 * Gibt die Abfrage aus, wohin die Bilder sollen
	 *
	 */
	function add_wohin()
	{
		$anz = count(functions::countfiles($this -> tempdir));
		if($anz == 0)
		{
			functions::output_fehler('Es existieren keine Bilder im Tempordner ('.$this -> tempdir . ')');
		}
		else
		{
			if($anz > 50)
			{
				functions::Output_warnung('Es sind mehr als 50 Bilder im Import-Ordner. Dies kann zu unerwarteten Problemen f�hren.');
			}
			functions::output_var('vorhanden', functions::selector('modgalerie', 'name'));
		}
	}

	/**
	 * Fügt die Bilder wie gew�nscht ein
	 *
	 * @param int $gId
	 * @deprecated
	 */
	function add($gId, $path)
	{
//		$thumber = new mythumbnail();
//		$thumber -> setPfadIn('');
//		$thumber -> setPfadOut(FILEBASE . 'galerie/');
//		try {
//			$file = $thumber -> doBildFile($path);
//		}
//		catch(Exception $ex)
//		{
//			echo '0' . $path . ' Fehler: ' . $ex -> getMessage();
//			return;
//		}
//
//
//
//		//Zur Filebase hinzufügen
//		$grossId = Filebase::addFile(strtolower('galerie/' . $thumber -> getNewFilename()));
//		$kleinId = Filebase::addFile(strtolower('galerie/' . $file));
//
//		$query = 'INSERT INTO modgaleriebilder(`gallerieId`, `pfad_k`, `pfad_g`) VALUES(\''. $gId .'\',\''. $kleinId .'\',\''. $grossId .'\')';
//		$insert = mysql_query($query) OR die('MYSQL-ERROR: JHSADFOIUHaW');
//		if(!$insert)
//		{
//			functions::Output_fehler('Bild konnte nicht hinzugefügt werden!');
//			return;
//		}
//		functions::Output_bericht('Bild erfolgreich hinzugefügt!');
	}

	/**
	 * Fügt die Bilder in eine Bestehende Galerie ein
	 *
	 *@deprecated
	 */
	function add_bestehend()
	{
		$gallerieId = functions::cleaninput($_REQUEST['vorhanden']);
		if($gallerieId == '')
		{
			functions::output_fehler('Es wurde keine ID f�r die gew�nschte Galerie angegeben!');
		}
		else
		{
			$this -> add($gallerieId);
		}
	}//Function add_bestehend()

	/**
	 * F�gt die Bilder in eine neue Galerie ein
	 *
	 */
	function add_neu()
	{

		$name = functions::cleaninput($_REQUEST['neu']);
		if($name == '')
		{
			functions::output_fehler('Es wurde keine Name f�r die zuk�nftige Galerie angegeben!');
		}
		else
		{
			$query = "INSERT INTO modgalerie VALUES('', '$name', 1, '')";
			$insert = mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 211 ');
			if($insert)
			{
				functions::output_bericht('Galerie erfolgreich erstellt!');
				$this -> add(mysql_insert_id());
			}
		}
	}//Function add_neu()

	/**
	 * Zeigt die Übersicht über die Bilder an in der man Bilder löschen oder einer anderen Galerie zuweisen kann
	 *
	 * @param int $gId
	 */
	/*function change($gId = '')
	{
		$query = "SELECT count(*) FROM modgalerie";
		$insert = @mysql_query($query) OR functions::output_fehler( '@mysql-Error: Nr. 14.1 ');
		if(mysql_result($insert, 0) == 0)
		{
			functions::output_fehler('Es wurden noch keine Galerien deklariert!');
			return false;
		}
		if($gId !== 0 )
		{
			$gId = functions::cleaninput($_REQUEST['id']);

			$i = 0;
			$query = "SELECT id, name, download FROM modgalerie";
			$insert = @mysql_query($query) OR functions::output_fehler( '@mysql-Error: Nr. 14 ');
			while($daten = @mysql_fetch_assoc($insert))
			{

				$g_query 	= "SELECT count(id) FROM modgaleriebilder WHERE gallerieId = '" . $daten['id'] . "'";
				$g_insert 	= @mysql_query($g_query) OR functions::output_fehler( '@mysql-Error: Nr. 15 ');
				$anz 		= @mysql_result($g_insert, 0);
				$auswahl[$i]['id'] 	= $daten['id'];
				$auswahl[$i]['anz']	= $anz;
				$auswahl[$i]['name']= $daten['name'];
				$auswahl[$i]['link']['change'] = functions::getLink(array('sub' => $_GET['sub'], 'action' => 'change', 'id' => $daten['id']));
				$auswahl[$i]['link']['zip'] = functions::getLink(array('sub' => $_GET['sub'], 'action' => 'zip', 'id' => $daten['id']));

				$auswahl[$i]['link']['undownload'] = functions::getLink(array('sub' => $_GET['sub'], 'action' => 'undownload', 'id' => $daten['id']));

				if($daten['download'] == '' || $daten['download'] == 0)
				{
					$i++;
					continue;
				}

				$subquery = "SELECT datei FROM moddownload WHERE id = '{$daten['download']}'";
				$subinsert 	= @mysql_query($subquery) OR functions::output_fehler( '@mysql-Error: Nr. 15.1 ');
				if(mysql_num_rows($insert) == 0)
				{
					$subsubquery = "UPDATE modgalerie SET download = '' WHERE id = '{$daten['id']}'";
					$subsubinsert 	= @mysql_query($subsubquery) OR functions::output_fehler( '@mysql-Error: Nr. 15.2 ');
					$auswahl[$i]['download']= '';
					$i++;
					continue;
				}
				if(file_exists(FILEBASE . Filebase::getFilePath(@mysql_result($subinsert, 0))))
				{
					$auswahl[$i]['download']= $daten['download'];
					$i++;
					continue;
				}

				$subquery = "DELETE FROM moddownload WHERE id = '{$daten['download']}'";
				$subinsert 	= @mysql_query($subquery) OR functions::output_fehler( '@mysql-Error: Nr. 15.3 ');

				$subsubquery = "UPDATE modgalerie SET download = '' WHERE id = '{$daten['id']}'";
				$subsubinsert 	= @mysql_query($subsubquery) OR functions::output_fehler( '@mysql-Error: Nr. 15.4 ');

				$i++;
			}
			functions::output_var("gId", $gId);
			functions::output_var("auswahl", $auswahl);
			if($gId == '' || $_REQUEST['action'] == 'zip')
			{
				return;
			}
		}
		$query = "SELECT id, gallerieId, pfad_k FROM modgaleriebilder WHERE gallerieId = '$gId' ORDER BY id desc";
		$insert = mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 212');
		$i = 0;
		while($daten = mysql_fetch_assoc($insert))
		{
			if($i % 1 == 0)
			{
				if($i != 0)
				{
					$change[$i]['nlc'] = true;
				}
				$change[$i]['nl'] = true;
			}
			$change[$i]['id'] = $daten['id'];
			$change[$i]['delLink'] = functions::getLink(array('sub' => $_GET['sub'], 'action' => 'del', 'did' => $daten['id'], 'id' => $_GET['id']));
			$change[$i]['pfadk'] = FILEBASE . Filebase::getFilePath($daten['pfad_k']);
			$change[$i]['gSelect'] = functions::selector('modgalerie', 'name', $gId);
			$i++;
		}
		functions::output_var('gid', $gId);
		functions::output_var('change', $change);
		functions::Output_var('changeSaveLink', functions::getLink(array('sub' => $_GET['sub'], 'action' => 'save', 'id' => $_GET['id'])));
		functions::output_var('dir', $this->enddir);
	}*/

	/**
	 * Speichert die Bilderauswahl ab
	 *
	 */
	/*function save()
	{
		$gId = functions::cleaninput($_REQUEST['gId']);
		if($gId == '' && $gId != 0)
		{
			functions::output_fehler('Keine ID angegeben');
			return;
		}

		$query = "SELECT id FROM modgaleriebilder WHERE gallerieId = '$gId' ORDER BY id desc";
		$insert = mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 213');
		while($daten = mysql_fetch_assoc($insert))
		{
			$subquery = "UPDATE modgaleriebilder SET gallerieId = '". functions::cleaninput($_REQUEST['gallerie_' . $daten['id']]) ."' WHERE id = '". $daten['id'] ."'";
			$subinsert = mysql_query($subquery) OR functions::output_fehler('@mysql-Error: Nr. 214');
		}
		functions::output_bericht('Erfolgreich gespeichert');


	}*/

	/**
	 * ZIPt eine Galerie und steltl sie als download zur verf�gung
	 *
	 */
	function zip()
	{
		if($_REQUEST['id'] == '')
		{
			functions::output_warnung('ZIP konnte nicht erstellt werden, da keine Datei angegeben ist.');
			return;
		}
		$id = functions::cleaninput($_REQUEST['id']);
		$query = "SELECT pfad_g FROM modgaleriebilder WHERE gallerieId = '$id'";
		$insert = mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 224');
		while($daten = mysql_fetch_assoc($insert))
		{
			$bilder[] = FILEBASE . Filebase::getFilePath($daten['pfad_g']) ;
		}

		$query = "SELECT name FROM modgalerie WHERE id = '$id'";
		$insert = mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 225');
		$name = mysql_result($insert, 0);
		$name = str_replace(' ', '_', $name);

		$query = "SELECT id FROM moddownloadgruppe LIMIT 1";
		$insert = mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 227');
		$gid = mysql_result($insert, 0);

		//Sollte die Datei bereits bestehen wird in der FUnktion selbst ein Zufallsstring angehängt
		$name = functions::createZip($bilder, FILEBASE . 'downloads/' . $name . '.zip');

		//Grösse des erstellten Zip-Files ermitteln
		$groesse = filesize($name);

		//Datei zur Filebase hinzufügen
		$filebaseId = Filebase::addFile(str_replace(FILEBASE, '', $name));

		$query = 'INSERT INTO moddownload(`gId`,`Beschreibung`,`datei`,`groesse`) VALUES(\''.$gid. '\' ,\'Bildergalerie\', \'' . $filebaseId .'\', \''.$groesse.'\')';
		$insert = mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 226');

		$query = "UPDATE modgalerie SET download = '". mysql_insert_id() ."' WHERE id = '$id'";
		$insert = mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 227.1');
		if($insert)
		{
			functions::output_bericht('ZIP File wurde erfolgreich ertellt.');
		}
		else
		{
			functions::output_fehler('ZIP File konnte nicht estellt werden.');
		}

	}

	/**
	 * Funktion die eine zum download verf�gbare gallerie wieder da draus entfernt!
	 *
	 */
	function undownload()
	{
		$id = functions::cleaninput($_REQUEST['id']);
		if($id == '')
		{
			functions::output_warnung('Keine ID angegeben!');
			return false;
		}

		//Erst die download-ID auslesen
		$query = "SELECT download FROM modgalerie WHERE id = '$id'";
		$insert = mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 227.4f');
		$downloadId = mysql_result($insert, 0);

		//Dann das File auslesen das zum download zur verf�gung gestellt wird
		$query = "SELECT datei FROM moddownload WHERE id = '$downloadId'";
		$insert = mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 227.4s');
		$downloadFile = mysql_result($insert, 0);

		//Dann Datensatz in der download-Tabelle l�schen
		$query = "DELETE FROM moddownload WHERE id = '$downloadId' LIMIT 1";
		$insert = @mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 227.41');

		//Jetzt noch das File l�schen
		if(!functions::delFile(MEDIEN . "download/" . $downloadFile) && $downloadFile != '')
		{
			functions::output_warnung('Das ZIP-File der Galerie konnte nicht gel�scht werden!');
		}

		//download-id in der galerie-tabelle auf 0 setzen
		$query = "UPDATE modgalerie SET download = '0' WHERE id = '$id' LIMIT 1";
		$insert = @mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 227.4hh');

		functions::output_bericht('Galerie wurde erfolgreich entfernt!');
	}

	/**
	 * Löscht ein einzelnes Bild
	 *
	 */
	private function delImage()
	{
		$id = functions::GetVar($_GET, 'did');
		if($id=='')
		{
			functions::Output_warnung('Kein Bild gelöscht da keine gültige ID angegeben wurde!');
			return ;
		}
		//Pfad der Bilder (thumb und bild) suchen
		$query = "SELECT pfad_k, pfad_g FROM modgaleriebilder WHERE id = '$id' LIMIT 1";
		$insert  = @mysql_query($query) OR Functions::Output_fehler('MySql-Error:hoasd8nann1ni12uho12212');
		if(!$insert)
		{
			functions::Output_fehler('Bild konnte nicht gelöscht werden!');
			return;
		}

		$daten = mysql_fetch_assoc($insert);

		if(!Filebase::removeFileById($daten['pfad_k']))
		{
			functions::Output_warnung('Die Datei ' . Filebase::getFilePath($daten['pfad_k']) . ' konnte nicht gelöscht werden!');
		}
		if(!Filebase::removeFileById($daten['pfad_g']))
		{
			functions::Output_warnung('Die Datei ' . Filebase::getFilePath($daten['pfad_g']) . ' konnte nicht gelöscht werden!');
		}

		//Bild löschen
		$query = "DELETE FROM modgaleriebilder WHERE id = '$id' LIMIT 1";
		$insert  = @mysql_query($query) OR Functions::Output_fehler('MySql-Error:hoasd8nnn1ni12uho12212');
		if(!$insert)
		{
			functions::Output_fehler('Bild konnte nicht gelöscht werdeN!');
			return;
		}
		functions::Output_bericht('Bild erfoglreich gelöscht!');
	}

}