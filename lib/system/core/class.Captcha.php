<?php

/**
 * Beinhaltet eine Klasse mit der Captchas erstellt werden können
 *
 * @package 	OpendixCMS.Core
 */


/**
 * This captcha-class (Completely Automated Public Turing test to tell Computers and Humans Apart)
 * was rather made to give an example of object-oriented programming than to create a perfect way of spam-protection.
 * This class is under public domain - please feel free to do whatever you want with it.
 * @author	Julian Moritz, public@julianmoritz.de
 * @version	1.0, 2007/02/11
 */
class Captcha {

	private $publicKey;
	private $privateKey;
	private $charCount;
	private $level;
	private $height;
	private $width;
	private	$maxlevel;
	private $fonts;

	/*
	 * This is the constructor for a new captcha
	 * @param int $id	if you want to reproduce a captcha, this is the id; null for a new one
	 */
	public function __construct( $id = null ) {

		if( $id == null ) {

			$this->publicKey	= uniqid();

		} else {

			$this->publicKey	= $id;

		}

		$this->privateKey	= "this is a simple string wich is the default";
		$this->charCount	= 5;
		$this->width		= 150;
		$this->height		= 50;
		$this->maxlevel		= 10;
		$this->level		= round( $this->maxlevel / 2 );
		$this->fonts		= array();

	}

	/*
	 * If you want to reproduce this captcha, get the id from here
	 * @return int 		the id of this very captcha
 	 */
	public function getId() {

		return $this->publicKey;

	}

	/*
	 * This methods sets the security level.
	 * @param int $n	10 for the more and 1 for the less secure level
	 * returns boolean 	returns true if the level is in the specified range, elsewise false
	 */
	public function setSecurityLevel( $n ) {

		if( $n >= 1 && $n <= $this->maxlevel ) {

			$this->level	= $n;
			return true;

		}

		return false;

	}

	/*
	 * This sets the private key, it is not secure to use the default one!
	 * @param string 	$key this is the private key, please don't use a very short or trivial string
	 * @return boolean	returns true in every case
	 */
	public function setPrivateKey( $key ) {

		$this->privateKey	= $key;
		return true;

	}

	/*
	 * This methods sets the amount of chars wich should be shown in the picture.
	 * @param int $n	please use a value between 1 and 40
	 * @return boolean	returns true if the value is in the specified range (1..40), elsewise false
	 */
	public function setChars( $n ) {

		if( $n <= 40 || $n > 1 ) {

			$this->charCount	= $n;
			return true;

		}

		return false;

	}


