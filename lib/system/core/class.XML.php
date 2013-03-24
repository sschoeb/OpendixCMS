<?php

class XML
{
	public static function CreateDocument($xmlDoc, $name = 'xmldoc')
	{
		return $xmlDoc->appendChild($xmlDoc->createElement($name));
	}
	
	public function AddElement($xmlDoc, $node,  $name, $attributes)
	{
		$item =  $xmlDoc -> createElement($name, '');	
		
		foreach ($attributes as $key => $value)
		{
			$item -> setAttribute($key, $value);
		}
		$node -> appendChild($item);
		return $node;
	}
}