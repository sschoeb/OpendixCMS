<?php
/**
 * Klassen um das Menü darzustellen
 * 
 * @package    	OpendixCMS.Core
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	1.0
 */

/**
 *
 * Repräsentiert und verwaltet das Menü
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		2.0
 */
class Menue
{
	/**
	 * Array in dem die Menügruppen mit ihren Menüpunkten abgelegt werden
	 *
	 * @var array
	 */
	private $items = array ();
	
	/**
	 * Boolean ob beim Instanzieren die Berechtigungen des Besuchers berücksichtigt werden
	 *
	 * @var Boolean
	 */
	private $regardLaw = true;
	
	/**
	 * Konstruktor
	 *
	 * @param Boolean 	$generate	Optionaler Boolean ob automatisch das Menü beim instanzieren generiert
	 * werden soll. Falls nicht angegeben -> true
	 * @param Boolean	$regardLaw	Optinaler Boolean ob beim erstellen ders Menüs nur Menüeinträge
	 * miteinbezogen werden auf die der User berechtig ist
	 * Dies kann benötigt werden um z.B. beim Menüadministrator
	 * eine Menüinstanz zu erstellen über die die gesamten
	 * Menüstruktur ausgegeben werden kann
	 */
	public function __construct($generate = true, $regardLaw = true)
	{
		$this->regardLaw = $regardLaw;
		if ($generate)
		{
			$this->Generate ();
			$this->IsVisible ();
		}
	}
	
	/**
	 * Stellt für jeden Menüeintrag das visible-Attribut
	 *
	 */
	private function IsVisible()
	{
		$count = count ( $this->items );
		for($i = 0; $i < $count; $i ++)
		{
			for($j = 0; $j < count ( $this->items [$i] ['items'] ); $j ++)
			{
				$this->items [$i] ['items'] [$j]->IsVisible ();
			}
		}
	}
	
	/**
	 * Erstellt das Menü
	 *
	 */
	public function Generate()
	{
		$subs = NULL;
		try
		{
			
			$subs = SqlManager::GetInstance ()->Select ( 'sysmenue', array ('id', 'gId' ), 'parent=\'0\'', array ('`order`', 'gId' ) );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Men&uuml;grundgerüst konnte nicht abgefragt werden!', CMSException::T_SYSTEMERROR );
		}
		
		$count = count ( $subs );
		for($i = 0; $i < $count; $i ++)
		{
			//Prüfen ob die View-Berechtigung zum Menüpunkt gegeben ist
			$haslaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_VIEW, $subs [$i] ['id'] );
			if (! $haslaw && $this->regardLaw)
			{
				//Wenn nicht dann wird der Menüpunkt ignoriert -> zum nächsten springen
				continue;
			}
			
			$group = $this->GetGroup ( $subs [$i] ['gId'] );
			if (is_null ( $group ))
			{
				$this->items [] = array ();
				$group = count ( $this->items ) - 1;
				$this->items [$group] ['id'] = $subs [$i] ['gId'];
				$this->items [$group] ['name'] = $this->GetGroupName ( $subs [$i] ['gId'] );
			}
			
			$this->items [$group] ['items'] [] = new MenueItem ( $subs [$i] ['id'] );
		}
	}
	
	/**
	 * Gibt den Namen der Gruppe zurück
	 *
	 * @param int $id	ID der Gruppe
	 */
	private function GetGroupName($id)
	{
		try
		{
			return SqlManager::GetInstance ()->SelectItem ( 'sysmenuegruppe', 'name', 'id=\'' . $id . '\'' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Gruppennamen konnte nicht abgefragt werden!', CMSException::T_SYSTEMERROR, $ex );
		}
	}
	
	/**
	 * Gibt die Nummer der Gruppe in dem items-Array zurück
	 * 
	 * Sollte die Gruppe noch nicht definiert worden sein wird NULL zurück gegeben
	 *
	 * @param int $id	ID der Gruppe die gesucht wird
	 * @return int oder NULL
	 */
	private function GetGroup($id)
	{
		for($i = 0; $i < count ( $this->items ); $i ++)
		{
			if ($this->items [$i] ['id'] == $id)
			{
				return $i;
			}
		}
		return NULL;
	}
	
	/**
	 * Gibt das Menü als verschachteltes Array zurück. 
	 *
	 */
	public function Output($except = array())
	{
		$menue = array ();
		
		//Jede Menügruppe durchlaufen
		for($i = 0; $i < count ( $this->items ); $i ++)
		{
			$arr = array ();
			$arr ['id'] = $this->items [$i] ['id'];
			$arr ['name'] = $this->items [$i] ['name'];
			
			//Menüpunkte der Gruppe
			for($j = 0; $j < count ( $this->items [$i] ['items'] ); $j ++)
			{
				$e = $this->items [$i] ['items'] [$j]->Output ( 0, $except );
				if (is_null ( $e ))
				{
					continue;
				}
				$arr ['items'] [] = $e;
			}
			$menue [] = $arr;
		}
		
		return $menue;
	}

}

