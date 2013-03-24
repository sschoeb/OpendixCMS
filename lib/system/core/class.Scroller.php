<?php
/**
 * Scroller
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Bietet Funktionen um eine Navigation zu erstellen
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		1.0
 */
class Scroller
{
	/**
	 * Array in dem temporär die Links gespeichert werden
	 *
	 * @var Array
	 */
	private $linkArray = array ();
	
	/**
	 * Anzahl Einträge die pro Seite angezeigt werden
	 *
	 * @var int
	 */
	private $count = 0;
	
	/**
	 * Anzahl der Einträge die in der Tabelle vorhanden sind
	 *
	 * @var int
	 */
	private $anz = 0;
	
	/**
	 * Seite auf der sich der Besucher befindet
	 *
	 * @var int
	 */
	private $page = 0;
	
	/**
	 * [Beschreibung]
	 *
	 * @var int
	 */
	private $maxPageCount = 0;
	
	/**
	 * Konstruktor
	 *
	 * @param int	$count	Anzahl der EInträge die pro Seite angezeigt werden
	 * @param int 	$anz	Anzahl der Einträge die in der Tabelle vorhanden sind
	 * @param int	$page	Seite auf der sich der User befindet
	 */
	public function __construct($count, $anz, $page)
	{
		$this->count = $count;
		$this->anz = $anz;
		$this->page = $page;
		
		$this->Init ();
	}
	
	/**
	 * Gibt die generierten Links zurück
	 *
	 * @return unknown
	 */
	public function GetLinks()
	{
		return $this->linkArray;
	}
	
	private function Init()
	{
		$this->linkArray = array ('link' => array ('start', 'back', 'end', 'next' ), 'site' => array ('current', 'total' ) );
		
		if ($this->anz <= $this->count)
			return;
		
		if ($this->page > 1)
		{
			$this->linkArray ['link'] ['start'] = Functions::GetLink ( array ('page' => 0, 'action' => '' ), true );
			$this->linkArray ['link'] ['back'] = Functions::GetLink ( array ('page' => $this->page - 1, 'action' => '' ), true );
		}
		
		$this->maxPageCount = ceil ( $this->anz / $this->count );
		
		if ($this->page != $this->maxPageCount)
		{
			$this->linkArray ['link'] ['next'] = Functions::GetLink ( array ('page' => $this->page + 1, 'action' => '' ), true );
			$this->linkArray ['link'] ['end'] = Functions::GetLink ( array ('page' => $this->maxPageCount, 'action' => '' ), true );
		}
		
		$this -> linkArray['site']['current'] = $this ->page;
		$this -> linkArray['site']['total'] = $this -> maxPageCount;
	}
	
	private function InitAlt()
	{
		//Wenn die Gesamt-Anzahl der Einträge kleiner ist als die Anzahl Einträge pro Seite
		//dann wird keine Navigation angezeigt
		if ($this->anz <= $this->count)
		{
			return array ();
		}
		//Wenn es sich nicht um die erste Seite handelt wird ein "Erste Seite"-Link und
		//ein "Zrück"-Link erstellt
		if ($this->page > 1)
		{
			//Erste Seite-Link
			$this->linkArray [] ['name'] = 'Erste';
			$this->linkArray [count ( $this->linkArray ) - 1] ['link'] = Functions::GetLink ( array ('page' => 0, 'action' => '' ), true );
			
			//Zurück Link
			$this->linkArray [] ['name'] = 'Zur&uuml;ck';
			$this->linkArray [count ( $this->linkArray ) - 1] ['link'] = Functions::GetLink ( array ('page' => $this->page - 1, 'action' => '' ), true );
		}
		
		//Ermitteln wieviele Seiten möglich sind
		$this->maxPageCount = ceil ( $this->anz / $this->count );
		
		//Erstellen der Links in der Mitte
		//		$temp = 0;
		//		while($temp != $this -> maxPageCount)
		//		{
		//			$temp++;
		//			$this -> linkArray[]['name'] = "$temp";
		//			$this -> linkArray[count($this -> linkArray) - 1]['link'] = Functions::GetLink(array('page' => $temp, 'action' => ''), true);
		//			if($temp == $this -> page)
		//			{
		//				$this -> linkArray[count($this -> linkArray) - 1]['activ'] = true;
		//			}
		//		}
		//		
		//Falls es sich nicht um die letzte Seite handelt dann noch einen "Ende"-Link
		//und einen "Vorwärts"-Link erstellen
		if ($this->page != $this->maxPageCount)
		{
			//Vorwärts Link
			$this->linkArray [] ['name'] = 'Vorw&auml;rts';
			$this->linkArray [count ( $this->linkArray ) - 1] ['link'] = Functions::GetLink ( array ('page' => $this->page + 1, 'action' => '' ), true );
			
			$this->linkArray [] ['name'] = 'Letzte';
			$this->linkArray [count ( $this->linkArray ) - 1] ['link'] = Functions::GetLink ( array ('page' => $this->maxPageCount, 'action' => '' ), true );
		}
	}

}