<?php
/**
 * Handelt die AJAX-Funktionen ab
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse die AJAX-Funktionen anbietet
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.1
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		1.0     
 */
class AJAX
{
	/**
	 * Gibt die Daten fÃ¼r den Filebrowser zurÃ¼ck
	 *
	 */
	public static function Fbbrowser()
	{
		$folder = '';
		
		if(isset($_GET['folder']))
		{
			$folder = preg_replace ( "-(\.\./)*-", '', $_GET['folder'] );;	
		}

		$filebasePath = Cms::GetInstance() -> GetConfiguration() -> Get('Path/filebaseFolder');
		$path = AJAX::AddSlash($filebasePath . $folder);
		$files = FileManager::GetFileList($path);

		//Wenn der User sich in einem Ordner befindet dann wird ein .. angefÃ¼gt Ã¼ber den
		//der User eine Ordnerebene nach oben verschieben kann
		if($folder != '')
		{
			array_unshift($files, '..');
		}

		//Ab hier wird nun die XML-Antwort generiert
		$xmlDoc = new DOMDocument();
		$root = $xmlDoc->createElement('filebrowser');
		$root = $xmlDoc->appendChild($root);

		$c=count($files);

		for($i=0;$i<$c;$i++)
		{
			$item = null;
			if(is_dir($path . $files[$i]))
			{
				if($files[$i] == '..')
				{
					$item =  $xmlDoc -> createElement('item', base64_encode(utf8_encode($files[$i])));
				}
				else
				{
					$item =  $xmlDoc -> createElement('item', base64_encode(utf8_encode(AJAX::AddSlash($folder) . $files[$i] . '/')));
				}

				$item -> setAttribute('type', 'folder');
			}
			else
			{
				$item =  $xmlDoc -> createElement('item', base64_encode(utf8_encode(AJAX::AddSlash($folder) .  $files[$i])));
				$item -> setAttribute('type', 'file');
			}
			$root -> appendChild($item);
		}
		
		$data = $xmlDoc -> saveXML();
		
		//XML-Header ausgeben damit der Browser weis, dass es sich bei der ANtwort um gÃ¼ltiges XML handelt
		AJAX::OutputXmlHeader(strlen($data));

		//XML-Daten ausgeben
		echo $data;
	}
	
	/**
	 * Gibt die XML-Header Daten an den Browser aus
	 *
	 * @param int $datalength
	 */
	public function OutputXmlHeader($datalength)
	{
		header('Expires: Mon, 26 Jul 2050 05:00:00 GMT');
		header('Last-Modified: ' . gmdate( "D, d M Y H:i:s" ) . 'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-Length: '.$datalength);
		header('Content-Type: application/xml');
	}
	
	/**
	 * Fügt am Ende des Strings einen / hinzu sollte nicht bereits einer vorhanden sein
	 *
	 * @param String $folder	String an dem der / angefügt wird
	 * @return String
	 */
	private static function AddSlash($folder)
	{
		if(strrpos($folder, '/') == strlen($folder) -1)
		{
			return $folder;
		}
		return $folder . '/';
	}
	
	public static function OutputXmlData($data)
	{
		AJAX::OutputXmlHeader(strlen($data));
		echo $data;
		die();
	}
	
	/**
	 * Gibt einen Fehler zurÃ¼ck der per Javascript verarbeitet werden kann
	 *
	 * @param String $message	Fehlermeldung
	 */
	public static function ThrowError($message)
	{
		$xmlDoc = new DOMDocument();
		$root 	= $xmlDoc->createElement('error');
		$root 	= $xmlDoc->appendChild($root);	
		
		$item = $xmlDoc -> createElement('message', $message);
		$root -> appendChild($item);

		AJAX::OutputXmlData($xmlDoc -> saveXML());
	}
}