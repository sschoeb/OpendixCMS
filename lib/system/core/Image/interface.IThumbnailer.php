<?php

/**
 * 
 * Interface welches von allen Klassen implementiert werden soll welche
 * Thumbnails erstellen 
 *  
 * @author Stefan
 *
 */
interface IThumbnailer
{
	/**
	 * Erstellt ein Thumbnail von dem als $imgName �bergebenen Bild aus dem
	 * mit SetInputrPath gesetztn Pfad
	 * 
	 * @param String $imgName name des Bild (Pfad bereits mit SetInputPaht gesetzt)
	 */
	public function CreateThumbnail($imgName);
	
	/**
	 * Setzt den Pfad aus dem die Bilder genommen werden
	 * 
	 * @param String $path
	 */
	public function SetInputPath($path);
	
	/**
	 * Setzt den Pfad in dem die verkleinerten Bilder abgelegt werden
	 * 
	 * @param String $path
	 */
	public function SetOutputPath($path);
	
	/**
	 * Setzt das Flag ob das urspr�ngliche Bild gel�scht werden soll
	 * 
	 * @param Boolean $flag
	 */
	public function SetInInputPaht($flag);
	
	/**
	 * Setzt den Prefix f�r das erstellte Thumbnail
	 * 
	 * @param String $prefix
	 */
	public function SetThumbPrefix($prefix);
}