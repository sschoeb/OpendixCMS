<?php

/**
 * Newsverwaltung
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse mit der man News verwalten kann
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		1.0
 */
class News extends TimerModulBase
{
	/**
	 * Konstruktor
	 *
	 */
	function __construct()
	{
		parent::__construct ();
		
		CssImport::ImportCss ( 'news.css' );
		
		if (! isset ( $_GET ['action'] ))
		{
			return;
		}
		
		switch ($_GET ['action'])
		{
			case 'switchactive' :
				$this->SwitchActive ();
				$this->Overview ();
				break;
			case 'getlinkedfile' :
				$this->GetFile ();
				$this->Item ();
				break;
			case 'ajaxfbb' :
				$this->AJAX_fbbrowser ();
				break;
			case 'ajaxconnmenue' :
				$this->AJAX_ConnMenue ();
				break;
			case 'ajaxconnconn' :
				$this->AJAX_ConnConn ();
				break;
			case 'ajaxfbb' :
				$this->AJAX_fbb ();
				break;
			case 'ajaxaddlink' :
				$this->AJAX_AddLink ();
			case 'ajaxdeletelink' :
				$this->AJAX_DeleteLink ();
				break;
		
		}
	
	}
	
	/**
	 * Zeigt einen einzelnen Newseintrag an
	 *
	 */
	protected function Item()
	{
		
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ung&uuml;ltige ID &uuml;bergeben!', CMSException::T_MODULEERROR );
		}
		
		JsImport::ImportModuleJS ( 'news', 'news.js' );
		JsImport::ImportSystemJS ( 'fbbrowser.js' );
		
		JsImport::RunJs ( "StartUp()" );
		
		$data = null;
		try
		{
			$data = SqlManager::GetInstance ()->SelectRow ( 'modnews', $id );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Abfrage des News-Eintrags fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		$data ['panel'] = HTML::Checkbox ( $data ['panel'] );
		$data ['frontpage'] = HTML::Checkbox ( $data ['frontpage'] );
		$data ['active'] = HTML::Checkbox ( $data ['active'] );
		$data ['newsfeed'] = HTML::Checkbox ( $data ['newsfeed'] );
		
		$modId = Cms::GetInstance ()->GetModule ()->GetModuleId ();
		
		$data ['timer'] ['actions'] = HTML::Select ( 'sysaction', 'name', '', '', 'moduleId=\'' . $modId . '\'' );
		$data ['timer'] ['data'] = Timer::GetTimerForEntity ( $modId, $id );
		
		$data ['menueselect'] = Cms::GetInstance ()->GetMenue ()->Output ();
	
		
		try
		{
			$data ['links'] = SqlManager::GetInstance ()->Select ( 'modnewslink', array ('id', 'name', 'linktype' ), 'newsId=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Abfrage der Links fehlgeschlagen', CMSException::T_MODULEERROR, $ex );
		}
		$c = count ( $data ['links'] );
		for($i = 0; $i < $c; $i ++)
		{
			$data ['links'] [$i] ['link'] ['delete'] = Functions::GetLink ( array ('id' => $id, 'action' => 'deleteLink', 'linkId' => $data ['links'] [$i] ['id'] ), true );
			$data ['links'] [$i] ['link'] ['link'] = $this->GenerateLink ( $data ['links'] [$i] ['linktype'], $data ['links'] [$i] ['id'] );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'item', $data );
	}
	
