<?php

/**
 * Menüverwaltung
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse zum verwalten der Menüstruktur
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 2.0     
 */
class MenueAdmin
{
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{
		if (! isset ( $_GET ['action'] ))
		{
			$this->Overview ();
			return;
		}
		switch ($_GET ['action'])
		{
			case 'save' :
				$this->Save ();
				$this->Overview ();
				break;
			case 'delete' :
				$this->Delete ();
				$this->Overview ();
				break;
			case 'add' :
				$this->Add ();
				break;
			case 'item' :
				$this->Item ();
				break;
			default :
				throw new CMSException ( 'Unbekannte Aktion!', CMSException::T_MODULEERROR );
				break;
		}
	}
	
	/**
	 * Speichert die Eigenschaften eines einzelnen Menüeintrages
	 *
	 * @todo Berechtigung
	 */
	private function Save()
	{
		
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ung&uuml;ltige ID &uuml;bergeben!', CMSException::T_MODULEERROR );
		}
		if (! Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_EDIT, $_GET ['sub'] ))
		{
			//throw new CMSException('Sie sind nicht berechtigt Elemente zu ver&auml;ndern!!', CMSException::T_MODULEERROR);
		}
		//Abfrage der Daten
		$moduleId = Validator::ForgeInput ( $_POST ['moduleid'] );
		$groupId = Validator::ForgeInput ( $_POST ['groupid'] );
		$name = Validator::ForgeInput ( $_POST ['name'] );
		$parent = Validator::ForgeInput ( $_POST ['parent'] );
		$href = Validator::ForgeInput ( $_POST ['href'] );
		$template = Validator::ForgeInput ( $_POST ['template'] );
		$class = Validator::ForgeInput ( $_POST ['class'] );
		$standard = Validator::ForgeInput ( $_POST ['stand'] );
		$active = FormParser::ParseCheckBox(Validator::ForgeInput($_POST['active']));
		$order = Validator::ForgeInput ( $_POST ['order'] );
		
	
		
		if (! is_numeric ( $moduleId ))
		{
			throw new CMSException ( 'ModulID hat einen ung&uuml;ltigen Wert!', CMSException::T_MODULEERROR );
		}
		if (! is_numeric ( $groupId ))
		{
			throw new CMSException ( 'GroupId hat einen ung&uuml;ltigen Wert!', CMSException::T_MODULEERROR );
		}
		if (! is_numeric ( $parent ))
		{
			throw new CMSException ( 'Parent hat einen ung&uuml;ltigen Wert!', CMSException::T_MODULEERROR );
		}
		if (! is_numeric ( $order ))
		{
			throw new CMSException ( 'Order hat einen ung&uuml;ltigen Wert!', CMSException::T_MODULEERROR );
		}
		if (strlen ( $name ) < 3)
		{
			throw new CMSException ( 'Der neue Name des Men&uuml;punktes ist zu kurz!', CMSException::T_MODULEERROR );
		}
		//Speichern der Daten
		try
		{
			SqlManager::GetInstance ()->Update ( 'sysmenue', array ('moduleid' => $moduleId, 'gId' => $groupId, 'name' => $name, 'parent' => $parent, 'href' => $href, 'template' => $template, 'class' => $class, 'standard' => $standard, 'active' => $active, '`order`' => $order ), 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Neue Daten konnten nicht gespeichert werden!', CMSException::T_MODULEERROR );
		}
		if (isset ( $_POST ['common'] ))
		{
			try
			{
				//Einfügen falls der Datensatz noch nicht vorhanden ist
				SqlManager::GetInstance ()->Query ( 'INSERT INTO syscommon (menueId) SELECT ' . $id . ' FROM DUAL WHERE not exists (SELECT id FROM syscommon WHERE menueid=\'' . $id . '\')' );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'Der Men&uuml;punkt konnte nicht &ouml;ffentlich gemacht werden!', CMSException::T_MODULEERROR, $ex );
			}
		} else
		{
			try
			{
				SqlManager::GetInstance ()->Delete ( 'syscommon', 'menueid=\'' . $id . '\'' );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'Der Men&uuml;punkt konnte nicht privatisiert werden!', CMSException::T_MODULEERROR, $ex );
			}
		}
		
		//Abfragen alle User und Usergruppen aus der Datenbank
		$user = array ();
		$groups = array ();
		try
		{
			$user = SqlManager::GetInstance ()->Select ( 'sysuser', array ('id' ) );
			$groups = SqlManager::GetInstance ()->Select ( 'syslawgroup', array ('id' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'User konnten nicht abgefragt werden!', CMSException::T_MODULEERROR, $ex );
		}
		
		//Falls keine Berechtigungen auf User oder Gruppen gesetzt wurden, sind $_POST['u']/$_POST['g']
		//nicht gesetzte Variabeln. Da dies ein PHP-Notice auslöst hier die Variabeln setzen falls nötig
		if (! isset ( $_POST ['u'] ))
		{
			$_POST ['u'] = array ();
		}
		
		if (! isset ( $_POST ['g'] ))
		{
			$_POST ['g'] = array ();
		}
		
		//Inhalt der Variabeln überprüfen
		$userdata = Validator::ForgeInput ( $_POST ['u'] );
		$groupdata = Validator::ForgeInput ( $_POST ['g'] );
		
		//Die Daten aus dem Formular mit den Daten aus der DB zusammenführen
		//Aus dem Formular kommen nur User/Gruppen die neu eine Berechtigung haben sollen
		//sollte ein Benutzer/eine Gruppe neu keine Berechtigung mehr haben, dann wird
		//dieser nicht über die Formular-Daten übergeben. Darum müssen sämtliche User
		//aus der Datenbank abgefragt werden um beim Berechtigungsupdate alle User
		//zu berücksichtigen.
		$userdata = $this->ConcateArrays ( $userdata, $user );
		$groupdata = $this->ConcateArrays ( $groupdata, $groups );
		
		//verarbeiteung der Benutzer-Daten
		$this->PrepareFormLawData ( $id, $userdata, true );
		
		//Bearbeiteung der Gruppen-Daten
		$this->PrepareFormLawData ( $id, $groupdata, false );
	}
	
	/**
	 * Führt die User/Gruppen Arrays zusammen
	 *
	 * @param unknown_type $dataFrm
	 * @param unknown_type $dataDb
	 * @return unknown
	 */
	private function ConcateArrays($dataFrm, $dataDb)
	{
		for($i = 0; $i < count ( $dataDb ); $i ++)
		{
			if (array_key_exists ( $dataDb [$i] ['id'], $dataFrm ))
			{
				continue;
			}
			$dataFrm [$dataDb [$i] ['id']] = array ();
		}
		return $dataFrm;
	}
	
	/**
	 * Bearbeitet die Daten aus dem Formular welche die Berechtigungen festlegen
	 *
	 * @param Array 	$data	Daten aus dem Formular
	 * @param Boolean 	$isUser	Boolean ob es sich um Benutzer oder Gruppen-Daten handelt
	 */
	private function PrepareFormLawData($menueId, $data, $isUser = true)
	{
		//Liste mit den vorhandenen Berechtigungen
		$l = array ('view' => 1, 'edit' => 2, 'add' => 3, 'delete' => 4 );
		
		//Alle Daten aus dem Formular durchgehen
		foreach ( $data as $userid => $laws )
		{
			//Jedes Element das aus dem Formular stammt beinhaltet die Liste mit den Berechtigungen
			//die für den User, welcher als Key mitgegeben wird, gesetzt sind
			//Berechtigungen die nicht gesetzt sind, sind nicht in der Liste vorhanden
			//Hier die Liste der Berechtigungen die es gibt durchgehen...
			foreach ( $l as $key => $value )
			{
				//.. und jede prüfen ob sie in den vom Formular übergebenen Daten
				//vorhanden sind
				if (isset ( $laws [$key] ))
				{
					//Vorhanden -> Der User/Die Gruppe hat die Berechtigung
					$this->UpdateLaw ( $menueId, $userid, $value, true, $isUser );
				} else
				{
					//Nicht vorhanden -> Der User/Die Gruppe hat die Berechtigung nicht
					$this->UpdateLaw ( $menueId, $userid, $value, false, $isUser );
				}
			}
		}
	}
	
	/**
	 * Updatet einen Berechtigungsdatensatz in der Datenbank
	 *
	 * Ist der User nicht berechtigt werden alfällig noch vorhandene
	 * Berechtigungen gelöscht
	 * 
	 * @param int 		$id			ID des Users/der Gruppe
	 * @param int 		$lawType	Typ der Berechtigung (1-4 -> View, Edit, Add, Delete)
	 * @param Boolean 	$hasLaw		true wenn der Besucher/die Gruppe berechtigt ist ansonsten false
	 * @param Boolean	$isUser		Boolean ob es sich um einen User oder eine Gruppe handelt (true = User/false = Gruppe)
	 */
	private function UpdateLaw($menueId, $id, $lawType, $hasLaw, $isUser = true)
	{
		//Spalte die in der syslaw-Tabelle verwendet wird bestimmen
		$col = '';
		if ($isUser)
		{
			$col = 'userid';
		} else
		{
			$col = 'groupid';
		}
		//Abfragen ob es zu diesem Menüpunkt mit dieser Berechtigung zu diesem User/dieser Gruppe
		//bereits einen Berechtigungseintrag gibt
		$count = 0;
		try
		{
			$count = SqlManager::GetInstance ()->Count ( 'syslaw', 'menueid=\'' . $menueId . '\' AND type=\'' . $lawType . '\' AND ' . $col . ' = \'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Berechtigungen konnten nicht nachgef&uuml;hrt werden!', CMSException::T_MODULEERROR, $ex );
		}
		
		//Es besteht kein Berechtigungseintrag und der User/die Gruppe soll neu auch nicht
		//auf den Menüpunkt berechtig sein -> Funktion verlassen
		if ($count == 0 && ! $hasLaw)
		{
			return;
		}
		
		//Es bestehen Berechtigungen für den User/die Gruppe aber neu soll keine bestehen
		//-> vorhandene Berechtigung löschen -> Funktion verlassen
		if ($count > 0 && ! $hasLaw)
		{
			try
			{
				SqlManager::GetInstance ()->Delete ( 'syslaw', 'menueid=\'' . $menueId . '\' AND type=\'' . $lawType . '\' AND ' . $col . ' = \'' . $id . '\'' );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'Update fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
			}
			return;
		}
		
		//Es bestehen keine Berechtigungen,es soll aber neu eine für den User/die Gruppe angelegt werden
		//-> Berechtigungen erstellen
		if ($count == 0 && $hasLaw)
		{
			try
			{
				SqlManager::GetInstance ()->Insert ( 'syslaw', array ('menueid' => $menueId, 'type' => $lawType, $col => $id ) );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'Neue Berechtigung konnte nicht eingef&uuml;gt werden!', CMSException::T_MODULEERROR, $ex );
			}
		}
	
	}
	
	/**
	 * Löscht einen einzelnen Menüeintrag
	 *
	 */
	private function Delete()
	{
		if (! Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_DELETE, $_GET ['sub'] ))
		{
			throw new CMSException ( 'Sie sind nicht berechtigt dieses Element zu l&ouml;schen!', CMSException::T_MODULEERROR );
		}
	}
	
	/**
	 * Fügt einen Menüeintrag hinzu
	 *
	 */
	private function Add()
	{
		if (! Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_ADD, $_GET ['sub'] ))
		{
			throw new CMSException ( 'Sie sind nicht berechtigt ein Element hinzuzuf&uuml;gen!!', CMSException::T_MODULEERROR );
		}
		
		$name = Validator::ForgeInput ( $_POST ['name'] );
		if (strlen ( $name ) < 3)
		{
			throw new CMSException ( 'Der angegebene Name ist zu kurz!', CMSException::T_MODULEERROR );
		}
		
		try
		{
			SqlManager::GetInstance ()->Insert ( 'sysmenue', array ('name' => $name ) );
			$_GET ['id'] = SqlManager::GetInstance ()->GetLastUsedId ();
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Einf&uuml;gen fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		$this->Item ();
	
	}
	
	/**
	 * Zeigt einen einzelnen Menüeintrag an
	 *
	 */
	private function Item()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die &uuml;bergebene ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		//Eigenschaften des Menüpunktes abfragen und ausgeben
		$daten = array ();
		try
		{
			$daten = SqlManager::GetInstance ()->Select ( 'sysmenue', array ('moduleId', 'gId' => 'groupid', 'name', 'parent', 'href', 'template', 'class', 'standard', 'active', '`order`' ), 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			echo SqlManager::GetInstance ()->GetLastQuery ();
			throw new CMSException ( 'Abfrage der Eigenschaften fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		$daten = $daten [0];
		$daten ['id'] = $id;
		$daten ['group'] = HTML::Select ( 'sysmenuegruppe', 'name', $daten ['groupid'] );
		$daten ['moduleid'] = HTML::Select ( 'sysmodul', 'name', $daten ['moduleId'] );
		$daten ['template'] = HTML::Select ( 'systemplate', 'template', $daten ['template'] );
		$daten ['standard'] = HTML::Checkbox ( $daten ['standard'] );
		$daten ['active'] = HTML::Checkbox ( $daten ['active'] );
		$daten ['parentselected'] = $daten ['parent'];
		$daten ['parent'] = array ();
		$daten ['parent'] [] = array ('id' => 0, 'name' => 'Oberste Ebene' );
		//Parent Eigenschaft ausgeben
		$menue = new Menue ( true, false );
		$menueData = $menue->Output ( array ($id ) );
		for($i = 0; $i < count ( $menueData ); $i ++)
		{
			if (! isset ( $menueData [$i] ['items'] ))
			{
				continue;
			}
			for($j = 0; $j < count ( $menueData [$i] ['items'] ); $j ++)
			{
				$daten ['parent'] [] = $menueData [$i] ['items'] [$j];
			}
		
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'properties', $daten );
		
		//Abfragen der User-Berechtigungen
		$law = array ();
		if ($this->IsCommon ( $id ))
		{
			$law ['common'] = HTML::Checkbox ( 1 );
		}
		
		try
		{
			$law ['user'] = SqlManager::GetInstance ()->Select ( 'sysuser', array ('id', 'nick' => 'name' ) );
			$law ['group'] = SqlManager::GetInstance ()->Select ( 'syslawgroup', array ('id', 'name' ) );
		} catch ( SqlManagerException $ex )
		{
		
		}
		
		for($i = 0; $i < count ( $law ['user'] ); $i ++)
		{
			$law ['user'] [$i] ['law'] = $this->GetLaw ( $id, $law ['user'] [$i] ['id'] );
		}
		for($i = 0; $i < count ( $law ['group'] ); $i ++)
		{
			$law ['group'] [$i] ['law'] = $this->GetLaw ( $id, $law ['user'] [$i] ['id'], false );
		}
		
		//Ausgabe der Berechtigungen
		MySmarty::GetInstance ()->OutputModuleVar ( 'law', $law );
		//Ausgabe des Links über den das Formular gespeichert werden kann
		MySmarty::GetInstance ()->OutputModuleVar ( 'savelink', Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'save', 'id' => $id ) ) );
	
	}
	
	/**
	 * Fragt in der Datenbank ab, ob ein Menüpunkt öffentlich ist
	 *
	 * @param int $id	Menüpunkt der überprüft werden soll
	 * @return Boolean
	 */
	private function IsCommon($id)
	{
		$c = 0;
		try
		{
			$c = SqlManager::GetInstance ()->Count ( 'syscommon', 'menueId=\'' . $id . '\'' );
		
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Es konnte nicht ermittelt werden ob die Seite &Ouml;ffentlich ist!', CMSException::T_MODULEERROR, $ex );
		}
		if ($c == 0)
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Gibt die Berechtigungen eines Users zu einem Menüpunkt zurück
	 *
	 * @param int $menueid	Menüpunkt dessen Berechtigung abgeafragt wird
	 * @param int $userid	User auf den die Berechtigung abgeafragt wird
	 * @param Boolean $type	Typ der Abfrage: true = User/false = Gruppe
	 * @return Array mit den vier Berechtigungen als checked="checked" wenn berechtigt
	 */
	private function GetLaw($menueid, $userid, $type = true)
	{
		
		$law = array ();
		$result = NULL;
		try
		{
			$sql = '';
			if ($type)
			{
				$sql = 'SELECT type FROM syslaw sl, sysuser su WHERE su.id=\'' . $userid . '\' AND su.id=sl.userid AND sl.menueid=\'' . $menueid . '\'';
			} else
			{
				$sql = 'SELECT type FROM syslaw sl, syslawgroup su WHERE su.id=\'' . $userid . '\' AND su.id=sl.groupid AND sl.menueid=\'' . $menueid . '\'';
			}
			$result = SqlManager::GetInstance ()->Query ( $sql );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'User-Berechtigungen konnten nicht abgeafragt werden!', CMSException::T_MODULEERROR, $ex );
		}
		
		while ( $daten = mysql_fetch_assoc ( $result ) )
		{
			switch ($daten ['type'])
			{
				case 1 :
					$law ['view'] = HTML::Checkbox ( 1 );
					break;
				case 2 :
					$law ['edit'] = HTML::Checkbox ( 1 );
					break;
				case 3 :
					$law ['add'] = HTML::Checkbox ( 1 );
					break;
				case 4 :
					$law ['delete'] = HTML::Checkbox ( 1 );
					break;
			}
		}
		return $law;
	}
	
	/**
	 * Zeigt die Übersicht über die gesamte Menüstruktur an
	 *
	 */
	private function Overview()
	{
		$menue = new Menue ( true, false );
		$menueData = $menue->Output ();
		
		$c = count ( $menueData );
		for($i = 0; $i < $c; $i ++)
		{
			for($j = 0; $j < count ( $menueData [$i] ['items'] ); $j ++)
			{
				$menueData [$i] ['items'] [$j] = $this->ChangeMenueLinks ( $menueData [$i] ['items'] [$j] );
			}
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'menueData', $menueData );
	}
	
	/**
	 * Rekursive Funktion um die Links, welche von der Menue
	 * Klasse generiert wurde auf die "Editieren"-Links anzupassen
	 *
	 * @param unknown_type $item
	 * @return unknown
	 */
	private function ChangeMenueLinks($item)
	{
		
		$item ['link'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'item', 'id' => $item ['id'] ) );
		if (isset ( $item ['children'] ))
		{
			$c = count ( $item ['children'] );
			for($i = 0; $i < $c; $i ++)
			{
				$item ['children'] [$i] = $this->ChangeMenueLinks ( $item ['children'] [$i] );
			}
		}
		return $item;
	}
}