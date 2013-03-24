<?php

class LinkAdmin extends ModulBase
{
	public function __construct()
	{
		parent::__construct ();
		
		CssImport::ImportCss ( 'content.css' );
		CssImport::ImportCss ( 'linkadmin.css' );
	}
	
	protected function Save()
	{
		$daten = Validator::ForgeInput ( $_POST ['links'] );
		
		foreach ( $daten as $id => $linkdata )
		{
			try
			{
				SqlManager::GetInstance ()->Update ( 'modlink', array ('gId' => $linkdata ['gid'], 'name' => $linkdata ['name'], 'url' => $linkdata ['url'] ), 'id=\'' . $id . '\'' );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
			}
		}
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Links erfolgreich gespeichert' );
	
	}
	
	protected function Item()
	{
		$this->Overview ();
	}
	
	protected function Delete()
	{
		$id = Validator::ForgeInputNumber ( $_GET ['id'] );
		try
		{
			SqlManager::GetInstance ()->DeleteById ( 'modlink', $id );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		MySmarty::GetInstance() -> OutputConfirmation('Link erfolgreich entfernt');
	}
	
	protected function Overview()
	{
		$daten = null;
		try
		{
			$daten = SqlManager::GetInstance ()->Select ( 'modlink', array ('id', 'gId', 'name', 'url' ), '', 'gId' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		for($i = 0; $i < count ( $daten ); $i ++)
		{
			$daten [$i] ['group'] = HTML::Select ( 'modlinkgruppe', 'gruppe_name', $daten [$i] ['gId'] );
			$daten[$i]['delete'] = Functions::GetLink(array('action' => 'delete', 'id' => $daten[$i]['id']), true);
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'links', $daten );
		MySmarty::GetInstance ()->OutputModuleVar ( 'group', HTML::Select ( 'modlinkgruppe', 'gruppe_name' ) );
	}
	
	protected function Add()
	{
		$daten = Validator::ForgeInput ( $_POST ['newlink'] );
		try
		{
			SqlManager::GetInstance ()->Insert ( 'modlink', array ('gId' => $daten ['gId'], 'name' => $daten ['name'], 'url' => $daten ['url'] ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		MySmarty::GetInstance() -> OutputConfirmation('Link erfolgreich hinzugef&uuml;gt.');
	}
}

