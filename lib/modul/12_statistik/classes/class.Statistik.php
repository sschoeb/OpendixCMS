<?php
/**
 * Statistik
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Sch�b <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.0
 */

/**
 * Statistik anzeigen
 * 
 * @author Stefan Sch�b
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Sch�b
 * @since 1.0     
 */
class Statistik
{
	function statistik()
	{
		$von = functions::cleaninput($_REQUEST['von']);
		$bis = functions::cleaninput($_REQUEST['bis']);
		if($von == '')
		{
			$von = 0;
		}
		if($bis == '')
		{
			$bis = 99999999999999999;
		}

		$ini = functions::parseini();

		$this -> maxbreite 	= $ini['statistik_maxbreite'];
		$this -> maxhoehe 	= $ini['statistik_maxhoehe'];
		$this -> statistik 	= array();


		$this -> create($von, $bis);		//Daten aus der Datenbank holen
		$this -> show_allgemein();			//Allgemeine Statistiken
		$this -> show_wochentag();			//Besucher pro wochentag
		$this -> show_browser();			//Browser
		$this -> show_uhrzeit();			//Besucher pro Urhzeit
		$this -> show_aufloesung();			//Aufl�sung
		$this -> show_farbtiefe();			//Farbtiefe
		$this -> show_host();				//Host
		$this -> show_herkunft();			//Herkunft
		$this -> show_referer();			//Referer
		$this -> show_betriebssystem();		//Betriebssystem
	}

