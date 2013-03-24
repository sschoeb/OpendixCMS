<?php
/**
 * Thumbnail
 *
 *
 * @package    	OpendixCMS.Core
 * @author     	Stefan Sch�b <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Sch�b
 * @version    	1.0
 */

/**
 * Klasse zum erstellen von Thumbnails
 *
 * Changelog:
 * - Neue Version verkleinert nur noch die Bilder, die nicht bereits eine genügend kleine grösse haben
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Sch�b
 * @since 		1.0
 */
class PHPthumbnailer extends BaseThumbnailer
{
	/**
	 * 
	 * @param $path
	 */
	public function CreateThumbnail($srcPath, $dstPath)
	{
		$image = new Image($srcPath);
		
		$newImg = @imagecreatetruecolor ( $this->outputWidth, $this->outputHeight );
		
		switch ($image -> GetType())
		{
			case ImageFormat::GIF :
				$altesBild = ImageCreateFromGIF ( $path );
				imagecopyresampled ( $newImg, $altesBild, 0, 0, 0, 0, $this->neueBreite, $this->neueHoehe, $this->pictureInfo [0], $this->pictureInfo [1] );
				imageGIF ( $newImg, $this->pfadOut . $this->prefix . $this->newfilename . ".gif" );
				break;
			case ImageFormat::JPG :
				$altesBild = ImageCreateFromJPEG ( $path );
				imagecopyresampled ( $newImg, $altesBild, 0, 0, 0, 0, $this->neueBreite, $this->neueHoehe, $this->pictureInfo [0], $this->pictureInfo [1] );
				ImageJPEG ( $newImg, $this->pfadOut . $this->prefix . $this->newfilename . ".jpg" );
				break;
			case ImageFormat::PNG :
				$altesBild = ImageCreateFromPNG ( $path );
				imagecopyresampled ( $newImg, $altesBild, 0, 0, 0, 0, $this->neueBreite, $this->neueHoehe, $this->pictureInfo [0], $this->pictureInfo [1] );
				ImagePNG ( $newImg, $this->pfadOut . $this->prefix . $this->newfilename . ".png" );
				break;
			default :
				throw new CMSException ( 'Unbekanntes Bildformat! Import nur von Gif, Jpg, Png-Bildern m&ouml;glich!', CMSException::T_WARNING );
				break;
		}
	
	}
	

	
//	
//	//Veränderbare Variabeln
////	private $onlySmaller = true; //Bilder nur verkleinern. Zu kleine Bilder also nicht vergrössern
////	private $pfadIn; //Ordner in dem die Fiels liegen
////	private $pfadOut; //Ordner in dem die neuen Files abgelegt werden
////	private $maxHigh = 100; //Maximale Bildhöhe des Thumbs
////	private $maxWidth = 100; //Maximale Bildbreite des Thumbs
////	private $delOldfile = true; //Altes File aus dem $pfadIn-Ordner l�schen
////	private $copyOldToOut = true; //Altes bild in den $pfadout-Ordner kopieren
////	private $oldResizeHigh = 500; //Soll das Oringalbild in der gr�se angepasst werden wenn es kopiert wird
////	private $oldResizeWidth = 500; //Soll das Oringalbild in der gr�se angepasst werden wenn es kopiert wird
////	private $oldResize = true; //Soll das Oringalbild in der gr�se angepasst werden wenn es kopiert wird
////	private $rename = true; //Bilder umbenennen in den angegebenen filenamen oder zufallsname
////	private $prefix = "thumb_"; //Prefix f�r die thumbnails
////	private $filename; //Filename den man zuweisen kann
////	private $randomname = true; //Zufallsname verwenden (ansonsten $filename)
////	private $randomlenght = 15; //L�nge der Zufallsnamen
////	private $wzeichen = false;
////	private $wzeichenbild = "logo/logo.gif";
////	private $wzeichenort = "RND"; //"TL" 	(oben links)
//	//"TM" 	(oben mitte)
//	//"TR" 	(oben rechts)
//	//"CL"	(mitte links)
//	//"C"  	(mitte mitte)
//	//"CR"  (mitte rechts)
//	//"BL" 	(unten Links)
//	//"BM" 	(Unten mitte)
//	//"BR" 	(unten rechts)
//	//"RND" (Zufallsposition der obigen)
//	
//
//	//Interne Variabeln
////	private $file;
////	private $neueHoehe;
////	private $neueBreite;
////	private $pictureInfo;
////	private $newfilename;
////	private $vater = false;
//	
//	/**
//	 * Konstruktor
//	 *
//	 * @param string $file
//	 * @return boolean
//	 */
////	function __construct()
////	{
////		//include_once('class.wasserzeichen.php');
////	}
//	
//	/**
//	 * Funktion die von ausen aufgerufen werden kann um ein einzelnes Bild zu bearbeiten
//	 *
//	 * @param string $name
//	 */
//	function doBild($name = "")
//	{
//		if ($name != '')
//		{
//			$this->setfile ( $name );
//		}
//		$this->pictureInfo = getimagesize ( $this->pfadIn . $this->file );
//		
//		if (! $this->vater)
//		{
//			$this->defineFileName ();
//		}
//		
//		//Prüfen ob das bild überhaupt verkleinert werden muss
//		if ($this->calcsize ())
//		{
//			//Wenn dann wird hier ein thumb erstellt
//			$this->createthumb ();
//		} else
//		{
//			//Ansonsten einfach hier hin kopieren
//			copy ( $this->pfadIn . $this->file, $this->pfadOut . $this->prefix . $this->newfilename . $this->getEndung () );
//		}
//		
//		if ($this->copyOldToOut)
//		{
//			chmod ( $this->pfadOut, 0777 );
//			if ($this->wzeichen)
//			{
//				$this->addWasserzeichen ( $this->pfadIn . $this->file );
//			}
//			
//			//$this -> doBildFile($this -> file);
//			if ($this->oldResize && ! $this->vater)
//			{
//				$tempHigh = $this->maxHigh;
//				$tempWidth = $this->maxWidth;
//				$tempPrefix = $this->prefix;
//				$tempDelOld = $this->delOldfile;
//				
//				$this->maxHigh = 400; //Standard-werte setzen für diese instanz
//				$this->maxWidth = 550;
//				$this->prefix = '';
//				$this->delOldfile = false;
//				
//				$this->vater = true;
//				$this->doBild ( $this->file );
//				$this->vater = false;
//				
//				$this->maxHigh = $tempHigh;
//				$this->maxWidth = $tempWidth;
//				$this->prefix = $tempPrefix;
//				$this->delOldfile = $tempDelOld;
//			}
//			//copy($this -> pfadIn . $this -> file , $this -> pfadOut . $this -> newfilename . $this -> getEndung());
//		}
//		if ($this->delOldfile)
//		{
//			unlink ( $this->pfadIn . $this->file );
//		}
//		
//		return true;
//	}
//	
//	/**
//	 * Funktion die das gleiche macht wie doBild aber den neuen filenamen zur�ckgibt
//	 *
//	 * @param string $name
//	 * @return string
//	 */
//	function doBildFile($name)
//	{
//		$this->doBild ( $name );
//		return $this->prefix . $this->newfilename . $this->getEndung ();
//	}
//	
//	/**
//	 * Funktion die von aussen aufgerufen werden kann um ein verzeichnis zu durchlaufen
//	 *
//	 * @param string $name
//	 * @return array mit allen abgearbeiteten dateien
//	 */
//	function doOrdner($name)
//	{
//		$this->pfadIn = $name;
//		$files = array ();
//		$files = $this->scandir ();
//		for($i = 0; $i < count ( $files ); $i ++)
//		{
//			if ($this->doBild ( $files [$i] ))
//			{
//				$return [$i] ['bild_g'] = $this->newfilename . $this->getEndung ();
//				$return [$i] ['bild_k'] = $this->prefix . $this->newfilename . $this->getEndung ();
//			}
//		
//		}
//		return $return;
//	}
//	
//	/**
//	 * Funktion welche die Dateiendung der momentan geladenen Datei zur�ckgibt
//	 *
//	 */
//	private function getEndung()
//	{
//		$spaltung = explode ( ".", $this->file );
//		return strtolower ( "." . $spaltung [count ( $spaltung ) - 1] );
//	}
//	
//	/**
//	 * Funktion die dem als Parameter �bergebenen Bild ein Wasserzeichen anf�gt
//	 *
//	 */
//	private function addWasserzeichen($datei)
//	{
//		// Instantiate phpWatermark
//		// The only parameter currently required is the name
//		// of the image, which should get marked
//		$wm = new watermark ( $this->pfadIn . $this->file );
//		
//		// Optionally specify the position of
//		// the watermark on the image
//		$wm->setPosition ( $this->wzeichenort );
//		
//		// Add a watermark containing the string
//		// "phpWatermark" to the image specified above
//		$wm->addWatermark ( $this->wzeichenbild, "IMAGE" );
//		
//		// Fetch the marked image
//		switch ($wm->type)
//		{
//			case "GIF" :
//				imagegif ( $wm->getMarkedImage (), $this->pfadIn . $this->file );
//				break;
//			case "JPG" :
//				imagejpeg ( $wm->getMarkedImage (), $this->pfadIn . $this->file );
//				break;
//			case "PNG" :
//				imagepng ( $wm->getMarkedImage (), $this->pfadIn . $this->file );
//				break;
//		}
//	}
//	
//	/**
//	 * Funktion zum zählen von Files in einem ordner
//	 *
//	 * @param 	string $ordner
//	 * @return 	mixed
//	 */
//	function scanDir()
//	{
//		
//		$handle = opendir ( $this->pfadIn );
//		
//		while ( false !== ($file = readdir ( $handle )) )
//		{
//			if ($file != "." && $file != ".." && $file != 'Thumbs.db')
//			{
//				$filearray [] = $file;
//			}
//		}
//		
//		closedir ( $handle );
//		return $filearray;
//	}
//	
//	/**
//	 * Berechnet neue Höhe/Breite
//	 *
//	 */
//	private function calcsize()
//	{
//		//Prüfen ob das Bild nicht nur verkleinert werden soll
//		if ($this->pictureInfo [1] <= $this->maxHigh && $this->pictureInfo [0] <= $this->maxWidth && $this->onlySmaller)
//		{
//			$this->neueBreite = $this->pictureInfo [0];
//			$this->neueHoehe = $this->pictureInfo [1];
//			return false;
//		}
//		
//		//Neue Werte bestimmen
//		if ($this->pictureInfo [0] > $this->pictureInfo [1])
//		{
//			$this->neueBreite = $this->maxWidth;
//			$this->neueHoehe = intval ( $this->pictureInfo [1] * $this->neueBreite / $this->pictureInfo [0] );
//		} else
//		{
//			$this->neueHoehe = $this->maxHigh;
//			$this->neueBreite = @intval ( $this->pictureInfo [0] * $this->neueHoehe / $this->pictureInfo [1] );
//		}
//		return true;
//	}
//	
//	/**
//	 * Funktion welche den File-namen definiert
//	 *
//	 * @return unknown
//	 */
//	private function defineFileName()
//	{
//		//Abfragen ob überhaupt eine namens�nderung durchgeführt werden soll
//		if ($this->rename)
//		{
//			//SOll ein randomname verwendet werden
//			if ($this->randomname)
//			{
//				$this->newFileName ();
//			} else
//			{
//				$this->newfilename = $this->filename;
//			}
//		} else
//		{
//			$file = explode ( ".", $this->file );
//			$this->newfilename = $file [0];
//		}
//		
//		//überprüfen ob auch ein neuer filename gesetzt wurde!
//		if ($this->newfilename == '')
//		{
//			throw new Exception ( "Es wurde kein Filename definiert!" );
//		}
//	}
//	
//	/**
//	 * Erstellt das Thumbnail
//	 *
//	 */
//	private function createthumb()
//	{
//		
//		//Prüfen ob das Bild nicht schon die korrekte Grösse hat
//		if ($this->pictureInfo [0] == $this->maxWidth && $this->pictureInfo [1] == $this->maxHigh)
//		{
//			
//			return;
//		}
//		
//		if (function_exists ( "ImageCreateTrueColor" ) && $this->pictureInfo [2] != 1)
//		{
//			$this->neueBreite;
//			$this->neueHoehe;
//			$neuesBild = @imagecreatetruecolor ( $this->neueBreite, $this->neueHoehe );
//		} else
//		{
//			$neuesBild = imageCreate ( $this->neueBreite, $this->neueHoehe );
//		}
//		
//		if ($this->pictureInfo [2] == 1)
//		{
//			// GIF
//			$altesBild = ImageCreateFromGIF ( $this->pfadIn . $this->file );
//			imagecopyresampled ( $neuesBild, $altesBild, 0, 0, 0, 0, $this->neueBreite, $this->neueHoehe, $this->pictureInfo [0], $this->pictureInfo [1] );
//			imageGIF ( $neuesBild, $this->pfadOut . $this->prefix . $this->newfilename . ".gif" );
//		}
//		
//		if ($this->pictureInfo [2] == 2)
//		{
//			// JPG
//			$altesBild = ImageCreateFromJPEG ( $this->pfadIn . $this->file );
//			imagecopyresampled ( $neuesBild, $altesBild, 0, 0, 0, 0, $this->neueBreite, $this->neueHoehe, $this->pictureInfo [0], $this->pictureInfo [1] );
//			ImageJPEG ( $neuesBild, $this->pfadOut . $this->prefix . $this->newfilename . ".jpg" );
//		}
//		
//		if ($this->pictureInfo [2] == 3)
//		{
//			// PNG
//			$altesBild = ImageCreateFromPNG ( $this->pfadIn . $this->file );
//			imagecopyresampled ( $neuesBild, $altesBild, 0, 0, 0, 0, $this->neueBreite, $this->neueHoehe, $this->pictureInfo [0], $this->pictureInfo [1] );
//			ImagePNG ( $neuesBild, $this->pfadOut . $this->prefix . $this->newfilename . ".png" );
//		}
//		if ($this->pictureInfo [2] != 1 && $this->pictureInfo [2] != 2 && $this->pictureInfo [2] != 3)
//		{
//			throw new Exception ( "Dieses Bild-Format wird nicht unterst&uuml;tzt!" );
//		}
//	}
//	
//	/**
//	 * Funktion die zufllig einen Namen setzt!
//	 *
//	 */
//	private function newFileName()
//	{
//		$this->newfilename = '';
//		for($i = 0; $i < $this->randomlenght; $i ++)
//		{
//			$abc = array ("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0" );
//			$zufall = rand ( 0, 32 );
//			$this->newfilename .= $abc [$zufall];
//		}
//	}
//	
//	/**
//	 * Setter $file
//	 *
//	 * @param string $value
//	 */
//	function setfile($value)
//	{
//		$this->file = $value;
//	
//	}
//	
//	/**
//	 * Setter $pfadIn
//	 *
//	 * @param string $value
//	 */
//	function setpfadIn($value)
//	{
//		$this->pfadIn = $value;
//	}
//	
//	/**
//	 * Setter $pfadOut
//	 *
//	 * @param string $value
//	 */
//	function setpfadOut($value)
//	{
//		$this->pfadOut = $value;
//	}
//	
//	/**
//	 * Setter $copyOldToOut
//	 *
//	 * @param boolean $value
//	 */
//	function setcopyOldToOut($value)
//	{
//		$this->copyOldToOut = $value;
//	}
//	
//	/**
//	 * Setter $maxHigh
//	 *
//	 * @param int $value
//	 */
//	function setmaxHigh($value)
//	{
//		$this->maxHigh = $value;
//	}
//	
//	/**
//	 * Setter $maxWidth
//	 *
//	 * @param int $value
//	 */
//	function setMaxWidth($value)
//	{
//		$this->maxWidth = $value;
//	}
//	
//	/**
//	 * Setter $delOldfile
//	 *
//	 * @param boolean $value
//	 */
//	function setdelOldFile($value)
//	{
//		$this->delOldfile = $value;
//	}
//	
//	/**
//	 * Setter Rename
//	 *
//	 * @param boolean $value
//	 */
//	function setRename($value)
//	{
//		$this->rename = $value;
//	}
//	
//	/**
//	 * Setter Prefix
//	 *
//	 * @param string $value
//	 */
//	function setPrefix($value)
//	{
//		$this->prefix = $value;
//	}
//	
//	/**
//	 * Setter $filename
//	 *
//	 * @param string $value
//	 */
//	function setFileName($value)
//	{
//		$this->filename = $value;
//	}
//	
//	/**
//	 * Setter $randomname
//	 *
//	 * @param boolean $value
//	 */
//	function setRandomname($value)
//	{
//		$this->randomname = $value;
//	}
//	
//	/**
//	 * Setter $randomlenght
//	 *
//	 * @param int $value
//	 */
//	function setrandomlenght($value)
//	{
//		$this->randomlenght = $value;
//	}
//	
//	/**
//	 * Setter $wzeichenort
//	 *
//	 * @param string $value
//	 */
//	function setWzeichenlogo($value)
//	{
//		$this->wzeichenbild = $value;
//	}
//	
//	/**
//	 * Setter wzeichenort
//	 *
//	 * @param string $value
//	 */
//	function setWzeichenort($value)
//	{
//		$this->wzeichenort = $value;
//	}
//	
//	/**
//	 * Setter f�r den boolean ob das alte bild auch in der gr�sse angepasst werden soll
//	 *
//	 * @param unknown_type $value
//	 */
//	function setoldResize($value)
//	{
//		$this->oldResize = $value;
//	}
//	
//	/**
//	 * Setter f�r die maximale H�he des alten Bild
//	 *
//	 * @param int $value
//	 */
//	function setoldmaxHigh($value)
//	{
//		$this->parentResizer->setmaxHigh ( $value );
//	}
//	
//	/**
//	 * Setter f�r die maximale Breite des alten Bildes
//	 *
//	 * @param int $value
//	 */
//	function setoldmaxWidth($value)
//	{
//		$this->parentResizer->setmaxWidht ( $value );
//	}
//	
//	/**
//	 * Setter f�r $onlySmaller - Boolean ob das Bild nur geschrupft oder auch gestreckt wird!
//	 *
//	 * @param Boolean $value
//	 */
//	function setOnlySmaller($value)
//	{
//		$this->onlySmaller = $value;
//	}
//	
//	/**
//	 * Gibt den Prefix zurück
//	 *
//	 * @return String
//	 */
//	public function getPrefix()
//	{
//		return $this->prefix;
//	}
//	
//	/**
//	 * Gibt den neuen Namen der Datei zurück
//	 *
//	 * @return String
//	 */
//	public function getNewFilename()
//	{
//		return $this->newfilename . $this->getEndung ();
//	}
//	/**
//	 * @param unknown_type $imgName
//	 */
//	public function CreateThumbnail($imgName)
//	{
//		return $this -> doBildFile($imgName);
//	}
//	
//	/**
//	 * @param unknown_type $path
//	 */
//	public function SetInputPath($path)
//	{
//		$this -> setpfadIn($path)
//	}
//	
//	/**
//	 * @param unknown_type $path
//	 */
//	public function SetOutputPath($path)
//	{
//		$this -> setpfadOut($path);
//	}
//	
//	/**
//	 * @param unknown_type $flag
//	 */
//	public function SetInInputPaht($flag)
//	{
//	
//	}
//	
//	/**
//	 * @param unknown_type $prefix
//	 */
//	public function SetThumbPrefix($prefix)
//	{
//	
//	}


}
