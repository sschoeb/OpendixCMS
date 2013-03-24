<?php

/**
 * Klasse um das eigene profil anzuzeigen wenn man eingeloggt ist
 * 
 * @author Stefan Sch�b
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Sch�b
 * @since 1.0     
 */
class MyProfil extends Profil
{
	function __Construct()
	{
		if(!isset($_SESSION))
		{
			return false;
		}

		if($_GET['action'] == 'save')
		{
			$this -> Save($_SESSION['userId']);
		}

		$this -> Showeinzel($_SESSION['userId']);
	}
}

?>