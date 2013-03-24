<?php

/**
 * Personenverwaltung
 *
 *
 * @package    OpendixCMS
 * @author     Stefan SchÃ¶b <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse zum verwalten der Personen-Gruppen
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 	1.0
 */
class PersonGroup extends ModulBase
{
	public function __construct()
	{
		parent::__construct ();
		
		if (! isset ( $_GET ['action'] ))
			return;
		
		switch ($_GET ['action'])
		{
			case 'up' :
				$this->UserUp ();
				$this->Overview ();
				break;
			case 'down' :
				$this->UserDown ();
				$this->Overview ();
				break;
			case 'delLink' :
				$this->DelLink ();
				$this->Overview ();
				break;
			case 'addLink' :
				$this->AddLink ();
				$this->Overview ();
				break;
		}
	}
	
	private function DelLink()
	{
		$id = Validator::ForgeInputNumber ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'ID nicht numerisch' );
		}
		
		try
		{
			SqlManager::GetInstance ()->DeleteById ( 'modpersoningroup', $id );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Verkn&uuml;pfung erfolgreich gel&ouml;scht!' );
	
	}
	
	private function AddLink()
	{
		
		$userId = Validator::ForgeInput ( $_POST ['userId'] );
		$groupId = Validator::ForgeInput ( $_POST ['groupId'] );
		$funktion = Validator::ForgeInput ( $_POST ['funktion'] );
		
		$data = array ('userid' => $userId, 'groupid' => $groupId, 'funktion' => $funktion );
		try
		{
			$data ['folge'] = SqlManager::GetInstance ()->SelectMax ( 'modpersoningroup', 'folge', 'groupId=' . $groupId ) + 1;
			SqlManager::GetInstance ()->Insert ( 'modpersoningroup', $data );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Verkn&uuml;pfung erfolgreich hinzugef&uuml;gt' );
	}
	
	protected function Item()
	{
		$this->Overview ();
	}
	
	/**
	 * Fügt eine Gruppe hinzu
	 *
	 */
	protected function Add()
	{
		//Namen auslesen
		$name = Validator::ForgeInput ( $_POST ['groupname'] );
		
		//Prüfen ob der Name 3 Zeichen lang ist
		if (strlen ( $name ) < 3)
		{
			MySmarty::GetInstance ()->OutputWarning ( 'Gruppe wurde nicht hinzugef&uuml;gt! Name zu kurz!' );
			return;
		}
		
		//Prüfen ob der Name nicht bereits existiert
		try
		{
			$anz = SqlManager::GetInstance ()->Count ( 'modpersongruppe', 'name=\'' . $name . '\'' );
			if ($anz != 0)
			{
				MySmarty::GetInstance ()->OutputError ( 'Es besteht bereits eine Gruppe mit diesem Namen!' );
				return;
			}
			
			//Namen einfügen
			SqlManager::GetInstance ()->Insert ( 'modpersongruppe', array ('name' => $name ) );
		
		} catch ( Exception $ex )
		{
			throw new CMSException ( 'sqlfehler', CMSException::T_MODULEERROR, $ex );
		}
		
		MySmarty::GetInstance ()->OutputConfirmation ( 'Gruppe wurde erfolgreich hinzugef&uuml;gt!' );
	}
	
	/**
	 * Entfernt eine Gruppe
	 *
	 */
	protected function Delete()
	{
		$id = Validator::ForgeInput ( $_GET ['id'] );
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Keine g&uuml;ltige ID angegeben!', CMSException::T_MODULEERROR );
		}
		try
		{
			$todelete = SqlManager::GetInstance ()->Select ( 'modpersoningroup', array ('id' ), 'groupid=\'' . $id . '\'' );
			
			SqlManager::GetInstance ()->Delete ( 'modpersoningroup', 'groupid=\'' . $id . '\'' );
			SqlManager::GetInstance ()->DeleteById ( 'modpersongruppe', $id );
			
		} catch ( Exception $ex )
		{
			throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		MySmarty::GetInstance() -> OutputConfirmation('Gruppe erfolgreich gel&ouml;scht');
	}
	
	/**
	 * Erhöht den User um eins in der Reihenfolge
	 *
	 */
	public function UserUp()
	{
		$this->SwitchPosition ( - 1 );
	}
	
	/**
	 * Stellt den Benutzer in der Reihenfolge eins zurück
	 *
	 */
	Public function UserDown()
	{
		$this->SwitchPosition ( 1 );
	}
	
	/**
	 * Ändert die Position eines Benutzers	
	 *
	 * @param int $newPosFix	-1 heisst der User wird um eine Position nach oben verschoeb!
	 * 1 heisst der User wird um eine Position nach unten verschoeb
	 */
	private function SwitchPosition($newPosFix)
	{
		//ID's aus der URL auslesen
		$userId = Validator::ForgeInput ( $_GET ['userid'] );
		$groupId = Validator::ForgeInput ( $_GET ['groupid'] );
		
		//Prüfen ob es sich um Integer-Werte handelt
		if (! is_numeric ( $userId ) || ! is_numeric ( $groupId ))
		{
			throw new CMSException ( 'Keine g&uuml;ltige ID angegeben!', CMSException::T_MODULEERROR );
		
		}
		
		//Aktuelle Position des Benutzers in der angegebenen Gruppe auslesen
		try
		{
			$position = SqlManager::GetInstance ()->SelectItem ( 'modpersoningroup', 'folge', 'userid=\'' . $userId . '\' AND groupid=\'' . $groupId . '\'' );
			
			$newposition = $position + $newPosFix;
			
			if ($newposition < 0)
			{
				MySmarty::GetInstance ()->OutputConfirmation ( 'Der Benutzer befindet sich bereits an der Spitze!' );
				return;
			}
			
			//Den User um eins zurückstufen der gerade vor dem zu erhebenden User ist
			SqlManager::GetInstance ()->Update ( 'modpersoningroup', array ('folge' => $position ), 'groupid=\'' . $groupId . '\' AND folge=\'' . $newposition . '\'' );
			
			//Den anderen User erhöhen
			SqlManager::GetInstance ()->Update ( 'modpersoningroup', array ('folge' => $newposition ), 'userid=\'' . $userId . '\' AND groupid=\'' . $groupId . '\'' );
		
		} catch ( Exception $ex )
		{
			throw new CMSException ( 'SQL Fehler', CMSException::T_MODULEERROR, $ex );
		}
	}
	
	/**
	 * Zeigt sämtliche Gruppen an
	 *
	 */
	Public function Overview()
	{
		CssImport::ImportCss ( 'content.css' );
		
		$user = HTML::Select ( 'sysuser', array('name', 'firstname'), '', 'name' );
		MySmarty::GetInstance ()->OutputModuleVar ( 'newformUser', $user );
		$groups = HTML::Select ( 'modpersongruppe', 'name' );
		MySmarty::GetInstance ()->OutputModuleVar ( 'newformGroups', $groups );
		MySmarty::GetInstance ()->OutputModuleVar ( 'newformAddLink', Functions::GetLink ( array ('action' => 'addLink' ), true ) );
		
		try
		{
			$lastgroupname = '';
			$output = array ();
			$insert = SqlManager::Query ( 'SELECT g.name AS groupname, pig.funktion, firstname,  g.id AS groupid, p.name AS personname, pig.id as connId, p.id AS personid FROM sysuser p,  modpersongruppe g, modpersoningroup pig WHERE p.id = pig.userid AND g.id = pig.groupid ORDER BY g.id, pig.folge' );
			while ( $daten = mysql_fetch_assoc ( $insert ) )
			{
				if ($lastgroupname != $daten ['groupname'])
				{
					if (isset ( $lastgroupid ))
					{
						$output [count ( $output ) - 1] ['user'] [count ( $output [count ( $output ) - 1] ['user'] ) - 1] ['last'] = true;
					}
					
					$output [] = array ();
					$arpos = count ( $output ) - 1;
					$output [$arpos] = array ();
					$output [$arpos] ['user'] = array ();
					$output [$arpos] ['group'] ['name'] = $daten ['groupname'];
					$output [$arpos] ['group'] ['dellink'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'delete', 'id' => $daten ['groupid'] ) );
					$lastgroupname = $daten ['groupname'];
					$lastgroupid = $daten ['groupid'];
				}
				$output [count ( $output ) - 1] ['user'] [] ['name'] = $daten ['personname'];
				
				if (count ( $output [count ( $output ) - 1] ['user'] ) == 1)
				{
					$output [count ( $output ) - 1] ['user'] [count ( $output [count ( $output ) - 1] ['user'] ) - 1] ['first'] = true;
				}
				
				$lastCount = count ( $output ) - 1;
				$output [$lastCount] ['user'] [count ( $output [$lastCount] ['user'] ) - 1] ['firstname'] = $daten ['firstname'];
				$output [$lastCount] ['user'] [count ( $output [$lastCount] ['user'] ) - 1] ['funktion'] = $daten ['funktion'];
				$output [$lastCount] ['user'] [count ( $output [$lastCount] ['user'] ) - 1] ['id'] = $daten ['personid'];
				$output [$lastCount] ['user'] [count ( $output [$lastCount] ['user'] ) - 1] ['link'] ['up'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'up', 'groupid' => $daten ['groupid'], 'userid' => $daten ['personid'] ) );
				$output [$lastCount] ['user'] [count ( $output [$lastCount] ['user'] ) - 1] ['link'] ['delete'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'delLink', 'id' => $daten ['connId'] ) );
				
				$output [$lastCount] ['user'] [count ( $output [$lastCount] ['user'] ) - 1] ['link'] ['down'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'down', 'groupid' => $daten ['groupid'], 'userid' => $daten ['personid'] ) );
			}
		} catch ( Exception $ex )
		{
			throw new CMSException ( 'sql fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		if (count ( $output ) > 0)
		{
			$output [count ( $output ) - 1] ['user'] [count ( $output [count ( $output ) - 1] ['user'] ) - 1] ['last'] = true;
		}
		
		try
		{
			$insert = SqlManager::GetInstance ()->Query ( 'SELECT g.name AS groupname, g.id AS groupid FROM modpersongruppe g' );
			while ( $daten = mysql_fetch_assoc ( $insert ) )
			{
				$continue = false;
				foreach ( $output as $key => $value )
				{
					if ($value ['group'] ['name'] == $daten ['groupname'])
					{
						$continue = true;
					}
				}
				if ($continue)
				{
					continue;
				}
				$details = array ();
				$details ['name'] = $daten ['groupname'];
				$details ['dellink'] = Functions::GetLink ( array ('sub' => $_GET ['sub'], 'action' => 'delete', 'id' => $daten ['groupid'] ) );
				$output [] ['group'] = $details;
			}
		} catch ( Exception $ex )
		{
			throw new CMSException ( 'sql fehler', CMSException::T_MODULEERROR, $ex );
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'modgroup_data', $output );
	}
}