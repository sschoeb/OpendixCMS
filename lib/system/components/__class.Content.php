<?php
/**
 * Conent-Klassen
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse zum verwalten von Contents
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 1.0     
 */
class Content
{
	function __Construct()
	{
		switch($_GET['action'])
		{
			case 'saveContent':
				//Speichern des Content-Inhalts
				$this -> SaveContent();
				$this -> ShowContent();
				return;
				break;
			case 'saveContentConfig':
				//Wird aufgerufen wenn ein Button im Config-Dialog gedrückt wird
				//Prüfen ob gespeichert oder verlassern werden soll:
				if(!isset($_POST['close']))
				{
					//Wenn nich dann speichern
					$this -> SaveContentConfig();
					$this -> ShowContentConfig();
					return;
				}
				break;
			case 'add':
				//Fügt einen Content hinzu
				$this -> Add();
				$this -> ShowContent();
				return;
				break;
			case 'delRequest':
				$this -> DelRequest();
				return;
				//Nachfrage ob ein Content gelöscht werden soll
				break;
			case 'switchactiv':
				$this -> ChangeActiv();
				breaK;
			case 'del':
				//Löscht einen Content
				$this -> Del();
				break;
			case 'showContent':
				//Zeigt einen einzelnen Content an (Inhalt)
				$this -> ShowContent();
				return;
				break;
			case 'showContentConfig':
				//Zeigt die Konfiguration eins einzelnen Contents an
				$this -> ShowContentConfig();
				return;
				break;
		}

		//Alle Contents in der Liste anzeigen!
		$this -> Show();
	}

