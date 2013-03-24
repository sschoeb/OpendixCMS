<?php
/**
 * Frontpage
 *
 *
 * @package    OpendixCMS
 * @author     Stefan Sch�b <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.0
 */

/**
 * Klasse zum darstellen einer Frontpage
 *
 * @author Stefan Sch�b
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Sch�b
 * @since 1.0
 */
class OpenFrontpage extends OpenModulBase
{
	public function __construct()
	{
		parent::__construct ();
		
		if (! isset ( $_GET ['action'] ))
		{
			return;
		}
		
		if ($_GET ['action'] == 'newsletter')
		{
			if (isset ( $_POST ['action'] ) && isset ( $_POST ['email'] ))
			{
				$this->handleNewsletter ();
			}
			$this->Overview ();
		}
		
		if ($_GET ['action'] == 'getlinkedfile')
		{
			News::GetFile ();
		}
	}
	
	private function handleNewsletter()
	{
		$action = Validator::ForgeInput ( $_POST ['action'] );
		$email = Validator::ForgeInput ( $_POST ['email'] );
		
		if (! Validator::IsValidEmail ( $email ))
		{
			MySmarty::GetInstance ()->OutputWarning ( 'Keine g&uuml;ltige E-Mail Adresse angegeben!' );
			return;
		}
		
		if ($action == 1)
		{
			// E-Mail in Newsletter eintragen
			try
			{
				SqlManager::GetInstance ()->Insert ( 'modnewsletter', array ('email' => $email ) );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
			}
			MySmarty::GetInstance ()->OutputConfirmation ( 'E-Mail (' . $email . ') erfolgreich eingetragen.' );
		} else
		{
			// E-Mail aus dem Newsletter verteilen
			try
			{
				SqlManager::GetInstance ()->Delete ( 'modnewsletter', 'email=\'' . $email . '\'' );
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
			}
			MySmarty::GetInstance ()->OutputConfirmation ( 'E-Mail (' . $email . ') erfolgreich ausgetragen.' );
		
		}
	}
	
	protected function Overview()
	{
		CssImport::ImportCss ( 'index.css' );
		
		$array = array ('termine' => $this->GetNextDates (), 'news' => $this->GetNews (), 'text' => Frontpage::GetText () );
		$array ['sponsor'] = $this->GetSponsor ();
		MySmarty::GetInstance ()->OutputModuleVar ( 'front', $array );
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'link', $this->GetLinks () );
	
	}
	
	private function GetSponsor()
	{
		$sponsors = SqlManager::GetInstance ()->SelectAll ( 'modsponsor', 'frontpage=\'1\'' );
		if (count ( $sponsors ) == 0)
			return;
		
		shuffle ( $sponsors );
		
		if (! is_numeric ( $sponsors [0] ['bild'] ))
			return;
		
		$sponsors [0] ['bild'] = Filebase::GetFilePath ( $sponsors [0] ['bild'], true );
		return $sponsors [0];
	}
	
	private function GetLinks()
	{
		$arr = array ();
		$arr ['newsletter'] = Functions::GetLink ( array ('action' => 'newsletter' ), true );
		return $arr;
	}
	
	private function GetNews()
	{
		//Anzahl News die angezeigt werden sollen auslesen
		$anz = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Frontpage/newscount' );
		
		//Soltle die Anzahl 0 sein dann ein leeres Array zur�ckgeben
		if ($anz == 0)
		{
			return array ();
		}
		
		//Ansonsten die gewünschte Anzahl auslesen
		$insert = null;
		try
		{
			$insert = SqlManager::GetInstance ()->Query ( 'SELECT id, name,  text, time FROM modnews WHERE active=\'1\' AND frontpage=\'1\' ORDER BY time DESC LIMIT ' . $anz );
		} catch ( Exception $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		//Sollte die Anzahl Newseinträge grösser 0 sein dann diese im Array zur�ckgeben
		if (mysql_num_rows ( $insert ) > 0)
		{
			$out = array ();
			while ( $daten = mysql_fetch_assoc ( $insert ) )
			{
				$links = SqlManager::GetInstance ()->Select ( 'modnewslink', array ('id', 'linktype' ), 'newsId=\'' . $daten ['id'] . '\'' );
				
				if (count ( $links ) > 0)
				{
					$daten ['url'] = News::GenerateLink ( $links [0] ['linktype'], $links [0] ['id'] );
				}
				$daten ['datum'] = date ( 'd.m.Y H:i', strtotime ( $daten ['time'] ) );
				$out [] = $daten;
			}
			return $out;
		}
		
		//Ansonsten ein leeres Array zur�ckgeben
		return array ();
	}
	
	protected function GetNextDates()
	{
		//Anzahl der anzuzeigenden Daten auslesen
		$anz = Cms::GetInstance ()->GetConfiguration ()->Get ( 'Frontpage/datecount' );
		
		//Sollte die Anzahl 0 sein, so ein leeres Array zurück geben
		if ($anz == 0)
			return array ();
		
		$daten = null;
		try
		{
			$daten = SqlManager::GetInstance ()->Select ( 'modagendatermin', array ('id', 'name', 'begin' ), 'active=\'1\' AND begin>NOW()', 'begin', 'ASC', array (0, $anz ) );
		
		//$insert = SqlManager::GetInstance ()->Query ( 'SELECT id, name, begin FROM modagendatermin WHERE active=\'1\' ORDER BY begin LIMIT ' . $anz );
		} catch ( Exception $ex )
		{
			throw new CMSException ( 'Fehler bei der Abfrage der n&auml;chsten Termine!', CMSException::T_MODULEERROR, $ex );
		}
		
		for($i = 0; $i < count ( $daten ); $i ++)
		{
			$daten [$i] ['begin'] = date ( 'd.m.Y H:i', strtotime ( $daten [$i] ['begin'] ) );
			$mod = new Module ( 1 );
			$params = $mod->GetLinkParams ( 1, array ('elementId' => $daten [$i] ['id'], 'menueId' => 2 ) );
			$daten [$i] ['link'] = Functions::GetLink ( $params );
		}
		
		return $daten;
	
	}

}
