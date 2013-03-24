<?php
/**
 * Beinhaltet die Klasse um einen Bericht anzuzeigen
 * 
 *
 * @package    	OpendixCMS
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	1.0
 */

/**
 * Zeigt einen Bericht in der Öffentlichkeit an
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.1
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		1.0     
 */
class OpenBericht extends OpenModulBase 
{
	
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{	
		CssImport::ImportCss('bericht.css');
		
		parent::__construct();
			
		if(!isset($_GET['action']))
		{
			return;
		}
		switch ($_GET['action']) {
			case 'getattachement':
				$this -> GetAttachement();
				$this -> Item();
			case 'item':
				$this -> Item();
				break;
			case 'ajaxgetbericht':
				$this -> AJAX_GetBerichte();
				break;
			break;
		}
		
	}
	
	/**
	 * Zeigt alle Berichte einer Berichtgruppe in der Übersicht an
	 *
	 */
	protected  function Overview()
	{
		//Abfrage der Gruppen-ID aus der Modul-Konfiguration
		$menueId = Cms::GetInstance() -> GetMenueId();
		$restriction = OpenBericht::GetMenuePointRestriction(Cms::GetInstance() -> GetModule(), $menueId);
		
		$daten = null;
		try 
		{
			$daten = SqlManager::GetInstance() -> Select('modbericht', array('title', 'id', 'created'), $restriction);
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Berichte fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		
		$c=count($daten);
		for($i=0;$i<$c;$i++)
		{
	
			$daten[$i]['links'] = array();
			$daten[$i]['links']['item'] =Functions::GetLink(array('action' => 'item', 'id' => $daten[$i]['id']), true);
			$daten[$i]['datum'] = date('d.m.Y', strtotime($daten[$i]['created']));
		}
		
		$title = Cms::GetInstance() -> GetModule() -> GetDbConfiguration('title');
		MySmarty::GetInstance() -> OutputModuleVar('title', $title);
		MySmarty::GetInstance() -> OutputModuleVar('berichte', $daten);
	}
	
	public static function GetMenuePointRestriction($module, $menueId)
	{
		$id = $module -> GetDbConfiguration('groupid', $menueId);
		return 'gId=\''. $id .'\' AND active=\'1\'';
	}
	
	/**
	 * Zeigt einen einzelnen Bericht an
	 *
	 */
	protected function Item()
	{
		$id = Validator::ForgeInput($_GET['id']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		$groups = Cms::GetInstance() -> GetModule() -> GetDbConfiguration('groupid');
		if($this -> checkLaw && !Law::IsItemOfGroup($id, $groups, 'modbericht'))
		{
			throw new CMSException('Sie sind nicht berechtigt, diese Seite einzusehen!', CMSException::T_MODULEERROR );
		}
		
		$daten = null;
		try 
		{
			$daten['general'] = SqlManager::GetInstance() -> SelectRow('modbericht', $id, array('created', 'title', 'active', 'creatorId', 'html'));
			$daten['gallery'] = SqlManager::GetInstance() -> QueryAndFetch('SELECT modgalerie.name AS name, gallerieId FROM modberichtgallery, modgalerie WHERE modberichtgallery.berichtId =\''. $id .'\'');
			$daten['attachements'] = SqlManager::GetInstance() -> Select('modberichtanhang', array('fileId'), 'berichtId=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Bericht-Informationen ist fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		
		$daten['general']['html'] = htmlspecialchars_decode($daten['general']['html']);
		$daten['general']['created'] = date('d.m.Y', strtotime($daten['general']['created']));
		
		$c=count($daten['gallery']);
		for($i=0;$i<$c;$i++)
		{
			$daten['gallery'][$i]['link'] = Functions::GetLink(array('action' => 'showgallery', 'gid' => $daten['gallery'][$i]['gallerieId']));
		}
		$c=count($daten['attachements']);
		for($i=0;$i<$c;$i++)
		{
			$daten['attachements'][$i]['link'] = Functions::GetLink(array('action' => 'getattachement', 'aid' => $daten['attachements'][$i]['fileId']), true);
			$daten['attachements'][$i]['name'] = Filebase::GetFileName($daten['attachements'][$i]['fileId']);
		}

		MySmarty::GetInstance() -> OutputModuleVar('item', $daten);
	}
	
	/**
	 * Sendet einen Anhang an den Besucher
	 *
	 */
	private function GetAttachement()
	{
		$aid = Validator::ForgeInput($_GET['aid']);
		if(!is_numeric($aid))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		
		
		Filebase::DownloadFileById($aid);
	}
	
	/**
	 * Zeigt eine Galerie an
	 *
	 */
	private function ShowGallery()
	{	
		$gid = Validator::ForgeInput($_GET['gid']);
		if(!is_numeric($gid))
		{
			throw new CMSException('Die GID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
		$_GET['gid'] = $gid;
		$gallery = new Galerie();
	}
	
	public function AJAX_GetBerichte()
	{
		//Berechtigun prüfen
		if(!Law::HasLaw(Law::T_VIEW, $_GET['sub']))
		{
			AJAX::ThrowError('Sie sind nicht berechtigt auf diesen Bericht zuzugreiffen!');
		}
		
		//Abfrage der Gruppen-ID für welche in diesem Menüpunkt die Berichte ausgegeben werden
		$id = Cms::GetInstance() -> GetModule() -> GetDbConfiguration('groupid');
		
		//Abfrage der Berichte für die Gruppe
		$berichte = null;
		try 
		{
			$berichte = SqlManager::GetInstance() -> Select('modbericht', array('id', 'title'), 'gId=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			AJAX::ThrowError('Abfrage der Berichte fehlgeschlagen!');
		}
		
		//Estellen des XML
		$xmlDoc = new DOMDocument();
		$root = $xmlDoc->createElement('berichte');
		$root = $xmlDoc->appendChild($root);
		
		$c=count($berichte);
		for($i=0;$i<$c;$i++)
		{
			$item =  $xmlDoc -> createElement('item', '');
			$item -> setAttribute('id', $berichte[$i]['id']);
			$item -> setAttribute('title', $berichte[$i]['title']);
			$root -> appendChild($item);
		}
		
		$data = $xmlDoc -> saveXML();
		AJAX::OutputXmlHeader(strlen($data));
		echo $data;
		die();
	}
}
