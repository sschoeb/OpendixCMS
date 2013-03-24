<?php
/**
 * Basisklasse für die Module
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * ModulBase
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.0
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		2.0     
 */
abstract class ModulBase
{
	/**
	 * Basiskonstruktor für alle Module
	 * 
	 * - 	überprüft in jedem Falle, ob der User auf den aufgerufenen Menpunkt 
	 * die Besichtigungsberechtigung besitzt
	 * -	Wird keine "action" mitgegeben, wird immer die "Overview"-Methode aufgerufen
	 * -	Folgende "actions" sind sonst noch bekannt:
	 * + add 
	 * + save
	 * --> 	Für die save-Action können folgende vier "Speicherbuttons" auf
	 * dem Template erstellt werden:
	 * -- save
	 * -- saveandclose
	 * -- reset
	 * -- cancel
	 * + delete
	 * + item
	 *
	 */
	public function __construct()
	{
		$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_VIEW, $_GET ['sub'] );
		if (! $hasLaw)
		{
			throw new CMSException ( 'Sie sind nicht berechtigt diese Seite anzuschauen!', CMSException::T_MODULEERROR );
		}
		if (! isset ( $_GET ['action'] ))
		{
			$this->Overview ();
			return;
		}
		
		switch ($_GET ['action'])
		{
			case 'add' :
				$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_ADD, $_GET ['sub'] );
				if (! $hasLaw)
				{
					throw new CMSException ( 'Sie sind nicht berechtigt Elemente hinzuzuf&uuml;gen!', CMSException::T_MODULEERROR );
				}
				$this->Add ();
				
				$this->Item ();
				
				break;
			case 'save' :
				//Prüfen ob der User berechtigt ist etwas zu speichern
				$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_EDIT, $_GET ['sub'] );
				if (! $hasLaw)
				{
					throw new CMSException ( 'Sie sind nicht berechtigt diese Seite zu bearbeiten!', CMSException::T_MODULEERROR );
				}
				
				if (isset ( $_POST ['cancel'] ))
				{
					$this->Overview ();
					return;
				}
				
				if (! isset ( $_POST ['reset'] ))
				{
					$this->Save ();
				}
				
				//Prüfen ob "Speichern und Schliessen" gedrükt wurde...
				if (isset ( $_POST ['saveandclose'] ))
				{
					//... wenn ja dann die Ãœbersicht anzeigen...
					$this->Overview ();
					return;
				}
			
				
				//.. ansonsten den einzelnen Eintrag neu aus der Datenbank laden und anzeigen!
				$this->Item ();
				break;
			case 'delete' :
				$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_DELETE, $_GET ['sub'] );
				if (! $hasLaw)
				{
					throw new CMSException ( 'Sie sind nicht berechtigt Elemente zu entfernen!', CMSException::T_MODULEERROR );
				}
				
				$this->Delete ();
				$this->Overview ();
				break;
			case 'item' :
				$this->Item ();
				break;
			default :
				//Nichts machen, hier wird die aktion im Konstruktor des Moduls abgehandelt
				break;
		}
	}
	
	protected function Overview()
	{
		throw new CMSException ( 'Overview-Methode wurde f&uuml;r dieses Modul nicht implementiert!', CMSException::T_MODULEERROR );
	}
	
	protected function Save()
	{
		throw new CMSException ( 'Save-Methode wurde f&uuml;r dieses Modul nicht implementiert!', CMSException::T_MODULEERROR );
	}
	
	protected function Item()
	{
		throw new CMSException ( 'Item-Methode wurde f&uuml;r dieses Modul nicht implementiert!', CMSException::T_MODULEERROR );
	}
	
	protected function Delete()
	{
		throw new CMSException ( 'Delete-Methode wurde f&uuml;r dieses Modul nicht implementiert!', CMSException::T_MODULEERROR );
	}
	
	protected function Add()
	{
		throw new CMSException ( 'Add-Methode wurde f&uuml;r dieses Modul nicht implementiert!', CMSException::T_MODULEERROR );
	}
}