	/**
	 * Erstellt die Statistik
	 *
	 * @param unknown_type $von
	 * @param unknown_type $bis
	 * @return unknown
	 * 
	 * @todo Speed
	 */
	function create($von, $bis)
	{
		//Array statistik initialisieren!
		$tage		= array();
		$stunden	= array();

		$i=0;

		$query = "SELECT * FROM modstatistik WHERE time > '$von' AND time < '$bis'";		//Alle statistik-daten von einer gewissen zeitspanne auslesen
		$insert = mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 234');
		while($daten = mysql_fetch_assoc($insert))
		{

			//Anzahl Tage
			$datum = date("dmY", $daten['time']);
			if(array_search($datum, $tage) === false)
			{
				$this -> statistik['anz_tage']++;
				$tage[] = date("dmY", $daten['time']);
			}

			//Anzahl Stunden
			/*$datum = date("dmYG", $daten['time']);
			if(array_search($datum, $stunden) === false)
			{
			$this -> statistik['anz_stunden']++;
			$stunden[] = date("dmYG", $daten['time']);
			}*/

			switch(date("l", $daten['time']))
			{
				case 'Monday':
					$this -> statistik['tag_montag']++;
					break;
				case 'Tuesday':
					$this -> statistik['tag_dienstag']++;
					break;
				case 'Wednesday':
					$this -> statistik['tag_mittwoch']++;
					break;
				case 'Thursday':
					$this -> statistik['tag_donnerstag']++;
					break;
				case 'Friday':
					$this -> statistik['tag_freitag']++;
					break;
				case 'Saturday':
					$this -> statistik['tag_samstag']++;
					break;
				case 'Sunday':
					$this -> statistik['tag_sonntag']++;
					break;
			}

			//Uhrzeit
			$uhrzeit = date("G", $daten['time']);
			$this -> statistik['clock_' . $uhrzeit]++;

			//Browser
			if (preg_match("=MSIE [0-9]{1,2}.[0-9]{1,2}.*Opera.([0-9]{1})=", $daten['agent']))
			{
				$this -> statistik['browser_opera']++;
			}
			elseif (preg_match("=MSIE ([0-9]{1,2}).[0-9]{1,2}=", $daten['agent']))
			{
				$this -> statistik['browser_ie']++;
			}
			elseif (preg_match("=Opera/([0-9]{1,2}).[0-9]{1,2}=", $daten['agent']))
			{
				$this -> statistik['browser_opera']++;
			}
			elseif (preg_match("=Konqueror=", $daten['agent']))
			{
				$this -> statistik['browser_konqueror']++;
			}
			elseif (preg_match("=Safari=", $daten['agent']))
			{
				$this -> statistik['browser_safari']++;
			}
			elseif (preg_match("=Netscape/7.[0-9]{1,2}=", $daten['agent']))
			{
				$this -> statistik['browser_netscape']++;
			}
			elseif (preg_match("=Mozilla/5.[0-9]{1,2}.*Firefox=", $daten['agent']))
			{
				$this -> statistik['browser_firefox']++;
			}
			elseif (preg_match("=Mozilla/5.[0-9]{1,2}=", $daten['agent']))
			{
				$this -> statistik['browser_firefox']++;
			}
			elseif (preg_match("=Mozilla/([0-9]{1,2}).[0-9]{1,2}=", $daten['agent']))
			{
				$this -> statistik['browser_firefox']++;
			}
			elseif ($this -> isSearchEngine($daten['agent']))
			{
				//Suchmaschiene ignorieren
			}
			else
			{
				$this -> statistik['browser_unbekannt']++;
			}

			//Betriebssystem
			if (preg_match("=Windows NT 5\.0|Windows 2000=", $daten['agent']))
			{
				$this -> statistik['bs_windows2000']++;
			}
			elseif (preg_match("=Windows NT 5\.1|Windows XP=", $daten['agent']))
			{
				$this -> statistik['bs_windowsxp']++;
			}
			elseif (preg_match("=Windows NT 4\.0|Windows NT|WinNT4\.0=", $daten['agent']))
			{
				$this -> statistik['bs_windowsNT']++;
			}
			elseif (preg_match("=Win98=", $daten['agent']))
			{
				$this -> statistik['bs_windows98']++;
			}
			elseif (preg_match("=Windows 98=", $daten['agent']))
			{
				$this -> statistik['bs_windows98']++;
			}
			elseif (preg_match("=Windows 95=", $daten['agent']))
			{
				$this -> statistik['bs_windows95']++;
			}
			elseif (preg_match("=Mac_PowerPC|Macintosh=", $daten['agent']))
			{
				$this -> statistik['bs_mac']++;
			}
			elseif (preg_match("=Linux=", $daten['agent']))
			{
				$this -> statistik['bs_linux']++;
			}
			else
			{
				if(!$this -> isSearchEngine($daten['agent'])){
					$this -> statistik['bs_searchEngine']++;
					
				}
				
			}

			//Auflösung
			switch($daten['aufloesung'])
			{
				case '800x600':
					$this -> statistik['aufloesung_800x600']++;
					break;
				case '1280x1024':
					$this -> statistik['aufloesung_1280x1024']++;
					break;
				case '1024x768';
				$this -> statistik['aufloesung_1024x768']++;
				break;
				case '1600x1200':
					$this -> statistik['aufloesung_1600x1200']++;
					break;
				default:
					$this -> statistik['aufloesung_unbekannt']++;
					break;
			}

			//Farbtiefe
			switch($daten['farbtiefe'])
			{
				case '32':
					$this -> statistik['color_32']++;
					break;
				case '16':
					$this -> statistik['color_16']++;
					break;
				case '8';
				$this -> statistik['color_8']++;
				break;
				case '1':
					$this -> statistik['color_1']++;
					break;
				default:
					$this -> statistik['color_unbekannt']++;
					break;
			}


			//Herkunft
			/*if (preg_match("=^.*\.([\_\-a-zA-Z0-9]*)\.([a-zA-Z]{2,4})=", $host, $hosts))
			{
				// Hosts
				$this -> statistik['host_' . $i . '_name'] = strtolower($hosts[1].".".$hosts[2]);
				$this -> statistik['host_' . $i . '_count']++;
				// L�nder
				$this -> statistik['land_' . $i . '_name'] = strtolower($hosts[2]);
				$this -> statistik['land_' . $i . '_count']++;
			}
			else
			{
				// Hosts
				$this -> statistik['host_unbekannt']++;
				// L�nder
				$this -> statistik['land_unbekannt']++;
			}*/
			$i++;
		}
		$this -> statistik['anz_user'] 		= mysql_num_rows($insert);
		$this -> statistik['anz_userDay'] 	= @round($this -> statistik['anz_user'] / $this -> statistik['anz_tage'], 1);
		//$this -> statistik['anz_userHour'] 	= @round($this -> statistik['anz_user'] / $this -> statistik['anz_stunden'], 3);
		return $this -> statistik;
	}

	//
	// Funktion zum Anzeigen der statistik
	//

	function show_allgemein()
	{
		$allgemein = array();

		$allgemein['tage'] 		= $this -> statistik['anz_tage'];
		$allgemein['total'] 	= $this -> statistik['anz_user'];
		$allgemein['userDay'] 	= $this -> statistik['anz_userDay'];
		$allgemein['userHour'] 	= $this -> statistik['anz_userHour'];

		functions::output_var('allgemein', $allgemein);
	}

