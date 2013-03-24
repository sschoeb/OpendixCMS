<?php

class EXCEL
{
	/**
	 * Erstellt eine Excel-Datei mit den übergebenen Daten
	 *
	 * @param String 	$path			Pfad an dem die Excel-Datei erstellt wird
	 * @param array		$colNames		Überschrift der Spalten
	 * @param array 	$data			Inhalt der Spalten
	 * @param String	$worksheetName	Name des Worksheets
	 */
	public static function CreateExcelFile($path, $colNames, $data, $worksheetName='worksheet')
	{
		$workbook 	= new Spreadsheet_Excel_Writer($path);		
		$worksheet 	= $workbook->addWorksheet($worksheetName);
		
		$c = count($colNames);
		for($i=0; $i<$c; $i++)
		{
			$worksheet -> write(0, $i, $colNames[$i]);
		}
		
		$c = count($data);
		for($i=0; $i<$c; $i++)
		{
			$ci = count($data[$i]);
			for($j=0; $j<$ci; $j++)
			{
				$worksheet -> write($i+1, $j, $data[$i][$j]);
			}	
		}
	
		$workbook -> close();
	}
}