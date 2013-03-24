<?php

/**
 * Newsfeed
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.1
 */

/**
 * Klasse mit der man Newsfeeds erstellen kann
 * Es ist m�glich, die Daten aus einer Tabelle auszulesen. welche tabelle kann gew�hlt werden
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.1
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		1.0
 */
class Mynewsfeed
{


	/**
	 * Erstellen von RSS V. 0.91
	 *
	 * @var boolean
	 */
	private $rss091 	= true;

	/**
	 * Erstellen von RSS V. 1.0
	 *
	 * @var boolean
	 */
	private $rss10 		= true;

	/**
	 * Erstellen von RSS V. 2.0
	 *
	 * @var boolean
	 */
	private $rss20 		= true;

	/**
	 * Erstellen von Atom 1.0
	 *
	 * @var boolean
	 */
	private $atom10 	= true;

	/**
	 * Prefix der vor die Dateinamen gesetzt wird
	 *
	 * @var string
	 */
	private $prefix		= 'feed_';

	/**
	 * Verzeichniss in dem die XML-Dateien abgelegt werden
	 *
	 * @var string
	 */
	private $enddir 	= '';

	/**
	 * Kopfvariabel - Titel des Feeds
	 *
	 * @var string
	 * @access private
	 */
	private $head_titel 		= '';

	/**
	 * Kopfvariabel - Link zum Feed
	 *
	 * @var string
	 * @access private
	 */
	private $head_link 			= '';

	/**
	 * Kopfvariabel - Beschreibung des Feeds
	 *
	 * @var string
	 * @access private
	 */
	private $head_description 	= '';

	/**
	 * Kopfvariabel - Sprache des Feeds
	 *
	 * @var string
	 * @access private
	 */
	private $head_language		= '';

	/**
	 * Kopfvariabel - Bild des Feeds
	 *
	 * @var string
	 * @access private
	 */
	private $head_image			= '';

	/**
	 * Kopfvariabel - Copyiright - [optional]
	 *
	 * @var string
	 * @access private
	 */
	private $head_copyright		= '';

	/**
	 * Kopfvariabel - letztes Erstellungsdatum - [optional]
	 *
	 * @var string
	 * @access private
	 */
	private $head_lastbuilddate = '';


	/**
	 * Funktion mit der man die ganzen Feeds erstellt
	 *
	 */
	public function Create()
	{

		if($this -> enddir == '')
		{
			return false;
		}

		if(!$this -> checkParams())
		{
			return false;
		}

		if($this -> rss091 || $this -> rss10 || $this -> rss20)
		{
			$this -> initRss();
		}

		if($this -> atom10)
		{
			$this -> initAtom();
		}
		return true;
	}

