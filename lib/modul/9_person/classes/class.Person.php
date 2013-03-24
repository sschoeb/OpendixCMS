<?php
/**
 * Personenverwaltung
 *
 *
 * @package    OpendixCMS
 * @author     Stefan SchÃ¶b <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schï¿½b
 * @version    1.0
 */

/**
 * Klasse zum verwalten der Personen
 *
 * @author 		Stefan SchÃ¶b
 * @package 	OpendixCMS
 * @version 	1.1
 * @copyright 	Copyright &copy; 2006, Stefan SchÃ¶b
 * @since 		1.0
 */
class Person extends ModulBase
{
	public function __construct()
	{
		CssImport::ImportCss ( 'content.css' );
		
		parent::__construct ();
		
		
	}
	
	protected function Overview()
	{
		$personen = SqlManager::GetInstance ()->Select ( 'sysuser', array ('id', 'name', 'firstname' ), null, 'name' );
		
		$currentUserid = Cms::GetInstance ()->GetUser ()->id;
		for($i = 0; $i < count ( $personen ); $i ++)
		{
			$links = array ();
			$links ['delete'] = Functions::GetLink ( array ('action' => 'delete', 'id' => $personen [$i] ['id'] ), true );
			$links ['edit'] = Functions::GetLink ( array ('action' => 'item', 'id' => $personen [$i] ['id'] ), true );
			$personen [$i] ['link'] = $links;
			
			$personen [$i] ['nodelete'] = $currentUserid == $personen [$i] ['id'];
			$personen [$i] ['name'] = $personen [$i] ['name'] . ' ' . $personen [$i] ['firstname'];
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'personen', $personen );
	}
	
	protected function Item()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ungültige ID angegeben', CMSException::T_MODULEERROR );
		}
		
		
		$manager = SqlManager::GetInstance ();
		$person = $manager->SelectRow ( 'sysuser', $id );
		$personconnectionsPers = $manager->QueryAndFetch ( 'SELECT name FROM modpersoningroup pig INNER JOIN modpersongruppe pg ON pig.userid=' . $id . ' AND pig.groupid=pg.id ORDER BY name' );
		$personconnectionsAdmin = $manager->QueryAndFetch ( 'SELECT name FROM sysuserlawgroup pig INNER JOIN syslawgroup pg ON pig.userid=' . $id . ' AND pig.groupid=pg.id ORDER BY name' );
		
		if ($person ['imageFileId'] != 0)
			$person ['image'] = Filebase::GetFilePath ( $person ['imageFileId'], true );
		
		CssImport::ImportCss ( 'personen.css' );
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'user', $person );
		MySmarty::GetInstance ()->OutputModuleVar ( 'usergroupsPers', $personconnectionsPers );
		MySmarty::GetInstance ()->OutputModuleVar ( 'usergroupsAdmin', $personconnectionsAdmin );
	
	}
	
	protected function Delete()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ungültige ID angegeben', CMSException::T_MODULEERROR );
		}
		$manager = SqlManager::GetInstance ();
		$manager->Delete ( 'modpersoningroup', 'userid=' . $id );
		$manager->DeleteById ( 'sysuser', $id );
	}
	
	protected function Save()
	{
		echo "Save";
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ungültige ID angegeben', CMSException::T_MODULEERROR );
		}
		
		$data = Validator::ForgeInput ( $_POST ['user'] );
		$colData = array ('firstname' => $data ['firstname'], 'name' => $data ['name'], 'street' => $data ['street'], 'zip' => $data ['zip'], 'residence' => $data ['residence'], 'email' => $data ['email'], 'phoneprivate' => $data ['phoneprivate'], 'phonebusiness' => $data ['phonebusiness'], 'phonemobile' => $data ['phonemobile'] );
		
		if (isset ( $_FILES ['newImage'] ) && $_FILES ['newImage'] ['name'] != '')
		{
			
			$path = Cms::GetInstance ()->GetModule ()->GetConfiguration ( 'Path/image' );
			$colData ['imageFileId'] = Filebase::UploadFile ( $path, 'newImage' );
			$srcPath = Filebase::GetFilePath ( $colData ['imageFileId'], true );
			
			try
			{
				$image = new Image ( $srcPath );
				$image->SaveThumbnail ( $srcPath, 130, 100 );
			} catch ( CMSException $ex )
			{
				MySmarty::GetInstance()->OutputWarning('Bild konnte nicht gespeichert werden: ' . $ex->getMessage());		
			}
		
		}
		
		SqlManager::GetInstance ()->Update ( 'sysuser', $colData, 'id=\'' . $id . '\'' );
		MySmarty::GetInstance ()->OutputConfirmation ( 'Person erfolgreich gespeichert' );
	
	}
	
	protected function Add()
	{
		
		$name = Validator::ForgeInput ( $_POST ['name'] );
		$firstname = Validator::ForgeInput ( $_POST ['firstname'] );
		$id = SqlManager::GetInstance ()->Insert ( 'sysuser', array ('name' => $name, 'firstname' => $firstname ) );
				
		// 2 steht für Gruppe User in welche jeder Benutzer kommt (1 wäre Administrator)
		SqlManager::GetInstance ()->Insert ( 'sysuserlawgroup', array ('userId' => $id, 'groupId' => 2 ) );
		
		$_GET ['id'] = $id;
		
	}
}
