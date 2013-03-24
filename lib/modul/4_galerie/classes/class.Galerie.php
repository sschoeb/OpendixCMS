<?php

class Galerie extends OpenModulBase
{
	
	public function __construct()
	{
		parent::__construct ();
	}
	
	public function Overview()
	{
		CssImport::ImportCss ( 'galerien.css' );
		
		$galeries = null;
		try
		{
			$galeries = SqlManager::GetInstance ()->SelectWithScrollMenue ( 'modgalerie', array ('id', 'name', 'date' ), 'active=\'1\'', 'date desc', '', null, 9 );
		} catch ( SQLManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		$output = array ();
		for($i = 0; $i < count ( $galeries ['daten'] ); $i ++)
		{
			$galeries ['daten'] [$i] ['date'] = date ( 'd.m.Y', strtotime ( $galeries ['daten'] [$i] ['date'] ) );
			$galeries ['daten'] [$i] ['thumb'] = $this->GetRandomGalerieImage ( $galeries ['daten'] [$i] ['id'] );
			$galeries ['daten'] [$i] ['link'] = Functions::GetLink ( array ('action' => 'item', 'id' => $galeries ['daten'] [$i] ['id'] ), true );
			try
			{
				$galeries ['daten'] [$i] ['imgCount'] = SqlManager::GetInstance ()->Count ( 'modgaleriebilder', 'galerieId=\'' . $galeries ['daten'] [$i] ['id'] . '\'' );
			} catch ( SQLManagerException $ex )
			{
				throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
			}
		
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'galeries', $galeries );
	
	}
	
	/**
	 * Gibt ein Pfad zu irgendeinem Thumbnail aus der angegebenen Galerie zurück
	 * 
	 * @todo nicht allae abfragen, sondern per sql random erzeugen
	 * 
	 * @param unknown_type $galerieId
	 */
	private function GetRandomGalerieImage($galerieId)
	{
		try
		{
			$images = SqlManager::GetInstance ()->Select ( 'modgaleriebilder', array ('thumbId' ), 'galerieId=\'' . $galerieId . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		if (count ( $images ) == 0)
			return;
		
		shuffle ( $images );
		return Filebase::GetFilePath ( $images [0] ['thumbId'], true );
	
	}
	
	public function Item()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Der angegebene Wert ist nicht numerisch! (Id)', CMSException::T_MODULEERROR );
		}
		
		CssImport::ImportCss ( 'galerie.css' );
		CssImport::ImportCss ( 'slimbox2.css' );
		JsImport::ImportModuleJS ( 'Galerie', 'jquery.js' );
		JsImport::ImportModuleJS ( 'Galerie', 'slimbox2.js' );
		
		$images = null;
		$galerie = null;
		try
		{
			$galerie = SqlManager::GetInstance ()->SelectRow ( 'modgalerie', $id, array ('name', 'description' ) );
			$images = SqlManager::GetInstance ()->Select ( 'modgaleriebilder', array ('thumbId', 'imgId' ), 'galerieId=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		for($i = 0; $i < count ( $images ); $i ++)
		{
			$images [$i] ['thumb'] = Filebase::GetFilePath ( $images [$i] ['thumbId'], true );
			$images [$i] ['img'] = Filebase::GetFilePath ( $images [$i] ['imgId'], true );
		}
		
		foreach ( $images as $key => $row )
		{
			$imgs [$key] = $row ['img'];
			$thmbs [$key] = $row['thumb'];
			
		}
		
		array_multisort($imgs, SORT_ASC, $thmbs, SORT_ASC, $images);
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'images', $images );
		MySmarty::GetInstance ()->OutputModuleVar ( 'galerie', $galerie );
	
	}

}