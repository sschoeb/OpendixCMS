<?php
/**
 * Links
 * 
 * @package    OpendixCMS
 * @author     Stefan Sch�b <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.1
 */

/**
 * �ffentliche anzeige der Links
 * 
 * @author Stefan Sch�b
 * @package OpendixCMS
 * @version 1.1
 * @copyright Copyright &copy; 2006, Stefan Sch�b
 * @since 1.0     
 */
class OpenLink extends OpenModulBase
{
	
	public function __construct()
	{
		parent::__construct ();
	}
	
	/**
	 * Zeigt eine �bersicht �ber alle Link-Gruppen sowie der beinhalteten Links an
	 * 
	 * Diese Methode wird vom Basis-Konstruktor aufgerufen
	 */
	protected function Overview()
	{
		// Abfragen welche Gruppen in diesem Men�punkt angezeigt werden sollen
		$module = Cms::GetInstance ()->GetModule ();
		$groupIds = $module->GetDbConfiguration ( 'groupid', Cms::GetInstance ()->GetMenueId () );
		
		$data = null;
		$i = 0;
		
		// F�r jede Gruppe den Gruppennamen sowie die jeweils enthaltetn Links abfragen 
		$sqlMan = SqlManager::GetInstance();
		foreach ( $groupIds as $group )
		{
			try
			{
				$data[$i]['name'] = $sqlMan->SelectItem ( 'modlinkgruppe', 'gruppe_name' , 'id=\''. $group[0] .'\'' );
				$data[$i]['data'] = $sqlMan->Select('modlink', array('name', 'url', 'beschreibung'), 'gId=\''. $group[0] .'\'');
			} catch ( SqlManagerException $ex )
			{
				throw new CMSException ( 'SQL-Fehler', CMSException::T_MODULEERROR, $ex );
			}
			$i++;
		}
		
		MySmarty::GetInstance ()->OutputModuleVar ( 'linkdata', $data );
	}
}
