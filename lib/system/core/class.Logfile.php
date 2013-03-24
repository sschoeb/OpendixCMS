<?php
/**
 * Log
 * 
 *
 * @package    	OpendixCMS.Core
 * @author     	Stefan Sch�b <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Sch�b
 * @version    	1.0
 */

/**
 * Klasse zum erstellen von Logs
 * 
 * @author 		Stefan Sch�b
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Sch�b
 * @since 		1.0     
 */
class Logfile
{
	private $files;
	
	/**
	 * Konstrukotr
	 *
	 */
	function __construct()
	{
		$this -> files = array( 'Besucher' 	=>	functions::getINIParam('Logg_besucher_ordner'),
			    			'Warnungen' => 	functions::getINIParam('Logg_warnung_ordner'),
				    		'Fehler' 	=> 	functions::getINIParam('Logg_fehler_ordner'),
				    		'Download' 	=>	functions::getINIParam('Logg_download_ordner'),
						'Upload' 	=>	functions::getINIParam('Logg_upload_ordner'));
		
		$logordner = functions::getINIParam('Logg_dir');

		$this -> showtypes();
		
		if(isset($_GET['type']))
		{
			$this -> showlogfiles();	
			
		}

		if(isset($_GET['file']))
		{	
			$this -> showfile();
		}

	}

	/**
	 * Funktion die alle m�glichen geloggten sachen anzeigt
	 *
	 */
	function Showtypes()
	{
		$i=0;
		foreach($this -> files as $key => $values)
		{
			$types[$i]['link'] = functions::GetLink(array('sub' => $_GET['sub'], 'type' => $key));
			$types[$i]['name'] = $key;
			$i++;
		}
		functions::output_var('types', $types);
	}

	/**
	 * Zeigt die Logfiles von einem typ an
	 *
	 * @return mixed false wenn fehlgeschlagen ansonsten nichts
	 */
	function Showlogfiles()
	{
		$type = functions::cleaninput($_GET['type']);
		if(!file_exists(getcwd() . functions::getINIparam('Logg_dir') . $this -> files[$type]))
		{
			functions::output_fehler('Dieser Log-Ordner existiert nicht!');
			return false;
		}
		
		if(!$logfiles = functions::countfiles(getcwd() . functions::getINIparam('Logg_dir') . $this -> files[$type]))
		{
			functions::output_warnung('Inhalt des Log-Ordners konnte nicht gelesen werden oder es sind keine Log-Dateien vorhanden');
			return false;
		}

		for($i=0; $i<count($logfiles); $i++)
		{
			$out[$i]['sortDate'] = @date('Ymd', $this -> logfilename2date($logfiles[$i]));
			$out[$i]['name'] = $logfiles[$i];
			$out[$i]['date'] = @date('d.m.Y', $this -> logfilename2date($logfiles[$i]));
			$file = explode('.', $logfiles[$i]);
			$out[$i]['link'] = functions::GetLink(array('sub' => $_GET['sub'], 'type' => $type, 'file' => $file[0]));
			
		}

		rsort($out);
		functions::output_var('logfiles', $out);
	}

	/**
	 * Funktion die aus einem dateinamen ein timestamp macht
	 *
	 * @param string $name	format DDMMYY.txt
	 * @return int	timestamp
	 * @access private
	 */
	private function logfilename2date($name)
	{
		//Der nam ekommt wie folgt daher
		//220406.txt also tag monat jahr immer zweistellig
		$name 	= explode('.', $name);
		
		$tag 	= substr($name[0], 0, 2);
		$monat 	= substr($name[0], 2, 2);
		$jahr 	= substr($name[0], 4, 4);
		return mktime(0,0,0,$monat, $tag, $jahr);			
	}

	/**
	 * Zeigt ein einzelnes File an
	 *
	 * @return mixed false wenn fehlgeschlagen ansonsten nichts
	 */
	function showfile()
	{
		$file = functions::cleaninput($_GET['file']) . '.txt';
		$type = functions::cleaninput($_GET['type']);

		
		
		if(!file_exists(getcwd() . functions::getINIparam('Logg_dir') . $this -> files[$type]. "/" . $file))
		{
			functions::output_fehler('Logfile existiert nicht!');
			return false;
		}

		if(!$inhalt = file_get_contents(getcwd() . functions::getINIparam('Logg_dir') . $this -> files[$type]. "/" . $file))
		{
			functions::output_fehler('Inhalt konnte nicht gelesen werden!');
			return false;
		}

		functions::output_var('file', trim($inhalt));
		
	}
	
	/**
	 * Erstellt ein log-eintrag
	 *
	 * @param string $folder ordnername
	 * @param string $eintrag eintrag der ins filekommen soll
	 * @return mixed false wenn fehlgeschlagen ansonsten nichts
	 */
	function log($folder, $eintrag)
	{
		$pfad = getcwd() . functions::getINIparam('Logg_dir') . $folder;
		if(!file_exists($pfad))
		{
			return false;
		}
		
		$file = date('dmY') . '.txt';
		if(!file_exists($pfad . "/" . $file))
		{
			functions::createFile($pfad . "/" . $file);
		}
		$input =  date("[d.m.Y-H:i:s]") . $eintrag; 
		
		$inhalt = file_get_contents($pfad . "/" . $file);
		$inhalt = $inhalt .  $input ."\r\n";
		file_put_contents($pfad . "/" . $file, $inhalt);
		return true;
	}
}