	public static function GenerateLink($linktype, $linkId)
	{
		
		switch ($linktype)
		{
			case 'FILEBASE' :
				
				return Functions::GetLink ( array ('action' => 'getlinkedfile', 'linkId' => $linkId ), true );
				break;
			case 'INTERNAL' :
				$modId = null;
				$ids = null;
				try
				{
					$ids = SqlManager::GetInstance ()->SelectRow ( 'modnewslink', $linkId, array ('menueId', 'connectionId', 'elementId' ) );
					
					$modId = SqlManager::GetInstance ()->SelectItem ( 'sysmenue', 'moduleId', 'id=\''.$ids ['menueId'] .'\'' );
					
				} catch ( SqlManagerException $ex )
				{
					throw new CMSException ( 'SQL-Abfrage der Module-ID fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
				}
				
				
				$modObj = new Module ( $modId );
				$linkParams = $modObj->GetLinkParams ( $ids ['connectionId'], array ('elementId' => $ids ['elementId'], 'menueId' => $ids ['menueId'] ) );
				
//				echo "<pre>";
//				var_dump($linkParams);
//				echo "</pre>";
//				
				return Functions::GetLink ( $linkParams );
				break;
			case 'EXTERNAL' :
				try
				{
					return SqlManager::GetInstance ()->SelectItem ( 'modnewslink', 'website', 'id=\'' . $linkId . '\'' );
				} catch ( SqlManagerException $ex )
				{
					throw new CMSException ( 'SQL-Abfrage der Website eines Links fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
				}
				break;
		}
	}
	
	public static function GetFile()
	{
		$id = Validator::ForgeInput ( $_GET ['linkId'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ung&uuml;ltige ID &uuml;bergeben!', CMSException::T_MODULEERROR );
		}
		
		$fileId = null;
		try
		{
			$fileId = SqlManager::GetInstance ()->SelectItem ( 'modnewslink', 'fileId', 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Abfrage der File-ID fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		Filebase::DownloadFileById ( $fileId );
	}
	
	/**
	 * Zeigt alle vorhandenenNewseinträge in der Übersicht an
	 *
	 */
	protected function Overview()
	{
		$data = null;
		try
		{
			$data = SqlManager::GetInstance ()->Select ( 'modnews', array ('id', 'name', 'active', 'time' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Abfrage der Elemente fehlgeschlagen', CMSException::T_MODULEERROR, $ex );
		}
		
		$c = count ( $data );
		for($i = 0; $i < $c; $i ++)
		{
			$data [$i] ['link'] ['edit'] = Functions::GetLink ( array ('action' => 'item', 'id' => $data [$i] ['id'] ), true );
			$data [$i] ['link'] ['delete'] = Functions::GetLink ( array ('action' => 'delete', 'id' => $data [$i] ['id'] ), true );
			$data [$i] ['link'] ['switchactive'] = Functions::GetLink ( array ('action' => 'switchactive', 'id' => $data [$i] ['id'] ), true );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'overview', $data );
	}
	
	/**
	 * Speichert einen Newseintrag
	 *
	 */
	protected function Save()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ung&uuml;ltige ID &uuml;bergeben!', CMSException::T_MODULEERROR );
		}
		
		$data = Validator::ForgeInput ( $_POST ['news'] );
		//Daten pruefen
		if (strlen ( $data ['name'] ) < 3)
		{
			$data ['name'] = $data ['name'] . str_repeat ( '_', 3 - strlen ( $data ['name'] ) );
			MySmarty::GetInstance ()->OutputWarning ( 'Der angegebene Name war zu kurz und wurde erweiternt!' );
		}
		$data ['active'] = ( int ) isset ( $data ['active'] );
		$data ['frontpage'] = ( int ) isset ( $data ['frontpage'] );
		$data ['panel'] = ( int ) isset ( $data ['panel'] );
		$data ['newsfeed'] = ( int ) isset ( $data ['newsfeed'] );
		
		$data ['time'] = FormParser::ProcessDateInput ( $data ['time'] );
		
		
		//Daten speichern
		try
		{
			SqlManager::GetInstance ()->Update ( 'modnews', array ('name' => $data ['name'], 'active' => $data ['active'], /*'text' => $data ['text'], 'frontpage' => $data ['frontpage'], 'panel' => $data ['panel'],*/ 'newsfeed' => $data ['newsfeed'], 'time' => $data ['time']), 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Speichern der Daten fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
	}
	
	/**
	 * Fügt einen Newseintag hinzu
	 *
	 */
	protected function Add()
	{
		$name = Validator::ForgeInput ( $_POST ['name'] );
		if (strlen ( $name ) < 3)
		{
			throw new CMSException ( 'Der anegebene Name ist zu kurz!', CMSException::T_MODULEERROR );
		}
		
		try
		{
			//Nachher wird die Item-Methode uafgerufen die als GET-Parameter die
			//ID des zu editierenden News-Elements enthält
			$_GET ['id'] = SqlManager::GetInstance ()->Insert ( 'modnews', array ('name' => $name, 'time' => date(DATE_ISO8601)));
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Einf&uuml;gen des neuen News-Elements fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Newseintrag wurde erfolgreich hinzugef&uuml;gt!' );
	}
	
	/**
	 * Löscht einen Newseintrag
	 *
	 */
	protected function Delete()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ung&uuml;ltige ID &uuml;bergeben!', CMSException::T_MODULEERROR );
		}
		$data = null;
		try
		{
			$data = SqlManager::GetInstance ()->SelectRow ( 'modnews', $id, array ('name', 'timerId1', 'timerId2' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Abfrage des News-Namens fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		$trash = new TrashItem ( 'Gel&ouml;schter News-Eintrag: ' . $data ['name'] );
		$trash->AddRecord ( new TrashRecord ( 'systimer', $data ['timerId1'] ) );
		$trash->AddRecord ( new TrashRecord ( 'systimer', $data ['timerId2'] ) );
		
		//Sämtliche links löschen
		$links = null;
		try
		{
			$links = SqlManager::GetInstance ()->Select ( 'modnewslink', array ('id' ), 'newsId=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Abfrage der Links fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		
		$c = count ( $links );
		for($i = 0; $i < $c; $i ++)
		{
			try
			{
				$linkdata = SqlManager::GetInstance ()->SelectRow ( 'modnewslink', $links [$i] ['id'], array ('linktype', 'fileId' ) );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'SQL-Abfrage des Linktyps fehlgeschlagen', CMSException::T_MODULEERROR, $ex );
			}
			
			$cc = count ( $linkdata );
			for($ii = 0; $ii < $cc; $ii ++)
			{
				if ($linkdata [$ii] ['linktype'] == 'FILEBASE')
				{
					Filebase::UnUseFileById ( $linkdata [$ii] ['fileId'] );
				}
			}
			$trash->AddRecord ( new TrashRecord ( 'modnewslink', $links [$i] ['id'] ) );
		}
		
		$trash->AddRecord ( new TrashRecord ( 'modnews', $id ) );
		
		Trash::Delete ( $trash );
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'News-Eintrag in den Papierkorb verschoben' );
	}
	
	/**
	 * Ändert den Status eines News-Eintrags
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function SwitchActive()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ung&uuml;ltige ID &uuml;bergeben!', CMSException::T_MODULEERROR );
		}
		
		Functions::SwitchActive ( 'modnews', $id );
	}
	
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
	
	private function AJAX_ConnMenue()
	{
		if (! Law::HasLaw ( Law::T_VIEW, $_GET ['sub'] ))
		{
			AJAX::ThrowError ( 'Sie sind nicht berechtigt auf diesen Bericht zuzugreiffen!' );
		}
		
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			AJAX::ThrowError ( 'Ung&uuml;ltige ID &uuml;bergeben!' );
		}
		
		$menueData = null;
		try
		{
			$menueData = SqlManager::GetInstance() -> SelectItem('sysmenue', 'type', 'id=\''. $id .'\'');	
		}
		catch(SqlManagerException $ex)
		{
			AJAX::ThrowError('SQL-Fehler');
		}
		
		if($menueData['type'] == 3)
		{
			AJAX::ThrowError('Sie koennen nicht auf einen Menuepunkt verlinken der auf eine externe Seite zeigt. Benutzen Sie dazu die Option "Webseite"!');
		}
		
		$moduleId = Module::GetModuleIdOutOfMenueId ( $id );
		$module = new Module ( $moduleId );
		
		try
		{
			$connections = $module->GetConnections ();
		} catch ( CMSException $ex )
		{
			$connections = array ();
		}
		
		$xmlDoc = new DOMDocument ();
		$root = XML::CreateDocument ( $xmlDoc, 'connections' );
		
		$c = count ( $connections );
		for($i = 1; $i <= $c; $i ++)
		{
			$root = XML::AddElement ( $xmlDoc, $root, 'item', array ('id' => $connections [$i] ['id'], 'name' => $connections [$i] ['name'] ) );
		}
		
		$data = $xmlDoc->saveXML ();
		
		AJAX::OutputXmlData ( $data );
	
	}
	
	private function AJAX_ConnConn()
	{
		if (! Law::HasLaw ( Law::T_VIEW, $_GET ['sub'] ))
		{
			AJAX::ThrowError ( 'Sie sind nicht berechtigt auf diesen Bericht zuzugreiffen!' );
		}
		
		$mid = Validator::ForgeInput ( $_GET ['mid'] );
		$cid = Validator::ForgeInput ( $_GET ['cid'] );
		if (! is_numeric ( $mid ) || ! is_numeric ( $cid ))
		{
			AJAX::ThrowError ( 'Ung&uuml;ltige ID &uuml;bergeben!' );
		}
		
		$moduleId = Module::GetModuleIdOutOfMenueId ( $mid );
		$module = new Module ( $moduleId );
		$elements = $module->GetConnectionElements ( $mid, $cid );
		
		$xmlDoc = new DOMDocument ();
		$root = XML::CreateDocument ( $xmlDoc, 'elements' );
		
		$c = count ( $elements );
		for($i = 0; $i < $c; $i ++)
		{
			$root = XML::AddElement ( $xmlDoc, $root, 'item', array ('id' => $elements [$i] ['id'], 'name' => $elements [$i] [1] ) );
		}
		$data = $xmlDoc->saveXML ();
		
		AJAX::OutputXmlData ( $data );
	}
	
	private function AJAX_fbb()
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
	
	private function AJAX_AddLink()
	{
		//Prüfen ob der User berechtigt ist
		$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_EDIT, $_GET ['sub'] );
		if (! $hasLaw)
		{
			AJAX::ThrowError ( 'Sie sind nicht berechtigt Links hinzuzufügen!' );
		}
		
		$data = Validator::ForgeInput ( $_GET );
		if (! is_numeric ( $data ['id'] ))
		{
			AJAX::ThrowError ( 'Ung&uuml;ltige ID &uuml;bergeben!' );
		}
		
		if (strlen ( trim ( $data ['name'] ) ) < 3)
		{
			AJAX::ThrowError ( 'Der angegebene Link-Name ist zur kurz!' );
		}
		
		switch ($data ['linktype'])
		{
			case 'FILEBASE' :
				try
				{
					$data ['file'] = Filebase::GetFileId ( $data ['file'] );
					Filebase::UseFileById ( $data ['file'] );
				} catch ( CMSException $ex )
				{
					AJAX::ThrowError ( 'File-ID konnte nicht ermittelt werden!' );
				}
				$data ['website'] = null;
				$data ['menueId'] = null;
				$data ['connectionId'] = null;
				$data ['elementId'] = null;
				break;
			case 'INTERNAL' :
				if (! is_numeric ( $data ['menueId'] ))
				{
					AJAX::ThrowError ( 'Ung&uuml;ltige Men&uuml;-ID' );
				}
				if (! is_numeric ( $data ['connectionId'] ))
				{
					$data ['connectionId'] = null;
					$data ['elementId'] = null;
				}
				if (! is_numeric ( $data ['elementId'] ))
				{
					$data ['elementId'] = null;
				}
				
				$data ['file'] = null;
				$data ['website'] = null;
				
				break;
			case 'EXTERNAL' :
				$data ['website'] = base64_decode($data ['website']);
				if (! Validator::IsValidUrl ( $data ['website'] ))
				{
					AJAX::ThrowError ( 'Ung&uuml;ltige Website angegeben!' );
				}
				$data ['file'] = null;
				$data ['menueId'] = null;
				$data ['connectionId'] = null;
				$data ['elementId'] = null;
				break;
			default :
				AJAX::ThrowError ( 'Ung&uuml;ltiger Linktype &uuml;bergeben!' );
				break;
		}
		
		$id = null;
		try
		{
			$id = SqlManager::GetInstance ()->Insert ( 'modnewslink', array ('name' => $data ['name'], 'linktype' => $data ['linktype'], 'fileId' => $data ['file'], 'website' => $data ['website'], 'menueId' => $data ['menueId'], 'connectionId' => $data ['connectionId'], 'elementId' => $data ['elementId'], 'newsId' => $data ['id'] ) );
		} catch ( SqlManagerException $ex )
		{
			AJAX::ThrowError ( $ex );
		}
		$xmlDoc = new DOMDocument ();
		$root = XML::CreateDocument ( $xmlDoc, 'answer' );
		$item = $xmlDoc->createElement ( 'element' );
		$item->setAttribute ( 'name', $data ['name'] );
		$item->setAttribute ( 'id', $id );
		$item->setAttribute ( 'link', $this->GenerateLink ( $data ['linktype'], $id ) );
		$item->setAttribute ( 'type', $data ['linktype'] );
		$root->appendChild ( $item );
		AJAX::OutputXmlData ( $xmlDoc->saveXML () );
	}
	
	/**
	 * Löscht einen Link der auf die News gesetzt wurde
	 *
	 */
	private function AJAX_DeleteLink()
	{
		$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_EDIT, $_GET ['sub'] );
		if (! $hasLaw)
		{
			AJAX::ThrowError ( 'Sie sind nicht berechtigt diese Seite zu bearbeiten!' );
		}
		
		$id = Validator::ForgeInput ( $_GET ['linkId'] );
		if (! is_numeric ( $id ))
		{
			AJAX::ThrowError ( 'Ung&uuml;ltige ID &uuml;bergeben!' );
		}
		
		try
		{
			SqlManager::GetInstance ()->DeleteById ( 'modnewslink', $id );
		} catch ( SqlManagerException $ex )
		{
			AJAX::ThrowError ( 'Entfernen des Links fehlgeschlagen!' );
		}
		
		$xmlDoc = new DOMDocument ();
		$root = XML::CreateDocument ( $xmlDoc, 'response' );
		$item = $xmlDoc->createElement ( 'element' );
		$item->setAttribute ( 'id', $id );
		$root->appendChild ( $item );
		AJAX::OutputXmlData ( $xmlDoc->saveXML () );
	
	}
}