	/*
	 * This method shows the PNG image.
	 * If you have not added any font, you'll see a red square instead of the captcha
 	 * @return void
	 */
	public function show() {

		//header ("Content-type: image/png");

		$img		= imagecreate( $this->width , $this->height );

		$this -> fonts = Functions::GetFileList(FILEBASE . '/fonts/');

		if( count( $this->fonts ) == 0 ) {

			ImageColorAllocate ($img, 255, 0, 0);
			imagePNG( $img );
			return;

		}

		ImageColorAllocate ($img, 255, 255, 255);

		$bgcolors		= array();

		for( $i = 0; $i < $this->level; $i++ ) {

			$c1		= mt_rand( 127 ,255 - (127 / $this->maxlevel) * $this->level );
			$c2		= mt_rand( 127 , 255 - (127 / $this->maxlevel) * $this->level );
			$c3		= mt_rand( 127 ,255 - (127 / $this->maxlevel) * $this->level );
			$bgcolors[] 	= ImageColorAllocate($img, $c1, $c2, $c3);

		}

		//set random background
		for( $i = 0; $i < $this->width; $i = $i + 1 ) {
			for( $j = 0; $j < $this->height; $j = $j + 1 ) {
				$ck	= mt_rand( 0 , count( $bgcolors ) - 1 );
				$flag	= mt_rand( 1 , $this->level );
				if( $flag > 1 ) {
					imagesetpixel( $img , $i , $j , $bgcolors[$ck] );
				}
			}
		}

		//draw text
		$fontcolors		= array();

		for( $i = 0; $i < $this->level; $i++ ) {

			$c1		= mt_rand( (127 / $this->maxlevel) * $this->level , 126 );
			$c2		= mt_rand( (127 / $this->maxlevel) * $this->level , 126 );
			$c3		= mt_rand( (127 / $this->maxlevel) * $this->level , 126 );
			$fontcolors[] 	= ImageColorAllocate($img, $c1, $c2, $c3);

		}

		$string		= substr( sha1( $this->publicKey . $this->privateKey ) , 0 , $this->charCount );

		for( $i = 0; $i < strlen( $string ); $i++ ) {

			$char	= $string{$i};

			$ck	= mt_rand( 0 , count( $fontcolors ) - 1 );

			$size	= mt_rand( $this->height * ( (90 - $this->level * 7) / 100 ) , $this->height * ( (95 - $this->level * 6) / 100 ) );

			$posX	= $i * ($this->width / $this->charCount );
			$posY	= mt_rand( $this->height , $size );

			$angle	= mt_rand( $this->level * -4 , $this->level * 4 );

			$fk	= mt_rand( 0 , $this->level - 1 ) % count( $this->fonts );

			imagettftext($img , $size , $angle , $posX , $posY , $fontcolors[ $ck ] , $this->fonts[ $fk ] , $char);

		}

		//draw lines
		for( $i = 0; $i < $this->level / ($this->maxlevel / 5); $i++ ) {

			$ck	= mt_rand( 0 , count( $bgcolors ) - 1 );

			$start	= mt_rand( 0 , $this->width + $this->height - 1 );

			if( $start < $this->width ) {

				//top to bottom
				$x1	= $start;
				$y1	= 0;

				$x2	= mt_rand( 0 , $this->width - 1 );
				$y2	= $this->height;

			} else {

				//left to right
				$x1	= 0;
				$y1	= $start - $this->width;

				$x2	= $this->width;
				$y2	= mt_rand( 0 , $this->height );

			}

			for( $j = 0; $j < $this->width * 0.02; $j++ ) {

				imageline( $img , $x1++ , $y1 , $x2++ , $y2 , $bgcolors[ $ck ] );

			}

		}

		$path = './temp/captcha/' . rand(0, 5000) . '.png';
		imagePNG($img, $path);

		return $path;

	}

	/*
	 * This method sets the width.
	 * @param int $width	the bewished width
	 * @return boolean 	true in every case
	 */
	public function setWidth( $width ) {

		$this->width	= $width;
		return true;

	}

	/*
	 * This method sets the height.
	 * @param int $heigh	the bewished height
	 * @return boolean 	true in every case
	 */
	public function setHeight( $height ) {

		$this->height	= $height;
		return true;

	}

	/*
	 * This method returns the width
	 * @return int 		the width of the image
	 */
	public function getWidth() {

		return $this->width;

	}

	/*
	 * This method returns the height.
	 * @return int 		the height of the image
	 */
	public function getHeight() {

		return $this->height;

	}

	/*
	 * This method tests the input of the user.
	 * @param string $chars	this is the input the user made
	 * @return boolean	true if the input was correct, elsewise false
	 */
	public function isCaptcha( $chars ) {

		$original	= substr( sha1( $this->publicKey . $this->privateKey ) , 0 , $this->charCount );

		if( $chars == $original ) {

			return true;

		}

		return false;

	}

	/*
 	 * This method adds a font file to the available fonts.
	 * @param string $path	the path to the file
	 * @return boolean	returns true if this path exists, elsewise false
	 */
	public function addFont( $path ) {

		if( is_file( $path ) ) {

			$this->fonts[]		= $path;
			return true;

		}

		return false;

	}

}
