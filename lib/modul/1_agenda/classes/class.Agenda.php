<?php

/**
 * Agenda
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse bietet Funktionen um die gesamte Agenda zu editieren
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.1
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		1.0     
 */
class Agenda extends TimerModulBase
{
	/**
	 * Filter der in der Übersicht angewendet wird
	 *
	 * @var AgendaFilter
	 */
	protected $filter;
	
	protected $isAdmin = true;
	
	/**
	 * Konstruktor
	 *
	 */
	public function __Construct()
	{
		
		//Update der Datenbank -> Termine die vorbei sind im Status ändern
		$this->update ();
		
		parent::__construct ();
		
		if (! isset ( $_GET ['action'] ))
		{
			return;
		}
		
		switch ($_GET ['action'])
		{
			case 'getVcs' :
				$this->GetVcs ();
				$this->Overview ();
				break;
			case 'editanmeldung' :
				$this->editanmeldung ();
				break;
			case 'editanmeldungsave' :
				$this->editanmeldungsave ();
				$this->Item ();
				break;
			case 'delattachement' :
				$this->DeleteAttachement ();
				$this->Item ();
				break;
			case 'getattachement' :
				$this->GetAttachement ();
				$this->Item ();
				break;
			case 'anmelden' :
				$this->Anmelden ();
				$this->Item ();
				break;
			case 'delanmeldung' :
				$this->Delangemeldet ();
			case 'anmeldungen' :
				$this->Anmeldungen ();
				break;
			case 'anmeldeform' :
				$this->Anform ();
				break;
			case 'ajaxfbb' :
				$this->AJAX_fbbrowser ();
				break;
		}
	
	}
	
	/**
	 * Löscht einen Termin
	 *
	 */
	protected function Delete()
	{
		//Prüfen ob der Besucher berechtigt ist Elemente zu löschen
		$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_DELETE, $_GET ['sub'] );
		if (! $hasLaw)
		{
			throw new CMSException ( 'Sie sind nicht berechtigt Elemente zu entfernen!', CMSException::T_MODULEERROR );
		}
		
