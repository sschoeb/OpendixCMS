<?php
/**
 * File f�r die Klasse functions
 *
 *
 * @package    	OpendixCMS.Core
 * @author     	Stefan Schoeb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Sch�b
 * @version    	1.0
 */

/**
 * Klasse die diverse Funktionen f�r alle zur Verf�gung stellt
 *
 * @author 		Stefan Schoeb <opendix@gmail.com>
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		1.0
 */
class Functions
{
	public static function GetWebsiteContent($website)
	{
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6' );
		curl_setopt ( $ch, CURLOPT_URL, $website );
		curl_setopt ( $ch, CURLOPT_FAILONERROR, true );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt ( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
		$data = curl_exec ( $ch );
		if (! $data)
		{
			throw new CMSException ( 'Webseite konnte nicht gelesen werden', CMSException::T_WARNING );
			exit ();
		}
		return $data;
	}
	
	public static function AddAttachement($jointable, $foreygnKeyName, $foreygnKey, $fileKey = 'fileId')
	{
		$filebasePath = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/filebaseFolder' );
		$anhaenge = Validator::ForgeInput ( $_POST ['anhang'], true, true, false);
		foreach ( $anhaenge as $value )
		{
			
			Filebase::UseFile ( $filebasePath . $value );
			$fid = Filebase::GetFileId ( $filebasePath . $value );
			try
			{
				SqlManager::GetInstance ()->Insert ( $jointable, array ($foreygnKeyName => $foreygnKey, $fileKey => $fid ) );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'Anhang konnte nicht eingetragen werden!', CMSException::T_MODULEERROR, $ex );
			}
		}
	}
	
	/**
	 * Singleton der immer die instanz von smarty zur�ckgibt
	 *
	 * @return smarty-object
	 */
	/*public static Function GetSmarty()
	{
		//single instance
		static $instance;

		//Sollte noch keine Instanz vorhanden sein, eine erstellen
		if(!is_object($instance))
		{
			$template = functions::selecttemplate();
			$instance = new MySmarty($template);
		}
		return($instance);
	}*/
	/**
	 * Funktion mit der man eine beliebige Variable im Template ersetzen kann
	 *
	 * @param string $var
	 * @param string $durch
	 */
	/*public static Function  Output_var($var, $durch)
	{
		$smarty = functions::getSmarty();
		$smarty -> assign($var, $durch);
	}*/
	
	/**
	 * Funktion mit der man eine Warnung ausgeben kann
	 *
	 * @param string $warnung
	 */
	/*public static Function  Output_warnung($warnung)
	{
		static $warnung_ausgabe;
		if(functions::getINIparam('Logg_warnung') == 'on')
		{
			Logfile::log(functions::getINIparam('Logg_warnung_ordner'), $warnung);
		}
		$warnung_ausgabe[count($warnung_ausgabe)+1] = $warnung;
		functions::output_var('warnung', $warnung_ausgabe);
	}*/
	
	/**
	 * FUnktion gibt einen Fehler aus
	 *
	 * @param string $fehler
	 */
	/*public static Function  Output_fehler($fehler)
	{
		static $fehler_ausgabe;
		if(functions::getINIparam('Logg_fehler') == 'on')
		{
			Logfile::log(functions::getINIparam('Logg_fehler_ordner'), $fehler);
		}
		$fehler_ausgabe[count($fehler_ausgabe)+1] = $fehler;
		functions::output_var('fehler', $fehler_ausgabe);
	}*/
	
	public static function validate_captcha($md5, $code)
	{
		$shouldbe = md5 ( $code . CAPTCHA_SALT . 'some salt' . $code . CAPTCHA_SALT . 'extra salt' );
		return ($shouldbe == $md5);
	}
	
	/**
	 * Funktion gibt einen Bericht, z.b. Daten gespeichert aus
	 *
	 * @param string $bericht
	 */
	/*public static Function  Output_bericht($bericht)
	{
		static $bericht_ausgabe;
		$bericht_ausgabe[count($bericht_ausgabe)+1] = $bericht;
		functions::output_var('bericht', $bericht_ausgabe);
	}*/
	
	/**
	 * Funktion mit der man eine String like (Home > Verwaltung > ...) ausgeben kann..
	 *
	 * @param int $sub
	 * @todo  Eibauen das die Startseite nicht oben angezeigt wird!
	 */
	public static Function OutPutPositionString($sub)
	{
		$vater = $sub; //Erster Vater ist $sub selbst, da erst noch name und vater von der aktuellen position geholt werden m�ssen
		$i = 0;
		
		while ( $vater != 0 ) //Solange vater != 0 weitermachen --> wenn vater = 0 heisst das, man ist auf der obersten stufe
		{
			$daten = array ();
			try
			{
				$daten = SqlManager::GetInstance ()->Select ( 'sysmenue', array ('id', 'eintrag', 'vater' ), 'id=' . $vater );
			} catch ( SqlManagerException $ex )
			{
				Functions::Output_fehler ( $ex );
				return;
			}
			$vater = $daten [0] ['vater']; //Vater definieren f�r den n�chsten durchlauf
			$position [$i] ['spitz'] = '>'; //Die spitze die imemr vor jeder stufe ausgegeben wird
			$position [$i] ['name'] = $daten [0] ['eintrag']; //Der Name der Stufen
			$position [$i] ['url'] = functions::GetLink ( array ('sub' => $daten [0] ['id'] ) );
			$i ++;
		}
		$position [0] ['last'] = 1; //Der Letzte eintrag kriegt noch eine markierung, damit man ihn erkennt bei der ausgabe (letzter ist kein link..)
		

		$position = array_reverse ( $position ); //Array umkehren da man sich ja von hinten nach vorne gearbeite ha
		

		//Den Link f�r die Startseite abfragen
		//Daf�r wird erst die ID des Men�eintrags ben�tigt der als Standard gesetzt ist
		$standard = '';
		try
		{
			$standard = SqlManager::GetInstance ()->SelectItem ( 'sysmenue', 'id', 'standard=1' );
		} catch ( SqlManagerException $ex )
		{
			Functions::Output_fehler ( $ex );
			return;
		}
		/*$query = "SELECT id FROM sysmenue WHERE standard='1'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 276.888 ');
		$daten = @mysql_result($insert, 0);*/
		
		//Anschliessend den Link erstellen
		$standardlink = functions::GetLink ( array ('sub' => $standard ) );
		
		//Pr�fen ob es sich beim ersten Element um die Startseite handelt
		if ($position [0] ['url'] == $standardlink)
		{
			//Dann diesen Eintrag entfernen damit es keine Eintr�ge ala: "Home -> Home" gibt
			unset ( $position [0] );
		}
		functions::Output_var ( 'homelink', $standardlink );
		functions::output_var ( 'position', $position );
	}
	
	/**
	 * Funktion mit der man sich den pos-strign einer menu-id zur�ckgeben lassen kann (z.B. pos=234|45|345)
	 *
	 * @param unknown_type $sub
	 * @return unknown
	 */
	public static Function PosVar($sub)
	{
		$position = array ();
		$vater = $sub; //Erster Vater ist $sub selbst, da erst noch name und vater von der aktuellen position geholt werden m�ssen
		$i = 0;
		
		while ( $vater != 0 ) //Solange vater != 0 weitermachen --> wenn vater = 0 heisst das, man ist auf der obersten stufe
		{
			//Für jede Stufe den vater und eintrag abfragen
			$daten = array ();
			try
			{
				$daten = SqlManager::GetInstance ()->Select ( 'sysmenue', array ('id', 'vater' ), 'id=' . $vater );
			} catch ( SqlManagerException $es )
			{
				Functions::Output_fehler ( $ex );
				return;
			}
			/*$query = "SELECT id, vater FROM sysmenue WHERE id = '$vater'";
			$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 276 ');
			$daten = mysql_fetch_assoc($insert);*/
			$vater = $daten [0] ['vater']; //Vater definieren f�r den n�chsten durchlauf
			$position [$i] = $daten [0] ['id']; //die sub ist die ID
			$i ++;
		}
		$position = array_reverse ( $position ); //Array umkehren da man sich ja von hinten nach vorne gearbeite hat
		

		$var = ''; //$var initialisieren
		for($i = 0; $i < count ( $position ); $i ++) //Hier wird noch die Position erarbeitet
		{
			$var .= '|' . $position [$i];
		}
		return $var;
	
	}
	
