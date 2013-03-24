<?php
/**
 * Öffentliche Agenda
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Zeigt die öffentliche Agenda an
 * 
 * Erbt von "Agenda" und überschreibt den Konstruktor damit man nicht auf Funktionen
 * zugreiffen kann die nur der Admin gebrauchen darf
 * 
 * @author 	Stefan Schöb
 * @package OpendixCMS
 * @version 1.1
 * @copyright 2006-2009, Stefan Schöb
 * @since 1.0     
 */
class Openagenda extends Agenda
{
	
	protected $isAdmin = false;
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{
		
		$this->update ();
		
		if (! isset ( $_GET ['action'] ))
		{
			$this->Overview ();
			return;
		}
		
		switch ($_GET ['action'])
		{
			case 'item' :
				$this->Item ();
				break;
			case 'getvcs' :
				$this->GetVcs ();
				$this->Overview ();
				break;
			case 'getattachement' :
				$this->GetAttachement ();
				$this->Item ();
				break;
			case 'register' :
				$this->Anform ();
				break;
			case 'signup' :
				$this->Signup ();
				$this->Item ();
				break;
			case 'registered' :
				$this->Registrations ();
				break;
			default :
				$this->Overview ();
				break;
		}
	}
	
	private function Registrations()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ung&uuml;ltige ID &uuml;bergeben!', CMSException::T_MODULEERROR );
		}
		
		$daten = null;
		try
		{
			//Prüfeno b es erlaubt ist die Angemeldeten pPersonen anzuschauen
			$signup = SqlManager::GetInstance ()->SelectItem ( 'modagendatermin', 'registrationView', 'id=\'' . $id . '\'' );
			if ($signup == 0)
			{
				throw new CMSException ( 'Die angemeldeten Personen k&ouml;nnen nicht eingesehen werden.', CMSException::T_MODULEERROR );
			}
			$daten = SqlManager::GetInstance ()->Select ( 'modagendaanmeldung', array ('firstName', 'name' ), 'terminId=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Abfrage der Registrationen fehlgeschlagen', CMSException::T_MODULEERROR );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'people', $daten );
		MySmarty::GetInstance ()->OutputModuleVar ( 'backLink', Functions::GetLink ( array ('id' => $id, 'action' => 'item' ), true ) );
	}
	
	/**
	 * Zeigt alle Agendaeinträge an die aktiv sind
	 *
	 */
	protected function Overview()
	{
		CssImport::ImportCss ( 'agenda.css' );
		
		//Nur die Aktiven anzeigen
		$this->SetFilter ();
		$daten = array ();
		try
		{
			//Abrufen aller Daten
			$daten = $this->GetDates ('ASC');
			
			//Prüfen ob Daten vorhanden sind mit den gewählten Filterkriterien
			if (count ( $daten ['daten'] ) == 0)
			{
				MySmarty::GetInstance ()->OutputWarning ( 'Zu der angegebenen Filter-Konfiguration konnten keine passenden Anl&auml;sse gefunden werden. Der Filter wurde zur&uuml;ckgesetzt!' );
				
				//Sollte das nicht der Fall sein wird der Filter zurückgesetzt und die Abfrage erneut gestaretet
				$this->filter->Init ();
				$daten = $this->GetDates ('ASC');
			}
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der Termine fehlgeschlagen', CMSException::T_MODULEERROR, $ex );
		}
		
		$c = count ( $daten ['daten'] );
		for($i = 0; $i < $c; $i ++)
		{
			$daten ['daten'] [$i] ['begin'] = date ( 'd.m.Y', strtotime ( $daten ['daten'] [$i] ['begin'] ) );
			$daten ['daten'] [$i] ['link'] ['item'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'item', 'id' => $daten ['daten'] [$i] ['id'] ) );
			$daten ['daten'] [$i] ['link'] ['getvcs'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'getvcs', 'id' => $daten ['daten'] [$i] ['id'] ) );
		
		//$daten['daten'][$i]['link']['del'] = functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'del', 'id' => $daten['daten'][$i]['id']));
		}
		
		$_SESSION ['agenda_filter'] = serialize ( $this->filter );
		$this->OutputFilterOptions ();
		MySmarty::GetInstance ()->OutputModuleVar ( 'daten', $daten );
	}
	
	/**
	 * Zeigt einen einzelnen Termin an
	 *
	 */
	protected function Item()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		CssImport::ImportCss ( 'anlass.css' );
		
		//Wenn es sich nicht ume inen Administrator handelt muss sichergestellt werden, 
		//dass nicht auf einen Termin zugegriffen wird, welcher sich nicht in einer
		//Gruppe befindet auf die der User Zugriff hat
		$groups = Cms::GetInstance ()->GetModule ()->GetDbConfiguration ( 'groupid' );
		if (! Law::IsItemOfGroup ( $id, $groups, 'modagendatermin', 'agendaId' ))
		{
			//TODO: Berechtigungspr�fung 
		//throw new CMSException ( 'Sie sind nicht berechtigt diesen Termin anzuzeigen!', CMSException::T_MODULEERROR );
		}
		
		$daten = null;
		try
		{
			$daten = SqlManager::GetInstance ()->SelectRow ( 'modagendatermin', $id, array ('name', 'description', 'begin', 'end', 'place', 'contactId', 'berichtConnection', 'state', 'registrationView', 'berichtMenueId', 'berichtId', 'registrationPossible', 'registrationDeadline' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der Termin-Eigenschaften fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		if ($daten ['berichtId'] != 0)
		{
			
			$modId = null;
			try
			{
				$daten ['bericht'] ['name'] = SqlManager::GetInstance ()->SelectItem ( 'modbericht', 'title', 'id=\'' . $daten ['berichtId'] . '\'' );
				$modId = SqlManager::GetInstance ()->SelectItem ( 'sysmenue', 'moduleId', 'id=\'' . $daten ['berichtMenueId'] . '\'' );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'Abfrage des Berichtnamens fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
			}
			
			$berichtModule = new Module ( $modId );
			$berichtParams = $berichtModule->GetLinkParams ( $daten ['berichtConnection'], array ('sub' => $daten ['berichtMenueId'], 'id' => $daten ['berichtId'] ) );
			$daten ['bericht'] ['link'] = Functions::GetLink ( $berichtParams );
		}
		
		$daten ['description'] = nl2br ( $daten ['description'] );
		
		if ($daten ['contactId'] != null)
		{
			$user = Cms::GetInstance ()->GetUser ( $daten ['contactId'] );
			$daten ['contact'] ['details'] = $user->GetContactProperties ();
			$daten ['contact'] ['image'] = $user->GetImagePath ();
		}
		
		$daten ['anzanmeldungen'] = $this->GetAnzAnmeldungen ( $id );
		
		$daten ['registration'] ['deadline'] = date ( "d.m.Y G:i:s", strtotime ( $daten ['registrationDeadline'] ) );
		$daten ['registration'] ['possible'] = $daten ['registrationPossible'];
		$daten ['registration'] ['view'] = $daten ['registrationView'];
		
		$daten ['begin'] = date ( "d.m.Y G:i:s", strtotime ( $daten ['begin'] ) );
		$daten ['end'] = date ( "d.m.Y G:i:s", strtotime ( $daten ['end'] ) );
		
		$daten ['signup'] ['link'] ['view'] = Functions::GetLink ( array ('action' => 'registered', 'id' => $id ), true );
		$daten ['signup'] ['link'] ['possible'] = Functions::GetLink ( array ('action' => 'register', 'id' => $id ), true );
		
		$daten ['attachements'] = $this->GetAttachements ( $id );
		$c = count ( $daten ['attachements'] );
		for($i = 0; $i < $c; $i ++)
		{
			
			$daten ['attachements'] [$i] ['name'] = /*$daten ['attachements'] [$i] ['folder'] . '/' .*/ $daten ['attachements'] [$i] ['file'];
			$daten ['attachements'] [$i] ['link'] ['get'] = Functions::GetLink ( array ('action' => 'getattachement', 'id' => $id, 'aid' => $daten ['attachements'] [$i] ['aid'] ), true );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'termin', $daten );
	}

}