<?php

/**
 * Erstellt ein Thumbnail mit Hilfe von ImgMagick
 * 
 * 
 * @author Stefan
 *
 */
class ImgMagickThumbnailer 
{
	private $inputPath = NULL;
	private $outputPath = NULL;
	private $prefix = 'thumb_';
	private $deleteOldFlag = true;
	
	/**
	 * @param unknown_type unknown_type $imgName
	 */
	public function CreateThumbnail($imgName)
	{
		
			
	}
	
	/**
	 * @param unknown_type unknown_type $path
	 */
	public function SetInputPath($path)
	{
		$this -> inputPath = $path;
	}
	
	/**
	 * @param unknown_type unknown_type $path
	 */
	public function SetOutputPath($path)
	{
		$this -> outputPath = $path;
	}
	
	/**
	 * @param unknown_type unknown_type $flag
	 */
	public function SetInInputPaht($flag)
	{
		$this -> deleteOldFlag = $flag;
	}
	
	/**
	 * @param unknown_type unknown_type $prefix
	 */
	public function SetThumbPrefix($prefix)
	{
		$this -> prefix = $prefix;
	}

	/**
	 * Gibt einen Boolean zurück ob ImgMagick instelliert wurde oder nicht
	 */
	public static function IsInstalled()
	{
		return file_exists(ImgMagickThumbnailer::GetImgMagickPath());
	}
	
	/**
	 * Gibt den Pfad zu ImgMagick zurück
	 */
	private static function GetImgMagickPath()
	{
		return '';
	}
}