<?php
require_once 'class.RSSBase.inc.php';

class RSSItemList extends ObjectList {
	
	function __construct() {
		parent::__construct(0,100);
	} // end constructor
	
	public function addRSSItem(RSSItem &$item) {
		parent::addObject($item);
	} // end function
	
} // end class
?>