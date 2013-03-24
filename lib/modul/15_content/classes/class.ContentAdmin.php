<?php

/**
 * Contentverwaltung
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse zum verwalten von Inhalten
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 2.0     
 */
class ContentAdmin extends ModulBase
{
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{
		parent::__construct ();
		
		CssImport::ImportCss ( 'content.css' );
	}
	
	/**
	 * Wechselt eine Contentseite von Aktiv zu Inaktiv oder umgekehrt
	 *
	 */
	private function SwitchActive()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		$menueId = NULL;
		try
		{
			$menueId = SqlManager::GetInstance ()->SelectItem ( 'modcontent', 'menueid', 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der Men&uuml;-ID fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		Functions::SwitchActiv ( 'sysmenue', $menueId );
	}
	
	/**
	 * Zeigt die Übersicht aller vorhandenen Contents an
	 *
	 */
	protected function Overview()
	{
		//Abfragen sämtlicher vorhandenen Contents
		$contents = array ();
		try
		{
			$contents = SqlManager::GetInstance ()->SelectWithScrollMenue ( 'modcontent', array ('id', 'name' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Fehler bei der Abfrage der Contents', CMSException::T_MODULEERROR, $ex );
		}
		//Löschenlink sowie den Link zu den Eigenschaften der einzelnen Contents hinzufügen
		$c = count ( $contents ['daten'] );
		for($i = 0; $i < $c; $i ++)
		{
			$contents ['daten'] [$i] ['link'] ['item'] = Functions::GetLink ( array ('action' => 'item', 'id' => $contents ['daten'] [$i] ['id'] ), true );
			$contents ['daten'] [$i] ['link'] ['delete'] = Functions::GetLink ( array ('action' => 'delete', 'id' => $contents ['daten'] [$i] ['id'] ), true );
			$contents ['daten'] [$i] ['link'] ['switchactive'] = Functions::GetLink ( array ('action' => 'switchactive', 'id' => $contents ['daten'] [$i] ['id'] ), true );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'contents', $contents );
	
	}
	
	/**
	 * Zeigt einen einzelnen Content an
	 *
	 */
	protected function Item()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		
		$properties = null;
		$menuedata = null;
		$sqlManager = SqlManager::GetInstance ();
		try
		{
			$properties = $sqlManager->SelectRow ( 'modcontent', $id, array ('name', 'content', 'menueId' ) );
			//Ob der Content Aktiv ist, wird �ber den Men�punkt festgelegt
			$menuedata = $sqlManager->SelectRow ( 'sysmenue', $properties ['menueId'] );
			
			$properties ['common'] = $sqlManager->Count ( 'syscommon', 'menueId=\'' . $properties ['menueId'] . '\'' );
		
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Inhaltseigenschaften konnten nicht abgefragt werden!', CMSException::T_MODULEERROR, $ex );
		}
		
		$properties ['common'] = HTML::Checkbox ( $properties ['common'] );
		$properties ['active'] = HTML::Checkbox ( $menuedata ['active'] );
		$properties ['menue'] = Functions::MenuSelect ( $menuedata ['parent'] );
		
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'properties', $properties );
		
		JsImport::ImportEditorJs ();
	}
	
	/**
	 * Speichert einen einzelnen Content
	 *
	 */
	protected function Save()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$name = Validator::ForgeInput ( $_POST ['name'] );
		$activ = ( int ) isset ( $_POST ['active'] );
		$content = Validator::ForgeInput ( $_POST ['opendixckeditor'], false );
		$parent = Validator::ForgeInputNumber ( $_POST ['menue'] );
		$common = isset ( $_POST ['common'] );
		
		$sqlManager = SqlManager::GetInstance ();
		
		try
		{
			$sqlManager->Update ( 'modcontent', array ('name' => $name, 'content' => $content ), 'id=\'' . $id . '\'' );
			$menueId = $sqlManager->SelectItemById ( 'modcontent', 'menueId', $id );
			$sqlManager->Update ( 'sysmenue', array ('active' => $activ, 'parent' => $parent ), 'id=\'' . $menueId . '\'' );
			$commonCount = $sqlManager->Count ( 'syscommon', 'menueId=\'' . $menueId . '\'' );
			if ($common && $commonCount == 0)
				$sqlManager->Insert ( 'syscommon', array ('menueId' => $menueId ) );
			if (! $common && $commonCount == 1)
				$sqlManager->Delete ( 'syscommon', 'menueId=\'' . $menueId . '\'' );
		
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Speichern der Eigenschfaten nicht m&ouml:glich!', CMSException::T_MODULEERROR, $ex );
		}
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Inhaltsseite efolgreich gespeichert' );
	}
	
