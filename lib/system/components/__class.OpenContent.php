<?php
/**
 * Content
 * 
 *
 * @package    OpendixCMS
 * @author     Carsten Franke <mail@dbCF.de>
 * @copyright  GPL
 * @version    1.0
 */

/** 
 * Content anzeigen
 *
 * 
 * @access   	public
 * @package 	OpendixCMS
 * @version  	1.0
 */
class Opencontent
{
	function __construct()
	{	
		$daten 		= functions::getsub();	
		$position 	= $daten['contentId'];
		
		if($position == "")
		{
			functions::output_fehler("Kein Inhalt ausgewählt");
		}
		else
		{
			$query 		= "SELECT inhalt FROM syscontent WHERE id = '$position'";
			$insert 	= @mysql_query($query) OR functions::output_fehler('MySQL-Error: Nr. 121 ');
			if(mysql_num_rows($insert) == 0)
			{
				functions::output_fehler('Kein Inhalt zu dieser ID bestimmt.');
			}
			else
			{
				$content 	= @mysql_result($insert, 0);
				
				functions::output_var("inhalt", functions::decodeText($content, false));
			}
		}
	}
}
?>