	/**
	 * Funktion die den RSS-Generator initialisiert
	 *
	 */
	private function initRss()
	{
		$config = Cms::GetInstance() -> GetConfiguration();
		$user = Cms::GetInstance() -> GetUser() -> GetProperties();
		$rssfile = new RSSBuilder(	$config -> Get('newsfeed/encoding'), 
									$this -> head_link, 
									$this -> head_titel, 
									$this -> head_description, 
									$this -> head_image,
									'', 
									false);
	
		$data = null;
		try 
		{
			$data = SqlManager::GetInstance() -> Select('modnews', array('name', 'text', 'id', 'time'), 'newsfeed=\'1\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('SQL-Abfrage der Newsfeed-Items fehlgeschlagen', CMSException::T_MODULEERROR, $ex );
		}
		
		$c = count($data);
		for($i=0; $i<$c; $i++)
		{
			$rssfile -> addRSSItem(	$this -> head_titel, 
									$data[$i]['name'], 
									$this->GetLink($data['id']), 
									$data[$i]['text'], 
									$data[$i]['name'], 
									$data[$i]['time'], 
									$user['name'], 
									'', 
									'', 
									md5($data[$i]['id']));	
		}

		if($this -> rss091)
		{
			$rssfile->saveRSS('0.91', $this -> enddir . $this -> prefix . '091.xml');
		}
		if($this -> rss10)
		{
			$rssfile->saveRSS('1.0', $this -> enddir . $this -> prefix . '1.xml');
		}
		if($this -> rss20)
		{
			$rssfile->saveRSS('2.0', $this -> enddir . $this -> prefix . '2.xml');
		}
	}
	
	/**
	 * Gibt den Link auf einen News-Eintrag zurück
	 *
	 * @param unknown_type $newsId
	 */
	private function GetLink($newsId)
	{
		$link=null;
		try 
		{
			$link = SqlManager::GetInstance() -> Select('modnewslink', array('id', 'linktype'), 'newsId=\''. $newsId.'\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('SQL-Abfrage eines Links fehlgeschlagen!', CMSException::T_MODULEERROR, $ex );
		}
		return News::GenerateLink($link[0]['linktype'], $link[0]['id']);
	}

	/**
	 * Funktion die den Atom-Generator initialisiert
	 *
	 * @access private
	 *
	 */
	private function initAtom()
	{
		$config = Cms::GetInstance() -> GetConfiguration();
		$user = Cms::GetInstance() -> GetUser() -> GetProperties();
		$atomfile 	= new AtomBuilder($this -> head_titel, $this -> head_link, 'tag:'. $user['email'] .',2004-12-31:' . $this -> enddir);
		$atomfile	->setUpdated(date('c'));
		$atomfile	->setEncoding($config -> Get('newsfeed/encoding'));
		$atomfile	->setLanguage($this -> head_language);
		$atomfile	->setSubtitle($this -> head_titel);
		$atomfile	->setRights($config -> Get('newsfeed/rights'));
		$atomfile	->setIcon($config -> Get('newsfeed/iconpath'));
		$atomfile	->setLogo($config -> Get('newsfeed/logopath'));
		$atomfile	->setAuthor($user['name'] , $user['email'], $this -> head_link);
		$atomfile	->addContributor($user['name'], $user['email']);
		$atomfile	->addContributor($user['name']);
		
		$data = null;
		try 
		{
			$data = SqlManager::GetInstance() -> Select('modnews', array('id', 'name'), 'newsfeed=\'1\'');
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('SQL-Abfrage der Newsfeed-Daten fehlgeschlagen', CMSException::T_MODULEERROR, $ex );
		}
		
		$c = count($data);
		for($i=0; $i<$c; $i++)
		{
			$entry 	= $atomfile->newEntry($data['name'], $this -> head_link, '');
			$entry	->setUpdated(date('c'));
			$entry ->addContributor($user['name'], '', $this -> head_link);				
		}

		$atomfile->saveAtom('1.0.0', $this -> enddir);
	}

	/**
	 * Funktion mit der man pr�fen kann ob alle Parameter gesetzt sind
	 *
	 * @return boolean
	 * @access public
	 */
	function checkParams()
	{
		//Jeden ben�tigen Parameter �berpr�fen
		if($this -> head_titel == '')
		{
			return false;
		}
		if($this -> head_link == '')
		{
			return false;
		}
		if($this -> head_language == '')
		{
			return false;
		}
		if($this -> head_image == '')
		{
			return false;
		}
		if($this -> head_description == '')
		{
			return false;
		}
		//Am Schluss true zur�ckgeben
		return true;
	}


/*   Getter + Setter                               */


	/**
	 * Funktion mit der man die Variabel rss091 setzen kann
	 *
	 * @param boolean $var	Neuer wert der Variabel
	 * @access public
	 */
	function setRss091($var)
	{
		$this -> rss091 = $var;
	}

	/**
	 * Funktion mit der man die Variabel rss10 setzen kann
	 *
	 * @param boolean $var	Neuer wert der Variabel
	 * @access public
	 */
	function setRss10($var)
	{
		$this -> rss10 = $var;
	}

	/**
	 * Funktion mit der man die Variabel rss020 setzen kann
	 *
	 * @param boolean $var	Neuer wert der Variabel
	 * @access public
	 */
	function setRss20($var)
	{
		$this -> rss20 = $var;
	}

	/**
	 * Funktion mit der man die Variabel atom10 setzen kann
	 *
	 * @param boolean $var	Neuer wert der Variabel
	 * @access public
	 */
	function setAtom10($var)
	{
		$this -> atom10 = $var;
	}

	/**
	 * Funktion mit der man die Variabel atom10 setzen kann
	 *
	 * @param string $var	Neuer wert der Variabel
	 * @access public
	 */
	function setEnddir($var)
	{
		//Erst pr�fen ob das verzeichnis auch existiert
		if(!file_exists($var))
		{
			return false;
		}
		$this -> enddir = $var;
	}

	/**
	 * Funktion mit der man die Variabel atom10 setzen kann
	 *
	 * @param string $var	Neuer wert der Variabel
	 * @access public
	 */
	function setHeadTitel($var)
	{
		$this -> head_titel = $var;
	}

	/**
	 * Funktion mit der man die Variabel atom10 setzen kann
	 *
	 * @param string $var	Neuer wert der Variabel
	 * @access public
	 */
	function setHeadImage($var)
	{
		$this -> head_image = $var;
	}

	/**
	 * Funktion mit der man die Variabel atom10 setzen kann
	 *
	 * @param string $var	Neuer wert der Variabel
	 * @access public
	 */
	function setHeadDescription($var)
	{
		$this -> head_description = $var;
	}

	/**
	 * Funktion mit der man die Variabel atom10 setzen kann
	 *
	 * @param string $var	Neuer wert der Variabel
	 * @access public
	 */
	function setHeadLanguage($var)
	{
		$this -> head_language = $var;
	}

	/**
	 * Funktion mit der man die Variabel atom10 setzen kann
	 *
	 * @param string $var	Neuer wert der Variabel
	 * @access public
	 */
	function setHeadLink($var)
	{
		$this -> head_link = $var;
	}

}