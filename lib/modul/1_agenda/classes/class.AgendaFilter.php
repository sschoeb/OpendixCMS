<?php

/**
 * Filterklasse für die Agenda
 * 
 *
 * @package    	OpendixCMS
 * @author     	Stefan Schöb <opendix@gmail.com>
 * @copyright  	2006-2009 Stefan Schöb
 * @version    	1.0
 */

/**
 * Filtert die Agendaansicht in der Übersicht
 * 
 * @author 		Stefan Schöb
 * @package 	OpendixCMS
 * @version 	1.0
 * @copyright 	2006-2009, Stefan Schöb
 * @since 		2.0     
 */
class AgendaFilter
{
	/**
	 * Boolean ob es sich bei der Übersicht um eine Übersicht für
	 * den Administrator oder einen Besucher handelt.
	 * 
	 * Dem Besucher werden nur Aktive Termine angezeigt und die definierten Gruppen.
	 * Dem Admin werden alle Termine angezeigt (entsprechend gefiltert..)
	 *
	 * @var Boolean
	 */
	private $isAdminFilter = false;
	
	/**
	 * Array in dem alle eingestellten Filter gespeichert werden
	 *
	 * @var Array
	 */
	private $options = array ('type' => '', 'search' => '', 'begin' => '', 'end' => '', 'upcoming' => '', 'active' => '' );
	
	/**
	 * Konstruktor
	 * 
	 * Der Konstruktor initialisiert die Standardeinstellung für den Filter.
	 * z.B. werden beim öffentlichen standardmässig nur die bevorstehenden
	 * Termine angezeigt
	 *
	 * @param Boolean $isAdminFilter
	 */
	public function __construct($isAdminFilter)
	{
		$this->isAdminFilter = $isAdminFilter;
		$this->Init ();
	}
	
	/**
	 * Gibt das Where-Statement für den Filter zurück
	 *
	 */
	public function GetWhere()
	{
		$conditions = array ();
		
		//Prüfen ob nach irgendeinem Type (Agendagruppe) gefiltert werden soll
		if ($this->options ['type'] != '')
		{
			//Es kann nach mehreren AgendaGruppen gefiltert werden
			//z.B. wenn die öffentliche Übersich angezeigt werden, werden nur die
			//Termine angezeigt, welchen in den für den Besucher ersichtlichen
			//Gruppen eingeordnet sind
			if (is_array ( $this->options ['type'] ))
			{
				$typeCondition = array ();
				$c = count ( $this->options ['type'] );
				
				for($i = 0; $i < $c; $i ++)
				{
					$typeCondition [] = 'agendaId=\'' . $this->options ['type'] [$i] [0] . '\'';
				}
				
				$conditions [] = ' (' . implode ( ' OR ', $typeCondition ) . ') ';
			} else
			{
				
				if($this -> options['type'] != '')
				{
				//Es handelt sich nur um eine einzelne Gruppe die angezeigt werden soll
				//Dies kann sein wenn öffentlich nur eine einzelne Gruppe angezeoigt wird
				//order spezifisch nach einer einzelnen Gruppe gefiltert wird
				$conditions [] = 'agendaId=\'' . $this->options ['type'] . '\'';	
				}
				
				
			}
		}
		
		if ($this->options ['search'] != '')
		{
			
			//Filtern nach einem Suchbegriff im Namen des Termins
			$conditions [] = 'name LIKE \'%' . $this->options ['search'] . '%\'';
		}
		
		if ($this->options ['begin'] != 'notime')
		{
			//Alle Termine ab einem gewissen Zeitpunkt anzeigen
			$conditions [] = '`begin` > \'' . $this->options ['begin'] ['Year'] . '-' . $this->options ['begin'] ['Month'] . '-' . $this->options ['begin'] ['Day'] . ' 00:00:00\'';
		}
		
		if ($this->options ['end'] != 'notime')
		{
			//Alle Termine vor einem gewissen Zeitpunkt anzeigen
			$conditions [] = '`begin` < \'' . $this->options ['end'] ['Year'] . '-' . $this->options ['end'] ['Month'] . '-' . $this->options ['end'] ['Day'] . ' 24:00:00\'';
		}
		
		if (($this->options ['upcoming']) == 'true')
		{
			//Nur bevorstehende Termine anzeigen
			$conditions [] = 'state=\''. TerminState::upcoming .'\'';
		}
		
		if ($this->options ['active'])
		{
			//Nur aktive Termine anzeigen -> öffentliche Anzeige
			$conditions [] = 'active=\'1\'';
		}
		
		//Alle Conditions mit einem "AND" (-> MySQL-Style) verknüpfen damit es ein korrektes
		//Where-Statement hergibt
		return implode ( ' AND ', $conditions );
	}
	
	/**
	 * Setzt eine einzelne Filter-Option
	 *
	 * @param String $option	Name der Option/des Filters
	 * @param String $value		Wert der Option
	 */
	public function SetOption($option, $value)
	{
		$this->options [$option] = $value;
	}
	
	/**
	 * Gibt die IsAdminFilter-Eigenschaft dieser AgendaFilter-Klasse zurück
	 *
	 * @return Boolean
	 */
	public function GetIsAdminFilter()
	{
		return $this->isAdminFilter;
	}
	
	/**
	 * Setzt mehrere Filter-Optionen
	 *
	 * @param Array $options	Alle zu setzenden Optionen
	 */
	public function SetOptions($options)
	{
		if (! is_array ( $options ))
		{
			throw new CMSException ( 'Filter-Optionen konnten nicht gesetzt werden!' );
		}
		foreach ( $options as $optionName => $value )
		{
			$this->SetOption ( $optionName, $value );
		}
	}
	
	/**
	 * Gibt den Wert einer Filter-Option zurück	
	 *
	 * @param String 	$optionName		Name der Filter-Option
	 */
	public function GetOption($optionName)
	{
		if (! isset ( $this->options [$optionName] ))
		{
			throw new CMSException ( 'Diese Filter-Option existiert nicht(' . $optionName . ')', CMSException::T_MODULEERROR );
		}
		return $this->options [$optionName];
	}
	
	/**
	 * Initialisiert den Filter so wie er in der Ausgangslage ist
	 * Je nach Wert von IsAdminFilter kann eine andere Konfiguration
	 * geladen werden.
	 *
	 */
	public function Init()
	{
		
		//Instanz des Moduls holen um die Konfigurationen nachher auszulesen
		$module = Cms::GetInstance ()->GetModule ();
		
		$prefix = 'admin';
		//Standardmässig wird von einem Adminbesucher ausgegangen, also wird der active-Filter nicht gesetzt
		//--> Es werden auch nicht aktive Termine angezeigt
		$this->options ['active'] = false;
		
		if (! $this->isAdminFilter)
		{
			//Sollte es sich um keinen Adminbesucher handeln..
			$prefix = 'open';
			//... werden nur gewisse Gruppen der Agenda angezeigt...
			$this->options ['type'] = $module->GetDbConfiguration ( 'groupid' );
			//... und es werden nur die aktiven Termine ausgegeben
			$this->options ['active'] = true;
		}
		
		//Auslesen aller weiteren (von Adminstatus unabhängigen) Filtereinstellungen
		$this->options ['search'] = $module->GetDbConfiguration ( $prefix . 'filtersearch' );
		$this->options ['begin'] = $module->GetDbConfiguration ( $prefix . 'filterbegin' );
		$this->options ['end'] = $module->GetDbConfiguration ( $prefix . 'filterend' );
		$this->options ['upcoming'] = $module->GetDbConfiguration ( $prefix . 'filterupcoming' );
	}
}