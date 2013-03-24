<?php

/**
 * Formular
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse zum erstellen von einem Formular
 *
 * 
 * @access   	public
 * @package 	OpendixCMS
 * @version  	1.0
 * 
 * 				$formular = new formular();
 *				$formular -> setHtml($html);
 *				$formular -> setAction($actionId);
 *				$formular -> setName($formularname);
 *				$formular -> create();
 *
 */

class Formular
{
	/**
		 * Funktion zum erstellen eins Neuen Formulars
		 *
		 */
	function create()
	{
		//Erst einen neuen Content anlegen in dem das Formular abgelegt werden kann!
		$this -> contentId = contentAdmin::addcontent(0, 1, 0, false);

		//MIt der Obigen "fremden" Funktion kann nicht alles gemacht werden, finishContent macht den rest!
		$this -> finishContent();

		//Erstellt das Formular in der form_template-tabelle -> formId
		$this -> InsertForm();

		//Parst den HTML-Code des Formulars um zu wissen, was für eingabefelder existieren--> this-input
		$this -> parseForm();

		//Fügt alle eingabefelder in die Tabelel ein!
		$this -> InsertField();

		//Fügt die Verbindung zwischen Content und Formular in die ContentForm-Tabelle ein
		$this -> InsertContentFormConnection();

		//Zu guter letzt noch in die Form-Tabelle einfügen
		$this -> Insert();

	}

	/**
		 * Funktion zum editieren eines Formulars
		 *
		 * @param int $id id des Formulars das man editieren will
		 * @param string $html neuer html-Code des Formulars
		 */
	function edit($id, $html)
	{
		//Neues HTML setzen
		$this -> setHtml($html);

		//FormId setzen
		$this -> formId = $id;

		//Alte Felder aus der DB entfernen
		$this -> removeField();

		//Neues Formular parsen
		$this -> parseForm();

		//Neues geparstes Formular einfügen in die DB
		$this -> InsertField();

		//Der FormularInhalt in der Content-Tabelle muss noch angepasst werden!
		$this -> updateContent();

		//Finito :)
	}
	//
	//***********************************************************************
	//
	//
	// Funktion mit der man ein Formular entfernen kann
	// Parameter:
	//	- Id des Formulars, das man entfernen will
	function remove($id)
	{
		//Content entfernen
		contentAdmin::deleteContent($id, 1);

		//Einträge der Felder die im Formular waren entfernen
		$this -> removeField();

		//Eintrag in der Verknüpfung mit der Action löschen
		$this -> removeForm();

		//Eintrag in der Verknüpfung mit dem template löschen
		$this -> removeFormTemplate();
	}

	//
	// Funktion die den Inhalt in der Content-Tabelle updatet!
	//
	function updateContent()
	{
		$query = "SELECT syscontent.id FROM syscontent, sysmenue, form_template WHERE sysmenue.contentId = syscontent.id AND sysmenue.template = form_template.template AND form_template.id = '". $this -> formId ."'";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 265');
		$this -> contentid = mysql_result($insert, 0);

		$query = "UPDATE syscontent SET inhalt = '". $this -> html ."' WHERE id = '". $this -> contentid  ."'";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 266');
	}

	//
	// Funktion mit der die namen + required eigenschaft eines eingabefelds geändert werden kann!
	//
	function params()
	{
		$i=0;
		$query = "SELECT id, post FROM form_field WHERE formId = '" . $this -> formId . "'";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 247');
		while($daten = mysql_fetch_assoc($insert))
		{
			if($_REQUEST['name_' . $daten['id']] == '')
			{
				$name = $daten['post'];
			}
			else
			{
				$name = functions::cleaninput($_REQUEST['name_' . $daten['id']]);
			}
			if($_REQUEST['required_' . $daten['id']] == 'on')
			{
				$required = 1;
			}
			else
			{
				$required = 0;
			}

			$subquery = "UPDATE form_field SET feldname= '$name', required = '$required' WHERE id = '". $daten['id'] ."' LIMIT 1";
			$subinsert = mysql_query($subquery) OR functions::output_fehler('MySQL-Error: Nr. 248');
			if(!$subinsert)
			{
				functions::output_warnung('Fehler beim Updaten der Felder');
				return false;
			}
			$i++;
		}
	}

	//
	//***********************************************************************

	//Hier kommen die FUnktionen um Parameter zu setzen

	//Html übergeben
	function SetHtml($html)
	{
		$this -> html = $html;
	}

	//Name für das Formular übergeben
	function SetName($var)
	{
		$this -> name = $var;
	}

	//ActionID für das Formular übergeben
	function SetAction($var)
	{
		$this -> action = $var;
	}

	function setError($var)
	{
		$this -> error = $var;
	}

	function setFormId($var)
	{
		$this -> formId = $var;
	}

	//Hie rkommen FUnktionen mit denen man Parameter auslesen kann
	function getHtml()
	{
		return $this -> html;
	}

	function getName()
	{
		return $this -> name;
	}

	function getAction()
	{
		return $this -> action;
	}

	function getFormId()
	{
		return $this -> formId;
	}

	function getError()
	{
		return $this -> error();
	}

	//***********************************************************************

	//
	// Ab hie rkommen Funktionen die von den obigen Drei benutzt werdenè


