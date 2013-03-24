<?php

/**
 * Rechtverwaltung
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Sch�b <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.0
 */

/**
 * Klasse um Benutzern/Gruppen recht zuzuweisen
 *
 * @author Stefan Sch�b
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Sch�b
 * @since 1.0
 */
class Userrecht
{
	/**
	 * Typ der ausgegeben werden soll, benutzer oder gruppe
	 *
	 * @var string $type
	 */
	private $type = 'benutzer';

	/**
	 * Konstruktor
	 *
	 */
	function __construct()
	{

		$this -> setType();
		functions::output_var('type', $this -> type);
		functions::Output_var('RechtLinkAdd', functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'add', 'type' => $this->type, 'id' => $_GET['id'])));
		switch ($_REQUEST['action'])
		{
			case del:
				$this -> del();
				$this -> showeinzel();
				break;
			case add:
				$this -> add();
				$this -> showeinzel();
				break;
			case showeinzel:
				$this -> showeinzel();
				break;
			default:
				$this -> showall();
				break;
		}

	}

	/**
	 * Funktion zum hinzufügen einer Berechtigung
	 *
	 * @return boolean
	 */
	function add()
	{
		$id = functions::cleaninput($_GET['id']);
		$recht = functions::cleaninput($_REQUEST['recht']);

		if($id == '')
		{
			functions::output_fehler('ID Angabe fehlt!');
			return false;
		}

		//�berpr�fen ob die Berechtigung nicht schon existiert
		if(!$this -> checkdouble($id, $recht))
		{
			functions::output_warnung('Diese Berechtigung existiert bereits!');
			return false;
		}

		if($this -> type == 'benutzer')
		{
			$query = "INSERT INTO sysuserrecht VALUES('','$id','$recht',NULL)";
		}
		elseif ($this -> type == 'gruppe')
		{
			$query = "INSERT INTO sysuserrecht VALUES('',NULL,'$recht','$id')";
		}
		else
		{
			functions::output_fehler('Type-Error');
		}

		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 338 ');
		if(!$insert)
		{
			functions::output_fehler('Berechtigung konnte nicht erstellt werden!');
			return false;
		}
		functions::output_bericht('Berechtigung erfolgreich hinzugef�gt!');
		return true;
	}

	/**
	 * Funktion die �berpr�ft ob diese Berechtigung nicht bereits schon existiert!
	 *
	 * @return boolean
	 * @param int $id
	 * @param int $recht
	 */
	function checkdouble($id, $recht)
	{
		if($this -> type == 'benutzer')
		{
			$query = "SELECT id FROM sysuserrecht WHERE benutzerId = '$id' AND rechtID = '$recht'";
		}
		else
		{
			$query = "SELECT id FROM sysuserrecht WHERE gruppeId = '$id' AND rechtID = '$recht'";
		}
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 345 ');
		if(mysql_num_rows($insert)>0)
		{
			return false;
		}
		return true;

	}

	/**
	 * Funktion die alle Benutzer/Benutzergruppen anzeigt
	 *
	 */
	function showall()
	{
		if($this -> type == 'benutzer')
		{
			$recht = $this -> showallbenutzer();
		}
		elseif ($this -> type == 'gruppe')
		{
			$recht = $this -> showallgruppen();
		}
		functions::output_var($this -> type, $recht);
	}

	/**
	 * Funktion die alle Benutzer ausgibt
	 *
	 * @return mixed array mit den Benutzern
	 */
	function Showallbenutzer()
	{
		$query = "SELECT id, nick FROM sysuser";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 339 ');
		$i=0;
		while($daten = mysql_fetch_array($insert))
		{

			$recht[$i]['id'] = $daten[0];
			$recht[$i]['name'] = $daten[1];
			$recht[$i]['link'] = Functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'showeinzel', 'id' => $daten[0]));
			$i++;
		}
		return $recht;
	}

	/**
	 * Funktion die alle BEnutzergruppen zur�ckgibt
	 *
	 * @return mixed array mit allen Benutzergruppen
	 */
	function showallgruppen()
	{
		$query = "SELECT id, name FROM sysrechtgruppe";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 339 ');
		$i=1;
		while($daten = mysql_fetch_array($insert))
		{

			$recht[$i]['id'] = $daten[0];
			$recht[$i]['name'] = $daten[1];
			$recht[$i]['link'] = Functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'showeinzel', 'id' => $daten[0], 'type' => 'gruppe'));
			$i++;
		}

		$recht[0]['id'] = 0;
		$recht[0]['name'] = 'Jeder';
		$recht[0]['link'] = Functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'showeinzel', 'id' => "0", 'type' => 'gruppe'));
		return $recht;
	}

	/**
	 * Funktion die einen einzelnen Benutzer anzeigt
	 *
	 * @return boolean false wenn fehlgeschlagen sonst keine r�ckgabe
	 */
	function showeinzel()
	{
		functions::output_var('rechtselect', functions::selector('sysrecht', 'seite',0,'id', 'seite'));
		$id = functions::cleaninput($_REQUEST['id']);
		if($id == '')
		{
			functions::output_fehler('ID Angabe fehlt!');
			return false;
		}
		if($this -> type == 'benutzer')
		{
			$query = "SELECT nick FROM sysuser WHERE id = '$id'";
			$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 342 ');
			functions::output_var('user',mysql_result($insert, 0));
			$query = "SELECT sysrecht.seite, sysuserrecht.id FROM sysrecht, sysuserrecht WHERE sysuserrecht.rechtid = sysrecht.id AND sysuserrecht.benutzerId = '$id'";

		}
		elseif ($this -> type == 'gruppe')
		{
			if($id == 0)
			{
				$gname = 'Jeder';
			}
			else
			{
				$query = "SELECT name FROM sysrechtgruppe WHERE id = '$id'";
				$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 343 ');
				$gname = @mysql_result($insert, 0);
			}
			functions::output_var('gruppe_name',$gname);
			$query = "SELECT sysrecht.seite, sysuserrecht.id FROM sysrecht, sysuserrecht WHERE sysuserrecht.rechtid = sysrecht.id AND sysuserrecht.gruppeId = '$id'";
		}
		$i=0;
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 340 ');
		while($daten = mysql_fetch_assoc($insert))
		{
			$berechtigung[$i]['id'] = $daten['id'];
			$berechtigung[$i]['seite'] = $daten['seite'];
			//seite}&id={$smarty.get.id}&type=gruppe&action=del&delid={$berechtigung.id
			$berechtigung[$i]['linkDelete'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'del', 'id' => $_GET['id'], 'type' => $this -> type, 'delid' => $daten['id'])) ;
			$i++;
		}
		functions::output_var('berechtigung', $berechtigung);

		//Dann sollen noch die gruppen ausgegeben werden, in denen der User ist:

		$i=1;
		$query = "SELECT sysrechtgruppe.name, sysuserrechtgruppe.id  FROM sysrechtgruppe, sysuserrechtgruppe WHERE sysrechtgruppe.id = gruppenId AND benutzerId = '$id'";
		$insert = @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 341 ');
		while($daten = mysql_fetch_assoc($insert))
		{
			$userrecht[$i]['name'] = $daten['name'];
			$userrecht[$i]['id'] = $daten['id'];
			$userrecht[$i]['link'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'showeinzel', 'type' => 'gruppe', 'id' => $daten['id']));
			$i++;
		}

		$userrecht[0]['id'] = 0;
		$userrecht[0]['name'] = 'Jeder';
		$userrecht[0]['link'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'showeinzel', 'type' => 'gruppe', 'id' => '0'));

		functions::output_var('usergruppe', $userrecht);
	}

	/**
	 * Funktion zum l�schen eines Datensatzes
	 *
	 * @return boolean
	 */
	function del()
	{
		$id = functions::cleaninput($_GET['delid']);
		if($id == '')
		{
			functions::output_fehler('ID Angabe fehlt');
			return false;
		}
		functions::switchDatensatz($id,'sysuserrecht',1);
		return true;
	}

	/**
	 * Funktion mit der man den Typ setzen kann
	 *
	 */
	function setType()
	{
		$this -> type = functions::cleaninput($_GET['type']);
		if($this-> type == '')
		{
			$this -> type='benutzer';
		}
		functions::output_var('type', $this -> type);
	}
}

?>