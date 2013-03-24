<?php

class Image
{
	private $info = null;
	private $srcPath = null;
	private $dstPath = null;
	private $thumber = null;
	private $maxHeight = null;
	private $maxWidth = null;
	private $outputHeight = null;
	private $outputWidth = null;
	private $exifinfo = null;
	private $shouldRotate = false;
	private $rotateAngle = 0;
	
	public function __construct($path)
	{
		$this->srcPath = $path;
		$this->info = getimagesize ( $path );
		$this->exifinfo = read_exif_data ( $path );
	}
	
	public function GetWidth()
	{
		// 0. Array-Element beschreibt die Breite des Bildes
		return $this->info [0];
	}
	
	public function GetHeight()
	{
		// 1. Array-Element beschreibt die Höhe des Bildes
		return $this->info [1];
	}
	
	public function GetType()
	{
		// 2. Array-Position gibt als int zurück was für ein
		// Bild-Format es ist (Mögliche Werte sihe ImageFormat)
		return $this->info [2];
	}
	
	public function SaveThumbnail($thumbPath, $height, $width)
	{
		$this->dstPath = $thumbPath;
		$this->maxHeight = $height;
		$this->maxWidth = $width;
		
		$this->CalcOutputSize ();
		
		if (isset ( $this->exifinfo ['Orientation'] ))
		{
			$this->correctOrientation ();
		}
		
		if (Image::ImgMagickIsInstalled ())
		{
			$this->CreateImgMagickThumb ();
		} else
		{
			$this->CreatePHPThumb ();
		}
		
		if ($this->shouldRotate)
			$this->turnRight ( $this->rotateAngle );
	}
	
	private function turnRight($degree)
	{
		switch ($this->GetType ())
		{
			case ImageFormat::GIF :
				$oldImg = ImageCreateFromGIF ( $this->dstPath );
				$newImg = imagerotate ( $oldImg, $degree, 0 );
				imageGIF ( $newImg, $this->dstPath );
				break;
			case ImageFormat::JPG :
				$oldImg = ImageCreateFromJPEG ( $this->dstPath );
				$newImg = imagerotate ( $oldImg, $degree, 0 );
				ImageJPEG ( $newImg, $this->dstPath );
				break;
			case ImageFormat::PNG :
				$oldImg = ImageCreateFromPNG ( $this->dstPath );
				$newImg = imagerotate ( $oldImg, $degree, 0 );
				ImagePNG ( $newImg, $this->dstPath );
				break;
			default :
				throw new CMSException ( 'Unbekanntes Bildformat! Import nur von Gif, Jpg, Png-Bildern m&ouml;glich!', CMSException::T_WARNING );
				break;
		}
	
	}
	
	private function correctOrientation()
	{
		switch ($this->exifinfo ['Orientation'])
		{
			case 1 :
				// OK
//				$this->shouldRotate = true;
//				$this->rotateAngle = 180;
				break;
			case 2 :
				$this->shouldRotate = true;
				$this->rotateAngle = 180;
				break;
			case 3 :
				// OK
				break;
			case 4 :
				// OK
				break;
			case 5 :
				break;
			case 6 :
				$this->shouldRotate = true;
				$this->rotateAngle = 270;
				break;
			case 7 :
				$this->shouldRotate = true;
				$this->rotateAngle = 270;
				break;
			case 8 :
				$this->shouldRotate = true;
				$this->rotateAngle = 90;
				break;
		}
	}
	
	private function CreateImgMagickThumb()
	{
		if (! file_exists ( $this->srcPath ))
			throw new CMSException ( 'Konnte kein Thumbnail erstellen, da das Eingabebild nicht gelesen werden konnte' );
		
		$imgMagickPath = ImgMagickThumbnailer::GetImgMagickPath ();
		
		// Bild mit ImgMagick verkleinern/resizen
		exec ( $imgMagickPath . ' -resize ' . $this->outputWidth . 'x' . $this->outputHeight . ' ' . $this->srcPath . ' ' . $this->dstPath, $error );
		
		// Prüfen ob bei der Erstellung des Thumbnails mit ImgMagick ein Fehler aufgetreten ist
		if ($error != null)
			throw new CMSException ( 'Bild konnte nicht verkleinert werden, ImgMagick fehler:' . $error );
	}
	
	private function CreatePHPThumb()
	{
		$newImg = @imagecreatetruecolor ( $this->outputWidth, $this->outputHeight );
		
		switch ($this->GetType ())
		{
			case ImageFormat::GIF :
				$oldImg = ImageCreateFromGIF ( $this->srcPath );
				imagecopyresampled ( $newImg, $oldImg, 0, 0, 0, 0, $this->outputWidth, $this->outputHeight, $this->GetWidth (), $this->GetHeight () );
				imageGIF ( $newImg, $this->dstPath );
				break;
			case ImageFormat::JPG :
				$oldImg = ImageCreateFromJPEG ( $this->srcPath );
				imagecopyresampled ( $newImg, $oldImg, 0, 0, 0, 0, $this->outputWidth, $this->outputHeight, $this->GetWidth (), $this->GetHeight () );
				ImageJPEG ( $newImg, $this->dstPath );
				break;
			case ImageFormat::PNG :
				$oldImg = ImageCreateFromPNG ( $this->srcPath );
				imagecopyresampled ( $newImg, $oldImg, 0, 0, 0, 0, $this->outputWidth, $this->outputHeight, $this->GetWidth (), $this->GetHeight () );
				ImagePNG ( $newImg, $this->dstPath );
				break;
			default :
				throw new CMSException ( 'Unbekanntes Bildformat! Import nur von Gif, Jpg, Png-Bildern m&ouml;glich!', CMSException::T_WARNING );
				break;
		}
	}
	
	/**
	 * Gibt einen Boolean zurück ob ImgMagick instelliert wurde oder nicht
	 */
	private static function ImgMagickIsInstalled()
	{
		$path = Image::GetImgMagickPath ();
		if ($path == '')
			return false;
		return file_exists ( $path );
	}
	
	/**
	 * Gibt den Pfad zu ImgMagick zurück
	 */
	private static function GetImgMagickPath()
	{
		return Cms::GetInstance ()->GetConfiguration ()->Get ( 'Path/ImgMagick' );
	}
	
	private function CalcOutputSize()
	{
		if ($this->GetWidth () > $this->GetHeight ())
		{
			$this->outputWidth = $this->maxWidth;
			$this->outputHeight = intval ( $this->GetHeight () * $this->outputWidth / $this->GetWidth () );
		} else
		{
			$this->outputHeight = $this->maxHeight;
			$this->outputWidth = @intval ( $this->GetWidth () * $this->outputHeight / $this->GetHeight () );
		}
	}
}
