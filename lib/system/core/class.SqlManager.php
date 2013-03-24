<?php

/**
 * SqlManager
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse die den Zugriff auf die Datenbank sicherstellt
 * 
 * @author 	Stefan Schöb
 * @package OpendixCMS
 * @version 1.1
 * @copyright 2006-2009, Stefan Schöb
 * @since 1.0     
 */
class SqlManager
{
	/**
	 * Statische Variabel fuer den Singleton
	 *
	 * @var SqlManager
	 */
	private static $myInstance;
	
	/**
	 * Zuletzt ausgefuehrte Abfrage
	 *
	 * @var String
	 */
	private $lastquery = '';
	
	/**
	 * Anzahl der ausgef�hrten Abfragen
	 *
	 * @var int
	 */
	private $querycount = 0;
	
	/**
	 * Gibt die Instanz eines SqlManagers zurueck
	 * 
	 * @access public
	 * @return SqlManager
	 */
	public static function GetInstance()
	{
		if (! self::$myInstance instanceof SqlManager)
		{
			self::$myInstance = new SqlManager ();
		}
		return self::$myInstance;
	}
	
	/**
	 * Gibt das zuletzt ausgefuehrte Query zurueck
	 *
	 * @return unknown
	 */
	public function GetLastQuery()
	{
		return $this->lastquery;
	}
	
	public function SelectMax($table, $col, $where)
	{
		$query = "SELECT MAX($col) FROM $table WHERE $where";
		$query = $this -> Query($query);
		return mysql_result($query, 0);
	}
	
	/**
	 * Gibt die zuletzt eingef&uuml,gte ID zurück
	 *
	 * @return int
	 */
	public function GetLastUsedId()
	{
		return mysql_insert_id ();
	}
	
	/**
	 * Konstruktor
	 * 
	 * @access public
	 */
	public function __construct($autoconnect = true)
	{
		if ($autoconnect)
		{
			return $this->connect ();
		}
	}
	
	/**
	 * L�scht eine ganze Tabelle
	 * 
	 * @param 	String	$table	Tabelle die gelöscht werden soll
	 * @access  public
	 *
	 */
	public function DeleteTable($table)
	{
		$query = 'DROP TABLE `' . $table . '`';
		$this->query ( $query );
	}
	
	/**
	 * Verbindet mit der Datenbank
	 * 
	 * Die Funktion verbindet die Klasse mit den Daten aus der Konfiguration
	 *
	 * @access 	public
	 * @return 	void	
	 * 
	 * @exception 		SqlManagerException		Wenn keine Verbindung hergestellt werden konnte
	 */
	public function Connect()
	{
		$user = Cms::GetInstance ()->GetConfiguration ()->__get ( 'Database/user' );
		$password = Cms::GetInstance ()->GetConfiguration ()->__get ( 'Database/password' );
		$host = Cms::GetInstance ()->GetConfiguration ()->__get ( 'Database/host' );
		$database = Cms::GetInstance ()->GetConfiguration ()->__get ( 'Database/database' );
		
		/*$user = Functions::getIniParam('db_user');
		$password = Functions::getIniParam('db_passwort');
		$host = Functions::getIniParam('db_host');
		$database = Functions::getIniParam('db_datenbank');*/
		
		$this->ConnectOther ( $user, $host, $password, $database );
	}
	
	/**
	 * Funktion verbindet auf eine Datenbank mit den angegebenen Daten
	 *
	 * @access 	public
	 * 
	 * @param 	String 	$user		Benutzername
	 * @param 	String 	$host		Host auf den Connected werden soll
	 * @param 	String 	$password	Passwort fuer den Benutzer
	 * @param 	String 	$database	Datenbank die geueffnet werden soll
	 * 
	 * @return 	void
	 * 
	 * @exception 		SqlManagerException		Wenn keine Verbindung hergestellt werden konnte
	 */
	public function ConnectOther($user, $host, $password, $database)
	{
		$connect = @mysql_connect ( $host, $user, $password );
		if (! $connect)
		{
			Throw new SqlManagerException ( mysql_error (), mysql_errno (), $this->getLastQuery (), '' );
		}
		$select = @mysql_select_db ( $database );
		if (! $select)
		{
			Throw new SqlManagerException ( mysql_error (), mysql_errno (), $this->GetLastQuery (), '' );
		}
	}
	
