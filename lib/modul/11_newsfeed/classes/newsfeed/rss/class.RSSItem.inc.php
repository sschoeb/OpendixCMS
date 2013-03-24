<?php
require_once 'class.RSSBase.inc.php';

class RSSItem extends RSSBase {
	
	protected $about;
	protected $title;
	protected $link;
	protected $description;
	protected $subject;
	protected $date;
	protected $author;
	protected $comments;
	protected $image;
	private $guid = '';
	function __construct($about = '', 
						 $title = '', 
						 $link = '', 
						 $description = '',
						 $subject = '',	
						 $date = 0,	
						 $author = '', 
						 $comments = '',
						 $image = '', 
						 $guid = '') {
		parent::__construct();
		parent::setVar($about, 'about', 'string');
		parent::setVar($title, 'title', 'string');
		parent::setVar($link, 'link', 'string');
		parent::setVar($description, 'description', 'string');
		parent::setVar($subject, 'subject', 'string');
		parent::setVar($date, 'date', 'int');
		parent::setVar($author, 'author', 'string');
		parent::setVar($comments, 'comments', 'string');
		parent::setVar($image, 'image', 'string');
		$this -> guid = $guid;
	} // end constructor

	public function &getAbout() {
		return parent::getVar('about');
	} // end function

	public function &getTitle() {
		return parent::getVar('title');
	} // end function
	
	public function &getLink() {
		return parent::getVar('link');
	} // end function	
	
	public function &getDescription() {
		return parent::getVar('description');
	} // end function	
	
	public function &getSubject() {
		return parent::getVar('subject');
	} // end function		
	
	public function &getItemDate() {
		return parent::getVar('date');
	} // end function		
	
	public function &getAuthor() {
		return parent::getVar('author');
	} // end function		
	
	public function &getComments() {
		return parent::getVar('comments');
	} // end function		
	
	public function &getImage() {
		return parent::getVar('image');
	} // end function		
	
	public function getGuid()
	{
		return $this -> guid;
	}
} // end class
?>