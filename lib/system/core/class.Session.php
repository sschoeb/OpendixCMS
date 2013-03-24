<?php

/**
 * Session
 * 
 * @package    	OpendixCMS.Core
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	1.0
 */

/** 
 * Klasse zum verwalten der Session-variabeln
 *
 * 
 * @access   	public
 * @package 	OpendixCMS.Core
 * @version  	1.0
 */

class Session
{	
	/**
	 * Erstellen der Session-variabeln
	 *
	 */
	public static function Create()
	{
		$_SESSION['SYSTEM']['visitstart'] 		= time();
		$_SESSION['SYSTEM']['letzte_bewegung'] 	= time();
		$_SESSION['SYSTEM']['eingelogt'] 		= false;
		$_SESSION['SYSTEM']['user_id'] 			= '';
		$_SESSION['SYSTEM']['verlauf']			= array();
	}
	
	public static function setUser($user)
	{
		$_SESSION['SYSTEM']['user_id'] = $user -> id;
	}
	
	public static function unsetUser()
	{
		$_SESSION['SYSTEM']['user_id'] = '';
	}
	
	/**
	 * Funktion die bei jedem durchgang aufgerufen wird
	 *
	 */
 	public static function Step()
	{
		if(!isset($_SESSION['SYSTEM']['visitstart']))
		{
			Session::Create();
		}
		$_SESSION['SYSTEM']['letzte_bewegung'] 	= time();
		if(isset($_GET['sub']))
		{
			$_SESSION['SYSTEM']['verlauf']			= $_GET['sub'];
		}
		
	}
	
}

