<?php
/**
 * Gästebuch Klassen
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse zum verwalten des Gastebuchs
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.1
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		1.0     
 */
class GBook extends ModulBase
{
	/**
	 * Konstruktor
	 *
	 */
	function __construct()
	{
		
		parent::__construct ();
	}
	
	/**
	 * Funktion die alle Einträge im Gästebuch anzeigt
	 *
	 */
	protected function Overview()
	{
		CssImport::ImportCss ( 'content.css' );
		
		$daten = array ();
		try
		{
			$daten = SqlManager::GetInstance ()->SelectWithScrollMenue ( 'modgbook', array ('id', 'date', 'eintrag', 'date', 'name' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Fehler bei der Abfrage der G&auml;stebucheintr&auml;ge!', CMSException::T_MODULEERROR, $ex );
		}
		
		$c = count ( $daten ['daten'] );
		for($i = 0; $i < $c; $i ++)
		{
			$daten ['daten'] [$i] ['date'] = date ( 'd.m.Y - H:i:s', $daten ['daten'] [$i] ['date'] );
			$daten ['daten'] [$i] ['eintrag'] = substr ( $daten ['daten'] [$i] ['eintrag'], 0, 24 );
			$daten ['daten'] [$i] ['link'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'item', 'id' => $daten ['daten'] [$i] ['id'] ) );
		}
		
		$daten ['links'] ['delete'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'delete' ) );
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'gbook', $daten );
	}
	
	/**
	 * Funktion die einen einzelnen Eintrag im Gästebuch anzeigt um diesen zu bearbeiten!
	 *
	 * @return mixed
	 */
	protected function Item()
	{
		
		CssImport::ImportCss ( 'content.css' );
		
		//Abfragen und überprüfen der ID
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		//Abfragen der Daten die zu diesem Gästebucheintrag gehören
		$daten = array ();
		try
		{
			$daten = SqlManager::GetInstance ()->SelectRow ( 'modgbook', $id );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Fehler bei der Abfrage der Eigenschaften des Eintrages!', CMSException::T_MODULEERROR, $ex );
		}
		
		//Verarbeiten und Ausgabe der Daten
		$daten ['eintrag'] = Functions::decodeText ( $daten ['eintrag'], false );
		$daten ['date'] = date ( "d.m.Y - H:i:s", $daten ['date'] );
		MySmarty::GetInstance ()->OutputModuleVar ( 'daten', $daten );
	}
	
	/**
	 * Funktion um einem bearbeiteten Eintrag zu speichern
	 *
	 * @return boolean
	 */
	protected function Save()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$name = Validator::ForgeInput($_POST['name']);
		$mail = Validator::ForgeInput ( $_POST ['mail'] );
		//$homepage = Validator::ForgeInput ( $_POST ['homepage'] );
		$eintrag = Validator::ForgeInput ( $_POST ['eintrag'] );
		
		try
		{
			SqlManager::GetInstance ()->Update ( 'modgbook', array ('name' => $name, 'mail' => $mail, /*'homepage' => $homepage,*/ 'eintrag' => $eintrag ), 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Fehler beim speichern des Eintrages!', CMSException::T_MODULEERROR, $ex );
		}
	}
	
	/**
	 * Funktion um alle markierten zu l�schen!
	 *
	 */
	protected function Delete()
	{
		$i = 0;
		
		//Jeder über die Checkbox markierte Gästebucheintrag erzeugt im 
		//del-Array der Post-Daten einen Eintrag. Als Schlüssel wird jeweils
		//die ID des Eintrages mitgegeben
		foreach ( $_POST ['del'] as $key => $value )
		{
			//Prüfen ob der Schlüssel numerisch ist
			//Der Schlüssel steht im Formular und könnte daher durch den User abgeändert worden sein
			if (! is_numeric ( $key ))
			{
				continue;
			}
			//Den Eintrag in den Papierkorb verschieben
			$trashItem = new TrashItem ( 'Gel&auml;schter G&auml;stebucheintrag!' );
			$trashItem->AddRecord ( new TrashRecord ( 'modgbook', $key ) );
			Trash::Delete ( $trashItem );
			
			$i ++;
		}
		
		if ($i > 1)
			$eintr = "Eintr&auml;ge";
		else
			$eintr = "Eintrag";
		MySmarty::GetInstance ()->OutputConfirmation ( $i . " " . $eintr . " in den Papierkorb verschoeben." );
	}
}