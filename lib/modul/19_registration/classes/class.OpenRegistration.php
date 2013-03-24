<?php

class OpenRegistration extends ModulBase
{
	public function __construct()
	{
		parent::__construct ();
		
		CssImport::ImportCss ( 'content.css' );
		
		if (! isset ( $_GET ['action'] ))
			return;
		
		switch ($_GET ['action'])
		{
			case 'register' :
				$this->Register ();
				$this->Overview ();
				break;
		}
	}
	
	protected function Overview()
	{
		$link = Functions::GetLink ( array ('action' => 'register' ), true );
		$abos = null;
		try
		{
			$abos = SqlManager::GetInstance ()->SelectAll ( 'modregistrationabo', '', 'folge' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', $ex );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'abos', $abos );
		MySmarty::GetInstance ()->OutputModuleVar ( 'abosSelect', HTML::Select ( 'modregistrationabo', 'name', 0, 'folge' ) );
		MySmarty::GetInstance ()->OutputModuleVar ( 'link', $link );
	}
	
	private function Register()
	{
		$data = Validator::ForgeInput ( $_POST ['regdata'] );
		
		if (! Validator::IsValidEmail ( $data ['email'] ))
		{
			MySmarty::GetInstance ()->OutputWarning ( 'Bitte geben Sie eine korrekte E-Mail Adresse an.' );
			return;
		}
		
		if (strlen ( $data ['name'] ) < 2)
		{
			MySmarty::GetInstance ()->OutputWarning ( 'Bitte geben Sie einen korrekten Namen an.' );
			return;
		}
		
		if($data['spam'] != 8)
		{
			MySmarty::GetInstance()->OutputWarning('Anmeldung konnte nicht erfolgreich da der Spamschutz nicht korrekt ausgef&uuml;llt wurde. Bitte berechnen Sie die Aufgabe und schreiben Sie die L&ouml;sung in das nebenstehende Feld!');
			return;
		}
		
		$adminText = file_get_contents ( 'config/adminText.txt' );
		$config = new Configuration ( 'config/registration.ini' );
		$emailAdmin = $config->Get ( 'admin/mail' );
		$userText = file_get_contents ( 'config/userText.txt' );
		
		// Anmeldung an den Admin senden
		Functions::Sendmail ( $emailAdmin, 'Neue Registration', $this->PrepareText ( $adminText ) );
		
		// Bestätigung an den neuen Benutzer senden
		Functions::Sendmail ( $data ['email'], 'Ihre Anmeldung beim Tennisclub Grabs', $this->PrepareText ( $userText ) );
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Sie haben sich erfolgreich beim Tennisclub Grabs angemeldet. Sie erhalten in k&uuml;rze eine E-Mail welche die Anmeldung best&auml;tigt. ' );
	}
	
	private function PrepareText($text)
	{
		$data = Validator::ForgeInput ( $_POST ['regdata'], true, true, true );
	
		foreach ( $data as $key => $value )
		{
			if ($key == 'abo')
			{
				$value = SqlManager::GetInstance ()->SelectItemById ( 'modregistrationabo', 'name', $value );
			}
			
			$text = str_replace ( '[' . strtoupper ( $key ) . ']', $value, $text );
		}
		return $text;
	}
}