	/**
	 * Insert eines Datensatzes
	 * 
	 * Fuegt einen Datensatz in eine Tabelle ein
	 *
	 * @access 	public
	 * @return 	Int					ID des eingefügten Datensatzes
	 * @param 	string 		$table 	Die Tabelle in die der Datensatz eingefuegt werden soll	 
	 * @param 	string[]	$values	Assoziatives Strign-Array wobei der Schlüssel immer die Spalte
	 * und der Wert den einzufuegenden Wert darstellt!
	 * 
	 * @exception 	SqlManagerException wenn das einfügen nicht funktioniert hat
	 */
	public function Insert($table, $values)
	{
		$keys = array ();
		$vals = array ();
		
		foreach ( $values as $key => $value )
		{
			$keys [] = '`' . $key . '`';
			if (! is_array ( $value ))
			{
				if (is_null ( $value ))
				{
					$vals [] = 'NULL';
					continue;
				}
				$vals [] = "'" . $value . "'";
				continue;
			}
			
			if (isset ( $value ['date'] ) && $value ['date'] == 'NOW')
			{
				$vals [] = "'" . mysql_result ( $this->Query ( 'SELECT NOW()' ), 0 ) . "'";
			}
		}
		
		$fkey = implode ( ',', $keys );
		$fvalue = implode ( ',', $vals );
		
		//Query-String zusammensetzen
		$query = 'INSERT INTO ' . $table . '(' . $fkey . ') VALUES(' . $fvalue . ')';
		$insert = $this->query ( $query );
		if (! $insert)
		{
			Throw new SqlManagerException ( mysql_error (), mysql_errno (), $this->lastquery (), '' );
		}
		return mysql_insert_id ();
	}
	
	/**
	 * Updaten eines Datensatzes
	 * 
	 * Updatet einen Datensatz in einer Tabelle
	 *
	 * @access 	public
	 * @return 	Boolean				true wenn OK
	 * false wenn fehlgeschlagen
	 * @param 	String		$table 	Die Tabelle in der der Datensatz geuendert werden soll
	 * @param 	String[]	$values	Assoziatives String-Array wobei der Schlüssel immer die Spalte 
	 * und der Wert den neuen Wert darstellt
	 * @param 	String		$where	Where-Clause zum eingrenzen der geupdateten Datensaetze (z.B. a=1)
	 * 
	 * @exception 			SqlManagerException 	Bei fehlern in der Abfrage
	 */
	public function Update($table, $values, $where)
	{
		$i = 0;
		$string = '';
		foreach ( $values as $key => $wert )
		{
			$i ++;
			
			if (is_null ( $wert ))
			{
				$wert = 'NULL';
			
			} elseif ($wert == '')
			{
				$wert = "''";
			} else
			{
				$wert = "'$wert'";
			}
			
			if (count ( $values ) == $i)
			{
				$string .= "$key = $wert";
			} else
			{
				$string .= "$key = $wert, ";
			}
		}
		
		$query = "UPDATE $table SET $string  WHERE $where";
		$update = $this->query ( $query );
		if (! $query)
		{
			Throw new SqlManagerException ( mysql_error (), mysql_errno (), $this->lastquery (), '' );
		}
		return true;
	}
	
