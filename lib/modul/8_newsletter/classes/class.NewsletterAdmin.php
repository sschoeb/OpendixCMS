<?php

class NewsletterAdmin extends ModulBase
{
	public function __construct()
	{
		parent::__construct ();
	}
	
	protected function Overview()
	{
		$data = null;
		try
		{
			$data = SqlManager::GetInstance ()->SelectAll ( 'modnewsletter' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'emails', $data );
	}
	
	protected function Save()
	{
		
		$data = Validator::ForgeInput ( $_POST ['data'] );
		
		$data = str_replace ( ';', '', $data );
		
		$emails = preg_split ( "[\n|\r]", $data );
		
		$emails = array_unique ( $emails, SORT_STRING );
		sort ( $emails, SORT_STRING );
		
		SqlManager::GetInstance ()->Delete ( 'modnewsletter' );
		
		for($i = 0; $i < count ( $emails ); $i ++)
		{
			if (trim ( $emails [$i] ) == '')
				continue;
			SqlManager::GetInstance ()->Insert ( 'modnewsletter', array ('gid' => 0, 'email' => $emails [$i] ) );
		}
	
	}
	
	protected function Item()
	{
		$this->Overview ();
	}
}