/**
 * Klasse repräsentiert einen Menüpunkt
 *
 * @author 		Stefan Schöb
 * @package 	OpendixCMS.Core
 * @version 	1.0
 * @copyright 	Copyright &copy; 2006, Stefan Schöb
 * @since 		2.0
 */
class MenueItem
{
	/**
	 * ID dieses Menüeintrags
	 *
	 * @var int
	 */
	private $id = NULL;
	
	/**
	 * Array mit allen Kindobjekten
	 *
	 * @var array
	 */
	private $children = array ();
	
	/**
	 * Array mit sämtlichen Informationen über den Menüpunkt
	 * 
	 * Assoziatives Array wobei die Schlüssel den Spaltennamen der sysmenue-Tabelle 
	 * gleichgesetzt sind
	 *
	 * @var array
	 */
	private $information = NULL;
	
	/**
	 * Boolean ob der Menüpunkt angezeigt wird
	 *
	 * @var Boolean
	 */
	private $visible = false;
	
	/**
	 * Boolean ob beim Instanzieren die Berechtigungen des Besuchers berücksichtigt werden
	 *
	 * @var Boolean
	 */
	private $regardLaw = true;
	
	/**
	 * Konstruktor
	 *
	 * @param int $id	ID des Menüpunktes der durch dieses Objekt repräsentiert werden soll
	 */
	public function __construct($id, $regardLaw = true)
	{
		$this->regardLaw = $regardLaw;
		
		//Id¨überprüfen und zuweisen
		if (! is_numeric ( $id ))
		{
			throw new CMSException ( 'Die &uuml;bergebene ID ist nicht numerisch!', CMSException::T_SYSTEMERROR );
		}
		$this->id = $id;
		
		//Informationen zum Menüpunkt auslesen
		$this->GetInformation ();
		
		//Alle Kinderobjekte (falls vorhanden) bearbeiten
		$this->ReadSubItems ();
	}
	
	/**
	 * Abfragen der Informationen zum Menüpunkt
	 *
	 */
	public function GetInformation()
	{
		try
		{
			$this->information = SqlManager::GetInstance ()->SelectRow ( 'sysmenue', $this->id );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Men&uuml;punkt-Informationen konnten nicht abgefragt werden!', CMSException::T_SYSTEMERROR, $ex );
		}
		return $this->information;
	}
	
	/**
	 * Sucht alle Kinder-Elemente dieses Punktes
	 * 
	 * Ein Kind-Element wird nur in die Klasse aufgenommen, wenn der Besucher
	 * zum ansehen der Seite auf die der Menüpunkt verlinkt berechtigt ist!
	 *
	 */
	private function ReadSubItems()
	{
		//Abfragen aller Kinder
		$subs = NULL;
		try
		{
			$subs = SqlManager::GetInstance ()->Select ( 'sysmenue', array ('id' ), 'parent=\'' . $this->id . '\'', '`order`' );
		} catch ( SqlManagerException $ex )
		{
			throw new CMSException ( 'Submen&uuml;punkte konnten nicht abgefragt werden!', CMSException::T_SYSTEMERROR, $ex );
		}
		
		//Durchlauf durch alle abgefragten Kinder
		$count = count ( $subs );
		for($i = 0; $i < $count; $i ++)
		{
			//Prüfen ob die View-Berechtigung zum Menüpunkt gegeben ist
			$haslaw = Cms::GetInstance ()->GetLaw ()->HasLaw ( Law::T_VIEW, $subs [$i] ['id'] );
			if (! $haslaw && $this->regardLaw)
			{
				//Wenn nicht dann wird der Menüpunkt ignoriert -> zum nächsten springen
				continue;
			}
			$this->AddChild ( new MenueItem ( $subs [$i] ['id'] ) );
		}
	}
	
	/**
	 * Fügt dem Menüpunkt ein Kind hinzu
	 *
	 * @param MenueItem $child	Kind das hinzugefügt werden soll
	 */
	public function AddChild(MenueItem $child)
	{
		$this->children [] = $child;
	}
	