	/**
	 * Fuehrt einen Select aus
	 * 
	 * @access 	public		
	 * @return 	mixed				Array 	Sollte die ABfrage erfolgreich sein wird ein Array zurückgegeben
	 * auf welches wie folge Zugegriffen werden kann:
	 * Assoziativer Zugriff:	$array[0]['spalte1']	1. Datensatz die erste ausgewählte Spalte
	 * $array[0]['spalte2']	1. Datensatz die zweite ausgewählte Spalte
	 * $array[1]['spalte1']	2. Datensatz die erste ausgewählte Spalte
	 * Indexierter Zugriff:	$array[0][0]			1. Datensatz die erste ausgewählte Spalte
	 * $array[0][1]			1. Datensatz die zweite ausgewählte Spalte
	 * $array[1][0]			2. Datensatz die erste ausgewählte Spalte
	 * Boolean	Schlägt die Abfrage fehl wird false zurückgegeben
	 * 
	 * @param 	String		$table	Tabelle auf der der Select ausgefuehrt wird. Kann auch als ein 
	 * indexiertes Array uebergeben werden.
	 * @param 	String[]	$rows	Indexiertes Array das alle abzufragenden Spalten beinhaltet
	 * @param 	String		$where	Where-Clause zum eingrenzen der geupdateten Datensaetze (z.B. a=1)
	 * @param 	String		$order	Order-Clause um Datensaetze zu ordnen (z.B id => Ordnung nach id)	
	 * @param 	Int[]		$limit	Array mit dem man das LIMIT angeben kann. Erstes Element ist der Datensatz ab dem 
	 * ausgegangen werden soll und das zweite die Anzahl Datensätze 
	 * 
	 */
	public function Select($table, $rows, $where = '', $order = '', $orderEffect = 'ASC', $limit = '')
	{
		$frows = $this->GetCols ( $rows );
		
		if (is_array ( $table ))
		{
			foreach ( $table as $item )
			{
				if ($item == '')
				{
					$tables = $item;
				} else
				{
					$tables .= ",$item";
				}
			}
		} else
		{
			$tables = $table;
		}
		
		$fwhere = '';
		if ($where != '')
		{
			$fwhere = " WHERE " . $where . " ";
		}
		
		$forder = '';
		if ($order != '')
		{
			if (is_array ( $order ))
			{
				
				for($i=0; $i<count($order); $i++)
				{
					if($forder != '')
					{
						$forder .= ', ';
					}
					$forder .= $order[$i];
				}
				$forder = " ORDER BY " . $forder . " " . $orderEffect;
				
			} else
			{
				$forder = " ORDER BY $order  $orderEffect";
			}
		}
		
		$query = "SELECT $frows FROM $tables $fwhere $forder";
		
		
		
		if ($limit != '')
		{
			$query .= ' LIMIT ' . $limit [0] . ', ' . $limit [1];
		}
		
		$select = $this->query ( $query );
		
		$back = array ();
		while ( $daten = mysql_fetch_array ( $select ) )
		{
			$back [] = $daten;
		}
		
		return $back;
	}
	
	public function QueryAndFetch($query, $type = MYSQL_BOTH)
	{
		$select = $this->Query ( $query );
		$back = array ();
		
		while ( $daten = mysql_fetch_array ( $select, $type ) )
		{
			$back [] = $daten;
		}
		
		return $back;
	}
	
	public function SelectWithScrollMenue($table, $rows, $where = '', $order = '', $orderEffect = 'ASC', $scrollPage = null, $countPerPage = null)
	{
		//Initialisieren der Daten
		$from = 0;
		$daten = array ();
		$returnDaten = array ();
		$returnDaten ['menue'] = null;
		$returnDaten ['daten'] = array ();
		
		//Falls keine Seite übergeben wird so wird automatisch die Seite aus demLink ausgelesen
		//Sollte da keine Angabe vorhanden sein wird die erste Seite angezeigt
		if ($scrollPage === null)
		{
			if (isset ( $_GET ['page'] ))
			{
				$scrollPage = Validator::ForgeInput ( $_GET ['page'] );
			}
			
			if (! is_numeric ( $scrollPage ))
			{
				$scrollPage = 0;
			}
		}
		
		//Falls keine spezielle Angabe über die Anzahl Einträge pro Seite übergeben wurde dann wird hier 
		//der Standard-Wert aus der Konfiguration ausgelesen
		if ($countPerPage === null)
		{
			$countPerPage = Cms::GetInstance ()->GetConfiguration ()->Get ( 'General/entriesPerSite' );
		}
		
		//Anzahl der Einträge die Betroffen sind von der BEdingung ermitteln
		$anz = $this->Count ( $table, $where );
		
		//Anzahl der Seiten berechnen
		$pageCount = ceil ( $anz / $countPerPage );
		
		//Berechnen ab welchem Eintrag die Liste ausgegeben werden soll
		if ($scrollPage == 0)
		{
			$scrollPage = 1;
		}
		$from = ($scrollPage - 1) * $countPerPage;
		
		//Links für das Scroll-Menü erstellen
		$scrollObj = new Scroller ( $countPerPage, $anz, $scrollPage );
		$returnDaten ['menue'] = $scrollObj->GetLinks ();
		
		//Abfragen der Daten
		$returnDaten ['daten'] = $this->Select ( $table, $rows, $where, $order, $orderEffect, array ($from, $countPerPage ) );
		
		return $returnDaten;
	}
	
