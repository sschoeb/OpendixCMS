<?php
/**
 * Personen
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse zum anzeigen von Personen
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	2.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		1.0
 */
class Openperson extends  OpenModulBase
{

	public function __construct()
	{
		parent::__construct();
	}
	
	protected function Overview()
	{		
		$data = $this->GetData ();


		for($i=0; $i<count($data); $i++)
		{
			try{
			$data[$i]['bild'] = Filebase::GetFilePath($data[$i]['bildId'], true);
			}catch(Exception $ex)
			{}
		}
			
		CssImport::ImportCss('personen.css');
		
		$title = Cms::GetInstance() -> GetModule() -> GetDbConfiguration('title' );
		MySmarty::GetInstance() -> OutputModuleVar('title', $title);
		MySmarty::GetInstance()-> OutputModuleVar('persona', $data);
	}

	
	private function GetData() {
		$groupId = Cms::GetInstance() -> GetModule() -> GetDbConfiguration('groupid' );	
		
		$data = null;
		try{
			$data = SqlManager::GetInstance() -> QueryAndFetch('SELECT su.id, su.imageFileId as bildId, su.firstname, su.name, su.email, su.phoneprivate, su.phonemobile, su.phonebusiness, su.residence, su.zip, su.street, mpg.funktion, g.name as groupname FROM modpersoningroup mpg LEFT JOIN sysuser su ON mpg.userid=su.id LEFT JOIN modpersongruppe g ON mpg.groupid=g.id WHERE mpg.groupid=\''. $groupId .'\'  ORDER BY mpg.folge', MYSQL_ASSOC);
			//$data = SqlManager::GetInstance() -> QueryAndFetch('SELECT mpg.funktion, mp.name, mp.bildId, mp.adresse, mp.wohnort, mp.email, mp.telprivat, mp.telbusiness, mp.telmobile FROM modpersoningroup mpg LEFT JOIN modperson mp ON mpg.userid=mp.id WHERE mpg.groupid=\''. $groupId .'\' ORDER BY mpg.folge', MYSQL_ASSOC);
		}catch(SqlManagerException $ex)
		{
			throw new CMSException('SQL-Fehler', CMSException::T_MODULEERROR, $ex);
		}
		return $data;
	}
}