<?php
/**
 * Sponsorenverwaltung
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Admin Bereich für den Sponsoren Teil
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 1.0     
 */

class Sponsor extends ModulBase
{
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{
		parent::__construct ();
		
		CssImport::ImportCss ( 'content.css' );
		
		if (! isset ( $_GET ['action'] ))
			return;
		
		switch ($_GET ['action'])
		{
			case 'up' :
				$this->up ();
				
				$this->Overview ();
				break;
			case 'down' :
				$this->down ();
				$this->Overview ();
				break;
			case 'gup' :
				$this->Group (-1);
				$this -> ReorderFolge('modsponsorgruppe', '');
				$this -> Overview();
				break;
			case 'gdown' :
				$this->Group (1);
				$this -> ReorderFolge('modsponsorgruppe', '');
				$this -> Overview();
				break;
		}
	}
	
	private function Group($newPosFix)
	{
		//ID's aus der URL auslesen
		$id = Validator::ForgeInput ( $_GET ['id'] );
		//Prüfen ob es sich um Integer-Werte handelt
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Keine g&uuml;ltige ID angegeben!', CMSException::T_MODULEERROR );
		}
		
	
		
		//Aktuelle Position des Benutzers in der angegebenen Gruppe auslesen
		try
		{
			$position = SqlManager::GetInstance ()->SelectItem ( 'modsponsorgruppe', 'folge', 'id=\'' . $id . '\'' );
			$newposition = $position + $newPosFix;
			
			if ($newposition < 0)
			{
				MySmarty::GetInstance ()->OutputConfirmation ( 'Die Gruppe befindet sich bereits an der Spitze!' );
				return;
			}
			
			SqlManager::GetInstance ()->Update ( 'modsponsorgruppe', array ('folge' => $position ), 'folge=\'' . $newposition . '\'' );
			SqlManager::GetInstance ()->Update ( 'modsponsorgruppe', array ('folge' => $newposition ), 'id=\'' . $id . '\'' );
		
		} catch ( Exception $ex )
		{
			throw new CMSException ( 'SQL Fehler', CMSException::T_MODULEERROR, $ex );
		}
	}
	
	/**
	 * Zeigt einen einzigen Sponsor an
	 *
	 * @return mixed false wenn fehlgeschlagen ansonsten nichts
	 */
	protected function Item()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ung&uuml;ltige ID', CMSException::T_MODULEERROR );
		}
		
		$daten = SqlManager::GetInstance ()->SelectRow ( 'modsponsor', $id );
		
		$daten ['links'] ['save'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'save', 'sid' => $id ) );
		$daten ['links'] ['abbrechen'] = Functions::GetLink ( array ('sub' => $_GET ['sub'] ) );
		$daten ['gruppe'] = HTML::Select ( 'modsponsorgruppe', 'name', $daten ['gId'] );
		
		if ($daten ['bild'] != null)
			$daten ['bild'] = Filebase::GetFilePath ( $daten ['bild'], true );
		
		$daten ['frontpage'] = HTML::Checkbox ( $daten ['frontpage'] );
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'sponsor', $daten );
	}
	
	/**
	 * Funktion zum hinzufügen eines Sponsors
	 *
	 */
	protected function Add()
	{
		
		$data = Validator::ForgeInput ( $_POST ['add'] );
		
		
//		if (strlen ( $data ['name'] ) < 3)
//		{
//			throw new CMSException ( 'Der Name des Sponsors muss mindestens drei Zeichen lang sein', CMSException::T_MODULEERROR );
//			return;
//		}
		
		$sqlMan = SqlManager::GetInstance ();
		
		$max = $this->getMaxFolge ( $data ['gId'] );
		
		$_GET ['id'] = $sqlMan->Insert ( 'modsponsor', array ('gId' => $data ['gId'], 'name' => $data ['name'], 'folge' => $max ) );
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Neuer Sponsor erfasst' );
	}
	
	private function getMaxFolge($gId)
	{
		$query = "SELECT MAX(folge) FROM modsponsor WHERE gId = '$gId'";
		$result = SqlManager::GetInstance ()->Query ( $query );
		return mysql_result ( $result, 0 ) + 1;
	}
	
	protected function Delete()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'ID ist nicht korrekt', CMSException::T_MODULEERROR );
		}
		
		//TODO: Sponsor Thumbnail auch löschen
		SqlManager::GetInstance ()->DeleteById ( 'modsponsor', $id );
		
		MySmarty::GetInstance()->OutputConfirmation('Sponsor erfolgreich gel&ouml;scht');
	
	}
	
	/**
	 * Funktion zum abspeichern eines Sponsors
	 *
	 * @return boolean
	 * @todo sollte schonein bild mit diesem namen existieren wird der neue filename verändert und das wird noch nicht berücksichtigt!
	 */
	protected function Save()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'ID ist nicht korrekt', CMSException::T_MODULEERROR );
		}
		
		$data = Validator::ForgeInput ( $_POST );
		
		$beforeData = SqlManager::GetInstance ()->SelectRow ( 'modsponsor', $id );
		
		if ($data ['gId'] != $beforeData ['gId'])
		{
			$data ['folge'] = $this->getMaxFolge ( $data ['gId'] );
		}
		
		if (isset ( $_FILES ['newImage'] ) && $_FILES ['newImage'] ['name'] != '')
		{
			//TODO: Altes Bild löschen
			$path = Cms::GetInstance ()->GetModule ()->GetConfiguration ( 'Path/image' );
			$data ['imgId'] = Filebase::UploadFile ( $path, 'newImage' );
			$srcPath = Filebase::GetFilePath ( $data ['imgId'], true );
			
			try
			{
				$image = new Image ( $srcPath );
				$image->SaveThumbnail ( $srcPath, 88, 256 );
			} catch ( CMSException $ex )
			{
				MySmarty::GetInstance ()->OutputWarning ( 'Bild konnte nicht gespeichert werden: ' . $ex->getMessage () );
			}
		
		} else
		{
			$data ['imgId'] = $beforeData ['bild'];
		}
		
		$data ['frontpage'] = isset ( $data ['frontpage'] );
		
		SqlManager::GetInstance ()->Update ( 'modsponsor', array ('gId' => $data ['gId'], 'frontpage' => $data ['frontpage'], 'beschreibung' => $data ['desc'], 'bild' => $data ['imgId'], 'url' => $data ['link'] ), 'id=\'' . $id . '\'' );
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Sponsor erfolgreich gespeichert' );
	}
	
	/**
	 * Funktion zum anzeigen aller Sponsoren
	 *
	 */
	public function Overview()
	{
		$output = array ();
		$groups = SqlManager::GetInstance ()->SelectAll ( 'modsponsorgruppe', '', 'folge' );
		for($i = 0; $i < count ( $groups ); $i ++)
		{
			$items = SqlManager::GetInstance ()->Select ( 'modsponsor', array ('id', 'name' ), 'gId=\'' . $groups [$i] ['id'] . '\'', 'folge' );
			
			for($k = 0; $k < count ( $items ); $k ++)
			{
				$items [$k] ['link'] ['item'] = Functions::GetLink ( array ('action' => 'item', 'id' => $items [$k] ['id'] ), true );
				$items [$k] ['link'] ['up'] = Functions::GetLink ( array ('action' => 'up', 'id' => $items [$k] ['id'] ), true );
				$items [$k] ['link'] ['down'] = Functions::GetLink ( array ('action' => 'down', 'id' => $items [$k] ['id'] ), true );
				$items [$k] ['link'] ['delete'] = Functions::GetLink ( array ('action' => 'delete', 'id' => $items [$k] ['id'] ), true );
			
			}
			
			if(count($items) > 0)
			{
			$items [0] ['first'] = true;
			$items [count ( $items ) - 1] ['last'] = true;
				
			}
			
			
			$uplink = Functions::GetLink ( array ('action' => 'gup', 'id' => $groups [$i] ['id'] ), true );
			$downlink = Functions::GetLink ( array ('action' => 'gdown', 'id' => $groups [$i] ['id'] ), true );
			
			$output [] = array ('name' => $groups [$i] ['name'], 'links' => array ('up' => $uplink, 'down' => $downlink ), 'items' => $items );
		}
		
		$output [0] ['first'] = true;
		$output [count ( $output ) - 1] ['last'] = true;
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'groups', HTML::Select ( 'modsponsorgruppe', 'name' ) );
		MySmarty::GetInstance ()->OutputModuleVar ( 'sponsoren', $output );
	}
	
	private function up()
	{
		$this->SwitchPosition ( - 1 );
	}
	
	private function down()
	{
		$this->SwitchPosition ( 1 );
	}
	
	private function SwitchPosition($newPosFix)
	{
		//ID's aus der URL auslesen
		$id = Validator::ForgeInput ( $_GET ['id'] );
		//Prüfen ob es sich um Integer-Werte handelt
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Keine g&uuml;ltige ID angegeben!', CMSException::T_MODULEERROR );
		
		}
		
		//Aktuelle Position des Benutzers in der angegebenen Gruppe auslesen
		try
		{
			$position = SqlManager::GetInstance ()->SelectItem ( 'modsponsor', 'folge', 'id=\'' . $id . '\'' );
			$groupId = SqlManager::GetInstance ()->SelectItem ( 'modsponsor', 'gId', 'id=\'' . $id . '\'' );
			$newposition = $position + $newPosFix;
			
			if ($newposition < 0)
			{
				MySmarty::GetInstance ()->OutputConfirmation ( 'Der Benutzer befindet sich bereits an der Spitze!' );
				return;
			}
			
			//Den User um eins zurückstufen der gerade vor dem zu erhebenden User ist
			SqlManager::GetInstance ()->Update ( 'modsponsor', array ('folge' => $position ), 'gId=\'' . $groupId . '\' AND folge=\'' . $newposition . '\'' );
			
			//Den anderen User erhöhen
			SqlManager::GetInstance ()->Update ( 'modsponsor', array ('folge' => $newposition ), 'id=\'' . $id . '\' AND gId=\'' . $groupId . '\'' );
			
			$this -> ReorderFolge('modsponsor', 'gId=\'' . $groupId . '\'');
		
		} catch ( Exception $ex )
		{
			throw new CMSException ( 'SQL Fehler', CMSException::T_MODULEERROR, $ex );
		}
	}
	
	private function move($side)
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'ID ist nicht korrekt', CMSException::T_MODULEERROR );
		}
		
		if ($side)
		{
			$ziel = - 1;
		} else
		{
			$ziel = 1;
		}
		
		$row = SqlManager::GetInstance ()->SelectRow ( 'modsponsor', $id );
		SqlManager::GetInstance ()->Update ( 'modsponsor', array ('folge' => $row ['folge'] ), 'folge=\'' . ($row ['folge'] + $ziel) . '\' AND gId = \'' . $row ['gId'] . '\'' );
		SqlManager::GetInstance ()->Update ( 'modsponsor', array ('folge' => $row ['folge'] + $ziel ), 'id=\'' . $id . '\'' );
	
	}
	
	private function ReorderFolge($table, $where)
	{
		$data = SqlManager::GetInstance()->SelectAll($table, $where, 'folge');
		for($i=0; $i<count($data); $i++)
		{
			SqlManager::GetInstance()->Update($table, array('folge' => $i), 'id=\''. $data[$i]['id'] .'\'');
		}
	}

}