	/**
	 * Gibt einen einzelnen Datensatz zurueck fuer eine ID
	 *
	 * @access 	public
	 * 
	 * @param 	String 	$table		Die Tabelle von der der Datensatz abgefragt werden soll
	 * @param	String 	$id			Die id des datensatzes
	 * @param 	Array	$cols		Array mit den gewünschten Spalten aus dem Datensatz
	 * Wird NULL übergeben werdenalle Spalte abgefragt
	 * 
	 */
	public function SelectRow($table, $id, $cols = NULL, $fetch = MYSQL_BOTH)
	{
		$query = 'SELECT ' . $this->GetCols ( $cols ) . ' FROM ' . $table . ' WHERE id=\'' . $id . '\'';
		return mysql_fetch_array ( $this->query ( $query ), $fetch );
	
	}
	
	private function GetCols($cols)
	{
		if (is_null ( $cols ))
		{
			return ' * ';
		}
		
		$rows = array ();
		foreach ( $cols as $key => $value )
		{
			if (is_array ( $value ))
			{
				//$key ist der Spaltenname und in $value sind Eigenschaften dafür angegeben
				$key = '`' . $key . '`';
				if (isset ( $value ['function'] ))
				{
					$key = $value ['function'] . '(' . $key . ')';
				}
				if (isset ( $value ['alias'] ))
				{
					$key .= ' AS ' . $value ['alias'];
				}
				$rows [] = $key;
			} elseif (! is_int ( $key ))
			{
				//$key ist der Spaltenname und $value der alias dafür	
				$key = '`' . $key . '` AS ' . $value;
				$rows [] = $key;
			} else
			{
				//$key ist der Intege-index des normalen Arrays und $value ist der Spaltenname
				$rows [] = $value;
			}
		}
		
		return implode ( ',', $rows );
	}
	
	/**
	 * Gibt ein einzelnes Feld zurueck
	 *
	 * @access 	public
	 * @return 	mixed				false wenn fehlgeschlagen
	 * ansonsten der Inhalt der im Feld steht
	 * @param 	String		$table	Tabelle die Abgefragt werden soll
	 * @param 	String		$row	Spalte die Abgefragt werden soll
	 * @param 	String		$where	Where-Clause zum eingrenzen der geupdateten Datensaetze (z.B. a=1)
	 *
	 */
	public function SelectItem($table, $row, $where)
	{
		$result = $this->select ( $table, array ($row ), $where );
		if (! $result)
		{
			return false;
		}
		if (count ( $result ) == 0)
		{
			throw new CMSException ( 'Die Abfrage liefert keine Ergebnisse!', CMSException::T_WARNING );
		}
		return $result [0] [$row];
	}
	
	public function SelectItemById($table, $column, $id)
	{
		return $this -> SelectItem($table, $column, 'id=\''. $id .'\'');
	}
	
	/**
	 * Fuehrt einen einfach query aus 
	 *
	 * @access 	public
	 * @return 	mixed				ressource wenn OK
	 * fehlermeldung wenn fehlgeschlagen
	 * @param 	String		$query	Ein SQL-String der ausgefuehrt werden soll 
	 */
	public function Query($query)
	{
		$this->querycount ++;
		$result = mysql_query ( $query );
		$this->lastquery = $query;
		if (! $result)
		{
			Throw new SqlManagerException ( mysql_error (), mysql_errno (), $query, '' );
		} else
		{
			
			return $result;
		}
	}
	
