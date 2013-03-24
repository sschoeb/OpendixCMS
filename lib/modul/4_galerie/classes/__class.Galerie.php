<?php
/**
 * Gallerie
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Sch�b <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.0
 */

/**
 * Klasse zur anzeigen der �ffenltichen Gallerie
 *
 * @author 		Stefan Sch�b
 * @package 	OpendixCMS
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Sch�b
 * @since 		1.0
 */
class Galerie
{
	/**
	 * Konstruktor
	 *
	 */
	function __construct()									
	{
		
		if(isset($_GET['action']))
		{
			if($_GET['action'] == 'getGalerie')
			{
				$this -> SendGalerie();
				$this -> Gallerie_ueber();
				return ;
				
			}
			if($_GET['action'] == 'downloadOriginal')
			{
				$this -> SendOriginal();
				
			}
			
		}
		
		if(isset($_REQUEST['bId']))
		{
			$this -> einzel(functions::cleanInput($_REQUEST['bId']));
		}
		elseif(isset($_REQUEST['gId']))
		{
			$this -> ueber(functions::cleanInput($_REQUEST['gId']));
		}
		else
		{
			$this -> gallerie_ueber();
		}

	}

	/**
	 * Sendet das Originalbild an den User
	 *
	 */
	private function SendOriginal()
	{
		$id = Validator::ForgeInput($_GET['bid']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		$pathId = '';
		try 
		{
			$pathId = SqlManager::GetInstance() -> SelectItem('modgaleriebilder', 'path_sg' ,'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			functions::Output_fehler($ex);
			return;
		}
		$path = '';
		try 
		{
			$path = Filebase::GetFilePath($pathId);
		}
		catch (Exception $ex)
		{
			functions::Output_fehler('Der Pfad zur Originaldatei konnte nicht ermittelt werden!');
		}

		Functions::Download(FILEBASE . $path);
	}
	
	/**
	 * Sendet eine Galerie, welche zum Download angeboten wird an den Besucher
	 *
	 */
	private function SendGalerie()
	{
		$id = functions::GetVar($_GET, 'gId');
		if(!is_numeric($id))
		{
			return;
		}
		//Abfrage der Filebase ID, welche in der moddownload-Tabelle gespeichert ist
		$query = 'SELECT datei FROM moddownload md, modgalerie mg WHERE md.id = mg.download AND mg.id = \''. $id .'\' LIMIT 1';
		$insert = mysql_query($query) OR functions::Output_fehler(mysql_error() . 'Mysql-Error: ajbqu123hb132uwr');
		if(!$insert)
		{
			return;
		}
		$id = mysql_result($insert, 0);
		$path =  Filebase::GetFilePath($id);
		//Datei an den User senden
		Functions::Download(FILEBASE . $path);
		
	}
	
	/**
	 * Zeigt alle verf�gbaren Galerien an
	 *
	 */
	function Gallerie_ueber()
	{
		$query = "SELECT id,name,download,photograf,isUser, UNIX_TIMESTAMP(datum) AS datum, withbis, UNIX_TIMESTAMP(bis) AS bis FROM modgalerie WHERE activ = 1 ORDER BY datum DESC";
		$insert = @mysql_query($query) OR functions::output_fehler('Mysql-Error: hu56');
		$i=0;
		while($daten = @mysql_fetch_assoc($insert))
		{
			$b_query = 'SELECT pfad_k FROM modgaleriebilder WHERE gallerieId = ' . $daten['id'] . ' LIMIT 1';
			$b_insert = @mysql_query($b_query) OR functions::output_fehler('Mysql-Error: hu57');
			$b_result = @mysql_result($b_insert, 0);
			if(@mysql_num_rows($b_insert) > 0)
			{
				if($b_result == "")
				{
					$b_result = "bilder/nopicture.gif";
				}
		
				
				$gallerie_ueber[$i]['id'] 		= $daten['id'];
				$gallerie_ueber[$i]['name']		= $daten['name'];
				$gallerie_ueber[$i]['photograf']= $daten['photograf'];
				$gallerie_ueber[$i]['datum']	= strftime('%d. ', $daten['datum']) . Functions::GetMonth($daten['datum']) . strftime(' %Y',$daten['datum']);
				
				if($daten['withbis'] == 1)
				{
					$timeint = $timeint =  mktime($daten['bis']);
					$gallerie_ueber[$i]['bis'] = strftime('%d. ', $daten['bis']) . Functions::GetMonth($daten['bis']) . strftime(' %Y',$daten['bis']);
				}
				
				if($daten['isUser'] == 1)
				{
					$userQuery = 'SELECT nick FROM sysuser WHERE id = \''. $daten['photograf'] .'\'';
					$userInsert = mysql_query($userQuery) OR Functions::Output_fehler('Mysql-Error: �laaaaasdnihahe7123n');
					$gallerie_ueber[$i]['photograf'] = mysql_result($userInsert, 0);
				}
				
				$sumQuery = 'SELECT COUNT(*) FROM modgaleriebilder WHERE gallerieId = \'' . $daten['id'] . '\'';
				$sumInsert = mysql_query($sumQuery) OR Functions::Output_fehler('Mysql-Error: �lasdnihahe7123n');
				$gallerie_ueber[$i]['pictureCount'] = mysql_result($sumInsert, 0);
				
				$gallerie_ueber[$i]['link']['galerie'] 	= functions::GetLink(array('sub' => $_GET['sub'], 'gId' => $daten['id']));
				
				if($daten['download'] > 1)
				{
					$gallerie_ueber[$i]['download'] = true;
					$gallerie_ueber[$i]['link']['download'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'getGalerie', 'gId' => $daten['id']));
				}
				try {
					$gallerie_ueber[$i]['picturePath'] 	= FILEBASE . Filebase::getFilePath( $b_result);
				}catch (Exception $ex)
				{
					functions::Output_warnung('Ein Bild wurde nicht gefunden: ' . $b_result);
				}
				
				
				if(!file_exists($gallerie_ueber[$i]['picturePath']))
				{

				
					$gallerie_ueber[$i]['picturePath'] = "bilder/nopicture.gif";
				}
				$i++;
			}
		}

		functions::output_var("galueber",'1');	 //Dem Head-Obejkt den Content (also die news) �bergeben
		functions::output_var("gallerie_ueber", $gallerie_ueber);	 //Dem Head-Obejkt den Content (also die news) �bergeben
		functions::output_var("link", htmlentities($_SERVER['PHP_SELF'] . "?". $_SERVER['QUERY_STRING']));

	}

	/**
	 * Alle verf�gbaren Bilder in der ausgew�hlten Galerie anzeigen
	 *
	 * @param int $gallerie_id
	 */
	function Ueber($gallerie_id)							//
	{
		$active_page = !empty($_GET['page']) ? $_GET['page'] : 0;		//Hier kommt die Bl�tterklasse ins Spiel

		$query 	= 'SELECT COUNT(id) FROM modgaleriebilder WHERE GallerieId = \''.$gallerie_id .'\'';					//Abfragen wieviele Eintr�ge die tabelle hat
		$result	= 	@mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 156.zzzz ');
		list($entries) = mysql_fetch_row($result);

		$blaettern=new Blaettern($active_page, $entries);				//Neue Instanz von blaettern erstellen
		$blaettern->set_Link_Href(URL . "?order=$order&amp;page=");
		$blaettern->set_Entries_Per_Page(25);					//Anzahl seiten pro seiten festlegen (Konstante wird hier im script gesetzt und steh tin der config.ini)

		$query = "SELECT id, pfad_k FROM modgaleriebilder WHERE GallerieId = '$gallerie_id' ORDER by id LIMIT ".($blaettern->get_Epp() * $blaettern->get_Active_Page()).', '.$blaettern->get_Epp();
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 26 ');
		if(@mysql_num_rows($insert)== 0)
		{
			functions::output_fehler('Die Gallerie ist leer!');
			return;
		}

		$i = 0;
		while($daten = @mysql_fetch_assoc($insert))
		{
			$gallerie[$i]['id'] 	= $daten['id'];
			try {
				$gallerie[$i]['pfad']	= FILEBASE . Filebase::getFilePath( $daten['pfad_k']);
			}
			catch (Exception $ex)
			{
				functions::Output_warnung('Bild nicht gefunden: ' . $daten['pfad_k']);
			}
			
			
			$gallerie[$i]['link']	= functions::GetLink(array('sub' => $_GET['sub'], 'gId' => $gallerie_id, 'bId' => $daten['id']));
			if(!file_exists($gallerie[$i]['pfad']))
			{
				//echo MEDIEN . $gallerie[$i]['pfad'];
				$gallerie[$i]['pfad'] = "bilder/nopicture.gif";
			}
			$i++;
		}

		functions::output_var("blaetter", $blaettern -> create());
		functions::output_var("gallerie_ok",'1');
		functions::output_var("gallerie", $gallerie);
		//functions::output_var("link", htmlentities($_SERVER['PHP_SELF'] . "?". $_SERVER['QUERY_STRING']));
	}

	/**
	 * Zeigt ein einzelnes Bild gros und 3 kleine an
	 *
	 * @param int $id
	 */
	function einzel($id)									//Einzelnes ausgew�hltes Bild anzeigen
	{
		$query = "SELECT id, pfad_g, pfad_k, gallerieId, kamera, comment, DATE_FORMAT(datum, '%d.%M %Y - %H:%i:%S') AS datum, path_sg FROM modgaleriebilder WHERE id = '$id'";
		$insert = @mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 1 ');
		if(mysql_num_rows($insert) == 0)
		{
			functions::Output_fehler('Kein Bild mit dieser ID gefunden!');
			return;
		}
		$einzel = @mysql_fetch_assoc($insert);

		$infos = array();
		
		if($einzel['path_sg'] != null)
		{
			$infos['originalAvaible'] = true;
			$infos['originalDownload'] = functions::GetLink(array('action' => 'downloadOriginal'), true);
		}
		
		$infos['camera'] = $einzel['kamera'];
		$infos['datetime'] = $einzel['datum'];
		if($infos['datetime'] == '0000-00-00 00:00:00')
		{
			$infos['datetime'] = '';
		}
		$infos['comment'] = $einzel['comment'];
		
		functions::Output_var('infos', $infos);
		
		$max_query = "SELECT max(id) FROM modgaleriebilder WHERE gallerieId = ". $einzel['gallerieId'] ;
		$max_insert = @mysql_query($max_query) OR functions::output_fehler('@mysql-Error: Nr. 2 ');
		$max = @mysql_result($max_insert, 0);

		$query = "SELECT id, pfad_k FROM modgaleriebilder WHERE gallerieId = ". $einzel['gallerieId'] . " AND id < ". $einzel['id'] ." ORDER BY id DESC LIMIT 1";
		$insert = @mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 3.1 ');
		if(mysql_num_rows($insert)==0)
		{
			$subbilder[0]['false'] = true;
		}
		else
		{
			$temp = mysql_fetch_assoc($insert);
			$subbilder[0]['id'] = $temp['id'];
			$subbilder[0]['pfad'] = FILEBASE . Filebase::getFilePath($temp['pfad_k']);
		}

		$query = "SELECT id, pfad_k FROM modgaleriebilder WHERE gallerieId = ". $einzel['gallerieId'] . " AND id > ". $einzel['id'] ." ORDER BY id LIMIT 1";
		$insert = @mysql_query($query) OR functions::output_fehler('@mysql-Error: Nr. 3.2 ');
		if(mysql_num_rows($insert)==0)
		{
			$subbilder[2]['false'] = true;
		}
		else
		{
			$temp = mysql_fetch_assoc($insert);
			$subbilder[2]['id'] = $temp['id'];
			$subbilder[2]['pfad'] = FILEBASE . Filebase::getFilePath( $temp['pfad_k']);
		}

		$subbilder[1]['id']		= $einzel['id'];
		$subbilder[1]['pfad']	= FILEBASE . Filebase::getFilePath( $einzel['pfad_k']);

		for($i = 0; $i<3; $i++)
		{
			$subbilder[$i]['link'] = functions::GetLink(array('sub' => $_GET['sub'], 'gId' => $_GET['gId'], 'bId' => $subbilder[$i]['id']));
			if(!file_exists($subbilder[$i]['pfad']))
			{
				$subbilder[$i]['pfad'] = "bilder/nopicture.gif";
			}
		}
		functions::output_var("bild", $subbilder);
		functions::Output_var('backLink', functions::GetLink(array('sub' => $_GET['sub'], 'gId' => $_GET['gId'])));
		functions::output_var("bild_ok",'1');
		functions::output_var("id", $einzel['id']);

		if(!file_exists( FILEBASE . Filebase::getFilePath( $einzel['pfad_g'])))
		{
			$einzel['pfad_g'] = MEDIEN . "bilder/nopicture.gif";
		}
		else
		{
			$einzel['pfad_g'] = FILEBASE . Filebase::getFilePath( $einzel['pfad_g']);
		}

		functions::output_var("pfad", $einzel['pfad_g']);

	}
}

?>