	/**
	 * Gibt die menu-id des Contents zur�ck
	 *
	 * @param int $Id
	 * @param mixed $switch
	 * @return mixed
	 */
	public static Function GetContentSub($Id, $switch)
	{
		switch ($switch)
		{
			//1: sub aus contentID
			case 1 :
				$query = "SELECT sysmenue.id FROM sysmenue, syscontent WHERE syscontent.id = '$Id' AND syscontent.id = sysmenue.contentId";
				break;
			//2: aus formId
			case 2 :
				$query = "SELECT sysmenue.id FROM sysmenue, SysContentForm WHERE SysContentForm.formId = '$Id' AND SysContentForm.contentId = sysmenue.contentId";
				break;
			default :
				die ( 'Fehler bei der Contentwahl! Fehlerhafter Parameter' );
				break;
		}
		
		$contentsub = '';
		try
		{
			$insert = SqlManager::GetInstance ()->Query ( $query );
			$contentsub = @mysql_result ( $insert, 0 );
		} catch ( SqlManagerException $ex )
		{
			Functions::Output_fehler ( $ex );
			return;
		}
		
		return $contentsub;
	}
	
	/**
	 * Gibt den namen des Icons f�r einen Dateityp zur�ck
	 *
	 * @param string $datei
	 * @return string
	 */
	public static Function DateTypBild($datei)
	{
		$temp = explode ( ".", basename ( $datei ) );
		switch ($temp [1])
		{
			case 'pdf' :
				$bild = 'pdf.gif';
				break;
			case 'xls' :
				$bild = 'xls.gif';
				break;
			case 'xlm' :
				$bild = 'xls.gif';
				break;
			case 'doc' :
				$bild = 'doc.gif';
				break;
			case 'dot' :
				$bild = 'doc.gif';
				break;
			case 'exe' :
				$bild = 'exe.gif';
				break;
			case 'rar' :
				$bild = 'rar.gif';
				break;
			case 'zip' :
				$bild = 'zip.gif';
				break;
			case 'tar' :
				$bild = 'rar.gif';
				break;
			case 'cab' :
				$bild = 'cab.gif';
				break;
			case 'txt' :
				$bild = 'txt.gif';
				break;
			case 'asp' :
				$bild = 'asp.gif';
				break;
			case 'js' :
				$bild = 'js.gif';
				break;
			case 'php' :
				$bild = 'php.gif';
				break;
			case 'dll' :
				$bild = 'dll.gif';
				break;
			case 'avi' :
				$bild = 'avi.gif';
				break;
			case 'gif' :
				$bild = 'gif.gif';
				break;
			case 'jpg' :
				$bild = 'jpg.gif';
				break;
			case 'jpeg' :
				$bild = 'jpg.gif';
				break;
			case 'fla' :
				$bild = 'fla.gif';
				break;
			case 'bmp' :
				$bild = 'bmp.gif';
				break;
			case 'html' :
				$bild = 'html.gif';
				break;
			case 'mdb' :
				$bild = 'mdb.gif';
				break;
			case 'mp3' :
				$bild = 'mp3.gif';
				break;
			case 'mpg' :
				$bild = 'mpg.gif';
				break;
			case 'swf' :
				$bild = 'swf.gif';
				break;
			case 'tif' :
				$bild = 'tif.gif';
				break;
			case 'tiff' :
				$bild = 'tif.gif';
				break;
			case 'ini' :
				$bild = 'ini.gif';
				break;
			case 'wav' :
				$bild = 'wav.gif';
				break;
			case 'png' :
				$bild = 'png.gif';
				break;
			default :
				$bild = 'file.gif';
				break;
		}
		return $bild;
	}
	
	/**
	 * Gibt einen Select f�r einen bestimmten action-type zur�ck
	 *
	 * @param int $type
	 * @param int $id
	 * @return mixed
	 */
	/*public static Function Actionselector($type,$id)
	{
		$return = '';
		$daten = array();
		try
		{
			$daten = SqlManager::GetInstance() -> Select('sysaction',array('id', 'name'),'type='. $type);
		}
		catch(SqlManagerException $es)
		{
			Functions::Output_fehler($ex);
			return;
		}
		
		for($i = 0; $i< count($daten); $i++)
		{
			if($daten[$i]['id'] == $id)
			{
				$return = $return . "<option value='". $daten[$i]['id'] ."' selected>". $daten[$i]['name'] ."</option>";
			}
			else
			{
				$return = $return . "<option value='". $daten[$i]['id'] ."'>". $daten[$i]['name'] ."</option>";
			}			
		}
		
	
		return $return;
	}*/
	
	/**
	 * Uploadad ein File das via Formular �bergeben wurde.
	 *
	 * @param string $wohin Zielordner
	 * @param string $name Name des eingabefeldes im Formular, standardm�sig file
	 * @todo  Filetype-Filter implementieren
	 * @return mixed
	 */
	public static Function Upload($wohin, $name = 'file', $type = 'all')
	{
		
		//Der Typ-Parameter definiert was für ein Typ die raufzuladende Datei haben muss
		switch ($type)
		{
			case 'images' :
				//Bilder
				break;
		}
		
		$size = $_FILES [$name] ['size'];
		if ($size > "20000000")
		{
			functions::output_fehler ( 'Fehler: Datei zu gross! Max. Grösse 1 MB!' );
			return false;
		}
		
		//VIelleicht soll der upload gelogt werden?
		if (functions::getINIparam ( 'Logg_upload' ) == 'on')
		{
			Logfile::log ( functions::getINIparam ( 'Logg_upload_ordner' ), 'File:' . $wohin . $_FILES [$name] ['name'] . '/Gr�sse: ' . $_FILES [$name] ['size'] . ' /User: ' . $_SESSION ['nick'] . '(' . $_SERVER ['REMOTE_ADDR'] . ')' );
		}
		//Sollte das File bereits existieren wird einfach noch eine Zahl vorne dran gesetzt.
		$i = 0;
		$tempname = $_FILES [$name] ['name'];
		
		while ( file_exists ( $wohin . $tempname ) )
		{
			$tempname = $i . '_' . $_FILES [$name] ['name'];
			$i ++;
		}
		
		$_FILES [$name] ['name'] = $tempname;
		
		move_uploaded_file ( $_FILES [$name] ['tmp_name'], $wohin . $_FILES [$name] ['name'] );
		if (file_exists ( $wohin . $_FILES [$name] ['name'] ))
		{
			return $wohin . $_FILES [$name] ['name'];
		} else
		{
			
			return false;
		}
	
	}
	
	/**
	 * Schickt dem Client eine Datei
	 *
	 * @param Pfad zu der Datei $pfad
	 * @param Boolean ob das Script mit die() abgebrochen wird nach dem senden (genutzt wenn zum senden ein neues Fenster ge�ffnet wird) $die
	 * @todo  Senden der Datei + alle orte an denen nun manuell gesendet wird auf das verlinken
	 */
	/*public static Function Download($pfad, $die = false)
	{
		if(functions::getINIparam('Logg_download') == 'on')
		{
			Logfile::log(functions::getINIparam('Logg_download_ordner'), 'File:' . $pfad . ' /IP: ' . $_SERVER['REMOTE_ADDR']);
		}

		//Hier nun Datei senden
		if(class_exists('HTTP_Download'))
		{
			$e = HTTP_Download::staticSend(array('file' => $pfad), true);
			if($e != '')
			{
				functions::output_fehler($e -> getMessage());
			}
			if($die)
			{
				die();
			}
		}
		else
		{
			functions::output_fehler('Klasse HTTP_Download von Pear ist nicht verf&uuml;gbar!');
		}
	}*/
	
	/**
	 * FUnktion mit derman Texte die mit htmlentities in "ungef�hrlichen" code umgewandelt wurden weiderherstellen kann
	 *
	 * @param string $_str Text
	 * @param boolean_type $_form False wenn aus Body true wenn aus Textbox
	 * @return string
	 */
	public static Function DecodeText($_str, $_form)
	{
		$trans_tbl = get_html_translation_table ( HTML_ENTITIES );
		$trans_tbl = array_flip ( $trans_tbl );
		$_str = strtr ( $_str, $trans_tbl );
		if ($_form)
		{
			$_nl = "\r\n";
		} else
		{
			$_nl = "<br>";
		}
		$_str = str_replace ( "#BR#", "$_nl", $_str );
		return ($_str);
	}
	
	/**
	 * Funktion um die Besucher(IP's) zu loggen
	 *
	 * @access 	public
	 * @return 	Boolean
	 *
	 */
	public static Function Logg_besucher()
	{
		
		$iniLog = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Logg/visitor' );
		if ($iniLog != 'on' || $_SESSION ['besucher_logged'] == true) //sollte dies wahr sein soll jeder besucher geloggt werden
		{
			return false;
		}
		
		$iniLog = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/loggVisitorFolder' );
		if (! Logfile::log ( functions::getINIparam ( 'Logg_besucher_ordner' ), USER_IP ))
		{
			functions::output_warnung ( 'Sie konnten nicht geloggt werden!' );
			return false;
		}
		$_SESSION ['besucher_logged'] = true;
		return true;
	}
	
	/**
	 * Funktion zum Entscheiden ob ein Radio Button auswahl von 2 St�ck mit values on off mit ja oder nein beantwortet wird
	 *
	 * @param string $param
	 * @param string $wert
	 */
	public static Function Yesno($param, $wert)
	{
		if ($wert == "on")
		{
			functions::output_var ( $param . "on", " checked" );
		} else
		{
			functions::output_var ( $param . "off", " checked" );
		}
	}
	
