<?php

class OpenContent extends OpenModulBase
{
	public function __construct()
	{
		parent::__construct();
	}
	
	protected function Overview()
	{
		
		CssImport::ImportCss('content.css');
		
		JsImport::ImportExternalJs('http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAWXsvLXcf2I8ZKAQhc9R65RQHTBJ5hqq2r81cPihkQosnbgOXnxQ_0P6TkZqu5INH3qkd1fVzPrOJHw');
		JsImport::ImportSystemJS('googlemaps.js');
		JsImport::RunJs('twGmapLoad()');
		
		 $id = Cms::GetInstance() -> GetModule() -> GetDbConfiguration('contentid');
		$data = null;
		try
		{
			$data = SqlManager::GetInstance() -> SelectItem('modcontent', 'content', 'id=\''. $id .'\'');	
			
		}catch(SqlManagerException $ex)
		{
			throw new CMSException('SQL-Fehler', CMSException::T_MODULEERROR, $ex);
		}
	
		$data = html_entity_decode($data, ENT_QUOTES, 'ISO-8859-15');
		
		MySmarty::GetInstance() -> OutputModuleVar('content', $data);
	}
}