	private function Show()
	{
		$i = 0;
		$query = "SELECT id, name FROM syscontent WHERE visible = 1";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 122 ');
		while($daten = @mysql_fetch_assoc($insert))
		{
			$content[$i]['id'] 		= $daten['id'];
			$content[$i]['name'] 	= $daten['name'];

			$i_query = "SELECT activ FROM sysmenue WHERE contentId = '{$daten['id']}' ";
			$i_insert = @mysql_query($i_query) OR functions::output_fehler('MySQL-Error: Nr. 123 ');
			if(@mysql_result($i_insert, 0) == 1)
			{
				$content[$i]['activ'] = 1;
			}

			$content[$i]['link']['einzel'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $daten['id'], 'action' => 'showContent') );
			$content[$i]['link']['activ'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $daten['id'], 'action' => 'switchactiv') );
			$content[$i]['link']['del'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $daten['id'], 'action' => 'del') );
			$i++;
		}

		$over['menuselect'] = functions::MenuSelect(0);
		$over['menuselectgruppe'] = functions::Selector('sysmenuegruppe', 'name');
		functions::Output_var('overview', $over);
		functions::output_var("contents", $content);
	}

	private function SaveContent()
	{
		$id = functions::GetVar($_GET, 'id');
		if($id == '')
		{
			functions::Output_fehler('ID-Angabe fehlt');
			return;
		}
		$input = functions::GetVar($_POST, 'editor', false);
		$query = "UPDATE syscontent SET inhalt='$input' WHERE id = '$id'";	//Datenbank mit dem inhalt updaten
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 125 ');
		if(!$insert)
		{
			functions::Output_fehler('Bericht konnte nicht gespeichert werden!');;
			return;
		}
		functions::output_bericht('Änderungen gespeichert!');	//Erfolgsmeldung ausgeben
	}

	/**
	 * Speichert die Konfiguration eines Contents
	 * 
	 * @access private
	 *
	 */
	private function SaveContentConfig()
	{
		$id = functions::GetVar($_GET, 'id');
		if($id == '')
		{
			functions::Output_fehler('ID-Angabe fehlt');
			return;
		}

		//Algemein
		$name 			= functions::cleaninput($_REQUEST['name']);
		$alias 			= functions::cleaninput($_REQUEST['alias']);

		//Anzeige
		$viewcrda 		= functions::onOfDB(functions::GetVar($_POST, 'viewCrDa'));
		$viewcrus 		= functions::onOfDB(functions::GetVar($_POST, 'viewCrUs'));
		$viewchda 		= functions::onOfDB(functions::GetVar($_POST, 'viewChDa'));
		$viewchus 		= functions::onOfDB(functions::GetVar($_POST, 'viewChUs'));
		$viewalias 		= functions::onOfDB(functions::GetVar($_POST, 'viewAlias'));
		$viewpdf 		= functions::onOfDB(functions::GetVar($_POST, 'viewpdf'));

		//Timer
		$timer_activ	= functions::onOfDB(functions::GetVar($_POST, 'timer_activ'));
		$timer_start 		= functions::dateToTimestamp(functions::GetVar($_POST, 'timer_start', 1));
		$timer_stop 		= functions::dateToTimestamp(functions::GetVar($_POST, 'timer_stop', 1));
		$timer_action		= functions::GetVar($_POST, 'timer_action');

		//Aktiv
		if( functions::GetVar($_POST, 'activ') == "on")	//Auch hier kann man den Status ändern
		{
			if(!$this -> takeOnline())		//Ist status auf on wird der Content auf aktiv geschaltet
			{
				functions::output_fehler('Content konnte nicht Online genommen werden!');
			}
		}
		else
		{
			if(!$this -> takeOffline())		//ist er auf off wird er inaktiv gemacht
			{
				functions::output_fehler('Content konnte nicht Offline genommen werden!');
			}
		}

		//Menü
		$menu 		= functions::GetVar($_POST, 'menu');
		$menugruppe = functions::GetVar($_POST, 'menugruppe');

		$query 		= "SELECT ur.rechtId FROM sysmenue, sysrecht, sysuserrecht ur WHERE sysmenue.contentid = '$id' AND sysmenue.template = sysrecht.seite AND ur.rechtId = sysrecht.id";
		$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 137 ');
		$rechtid 	= @mysql_result($insert, 0);

		$query 		= "UPDATE sysmenue, sysrecht SET sysmenue.vater = '$menu', sysmenue.gId = '$menugruppe' WHERE sysrecht.Id = '$rechtid' AND sysrecht.seite=sysmenue.template";
		$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 138 ');

		//Hier wird der Content-Datensatz geupdatet
		$query 		= "UPDATE `syscontent` SET name = '$name',changeUser = '" . $_SESSION['nick'] . "',timer_start = '$timer_start', timer_stop='$timer_stop', timer_action='$timer_action',timer_activ = '$timer_activ', viewpdf = '$viewpdf' , changeDate='". time() ."', `alias` = '$alias',`viewCrDa` = '$viewcrda',`viewCrUs` = '$viewcrus',`viewChDa` = '$viewchda',`viewChUs` = '$viewchus',`viewAlias` = '$viewalias'WHERE `id` ='$id'";
		$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 128 ');

		$query 		= "UPDATE sysmenue SET eintrag = '$name' WHERE `contentId` ='". $id ."' LIMIT 1";
		$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 146 ');

		functions::output_bericht('Änderungen gespeichert!');
	}

	/**
	 * Funktion die einen Content erstellt
	 * 
	 * @access private
	 * @todo  RollBack
	 *
	 */
	private function Add()
	{
		$templatename 	= "content_" . functions::createpw(10) . ".tpl";
		$name 			= functions::GetVar($_POST, '_name');
		$activ 			= functions::onofDB(functions::GetVar($_POST, 'activ'));
		$menu 			= functions::GetVar($_POST, 'menu');
		$menugruppe 	= functions::GetVar($_POST, 'menugruppe');
		$time 			= time();

		//Hier drin können alle nötigen Sachen für einen korrekten Content (!!) geprüft werden!
		if($name == '')
		{
			functions::output_fehler('Sie müssen einen Namen für den Content angeben!');
			return;
		}

		//Template erstelle
		functions::createfile(TEMPLATEDIR . $templatename, '{$inhalt}'); //Datei erstelen (Bspname: content_qjfn5odl0o.tpl

		//Eintrag in die contenttabelle
		$inhalt 		= functions::GetVar($_POST, 'editor', false);
		$query 			= "INSERT INTO `syscontent` (`visible` ,`name` , `inhalt` , `createDate` , `createUser` , `changeDate` , `changeUser` , `alias` , `pdfId` , `viewCrDa` , `viewCrUs` , `viewChDa` , `viewChUs` , `viewAlias`) VALUES ('1','$name', '$inhalt', '$time', '{$_SESSION['nick']}', '0', '0', '', '', '0', '0', '1', '0', '1');";
		$insert 		= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 140');

		//Abfragen unter welcher ID der Content eingetragen wurde
		$contentId 		= mysql_insert_id();

		//Einfügen in das Menü
		$query 			= "INSERT INTO `sysmenue` ( `id` ,gId, `eintrag` , `vater` , `href` , `template` , `class` , `standard` , `folge` , `extern` , `phpfile` , `contentId` , `activ` ) VALUES ('','$menugruppe', '$name', '$menu', NULL , '$templatename', 'Opencontent', NULL , NULL , NULL , NULL , '$contentId', '$activ');";
		$insert 		= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 141');

		//Einfügen in rechttabelle
		$query 			= "INSERT INTO sysrecht VALUES('', '$templatename', '')";
		$insert 		= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 143');

		//Abfragen undter welcher recht-id der content abgelegt wurde
		$rechtId 		= mysql_insert_id();

		//einfügen in die userrecht tabelle --> neuer eintrag hat immer zugriff für alle
		$query 			= "INSERT INTO sysuserrecht VALUES('', NULL, '$rechtId', '0')";
		$insert 		= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 144');

		//Dem globalen GET-Array den ID-Parameter hinzufügen damit nachher gleich der Content angezeigt werden kann
		$_GET['id'] = $contentId;

		functions::Output_bericht('Content wurde erfolgreich erstellt!');
	}

	/**
	 * Zeigt den Content an (im Editor)
	 *
	 */
	private function ShowContent()
	{
		$id = functions::GetVar($_GET, 'id');
		if($id == '')
		{
			functions::Output_fehler('ID-Angabe fehlt');
			return;
		}

		//Den aktuellen Inhalt des Contents aus der Datenbank holen
		$query = "SELECT inhalt FROM syscontent WHERE id = '$id'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 124 ');
		//Prüfen ob zu der ID ein Content existiert...
		if(@mysql_num_rows($insert) == 0)
		{
			//... Wenn nicht funtkion verlassen
			functions::Output_fehler('Zu dieser ID ist kein Content vorhanden!');
			return;
		}
		$value = @mysql_result($insert, 0);

		//Editor initialisieren mit dem Inhalt der vorhin aus der Datenbank geholt wurde
		functions::initEditor($value);

		//Links generieren
		$links['save'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $_GET['id'], 'action' => 'saveContent'));
		//$links['inhalt'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $_GET['id'], 'tab'=>1, 'subId' => 2));
		$links['contentverwaltung'] = functions::GetLink(array('sub' => $_GET['sub']));
		$links['einstellungen'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $_GET['id'], 'action' => 'showContentConfig'));

		//Links ausgeben
		functions::Output_var('links', $links);
	}

	/**
	 * Zeigt die Konfiguration eines Contents an
	 * 
	 * @access private
	 *
	 */
	private function ShowContentConfig()
	{
		$id = functions::GetVar($_GET, 'id');
		if($id == '')
		{
			functions::Output_fehler('ID-Angabe fehlt');
			return;
		}

		$query 	= "SELECT syscontent.id ,sysmenue.activ,sysmenue.gId , syscontent.name, viewpdf, `createDate`,`createUser`,`changeDate`,`changeUser`,`alias`,`pdfId`,`viewCrDa`,`viewCrUs`,`viewChDa`, `viewChUs`, `viewAlias`, `timer_activ`, `timer_action`, `timer_start`, `timer_stop` FROM syscontent, sysmenue WHERE syscontent.id = '$id' AND syscontent.id = sysmenue.contentId AND syscontent.visible = '1'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 319 ');
		//Prüfen ob Datensatz zu Content vorhanden ist...
		if(@mysql_num_rows($insert) == 0)
		{
			//... Ansonsten funktion verlassen
			functions::Output_fehler('Zu dieser ID konnte kein Content gefunden werden!');
			return;
		}
		$daten 	= @mysql_fetch_assoc($insert);

		$tocheck = array('activ', 'viewCrDa', 'viewCrUs','viewChDa','viewpdf','viewChUs','viewAlias', 'timer_activ');
		for($i=0; $i<count($tocheck); $i++)
		{
			$daten[$tocheck[$i]] = functions::CheckBoxChecker($daten[$tocheck[$i]]);
		}

		$daten['createDate'] = @date("d.m.Y - H:i:s", $daten['createDate']);
		$daten['changeDate'] = @date("d.m.Y - H:i:s", $daten['changeDate']);
		$daten['timer_start'] = @date("d.m.Y - H:i:s", $daten['timer_start']);
		$daten['timer_stop'] = @date("d.m.Y - H:i:s", $daten['timer_stop']);
		$daten['timer_action'] = functions::actionselector(2, $daten['timer_action']);
		$daten['menu_gruppe'] = functions::selector('sysmenuegruppe', 'name', $daten['gId']);
		functions::output_var('config', $daten);

		$query 	= "SELECT vater FROM sysmenue WHERE contentId = '". $daten['id'] ."'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 139 ');
		$id 	= @mysql_result($insert, 0);

		functions::output_var("menu", functions::menuSelect($id));

		//Hier noch die Links generieren und ausgeben:
		$links['save'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $_GET['id'], 'action' => 'saveContentConfig'));
		$links['inhalt'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $_GET['id'], 'action' => 'showContent'));
		$links['contentverwaltung'] = functions::GetLink(array('sub' => $_GET['sub']));
		functions::Output_var('links', $links);
	}

	/**
	 * Nachfrage ob ein Content wirklich gelöscht werden soll
	 *
	 */
	private function DelRequest()
	{
		$id = functions::GetVar($_GET, 'id');
		if($id == '')
		{
			functions::Output_fehler('ID-Angabe fehlt');
			return;
		}
		$links['ja'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $id, 'action' => 'delete'));
		$links['nein'] = functions::GetLink(array('sub' => $_GET['sub']));
		functions::Output_var('links', $links);
	}

	/**
	 * Löscht einen Content
	 * 
	 * @access private
	 * @todo Rollback
	 *
	 */
	private function Del()
	{
		$id = functions::GetVar($_GET, 'id');
		if($id == '')
		{
			functions::Output_fehler('ID-Angabe fehlt');
			return;
		}
		$query 		= "SELECT sysmenue.template, sysrecht.id FROM sysmenue, sysrecht WHERE contentId = '$id' AND sysmenue.template = sysrecht.seite";
		$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 147');
		$daten 		= @mysql_fetch_array($insert);

		$template 	= $daten[0];
		$rechtId 	= $daten[1];

		if(!functions::delfile(TEMPLATEDIR . $template) && $template != '')		//Das template mit der delfile() funktion löschen..
		{
			functions::output_fehler("Das template ($template) konnte nicht gel&ouml;scht werden!");
		}
		else												//... und nur weitermachen wenn das löschen erfolgreich war
		{													//löschen aus der userrechttabell
			$query 	= "DELETE FROM sysuserrecht WHERE rechtId = '$rechtId'";
			$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 148');
			//löschen aus der recht tabelle
			$query 	= "DELETE FROM sysrecht WHERE id = '$rechtId'";
			$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 149');
			//löschen aus dem menü
			$query 	= "DELETE FROM sysmenue WHERE template = '$template' AND contentId = '$id'";
			$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 150');
			//löschen aus der content tabelle
			$query 	= "DELETE FROM syscontent WHERE id = '$id'";
			$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 151');
			//erfolgsmeldung ausgeben
			functions::output_bericht("Der Content wurde erfolgeich gelöscht!");
		}
	}

	private function ChangeActiv()
	{
		$id = $this -> GetMenuId();
		if(!$id)
		{
			return;
		}

		$query = "SELECT activ FROM sysmenue WHERE id = '$id'";		//Den aktuellen Status des Contents abfragen
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 130');
		$activ = @mysql_result($insert, 0);
		if($activ == 1)		//Sollte er aktiv sein muss er zu offline geswitched werden
		{
			if(!$this -> takeOffline()) //Das gescheiht mit der funktion takeOffline()
			{
				functions::output_fehler('Content konnte nicht Offline genommen werden!');
			}

		}
		else				//ist er inaktiv...
		{
			if(!$this -> takeOnline()) //.. wird er mit der Funktion takeOnline aktiv gemacht
			{
				functions::output_fehler('Content konnte nicht Online genommen werden!');
			}
		}
	}

	private function GetMenuId()
	{
		$id = functions::GetVar($_GET, 'id');
		if($id == '')
		{
			functions::Output_fehler('ID Angabe zur ermittlung des Menüs fehlt!');
			return false;
		}
		$query = "SELECT id FROM sysmenue WHERE contentId = '$id'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 131');
		return @mysql_result($insert, 0);
	}

	/**
	 * Funktion um einen Content wieder Online zu nehmen
	 *
	 * @return boolean
	 */
	function TakeOnline()
	{
		$query = "UPDATE sysmenue SET activ = '1' WHERE id = '" . $this -> getMenuId() . "'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 129');
		if($insert == true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Funktion um einen Content ganz Offline zu nehmen
	 *
	 * @return boolean
	 */
	function takeOffline()
	{
		$query = "UPDATE sysmenue SET activ = '0' WHERE id = '" . $this -> getMenuId() . "'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 128');
		if($insert == true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}

/*class ALT_Content
{
/**
* Konstruktor
*
*/
/*function __construct()
{

//functions::import_js('menu.js');

$position = functions::cleaninput($_REQUEST['subId']);

$seite = explode("&subId", $seite);
$seite = $seite[0];

functions::output_var("seite", $seite);

switch($position)
{
case 1:			//Status ändern, also von aktiv auf inaktiv
$this -> changestate();		//Funktion um den Status zu ändern
$this -> showall();			//Alle definierten Contents anzeigen
break;
case 2:			//Bearbeiten

functions::output_var("action", "edit");			//Smarty variabeln übergeben
functions::output_var("id", $_REQUEST['id']);		//			"
functions::output_var("tab", $_REQUEST['tab']);  	//			"
if($_REQUEST['tab'] == 1)								//Es gibt 2 Tabs (Inhalt -> tab 1/Config -> tab 2)
{
if($_GET['action'] == "save")						//Will der Admin speichern ist action auf save gesetzt und der Inhalt wird gesichert
{
$input = htmlentities($_POST['editor']);		//html elemente in &lt;, ... umwandeln
$query = "UPDATE syscontent SET inhalt='$input' WHERE id = '". functions::cleaninput($_REQUEST['id']) ."'";	//Datenbank mit dem inhalt updaten
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 125 ');
functions::output_bericht('Änderungen gespeichert!');	//Erfolgsmeldung ausgeben
}
//Da die seit eim Template included wird wird sie nicht automatisch vom Scrpt geprüft darum hier noch pürfung des editors
//if(!RechtCheck::check("fckeditor.php"))
//{
//	die('Berechtigung fehlt');
//}
$query = "SELECT inhalt FROM syscontent WHERE id = '". functions::cleaninput($_REQUEST['id']) ."'";		//Den aktuellen Inhalt des Contents aus der Datenbank holen
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 124 ');
$value = @mysql_result($insert, 0);
$links['save'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $_GET['id'], 'tab'=>2, 'subId' => 2, 'action' => 'save'));
$links['inhalt'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $_GET['id'], 'tab'=>1, 'subId' => 2));
$links['contentverwaltung'] = functions::GetLink(array('sub' => $_GET['sub']));
$links['einstellungen'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $_GET['id'], 'tab'=>'2', 'subId' => 2));
functions::Output_var('links', $links);
$this -> initEditor($value);		//Editor initialisieren mit dem Inhalt der vorhin aus der Datenbank geholt wurde

}
else								//Hier wäre dann tab 2 also Einstellungen des Contents
{
if($_GET['action'] == "save")	//Will der Administrator die Einstellugnen speichern ist action auf save gesetzt
{
$this -> savecontentconfig(); //Funktion zum speichern der Einstellungen des Contents
}

$this -> showContentConfig(); //Funktion zum anzeigen der momentanen einstellungen
}
break;
case 3:
$this -> addcontent();

break;
case 4:
$id = functions::cleaninput($_REQUEST['id']);
$this -> deleteContent($id);
break;
case 5:
$this -> genpdf();
$this -> showContentConfig();
break;
case 6:
$this -> viewpdf();
$this -> showContentConfig();
break;
default:
$this -> showall();					//Anzeigen von allen Contents die definiert wurden.
break;
}
}

/**
* Zeigt ein PDF an
*
* @return mixed false wenn fehlgeschlagen ansonsten nichts
*/
/*function viewpdf()
{

$id = functions::cleaninput($_REQUEST['id']);

if($id == '')
{
functions::output_fehler('ID Angabe fehlt!');
return false;
}

// "SELECT pfad FROM syspdf, syscontent WHERE syspdf.idSysContentent.pdfISysContentontent.id = '$id'";
$query = "SELECT pfad FROM syspdf, syscontent WHERE syspdf.idSysContentent.pdfISysContentontent.id = '$id'";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 304 ');
$pdf = @mysql_result($insert, 0);
if($pdf == '')
{
functions::output_fehler('PDF nicht vorhanden');
return false;
}
$pdf = str_replace(' ', '_', $pdf);
$error = HTTP_Download::staticSend(array('file' => $pdf));
if($error != '')
{
functions::output_fehler('PDF konnte nicht zugeschickt werden! PEAR ERROR: '. $error->getMessage());
}


}//End Function viewPdf

/**
* Generiert ein PDF und speichert es in der DB
*
* @return mixed false wenn fehlgeschlagen ansonsten true
*/
/*function genpdf()
{
$id = functions::cleaninput($_REQUEST['id']);

if($id == '')
{
functions::output_fehler('ID Angabe fehlt!');
return false;
}

$query = "SELECT inhalt, pdfId, name FROM syscontent WHERE id = '$id'";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 299 ');
$daten = mysql_fetch_assoc($insert);

//Soltle der Namen leer sein wird das PDF nicht erstellt!
if($daten['name'] == ''){
functions::output_warnung('Kein Name vorhanden, Vorgang abgebrochen');
return false;
}


if($daten['pdfId'] != '' && $daten['pdfId'] != 0)
{
//Erste den alten Pfad ermitteln um das alte PDF zu löschen
$query = "SELECT pfad FROM syspdf WHERE id = '".$daten['pdfId']."'";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 300 ');
$pfad = @mysql_result($insert, 0);
functions::delfile($pfad);
}
else
{
//Existiert noch kein PDF hats in der Tabelle Content noh keinen pdfID eintrag --> Eintrag erstellen
$query = "INSERT INTO syspdf VALUES('','')";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 301 ');
$pdfId	= mysql_insert_id();

$query = "UPDATE syscontent SET pdfId = '$pdfId' WHERE id = '$id'";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 302 ');
}

if(functions::createPDF($daten['inhalt'], str_replace(' ', '_', $daten['name'])))
{
$query = "UPDATE syspdf SET pfad = '". MEDIEN ."pdf/". $daten['name'] .".pdf' WHERE id = '".$daten['pdfId']."' LIMIT 1";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 303 ');
functions::output_bericht('PDF wurde erfolgreich erstellt');
}
else
{
functions::output_warnung('Neues PDF konnte nicht erstellt werden!');
return false;
}

}

//
// Funktion um den Status den Contents zu switchen (Online <-> Offline)
//
function changestate()
{
$query = "SELECT activ FROM sysmenue WHERE id = '" . $this -> getMenuId() . "'";		//Den aktuellen Status des Contents abfragen
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 130');
$activ = @mysql_result($insert, 0);
if($activ == 1)		//Sollte er aktiv sein muss er zu offline geswitched werden
{
if(!$this -> takeOffline()) //Das gescheiht mit der funktion takeOffline()
{
functions::output_fehler('Content konnte nicht Offline genommen werden!');
}

}
else				//ist er inaktiv...
{
if(!$this -> takeOnline()) //.. wird er mit der Funktion takeOnline aktiv gemacht
{
functions::output_fehler('Content konnte nicht Online genommen werden!');
}
}
}

//
// Funktion die die Konfiguration eines Content's speichert
//
function saveContentConfig()
{
$id = functions::cleaninput($_REQUEST['id']);
if($id == '')
{
functions::output_fehler('ID Angabe fehlt');
return false;
}

//Algemein
$name 			= functions::cleaninput($_REQUEST['name']);						//Die ganzen Variabeln aus dem Formular holen
$alias 			= functions::cleaninput($_REQUEST['alias']);

//Anzeige
$viewcrda 		= functions::onOfDB(functions::cleaninput($_REQUEST['viewCrDa']));
$viewcrus 		= functions::onOfDB(functions::cleaninput($_REQUEST['viewCrUs']));
$viewchda 		= functions::onOfDB(functions::cleaninput($_REQUEST['viewChDa']));
$viewchus 		= functions::onOfDB(functions::cleaninput($_REQUEST['viewChUs']));
$viewalias 		= functions::onOfDB(functions::cleaninput($_REQUEST['viewAlias']));
$viewpdf 		= functions::onOfDB(functions::cleaninput($_REQUEST['viewpdf']));

//Timer
$timer_activ	= functions::onOfDB(functions::cleaninput($_REQUEST['timer_activ']));
$timer_start 		= functions::dateToTimestamp(functions::cleaninput($_POST['timer_start'], 1));
$timer_stop 		= functions::dateToTimestamp(functions::cleaninput($_POST['timer_stop'], 1));
$timer_action		= functions::cleaninput($_POST['timer_action']);

//PDF
if ($_FILES['pdf']['name'] != '')
{
$pfad = functions::upload(MEDIEN . 'pdf/', 'pdf');
$query = "SELECT pdfId FROM syscontent WHERE id = '$id'";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 292 ');
$pdfId = mysql_result($insert, 0);
if($pdfId == 0 || $pdfId == '')
{
$query = "INSERT INTO syspdf VALUES('', '$pfad')";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 293 ');
$pdfId = mysql_insert_id();
}

$query = "UPDATE syspdf SET pfad = '$pfad' WHERE id = '$pdfId' LIMIT 1";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 294 ');
}//Ende PDF if

//Aktiv
if( functions::cleaninput($_REQUEST['activ']) == "on")	//Auch hier kann man den Status ändern
{
if(!$this -> takeOnline())		//Ist status auf on wird der Content auf aktiv geschaltet
{
functions::output_fehler('Content konnte nicht Online genommen werden!');
}
}
else
{
if(!$this -> takeOffline())		//ist er auf off wird er inaktiv gemacht
{
functions::output_fehler('Content konnte nicht Offline genommen werden!');
}
}

//Menü
$menu 		= functions::cleaninput($_REQUEST['menu']);
$menugruppe = functions::cleaninput($_REQUEST['menugruppe']);

$query 		= "SELECT ur.rechtId FROM sysmenue, sysrecht, sysuserrecht ur WHERE sysmenue.contentid = '" . functions::cleaninput($_REQUEST['id']) . "' AND sysmenue.template = sysrecht.seite AND ur.rechtId = sysrecht.id";
$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 137 ');
$rechtid 	= @mysql_result($insert, 0);

$query 		= "UPDATE sysmenue, sysrecht SET sysmenue.vater = '$menu', sysmenue.gId = '$menugruppe' WHERE SysRecht.Id = '$rechtid' AND sysrecht.seite=sysmenue.template";
$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 138 ');

//Hier wird der Content-Datensatz geupdatet
$query 		= "UPDATE `syscontent` SET name = '$name',changeUser = '" . $_SESSION['nick'] . "',timer_start = '$timer_start', timer_stop='$timer_stop', timer_action='$timer_action',timer_activ = '$timer_activ', viewpdf = '$viewpdf' , changeDate='". time() ."', `alias` = '$alias',`viewCrDa` = '$viewcrda',`viewCrUs` = '$viewcrus',`viewChDa` = '$viewchda',`viewChUs` = '$viewchus',`viewAlias` = '$viewalias'WHERE `id` ='$id'";
$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 128 ');

$query 		= "UPDATE sysmenue SET eintrag = '$name' WHERE `contentId` ='". $id ."' LIMIT 1";
$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 146 ');

functions::output_bericht('Änderungen gespeichert!');

}


/**
* Zeigt die Konfiguration eines Contents an
*
*/
/*function showContentConfig()
{	//Abfrage der Daten für den Aktuellen Content

$id = functions::cleaninput($_REQUEST['id']);
if($id == '')
{
functions::output_fehler('ID angebe fehlt!');
return false;
}

//functions::ImportJS('');

functions::output_var('action', 'edit');
functions::output_var('tab', 2);


echo 	$query 	= "SELECT syscontent.id ,sysmenue.activ,sysmenue.gId , syscontent.name, viewpdf, `createDate`,`createUser`,`changeDate`,`changeUser`,`alias`,`pdfId`,`viewCrDa`,`viewCrUs`,`viewChDa`, `viewChUs`, `viewAlias`, `timer_activ`, `timer_action`, `timer_start`, `timer_stop` FROM syscontent, sysmenue WHERE syscontent.id = '$id' AND syscontent.id = sysmenue.contentId AND syscontent.visible = '1'";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 319 ');
$daten 	= @mysql_fetch_assoc($insert);


if($daten['activ'] == 1)
{
$daten['activ'] = " checked=\"checked\"";
}
if($daten['viewCrDa'] == 1)
{
$daten['viewCrDa'] = " checked=\"checked\"";
}
if($daten['viewCrUs'] == 1)
{
$daten['viewCrUs'] = " checked=\"checked\"";
}
if($daten['viewChDa'] == 1)
{
$daten['viewChDa'] = " checked=\"checked\"";
}
if($daten['viewpdf'] == 1)
{
$daten['viewpdf'] = " checked=\"checked\"";
}
if($daten['viewChUs'] == 1)
{
$daten['viewChUs'] = " checked=\"checked\"";
}
if($daten['viewAlias'] == 1)
{
$daten['viewAlias'] = " checked=\"checked\"";
}
if($daten['timer_activ'] == 1)
{
$daten['timer_activ'] = " checked=\"checked\"";
}

$daten['createDate'] = @date("d.m.Y - H:i:s", $daten['createDate']);
$daten['changeDate'] = @date("d.m.Y - H:i:s", $daten['changeDate']);
$daten['timer_start'] = @date("d.m.Y - H:i:s", $daten['timer_start']);
$daten['timer_stop'] = @date("d.m.Y - H:i:s", $daten['timer_stop']);
$daten['timer_action'] = functions::actionselector(2, $daten['timer_action']);
$daten['menu_gruppe'] = functions::selector('sysmenuegruppe', 'name', $daten['gId']);
functions::output_var('config', $daten);

$query 	= "SELECT vater FROM sysmenue WHERE contentId = '". $daten['id'] ."'";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 139 ');
$id 	= @mysql_result($insert, 0);

functions::output_var("menu", functions::menuSelect($id));




//Hier noch die Links generieren und ausgeben:
$links['save'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $_GET['id'], 'tab'=>2, 'subId' => 2, 'action' => 'save'));
$links['inhalt'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $_GET['id'], 'tab'=>1, 'subId' => 2));
$links['contentverwaltung'] = functions::GetLink(array('sub' => $_GET['sub']));
$links['einstellungen'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $_GET['id'], 'tab'=>2, 'subId' => 2));
functions::Output_var('links', $links);
}


/**
* Funktion um alle Datensätze anzuzeugen in einer Liste
*
*/
/*function Showall()
{
$i = 0;
$query = "SELECT id, name FROM syscontent WHERE visible = 1";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 122 ');
while($daten = @mysql_fetch_assoc($insert))
{
$content[$i]['id'] 		= $daten['id'];
$content[$i]['name'] 	= $daten['name'];

$i_query = "SELECT activ FROM sysmenue WHERE contentId = '" . $daten['id'] . "' ";
$i_insert = @mysql_query($i_query) OR functions::output_fehler('MySQL-Error: Nr. 123 ');
if(@mysql_result($i_insert, 0) == 1)
{
$content[$i]['activ'] = 1;
}

$content[$i]['link']['einzel'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $daten['id'], 'subId' => 2, 'tab' => 1) );
$content[$i]['link']['activ'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $daten['id'], 'subId' => 1) );
$content[$i]['link']['del'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $daten['id'], 'subId' => 4) );
$i++;
}

functions::Output_var('linkAdd', functions::GetLink(array('sub' => $_GET['sub'],  'subId' => 3) ));

functions::output_var("action", "all");
functions::output_var("inhalt", $content);
}

/**
* Funktion um einen Content ganz Offline zu nehmen
*
* @return boolean
*/
/*function takeOffline()
{
$query = "UPDATE sysmenue SET activ = '0' WHERE id = '" . $this -> getMenuId() . "'";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 128');
if($insert == true)
{
return true;
}
else
{
return false;
}
}
/**
* Funktion holt menu-Id
*
* @return int
*/
/*function getMenuId()
{
$query = "SELECT id FROM sysmenue WHERE contentId = '" . functions::cleaninput($_GET['id']) . "'";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 131');
return @mysql_result($insert, 0);
}

/**
* Funktion um einen Content wieder Online zu nehmen
*
* @return boolean
*/
/*function takeOnline()
{
$query = "UPDATE sysmenue SET activ = '1' WHERE id = '" . $this -> getMenuId() . "'";
$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 129');
if($insert == true)
{
return true;
}
else
{
return false;
}
}

/**
* Funktion zum Aufrufen des Editors
*
* @param string 	$value Startwert im Editor
*/
/*function initEditor($value)
{
functions::initEditor($value);
}

/**
* Funktion zum hinzufügen eines Contents
*
* @param int $visible	ist der eintrag in der content-anzeige sichtbar
* @param int $step		welcher step der funktion soll ausgeführt werden
* @param int $output	soll nach einfügen die übersicht angezeigt werden
* @param boolean $check soll gepürft werden ob der Content alle benötigten eingeschaften aus dem formular korrekt bekommen hat?
* @return int content-ID
*/
/*function addcontent($visible = 1, $step = '', $output = 1, $check=true)
{
if ($step == '')
{
$step = $_REQUEST['step'];
}
switch($step)
{
case 1:				//Nach dem erstellen des Content-Inhaltes werden die Datenbankeinträge erstellt
$zufall 		= functions::createpw(10);		//Zufall erstellen für Dateinamen
$templatename 	= "content_" . $zufall . ".tpl";
$name 			= functions::cleaninput($_REQUEST['name']);
$activ 			= functions::onofDB(functions::cleaninput($_REQUEST['activ']));
$menu 			= functions::cleaninput($_REQUEST['menu']);
$menugruppe 	= functions::cleaninput($_REQUEST['menugruppe']);
$time 			= time();
$user 			= $_SESSION['nick'];

if($check)
{
//Hier drin können alle nötigen Sachen für einen korrekten Content (!!) geprüft werden!
if($name == '')
{
functions::output_fehler('Sie müssen einen Namen für den Content angeben!');
return false;
}
}

functions::createfile("templates/" . $templatename, '{$inhalt}'); //Datei erstelen (Bspname: content_qjfn5odl0o.tpl

//Eintrag in die contenttabelle
$inhalt 		= functions::cleaninput($_REQUEST['editor']);
$query 			= "INSERT INTO `syscontent` ( `id` ,`visible` ,`name` , `inhalt` , `createDate` , `createUser` , `changeDate` , `changeUser` , `alias` , `pdfId` , `viewCrDa` , `viewCrUs` , `viewChDa` , `viewChUs` , `viewAlias`) VALUES ('','$visible','$name', '$inhalt', '$time', '$user', '0', '0', '', '', '0', '0', '1', '0', '1');";
$insert 		= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 140');

//Abfragen unter welcher ID der Content eingetragen wurde
$contentId 		= mysql_insert_id();

//Einfügen in das Menü
$query 			= "INSERT INTO `sysmenue` ( `id` ,gId, `eintrag` , `vater` , `href` , `template` , `class` , `standard` , `folge` , `extern` , `phpfile` , `contentId` , `activ` ) VALUES ('','$menugruppe', '$name', '$menu', NULL , '$templatename', 'Opencontent', NULL , NULL , NULL , NULL , '$contentId', '$activ');";
$insert 		= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 141');

//Einfügen in rechttabelle
$query 			= "INSERT INTO sysrecht VALUES('', '$templatename', '')";
$insert 		= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 143');

//Abfragen undter welcher recht-id der content abgelegt wurde
$rechtId 		= mysql_insert_id();

//einfügen in die userrecht tabelle --> neuer eintrag hat immer zugriff für alle
$query 			= "INSERT INTO sysuserrecht VALUES('', NULL, '$rechtId', '0')";
$insert 		= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 144');
if($output == 1)
{
//Parameter übergeben an smarty das die richtige seite angezeigt wird + erfoglsmeldung
$this -> showall();
}
return $contentId;
break;
case 2:
$this -> showall();
break;
default:	//Zu beginn wird der Editor ausgegeben damit der Autor sein Content erstellen kann
$daten['menu_gruppe'] 	= functions::selector('sysmenuegruppe', 'name', $daten['gId']);
$daten['menu'] 			= functions::menuSelect();

$daten['linkAdd'] = functions::GetLink(array('sub' => $_GET['sub'], 'subId' => 3, 'step' => 1));
functions::output_var("daten", $daten);
functions::output_var("action", "new");
functions::initeditor();
break;
}
}

/**
* Funktionen zum entfernen von einem Content
*
* @param int 	$id			id des zu löschenden contents
* @param int 	$direct		ohne nachfrage löschen wenn direct = 1
*/
/*function deleteContent($id, $direct = '')
{
if($_REQUEST['ok'] == 1 || $direct == 1)		//Erst muss nachgefragt werden (conent kommt nicht in den Papierkorb) darum muss ok erst bestätigt werden
{								//Abfragen von template und rechtid die für den löschvorgang von vorteil sind
$query 		= "SELECT sysmenue.template, sysrecht.id FROM sysmenue, sysrecht WHERE contentId = '$id' AND sysmenue.template = sysrecht.seite";
$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 147');
$daten 		= @mysql_fetch_array($insert);

$template 	= $daten[0];
$rechtId 	= $daten[1];

if(!functions::delfile("templates/" . $template) && $template != '')		//Das template mit der delfile() funktion löschen..
{
functions::output_fehler("Das template ($template) konnte nicht gel&ouml;scht werden!");
}
else												//... und nur weitermachen wenn das löschen erfolgreich war
{													//löschen aus der userrechttabell
$query 	= "DELETE FROM sysuserrecht WHERE rechtId = '$rechtId'";
$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 148');
//löschen aus der recht tabelle
$query 	= "DELETE FROM sysrecht WHERE id = '$rechtId'";
$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 149');
//löschen aus dem menü
$query 	= "DELETE FROM sysmenue WHERE template = '$template' AND contentId = '$id'";
$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 150');
//löschen aus der content tabelle
$query 	= "DELETE FROM syscontent WHERE id = '$id'";
$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 151');
//erfolgsmeldung ausgeben
functions::output_bericht("Der Content wurde erfolgeich gelöscht!");

if($direct == '')
{
$this -> showall();								//Alle noch vorhandenen ausgeben
}
}



}
else		//Best$tigung abfragen
{
$links['ja'] = functions::GetLink(array('sub' => $_GET['sub'], 'id' => $id, 'ok' => 1));
$links['nein'] = functions::GetLink(array('sub' => $_GET['sub']));
functions::Output_var('links', $links);
functions::output_var("id", $id);
functions::output_var("askdel", 1);
}
}

/**
* Funktion die ein Formular macht!
*
* @param 	int 		$id		Content ID
* @param 	string 		$name
* @return 	int
*/
/*function isForm($id, $name='')
{
$query 		= "SELECT template, inhalt FROM sysmenue, syscontent WHERE contentId = '$id' AND syscontent.id = '$id'";
$insert 	= mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 228');
$daten 		= mysql_fetch_assoc($insert);

$template 	= $daten['template'];
$html 		= $daten['inhalt'];

$query 		= "INSERT INTO form_template VALUES('','$template', '$name')";
$insert 	= mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 229');
if(!insert)
{
functions::output_fehler('Es konnte ein Eintrag in die Tabelle form_template erstellt werden!');
return;
}
$formId = mysql_insert_id();

$forms = contentAdmin::parseForm($html);		//ALle Namen der eingabefelder holen
for($i=0; $i<count($forms); $i++)
{												//Jedes Feld in die Tabelle eintragen
$query 		= "INSERT INTO form_field VALUES('', '$id','','". $forms[$i] ."', '')";
$insert 	= mysql_query($query) OR functions::output_fehler(mysql_error());
}
return $formId;
}

/**
* Gibt die Felder zurück die in einem Formular sind
*
* @param string $html
* @return array
*
*/
/*function parseForm($html)
{
$j=0;
$textarea = explode('<textarea', $html);
for($i=1; $i<count($textarea); $i++)
{
$temp = explode('name="', $textarea[$i]);
$name = explode('"', $temp[1]);
$feld[$j] = $name[0];
$j++;
}

$input = explode('<input', $html);
for($i=1; $i<count($input); $i++)
{
$temp = explode('name="', $input[$i]);
$name = explode('"', $temp[1]);
$feld[$j] = $name[0];
$j++;
}

return $feld;
}

}*/
?>
