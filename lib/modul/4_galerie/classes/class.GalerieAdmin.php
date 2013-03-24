<?php
/**
 * Gallerie
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * 
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		1.0
 */
class GalerieAdmin extends ModulBase
{
	public function __construct()
	{
		parent::__construct ();
		
		CssImport::ImportCss ( 'content.css' );
		
		if (! isset ( $_GET ['action'] ))
			return;
		
		switch ($_GET ['action'])
		{
			case 'AJAX_import' :
				$this->ImportImage ();
				break;
			case 'removeImage' :
				$this->removeImage ();
				$this->Item ();
				break;
		}
	
	}
	
	private function removeImage()
	{
		$imgId = Validator::ForgeInput ( $_GET ['imgId'] );
		if (! is_numeric ( $imgId ))
		{
			throw new CMSException ( 'Keine g&uuml;ltige ID angegeben!', CMSException::T_MODULEERROR );
		}
		
		$this->RemoveImageByImageId ( $imgId );
	
	}
	/**
	 * @param ex
	 */
	private function RemoveImageByImageId($imgId)
	{
		$data = null;
		try
		{
			$data = SqlManager::GetInstance ()->SelectRow ( 'modgaleriebilder', $imgId );
			SqlManager::GetInstance ()->DeleteById ( 'modgaleriebilder', $imgId );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-fehler', CMSException::T_MODULEERROR, $ex );
		}
		
	
		
		if (!$data)
			return;
		
		Filebase::RemoveFileById ( $data ['thumbId'] );
		Filebase::RemoveFileById ( $data ['imgId'] );
	}
	
	private function FindImportImage($path)
	{
		$images = FileManager::GetFileList ( $path );
		
		if (count ( $images ) == 0)
			die ( '3Import abgeschlossen' );
		
		return $images [0];
	}
	
	private function GetImportOutPath($gId)
	{
		$filebasePath = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/filebaseFolder' );
		$galeriePath = Cms::GetInstance ()->GetModule ()->GetConfiguration ( 'Path/galerie' );
		
		return $filebasePath . $galeriePath . $gId . '/';
	}
	
	private function ImportImage()
	{
		if (! is_numeric ( $_GET ['gid'] ))
		{
			die ( '0Fehlerhafte Id (nicht numerisch) fuer den Import erhalten.' );
		}
		
		$gallerieId = $_GET ['gid'];
		
		// Pfade für die Bilder zusammenstellen
		$imageImportTempPath = Cms::GetInstance ()->GetModule ()->GetConfiguration ( 'Path/import' );
		$imgName = $this->FindImportImage ( $imageImportTempPath );
		$outPath = $this->GetImportOutPath ( $gallerieId );
		$normalPath = $outPath . $imgName;
		$thumbPath = $outPath . 'thumb_' . $imgName;
		$srcPath = $imageImportTempPath . $imgName;
		
		// Thumbnails erstellen
		try
		{
			$image = new Image ( $srcPath );
			$image->SaveThumbnail ( $thumbPath, 150, 150 );
			$image->SaveThumbnail ( $normalPath, 600, 600 );
		} catch ( CMSException $ex )
		{
			die ( '0Bild (' . $srcPath . ') konnte nicht importiert werden: ' . $ex->getMessage () );
		}
		
		// Generierte Bilder der Filebase hinzufügen
		$thumbId = Filebase::addFile ( $thumbPath );
		$bigId = Filebase::addFile ( $normalPath );
		
		// Alte Datei die nun importiert wurde löschen
		FileManager::RemoveFile ( $srcPath );
		
		try
		{
			SqlManager::GetInstance ()->Insert ( 'modgaleriebilder', array ('galerieId' => $gallerieId, 'thumbId' => $thumbId, 'imgId' => $bigId ) );
		} catch ( SqlManagerException $ex )
		{
			die ( '0SQL-Fehler!' . mysql_error () );
		}
		
		die ( '1Bild (' . $imgName . ') erfolgreich importiert' );
	}
	
