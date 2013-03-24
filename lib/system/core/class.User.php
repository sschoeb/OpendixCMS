<?php

class User
{
	
	/**
	 * Array mit den Eigenschaften für den Besucher
	 *
	 * @var mixed
	 */
	private $config = array('id' => 0);
	
	/**
	 * Konstruktor
	 * 
	 * Wird als Parameter false übergeben wird nicht automatisch der Besucher instanziert.
	 * D.h. wenn true übergeben wird, ruft der Konstruktor die Funktion "Load" auf welche
	 * für den User der angemeldet ist (oder eben den unbekannten User) die Klasse instanziert
	 * und die entsprechenden Eigenschaftengesetzt.
	 *
	 * @param unknown_type $load
	 */
	public function __construct($load = true)
	{
		if($load)
		{
			$this -> Load();
		}
	}
	
	/**
	 * Lädt die Standardeigenschaften
	 * 
	 * Als Parameter kann optional eine ID eines Users angegeben werden. Wird keine ID angegeben
	 * so wird der angemeldete User aus der Datenbank abgefragt. Sollte es sich um einen unbekannten
	 * Besucher der Webseite handeln werden Standardwerte gesetzt.
	 * Achtung: Sollten bereits Parameter geladen sein werden diese gel�scht!
	 * 
	 * @param 	int 	$id		ID des Users der geladen werden soll
	 * @todo  richtige ID wenn is_null + Datenbank
	 */
	public function Load($id = NULL)
	{
		if(is_null($id))
		{
			//Ist keine ID gesetzt dann wird die Funktion mit der in der SESSION gespeicherten
			//ID aufgerufen. Das in der SESSION eine id vorhanden ist wird in der SESSION-Klasse
			//sichergestellt. Diese wird vor der CMS-Klasse instanziert
			if($_SESSION['SYSTEM']['user_id'] != null)
			{
				$this -> Load($_SESSION['SYSTEM']['user_id']);
			}
			else
			{
				$this -> LoadPublicUser();	
			}
			return;	
		}

		try 
		{	
			$c = SqlManager::GetInstance() -> Count('sysuser', 'id=\''. $id .'\'');
			if($c==0)
			{
				throw new CMSException('Kein User mit dieser ID', CMSException::T_MODULEERROR );
			}
			$this -> config = SqlManager::GetInstance() -> SelectRow('sysuser', $id);
		}
		catch (SqlManagerException $ex)
		{
			throw new CMSException('Die Eigenschaften des Users konnten nicht abgefragt werden!', $ex);
		}
		
		if($this -> config['imageFileId'] == 0)
		{
			$this -> config['imageFileId'] = Cms::GetInstance() -> GetConfiguration() -> Get('General/unknownuserimageid');
		}
		
	}
	
	private function LoadPublicUser()
	{
		$this -> config['template'] = 4;
	}
	
	/**
	 * Gibt den Wert einer Eigenschaft zurück für die nicht explizit ein Getter implementiert wurde
	 * 
	 * @exception 	CMSException wenn die Eigenschaft nicht vorhanden ist
	 * @param 		String 	$name	Name der Eigenschaft die abgefragt wird
	 * @return 		mixed			
	 */
	public function __get($name)
	{
		
		//Pr�fen ob die Eigenschaft vorhanden ist ansonsten Exception werfen
		if(!isset($this -> config[$name]))
		{
			throw new CMSException('Die Klasse User hat keine Eigenschaft "' . $name . '"', CMSException::T_SYSTEMERROR);
		}
		
		return $this -> config[$name];
	}
	
	/**
	 * Setzt den Wert für eine Eigenschaft für die nicht explizit ein Setter implementiert wurde
	 * 
	 * Es findet keine überprüfung des Wertes statt. Existiert die Eigenschaft noch nicht
	 * in der Klasse so wird diese angelegt.
	 *
	 * @param String 	$name	Name der Eigenschaft
	 * @param mixed 	$value	Wert der Eigenschaft
	 */
	public function __set($name, $value)
	{
		//Pr�fen ob die Eigenschaft vorhanden ist ansonsten erstellen
		if(!isset($this -> config[$name]))
		{
			$this -> config[$name] = '';
		}
		$this -> config[$name] = $value;
	}
	
	/**
	 * Gibt zurück ob der User angemeldet ist
	 * 
	 * @return  Boolean
	 *
	 */
	public function IsDeclared()
	{
		if(!isset($this -> config['id']) || $this -> config['id'] == 0)
			return false;
			
		return $_SESSION['SYSTEM']['user_id'] == $this -> config['id'];
	}
	
	public function GetContactProperties()
	{
		return array(	'firstname' => $this -> config['firstname'], 
						'name' => $this -> config['name'], 
						'email' => $this -> config['email'], 
						'residence' => $this -> config['residence'], 
						'zip' => $this -> config['zip'],
						'street' => $this -> config['street'],
						'phoneprivate' => $this -> config['phoneprivate'],
						'phonebusiness' => $this -> config['phonebusiness'],
						'phonemobile' => $this -> config['phonemobile'],
						'mobilephone' => $this -> config['mobilephone']);
								
	}
	
	public function GetImagePath()
	{
		try {
			return Filebase::GetFilePath($this -> config['imageFileId'], true);
		}catch(CMSException $ex)
		{
			
		}
		return "";
	}
}