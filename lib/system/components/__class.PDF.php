<?php
/**
 * PDF
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Schöb <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Schöb
 * @version    1.0
 */

/**
 * Klasse die zum erstellen des PDFs benötigt wird
 * 
 * @author Stefan Schöb
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Schöb
 * @since 1.0     
 */

class PDF extends HTML2FPDF
{
	  
	/*function Header()
{
    //Logo
    $this->Image('logo_pb.png',10,8,33);
    //Arial fett 15
    $this->SetFont('Arial','B',15);
    //nach rechts gehen
    $this->Cell(80);
    //Titel
    $this->Cell(30,10,'Titel',1,0,'C');
    //Zeilenumbruch
    $this->Ln(20);
}*/

	function Header()
	{
		//Select Arial bold 15
		$this->SetFont('Arial','B',15);
		//Move to the right
		$this->Cell(5);
		
		//Framed title
		$this->Cell(100,0,'Titlexxx',0,0,'C');
		
		$this -> Image(MEDIEN . 'bilder/icons/scgams.jpg', 165, 2);
		//Line break
		$this->Ln(20);
	}
	
	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-15);
		//Select Arial italic 8
		$this->SetFont('Arial','I',8);
		//Print centered page number
		$this->Cell(0,10,'Seite '.$this->PageNo() . ' von {nb}' ,0,0,'C');
	}
}

?>