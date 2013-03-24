<?php

class OpenProfil extends OpenModulBase
{
	
	public function __construct()
	{
		parent::__construct ();
		
		if (! isset ( $_GET ['action'] ))
			return;
		
		switch ($_GET ['action'])
		{
			case 'login' :
				if ($this->login ())
					$this->Item ();
				else
					$this->Overview ();
				break;
			case 'logout' :
				$this->Logout ();
				$this->Overview ();
				break;
		}
	
	}
	
	protected function Save()
	{
		$data = Validator::ForgeInput ( $_POST ['user'] );
		$userId = Cms::GetInstance ()->GetUser ()->id;
		
		if (! empty ( $data ['newpassword'] ))
		{
			$this->SavePassword ( $data );
		
		}
		
		try
		{
			SqlManager::GetInstance ()->Update ( 'sysuser', array ('firstname' => $data ['firstname'], 'name' => $data ['name'], 'email' => $data ['email'], 'phoneprivate' => $data ['phoneprivate'], 'phonebusiness' => $data['phonebusiness'], 'phonemobile' => $data ['phonemobile'], 'residence' => $data ['residence'], 'zip' => $data ['zip'], 'street' => $data ['street'] ), 'id=\'' . $userId . '\'' );
		
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
	
		MySmarty::GetInstance() -> OutputConfirmation('Profil gespeichert');
		
	}
	
	private function SavePassword($data)
	{
		$userId = Cms::GetInstance ()->GetUser ()->id;
		$oldpw = null;
		try{
			$oldpw = SqlManager::GetInstance() -> SelectItem('sysuser', 'password', 'id=\''. $userId .'\'');
			
		}
		catch(SqlManagerException $ex)
		{
			throw new CMSException('Sql-Fehler', CMSException::T_MODULEERROR, $ex);
		}
		
		if($oldpw != md5($data['oldpassword']))
		{
			MySmarty::GetInstance ()->OutputWarning ( 'Altes Passwort stimmt nicht &uuml;berein. Passwort nicht &uuml;bernommen.' );
			return;
		}
		
		if(!Functions::CheckPwComplex($data['newpassword']))
		{
			MySmarty::GetInstance ()->OutputWarning ( 'Neues Passwort ist zu einfach. Passwort nicht &uuml;bernommen.' );
			return;
		}
		
		if ($data ['newpassword'] != $data ['newpasswordconf'])
		{
			MySmarty::GetInstance ()->OutputWarning ( 'Neues Passwort simmt nicht &uuml;berein. Passwort nicht &uuml;bernommen.' );
			return;
		}
		
		try
		{
			SqlManager::GetInstance ()->Update ( 'sysuser', array ('password' => md5 ( $data ['newpassword'] ) ), 'id=\'' . $userId . '\'' );
		
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
	}
	
	protected function Overview()
	{
		CssImport::ImportCss ( 'profil.css' );
		
		if (Cms::GetInstance ()->GetUser ()->IsDeclared ())
		{
			$this->Item ();
			return;
		}
		
		$loginLink = Functions::GetLink ( array ('action' => 'login' ), true );
		MySmarty::GetInstance ()->OutputModuleVar ( 'loginlink', $loginLink );
		$this->OutputLoginFlag ();
	}
	
	private function OutputLoginFlag()
	{
		MySmarty::GetInstance ()->OutputModuleVar ( 'action', 'login' );
	}
	
	protected function Item()
	{
		CssImport::ImportCss ( 'profil.css' );
		$userId = Cms::GetInstance ()->GetUser ()->id;
		
		$data = null;
		try
		{
			$data = SqlManager::GetInstance ()->SelectRow ( 'sysuser', $userId );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'user', $data );
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'template', HTML::Select ( 'sysstyle', 'name', 4 ) );
	
	}
	
	private function login()
	{
		$username = Validator::ForgeInput ( $_POST ['username'] );
		$password = md5 ( Validator::ForgeInput ( $_POST ['password'] ) );
		
		$userData = null;
		try
		{
			$userData = SqlManager::GetInstance ()->Select ( 'sysuser', array ('id' ), 'nick=\'' . $username . '\' AND password=\'' . $password . '\'', '', 1 );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		if (count ( $userData ) == 0)
		{
			MySmarty::GetInstance ()->OutputWarning ( 'Login fehlgeschlagen, Zugangsdaten falsch!' );
			$this->OutputLoginFlag ();
			return false;
		}
		
		// Neue Instanz der user-Klasse anlegenw elche nicht sofort versucht den User zu laden
		$user = new User ( false );
		
		// Laden des Benutzers ewlcher sich angemeldet hat
		$user->Load ( $userData [0] ['id'] );
		
		// Benutzer in der Session speichern damit...
		Session::setUser ( $user );
		
		// .. beim reboot der jetzt angemeldete erkannt wird
		Cms::GetInstance ()->reboot ();
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Login erfolgreich' );
		
		return true;
	}
	
	private function Logout()
	{
		Session::unsetUser ();
		Cms::GetInstance ()->reboot ();
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Logout erfolgreich' );
	}
}
