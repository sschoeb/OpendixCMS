<?php

define('REWRITERULEFILE', '.htaccess');

class ModRewriteRuleEditor
{
	function __Construct()
	{
		if($_GET['action'] == 'save')
		{
			$this -> Save();
		}
		$this -> Show();
	}

	private function Save()
	{
		$newrules = $_POST['rewriteRules'];
		if(file_exists(REWRITERULEFILE))
		{
			functions::output_warnung('Datei mit den Regel existiert nicht!');
		}

		$rules = file(REWRITERULEFILE);
		
		for($i=0; $i<count($rules); $i++)
		{
			$inhalt .= $rules[$i];
			
			if($rules[$i] == '#RuleStart')
			{
				break;
			}
		}

		if(!file_put_contents(REWRITERULEFILE, $inhalt . $newrules))
		{
			functions::output_warnung('Neue RewriteRules konnten nicht gespeichert werden!');
			return;
		}
		functions::Output_bericht('RewriteRules erfolgreich gespeichert!');
	}

	private function Show()
	{
		if(file_exists(REWRITERULEFILE))
		{
			functions::output_warnung('Datei mit den Regel existiert nicht!');
		}
		$rules = file(REWRITERULEFILE);
		$start = false;
		$inhalt = '';
		for($i=0; $i<count($rules); $i++)
		{
			if($start)
			{
				$inhalt .= $rules[$i];
			}
			if($rules[$i] == '#RuleStart')
			{
				$start = true;
			}
		}

		functions::Output_var('rewriteRules', $inhalt);		
		
	}
}

?>