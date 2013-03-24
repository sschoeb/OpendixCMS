<?php

/**
 * Datei welche vom Filebrowser aufgerufen wird
 *
 * This is a demonstration of a useful AJAX capability. It is a simple file browser.
 * The nice thing about this, as opposed to file browser interfaces on the Web
 * server, is that it is completely customizable, and is very "snappy."
 * The entire file browser is contained in a JavaScript file, so you simply add
 * a <script> tag to your code where you want the browser to appear.
 * This relies on my AJAX Queue Class. You need to have that class included into
 * the <head> of your page for this to work.
 * All client-side HTML is generated dynamically, using JavaScript DHTML techniques.
 * This is a 100% JavaScript solution.
 * This file is the server-side file. It actually browses the file system, and returns the
 * results as a definition list <dl></dl>.
 *
 * @package 	OpendixCMS.Core.AJAX
 * @version 	1.0
 * @author 		Chris Marshall
 * @author 		Stefan Schöb
 * @copyright 	Chris Marshall http://www.cmarshall.net/
 */

if(!$_SESSION['login'])
{
	//die('NICHT EINGELOGGT');
}

//Pfad von dem ausgegangen wird
$fixed_root = "../../../../filebase/";	// Security Feature
$folderFromRoot = 'filebase/';
//Abfrage des Ordners
$dir_root = $_GET['dir_root'];	// The place to begin the parse, relative to the fixed root.

$dir_root = preg_replace ( "-(\.\./)*-", "", $dir_root );	// Just so no one sneaks any nasties in.

$div_id = $_GET['div_id'];	// The ID of the enclosing <div>, <dt> or <dd> tag.

$item_name = $_GET['item_name'];	// The name of the enclosing directory.

$collapse = $_GET['collapse'];	// Set to 1 if the directory is open, and needs to be closed.

$posix_root = $fixed_root . $dir_root;

$item_count = 0;

if ( is_dir ( $posix_root ) )
{
	// Make sure that it is a directory.
	if ( $dh = opendir ( $posix_root ) )
	{
		rewinddir ( $dh );
		// If we will be displaying it closed, we use one class. These are for the main definition term element (<dt>).
		if ( $collapse )
		{
			$collapse = "";
			$d_class = "dt_class_dir_closed";
		}
		else	// If open, we use another.
		{
			$collapse = 1;
			$d_class = "dt_class_dir_open";
		}

		// A directory is always displayed as a definition list, even if there is only one element (closed directory).
		echo "<dl class=\"dl_class_dir_tree\">";
		// If we have an item that rates a link.
		if ( $item_name )
		{
			echo "<dt class=\"$d_class\"><a href=\"javascript:callout('collapse=$collapse&amp;dir_root=".urlencode($dir_root)."&amp;div_id=$div_id&amp;item_name=".urlencode($item_name)."', '$div_id')\" class=\"";

			// Again, we assign different classes, based upon the "open" or "closed" state of the directory. These are for the links.
			if ( $collapse )
			{
				echo "a_class_dir_link_open\"><img src=\"filebase/images/opendir.gif\">".htmlspecialchars ( $item_name )."</a></dt>\n";
			}
			else
			{
				echo "a_class_dir_link_closed\"><img src=\"filebase/images//dir.gif\">".htmlspecialchars ( $item_name )."</a></dt>\n";
			}

		}

		// If the directory is open, we list its contents here.
		while ( $collapse && ($item = readdir ( $dh )) )
		{
			if ( !preg_match ( "/^\./", $item ) )
			{
				$this_div = $div_id."_".$item_count;
				$item_count++;
				$viable_link = true;
				// We check to see if nested subdirectories have contents (they can be opened, if so).
				if ( is_dir ( $posix_root."/".$item ) )
				{
					$viable_link = false;
					if ( $dh2 = opendir ( $posix_root."/".$item ) )
					{
						rewinddir ( $dh2 );
						while ( $item2 = readdir ( $dh2 ) )
						{
							if ( !preg_match ( "/^\./", $item2 ) )
							{
								$viable_link = true;
								break;
							}
						}
						closedir ( $dh2 );
					}
				}


				// Nested contents are always in <dd> tags.
				echo "<dd id=\"$this_div\"";
				if ( is_dir ( $posix_root."/".$item ) )	// If it is a directory, then we allow an AJAX call to open it.
				{
					
					if ( $viable_link )	// If the directory contains files or subdirectories, it can be opened.
					{
						echo " class=\"dd_class_dir_closed\">";
						echo "<a class=\"a_class_dir_link_closed\" href=\"javascript:callout('actionOnClick=". $_GET['actionOnClick'] . "&dir_root=".urlencode($dir_root."/".$item)."&amp;div_id=$this_div&amp;item_name=".urlencode($item)."', '$this_div')\"><img src=\"filebase/images/dir.gif\">";
					}
					else	// If not, it is "dead."
					{
						echo " class=\"dd_class_dir_empty\"><img src=\"filebase/images/emptydir.gif\">";
					}
				}
				else	// If it is a file, then we just provide a URI to that file.
				{
					echo " class=\"dd_class_file\">";
					
					$tposix_root = str_replace($fixed_root, '', $posix_root);
					
					if(!isset($_GET['actionOnClick']))
					{
						$_GET['actionOnClick'] = 'takeFile';
					}
					echo "<a class=\"a_class_file_link\" href=\"javascript:". $_GET['actionOnClick'] ."('".htmlspecialchars($tposix_root."/".$item)."');\"><img src=\"filebase/images/file.gif\">";
				}

				echo '' . $item;

				if ( $viable_link )
				{
					echo "</a>";
				}
				echo "</dd>\n";
			}
		}
		closedir ( $dh );
	}
	echo "</dl>\n";
	
	
}

?>
