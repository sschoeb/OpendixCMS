<?php
/**
 * Gästebuch
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse die den öffenltichen Zugriff auf das Gästebuch sicherstellt
 * 
 * @author 	Stefan Schöb
 * @package OpendixCMS
 * @version 1.1
 * @copyright 2006-2009, Stefan Schöb
 * @since 1.0     
 */
class Opengbook extends OpenModulBase
{
	/**
	 * Konstruktor
	 *
	 */
	public function __construct()
	{
		
		CssImport::ImportCss ( 'gbook.css' );
		
		
		parent::__construct ();
		
		if (! isset ( $_GET ['action'] ))
		{
			return;
		}
		
		switch ($_GET ['action'])
		{
			case 'addEntry' :
				$this->Add ();
				$this->Overview ();
				break;
		}
	}
	
	
	/**
	 * Gibt die übergebenen Parameter als Startwerde in dem Formular aus (z.B. wenn ein Fehler bei der Eingabe vorkam muss man so nicht alles nochmal schreiben...)
	 *
	 * @param String $name
	 * @param String $email
	 * @param String $text
	 */
	private function OutputData($name, $email, $text)
	{
		$data = array ('name' => $name, 'email' => $email, 'text' => $text );
		MySmarty::GetInstance ()->OutputModuleVar ( 'gbook', $data );
	}
	
