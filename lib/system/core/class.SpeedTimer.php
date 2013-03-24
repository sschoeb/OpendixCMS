<?php
/**
 * Beinhaltet den Timer von Julian Moritz
 * 
 * @package 	OpendixCMS.Core
 */

/**
 * Timer-Klasse
 * 
 * @author 		Julian Moritz <jmoritz@BeCreated.de>
 * @version 	1.0
 * @package 	OpendixCMS.Core
 *
 */
class SpeedTimer
{
	public $start_time = 0;
	public $stop_time = 0;
	
	static $instance = '';
	
	public static Function GetInstance()
	{
		//single instance
		static $instance;
		
		//Sollte noch keine Instanz vorhanden sein, eine erstellen
		if (! is_object ( $instance ))
		{
			$instance = new Timer ();
		}
		return ($instance);
	}
	
	//sets the starting point
	function start()
	{
		$mt = microtime ();
		$ta = explode ( " ", $mt );
		$this->start_time = $ta [0] + $ta [1];
	}
	
	//sets the ending point
	function stop()
	{
		$mt = microtime ();
		$ta = explode ( " ", $mt );
		$this->stop_time = $ta [0] + $ta [1];
	}
	
	//returns the difference between starting point and ending point in seconds
	function runtime()
	{
		$td = $this->stop_time - $this->start_time;
		return $td;
	}

}
