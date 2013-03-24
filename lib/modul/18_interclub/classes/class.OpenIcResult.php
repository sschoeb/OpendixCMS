<?php

class OpenIcResult extends OpenModulBase
{
	public function Overview()
	{
		return;
		$link = 'http://comp.swisstennis.ch/ic2010/servlet/teamChoice?Lang=D&ClubNr=1097';
		$data = Functions::GetWebsiteContent ( $link );
		
		//<a href="../servlet/TeamResults?TeamId=6046&Lang=D">JC Damen (Grp   6)</a>
		$pattern = '/\<a href="\.\.\/servlet\/TeamResults\?TeamId=(?P<nr>[0-9]+).*?"\>(?P<team>.*?)\<\/a/';
		$count = preg_match_all ( $pattern, $data, $matches );
		
		$teams = array ();
		for($i = 0; $i < $count; $i ++)
		{
			$teams [$i] ['name'] = $matches ['team'] [$i];
			$teams [$i] ['link'] = Functions::GetLink(array('action' => 'item', 'nr' => $matches ['nr'] [$i], 'team' => $teams[$i]['name']), true);//'http://comp.swisstennis.ch/ic2010/servlet/TeamResults?TeamId=' . $matches ['nr'] [$i] . '&Lang=D';
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'teams', $teams );
	
	}
	public function Item()
	{
		$nr = Validator::ForgeInput ( $_GET ['nr'] );
		if (! is_numeric ( $nr ))
		{
			throw new CMSException ( 'Keine g&uuml;ltige Nr.', CMSException::T_MODULEERROR );
		}
		
		$link = 'http://comp.swisstennis.ch/ic2010/servlet/TeamResults?TeamId=' . $nr . '&Lang=D';
		MySmarty::GetInstance ()->OutputModuleVar ( 'link', $link );
		//$data = file_get_contents($link);
		
	}

}