	/**
	 * Abfrage auf eine Tabelle die alle Spalten zurueckgibt	
	 *
	 * @param 		String 	$table		Die Tabelle von der die Daten abgefrag werden sollen
	 * @param 		mixed 	$where 		String		Bedingung die genau so hinter das WHERE angeh�ngt wird
	 * Array		Array mit Key => Value Paaren wobei der Key immer den 
	 * Spaltennamen und Value den gew�nschten Wert entspricht 
	 * @return 		mixed	ressource 	wenn OK
	 * null	wenn fehler
	 * @exception 	SqlException Wird geworfen wenn ein Fehler auftritt
	 */
	public function SelectAll($table, $where = '', $order = '', $limit = '')
	{
		$query = "SELECT * FROM $table ";
		if ($where != '')
		{
			$query .= ' WHERE ';
			//Pr�fen ob ein Array von Where-Bedingungen �bergeben wurde
			if (is_array ( $where ))
			{
				//Wenn ja dann jeden Eintrag in diesme Array durchgehen
				//Der Schlüssel stellt den Spaltennamen und der Wert den gew�nschten Wert in der Spalte dar.
				foreach ( $where as $key => $value )
				{
					//Bei allen ausser dem ersten muss erst noch ein AND angekn�pft werden
					if ($query != 'SELECT * FROM ' . $table . ' WHERE ')
					{
						$query .= ' AND ';
					}
					//die Bedingung anh�ngen
					$query .= ' ' . $key . ' = \'' . $value . '\' ';
				}
			} else
			{
				//$where ist kein Array also kann man die Bedingung einfach hinter das WHERE anh�ngen
				$query .= $where;
			}
		}
		
		if ($order != '')
		{
			$query .= ' ORDER BY ' . $order . ' ';
		} else
		{
			$query .= ' ORDER BY id ';
		}
		
		if ($limit != '')
		{
			$query .= ' LIMIT ' . $limit [0] . ', ' . $limit [1];
		}
		//Array definieren in welchem die Datens�tze abgelegt werden
		$back = array ();
		//Jeden Datensatz durchf�hren und zu dem Array hinzuf�gen
		$result = $this->query ( $query );
		while ( $daten = @mysql_fetch_array ( $result ) )
		{
			$back [] = $daten;
		}
		//Alle Datensätze zur�ckgeben
		return $back;
	}
	
	/**
	 * Führt eine DELETE-Anweisung auf einer Tabelle aus
	 *
	 * @access 	public
	 * 
	 * @param 	String 	$table		Tabelle auf der das DELETE ausgef�hrt werden soll
	 * @param 	mixed 	$where		String		Angabe eines einzelnen Where-Statements. z.B id=5
	 * Array		Angabe mehrerer Where-Statements. z.b. array('id' => 5, 'subId' => 3)	
	 */
	public function Delete($table, $where = '')
	{
		//Grundstruktur eines DELETE-Statements
		$query = 'DELETE FROM ' . $table;
		//Pr�fen ob ein WHERE angegen wurde, wenn nicht wird das obige Statement ausgef�hrt
		if ($where != '')
		{
			$query .= ' WHERE ';
			//Als Parameter kann ein Array oder nur ein String �bergeben werden
			if (is_array ( $where ))
			{
				//Sollte es ein Array sein jedes Element durchgehen
				foreach ( $where as $key => $value )
				{
					//Wenn es das erste ist kein AND ansetzen..
					if ($query == 'DELETE FROM ' . $table . ' WHERE ')
					{
						$query .= ' ' . $key . '=\'' . $value . '\'';
					} else
					{
						//ansonsten vor dem spaltennamen erst ein AND
						$query .= ' AND ' . $key . '=\'' . $value . '\'';
					}
				}
			} else
			{
				//Wenn als Parameter ein String kommt dann einfach diesen Anh�ngen
				$query .= $where;
			}
		}
		//Das zusammengesetze DELETE-Statement ausf�hren
		$this->query ( $query );
	}
	
	/**
	 * L�scht einen Datensatz nach seiner ID
	 *
	 * @param String 	$table
	 * @param int 		$id
	 */
	public function DeleteById($table, $id)
	{
		$this->Delete ( $table, 'id=\'' . $id . '\'' );
	}
	
	/**
	 * Fuert einen COUNT() aus
	 *
	 * @access 	public
	 * @return 	int					Anzahl Datensaetze
	 * @param 	String		$table	Tabelle auf die der Count angewendet werden soll
	 * @param 	String		$where	Where-Clause zum eingrenzen der geupdateten Datensaetze (z.B. a=1)
	 * @param 	String		$count	COUNT-Eigenschaft, Standardmaesig * aber z.B. wenn COUNT(id) gewollt => $count = "id"	 
	 */
	public function Count($table, $where = '', $count = '*')
	{
		if ($where != '')
		{
			$where = " WHERE $where";
		}
		$query = "SELECT COUNT($count) FROM $table $where";
		
		return mysql_result ( $this->query ( $query ), 0 );
	}
	
