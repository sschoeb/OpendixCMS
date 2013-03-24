<?php

class RegistrationAdmin extends ModulBase
{
	public function __construct()
	{
		CssImport::ImportCss('content.css');
		
		if (isset ( $_GET ['action'] ) && $_GET ['action'] == 'save')
		{
			$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_EDIT, $_GET ['sub'] );
			if (! $hasLaw)
			{
				throw new CMSException ( 'Sie sind nicht berechtigt diese Seite zu bearbeiten!', CMSException::T_MODULEERROR );
			}
			$this->Save ();
		}
		$this->Overview ();
	}
	
	protected function Overview()
	{
		$data = array();
		$config = new Configuration('config/registration.ini');
		$data['adminText'] = Functions::Br2nl(file_get_contents('config/adminText.txt'));
		$data['adminMail'] = $config -> Get('admin/mail');
		$data['userText'] = Functions::Br2nl(file_get_contents('config/userText.txt'));
		MySmarty::GetInstance()->OutputModuleVar('data', $data);
	}
	
	protected function Save()
	{
		$config = new Configuration('config/registration.ini');
		$data = array('admin' => array());
		//$data['admin']['text'] = $_POST['textadmin'];
		$data['admin']['mail'] = $_POST['mailadmin'];
		//$data['user']['text'] = $_POST['textuser'];
		
		file_put_contents('config/adminText.txt', nl2br(trim($_POST['textadmin'])));
		file_put_contents('config/userText.txt', nl2br(trim($_POST['textuser'])));
		
		$config -> WriteIniFile($data);
		
		MySmarty::GetInstance()->OutputConfirmation('Erfolgreich gespeichert');
	}
}