	function show_browser()
	{
		$temp 	= array();

		$temp ['opera'] = $this -> statistik['browser_opera'];
		$temp ['ie'] = $this -> statistik['browser_ie'];
		$temp ['netscape'] = $this -> statistik['browser_netscape'];
		$temp ['konqueror'] = $this -> statistik['browser_konqueror'];
		$temp ['firefox'] = $this -> statistik['browser_firefox'];
		$temp ['safari'] = $this -> statistik['browser_safari'];
		$temp ['unbekannt'] = $this -> statistik['browser_unbekannt'];

		arsort($temp);

		$i=0;
		foreach ($temp as $key => $wert)
		{
			if($i>4 || $temp[$key] == 0)
			{
				break;
			}
			switch($key)
			{
				case 'opera':
					$browser[$i]['name'] 	= 'Opera';
					break;
				case 'ie':
					$browser[$i]['name'] = 'Internet Explorer';
					break;
				case 'netscape':
					$browser[$i]['name'] = 'Netscape';
					break;
				case 'konqueror':
					$browser[$i]['name'] = 'Konqueror';
					break;
				case 'firefox':
					$browser[$i]['name'] = 'Firefox';
					break;
				case 'safari':
					$browser[$i]['name'] = 'Safari';
					break;
				default:
					$browser[$i]['name'] = 'unbekannt';
					break;
			}
			$browser[$i]['anz'] 	= $temp[$key];
			$prozent = $this -> calcPercent($this -> statistik['anz_user'], $temp[$key]);
			$browser[$i]['prozent'] = $prozent .  ' %';
			$browser[$i]['width'] = $this -> calcPercentWert($this -> maxbreite, $prozent);
			$i++;


		}

		functions::output_var('browser', $browser);
	}

	/**
	 * Pr�ft ob es sich beim Agenten um eine Suchmaschiene handelt
	 *
	 * @param unknown_type $data
	 */
	function isSearchEngine($data)
	{
		$engines = array('msnbot', 'Googlebot', 'Yahoo! Slurp', 'Gigabot', 'Google-Sitemaps', 'ia_archiver', 'Exabot', 'ichiro', 'LinkWalker');
		for($i=0; $i<count($engines); $i++){
			if(!strstr($data, $engines[$i])){
				continue;
			}
			return true;
		}
		return  false;
	}
	
	function show_herkunft()
	{

	}

	function show_host()
	{

	}

	function show_betriebssystem()
	{
		$temp 	= array();

		$temp ['winxp'] = $this -> statistik['bs_windowsxp'];
		$temp ['win2000'] = $this -> statistik['bs_windows2000'];
		$temp ['winnt'] = $this -> statistik['bs_windowsnt'];
		$temp ['win98'] = $this -> statistik['bs_windows98'];
		$temp ['win95'] = $this -> statistik['bs_windows95'];
		$temp ['mac'] = $this -> statistik['bs_mac'];
		$temp ['linux'] = $this -> statistik['bs_linux'];
		$temp ['unbekannt'] = $this -> statistik['bs_unbekannt'];
		$temp ['searchEngine'] = $this -> statistik['bs_searchEngine'];

		arsort($temp);

		$i=0;
		foreach ($temp as $key => $wert)
		{
			if($i>4 || $temp[$key] == 0)
			{
				break;
			}
			switch(strtolower($key))
			{
				case 'winxp':
					$bs[$i]['name'] = 'Windows XP';
					break;
				case 'win2000':
					$bs[$i]['name'] = 'Windows 2000';
					break;
				case 'winnt':
					$bs[$i]['name'] = 'Windows NT';
					break;
				case 'win98':
					$bs[$i]['name'] = 'Windows 98';
					break;
				case 'win95':
					$bs[$i]['name'] = 'Windows 95';
					break;
				case 'mac':
					$bs[$i]['name'] = 'Macintosh';
					break;
				case 'linux':
					$bs[$i]['name'] = 'Linux';
					break;
				case 'searchEngine':
					$bs[$i]['name'] = 'Suchmaschiene';
					break;
				default:
					$bs[$i]['name'] = 'unbekannt';
					break;
			}
			$bs[$i]['anz'] 	= $temp[$key];
			$prozent = $this -> calcPercent($this -> statistik['anz_user'], $temp[$key]);
			$bs[$i]['prozent'] = $prozent .  ' %';
			$bs[$i]['width'] = $this -> calcPercentWert($this -> maxbreite, $prozent);
			$i++;


		}

		functions::output_var('bs', $bs);
	}