	/**
	 * Entfernt ein Kind-Element
	 *
	 * @param MenueItem $item	Kindelement welches entfernt werden soll
	 * @return Boolean ob das Kind entfernt werden konnte
	 */
	public function RemoveChild(MenueItem $item)
	{
		$index = array_search ( $item, $this->children, true );
		if ($index === false)
		{
			return false;
		}
		array_splice ( $this->children, $index, 1 );
		return true;
	}
	
	/**
	 * Gibt diesen Menüpunkt aus
	 *
	 * @param int 	$level
	 * @param array	$exclude	Array mit allen IDs die nicht ausgegeben werden sollen
	 * Dabei werden auch die Menüpunkte die den entsprechenden
	 * IDs untergeordnet sind nicht ausgegeben
	 */
	public function Output($level = 0, $exclude = array())
	{
		if (in_array ( $this->id, $exclude ))
		{
			return NULL;
		}
		//Informationen zuweisen + Link generieren der zu diesem Menüpunkt führt
		$info = array ();
		$info ['level'] = $level;
		$info ['name'] = $this->information ['name'];
		$info ['visible'] = $this->visible;
		$info ['id'] = $this->id;
		$info ['type'] = $this->information ['type'];
		if ($this->information ['type'] == 3)
		{
			$info ['link'] = $this->information ['href'];
		
		} else
		{
			$info ['link'] = Functions::GetLink ( array_merge ( array ('sub' => $this->id ), Functions::SplitHref ( $this->information ['href'] ) ) );
		}
		$info ['selected'] = false;
		if (isset ( $_GET ['sub'] ) && $this->id == $_GET ['sub'])
		{
			$info ['selected'] = true;
		}
		
		//Falls der Menüpunkt Kinderobjekte hat dann dieser abarbeiten
		if ($this->HasChildren ())
		{
			$info ['children'] = array ();
			
			foreach ( $this->children as $child )
			{
				//Von jedem wieder die Informationen auslesen
				$e = $child->output ( $level + 1, $exclude );
				if (is_null ( $e ))
				{
					continue;
				}
				$info ['children'] [] = $e;
			}
		}
		
		return $info;
	}
	
	/**
	 * Gibt zurück ob dieser Menüpunkt Kinder-Elemente besitzt
	 *
	 * @return Boolean
	 */
	public function HasChildren()
	{
		if (count ( $this->children ) == 0)
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Setzt das Visible-Attribut dieses Menüpunktes
	 *
	 */
	public function IsVisible()
	{
		if ($this->information ['active'] == 0)
		{
			$this->visible = false;
			return;
		}
		
		//Die oberste Schicht hat immer visible und falls die Objekt-ID mit der 
		//gesuchten sub-ID �bereinstimmt dann auch
		if ($this->OnCheckedLevel () || $this->information ['parent'] == 0 || (isset ( $_GET ['sub'] ) && $this->IsCheckedItem ()))
		{
			$this->visible = true;
		}
		
		//Die Kinder-Objekte durchgehen
		for($i = 0; $i < count ( $this->children ); $i ++)
		{
			//Wenn ein Kind visible ist dann muss dieses Objekt auch visible sein
			if ($this->children [$i]->IsVisible ())
			{
				$this->visible = true;
				
				for($k = 0; $k < count ( $this->children ); $k ++)
				{
					$this -> children[$k]->SetVisible ( true );
				}
			}
		}
		
		if ($this->IsCheckedItem ())
		{
			for($i = 0; $i < count ( $this->children ); $i ++)
			{
				$arr = $this->children [$i]->GetInformation ();
				if (! $arr ['active'])
					continue;
				
				$this->children [$i]->SetVisible ( true );
			}
		}
		
		return $this->visible;
	}
	
	/**
	 * Gibt einen boolean zur�ck ob sich der Men�punkt auf dem selben Level
	 * befindet wie der aktivierte
	 */
	private function OnCheckedLevel()
	{
		if (! isset ( $_GET ['sub'] ) || ! is_numeric ( $_GET ['sub'] ))
			return false;
		
		$paramparent = null;
		try
		{
			$paramparent = SqlManager::GetInstance ()->SelectRow ( 'sysmenue', $_GET ['sub'], array ('active', 'parent' ) );
		} catch ( SqlManagerException $ex )
		{
			return false;
		}
		
		return $paramparent ['active'] && $paramparent ['parent'] == $this->information ['parent'];
	}
	
	/**
	 * Gibt zur�ck ob es sich bei diesem Men�eintrag um den aktuell
	 * ausgew�hlten handelt
	 */
	private function IsCheckedItem()
	{
		if (! isset ( $_GET ['sub'] ))
			return false;
		
		return $this->id == $_GET ['sub'];
	}
	
	public function SetVisible($value)
	{
		$this->visible = $value;
	}

}