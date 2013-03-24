<?php

/**
 * Öffentliche Download-Anzeige
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse Zeigt die Downloads öffentlich an
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	2.0
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		1.0     
 */
class OpenDownload extends OpenModulBase 
{
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{
		
		CssImport::ImportCss('download.css');
		
		parent::__construct();
		
		if(!isset($_GET['action']))
		{
			return;
		}
		
		switch ($_GET['action']) 
		{
			case 'get':
				
				$this -> GetFile();
				
				$this -> Overview();
				break;
		}
	}
	
	/**
	 * Zeigt die �bersicht der Downloads an
	 *
	 * Ausgabe:
	 * $output[0]['name'] = 'Der Name der Gruppe';
	 * $output[0]['items'][0]['name'] = 'Name des Downloads';
	 * $output[0]['items'][0]['size'] = 124; //Groesse der Datei in Bytes
	 * $output[0]['items'][0]['link'] = 'http://www.blub.ch/index.php?....&action=get&id=x';
	 * 
	 * 
	 */
	protected function Overview()
	{
		$groups = Cms::GetInstance() -> GetModule() -> GetDbConfiguration('groupid');
		
		$output = array();
		
		if(is_array($groups))
		{
		
			//Es sollen mehrere Gruppen angezeigt werden
			$c=count($groups);
			for($i=0;$i<$c;$i++)
			{
				$data = $this -> GetOutputForGroup($groups[$i]['propertyValue']);
				if(is_null($data))
				{
					continue;
				}
				$output[] = $data;
			}
		}
		else 
		{
			
			//.. es wird nur eine Gruppe angezeigt
			$data = $this -> GetOutputForGroup($groups[0]);

			if(is_null($data))
			{
				MySmarty::GetInstance() -> OutputConfirmation('Keine Dokumente vorhanden!');
				
				return;
			}
			$output[] = $data;
		}
		
		MySmarty::GetInstance() -> OutputModuleVar('downloads', $output);
		
	}
	
	/**
	 * Gibt das Daten-Array für eine Gruppe zurück
	 *
	 * @param int $id	ID der abzufragenden Gruppe
	 */
	private function GetOutputForGroup($id)
	{
		
		$data = array('name', 'downloads');
		$data['items'] = $this -> GetDownloads($id);
		if(count($data['items'])==0)
		{
			return null;
		}
		$data['name'] = $this -> GetDownloadGroupName($id);
			
		return $data;
	}
	
	/**
	 * Gibt den Namen einer Downloadgruppe zurück
	 *
	 * @param int $id	ID der Gruppe deren Namen man wissen will	
	 * @return unknown
	 */
	private function GetDownloadGroupName($id)
	{
		$name = '';
		try 
		{
			$name = SqlManager::GetInstance() -> SelectItem('moddownloadgroup', 'name', 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage des Gruppennamens fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}	
		return $name;
	}
	
	/**
	 * Gibt alle Downloads zurück
	 *
	 * @param int $gid 	ID der Gruppe deren Downloads abgefragt werden sollen
	 */
	private function GetDownloads($gid)
	{

		$data = null;
		try 
		{
			$data = SqlManager::GetInstance() -> Select('moddownload', array('id', 'fileAlias' => 'name', 'fileId', 'description'), 'gId=\''. $gid .'\'','`order`');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Downloads fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		
		$c=count($data);
		for($i=0;$i<$c;$i++)
		{
			
			try 
			{
				if($data[$i]['name'] == '')
				{
					//Falls kein Alias gesetzt wurde dann einfach den Dateinamen anzeigen
					$data[$i]['name'] = basename(Filebase::GetFilePath($data[$i]['fileId']));
				}
				$data[$i]['size'] = Filebase::GetFileSizeById($data[$i]['fileId']);
			}
			catch (CMSException $ex)
			{
				//Datei existiert nicht, also einfach ignorieren
				unset($data[$i]);
				continue;
			}
			
			$data[$i]['link'] = Functions::GetLink(array('action' => 'get', 'id' => $data[$i]['id']), true);
		}
		
		return $data;
	}
	
	/**
	 * Sendet eine Datei an den Besucher
	 *
	 */
	private function GetFile()
	{
		$id = Validator::ForgeInput($_GET['id']);
		if(!is_numeric($id))
		{
			throw new CMSException('Die ID ist nicht numerisch!', CMSException::T_MODULEERROR );
		}
	
		if(!$this -> IsInMenueGroup($id))
		{
			throw new CMSException('Sie sind nicht berechtigt auf diesen Download zuzugreiffen!', CMSException::T_MODULEERROR );
		}
		
		$fileId = null;
		try 
		{
			$fileId = SqlManager::GetInstance() -> SelectItem('moddownload', 'fileId', 'id=\''. $id .'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Abfrage der Datei fehlgeschlagen!',CMSException::T_MODULEERROR ,$ex);
		}
		
		Filebase::DownloadFileById($fileId);
	}
	
	
	/**
	 * Gibt zurück, ob sich der angeforderte Download in einer der angezeigten Gruppen befindet
	 * Dies verhindert, dass man über die URL Zugriff auf sämtliche Downloads hat
	 *
	 * @param int $downloadId	ID des Downloads der überprüft werden soll
	 * @return Boolean
	 */
	private function IsInMenueGroup($downloadId)
	{
		$groups = Cms::GetInstance() -> GetModule() -> GetDbConfiguration('groupid');
		return Law::IsItemOfGroup($downloadId, $groups, 'moddownload');
	}
}