	//
	//FUnktion der man das html übergeben kann und die einem alle Namen der eingabe-felder übergibt!
	//Parameter:
	// 	-html
	//
	// Rückgabe:
	//	- Array[] mit feldnamen
	function parseForm()
	{
		$j=0;
		$textarea = explode('<textarea', $this -> html);
		for($i=1; $i<count($textarea); $i++)
		{
			$temp = explode('name=\"', $textarea[$i]);
			$name = explode('\"', $temp[1]);
			$this -> input[$j] = $name[0];
			$j++;
		}
		$this -> insert = explode('<input', $this -> html);
		for($i=1; $i<count($this -> insert); $i++)
		{
			$typetemp = explode('type=\"', $this -> insert[$i]);
			$type = explode('\"', $typetemp[1]);
			if($type[0] != 'submit' && $type[0] != 'reset')		//Submit und reset buttons müssen nicht in der db stehen
			{
				$temp = explode('name=\"', $this -> insert[$i]);
				$name = explode('\"', $temp[1]);
				$this -> input[$j] = $name[0];

				$j++;
			}
		}
	}

	//
	// Funktion die den Content mIT dem Formular verbindet anhand der ContentForm-Tabelle
	//
	function InsertContentFormConnection()
	{
		$query = "INSERT INTO SysContentForm VALUES('','". $this -> contentId ."','". $this -> formId ."')";
		$insert 	= mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 271');
		if(!insert)
		{
			functions::output_fehler('Verbindung zwischen Content und Formular konnte nicht hergestellt werden!');
			return false;
		}
	}

	//
	// Ersteltl das Formular in der Form-Template-Tabelle
	//
	function InsertForm()
	{
		$query 		= "SELECT template, inhalt FROM sysmenue, syscontent WHERE contentId = '". $this -> contentId . "' AND syscontent.id = '". $this -> contentId . "'";
		$insert 	= mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 228');
		$daten 		= mysql_fetch_assoc($insert);

		$template 	= $daten['template'];
		$html 		= $daten['inhalt'];

		$query 		= "INSERT INTO form_template VALUES('','$template', '" .$this -> name . "')";
		$insert 	= mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 229');
		if(!insert)
		{
			functions::output_fehler('Es konnte ein Eintrag in die Tabelle form_template erstellt werden!');
			return false;
		}
		$this -> formId = mysql_insert_id();
		return $formId;
	}

	//
	// Funktion die ALle Eingabefelder in die Datenbank einfügt!
	//
	function InsertField()
	{
		$this -> doubleFieldName();
		for($i=0; $i<count($this -> input); $i++)
		{												//Jedes Feld in die Tabelle eintragen
			$query 		= "INSERT INTO form_field VALUES('', '". $this -> formId ."','','". $this -> input[$i] ."', '','')";
			$insert 	= mysql_query($query) OR functions::output_fehler(mysql_error());
		}
	}

	//
	// Funktion die doppelt benamste Felder umbenennt
	//
	function doubleFieldName()
	{
		$this -> newarray = array();									//Neues Array definieren

		for($i=0; $i<count($this -> input); $i++)						//Jedes Feld im Formular durchgehen
		{
			$ok = 0;													//Ok-variabel für die While-schalufe
			$x = 0;														//Counter der hochzählt wievielmal ein element schon vorkam
			while($ok != 1)												//Solange der Name nicht eindeutig ist weitermachen
			{
				if(!in_array($this -> input[$i], $this -> newarray))	//Prüfen ob der eintrag shcon im array existiert
				{

					$ok = 1;											//Falls ja while-variable auf 1 setzen um  zu verlassen
					$this -> newarray[$i] =  $this -> input[$i];			//und den einzigartigen wert dem neuen array zuweisen
				}
				else
				{
					$temp = explode('_', $this -> input[$i]);			//Ansonsten erste evtl zusammengemacht werte auseinandernehmen
					$this -> input[$i] = $temp[0] . '_' . $x;			//Neuen namen erstellen der dann nachher wieder geprüft wird
					$x++;
				}
			}
		}
		$this -> input = $this -> newarray;								//Dem alten Array die neuen werte zuweisen
	}
	//
	//
	//
	function finishContent()
	{
		$form = explode('<form', $this -> html);
		if(count($form) == 1)

		$this -> html = '<form action=\"{$seite}\" method=\"POST\">'. $this -> html .'</form>';
		$query = "UPDATE syscontent SET inhalt = '". $this -> html ."' WHERE id = '". $this -> contentId ."'";
		$insert 	= mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 241');

		$query = "UPDATE sysmenue SET visible = 0 WHERE contentId = '". $this -> contentId ."' LIMIT 1";
		$insert 	= mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 256');
	}

	function insert()
	{
		$query = "INSERT INTO form VALUES('','". $this -> formId ."', '". $this -> action ."', '')";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 238 ');
		if(!insert)
		{
			return false;
		}
	}

	function removeField()
	{
		$query = "DELETE FROM form_field WHERE formId = '". $this -> formId ."'";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 242 ');
	}

	function removeForm()
	{
		$query = "DELETE FROM form WHERE formId = '". $this -> formId ."'";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 243 ');
	}
	function removeFormTemplate()
	{
		$query = "DELETE FROM form_template WHERE id = '". $this -> formId ."'";
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 244 ');
	}

}

?>