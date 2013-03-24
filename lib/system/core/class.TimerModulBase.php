<?php

class TimerModulBase extends ModulBase
{
	public function __construct()
	{
		parent::__construct ();
		
		JsImport::ImportSystemJS('timer.js');
		JsImport::RunJs("checkTimerBlockVisibility();");
		
		if (! isset ( $_GET ['action'] ))
			return;
		
		switch ($_GET ['action'])
		{
			case 'AJAXAddTimer' :
				$this->AJAX_AddTimer ();
				break;
			case 'AJAXRemoveTimer' :
				$this->AJAX_RemoveTimer ();
				break;
		}
	}
	
	protected function AJAX_AddTimer()
	{
		$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_EDIT, $_GET ['sub'] );
		if (! $hasLaw)
		{
			AJAX::ThrowError ( 'Sie sind nicht berechtigt Timer hinzuzufuegen!' );
		}
		
		$actionId = Validator::ForgeInput ( $_GET ['actionid'] );
		$startTime = Validator::ForgeInput ( $_GET ['date'] );
		$entityId = Validator::ForgeInput($_GET['entityId']);
		
		$modId= Cms::GetInstance() -> GetModule() -> GetModuleId();
		
		if (! is_numeric ( $actionId ) || empty ( $startTime ))
		{
			AJAX::ThrowError ( 'Ung&uuml;ltige Parameter erhalten. Timer konnte nicht hinzgef&uuml;gt werden!' );
		}
		
		Timer::Set(null, date('c', $startTime), $actionId, $modId, $entityId);
		
		$xmlDoc = new DOMDocument ();
		$root = XML::CreateDocument ( $xmlDoc, 'answer' );
		$item = $xmlDoc->createElement ( 'element' );
		$item->setAttribute ( 'id', SqlManager::GetInstance() -> GetLastUsedId() );
		$root->appendChild ( $item );
		AJAX::OutputXmlData ( $xmlDoc->saveXML () );
	
	}
	
	protected function AJAX_RemoveTimer()
	{
		$hasLaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_EDIT, $_GET ['sub'] );
		if (! $hasLaw)
		{
			AJAX::ThrowError ( 'Sie sind nicht berechtigt Timer hinzuzuf&uuml;gen!' );
		}
		
		$timerId = Validator::ForgeInput ( $_GET ['timerId'] );
		
		if (! is_numeric ( $timerId ))
		{
			AJAX::ThrowError ( 'Ung&uuml;ltige Parameter erhalten. Timer konnte nicht hinzgef&uuml;gt werden!' );
		}
		
		
		
		Timer::Remove($timerId);
		
		$xmlDoc = new DOMDocument ();
		$root = XML::CreateDocument ( $xmlDoc, 'answer' );
		$item = $xmlDoc->createElement ( 'element' );
		$item->setAttribute ( 'id', $timerId );
		$root->appendChild ( $item );
		AJAX::OutputXmlData ( $xmlDoc->saveXML () );
	}
	
	protected function GetTimer()
	{
		$moduleId = Cms::GetInstance() -> GetMenueId();
		$entityId = Validator::ForgeInput($_GET['id']);
		if(!is_numeric($entityId))
		{
			throw new CMSException('Keine g&uuml;ltige ID!', CMSException::T_SYSTEMERROR);
		}
		return Timer::GetTimerForEntity($moduleId, $entityId);
	}

}
