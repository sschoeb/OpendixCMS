<?php

/**
 * Erstellt VCards
 * 
 * @package 	OpendixCMS.Core
 */

/**
 * Erstellt VCards
 * 
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @author 		Stefan Schöb <opendix@gmail.com>
 */
class VCardCreator
{

	/**
	 * Vorname der Person	
	 *
	 * @var	String
	 */
	private $firstname;
	
	/**
	 * Nachname der Person
	 *
	 * @var	String
	 */
	private $lastname;
	
	/**
	 * E-Mail der Person
	 *
	 * @var String
	 */
	private $email;
	
	/**
	 * Private Telefonnummer
	 *
	 * @var String
	 */
	private $phoneHome;
	
	/**
	 * Geschäftliche Telefonnummer
	 *
	 * @var String
	 */
	private $phoneCompany;
	
	/**
	 * Natelnummer
	 *
	 * @var String
	 */
	private $phoneMobile;
	
	/**
	 * Position der Person
	 *
	 * @var String
	 */
	private $position;
	
	/**
	 * Postleitzahl des Wohnorts
	 *
	 * @var int
	 */
	private $plz;
	
	/**
	 * Strasse an der die Person wohnt
	 *
	 * @var String
	 */
	private $road;
	
	/**
	 * Stadt in der die Person wohnt
	 *
	 * @var String
	 */
	private $city;
	
	/**
	 * Kanton in dem die Person wohnt
	 *
	 * @var String
	 */
	private $department;
	
	/**
	 * Land in dem die Person wohnt
	 *
	 * @var String
	 */
	private $country;

	/**
	 * Konstruktor
	 *
	 * @param 	String	$firstname		Vorname der Person
	 * @param 	String 	$lastname		Nachname der Person
	 * @param 	String 	$email			Email der Person
	 * @param 	String 	$phoneHome		Private Rufnummer der Person
	 * @param 	String 	$phoneCompany	Geschäftliche Nummer der Person
	 * @param 	String 	$phoneMobile	Natelnummer der Person
	 * @param 	String 	$position		Poistion der Person
	 * @param 	String 	$plz			Postleitzahl
	 * @param 	String 	$road			Strasse
	 * @param 	String 	$city			Ortschaft
	 */
	public function __construct($firstname='', $lastname='', $email='', $phoneHome='', $phoneCompany='', $phoneMobile= '', $position='', $plz='', $road='', $city='')
	{
		$this -> firstname 		= $firstname;
		$this -> lastname 		= $lastname;
		$this -> email 			= $email;
		$this -> phoneHome 		= $phoneHome;
		$this -> phoneCompany 	= $phoneCompany;
		$this -> phoneMobile 	= $phoneMobile;
		$this -> position 		= $position;
		$this -> plz			= $plz;
		$this -> road			= $road;
		$this -> city			= $city;
	}

	/**
	 * Erstellt die Datei in dem angegebenen Pfad
	 *
	 * @param	String 	$path	Pfad an dem die Datei erstellt werden soll
	 */
	public function create($path)
	{
	$vcard = "BEGIN:VCARD\r\n";
	$vcard .= "VERSION:2.1\r\n";
	$vcard .= 'N:' . $this -> lastname .  ';'. $this -> firstname .";;;\r\n";
	$vcard .= 'FN:' . $this -> lastname . ' ' . $this -> firstname . "\r\n";
	$vcard .= 'TITLE: ' . $this -> position . "\r\n";
	$vcard .= 'TEL;WORK;VOICE:' . $this -> phoneCompany . "\r\n";
	$vcard .= 'TEL;HOME;VOICE:' . $this -> phoneHome . "\r\n";
	$vcard .= 'TEL;CELL;VOICE:' . $this -> phoneMobile . "\r\n";
	$vcard .= 'ADR;HOME:;;'. $this -> road .';' . $this -> city . ';'. $this -> department.';'. $this -> plz . ';'. $this ->country  . "\r\n";
	$vcard .= 'EMAIL;PREF;INTERNET:' . $this -> email . "\r\n";
	$vcard .= "END:VCARD\r\n";
	file_put_contents($path, $vcard);
	}

}
