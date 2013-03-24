<?php
/**
 * Frontpage
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Schoeb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schoeb
 * @version    1.2
 */

/**
 * Klasse zum darstellen einer Frontpage
 *
 * @author 		Stefan Schoeb
 * @package 	OpendixCMS
 * @version 	1.2
 * @copyright 	Copyright &copy; 2006, Stefan Schoeb
 * @since 		1.0
 */
class Frontpage extends ModulBase
{
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{
		parent::__construct ();
	}
	
	/**
	 * Zeigt denEditor an über den die Startseite editiert werden kann
	 *
	 */
	protected function Overview()
	{
		//Hier die Config anzeigen + Editor initialisieren
		Functions::initEditor ( Frontpage::GetText () );
	}
	
	/**
	 * Speichert die neuen Einstellungen
	 *
	 */
	protected function Save()
	{
		//Neue Config aus dem Formular auslesen
		$anzNews = Validator::ForgeInput ( $_POST ['anzNews'] );
		$anzDate = Validator::ForgeInput ( $_POST ['anzDate'] );
		$text = Validator::ForgeInput ( $_POST ['editor'], false );
		
		echo $text;
		//Scripts entfernen
		$text = functions::stripSelectedTags ( $text, array ('script' ) );
		
		//Hier muss nun nur die Config in die config-Datei abgelegt werden
		$frontFile = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Frontpage/file' );
		file_put_contents ( $frontFile, $text );
	}
	
	/**
	 * Gibt den auf der Startseite anzuzeigenden Text zurueck
	 *
	 * @return String
	 */
	public static function GetText()
	{
		//Wenn das File nicht existiert eifnach leer zurückgeben
		$frontFile = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Frontpage/file' );
		if (! file_exists ( $frontFile ))
		{
			
			return '';
		}
		
		//Ansonsten den Inhalt des Files zurückgeben
		if (! function_exists ( 'htmlspecialchars_decode' ))
		{
			$str = strtr ( file_get_contents ( $frontFile ), array_flip ( get_html_translation_table ( HTML_SPECIALCHARS ) ) );
			$str = file_get_contents ( $frontFile );
			return $str;
		}
		return htmlspecialchars_decode ( file_get_contents ( $frontFile ) );
	}
}
