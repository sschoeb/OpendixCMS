<?php

abstract class SimpleAdminModulBase extends ModulBase
{
	protected $table;
	
	public function __construct()
	{
		CssImport::ImportCss ( 'content.css' );
		$this->table = Cms::GetInstance ()->GetModule ()->GetDbConfiguration ( 'admintable' );
		parent::__construct ();
	}
	
	function Overview()
	{
		$data = null;
		try
		{
			$data = SqlManager::GetInstance ()->SelectAll ( $this->table );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL Fehler SimpleAdminModuleBase', CMSException::T_MODULEERROR );
		}
		
		// Jeder Zeile einen Löschen-Link hinzufügen
		for($i = 0; $i < count ( $data ); $i ++)
		{
			$data [$i] ['links'] ['delete'] = Functions::GetLink ( array ('action' => 'delete', 'id' => $data [$i] ['id'] ), true );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'data', $data );
	}
	
	function Save()
	{
		$data = Validator::ForgeInput ( $_POST ['data'] );
		
		foreach ( $data as $id => $data )
		{
			$this->SaveItem ( $id, $data );
		}
		MySmarty::GetInstance ()->OutputConfirmation ( 'Speichern erfolgreich!' );
	}
	
	function Delete()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Ung&uuml;ltige ID', CMSException::T_MODULEERROR );
		}
		try
		{
			$data = SqlManager::GetInstance ()->DeleteById ( $this->table, $id );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL Fehler SimpleAdminModuleBase', CMSException::T_MODULEERROR );
		}
		MySmarty::GetInstance ()->OutputConfirmation ( 'L&ouml;schen erfolgreich!' );
	}
	
	function Add()
	{
		$this->AddNewItem ( Validator::ForgeInput ( $_POST ['newData'] ) );
	}
	
	function Item()
	{
		$this->Overview ();
	}
	
	abstract function SaveItem($id, $row);
	abstract function AddNewItem($data);

}