	/**
	 * Fügt einen Content hinzu
	 *
	 */
	protected function Add()
	{
		//Namen abfragen und überprüfen
		$name = Validator::ForgeInput ( $_POST ['name'] );
		if (strlen ( $name ) < 3)
		{
			throw new CMSException ( 'Der angegebene Name ist zu kurz!', CMSException::T_MODULEERROR );
		}
		
		//Template ermitteln das Standardmässig für öffentliche Inhaltsseiten verwendet wird
		$template = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Content/standardtemplate' );
		
		//Modul-ID ermitteln -> Da es sich hier um den ContentAdmin handelt wird im CMS
		//dasselbe Modul für diese Seite wie auch für die Öffentliche Content-Seite
		//verwendet. Darum kann einfach das vom CMS geladene Modul nach der ID gefragt werden	
		$moduleId = Cms::GetInstance ()->GetModule ()->GetModuleid ();
		try
		{
			//ID des Template abfragen da in der Config nur der Name steht
			$template = SqlManager::GetInstance ()->SelectItem ( 'systemplate', 'id', 'template=\'' . $template . '\'' );
			//Neuen Menüpunkt für den Inhalt einfügen
			$mid = SqlManager::GetInstance ()->Insert ( 'sysmenue', array ('type' => 1, 'name' => $name, 'template' => $template, 'class' => 'OpenContent', 'moduleId' => $moduleId ) );
			//Neue Inhaltsseite in der Datenbank erstellen
			$_GET ['id'] = SqlManager::GetInstance ()->Insert ( 'modcontent', array ('name' => $name, 'menueId' => $mid ) );
			
			// Property erstellen
			SqlManager::GetInstance() -> Insert('sysmodulproperties', array('propertyValue' => $_GET['id'], 'menueId' => $mid, 'propertyName' => 'contentid'));
		
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Einf&uuml;gen der neuen Contentseite fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
	
	}
	
	/**
	 * Löscht einen Content
	 *
	 */
	protected function Delete()
	{
		//Prüfen ob der User berechtigt ist Content-Einträge zu löschen
		$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_DELETE, $_GET ['sub'] );
		if (! $hasLaw)
		{
			throw new CMSException ( 'Sie sind nicht berechtigt Elemente zu entfernen!', CMSException::T_MODULEERROR );
		}
		
		//Abfrage und prüfen der ID
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		//Abfrage des Namens des Contents der gelöscht werden soll -> Wird für das Summary gebraucht
		$name = '';
		try
		{
			$daten = SqlManager::GetInstance ()->SelectRow ( 'modcontent', $id, array ('menueid', 'name' ) );
			$menueLaws = SqlManager::GetInstance ()->Select ( 'syslaw', array ('id' ), 'menueid=\'' . $daten ['menueid'] . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage des Namens fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		//Löschen des Eintrages
		$trashitem = new TrashItem ( 'Inhaltsseite: ' . $daten ['name'] );
		//Eintrag in der Content-Tabelle
		$trashitem->AddRecord ( new TrashRecord ( 'modcontent', $id ) );
		//Menüpunkt der auf die Inhaltsseite verweist
		$trashitem->AddRecord ( new TrashRecord ( 'sysmenue', $daten ['menueid'] ) );
		$c = count ( $menueLaws );
		for($i = 0; $i < $c; $i ++)
		{
			//Alle Berechtigungen die auf den Menüpunkt vergeben wurden
			$trashitem->AddRecord ( new TrashRecord ( 'syslaw', $menueLaws [$i] ['id'] ) );
		}
		Trash::Delete ( $trashitem );
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Inhaltsseite efolgreich gel&ouml;scht!' );
	}
}