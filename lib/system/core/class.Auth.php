<?php
/**
 * Authentifizerungsklassen
 * 
 *
 * @package    	OpendixCMS.Core
 * @author     	Stefan Sch�b <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Sch�b
 * @version    	1.0
 */

/**
 * Klsse f�r login, logout und standard authentifizierung
 *
 * 
 * @access   	public
 * @package 	OpendixCMS.Core
 * @version  	1.0
 */

class Auth
{
	/**
	 * Funktion mit der man einen Benutzer einloggen kann
	 *
	 * @return boolean
	 */
	function Login()
	{
		if(isset($_POST['user']))
		{
			$user		= functions::cleaninput($_POST['user']);
		}
		else
		{
			$user = '';
		}
		if(isset($_POST['pw']))
		{
			$passwort 	= md5(functions::cleaninput($_POST['pw']));
		}
		else 
		{
			$passwort = '';
		}

		$query		= "SELECT id, nick, vorname, nachname, email FROM sysuser WHERE nick = '$user' AND passwort = '$passwort'";
		$insert		= @mysql_query($query) OR die('Fehler: login.php - Loginabfrage');
		if(@mysql_num_rows($insert) == 0)
		{
			MySmarty::GetInstance() -> OutputError('Falsche Login-Daten');
			return false;
		}
		$daten = mysql_fetch_assoc($insert);

		//Session-Variabelnd es Benutzers erstellen
		$_SESSION['SYSTEM']['userId']		= $daten['id'];
		$_SESSION['SYSTEM']['nick']			= $daten['nick'];
		$_SESSION['SYSTEM']['vorname']		= $daten['vorname'];
		$_SESSION['SYSTEM']['nachname']		= $daten['nachname'];
		$_SESSION['SYSTEM']['email']		= $daten['email'];
		$_SESSION['SYSTEM']['login']		= true;

		MySmarty::GetInstance() -> OutputConfirmation('Login erfolgreich');
		return true;
	}

	/**
	 * Funktion mit der man einen benutzer abmelden kann
	 *
	 * @return boolean
	 */
	function Logout()
	{
		ob_start ();
		session_start ();
		session_unset ();
		session_destroy ();
		ob_end_flush ();

		if(isset($_SESSION['userid']))
		{
			MySmarty::GetInstance() -> OutputError('Sie konnten nicht abgeleldet werden');
			return false;
		}
		MySmarty::GetInstance() -> OutputConfirmation('Sie haben sich erfolgreich abgemeldet!');
		return true;
	}

	/**
	 * Funktion die �berpr�ft ob man eingeloggt ist
	 *
	 * @return true
	 */
	function Checklogedin()
	{
		return isset($_SESSION['userId']);
	}

	/**
	 * Überprüft ob man sich einloggen will, falls ja ruft es die login-Funktion auf
	 *
	 * @return mixed false wenn fehlgeschlagen ansonsten nichts
	 */
	function Checklogin()
	{
		if(isset($_GET['action']))
		{
			if($_GET['action'] != 'login')
			{
				auth::checklogout();
				return false;
			}

		}
		else 
		{
			return false;
		}

		$authclass = new auth;
		if(!$authclass->login())
		{
			MySmarty::GetInstance() -> OutputError('Login fehlgeschlagen!');
			return false;
		}


	}

	/**
	 * �berpr�ft ob man sich ausloggen will
	 *
	 * @return mixed false wenn fehlgeschlagen ansonsten nichts
	 */
	function checklogout()
	{
		if(isset($_GET['action']))
		{
			if($_GET['action'] != 'logout')
			{
				return false;
			}
		}else {
			return false;
		}

		$authclass = new auth;
		if(!$authclass -> logout())
		{
			MySmarty::GetInstance() ->OutputError('Logout fehlgeschlagen');
			return false;
		}

	}

	function checkloginform()
	{

		if(!isset($_SESSION['userId']))
		{
			MySmarty::GetInstance() -> OutputSystemVar('showLoginForm', 1);
			//functions::output_var('opendix_loginform', 1);
		}
	}
}
