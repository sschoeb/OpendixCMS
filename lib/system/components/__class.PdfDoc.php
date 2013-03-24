<?php
/**
 * Opendix
 * 
 *
 * @package    OpendixCMS
 * @author     Stefan Sch�b <opendix@gmail.com>
 * @copyright  2006-2009 Stefan Sch�b
 * @version    1.0
 */

/**
 * Hauptklasse mit der die ganze Seit einstanziert wird und aufgebaut wird
 * 
 * @author Stefan Sch�b
 * @package OpendixCMS
 * @version 1.0
 * @copyright Copyright &copy; 2006, Stefan Sch�b
 * @since 1.0  
 * @todo wird die gebraucht?   
 */
	class PDFDoc extends PDF 
	{
	  function PDFDoc() 
	  {    
		  $this->pdf = new PDF("P", "mm", "A4");    
		  $this->pdf->SetTitle("PDF-Vortrag");    
		  $this->pdf->AliasNbPages();     
		  $this->pdf->Open();    
		  $this->pdf->SetMargins(25,25,20);    
		  $this->pdf->AddPage();  
	  }
	}

//$pdf = new pdfdoc;
?>