	/**
	 * Fügt einen Eintrag zu dem G�stebuch hinzu
	 *
	 * @return boolean
	 */
	protected function Add()
	{
		//Prüfen ob das Captcha korrekt ausgefüllt wurde
		/*if(!Functions::validate_captcha($_POST['captcha'], $_POST['securekey']))
		{
			$this -> OutputData($_POST['name'], $_POST['email'],  $_POST['homepage'],$_POST['text']);
			MySmarty::GetInstance() -> OutputWarning('Der Sicherheits-Code wurde nicht korrekt eingegeben!');
			return false;		
		}*/
		
		if (! $_POST ['captcha'] == '8')
		{
			$this->OutputData ( $_POST ['name'], $_POST ['email'], $_POST ['nachricht'] );
			MySmarty::GetInstance ()->OutputWarning ( 'Der Sicherheits-Code wurde nicht korrekt eingegeben!' );
			return false;
		}
		
		
		$lasttime = Cms::GetInstance ()->GetModule ()->GetConfiguration ('General/timebetweenpost' );
		$maxlen = Cms::GetInstance ()->GetModule ()->GetConfiguration ( 'General/maxentrylength' );
		
		
		$ip = $_SERVER ['REMOTE_ADDR'];
		$date = time ();
		
		//abfrage ob diese IP nicht gerade erst gepostet hat (Spam-Zeit-Sperre)
		$lastpost = 0;
		try
		{
			$lastpost = SqlManager::GetInstance ()->SelectItem ( 'modgbook', 'date', 'ip = \'' . $ip . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Fehler bei der Abfrage des letzten Posts', CMSException::T_MODULEERROR, $ex );
		}
		if (($lastpost + $lasttime) > $date)
		{
			MySmarty::GetInstance ()->OutputWarning ( 'Sie m&uuml;ssen min. ' . $lasttime . ' Sekunden warten bis Sie sich wieder eintragen k&ouml;nnen!' );
			$this->OutputData ( $_POST ['name'], $_POST ['email'], $_POST ['nachricht'] );
			return false;
		}
		
		$name = Validator::ForgeInput ( Functions::badword ( $_REQUEST ['name'] ) );
		//$homepage 	= functions::formatUrl(Validator::ForgeInput(functions::badword($_REQUEST['homepage'])));
		$email = Validator::ForgeInput ( Functions::badword ( $_REQUEST ['email'] ) );
		$eintrag = $this->formatText ( Validator::ForgeInput ( Functions::badword ( $_REQUEST ['nachricht'] ) ) );
		
		if (strlen ( $name ) < 3)
		{
			$this->OutputEntryMisstage ( 'Ich glaube ja kaum, dass Sie einen Namen mit gerademal zwei Buchstaben haben... ' );
			return false;
		}
		
		if (! Validator::IsValidEmail ( $email ))
		{
			$this->OutputEntryMisstage ( 'Ihr Eintrag wurde nicht gespeichert! Sie haben keine korrekte E-Mail angegeben!' );
			return false;
		}
		
		if ($maxlen != 0 && strlen ( $eintrag ) > $maxlen)
		{
			$this->OutputEntryMisstage ( 'Ihr Eintrag wurde nicht gespeichert da er zu lang ist! Sie d&uuml;rfen max. ' . $maxlen . ' Zeichen werdenden' );
			return false;
		}
		
		if (strpos ( $_POST ['nachricht'], '[URL' ) !== false || strpos ( $_POST ['nachricht'], '[url' ) !== false)
		{
			$this->OutputEntryMisstage ( '[URL darf infolge von SPAM-Bots nicht in dem Betrag vorkommen!' );
			return false;
		}
		
		
		if ($eintrag == '' || $name == '')
		{
			$this->OutputEntryMisstage ( 'Es wurde nicht alle ben&ouml;tigen Felder korrekt ausgef&uuml;llt! Bitte &uuml;berpr&uuml;fen Sie die Eingabe!' );
			return false;
		}
		
		try
		{
			SqlManager::GetInstance ()->Insert ( 'modgbook', array ('ip' => $ip, 'name' => $name, 'mail' => $email, 'eintrag' => $eintrag, 'date' => $date ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Eintrag konnte nicht gespeichert werden!', CMSException::T_MODULEERROR, $ex );
		}
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Ihr Eintrag wurde erfolgreich in das G&auml;stebuch hinzugef&uuml;gt' );
		return true;
	}
	
	private function OutputEntryMisstage($error)
	{
		MySmarty::GetInstance ()->OutputWarning ( $error );
		$this->OutputData ( $_POST ['name'], $_POST ['email'], $_POST ['nachricht'] );
	}
	
	/**
	 * Zeigt das Gästebuch an
	 *
	 */
	protected function Overview()
	{
		//Javascript importieren welches benötigt wird um Smilis automatisch einzufügen
		JsImport::ImportModuleJS ( 'gbook', 'gb.js' );
		
		//Abfragen aller Eintäge aus der Datenbank
		$daten = array ();
		try
		{
			
			$daten = SqlManager::GetInstance ()->SelectWithScrollMenue ( 'modgbook', array ('id', 'name', 'mail', 'homepage', 'eintrag', 'date' ), '', 'date', 'DESC' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Fehler beim Abfragen der Eintr&auml;ge!', CMSException::T_MODULEERROR, $ex );
		}
		
		$c = count ( $daten ['daten'] );
		for($i = 0; $i < $c; $i ++)
		{
			$daten ['daten'] [$i] ['text'] =  Functions::DecodeText( nl2br ( $daten ['daten'] [$i] ['eintrag'] ), false);
			$daten ['daten'] [$i] ['date'] = date ( "d.m.Y - H:i:s", $daten ['daten'] [$i] ['date'] );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'gbook', $daten );
		MySmarty::GetInstance ()->OutputModuleVar ( 'addLink', Functions::GetLink ( array ('action' => 'addEntry' ), true ) );
	
	}
	
	/**
	 * Formatiert den eingegeben Text
	 * 
	 * Wörter die länger als 80 Zeigen sind werden mit einem - getrennt
	 *
	 * @param String $text
	 * @return String
	 */
	private function FormatText($text)
	{
		$words = explode ( ' ', $text );
		$count = count ( $words );
		for($i = 0; $i < $count; $i ++)
		{
			if (strlen ( $words [$i] ) > 80)
			{
				$words [$i] = wordwrap ( $words [$i], 50, '-', true );
			}
		}
		$text = implode ( ' ', $words );
		return htmlspecialchars($text,ENT_QUOTES, 'UTF-8' );
	}
}