	function show_farbtiefe()
	{
		$temp 	= array();

		$temp ['32'] 	= $this -> statistik['color_32'];
		$temp ['16'] 	= $this -> statistik['color_16'];
		$temp ['8'] 	= $this -> statistik['color_8'];
		$temp ['1'] 	= $this -> statistik['color_1'];
		$temp ['unbekannt'] = $this -> statistik['color_unbekannt'];

		arsort($temp);

		$i=0;
		foreach ($temp as $key => $wert)
		{
			if($i>4 || $temp[$key] == 0)
			{
				break;
			}
			switch($key)
			{
				case '32':
					$color[$i]['name'] = '32 Bit';
					break;
				case '16':
					$color[$i]['name'] = '16 Bit';
					break;
				case '8':
					$color[$i]['name'] = '8 Bit';
					break;
				case '1':
					$color[$i]['name'] = '1 Bit';
					break;
				default:
					$color[$i]['name'] = 'unbekannt';
					break;
			}
			$color[$i]['anz'] 	= $temp[$key];
			$prozent = $this -> calcPercent($this -> statistik['anz_user'], $temp[$key]);
			$color[$i]['prozent'] = $prozent .  ' %';
			$color[$i]['width'] = $this -> calcPercentWert($this -> maxbreite, $prozent);
			$i++;


		}

		functions::output_var('color', $color);
	}

	function show_aufloesung()
	{
		$temp 	= array();

		$temp ['800x600'] = $this -> statistik['aufloesung_800x600'];
		$temp ['1024x768'] = $this -> statistik['aufloesung_1024x768'];
		$temp ['1280x1024'] = $this -> statistik['aufloesung_1280x1024'];
		$temp ['1600x1400'] = $this -> statistik['aufloesung_1600x1400'];
		$temp ['unbekannt'] = $this -> statistik['aufloesung_unbekannt'];

		arsort($temp);

		$i=0;
		foreach ($temp as $key => $wert)
		{
			if($i>4 || $temp[$key] == 0)
			{
				break;
			}
			$auf[$i]['name'] 	= $key;
			$auf[$i]['anz'] 	= $temp[$key];
			$prozent 			= $this -> calcPercent($this -> statistik['anz_user'], $temp[$key]);
			$auf[$i]['prozent'] = $prozent .  ' %';
			$auf[$i]['width'] 	= $this -> calcPercentWert($this -> maxbreite, $prozent);
			$i++;


		}
		functions::output_var('auf', $auf);
	}

	function show_referer()
	{

	}

	function show_wochentag()
	{
		$userPerDay = array();
		$tage		= array('montag','dienstag','mittwoch','donnerstag','freitag','samstag','sonntag');

		for($i=0; $i<7; $i++)
		{
			$prozent = $this -> calcPercent($this -> statistik['anz_user'], $this -> statistik['tag_' . $tage[$i]]);
			$userPerDay[$tage[$i] . '_pro'] = $prozent . ' %';
			$userPerDay[$tage[$i] . '_prowert'] = $this -> calcPercentWert($this -> maxbreite, $prozent);
			$userPerDay[$tage[$i]] = $this -> statistik['tag_' . $tage[$i]];
		}
		functions::output_var('userperDay', $userPerDay);
	}

	function show_uhrzeit()
	{
		$userPerHour = array();
		for($i=0; $i<24; $i++)
		{
			$prozent = $this -> calcPercent($this -> statistik['anz_user'], $this -> statistik['clock_' . $i]);
			$userPerHour['clock_' . $i . '_hoehe'] = $this -> calcPercentWert($this -> maxhoehe, $prozent);
			$userPerHour['clock_' . $i] = $this -> statistik['clock_' . $i];
			if($userPerHour['clock_' . $i] == '')
			{
				$userPerHour['clock_' . $i] = 0;
			}
		}

		//Pr�fen welches am h�chsten ist
		$count = count($userPerHour);
		$temp = array();
		for($i=0; $count>$i; $i++)
		{
			if($userPerHour['clock_' . $i . '_hoehe'] > $temp['value'])
			{
				$temp['value'] = $userPerHour['clock_' . $i . '_hoehe'];
				$temp['id'] = $i;
			}
		}

		//$temp['value'] enstrpciht nun dem Wert der am h�chsten ist
		for($i=0; $i<$count; $i++)
		{
			$userPerHour['clock_' . $i . '_hoehe'] = $this -> calcPercent($temp['value'], $userPerHour['clock_' . $i . '_hoehe']);
		}

		$userPerHour['maxhoehe'] = $this -> maxhoehe - 30;
		functions::output_var('userPerHour', $userPerHour);
	}

	function calcPercent($max, $teil)
	{
		return round(100 / $max * $teil, 1);
	}

	function calcPercentWert($max, $prozent)
	{
		return round($max / 100 * $prozent, 1);
	}
}

/**
 * Klasse f�r die �ffenltiche statistik
 * 
 * @author Stefan Sch�b
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Sch�b
 * @since 1.0     
 */
class Openstatistik extends statistik
{
	function openstatistik()
	{
		$this -> statistik();
	}
	function statistik()
	{

	}
}
?>