	/**
	 * Fügt eine neue Galerie hinzu
	 */
	protected function Add()
	{
		$name = Validator::ForgeInput ( $_POST ['name'] );
		if (strlen ( $name ) < 3)
		{
			throw new CMSException ( 'Der angegebene Name ist zu kurz (min. 3 Zeichen)', CMSException::T_MODULEERROR );
		}
		
		$di = null;
		try
		{
			$id = SqlManager::GetInstance ()->Insert ( 'modgalerie', array ('name' => $name, 'date' => 'NOW' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		FileManager::CreateFolder ( $this->GetgaleriePath ( $id ) );
		$_GET ['id'] = $id;
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Galerie "' . $name . '" erfolgreich hinzugef&uuml;gt!' );
	}
	
	private function GetgaleriePath($id)
	{
		$galerie = Cms::GetInstance ()->GetModule ()->GetConfiguration ( 'Path/galerie' );
		$filebase = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/filebaseFolder' );
		return $filebase . $galerie . $id;
	}
	
	/**
	 * Löscht eine bestehende Galerie
	 * 
	 * Sämtliche enthaltenen Bilder werden ebenfalls gelöscht
	 */
	protected function Delete()
	{
		$galerieId = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $galerieId ))
		{
			throw new CMSException ( 'Keine g&uuml;ltige ID angegeben!', CMSException::T_MODULEERROR );
		}
		
		$images = null;
		try
		{
			$images = SqlManager::GetInstance ()->Select ( 'modgaleriebilder', array ('id' ), 'galerieId=\'' . $galerieId . '\'' );
			SqlManager::GetInstance ()->DeleteById ( 'modgalerie', $galerieId );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		for($i = 0; $i < count ( $images ); $i ++)
		{
			$this->RemoveImageByImageId ( $images [$i] ['id'] );
		}
		
		FileManager::RemoveFolder ( $this->GetgaleriePath ( $galerieId ) );
	
	}
	
	/**
	 * Zeigt eine Übersicht über sämtliche verfügbaren Galerien an
	 */
	protected function Overview()
	{
		$data = null;
		try
		{
			$data = SqlManager::GetInstance ()->SelectWithScrollMenue ( 'modgalerie', array ('id', 'name' ),'','date','DESC' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		for($i = 0; $i < count ( $data ['daten'] ); $i ++)
		{
			$id = $data ['daten'] [$i] ['id'];
			$editLink = Functions::GetLink ( array ('action' => 'item', 'id' => $id ), true );
			$deleteLink = Functions::GetLink ( array ('action' => 'delete', 'id' => $id ), true );
			$data ['daten'] [$i] ['link'] = array ('edit' => $editLink, 'delete' => $deleteLink );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'galerien', $data );
	}
	
	/**
	 * Zeigt eine einzelne Galerie an. D.h. es werden sämtliche in der 
	 * Galerie vorhandenen Fotos als Thumbnails angezeigt. Der Benutzer kann 
	 * dann einzelne Fotos löschen oder neue Fotos hinzufügen
	 */
	protected function Item()
	{
		$gid = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $gid ))
		{
			throw new CMSException ( 'Keine g&uuml;ltige ID angegeben!', CMSException::T_MODULEERROR );
		}
		
		JsImport::ImportModuleJS ( 'Galerie', 'galerie.js' );
		
		$data = null;
		try
		{
			$galerie = SqlManager::GetInstance ()->SelectRow ( 'modgalerie', $gid, array ('id', 'name', 'description', 'date', 'active', 'isUser', 'photograf' ) );
			$data = SqlManager::GetInstance ()->Select ( 'modgaleriebilder', array ('id', 'imgId' ), 'galerieId=\'' . $gid . '\'' );
			$selected = '';
			if ($galerie ['isUser'] == 1)
				$selected = $galerie ['photograf'];
			$galerie ['user'] = HTML::Select ( 'sysuser', 'nick', $selected, 'nick' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		// In der modgaleriebilder ist nur die ID der Bilder gespeichret, hier die Pfade ermitteln
		for($i = 0; $i < count ( $data ); $i ++)
		{
			$data [$i] ['path'] ['image'] = Filebase::GetFilePath ( $data [$i] ['imgId'], true );
			$data [$i] ['path'] ['delete'] = Functions::GetLink ( array ('action' => 'removeImage', 'imgId' => $data [$i] ['id'] ), true );
		}
		
		$galerie ['importImgCount'] = FileManager::CountFiles ( Cms::GetInstance ()->GetModule ()->GetConfiguration ( 'Path/import' ) );
		
		if ($galerie ['isUser'] == 1)
		{
			$galerie ['photograf'] = '';
			$galerie ['isUser'] = array ('known' => HTML::Checkbox ( 1 ) );
		} else
			$galerie ['isUser'] = array ('unknown' => HTML::Checkbox ( 1 ) );
		
		$galerie ['active'] = HTML::Checkbox ( $galerie ['active'] );
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'galerie', $galerie );
		MySmarty::GetInstance ()->OutputModuleVar ( 'data', $data );
	}
	
	/**
	 * Momentan wird noch nichts gespeichert
	 */
	protected function Save()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Keine g&uuml;ltige ID &uuml;bergeben!', CMSException::T_MODULEERROR );
		}
		
		$data = Validator::ForgeInput ( $_POST ['galerie'] );
		
		$data ['active'] = ( int ) isset ( $data ['active'] );
		
		$data ['date'] = FormParser::ProcessDateInput ( $data ['date'] );
		if ($data ['isUser'] == 1)
		{
			$data ['photograf'] = $data ['knownPhotograf'];
		}
		
		try
		{
			SqlManager::GetInstance ()->Update ( 'modgalerie', array ('name' => $data ['name'], 'active' => $data ['active'], 'isUser' => $data ['isUser'], 'photograf' => $data ['photograf'], 'description' => $data ['description'], 'date' => $data ['date'] ), 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
	
	}
	
	/**
	 * Löscht alle Bilder in dieser Galerie
	 *
	 * @param int $id ID der zu löschenden Galerie
	 * @return Boolean true wenn löschen erfoglreich, ansonsten false
	 */
	private function DeleteGaleriePictures($id)
	{
	
	}
}