	/**
	 * Gibt die Anzahl ausgef�hrter Abfragen zur�ck	
	 *
	 * @return int
	 */
	public function GetQueryCount()
	{
		return $this->querycount;
	}
	
	/**
	 * Gibt alle Feldnamen einer Tabelle zurück
	 *
	 * @param String	$table	Name der Tabelle deren Spaltennamen abgefragt werden
	 */
	public function GetFieldNames($table)
	{
		$cols = array ();
		$query = 'SHOW COLUMNS FROM ' . $table;
		$insert = $this->Query ( $query );
		while ( $daten = mysql_fetch_assoc ( $insert ) )
		{
			$cols [] = $daten ['Field'];
		}
		return $cols;
	}
	
	/**
	 * Setzt die MySql-Sprache
	 *
	 * @param String $language
	 */
	public function SetMySqlLanguage($language)
	{
		$this->Query ( 'SET lc_time_names = \'' . $language . '\'' );
	}
}

/**
 * Beinhaltet SqlManagerException
 *
 * @package 	Framework
 */

/**
 * SqlException-Klasse
 * 
 * @package 	Framework
 * @author 		Stefan Schoeb <stefan.schoeb@igastro.ch>
 * @version 	1.0
 * 
 */
class SqlManagerException extends Exception
{
	/**
	 * Fehlermeldung die von mysql_error() ausgegeben wird
	 *
	 * @var String
	 */
	private $sqlError = '';
	
	/**
	 * Fehlermeldung die vom Benutzer hinzugef�gt wird
	 *
	 * @var String
	 */
	private $userError = '';
	
	/**
	 * Fehlercode der von mysql_errno() ausgegeben wird
	 *
	 * @var int
	 */
	private $sqlErrorCode = 0;
	
	/**
	 * Query das versucht wurde auszuf�hren und zum Fehler gef�hrt hat
	 *
	 * @var String
	 */
	private $query = '';
	
	/**
	 * Konstruktor
	 *
	 * @access 	public
	 * 
	 * @param 	String	$sqlError		Fehler der von mysql_query() ausgegeben wird
	 * @param 	int		$sqlErrorCode	Fehler-Code der von mysql_errno() ausgegeben wird
	 * @param 	String	$query			Query das ausgef�hrt wurde
	 * @param 	String 	$userError		Fehler der vom User definiert werden kann
	 * 
	 */
	public function __construct($sqlError, $sqlErrorCode, $query, $userError)
	{
		//Konstruktor von Exception ausf�hren
		parent::__construct ( $sqlError );
		
		//Eigenschaften zuweisen
		$this->sqlError = $sqlError;
		$this->sqlErrorCode = $sqlErrorCode;
		$this->query = $query;
		$this->userError = $userError;
	}
	
	/**
	 * Gibt den vorgefallenen SQL-Fehler zur�ck
	 *
	 * @access 	public
	 * 
	 */
	public function getSqlError()
	{
		return $this->sqlError;
	}
	
	/**
	 * Gibt Error-Code des SQL-Fehlers zur�ck
	 *
	 * @access 	public
	 * 
	 */
	public function getSqlErrorCode()
	{
		return $this->sqlErrorCode;
	}
	
	/**
	 * Gibt das Query zur�ck das zu diesem Fehler gef�hrt hat
	 *
	 * @access 	public
	 * 
	 */
	public function getQuery()
	{
		return $this->query;
	}
	
	/**
	 * Gibt eine individuelle Fehlermeldung zur�ck
	 *
	 * @access 	public
	 * 
	 */
	public function getUserError()
	{
		return $this->userError;
	}
	
	/**
	 * R�ckgabe des Fehlers
	 * 
	 * Die magische Methode __toString wird �berschrieben um einen genauen Fehler
	 * f�r die SqlManager-Klasse zu generieren.
	 *
	 * @return String
	 */
	public function __toString()
	{
		return "SqlManager-Fehler(" . $this->file . " on " . $this->line . "): [{$this->code}]:  {$this->message}\n";
	}

}