	/**
	 * Funktion die ein Select zur�ckgigbt mit dem Men�
	 *
	 * @param int $id
	 * @return string
	 */
	public static Function MenuSelect($id = "")
	{
		
		$menu = functions::createmenuselect ( 0, 0 );
		$option = "<option value=\"0\">Main</option>";
		
		for($i = 0; $i < count ( $menu ); $i ++)
		{
			$option .= "<option value=" . $menu [$i] ['row0'];
			if ($id == $menu [$i] ['row0'])
			{
				$option .= " selected>";
			} else
			{
				$option .= " >";
			}
			$option .= $menu [$i] ['rein'] . $menu [$i] ['row1'] . "</option>";
		}
		return $option;
	}
	
	/**
	 * Funktion erstellt Array mit Men�eintr�gen (Unterfunktion con menuselect())
	 *
	 * @param 	int 	$vater
	 * @param 	int 	$zahl
	 * @return 	array
	 *
	 * @todo Scheiss namen row0 etc. raus.
	 */
	public static Function CreateMenuSelect($vater, $zahl, $leer = '-&nbsp;', $all = false) //Men� zeichnen
	{ //Variabeln deklarieren
		if (! $all)
		{
			$filter = ' AND active=1 ';
		}
		static $ebene = 0; //Ebene der Rekursion
		static $j = 0; //Hochz�hl-Variable
		static $menue = array (); //Men�
		$j = $zahl; //Da j statisch ist hat man ein problem wenn man createmenuselect mehrmals aufruft da j dann immer weiter z�hlt und somit hat man ewig lange listen, darum muss am anfang immer j wieder zur�ckgesetzt werden
		//$query 			= "SELECT id, eintrag, vater FROM `sysmenue` WHERE `vater` = '$vater' $filter  ORDER BY folge ASC ";
		//$erg[$ebene] 	= @mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 134 ');
		

		try
		{
			$erg [$ebene] = SqlManager::GetInstance ()->Query ( 'SELECT id, name, parent FROM `sysmenue` WHERE `parent` = \'' . $vater . '\'' . $filter . ' ORDER BY `order` ASC ' );
		} catch ( Exception $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		$menge = @mysql_num_rows ( $erg [$ebene] );
		
		for($i = 0; $i < $menge; $i ++)
		{
			
			$row = @mysql_fetch_row ( $erg [$ebene] );
			$rein = '';
			if ($ebene > 0) //Einr�cken des Untermen�s
			{
				$rein = $rein . str_repeat ( $leer, $ebene );
			}
			//Werte an Smarty �bergeben
			$menue [$j] ['rein'] = $rein; //Einzug
			$menue [$j] ['row0'] = $row [0]; //ID (oder auch die sub die angegeben wird zum verlinken)
			$menue [$j] ['row1'] = $row [1]; //Name des Links
			

			//Neue Namen
			$menue [$j] ['name'] = $row [1];
			$menue [$j] ['id'] = $row [0];
			$j ++; //Rundlauf eins hochz�hlen
			$rein = ""; //Einzug leeren, damit das n�chste element, das evtl auf der gleichen ebene ist nicht weiter rein geschoben wird
			

			$subinsert = null;
			try
			{
				$subinsert = SqlManager::GetInstance ()->Query ( 'SELECT id FROM sysmenue WHERE parent = \'' . $row [0] . '\'' . $filter );
			} catch ( Exception $ex )
			{
				throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
			}
			
			if (mysql_num_rows ( $subinsert ) > 0)
			{
				$ebene ++;
				functions::createMenuSelect ( $row [0], $j, $leer, $all );
				$ebene --;
			}
		}
		
		return $menue; //Men� zur�ckgeben
	}
	
	/**
	 * Datum in Timestamp umwandeln -> Parameterformat:
	 *
	 * @param string $date
	 * @param int $format
	 * @return int
	 */
	public static Function DateToTimestamp($date, $format = 1)
	{
		switch ($format)
		{ //Format 01.01.1970 - 01:00:00
			case 1 :
				$date = explode ( " ", $date );
				$date [0] = trim ( $date [0] );
				$date [1] = trim ( $date [2] );
				
				$zeit = explode ( ":", $date [1] );
				$datum = explode ( ".", $date [0] );
				
				$time = @mktime ( $zeit [0], $zeit [1], $zeit [2], $datum [1], $datum [0], $datum [2] );
				
				return $time;
				break;
			//Format 01.01.1970
			case 2 :
				$date = trim ( $date );
				$datum = explode ( '.', $date );
				$time = @mktime ( 0, 0, 0, $datum [1], $datum [0], $datum [2] );
				return $time;
				break;
		}
	}
	
	/**
	 * Gibt das HTML zur�ck, welches f�r eine gecheckte bzw. nicht gecheckteCheckbox eingesetzt werden muss
	 *
	 * @param int $item 1 oder 0; 1 heisst die Checkbox ist gecheckt; 0 das gegenteil
	 * @return String
	 */
	/*public static Function CheckBoxChecker($item)
	{
		if($item == 1)
		{
			return " checked=\"checked\"";
		}
		else {
			return '';
		}
	}*/
	
	/**
	 * Funktion zum ersetzen aller schlechten W�rter wie z.B. "Arschloch"
	 *
	 * @param string $var
	 * @return string
	 * @todo Die Liste der W�rter aus einem File oder Datenbank auslesen
	 */
	public static Function Badword($var)
	{
		$file = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/badwordlist' );
		
		$bad = file ( $file );
		
		for($i = 0; $i < count ( $bad ); $i ++)
		{
			//Alle "schlechten" werte ersetzen durch Sterne
			$stern = str_repeat ( '*', strlen ( trim ( $bad [$i] ) ) );
			$var = str_ireplace ( trim ( $bad [$i] ), " " . $stern . " ", $var );
		}
		return $var;
	}
	
	/**
	 * Funktion mit der man aus der ID eines Benutzers dessen Nicknamen holen kann
	 *
	 * @param int $id
	 * @return string
	 */
	public static Function GetUserNameFromId($id)
	{
		try
		{
			return SqlManager::GetInstance ()->SelectItem ( 'sysuser', 'nick', 'id=\'' . $id . '\'' );
		} catch ( Exception $ex )
		{
			functions::Output_fehler ( 'Mysql-Error: nasdnnnnasdtqtwertzuiuzrtgbhrfgv' );
			return;
		}
	}
	
	/**
	 * Pr�ft Passwort auf gen�gende komplexit�t
	 *
	 * @param string $pw
	 * @return int
	 */
	public static Function CheckPwComplex($pw)
	{
		$einfach = array ("123456", "windows", "linux", "gott", "jesus", "passwort", "qwertzuiop", "asdfghjkl", "yxcvbnm", "asdfadsf", "sdfgsdfg", "lkjhlkjh", "norton", "office" );
		for($i = 0; $i < count ( $einfach ); $i ++)
		{
			if (strcasecmp ( $pw, $einfach [$i] ) == 0)
			{
				
				return 1;
			}
		}
		
		if (strlen ( $pw ) <= 5)
		{
			return 2;
		}
	
	}
	
	/**
	 * Erstellt ein Neues Passwort aus Zahlen und Buchstaben
	 *
	 * @param int $laenge l�nge des zu konstruierenden passworts
	 * @return string
	 */
	public static Function CreatePw($laenge)
	{
		for($i = 0; $i < $laenge; $i ++)
		{
			$abc = array ("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0" );
			$zufall = rand ( 0, 32 );
			$newpasswort .= $abc [$zufall];
		}
		return $newpasswort;
	}
	
	/**
	 * Funktion die eine eingabe "unsch�dlich" macht
	 *
	 * @param 	mixed 		$var 			die eingabe die gepr�ft werden soll
	 * @param 	boolean 	$html	true	es wird htmlspecialschars udn strip_tags auf die �bergabe angewendet
	 * false	es wird nichts gemacht mit dem string
	 * @param 	boolean 	$trim	true	es wird trim auf die �bergabe angewendet
	 * false 	es wird nichts gemacht mit dem string
	 * @return 	mixed	array				Es wird ein array zur�ckgegeben sollte eins als parameter �bergeben worden sein
	 * string				r�ckgabe ist ein string wenn ein string als parameter �bergeben wurde
	 */
	/*public static Function Cleaninput($var, $html = true, $trim = true)
	{
		//Es können auch arrays �bergeben werden, bei denen wird dan eifnach jeder eintrag nochmal mit der funktiona ufgerufen
		if(is_array($var))
		{
			foreach($var as $key => $value)
			{
				$var[$key] = functions::cleaninput($value, $html, $trim, $logg);
			}
			return $var;
		}

		if($trim)
		{
			$var = trim($var);
		}

		if($html)
		{
			$var = strip_tags($var);
		}

		return htmlspecialchars($var, ENT_QUOTES, 'utf-8');
	}*/
	
	/**
	 * Hiermit kann man einen Sleect erstellen, es muss nur die gew�nschte Tabelle angegeben werden und von welcher Spalte
	 *
	 * @param string 	$fremd_table 	Fremde Tabelle
	 * @param string 	$fremd_spalte 	Spalte ind er Fremden Tabelle
	 * @param int 		$selected 		evtl. vorselektierter eintrag
	 * @param string 	$value 			welcher spaltenwert soll im value-tag stehen?
	 * @param string 	$order			Nach was sortiert werden soll
	 * @return string
	 */
	public static Function Selector($fremd_table, $fremd_spalte, $selected = 0, $value = "id", $order = '')
	{
		if ($order != '')
		{
			$order = ' ORDER BY ' . $order;
		}
		
		$insert = array ();
		try
		{
			$insert = SqlManager::GetInstance ()->Query ( "SELECT id, $value, $fremd_spalte FROM $fremd_table " . $order );
		} catch ( SqlManagerException $es )
		{
			Functions::Output_fehler ( $ex );
			return;
		}
		
		//$query = "SELECT id, $value, $fremd_spalte FROM $fremd_table " . $order;
		//$insert = @mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 107 ');
		$return = '';
		while ( $daten = @mysql_fetch_assoc ( $insert ) )
		{
			if ($daten ['id'] == $selected)
			{
				$return = $return . "<option value='" . $daten [$value] . "' selected>" . $daten [$fremd_spalte] . "</option>";
			} else
			{
				$return = $return . "<option value='" . $daten [$value] . "'>" . $daten [$fremd_spalte] . "</option>";
			}
		}
		return $return;
	}
	
	/**
	 * Funktion zum �berpr�fen ob etwas abgelaufen ist gibt rue zur�ck wenn abgelaufen ansonsten false
	 *
	 * @param int $abgelaufen
	 * @return boolean
	 */
	public static Function CheckToOld($abgelaufen)
	{
		if ($abgelaufen < time ())
		{
			return true;
		} else
		{
			return false;
		}
	
	}
	
	/**
	 * Funktion um zu einer Sub die n�tigen Daten zu holen (template, klasse, ...)
	 *
	 * @return mixed
	 */
	
	public static Function Getsub()
	{
		$sub = functions::cleaninput ( $_REQUEST ['sub'] ); //Sub holen (eine zahl)
		if ($_REQUEST ['sub'] == '') //Sollte kein Sub gesestzt sein wird die seite die als standard gesetzt ist genommen
		{
			$query = "SELECT class, template, extern, phpfile, contentId FROM sysmenue WHERE standard = 1";
		} else //Ansonsten daten der gew�nschten sub holen
		{
			$openId = '';
			try
			{
				$openId = SqlManager::GetInstance ()->SelectItem ( 'sysmenue', 'openId', 'id=' . $sub );
			} catch ( SqlManagerException $es )
			{
				Functions::Output_fehler ( $ex );
				return;
			}
			/*$query = "SELECT openId FROM sysmenue WHERE id = ". $sub;
			$insert = @mysql_query($query) OR die('FEHLER: index.php -.- Fehler Nr: 104');
			$openId = @mysql_result($insert, 0);*/
			if ($openId != 0)
			{
				$sub = $openId;
			}
			
			$query = "SELECT class, template, extern, phpfile, contentId FROM sysmenue WHERE id = " . $sub;
		}
		
		$insert = '';
		try
		{
			$insert = SqlManager::GetInstance ()->Query ( $query );
		} catch ( SqlManagerException $es )
		{
			Functions::Output_fehler ( $ex );
			die ();
		}
		//$insert = @mysql_query($query) OR die('FEHLER: index.php -.- Fehler Nr: 101');
		if (@mysql_num_rows ( $insert ) == 0) //Gibt es kein Datensatz zur�ck existiert zu der Sub nicht sund das Script wird abgebrochen da das nicht sein darf!
		{
			//False zur�ckgeben und nicht mehr abbrechen damit man eine sch�ne Meldung ausgeben
			//kann, dass diese Seite nicht existiert!
			//die('FEHLER: index.php -.- Nr. 103');
			return false;
		} else //Ansonsten Daten in ein assoziatives array verpacken und zur�ckgeben
		{
			$daten = @mysql_fetch_assoc ( $insert );
			$daten ['sub'] = $sub;
			return $daten;
		}
	}
	
	/**
	 * Pr�ft ob eine IP gesperrt ist f�r die Seite
	 *
	 * @param unknown_type $testip
	 */
	public static function CheckIpGesperrt($testip = '')
	{
		if ($testip == '')
		{
			//Sollte keine IP �bergeben worden sein die des Besuchers auslesen
			$testip = $_SERVER ['REMOTE_ADDR'];
		}
		
		//File mit den IP's aus der Config auslesen
		$file = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/gesperrteIpsFile' );
		
		//Sollte das File nicht existieren sind alle f�r die Seite zugelassen
		if (! file_exists ( $file ))
		{
			return;
		}
		
		//Alle IP's aus dem File auslesen
		$ip = array ();
		$ip = file ( $file );
		foreach ( $ip as $ips )
		{
			//Sollte irgendeine IP da im file vorkommen dann ist diese gesperrt
			if ($ips == $testip)
			{
				//Script abbrechen und Fehler ausgeben das IP gesperrt ist
				$adminmail = functions::GetIniParam ( 'adminmail' );
				die ( 'Ihre IP wurde von dieser Homepage geblockt! Nehmen Sie Kontakt mit dem Administrator der Seite auf! (' . $adminmail . ')' );
			}
		}
	}
	
	/**
	 * Funktion die eine gecleante eingabe zur�ckgibt
	 *
	 * @param 	array 		$array		$_POST, $_GET, $_REQUEST, ...
	 * @param 	string 		$name		Schl�ssel im obigen array
	 * @param 	boolean 	$html		Sollen html-tags rausgefiltert werden
	 * @param 	boolean 	$trim		Soll der String getrimmt werden
	 * @param 	string 		$pattern	Es kann ein pattern �bergeben werden um den String zu pr�fen
	 * @return 	mixed 		false		Wenn $array kein array ist oder das pattern nicht passt
	 * string		gecleanten strings
	 * @todo  	Unterst�tzung des Regex
	 */
	/*public static Function GetVar($array, $name, $html = true, $trim = true, $pattern = "")
	{
		if(!array_key_exists($name, $array))
		{
			return false;
		}

		//if(!preg_match($pattern, $array[$name]))
		//{
		//	return false;
		//}


		return functions::Cleaninput($array[$name], $html, $trim);
	}*/
	
	/**
	 * Funktion um eine einzelne E-Mail zu versenden
	 *
	 * @param 	string $to
	 * @param 	string $betreff
	 * @param 	string $text
	 * @param 	string $anhang
	 */
	public static Function Sendmail($to, $betreff, $text, $anhang = '')
	{
		$crlf = "\n";
		$exhdrs = array ('From' => 'Tennisclub Grabs<mailer@tc-grabs.ch>', 'Subject' => $betreff );
		
		$mime = new Mail_mime ( $crlf );
		
		//$mime->setTXTBody ( $text );
		$mime -> setHTMLBody($text, false);
		//$mime->addAttachment ( $file, 'text/plain' );
		

		$body = $mime->get ();
		$hdrs = $mime->headers ( $exhdrs );
		
		$host = 'smtp.tc-grabs.ch';
		$username = 'mailer@tc-grabs.ch';
		$password = 'o32801ka$3';
		$mail = Mail::factory ( 'smtp', array ('host' => $host, 'auth' => true, 'username' => $username, 'password' => $password ) );
		
		$result = $mail->send ( $to, $hdrs, $body );
		
		if (PEAR::isError ( $result ))
		{
			MySmarty::GetInstance ()->OutputWarning ( $result->getMessage () );
		}
	}
	
	/**
	 * Funktion mit der man einen Datensatz in den Papierkorb(1) oder ind as Archiv(2) verschieben kann
	 *
	 * @param int $id
	 * @param string $table
	 * @param int $wohin 1=Papierkorb 2=archiv(Ausgeschaltet!)
	 */
	public static Function SwitchDatensatz($id, $table, $wohin)
	{
		if (! functions::zusammenhang ( $table, $id ))
		{
			return false;
		}
		
		$daten = array ();
		
		try
		{
			$daten = SqlManager::GetInstance ()->SelectRow ( $table, $id );
		} catch ( Exception $ex )
		{
			functions::Output_fehler ( 'Mysql-Error: llaoiuhqwjezgasdf' );
			return;
		}
		
		for($i = 0; $i < (count ( $daten ) / 2); $i ++) //hier werden dann alle Daten zusammengef�gt, zwischen den einzelnen spalten hats ein _ damit man sie wieder auseinandernehmen kann
		{ // Endformat: '1'_'titel'_'text'
			if ($value == "")
			{
				$value = $daten [$i];
			} else
			{
				$value = $value . "~" . $daten [$i];
			}
		}
		$time = time (); //Zeit an der der Datensatz in den Papierkorb verschoben wurde damit man Datens�tze �lter als 1 woche automatisch l�schen k�nnte
		

		if ($wohin == 1)
		{ //Datensatz im Papierkorb einf�gen
			$query = "INSERT INTO syspapierkorb VALUES ('', '$table', '$value', '$time')";
		} elseif ($wohin == 2)
		{ //Datensatz kommt ins archiv
			if (isset ( $_SESSION ['userId'] ))
			{
				$user = $_SESSION ['userId'];
			} else
			{
				$user = 0;
			}
			$query = "INSERT INTO archiv VALUES ('', '$table', '$value', '$time', '$user')";
		} else
		{
			die ( 'Falsche Parameter! - Error-Code: 65tzhjnkf' );
		}
		
		try
		{
			SqlManager::GetInstance ()->Query ( $query );
			if ($insert == true)
			{ //Soltle das obige gelungen sein Datensatz aus der alten Tabelle l�schen
				return;
			}
			SqlManager::GetInstance ()->DeleteById ( $table, $id );
		
		} catch ( Exception $ex )
		{
			functions::Output_fehler ( 'Mysql-Error: akjsjdjasdbzrtzutzhbtgbqypl,m' );
			return;
		}
		
		if ($wohin == 1)
		{
			functions::output_bericht ( 'Der Datensatz wurde in den Papierkorb verschoeben!' );
		} else
		{
			functions::output_bericht ( 'Der Datensatz wurde in das Archiv verschoeben!' );
		}
	
	}
	
	/**
	 * Funktion um den Status von aktiv auf inaktiv und umgekehrt zu setzen
	 *
	 * @param string $table
	 * @param int $id
	 * @return boolean
	 */
	public static Function SwitchActive($table, $id = '')
	{
		$status = 0;
		try
		{
			$status = SqlManager::GetInstance ()->SelectItem ( $table, 'active', 'id=\'' . $id . '\'' );
			if ($status == 1)
			{
				$status = 0;
			} else
			{
				$status = 1;
			}
			SqlManager::GetInstance ()->Update ( $table, array ('active' => $status ), 'id=\'' . $id . '\'' );
		} catch ( Exception $ex )
		{
			throw new CMSException ( 'Fehler beim Switch-Update', CMSException::T_SYSTEMERROR, $ex );
		}
	}
	
	/**
	 * Funktion mit der man Javascripts bei laden der seite laufen lassen kann daf�r ms�ssen die scripts erst mit ImportJS() reingeholt werden!!
	 *
	 * @param string $function Javascript Funktionsname
	 */
	/*public static Function RunJs($function)
	{
		static $runjs;
		$runjs[] = $function;
		functions::output_var('runatstart', $runjs);
	}*/
	
	/**
	 * Funktion mit der man die Newsfeed neu erstellen kann
	 *
	 * @return boolean true wenns geklappt hat sonst false
	 */
	public static Function CreateNewsfeed()
	{
		$newsfeed = new mynewsfeed ();
		$config = Cms::GetInstance ()->GetConfiguration ();
		$newsfeed->setEnddir ( $config->Get ( 'newsfeed/path' ) );
		$newsfeed->setHeadDescription ( $config->Get ( 'newsfeed/description' ) );
		$newsfeed->setHeadImage ( $config->Get ( 'newsfeed/image' ) );
		$newsfeed->setHeadLanguage ( $config->Get ( 'newsfeed/language' ) );
		$newsfeed->setHeadTitel ( $config->Get ( 'newsfeed/title' ) );
		$newsfeed->setHeadLink ( $config->Get ( 'newsfeed/link' ) );
		
		if (! @$newsfeed->create ())
		{
			throw new CMSException ( 'Fehler beim erstellen des Newsfeeds', CMSException::T_WARNING );
		}
		return true;
	}
	
	/**
	 * Funktion mit der man CSS-Dateien importieren kann
	 *
	 * @var string $datei Dateiname inklusiv pfad-Angabe
	 */
	public static Function ImportCSS($datei)
	{
		static $cssfiles;
		$cssfiles [] = $datei;
		functions::output_var ( 'opendix_css', $cssfiles );
	}
	
	/**
	 * Funktion um den Newsflash zu auszugeben
	 *
	 */
	public static Function News()
	{
		//Variabeln initialisieren
		$news = '';
		$i = 0;
		$insert = null;
		
		//Abfrage der Daten
		try
		{
			$insert = SqlManager::GetInstance ()->Query ( 'SELECT time, title, url, linkType, filebaseId FROM modnews WHERE active = 1  ORDER BY time DESC' );
		} catch ( Exception $ex )
		{
			functions::Output_fehler ( 'Mysql-Error: ajsdrftghjtghqw4415155151' );
			return;
		}
		
		while ( $daten = mysql_fetch_assoc ( $insert ) )
		{
			
			$news [$i] ['title'] = $daten ['title'];
			$news [$i] ['start'] = @date ( 'd.m.Y - H:i', $daten ['time'] );
			if ($daten ['linkType'] == 2)
			{
				//$news[$i]['url']	= functions::getLink(Functions::SplitHref($daten['url']));
				$news [$i] ['url'] = $daten ['url'];
			} elseif ($daten ['linkType'] == 3)
			{
				$news [$i] ['extern'] = true;
				$news [$i] ['url'] = $daten ['url'];
			} elseif ($daten ['linkType'] == 4)
			{
				
				//Verlinkung auf Filebase
				if (is_numeric ( $daten ['filebaseId'] ))
				{
					try
					{
						//Pfad überprüfen ob Datei noch existiert
						$path = Filebase::getFilePath ( $daten ['filebaseId'] );
						$filebasePath = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/filebaseFolder' );
						if (file_exists ( $filebasePath . $path ))
						{
							//isset Prüfung damit keine PHP-Notice geworfen wird
							if (! isset ( $_GET ['sub'] ))
							{
								$_GET ['sub'] = '';
							}
							$news [$i] ['url'] = functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'getDownload', 'dId' => $daten ['filebaseId'] ) );
						}
					} catch ( Exception $ex )
					{
					}
				
				}
			
			}
			
			$i ++;
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'news', $news );
	