		//Abfrage und prüfen der ID es Elements welches gelöscht werden soll
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		//Abfrage des Namens des Termins der gelöscht werden soll
		$daten = null;
		try
		{
			$daten = SqlManager::GetInstance ()->SelectRow ( 'modagendatermin', $id, array ('name', 'timerId1', 'timerId2' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Fehler bei der Abfrage des Terminnamens!', CMSException::T_MODULEERROR, $ex );
		}
		
		//Neues TrashItem erstellen und den Termin-Datensatz zuweisen
		$trashItem = new TrashItem ( 'Termin: ' . $daten ['name'] );
		$trashItem->AddRecord ( new TrashRecord ( 'modagendatermin', $id ) );
		
		//Abfragen aller Anmeldungen zu diesem Termin
		$anmeldungen = array ();
		try
		{
			$anmeldungen = SqlManager::GetInstance ()->Select ( 'modagendaanmeldung', array ('id' ), 'terminId=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der Anmeldungen fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		//Jede Anmeldung zum TrashItem hinzufügen
		$c = count ( $anmeldungen );
		for($i = 0; $i < $c; $i ++)
		{
			$trashItem->AddRecord ( new TrashRecord ( 'modagendaanmeldung', $anmeldungen [$i] ['id'] ) );
		}
		
		//Alle verlinkten Anhänge
		$attachements = null;
		try
		{
			$attachements = SqlManager::GetInstance ()->Select ( 'modagendaanhang', array ('id', 'fileId' ), 'terminId=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der Anh&auml;nge fehlgeschlagen!', CMSException::T_WARNING, $ex );
		}
		
		$c = count ( $attachements );
		for($i = 0; $i < $c; $i ++)
		{
			$trashItem->AddFile ( new TrashFile ( Filebase::GetFilePath ( $attachements [$i] ['fileId'] ) ) );
			$trashItem->AddRecord ( new TrashRecord ( 'modagendaanhang', $attachements [$i] ['id'] ) );
		}
		
		//Löschen des ganzen
		Trash::Delete ( $trashItem );
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Termin in den Papierkorb verschoben' );
	}
	
	public function GetVcs()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		//Daten abfragen
		$daten = array ();
		try
		{
			$daten = SqlManager::GetInstance ()->SelectRow ( 'modagendatermin', $id, array ('place', 'begin', 'end', 'name' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfragen der Termindaten fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		//Wenn $daten['ende'] == 0 ist ein offendes Ende
		if ($daten ['end'] == 0)
		{
			//Dann als Ende den beginn eintragen um ein gültiges File zu erstellen
			$daten ['end'] = $daten ['begin'];
		}
		$start = date ( 'Ymd\THi00', strtotime ( $daten ['begin'] ) );
		$ende = date ( 'Ymd\THi00', strtotime ( $daten ['end'] ) );
		
		$filedata = "BEGIN:VCALENDAR\r\nVERSION:1.0\r\nPRODID:-//Microsoft Corporation//Outlook MIMEDIR//DE\r\nBEGIN:VEVENT\r\nCATEGORIES:\r\nDTSTART:{$start}T000000Z\r\nDTEND:{$ende}T000000Z\r\nLOCATION;ENCODING=ISO-8859-1:{$daten['place']}\r\nDESCRIPTION;ENCODING=ISO-8859-1:\r\nSUMMARY;ENCODING=UTF-8:{$daten['name']}\r\nEND:VEVENT\r\nEND:VCALENDAR";
		
		//Datei erstellen
		$tempPath = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Agenda/tempfolder' );
		
		$file = $tempPath . FileManager::GetNameAsFileName ( $daten ['name'] ) . '.vcs';
		FileManager::CreateFile ( $file, $filedata, true );
		
		//Datei an den Besucher senden
		$e = HTTP_Download::staticSend ( array ('file' => $file ), true );
		if ($e != '')
		{
			throw new CMSException ( 'VCS konnte nicht gesendet werden:' . $e->getMessage (), CMSException::T_MODULEERROR );
		}
	
	}
	
	/**
	 * Wird im Bootvorgang aufgerufen und prüft die Termine auf Timer-Actions
	 *
	 */
	public function CheckAgendaOnBoot()
	{
		
		$time = time ();
		$daten = array ();
		//Erst all abfragen die nicht aktiv, den timer auf on haben und schon aktiv sen ü�ssten
		try
		{
			$daten = SqlManager::GetInstance ()->Select ( 'modagendatermin', array ('id' ), 'timer_aktiv=\'1\' AND aktiv=\'0\' AND timer_start < \'' . $time . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Inaktive konnten nicht abgefragt werden!', CMSException::T_MODULEERROR, $ex );
		}
		$c = count ( $daten );
		for($i = 0; $i < $c; $i ++)
		{
			try
			{
				SqlManager::GetInstance ()->Update ( 'modagendatermin', array ('aktiv' => 1 ), 'id=\'' . $daten [$i] ['id'] . '\'' );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'Eintrag konnte nicht geupdatet werden!', CMSException::T_MODULEERROR, $ex );
			}
		}
		
		//Nun alle abfragen bei denen der Timer abgelaufen ist
		try
		{
			$daten = SqlManager::GetInstance ()->Select ( 'modagendatermin', array ('id', 'timer_action' ), 'timer_aktiv=\'1\' AND aktiv=\'1\' AND timer_stop < \'' . $time . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Aktive mit abgelaufenen Timer konnten nicht abgefragt werden!', CMSException::T_MODULEERROR, $ex );
		}
		
		$c = count ( $daten );
		for($i = 0; $i < $c; $i ++)
		{
			Functions::runAction ( $daten [$i] ['timer_action'], $daten [$i] ['id'] );
		}
	}
	
	/**
	 * Deaktiviert einen Termin durch Aufruf des Timers
	 *
	 * @param int $timerId ID des Timers über welche der zu löschende Termin gefunden wird
	 */
	public static function TimerDeactivate($timerId)
	{
		$id = Agenda::GetIdOfTimerId ( $timerId );
		Agenda::SwitchActive ( $id, 0 );
	}
	
	/**
	 * Löscht einen Termin durch Aufruf des Timers
	 *
	 * @param int $timerId	ID des Timers über welche der zu löschende Termin gefunden wird
	 */
	public function TimerDelete($timerId)
	{
		$id = Agenda::GetIdOfTimerId ( $timerId );
		try
		{
			$_GET ['id'] = $id;
			$this->Delete ();
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Termin konnte nicht gel&ouml;scht werden!', CMSException::T_MODULEERROR, $ex );
		}
	}
	
	/**
	 * Aktiviert einen Termin durch Aufruf des Timers
	 *
	 * @param int $timerId	ID des Timers über welche der zu löschende Termin gefunden wird
	 */
	public static function TimerActivate($timerId)
	{
		$id = Agenda::GetIdOfTimerId ( $timerId );
		Agenda::SwitchActive ( $id, 1 );
	}
	
	/**
	 * Gibt die ID des Termins zurück, welche den gewünschten Timer ausführt
	 *
	 * @param int $timerId
	 * @return int	ID des Termins
	 */
	private static function GetIdOfTimerId($timerId)
	{
		try
		{
			return SqlManager::GetInstance ()->SelectItem ( 'modagendatermin', 'id', 'timerId1=\'' . $timerId . '\' OR timerId2=\'' . $timerId . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der ID aus Timer-ID fehlgeschlagen!', CMSException::T_SYSTEMERROR, $ex );
		}
	}
	
	/**
	 * Wechselt den Aktiv-Status eines Termins
	 *
	 * @param int $id		ID des Termins
	 * @param int $value	0 => Inaktiv, 1 => Aktiv
	 */
	private static function SwitchActive($id, $value)
	{
		try
		{
			SqlManager::GetInstance ()->Update ( 'modagendatermin', array ('active' => $value ), 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Termin konnte nicht deaktiviert werden!', CMSException::T_MODULEERROR, $ex );
		}
	}
	
	/**
	 * Wird immer beim Booten aufgerufen. So werden alle Termine
	 *
	 */
	public function Update()
	{
		
		try
		{
			SqlManager::GetInstance ()->Update ( 'modagendatermin', array ('state' => 1 ), '(end < NOW() OR end is NULL) AND begin < NOW() AND state = \'2\'' );
		
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Update der Termin-Datenbank fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
	}
	
	/**
	 * Fügt einen Termin in die Tabelle hinzu und zeigt diesen noch leeren Termin an
	 *
	 */
	protected function Add()
	{
		$name = Validator::ForgeInput ( $_POST ['addname'] );
		if (strlen ( $name ) < 3)
		{
			throw new CMSException ( 'Termin konnte nicht erstellt werden! Name zu kurz!', CMSException::T_MODULEERROR );
		}
		try
		{
			$_GET ['id'] = SqlManager::GetInstance ()->Insert ( 'modagendatermin', array ('name' => $name, 'active' => 0, 'state' => 2, 'registrationDeadline' => array ('date' => 'NOW' ), 'begin' => array ('date' => 'NOW' ), 'end' => array ('date' => 'NOW' ), 'contactId' => 0, 'description' => '', 'place' => '' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Fehler beim eintragen in die Datenbank', CMSException::T_MODULEERROR, $ex );
		}
	
	}
	
	/**
	 * Zeigt alle angemeldeten leute an
	 *
	 * @return void
	 */
	private function Anmeldungen()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$daten = null;
		$termin = null;
		try
		{
			$daten = SqlManager::GetInstance ()->Select ( 'modagendaanmeldung', array ('id', 'firstName', 'name', 'phone', 'email', 'comment', 'street', 'residence' ), 'terminId=\'' . $id . '\'' );
			$termin = SqlManager::GetInstance ()->SelectItem ( 'modagendatermin', 'name', 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der Anmeldungen fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		$c = count ( $daten );
		for($i = 0; $i < $c; $i ++)
		{
			$daten [$i] ['link'] ['delete'] = Functions::GetLink ( array ('action' => 'delanmeldung', 'aid' => $daten [$i] ['id'] ), true );
			$daten [$i] ['comment'] = Functions::DecodeText ( $daten [$i] ['comment'], true );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'termin', $termin );
		MySmarty::GetInstance ()->OutputModuleVar ( 'zurueckLink', Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'item', 'id' => $id ) ) );
		MySmarty::GetInstance ()->OutputModuleVar ( 'anmeldungen', $daten );
	}
	
	/**
	 * Funktion die einen Anhang an den Benutzer verschickt
	 *
	 */
	protected function GetAttachement()
	{
		$aid = Validator::ForgeInput ( $_GET ['aid'] );
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $aid ) || ! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		//Prüfen ob der aufgerufene Anhang auch mit diesem Termin verlinkt ist
		//Somit wird verhindert, dass der Besucher Zugriff auf sämtliche, mit irgendwelchen Terminen
		//verlinkten Dateien erhält
		if (! Law::IsInFileLinkTable ( $id, $aid, 'modagendaanhang', 'terminId' ))
		{
			throw new CMSException ( 'Sie sind nicht berechtigt diesen Anhang runterzuladen!', CMSException::T_MODULEERROR );
		}
		$fileId = null;
		try
		{
			$fileId = SqlManager::GetInstance ()->SelectItem ( 'modagendaanhang', 'fileId', 'id=\'' . $aid . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der File-ID fehlgeschlagen!', CMSException::T_WARNING, $ex );
		}
		
		Filebase::DownloadFileById ( $fileId );
	
	}
	
	/**
	 * Zeit das anmeldeformular an
	 *
	 */
	private function EditAnmeldung()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$daten = array ();
		try
		{
			$daten = SqlManager::GetInstance ()->SelectRow ( 'modagendatermin', $id, array ('registrationText', 'registrationFirstname', 'registrationName', 'registrationStreet', 'registrationResidence', 'registrationPhone', 'registrationEmail', 'registrationComment', 'registrationConfirmationUser', 'registrationConfirmationContact' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der Formularinformationen fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		Functions::InitEditor ( $daten ['registrationComment'], 200 );
		
		$agendaform = array ();
		$agendapflicht = array ();
		
		foreach ( $daten as $key => $value )
		{
			//$key = str_replace('registration', '', $key);
			if ($value == '1' || $value == '1x')
			{
				$agendaform [$key] = ' checked=\"checked\" ';
			}
			if ($value == '1x')
			{
				$agendapflicht [$key] = ' checked=\"checked\" ';
			}
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'form', $agendaform );
		MySmarty::GetInstance ()->OutputModuleVar ( 'duty', $agendapflicht );
		MySmarty::GetInstance ()->OutputModuleVar ( 'saveLink', Functions::getLink ( array ('sub' => $_GET ['sub'], 'action' => 'editanmeldungsave', 'id' => $_GET ['id'] ) ) );
	}
	
	/**
	 * Speichert das Formular ab
	 *
	 */
	private function Editanmeldungsave()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$felder = array ('vorname' => '', 'nachname' => '', 'telefon' => '', 'email' => '', 'strasse' => '', 'wohnort' => '', 'bemerkung' => '' );
		if (! isset ( $_POST ['pflicht'] ))
		{
			$_POST ['pflicht'] = array ();
		}
		if (! isset ( $_POST ['bes'] ))
		{
			$_POST ['bes'] = array ();
		}
		
		$text = Validator::ForgeInput ( $_POST ['editor'], false );
		
		$fields = array ('firstname' => 0, 'name' => 0, 'residence' => 0, 'street' => 0, 'email' => 0, 'phone' => 0, 'comment' => 0 );
		$confirmation = array ('user' => 0, 'contact' => 0 );
		
		foreach ( $fields as $key => $value )
		{
			if (isset ( $_POST ['registration'] ['view'] [$key] ))
			{
				$fields [$key] = '1';
			}
			if (isset ( $_POST ['registration'] ['duty'] [$key] ))
			{
				$fields [$key] = '1x';
			}
		}
		
		if (isset ( $_POST ['registration'] ['confirmation'] ['user'] ))
		{
			$confirmation ['user'] = 1;
		}
		
		if (isset ( $_POST ['registration'] ['confirmation'] ['contact'] ))
		{
			$confirmation ['contact'] = 1;
		}
		
		try
		{
			SqlManager::GetInstance ()->Update ( 'modagendatermin', array ('registrationText' => $text, 'registrationFirstname' => $fields ['firstname'], 'registrationName' => $fields ['name'], 'registrationStreet' => $fields ['street'], 'registrationResidence' => $fields ['residence'], 'registrationPhone' => $fields ['phone'], 'registrationEmail' => $fields ['email'], 'registrationComment' => $fields ['comment'], 'registrationConfirmationUser' => $confirmation ['user'], 'registrationConfirmationContact' => $confirmation ['contact'] ), 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Daten konnten nicht gespeichert werden!', CMSException::T_MODULEERROR, $ex );
		}
	
	}
	
	/**
	 * Funktion mit der man einen Anhang löscht
	 *
	 */
	private function DeleteAttachement()
	{
		$aid = Validator::ForgeInput ( $_GET ['aid'] );
		if (! is_numeric ( $aid ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		//Prüfen ob der aufgerufene Anhang auch mit diesem Termin verlinkt ist
		//Somit wird verhindert, dass der Besucher Zugriff auf sämtliche, mit irgendwelchen Terminen
		//verlinkten Dateien erhält
		if (! Law::IsInFileLinkTable ( $id, $aid, 'modagendaanhang', 'terminId' ))
		{
			throw new CMSException ( 'Sie sind nicht berechtigt diesen Anhang runterzuladen!', CMSException::T_MODULEERROR );
		}
		
		$fId = null;
		try
		{
			$fId = SqlManager::GetInstance ()->SelectItem ( 'modagendaanhang', 'fileId', 'id=\'' . $aid . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der FilebaseID fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		$trashItem = new TrashItem ( 'Gel&ouml;schter Anhang' );
		$trashItem->AddRecord ( new TrashRecord ( 'modagendaanhang', $aid ) );
		
		Trash::Delete ( $trashItem );
		
		Filebase::UnUseFileById ( $fId );
	}
	
	/**
	 * Speichert einen Termin ab
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
		
		$termin = Validator::ForgeInput ( $_POST ['termin'] );
		$termin ['time'] ['begin'] = FormParser::ProcessDateInput ( $termin ['time'] ['begin'] );
		$termin ['time'] ['end'] = FormParser::ProcessDateInput ( $termin ['time'] ['end'] );
		//$termin ['registration'] ['deadline'] = FormParser::ProcessDateInput ( $termin ['registration'] ['deadline'] );
		

		if (isset ( $_POST ['anhang'] ))
		{
			Functions::AddAttachement ( 'modagendaanhang', 'terminId', $id );
		}
		
		$termin ['active'] = ( int ) isset ( $termin ['active'] );
		//$termin ['registration'] ['possible'] = ( int ) isset ( $termin ['registration'] ['possible'] );
		//$termin ['registration'] ['view'] = ( int ) isset ( $termin ['registration'] ['view'] );
		

		$termin ['description'] = Validator::ForgeInput ( $_POST ['opendixckeditor'], false, true );
		
		if (! isset ( $termin ['berichtId'] ))
		{
			$termin ['berichtId'] = null;
		}
		
		if (isset ( $termin ['time'] ['openend'] ))
		{
			$termin ['time'] ['end'] = null;
		}
		
		if (! is_numeric ( $termin ['state'] ))
		{
			MySmarty::GetInstance ()->OutputWarning ( 'Ung&uuml;ltiger Status &uuml;bergeben!' );
			$termin ['state'] = 1;
		
		}
		
		if($termin['contactId'] == "NULL")
			$termin['contactId'] = null;
		
		try
		{
			SqlManager::GetInstance ()->Update ( 'modagendatermin', array ('active' => $termin ['active'], 'state' => $termin ['state'], 'agendaId' => $termin ['agendaId'], 'name' => $termin ['name'], 'begin' => $termin ['time'] ['begin'], 'end' => $termin ['time'] ['end'], 'description' => $termin ['description'], 'place' => $termin ['place'], 'contactId' => $termin ['contactId'], /*'registrationView' => $termin ['registration'] ['view'], 'registrationPossible' => $termin ['registration'] ['possible'],*/ 'berichtId' => $termin ['berichtId'], 'berichtMenueId' => $termin ['berichtMenueId'], /*'registrationDeadline' => $termin ['registration'] ['deadline']*/ ), 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Speichern fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
	
	}
	
	/**
	 * Zeigt einen einzelnen Termin an
	 *
	 * @return mixed false wenn fehlgeschlagen ansonsten nichts
	 */
	protected function Item()
	{
		
		//Überprüfen der ID des Termins der angezeigt werden soll
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		//Importieren aller benötigten Javascript-Dateien
		JsImport::ImportSystemJS ( 'fbbrowser.js' );
		JsImport::ImportModuleJS ( 'agenda', 'agenda.js' );
		JsImport::RunJs ( "CheckBerichtOnBoot();" );
		JsImport::ImportEditorJs ();
		
		CssImport::ImportCss ( 'content.css' );
		CssImport::ImportCss ( 'fbbrowser.css' );
		
		$daten = array ();
		try
		{
			$daten = SqlManager::GetInstance ()->SelectRow ( 'modagendatermin', $id, array ('active', 'state', 'agendaId', 'place', 'name', 'timerId1', 'timerId2', 'contactId', 'begin', 'end', 'description', 'registrationView', 'registrationPossible', 'registrationDeadline', 'berichtmenueid', 'registrationDeadline' => array ('function' => 'UNIX_TIMESTAMP', 'alias' => 'registrationDeadlineUnix' ), 'berichtId' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Fehler beim Abfragen der Termin-Eigenschaften!', CMSException::T_MODULEERROR, $ex );
		}
		
		//Nun müssen die daten aus der db noch verarbeitet werden für die ausgabe
		//Stauts
		$outState = array ();
		switch ($daten ['state'])
		{
			case TerminState::finished :
				$outState ['finished'] = ' selected=\"selected\" ';
				break;
			case TerminState::upcoming :
				$outState ['before'] = ' selected=\"selected\" ';
				
				break;
			case TerminState::canceled :
				$outState ['canceled'] = ' selected=\"selected\" ';
				break;
		}
		
		$daten ['state'] = $outState;
		$daten ['kontakt'] = HTML::Select ( 'sysuser', array ('name', 'firstname' ), $daten ['contactId'] );
		
		if ($daten ['end'] == '')
		{
			$daten ['openEnd'] = HTML::Checkbox ( 1 );
		}
		
		$modId = Cms::GetInstance ()->GetModule ()->GetModuleId ();
		$daten ['timer'] ['actions'] = HTML::Select ( 'sysaction', 'name', '', '', 'moduleId=\'' . $modId . '\'' );
		$daten ['timer'] ['data'] = Timer::GetTimerForEntity ( $modId, $id );
		
		$daten ['description'] = Functions::DecodeText ( $daten ['description'], true );
		$daten ['active'] = HTML::Checkbox ( $daten ['active'] );
		$daten ['agenda'] = HTML::Select ( 'modagenda', 'name', $daten ['agendaId'] );
		
		if ($daten ['registrationPossible'] == 1 && $daten ['registrationDeadlineUnix'] < time ())
		{
			$daten ['registrationPossible'] = 0;
			try
			{
				SqlManager::GetInstance ()->Update ( 'modagendatermin', array ('registrationPossible' => 0 ), 'id=\'' . $id . '\'' );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'Fehler beim Updaten der Anmeldem&ouml;glichkeit!', CMSException::T_MODULEERROR, $ex );
			}
		}
		
		$daten ['registration'] ['possible'] = HTML::Checkbox ( $daten ['registrationPossible'] );
		$daten ['registration'] ['view'] = HTML::Checkbox ( $daten ['registrationView'] );
		
		$daten ['berichtmenueid'] = HTML::Select ( 'sysmenue', 'name', $daten ['berichtmenueid'], '', 'class=\'OpenBericht\' AND active=\'1\'' );
		
		try
		{
			$daten ['selectedberichtmenueid'] = SqlManager::GetInstance ()->SelectItem ( 'modagendatermin', 'berichtMenueId', 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der Men&uuml;id fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		if ($daten ['selectedberichtmenueid'] != 0)
		{
			$moduleId = null;
			try
			{
				$moduleId = SqlManager::GetInstance ()->SelectItem ( 'sysmenue', 'moduleId', 'id=\'' . $daten ['selectedberichtmenueid'] . '\'' );
			
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'Abfrage der Men&uuml;id fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
			}
			$mod = new Module ( $moduleId );
			$gId = $mod->GetDbConfiguration ( 'groupid', $daten ['selectedberichtmenueid'] );
			
			$daten ['berichtId'] = HTML::Select ( 'modbericht', 'title', $daten ['berichtId'], '', 'gId=\'' . $gId . '\'' );
		}
		
		$c = count ( $daten ['berichtmenueid'] );
		for($i = 0; $i < $c; $i ++)
		{
			if (! Law::HasLaw ( Law::T_VIEW, $daten ['berichtmenueid'] [$i] ['value'] ))
			{
				unset ( $daten ['berichtmenueid'] [$i] );
			}
		}
		
		//Anhänge
		$daten ['attachement'] = $this->GetAttachements ( $id );
		$c = count ( $daten ['attachement'] );
		for($i = 0; $i < $c; $i ++)
		{
			$daten ['attachement'] [$i] ['name'] = $daten ['attachement'] [$i] ['folder'] . '/' . $daten ['attachement'] [$i] ['file'];
			$linkArr = array ('get', 'delete' );
			$linkArr ['get'] = Functions::GetLink ( array ('action' => 'getattachement', 'id' => $id, 'aid' => $daten ['attachement'] [$i] ['aid'] ), true );
			$linkArr ['delete'] = Functions::GetLink ( array ('action' => 'delattachement', 'id' => $id, 'aid' => $daten ['attachement'] [$i] ['aid'] ), true );
			$daten ['attachement'] [$i] ['link'] = $linkArr;
		
		}
		
		if (count ( $daten ['attachement'] ) == 0)
		{
			$daten ['attachement'] = null;
		}
		
		//Anzahl der anmeldungen abfragen
		$daten ['anzanmeldungen'] = $this->GetAnzAnmeldungen ( $id );
		$daten ['registrationDeadline'] = date ( "d.m.Y G:i:s", strtotime ( $daten ['registrationDeadline'] ) );
		
		//Links generieren
		$daten ['link'] ['save'] = Functions::GetLink ( array ('action' => 'save', 'id' => $id ), true );
		$daten ['link'] ['editanmeldung'] = Functions::GetLink ( array ('action' => 'editanmeldung', 'id' => $id ), true );
		$daten ['link'] ['anmeldungen'] = Functions::GetLink ( array ('action' => 'anmeldungen', 'id' => $id ), true );
		$daten ['link'] ['abbrechen'] = Functions::GetLink ( array ('action' => 'item', 'id' => $id ), true );
		
		//Editor::InitEditor($daten['description'], 300);
		MySmarty::GetInstance ()->OutputModuleVar ( 'termin', $daten );
	}
	
	/**
	 * Gibt alle Anhänge zu einem Termin zurück
	 *
	 * @param int $terminId	ID des Termins zu dem die Anhänge abgefragt werden
	 * @return array Alle Datensätze für die Anhänge
	 */
	protected function GetAttachements($terminId)
	{
		try
		{
			return SqlManager::GetInstance ()->QueryAndFetch ( 'SELECT 	maa.id 		AS `aid`, 
																		maa.fileId 	AS `anhangid`, 
																		sfb.file 	AS `file`, 
																		sfb.folder 	AS `folder`
					 											FROM 
					 													modagendaanhang maa, 
					 													sysfilebase sfb 
					 											WHERE 
					 													sfb.id=maa.fileId 
					 												AND 
					 													maa.terminId=\'' . $terminId . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der Anhaenge fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
	}
	
	/**
	 * Gibt die Anzahl der angemeldeten Personen für einen Termin zurück
	 *
	 * @param int $terminId	ID des Termins zu dem die Anmeldungen abgefragt werden
	 * @return int	Anzahl der Anmeldungen
	 */
	protected function GetAnzAnmeldungen($terminId)
	{
		try
		{
			return SqlManager::GetInstance ()->Count ( 'modagendaanmeldung', 'terminId=\'' . $terminId . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der bereits angemeldeten Fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
	}
	
	/**
	 * Setzt die Filter-Optionen die aus dem Formular kommen in der AgendaFilter-Instanz
	 *
	 */
	protected function SetFilter()
	{
		//Erst $this -> filter mit einer AgendaFilter-Instanz füllen
		if (isset ( $_SESSION ['agenda_filter'] ) && ! isset ( $_POST ['setfilter'] ))
		{
			//.. wenn eine Filter-Instanz in der Session gespeichert ist und kein neuer Filter
			//gesetzt wurde, dann die Session-AgendaFilter-Instanz laden
			$this->filter = unserialize ( $_SESSION ['agenda_filter'] );
			if (($this->filter->GetIsAdminFilter () && ! $this->isAdmin) || (! $this->filter->GetIsAdminFilter () && $this->isAdmin))
			{
				$this->filter = new AgendaFilter ( $this->isAdmin );
			}
		} else
		{
			//Ansonsten eine neue AgendaFilterInstanz erstellen da entweder noch
			//keine existiert (es wurde noch kein Filter gesetzt) order ein 
			//neuer Filter gesetzt wurde
			$this->filter = new AgendaFilter ( $this->isAdmin );
		}
		
		if (! isset ( $_POST ['filter'] ))
		{
			return;
		}
		
		//Alle Filteroptionen setzen
		$this->filter->SetOption ( 'upcoming', isset ( $_POST ['filter'] ['upcoming'] ) );
		
		$searchData = Trim ( Validator::ForgeInput ( $_POST ['filter'] ['search'] ) );
		if (isset ( $searchData [0] ))
		{
			$this->filter->SetOption ( 'search', $searchData );
		}
		
		$begin = Validator::ForgeInput ( $_POST ['filter'] ['begin'] );
		if ($begin ['Month'] != 0 && $begin ['Day'] != 0 && $begin ['Year'] && checkdate ( $begin ['Month'], $begin ['Day'], $begin ['Year'] ))
		{
			$this->filter->SetOption ( 'begin', $begin );
		} else
		{
			$this->filter->SetOption ( 'begin', 'notime' );
		}
		
		$end = Validator::ForgeInput ( $_POST ['filter'] ['end'] );
		if ($end ['Month'] != 0 && $end ['Day'] != 0 && $end ['Year'] && checkdate ( $end ['Month'], $end ['Day'], $end ['Year'] ))
		{
			$this->filter->SetOption ( 'end', $end );
		} else
		{
			$this->filter->SetOption ( 'end', 'notime' );
		}
		
		$type = Validator::ForgeInput ( $_POST ['filter'] ['type'] );
		if (is_numeric ( $type ))
		{
			$this->filter->SetOption ( 'type', $type );
		}
	
	}
	
	/**
	 * Zeigt alle Agendaeinträge an
	 *
	 */
	protected function Overview()
	{
		//Importieren von Javascript
		JsImport::ImportModuleJS ( 'agenda', 'agenda.js' );
		JsImport::RunJs ( 'SwitchView();' );
		
		CssImport::ImportCss ( 'agenda.css' );
		CssImport::ImportCss ( 'content.css' );
		
		//Filter setzen
		$this->SetFilter ();
		
		
		$daten = array ();
		try
		{
			//Abrufen aller Daten
			$daten = $this->GetDates ();
			
			//Prüfen ob Daten vorhanden sind mit den gewählten Filterkriterien
			if (count ( $daten ['daten'] ) == 0)
			{
				
				//Sollte das nicht der Fall sein wird der Filter zurückgesetzt und die Abfrage erneut gestaretet
				$this->filter->Init ();
				$daten = $this->GetDates ();
			}
		
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der Termine fehlgeschlagen', CMSException::T_MODULEERROR, $ex );
		}
		
		$c = count ( $daten ['daten'] );
		for($i = 0; $i < $c; $i ++)
		{
			$daten ['daten'] [$i] ['begin'] = date ( 'd.m.Y H:i', strtotime ( $daten ['daten'] [$i] ['begin'] ) );
			$daten ['daten'] [$i] ['link'] ['alone'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'item', 'id' => $daten ['daten'] [$i] ['id'] ) );
			$daten ['daten'] [$i] ['link'] ['del'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'delete', 'id' => $daten ['daten'] [$i] ['id'] ) );
		}
		
		$_SESSION ['agenda_filter'] = serialize ( $this->filter );
		//$this -> filter -> OutputFilterOptions();
		MySmarty::GetInstance ()->OutputModuleVar ( 'dates', $daten );
		$this->OutputFilterOptions ();
	
	}
	
	/**
	 * Gibt die Filter-Optionen aus
	 *
	 */
	protected function OutputFilterOptions()
	{
		$filterOptions = array ();
		
		if ($this->filter->GetOption ( 'upcoming' ))
		{
			$filterOptions ['upcoming'] = HTML::Checkbox ( 1 );
		}
		
		$begin = $this->filter->GetOption ( 'begin' );
		if ($begin != 'notime')
		{
			$begin = $begin ['Year'] . '-' . $begin ['Month'] . '-' . $begin ['Day'];
		}
		
		$filterOptions ['begin'] = $begin;
		$end = $this->filter->GetOption ( 'end' );
		if ($end != 'notime')
		{
			$end = $end ['Year'] . '-' . $end ['Month'] . '-' . $end ['Day'];
		}
		$filterOptions ['end'] = $end;
		
		if ($this->filter->GetIsAdminFilter () || Cms::GetInstance ()->GetModule ()->GetDbConfiguration ( 'showallgroups' ))
		{
			//Der Type-Select wird nur angezeigt, wenn es sich beim Besucher um einen Administrator
			//handelt (bei dem werden alle Agenda-Typen angezeigt)...
			$filterOptions ['type'] = HTML::Select ( 'modagenda', 'name', $this->filter->GetOption ( 'type' ) );
		
		} elseif (is_array ( Cms::GetInstance ()->GetModule ()->GetDbConfiguration ( 'groupid' ) ))
		{
			//..oder falls mehrere Typen öffentlich angezeigt werden sollen
			$avaibleTypes = Cms::GetInstance ()->GetModule ()->GetDbConfiguration ( 'groupid' );
			
			$filterOption = $this->filter->GetOption ( 'type' );
			
			$c = count ( $avaibleTypes );
			for($i = 0; $i < $c; $i ++)
			{
				$ri = array ();
				$ri ['value'] = $avaibleTypes [$i] ['propertyValue'];
				try
				{
					$ri ['name'] = SqlManager::GetInstance ()->SelectItem ( 'modagenda', 'name', 'id=\'' . $avaibleTypes [$i] [0] . '\'' );
				} catch ( SqlManagerException $ex )
				{
					throw new CMSException ( 'Abfrage eines GruppenNamens fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
				}
				
				if ($ri ['value'] == $filterOption)
				{
					$ri ['selected'] = true;
				}
				$filterOptions ['type'] [] = $ri;
			}
		}
		
		$filterOptions ['search'] = $this->filter->GetOption ( 'search' );
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'filter', $filterOptions );
	}
	
	/**
	 * Gibt alle Daten zurück die zu den definierten Filter-Kriterien passen
	 *
	 * @return unknown
	 */
	protected function GetDates($order = 'DESC')
	{
		return SqlManager::GetInstance ()->SelectWithScrollMenue ( 'modagendatermin', array ('id', 'begin', 'name', 'state' ), $this->filter->GetWhere (), 'begin', $order );
	}
	
	/**
	 * Funktion die ein anmeldeformular anzeigt
	 *
	 * @param String $vorname
	 * @param String $nachname
	 * @param String $strasse
	 * @param String $wohnort
	 * @param String $telefon
	 * @param String $email
	 * @param String $bemerkung
	 * @return mixed false wenn fehlgeschlagen ansonsten nichts
	 */
	public function Anform($vorname = "", $nachname = '', $strasse = '', $wohnort = '', $telefon = '', $email = '', $bemerkung = '')
	{
		
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$_SESSION ['secureLink'] = true;
		
		$daten = array ();
		try
		{
			$daten = SqlManager::GetInstance ()->SelectRow ( 'modagendatermin', $id, array ('registrationText', 'registrationFirstName', 'registrationName', 'registrationStreet', 'registrationResidence', 'registrationPhone', 'registrationEmail', 'registrationComment', 'registrationConfirmationUser', 'registrationPossible' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage der Anmelde-Daten fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		if (count ( $daten ) == 0)
		{
			throw new CMSException ( 'Zu diesem Termin k&ouml;nnen Sie sich nicht anmelden!', CMSException::T_MODULEERROR );
		}
		
		if ($daten ['registrationPossible'] == 0)
		{
			throw new CMSException ( 'Zu diesem Termin k&ouml;nnen Sie sich nicht anmelden!', CMSException::T_MODULEERROR );
		}
		
		unset ( $daten ['registrationPossible'] );
		
		foreach ( $daten as $key => $value )
		{
			if ($value == '1' || $value == '1x')
			{
				$key = str_replace ( 'registration', '', $key );
				$ansicht [$key] ['view'] = 1;
				//$ansicht[$key]['vorbelegt'] = $$key;
				if ($value == '1x')
				{
					$ansicht [$key] ['pflicht'] = true;
				}
			}
		
		}
		
		$ansicht ['anlink'] = Functions::GetLink ( array ('action' => 'signup', 'id' => $id ), true );
		$ansicht ['text'] = $daten ['registrationText'];
		MySmarty::GetInstance ()->OutputModuleVar ( 'anform', $ansicht );
	}
	
	/**
	 * Funktion nmit der man einen angemeldeten aus der Liste entfernen kann
	 *
	 * @return mixed false wenn fehlgeschlagen ansonsten nichts
	 * @access private
	 */
	private function Delangemeldet()
	{
		$id = Validator::ForgeInput ( $_GET ['aid'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		$name = '';
		try
		{
			$c = SqlManager::GetInstance ()->Count ( 'modagendaanmeldung', 'id=\'' . $id . '\'' );
			if ($c == 0)
			{
				throw new CMSException ( 'Zu dieser ID ist keine Anmeldung vorhanden!', CMSException::T_MODULEERROR );
			}
			$name = SqlManager::GetInstance ()->SelectRow ( 'modagendaanmeldung', $id, array ('firstName', 'name' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Abfrage des Namens fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		$name = implode ( ' ', $name );
		
		$trashItem = new TrashItem ( 'Anmeldung von ' . $name );
		$trashItem->AddRecord ( new TrashRecord ( 'modagendaanmeldung', $id ) );
		Trash::Delete ( $trashItem );
	}
	
	/**
	 * Funktion mit der man sich an einen Temrin anmelden kann.
	 *
	 * @return boolean
	 */
	protected function Signup()
	{
		
		//Prüfen ob die Seite nicht gerade erst aufgerufen wurde
		if (isset ( $_SESSION ['secureLink'] ) && ! $_SESSION ['secureLink'])
		{
			//Wenn ja um anmelden per F5 zu verhindern funktion verlassen
			return true;
		}
		$_SESSION ['secureLink'] = false;
		
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$accessData = array ();
		$formData = array ();
		try
		{
			$accessData = SqlManager::GetInstance ()->SelectRow ( 'modagendatermin', $id, array ('registrationPossible', 'registrationConfirmationContact', 'registrationConfirmationUser', 'contactId', 'registrationText', 'name' ) );
			$formData = SqlManager::GetInstance ()->SelectRow ( 'modagendatermin', $id, array ('registrationFirstName', 'registrationName', 'registrationStreet', 'registrationResidence', 'registrationPhone', 'registrationEmail', 'registrationComment' ), MYSQL_ASSOC );
		} 

		catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Formularinformationen konnten nicht abgefragt werden!', CMSException::T_MODULEERROR, $ex );
		}
		if (count ( $accessData ) == 0 || $accessData ['registrationPossible'] != 1)
		{
			throw new CMSException ( 'Zu diesem Termin können Sie sich nicht anmelden!', CMSException::T_MODULEERROR );
		}
		
		$rowNames = array ('FirstName' => 'firstName', 'Name' => 'name', 'Street' => 'street', 'Residence' => 'residence', 'Phone' => 'phone', 'Email' => 'email', 'Comment' => 'comment' );
		
		$insertArr = array ('terminId' => $id );
		foreach ( $formData as $key => $value )
		{
			$shortcut = str_replace ( 'registration', '', $key );
			
			$data = Validator::ForgeInput ( $_POST [$shortcut] );
			
			if ($value == '1x' && strlen ( $data ) == 0)
			{
				MySmarty::GetInstance ()->OutputWarning ( 'Sie haben nicht alle Felder korrekt ausgef&uuml;llt!' );
				return false;
			}
			
			$insertArr [$rowNames [$shortcut]] = $data;
		
		}
		
		try
		{
			
			SqlManager::GetInstance ()->Insert ( 'modagendaanmeldung', $insertArr );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Anmeldung fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		//Evtl. Bericht an Kontaktperson senden!
		if ($accessData ['registrationConfirmationContact'] == 1)
		{
			$contactEmail = NULL;
			try
			{
				$contactEmail = SqlManager::GetInstance ()->SelectItem ( 'sysuser', 'email', 'id=\'' . $accessData ['contactId'] . '\'' );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'Abfrage der Kontakt-Email fehlgeschlagen!', CMSException::T_MODULEERROR );
			}
			if (Validator::IsValidEmail ( $contactEmail ))
			{
				Functions::sendmail ( $contactEmail, 'Neue Anmeldung', 'Es gibt eine neue Anmeldung zum Termin: ' . $daten ['titel'] );
				MySmarty::GetInstance ()->OutputConfirmation ( 'Die Kontaktperson wurde per E-Mail informiert!' );
			} else
			{
				MySmarty::GetInstance ()->OutputWarning ( 'E-Mail an Kontaktperson konnte nicht gesendet werden. Nehmen Sie zur Sicherheit pers&ouml;nlich mit der zust&auml;ndigen Person Konktakt auf!' );
			}
		
		}
		
		//Evetl. eine Anmeldebestätigung an den Benutzer senden
		if ($accessData ['registrationConfirmationUser'] == 1)
		{
			if (Validator::IsValidEmail ( Validator::ForgeInput ( $_POST ['Email'] ) ))
			{
				Functions::sendmail ( Validator::ForgeInput ( $_POST ['Email'] ), 'Anmeldebestaetigung - ' . $daten ['titel'], 'Sie haben sich erfolgreich angemeldet!' );
				MySmarty::GetInstance ()->OutputConfirmation ( 'Anmeldebest&auml;tigung an Sie wurde versendet!' );
			} else
			{
				MySmarty::GetInstance ()->OutputWarning ( 'Ihre angegebene E-Mail konnte nicht als korrekt identifiziert werden! An sie wurde keine Best&auml;tigung gesendet!' );
			}
		}
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Sie haben sich erfolgreich angemeldet!' );
		return true;
	}
	
	/**
	 * Anzeige des Filebasebrowsers
	 * 
	 * Nach afruf dieser Funktion wird das Script per die() beendet!
	 *
	 */
	public static function AJAX_fbbrowser()
	{
		//Prüfen ob der User berechtigt ist
		$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_VIEW, $_GET ['sub'] );
		if (! $hasLaw)
		{
			AJAX::ThrowError ( 'Sie sind nicht berechtigt sich den Filebasebrowser anzuzeigen!' );
		}
		//Ausgabe des Filebrowsers
		AJAX::Fbbrowser ();
		//Da dies ein AJAX-Aufruf ist wird hier das Script abgebrochen ansonsten würde noch die ganze Seite ausgegebenw erden
		die ();
	}
	
	public static function GetMenuePointRestriction()
	{
		return '';
	}
}
