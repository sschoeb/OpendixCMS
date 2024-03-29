<?php
require_once 'interface.AtomBuilder.inc.php';
require_once 'class.AtomBuilderBase.inc.php';

/**
* Class for creating an Atom-Feed
* @author Michael Wimmer <flaimo@gmx.net>
* @category FLP
* @copyright Copyright © 2002-2006, Michael Wimmer
* @license Free for non-commercial use
* @link http://flp.sf.net/
* @package Atom
* @version 1.00RC2
*/
abstract class AtomBuilder_V_abstract extends AtomBuilderBase implements AtomBuilderInterface {

	protected $atomdata;
	protected $xml;
	protected $filename;

	function __construct(AtomBuilder &$atomdata) {
		parent::__construct();
		$this->atomdata =& $atomdata;
	} // end constructor

	protected function getAtomData() {
		return $this->atomdata;
	} // end function

	protected function generateXML() {
		$this->xml = new DomDocument('1.0', $this->atomdata->getEncoding());
		$this->xml->appendChild($this->xml->createComment('[' .  date('Y-m-d H:i:s')  .']'));
	} // end function

	public function outputAtom($output = TRUE) {
		if (!isset($this->xml)) {
			$this->generateXML();
		} // end if
		echo $this->xml->saveXML();
	} // end function

	public function saveAtom($path = '') {
		if (!isset($this->xml)) {
			$this->generateXML();
		} // end if
		$this->xml->save($path . $this->atomdata->getFilename());
		return (string) $path . $this->atomdata->getFilename();
	} // end function

	public function getAtomOutput() {
		if (!isset($this->xml)) {
			$this->generateXML();
		} // end if
		return $this->xml->saveXML();
	} // function
} // end class
?>