		//functions::output_var('opendix_news', $news);
	}
	
	/**
	 * Formatiert eine URL
	 *
	 * @param String $url	Die zu formatierende URL
	 * @return String Formatierte URL
	 */
	public static function FormatUrl($url)
	{
		if ($url == '')
		{
			return '';
		}
		if (substr ( $url, 0, 7 ) != 'http://')
		{
			$url = 'http://' . $url;
		}
		return $url;
	}
	
	/**
	 * Entfernt die angegebenen Tags
	 *
	 * @param String $text
	 * @param Array $tags
	 * @return String
	 */
	public static function StripSelectedTags($text, $tags = array())
	{
		$args = func_get_args ();
		$text = array_shift ( $args );
		$tags = func_num_args () > 2 ? array_diff ( $args, array ($text ) ) : ( array ) $tags;
		foreach ( $tags as $tag )
		{
			if (preg_match_all ( '/<' . $tag . '[^>]*>(.*)<\/' . $tag . '>/iU', $text, $found ))
			{
				$text = str_replace ( $found [0], $found [1], $text );
			}
		}
		
		return $text;
	}
	
	public static function Br2nl($text)
	{
		/* Remove XHTML linebreak tags. */
		$text = str_replace ( "<br />", "", $text );
		/* Remove HTML 4.01 linebreak tags. */
		$text = str_replace ( "<br>", "", $text );
		/* Return the result. */
		return $text;
	}
	
	/**
	 * Pr�ft ob keine Newseintr�ge zu alt sind
	 *
	 */
	public static Function NewsToOld()
	{
		$insert = null;
		try
		{
			$insert = SqlManager::GetInstance ()->Query ( 'SELECT expire, id FROM  modnews' );
		} catch ( Exception $ex )
		{
			functions::Output_fehler ( 'Mysql-Error: lkjhagsdfiuh97234243e' );
			return;
		}
		
		while ( $daten = mysql_fetch_assoc ( $insert ) )
		{
			if ($daten ['expire'] < time ())
			{
				try
				{
					SqlManager::GetInstance ()->Delete ( 'modnews', $daten ['id'] );
				} catch ( Exception $ex )
				{
					functions::Output_fehler ( 'Mysql-Error: ashdfhlasdfhlhahreufhwkerhftwkehrtqqq22323' );
					return;
				}
			}
		}
	}
	
	/**
	 * Funktion zum erzeugen eines PDF's
	 *
	 * @param string $html
	 * @param string $pdfname
	 * @return boolean
	 */
	/*public static Function  CreatePDF($html, $pdfname)
	{
		$pdf=new PDF("P", "mm", "A4");

		$pdf->SetTitle($pdfname);
		$pdf->AliasNbPages();
		$pdf->Open();
		$pdf->SetMargins(25,25,25);
		$pdf->AddPage();
		$pdf->WriteHTML($html);
		$pdf->Output(MEDIEN . 'pdf/'. $pdfname .'.pdf');
		return true;
	}*/
	
	/**
	 * Funktion die Zusammenh�nge �berpr�fen kann, also ob z.b. ein Datensatz gel�scht werden kann
	 *
	 * @param string $table
	 * @param string $wert
	 * @return boolean
	 */
	public static Function Zusammenhang($table, $wert)
	{
		if ($table == '' || $wert == '')
		{
			functions::output_fehler ( 'Falsche Parameterzahl fuer Zusammenhang' );
			return false;
		}
		
		$insert = null;
		try
		{
			$insert = SqlManager::GetInstance ()->Query ( 'SELECT id, tabelle2, spalte2, error FROM sysknopf WHERE tabelle1 = \'' . $table . '\'' );
		} catch ( Exception $ex )
		{
			functions::Output_fehler ( 'Mysql-Error: hkjhjhjhjhuuuuu' );
			return;
		}
		
		while ( $daten = @mysql_fetch_assoc ( $insert ) )
		{
			$subinsert = null;
			try
			{
				$subinsert = SqlManager::GetInstance ()->Query ( 'SELECT id FROM \'' . $daten ['tabelle2'] . '\' WHERE \'' . $daten ['spalte2'] . '\' = \'' . $wert . '\'' );
			} catch ( Exception $ex )
			{
				functions::Output_fehler ( 'Mysql-Error: njnnjnjnjjnnjjnjnjjn' );
				return;
			}
			
			if (@mysql_num_rows ( $subinsert ) > 0)
			{
				functions::output_fehler ( $daten ['error'] );
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Funktion zum ausw�hlen eines templates
	 *
	 * @return string
	 */
	/*public static Function SelectTemplate()
	{
		if(isset($_SESSION['nick']))
		{
			$insert = null;
			try
			{
				$insert = SqlManager::GetInstance() -> Query('SELECT ordner FROM sysuser b, systemplate s WHERE b.template = s.id');
			}
			catch (Exception $ex)
			{
				functions::Output_fehler('Mysql-Error: asdfghjzhjntgb  bgb');
				return;
			}

			if(mysql_num_rows($insert) != 0)
			{
				return mysql_result($insert, 0);
			}
		}

		$insert = null;
		try
		{
			$insert = SqlManager::GetInstance() -> Query('SELECT ordner FROM systemplate WHERE standard = 1');
		}
		catch (Exception $ex)
		{
			functions::Output_fehler('Mysql-Error: hhhh');
			return;
		}

		if(@mysql_num_rows($insert) == 0)
		{
			die('Es sind keine Templates vorhanden!');
		}
		else
		{
			return mysql_result($insert, 0);
		}

	}*/
	
	/**
	 * Funktion um Zip_fileszu erstellen
	 *
	 * @param mixed $files array mit den verschiedenen fiels die ins zip sollen
	 * @param string $name zip-file name
	 * @return string
	 */
	/*public static Function CreateZip($files, $name)
	{
		// Create new zip file in the directory below the current one
		if(file_exists($name))
		{
			$name = explode('.', $name);
			$name = $name[0] . functions::createpw(3) . '.'.$name[1];
		}
		$zip = new zip_file($name);
		$zip->set_options(array('recurse' => 0, 'storepaths' => 0));
		for($i=0; $i<count($files); $i++)
		{
			if(file_exists($files[$i]))
			{
				$zip->add_files($files[$i]);
			}
		}
		// Create archive in memory
		$zip->create_archive();

		return $name;
	}*/
	
	/**
	 * Erstellt ein Tar
	 *
	 * @param unknown_type $files
	 * @param unknown_type $name
	 * @return unknown
	 */
	/*public static Function CreateTar($files, $name)
	{
		// Create new zip file in the directory below the current one
		if(file_exists($name))
		{
			$name = explode('.', $name);
			$name = $name[0] . functions::createpw(3) . '.'.$name[1];
		}
		$zip = new tar_file($name);
		$zip->set_options(array('recurse' => 0, 'storepaths' => 0));
		for($i=0; $i<count($files); $i++)
		{
			if(file_exists($files[$i]))
			{
				$zip->add_files($files[$i]);
			}
		}
		// Create archive in memory
		$zip->create_archive();

		return $name;
	}*/
	
	/**
	 * Funktion um Dateigr�ssen in der gew�nschten gr�ssen anzugeben
	 *
	 * @param int $size gr�sse der Datei in Bytes
	 * @return string
	 */
	public static Function GetSize($size)
	{
		$k = 0;
		$groessen = array (' Bytes', ' KB', ' MB', ' GB' );
		while ( $size > 1023 )
		{
			$size /= 1024;
			$k ++;
		}
		
		$size = round ( $size, 2 ); //Runden damit es nicht heisst: 23.98691726397  MB
		return $size .= $groessen [$k];
	}
	
	/**
	 * Funktion die alle Berichte �berpr�ft ob sie abgelaufen sind oder ob sie nun online gehen
	 *
	 */
	public static Function CheckBerichtAlter()
	{
		//Jetztige Zeit
		$time = time ();
		
		//Erst werden alle Berichte auf aktiv gestellt, die noch nicht sidn aber �ber dem timer_start und vor dem timers top sind
		$insert = null;
		try
		{
			$insert = SqlManager::GetInstance ()->Query ( 'SELECT id, activ FROM modbericht WHERE timer_start < \'' . $time . '\' AND timer_stop > \'' . $time . '\' AND timer_activ = \'1\'' );
		} catch ( Exception $ex )
		{
			functions::Output_fehler ( 'Mysql-Error: 309' );
			return;
		}
		
		while ( $daten = mysql_fetch_assoc ( $insert ) )
		{
			//Sollte hier aktiv noch auf 0 sein wird er online gebracht.
			if ($daten ['activ'] != 1)
			{
				//Bericht online stellen
				try
				{
					SqlManager::GetInstance ()->Update ( 'modbericht', array ('activ' => '1' ), 'id=\'' . $daten ['id'] . '\'' );
				} catch ( Exception $ex )
				{
					functions::Output_fehler ( 'Mysql-Error: 310' );
					return;
				}
			}
		} //End while
		

		//Dann werden noch berichte, die �ber dem stop-datum sind bearbeitet
		try
		{
			$insert = SqlManager::GetInstance ()->Query ( 'SELECT id, timer_action FROM modbericht WHERE timer_activ = \'1\' AND timer_stop < \'' . $time . '\'' );
		} catch ( Exception $ex )
		{
			functions::Output_fehler ( 'Mysql-Error: 311' );
			return;
		}
		
		while ( $daten = mysql_fetch_assoc ( $insert ) )
		{
			//Hier werden nun alle Berichte mit der gew�nschten Actio bearbeitet!
			functions::runAction ( $daten ['timer_action'], $daten ['id'] );
		}
	
	} //End function checkBerichtAlter()
	

	/**
	 * Funktion mit der man XML-Files erstellen kann
	 *
	 * @param 	array 	$data			array mit den daten
	 * @param 	string 	$speicherort	Speicherplatz des XML-Files
	 * @param 	string 	$rootname		Name des Root-Verzeichnis
	 * @return 	boolean
	 */
	public static Function CreateXMLfile($data, $speicherort, $rootname)
	{
		
		$options = array ('addDecl' => TRUE, 'encoding' => 'ISO-8859-1', 'indent' => '  ', 'rootName' => $rootname, 'defaultTagName' => 'item' );
		// create object
		$serializer = new XML_Serializer ( $options );
		
		// perform serialization
		$result = $serializer->serialize ( $data );
		
		// check result code and display XML if success
		if ($result !== true)
		{
			return false;
		
		//echo $serializer->getSerializedData();
		}
		
		if (! file_exists ( $speicherort ))
		{
			functions::createfile ( $speicherort );
		}
		file_put_contents ( $speicherort, $serializer->getSerializedData () );
		
		return true;
	}
	
	/**
	 * Weil das sonst nicht richtig hinhaut mit der php-funktion
	 *
	 * @param 	string $filename Dateiname
	 * @return 	unknown
	 */
	public static Function Mime_content_type_($filename)
	{
		$mime = array (".htm" => "application/xhtml+xml", ".3dm" => "x-world/x-3dmf", ".3dmf" => "x-world/x-3dmf", ".ai" => "application/postscript", ".aif" => "audio/x-aiff", ".aifc" => "audio/x-aiff", ".aiff" => "audio/x-aiff", ".au" => "audio/basic", ".avi" => "video/x-msvideo", ".bcpio" => "application/x-bcpio", ".bin" => "application/octet-stream", ".cab" => "application/x-shockwave-flash", ".cdf" => "application/x-netcdf", ".chm" => "application/mshelp", ".cht" => "audio/x-dspeeh", ".class" => "application/octet-stream", ".cod" => "image/cis-cod", ".com" => "application/octet-stream", ".cpio" => "application/x-cpio", ".csh" => "application/x-csh", ".css" => "text/css", ".csv" => "text/comma-separated-values", ".dcr" => "application/x-director", ".dir" => "application/x-director", ".dll" => "application/octet-stream", ".doc" => "application/msword", ".dot" => "application/msword", ".dus" => "audio/x-dspeeh", ".dvi" => "application/x-dvi", ".dwf" => "drawing/x-dwf", ".dwg" => "application/acad", ".dxf" => "application/dxf", ".dxr" => "application/x-director", ".eps" => "application/postscript", ".es" => "audio/echospeech", ".etx" => "text/x-setext", ".evy" => "application/x-envoy", ".exe" => "application/octet-stream", ".fh4" => "image/x-freehand", ".fh5" => "image/x-freehand", ".fhc" => "image/x-freehand", ".fif" => "image/fif", ".gif" => "image/gif", ".gtar" => "application/x-gtar", ".gz" => "application/gzip", ".hdf" => "application/x-hdf", ".hlp" => "application/mshelp", ".hqx" => "application/mac-binhex40", ".htm" => "text/html", ".html" => "text/html", ".ief" => "image/ief", ".jpeg" => "image/jpeg", ".jpe" => "image/jpeg", ".jpg" => "image/jpeg", ".js" => "text/javascript", ".latex" => "application/x-latex", ".man" => "application/x-troff-man", ".mbd" => "application/mbedlet", ".mcf" => "image/vasa", ".me" => "application/x-troff-me", ".mid" => "audio/x-midi", ".midi" => "audio/x-midi", ".mif" => "application/mif", ".mov" => "video/quicktime", ".movie" => "video/x-sgi-movie", ".mp2" => "audio/x-mpeg", ".mpe" => "video/mpeg", ".mpeg" => "video/mpeg", ".mpg" => "video/mpeg", ".nc" => "application/x-netcdf", ".nsc" => "application/x-nschat", ".oda" => "application/oda", ".pbm" => "image/x-portable-bitmap", ".pdf" => "application/pdf", ".pgm" => "image/x-portable-graymap", ".php" => "application/x-httpd-php", ".phtml" => "application/x-httpd-php", ".png" => "image/png", ".pnm" => "image/x-portable-anymap", ".pot" => "application/mspowerpoint", ".ppm" => "image/x-portable-pixmap", ".pps" => "application/mspowerpoint", ".ppt" => "application/mspowerpoint", ".ppz" => "application/mspowerpoint", ".ps" => "application/postscript", ".ptlk" => "application/listenup", ".qd3" => "x-world/x-3dmf", ".qd3d" => "x-world/x-3dmf", ".qt" => "video/quicktime", ".ram" => "audio/x-pn-realaudio", ".ra" => "audio/x-pn-realaudio", ".ras" => "image/cmu-raster", ".rgb" => "image/x-rgb", ".roff" => "application/x-troff", ".rpm" => "audio/x-pn-realaudio-plugin", ".rtf" => "application/rtf", ".rtf" => "text/rtf", ".rtx" => "text/richtext", ".sca" => "application/x-supercard", ".sgm" => "text/x-sgml", ".sgml" => "text/x-sgml", ".sh" => "application/x-sh", ".shar" => "application/x-shar", ".shtml" => "text/html", ".sit" => "application/x-stuffit", ".smp" => "application/studiom", ".snd" => "audio/basic", ".spc" => "text/x-speech", ".spl" => "application/futuresplash", ".spr" => "application/x-sprite", ".sprite" => "application/x-sprite", ".src" => "application/x-wais-source", ".stream" => "audio/x-qt-stream", ".sv4cpio" => "application/x-sv4cpio", ".sv4crc" => "application/x-sv4crc", ".swf" => "application/x-shockwave-flash", ".t" => "application/x-troff", ".talk" => "text/x-speech", ".tar" => "application/x-tar", ".tbk" => "application/toolbook", ".tcl" => "application/x-tcl", ".tex" => "application/x-tex", ".texinfo" => "application/x-texinfo", ".texi" => "application/x-texinfo", ".tif" => "image/tiff", ".tiff " => "image/tiff", ".trtc" => "application/rtc", ".trtc" => "application/x-troff", ".tsi" => "audio/tsplayer", ".tsp" => "application/dsptype", ".tsv" => "text/tab-separated-values", ".txt" => "text/plain", ".ustar" => "application/x-ustar", ".viv" => "video/vnd.vivo", ".vivo" => "video/vnd.vivo", ".vmd" => "application/vocaltec-media-desc", ".vmf" => "application/vocaltec-media-file", ".vox" => "audio/voxware", ".vcs" => "text/plain", ".vcf" => "text/plain", ".vts" => "workbook/formulaone", ".vtts" => "workbook/formulaone", ".wav" => "audio/x-wav", ".wbmp" => "image/vnd.wap.wbmp", ".wml" => "text/vnd.wap.wml", ".wmlc" => "application/vnd.wap.wmlc", ".wmls" => "text/vnd.wap.wmlscript", ".wmlsc" => "application/vnd.wap.wmlscriptc", ".wrl" => "model/vrml", ".wrl" => "x-world/x-vrml", ".xbm" => "image/x-xbitmap", ".xhtml" => "application/xhtml+xml", ".xla" => "application/msexcel", ".xls" => "application/msexcel", ".xml" => "text/xml", ".xpm" => "image/x-xpixmap", ".xwd" => "image/x-windowdump", ".z" => "application/x-compress", ".zip" => "application/zip", ".ttf" => "font/ttf", '.svn-base' => 'text/plain' );
		
		$filename = substr ( $filename, strripos ( $filename, '\\' ) );
		
		if (! strstr ( $filename, '.' ))
		{
			return "text/plain";
		}
		
		return $mime [strtolower ( strrchr ( $filename, '.' ) )];
	}
	
	/**
	 * FUnktion die das komplette ini-file neu schreibt, als assoc_array m�ssen alle parameter im ini-file �bergeben werden!!
	 *
	 * @param 	string $path
	 * @param 	array $assoc_array
	 * @return 	boolean
	 */
	/*public static Function Write_ini_file($assoc_array)
	{
		foreach ($assoc_array as $key => $item)
		{
			if (is_array($item))
			{
				$content .= "\n[$key]\n";
				foreach ($item as $key2 => $item2)
				{
					$content .= "$key2 = \"$item2\"\n";
				}
			}
			else
			{
				$content .= "$key = \"$item\"\r\n";
			}
		}
		if(!file_put_contents(INIFILE, $content))
		{
			return false;
		}

		return true;
	}*/
	
	/**
	 * Funktion die einen String mit Datum / Zeit zur�ckgibt
	 *
	 * @return string
	 *
	 */
	public static Function GetTimeString($format = 'd.m.Y - H:i:s', $timestamp = null)
	{
		if ($timestamp === null)
		{
			$timestamp = time ();
		}
		switch (date ( "l" ))
		{
			case "Monday" :
				$timestring = "Montag";
				break;
			case "Tuesday" :
				$timestring = "Dienstag";
				break;
			case "Wednesday" :
				$timestring = "Mittwoch";
				break;
			case "Thursday" :
				$timestring = "Donnerstag";
				break;
			case "Friday" :
				$timestring = "Freitag";
				break;
			case "Saturday" :
				$timestring = "Samstag";
				break;
			case "Sunday" :
				$timestring = "Sonntag";
				break;
			default :
				$timestring = "";
				break;
		}
		
		return $timestring . ", " . date ( "d.m.Y - H:i:s", $timestamp );
	}
	
	/**
	 * Gibt die Select-Optionen f�r einen Ordner zur�ck (Alle Dateien als Select)
	 *
	 * @param String $folder	Ordner mit den Files
	 * @param String $selected 	Vorselektiertes File (fals vorhanden)
	 * @param array $filter		Erlaubte Dateiendungen, ohne => alles erlaubt
	 */
	public static function GetFileSelect($folder, $selected = '', $filter = array())
	{
		if (! file_exists ( $folder ))
		{
			Functions::Output_warnung ( 'Files k�nnen nicht aufgelistet werden. Ordner nicht vorhanden!(' . $folder . ')' );
			return;
		}
		
		$files_all = scandir ( $folder );
		for($i = 0; $i < count ( $files_all ); $i ++)
		{
			//Prüfen ob es sich um ein Filehandelt
			if (! is_file ( TEMPLATEDIR . $files_all [$i] ))
			{
				continue;
			}
			
			//Prüfen ob der Dateityp gebraucht werden kann
			if (count ( $array ) != 0 && ! in_array ( substr ( $files_all [$i], strrpos ( $files_all [$i], '.' ) ), $filter ))
			{
				continue;
			}
			
			//Hinzufügen zu Rückgabe
			if ($files_all [$i] == $selected)
			{
				
				$files .= "<option value='{$files_all[$i]}' selected=\"selected\">{$files_all[$i]}</option>";
			} else
			{
				$files .= "<option value='{$files_all[$i]}'>{$files_all[$i]}</option>";
			}
		
		}
		return $files;
	}
	
	/**
	 * Gibt den Link zurück
	 *
	 * @param 	Array 	$params
	 * @param   Boolean $addparams
	 * @return 	String
	 */
	public static Function GetLink($params, $addparams = false, $not = array())
	{
		
		$modRewrite = CMs::GetInstance ()->GetConfiguration ()->__get ( 'General/modrewrite' );
		if ($modRewrite == 'on')
		{
			return Functions::GetModRewriteLink ( $params, $addparams, $not );
		} else
		{
			return Functions::GetNormalLink ( $params, $addparams, $not );
		}
	}
	
	/**
	 * Pr�ft ob dem User eine Datei gesendet werden soll
	 *
	 * Wird als Boot-Action ausgeführt; Parameter können der URL mitgegeben werden
	 * Damit die Funktion eine Datei sendet müssen folgende Angaben gemacht werden:
	 * action=getDownload
	 * dId=Id des Downloads in der Filebase
	 *
	 * Beispiel:
	 * http://www.scgams.ch/Skiclub-Gams-action_getDownload-dId_2.html
	 *
	 * Hier wird das File mit der ID 2 aus der Filebase an den User gesendet
	 *
	 */
	public function GetDownload()
	{
		//Pr�fen ob es sich �berhaupt um einen download handeln kann
		//Daf�r m�ssen in der url die werte action und dId gesetzt sein
		if (! isset ( $_GET ['action'] ) || ! isset ( $_GET ['dId'] ))
		{
			//wenn nicht dann gleich hier abbrechen
			return;
		}
		
		//Informationen aus der URL auslesen
		$action = functions::getVar ( $_GET, 'action' );
		$id = functions::getVar ( $_GET, 'dId' );
		
		//Prüfen ob die Werte den gewünschten für einen Download entsprechen
		//für action muss getDownload angegeben werden und die ID muss eine zahl sein
		if ($action != 'getDownload' || ! is_numeric ( $id ))
		{
			//wenn das nicht der Fall ist dann gleich abbrechen
			return;
		}
		
		try
		{
			//Abfrage des Pfades zu der Datei die gewünscht wird
			$file = FILEBASE . Filebase::getFilePath ( $id );
		} catch ( Exception $ex )
		{
			functions::Output_fehler ( $ex->getMessage () );
		}
		
		//Filebase::getFileById($id) gibt false zurück wenn die Datei nicht in der DB gefunden wurde
		if (! $file)
		{
			//dann Warnung ausgeben und abbrechen
			functions::output_warnung ( 'Es wurde keine Datei zu diesem File gefunden' );
			return;
		}
		
		//Datei als Download an den User senden
		Functions::Download ( $file );
	}
	
	/**
	 * Gibt den Link f�r Mod_Rewrite zur�ck
	 *
	 * @param 	Array $params
	 * @return 	String
	 *
	 * @example Die Funktion ersteltl einen Link: http://www.scgams.ch/956/Agenda/action-add/id-5/Skiclub-Gams.html
	 */
	public static function GetModRewriteLink($params, $addparams = false, $not = array())
	{
		if (! is_array ( $params ))
		{
			return false;
		}
		
		//Variabel $link deklarieren
		$link = Functions::GetIniParam ( 'modrewritelink' );
		
		//Sollte dies Boolean gesetzt sein, dass Array $_GET hinzuf�gen
		if ($addparams)
		{
			$params = @array_merge ( $_GET, $params );
		}
		
		//Zu erst kommt der Parameter sub:
		if (array_key_exists ( 'sub', $params ))
		{
			$link .= $params ['sub'];
			unset ( $params ['sub'] );
		}
		
		//Dann alf$llige Actions
		if (array_key_exists ( 'action', $params ) && ! in_array ( 'action', $not ))
		{
			if ($params ['action'] != '')
			{
				$link .= '_action-' . $params ['action'];
			}
			
			unset ( $params ['action'] );
		}
		
		//Nun noch die ganzen restlichen Parameter anh�ngen
		foreach ( $params as $key => $value )
		{
			if ($value == '' || in_array ( $key, $not ))
			{
				continue;
			}
			$link .= '_' . $key . '-' . $value;
		}
		
		if ($link == Functions::GetIniParam ( 'modrewritelink' ))
		{
			$link = substr ( Functions::GetIniParam ( 'modrewritelink' ), 0, strlen ( $link ) - 1 );
		}
		return $link . '.html';
	}
	
	/**
	 * Gibt den normalen Link zur�ck
	 *
	 * @param 	Array $params
	 * @return 	String
	 */
	public static Function GetNormalLink($params, $addparams = false, $not = array())
	{
		if (! is_array ( $params ))
		{
			return false;
		}
		
		//Sollte dies Boolean gesetzt sein, dass Array $_GET hinzuf�gen
		if ($addparams)
		{
			$params = @array_merge ( $_GET, $params );
		}
		
		//Variabel $link deklarieren
		$link = 'index.php?';
		
		//Zu erst kommt der Parameter sub:
		if (array_key_exists ( 'sub', $params ))
		{
			$link .= 'sub=' . $params ['sub'];
			unset ( $params ['sub'] );
		} else
		{
			//Sollte kein sub definiert sein einfach ein leeres anh�ngen
			$link .= 'sub=';
		}
		
		//Dann alf$llige Actions
		if (array_key_exists ( 'action', $params ))
		{
			if ($params ['action'] != '')
			{
				$link .= '&amp;action=' . $params ['action'];
			}
			
			unset ( $params ['action'] );
		}
		
		//Nun noch die ganzen restlichen Parameter anh�ngen
		foreach ( $params as $key => $value )
		{
			if ($value == '' || in_array ( $key, $not ))
			{
				continue;
			}
			$link .= '&amp;' . $key . '=' . $value;
		}
		
		return $link;
	
	}
	
	/**
	 * Splittet einen Parameter-String in ein Array auf
	 *
	 * @param String $href
	 */
	public static function SplitHref($href)
	{
		if ($href == '')
		{
			return array ();
		}
		
		//Array initialisiern das zur�ckgegeben wird
		$back = array ();
		
		//Erst allf�llige sachen vor ? entfernen
		$temp = preg_split ( '/\?/', $href );
		$href = $temp [count ( $temp ) - 1];
		
		//Nun nach trennzeichen aufspliten
		$params = preg_split ( '/&amp;/', $href );
		
		//Jeden Parameter durchgehen
		for($i = 0; $i < count ( $params ); $i ++)
		{
			//Parameter in name/Wert trennen
			$temp = preg_split ( '/=/', $params [$i] );
			
			//Dem array das zur�ckgegeben wird dem Element mit dem schl�ssel des Parameternamens dessen Wert zuweisen
			if (count ( $temp ) == 2)
			{
				$back [$temp [0]] = $temp [1];
			}
		
		}
		
		return $back;
	}
	
	/**
	 * F�hrt die Action aus der sysaction-Tabelle mit der angegebenen ID aus
	 *
	 * @param unknown_type $id
	 */
	public static function runAction($id, $params = '')
	{
		$daten = array ();
		try
		{
			$daten = SqlManager::GetInstance ()->Select ( 'sysaction', array ('public', 'class', 'function' ), 'id=\'' . $id . '\'' );
		} catch ( Exception $ex )
		{
			functions::Output_fehler ( 'Mysql-Error: hhhkl���l�l�lopopo����p�' );
			return;
		}
		
		if ($daten ['class'] == '')
		{
			return;
		}
		
		if ($daten ['public'] == 1) //Die funktion ist als public definiert, so muss keine instanz der klasse erzeugt werden
		{
			
			call_user_func ( array ($daten ['class'], $daten ['function'] ), $params );
		} else
		{
			if (class_exists ( $daten ['class'] ))
			{
				
				$object = new $daten ['class'] ();
				$object->$daten ['function'] ( $params );
			
			}
		}
	
	}
	
	public function GetFileList($path)
	{
		$files = array ();
		$handle = opendir ( $path );
		while ( $file = readdir ( $handle ) )
		{
			if ($file != "." && $file != "..")
			{
				
				$files [] = $path . $file;
			}
		}
		closedir ( $handle );
		return $files;
	}
	
	public static function GetMonth($date)
	{
		$datum = date ( 'F', $date );
		switch ($datum)
		{
			
			case 'January' :
				return 'Januar';
				break;
			case 'July' :
				return 'Juli';
				break;
			case 'February' :
				return 'Februar';
				break;
			case 'March' :
				return 'M&auml;rz';
				break;
			case 'October' :
				return 'Oktober';
				break;
			case 'May' :
				return 'Mai';
				break;
			case 'June' :
				return 'Juni';
				break;
			case 'December' :
				return 'Dezember';
				break;
		}
		return $datum;
	}

}//End Class functions

