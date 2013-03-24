<?php
/**
 * Konfigurationsklassen
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse zum verwalten der Konfiguration
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 1.0   
 * @todo brauch tman eigentlich nicht mehr, da es eine universelle konfig-klasse gibt  
 */
class Konfiguration
{
	/**
	 * Konstruktor
	 *
	 */
	function __Construct()
	{


		if($_REQUEST['action'] == "save")
		{
			$this -> saveconfig();
			
		}
		$this -> showconfig();
	}

	/**
	 * Zeigt die gesamte Konfiguration an
	 *
	 */
	function Showconfig()
	{
		$ini = functions::parseini(true);
		if(isset($_REQUEST['action']))
		{
			//functions::output_var("seite", $this -> seite_aktuell("&action" ));
		}
		else
		{
			//functions::output_var("seite", $this -> seite_aktuell("&subId" ));
		}

		switch(functions::cleaninput($_REQUEST['subId']))
		{
			case 1:						//Seite
			if($ini['online'] == 1)
			{
				functions::output_var("online", " selected");
			}
			else
			{
				functions::output_var("offline", " selected");
			}
			functions::output_var("anzseiten", $ini['anzseiten']);
			functions::output_var("siteoffline", $ini['siteoffline']);
			functions::output_var("siteerror", $ini['siteerror']);
			functions::output_var("sitename", $ini['sitename']);
			functions::output_var("siteicon", $ini['siteicon']);
			functions::output_var("nonjavascriptError", $ini['nonjavascriptError']);
			functions::yesno("javascript", $ini['javascript']);
			functions::yesno("ModRewrite", $ini['ModRewrite']);
			functions::output_var("subId", 1);
			functions::Output_var('saveLink', functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'save', 'subId' => 1)));
			break;
			case 2:						//RSS
			functions::Output_var('saveLink', functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'save', 'subId' => 2)));
			functions::output_var("rsstitel", $ini['rss_Titel']);
			functions::output_var("rssLink", $ini['rss_Link']);
			functions::output_var("rssDescription", $ini['rss_Description']);
			functions::output_var("rssLanguage", $ini['rss_Language']);
			switch($ini['rss_Language'])
			{
				case 'de':
					functions::output_var("rsslanselect1", 'selected');
					break;
				case 'en':
					functions::output_var("rsslanselect2", 'selected');
					break;
				default:
					functions::output_var("rsslanselect1", 'selected');
					break;
			}
			functions::output_var("rssWebMaster", $ini['rss_WebMaster']);
			functions::output_var("rssdir", $ini['rss_dir']);
			if($ini['rss_backup'] == "on")
			{
				functions::output_var("rssbackupon", " checked");
			}
			else
			{
				functions::output_var("rssbackupoff", " checked");
			}
			switch($ini['rss_version'])
			{
				case 1:
					functions::output_var("rssversel1", " selected");
					break;
				case 2:
					functions::output_var("rssversel2", " selected");
					break;
				case 3:
					functions::output_var("rssversel3", " selected");
					break;
			}
			functions::output_var("subId", 2);
			break;
			case 3:						//Datenbank
			functions::Output_var('saveLink', functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'save', 'subId' => 3)));
			/*functions::output_var("dbhost", $ini['db_host']);
			functions::output_var("dbuser", $ini['db_user']);
			functions::output_var("dbpasswort", $ini['db_passwort']);
			functions::output_var("dbdatenbank", $ini['db_datenbank']);
			functions::output_var("subId", 3);*/
			break;
			case 4:						//Mail
			functions::Output_var('saveLink', functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'save', 'subId' => 4)));
			functions::output_var("subId", 4);
			switch ($ini['mail_mailer'])
			{
				case 1:
					functions::output_var("mailer1", " selected");
					break;
				case 2:
					functions::output_var("mailer2", " selected");
					break;
				case 3:
					functions::output_var("mailer3", " selected");
					break;
			}

			if($ini['mail_smtpauth'] == "on")
			{
				functions::output_var("smtpauthon", " checked");


			}
			else
			{
				functions::output_var("smtpauthoff", " checked");
			}
			functions::output_var("from", $ini['mail_from']);
			functions::output_var("fromname", $ini['mail_fromname']);
			functions::output_var("smtpuser", $ini['mail_smtpuser']);
			functions::output_var("smtppass", $ini['mail_smtppass']);
			functions::output_var("smtphost", $ini['mail_smtphost']);
			functions::output_var("smtpport", $ini['mail_smtpport']);
			functions::output_var("sendmailpath", $ini['mail_sendmailpath']);
			break;
			case 5:						//Statistik
			functions::Output_var('saveLink', functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'save', 'subId' => 5)));
			functions::output_var("subId", 5);

			if($ini['statistik'] == "on")
			{
				functions::output_var("statistikon", " checked");

			}
			else
			{
				functions::output_var("statistikoff", " checked");
			}
			break;
			case 6:						//Logg
			functions::Output_var('saveLink', functions::GetLink(array('sub' => $_GET['sub'], 'action' => 'save', 'subId' => 6)));
			functions::output_var("subId", 6);
			functions::output_var("logg_dir", $ini['Logg_dir']);
			if($ini['Logg_besucher'] == "on")
			{
				functions::output_var("logg_besucheron", " checked");

			}
			else
			{
				functions::output_var("logg_besucheroff", " checked");
			}
			if($ini['Logg_input'] == "on")
			{
				functions::output_var("logg_inputon", " checked");
			}
			else
			{
				functions::output_var("logg_inputoff", " checked");
			}
			if($ini['Logg_warnung'] == "on")
			{
				functions::output_var("logg_warnungon", " checked");
			}
			else
			{
				functions::output_var("logg_warnungoff", " checked");
			}
			if($ini['Logg_fehler'] == "on")
			{
				functions::output_var("logg_fehleron", " checked");
			}
			else
			{
				functions::output_var("logg_fehleroff", " checked");
			}
			if($ini['Logg_upload'] == "on")
			{
				functions::output_var("logg_uploadon", " checked");
			}
			else
			{
				functions::output_var("logg_uploadoff", " checked");
			}
			if($ini['Logg_download'] == "on")
			{
				functions::output_var("logg_downloadon", " checked");
			}
			else
			{
				functions::output_var("logg_downloadoff", " checked");
			}
			break;
		}



	}

	/**
	 * Speichert die Konfiguration
	 *
	 */
	function Saveconfig()
	{
		$ini = functions::parseini();
		switch(functions::cleaninput($_REQUEST['subId']))
		{
			case 1:						//Seite
			$ini['online'] 			= functions::cleaninput($_REQUEST['onoff']);
			$ini['siteoffline'] 	= functions::cleaninput($_REQUEST['siteoffline']);
			$ini['siteerror'] 		= functions::cleaninput($_REQUEST['siteerror']);
			$ini['sitename'] 		= functions::cleaninput($_REQUEST['sitename']);
			$ini['siteicon'] 		= functions::cleaninput($_REQUEST['siteicon']);
			$ini['javascript'] 		= functions::cleaninput($_REQUEST['javascript']);
			$ini['nonjavascriptError'] 		= functions::cleaninput($_REQUEST['nonjavascriptError']);
			$ini['anzseiten'] 		= functions::cleaninput($_REQUEST['anzseiten']);
			$ini['ModRewrite'] 		= functions::cleaninput($_REQUEST['ModRewrite']);
			break;
			case 2:						//RSS
			$ini['rss_Titel']		= functions::cleaninput($_REQUEST['rssTitel']);
			$ini['rss_Link']		= functions::cleaninput($_REQUEST['rssLink']);
			$ini['rss_Description']	= functions::cleaninput($_REQUEST['rssDescription']);
			$ini['rss_Language']	= functions::cleaninput($_REQUEST['rssLanguage']);
			$ini['rss_WebMaster']	= functions::cleaninput($_REQUEST['rssWebMaster']);
			$ini['rss_Docs']		= functions::cleaninput($_REQUEST['rssDocs']);
			$ini['rss_dir']			= functions::cleaninput($_REQUEST['rssdir']);
			$ini['rss_backup']		= functions::cleaninput($_REQUEST['rssbackup']);
			$ini['rss_file']		= functions::cleaninput($_REQUEST['rssfile']);
			$ini['rss_version']		= functions::cleaninput($_REQUEST['rssVersion']);
			break;
			case 3:						//Datenbank
			/*$ini['db_host']			= functions::cleaninput($_REQUEST['dbhost']);
			$ini['db_user']			= functions::cleaninput($_REQUEST['dbuser']);
			$ini['db_passwort']		= functions::cleaninput($_REQUEST['dbpasswort']);
			$ini['db_datenbank']	= functions::cleaninput($_REQUEST['dbdatenbank']);*/
			break;
			case 4:						//Mail
			$ini['mail_mailer']		= functions::cleaninput($_REQUEST['mailer']);
			$ini['mail_from']		= functions::cleaninput($_REQUEST['from']);
			$ini['mail_fromname']	= functions::cleaninput($_REQUEST['fromname']);
			$ini['mail_smtpauth']	= functions::cleaninput($_REQUEST['smtpauth']);
			$ini['mail_smtpuser']	= functions::cleaninput($_REQUEST['smtpuser']);
			$ini['mail_smtppass']	= functions::cleaninput($_REQUEST['smtppass']);
			$ini['mail_smtphost']	= functions::cleaninput($_REQUEST['smtphost']);
			$ini['mail_smtpport']	= functions::cleaninput($_REQUEST['smtpport']);
			$ini['mail_sendmailpath']	= functions::cleaninput($_REQUEST['sendmailpath']);
			break;
			case 5:						//Statistik
			$ini['statistik'] 			= functions::cleaninput($_REQUEST['statistik']);
			break;
			case 6:						//Logg
			$ini['Logg_besucher'] 		= functions::cleaninput($_REQUEST['logg_besucher']);
			$ini['Logg_input'] 			= functions::cleaninput($_REQUEST['logg_input']);
			$ini['Logg_fehler'] 		= functions::cleaninput($_REQUEST['logg_fehler']);
			$ini['Logg_warnung'] 		= functions::cleaninput($_REQUEST['logg_warnung']);
			$ini['Logg_upload'] 		= functions::cleaninput($_REQUEST['logg_upload']);
			$ini['Logg_download'] 		= functions::cleaninput($_REQUEST['logg_download']);
			$ini['Logg_dir'] 			= functions::cleaninput($_REQUEST['logg_dir']);
			break;
		}



		if(functions::write_ini_file($ini) != true)
		{
			functions::output_fehler('Die Konfiguration konnte nicht in die config.ini eingschrieben werden!');
		}
	}



}

?>