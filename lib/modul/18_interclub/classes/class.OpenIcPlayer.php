<?php

class OpenIcPlayer extends OpenModulBase
{
	public function Overview()
	{
		return;
		//$data = file_get_contents ( 'http://www.swisstennis.ch/custom/includes/public/getLizenzSpieler.cfm?action=lizenz_spieler&mitnr=1097&geschlecht=0&sortOrder=1&abfrage=1&Suchart=1' );
		$website = 'http://www.swisstennis.ch/custom/includes/public/getLizenzSpieler.cfm?action=lizenz_spieler&mitnr=1097&geschlecht=0&sortOrder=1&abfrage=1&Suchart=1';
		$data = Functions::GetWebsiteContent ( $website ); //file_get_contents($website);//
		$data = utf8_decode ( $data );
		$pattern = '/\<td\>(?P<lizenznr>[0-9]{3}\.[0-9]{2}\.[0-9]{3}\.[0-9]{1})\<\/td\>\<td\>\<ahref=".*?"target="_blank"\>(?P<name>[a-zA-ZÈ‰ˆ¸ƒ÷‹ ]*)\<\/a\>\<\/td\>\<tdalign="center"\>(?P<klass>R[0-9])\<\/td\>\<tdalign="center"\>(?P<klasswert>[0-9]\.[0-9]{3})\<\/td\>\<tdalign="center"\>(?P<klassalt>R[0-9]{1})\<\/td\>\<tdalign="center"\>(?P<klasswertalt>[0-9]\.[0-9]{3})\<\/td\>\<tdalign="center"\>(?P<ak>.*?)\<\/td\>\<tdalign="center"\>(?P<status>A|B)\<\/td\>/';
		
		$data = preg_replace ( "/\s/", '', $data );
		$matchCount = preg_match_all ( $pattern, $data, $matches );
		
		$personen = array ();
		for($i = 0; $i < $matchCount; $i ++)
		{
			$personen [$i] ['lizenznr'] = $matches ['lizenznr'] [$i];
			
			$name = preg_split ( '/(?<=\\w)(?=[A-Z])/', $matches ['name'] [$i] );
			//$name = preg_split('/[A-Z]/', $matches['name'][$i], -1, PREG_SPLIT_OFFSET_CAPTURE);
			

			$personen [$i] ['name'] = htmlentities ( join ( ' ', $name ) );
			$personen [$i] ['klass'] = $matches ['klass'] [$i];
			$personen [$i] ['klassalt'] = $matches ['klassalt'] [$i];
			$personen [$i] ['klasswert'] = $matches ['klasswert'] [$i];
			$personen [$i] ['klasswertalt'] = $matches ['klasswertalt'] [$i];
			$personen [$i] ['status'] = $matches ['status'] [$i];
			$personen [$i] ['ak'] = $matches ['ak'] [$i];
			$personen [$i] ['links'] ['item'] = 'http://www.swisstennis.ch/custom/includes/public/index.cfm?LizenzNr='.$matches['lizenznr'][$i];
//Functions::GetLink ( array ('action' => 'item', 'nr' => $matches ['lizenznr'] [$i], 'name' => $personen [$i] ['name'] ), true );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'player', $personen );
	}
	
	public function Item()
	{
		CssImport::ImportCss ( 'interclub.css' );
		
		$nr = Validator::ForgeInput ( $_GET ['nr'] );
		$link = 'http://www.swisstennis.ch/custom/includes/public/index_print.cfm?LizenzNr=' . $nr;
		$data = Functions::GetWebsiteContent ( $link ); //file_get_contents($link);//
		

		$pos = strpos ( $data, '<body onload="javascript:window.print()">' );
		$data = substr ( $data, $pos );
		$data = str_replace ( '<div style="padding:15px 15px 0px;">', '', $data );
		$data = str_replace ( '2/2010', '', $data );
		$data = str_replace ( '</div>', '', $data );
		$data = str_replace ( '<div>', '', $data );
		$data = str_replace ( '<div id="divMainContainer">', '', $data );
		$data = str_replace ( '<div id="divContainer">', '', $data );
		$data = str_replace ( '<body onload="javascript:window.print()">', '', $data );
		$data = str_replace ( '<hr>', '<p style="border:solid #dddddd 1px;"></p>', $data );
		
		//
		$data = str_replace ( '548', '650', $data );
		
		$data = preg_replace ( '/\<td\>\<h3\>.*?\<\/h3\>\<\/td\>/', '', $data );
		$data = preg_replace ( '/\<a href="(j|J)ava.*?\>([A-Za-z0-9 ]*?)<\/a\>/', '$2', $data );
		
		//MySmarty::GetInstance ()->OutputModuleVar ( 'link', $link );
		MySmarty::GetInstance ()->OutputModuleVar ( 'player', $data );
		MySmarty::GetInstance ()->OutputModuleVar ( 'link', 'http://www.swisstennis.ch/custom/includes/public/index.cfm?LizenzNr=' . $nr );
	}

}
